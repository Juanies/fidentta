<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['customer_user_id', 'customer_registration_field_id', 'value'])]
class CustomerRegistrationValue extends Model
{
    public function customerUser()
    {
        return $this->belongsTo(CustomerUser::class);
    }

    public function field()
    {
        return $this->belongsTo(CustomerRegistrationField::class, 'customer_registration_field_id');
    }
}
