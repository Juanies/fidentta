<?php

use App\Models\CustomerRegistrationField;
use App\Models\CustomerRegistrationValue;
use App\Models\Location;
use App\Models\Team;
use App\Models\cardDesign;
use App\Models\CustomerUser;
use App\Models\card;

function createRegistrationLocation(string $mode): Location
{
    $team = Team::factory()->create(['customer_registration_type' => $mode]);

    cardDesign::create([
        'team_id' => $team->id,
        'is_active' => true,
        'color_scheme' => [],
        'stamps_required' => 8,
        'reward' => 'Bebida gratis',
    ]);

    return Location::create([
        'team_id' => $team->id,
        'name' => 'Local de prueba',
        'is_active' => true,
    ]);
}

test('none mode creates a guest customer without personal data and one card', function () {
    $location = createRegistrationLocation('none');

    $response = $this->post(route('wallet.card.store', $location->qr_token));
    $response->assertOk()->assertViewIs('wallet.card-created');

    $customer = CustomerUser::where('team_id', $location->team_id)->firstOrFail();

    expect($customer->email)->toBeNull()
        ->and($customer->password)->toBeNull()
        ->and($customer->guest)->toBeTrue()
        ->and($customer->location_id)->toBe($location->id)
        ->and(card::where('customer_id', $customer->id)->count())->toBe(1);

    $this->post(route('wallet.card.store', $location->qr_token));
    expect(CustomerUser::where('team_id', $location->team_id)->count())->toBe(1)
        ->and(card::where('team_id', $location->team_id)->count())->toBe(1);
});

test('normal mode requires email and confirmed password and creates a card', function () {
    $location = createRegistrationLocation('normal');

    $response = $this->post(route('wallet.card.store', $location->qr_token), [
        'email' => 'customer@example.com',
        'password' => 'secret-password',
        'password_confirmation' => 'secret-password',
    ]);

    $response->assertOk()->assertViewIs('wallet.card-created');

    $customer = CustomerUser::where('team_id', $location->team_id)->firstOrFail();
    expect($customer->email)->toBe('customer@example.com')
        ->and($customer->guest)->toBeFalse()
        ->and(password_verify('secret-password', $customer->password))->toBeTrue()
        ->and(card::where('customer_id', $customer->id)->exists())->toBeTrue();
});

test('custom mode stores configured customer fields and creates a card', function () {
    $location = createRegistrationLocation('custom');
    $field = CustomerRegistrationField::create([
        'team_id' => $location->team_id,
        'field_key' => 'nombre',
        'label' => 'Nombre',
        'type' => 'text',
        'is_required' => true,
        'sort_order' => 0,
        'is_active' => true,
    ]);

    $response = $this->post(route('wallet.card.store', $location->qr_token), [
        'fields' => [$field->id => 'Ada Lovelace'],
    ]);

    $response->assertOk()->assertViewIs('wallet.card-created');

    $customer = CustomerUser::where('team_id', $location->team_id)->firstOrFail();
    expect($customer->guest)->toBeFalse()
        ->and(CustomerRegistrationValue::where('customer_user_id', $customer->id)
            ->where('customer_registration_field_id', $field->id)
            ->value('value'))->toBe('Ada Lovelace')
        ->and(card::where('customer_id', $customer->id)->exists())->toBeTrue();
});
