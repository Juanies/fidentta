<?php

namespace App\Http\Controllers;

use App\Models\CustomerUser;
use App\Models\Location;
use App\Models\Team;
use App\Models\card as LoyaltyCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Spatie\LaravelMobilePass\Builders\Apple\StoreCardPassBuilder;
use Spatie\LaravelMobilePass\Builders\Google\LoyaltyPassBuilder;
use Spatie\LaravelMobilePass\Builders\Google\LoyaltyPassClass;
use Spatie\LaravelMobilePass\Enums\BarcodeType;
use Spatie\LaravelMobilePass\Enums\PassType;
use Spatie\LaravelMobilePass\Models\MobilePass;
use App\Services\WalletStampProgress;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;

class Card extends Controller
{
    public function index(string $qr_token)
    {
        $location = Location::where('qr_token', $qr_token)
            ->with(['team.customerRegistrationFields' => fn($query) => $query
                ->where('is_active', true)
                ->orderBy('sort_order')])
            ->firstOrFail();

        abort_unless($location->is_active, 404);

        $team = $location->team;
        $mode = $this->registrationMode($team->customer_registration_type);

        return view('wallet.page-links', [
            'team' => $team,
            'location' => $location,
            'mode' => $mode,
            'fields' => $team->customerRegistrationFields,
        ]);
    }

    public function pageLinks(string $qr_token){

       $location = Location::where('qr_token', $qr_token)
            ->with(['team.customerRegistrationFields' => fn($query) => $query
                ->where('is_active', true)
                ->orderBy('sort_order')])
            ->firstOrFail();

        $team = $location->team;


        abort_unless($location->is_active, 404);

        return view('wallet.page-links', [
            'team' => $team,
            'location' => $location,

        ]);
    }

    public function store(Request $request, string $qr_token)
    {
        $location = Location::where('qr_token', $qr_token)
            ->with(['team.customerRegistrationFields' => fn($query) => $query
                ->where('is_active', true)
                ->orderBy('sort_order')])
            ->firstOrFail();

        abort_unless($location->is_active, 404);

        $team = $location->team;
        $mode = $this->registrationMode($team->customer_registration_type);
        $fields = $team->customerRegistrationFields;

        if ($mode === 'none') {
            $sessionKey = "guest_customer_{$team->id}_{$location->id}";
            $existingCustomer = CustomerUser::with('cards')
                ->where('team_id', $team->id)
                ->where('location_id', $location->id)
                ->where('guest', true)
                ->find($request->session()->get($sessionKey));

            if ($existingCustomer && $existingCustomer->cards->isNotEmpty()) {
                $card = $existingCustomer->cards->first();
                $walletUrls = $this->issueWalletPasses($existingCustomer, $card);

                return view('wallet.card-created', [
                    'team' => $team,
                    'location' => $location,
                    'card' => $card,
                    'walletUrls' => $walletUrls,
                    // 'appleWalletNeedsPublicHttps' => $this->appleWalletNeedsPublicHttps(),
                ]);
            }

            $validated = [];
        } elseif ($mode === 'normal') {
            $validated = $request->validate([
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('customer_users', 'email')->where('team_id', $team->id),
                ],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'telefono' => ['nullable', 'string', 'max:40'],
            ]);
        } else {
            $rules = [];

            foreach ($fields as $field) {
                $fieldRules = [$field->is_required ? 'required' : 'nullable'];

                if ($field->type === 'email') {
                    $fieldRules[] = 'email';
                    $fieldRules[] = 'max:255';

                    if ($field->is_required) {
                        $fieldRules[] = Rule::unique('customer_users', 'email')->where('team_id', $team->id);
                    }
                } elseif ($field->type === 'date') {
                    $fieldRules[] = 'date';
                } else {
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:1000';
                }

                $rules["fields.{$field->id}"] = $fieldRules;
            }

            $validated = $request->validate($rules);
        }

