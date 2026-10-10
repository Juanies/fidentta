<?php

use App\Http\Middleware\EnsureTeamMembership;
use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Middleware\Subscribed;
use App\Http\Middleware\isRegistered as IsRegistered;
use App\Http\Controllers\Card;
use App\Http\Controllers\SubscriptionSuccess;



Route::livewire('/', 'pages::inicio')->name('home');
Route::livewire('/registera', 'pages::register')->name('registera')->middleware(IsRegistered::class);

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

Route::get('/auth/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class, Subscribed::class])
    ->group(function () {

        Route::livewire('dashboard', 'pages::dashboard.inicio')->name('dashboard');
        Route::livewire('dashboard/clientes', 'pages::dashboard.clientes')->name('dashboard.clientes');
        Route::livewire('dashboard/fidelizacion', 'pages::dashboard.fidelizacion')->name('dashboard.fidelizacion');
        Route::livewire('dashboard/tarjeta', 'pages::dashboard.tarjeta')->name('dashboard.tarjeta');
        Route::livewire('dashboard/configuracion', 'pages::dashboard.configuracion')->name('dashboard.configuracion');
        Route::livewire('dashboard/locales', 'pages::dashboard.locales')->name('dashboard.locales');
        Route::livewire('dashboard/locales/{id}', 'pages::dashboard.location.show')->name('locations.index');
    });


Route::get('/subscription/success', [
    SubscriptionSuccess::class,
    'success'
])->middleware('auth')->name('subscription.success');

Route::get('/subscription-checkout', function (Request $request) {
    return $request->user()
        ->newSubscription('fidentta', 'price_1UJfmrFW2Hwxc0Uk8mZTPfN1')
        ->trialDays(5)
        ->allowPromotionCodes()
        ->checkout([
            'success_url' => route('subscription.success'),

        ]);
})->middleware(['auth', IsRegistered::class])->name('subscription-checkout');


Route::mobilePass();


require __DIR__ . '/settings.php';
