<?php

use App\Models\Location;
use App\Models\cardDesign as CardDesign;
use App\Models\User;
use App\Actions\Teams\CreateTeam;
use App\Services\TeamRegistrationService;
use Illuminate\Support\Facades\Hash;

test('team setup can be run repeatedly without creating duplicate locations or card designs', function () {
    $user = User::create([
        'name' => 'Test owner',
        'email' => 'owner@example.com',
        'password' => Hash::make('password'),
    ]);
    app(CreateTeam::class)->handle($user, 'Test owner team');
    $user->refresh();
    $registration = [
        'name' => 'Café Paso Uno',
        'registro_usuarios' => ['modo' => 'normal'],
        'logo' => ['type' => 'img', 'url' => '/storage/photos/logo.png'],
        'paleta' => ['slug' => 'cacao-clasico'],
        'sellos' => 8,
        'recompensa' => 'Bebida gratis',
    ];
    $service = new TeamRegistrationService;

    $service->setup($user, $registration);
    $service->setup($user, $registration);

    $team = $user->currentTeam->fresh();
    $design = CardDesign::where('team_id', $team->id)->firstOrFail();

    expect(Location::where('team_id', $team->id)->count())->toBe(1)
        ->and($team->name)->toBe('Café Paso Uno')
        ->and($team->slug)->toStartWith('cafe-paso-uno')
        ->and($team->logo)->toBe('/storage/photos/logo.png')
        ->and($team->customer_registration_type)->toBe('normal')
        ->and($design->stamps_required)->toBe(8)
        ->and($design->reward)->toBe('Bebida gratis')
        ->and($design->color_scheme)->toBe(['slug' => 'cacao-clasico']);
});