        [$customer, $card] = DB::transaction(function () use ($mode, $team, $location, $fields, $validated) {
            $email = match ($mode) {
                'normal' => strtolower($validated['email']),
                'custom' => $this->customEmail($fields, $validated['fields'] ?? []),
                default => null,
            };

            $customer = CustomerUser::create([
                'email' => $email,
                'password' => $mode === 'normal' ? $validated['password'] : null,
                'team_id' => $team->id,
                'location_id' => $location->id,
                'guest' => $mode === 'none',
            ]);

            if ($mode === 'normal' && isset($validated['telefono'])) {
                $phoneField = $fields->firstWhere('field_key', 'telefono');

                if ($phoneField) {
                    $customer->registrationValues()->create([
                        'customer_registration_field_id' => $phoneField->id,
                        'value' => $validated['telefono'],
                    ]);
                }
            }

            foreach ($fields as $field) {
                $value = $validated['fields'][$field->id] ?? null;

                if ($mode === 'custom' && $value !== null && $field->field_key !== 'email') {
                    $customer->registrationValues()->create([
                        'customer_registration_field_id' => $field->id,
                        'value' => $value,
                    ]);
                }
            }

            $design = $team->cardDesign()->where('is_active', true)->firstOrFail();
            $card = $customer->cards()->create([
                'team_id' => $team->id,
                'card_design_id' => $design->id,
                'stamps_collected' => 0,
                'is_active' => true,
            ]);

            return [$customer, $card];
        });

        if ($mode === 'none') {
            $request->session()->put("guest_customer_{$team->id}_{$location->id}", $customer->id);
        }

        $walletUrls = $this->issueWalletPasses($customer, $card);

