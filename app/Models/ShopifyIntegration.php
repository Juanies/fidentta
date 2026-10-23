<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['team_id', 'shop_domain', 'webhook_token', 'webhook_secret', 'access_token', 'stamps_per_order', 'min_order_amount', 'is_active'])]
#[Hidden(['webhook_secret', 'access_token'])]
class ShopifyIntegration extends Model
{
    protected function casts(): array
    {
        return [
            'webhook_secret' => 'encrypted',
            'access_token' => 'encrypted',
            'min_order_amount' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /** Conectada por OAuth: los webhooks se firman con el secreto de la app. */
    public function usesOAuth(): bool
    {
        return $this->access_token !== null;
    }

    public function signingSecret(): string
    {
        return $this->usesOAuth()
            ? (string) config('services.shopify.client_secret')
            : (string) $this->webhook_secret;
    }

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
