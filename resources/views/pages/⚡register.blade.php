<?php

use Livewire\Component;

new class extends Component {
    public int $pasosTotales = 5;
    public int $paso = 1;
    public string $pasoTitle = 'Tipo de negocio';
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
            'modo' => 'basico',
            'campos' => ['email'],
        ],
        'tarjeta' => null,
    ];

    public function mount()
    {
        $this->paso = (int) request()->cookie('registro_step', 1);
    }
    public function siguiente()
    {
        if ($this->paso === 1) {
            $this->validate([
                'registro.name' => 'required|min:3|max:10',
            ]);
        }

        $this->paso++;

        if ($this->paso > $this->pasosTotales) {
            $this->paso = $this->pasosTotales;
        }

        if($this->paso == 5 ){
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

<section class="relative isolate min-h-[calc(100svh-4rem)] overflow-hidden bg-fidentta-navy">
    <div class="pointer-events-none absolute inset-0 -z-10 bg-fidentta-gradient-soft opacity-10"></div>
    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <div
            class="flex flex-col gap-6 border-b border-text-secondary/10 pb-8 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-fidentta-cyan">Configuración inicial
                </p>
                <h1 class="text-3xl font-semibold tracking-tight text-text sm:text-4xl">Fiddenta</h1>
            </div>
            <div class="sm:text-right">
                <p class="text-sm text-text-secondary/70">Paso {{ $paso }} de {{ $pasosTotales }}</p>
                <p class="mt-1 text-lg font-medium text-text">{{ $pasoTitle }}</p>
            </div>
        </div>
        <div class="mt-8 grid grid-cols-{{ $pasosTotales }} gap-2" aria-label="Progreso de configuración">
            @for ($i = 1; $i <= $pasosTotales; $i++)
                <div
                    class="h-2 w-full rounded-full {{ $i < $paso ? 'bg-fidentta-teal' : ($i === $paso ? 'bg-fidentta-cyan' : 'bg-fidentta-blue/30') }}">
                </div>
            @endfor
        </div>
        <div class="mt-10 rounded-[2rem] border border-white/10 bg-white/[0.03] p-5 shadow-2xl shadow-black/10 sm:p-8">
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
        <div class="mt-8 flex items-center justify-between gap-4 border-t border-white/10 pt-6">
            @if ($paso != 1)
                <button type="button"
                    class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-white/[0.04] px-5 py-3 text-sm font-semibold text-text transition hover:border-fidentta-cyan hover:bg-white/[0.08] hover:text-fidentta-cyan focus:outline-none focus:ring-2 focus:ring-fidentta-cyan/50"
                    wire:click="volver">
                    <span aria-hidden="true">←</span>
                    Volver
                </button>
            @else
                <span></span>
            @endif
            <button type="button" wire:click="siguiente"
                class="inline-flex items-center gap-2 rounded-xl border border-white/15 bg-fidentta-cyan px-5 py-3 text-sm font-semibold text-white">
                Continuar
                <span aria-hidden="true">→</span>
            </button>
        </div>
    </div>
</section>
