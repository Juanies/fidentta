<?php

use App\Actions\Teams\CreateTeam;
use App\Models\card as LoyaltyCard;
use App\Models\CustomerUser;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\LaravelMobilePass\Enums\PassType;
use Spatie\LaravelMobilePass\Enums\Platform;

function makeDashboardFilterUser(): User
{
    $user = User::create([
        'name' => 'Dashboard owner',
        'email' => 'dashboard-owner@example.com',
        'password' => Hash::make('password'),
    ]);

    app(CreateTeam::class)->handle($user, 'Dashboard test team');

    return $user->fresh();
}

test('customers page filters customer records by the selected location', function () {
    $user = makeDashboardFilterUser();
    $team = $user->currentTeam;
    $firstLocation = Location::create([
        'team_id' => $team->id,
        'name' => 'Local Norte',
        'is_active' => true,
    ]);
    $secondLocation = Location::create([
        'team_id' => $team->id,
        'name' => 'Local Sur',
        'is_active' => true,
    ]);
    CustomerUser::create([
        'team_id' => $team->id,
        'location_id' => $firstLocation->id,
        'email' => 'norte@example.com',
        'guest' => false,
    ]);
    CustomerUser::create([
        'team_id' => $team->id,
        'location_id' => $secondLocation->id,
        'email' => 'sur@example.com',
        'guest' => false,
    ]);

    $this->actingAs($user);

    Livewire::test('pages::dashboard.clientes')
        ->assertSee('norte@example.com')
        ->assertSee('sur@example.com')
        ->set('locationId', (string) $firstLocation->id)
        ->assertSee('norte@example.com')
        ->assertDontSee('sur@example.com');
});

test('staff can award one stamp to an active customer card from the customer list', function () {
    $user = makeDashboardFilterUser();
    $team = $user->currentTeam;
    $location = Location::create([
        'team_id' => $team->id,
        'name' => 'Local Centro',
        'is_active' => true,
    ]);
    $design = $team->cardDesign()->create([
        'is_active' => true,
        'color_scheme' => [],
        'stamps_required' => 8,
        'reward' => 'Café',
    ]);
    $customer = CustomerUser::create([
        'team_id' => $team->id,
        'location_id' => $location->id,
        'email' => 'stamp-customer@example.com',
        'guest' => false,
    ]);
    $card = $customer->cards()->create([
        'team_id' => $team->id,
        'card_design_id' => $design->id,
        'stamps_collected' => 0,
        'is_active' => true,
    ]);

    $this->actingAs($user);

    Livewire::test('pages::dashboard.clientes')
        ->call('addStamp', $customer->id)
        ->assertSee('Sello añadido a la tarjeta');

    expect($card->fresh()->stamps_collected)->toBe(1);
    $this->assertDatabaseHas('card_transactions', [
        'card_id' => $card->id,
        'customer_id' => $customer->id,
        'location_id' => $location->id,
        'user_id' => $user->id,
        'stamps_added' => 1,
    ]);
});

test('customers page filters the list with the search input', function () {
    $user = makeDashboardFilterUser();
    $team = $user->currentTeam;
    $location = Location::create([
        'team_id' => $team->id,
        'name' => 'Local Centro',
        'is_active' => true,
    ]);
    CustomerUser::create([
        'team_id' => $team->id,
        'location_id' => $location->id,
        'email' => 'maria.busqueda@example.com',
        'guest' => false,
    ]);
    CustomerUser::create([
        'team_id' => $team->id,
        'location_id' => $location->id,
        'email' => 'otro.cliente@example.com',
        'guest' => false,
    ]);

    $this->actingAs($user);

    Livewire::test('pages::dashboard.clientes')
        ->set('search', 'maria.busqueda')
        ->assertSee('maria.busqueda@example.com')
        ->assertDontSee('otro.cliente@example.com');
});

