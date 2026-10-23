<?php

use App\Models\card;
use App\Models\cardDesign;
use App\Models\CustomerUser;
use App\Models\Location;
use App\Models\ShopifyIntegration;
use App\Models\Team;
use App\Models\User;
use Livewire\Livewire;

function shopifySetup(): array
{
    $team = Team::factory()->create(['customer_registration_type' => 'normal']);
    $design = cardDesign::create([
        'team_id' => $team->id,
        'is_active' => true,
        'color_scheme' => [],
        'stamps_required' => 8,
        'reward' => 'Bebida gratis',
    ]);
    $location = Location::create(['team_id' => $team->id, 'name' => 'Local', 'is_active' => true]);
    $customer = CustomerUser::create([
        'email' => 'ana@example.com',
        'password' => 'secret-password',
        'team_id' => $team->id,
        'location_id' => $location->id,
        'guest' => false,
    ]);
    $card = $customer->cards()->create([
        'team_id' => $team->id,
        'card_design_id' => $design->id,
        'stamps_collected' => 0,
        'is_active' => true,
    ]);
    $integration = ShopifyIntegration::create([
        'team_id' => $team->id,
        'shop_domain' => 'mitienda.myshopify.com',
        'webhook_token' => 'token-123',
        'webhook_secret' => 'shpss_secret',
        'stamps_per_order' => 2,
        'min_order_amount' => '10.00',
    ]);

    return [$team, $card, $integration];
}

function shopifyPost(array $order, array $headers = [], string $secret = 'shpss_secret')
{
    $body = json_encode($order);
    $server = array_merge([
        'HTTP_X_SHOPIFY_HMAC_SHA256' => base64_encode(hash_hmac('sha256', $body, $secret, true)),
        'HTTP_X_SHOPIFY_SHOP_DOMAIN' => 'mitienda.myshopify.com',
        'HTTP_X_SHOPIFY_TOPIC' => 'orders/paid',
        'CONTENT_TYPE' => 'application/json',
    ], $headers);

    return test()->call('POST', route('webhooks.shopify', 'token-123'), [], [], [], $server, $body);
}

test('a paid order adds the configured stamps and is idempotent', function () {
    [, $card] = shopifySetup();
    $order = ['id' => 1001, 'email' => 'ANA@example.com', 'total_price' => '25.00'];

    shopifyPost($order)->assertOk()->assertJson(['status' => 'stamped']);
    shopifyPost($order)->assertOk()->assertJson(['status' => 'duplicate']);

    expect($card->fresh()->stamps_collected)->toBe(2);
});

test('orders below the minimum amount or from unknown customers are ignored', function () {
    [, $card] = shopifySetup();

    shopifyPost(['id' => 1, 'email' => 'ana@example.com', 'total_price' => '5.00'])
        ->assertJson(['status' => 'ignored_below_minimum']);
    shopifyPost(['id' => 2, 'email' => 'nadie@example.com', 'total_price' => '50.00'])
        ->assertJson(['status' => 'ignored_no_customer']);

    expect($card->fresh()->stamps_collected)->toBe(0);
});

test('invalid signatures and wrong shop domains are rejected', function () {
    [, $card] = shopifySetup();
    $order = ['id' => 3, 'email' => 'ana@example.com', 'total_price' => '50.00'];

    shopifyPost($order, [], 'otra-clave')->assertUnauthorized();
    shopifyPost($order, ['HTTP_X_SHOPIFY_SHOP_DOMAIN' => 'otra.myshopify.com'])->assertUnauthorized();
    test()->postJson(route('webhooks.shopify', 'token-inexistente'), $order)->assertNotFound();

    expect($card->fresh()->stamps_collected)->toBe(0);
});

test('other topics are ignored', function () {
    [, $card] = shopifySetup();

    shopifyPost(['id' => 4, 'email' => 'ana@example.com', 'total_price' => '50.00'], ['HTTP_X_SHOPIFY_TOPIC' => 'orders/create'])
        ->assertJson(['status' => 'ignored_topic']);

    expect($card->fresh()->stamps_collected)->toBe(0);
});

