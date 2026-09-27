<?php

use Livewire\Component;
use Livewire\Attributes\Modelable;
use Livewire\Attributes\Reactive;

new class extends Component {
    public bool $isPersonalizado = false;
    public string $paletaSeleccionada = 'cacao-clasico';
    public string $colorPrincipal = '#7C2D12';
    public string $colorSecundario = '#F59E0B';
    public string $colorTexto = '#F8FAFC';

    #[Modelable]
    public array $registro = [
        'name' => 'Cafe laté',
                'errora' => false,

        'logo' => [
            'type' => 'text',
            'content' => 'C',
            'url' => null,
        ],
        'type' => 'preseleccionado',
        'sellos' => 8,
        'recompensa' => 'Bebida gratis',
        'paleta' => [
            'slug' => 'cacao-clasico',
            'tipo' => 'preseleccionado',
            'isPersonalizado' => false,
            'colorPrincipal' => '#7C2D12',
            'colorSecundario' => '#F59E0B',
            'colorTexto' => '#F8FAFC',
            'texto' => '#F8FAFC',
            'fondo' => 'linear-gradient(135deg, #7C2D12 0%, #B45309 38%, #F59E0B 100%)',
        ],
        'tarjeta' => null,
    ];

    public function mount()
    {
        $this->syncFromRegistro();
    }

    private function syncFromRegistro(): void
    {
        $paleta = $this->registro['paleta'] ?? [
            'slug' => 'cacao-clasico',
            'tipo' => 'preseleccionado',
            'isPersonalizado' => false,
            'colorPrincipal' => '#7C2D12',
            'colorSecundario' => '#F59E0B',
            'colorTexto' => '#F8FAFC',
            'texto' => '#F8FAFC',
            'fondo' => 'linear-gradient(135deg, #7C2D12 0%, #B45309 38%, #F59E0B 100%)',
        ];

        $this->paletaSeleccionada = $paleta['slug'] ?? 'cacao-clasico';
        $this->isPersonalizado = ($this->registro['type'] ?? 'preseleccionado') === 'personalizado' || ($paleta['isPersonalizado'] ?? false);
        $this->colorPrincipal = $paleta['colorPrincipal'] ?? ($paleta['colores'][0] ?? '#7C2D12');
        $this->colorSecundario = $paleta['colorSecundario'] ?? ($paleta['colores'][1] ?? '#F59E0B');
        $this->colorTexto = $paleta['colorTexto'] ?? ($paleta['texto'] ?? '#F8FAFC');

        if (!isset($this->registro['type'])) {
            $this->registro['type'] = $this->isPersonalizado ? 'personalizado' : 'preseleccionado';
        }

        $this->registro['paleta'] = $this->buildPaletaPayload();
        $this->registro['tarjeta'] = $this->registro['paleta'];
    }
    public array $paletas = [
        [
            'slug' => 'cacao-clasico',
            'nombre' => 'Cacao Clásico',
            'descripcion' => 'Templado y premium',
            'colores' => ['#F59E0B', '#D97706', '#7C2D12'],
            'texto' => '#F8FAFC',
            'fondo' => 'linear-gradient(135deg, #7C2D12 0%, #B45309 38%, #F59E0B 100%)',
        ],
        [
            'slug' => 'azul-royal',
            'nombre' => 'Azul Royal',
            'descripcion' => 'Elegante y digital',
            'colores' => ['#2563EB', '#1D4ED8', '#0F172A'],
            'texto' => '#E0F2FE',
            'fondo' => 'linear-gradient(135deg, #0F172A 0%, #1D4ED8 55%, #60A5FA 100%)',
        ],
        [
            'slug' => 'verde-fresno',
            'nombre' => 'Verde Fresco',
            'descripcion' => 'Natural y moderno',
            'colores' => ['#34D399', '#10B981', '#064E3B'],
            'texto' => '#ECFDF5',
            'fondo' => 'linear-gradient(135deg, #022C22 0%, #065F46 38%, #34D399 100%)',
        ],
        [
            'slug' => 'magenta-boost',
            'nombre' => 'Magenta Boost',
            'descripcion' => 'Impactante y premium',
            'colores' => ['#EC4899', '#8B5CF6', '#3B0764'],
            'texto' => '#FDF2F8',
            'fondo' => 'linear-gradient(135deg, #3B0764 0%, #7C3AED 40%, #EC4899 100%)',
        ],
        [
            'slug' => 'ice-mint',
            'nombre' => 'Ice Mint',
            'descripcion' => 'Minimalista y limpio',
            'colores' => ['#67E8F9', '#14B8A6', '#0F172A'],
            'texto' => '#ECFEFF',
            'fondo' => 'linear-gradient(135deg, #0F172A 0%, #0F766E 45%, #67E8F9 100%)',
        ],
        [
            'slug' => 'sunset-glow',
            'nombre' => 'Sunset Glow',
            'descripcion' => 'Cálido y vibrante',
            'colores' => ['#FB7185', '#F59E0B', '#7C2D12'],
            'texto' => '#FFF7ED',
            'fondo' => 'linear-gradient(135deg, #7C2D12 0%, #F97316 35%, #FB7185 100%)',
        ],
    ];

    public array $paletasExtras = [
        [
            'slug' => 'midnight-violet',
            'nombre' => 'Midnight Violet',
            'descripcion' => 'Profundo y premium',
            'colores' => ['#4F46E5', '#7C3AED', '#0F172A'],
            'texto' => '#EDE9FE',
            'fondo' => 'linear-gradient(135deg, #0F172A 0%, #4F46E5 45%, #A78BFA 100%)',
        ],
        [
            'slug' => 'forest-emerald',
            'nombre' => 'Forest Emerald',
            'descripcion' => 'Natural y seguro',
            'colores' => ['#10B981', '#166534', '#D1FAE5'],
            'texto' => '#ECFDF5',
            'fondo' => 'linear-gradient(135deg, #022C22 0%, #166534 40%, #34D399 100%)',
        ],
        [
            'slug' => 'rose-luxe',
            'nombre' => 'Rose Luxe',
            'descripcion' => 'Fresco y exclusivo',
            'colores' => ['#F472B6', '#EC4899', '#FDF2F8'],
            'texto' => '#FFF1F2',
            'fondo' => 'linear-gradient(135deg, #831843 0%, #EC4899 50%, #F9A8D4 100%)',
        ],
        [
            'slug' => 'ocean-glow',
            'nombre' => 'Ocean Glow',
            'descripcion' => 'Claro y relajante',
            'colores' => ['#0EA5E9', '#14B8A6', '#E0F2FE'],
            'texto' => '#F0FDFF',
            'fondo' => 'linear-gradient(135deg, #082F49 0%, #0EA5E9 42%, #5EEAD4 100%)',
        ],
        [
            'slug' => 'amber-sunset',
            'nombre' => 'Amber Sunset',
            'descripcion' => 'Cálido y distinguido',
            'colores' => ['#F59E0B', '#F97316', '#FFF7ED'],
            'texto' => '#FFF7ED',
            'fondo' => 'linear-gradient(135deg, #7C2D12 0%, #F97316 38%, #FBBF24 100%)',
        ],
        [
            'slug' => 'lavender-soft',
            'nombre' => 'Lavender Soft',
            'descripcion' => 'Suave y elegante',
            'colores' => ['#A78BFA', '#C4B5FD', '#F5F3FF'],
            'texto' => '#2E1065',
            'fondo' => 'linear-gradient(135deg, #4C1D95 0%, #A78BFA 50%, #E9D5FF 100%)',
        ],
    ];

    public function cambiarPersonalizado(bool $isPersonalizado)
    {
        $this->isPersonalizado = $isPersonalizado;
        $this->persistPaleta();
    }

    public function cambiarPaleta(string $slug)
    {
        $this->paletaSeleccionada = $slug;
        $this->persistPaleta();
    }

    public function updatedColorPrincipal(): void
    {
        $this->persistPaleta();
    }

    public function updatedColorSecundario(): void
    {
        $this->persistPaleta();
    }

    public function updatedColorTexto(): void
    {
        $this->persistPaleta();
    }

    public function getColor(): void
    {
        $paleta =
            collect($this->paletas)->firstWhere('slug', $this->paletaSeleccionada) ??
            (collect($this->paletasExtras)->firstWhere('slug', $this->paletaSeleccionada) ?? [
                'slug' => 'cacao-clasico',
                'nombre' => 'Cacao Clásico',
                'descripcion' => 'Templado y premium',
                'colores' => ['#F59E0B', '#D97706', '#7C2D12'],
                'texto' => '#F8FAFC',
                'fondo' => 'linear-gradient(135deg, #7C2D12 0%, #B45309 38%, #F59E0B 100%)',
            ]);

        $this->colorPrincipal = $paleta['colores'][0] ?? '#7C2D12';
        $this->colorSecundario = $paleta['colores'][1] ?? '#F59E0B';
        $this->colorTexto = $paleta['texto'] ?? '#F8FAFC';
    }

    public function sincronizarColoresDesdePaleta(): void
    {
        $this->getColor();
        $this->persistPaleta();
    }

    private function buildPaletaPayload(): array
    {
        $basePaleta =
            collect($this->paletas)->firstWhere('slug', $this->paletaSeleccionada) ??
            (collect($this->paletasExtras)->firstWhere('slug', $this->paletaSeleccionada) ?? [
                'slug' => $this->paletaSeleccionada,
                'nombre' => 'Personalizada',
                'descripcion' => 'Paleta personalizada',
                'colores' => [$this->colorPrincipal, $this->colorSecundario, $this->colorTexto],
                'texto' => $this->colorTexto,
                'fondo' => "linear-gradient(135deg, {$this->colorPrincipal} 0%, {$this->colorSecundario} 100%)",
            ]);

        $tipo = $this->isPersonalizado ? 'personalizado' : 'preseleccionado';

        return [
            'slug' => $this->paletaSeleccionada,
            'tipo' => $tipo,
            'isPersonalizado' => $this->isPersonalizado,
            'colorPrincipal' => $this->isPersonalizado ? $this->colorPrincipal : $basePaleta['colores'][0] ?? '#7C2D12',
            'colorSecundario' => $this->isPersonalizado ? $this->colorSecundario : $basePaleta['colores'][1] ?? '#F59E0B',
            'colorTexto' => $this->isPersonalizado ? $this->colorTexto : $basePaleta['texto'] ?? '#F8FAFC',
            'texto' => $this->isPersonalizado ? $this->colorTexto : $basePaleta['texto'] ?? '#F8FAFC',
            'fondo' => $this->isPersonalizado ? "linear-gradient(135deg, {$this->colorPrincipal} 0%, {$this->colorSecundario} 100%)" : $basePaleta['fondo'] ?? "linear-gradient(135deg, {$this->colorPrincipal} 0%, {$this->colorSecundario} 100%)",
            'nombre' => $basePaleta['nombre'] ?? 'Personalizada',
            'descripcion' => $basePaleta['descripcion'] ?? 'Paleta personalizada',
        ];
    }

    private function persistPaleta(): void
    {
        $this->registro['type'] = $this->isPersonalizado ? 'personalizado' : 'preseleccionado';
        $this->registro['paleta'] = $this->buildPaletaPayload();
        $this->registro['tarjeta'] = $this->registro['paleta'];
    }
};
?>

