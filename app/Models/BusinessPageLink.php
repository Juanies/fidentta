<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['url', 'icon', 'name', 'team_id', 'location_id'])]
class BusinessPageLink extends Model {}
