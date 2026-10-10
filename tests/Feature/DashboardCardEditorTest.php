<?php

use App\Actions\Teams\CreateTeam;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

function makeCardEditorUser(): User
{
    $user = User::create([
        'name' => 'Card owner',
        'email' => 'card-owner@example.com',
        'password' => Hash::make('password'),
    ]);

    app(CreateTeam::class)->handle($user, 'Card test team');

    return $user->fresh();
}

test('owner can edit card branding, mechanics and registration mode from the dashboard', function () {
    $user = makeCardEditorUser();
    $team = $user->currentTeam;
    $design = $team->cardDesign()->create([
        'is_active' => true,
        'color_scheme' => ['slug' => 'cacao-clasico', 'fondo' => 'linear-gradient(135deg, #7C2D12 0%, #F59E0B 100%)', 'texto' => '#F8FAFC'],
        'stamps_required' => 8,
        'reward' => 'Café',
    ]);
    Location::create(['team_id' => $team->id, 'name' => 'Local Centro', 'is_active' => true]);

    $this->actingAs($user);

    Livewire::test('pages::dashboard.tarjeta')
        ->assertSet('name', 'Card test team')
        ->set('name', 'Café Renovado')
        ->set('sellos', 10)
        ->set('recompensa', 'Postre gratis')
        ->call('cambiarPaleta', 'azul-royal')
        ->set('modoRegistro', 'none')
        ->call('guardar')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard.tarjeta', ['current_team' => 'cafe-renovado']));

    expect(session('tarjeta_status'))->toBe('Tarjeta actualizada correctamente.');

    $team->refresh();
    $design->refresh();

    expect($team->name)->toBe('Café Renovado')
        ->and($team->customer_registration_type)->toBe('none')
        ->and($design->stamps_required)->toBe(10)
        ->and($design->reward)->toBe('Postre gratis')
        ->and($design->color_scheme['slug'])->toBe('azul-royal');
});

test('editor validates the card mechanics before saving', function () {
    $user = makeCardEditorUser();

    $this->actingAs($user);

    Livewire::test('pages::dashboard.tarjeta')
        ->set('name', 'Ok')
        ->set('sellos', 20)
        ->set('recompensa', '')
        ->call('guardar')
        ->assertHasErrors(['name', 'sellos', 'recompensa']);
});
