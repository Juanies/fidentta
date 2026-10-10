<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(["url", "icon", "name", "team_id", "location_id"])]
class BusinessPageLink extends Model
{
}
