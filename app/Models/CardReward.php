<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['team_id', 'customer_id', 'card_id', 'reward_name', 'cycle_number', 'earned_at', 'earned_by', 'delivered_at', 'delivered_by'])]
class CardReward extends Model
{
    protected function casts(): array
    {
        return [
            'earned_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /** @return BelongsTo<CustomerUser, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerUser::class);
    }

    /** @return BelongsTo<card, $this> */
    public function card(): BelongsTo
    {
        return $this->belongsTo(card::class);
    }

    /** @return BelongsTo<User, $this> */
    public function earnedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'earned_by');
    }

    /** @return BelongsTo<User, $this> */
    public function deliveredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'delivered_by');
    }
}
