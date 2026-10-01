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
            'modo' => 'normal',
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

    public function elegirModo(string $modo): void
    {
        if (!in_array($modo, ['none', 'normal', 'custom'], true)) {
            return;
        }

        $this->registro['registro_usuarios']['modo'] = $modo;

        if ($modo === 'none') {
            $this->registro['registro_usuarios']['campos'] = [];
        } elseif ($modo === 'normal') {
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
        <p class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] text-brand">Paso 4 · Registro de clientes</p>
        <h2 class="text-2xl font-semibold tracking-tight text-ink sm:text-3xl">¿Qué datos quieres pedir?
        </h2>
        <p class="mt-3 text-base leading-7 text-muted-foreground">
            Pide solo lo necesario para que registrarse sea sencillo y tu programa pueda crecer.
        </p>
    </div>

    <div class="mt-10 grid grid-cols-1 gap-4 lg:grid-cols-2">
        @php
            $modoActual = $registro['registro_usuarios']['modo'] ?? 'basico';
            $camposActuales = $registro['registro_usuarios']['campos'] ?? ['email'];
        @endphp

        <button type="button" wire:click="elegirModo('none')"
            class="text-left rounded-xl border p-5 transition hover:border-brand/50 lg:col-span-2 {{ $modoActual === 'none' ? 'border-brand bg-brand-soft' : 'border-border bg-card' }}">
            <div class="flex items-start justify-between gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-brand-soft text-xl text-brand">✦
                </div>
                <span
                    class="h-5 w-5 rounded-full border-2 {{ $modoActual === 'none' ? 'border-brand bg-brand' : 'border-border' }}"></span>
            </div>
            <h3 class="mt-5 text-lg font-semibold text-text">Ninguno: sin ventana de registro</h3>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-text-secondary/70">Tus clientes empiezan al instante, sin
                crear una cuenta ni compartir datos. Ideal si quieres eliminar cualquier fricción.</p>
            <p class="mt-4 text-xs font-semibold uppercase tracking-[0.16em] text-fidentta-cyan">Más rápido
            </p>
        </button>

        <button type="button" wire:click="elegirModo('normal')"
            class="text-left rounded-xl border p-5 transition hover:border-brand/50 {{ $modoActual === 'normal' ? 'border-brand bg-brand-soft' : 'border-border bg-card' }}">
            <div class="flex items-start justify-between gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-brand-soft text-xl text-brand">◎
                </div>
                <span
                    class="h-5 w-5 rounded-full border-2 {{ $modoActual === 'normal' ? 'border-brand bg-brand' : 'border-border' }}"></span>
            </div>
            <h3 class="mt-5 text-lg font-semibold text-text">Registro básico</h3>
            <p class="mt-2 text-sm leading-6 text-text-secondary/70">La opción sencilla: email y contraseña. El teléfono
                queda disponible como dato opcional.</p>
            <p class="mt-4 text-xs font-semibold uppercase tracking-[0.16em] text-fidentta-cyan">Recomendado
            </p>
        </button>

        <button type="button" wire:click="elegirModo('custom')"
            class="text-left rounded-xl border p-5 transition hover:border-brand/50 {{ $modoActual === 'custom' ? 'border-brand bg-brand-soft' : 'border-border bg-card' }}">
            <div class="flex items-start justify-between gap-4">
                <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-brand-soft text-xl text-brand">＋
                </div>
                <span
                    class="h-5 w-5 rounded-full border-2 {{ $modoActual === 'custom' ? 'border-brand bg-brand' : 'border-border' }}"></span>
            </div>
            <h3 class="mt-5 text-lg font-semibold text-text">Personalizado</h3>
            <p class="mt-2 text-sm leading-6 text-text-secondary/70">Construye el formulario con los datos
                que mejor encajen con tu negocio.</p>
            <p class="mt-4 text-xs font-semibold uppercase tracking-[0.16em] text-fidentta-cyan">Más control
            </p>
        </button>
    </div>

    @if ($modoActual === 'normal')
        <div class="mt-4 rounded-xl border border-border bg-card p-5 sm:p-6 lg:col-span-2">
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
                    class="flex items-center justify-between rounded-lg border px-4 py-3 text-left transition {{ in_array('telefono', $camposActuales, true) ? 'border-brand bg-brand-soft' : 'border-border bg-background hover:border-brand/40' }}">
                    <span class="text-sm font-medium text-text">Teléfono</span>
                    <span
                        class="text-xs font-semibold {{ in_array('telefono', $camposActuales, true) ? 'text-fidentta-cyan' : 'text-text-secondary/50' }}">{{ in_array('telefono', $camposActuales, true) ? 'Opcional activo' : 'Opcional' }}</span>
                </button>
            </div>
        </div>
    @elseif ($modoActual === 'custom')
        <div class="mt-4 rounded-xl border border-border bg-card p-5 sm:p-6 lg:col-span-2">
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
                        class="flex items-center justify-between rounded-lg border px-4 py-3 text-left transition {{ in_array($campo, $camposActuales, true) ? 'border-brand bg-brand-soft' : 'border-border bg-background hover:border-brand/40' }}">
                        <span class="text-sm font-medium text-text">{{ $etiqueta }}</span>
                        <span
                            class="flex h-5 w-5 items-center justify-center rounded border {{ in_array($campo, $camposActuales, true) ? 'border-brand bg-brand text-white' : 'border-border' }}">
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
                    class="min-w-0 flex-1 rounded-lg border border-border bg-background px-4 py-3 text-sm text-ink outline-none placeholder:text-muted-foreground/70 focus:border-brand focus:ring-2 focus:ring-brand/20">
                <button type="button" wire:click="agregarCampoPersonalizado"
                    class="rounded-xl bg-fidentta-cyan px-4 py-3 text-sm font-bold text-fidentta-navy transition hover:bg-white">Añadir
                    campo</button>
            </div>
        </div>
    @elseif ($modoActual === 'none')
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