        return view('wallet.card-created', [
            'team' => $team,
            'location' => $location,
            'card' => $card,
            'walletUrls' => $walletUrls,
            'appleWalletNeedsPublicHttps' => $this->appleWalletNeedsPublicHttps(),
        ]);
    }

    public function qr(Location $location)
    {
        $url = route('wallet.card', [
            'qr_token' => $location->qr_token,
        ]);

        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);

        $qr = $writer->writeString($url);

        return response($qr)
            ->header('Content-Type', 'image/svg+xml');
    }

    private function registrationMode(?string $mode): string
    {
        return match ($mode) {
            'normal', 'basico' => 'normal',
            'custom' => 'custom',
            default => 'none',
        };
    }

    private function customEmail($fields, array $values): ?string
    {
        $emailField = $fields->firstWhere('field_key', 'email');
        $email = $emailField ? ($values[$emailField->id] ?? null) : null;

        return $email ? strtolower($email) : null;
    }

    private function issueWalletPasses(CustomerUser $customer, LoyaltyCard $card): array
    {
        return array_filter([
            'apple' => $this->issueAppleWalletPass($customer, $card)?->addToWalletUrl(),
            'google' => $this->issueGoogleWalletPass($customer, $card)?->addToWalletUrl(),
        ]);
    }

    private function issueAppleWalletPass(CustomerUser $customer, LoyaltyCard $card): ?MobilePass
    {
        $certificate = config('mobile-pass.apple.certificate');
        $certificatePath = config('mobile-pass.apple.certificate_path');
        $hasCertificate = filled($certificate) || (filled($certificatePath) && is_file($certificatePath));
        // || $this->appleWalletNeedsPublicHttps()
        if (! $hasCertificate || ! filled(config('mobile-pass.apple.type_identifier')) || ! filled(config('mobile-pass.apple.team_identifier'))) {
            return null;
        }

        $existingPass = $customer->firstApplePass(PassType::StoreCard);

        try {
            $design = $card->cardDesign;
            $colorScheme = $design?->color_scheme ?? [];
            $backgroundColor = $colorScheme['colorPrincipal'] ?? '#2563EB';
            $requiredStamps = max(1, (int) $design?->stamps_required);
            $progress = WalletStampProgress::circles((int) $card->stamps_collected, $requiredStamps);

            if (! preg_match('/^#[0-9A-Fa-f]{6}$/', $backgroundColor)) {
                $backgroundColor = '#2563EB';
            }

            if ($existingPass) {
                $builder = $existingPass->builder()
                    ->setIconImage($iconPath = $this->teamLogoPath($card->team) ?? storage_path('app/private/passgenerator/assets/icon.png'));

                if ($iconPath !== storage_path('app/private/passgenerator/assets/icon.png')) {
                    $builder->setLogoImage($iconPath);
                }

                $builder->updateField('stamps', $card->stamps_collected . '/' . $requiredStamps)
                    ->updateField('progress', $progress)
                    ->save();
                $existingPass->refresh();
                $existingPass->generate();

                return $existingPass;
            }

            $passBuilder = StoreCardPassBuilder::make()
                ->setOrganizationName(config('mobile-pass.apple.organization_name') ?: $card->team->name)
                ->setSerialNumber('card-' . $card->id)
                ->setDownloadName(Str::slug($card->team->name) . '-tarjeta')
                ->setDescription('Tarjeta de fidelidad de ' . $card->team->name)
                ->setLogoText(Str::limit($card->team->name, 20, ''))
                ->setIconImage(storage_path('app/private/passgenerator/assets/icon.png'));

            if ($logoPath = $this->teamLogoPath($card->team)) {
                $passBuilder->setLogoImage($logoPath)->setIconImage($logoPath);
            }

            $pass = $passBuilder
                ->setBackgroundColor($backgroundColor)
                ->setForegroundColor('#FFFFFF')
                ->setBarcode(BarcodeType::Qr, 'FIDELIDAD-CARD-' . $card->id, (string) $card->id)
                ->addField('stamps', $card->stamps_collected . '/' . $requiredStamps, label: 'Sellos')
                ->addSecondaryField('progress', $progress, label: 'Progreso')
                ->addSecondaryField('reward', (string) $design?->reward, label: 'Recompensa')
                ->save();

            $pass->generate();
            $customer->addMobilePass($pass);

            return $pass;
        } catch (\Throwable $exception) {
            Log::error('Apple Wallet pass generation failed.', [
                'customer_id' => $customer->id,
                'card_id' => $card->id,
                'exception' => $exception::class,
            ]);

            return null;
        }
    }

    private function teamLogoPath(Team $team): ?string
    {
        if (! $team->logo) {
            return null;
        }

        $path = (string) parse_url($team->logo, PHP_URL_PATH);
        $relative = str_starts_with($path, '/storage/')
            ? substr($path, strlen('/storage/'))
            : $team->logo;
        $absolute = storage_path('app/public/' . ltrim($relative, '/'));

        return is_file($absolute) ? $absolute : null;
    }

    private function appleWalletNeedsPublicHttps(): bool
    {
        $appUrl = (string) config('app.url');
        $scheme = parse_url($appUrl, PHP_URL_SCHEME);
        $host = strtolower((string) parse_url($appUrl, PHP_URL_HOST));

        if ($scheme !== 'https' || $host === '') {
            return true;
        }

        if (
            in_array($host, ['localhost', '127.0.0.1', '::1'], true) ||
            preg_match('/\.(localhost|local|test|lan|internal)$/', $host)
        ) {
            return true;
        }

        if (filter_var($host, FILTER_VALIDATE_IP) && ! filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return true;
        }

        return false;
    }

    private function issueGoogleWalletPass(CustomerUser $customer, LoyaltyCard $card): ?MobilePass
    {
        $serviceAccountKey = config('mobile-pass.google.service_account_key');
        $serviceAccountPath = config('mobile-pass.google.service_account_key_path');

        if (
            ! filled(config('mobile-pass.google.issuer_id')) ||
            (! filled($serviceAccountKey) && (! filled($serviceAccountPath) || ! is_file($serviceAccountPath)))
        ) {
            return null;
        }

        $existingPass = $customer->firstGooglePass(PassType::StoreCard);

        if ($existingPass) {
            return $existingPass;
        }

        try {
            $team = $card->team;
            $design = $card->cardDesign;
            $color = $design?->color_scheme['colorPrincipal'] ?? '#2563EB';
            $requiredStamps = max(1, (int) $design?->stamps_required);
            $progress = WalletStampProgress::circles((int) $card->stamps_collected, $requiredStamps);

            if (! preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
                $color = '#2563EB';
            }

            $classSuffix = 'fidentta_team_' . $team->id;
            $passClass = LoyaltyPassClass::find($classSuffix) ?? LoyaltyPassClass::make($classSuffix)
                ->setIssuerName($team->name)
                ->setProgramName($team->name)
                ->setAccountNameLabel('Cliente')
                ->setAccountIdLabel('Número de tarjeta')
                ->setBackgroundColor($color)
                ->save();

            if ($team->logo) {
                $passClass->setProgramLogoUrl(url($team->logo))->save();
            }

            $pass = LoyaltyPassBuilder::make()
                ->setClass($classSuffix)
                ->setObjectSuffix('card_' . $card->id)
                ->setAccountId((string) $card->id)
                ->setAccountName($customer->email ?: 'Cliente ' . $customer->id)
                ->setBalanceString($card->stamps_collected . '/' . $requiredStamps)
                ->setBalanceLabel('Sellos')
                ->addTextModule('Progreso', $progress, 'stamp_progress')
                ->setBarcode(BarcodeType::Qr, route('wallet.card', ['qr_token' => $customer->location->qr_token]))
                ->save();

            $customer->addMobilePass($pass);

            return $pass;
        } catch (\Throwable $exception) {
            Log::error('Google Wallet pass generation failed.', [
                'customer_id' => $customer->id,
                'card_id' => $card->id,
                'exception' => $exception::class,
            ]);

            return null;
        }
    }
}
