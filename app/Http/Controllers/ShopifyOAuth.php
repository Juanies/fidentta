<?php

namespace App\Http\Controllers;

use App\Enums\TeamPermission;
use App\Models\ShopifyIntegration;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ShopifyOAuth extends Controller
{
    private const SHOP_PATTERN = '/^[a-z0-9][a-z0-9\-]*\.myshopify\.com$/';

    private ?string $lastWebhookError = null;

    public function connect(Request $request): RedirectResponse
    {
        $team = $request->user()->currentTeam;
        abort_unless($team && $request->user()->hasTeamPermission($team, TeamPermission::UpdateTeam), 403);

        $shop = $this->normalizeShop((string) $request->query('shop'));

        if (! $shop) {
            return $this->back($team, 'Escribe el dominio de tu tienda, por ejemplo mitienda.myshopify.com.', true);
        }

        if (! filled(config('services.shopify.client_id')) || ! filled(config('services.shopify.client_secret'))) {
            return $this->back($team, 'La app de Shopify no está configurada en el servidor.', true);
        }

        $state = Str::random(40);
        $request->session()->put('shopify_oauth', ['state' => $state, 'team_id' => $team->id, 'shop' => $shop]);

        return redirect()->away("https://{$shop}/admin/oauth/authorize?" . http_build_query([
            'client_id' => config('services.shopify.client_id'),
            'scope' => config('services.shopify.scopes'),
            'redirect_uri' => route('shopify.callback'),
            'state' => $state,
        ]));
    }

    public function callback(Request $request): RedirectResponse
    {
        $flow = $request->session()->pull('shopify_oauth');
        $team = is_array($flow) ? Team::find($flow['team_id'] ?? null) : null;

        abort_unless($team && $request->user()->hasTeamPermission($team, TeamPermission::UpdateTeam), 403);

        $shop = $this->normalizeShop((string) $request->query('shop'));

        if (
            ! $shop || $shop !== $flow['shop']
            || ! hash_equals($flow['state'], (string) $request->query('state'))
            || ! $this->validHmac($request)
        ) {
            return $this->back($team, 'No se pudo verificar la respuesta de Shopify. Inténtalo de nuevo.', true);
        }

        $token = Http::asForm()->post("https://{$shop}/admin/oauth/access_token", [
            'client_id' => config('services.shopify.client_id'),
            'client_secret' => config('services.shopify.client_secret'),
            'code' => (string) $request->query('code'),
        ])->json('access_token');

        if (! is_string($token) || $token === '') {
            return $this->back($team, 'Shopify no entregó el acceso. Inténtalo de nuevo.', true);
        }

        $integration = ShopifyIntegration::query()->firstOrNew(['team_id' => $team->id]);
        $integration->fill([
            'shop_domain' => $shop,
            'access_token' => $token,
            'webhook_secret' => null,
            'is_active' => true,
        ]);
        $integration->webhook_token ??= Str::random(48);

        if (! $this->registerWebhooks($integration)) {
            return $this->back($team, 'Conectado, pero Shopify rechazó crear los webhooks (' . $this->lastWebhookError . ').', true);
        }

        $integration->save();

        return $this->back($team, 'Shopify conectado. Los pedidos pagados ya sumarán sellos.');
    }

    private function registerWebhooks(ShopifyIntegration $integration): bool
    {
        $address = route('webhooks.shopify', $integration->webhook_token);
        $version = config('services.shopify.api_version');

        foreach (['orders/paid', 'app/uninstalled'] as $topic) {
            $response = Http::withHeaders(['X-Shopify-Access-Token' => $integration->access_token])
                ->post("https://{$integration->shop_domain}/admin/api/{$version}/webhooks.json", [
                    'webhook' => ['topic' => $topic, 'address' => $address, 'format' => 'json'],
                ]);

            $body = $response->json('errors');
            $taken = $response->status() === 422 && str_contains(json_encode($body), 'already been taken');

            if (! $response->successful() && ! $taken) {
                Log::warning('Shopify webhook registration failed.', [
                    'shop' => $integration->shop_domain,
                    'topic' => $topic,
                    'address' => $address,
                    'status' => $response->status(),
                    'errors' => $body,
                ]);
                $this->lastWebhookError = "{$topic}: HTTP {$response->status()} " . Str::limit(json_encode($body, JSON_UNESCAPED_UNICODE), 200);

                return false;
            }
        }

        return true;
    }

    private function validHmac(Request $request): bool
    {
        $params = $request->query();
        $received = (string) ($params['hmac'] ?? '');
        unset($params['hmac']);
        ksort($params);

        $message = collect($params)->map(fn($value, $key) => $key . '=' . $value)->implode('&');
        $expected = hash_hmac('sha256', $message, (string) config('services.shopify.client_secret'));

        return $received !== '' && hash_equals($expected, $received);
    }

    private function normalizeShop(string $shop): ?string
    {
        $shop = strtolower(trim((string) preg_replace('#^https?://#i', '', trim($shop)), '/ '));

        return preg_match(self::SHOP_PATTERN, $shop) ? $shop : null;
    }

    private function back(Team $team, string $message, bool $error = false): RedirectResponse
    {
        return redirect()
            ->route('dashboard.configuracion', ['current_team' => $team])
            ->with($error ? 'shopify_error' : 'shopify_status', $message);
    }
}
