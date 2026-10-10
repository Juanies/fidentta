<?php

use App\Actions\Teams\CreateTeam;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('subscription success returns the owner to their current team dashboard', function () {
    $user = User::create([
        'name' => 'MVP Business',
        'email' => 'mvp-owner@example.com',
        'password' => Hash::make('password'),
    ]);
    $team = app(CreateTeam::class)->handle($user, 'MVP Business');
    $user->refresh();

    $response = $this->actingAs($user)->get(route('subscription.success'));

    $response->assertRedirect(route('dashboard', ['current_team' => $team->slug]));
    expect(Location::where('team_id', $team->id)->count())->toBe(1);
});
