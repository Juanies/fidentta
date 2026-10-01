<?php

use App\Actions\Teams\CreateTeam;
use App\Models\CustomerUser;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

function makeDashboardFilterUser(): User
{
    $user = User::create([
        'name' => 'Dashboard owner',
        'email' => 'dashboard-owner@example.com',
        'password' => Hash::make('password'),
    ]);

    app(CreateTeam::class)->handle($user, 'Dashboard test team');

    return $user->fresh();
}

test('customers page filters customer records by the selected location', function () {
    $user = makeDashboardFilterUser();
    $team = $user->currentTeam;
    $firstLocation = Location::create([
        'team_id' => $team->id,
        'name' => 'Local Norte',
        'is_active' => true,
    ]);
    $secondLocation = Location::create([
        'team_id' => $team->id,
        'name' => 'Local Sur',
        'is_active' => true,
    ]);
    CustomerUser::create([
        'team_id' => $team->id,
        'location_id' => $firstLocation->id,
        'email' => 'norte@example.com',
        'guest' => false,
    ]);
    CustomerUser::create([
        'team_id' => $team->id,
        'location_id' => $secondLocation->id,
        'email' => 'sur@example.com',
        'guest' => false,
    ]);

    $this->actingAs($user);

    Livewire::test('pages::dashboard.clientes')
        ->assertSee('norte@example.com')
        ->assertSee('sur@example.com')
        ->set('locationId', (string) $firstLocation->id)
        ->assertSee('norte@example.com')
        ->assertDontSee('sur@example.com');
});

test('dashboard page renders its live metrics and activity with a location filter', function () {
    $user = makeDashboardFilterUser();
    $team = $user->currentTeam;
    $location = Location::create([
        'team_id' => $team->id,
        'name' => 'Local Centro',
        'is_active' => true,
    ]);

    $this->actingAs($user);

    Livewire::test('pages::dashboard.inicio')
        ->assertSee('Sellos entregados por día')
        ->set('locationId', (string) $location->id)
        ->assertSee('Local Centro');
});
