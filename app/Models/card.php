<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable([ 'team_id', 'card_design_id', 'stamps_collected', 'is_active', 'card_type'])]

class card extends Model
{

    public function team(){
        return $this->belongsTo(Team::class);
    }

    public function cardDesign(){
        return $this->belongsTo(cardDesign::class);
    }

    public function CardTransactions(){
        return $this->hasMany(CardTransaction::class);
    }
}
