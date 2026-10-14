<?php

namespace App\Models;

use Database\Factories\CardTransactionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['card_id', 'customer_id', 'location_id', 'stamps_added', 'user_id', 'request_key'])]

class CardTransaction extends Model
{
    /** @use HasFactory<CardTransactionFactory> */
    use HasFactory;

    /** @return BelongsTo<card, $this> */
    public function card(): BelongsTo
    {
        return $this->belongsTo(card::class);
    }

    /** @return BelongsTo<Location, $this> */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /** @return BelongsTo<CustomerUser, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerUser::class, 'customer_id');
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
