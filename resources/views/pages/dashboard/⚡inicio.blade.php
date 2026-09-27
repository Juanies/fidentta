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
        class="relative overflow-hidden rounded-4xl border border-white/10 bg-fidentta-gradient-soft p-6 shadow-2xl shadow-fidentta-purple/10 md:p-8">
        <div class="absolute -right-20 -top-24 h-72 w-72 rounded-full bg-fidentta-cyan/15 blur-3xl"></div>
        <div class="relative flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <div
                    class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-3 py-1.5 text-[10px] font-semibold uppercase tracking-[0.2em] text-fidentta-cyan">
                    <span class="h-2 w-2 rounded-full bg-fidentta-teal"></span> Panel de negocio
                </div>
                <p class="text-sm text-text-secondary/70">Buenos días, {{ auth()->user()->email }}</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-text sm:text-4xl">Tu programa está
                    generando movimiento.</h1>
                <p class="mt-3 max-w-xl text-sm leading-7 text-text-secondary/80 sm:text-base">Aquí tienes lo
                    esencial para saber qué está pasando hoy y qué puedes hacer después.</p>
            </div>
            <a href="{{ route('dashboard.fidelizacion', ['current_team' => $team]) }}" wire:navigate
                class="inline-flex items-center justify-center gap-2 rounded-full bg-fidentta-cyan px-5 py-3 text-sm font-bold text-fidentta-navy transition hover:bg-white"><span
                    class="text-lg">+</span> Añadir un sello</a>
        </div>
    </header>

    

    {{ Auth::user()->email }}
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10">
            <div class="flex items-center justify-between"><span
                    class="flex h-10 w-10 items-center justify-center rounded-2xl bg-fidentta-cyan/12 text-fidentta-cyan">&#9673;</span><span
                    class="text-xs font-semibold text-fidentta-teal">+12%</span></div>
            <p class="mt-5 text-xs uppercase tracking-[0.16em] text-text-secondary/60">Clientes activos</p>
            <p class="mt-2 text-3xl font-semibold text-text">1.482</p>
            <p class="mt-1 text-xs text-text-secondary/60">vs. mes anterior</p>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10">
            <div class="flex items-center justify-between"><span
                    class="flex h-10 w-10 items-center justify-center rounded-2xl bg-fidentta-teal/12 text-fidentta-teal">&#10003;</span><span
                    class="text-xs font-semibold text-fidentta-teal">+8%</span></div>
            <p class="mt-5 text-xs uppercase tracking-[0.16em] text-text-secondary/60">Sellos entregados</p>
            <p class="mt-2 text-3xl font-semibold text-text">3.264</p>
            <p class="mt-1 text-xs text-text-secondary/60">este mes</p>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10">
            <div class="flex items-center justify-between"><span
                    class="flex h-10 w-10 items-center justify-center rounded-2xl bg-fidentta-purple/12 text-fidentta-purple">&#9733;</span><span
                    class="text-xs font-semibold text-fidentta-purple">+14%</span></div>
            <p class="mt-5 text-xs uppercase tracking-[0.16em] text-text-secondary/60">Recompensas canjeadas</p>
            <p class="mt-2 text-3xl font-semibold text-text">348</p>
            <p class="mt-1 text-xs text-text-secondary/60">este mes</p>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10">
            <div class="flex items-center justify-between"><span
                    class="flex h-10 w-10 items-center justify-center rounded-2xl bg-fidentta-blue/12 text-fidentta-blue">&#8599;</span><span
                    class="text-xs font-semibold text-fidentta-cyan">+9,2%</span></div>
            <p class="mt-5 text-xs uppercase tracking-[0.16em] text-text-secondary/60">Tasa de regreso</p>
            <p class="mt-2 text-3xl font-semibold text-text">68%</p>
            <p class="mt-1 text-xs text-text-secondary/60">clientes que repiten</p>
        </div>
    </section>

    <section class="grid gap-4 xl:grid-cols-[1.55fr,0.85fr]">
        <div class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10 md:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-fidentta-cyan">Actividad diaria</p>
                    <h2 class="mt-2 text-xl font-semibold text-text">Sellos y recompensas</h2>
                    <p class="mt-1 text-sm text-text-secondary/60">Últimos 7 días · actualizado hace 5 minutos
                    </p>
                </div><span
                    class="rounded-full border border-white/10 bg-white/4 px-3 py-1.5 text-xs text-text-secondary/70">Total:
                    486 acciones</span>
            </div>
            <div class="mt-6 rounded-2xl border border-white/10 bg-fidentta-navy/40 p-4"><svg class="h-60 w-full"
                    viewBox="0 0 640 240" fill="none" xmlns="http://www.w3.org/2000/svg" role="img"
                    aria-label="Actividad diaria de los últimos siete días">
                    <defs>
                        <linearGradient id="dashboardChart" x1="0" y1="0" x2="1" y2="0">
                            <stop stop-color="#06B6D4" />
                            <stop offset="1" stop-color="#18B981" />
                        </linearGradient>
                        <linearGradient id="dashboardFill" x1="0" y1="0" x2="0" y2="1">
                            <stop stop-color="#06B6D4" stop-opacity=".3" />
                            <stop offset="1" stop-color="#06B6D4" stop-opacity="0" />
                        </linearGradient>
                    </defs>
                    <g stroke="#E2E8F0" stroke-opacity=".14">
                        <path d="M44 174H604" />
                        <path d="M44 130H604" />
                        <path d="M44 86H604" />
                        <path d="M44 42H604" />
                    </g>
                    <path
                        d="M44 151C78 143 100 148 124 138C151 127 173 142 204 116C232 93 254 121 285 106C318 90 342 130 367 112C397 90 422 100 448 83C478 63 503 99 528 73C550 50 575 67 604 48V174H44V151Z"
                        fill="url(#dashboardFill)" />
                    <path
                        d="M44 151C78 143 100 148 124 138C151 127 173 142 204 116C232 93 254 121 285 106C318 90 342 130 367 112C397 90 422 100 448 83C478 63 503 99 528 73C550 50 575 67 604 48"
                        stroke="url(#dashboardChart)" stroke-width="4" stroke-linecap="round" />
                    <g fill="#E6EEF8" font-size="12" font-family="sans-serif"><text x="35" y="202">Lun
                            18</text><text x="111" y="202">Mar 19</text><text x="187" y="202">Mié 20</text><text x="263"
                            y="202">Jue 21</text><text x="339" y="202">Vie 22</text><text x="415" y="202">Sáb
                            23</text><text x="491" y="202">Dom 24</text><text x="570" y="202">Hoy</text>
                    </g>
                </svg></div>
            <div class="mt-4 flex flex-wrap items-center gap-5 text-xs text-text-secondary/70"><span
                    class="flex items-center gap-2"><span
                        class="h-2.5 w-2.5 rounded-full bg-fidentta-cyan"></span>Sellos entregados</span><span
                    class="flex items-center gap-2"><span
                        class="h-2.5 w-2.5 rounded-full bg-fidentta-teal"></span>Recompensas canjeadas</span>
            </div>
        </div>

        <div class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10 md:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-fidentta-teal">Estado del programa</p>
                    <h2 class="mt-2 text-xl font-semibold text-text">Todo listo para crecer</h2>
                </div><span
                    class="flex h-10 w-10 items-center justify-center rounded-2xl bg-fidentta-teal/12 text-fidentta-teal">&#10003;</span>
            </div>
            <div class="mt-6 flex items-center gap-4">
                <div class="relative flex h-24 w-24 items-center justify-center rounded-full"
                    style="background: conic-gradient(#18b981 0 82%, rgba(255,255,255,.08) 82% 100%);">
                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-full bg-[#18233f] text-xl font-bold text-text">
                        82%</div>
                </div>
                <div>
                    <p class="font-semibold text-text">Configuración completa</p>
                    <p class="mt-1 text-sm leading-6 text-text-secondary/65">Solo falta compartir tu QR con el
                        equipo.</p>
                </div>
            </div>
            <div class="mt-6 space-y-3">
                <div
                    class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/2 p-3 text-sm">
                    <span class="text-text-secondary/75">Tarjeta publicada</span><span
                        class="text-fidentta-teal">Listo</span>
                </div>
                <div
                    class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/2 p-3 text-sm">
                    <span class="text-text-secondary/75">Recompensa creada</span><span
                        class="text-fidentta-teal">Listo</span>
                </div><a href="{{ route('dashboard.fidelizacion', ['current_team' => $team]) }}" wire:navigate
                    class="flex items-center justify-between rounded-2xl border border-fidentta-cyan/30 bg-fidentta-cyan/10 p-3 text-sm font-semibold text-fidentta-cyan transition hover:bg-fidentta-cyan/20"><span>Compartir
                        QR</span><span>&rarr;</span></a>
            </div>
        </div>
    </section>

    <section class="grid gap-4 lg:grid-cols-[1.1fr,0.9fr]">
        <div class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-fidentta-cyan">Actividad reciente</p>
                    <h2 class="mt-2 text-xl font-semibold text-text">Últimas interacciones</h2>
                </div><a href="{{ route('dashboard.clientes', ['current_team' => $team]) }}" wire:navigate
                    class="text-xs font-semibold text-fidentta-cyan hover:text-white">Ver clientes &rarr;</a>
            </div>
            <div class="mt-5 space-y-3">
                <div
                    class="flex items-center justify-between gap-4 rounded-2xl border border-white/10 bg-white/2 p-3.5">
                    <div class="flex items-center gap-3"><span
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-fidentta-cyan/12 text-fidentta-cyan">A</span>
                        <div>
                            <p class="text-sm font-medium text-text">Ana Torres canjeó un café gratis</p>
                            <p class="text-xs text-text-secondary/60">Hace 12 minutos</p>
                        </div>
                    </div><span
                        class="rounded-full bg-fidentta-teal/15 px-2 py-1 text-[10px] font-semibold text-fidentta-teal">OK</span>
                </div>
                <div
                    class="flex items-center justify-between gap-4 rounded-2xl border border-white/10 bg-white/2 p-3.5">
                    <div class="flex items-center gap-3"><span
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-fidentta-purple/12 text-fidentta-purple">J</span>
                        <div>
                            <p class="text-sm font-medium text-text">Juan Pérez completó su perfil</p>
                            <p class="text-xs text-text-secondary/60">Hace 1 hora</p>
                        </div>
                    </div><span
                        class="rounded-full bg-fidentta-cyan/15 px-2 py-1 text-[10px] font-semibold text-fidentta-cyan">NUEVO</span>
                </div>
            </div>
        </div>
        <div class="rounded-3xl border border-fidentta-cyan/20 bg-fidentta-cyan/5 p-5">
            <p class="text-xs uppercase tracking-[0.18em] text-fidentta-cyan">Siguiente mejor acción</p>
            <h2 class="mt-3 text-xl font-semibold text-text">Comparte el QR con tu equipo</h2>
            <p class="mt-2 text-sm leading-6 text-text-secondary/70">Tus empleados podrán reconocer cada visita
                y
                entregar sellos sin complicar el proceso.</p><a
                href="{{ route('dashboard.fidelizacion', ['current_team' => $team]) }}" wire:navigate
                class="mt-5 inline-flex rounded-full bg-fidentta-cyan px-4 py-2.5 text-sm font-bold text-fidentta-navy transition hover:bg-white">Abrir
                fidelización &rarr;</a>
        </div>
    </section>
</div>
