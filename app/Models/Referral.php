<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['team_id', 'referrer_card_id', 'referred_card_id'])]
class Referral extends Model
{
    /** @return BelongsTo<card, $this> */
    public function referrerCard(): BelongsTo
    {
        return $this->belongsTo(card::class, 'referrer_card_id');
    }

    /** @return BelongsTo<card, $this> */
    public function referredCard(): BelongsTo
    {
        return $this->belongsTo(card::class, 'referred_card_id');
    }
}
