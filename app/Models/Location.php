<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['id', 'team_id', 'name', 'address', 'is_active', 'long', 'lat'])]

class Location extends Model
{
    /** @use HasFactory<\Database\Factories\LocationFactory> */
    use HasFactory;

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function CardTransactions()
    {
        return $this->hasMany(CardTransaction::class);
    }

    public function customerusers(){
        return $this->hasMany(CustomerUser::class);
    }
}
