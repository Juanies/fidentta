<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\LaravelMobilePass\Models\Concerns\HasMobilePasses;

#[Fillable(['email', 'password', 'team_id', 'location_id', 'guest'])]
class CustomerUser extends Model
{
    use HasMobilePasses;

    // customeruser pertenece a un negocio que es team
    // tambien hay que saber la ubicacion de que local
    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /** @return BelongsTo<Location, $this> */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    /** @return HasMany<card, $this> */
    public function cards(): HasMany
    {
        return $this->hasMany(card::class, 'customer_id');
    }

    /** @return HasMany<CustomerRegistrationValue, $this> */
    public function registrationValues(): HasMany
    {
        return $this->hasMany(CustomerRegistrationValue::class);
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'guest' => 'boolean',
        ];
    }
}
