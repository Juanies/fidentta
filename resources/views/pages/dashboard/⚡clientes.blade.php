<?php

use Livewire\Component;
use App\Models\Team;

new class extends Component {
    public $team;

    public function mount()
    {
        $team = request()->route('current_team');
        $this->team = $team;
    }
}; ?>
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-2 md:p-5">
        <header
            class="rounded-[2rem] border border-white/10 bg-fidentta-gradient-soft p-6 shadow-2xl shadow-fidentta-purple/10 md:p-8">
            <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-fidentta-cyan">Clientes</p>
                    <h1 class="text-3xl font-semibold tracking-tight text-text">Base de clientes</h1>
                </div>
                <button
                    class="inline-flex items-center gap-2 rounded-xl bg-fidentta-cyan px-4 py-2.5 text-sm font-bold text-fidentta-navy shadow-lg shadow-fidentta-cyan/20 hover:bg-white transition">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true">
                        <path d="M10 4V16M4 10H16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                    </svg>
                    Nuevo cliente
                </button>
            </div>
        </header>

        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-[1.75rem] border border-white/10 bg-white/[0.03] p-5">
                <p class="text-xs uppercase tracking-[0.18em] text-text-secondary/60">Activos</p>
                <p class="mt-3 text-3xl font-semibold text-text">1,482</p>
                <p class="mt-2 text-sm text-fidentta-teal">+8.4% este mes</p>
            </div>
            <div class="rounded-[1.75rem] border border-white/10 bg-white/[0.03] p-5">
                <p class="text-xs uppercase tracking-[0.18em] text-text-secondary/60">Nuevos</p>
                <p class="mt-3 text-3xl font-semibold text-text">219</p>
                <p class="mt-2 text-sm text-fidentta-cyan">+17 esta semana</p>
            </div>
            <div class="rounded-[1.75rem] border border-white/10 bg-white/[0.03] p-5">
                <p class="text-xs uppercase tracking-[0.18em] text-text-secondary/60">Sin actividad</p>
                <p class="mt-3 text-3xl font-semibold text-text">84</p>
                <p class="mt-2 text-sm text-fidentta-purple">-6% vs semana pasada</p>
            </div>
        </section>

        <section class="rounded-[1.75rem] border border-white/10 bg-white/[0.03] p-5 shadow-xl shadow-black/10">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-fidentta-cyan">Listado</p>
                    <h2 class="mt-2 text-xl font-semibold text-text">Clientes recientes</h2>
                </div>
                <span
                    class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1 text-xs text-text-secondary/80">Últimos
                    7 días</span>
            </div>

            <div class="space-y-3">
                @php($clientes = [['nombre' => 'Ana Torres', 'email' => 'ana@correo.com', 'puntos' => '3.420', 'estado' => 'Activa'], ['nombre' => 'Juan Pérez', 'email' => 'juan@correo.com', 'puntos' => '1.890', 'estado' => 'Activa'], ['nombre' => 'Marta Gómez', 'email' => 'marta@correo.com', 'puntos' => '2.150', 'estado' => 'Activa'], ['nombre' => 'Luis Ramírez', 'email' => 'luis@correo.com', 'puntos' => '840', 'estado' => 'Inactiva']])

                @foreach ($clientes as $cliente)
                    <div
                        class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/[0.02] p-3.5">
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-fidentta-cyan/12 text-sm font-bold text-fidentta-cyan">
                                {{ strtoupper(substr($cliente['nombre'], 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-text">{{ $cliente['nombre'] }}</p>
                                <p class="text-xs text-text-secondary/60">{{ $cliente['email'] }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm text-text-secondary/80">{{ $cliente['puntos'] }} pts</span>
                            <span
                                class="rounded-full {{ $cliente['estado'] === 'Activa' ? 'bg-fidentta-teal/15 text-fidentta-teal' : 'bg-fidentta-purple/15 text-fidentta-purple' }} px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.14em]">
                                {{ $cliente['estado'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    </div>
