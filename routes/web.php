<?php

use App\Http\Controllers\Card;
use App\Http\Controllers\CardScan;
use App\Http\Controllers\ShopifyOAuth;
use App\Http\Controllers\ShopifyPrivacyWebhook;
use App\Http\Controllers\ShopifyWebhook;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\SubscriptionSuccess;
use App\Http\Middleware\EnsureTeamMembership;
use App\Http\Middleware\isRegistered as IsRegistered;
use App\Http\Middleware\Subscribed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Stripe\Exception\ApiConnectionException;
use Stripe\Exception\ApiErrorException;

Route::livewire('/', 'pages::inicio')->name('home');
Route::livewire('/register', 'pages::register')->name('register')->middleware(IsRegistered::class);

Route::get(
    '/locations/{location}/qr',
    [Card::class, 'qr']
)->name('location.qr');

Route::get(
    '/location/{qr_token}/card',
    [Card::class, 'index']
)->name('wallet.card');

Route::post(
    '/location/{qr_token}/card',
    [Card::class, 'store']
)->name('wallet.card.store');

Route::get(
    '/location/{qr_token}/links',
    [Card::class, 'pageLinks']
)->name('wallet.links');

Route::get('/c/{token}', [CardScan::class, 'show'])->name('wallet.scan');
Route::post('/webhooks/shopify/privacy/{topic}', ShopifyPrivacyWebhook::class)
    ->where('topic', 'customers-data-request|customers-redact|shop-redact')
    ->middleware('throttle:120,1')
    ->name('webhooks.shopify.privacy');
Route::post('/webhooks/shopify/{token}', ShopifyWebhook::class)->middleware('throttle:120,1')->name('webhooks.shopify');
Route::get('/integrations/shopify/connect', [ShopifyOAuth::class, 'connect'])
    ->middleware(['auth', 'verified'])->name('shopify.connect');
Route::get('/integrations/shopify/callback', [ShopifyOAuth::class, 'callback'])
    ->middleware(['auth', 'verified'])->name('shopify.callback');
Route::post('/c/{token}/stamp', [CardScan::class, 'stamp'])->middleware('auth')->name('wallet.scan.stamp');

Route::get('/auth/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class, Subscribed::class])
    ->group(function () {

        Route::livewire('dashboard', 'pages::dashboard.inicio')->name('dashboard');
        Route::livewire('dashboard/clientes', 'pages::dashboard.clientes')->name('dashboard.clientes');
        Route::livewire('dashboard/recompensas', 'pages::dashboard.recompensas')->name('dashboard.recompensas');
        Route::livewire('dashboard/referidos', 'pages::dashboard.referidos')->name('dashboard.referidos');
        Route::livewire('dashboard/fidelizacion', 'pages::dashboard.fidelizacion')->name('dashboard.fidelizacion');
        Route::livewire('dashboard/tarjeta', 'pages::dashboard.tarjeta')->name('dashboard.tarjeta');
        Route::livewire('dashboard/configuracion', 'pages::dashboard.configuracion')->name('dashboard.configuracion');
        Route::livewire('dashboard/locales', 'pages::dashboard.locales')->name('dashboard.locales');
        Route::livewire('dashboard/locales/{id}', 'pages::dashboard.location.show')->name('locations.index');
    });

Route::get('/subscription/success', [
    SubscriptionSuccess::class,
    'success',
])->middleware('auth')->name('subscription.success');

Route::get('/subscription-checkout', function (Request $request) {
    $priceId = config('cashier.price_id');

    if (! is_string($priceId) || $priceId === '') {
        report(new RuntimeException('Stripe price ID is not configured.'));

        return redirect()->route('home')->with(
            'stripe_checkout_error',
            'El servicio de pago no está configurado. Inténtalo de nuevo más tarde.',
        );
    }

    try {
        return $request->user()
            ->newSubscription('fidentta', $priceId)
            ->trialDays(5)
            ->allowPromotionCodes()
            ->checkout([
                'success_url' => route('subscription.success'),
            ]);
    } catch (ApiConnectionException|ApiErrorException $exception) {
        report($exception);

        return redirect()->route('home')->with(
            'stripe_checkout_error',
            'No se pudo iniciar el proceso de pago. No se ha completado ningún cobro. Inténtalo de nuevo en unos minutos.',
        );
    }
})->middleware(['auth', IsRegistered::class])->name('subscription-checkout');

Route::mobilePass();

require __DIR__.'/settings.php';
