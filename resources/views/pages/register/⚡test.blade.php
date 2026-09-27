<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div>
    <div class="max-w-2xl">
        <p class="mb-3 text-sm font-semibold text-fidentta-teal">Define la experiencia</p>
        <h2 class="text-3xl font-semibold tracking-tight text-text sm:text-5xl">Qué te gustaría saber de tus clientes
        </h2>
        <p class="mt-4 text-base leading-7 text-text-secondary/70 sm:text-lg">Esto decide qué verá tu cliente antes de
            añadir su tarjeta.</p>
    </div>

    <div class="mt-10   flex flex-col gap-4">
        <div
            class="flex col-span-2 h-fit flex-col gap-3 rounded-2xl border border-text-secondary/10 bg-text-secondary/5 p-6 transition hover:border-fidentta-cyan/50">
            <div class="flex items-center justify-between">
                <span class="text-xl font-semibold text-text">Nada</span>
                <span
                    class="flex h-9 w-9 items-center justify-center rounded-full bg-fidentta-blue/20 text-fidentta-cyan">0</span>
            </div>
            <span class="text-sm leading-6 text-text-secondary/70">Alta anónima: solo el botón de añadir a wallet, sin
                ningún campo.</span>
        </div>
        <div class="flex gap-4 w-full">
            <div
                class="relative w-full flex flex-col justify-end gap-4 rounded-2xl border border-fidentta-cyan/70 bg-fidentta-purple/20 p-6 shadow-xl shadow-fidentta-purple/10 transition hover:-translate-y-1">
                <span
                    class="absolute -top-3 left-5 rounded-full bg-fidentta-cyan px-3 py-1 text-xs font-bold uppercase tracking-wide text-fidentta-navy">
                    Recomendado
                </span>

                <div class="flex flex-col gap-3 rounded-xl bg-fidentta-cyan/20 p-4">
                    <div class="rounded-lg bg-text-secondary/90 px-4 py-2.5 text-sm text-fidentta-navy">Nombre</div>
                    <div class="rounded-lg bg-text-secondary/90 px-4 py-2.5 text-sm text-fidentta-navy">Email</div>
                    <div class="rounded-lg bg-text-secondary/90 px-4 py-2.5 text-sm text-fidentta-navy">Cumpleaños</div>

                </div>
                <span class="text-xl font-semibold text-text">Lo esencial</span>
                <span class="text-sm leading-6 text-text-secondary/70">Rápido de rellenar, casi nadie abandona.</span>
            </div>
            <div
                class="flex w-full  flex-col gap-4 rounded-2xl border border-text-secondary/10 bg-fidentta-blue/10 p-6 transition hover:border-fidentta-teal/50">
                <div class="flex flex-col gap-3 rounded-xl bg-fidentta-teal/20 p-4">
                    <div class="rounded-lg bg-text-secondary/90 px-4 py-2.5 text-sm text-fidentta-navy">Nombre</div>
                    <div class="rounded-lg bg-text-secondary/90 px-4 py-2.5 text-sm text-fidentta-navy">Email</div>
                    <div class="rounded-lg bg-text-secondary/90 px-4 py-2.5 text-sm text-fidentta-navy">Cumpleaños</div>
                    <div class="rounded-lg bg-text-secondary/90 px-4 py-2.5 text-sm text-fidentta-navy">Teléfono</div>

                </div>
                <span class="text-xl font-semibold text-text">Quiero conocerlos</span>
                <span class="text-sm leading-6 text-text-secondary/70">Tú decides qué preguntar.</span>
            </div>
        </div>
    </div>


</div>
