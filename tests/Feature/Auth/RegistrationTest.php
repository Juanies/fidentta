<?php

test('registration route renders the custom onboarding', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('Fortify registration submission is no longer available', function () {
    $this->post('/register')->assertStatus(405);
});