test('customers page can filter cards that have completed their stamp goal', function () {
    $user = makeDashboardFilterUser();
    $team = $user->currentTeam;
    $location = Location::create([
        'team_id' => $team->id,
        'name' => 'Local Centro',
        'is_active' => true,
    ]);
    $design = $team->cardDesign()->create([
        'is_active' => true,
        'color_scheme' => [],
        'stamps_required' => 6,
        'reward' => 'Café',
    ]);

    foreach ([['completo@example.com', 6], ['pendiente@example.com', 4]] as [$email, $stamps]) {
        $customer = CustomerUser::create([
            'team_id' => $team->id,
            'location_id' => $location->id,
            'email' => $email,
            'guest' => false,
        ]);
        $customer->cards()->create([
            'team_id' => $team->id,
            'card_design_id' => $design->id,
            'stamps_collected' => $stamps,
            'is_active' => true,
        ]);
    }

    $this->actingAs($user);

    Livewire::test('pages::dashboard.clientes')
        ->set('stampFilter', 'completed')
        ->assertSee('completo@example.com')
        ->assertDontSee('pendiente@example.com');
});

test('reducing the stamp goal never displays an overfull count or negative remaining stamps', function () {
    $user = makeDashboardFilterUser();
    $team = $user->currentTeam;
    $location = Location::create([
        'team_id' => $team->id,
        'name' => 'Local Centro',
        'is_active' => true,
    ]);
    $design = $team->cardDesign()->create([
        'is_active' => true,
        'color_scheme' => [],
        'stamps_required' => 6,
        'reward' => 'Café',
    ]);
    $customer = CustomerUser::create([
        'team_id' => $team->id,
        'location_id' => $location->id,
        'email' => 'sobrecompleto@example.com',
        'guest' => false,
    ]);
    $customer->cards()->create([
        'team_id' => $team->id,
        'card_design_id' => $design->id,
        'stamps_collected' => 8,
        'is_active' => true,
    ]);

    $this->actingAs($user);

    Livewire::test('pages::dashboard.clientes')
        ->assertSee('6/6')
        ->assertDontSee('8/6')
        ->set('locationId', (string) $location->id)
        ->call('addStamp', $customer->id)
        ->assertSee('Sello añadido a la tarjeta')
        ->assertSee('1/6')
        ->assertDontSee('-2 sellos');

    $this->assertDatabaseHas('card_rewards', [
        'team_id' => $team->id,
        'customer_id' => $customer->id,
        'reward_name' => 'Café',
        'cycle_number' => 1,
        'delivered_at' => null,
    ]);
});

test('public page link renders its configured title and links', function () {
    $user = makeDashboardFilterUser();
    $team = $user->currentTeam;
    $location = Location::create([
        'team_id' => $team->id,
        'name' => 'Local Centro',
        'page_link_name' => 'Café del Centro',
        'is_active' => true,
    ]);
    $location->businessPageLinks()->create([
        'team_id' => $team->id,
        'name' => 'Instagram',
        'url' => 'https://instagram.com/cafecentro',
        'icon' => 'instagram',
    ]);

    $this->get(route('wallet.links', ['qr_token' => $location->qr_token]))
        ->assertOk()
        ->assertSee('Café del Centro')
        ->assertSee('https://instagram.com/cafecentro')
        ->assertSee(route('wallet.card', ['qr_token' => $location->qr_token]), false);

    $this->get(route('wallet.card', ['qr_token' => $location->qr_token]))
        ->assertOk()
        ->assertSee('Únete a');
});

test('location page editor saves its title and external links', function () {
    $user = makeDashboardFilterUser();
    $team = $user->currentTeam;
    $location = Location::create([
        'team_id' => $team->id,
        'name' => 'Local Centro',
        'is_active' => true,
    ]);

    $this->actingAs($user);

    Livewire::test('pages::dashboard.location.show', ['id' => $location->id])
        ->call('addPageLink')
        ->set('pageLinkName', 'Café del Centro')
        ->set('links.0.name', 'Instagram')
        ->set('links.0.url', 'https://instagram.com/cafecentro')
        ->set('links.0.icon', 'instagram')
        ->call('guardarPageLink')
        ->assertHasNoErrors();

    expect($location->fresh()->page_link_name)->toBe('Café del Centro')
        ->and($location->businessPageLinks()->first())
        ->name->toBe('Instagram');
});

