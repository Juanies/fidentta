<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['customer_user_id', 'customer_registration_field_id', 'value'])]
class CustomerRegistrationValue extends Model
{
    /** @return BelongsTo<CustomerUser, $this> */
    public function customerUser(): BelongsTo
    {
        return $this->belongsTo(CustomerUser::class);
    }

    /** @return BelongsTo<CustomerRegistrationField, $this> */
    public function field(): BelongsTo
    {
        return $this->belongsTo(CustomerRegistrationField::class, 'customer_registration_field_id');
    }
}
