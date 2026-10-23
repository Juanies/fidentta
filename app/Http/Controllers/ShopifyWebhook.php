<?php

namespace App\Http\Controllers;

use App\Models\ShopifyIntegration;
use App\Services\ShopifyOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShopifyWebhook extends Controller
{
    public function __invoke(Request $request, string $token, ShopifyOrderService $orders): JsonResponse
    {
        $integration = ShopifyIntegration::query()
            ->where('webhook_token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        $secret = $integration->signingSecret();
        $expected = base64_encode(hash_hmac('sha256', $request->getContent(), $secret, true));
        $received = (string) $request->header('X-Shopify-Hmac-Sha256');

        abort_unless($secret !== '' && hash_equals($expected, $received), 401);
        abort_unless(
            strtolower((string) $request->header('X-Shopify-Shop-Domain')) === strtolower($integration->shop_domain),
            401,
        );

        if ($request->header('X-Shopify-Topic') === 'app/uninstalled') {
            $integration->delete();

            return response()->json(['status' => 'uninstalled']);
        }

        if ($request->header('X-Shopify-Topic') !== 'orders/paid') {
            return response()->json(['status' => 'ignored_topic']);
        }

        $payload = json_decode($request->getContent(), true);

        abort_unless(is_array($payload), 400);

        return response()->json(['status' => $orders->handle($integration, $payload)]);
    }
}
