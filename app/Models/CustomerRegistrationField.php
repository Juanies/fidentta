<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /** @return HasMany<CustomerRegistrationValue, $this> */
    public function values(): HasMany
    {
        return $this->hasMany(CustomerRegistrationValue::class);
    }
}
