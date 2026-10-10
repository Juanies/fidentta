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
        return $this->team
            ->locations()
            ->withCount('customerUsers')
            ->withSum(['cardTransactions as stamps_month' => fn($transactions) => $transactions->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])], 'stamps_added')
            ->orderByDesc('is_active')
            ->orderByDesc('stamps_month')
            ->get();
    }

    public function getResumenProperty(): array
    {
        $locales = $this->locales;

        return [
            'activos' => $locales->where('is_active', true)->count(),
            'clientes' => (int) $locales->sum('customer_users_count'),
            'sellos' => (int) $locales->sum(fn($local) => (int) ($local->stamps_month ?? 0)),
        ];
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-px p-4 md:p-6" style="background: var(--border);">
    <header class="flex flex-col gap-4 bg-background px-4 py-6 sm:flex-row sm:items-end sm:justify-between md:px-6">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-fidentta-teal">Gestión · Locales</p>
            <h1 class="mt-1 text-4xl tracking-tight text-ink">Locales de {{ $team->name }}</h1>
            <p class="mt-2 max-w-xl text-sm leading-6 text-ink2">Puntos de atención y su QR de fidelidad.</p>
        </div>
        <button type="button" wire:click="$toggle('showCreateForm')"
            class="inline-flex w-fit items-center gap-2 bg-ink px-5 py-3 text-sm font-bold text-paper transition hover:opacity-90">
            <span aria-hidden="true">+</span> Añadir local
        </button>
    </header>

    @if ($showCreateForm)
        <form wire:submit="crearLocal" class="grid gap-4 bg-card p-5 sm:grid-cols-2 md:p-6">
            <div>
                <label for="location-name"
                    class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.16em] text-ink2">Nombre del
                    local</label>
                <input id="location-name" wire:model="name" required maxlength="120" placeholder="Sucursal Centro"
                    class="w-full border-b-2 border-ink bg-transparent px-1 py-2 text-sm font-medium text-ink placeholder:text-ink2/60 focus:border-fidentta-teal focus:outline-none">
                @error('name')
                    <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="location-address"
                    class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.16em] text-ink2">Dirección</label>
                <input id="location-address" wire:model="address" maxlength="255" placeholder="Calle y número"
                    class="w-full border-b-2 border-ink bg-transparent px-1 py-2 text-sm font-medium text-ink placeholder:text-ink2/60 focus:border-fidentta-teal focus:outline-none">
                @error('address')
                    <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end gap-3 sm:col-span-2">
                <button type="button" wire:click="$set('showCreateForm', false)"
                    class="px-4 py-2.5 text-sm font-semibold text-ink2 transition hover:text-ink">Cancelar</button>
                <button type="submit"
                    class="bg-ink px-5 py-2.5 text-sm font-bold text-paper transition hover:opacity-90">Crear
                    local</button>
            </div>
        </form>
    @endif

    @if ($this->locales->isNotEmpty())
        <section class="grid grid-cols-3 gap-px" style="background: var(--border);" aria-label="Resumen de locales">
            <article class="bg-card p-5">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">Locales activos</p>
                <p class="mt-2 text-3xl leading-none tracking-tight text-ink tabular-nums">
                    {{ $this->resumen['activos'] }}</p>
            </article>
            <article class="bg-card p-5">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">Clientes totales</p>
                <p class="mt-2 text-3xl leading-none tracking-tight text-ink tabular-nums">
                    {{ number_format($this->resumen['clientes']) }}</p>
            </article>
            <article class="bg-card p-5">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">Sellos este mes</p>
                <p class="mt-2 text-3xl leading-none tracking-tight text-ink tabular-nums">
                    {{ number_format($this->resumen['sellos']) }}</p>
            </article>
        </section>
    @endif

    @if ($this->locales->isEmpty())
        <section class="bg-card px-6 py-14 text-center">
            <h2 class="text-xl tracking-tight text-ink">Aún no hay locales</h2>
            <p class="mt-2 text-sm text-ink2">Crea una sucursal para generar su QR y empezar a registrar visitas.</p>
            <button type="button" wire:click="$set('showCreateForm', true)"
                class="mt-5 inline-flex items-center gap-2 bg-ink px-5 py-3 text-sm font-bold text-paper transition hover:opacity-90">Crear
                el primero <span aria-hidden="true">&rarr;</span></button>
        </section>
    @else
        @php($maxSellos = max(1, (int) $this->locales->max(fn($local) => (int) ($local->stamps_month ?? 0))))
        <section class="grid grid-cols-1 gap-px sm:grid-cols-2 xl:grid-cols-3" style="background: var(--border);">
            @foreach ($this->locales as $local)
                <a href="{{ route('locations.index', ['current_team' => $team, 'id' => $local->id]) }}" wire:navigate
                    class="group flex min-w-0 flex-col bg-card p-5 transition hover:bg-mint/30">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">Local</p>
                        <span
                            class="rounded-sm px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $local->is_active ? 'bg-mint text-ink' : 'bg-border text-ink2' }}">
                            {{ $local->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <h2 class="mt-2 truncate text-2xl tracking-tight text-ink">{{ $local->name }}</h2>
                    <p class="mt-1 min-h-5 truncate text-sm text-ink2">
                        {{ $local->address ?: 'Sin dirección añadida' }}</p>
                    <div class="mt-5">
                        <div class="flex items-baseline justify-between text-xs">
                            <span class="text-ink2">Sellos del mes</span>
                            <span
                                class="font-semibold text-ink tabular-nums">{{ (int) ($local->stamps_month ?? 0) }}</span>
                        </div>
                        <div class="mt-1.5 h-1.5 w-full bg-border">
                            <div class="h-1.5 bg-fidentta-teal"
                                style="width: {{ (int) round((((int) ($local->stamps_month ?? 0)) / $maxSellos) * 100) }}%">
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between border-t border-border pt-4 text-sm">
                        <span class="text-ink2 tabular-nums">{{ $local->customer_users_count }} clientes</span>
                        <span class="font-semibold text-fidentta-teal transition group-hover:text-ink">Gestionar <span
                                aria-hidden="true">&rarr;</span></span>
                    </div>
                </a>
            @endforeach
        </section>
    @endif

</div>
