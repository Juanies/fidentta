<?php

use Livewire\Component;
use Livewire\Attributes\Modelable;

new class extends Component {
    public string $logoType = 'fidentta';
    public array $ideasRecompensa = ['Bebida gratis', 'Pastel gratis', 'Regalo gratis', 'Descuento', '50% en el segundo', '2x1', 'Cámbiame — decido después'];

    public function cambiarLogoType(string $type)
    {
        $this->logoType = $type;
    }

    #[Modelable]
    public array $registro = [
        'name' => 'Cafe laté',
                'error' => false,

        'logo' => [
            'type' => 'text',
            'content' => 'C',
            'url' => null,
        ],
        'sellos' => 8,
        'recompensa' => 'Bebida gratis',
    ];

    public function sumarSello(): void
    {
        $this->registro['sellos'] = min(12, ($this->registro['sellos'] ?? 8) + 1);
    }

    public function restarSello(): void
    {
        $this->registro['sellos'] = max(4, ($this->registro['sellos'] ?? 8) - 1);
    }
};

?>

<div>
    <div class="max-w-2xl">
        <p class="mb-3 text-sm font-semibold text-fidentta-teal">Define la experiencia</p>
        <h2 class="text-3xl font-semibold tracking-tight text-text sm:text-5xl">¿Cuántos sellos llenan la tarjeta?</h2>
        <p class="mt-4 text-base leading-7 text-text-secondary/70 sm:text-lg">
            La cantidad ideal debe ser fácil de completar, pero suficiente para crear hábito.
        </p>
    </div>

    <div class="mt-10 grid grid-cols-1 gap-8 lg:grid-cols-5">
        <div class="lg:col-span-3">
            <div class="rounded-3xl border border-white/10 bg-white/[0.03] p-5 shadow-[0_20px_50px_rgba(15,23,42,0.18)]">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.2em] text-fidentta-cyan">Nivel de
                            dificultad</p>
                        <p class="mt-1 text-sm text-text-secondary/70">Ajusta la progresión de recompensas</p>
                    </div>
                    <div
                        class="rounded-full border border-fidentta-cyan/30 bg-fidentta-cyan/10 px-3 py-1 text-xs font-semibold text-fidentta-cyan">
                        {{ $registro['sellos'] ?? 8 }} sellos
                    </div>
                </div>

                <div
                    class="mt-6 flex items-center justify-center gap-4 rounded-2xl border border-white/10 bg-fidentta-navy/40 p-4">
                    <button type="button" wire:click="restarSello" aria-label="Restar sello"
                        class="flex h-12 w-12 items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] text-2xl font-light text-text transition hover:border-fidentta-cyan hover:text-fidentta-cyan focus:outline-none focus:ring-2 focus:ring-fidentta-cyan/50">
                        −
                    </button>

                    <div class="min-w-[120px] text-center">
                        <div class="text-5xl font-black leading-none tracking-tight text-text">
                            {{ $registro['sellos'] ?? 8 }}
                        </div>
                        <div class="mt-2 text-xs uppercase tracking-[0.25em] text-text-secondary/60">sellos</div>
                    </div>

                    <button type="button" wire:click="sumarSello" aria-label="Sumar sello"
                        class="flex h-12 w-12 items-center justify-center rounded-xl border border-white/10 bg-white/[0.04] text-2xl font-light text-text transition hover:border-fidentta-cyan hover:text-fidentta-cyan focus:outline-none focus:ring-2 focus:ring-fidentta-cyan/50">
                        +
                    </button>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach ([4, 6, 8, 10, 12] as $preset)
                        <button type="button"
                            wire:click="{{ $preset === 4 ? 'restarSello' : ($preset === 12 ? 'sumarSello' : '') }}"
                            class="rounded-xl border px-3 py-2 text-sm font-semibold transition {{ ($registro['sellos'] ?? 8) === $preset ? 'border-fidentta-cyan bg-fidentta-cyan/15 text-fidentta-cyan' : 'border-white/10 bg-white/[0.02] text-text-secondary hover:border-white/20 hover:text-text' }}">
                            {{ $preset }} sellos
                        </button>
                    @endforeach
                </div>

                <div class="mt-6 rounded-2xl border border-fidentta-cyan/20 bg-fidentta-cyan/5 p-4">
                    <p class="text-sm font-semibold text-fidentta-cyan">Recomendación</p>
                    <p class="mt-1 text-sm text-text-secondary/80">
                        @if (($registro['sellos'] ?? 8) <= 6)
                            Ideal para promociones rápidas y campañas con mucha frecuencia de compra.
                        @elseif (($registro['sellos'] ?? 8) <= 8)
                            El equilibrio perfecto entre motivación y tiempo de recompensa.
                        @else
                            Mejor para programas de fidelidad más premium y con beneficios de mayor valor.
                        @endif
                    </p>
                </div>

                <div class="mt-6 rounded-2xl border border-white/10 bg-white/[0.03] p-4">
                    <label for="recompensa" class="block text-sm font-semibold text-text">Recompensa</label>
                    <p class="mt-1 text-xs text-text-secondary/60">Escribe una recompensa o elige una idea</p>

                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($ideasRecompensa as $idea)
                            <button type="button" wire:click="$set('registro.recompensa', '{{ $idea }}')"
                                class="rounded-full border px-3 py-1.5 text-xs font-medium transition {{ ($registro['recompensa'] ?? 'Bebida gratis') === $idea ? 'border-fidentta-cyan bg-fidentta-cyan/15 text-fidentta-cyan' : 'border-white/10 bg-white/[0.02] text-text-secondary hover:border-white/20 hover:text-text' }}">
                                {{ $idea }}
                            </button>
                        @endforeach
                    </div>

                    <input id="recompensa" wire:model.live="registro.recompensa" type="text"
                        placeholder="Ej. Un café gratis"
                        class="mt-4 w-full rounded-xl border border-white/15 bg-white/[0.04] px-4 py-3 text-text outline-none transition placeholder:text-text-secondary/50 focus:border-fidentta-cyan focus:bg-white/[0.1] focus:ring-2 focus:ring-fidentta-cyan/20">
                </div>
            </div>
        </div>

        <div class="col-span-2">
            @php
                $previewBackground =
                    $registro['paleta']['fondo'] ?? 'linear-gradient(135deg, #7C2D12 0%, #B45309 38%, #F59E0B 100%)';
                $previewTextColor = $registro['paleta']['texto'] ?? '#F8FAFC';
                $logoInfo = $registro['logo'] ?? ['type' => 'text', 'content' => 'C', 'url' => null];
                $logoUrl = $logoInfo['url'] ?? null;
                $logoInitial = strtoupper(substr($logoInfo['content'] ?? ($registro['name'] ?? 'C'), 0, 1));
                $sellosActuales = $registro['sellos'] ?? 8;
            @endphp

            <div class="rounded-2xl border border-white/10 p-4 h-fit shadow-2xl shadow-fidentta-purple/10"
                style="background: {{ $previewBackground }}; color: {{ $previewTextColor }};">
                <div class="flex items-center gap-4">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo del negocio"
                            class="h-10 w-10 rounded-full border border-white/20 bg-white/10 object-cover" />
                    @else
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-full border border-white/20 bg-white/10 text-lg font-bold">
                            {{ $logoInitial }}
                        </div>
                    @endif
                    <span class="text-base font-semibold">{{ $registro['name'] ?? 'Cafe laté' }}</span>
                </div>

                <div class="mt-5 grid grid-cols-4 gap-3">
                    @for ($i = 0; $i < $sellosActuales; $i++)
                        <div class="flex items-center justify-center">
                            <div
                                class="h-14 w-14 rounded-full border border-white/30 bg-white/10 shadow-inner shadow-white/20">
                            </div>
                        </div>
                    @endfor
                </div>

                <p class="mt-5 text-sm opacity-85">Completa los {{ $sellosActuales }} sellos y consigue:
                    {{ $registro['recompensa'] ?? 'tu recompensa' }}</p>

                <div class="bg-white p-4 rounded-lg mb-8 w-fit mx-auto mt-12">
                    <svg width="120" height="120" viewBox="0 0 21 21" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M11.75 8.75C12.693 8.75 13.164 8.75 13.457 8.457C13.75 8.164 13.75 7.693 13.75 6.75C13.75 5.807 13.75 5.336 14.043 5.043C14.336 4.75 14.807 4.75 15.75 4.75M4.75 11.75H6.75C7.693 11.75 8.164 11.75 8.457 12.043C8.75 12.336 8.75 12.807 8.75 13.75V15.75M5.125 15.5H5M4.75 19.75C4.286 19.75 4.053 19.75 3.858 19.728C3.07013 19.6392 2.33575 19.2855 1.77511 18.7249C1.21447 18.1643 0.860796 17.4299 0.772 16.642C0.75 16.447 0.75 16.214 0.75 15.75M15.75 19.75C16.214 19.75 16.447 19.75 16.642 19.728C17.4299 19.6392 18.1643 19.2855 18.7249 18.7249C19.2855 18.1643 19.6392 17.4299 19.728 16.642C19.75 16.447 19.75 16.214 19.75 15.75M4.75 0.75C4.286 0.75 4.053 0.75 3.858 0.772C3.07013 0.860796 2.33575 1.21447 1.77511 1.77511C1.21447 2.33575 0.860796 3.07013 0.772 3.858C0.75 4.053 0.75 4.286 0.75 4.75M15.75 0.75C16.214 0.75 16.447 0.75 16.642 0.772C17.4299 0.860796 18.1643 1.21447 18.7249 1.77511C19.2855 2.33575 19.6392 3.07013 19.728 3.858C19.75 4.053 19.75 4.286 19.75 4.75M5.043 5.043C4.75 5.336 4.75 5.807 4.75 6.75C4.75 7.693 4.75 8.164 5.043 8.457C5.336 8.75 5.807 8.75 6.75 8.75C7.693 8.75 8.164 8.75 8.457 8.457C8.75 8.164 8.75 7.693 8.75 6.75C8.75 5.807 8.75 5.336 8.457 5.043C8.164 4.75 7.693 4.75 6.75 4.75C5.807 4.75 5.336 4.75 5.043 5.043ZM5.25 15.5C5.25 15.5663 5.22366 15.6299 5.17678 15.6768C5.12989 15.7237 5.0663 15.75 5 15.75C4.9337 15.75 4.87011 15.7237 4.82322 15.6768C4.77634 15.6299 4.75 15.5663 4.75 15.5C4.75 15.4337 4.77634 15.3701 4.82322 15.3232C4.87011 15.2763 4.9337 15.25 5 15.25C5.0663 15.25 5.12989 15.2763 5.17678 15.3232C5.22366 15.3701 5.25 15.4337 5.25 15.5ZM12.043 12.043C11.75 12.336 11.75 12.807 11.75 13.75C11.75 14.693 11.75 15.164 12.043 15.457C12.336 15.75 12.807 15.75 13.75 15.75C14.693 15.75 15.164 15.75 15.457 15.457C15.75 15.164 15.75 14.693 15.75 13.75C15.75 12.807 15.75 12.336 15.457 12.043C15.164 11.75 14.693 11.75 13.75 11.75C12.807 11.75 12.336 11.75 12.043 12.043Z"
                            stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>
