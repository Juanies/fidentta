<?php

use App\Models\User;

test('home shows a safe Stripe checkout error and retry link', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->withSession([
            'stripe_checkout_error' => 'No se pudo iniciar el proceso de pago.',
        ])
        ->get(route('home'))
        ->assertOk()
        ->assertSee('No se pudo iniciar el proceso de pago.')
        ->assertSee(route('subscription-checkout'), false)
        ->assertSee('Intentar de nuevo');
});
