<?php

use App\Models\Location;
use Livewire\Component;

new class extends Component {
    public $team;
    public bool $showCreateForm = false;
    public string $name = '';
    public string $address = '';

    public function mount()
    {
        $this->team = auth()->user()->currentTeam;
    }

    public function crearLocal(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $location = $this->team->locations()->create([...$validated, 'is_active' => true]);

        $this->redirectRoute(
            'locations.index',
            [
                'current_team' => $this->team,
                'id' => $location->id,
            ],
            navigate: true,
        );
    }

    public function getLocalesProperty()
    {
        return $this->team->locations()->withCount('customerusers')->latest()->get();
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-2 md:p-5">
    <header class="flex flex-col gap-5 border-b border-border pb-6 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-brand">GESTIÓN / LOCALES</p>
            <h1 class="mt-2 text-3xl font-semibold text-ink">Locales de {{ $team->name }}</h1>
            <p class="mt-2 max-w-xl text-sm leading-6 text-muted-foreground">Gestiona los puntos de atención y su QR de
                fidelidad.</p>
        </div>
        <button type="button" wire:click="$toggle('showCreateForm')"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700">
            <span aria-hidden="true">+</span> Añadir local
        </button>
    </header>

    @if ($showCreateForm)
        <form wire:submit="crearLocal" class="grid gap-4 rounded-xl border border-border bg-card p-5 sm:grid-cols-2">
            <div>
                <label for="location-name" class="mb-1.5 block text-sm font-medium text-ink">Nombre del local</label>
                <input id="location-name" wire:model="name" required maxlength="120" placeholder="Sucursal Centro"
                    class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20">
                @error('name')
                    <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="location-address" class="mb-1.5 block text-sm font-medium text-ink">Dirección</label>
                <input id="location-address" wire:model="address" maxlength="255" placeholder="Calle y número"
                    class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20">
                @error('address')
                    <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end gap-2 sm:col-span-2">
                <button type="button" wire:click="$set('showCreateForm', false)"
                    class="rounded-lg border border-border px-4 py-2 text-sm font-medium text-ink">Cancelar</button>
                <button type="submit"
                    class="rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Crear
                    local</button>
            </div>
        </form>
    @endif

    @if ($this->locales->isEmpty())
        <section class="rounded-xl border border-dashed border-border bg-card px-6 py-14 text-center">
            <h2 class="text-lg font-semibold text-ink">Aún no hay locales</h2>
            <p class="mt-2 text-sm text-muted-foreground">Crea una sucursal para generar su QR y empezar a registrar
                visitas.</p>
        </section>
    @else
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach ($this->locales as $local)
                <a href="{{ route('locations.index', ['current_team' => $team, 'id' => $local->id]) }}" wire:navigate
                    class="group flex min-w-0 flex-col rounded-xl border border-border bg-card p-5 transition hover:border-brand/40 hover:shadow-md">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-brand-soft text-brand"
                            aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M3 10h18M5 10v10h14V10M3 10l2-6h14l2 6M9 20v-6h6v6" />
                            </svg>
                        </div>
                        <span
                            class="rounded-full px-2.5 py-1 text-xs font-medium {{ $local->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                            {{ $local->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <h2 class="mt-4 truncate text-base font-semibold text-ink">{{ $local->name }}</h2>
                    <p class="mt-1 min-h-5 truncate text-sm text-muted-foreground">
                        {{ $local->address ?: 'Sin dirección añadida' }}</p>
                    <div class="mt-5 flex items-center justify-between border-t border-border pt-4 text-sm">
                        <span class="text-muted-foreground">{{ $local->customerusers_count }} clientes</span>
                        <span class="font-semibold text-brand group-hover:text-brand-700">Gestionar <span
                                aria-hidden="true">&rarr;</span></span>
                    </div>
                </a>
            @endforeach
        </section>
    @endif

</div>
