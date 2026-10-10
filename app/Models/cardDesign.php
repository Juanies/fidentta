<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['team_id', 'is_active', 'color_scheme', 'stamps_required', 'reward'])]

class cardDesign extends Model
{
    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /** @return HasMany<card, $this> */
    public function cardsDesigns(): HasMany
    {
        return $this->hasMany(card::class);
    }

    protected $casts = [
        'color_scheme' => 'array',
    ];
}