test('dashboard page renders its live metrics and activity with a location filter', function () {
    $user = makeDashboardFilterUser();
    $team = $user->currentTeam;
    $location = Location::create([
        'team_id' => $team->id,
        'name' => 'Local Centro',
        'is_active' => true,
    ]);

    $this->actingAs($user);

    Livewire::test('pages::dashboard.inicio')
        ->assertSee('Actividad diaria')
        ->set('locationId', (string) $location->id)
        ->assertSee('Local Centro');
});

test('dashboard compares all four metric totals across consecutive seven-day periods', function () {
    $this->travelTo(now()->setDate(2026, 10, 6)->setTime(12, 0));
    $user = makeDashboardFilterUser();
    $team = $user->currentTeam;
    $location = Location::create([
        'team_id' => $team->id,
        'name' => 'Local Métricas',
        'is_active' => true,
    ]);
    $design = $team->cardDesign()->create([
        'is_active' => true,
        'color_scheme' => [],
        'stamps_required' => 8,
        'reward' => 'Café',
    ]);

    $customerSeeds = [
        ['current-one@example.com', now()->subDay()],
        ['current-two@example.com', now()->subDays(2)],
        ['previous@example.com', now()->subDays(8)],
    ];

    /** @var list<LoyaltyCard> $cards */
    $cards = [];

    foreach ($customerSeeds as $data) {
        $customer = CustomerUser::create([
            'team_id' => $team->id,
            'location_id' => $location->id,
            'email' => $data[0],
            'guest' => false,
        ]);
        $customer->forceFill(['created_at' => $data[1], 'updated_at' => $data[1]])->saveQuietly();

        /** @var LoyaltyCard $card */
        $card = $customer->cards()->create([
            'team_id' => $team->id,
            'card_design_id' => $design->id,
            'stamps_collected' => 0,
            'is_active' => true,
        ]);
        $cards[] = $card;
    }
    $cards[0]->cardTransactions()->create([
        'customer_id' => $cards[0]->customer_id,
        'location_id' => $location->id,
        'user_id' => $user->id,
        'stamps_added' => 3,
    ]);
    $cards[1]->cardTransactions()->create([
        'customer_id' => $cards[1]->customer_id,
        'location_id' => $location->id,
        'user_id' => $user->id,
        'stamps_added' => 3,
    ]);
    $previousVisit = $cards[2]->cardTransactions()->create([
        'customer_id' => $cards[2]->customer_id,
        'location_id' => $location->id,
        'user_id' => $user->id,
        'stamps_added' => 4,
    ]);
    $previousVisit->forceFill(['created_at' => now()->subDays(8), 'updated_at' => now()->subDays(8)])->saveQuietly();

    foreach ([[$cards[0], now()->subDay(), 'current'], [$cards[2], now()->subDays(8), 'previous']] as [$card, $savedAt, $suffix]) {
        $pass = $card->customer->mobilePasses()->create([
            'pass_serial' => 'wallet-'.$suffix.'-'.$card->id,
            'type' => PassType::StoreCard->value,
            'platform' => Platform::Google->value,
            'builder_name' => 'loyalty_pass',
            'content' => ['googleClassType' => 'loyaltyClass', 'googleObjectId' => 'issuer.'.$suffix],
            'images' => [],
        ]);
        $pass->googleEvents()->create([
            'event_type' => 'save',
            'received_at' => $savedAt,
        ]);
    }

    $this->actingAs($user);

    Livewire::test('pages::dashboard.inicio')
        ->set('locationId', (string) $location->id)
        ->assertSee('Clientes nuevos')
        ->assertSee('+100.0%')
        ->assertSee('Sellos entregados')
        ->assertSee('+50.0%')
        ->assertSee('Pases en Wallet')
        ->assertSee('0.0%')
        ->assertSee('Visitas');
});
