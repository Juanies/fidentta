<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['team_id', 'is_active', 'color_scheme', 'stamps_required', 'reward'])]

class cardDesign extends Model
{
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function cardsDesigns()
    {
        return $this->hasMany(card::class);
    }

    protected $casts = [
        'color_scheme' => 'array',
    ];
}
