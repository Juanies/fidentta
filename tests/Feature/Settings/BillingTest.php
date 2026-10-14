<?php

use App\Models\User;

test('guests are redirected from billing settings', function () {
    $this->get(route('billing.edit'))
        ->assertRedirect(route('login'));
});

test('billing settings show the current subscription and previous invoices section', function () {
    $user = User::factory()->create();
    $user->subscriptions()->create([
        'type' => 'fidentta',
        'stripe_id' => 'sub_billing_test_' . uniqid(),
        'stripe_status' => 'active',
    ]);

    $this->actingAs($user)
        ->get(route('billing.edit'))
        ->assertOk()
        ->assertSee('Suscripción actual')
        ->assertSee('Activa')
        ->assertSee('Cancelar suscripción')
        ->assertSee('Facturas anteriores')
        ->assertSee('Todavía no hay facturas pagadas.');
});
