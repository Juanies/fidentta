<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelMobilePass\Models\Concerns\HasMobilePasses;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['email', 'password', 'team_id', 'location_id', 'guest'])]
class CustomerUser extends Model
{
    use HasMobilePasses;

    // customeruser pertenece a un negocio que es team
    // tambien hay que saber la ubicacion de que local
    public function team()
    {
        return $this->belongsTo(Team::class);
    }




    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function cards()
    {
        return $this->hasMany(card::class, 'customer_id');
    }

    public function registrationValues()
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
