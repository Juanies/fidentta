<?php

use App\Actions\Teams\CreateTeam;
use App\Models\CardReward;
use App\Models\CustomerUser;
use App\Models\Location;
use App\Models\User;
use App\Services\AddStampsToCard;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Spatie\LaravelMobilePass\Builders\Apple\StoreCardPassBuilder;
use Spatie\LaravelMobilePass\Enums\FieldType;
use Spatie\LaravelMobilePass\Enums\PassType;
use Spatie\LaravelMobilePass\Enums\Platform;
use Spatie\LaravelMobilePass\Jobs\PushPassUpdateJob;

test('adding a stamp after completing the card starts the next cycle at one', function () {
    $user = User::factory()->create();
    app(CreateTeam::class)->handle($user, 'Rewards test team');
    $team = $user->fresh()->currentTeam;
    $location = Location::create([
        'team_id' => $team->id,
        'name' => 'Main location',
        'is_active' => true,
    ]);
    $design = $team->cardDesign()->create([
        'is_active' => true,
        'color_scheme' => [],
        'stamps_required' => 8,
        'reward' => 'Coffee',
    ]);
    $customer = CustomerUser::create([
        'team_id' => $team->id,
        'location_id' => $location->id,
        'email' => 'rewards-customer@example.com',
        'guest' => false,
    ]);
    $card = $customer->cards()->create([
        'team_id' => $team->id,
        'card_design_id' => $design->id,
        'stamps_collected' => 0,
        'is_active' => true,
    ]);
    $addStamps = app(AddStampsToCard::class);

    $firstStamp = $addStamps->add($card, $user, $location, requestKey: 'same-click-request');
    $duplicateStamp = $addStamps->add($card, $user, $location, requestKey: 'same-click-request');

    expect($duplicateStamp->id)->toBe($firstStamp->id)
        ->and((int) $card->fresh()->stamps_collected)->toBe(1)
        ->and($card->cardTransactions()->count())->toBe(1);

    $unauthorizedUser = User::factory()->create();

    expect(fn() => $addStamps->add($card, $unauthorizedUser, $location, requestKey: 'same-click-request'))
        ->toThrow(AuthorizationException::class);

    foreach (range(2, 8) as $_) {
        $addStamps->add($card, $user, $location);
    }

    $reward = $card->rewards()->sole();

    expect($reward)
        ->toBeInstanceOf(CardReward::class)
        ->and($reward->reward_name)->toBe('Coffee')
        ->and($reward->delivered_at)->toBeNull();

    $addStamps->add($card, $user, $location);

    expect((int) $card->fresh()->stamps_collected)->toBe(1)
        ->and($card->rewards()->count())->toBe(1);

    $this->actingAs($user);

    Livewire::test('pages::dashboard.recompensas')
        ->assertSee('rewards-customer@example.com')
        ->assertSee('Coffee')
        ->call('markRewardDelivered', $reward->id)
        ->assertSee('Recompensa marcada como entregada.')
        ->assertSee('Entregada');

    expect($reward->fresh()->delivered_at)->not->toBeNull()
        ->and($reward->fresh()->delivered_by)->toBe($user->id);
});

test('wallet progress updates the stored pass and queues a remote update', function () {
    $user = User::factory()->create();
    app(CreateTeam::class)->handle($user, 'Wallet sync test team');
    $team = $user->fresh()->currentTeam;
    $location = Location::create([
        'team_id' => $team->id,
        'name' => 'Wallet location',
        'is_active' => true,
    ]);
    $design = $team->cardDesign()->create([
        'is_active' => true,
        'color_scheme' => [],
        'stamps_required' => 8,
        'reward' => 'Coffee',
    ]);
    $customer = CustomerUser::create([
        'team_id' => $team->id,
        'location_id' => $location->id,
        'email' => 'wallet-sync@example.com',
        'guest' => false,
    ]);
    $card = $customer->cards()->create([
        'team_id' => $team->id,
        'card_design_id' => $design->id,
        'stamps_collected' => 0,
        'is_active' => true,
    ]);
    $pass = $customer->mobilePasses()->create([
        'pass_serial' => 'wallet-sync-' . $card->id,
        'type' => PassType::StoreCard->value,
        'platform' => Platform::Google->value,
        'builder_name' => 'loyalty_pass',
        'content' => [
            'googleClassType' => 'loyaltyClass',
            'googleObjectId' => 'issuer.wallet-sync',
            'googleObjectPayload' => [
                'loyaltyPoints' => ['balance' => ['string' => '0/8']],
                'textModulesData' => [],
            ],
        ],
        'images' => [],
    ]);

    config(['mobile-pass.queue.connection' => 'database']);
    Queue::fake();

    $card->update(['stamps_collected' => 1]);

    $updatedPayload = $pass->fresh()->content['googleObjectPayload'];

    expect($updatedPayload['loyaltyPoints']['balance']['string'])->toBe('1/8')
        ->and($updatedPayload['textModulesData'][0]['body'])->toBe('● ○ ○ ○ ○ ○ ○ ○');

    Queue::assertPushed(PushPassUpdateJob::class);
});

