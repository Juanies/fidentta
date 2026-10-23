<?php

namespace App\Services;

use App\Models\CustomerUser;
use App\Models\Location;
use App\Models\ShopifyIntegration;
use App\Models\ShopifyOrder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class ShopifyOrderService
{
    public function __construct(private AddStampsToCard $stamps) {}

    /** @param array<string, mixed> $order Payload de orders/paid. */
    public function handle(ShopifyIntegration $integration, array $order): string
    {
        $orderId = (string) ($order['id'] ?? '');
        $email = strtolower(trim((string) ($order['email'] ?? $order['customer']['email'] ?? '')));

        if ($orderId === '' || $email === '') {
            return 'ignored_no_email';
        }

        $min = $integration->min_order_amount;

        if ($min !== null && (float) ($order['total_price'] ?? 0) < (float) $min) {
            return 'ignored_below_minimum';
        }

        $customer = CustomerUser::query()
            ->where('team_id', $integration->team_id)
            ->where('guest', false)
            ->whereRaw('lower(email) = ?', [$email])
            ->first();

        $card = $customer?->cards()->where('is_active', true)->first();

        if (! $customer || ! $card) {
            return 'ignored_no_customer';
        }

        $location = $customer->location?->is_active
            ? $customer->location
            : Location::where('team_id', $integration->team_id)->where('is_active', true)->first();

        if (! $location) {
            return 'ignored_no_location';
        }

        try {
            DB::transaction(function () use ($integration, $orderId, $card, $location): void {
                ShopifyOrder::create([
                    'shopify_integration_id' => $integration->id,
                    'shopify_order_id' => $orderId,
                    'card_id' => $card->id,
                    'stamps_added' => $integration->stamps_per_order,
                ]);

                $this->stamps->addAutomatic($card, $location, $integration->stamps_per_order);
            });
        } catch (UniqueConstraintViolationException) {
            return 'duplicate';
        }

        return 'stamped';
    }
}
