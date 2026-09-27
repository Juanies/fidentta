<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div class="min-h-screen  overflow-hidden bg-background text-ink  ">
    <header class="relative flex bg-glass  justify-between items-center mx-auto rounded-xl mt-8 p-4 max-w-7xl z-20 border  border-border">
        <h1 class="font-bold text-xl">fidentta<span class="text-brand">.</span> </h1>
        <nav>
            <ul class=" flex items-center gap-4">
                <li><a href="">Como funciona</a></li>
                <li><a href="">Tarjeta</a></li>
                <li><a href="">Precios</a></li>

            </ul>
        </nav>
        <a href="{{ route('registera') }}" class="bg-brand hover:bg-brand-700 px-4 py-2 rounded-full text-white">Empezar</a>
    </header>

    <main>
        <section class="relative isolate overflow-hidden   text-ink">
            <div class="absolute -right-32 -top-32 -z-10 h-96 w-96 rounded-full bg-[#6f54d9]/30 blur-3xl"></div>
            <div class="absolute -bottom-40 left-1/3 -z-10 h-96 w-96 rounded-full bg-[#28a9e8]/20 blur-3xl"></div>
            <div
                class="mx-auto grid max-w-7xl gap-16 px-5 pb-20 pt-16 sm:px-8 sm:pt-24 lg:grid-cols-[0.9fr_1.1fr] lg:items-center lg:px-10 lg:pb-28">
                <div>
                    <div
                        class="mb-7 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-3 py-1.5 text-xs font-semibold uppercase tracking-[0.2em] text-[#76e3c1]">
                        <span class="h-2 w-2 rounded-full bg-[#76e3c1]"></span> Fidelidad que si vuelve
                    </div>
                    <h1 class="max-w-[12ch] text-5xl font-black leading-[0.98] tracking-tight sm:text-7xl">Haz que cada
                        visita cuente.</h1>
                    <p class="mt-7 max-w-[52ch] text-lg leading-8 text-muted-foreground sm:text-xl">Convierte clientes
                        ocasionales en habituales con tarjetas digitales, recompensas y campanas que trabajan para tu
                        negocio.</p>
                    <div class="mt-9 flex flex-col gap-3 sm:flex-row"><a href="{{ route('registera') }}"
                            class="inline-flex items-center text-white justify-center gap-2 rounded-full bg-brand px-6 py-3.5 font-bold transition hover:bg-brand-700">Crear mi tarjeta</a><a href="#como-funciona"
                            class="inline-flex items-center justify-center rounded-full   px-6 py-3.5 font-semibold border border-border transition bg-glass ">Ver como funciona</a></div>
                    <div class="mt-10 flex flex-wrap gap-x-7 gap-y-3 text-sm "><span
                            class="flex items-center gap-2"><span class="text-[#76e3c1]">&#10003;</span> Sin
                            permanencia</span><span class="flex items-center gap-2"><span
                                class="text-[#76e3c1]">&#10003;</span> Configuracion sencilla</span><span
                            class="flex items-center gap-2"><span class="text-[#76e3c1]">&#10003;</span> Soporte
                            humano</span></div>
                </div>
                <div class="relative mx-auto w-full max-w-xl lg:ml-auto">
                    <div class="absolute -inset-5 rounded-4xl border border-[#76e3c1]/20"></div>
                    <div class="relative rounded-4xl bg-white p-4 text-[#14203d] shadow-2xl sm:p-5">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div class="flex items-center gap-2 text-sm font-semibold"><span
                                    class="h-2.5 w-2.5 rounded-full bg-[#76e3c1]"></span> Panel de fidelidad</div><span
                                class="rounded-full bg-[#eafbf5] px-3 py-1 text-xs font-semibold text-[#228f70]">Este
                                mes &uarr; 24%</span>
                        </div>
                        <div class="grid gap-3 py-5 sm:grid-cols-3">
                            <div class="rounded-2xl bg-[#f3f6fb] p-4">
                                <p class="text-xs text-slate-500">Clientes activos</p>
                                <p class="mt-2 text-2xl font-bold">2.480</p>
                                <p class="mt-1 text-xs text-[#228f70]">+18,4%</p>
                            </div>
                            <div class="rounded-2xl bg-[#f3f6fb] p-4">
                                <p class="text-xs text-slate-500">Visitas repetidas</p>
                                <p class="mt-2 text-2xl font-bold">68%</p>
                                <p class="mt-1 text-xs text-[#228f70]">+12,1%</p>
                            </div>
                            <div class="rounded-2xl bg-[#f3f6fb] p-4">
                                <p class="text-xs text-slate-500">Recompensas</p>
                                <p class="mt-2 text-2xl font-bold">1.126</p>
                                <p class="mt-1 text-xs text-[#228f70]">+31,8%</p>
                            </div>
                        </div>
                        <div class="rounded-2xl bg-[#14203d] p-5 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-white/60">Actividad de clientes</p>
                                    <p class="mt-1 text-2xl font-bold">+32,8%</p>
                                </div><span class="rounded-full bg-white/10 px-3 py-1 text-xs text-[#76e3c1]">Ultimos 30
                                    dias</span>
                            </div>
                            <div class="mt-8 flex h-28 items-end gap-2 sm:gap-3"><span
                                    class="h-[34%] flex-1 rounded-t-lg bg-[#76e3c1]/40"></span><span
                                    class="h-[48%] flex-1 rounded-t-lg bg-[#76e3c1]/50"></span><span
                                    class="h-[42%] flex-1 rounded-t-lg bg-[#76e3c1]/60"></span><span
                                    class="h-[67%] flex-1 rounded-t-lg bg-[#76e3c1]/70"></span><span
                                    class="h-[58%] flex-1 rounded-t-lg bg-[#76e3c1]/80"></span><span
                                    class="h-[82%] flex-1 rounded-t-lg bg-[#76e3c1]"></span><span
                                    class="h-full flex-1 rounded-t-lg bg-[#28a9e8]"></span></div>
                        </div>
                    </div>
                    <div class="absolute -bottom-7 -left-4 rounded-2xl bg-[#76e3c1] p-4 shadow-xl sm:-left-8">
                        <p class="text-xs font-semibold text-[#14203d]/70">Nueva recompensa</p>
                        <p class="mt-1 text-sm font-bold text-[#14203d]">Cafe gratis desbloqueado</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="border-b border-slate-200 bg-white">
            <div class="mx-auto grid max-w-7xl gap-6 px-5 py-8 text-center sm:grid-cols-3 sm:px-8 lg:px-10">
                <div>
                    <p class="text-3xl font-black text-[#14203d]">+40%</p>
                    <p class="mt-1 text-sm text-slate-500">mas visitas recurrentes</p>
                </div>
                <div class="border-slate-200 sm:border-x">
                    <p class="text-3xl font-black text-[#14203d]">0 plastico</p>
                    <p class="mt-1 text-sm text-slate-500">todo desde el movil</p>
                </div>
                <div>
                    <p class="text-3xl font-black text-[#14203d]">1 panel</p>
                    <p class="mt-1 text-sm text-slate-500">para todos tus locales</p>
                </div>
            </div>
        </section>

        <section id="como-funciona" class="mx-auto max-w-7xl px-5 py-24 sm:px-8 lg:px-10">
            <div class="max-w-2xl">
                <p class="text-sm font-bold uppercase tracking-[0.2em] text-[#28a9e8]">Asi de facil</p>
                <h2 class="mt-4 text-4xl font-black tracking-tight text-ink sm:text-5xl">De la primera visita a la
                    proxima
                    reserva.</h2>
                <p class="mt-5 text-lg leading-8 text-slate-600">Pon en marcha tu programa de fidelizacion en minutos y
                    deja que tus clientes hagan el resto.</p>
            </div>
            <div class="mt-14 grid gap-6 md:grid-cols-3">
                <div class="rounded-3xl bg-white p-7 shadow-[0_15px_50px_rgba(20,40,90,0.08)]"><span
                        class="text-5xl font-black text-muted-foreground">01</span>
                    <h3 class="mt-8 text-xl font-bold text-muted-foreground">Crea tu tarjeta</h3>
                    <p class="mt-3 leading-7 text-slate-600">Crea una tarjeta con tus colores, logo y las recompensas
                        que tus clientes quieren conseguir.</p>
                </div>
                <div class="rounded-3xl bg-[#14203d] p-7  shadow-[0_15px_50px_rgba(20,40,90,0.18)]"><span
                        class="text-5xl font-black text-white">02</span>
                    <h3 class="mt-8 text-xl font-bold text-white">Sella al cobrar</h3>
                    <p class="mt-3 leading-7 text-white/65">Tus clientes se unen en un instante, sin descargar
                        aplicaciones ni rellenar formularios interminables.</p>
                </div>
                <div class="rounded-3xl bg-white text-muted-foreground p-7 shadow-[0_15px_50px_rgba(20,40,90,0.08)]">
                    <span class="text-5xl font-black ">03</span>
                    <h3 class="mt-8 text-xl font-bold">Recupera al cliente</h3>
                    <p class="mt-3 leading-7 ">Automatiza recompensas y campanas para estar presente en
                        el momento exacto.</p>
                </div>
            </div>
        </section>

        <section id="funcionalidades" class="bg-[#eaf0fa]">
            <div class="mx-auto max-w-7xl px-5 py-24 sm:px-8 lg:px-10">
                <div class="flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.2em] text-[#28a9e8]">Una plataforma completa
                        </p>
                        <h2 class="mt-4 text-4xl font-black tracking-tight sm:text-5xl">Todo lo que tu negocio
                            necesita.</h2>
                    </div>
                    <p class="max-w-md text-lg leading-8 text-slate-600">Menos herramientas desconectadas. Mas clientes
                        que vuelven y mas tiempo para hacer crecer tu marca.</p>
                </div>
                <div class="mt-14 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-3xl bg-white p-6">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#eafbf5] text-xl text-[#228f70]">
                            &#9733;</div>
                        <h3 class="mt-6 font-bold">Recompensas</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Premia la frecuencia y convierte cada compra
                            en motivacion.</p>
                    </div>
                    <div class="rounded-3xl bg-white p-6">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#e8f5fd] text-xl text-[#1689bd]">
                            &#9638;</div>
                        <h3 class="mt-6 font-bold">QR y NFC</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Una entrada rapida y sin friccion para todos
                            tus clientes.</p>
                    </div>
                    <div class="rounded-3xl bg-white p-6">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#f0ebff] text-xl text-[#7656ce]">
                            &#8599;</div>
                        <h3 class="mt-6 font-bold">Campanas</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Envia comunicaciones relevantes segun habitos
                            reales.</p>
                    </div>
                    <div class="rounded-3xl bg-white p-6">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#fff3dc] text-xl text-[#bd7b15]">
                            &#9679;</div>
                        <h3 class="mt-6 font-bold">Analitica</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Entiende que funciona y toma decisiones con
                            datos.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="precio" class="mx-auto max-w-7xl px-5 py-24 sm:px-8 lg:px-10">
            <div class="rounded-4xl bg-[#14203d] p-7 text-white sm:p-10 lg:p-14">
                <div class="grid gap-12 lg:grid-cols-[0.8fr_1.2fr] lg:items-center">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-[0.2em] text-[#76e3c1]">Un plan. Sin sorpresas.
                        </p>
                        <h2 class="mt-4 text-4xl font-black tracking-tight sm:text-5xl">Crece sin pagar por crecer.
                        </h2>
                        <p class="mt-5 max-w-md text-lg leading-8 text-white/65">Todo Fidentta incluido en una unica
                            suscripcion, desde el primer dia hasta el ultimo local.</p>
                        <div class="mt-8 flex items-end gap-2"><span class="text-6xl font-black">39€</span><span
                                class="pb-2 text-white/60">/ mes</span></div><a href="{{ route('registera') }}"
                            class="mt-8 inline-flex rounded-full bg-[#76e3c1] px-6 py-3.5 font-bold text-[#14203d] transition hover:bg-white">Empezar
                            ahora &rarr;</a>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                            <p class="text-[#76e3c1]">&#10003;</p>
                            <p class="mt-4 font-semibold">Usuarios ilimitados</p>
                            <p class="mt-2 text-sm leading-6 text-white/55">Invita a todo tu equipo sin costes extra.
                            </p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                            <p class="text-[#76e3c1]">&#10003;</p>
                            <p class="mt-4 font-semibold">Locales ilimitados</p>
                            <p class="mt-2 text-sm leading-6 text-white/55">Centraliza tu operacion sin limites de
                                expansion.</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                            <p class="text-[#76e3c1]">&#10003;</p>
                            <p class="mt-4 font-semibold">Soporte incluido</p>
                            <p class="mt-2 text-sm leading-6 text-white/55">Te ayudamos a convertir la estrategia en
                                resultados.</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                            <p class="text-[#76e3c1]">&#10003;</p>
                            <p class="mt-4 font-semibold">Sin permanencia</p>
                            <p class="mt-2 text-sm leading-6 text-white/55">Tu decides como y cuando crecer.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="mx-auto max-w-4xl px-5 pb-24 sm:px-8">
            <div class="text-center">
                <p class="text-sm font-bold uppercase tracking-[0.2em] text-[#28a9e8]">Preguntas frecuentes</p>
                <h2 class="mt-4 text-4xl font-black tracking-tight">Lo importante, claro.</h2>
            </div>
            <div
                class="mt-10 divide-y divide-slate-200 rounded-3xl bg-white px-6 shadow-[0_15px_50px_rgba(20,40,90,0.06)]">
                <details class="group py-5">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-5 font-semibold">
                        Necesito instalar una aplicacion?<span
                            class="text-2xl text-[#28a9e8] transition group-open:rotate-45">+</span></summary>
                    <p class="mt-3 max-w-2xl leading-7 text-slate-600">No. Tus clientes acceden desde el navegador y
                        pueden guardar su tarjeta en su movil para tenerla siempre a mano.</p>
                </details>
                <details class="group py-5">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-5 font-semibold">
                        Puedo gestionar varios locales?<span
                            class="text-2xl text-[#28a9e8] transition group-open:rotate-45">+</span></summary>
                    <p class="mt-3 max-w-2xl leading-7 text-slate-600">Si. El plan incluye locales ilimitados y un
                        panel centralizado para revisar el rendimiento de cada uno.</p>
                </details>
                <details class="group py-5">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-5 font-semibold">
                        Cuanto tardo en empezar?<span
                            class="text-2xl text-[#28a9e8] transition group-open:rotate-45">+</span></summary>
                    <p class="mt-3 max-w-2xl leading-7 text-slate-600">Puedes crear tu cuenta y configurar tu programa
                        en minutos. Despues solo tienes que compartir tu QR o activar tu punto NFC.</p>
                </details>
            </div>
        </section>

        <section class="mx-5 mb-10 overflow-hidden rounded-4xl bg-[#76e3c1] sm:mx-8 lg:mx-auto lg:max-w-7xl">
            <div class="relative px-7 py-14 text-center sm:px-12 sm:py-16">
                <div class="absolute -right-16 -top-24 h-64 w-64 rounded-full border-30 border-white/20"></div>
                <h2 class="relative text-4xl font-black tracking-tight text-muted-foreground sm:text-5xl">Tu proximo
                    cliente
                    fiel<br class="hidden sm:block"> puede empezar hoy.</h2>
                <p class="relative mx-auto mt-5 max-w-xl text-lg text-muted-foreground">Crea una experiencia que tus
                    clientes quieran repetir.</p><a href="{{ route('registera') }}"
                    class="relative mt-8 inline-flex rounded-full bg-[#14203d] px-7 py-3.5 font-bold text-white transition hover:bg-[#263b68]">Crear
                    mi cuenta gratis &rarr;</a>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div
            class="mx-auto flex max-w-7xl flex-col gap-4 px-5 py-8 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-8 lg:px-10">
            <a href="{{ route('home') }}" class="font-bold ">Fidentta</a>
            <p>Fidelidad digital para negocios que quieren crecer.</p><span>&copy; {{ date('Y') }} Fidentta</span>
        </div>
    </footer>
</div>
