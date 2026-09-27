<?php

use App\Http\Middleware\EnsureTeamMembership;
use App\Http\Controllers\GoogleAuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Middleware\Subscribed;
use App\Http\Middleware\isRegistered;
use App\Http\Controllers\Card;


Route::livewire('/', 'pages::inicio')->name('home');
Route::livewire('/registera', 'pages::register')->name('registera')->middleware(isRegistered::class);

Route::get('/wallet/card', [Card::class, 'index'])->name("wallet.card");


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
    });


Route::get('/subscription-checkout', function (Request $request) {
    return $request->user()
        ->newSubscription('fidentta', 'price_1UJfmrFW2Hwxc0Uk8mZTPfN1')
        ->trialDays(5)
        ->allowPromotionCodes()
        ->checkout();
})->middleware([App\Http\Middleware\isRegistered::class])->name('subscription-checkout');;


Route::mobilePass();


require __DIR__ . '/settings.php';
