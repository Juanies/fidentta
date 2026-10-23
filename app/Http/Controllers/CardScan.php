<?php

namespace App\Http\Controllers;

use App\Models\card as LoyaltyCard;
use App\Models\Location;
use App\Services\AddStampsToCard;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CardScan extends Controller
{
    public function show(Request $request, string $token): View
    {
        $card = $this->findCard($token);
        $user = $request->user();

        if ($user && $user->belongsToTeam($card->team)) {
            return view('wallet.scan-staff', [
                'card' => $card,
                'team' => $card->team,
                'required' => (int) $card->cardDesign?->stamps_required,
                'requestKey' => (string) Str::uuid(),
            ]);
        }

        // Tras iniciar sesión, el equipo vuelve a esta misma página.
        $request->session()->put('url.intended', $request->url());

        return view('wallet.scan-invite', [
            'card' => $card,
            'team' => $card->team,
            'registerUrl' => route('wallet.card', ['qr_token' => $card->customer->location->qr_token, 'ref' => $card->referral_token]),
        ]);
    }

    public function stamp(Request $request, string $token, AddStampsToCard $stamps): RedirectResponse
    {
        $card = $this->findCard($token);
        $user = $request->user();

        abort_unless($user && $user->belongsToTeam($card->team), 403);

        $data = $request->validate(['request_key' => ['required', 'uuid']]);

        $location = $card->customer->location;

        if (! $location?->is_active) {
            $location = Location::where('team_id', $card->team_id)->where('is_active', true)->first();
        }

        abort_unless($location, 422, 'No hay ningún local activo para registrar el sello.');

        $stamps->add($card, $user, $location, 1, $data['request_key']);

        return redirect()->route('wallet.scan', $token)->with('status', 'Sello añadido.');
    }

    private function findCard(string $token): LoyaltyCard
    {
        $card = LoyaltyCard::query()
            ->with(['team', 'cardDesign', 'customer.location'])
            ->where('referral_token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        abort_unless($card->customer?->location?->is_active, 404);

        return $card;
    }
}