<div>
    <div class="max-w-2xl">
        <p class="mb-3 text-sm font-semibold text-fidentta-teal">Define la experiencia</p>
        <h2 class="text-3xl font-semibold tracking-tight text-text sm:text-5xl">¿Qué colores lleva tu tarjeta?
        </h2>
        <p class="mt-4 text-base leading-7 text-text-secondary/70 sm:text-lg">Se pueden ajustar después desde el panel
            cuando quieras.</p>
    </div>

    <div class="mt-10   grid grid-cols-5 gap-4">
        <div class="flex flex-col col-span-3  items-start gap-4">
            <div class="flex rounded-full bg-fidentta-blue/90 p-1.5 shadow-lg shadow-fidentta-blue/20">
                <label class="relative flex-1 cursor-pointer">
                    <input type="radio" value="0" wire:model.live="isPersonalizado" class="peer sr-only" />
                    <span
                        class="flex items-center justify-center rounded-full px-4 py-2 text-sm font-semibold transition-all duration-200 peer-checked:bg-fidentta-cyan peer-checked:text-fidentta-navy peer-checked:shadow-lg peer-checked:shadow-fidentta-cyan/25 {{ !$isPersonalizado ? 'bg-fidentta-cyan text-fidentta-navy shadow-lg shadow-fidentta-cyan/25' : 'text-white/80 hover:text-white' }}">
                        Preseleccionado
                    </span>
                </label>

                <label class="relative flex-1 cursor-pointer">
                    <input type="radio" value="1" wire:model.live="isPersonalizado" class="peer sr-only" />
                    <span
                        class="flex items-center justify-center rounded-full px-4 py-2 text-sm font-semibold transition-all duration-200 peer-checked:bg-fidentta-cyan peer-checked:text-fidentta-navy peer-checked:shadow-lg peer-checked:shadow-fidentta-cyan/25 {{ $isPersonalizado ? 'bg-fidentta-cyan text-fidentta-navy shadow-lg shadow-fidentta-cyan/25' : 'text-white/80 hover:text-white' }}">
                        Personalizado
                    </span>
                </label>
            </div>
            @if ($isPersonalizado)
                <div class="w-full rounded-2xl border border-white/10 bg-white/5 p-4">
                    <p class="mb-4 text-sm font-semibold uppercase tracking-[0.2em] text-fidentta-cyan">Personaliza la
                        tarjeta</p>
                    <div class="grid gap-4 sm:grid-cols-3">
                        <label class="block rounded-xl border border-white/10 bg-fidentta-navy/40 p-3">
                            <span
                                class="mb-2 block text-xs font-medium uppercase tracking-[0.18em] text-text-secondary/70">Principal</span>
                            <input type="color" wire:model.live="colorPrincipal" value="{{ $colorPrincipal }}"
                                class="h-12 w-full cursor-pointer rounded-lg border-0 bg-transparent p-0" />
                        </label>

                        <label class="block rounded-xl border border-white/10 bg-fidentta-navy/40 p-3">
                            <span
                                class="mb-2 block text-xs font-medium uppercase tracking-[0.18em] text-text-secondary/70">Secundario</span>
                            <input type="color" wire:model.live="colorSecundario" value="{{ $colorSecundario }}"
                                class="h-12 w-full cursor-pointer rounded-lg border-0 bg-transparent p-0" />
                        </label>

                        <label class="block rounded-xl border border-white/10 bg-fidentta-navy/40 p-3">
                            <span
                                class="mb-2 block text-xs font-medium uppercase tracking-[0.18em] text-text-secondary/70">Texto</span>
                            <input type="color" wire:model.live="colorTexto" value="{{ $colorTexto }}"
                                class="h-12 w-full cursor-pointer rounded-lg border-0 bg-transparent p-0" />
                        </label>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-3 w-full gap-4">
                    @foreach ($paletasExtras as $paleta)
                        <div wire:click="cambiarPaleta('{{ $paleta['slug'] }}')"
                            class="h-24 flex cursor-pointer relative flex-col justify-between w-full rounded-lg p-4 {{ $paletaSeleccionada === $paleta['slug'] ? 'border-2 border-fidentta-blue' : '' }}"
                            style="background:  {{ $paleta['colores'][0] }};">
                            <div class="flex gap-2">
                                @foreach (array_slice($paleta['colores'], 1, 2) as $color)
                                    <div class="w-2/5 h-3 rounded-full" style="background-color: {{ $color }};">
                                    </div>
                                @endforeach
                            </div>

                            {{ $paleta['nombre'] }}

                            @if ($paletaSeleccionada === $paleta['slug'])
                                <div
                                    class="absolute top-0 right-0 m-4 flex items-center justify-center rounded-full w-5 h-5 bg-white">
                                    <svg class="w-3 h-3" viewBox="0 0 18 14" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 7L7 13L17 1" stroke="black" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                <!--Colores seleccionados -->
                <div class="flex w-full items-center gap-2">
                    <hr class="flex-1 border-0 border-t border-gray-400/70">
                    <span class="text-gray-400/70">o</span>
                    <hr class="flex-1 border-0 border-t border-gray-400/70">
                </div>
                <div class="grid grid-cols-3 w-full gap-4">
                    @foreach ($paletas as $paleta)
                        <div wire:click="cambiarPaleta('{{ $paleta['slug'] }}')"
                            class="h-24 cursor-pointer {{ $paletaSeleccionada === $paleta['slug'] ? 'border-2 border-fidentta-blue' : '' }} flex relative flex-col justify-between w-full rounded-lg p-4"
                            style="background: linear-gradient(135deg, {{ implode(', ', $paleta['colores']) }});">
                            <div class="flex gap-2">
                                @foreach ($paleta['colores'] as $color)
                                    <div class="w-2/5 h-3 rounded-full" style="background-color: {{ $color }};">
                                    </div>
                                @endforeach
                            </div>

                            {{ $paleta['nombre'] }}

                            @if ($paletaSeleccionada === $paleta['slug'])
                                <div
                                    class="absolute top-0 right-0 m-4 flex items-center justify-center rounded-full w-5 h-5 bg-white">
                                    <svg class="w-3 h-3" viewBox="0 0 18 14" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path d="M1 7L7 13L17 1" stroke="black" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                    @endforeach

                </div>


            @endif
        </div>
        <div class="col-span-2">
            @php
                $sellosPreview = $this->registro['sellos'] ?? 8;
                $previewBackground =
                    $this->registro['paleta']['fondo'] ??
                    ($this->isPersonalizado
                        ? 'linear-gradient(135deg, ' .
                            $this->colorPrincipal .
                            ' 0%, ' .
                            $this->colorSecundario .
                            ' 100%)'
                        : 'linear-gradient(135deg, #7C2D12 0%, #B45309 38%, #F59E0B 100%)');

                $previewTextColor =
                    $this->registro['paleta']['texto'] ?? ($this->isPersonalizado ? $this->colorTexto : '#F8FAFC');
                $logoInfo = $this->registro['logo'] ?? ['type' => 'text', 'content' => 'C', 'url' => null];
                $logoUrl = $logoInfo['url'] ?? null;
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
                            {{ strtoupper(substr($registro['name'] ?? 'Cafe laté', 0, 1)) }}
                        </div>
                    @endif
                    <span class="text-base font-semibold">{{ $registro['name'] ?? 'Cafe laté' }}</span>
                </div>

                <div class="mt-5 grid grid-cols-4 gap-3">
                    @for ($i = 0; $i < $sellosPreview; $i++)
                        <div class="flex items-center justify-center">
                            <div
                                class="h-14 w-14 rounded-full border border-white/30 bg-white/10 shadow-inner shadow-white/20">
                            </div>
                        </div>
                    @endfor
                </div>

                <p class="mt-5 text-sm opacity-85">Completa los {{ $registro['sellos'] ?? 8 }} sellos y consigue:
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
