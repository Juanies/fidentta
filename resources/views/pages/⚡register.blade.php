<?php

use Livewire\Component;

new class extends Component {
    public int $pasosTotales = 5;
    public int $paso = 1;
    public array $registro = [
        'name' => 'Cafe laté',
        'errora' => false,

        'logo' => [
            'type' => 'text',
            'content' => 'C',
            'url' => null,
        ],
        'sellos' => 8,
        'recompensa' => 'Ej. Café gratis',
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
        'registro_usuarios' => [
            'modo' => 'normal',
            'campos' => ['email'],
        ],
        'tarjeta' => null,
    ];

    public function getPasoTitleProperty(): string
    {
        return match ($this->paso) {
            1 => 'Tu negocio',
            2 => 'Identidad visual',
            3 => 'Sellos y recompensa',
            4 => 'Registro de clientes',
            5 => 'Crear cuenta',
            default => 'Configuración inicial',
        };
    }

    public function siguiente()
    {
        if ($this->paso === 1) {
            $this->validate([
                'registro.name' => 'required|min:3|max:60',
            ]);
        }

        $this->paso++;

        if ($this->paso > $this->pasosTotales) {
            $this->paso = $this->pasosTotales;
        }

        if ($this->paso == 5) {
            session()->put('registro', $this->registro);
        }
        $this->guardarPaso();
    }
    public function volver()
    {
        $this->paso--;
        $this->guardarPaso();
    }
    private function guardarPaso()
    {
        cookie()->queue('registro_step', $this->paso, 60 * 24 * 30);
    }
};

?>

<section class="min-h-[calc(100svh-4rem)] bg-[#f3f6f4] text-ink">
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <div class="flex flex-col gap-6 border-b border-border pb-8 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-fidentta-cyan">Configuración inicial
                </p>
                <h1 class="text-3xl font-semibold tracking-tight text-text sm:text-4xl">Fidentta</h1>
            </div>
            <div class="sm:text-right">
                <p class="text-sm text-text-secondary/70">Paso {{ $paso }} de {{ $pasosTotales }}</p>
                <p class="mt-1 text-lg font-medium text-ink">{{ $this->pasoTitle }}</p>
            </div>
        </div>
        @php
            $etiquetasPasos = ['Negocio', 'Colores', 'Recompensa', 'Registro', 'Cuenta'];
        @endphp
        <ol class="mt-8 grid grid-cols-5 gap-2" aria-label="Progreso de configuración">
            @foreach ($etiquetasPasos as $indice => $etiqueta)
                <li class="min-w-0 text-center" @if ($paso === $indice + 1) aria-current="step" @endif>
                    <span
                        class="mx-auto flex size-8 items-center justify-center rounded-full border text-xs font-semibold {{ $paso > $indice + 1 ? 'border-brand bg-brand text-white' : ($paso === $indice + 1 ? 'border-brand bg-white text-brand ring-2 ring-brand/15' : 'border-border bg-white/70 text-muted-foreground') }}">
                        {{ $paso > $indice + 1 ? '✓' : $indice + 1 }}
                    </span>
                    <span
                        class="mt-2 block truncate text-[11px] font-medium {{ $paso === $indice + 1 ? 'text-ink' : 'text-muted-foreground' }} sm:text-xs">{{ $etiqueta }}</span>
                </li>
            @endforeach
        </ol>
        <div class="mt-4 h-1.5 overflow-hidden rounded-full bg-white/80" aria-hidden="true">
            <div class="h-full rounded-full bg-brand transition-[width] duration-300"
                style="width: {{ ($paso / $pasosTotales) * 100 }}%"></div>
        </div>
        <div class="mt-8">
            @if ($this->paso == 1)
                <livewire:pages::register.paso1 wire:model="registro" />
            @elseif($this->paso == 2)
                <livewire:pages::register.paso2 wire:model="registro" />
            @elseif($this->paso == 3)
                <livewire:pages::register.paso3 wire:model="registro" />
            @elseif($this->paso == 4)
                <livewire:pages::register.paso4 wire:model="registro" />
            @elseif($this->paso == 5)
                <livewire:pages::register.paso5 wire:model="registro" />
            @endif
        </div>
        <div class="mt-8 flex items-center justify-between gap-4 border-t border-border pt-6">
            @if ($paso != 1)
                <button type="button"
                    class="inline-flex items-center gap-2 rounded-xl border border-border bg-card px-5 py-3 text-sm font-semibold text-ink transition hover:border-brand hover:bg-brand-soft hover:text-brand focus:outline-none focus:ring-2 focus:ring-brand/50"
                    wire:click="volver">
                    <span aria-hidden="true">←</span>
                    Volver
                </button>
            @else
                <span></span>
            @endif
            @if ($paso < $pasosTotales)
                <button type="button" wire:click="siguiente" wire:loading.attr="disabled" wire:target="siguiente"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-brand/20 transition hover:bg-brand-700 disabled:opacity-60">
                    Continuar <span aria-hidden="true">&rarr;</span>
                </button>
            @endif
        </div>
    </div>
</section>
