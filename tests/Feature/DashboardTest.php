<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $user = User::factory()->create();
    $team = $user->currentTeam;

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users without a subscription are redirected to checkout', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('dashboard'));

    $response->assertRedirect(route('subscription-checkout'));
});

test('subscribed users can visit the dashboard', function () {
    $user = User::factory()->create();
    $user->subscriptions()->create([
        'type' => 'fidentta',
        'stripe_id' => 'sub_test_' . uniqid(),
        'stripe_status' => 'active',
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('dashboard'));

    $response->assertOk();
});
