<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div class="landing-page min-h-screen overflow-hidden bg-background text-ink">
    <header class="relative z-20 mx-auto flex max-w-7xl items-center justify-between gap-5 px-5 py-5 sm:px-8 lg:px-10">
        <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5" aria-label="Fidentta, inicio">
            <img class="size-9" src="{{ asset('logo.png') }}" alt="">
            <span class="text-xl font-bold tracking-tight">fidentta<span class="text-mintd">.</span></span>
        </a>

        <nav aria-label="Navegación principal" class="hidden md:block">
            <ul class="flex items-center gap-8 text-sm font-medium text-ink2">
                <li><a href="#como-funciona" class="transition hover:text-ink">Cómo funciona</a></li>
                <li><a href="#herramientas" class="transition hover:text-ink">Herramientas</a></li>
                <li><a href="#planes" class="transition hover:text-ink">Planes</a></li>
                <li><a href="#stand" class="transition hover:text-ink">Stand QR</a></li>
                <li><a href="#preguntas" class="transition hover:text-ink">Preguntas</a></li>
            </ul>
        </nav>

        <div class="flex items-center gap-3">
            @if (Route::has('login'))
                <a href="{{ route('login') }}"
                    class="hidden px-2 py-2 text-sm font-semibold text-ink2 transition hover:text-ink sm:inline-flex">Iniciar
                    sesión</a>
            @endif
            <a href="{{ route('register') }}"
                class="inline-flex items-center justify-center rounded-md bg-ink px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-mintd hover:text-ink">Crear
                cuenta</a>
        </div>
    </header>

    @if (session('stripe_checkout_error'))
        <div class="mx-auto mt-6 flex max-w-7xl flex-col gap-3 border-s-4 border-amber-500 bg-amber-50 px-5 py-4 text-amber-950 sm:flex-row sm:items-center sm:justify-between sm:px-8 lg:px-10"
            role="alert">
            <p>{{ session('stripe_checkout_error') }}</p>
            <a href="{{ route('subscription-checkout') }}"
                class="shrink-0 font-semibold underline underline-offset-4">Intentar de nuevo</a>
        </div>
    @endif

    <main>
        <section class="relative isolate overflow-hidden">
            <div class="landing-hero-shape landing-hero-shape--mint" aria-hidden="true"></div>
            <div class="landing-hero-shape landing-hero-shape--coral" aria-hidden="true"></div>

            <div
                class="mx-auto grid max-w-7xl gap-12 px-5 pb-20 pt-10 sm:px-8 sm:pb-24 sm:pt-14 lg:grid-cols-[0.88fr_1.12fr] lg:items-center lg:gap-12 lg:px-10 lg:pb-28 lg:pt-16">
                <div class="landing-reveal relative z-10">
                    <p class="mb-6 inline-flex items-center gap-2 text-sm font-semibold text-mintd">
                        <span class="h-px w-8 bg-mintd"></span> Fidelización para negocios locales
                    </p>
                    <h1 class="max-w-[11ch] font-display text-5xl leading-[0.98] sm:text-6xl lg:text-7xl">Una visita, un nuevo comienzo</h1>
                    <p class="mt-6 max-w-xl text-lg leading-8 text-ink2 sm:text-xl">Crea una experiencia de fidelidad
                        sencilla: una tarjeta digital, un QR por local y recompensas que invitan a volver.</p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center justify-center gap-3 rounded-md bg-mint px-6 py-3.5 font-semibold text-ink transition hover:bg-mintd">
                            Crea tu programa <span aria-hidden="true">&rarr;</span>
                        </a>
                        <a href="#como-funciona"
                            class="inline-flex items-center justify-center rounded-md border border-border bg-white/70 px-6 py-3.5 font-semibold text-ink transition hover:bg-white">Conoce
                            cómo funciona</a>
                    </div>
                    <div class="mt-8 flex flex-wrap gap-x-6 gap-y-2 text-sm text-ink2">
                        <span class="flex items-center gap-2"><span class="text-mintd"
                                aria-hidden="true">&#10003;</span> Sin app para el cliente</span>
                        <span class="flex items-center gap-2"><span class="text-mintd"
                                aria-hidden="true">&#10003;</span> QR por local</span>
                    </div>
                </div>

                <div class="landing-reveal landing-reveal--delay relative mx-auto w-full max-w-2xl lg:ml-auto">
                    <div class="landing-photo-frame relative overflow-hidden rounded-lg">
                        <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=1500&q=85"
                            alt="Interior cálido de una cafetería de barrio"
                            class="absolute inset-0 size-full object-cover" fetchpriority="high">
                        <div class="absolute inset-0 bg-ink/10" aria-hidden="true"></div>
                        <div
                            class="absolute right-4 top-4 rounded-md bg-white/90 px-3 py-2 text-xs font-semibold text-ink shadow-soft sm:right-6 sm:top-6">
                            <span class="mr-2 inline-block size-2 rounded-full bg-mintd"></span>Tu negocio, más cerca
                        </div>
                    </div>

                    <div
                        class="landing-pass-card absolute -bottom-8 left-3 w-[min(18rem,calc(100%-1.5rem))] rounded-lg bg-white p-4 shadow-card sm:-bottom-7 sm:left-8 sm:p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-mintd">Club de la casa
                                </p>
                                <p class="mt-1 text-lg font-semibold">Tu café de siempre</p>
                            </div>
                            <div class="flex size-9 shrink-0 items-center justify-center rounded-md bg-mint/20 text-lg text-mintd"
                                aria-hidden="true">&#9733;</div>
                        </div>
                        <div class="mt-4 grid grid-cols-5 gap-2" aria-label="Cinco de ocho sellos">
                            <span class="landing-stamp landing-stamp--filled">&#10003;</span>
                            <span class="landing-stamp landing-stamp--filled">&#10003;</span>
                            <span class="landing-stamp landing-stamp--filled">&#10003;</span>
                            <span class="landing-stamp landing-stamp--filled">&#10003;</span>
                            <span class="landing-stamp landing-stamp--filled">&#10003;</span>
                            <span class="landing-stamp"></span>
                            <span class="landing-stamp"></span>
                            <span class="landing-stamp"></span>
                            <span class="col-span-2 self-center text-right text-xs font-medium text-ink2">5 de 8
                                sellos</span>
                        </div>
                    </div>
                    <div
                        class="landing-photo-note absolute -right-2 bottom-8 hidden items-center gap-3 rounded-md bg-ink px-4 py-3 text-white shadow-card sm:flex">
                        <span class="flex size-9 items-center justify-center rounded-md bg-mint text-ink"
                            aria-hidden="true">&#10003;</span>
                        <span><span class="block text-xs text-white/65">Una visita más</span><span
                                class="font-semibold">Recompensa desbloqueada</span></span>
                    </div>
                </div>
            </div>
        </section>

        <section class="border-y border-border bg-white/55" aria-label="Resumen del programa">
            <div class="mx-auto grid max-w-7xl gap-6 px-5 py-7 sm:grid-cols-3 sm:px-8 lg:px-10">
                <div class="flex items-center gap-4 sm:justify-center"><span
                        class="font-display text-3xl text-mintd">01</span><span
                        class="text-sm font-medium text-ink2">Tarjeta digital de sellos</span></div>
                <div class="flex items-center gap-4 border-border sm:justify-center sm:border-x"><span
                        class="font-display text-3xl text-mintd">02</span><span class="text-sm font-medium text-ink2">Un
                        QR para cada local</span></div>
                <div class="flex items-center gap-4 sm:justify-center"><span
                        class="font-display text-3xl text-mintd">03</span><span
                        class="text-sm font-medium text-ink2">Recompensas a tu medida</span></div>
            </div>
        </section>

        <section id="como-funciona" class="mx-auto max-w-7xl px-5 py-20 sm:px-8 sm:py-24 lg:px-10 lg:py-28">
            <div class="grid gap-10 lg:grid-cols-[0.8fr_1.2fr] lg:gap-20">
                <div>
                    <p class="text-sm font-bold uppercase tracking-[0.14em] text-mintd">Claro desde el primer día</p>
                    <h2 class="mt-4 max-w-[13ch] font-display text-4xl leading-tight sm:text-5xl">De la primera visita
                        a la próxima recompensa.</h2>
                    <p class="mt-5 max-w-lg text-lg leading-8 text-ink2">Pon en marcha tu programa sin complicar la
                        experiencia en el mostrador.</p>
                </div>
                <ol class="divide-y divide-border border-y border-border">
                    <li class="grid gap-3 py-6 sm:grid-cols-[4rem_1fr] sm:gap-5 sm:py-7">
                        <span class="font-display text-3xl text-mintd">01</span>
                        <div>
                            <h3 class="text-lg font-semibold">Diseña la tarjeta</h3>
                            <p class="mt-2 max-w-xl leading-7 text-ink2">Elige el número de sellos y define una
                                recompensa que tenga sentido para tu negocio.</p>
                        </div>
                    </li>
                    <li class="grid gap-3 py-6 sm:grid-cols-[4rem_1fr] sm:gap-5 sm:py-7">
                        <span class="font-display text-3xl text-mintd">02</span>
                        <div>
                            <h3 class="text-lg font-semibold">Comparte el QR</h3>
                            <p class="mt-2 max-w-xl leading-7 text-ink2">Cada local tiene su propio código para que tus
                                clientes se registren desde el navegador.</p>
                        </div>
                    </li>
                    <li class="grid gap-3 py-6 sm:grid-cols-[4rem_1fr] sm:gap-5 sm:py-7">
                        <span class="font-display text-3xl text-mintd">03</span>
                        <div>
                            <h3 class="text-lg font-semibold">Acompaña el progreso</h3>
                            <p class="mt-2 max-w-xl leading-7 text-ink2">Añade sellos y consulta la actividad de
                                clientes y locales desde tu panel.</p>
                        </div>
                    </li>
                </ol>
            </div>
        </section>

        <section id="herramientas" class="relative overflow-hidden bg-ink text-white">
            <div class="landing-feature-shape" aria-hidden="true"></div>
            <div
                class="relative mx-auto grid max-w-7xl gap-12 px-5 py-20 sm:px-8 sm:py-24 lg:grid-cols-[1fr_0.9fr] lg:items-center lg:gap-20 lg:px-10 lg:py-28">
                <div class="relative z-10">
                    <p class="text-sm font-semibold uppercase tracking-[0.14em] text-mint">Todo conectado</p>
                    <h2 class="mt-4 max-w-[12ch] font-display text-4xl leading-tight sm:text-5xl">Una experiencia que
                        se siente tuya.</h2>
                    <p class="mt-5 max-w-xl text-lg leading-8 text-white/70">Personaliza el programa para reflejar tu
                        marca y gestiona cada punto de contacto desde un solo lugar.</p>
                    <ul class="mt-8 grid gap-4 sm:grid-cols-2">
                        <li class="border-t border-white/20 pt-4"><span
                                class="text-sm font-semibold text-mint">01</span>
                            <p class="mt-2 font-semibold">Identidad propia</p>
                            <p class="mt-1 text-sm leading-6 text-white/65">Colores, sellos y recompensa a tu manera.
                            </p>
                        </li>
                        <li class="border-t border-white/20 pt-4"><span
                                class="text-sm font-semibold text-mint">02</span>
                            <p class="mt-2 font-semibold">Varios locales</p>
                            <p class="mt-1 text-sm leading-6 text-white/65">Organiza códigos y actividad por ubicación.
                            </p>
                        </li>
                        <li class="border-t border-white/20 pt-4"><span
                                class="text-sm font-semibold text-mint">03</span>
                            <p class="mt-2 font-semibold">Registro flexible</p>
                            <p class="mt-1 text-sm leading-6 text-white/65">Adapta los datos que solicitas a tus
                                clientes.</p>
                        </li>
                        <li class="border-t border-white/20 pt-4"><span
                                class="text-sm font-semibold text-mint">04</span>
                            <p class="mt-2 font-semibold">Wallet opcional</p>
                            <p class="mt-1 text-sm leading-6 text-white/65">Pases digitales disponibles al configurar
                                el proveedor.</p>
                        </li>
                    </ul>
                </div>
                <figure class="relative z-10">
                    <div class="landing-secondary-image overflow-hidden rounded-lg">
                        <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=1200&q=85"
                            alt="Cliente utilizando su teléfono durante una compra" class="size-full object-cover"
                            loading="lazy">
                    </div>
                    <figcaption class="mt-3 text-xs text-white/55">Una experiencia sencilla para el equipo y para quien
                        vuelve.</figcaption>
                </figure>
            </div>
        </section>

        <section id="planes" class="border-y border-border bg-white/55">
            <div
                class="mx-auto grid max-w-7xl gap-10 px-5 py-20 sm:px-8 sm:py-24 lg:grid-cols-[0.8fr_1.2fr] lg:items-center lg:gap-16 lg:px-10 lg:py-28">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.14em] text-mintd">Un plan, todo el programa
                    </p>
                    <h2 class="mt-4 max-w-[12ch] font-display text-4xl leading-tight sm:text-5xl">Tu fidelización, sin
                        complicaciones.</h2>
                    <p class="mt-5 max-w-lg text-lg leading-8 text-ink2">Todas las herramientas para poner en marcha tu
                        programa y acompañar cada visita desde el panel.</p>
                </div>

                <article class="relative overflow-hidden border border-border bg-white p-6 shadow-card sm:p-8">
                    <div class="absolute right-0 top-0 h-2 w-28 bg-mint" aria-hidden="true"></div>
                    <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-sm font-bold uppercase tracking-[0.14em] text-mintd">Fidentta completo</p>
                            <p class="mt-3 flex items-baseline gap-2 text-ink">
                                <span class="font-display text-6xl leading-none">49&nbsp;€</span>
                                <span class="text-sm text-ink2">/ mes</span>
                            </p>
                        </div>
                        <span
                            class="inline-flex w-fit items-center gap-2 bg-mint/20 px-3 py-2 text-xs font-semibold text-ink">
                            <span class="size-2 rounded-full bg-mintd" aria-hidden="true"></span> Todo incluido
                        </span>
                    </div>

                    <ul class="mt-8 grid gap-x-8 gap-y-4 border-t border-border pt-6 sm:grid-cols-2">
                        <li class="flex gap-3 text-sm leading-6 text-ink2"><span class="font-bold text-mintd"
                                aria-hidden="true">&#10003;</span><span>Tarjetas personalizadas con tu marca, colores y
                                recompensa.</span></li>
                        <li class="flex gap-3 text-sm leading-6 text-ink2"><span class="font-bold text-mintd"
                                aria-hidden="true">&#10003;</span><span>Sellos configurables y registro de visitas
                                desde el panel.</span></li>
                        <li class="flex gap-3 text-sm leading-6 text-ink2"><span class="font-bold text-mintd"
                                aria-hidden="true">&#10003;</span><span>Gestión de clientes, búsqueda y filtros por
                                local y progreso.</span></li>
                        <li class="flex gap-3 text-sm leading-6 text-ink2"><span class="font-bold text-mintd"
                                aria-hidden="true">&#10003;</span><span>Un código QR y actividad para cada uno de tus
                                locales.</span></li>
                        <li class="flex gap-3 text-sm leading-6 text-ink2"><span class="font-bold text-mintd"
                                aria-hidden="true">&#10003;</span><span>Métricas de clientes, visitas, sellos y uso de
                                Wallet.</span></li>
                        <li class="flex gap-3 text-sm leading-6 text-ink2"><span class="font-bold text-mintd"
                                aria-hidden="true">&#10003;</span><span>Campos de registro adaptables a las necesidades
                                de tu negocio.</span></li>
                    </ul>

                    <div
                        class="mt-8 flex flex-col gap-3 border-t border-border pt-6 sm:flex-row sm:items-center sm:justify-between">
                        <p class="text-xs leading-5 text-ink2">La suscripción se completa después de crear tu cuenta.
                        </p>
                        <a href="{{ route('register') }}"
                            class="inline-flex shrink-0 items-center justify-center gap-3 rounded-md bg-ink px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-mint hover:text-ink">
                            Elegir este plan <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </article>
            </div>
        </section>

        <section id="stand" class="relative isolate overflow-hidden">
            <div class="landing-hero-shape landing-hero-shape--mint" aria-hidden="true"></div>
            <div
                class="mx-auto grid max-w-7xl gap-12 px-5 py-20 sm:px-8 sm:py-24 lg:grid-cols-[1fr_0.9fr] lg:items-center lg:gap-20 lg:px-10 lg:py-28">
                <div class="relative mx-auto w-full max-w-xl">
                    <div class="landing-secondary-image relative aspect-4/3 overflow-hidden rounded-lg">
                        <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=1200&q=85"
                            alt="Cliente pagando en un comercio local" class="size-full object-cover" loading="lazy">
                        <div class="absolute inset-0 bg-ink/15" aria-hidden="true"></div>
                    </div>
                    <div class="absolute bottom-4 left-1/2 w-40 -translate-x-1/2 sm:bottom-6">
                        <div
                            class="relative z-10 mx-auto flex min-h-40 flex-col items-center justify-center gap-2 border border-border bg-white p-4 text-center shadow-card">
                            <span class="text-xs font-bold tracking-wide text-ink">fidentta<span
                                    class="text-mintd">.</span></span>
                            <flux:icon name="qr-code" class="size-16 text-ink" aria-hidden="true" />
                            <span class="text-[10px] font-semibold text-ink2">Escanea y suma sellos</span>
                        </div>
                        <div class="mx-auto h-10 w-32 bg-mintd shadow-soft"
                            style="clip-path: polygon(12% 0, 88% 0, 100% 100%, 0 100%);" aria-hidden="true"></div>
                        <div class="mx-auto h-3 w-40 bg-ink" aria-hidden="true"></div>
                    </div>
                    <span
                        class="absolute left-4 top-4 bg-white/90 px-3 py-2 text-xs font-semibold text-ink shadow-soft">Tu
                        QR, siempre a la vista</span>
                </div>

                <div class="relative z-10">
                    <p class="text-sm font-semibold uppercase tracking-[0.14em] text-mintd">Para el mostrador</p>
                    <h2 class="mt-4 max-w-[12ch] font-display text-4xl leading-tight sm:text-5xl">Haz que escanear sea
                        lo más fácil.</h2>
                    <p class="mt-5 max-w-xl text-lg leading-8 text-ink2">Un soporte físico para colocar el QR de tu
                        local junto a la caja. Tus clientes podrán abrir el programa y registrarse desde su móvil.</p>
                    <ul class="mt-7 space-y-3 text-sm text-ink2">
                        <li class="flex gap-3"><span class="font-bold text-mintd"
                                aria-hidden="true">&#10003;</span><span>Diseñado para tener presencia en el punto de
                                pago.</span></li>
                        <li class="flex gap-3"><span class="font-bold text-mintd"
                                aria-hidden="true">&#10003;</span><span>Asocia el QR al local que quieras
                                promocionar.</span></li>
                        <li class="flex gap-3"><span class="font-bold text-mintd"
                                aria-hidden="true">&#10003;</span><span>Compatible con el registro desde el navegador,
                                sin app.</span></li>
                    </ul>
                    <a href="mailto:{{ config('mail.from.address') }}?subject={{ rawurlencode('Quiero comprar un stand Fidentta') }}"
                        class="mt-8 inline-flex items-center justify-center gap-3 rounded-md border border-ink bg-ink px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-mint hover:text-ink">
                        Consultar y comprar un stand <span aria-hidden="true">&rarr;</span>
                    </a>
                    <p class="mt-3 text-xs leading-5 text-ink2">Al continuar se abrirá tu aplicación de correo para
                        solicitar el stand.</p>
                </div>
            </div>
        </section>

        <section id="preguntas" class="mx-auto max-w-4xl px-5 py-20 sm:px-8 sm:py-24">
            <div class="text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.14em] text-mintd">Preguntas frecuentes</p>
                <h2 class="mt-4 font-display text-4xl sm:text-5xl">Lo importante, claro.</h2>
            </div>
            <div class="mt-10 divide-y divide-border border-y border-border">
                <details class="group py-5">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-5 font-semibold">
                        ¿Mis clientes necesitan descargar una aplicación?<span
                            class="text-2xl text-mintd transition group-open:rotate-45" aria-hidden="true">+</span>
                    </summary>
                    <p class="mt-3 max-w-2xl leading-7 text-ink2">No. El registro se abre en el navegador al escanear
                        el QR. Añadir un pase a Wallet es opcional y requiere configurar el proveedor.</p>
                </details>
                <details class="group py-5">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-5 font-semibold">
                        ¿Puedo gestionar varios locales?<span
                            class="text-2xl text-mintd transition group-open:rotate-45" aria-hidden="true">+</span>
                    </summary>
                    <p class="mt-3 max-w-2xl leading-7 text-ink2">Sí. Puedes crear locales, asociar un QR a cada uno y
                        consultar la actividad por ubicación.</p>
                </details>
                <details class="group py-5">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-5 font-semibold">
                        ¿Qué puedo personalizar?<span class="text-2xl text-mintd transition group-open:rotate-45"
                            aria-hidden="true">+</span></summary>
                    <p class="mt-3 max-w-2xl leading-7 text-ink2">Puedes configurar el número de sellos, la recompensa,
                        los colores de la tarjeta y el modo de registro.</p>
                </details>
            </div>
        </section>

        <section
            class="relative isolate mx-5 mb-10 overflow-hidden rounded-lg bg-mint sm:mx-8 lg:mx-auto lg:max-w-7xl">
            <div class="landing-cta-shape" aria-hidden="true"></div>
            <div
                class="relative z-10 flex flex-col gap-8 px-7 py-12 sm:px-12 sm:py-14 lg:flex-row lg:items-center lg:justify-between lg:px-16">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.14em] text-ink2">Fidentta para tu negocio</p>
                    <h2 class="mt-3 font-display text-3xl leading-tight sm:text-4xl">Haz que la próxima visita ya tenga
                        un motivo.</h2>
                    <p class="mt-4 max-w-xl leading-7 text-ink2">Empieza con una tarjeta digital y crea un programa a
                        la medida de tu local.</p>
                </div>
                <a href="{{ route('register') }}"
                    class="inline-flex shrink-0 items-center justify-center gap-3 rounded-md bg-ink px-6 py-3.5 font-semibold text-white transition hover:bg-white hover:text-ink">Crear
                    mi cuenta <span aria-hidden="true">&rarr;</span></a>
            </div>
        </section>
    </main>

    <footer class="border-t border-border bg-white/55">
        <div
            class="mx-auto flex max-w-7xl flex-col gap-4 px-5 py-7 text-sm text-ink2 sm:flex-row sm:items-center sm:justify-between sm:px-8 lg:px-10">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-ink"><img
                    src="{{ asset('logo.png') }}" alt="" class="size-7">Fidentta<span
                    class="text-mintd">.</span></a>
            <p>Fidelidad digital para negocios locales.</p>
            <span>&copy; {{ date('Y') }} Fidentta</span>
        </div>
    </footer>
</div>
