<?php

use Livewire\Component;
use App\Models\CardTransaction;

new class extends Component {
    public $team;
    public $locations;
    public $design;
    public string $locationId = '';

    public function mount()
    {
        $this->team = auth()->user()->currentTeam;
        abort_unless($this->team !== null, 404);
        $this->design = $this->team->cardDesign()->where('is_active', true)->first();
        $this->locations = $this->team->locations()->where('is_active', true)->orderBy('name')->get();
        $this->locationId = (string) ($this->locations->first()?->id ?? '');
    }

    public function updatedLocationId(): void
    {
        if ($this->locationId !== '' && !$this->team->locations()->whereKey($this->locationId)->exists()) {
            $this->locationId = '';
        }
    }

    public function getSelectedLocationProperty()
    {
        return $this->locationId !== '' ? $this->team->locations()->find($this->locationId) : null;
    }

    public function getStatsProperty(): array
    {
        $transactions = CardTransaction::query()->whereHas('location', fn($locations) => $locations->where('team_id', $this->team->id));
        $customers = $this->team->customerusers();

        if ($this->locationId !== '') {
            $transactions->where('location_id', $this->locationId);
            $customers->where('location_id', $this->locationId);
        }

        return [
            'customers' => $customers->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
            'stamps' => (int) (clone $transactions)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('stamps_added'),
            'visits' => (clone $transactions)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
        ];
    }
}; ?>
<div class="flex h-full w-full flex-1 flex-col gap-px p-4 md:p-6" style="background: var(--border);">
    <header class="flex flex-col gap-4 bg-background px-4 py-6 md:flex-row md:items-end md:justify-between md:px-6">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-fidentta-teal">Programa de sellos</p>
            <h1 class="mt-1 text-4xl tracking-tight text-ink">Tu programa, conectado a cada local</h1>
            <p class="mt-2 max-w-xl text-sm leading-6 text-ink2">El QR registra al cliente; el equipo añade sellos
                desde el panel.</p>
        </div>
        <div class="flex items-center gap-2 text-sm">
            <span class="h-2 w-2 rounded-full bg-fidentta-teal" aria-hidden="true"></span>
            <span class="font-semibold text-ink">{{ $team->name }}</span>
            <span class="text-ink2">· Programa activo</span>
        </div>
    </header>

    <section class="grid grid-cols-1 gap-px lg:grid-cols-12" style="background: var(--border);">
        <article class="bg-card p-5 md:p-6 lg:col-span-7">
            <div class="flex items-baseline justify-between gap-4">
                <h2 class="text-2xl tracking-tight text-ink">¿Qué ocurre al escanear?</h2>
                <span class="text-xs font-semibold uppercase tracking-[0.16em] text-ink2">QR + sello</span>
            </div>
            <ol class="mt-6 grid gap-px sm:grid-cols-3" style="background: var(--border);">
                <li class="bg-card p-4">
                    <span class="text-2xl font-bold text-fidentta-teal">01</span>
                    <h3 class="mt-3 text-sm font-semibold text-ink">Cliente escanea</h3>
                    <p class="mt-1.5 text-xs leading-5 text-ink2">El QR abre el registro de este local.</p>
                </li>
                <li class="bg-card p-4">
                    <span class="text-2xl font-bold text-fidentta-teal">02</span>
                    <h3 class="mt-3 text-sm font-semibold text-ink">Obtiene su tarjeta</h3>
                    <p class="mt-1.5 text-xs leading-5 text-ink2">Se registra según el modo elegido.</p>
                </li>
                <li class="bg-card p-4">
                    <span class="text-2xl font-bold text-fidentta-teal">03</span>
                    <h3 class="mt-3 text-sm font-semibold text-ink">Equipo añade sellos</h3>
                    <p class="mt-1.5 text-xs leading-5 text-ink2">Desde Clientes, se registra la visita.</p>
                </li>
            </ol>
            <div class="mt-6 flex flex-col gap-3 bg-mint/40 p-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold text-ink">Tu QR está listo para compartir</p>
                    <p class="mt-1 text-xs text-ink2">Colócalo en el mostrador o en el ticket de compra.</p>
                </div>
                <a href="#qr-programa"
                    class="inline-flex w-fit items-center gap-2 bg-ink px-4 py-2.5 text-xs font-bold text-paper transition hover:opacity-90">Ver
                    QR del programa &rarr;</a>
            </div>
        </article>

        <article id="qr-programa" class="bg-card p-5 md:p-6 lg:col-span-5">
            <div class="flex items-baseline justify-between gap-4">
                <h2 class="text-2xl tracking-tight text-ink">
                    {{ $this->selectedLocation?->name ?? 'Selecciona un local' }}</h2>
                <label class="sr-only" for="loyalty-location">Local</label>
                <select id="loyalty-location" wire:model.live="locationId"
                    class="border-b-2 border-ink bg-transparent px-1 py-1.5 text-sm font-medium text-ink focus:border-fidentta-teal focus:outline-none">
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
            @if ($this->selectedLocation)
                <div class="mt-5 flex items-center gap-5">
                    <img src="{{ route('location.qr', $this->selectedLocation) }}"
                        alt="QR de {{ $this->selectedLocation->name }}" width="160" height="160"
                        class="h-36 w-36 shrink-0 bg-white p-2">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-ink">
                            {{ $this->selectedLocation->address ?: 'Dirección pendiente' }}</p>
                        <p class="mt-2 break-all text-xs text-ink2">
                            {{ route('wallet.card', ['qr_token' => $this->selectedLocation->qr_token]) }}</p>
                        <a href="{{ route('locations.index', ['current_team' => $team, 'id' => $this->selectedLocation->id]) }}"
                            wire:navigate
                            class="mt-3 inline-flex text-sm font-semibold text-fidentta-teal underline decoration-2 underline-offset-4 transition hover:text-ink">Gestionar
                            local &rarr;</a>
                    </div>
                </div>
            @else
                <p class="mt-5 border border-dashed border-border p-6 text-sm text-ink2">Crea un local para generar su
                    QR.</p>
            @endif
            <div class="mt-6 grid grid-cols-3 gap-px border border-border" style="background: var(--border);">
                <div class="bg-background p-3 text-center">
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-ink2">Clientes nuevos</p>
                    <p class="mt-1 text-xl font-semibold text-ink tabular-nums">{{ number_format($this->stats['customers']) }}</p>
                </div>
                <div class="bg-background p-3 text-center">
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-ink2">Sellos del mes</p>
                    <p class="mt-1 text-xl font-semibold text-ink tabular-nums">{{ number_format($this->stats['stamps']) }}</p>
                </div>
                <div class="bg-background p-3 text-center">
                    <p class="text-[10px] font-semibold uppercase tracking-wide text-ink2">Visitas</p>
                    <p class="mt-1 text-xl font-semibold text-ink tabular-nums">{{ number_format($this->stats['visits']) }}</p>
                </div>
            </div>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-px lg:grid-cols-2" style="background: var(--border);">
        <article class="bg-card p-5 md:p-6">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-fidentta-teal">Experiencia del cliente
            </p>
            <h2 class="mt-2 text-xl tracking-tight text-ink">Si no está registrado</h2>
            <ul class="mt-5 space-y-4">
                <li class="flex gap-3">
                    <span class="text-sm font-bold text-ink2">01</span>
                    <div>
                        <p class="text-sm font-semibold text-ink">Ve tu programa</p>
                        <p class="mt-1 text-xs leading-5 text-ink2">Después de escanear, el cliente ve el nombre, la
                            recompensa y los sellos disponibles.</p>
                    </div>
                </li>
                <li class="flex gap-3">
                    <span class="text-sm font-bold text-ink2">02</span>
                    <div>
                        <p class="text-sm font-semibold text-ink">Añade su tarjeta</p>
                        <p class="mt-1 text-xs leading-5 text-ink2">Se registra o guarda la tarjeta para empezar a
                            acumular sellos.</p>
                    </div>
                </li>
            </ul>
        </article>
        <article class="bg-card p-5 md:p-6">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-fidentta-teal">Acción del equipo</p>
            <h2 class="mt-2 text-xl tracking-tight text-ink">Registrar una visita</h2>
            <p class="mt-4 text-sm leading-6 text-ink2">En Clientes, filtra por local y pulsa “Dar sello” en la
                tarjeta activa. Cada operación queda asociada al usuario y al local.</p>
            <a href="{{ route('dashboard.clientes', ['current_team' => $team]) }}" wire:navigate
                class="mt-5 inline-flex items-center gap-2 bg-ink px-5 py-3 text-sm font-bold text-paper transition hover:opacity-90">Abrir
                clientes <span aria-hidden="true">&rarr;</span></a>
        </article>
    </section>
</div>
