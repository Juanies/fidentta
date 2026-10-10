<?php

namespace App\Models;

use Database\Factories\LocationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['id', 'team_id', 'name', 'page_link_name', 'qr_token', 'address', 'is_active', 'long', 'lat'])]

class Location extends Model
{
    /** @use HasFactory<LocationFactory> */
    use HasFactory;

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /** @return HasMany<CardTransaction, $this> */
    public function cardTransactions(): HasMany
    {
        return $this->hasMany(CardTransaction::class);
    }

    /** @return HasMany<CustomerUser, $this> */
    public function customerUsers(): HasMany
    {
        return $this->hasMany(CustomerUser::class);
    }

    /** @return HasMany<BusinessPageLink, $this> */
    public function businessPageLinks(): HasMany
    {
        return $this->hasMany(BusinessPageLink::class)->orderBy('id');
    }

    protected static function booted(): void
    {
        static::creating(function ($location) {
            $location->qr_token ??= (string) Str::uuid();
        });
    }
}
