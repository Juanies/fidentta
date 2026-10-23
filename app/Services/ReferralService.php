<?php

namespace App\Services;

use App\Models\card as LoyaltyCard;
use App\Models\Referral;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class ReferralService
{
    public function __construct(private AddStampsToCard $stamps) {}

    /** Da 1 sello a ambas tarjetas si el referido es válido. Devuelve true si se aplicó. */
    public function reward(?string $referrerToken, LoyaltyCard $newCard): bool
    {
        if (! filled($referrerToken)) {
            return false;
        }

        $referrer = LoyaltyCard::query()
            ->with('customer')
            ->where('referral_token', $referrerToken)
            ->where('team_id', $newCard->team_id)
            ->where('is_active', true)
            ->first();

        $newCard->loadMissing('customer');

        // Sin registro no hay identidad: el premio exige clientes registrados.
        if ($newCard->customer?->guest || $referrer?->customer?->guest) {
            return false;
        }

        if (! $referrer || $referrer->id === $newCard->id || $referrer->customer_id === $newCard->customer_id) {
            return false;
        }

        $newEmail = $newCard->customer?->email;

        if ($newEmail !== null && $newEmail === $referrer->customer?->email) {
            return false;
        }

        $referrerLocation = $referrer->customer?->location;
        $newLocation = $newCard->customer?->location;

        if (! $referrerLocation || ! $newLocation) {
            return false;
        }

        try {
            DB::transaction(function () use ($referrer, $newCard, $referrerLocation, $newLocation): void {
                Referral::create([
                    'team_id' => $newCard->team_id,
                    'referrer_card_id' => $referrer->id,
                    'referred_card_id' => $newCard->id,
                ]);

                $this->stamps->addAutomatic($referrer, $referrerLocation);
                $this->stamps->addAutomatic($newCard, $newLocation);
            });
        } catch (UniqueConstraintViolationException) {
            return false;
        }

        return true;
    }
}
