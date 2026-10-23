<?php

use App\Models\card;
use App\Models\cardDesign;
use App\Models\CardTransaction;
use App\Models\CustomerUser;
use App\Models\Location;
use App\Models\Referral;
use App\Models\Team;
use App\Models\User;

function referralSetup(): array
{
    $team = Team::factory()->create(['customer_registration_type' => 'normal']);
    $design = cardDesign::create([
        'team_id' => $team->id,
        'is_active' => true,
        'color_scheme' => [],
        'stamps_required' => 8,
        'reward' => 'Bebida gratis',
    ]);
    $location = Location::create(['team_id' => $team->id, 'name' => 'Local', 'is_active' => true]);
    $customer = CustomerUser::create([
        'email' => 'ana@example.com',
        'password' => 'secret-password',
        'team_id' => $team->id,
        'location_id' => $location->id,
        'guest' => false,
    ]);
    $card = $customer->cards()->create([
        'team_id' => $team->id,
        'card_design_id' => $design->id,
        'stamps_collected' => 0,
        'is_active' => true,
    ]);

    return [$team, $location, $card];
}

function registerReferred(Location $location, string $email = 'luis@example.com')
{
    return test()->post(route('wallet.card.store', $location->qr_token), [
        'email' => $email,
        'password' => 'secret-password',
        'password_confirmation' => 'secret-password',
    ]);
}

test('new cards get a referral token', function () {
    [, , $card] = referralSetup();

    expect($card->referral_token)->toHaveLength(32);
});

test('anonymous visitors see the invitation to get a card', function () {
    [, $location, $card] = referralSetup();

    $this->get(route('wallet.scan', $card->referral_token))
        ->assertOk()
        ->assertViewIs('wallet.scan-invite')
        ->assertSee(route('wallet.card', ['qr_token' => $location->qr_token, 'ref' => $card->referral_token]), false);
});

test('a team member sees the stamp page and can add a stamp', function () {
    [$team, , $card] = referralSetup();
    $user = User::factory()->create();
    $team->members()->attach($user, ['role' => 'admin']);

    $this->actingAs($user)->get(route('wallet.scan', $card->referral_token))
        ->assertOk()
        ->assertViewIs('wallet.scan-staff');

    $key = (string) Illuminate\Support\Str::uuid();
    $url = route('wallet.scan.stamp', $card->referral_token);

    $this->actingAs($user)->post($url, ['request_key' => $key])->assertRedirect();
    $this->actingAs($user)->post($url, ['request_key' => $key])->assertRedirect();

    expect($card->fresh()->stamps_collected)->toBe(1);
});

test('a user from another team cannot stamp the card', function () {
    [, , $card] = referralSetup();
    $stranger = User::factory()->create();

    $this->actingAs($stranger)->get(route('wallet.scan', $card->referral_token))
        ->assertViewIs('wallet.scan-invite');
    $this->actingAs($stranger)
        ->post(route('wallet.scan.stamp', $card->referral_token), ['request_key' => (string) Illuminate\Support\Str::uuid()])
        ->assertForbidden();

    expect($card->fresh()->stamps_collected)->toBe(0);
});

test('registering through a referral link gives a stamp to both cards', function () {
    [, $location, $card] = referralSetup();

    $this->get(route('wallet.card', ['qr_token' => $location->qr_token, 'ref' => $card->referral_token]));
    registerReferred($location)->assertOk();

    $newCard = card::where('customer_id', '!=', $card->customer_id)->firstOrFail();

    expect($newCard->stamps_collected)->toBe(1)
        ->and($card->fresh()->stamps_collected)->toBe(1)
        ->and(Referral::where('referrer_card_id', $card->id)->where('referred_card_id', $newCard->id)->exists())->toBeTrue()
        ->and(CardTransaction::whereNull('user_id')->count())->toBe(2);
});

test('registering without a referral link gives no bonus', function () {
    [, $location, $card] = referralSetup();

    registerReferred($location)->assertOk();

    expect($card->fresh()->stamps_collected)->toBe(0)
        ->and(Referral::count())->toBe(0);
});

test('invalid or self referrals are ignored', function () {
    [, $location, $card] = referralSetup();

    $this->get(route('wallet.card', ['qr_token' => $location->qr_token, 'ref' => 'no-existe']));
    registerReferred($location, 'otro@example.com')->assertOk();

    expect(Referral::count())->toBe(0);
});
