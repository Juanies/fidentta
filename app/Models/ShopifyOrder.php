<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['shopify_integration_id', 'shopify_order_id', 'card_id', 'stamps_added'])]
class ShopifyOrder extends Model {}
