<?php

use App\Actions\Teams\CreateTeam;
use App\Models\CardReward;
use App\Models\CustomerUser;
use App\Models\Location;
use App\Models\User;
use App\Services\AddStampsToCard;
use Livewire\Livewire;

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

    foreach (range(1, 8) as $_) {
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
