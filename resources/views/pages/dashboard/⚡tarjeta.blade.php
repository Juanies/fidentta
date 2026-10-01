<?php

use Livewire\Component;

new class extends Component {
    public $card;
    public $registro;
    public $team;
    public $locations;
    public ?string $locationId = null;

    public function mount()
    {
        $this->team = auth()->user()->currentTeam;
        abort_unless($this->team !== null, 404);

        $this->card = $this->team->cardDesign;

        if ($this->card) {
            $this->registro = $this->card->color_scheme;
        }

        $this->locations = $this->team->locations()->where('is_active', true)->orderBy('name')->get();
        $this->locationId = $this->locations->first()?->id;
    }

    public function updatedLocationId(): void
    {
        if ($this->locationId !== null && !$this->team->locations()->whereKey($this->locationId)->exists()) {
            $this->locationId = null;
        }
    }

    public function getSelectedLocationProperty()
    {
        return $this->locationId ? $this->team->locations()->find($this->locationId) : null;
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-2 md:p-5">

    <header
        class="rounded-4xl border border-white/10 bg-fidentta-gradient-soft p-6 shadow-2xl shadow-fidentta-purple/10 md:p-8">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] text-brand">Constructor de
                    fidelización</p>
                <h1 class="text-3xl font-semibold tracking-tight text-ink md:text-4xl">Diseña una tarjeta que tus
                    clientes quieran usar</h1>
                <p class="mt-3 max-w-2xl text-sm leading-7 text-text-secondary/80 md:text-base">Configura la
                    mecánica, la recompensa y la forma de compartirla. La tarjeta no es un pago: es el camino más
                    corto hacia la próxima visita.</p>
            </div>
            <div class="text-left lg:text-right">
                <p class="text-xs text-muted-foreground">Estado de la tarjeta</p>
                <p class="mt-1 text-2xl font-semibold text-ink">{{ $card ? 'Configurada' : 'Pendiente' }}</p>
            </div>
        </div>
        <div class="mt-7 h-1.5 overflow-hidden rounded-full bg-white/10">
            <div class="h-full rounded-full bg-brand transition-all {{ $card ? 'w-full' : 'w-1/4' }}"></div>
        </div>
    </header>


    @if ($card)
        @php($palette = $card->color_scheme ?? [])
        <div class="rounded-xl border border-border p-5 shadow-sm sm:p-6"
            style="background: {{ $palette['fondo'] ?? '#1D4ED8' }}; color: {{ $palette['texto'] ?? '#FFFFFF' }};">
            <div class="flex items-center gap-4">
                <div
                    class="flex h-11 w-11 items-center justify-center rounded-full border border-white/30 bg-white/15 text-lg font-bold">
                    {{ strtoupper(substr($team->name, 0, 1)) }}
                </div>
                <span class="text-base font-semibold">{{ $team->name }}</span>
            </div>

            <div class="mt-5 grid grid-cols-4 gap-3">
                @for ($i = 0; $i < $card->stamps_required; $i++)
                    <div class="flex items-center justify-center">
                        <div
                            class="h-14 w-14 rounded-full border border-white/30 bg-white/10 shadow-inner shadow-white/20">
                        </div>
                    </div>
                @endfor
            </div>

            <p class="mt-5 text-sm opacity-85">Completa los {{ $card->stamps_required }} sellos y consigue:
                {{ $card->reward }}</p>

            <div
                class="mt-8 flex flex-col items-center gap-4 rounded-lg bg-white/15 p-4 sm:flex-row sm:justify-between">
                @if ($this->selectedLocation)
                    <div class="flex items-center gap-3">
                        <label for="card-location" class="text-sm font-semibold">QR de este local</label>
                        <select id="card-location" wire:model.live="locationId"
                            class="rounded-lg border border-white/30 bg-white/10 px-3 py-2 text-sm">
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <a href="{{ route('locations.index', ['current_team' => $team, 'id' => $this->selectedLocation->id]) }}"
                        wire:navigate class="text-sm font-semibold underline underline-offset-4">Gestionar local</a>
                    <img src="{{ route('location.qr', $this->selectedLocation) }}"
                        alt="QR de {{ $this->selectedLocation->name }}" width="150" height="150"
                        class="size-36 rounded-md bg-white p-2">
                @else
                    <p class="text-sm">Crea un local para generar su QR.</p>
                    <a href="{{ route('dashboard.locales', ['current_team' => $team]) }}" wire:navigate
                        class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-ink">Crear local</a>
                @endif
            </div>

            <div class="hidden">
                <svg width="120" height="120" viewBox="0 0 21 21" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M11.75 8.75C12.693 8.75 13.164 8.75 13.457 8.457C13.75 8.164 13.75 7.693 13.75 6.75C13.75 5.807 13.75 5.336 14.043 5.043C14.336 4.75 14.807 4.75 15.75 4.75M4.75 11.75H6.75C7.693 11.75 8.164 11.75 8.457 12.043C8.75 12.336 8.75 12.807 8.75 13.75V15.75M5.125 15.5H5M4.75 19.75C4.286 19.75 4.053 19.75 3.858 19.728C3.07013 19.6392 2.33575 19.2855 1.77511 18.7249C1.21447 18.1643 0.860796 17.4299 0.772 16.642C0.75 16.447 0.75 16.214 0.75 15.75M15.75 19.75C16.214 19.75 16.447 19.75 16.642 19.728C17.4299 19.6392 18.1643 19.2855 18.7249 18.7249C19.2855 18.1643 19.6392 17.4299 19.728 16.642C19.75 16.447 19.75 16.214 19.75 15.75M4.75 0.75C4.286 0.75 4.053 0.75 3.858 0.772C3.07013 0.860796 2.33575 1.21447 1.77511 1.77511C1.21447 2.33575 0.860796 3.07013 0.772 3.858C0.75 4.053 0.75 4.286 0.75 4.75M15.75 0.75C16.214 0.75 16.447 0.75 16.642 0.772C17.4299 0.860796 18.1643 1.21447 18.7249 1.77511C19.2855 2.33575 19.6392 3.07013 19.728 3.858C19.75 4.053 19.75 4.286 19.75 4.75M5.043 5.043C4.75 5.336 4.75 5.807 4.75 6.75C4.75 7.693 4.75 8.164 5.043 8.457C5.336 8.75 5.807 8.75 6.75 8.75C7.693 8.75 8.164 8.75 8.457 8.457C8.75 8.164 8.75 7.693 8.75 6.75C8.75 5.807 8.75 5.336 8.457 5.043C8.164 4.75 7.693 4.75 6.75 4.75C5.807 4.75 5.336 4.75 5.043 5.043ZM5.25 15.5C5.25 15.5663 5.22366 15.6299 5.17678 15.6768C5.12989 15.7237 5.0663 15.75 5 15.75C4.9337 15.75 4.87011 15.7237 4.82322 15.6768C4.77634 15.6299 4.75 15.5663 4.75 15.5C4.75 15.4337 4.77634 15.3701 4.82322 15.3232C4.87011 15.2763 4.9337 15.25 5 15.25C5.0663 15.25 5.12989 15.2763 5.17678 15.3232C5.22366 15.3701 5.25 15.4337 5.25 15.5ZM12.043 12.043C11.75 12.336 11.75 12.807 11.75 13.75C11.75 14.693 11.75 15.164 12.043 15.457C12.336 15.75 12.807 15.75 13.75 15.75C14.693 15.75 15.164 15.75 15.457 15.457C15.75 15.164 15.75 14.693 15.75 13.75C15.75 12.807 15.75 12.336 15.457 12.043C15.164 11.75 14.693 11.75 13.75 11.75C12.807 11.75 12.336 11.75 12.043 12.043Z"
                        stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>
    @else
        <section class="rounded-xl border border-dashed border-border bg-card p-8 text-center">
            <h2 class="text-lg font-semibold text-ink">Todavía no hay tarjeta configurada</h2>
            <p class="mt-2 text-sm text-muted-foreground">Completa el asistente de configuración para definir sellos,
                colores y recompensa.</p>
            <a href="{{ route('registera') }}"
                class="mt-5 inline-flex rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-white">Abrir
                configuración</a>
        </section>
    @endif

    <section class="grid gap-5 lg:grid-cols-2">
        <article class="rounded-xl border border-border bg-card p-5 sm:p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand">Diseño guardado</p>
            <h2 class="mt-2 text-xl font-semibold text-ink">Reglas de la tarjeta</h2>
            <dl class="mt-5 divide-y divide-border">
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-sm text-muted-foreground">Meta de sellos</dt>
                    <dd class="font-semibold text-ink">{{ $card?->stamps_required ?? '—' }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-sm text-muted-foreground">Recompensa</dt>
                    <dd class="max-w-[60%] text-right font-semibold text-ink">{{ $card?->reward ?? 'Sin configurar' }}
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-sm text-muted-foreground">Locales activos</dt>
                    <dd class="font-semibold text-ink">{{ $locations->count() }}</dd>
                </div>
            </dl>
            <a href="{{ route('dashboard.fidelizacion', ['current_team' => $team]) }}" wire:navigate
                class="mt-4 inline-flex items-center rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700">Ver
                programa <span class="ml-2" aria-hidden="true">&rarr;</span></a>
        </article>

        <article class="rounded-xl border border-border bg-card p-5 sm:p-6">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand">Distribución</p>
            <h2 class="mt-2 text-xl font-semibold text-ink">Un QR para cada local</h2>
            <p class="mt-2 text-sm leading-6 text-muted-foreground">El QR abre el registro de clientes del local. Los
                pases para Wallet dependen de la configuración de cada plataforma.</p>
            <div class="mt-5 flex flex-wrap gap-2">
                @forelse ($locations as $location)
                    <a href="{{ route('locations.index', ['current_team' => $team, 'id' => $location->id]) }}"
                        wire:navigate
                        class="inline-flex items-center gap-2 rounded-lg border border-border bg-background px-3 py-2 text-sm font-medium text-ink transition hover:border-brand/40 hover:text-brand">
                        <span
                            class="h-2 w-2 rounded-full {{ $location->is_active ? 'bg-green-600' : 'bg-gray-400' }}"></span>{{ $location->name }}
                    </a>
                @empty
                    <p class="text-sm text-muted-foreground">Todavía no hay locales activos.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="grid gap-4 md:grid-cols-3">
        <div class="rounded-3xl border border-white/10 bg-white/3 p-5"><span
                class="text-xs font-semibold uppercase tracking-[0.18em] text-fidentta-cyan">Siguiente</span>
            <h3 class="mt-3 text-lg font-semibold text-text">Define la recompensa</h3>
            <p class="mt-2 text-sm leading-6 text-text-secondary/70">Elige qué recibe el cliente al completar sus
                sellos.</p><span
                class="mt-4 inline-flex rounded-full bg-white/5 px-3 py-1 text-xs text-text-secondary/60">Pendiente</span>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/3 p-5"><span
                class="text-xs font-semibold uppercase tracking-[0.18em] text-fidentta-teal">Distribución</span>
            <h3 class="mt-3 text-lg font-semibold text-text">QR y NFC incluidos</h3>
            <p class="mt-2 text-sm leading-6 text-text-secondary/70">Descarga e imprime el QR asociado a cada local.</p>
            <a href="{{ route('dashboard.locales', ['current_team' => $team]) }}" wire:navigate
                class="mt-4 inline-flex text-xs font-semibold text-fidentta-teal">Ver QR del programa
                &rarr;</a>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/3 p-5"><span
                class="text-xs font-semibold uppercase tracking-[0.18em] text-fidentta-purple">Seguridad</span>
            <h3 class="mt-3 text-lg font-semibold text-text">Sellos protegidos</h3>
            <p class="mt-2 text-sm leading-6 text-text-secondary/70">El cliente añade su tarjeta; el negocio valida
                cada visita.</p><span
                class="mt-4 inline-flex rounded-full bg-fidentta-purple/10 px-3 py-1 text-xs text-fidentta-purple">Configurado</span>
        </div>
    </section>
</div>
