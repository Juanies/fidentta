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
            class="rounded-4xl border border-white/10 bg-fidentta-gradient-soft p-6 shadow-2xl shadow-fidentta-purple/10 md:p-8">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-fidentta-cyan">Constructor de
                        fidelización</p>
                    <h1 class="text-3xl font-semibold tracking-tight text-text md:text-4xl">Diseña una tarjeta que tus
                        clientes quieran usar</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-7 text-text-secondary/80 md:text-base">Configura la
                        mecánica, la recompensa y la forma de compartirla. La tarjeta no es un pago: es el camino más
                        corto hacia la próxima visita.</p>
                </div>
                <div class="text-left lg:text-right">
                    <p class="text-xs text-text-secondary/60">Progreso del programa</p>
                    <p class="mt-1 text-2xl font-semibold text-text">2 de 4 pasos</p>
                </div>
            </div>
            <div class="mt-7 h-1.5 overflow-hidden rounded-full bg-white/10">
                <div class="h-full w-1/2 rounded-full bg-fidentta-cyan"></div>
            </div>
        </header>

        <nav aria-label="Pasos del programa" class="grid gap-3 sm:grid-cols-4">
            <div class="flex items-center gap-3 rounded-2xl border border-fidentta-teal/25 bg-fidentta-teal/10 p-4">
                <span
                    class="flex h-8 w-8 items-center justify-center rounded-full bg-fidentta-teal text-sm font-bold text-fidentta-navy">&#10003;</span>
                <div>
                    <p class="text-[10px] uppercase tracking-[0.12em] text-fidentta-teal">Completado</p>
                    <p class="text-sm font-semibold text-text">Negocio</p>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-2xl border border-fidentta-cyan bg-fidentta-cyan/10 p-4"><span
                    class="flex h-8 w-8 items-center justify-center rounded-full bg-fidentta-cyan text-sm font-bold text-fidentta-navy">2</span>
                <div>
                    <p class="text-[10px] uppercase tracking-[0.12em] text-fidentta-cyan">Ahora</p>
                    <p class="text-sm font-semibold text-text">Sellos</p>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/2 p-4"><span
                    class="flex h-8 w-8 items-center justify-center rounded-full bg-white/10 text-sm font-bold text-text-secondary">3</span>
                <div>
                    <p class="text-[10px] uppercase tracking-[0.12em] text-text-secondary/60">Después</p>
                    <p class="text-sm font-semibold text-text">Recompensa</p>
                </div>
            </div>
            <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/2 p-4"><span
                    class="flex h-8 w-8 items-center justify-center rounded-full bg-white/10 text-sm font-bold text-text-secondary">4</span>
                <div>
                    <p class="text-[10px] uppercase tracking-[0.12em] text-text-secondary/60">Final</p>
                    <p class="text-sm font-semibold text-text">Publicar</p>
                </div>
            </div>
        </nav>

        <section class="grid gap-4 xl:grid-cols-[1.05fr,0.95fr]">
            <div class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10 md:p-7">
                <div class="flex items-start justify-between gap-5">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-fidentta-cyan">Paso 2 · Sellos
                        </p>
                        <h2 class="mt-2 text-2xl font-semibold text-text">Define la meta de tu cliente</h2>
                        <p class="mt-2 max-w-lg text-sm leading-6 text-text-secondary/70">Una meta clara hace que el
                            premio se sienta alcanzable. Puedes cambiarla cuando quieras.</p>
                    </div><span
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-fidentta-cyan/12 text-xl text-fidentta-cyan">&#9733;</span>
                </div>
                <div
                    class="mt-7 flex items-center gap-4 rounded-3xl border border-fidentta-cyan/30 bg-fidentta-cyan/10 p-4">
                    <button type="button" aria-label="Reducir sellos"
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/10 text-2xl text-text transition hover:bg-white/20">−</button>
                    <div class="flex-1 text-center">
                        <p class="text-5xl font-black tracking-tight text-text">8</p>
                        <p class="mt-1 text-xs uppercase tracking-[0.16em] text-text-secondary/60">sellos para completar
                        </p>
                    </div><button type="button" aria-label="Aumentar sellos"
                        class="flex h-11 w-11 items-center justify-center rounded-2xl bg-fidentta-cyan text-2xl font-bold text-fidentta-navy transition hover:bg-white">+</button>
                </div>
                <div class="mt-6 grid gap-3 sm:grid-cols-3"><button type="button"
                        class="rounded-2xl border border-white/10 bg-white/2 p-4 text-left transition hover:border-fidentta-cyan/50">
                        <p class="text-lg font-bold text-text">5 sellos</p>
                        <p class="mt-1 text-xs text-text-secondary/60">Para compras frecuentes</p>
                    </button><button type="button"
                        class="rounded-2xl border border-fidentta-cyan bg-fidentta-cyan/10 p-4 text-left">
                        <p class="text-lg font-bold text-text">8 sellos</p>
                        <p class="mt-1 text-xs text-fidentta-cyan">Recomendado para Café Laté</p>
                    </button><button type="button"
                        class="rounded-2xl border border-white/10 bg-white/2 p-4 text-left transition hover:border-fidentta-cyan/50">
                        <p class="text-lg font-bold text-text">10 sellos</p>
                        <p class="mt-1 text-xs text-text-secondary/60">Para una recompensa premium</p>
                    </button></div>
                <div class="mt-7 rounded-2xl border border-white/10 bg-white/2 p-4">
                    <div class="flex items-center gap-3"><span
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-fidentta-teal/12 text-fidentta-teal">&#10003;</span>
                        <div>
                            <p class="text-sm font-semibold text-text">Una visita, un sello</p>
                            <p class="mt-1 text-xs text-text-secondary/65">Solo el negocio o un administrador puede
                                validar el escaneo.</p>
                        </div>
                    </div>
                </div>
                <div class="mt-7 flex items-center justify-between border-t border-white/10 pt-5"><a
                        href="{{ route('dashboard', ['current_team' => $team]) }}" wire:navigate
                        class="rounded-full border border-white/15 px-4 py-2 text-sm font-semibold text-text-secondary transition hover:border-white/30 hover:text-text">Volver</a><button
                        type="button"
                        class="rounded-full bg-fidentta-cyan px-5 py-2.5 text-sm font-bold text-fidentta-navy transition hover:bg-white">Continuar
                        a recompensa &rarr;</button></div>
            </div>

            <div
                class="rounded-3xl border border-white/10 bg-linear-to-br from-fidentta-blue via-fidentta-purple to-fidentta-navy p-6 text-white shadow-2xl shadow-fidentta-purple/20 md:p-7">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-white/60">Preview en móvil</p>
                        <h2 class="mt-2 text-2xl font-semibold">Así la verá tu cliente</h2>
                    </div><span
                        class="rounded-full bg-white/10 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.14em]">En
                        vivo</span>
                </div>
                <div class="mx-auto mt-7 max-w-sm rounded-4xl border border-white/15 bg-white/10 p-5 backdrop-blur-sm">
                    <div class="flex items-center gap-3"><span
                            class="flex h-11 w-11 items-center justify-center rounded-full bg-white/15 text-lg font-bold">C</span>
                        <div>
                            <p class="font-semibold">Mi tarjeta de sellos</p>
                            <p class="text-xs text-white/60">Café Laté · Programa activo</p>
                        </div>
                    </div>
                    <div class="mt-8 grid grid-cols-4 gap-3">
                        @for ($i = 1; $i <= 8; $i++)
                            <span
                                class="flex aspect-square items-center justify-center rounded-full border border-white/30 text-sm {{ $i <= 3 ? 'bg-fidentta-cyan text-fidentta-navy' : 'bg-white/5 text-white/50' }}">
                                @if ($i <= 3)
                                    &#10003;@else{{ $i }}
                                @endif
                            </span>
                        @endfor
                    </div>
                    <div class="mt-7 border-t border-white/15 pt-4">
                        <p class="text-xs text-white/60">Te faltan 5 sellos para desbloquear</p>
                        <p class="mt-1 text-lg font-semibold">Bebida gratis</p>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-between text-sm text-white/70"><span>Progreso de
                        ejemplo</span><span class="font-semibold text-white">3/8</span></div>
                <div class="mt-2 h-2 overflow-hidden rounded-full bg-white/15">
                    <div class="h-full w-[37.5%] rounded-full bg-fidentta-cyan"></div>
                </div>
            </div>
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
                <p class="mt-2 text-sm leading-6 text-text-secondary/70">Comparte la tarjeta desde el mostrador, el
                    ticket o una mesa.</p><a href="{{ route('dashboard.fidelizacion', ['current_team' => $team]) }}"
                    wire:navigate class="mt-4 inline-flex text-xs font-semibold text-fidentta-teal">Ver QR del programa
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
