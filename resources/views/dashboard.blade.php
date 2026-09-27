<x-layouts::app.sidebar :title="__('Dashboard')">
    <livewire:pages::teams.pending-invitations-modal />

    <div class="flex h-full w-full flex-1 flex-col gap-6 p-2 md:p-5">
        <header
            class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-fidentta-gradient-soft p-6 shadow-2xl shadow-fidentta-purple/10 md:p-8">
            <div
                class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(6,182,212,0.25),transparent_35%)]">
            </div>
            <div class="absolute -bottom-12 right-10 h-40 w-40 rounded-full bg-fidentta-cyan/20 blur-3xl"></div>

            <div class="relative flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
                <div class="max-w-xl">
                    <div
                        class="mb-3 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.2em] text-fidentta-cyan">
                        <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true">
                            <path
                                d="M10 1.5C5.86 1.5 2.5 4.86 2.5 9C2.5 12.4 4.77 15.3 7.9 16.22V18.5L11.2 15.7C12.9 15.54 14.45 14.77 15.62 13.6C17.05 12.18 17.82 10.2 17.82 8.07C17.82 4.91 14.9 1.5 10 1.5ZM10.2 10.9H8.4V9.1H10.2V10.9ZM10.2 7.5H8.4V5.7H10.2V7.5Z"
                                fill="currentColor" />
                        </svg>
                        Panel de negocio
                    </div>
                    <h1 class="text-3xl font-semibold tracking-tight text-text sm:text-4xl">Resumen de fidelización</h1>
                    <p class="mt-3 max-w-lg text-sm text-text-secondary/80 sm:text-base">
                        Mantén a tus clientes activos, mide el rendimiento y acelera la conversión con tu programa de
                        recompensas.
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-3 xl:min-w-[420px]">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm">
                        <p class="text-[10px] uppercase tracking-[0.18em] text-text-secondary/70">Clientes</p>
                        <div class="mt-2 flex items-end justify-between">
                            <span class="text-2xl font-semibold text-text">2.4k</span>
                            <span
                                class="rounded-full bg-fidentta-teal/15 px-2 py-1 text-[10px] font-bold text-fidentta-teal">+12%</span>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm">
                        <p class="text-[10px] uppercase tracking-[0.18em] text-text-secondary/70">Canjes</p>
                        <div class="mt-2 flex items-end justify-between">
                            <span class="text-2xl font-semibold text-text">348</span>
                            <span
                                class="rounded-full bg-fidentta-cyan/15 px-2 py-1 text-[10px] font-bold text-fidentta-cyan">+8%</span>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-3 backdrop-blur-sm">
                        <p class="text-[10px] uppercase tracking-[0.18em] text-text-secondary/70">Ticket</p>
                        <div class="mt-2 flex items-end justify-between">
                            <span class="text-2xl font-semibold text-text">$89</span>
                            <span
                                class="rounded-full bg-fidentta-purple/15 px-2 py-1 text-[10px] font-bold text-fidentta-purple">+15%</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-[1.75rem] border border-white/10 bg-white/[0.03] p-5 shadow-xl shadow-black/10">
                <div class="flex items-center justify-between">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-fidentta-cyan/12 text-fidentta-cyan">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true">
                            <path
                                d="M16 19V5C16 3.9 15.1 3 14 3H10C8.9 3 8 3.9 8 5V19M6 8H3.5C2.67 8 2 8.67 2 9.5V17.5C2 18.33 2.67 19 3.5 19H6M18 8H20.5C21.33 8 22 8.67 22 9.5V17.5C22 18.33 21.33 19 20.5 19H18"
                                stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                    <span
                        class="rounded-full bg-fidentta-teal/15 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-fidentta-teal">Activo</span>
                </div>
                <div class="mt-6">
                    <p class="text-xs uppercase tracking-[0.18em] text-text-secondary/60">Clientes activos</p>
                    <p class="mt-3 text-3xl font-semibold text-text">1,482</p>
                </div>
                <div class="mt-5 flex items-center gap-2 text-sm text-fidentta-teal">
                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true">
                        <path d="M12 10L8 14L4 10M8 14V2" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    8.4% vs el mes pasado
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-white/10 bg-white/[0.03] p-5 shadow-xl shadow-black/10">
                <div class="flex items-center justify-between">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-fidentta-purple/12 text-fidentta-purple">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true">
                            <path
                                d="M12 2L15.2 7.1L21 8.1L16.9 12.3L17.8 18.1L12 15.5L6.2 18.1L7.1 12.3L3 8.1L8.8 7.1L12 2Z"
                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </div>
                    <span
                        class="rounded-full bg-fidentta-purple/15 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-fidentta-purple">Premios</span>
                </div>
                <div class="mt-6">
                    <p class="text-xs uppercase tracking-[0.18em] text-text-secondary/60">Puntos canjeados</p>
                    <p class="mt-3 text-3xl font-semibold text-text">18.9k</p>
                </div>
                <div class="mt-5 flex items-center gap-2 text-sm text-fidentta-purple">
                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true">
                        <path d="M8 13V3M8 3L4 7M8 3L12 7" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    14% más recompensas redeemed
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-white/10 bg-white/[0.03] p-5 shadow-xl shadow-black/10">
                <div class="flex items-center justify-between">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-fidentta-blue/12 text-fidentta-blue">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true">
                            <path
                                d="M3 8.5C3 7.12 4.12 6 5.5 6H18.5C19.88 6 21 7.12 21 8.5V15.5C21 16.88 19.88 18 18.5 18H5.5C4.12 18 3 16.88 3 15.5V8.5Z"
                                stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" />
                            <path d="M3 9.5H21" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                            <path d="M7 13.5H11" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" />
                        </svg>
                    </div>
                    <span
                        class="rounded-full bg-fidentta-cyan/15 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-fidentta-cyan">Card</span>
                </div>
                <div class="mt-6">
                    <p class="text-xs uppercase tracking-[0.18em] text-text-secondary/60">Tarjeta activa</p>
                    <p class="mt-3 text-3xl font-semibold text-text">87%</p>
                </div>
                <div class="mt-5 flex items-center gap-2 text-sm text-fidentta-blue">
                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true">
                        <path d="M12 6L8 2L4 6M8 2V10" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    9.2% de uso semanal
                </div>
            </div>
        </section>

        <section class="grid gap-4 xl:grid-cols-[1.6fr,0.9fr]">
            <div class="rounded-[1.75rem] border border-white/10 bg-white/[0.03] p-5 shadow-xl shadow-black/10">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-fidentta-cyan">Rendimiento</p>
                        <h2 class="mt-2 text-xl font-semibold text-text">Fidelización por semanas</h2>
                    </div>
                    <div
                        class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1 text-xs font-medium text-text-secondary/80">
                        Últimos 8 semanas</div>
                </div>

                <div class="mt-6 rounded-2xl border border-white/10 bg-fidentta-navy/40 p-4">
                    <svg class="h-52 w-full" viewBox="0 0 640 220" fill="none" xmlns="http://www.w3.org/2000/svg"
                        aria-label="Gráfico de rendimiento">
                        <defs>
                            <linearGradient id="chartStroke" x1="0" y1="0" x2="1"
                                y2="0">
                                <stop offset="0%" stop-color="#06B6D4" />
                                <stop offset="100%" stop-color="#18B981" />
                            </linearGradient>
                            <linearGradient id="chartFill" x1="0" y1="0" x2="0"
                                y2="1">
                                <stop offset="0%" stop-color="#06B6D4" stop-opacity="0.38" />
                                <stop offset="100%" stop-color="#06B6D4" stop-opacity="0.03" />
                            </linearGradient>
                        </defs>
                        <g opacity="0.4" stroke="#E2E8F0" stroke-opacity="0.18">
                            <path d="M40 180H600" />
                            <path d="M40 140H600" />
                            <path d="M40 100H600" />
                            <path d="M40 60H600" />
                        </g>
                        <path
                            d="M40 158C90 154 120 135 155 128C196 120 214 78 260 84C306 90 325 146 366 126C408 106 425 55 470 62C517 69 540 116 600 90V180H40V158Z"
                            fill="url(#chartFill)" />
                        <path
                            d="M40 158C90 154 120 135 155 128C196 120 214 78 260 84C306 90 325 146 366 126C408 106 425 55 470 62C517 69 540 116 600 90"
                            stroke="url(#chartStroke)" stroke-width="4" stroke-linecap="round" />
                        <g fill="#E6EEF8" font-size="12" font-family="sans-serif">
                            <text x="30" y="190">W1</text>
                            <text x="105" y="190">W2</text>
                            <text x="180" y="190">W3</text>
                            <text x="255" y="190">W4</text>
                            <text x="330" y="190">W5</text>
                            <text x="405" y="190">W6</text>
                            <text x="480" y="190">W7</text>
                            <text x="555" y="190">W8</text>
                        </g>
                    </svg>
                </div>
            </div>

            <div class="space-y-4">
                <div class="rounded-[1.75rem] border border-white/10 bg-white/[0.03] p-5 shadow-xl shadow-black/10">
                    <p class="text-xs uppercase tracking-[0.18em] text-fidentta-cyan">Acciones rápidas</p>
                    <div class="mt-4 space-y-3 text-sm text-text">
                        <div
                            class="flex items-center justify-between rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2.5">
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-8 w-8 items-center justify-center rounded-xl bg-fidentta-cyan/12 text-fidentta-cyan">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path
                                            d="M16 21V19C16 17.34 14.66 16 13 16H7C5.34 16 4 17.34 4 19V21M13 7C13 9.21 11.21 11 9 11C6.79 11 5 9.21 5 7C5 4.79 6.79 3 9 3C11.21 3 13 4.79 13 7ZM20 21V19C19.94 17.62 18.73 16.5 17.3 16.3M16 3.13C17.17 3.46 18.1 4.42 18.38 5.63C18.66 6.84 18.38 8.11 17.6 9"
                                            stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span>Invitar clientes</span>
                            </div>
                            <span class="text-xs text-text-secondary/70">Hoje</span>
                        </div>
                        <div
                            class="flex items-center justify-between rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2.5">
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-8 w-8 items-center justify-center rounded-xl bg-fidentta-purple/12 text-fidentta-purple">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path
                                            d="M12 2.5L14.8 7.7L20.5 8.5L16.5 12.5L17.4 18.2L12 15.7L6.6 18.2L7.5 12.5L3.5 8.5L9.2 7.7L12 2.5Z"
                                            stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </span>
                                <span>Crear premio</span>
                            </div>
                            <span class="text-xs text-text-secondary/70">2 min</span>
                        </div>
                        <div
                            class="flex items-center justify-between rounded-xl border border-white/10 bg-white/[0.03] px-3 py-2.5">
                            <div class="flex items-center gap-3">
                                <span
                                    class="flex h-8 w-8 items-center justify-center rounded-xl bg-fidentta-blue/12 text-fidentta-blue">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <path
                                            d="M3 8.5C3 7.12 4.12 6 5.5 6H18.5C19.88 6 21 7.12 21 8.5V15.5C21 16.88 19.88 18 18.5 18H5.5C4.12 18 3 16.88 3 15.5V8.5Z"
                                            stroke="currentColor" stroke-width="1.7" stroke-linejoin="round" />
                                        <path d="M3 9.5H21" stroke="currentColor" stroke-width="1.7"
                                            stroke-linecap="round" />
                                    </svg>
                                </span>
                                <span>Ver tarjeta</span>
                            </div>
                            <span class="text-xs text-text-secondary/70">Live</span>
                        </div>
                    </div>
                </div>

                <div
                    class="rounded-[1.75rem] border border-white/10 bg-gradient-to-br from-fidentta-cyan/10 to-fidentta-purple/10 p-5 shadow-xl shadow-fidentta-purple/10">
                    <p class="text-xs uppercase tracking-[0.18em] text-fidentta-cyan">Tu tarjeta</p>
                    <div
                        class="mt-4 rounded-[1.5rem] border border-white/15 bg-linear-to-br from-fidentta-blue via-fidentta-purple to-fidentta-navy p-4 text-white shadow-2xl shadow-fidentta-purple/20">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] uppercase tracking-[0.22em] text-white/70">Fiddenta</span>
                            <span class="rounded-full bg-white/10 px-2 py-1 text-[10px] font-semibold">Gold</span>
                        </div>
                        <div class="mt-8 flex items-center justify-between">
                            <div>
                                <p class="text-[10px] uppercase tracking-[0.18em] text-white/70">Titular</p>
                                <p class="mt-2 text-lg font-semibold">Cafe Laté</p>
                            </div>
                            <svg class="h-9 w-9" viewBox="0 0 40 40" fill="none"
                                xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path
                                    d="M20 4C11.16 4 4 11.16 4 20C4 28.84 11.16 36 20 36C28.84 36 36 28.84 36 20C36 11.16 28.84 4 20 4ZM20 8.5C26.35 8.5 31.5 13.65 31.5 20C31.5 26.35 26.35 31.5 20 31.5C13.65 31.5 8.5 26.35 8.5 20C8.5 13.65 13.65 8.5 20 8.5ZM11.5 19.5H28.5V22.5H11.5V19.5ZM18 11.5H22V28.5H18V11.5Z"
                                    fill="currentColor" />
                            </svg>
                        </div>
                        <div class="mt-8 flex items-end justify-between text-sm text-white/80">
                            <span>•••• 4821</span>
                            <span>12/28</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-4 lg:grid-cols-[1.3fr,0.7fr]">
            <div class="rounded-[1.75rem] border border-white/10 bg-white/[0.03] p-5 shadow-xl shadow-black/10">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.18em] text-fidentta-cyan">Actividad reciente</p>
                        <h2 class="mt-2 text-xl font-semibold text-text">Últimas interacciones</h2>
                    </div>
                    <button
                        class="rounded-full border border-white/10 bg-white/[0.04] px-3 py-1.5 text-xs font-semibold text-text-secondary/80 hover:border-fidentta-cyan/50 hover:text-fidentta-cyan">Ver
                        todo</button>
                </div>

                <div class="mt-5 space-y-3">
                    <div
                        class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/[0.02] p-3.5">
                        <div class="flex items-center gap-3">
                            <span
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-fidentta-cyan/12 text-fidentta-cyan">A</span>
                            <div>
                                <p class="font-medium text-text">Ana Torres canjeó un café gratis</p>
                                <p class="text-xs text-text-secondary/60">Hace 12 minutos</p>
                            </div>
                        </div>
                        <span
                            class="rounded-full bg-fidentta-teal/15 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-fidentta-teal">OK</span>
                    </div>

                    <div
                        class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/[0.02] p-3.5">
                        <div class="flex items-center gap-3">
                            <span
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-fidentta-purple/12 text-fidentta-purple">J</span>
                            <div>
                                <p class="font-medium text-text">Juan Pérez completó su perfil</p>
                                <p class="text-xs text-text-secondary/60">Hace 1 hora</p>
                            </div>
                        </div>
                        <span
                            class="rounded-full bg-fidentta-cyan/15 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-fidentta-cyan">NUEVO</span>
                    </div>

                    <div
                        class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/[0.02] p-3.5">
                        <div class="flex items-center gap-3">
                            <span
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-fidentta-blue/12 text-fidentta-blue">M</span>
                            <div>
                                <p class="font-medium text-text">Marta Gómez acumuló 320 puntos</p>
                                <p class="text-xs text-text-secondary/60">Hace 3 horas</p>
                            </div>
                        </div>
                        <span
                            class="rounded-full bg-fidentta-purple/15 px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.14em] text-fidentta-purple">+320</span>
                    </div>
                </div>
            </div>

            <div class="rounded-[1.75rem] border border-white/10 bg-white/[0.03] p-5 shadow-xl shadow-black/10">
                <p class="text-xs uppercase tracking-[0.18em] text-fidentta-cyan">Progreso</p>
                <h2 class="mt-2 text-xl font-semibold text-text">Meta del mes</h2>

                <div class="mt-6">
                    <div class="flex items-center justify-between text-sm text-text-secondary/80">
                        <span>Objetivo</span>
                        <span>68%</span>
                    </div>
                    <div class="mt-3 h-3 overflow-hidden rounded-full bg-white/10">
                        <div
                            class="h-full w-[68%] rounded-full bg-gradient-to-r from-fidentta-cyan via-fidentta-blue to-fidentta-teal">
                        </div>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    <div class="rounded-2xl border border-white/10 bg-white/[0.02] p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-text-secondary/80">Clientes activos</span>
                            <span class="text-sm font-semibold text-text">1,482</span>
                        </div>
                        <div class="mt-2 h-2.5 overflow-hidden rounded-full bg-white/10">
                            <div class="h-full w-[72%] rounded-full bg-fidentta-cyan"></div>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-white/10 bg-white/[0.02] p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-text-secondary/80">Canjes reales</span>
                            <span class="text-sm font-semibold text-text">348</span>
                        </div>
                        <div class="mt-2 h-2.5 overflow-hidden rounded-full bg-white/10">
                            <div class="h-full w-[58%] rounded-full bg-fidentta-teal"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layouts::app.sidebar>
