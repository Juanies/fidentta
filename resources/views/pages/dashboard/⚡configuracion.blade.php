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
            <p class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-fidentta-cyan">Configuración</p>
            <h1 class="text-3xl font-semibold tracking-tight text-text">Ajustes del negocio</h1>
        </header>

        <section class="grid gap-4 md:grid-cols-2">
            <div class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10">
                <p class="text-xs uppercase tracking-[0.18em] text-fidentta-cyan">Perfil</p>
                <div class="mt-4 space-y-4 text-sm text-text-secondary/80">
                    <div class="rounded-2xl border border-white/10 bg-white/2 p-4">
                        <p class="text-xs uppercase tracking-[0.14em] text-text-secondary/60">Nombre</p>
                        <p class="mt-2 font-medium text-text">Cafe Laté</p>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/2 p-4">
                        <p class="text-xs uppercase tracking-[0.14em] text-text-secondary/60">Email</p>
                        <p class="mt-2 font-medium text-text">hola@cafelate.com</p>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10">
                <p class="text-xs uppercase tracking-[0.18em] text-fidentta-cyan">Preferencias</p>
                <div class="mt-4 space-y-3 text-sm">
                    <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/2 p-3.5">
                        <span class="text-text-secondary/80">Notificaciones por email</span>
                        <span
                            class="rounded-full bg-fidentta-teal/15 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-fidentta-teal">On</span>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/2 p-3.5">
                        <span class="text-text-secondary/80">Membresía avanzada</span>
                        <span
                            class="rounded-full bg-fidentta-cyan/15 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-fidentta-cyan">Activa</span>
                    </div>
                    <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/2 p-3.5">
                        <span class="text-text-secondary/80">Modo oscuro</span>
                        <span
                            class="rounded-full bg-fidentta-purple/15 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-fidentta-purple">Tema</span>
                    </div>
                </div>
            </div>
        </section>

        <section class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10 md:p-6">
            <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-fidentta-cyan">Conexiones TPV</p>
                    <h2 class="mt-2 text-xl font-semibold text-text">Conecta tus ventas con Fidentta</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-text-secondary/70">
                        Sincroniza clientes, compras y recompensas desde las herramientas que ya utilizas en tu negocio.
                    </p>
                </div>
                <span
                    class="rounded-full bg-fidentta-teal/15 px-3 py-1.5 text-[10px] font-semibold uppercase tracking-[0.14em] text-fidentta-teal">
                    1 conexión activa
                </span>
            </div>

            <div class="mt-6 grid gap-4 lg:grid-cols-3">
                <div
                    class="flex min-h-52 flex-col rounded-2xl border border-white/10 bg-white/2 p-5 transition hover:border-fidentta-cyan/40 hover:bg-white/4">
                    <div class="flex items-start justify-between gap-4">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#95bf47]/15 text-xl font-black text-[#95bf47]">
                            S</div>
                        <span
                            class="rounded-full bg-fidentta-teal/15 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-fidentta-teal">Conectado</span>
                    </div>
                    <h3 class="mt-5 text-base font-semibold text-text">Shopify</h3>
                    <p class="mt-2 flex-1 text-sm leading-6 text-text-secondary/70">Importa pedidos y clientes de tu
                        tienda online para activar recompensas automáticamente.</p>
                    <button type="button"
                        class="mt-5 w-fit text-xs font-semibold text-fidentta-cyan transition hover:text-white">Gestionar
                        conexión <span aria-hidden="true">&rarr;</span></button>
                </div>

                <div
                    class="flex min-h-52 flex-col rounded-2xl border border-white/10 bg-white/2 p-5 transition hover:border-fidentta-cyan/40 hover:bg-white/4">
                    <div class="flex items-start justify-between gap-4">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#ff6b35]/15 text-sm font-black text-[#ff8a62]">
                            L.app</div>
                        <span
                            class="rounded-full bg-white/10 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-text-secondary/70">Disponible</span>
                    </div>
                    <h3 class="mt-5 text-base font-semibold text-text">Last.app</h3>
                    <p class="mt-2 flex-1 text-sm leading-6 text-text-secondary/70">Conecta tu TPV de hostelería y
                        registra cada visita sin cambiar la forma de trabajar de tu equipo.</p>
                    <button type="button"
                        class="mt-5 w-fit rounded-full border border-fidentta-cyan/30 px-3 py-1.5 text-xs font-semibold text-fidentta-cyan transition hover:bg-fidentta-cyan/10">Conectar</button>
                </div>

                <div
                    class="flex min-h-52 flex-col rounded-2xl border border-white/10 bg-white/2 p-5 transition hover:border-fidentta-cyan/40 hover:bg-white/4">
                    <div class="flex items-start justify-between gap-4">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#5b5ce2]/15 text-xl font-black text-[#8586ff]">
                            A</div>
                        <span
                            class="rounded-full bg-white/10 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.12em] text-text-secondary/70">Disponible</span>
                    </div>
                    <h3 class="mt-5 text-base font-semibold text-text">Agora</h3>
                    <p class="mt-2 flex-1 text-sm leading-6 text-text-secondary/70">Lleva las ventas de tu TPV a tus
                        métricas de fidelización y conoce mejor a tus clientes.</p>
                    <button type="button"
                        class="mt-5 w-fit rounded-full border border-fidentta-cyan/30 px-3 py-1.5 text-xs font-semibold text-fidentta-cyan transition hover:bg-fidentta-cyan/10">Conectar</button>
                </div>
            </div>

            <div
                class="mt-5 flex items-start gap-3 rounded-2xl border border-fidentta-purple/20 bg-fidentta-purple/10 p-4 text-sm text-text-secondary/80">
                <span class="mt-0.5 text-fidentta-purple">&#9432;</span>
                <p>Las conexiones sincronizan los datos necesarios para la fidelización. Puedes revocarlas en cualquier
                    momento desde esta pantalla.</p>
            </div>
        </section>
    </div>
