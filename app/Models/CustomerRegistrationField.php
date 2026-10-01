<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['team_id', 'field_key', 'label', 'type', 'is_required', 'sort_order', 'is_active'])]
class CustomerRegistrationField extends Model
{
    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function values()
    {
        return $this->hasMany(CustomerRegistrationValue::class);
    }
}