test('an admin can connect and disconnect shopify from settings', function () {
    $team = Team::factory()->create();
    $user = User::factory()->create();
    $team->members()->attach($user, ['role' => 'owner']);
    $user->switchTeam($team);
    $this->actingAs($user);

    Livewire::test('pages::dashboard.configuracion')
        ->set('shopDomain', 'https://MiTienda.myshopify.com/')
        ->set('webhookSecret', 'shpss_abcdefgh')
        ->set('stampsPerOrder', 3)
        ->call('saveShopify')
        ->assertHasNoErrors();

    $integration = $team->fresh()->shopifyIntegration;
    expect($integration->shop_domain)->toBe('mitienda.myshopify.com')
        ->and($integration->webhook_secret)->toBe('shpss_abcdefgh')
        ->and($integration->stamps_per_order)->toBe(3)
        ->and($integration->webhook_token)->toHaveLength(48);

    Livewire::test('pages::dashboard.configuracion')
        ->set('shopDomain', 'no-es-shopify.com')
        ->call('saveShopify')
        ->assertHasErrors('shopDomain');

    Livewire::test('pages::dashboard.configuracion')->call('disconnectShopify');

    expect($team->fresh()->shopifyIntegration)->toBeNull();
});

test('a plain member cannot change the shopify integration', function () {
    $team = Team::factory()->create();
    $user = User::factory()->create();
    $team->members()->attach($user, ['role' => 'member']);
    $user->switchTeam($team);
    $this->actingAs($user);

    Livewire::test('pages::dashboard.configuracion')
        ->set('shopDomain', 'mitienda.myshopify.com')
        ->set('webhookSecret', 'shpss_abcdefgh')
        ->call('saveShopify')
        ->assertForbidden();
});


function oauthAdmin(): array
{
    config(['services.shopify.client_id' => 'client-id', 'services.shopify.client_secret' => 'app-secret']);
    $team = Team::factory()->create(['customer_registration_type' => 'normal']);
    $user = User::factory()->create();
    $team->members()->attach($user, ['role' => 'owner']);
    $user->switchTeam($team);

    return [$team, $user];
}

test('connect redirects to the shopify authorize page with the app scopes', function () {
    [, $user] = oauthAdmin();

    $response = $this->actingAs($user)->get(route('shopify.connect', ['shop' => 'https://MiTienda.myshopify.com/']));

    $location = $response->headers->get('Location');
    expect($location)->toStartWith('https://mitienda.myshopify.com/admin/oauth/authorize?')
        ->and($location)->toContain('client_id=client-id')
        ->and($location)->toContain('scope=read_orders')
        ->and(session('shopify_oauth.shop'))->toBe('mitienda.myshopify.com');
});

test('connect rejects non shopify domains and non admins', function () {
    [$team, $user] = oauthAdmin();

    $this->actingAs($user)->get(route('shopify.connect', ['shop' => 'evil.com']))
        ->assertRedirect()->assertSessionHas('shopify_error');

    $member = User::factory()->create();
    $team->members()->attach($member, ['role' => 'member']);
    $member->switchTeam($team);
    $this->actingAs($member)->get(route('shopify.connect', ['shop' => 'mitienda.myshopify.com']))->assertForbidden();
});

