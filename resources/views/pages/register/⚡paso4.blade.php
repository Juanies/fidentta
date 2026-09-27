<?php

use Livewire\Component;
use Livewire\Attributes\Modelable;

new class extends Component {
    #[Modelable]
    public array $registro = [
        'name' => 'Cafe laté',
        'logo' => ['type' => 'text', 'content' => 'C', 'url' => null],
        'sellos' => 8,
                'error' => false,

        'recompensa' => 'Bebida gratis',
        'registro_usuarios' => [
            'modo' => 'basico',
            'campos' => ['email'],
        ],
    ];

    public array $opcionesCampos = [
        'nombre' => 'Nombre',
        'email' => 'Email',
        'telefono' => 'Teléfono',
        'cumpleanos' => 'Fecha de cumpleaños',
    ];

    public string $nuevoCampo = '';

    public function mount(): void
    {
        if (($this->registro['registro_usuarios']['modo'] ?? 'basico') === 'basico') {
            $campos = $this->registro['registro_usuarios']['campos'] ?? [];
            $this->registro['registro_usuarios']['campos'] = array_values(array_unique(array_merge(['email', 'password'], $campos)));
        }
    }

    public function elegirModo(string $modo): void
    {
        $this->registro['registro_usuarios']['modo'] = $modo;

        if ($modo === 'sin_registro') {
            $this->registro['registro_usuarios']['campos'] = [];
        } elseif ($modo === 'basico') {
            $this->registro['registro_usuarios']['campos'] = ['email', 'password'];
        }
    }

    public function alternarCampo(string $campo): void
    {
        $campos = $this->registro['registro_usuarios']['campos'] ?? [];

        if (in_array($campo, $campos, true)) {
            $campos = array_values(array_diff($campos, [$campo]));
        } else {
            $campos[] = $campo;
        }

        $this->registro['registro_usuarios']['campos'] = $campos;
    }

    public function agregarCampoPersonalizado(): void
    {
        $campo = trim($this->nuevoCampo);

        if ($campo === '') {
            return;
        }

        $campos = $this->registro['registro_usuarios']['campos'] ?? [];

        if (!in_array($campo, $campos, true)) {
            $campos[] = $campo;
        }

        $this->registro['registro_usuarios']['campos'] = $campos;
        $this->nuevoCampo = '';
    }
};

?>

