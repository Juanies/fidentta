<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

use Illuminate\Support\Str;

#[Fillable(['id', 'team_id', 'name', 'qr_token', 'address', 'is_active', 'long', 'lat'])]

class Location extends Model
{
    /** @use HasFactory<\Database\Factories\LocationFactory> */
    use HasFactory;

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function cardTransactions()
    {
        return $this->hasMany(CardTransaction::class);
    }

    public function customerUsers()
    {
        return $this->hasMany(CustomerUser::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($location) {
            $location->qr_token ??= (string) Str::uuid();
        });
    }
}
