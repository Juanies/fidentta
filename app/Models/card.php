<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use App\Services\WalletStampProgress;
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

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function cardDesign()
    {
        return $this->belongsTo(cardDesign::class);
    }

    public function cardTransactions()
    {
        return $this->hasMany(CardTransaction::class);
    }

    public function customer()
    {
        return $this->belongsTo(CustomerUser::class, 'customer_id');
    }
}
