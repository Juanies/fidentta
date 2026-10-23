<?php

namespace App\Http\Controllers;

use App\Models\ShopifyIntegration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShopifyPrivacyWebhook extends Controller
{
    public function __invoke(Request $request, string $topic): JsonResponse
    {
        $secret = (string) config('services.shopify.client_secret');
        $expected = base64_encode(hash_hmac('sha256', $request->getContent(), $secret, true));

        abort_unless($secret !== '' && hash_equals($expected, (string) $request->header('X-Shopify-Hmac-Sha256')), 401);

        // Solo guardamos el id del pedido y la tarjeta, sin datos personales de Shopify.
        if ($topic === 'shop-redact') {
            $shop = strtolower((string) $request->input('shop_domain'));
            ShopifyIntegration::where('shop_domain', $shop)->delete();
        }

        return response()->json(['status' => 'ok']);
    }
}
