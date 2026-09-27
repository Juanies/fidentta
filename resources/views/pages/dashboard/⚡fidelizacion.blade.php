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
            <div class="absolute -right-20 -top-24 h-64 w-64 rounded-full bg-fidentta-cyan/15 blur-3xl"></div>
            <div class="relative flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
                <div class="max-w-2xl">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-fidentta-cyan">Programa de
                        sellos</p>
                    <h1 class="text-3xl font-semibold tracking-tight text-text md:text-4xl">Cada escaneo puede
                        convertirse en una visita más.</h1>
                    <p class="mt-3 max-w-xl text-sm leading-7 text-text-secondary/80 md:text-base">El cliente escanea
                        para guardar su tarjeta. Tu equipo escanea para añadir un sello. Un mismo QR, dos experiencias
                        seguras.</p>
                </div>
                <div class="flex items-center gap-3 rounded-2xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm">
                    <span
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-fidentta-teal/15 text-fidentta-teal">&#10003;</span>
                    <div>
                        <p class="text-xs text-text-secondary/60">Programa activo</p>
                        <p class="text-sm font-semibold text-text">Café Laté</p>
                    </div>
                </div>
            </div>
        </header>

        <section class="grid gap-4 xl:grid-cols-[1.2fr,0.8fr]">
            <div class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10 md:p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-fidentta-cyan">Flujo principal</p>
                        <h2 class="mt-2 text-xl font-semibold text-text">¿Qué ocurre al escanear?</h2>
                    </div><span
                        class="rounded-full bg-fidentta-cyan/10 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-fidentta-cyan">QR
                        + sello</span>
                </div>
                <div class="mt-6 grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-fidentta-cyan/30 bg-fidentta-cyan/10 p-4"><span
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-fidentta-cyan text-fidentta-navy">1</span>
                        <h3 class="mt-4 text-sm font-semibold text-text">Escanea</h3>
                        <p class="mt-2 text-xs leading-5 text-text-secondary/70">El cliente o el equipo abre el mismo
                            QR.</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/2 p-4"><span
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/10 text-fidentta-cyan">2</span>
                        <h3 class="mt-4 text-sm font-semibold text-text">Identifica el rol</h3>
                        <p class="mt-2 text-xs leading-5 text-text-secondary/70">Fidentta reconoce si es cliente o
                            negocio.</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/2 p-4"><span
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-fidentta-teal/15 text-fidentta-teal">3</span>
                        <h3 class="mt-4 text-sm font-semibold text-text">Añade el sello</h3>
                        <p class="mt-2 text-xs leading-5 text-text-secondary/70">El negocio valida la visita y suma un
                            sello.</p>
                    </div>
                </div>
                <div
                    class="mt-6 flex flex-col gap-3 rounded-2xl border border-fidentta-purple/20 bg-fidentta-purple/10 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-text">Tu QR está listo para compartir</p>
                        <p class="mt-1 text-xs text-text-secondary/70">Colócalo en el mostrador o en el ticket de
                            compra.</p>
                    </div><a href="#qr-programa"
                        class="rounded-full bg-fidentta-cyan px-4 py-2 text-center text-xs font-bold text-fidentta-navy transition hover:bg-white">Ver
                        QR del programa</a>
                </div>
            </div>

            <div id="qr-programa"
                class="rounded-3xl border border-white/10 bg-linear-to-br from-fidentta-blue via-fidentta-purple to-fidentta-navy p-6 text-white shadow-2xl shadow-fidentta-purple/20">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-white/60">QR del programa</p>
                        <h2 class="mt-2 text-2xl font-semibold">Café Laté</h2>
                    </div><span
                        class="rounded-full bg-white/10 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.14em]">Activo</span>
                </div>
                <div class="mx-auto mt-6 flex h-44 w-44 items-center justify-center rounded-2xl bg-white p-4">
                    <div class="grid h-full w-full grid-cols-7 gap-1" aria-label="Código QR de demostración">
                        @for ($i = 0; $i < 49; $i++)
                            <span
                                class="rounded-sm {{ in_array($i, [0, 1, 2, 7, 9, 14, 15, 16, 6, 12, 13, 20, 24, 27, 29, 31, 34, 35, 38, 40, 42, 43, 45, 47, 48], true) ? 'bg-fidentta-navy' : 'bg-slate-100' }}"></span>
                        @endfor
                    </div>
                </div>
                <div class="mt-5 flex items-center justify-between text-xs text-white/65"><span>Escaneos este
                        mes</span><span class="font-semibold text-white">1.284</span></div>
            </div>
        </section>

        <section class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10 md:p-6">
                <div class="flex items-center gap-3"><span
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-fidentta-cyan/12 text-fidentta-cyan">&#128241;</span>
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-fidentta-cyan">Experiencia del cliente</p>
                        <h2 class="mt-1 text-lg font-semibold text-text">Si no está registrado</h2>
                    </div>
                </div>
                <div class="mt-5 space-y-3">
                    <div class="flex gap-3 rounded-2xl border border-white/10 bg-white/2 p-4"><span
                            class="font-semibold text-fidentta-cyan">01</span>
                        <div>
                            <p class="text-sm font-semibold text-text">Ve tu programa</p>
                            <p class="mt-1 text-xs leading-5 text-text-secondary/70">Después de escanear, el cliente ve
                                el nombre, la recompensa y los sellos disponibles.</p>
                        </div>
                    </div>
                    <div class="flex gap-3 rounded-2xl border border-white/10 bg-white/2 p-4"><span
                            class="font-semibold text-fidentta-cyan">02</span>
                        <div>
                            <p class="text-sm font-semibold text-text">Añade su tarjeta</p>
                            <p class="mt-1 text-xs leading-5 text-text-secondary/70">Se registra o guarda la tarjeta
                                para recibir el sello de esta visita.</p>
                        </div>
                    </div>
                </div><span
                    class="mt-5 inline-flex rounded-full bg-fidentta-cyan/10 px-3 py-1.5 text-xs font-semibold text-fidentta-cyan">Entrada
                    sin fricción</span>
            </div>
            <div class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10 md:p-6">
                <div class="flex items-center gap-3"><span
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-fidentta-teal/12 text-fidentta-teal">&#9733;</span>
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-fidentta-teal">Experiencia del negocio</p>
                        <h2 class="mt-1 text-lg font-semibold text-text">Si es equipo o admin</h2>
                    </div>
                </div>
                <div class="mt-5 space-y-3">
                    <div class="flex gap-3 rounded-2xl border border-white/10 bg-white/2 p-4"><span
                            class="font-semibold text-fidentta-teal">01</span>
                        <div>
                            <p class="text-sm font-semibold text-text">Accede a modo negocio</p>
                            <p class="mt-1 text-xs leading-5 text-text-secondary/70">El usuario inicia sesión y Fidentta
                                habilita las acciones del establecimiento.</p>
                        </div>
                    </div>
                    <div class="flex gap-3 rounded-2xl border border-white/10 bg-white/2 p-4"><span
                            class="font-semibold text-fidentta-teal">02</span>
                        <div>
                            <p class="text-sm font-semibold text-text">Añade y valida sellos</p>
                            <p class="mt-1 text-xs leading-5 text-text-secondary/70">Puede sumar un sello, revisar el
                                historial y evitar duplicados.</p>
                        </div>
                    </div>
                </div><span
                    class="mt-5 inline-flex rounded-full bg-fidentta-teal/10 px-3 py-1.5 text-xs font-semibold text-fidentta-teal">Acción
                    protegida</span>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-3xl border border-white/10 bg-white/3 p-5">
                <p class="text-xs uppercase tracking-[0.18em] text-text-secondary/60">Sellos entregados</p>
                <p class="mt-3 text-3xl font-semibold text-text">18.942</p>
                <p class="mt-2 text-sm text-fidentta-teal">+14% este mes</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-white/3 p-5">
                <p class="text-xs uppercase tracking-[0.18em] text-text-secondary/60">Tarjetas activas</p>
                <p class="mt-3 text-3xl font-semibold text-text">1.482</p>
                <p class="mt-2 text-sm text-fidentta-cyan">68% vuelven a escanear</p>
            </div>
            <div class="rounded-3xl border border-white/10 bg-white/3 p-5">
                <p class="text-xs uppercase tracking-[0.18em] text-text-secondary/60">Próxima recompensa</p>
                <p class="mt-3 text-3xl font-semibold text-text">8 sellos</p>
                <p class="mt-2 text-sm text-fidentta-purple">Café gratis</p>
            </div>
        </section>
    </div>