<div>
    <div class="max-w-3xl">
        <p class="mb-3 text-sm font-semibold text-fidentta-teal">Diseña el acceso de tus clientes</p>
        <h2 class="text-3xl font-semibold tracking-tight text-text sm:text-5xl">¿Qué datos quieres pedir?
        </h2>
        <p class="mt-4 text-base leading-7 text-text-secondary/70 sm:text-lg">
            Pide solo lo necesario para que registrarse sea sencillo y tu programa pueda crecer.
        </p>
    </div>

    <div class="mt-10 grid grid-cols-1 gap-4 lg:grid-cols-2">
        @php
            $modoActual = $registro['registro_usuarios']['modo'] ?? 'basico';
            $camposActuales = $registro['registro_usuarios']['campos'] ?? ['email'];
        @endphp

        <button type="button" wire:click="elegirModo('sin_registro')"
            class="text-left rounded-2xl border p-6 transition hover:-translate-y-0.5 lg:col-span-2 {{ $modoActual === 'sin_registro' ? 'border-fidentta-cyan bg-fidentta-cyan/10 shadow-lg shadow-fidentta-cyan/10' : 'border-white/10 bg-white/[0.03] hover:border-white/20' }}">
            <div class="flex items-start justify-between gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 text-xl">✦
                </div>
                <span
                    class="h-5 w-5 rounded-full border-2 {{ $modoActual === 'sin_registro' ? 'border-fidentta-cyan bg-fidentta-cyan' : 'border-white/30' }}"></span>
            </div>
            <h3 class="mt-5 text-lg font-semibold text-text">Ninguno: sin ventana de registro</h3>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-text-secondary/70">Tus clientes empiezan al instante, sin
                crear una cuenta ni compartir datos. Ideal si quieres eliminar cualquier fricción.</p>
            <p class="mt-4 text-xs font-semibold uppercase tracking-[0.16em] text-fidentta-cyan">Más rápido
            </p>
        </button>

        <button type="button" wire:click="elegirModo('basico')"
            class="text-left rounded-2xl border p-6 transition hover:-translate-y-0.5 {{ $modoActual === 'basico' ? 'border-fidentta-cyan bg-fidentta-cyan/10 shadow-lg shadow-fidentta-cyan/10' : 'border-white/10 bg-white/[0.03] hover:border-white/20' }}">
            <div class="flex items-start justify-between gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 text-xl">◎
                </div>
                <span
                    class="h-5 w-5 rounded-full border-2 {{ $modoActual === 'basico' ? 'border-fidentta-cyan bg-fidentta-cyan' : 'border-white/30' }}"></span>
            </div>
            <h3 class="mt-5 text-lg font-semibold text-text">Registro básico</h3>
            <p class="mt-2 text-sm leading-6 text-text-secondary/70">La opción sencilla: email y contraseña. El teléfono
                queda disponible como dato opcional.</p>
            <p class="mt-4 text-xs font-semibold uppercase tracking-[0.16em] text-fidentta-cyan">Recomendado
            </p>
        </button>

        <button type="button" wire:click="elegirModo('personalizado')"
            class="text-left rounded-2xl border p-6 transition hover:-translate-y-0.5 {{ $modoActual === 'personalizado' ? 'border-fidentta-cyan bg-fidentta-cyan/10 shadow-lg shadow-fidentta-cyan/10' : 'border-white/10 bg-white/[0.03] hover:border-white/20' }}">
            <div class="flex items-start justify-between gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 text-xl">＋
                </div>
                <span
                    class="h-5 w-5 rounded-full border-2 {{ $modoActual === 'personalizado' ? 'border-fidentta-cyan bg-fidentta-cyan' : 'border-white/30' }}"></span>
            </div>
            <h3 class="mt-5 text-lg font-semibold text-text">Personalizado</h3>
            <p class="mt-2 text-sm leading-6 text-text-secondary/70">Construye el formulario con los datos
                que mejor encajen con tu negocio.</p>
            <p class="mt-4 text-xs font-semibold uppercase tracking-[0.16em] text-fidentta-cyan">Más control
            </p>
        </button>
    </div>

    @if ($modoActual === 'basico')
        <div class="mt-4 rounded-2xl border border-white/10 bg-white/[0.03] p-5 sm:p-6 lg:col-span-2">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-text">Registro básico</h3>
                    <p class="mt-1 text-sm text-text-secondary/70">Email y contraseña son necesarios. Puedes añadir el
                        teléfono si te resulta útil.</p>
                </div>
                <span class="text-xs font-medium text-text-secondary/60">2 obligatorios</span>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                <div
                    class="flex items-center justify-between rounded-xl border border-fidentta-cyan/30 bg-fidentta-cyan/10 px-4 py-3">
                    <span class="text-sm font-medium text-text">Email</span>
                    <span class="text-xs font-semibold text-fidentta-cyan">Obligatorio</span>
                </div>
                <div
                    class="flex items-center justify-between rounded-xl border border-fidentta-cyan/30 bg-fidentta-cyan/10 px-4 py-3">
                    <span class="text-sm font-medium text-text">Contraseña</span>
                    <span class="text-xs font-semibold text-fidentta-cyan">Obligatorio</span>
                </div>
                <button type="button" wire:click="alternarCampo('telefono')"
                    class="flex items-center justify-between rounded-xl border px-4 py-3 text-left transition {{ in_array('telefono', $camposActuales, true) ? 'border-fidentta-cyan bg-fidentta-cyan/10' : 'border-white/10 bg-white/[0.02] hover:border-white/20' }}">
                    <span class="text-sm font-medium text-text">Teléfono</span>
                    <span
                        class="text-xs font-semibold {{ in_array('telefono', $camposActuales, true) ? 'text-fidentta-cyan' : 'text-text-secondary/50' }}">{{ in_array('telefono', $camposActuales, true) ? 'Opcional activo' : 'Opcional' }}</span>
                </button>
            </div>
        </div>
    @elseif ($modoActual === 'personalizado')
        <div class="mt-4 rounded-2xl border border-white/10 bg-white/[0.03] p-5 sm:p-6 lg:col-span-2">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-text">Campos del formulario</h3>
                    <p class="mt-1 text-sm text-text-secondary/70">Puedes cambiar esta selección en
                        cualquier momento.</p>
                </div>
                <span class="text-xs font-medium text-text-secondary/60">{{ count($camposActuales) }}
                    seleccionados</span>
            </div>

            <div class="mt-5 grid gap-3 sm:grid-cols-2">
                @foreach ($opcionesCampos as $campo => $etiqueta)
                    <button type="button" wire:click="alternarCampo('{{ $campo }}')"
                        class="flex items-center justify-between rounded-xl border px-4 py-3 text-left transition {{ in_array($campo, $camposActuales, true) ? 'border-fidentta-cyan bg-fidentta-cyan/10' : 'border-white/10 bg-white/[0.02] hover:border-white/20' }}">
                        <span class="text-sm font-medium text-text">{{ $etiqueta }}</span>
                        <span
                            class="flex h-5 w-5 items-center justify-center rounded border {{ in_array($campo, $camposActuales, true) ? 'border-fidentta-cyan bg-fidentta-cyan text-fidentta-navy' : 'border-white/25' }}">
                            @if (in_array($campo, $camposActuales, true))
                                ✓
                            @endif
                        </span>
                    </button>
                @endforeach
            </div>

            @if (empty($camposActuales))
                <p class="mt-4 text-sm text-amber-300">Selecciona al menos un campo para que tus clientes
                    puedan identificarse.</p>
            @endif

            <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                <input wire:model="nuevoCampo" wire:keydown.enter="agregarCampoPersonalizado" type="text"
                    placeholder="Añade otro campo, por ejemplo: ciudad"
                    class="min-w-0 flex-1 rounded-xl border border-white/15 bg-white/[0.04] px-4 py-3 text-sm text-text outline-none placeholder:text-text-secondary/50 focus:border-fidentta-cyan focus:ring-2 focus:ring-fidentta-cyan/20">
                <button type="button" wire:click="agregarCampoPersonalizado"
                    class="rounded-xl bg-fidentta-cyan px-4 py-3 text-sm font-bold text-fidentta-navy transition hover:bg-white">Añadir
                    campo</button>
            </div>
        </div>
    @else
        <div class="mt-4 flex gap-3 rounded-2xl border border-fidentta-cyan/20 bg-fidentta-cyan/5 p-5 lg:col-span-2">
            <span class="text-lg text-fidentta-cyan">✓</span>
            <div>
                <p class="font-semibold text-fidentta-cyan">Entrada sin fricción</p>
                <p class="mt-1 text-sm leading-6 text-text-secondary/70">Tus clientes podrán usar su tarjeta
                    sin crear una cuenta ni compartir datos personales.</p>
            </div>
        </div>
    @endif
</div>
