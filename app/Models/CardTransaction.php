<?php

namespace App\Models;

use Database\Factories\CardTransactionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['card_id', 'customer_id', 'location_id', 'stamps_added', 'user_id'])]

class CardTransaction extends Model
{
    /** @use HasFactory<CardTransactionFactory> */
    use HasFactory;

    public function card()
    {
        return $this->belongsTo(card::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function customer()
    {
        return $this->belongsTo(CustomerUser::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
