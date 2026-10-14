<?php

namespace App\Models;

use App\Services\WalletStampProgress;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

#[Fillable(['customer_id', 'team_id', 'card_design_id', 'stamps_collected', 'is_active', 'card_type'])]

class card extends Model
{
    protected static function booted(): void
    {
        static::updated(function (card $card) {
            if ($card->wasChanged('stamps_collected')) {
                DB::afterCommit(function () use ($card) {
                    $updatedCard = $card->fresh();

                    if ($updatedCard) {
                        app(WalletStampProgress::class)->sync($updatedCard);
                    }
                });
            }
        });
    }

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /** @return BelongsTo<cardDesign, $this> */
    public function cardDesign(): BelongsTo
    {
        return $this->belongsTo(cardDesign::class);
    }

    /** @return HasMany<CardTransaction, $this> */
    public function cardTransactions(): HasMany
    {
        return $this->hasMany(CardTransaction::class);
    }

    /** @return HasMany<CardReward, $this> */
    public function rewards(): HasMany
    {
        return $this->hasMany(CardReward::class);
    }

    /** @return BelongsTo<CustomerUser, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerUser::class, 'customer_id');
    }
}
