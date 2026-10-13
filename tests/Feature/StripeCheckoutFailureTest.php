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

test('checkout returns a safe error when the Stripe price is missing', function () {
    config(['cashier.price_id' => null]);

    $this->actingAs(User::factory()->create())
        ->get(route('subscription-checkout'))
        ->assertRedirect(route('home'))
        ->assertSessionHas(
            'stripe_checkout_error',
            'El servicio de pago no está configurado. Inténtalo de nuevo más tarde.',
        );
});
