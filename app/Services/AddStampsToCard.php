<?php

namespace App\Services;

use App\Models\CardTransaction;
use App\Models\Location;
use App\Models\User;
use App\Models\card as LoyaltyCard;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AddStampsToCard
{
    public function add(
        LoyaltyCard $card,
        User $user,
        Location $location,
        int $amount = 1,
    ): CardTransaction {
        if ($amount < 1) {
            throw ValidationException::withMessages([
                'amount' => 'Debes añadir al menos un sello.',
            ]);
        }

        return DB::transaction(function () use ($card, $user, $location, $amount) {
            $lockedCard = LoyaltyCard::query()
                ->with(['team', 'cardDesign', 'customer'])
                ->lockForUpdate()
                ->findOrFail($card->id);

            if (! $user->belongsToTeam($lockedCard->team)) {
                throw new AuthorizationException('No tienes acceso al negocio de esta tarjeta.');
            }

            if (! $lockedCard->is_active || ! $location->is_active || $location->team_id !== $lockedCard->team_id) {
                throw new AuthorizationException('La tarjeta o el local no están disponibles para registrar sellos.');
            }

            if (! $lockedCard->customer || $lockedCard->customer->team_id !== $lockedCard->team_id) {
                throw new ModelNotFoundException('No se encontró el cliente asociado a esta tarjeta.');
            }

            $required = (int) $lockedCard->cardDesign?->stamps_required;

            if ($required < 1) {
                throw new ModelNotFoundException('La tarjeta no tiene un diseño de fidelidad válido.');
            }

            $current = (int) $lockedCard->stamps_collected;

            if ($current + $amount > $required) {
                throw ValidationException::withMessages([
                    'amount' => "Solo quedan " . ($required - $current) . ' sellos para completar esta tarjeta.',
                ]);
            }

            $lockedCard->update(['stamps_collected' => $current + $amount]);

            return $lockedCard->cardTransactions()->create([
                'customer_id' => $lockedCard->customer_id,
                'location_id' => $location->id,
                'user_id' => $user->id,
                'stamps_added' => $amount,
            ]);
        });
    }
}
