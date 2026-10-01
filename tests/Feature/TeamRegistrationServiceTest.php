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
        'registro_usuarios' => ['modo' => 'normal'],
        'paleta' => ['slug' => 'cacao-clasico'],
        'sellos' => 8,
        'recompensa' => 'Bebida gratis',
    ];
    $service = new TeamRegistrationService;

    $service->setup($user, $registration);
    $service->setup($user, $registration);

    expect(Location::where('team_id', $user->currentTeam->id)->count())->toBe(1)
        ->and(CardDesign::where('team_id', $user->currentTeam->id)->count())->toBe(1);
});
