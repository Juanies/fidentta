<?php

use Livewire\Component;

new class extends Component {
    public $team;
    public $design;

    public function mount(): void
    {
        $this->team = auth()->user()->currentTeam;
        abort_unless($this->team !== null, 404);
        $this->design = $this->team->cardDesign()->where('is_active', true)->first();
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-2 md:p-5">
    <header class="flex flex-col gap-3 border-b border-border pb-6">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-brand">Configuración del negocio</p>
        <h1 class="text-3xl font-semibold tracking-tight text-ink">Perfil y programa</h1>
        <p class="max-w-2xl text-sm leading-6 text-muted-foreground">Consulta los datos actuales y continúa la
            configuración desde la sección correspondiente.</p>
    </header>

    <section class="grid gap-5 lg:grid-cols-2">
        <article class="rounded-xl border border-border bg-card p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand">Perfil del equipo</p>
                    <h2 class="mt-2 text-lg font-semibold text-ink">{{ $team->name }}</h2>
                </div>
                <span
                    class="rounded-lg bg-brand-soft px-3 py-2 text-xs font-semibold text-brand">{{ $team->is_active ? 'Activo' : 'Inactivo' }}</span>
            </div>
            <dl class="mt-5 divide-y divide-border">
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-sm text-muted-foreground">Cuenta propietaria</dt>
                    <dd class="truncate text-sm font-medium text-ink">{{ auth()->user()->email }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-sm text-muted-foreground">Locales activos</dt>
                    <dd class="text-sm font-medium text-ink">{{ $team->locations()->where('is_active', true)->count() }}
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-sm text-muted-foreground">Registro de clientes</dt>
                    <dd class="text-sm font-medium text-ink">
                        {{ match ($team->customer_registration_type) {'none' => 'Sin datos','normal' => 'Email y contraseña','custom' => 'Personalizado',default => 'No configurado'} }}
                    </dd>
                </div>
            </dl>
            <a href="{{ route('profile.edit') }}" wire:navigate
                class="mt-4 inline-flex text-sm font-semibold text-brand hover:text-brand-700">Cuenta y seguridad <span
                    class="ml-1" aria-hidden="true">&rarr;</span></a>
        </article>

        <article class="rounded-xl border border-border bg-card p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand">Tarjeta de fidelidad</p>
                    <h2 class="mt-2 text-lg font-semibold text-ink">
                        {{ $design ? 'Programa configurado' : 'Programa pendiente' }}</h2>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-accent/20 text-accent"
                    aria-hidden="true">&#9733;</span>
            </div>
            @if ($design)
                <dl class="mt-5 divide-y divide-border">
                    <div class="flex items-center justify-between gap-4 py-3">
                        <dt class="text-sm text-muted-foreground">Sellos para completar</dt>
                        <dd class="text-sm font-semibold text-ink">{{ $design->stamps_required }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-3">
                        <dt class="text-sm text-muted-foreground">Recompensa</dt>
                        <dd class="max-w-[60%] text-right text-sm font-semibold text-ink">{{ $design->reward }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-3">
                        <dt class="text-sm text-muted-foreground">Estado</dt>
                        <dd
                            class="text-sm font-semibold {{ $design->is_active ? 'text-green-700' : 'text-muted-foreground' }}">
                            {{ $design->is_active ? 'Publicado' : 'Pausado' }}</dd>
                    </div>
                </dl>
            @else
                <p class="mt-4 text-sm leading-6 text-muted-foreground">Completa la configuración de sellos y recompensa
                    para publicar tu programa.</p>
            @endif
            <a href="{{ route('dashboard.fidelizacion', ['current_team' => $team]) }}" wire:navigate
                class="mt-4 inline-flex rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700">Gestionar
                fidelización</a>
        </article>
    </section>

    <section class="grid gap-4 sm:grid-cols-2">
        <a href="{{ route('dashboard.locales', ['current_team' => $team]) }}" wire:navigate
            class="flex items-center justify-between gap-4 rounded-xl border border-border bg-card p-5 transition hover:border-brand/40">
            <div>
                <p class="text-sm font-semibold text-ink">Gestionar locales</p>
                <p class="mt-1 text-sm text-muted-foreground">Direcciones, QR y actividad por local.</p>
            </div><span class="text-xl text-brand" aria-hidden="true">&rarr;</span>
        </a>
        <a href="{{ route('dashboard.clientes', ['current_team' => $team]) }}" wire:navigate
            class="flex items-center justify-between gap-4 rounded-xl border border-border bg-card p-5 transition hover:border-brand/40">
            <div>
                <p class="text-sm font-semibold text-ink">Ver clientes</p>
                <p class="mt-1 text-sm text-muted-foreground">Filtra por local y revisa el progreso.</p>
            </div><span class="text-xl text-brand" aria-hidden="true">&rarr;</span>
        </a>
    </section>
</div>
