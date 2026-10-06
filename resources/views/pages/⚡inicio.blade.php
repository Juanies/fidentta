<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div class="min-h-screen  overflow-hidden bg-background text-ink  ">
    <header
        class="relative z-20 mx-auto mt-5 flex max-w-7xl items-center justify-between gap-4 rounded-lg border border-border bg-card/90 p-4 shadow-soft backdrop-blur">

        <div class="flex items-center gap-4">
            <img class="w-7 h-7" src="{{ asset('logo.png') }}" alt="Fidentta">
            <h1 class="font-bold text-xl">fidentta<span class="text-brand">.</span> </h1>
        </div>
        <nav>
            <ul class=" flex items-center gap-4">
                <li><a href="#como-funciona" class="text-ink2 transition hover:text-ink">Cómo funciona</a></li>
                <li><a href="#funcionalidades" class="text-ink2 transition hover:text-ink">Herramientas</a></li>
                <li><a href="#preguntas" class="text-ink2 transition hover:text-ink">Preguntas</a></li>

            </ul>
        </nav>
        <a href="{{ route('registera') }}"
            class="bg-brand hover:bg-brand-700 px-4 py-2 rounded-full text-white">Empezar</a>
    </header>

    <main>
        <section class="relative isolate overflow-hidden text-ink">
            <div
                class="mx-auto grid max-w-7xl gap-16 px-5 pb-20 pt-16 sm:px-8 sm:pt-24 lg:grid-cols-[0.9fr_1.1fr] lg:items-center lg:px-10 lg:pb-28">
                <div>
                    <div
                        class="mb-7 inline-flex items-center gap-2 rounded-full border border-border bg-card px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-mintd">
                        <span class="h-2 w-2 rounded-full bg-mintd"></span> Fidelidad para negocios locales
                    </div>
                    <h1 class="max-w-[12ch] text-5xl font-black leading-[0.98] tracking-tight sm:text-7xl">Haz que cada
                        visita cuente.</h1>
                    <p class="mt-7 max-w-[52ch] text-lg leading-8 text-ink2 sm:text-xl">Crea una tarjeta de sellos
                        digital, comparte un QR por local y registra visitas desde un panel sencillo.</p>
                    <div class="mt-9 flex flex-col gap-3 sm:flex-row"><a href="{{ route('registera') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-6 py-3.5 font-semibold text-primary-foreground transition hover:bg-primary/90">Crear
                            mi programa</a><a href="#como-funciona"
                            class="inline-flex items-center justify-center rounded-lg border border-border bg-card px-6 py-3.5 font-semibold transition hover:bg-muted">Ver
                            cómo funciona</a></div>
                    <div class="mt-10 flex flex-wrap gap-x-7 gap-y-3 text-sm "><span
                            class="flex items-center gap-2"><span class="text-mintd">&#10003;</span> QR por
                            local</span><span class="flex items-center gap-2"><span class="text-mintd">&#10003;</span>
                            Registro configurable</span><span class="flex items-center gap-2"><span
                                class="text-mintd">&#10003;</span> Sellos y recompensas</span></div>
                </div>
                <div class="relative mx-auto w-full max-w-xl lg:ml-auto">
                    <div class="relative rounded-xl border border-border bg-card p-4 text-ink shadow-card sm:p-5">
                        <div class="flex items-center justify-between border-b border-border pb-4">
                            <div class="flex items-center gap-2 text-sm font-semibold"><span
                                    class="h-2.5 w-2.5 rounded-full bg-mintd"></span> Panel del negocio</div><span
                                class="rounded-md bg-secondary/30 px-3 py-1 text-xs font-semibold text-ink">Vista de
                                ejemplo</span>
                        </div>
                        <div class="grid gap-3 py-5 sm:grid-cols-3">
                            <div class="rounded-lg bg-muted p-4">
                                <p class="text-xs text-ink2">Clientes</p>
                                <p class="mt-2 text-2xl font-semibold">—</p>
                                <p class="mt-1 text-xs text-ink2">dato del equipo</p>
                            </div>
                            <div class="rounded-lg bg-muted p-4">
                                <p class="text-xs text-ink2">Sellos del mes</p>
                                <p class="mt-2 text-2xl font-semibold">—</p>
                                <p class="mt-1 text-xs text-ink2">dato del equipo</p>
                            </div>
                            <div class="rounded-lg bg-muted p-4">
                                <p class="text-xs text-ink2">Locales</p>
                                <p class="mt-2 text-2xl font-semibold">—</p>
                                <p class="mt-1 text-xs text-ink2">dato del equipo</p>
                            </div>
                        </div>
                        <div class="rounded-lg bg-ink p-5 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-white/60">Actividad de clientes</p>
                                    <p class="mt-1 text-lg font-semibold">Sellos por día</p>
                                </div><span class="rounded-md bg-white/10 px-3 py-1 text-xs text-white/80">Ejemplo
                                    visual</span>
                            </div>
                            <div class="mt-8 flex h-28 items-end gap-2 sm:gap-3"><span
                                    class="h-[34%] flex-1 rounded-t bg-mint/40"></span><span
                                    class="h-[48%] flex-1 rounded-t bg-mint/50"></span><span
                                    class="h-[42%] flex-1 rounded-t bg-mint/60"></span><span
                                    class="h-[67%] flex-1 rounded-t bg-mint/70"></span><span
                                    class="h-[58%] flex-1 rounded-t bg-mint/80"></span><span
                                    class="h-[82%] flex-1 rounded-t bg-mint"></span><span
                                    class="h-full flex-1 rounded-t bg-accent"></span></div>
                        </div>
                    </div>
                    <div class="absolute -bottom-7 -left-4 rounded-lg bg-secondary p-4 shadow-card sm:-left-8">
                        <p class="text-xs font-semibold text-ink2">Ejemplo de recompensa</p>
                        <p class="mt-1 text-sm font-semibold text-ink">Bebida gratis</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="border-y border-border bg-card">
            <div class="mx-auto grid max-w-7xl gap-6 px-5 py-8 text-center sm:grid-cols-3 sm:px-8 lg:px-10">
                <div>
                    <p class="text-3xl font-display text-ink">QR</p>
                    <p class="mt-1 text-sm text-ink2">un codigo por local</p>
                </div>
                <div class="border-border sm:border-x">
                    <p class="text-3xl font-display text-ink">Sellos</p>
                    <p class="mt-1 text-sm text-ink2">progreso de cada cliente</p>
                </div>
                <div>
                    <p class="text-3xl font-display text-ink">Wallet</p>
                    <p class="mt-1 text-sm text-ink2">si el proveedor esta configurado</p>
                </div>
            </div>
        </section>

        <section id="como-funciona" class="mx-auto max-w-7xl px-5 py-24 sm:px-8 lg:px-10">
            <div class="max-w-2xl">
                <p class="text-sm font-bold uppercase tracking-[0.18em] text-mintd">Como funciona</p>
                <h2 class="mt-4 text-4xl tracking-tight text-ink sm:text-5xl">De la primera visita a la siguiente
                    recompensa.</h2>
                <p class="mt-5 text-lg leading-8 text-ink2">Configura el programa, comparte un QR por local y registra
                    los sellos de cada visita.</p>
            </div>
            <div class="mt-14 grid gap-6 md:grid-cols-3">
                <div class="rounded-lg border border-border bg-card p-7 shadow-soft"><span
                        class="text-4xl font-display text-mintd">01</span>
                    <h3 class="mt-6 text-xl font-semibold text-ink">Define la tarjeta</h3>
                    <p class="mt-3 leading-7 text-ink2">Elige una paleta, el número de sellos y la recompensa del
                        programa.</p>
                </div>
                <div class="rounded-lg bg-ink p-7 text-white shadow-card"><span
                        class="text-4xl font-display text-accent">02</span>
                    <h3 class="mt-6 text-xl font-semibold text-white">Comparte el QR</h3>
                    <p class="mt-3 leading-7 text-white/75">Cada local tiene un codigo que abre el registro del cliente.
                    </p>
                </div>
                <div class="rounded-lg border border-border bg-card p-7 shadow-soft">
                    <span class="text-4xl font-display text-mintd">03</span>
                    <h3 class="mt-6 text-xl font-semibold text-ink">Registra la visita</h3>
                    <p class="mt-3 leading-7 text-ink2">El equipo añade sellos y consulta el historial por local.</p>
                </div>
            </div>
        </section>

        <section id="funcionalidades" class="bg-paper">
            <div class="mx-auto max-w-7xl px-5 py-24 sm:px-8 lg:px-10">
                <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.18em] text-mintd">Herramientas del programa
                        </p>
                        <h2 class="mt-4 text-4xl font-black tracking-tight sm:text-5xl">Todo lo que tu negocio
                            necesita.</h2>
                    </div>
                    <p class="max-w-md text-lg leading-8 text-ink2">Gestiona locales, clientes, tarjetas y visitas
                        desde el mismo panel.</p>
                </div>
                <div class="mt-14 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg border border-border bg-card p-6 shadow-soft">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-lg bg-mint/20 text-xl text-mintd">
                            &#9733;</div>
                        <h3 class="mt-6 font-bold">Recompensas</h3>
                        <p class="mt-2 text-sm leading-6 text-ink2">Define la meta y la recompensa de tu tarjeta de
                            sellos.</p>
                    </div>
                    <div class="rounded-lg border border-border bg-card p-6 shadow-soft">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-lg bg-mint/20 text-xl text-mintd">
                            &#9638;</div>
                        <h3 class="mt-6 font-bold">QR por local</h3>
                        <p class="mt-2 text-sm leading-6 text-ink2">Abre el registro desde un codigo asociado al local.
                        </p>
                    </div>
                    <div class="rounded-lg border border-border bg-card p-6 shadow-soft">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-lg bg-accent/25 text-xl text-accent-foreground">
                            &#8599;</div>
                        <h3 class="mt-6 font-bold">Registro flexible</h3>
                        <p class="mt-2 text-sm leading-6 text-ink2">Elige invitado, cuenta básica o campos
                            personalizados.</p>
                    </div>
                    <div class="rounded-lg border border-border bg-card p-6 shadow-soft">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-lg bg-accent/25 text-xl text-accent-foreground">
                            &#9679;</div>
                        <h3 class="mt-6 font-bold">Apple y Google Wallet</h3>
                        <p class="mt-2 text-sm leading-6 text-ink2">Los pases se habilitan cuando sus credenciales
                            estan configuradas.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="precio" class="mx-auto max-w-7xl px-5 py-20 sm:px-8 lg:px-10 lg:py-24">
            <div
                class="flex flex-col gap-8 rounded-lg bg-secondary p-7 text-ink sm:p-10 lg:flex-row lg:items-center lg:justify-between lg:p-14">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.18em] text-ink2">Empieza paso a paso</p>
                    <h2 class="mt-4 text-3xl tracking-tight sm:text-4xl">Configura tu primera tarjeta de sellos.</h2>
                    <p class="mt-4 max-w-xl leading-7 text-ink2">Define la recompensa, el registro de clientes y tu
                        primer local desde el asistente.</p>
                </div>
                <a href="{{ route('registera') }}"
                    class="inline-flex shrink-0 items-center justify-center rounded-lg bg-primary px-6 py-3.5 font-semibold text-primary-foreground transition hover:bg-primary/90">Empezar
                    ahora <span class="ml-2" aria-hidden="true">&rarr;</span></a>
            </div>
        </section>

        <section id="preguntas" class="mx-auto max-w-4xl px-5 pb-24 sm:px-8">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-mintd">Preguntas frecuentes</p>
                <h2 class="mt-4 text-3xl tracking-tight sm:text-4xl">Lo importante, claro.</h2>
            </div>
            <div class="mt-8 divide-y divide-border border-y border-border">
                <details class="group py-5">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-5 font-semibold">
                        ¿Necesito una aplicación?<span
                            class="text-2xl text-mintd transition group-open:rotate-45">+</span>
                    </summary>
                    <p class="mt-3 max-w-2xl leading-7 text-ink2">El registro del cliente se abre en el navegador al
                        escanear el QR. Añadir el pase a Wallet es opcional y requiere configurar el proveedor.</p>
                </details>
                <details class="group py-5">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-5 font-semibold">
                        ¿Puedo gestionar varios locales?<span
                            class="text-2xl text-mintd transition group-open:rotate-45">+</span>
                    </summary>
                    <p class="mt-3 max-w-2xl leading-7 text-ink2">Sí. Puedes crear locales en el equipo, asociar un QR
                        a cada uno y filtrar la actividad por local.</p>
                </details>
                <details class="group py-5">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-5 font-semibold">
                        ¿Qué puedo configurar?<span
                            class="text-2xl text-mintd transition group-open:rotate-45">+</span>
                    </summary>
                    <p class="mt-3 max-w-2xl leading-7 text-ink2">Número de sellos, recompensa, colores de la tarjeta y
                        modo de registro del cliente.</p>
                </details>
            </div>
        </section>

        <section class="mx-5 mb-10 overflow-hidden rounded-lg bg-secondary sm:mx-8 lg:mx-auto lg:max-w-7xl">
            <div class="px-7 py-14 sm:px-12 sm:py-16">
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-ink2">Fiddenta</p>
                <h2 class="mt-3 max-w-2xl text-3xl tracking-tight text-ink sm:text-4xl">Tu próximo cliente fiel puede
                    empezar hoy.</h2>
                <p class="mt-4 max-w-xl text-lg text-ink2">Crea un programa de sellos y compártelo con el QR de tu
                    local.</p>
                <a href="{{ route('registera') }}"
                    class="mt-7 inline-flex items-center rounded-lg bg-primary px-6 py-3.5 font-semibold text-primary-foreground transition hover:bg-primary/90">Crear
                    mi cuenta <span class="ml-2" aria-hidden="true">&rarr;</span></a>
            </div>
        </section>
    </main>

    <footer class="border-t border-border bg-card">
        <div
            class="mx-auto flex max-w-7xl flex-col gap-4 px-5 py-8 text-sm text-ink2 sm:flex-row sm:items-center sm:justify-between sm:px-8 lg:px-10">
            <a href="{{ route('home') }}" class="font-bold text-ink">Fiddenta<span class="text-mintd">.</span></a>
            <p>Fidelidad digital para negocios locales.</p><span>&copy; {{ date('Y') }} Fiddenta</span>
        </div>
    </footer>
</div>