test('apple wallet registration is counted and stamp changes queue an updated pass', function () {
    $user = User::factory()->create();
    app(CreateTeam::class)->handle($user, 'Apple Wallet sync team');
    $team = $user->fresh()->currentTeam;
    $location = Location::create([
        'team_id' => $team->id,
        'name' => 'Apple location',
        'is_active' => true,
    ]);
    $design = $team->cardDesign()->create([
        'is_active' => true,
        'color_scheme' => [],
        'stamps_required' => 8,
        'reward' => 'Coffee',
    ]);
    $customer = CustomerUser::create([
        'team_id' => $team->id,
        'location_id' => $location->id,
        'email' => 'apple-sync@example.com',
        'guest' => false,
    ]);
    $card = $customer->cards()->create([
        'team_id' => $team->id,
        'card_design_id' => $design->id,
        'stamps_collected' => 0,
        'is_active' => true,
    ]);
    $secret = str_repeat('a', 32);

    config([
        'mobile-pass.apple.organization_name' => $team->name,
        'mobile-pass.apple.type_identifier' => 'pass.com.fidentta.test',
        'mobile-pass.apple.team_identifier' => 'FIDENTTA123',
        'mobile-pass.apple.webservice.secret' => $secret,
        'mobile-pass.apple.webservice.host' => 'https://wallet.example.test',
        'mobile-pass.queue.connection' => 'database',
    ]);

    $pass = StoreCardPassBuilder::make()
        ->setOrganizationName($team->name)
        ->setSerialNumber('apple-card-' . $card->id)
        ->setDescription('Loyalty card')
        ->setLogoText($team->name)
        ->addField('stamps', '0/8', FieldType::Primary, label: 'Sellos')
        ->addSecondaryField('progress', '○ ○ ○ ○ ○ ○ ○ ○', label: 'Progreso')
        ->save();

    Queue::fake();
    $customer->addMobilePass($pass);

    $this->postJson(route('mobile-pass.register-device', [
        'deviceId' => 'apple-device-test',
        'passTypeId' => 'pass.com.fidentta.test',
        'passSerial' => 'apple-card-' . $card->id,
    ]), ['pushToken' => 'apple-push-token-test'], [
        'Authorization' => 'ApplePass ' . $secret,
    ])->assertCreated();

    expect($pass->fresh()->isCurrentlyInWallet())->toBeTrue()
        ->and($pass->fresh()->wallet_added_at)->not->toBeNull();

    $this->actingAs($user);
    $dashboard = Livewire::test('pages::dashboard.inicio');

    expect($dashboard->instance()->stats['wallet_adds']['value'])->toBe(1);

    Queue::fake();
    $card->update(['stamps_collected' => 1]);
    $updatedFields = $pass->fresh()->content['storeCard'];

    expect(collect($updatedFields['primaryFields'])->firstWhere('key', 'stamps')['value'])->toBe('1/8')
        ->and(collect($updatedFields['secondaryFields'])->firstWhere('key', 'progress')['value'])->toBe('● ○ ○ ○ ○ ○ ○ ○');

    Queue::assertPushed(PushPassUpdateJob::class);
});

test('apple wallet pass is not issued without a usable public web service', function () {
    $user = User::factory()->create();
    app(CreateTeam::class)->handle($user, 'Apple config test team');
    $team = $user->fresh()->currentTeam;
    $location = Location::create([
        'team_id' => $team->id,
        'name' => 'Apple config location',
        'is_active' => true,
    ]);
    $team->cardDesign()->create([
        'is_active' => true,
        'color_scheme' => [],
        'stamps_required' => 8,
        'reward' => 'Coffee',
    ]);

    config([
        'mobile-pass.apple.certificate' => 'configured-but-not-used',
        'mobile-pass.apple.certificate_path' => null,
        'mobile-pass.apple.type_identifier' => 'pass.com.fidentta.test',
        'mobile-pass.apple.team_identifier' => 'FIDENTTA123',
        'mobile-pass.apple.webservice.secret' => null,
        'mobile-pass.apple.webservice.host' => 'https://wallet.example.test',
        'mobile-pass.google.issuer_id' => null,
    ]);

    $this->post(route('wallet.card.store', ['qr_token' => $location->qr_token]))
        ->assertOk()
        ->assertSee('web service público con HTTPS');

    $this->assertDatabaseMissing('mobile_passes', [
        'platform' => Platform::Apple->value,
    ]);
});

test('only Apple Wallet write endpoints bypass CSRF verification', function () {
    $excludedPaths = app(PreventRequestForgery::class)->getExcludedPaths();

    expect($excludedPaths)
        ->toContain('passkit/v1/devices/*/registrations/*/*')
        ->not->toContain('passkit/*')
        ->not->toContain('passkit/v1/passes/*/*/personalize')
        ->not->toContain('passkit/v1/log')
        ->not->toContain('passkit/v1/google/callbacks');
});