test('the callback stores the token, creates webhooks and signs with the app secret', function () {
    [$team, $user] = oauthAdmin();
    Illuminate\Support\Facades\Http::fake([
        'mitienda.myshopify.com/admin/oauth/access_token' => Illuminate\Support\Facades\Http::response(['access_token' => 'shpat_token']),
        'mitienda.myshopify.com/admin/api/*' => Illuminate\Support\Facades\Http::response(['webhook' => ['id' => 1]], 201),
    ]);

    $this->actingAs($user)->get(route('shopify.connect', ['shop' => 'mitienda.myshopify.com']));
    $state = session('shopify_oauth.state');

    $params = ['code' => 'abc', 'shop' => 'mitienda.myshopify.com', 'state' => $state, 'timestamp' => '1700000000'];
    ksort($params);
    $params['hmac'] = hash_hmac('sha256', collect($params)->map(fn($v, $k) => "$k=$v")->implode('&'), 'app-secret');

    $this->actingAs($user)->get(route('shopify.callback', $params))
        ->assertRedirect()->assertSessionHas('shopify_status');

    $integration = $team->fresh()->shopifyIntegration;
    expect($integration->usesOAuth())->toBeTrue()
        ->and($integration->access_token)->toBe('shpat_token')
        ->and($integration->signingSecret())->toBe('app-secret');

    Illuminate\Support\Facades\Http::assertSent(fn($request) => str_contains($request->url(), '/webhooks.json')
        && $request['webhook']['topic'] === 'orders/paid'
        && $request->hasHeader('X-Shopify-Access-Token', 'shpat_token'));
});

test('the callback rejects a bad hmac or state', function () {
    [$team, $user] = oauthAdmin();
    Illuminate\Support\Facades\Http::fake();

    $this->actingAs($user)->get(route('shopify.connect', ['shop' => 'mitienda.myshopify.com']));

    $this->actingAs($user)->get(route('shopify.callback', [
        'code' => 'abc', 'shop' => 'mitienda.myshopify.com', 'state' => session('shopify_oauth.state'), 'hmac' => 'falso',
    ]))->assertSessionHas('shopify_error');

    expect($team->fresh()->shopifyIntegration)->toBeNull();
    Illuminate\Support\Facades\Http::assertNothingSent();
});

test('oauth integrations verify webhooks with the app secret and uninstall removes them', function () {
    [$team] = oauthAdmin();
    $integration = ShopifyIntegration::create([
        'team_id' => $team->id,
        'shop_domain' => 'mitienda.myshopify.com',
        'webhook_token' => 'token-oauth',
        'access_token' => 'shpat_token',
    ]);
    $body = json_encode(['id' => 9]);
    $server = [
        'HTTP_X_SHOPIFY_HMAC_SHA256' => base64_encode(hash_hmac('sha256', $body, 'app-secret', true)),
        'HTTP_X_SHOPIFY_SHOP_DOMAIN' => 'mitienda.myshopify.com',
        'HTTP_X_SHOPIFY_TOPIC' => 'app/uninstalled',
        'CONTENT_TYPE' => 'application/json',
    ];

    $this->call('POST', route('webhooks.shopify', 'token-oauth'), [], [], [], $server, $body)
        ->assertOk()->assertJson(['status' => 'uninstalled']);

    expect(ShopifyIntegration::count())->toBe(0);
});

test('privacy webhooks require a valid signature and shop-redact removes the integration', function () {
    [$team] = oauthAdmin();
    ShopifyIntegration::create([
        'team_id' => $team->id,
        'shop_domain' => 'mitienda.myshopify.com',
        'webhook_token' => 'token-priv',
        'access_token' => 'shpat_token',
    ]);
    $body = json_encode(['shop_domain' => 'mitienda.myshopify.com']);
    $url = route('webhooks.shopify.privacy', 'shop-redact');

    $this->call('POST', $url, [], [], [], ['HTTP_X_SHOPIFY_HMAC_SHA256' => 'mal', 'CONTENT_TYPE' => 'application/json'], $body)
        ->assertUnauthorized();
    expect(ShopifyIntegration::count())->toBe(1);

    $signature = base64_encode(hash_hmac('sha256', $body, 'app-secret', true));
    $this->call('POST', $url, [], [], [], ['HTTP_X_SHOPIFY_HMAC_SHA256' => $signature, 'CONTENT_TYPE' => 'application/json'], $body)
        ->assertOk();
    expect(ShopifyIntegration::count())->toBe(0);
});
