<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['card_id', 'location_id','stamps_added', 'user_id'])]

class CardTransaction extends Model
{
    /** @use HasFactory<\Database\Factories\CardTransactionFactory> */
    use HasFactory;

    public function cards(){
        return $this->belongsToMany(card::class);
    }
    public function Location(){
        return $this->belongsToMany(Location::class);
    }
}
