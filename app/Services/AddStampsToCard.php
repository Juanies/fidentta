<?php

namespace App\Services;

use App\Models\card as LoyaltyCard;
use App\Models\CardTransaction;
use App\Models\Location;
use App\Models\User;
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
        ?string $requestKey = null,
    ): CardTransaction {
        return $this->stamp($card, $user, $location, $amount, $requestKey);
    }

    /** Sello automático (p. ej. bonus por referido), sin usuario del equipo. */
    public function addAutomatic(LoyaltyCard $card, Location $location, int $amount = 1): CardTransaction
    {
        return $this->stamp($card, null, $location, $amount, null);
    }

    private function stamp(
        LoyaltyCard $card,
        ?User $user,
        Location $location,
        int $amount,
        ?string $requestKey,
    ): CardTransaction {
        if ($amount < 1) {
            throw ValidationException::withMessages([
                'amount' => 'Debes añadir al menos un sello.',
            ]);
        }

        return DB::transaction(function () use ($card, $user, $location, $amount, $requestKey) {
            $lockedCard = LoyaltyCard::query()
                ->with(['team', 'cardDesign', 'customer'])
                ->lockForUpdate()
                ->findOrFail($card->id);

            if ($user !== null && ! $user->belongsToTeam($lockedCard->team)) {
                throw new AuthorizationException('No tienes acceso al negocio de esta tarjeta.');
            }

            if (! $lockedCard->is_active || ! $location->is_active || $location->team_id !== $lockedCard->team_id) {
                throw new AuthorizationException('La tarjeta o el local no están disponibles para registrar sellos.');
            }

            if (! $lockedCard->customer || $lockedCard->customer->team_id !== $lockedCard->team_id) {
                throw new ModelNotFoundException('No se encontró el cliente asociado a esta tarjeta.');
            }

            if ($requestKey !== null) {
                $previousTransaction = $lockedCard->cardTransactions()
                    ->where('request_key', $requestKey)
                    ->first();

                if ($previousTransaction) {
                    return $previousTransaction;
                }
            }

            $required = (int) $lockedCard->cardDesign?->stamps_required;

            if ($required < 1) {
                throw new ModelNotFoundException('La tarjeta no tiene un diseño de fidelidad válido.');
            }

            $current = (int) $lockedCard->stamps_collected;
            $lastCycle = (int) ($lockedCard->rewards()->max('cycle_number') ?? 0);
            $cycleNumber = $lastCycle + 1;

            if ($current >= $required) {
                if (! $lockedCard->rewards()->where('cycle_number', $lastCycle)->exists()) {
                    $this->recordReward($lockedCard, $user, $cycleNumber);
                    $lastCycle = $cycleNumber;
                }

                $current = 0;
                $cycleNumber = $lastCycle + 1;
            }

            $stampsRemaining = $amount;

            while ($stampsRemaining > 0) {
                $stampsToComplete = $required - $current;
                $stampsAdded = min($stampsToComplete, $stampsRemaining);
                $current += $stampsAdded;
                $stampsRemaining -= $stampsAdded;

                if ($current === $required) {
                    $this->recordReward($lockedCard, $user, $cycleNumber);

                    if ($stampsRemaining > 0) {
                        $current = 0;
                        $cycleNumber++;
                    }
                }
            }

            $lockedCard->update(['stamps_collected' => $current]);

            return $lockedCard->cardTransactions()->create([
                'customer_id' => $lockedCard->customer_id,
                'location_id' => $location->id,
                'user_id' => $user?->id,
                'stamps_added' => $amount,
                'request_key' => $requestKey,
            ]);
        });
    }

    private function recordReward(LoyaltyCard $card, ?User $user, int $cycleNumber): void
    {
        $card->rewards()->create([
            'team_id' => $card->team_id,
            'customer_id' => $card->customer_id,
            'reward_name' => $card->cardDesign->reward ?: 'Recompensa',
            'cycle_number' => $cycleNumber,
            'earned_at' => now(),
            'earned_by' => $user?->id,
        ]);
    }
}
