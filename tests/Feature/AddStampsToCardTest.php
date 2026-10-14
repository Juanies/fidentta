<?php

use App\Actions\Teams\CreateTeam;
use App\Models\CardReward;
use App\Models\CustomerUser;
use App\Models\Location;
use App\Models\User;
use App\Services\AddStampsToCard;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
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
