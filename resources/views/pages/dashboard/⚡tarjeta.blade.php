<?php

use App\Models\CustomerRegistrationField;
use App\Models\cardDesign as CardDesign;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $team;
    public $card;
    public $locations;
    public ?string $locationId = null;
    public $photo;

    public string $name = '';
    public ?string $logoUrl = null;
    public int $sellos = 8;
    public string $recompensa = '';
    public bool $isPersonalizado = false;
    public string $paletaSeleccionada = 'cacao-clasico';
    public string $colorPrincipal = '#7C2D12';
    public string $colorSecundario = '#F59E0B';
    public string $colorTexto = '#F8FAFC';
    public string $modoRegistro = 'normal';
    public array $campos = [];
    public string $nuevoCampo = '';
    public ?string $statusMessage = null;

    public array $paletas = [
        ['slug' => 'cacao-clasico', 'nombre' => 'Cacao Clásico', 'colores' => ['#F59E0B', '#D97706', '#7C2D12'], 'texto' => '#F8FAFC', 'fondo' => 'linear-gradient(135deg, #7C2D12 0%, #B45309 38%, #F59E0B 100%)'],
        ['slug' => 'azul-royal', 'nombre' => 'Azul Royal', 'colores' => ['#2563EB', '#1D4ED8', '#0F172A'], 'texto' => '#E0F2FE', 'fondo' => 'linear-gradient(135deg, #0F172A 0%, #1D4ED8 55%, #60A5FA 100%)'],
        ['slug' => 'verde-fresno', 'nombre' => 'Verde Fresco', 'colores' => ['#34D399', '#10B981', '#064E3B'], 'texto' => '#ECFDF5', 'fondo' => 'linear-gradient(135deg, #022C22 0%, #065F46 38%, #34D399 100%)'],
        ['slug' => 'magenta-boost', 'nombre' => 'Magenta Boost', 'colores' => ['#EC4899', '#8B5CF6', '#3B0764'], 'texto' => '#FDF2F8', 'fondo' => 'linear-gradient(135deg, #3B0764 0%, #7C3AED 40%, #EC4899 100%)'],
        ['slug' => 'ice-mint', 'nombre' => 'Ice Mint', 'colores' => ['#67E8F9', '#14B8A6', '#0F172A'], 'texto' => '#ECFEFF', 'fondo' => 'linear-gradient(135deg, #0F172A 0%, #0F766E 45%, #67E8F9 100%)'],
        ['slug' => 'sunset-glow', 'nombre' => 'Sunset Glow', 'colores' => ['#FB7185', '#F59E0B', '#7C2D12'], 'texto' => '#FFF7ED', 'fondo' => 'linear-gradient(135deg, #7C2D12 0%, #F97316 35%, #FB7185 100%)'],
    ];

    public array $paletasExtras = [
        ['slug' => 'midnight-violet', 'nombre' => 'Midnight Violet', 'colores' => ['#4F46E5', '#7C3AED', '#0F172A'], 'texto' => '#EDE9FE', 'fondo' => 'linear-gradient(135deg, #0F172A 0%, #4F46E5 45%, #A78BFA 100%)'],
        ['slug' => 'forest-emerald', 'nombre' => 'Forest Emerald', 'colores' => ['#10B981', '#166534', '#D1FAE5'], 'texto' => '#ECFDF5', 'fondo' => 'linear-gradient(135deg, #022C22 0%, #166534 40%, #34D399 100%)'],
        ['slug' => 'rose-luxe', 'nombre' => 'Rose Luxe', 'colores' => ['#F472B6', '#EC4899', '#FDF2F8'], 'texto' => '#FFF1F2', 'fondo' => 'linear-gradient(135deg, #831843 0%, #EC4899 50%, #F9A8D4 100%)'],
        ['slug' => 'ocean-glow', 'nombre' => 'Ocean Glow', 'colores' => ['#0EA5E9', '#14B8A6', '#E0F2FE'], 'texto' => '#F0FDFF', 'fondo' => 'linear-gradient(135deg, #082F49 0%, #0EA5E9 42%, #5EEAD4 100%)'],
        ['slug' => 'amber-sunset', 'nombre' => 'Amber Sunset', 'colores' => ['#F59E0B', '#F97316', '#FFF7ED'], 'texto' => '#FFF7ED', 'fondo' => 'linear-gradient(135deg, #7C2D12 0%, #F97316 38%, #FBBF24 100%)'],
        ['slug' => 'lavender-soft', 'nombre' => 'Lavender Soft', 'colores' => ['#A78BFA', '#C4B5FD', '#F5F3FF'], 'texto' => '#2E1065', 'fondo' => 'linear-gradient(135deg, #4C1D95 0%, #A78BFA 50%, #E9D5FF 100%)'],
    ];

    public array $ideasRecompensa = ['Bebida gratis', 'Pastel gratis', 'Regalo gratis', 'Descuento', '50% en el segundo', '2x1'];

    public array $opcionesCampos = [
        'nombre' => 'Nombre',
        'email' => 'Email',
        'telefono' => 'Teléfono',
        'cumpleanos' => 'Fecha de cumpleaños',
    ];

    public function mount(): void
    {
        $this->team = auth()->user()->currentTeam;
        abort_unless($this->team !== null, 404);

        $this->card = $this->team->cardDesign;
        $this->locations = $this->team->locations()->where('is_active', true)->orderBy('name')->get();
        $this->locationId = $this->locations->first()?->id;

        $this->name = $this->team->name;
        $this->logoUrl = $this->team->logo;
        $this->modoRegistro = in_array($this->team->customer_registration_type, ['none', 'normal', 'custom'], true) ? $this->team->customer_registration_type : 'normal';
        $this->campos = $this->team->customerRegistrationFields()->where('is_active', true)->orderBy('sort_order')->pluck('field_key')->all();

        $paleta = $this->card?->color_scheme ?? [];
        $this->paletaSeleccionada = $paleta['slug'] ?? 'cacao-clasico';
        $this->isPersonalizado = (bool) ($paleta['isPersonalizado'] ?? false);
        $this->colorPrincipal = $paleta['colorPrincipal'] ?? '#7C2D12';
        $this->colorSecundario = $paleta['colorSecundario'] ?? '#F59E0B';
        $this->colorTexto = $paleta['colorTexto'] ?? ($paleta['texto'] ?? '#F8FAFC');
        $this->sellos = (int) ($this->card?->stamps_required ?? 8);
        $this->recompensa = (string) ($this->card?->reward ?? 'Bebida gratis');
        $this->statusMessage = session('tarjeta_status');
    }

    public function updatedLocationId(): void
    {
        if ($this->locationId !== null && !$this->team->locations()->whereKey($this->locationId)->exists()) {
            $this->locationId = null;
        }
    }

    public function getSelectedLocationProperty()
    {
        return $this->locationId ? $this->team->locations()->find($this->locationId) : null;
    }

    public function updatedPhoto(): void
    {
        $this->validate(['photo' => 'image|max:1024']);

        $this->logoUrl = Storage::url($this->photo->store('photos', 'public'));
    }

    public function quitarLogo(): void
    {
        $this->logoUrl = null;
        $this->photo = null;
    }

    public function cambiarPersonalizado(bool $personalizado): void
    {
        $this->isPersonalizado = $personalizado;

        if (!$personalizado) {
            $this->sincronizarColoresDesdePaleta();
        }
    }

    public function cambiarPaleta(string $slug): void
    {
        $this->paletaSeleccionada = $slug;
        $this->sincronizarColoresDesdePaleta();
    }

    private function sincronizarColoresDesdePaleta(): void
    {
        $paleta = $this->paletaActual();

        $this->colorPrincipal = $paleta['colores'][0] ?? $this->colorPrincipal;
        $this->colorSecundario = $paleta['colores'][1] ?? $this->colorSecundario;
        $this->colorTexto = $paleta['texto'] ?? $this->colorTexto;
    }

    private function paletaActual(): array
    {
        return collect($this->paletas)->firstWhere('slug', $this->paletaSeleccionada) ??
            (collect($this->paletasExtras)->firstWhere('slug', $this->paletaSeleccionada) ?? [
                'slug' => $this->paletaSeleccionada,
                'nombre' => 'Personalizada',
                'colores' => [$this->colorPrincipal, $this->colorSecundario, $this->colorTexto],
                'texto' => $this->colorTexto,
                'fondo' => "linear-gradient(135deg, {$this->colorPrincipal} 0%, {$this->colorSecundario} 100%)",
            ]);
    }

    public function getPreviewPaletaProperty(): array
    {
        $base = $this->paletaActual();
        $principal = $this->isPersonalizado ? $this->colorPrincipal : $base['colores'][0] ?? '#7C2D12';
        $secundario = $this->isPersonalizado ? $this->colorSecundario : $base['colores'][1] ?? '#F59E0B';
        $texto = $this->isPersonalizado ? $this->colorTexto : $base['texto'] ?? '#F8FAFC';

        return [
            'slug' => $this->paletaSeleccionada,
            'tipo' => $this->isPersonalizado ? 'personalizado' : 'preseleccionado',
            'isPersonalizado' => $this->isPersonalizado,
            'colorPrincipal' => $principal,
            'colorSecundario' => $secundario,
            'colorTexto' => $texto,
            'texto' => $texto,
            'fondo' => $this->isPersonalizado ? "linear-gradient(135deg, {$principal} 0%, {$secundario} 100%)" : $base['fondo'] ?? "linear-gradient(135deg, {$principal} 0%, {$secundario} 100%)",
            'nombre' => $base['nombre'] ?? 'Personalizada',
        ];
    }

    public function sumarSello(): void
    {
        $this->sellos = min(12, $this->sellos + 1);
    }

    public function restarSello(): void
    {
        $this->sellos = max(4, $this->sellos - 1);
    }

    public function seleccionarSellos(int $cantidad): void
    {
        $this->sellos = min(12, max(4, $cantidad));
    }

    public function elegirModo(string $modo): void
    {
        if (!in_array($modo, ['none', 'normal', 'custom'], true)) {
            return;
        }

        $this->modoRegistro = $modo;

        if ($modo === 'none') {
            $this->campos = [];
        }
    }

    public function alternarCampo(string $campo): void
    {
        if (in_array($campo, $this->campos, true)) {
            $this->campos = array_values(array_diff($this->campos, [$campo]));
        } else {
            $this->campos[] = $campo;
        }
    }

    public function agregarCampoPersonalizado(): void
    {
        $campo = trim($this->nuevoCampo);

        if ($campo !== '' && !in_array($campo, $this->campos, true)) {
            $this->campos[] = $campo;
        }

        $this->nuevoCampo = '';
    }

    public function guardar(): void
    {
        $this->statusMessage = null;

        $this->validate([
            'name' => ['required', 'string', 'min:3', 'max:60'],
            'sellos' => ['required', 'integer', 'min:4', 'max:12'],
            'recompensa' => ['required', 'string', 'min:3', 'max:120'],
            'modoRegistro' => ['required', 'in:none,normal,custom'],
        ]);

        if ($this->modoRegistro === 'custom' && $this->campos === []) {
            $this->addError('campos', 'Selecciona al menos un campo para el registro personalizado.');

            return;
        }

        $slugAnterior = $this->team->slug;

        DB::transaction(function () {
            $this->team->update([
                'name' => $this->name,
                'logo' => $this->logoUrl,
                'customer_registration_type' => $this->modoRegistro,
            ]);

            $this->card = CardDesign::updateOrCreate(
                ['team_id' => $this->team->id],
                [
                    'is_active' => true,
                    'color_scheme' => $this->previewPaleta,
                    'stamps_required' => $this->sellos,
                    'reward' => $this->recompensa,
                ],
            );

            $this->syncRegistrationFields();
        });

        $this->team->refresh();

        if ($this->team->slug !== $slugAnterior) {
            session()->flash('tarjeta_status', 'Tarjeta actualizada correctamente.');
            $this->redirect(route('dashboard.tarjeta', ['current_team' => $this->team->slug]), navigate: true);

            return;
        }

        $this->statusMessage = 'Tarjeta actualizada correctamente.';
    }

    private function syncRegistrationFields(): void
    {
        $configured = match ($this->modoRegistro) {
            'custom' => $this->campos,
            'normal' => array_intersect($this->campos, ['telefono']),
            default => [],
        };
        $configured = array_values(array_unique(array_filter($configured, 'is_string')));

        $this->team->customerRegistrationFields()->update(['is_active' => false]);

        $knownFields = [
            'nombre' => ['Nombre', 'text'],
            'email' => ['Email', 'email'],
            'telefono' => ['Teléfono', 'tel'],
            'cumpleanos' => ['Fecha de cumpleaños', 'date'],
        ];

        foreach ($configured as $position => $campo) {
            $fieldKey = Str::slug($campo, '_');

            if ($fieldKey === '' || $fieldKey === 'password') {
                continue;
            }

            [$label, $type] = $knownFields[$fieldKey] ?? [$campo, 'text'];

            CustomerRegistrationField::updateOrCreate(
                ['team_id' => $this->team->id, 'field_key' => $fieldKey],
                [
                    'label' => $label,
                    'type' => $type,
                    'is_required' => $this->modoRegistro === 'custom',
                    'sort_order' => $position,
                    'is_active' => true,
                ],
            );
        }
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-2 md:p-5">

    <header class="flex flex-col gap-3 border-b border-border pb-6 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-brand">Tarjeta de fidelidad</p>
            <h1 class="mt-1 text-3xl font-semibold tracking-tight text-ink">Edita tu tarjeta</h1>
            <p class="mt-2 max-w-xl text-sm leading-6 text-muted-foreground">Los mismos datos del asistente
                inicial: nombre, logo, colores, sellos, recompensa y registro de clientes.</p>
        </div>
        <p class="text-sm text-text-secondary/70">Estado: <span
                class="font-semibold {{ $card ? 'text-fidentta-teal' : 'text-text-secondary/60' }}">{{ $card ? 'Publicada' : 'Borrador' }}</span>
        </p>
    </header>

    @if ($statusMessage)
        <p role="status" class="rounded-lg border border-green-700/20 bg-green-700/5 px-4 py-3 text-sm text-green-800">
            {{ $statusMessage }}</p>
    @endif

    <section class="grid gap-6 lg:grid-cols-5">
        <div class="space-y-5 lg:col-span-3">
            <article class="rounded-xl border border-border bg-card p-5 sm:p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand">Paso 1 · Negocio</p>
                <h2 class="mt-2 text-lg font-semibold text-ink">Nombre y logo</h2>
                <div class="mt-5 space-y-2">
                    <label for="card-name" class="block text-sm font-semibold text-ink">Nombre del negocio</label>
                    <input id="card-name" wire:model.live="name" type="text" maxlength="60"
                        class="w-full rounded-lg border border-border bg-background px-4 py-3 text-ink outline-none transition placeholder:text-muted-foreground/70 focus:border-brand focus:ring-2 focus:ring-brand/20">
                    @error('name')
                        <span class="mt-1 block text-sm text-red-700">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mt-5">
                    <p class="text-sm font-semibold text-ink">Logo (opcional)</p>
                    <div class="mt-3 flex items-center gap-4">
                        @if ($logoUrl)
                            <img src="{{ $logoUrl }}" alt="Logo actual"
                                class="h-12 w-12 rounded-full border border-border object-cover">
                            <button type="button" wire:click="quitarLogo"
                                class="rounded-lg border border-border px-3 py-2 text-xs font-semibold text-muted-foreground transition hover:border-red-400 hover:text-red-600">Quitar
                                logo</button>
                        @endif
                        <input wire:model.live="photo" type="file" accept="image/*"
                            class="block w-full text-sm text-muted-foreground file:mr-4 file:rounded-lg file:border-0 file:bg-brand file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-brand-700">
                    </div>
                    @error('photo')
                        <span class="mt-1 block text-sm text-red-700">{{ $message }}</span>
                    @enderror
                </div>
            </article>

            <article class="rounded-xl border border-border bg-card p-5 sm:p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand">Paso 2 · Identidad visual</p>
                <h2 class="mt-2 text-lg font-semibold text-ink">Colores de la tarjeta</h2>
                <div class="mt-4 flex gap-2">
                    <button type="button" wire:click="cambiarPersonalizado(false)"
                        class="rounded-full px-4 py-2 text-sm font-semibold transition {{ !$isPersonalizado ? 'bg-brand text-white' : 'border border-border text-muted-foreground hover:text-ink' }}">Preseleccionado</button>
                    <button type="button" wire:click="cambiarPersonalizado(true)"
                        class="rounded-full px-4 py-2 text-sm font-semibold transition {{ $isPersonalizado ? 'bg-brand text-white' : 'border border-border text-muted-foreground hover:text-ink' }}">Personalizado</button>
                </div>
                @if ($isPersonalizado)
                    <div class="mt-4 grid gap-4 sm:grid-cols-3">
                        <label class="block rounded-lg border border-border bg-background p-3">
                            <span
                                class="mb-2 block text-xs font-medium uppercase tracking-[0.18em] text-text-secondary/70">Principal</span>
                            <input type="color" wire:model.live="colorPrincipal"
                                class="h-12 w-full cursor-pointer rounded-lg border-0 bg-transparent p-0">
                        </label>
                        <label class="block rounded-lg border border-border bg-background p-3">
                            <span
                                class="mb-2 block text-xs font-medium uppercase tracking-[0.18em] text-text-secondary/70">Secundario</span>
                            <input type="color" wire:model.live="colorSecundario"
                                class="h-12 w-full cursor-pointer rounded-lg border-0 bg-transparent p-0">
                        </label>
                        <label class="block rounded-lg border border-border bg-background p-3">
                            <span
                                class="mb-2 block text-xs font-medium uppercase tracking-[0.18em] text-text-secondary/70">Texto</span>
                            <input type="color" wire:model.live="colorTexto"
                                class="h-12 w-full cursor-pointer rounded-lg border-0 bg-transparent p-0">
                        </label>
                    </div>
                @else
                    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach (array_merge($paletas, $paletasExtras) as $paleta)
                            <button type="button" wire:click="cambiarPaleta('{{ $paleta['slug'] }}')"
                                class="relative flex h-16 flex-col justify-between rounded-lg p-2.5 text-left text-xs font-semibold shadow-sm transition hover:-translate-y-0.5 {{ $paletaSeleccionada === $paleta['slug'] ? 'ring-2 ring-brand ring-offset-2' : '' }}"
                                style="background: {{ $paleta['fondo'] }}; color: {{ $paleta['texto'] }};">
                                <span class="flex gap-1">
                                    @foreach ($paleta['colores'] as $color)
                                        <span class="h-2 w-4 rounded-full"
                                            style="background-color: {{ $color }};"></span>
                                    @endforeach
                                </span>
                                {{ $paleta['nombre'] }}
                            </button>
                        @endforeach
                    </div>
                @endif
            </article>

            <article class="rounded-xl border border-border bg-card p-5 sm:p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand">Paso 3 · Sellos y recompensa
                </p>
                <h2 class="mt-2 text-lg font-semibold text-ink">Mecánica del programa</h2>
                <div class="mt-4 flex items-center gap-4">
                    <button type="button" wire:click="restarSello" aria-label="Restar sello"
                        class="flex h-10 w-10 items-center justify-center rounded-lg border border-border bg-background text-xl text-ink transition hover:border-brand hover:text-brand">−</button>
                    <div class="min-w-16 text-center">
                        <span class="text-3xl font-semibold text-ink">{{ $sellos }}</span>
                        <span class="block text-xs uppercase tracking-[0.18em] text-muted-foreground">sellos</span>
                    </div>
                    <button type="button" wire:click="sumarSello" aria-label="Sumar sello"
                        class="flex h-10 w-10 items-center justify-center rounded-lg border border-border bg-background text-xl text-ink transition hover:border-brand hover:text-brand">+</button>
                    <div class="flex flex-wrap gap-2">
                        @foreach ([4, 6, 8, 10, 12] as $preset)
                            <button type="button" wire:click="seleccionarSellos({{ $preset }})"
                                class="rounded-lg border px-3 py-2 text-xs font-semibold transition {{ $sellos === $preset ? 'border-brand bg-brand-soft text-brand' : 'border-border text-muted-foreground hover:border-brand/40' }}">{{ $preset }}</button>
                        @endforeach
                    </div>
                </div>
                @error('sellos')
                    <span class="mt-1 block text-sm text-red-700">{{ $message }}</span>
                @enderror
                <div class="mt-5">
                    <label for="card-reward" class="block text-sm font-semibold text-ink">Recompensa</label>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($ideasRecompensa as $idea)
                            <button type="button" wire:click="$set('recompensa', '{{ $idea }}')"
                                class="rounded-full border px-3 py-1.5 text-xs font-medium transition {{ $recompensa === $idea ? 'border-brand bg-brand-soft text-brand' : 'border-border text-muted-foreground hover:border-brand/40 hover:text-ink' }}">{{ $idea }}</button>
                        @endforeach
                    </div>
                    <input id="card-reward" wire:model.live="recompensa" type="text" maxlength="120"
                        placeholder="Ej. Un café gratis"
                        class="mt-3 w-full rounded-lg border border-border bg-background px-4 py-3 text-ink outline-none transition placeholder:text-muted-foreground/70 focus:border-brand focus:ring-2 focus:ring-brand/20">
                    @error('recompensa')
                        <span class="mt-1 block text-sm text-red-700">{{ $message }}</span>
                    @enderror
                </div>
            </article>

            <article class="rounded-xl border border-border bg-card p-5 sm:p-6">
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand">Paso 4 · Registro de clientes
                </p>
                <h2 class="mt-2 text-lg font-semibold text-ink">Qué datos pide el formulario</h2>
                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                    <button type="button" wire:click="elegirModo('none')"
                        class="rounded-lg border p-4 text-left transition {{ $modoRegistro === 'none' ? 'border-brand bg-brand-soft' : 'border-border hover:border-brand/40' }}">
                        <span class="text-sm font-semibold text-ink">Sin registro</span>
                        <span class="mt-1 block text-xs text-muted-foreground">Tarjeta al instante, sin datos.</span>
                    </button>
                    <button type="button" wire:click="elegirModo('normal')"
                        class="rounded-lg border p-4 text-left transition {{ $modoRegistro === 'normal' ? 'border-brand bg-brand-soft' : 'border-border hover:border-brand/40' }}">
                        <span class="text-sm font-semibold text-ink">Básico</span>
                        <span class="mt-1 block text-xs text-muted-foreground">Email y contraseña.</span>
                    </button>
                    <button type="button" wire:click="elegirModo('custom')"
                        class="rounded-lg border p-4 text-left transition {{ $modoRegistro === 'custom' ? 'border-brand bg-brand-soft' : 'border-border hover:border-brand/40' }}">
                        <span class="text-sm font-semibold text-ink">Personalizado</span>
                        <span class="mt-1 block text-xs text-muted-foreground">Eliges los campos.</span>
                    </button>
                </div>
                @if ($modoRegistro === 'normal')
                    <div
                        class="mt-4 flex items-center justify-between rounded-lg border border-border bg-background px-4 py-3">
                        <span class="text-sm text-ink">Teléfono (opcional)</span>
                        <button type="button" wire:click="alternarCampo('telefono')"
                            class="rounded-full px-3 py-1.5 text-xs font-semibold transition {{ in_array('telefono', $campos, true) ? 'bg-brand text-white' : 'border border-border text-muted-foreground' }}">
                            {{ in_array('telefono', $campos, true) ? 'Activo' : 'Inactivo' }}</button>
                    </div>
                @elseif ($modoRegistro === 'custom')
                    <div class="mt-4 grid gap-2 sm:grid-cols-2">
                        @foreach ($opcionesCampos as $campo => $etiqueta)
                            <button type="button" wire:click="alternarCampo('{{ $campo }}')"
                                class="flex items-center justify-between rounded-lg border px-4 py-3 text-left text-sm transition {{ in_array($campo, $campos, true) ? 'border-brand bg-brand-soft text-ink' : 'border-border text-muted-foreground hover:border-brand/40' }}">
                                {{ $etiqueta }}
                                <span>{{ in_array($campo, $campos, true) ? '✓' : '' }}</span>
                            </button>
                        @endforeach
                    </div>
                    <div class="mt-3 flex flex-col gap-3 sm:flex-row">
                        <input wire:model="nuevoCampo" wire:keydown.enter="agregarCampoPersonalizado" type="text"
                            placeholder="Añade otro campo, por ejemplo: ciudad"
                            class="min-w-0 flex-1 rounded-lg border border-border bg-background px-4 py-3 text-sm text-ink outline-none placeholder:text-muted-foreground/70 focus:border-brand focus:ring-2 focus:ring-brand/20">
                        <button type="button" wire:click="agregarCampoPersonalizado"
                            class="rounded-lg bg-brand px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-700">Añadir</button>
                    </div>
                    @error('campos')
                        <span class="mt-1 block text-sm text-red-700">{{ $message }}</span>
                    @enderror
                @endif
            </article>

            <div class="flex items-center gap-4">
                <button type="button" wire:click="guardar" wire:loading.attr="disabled" wire:target="guardar,photo"
                    class="rounded-lg bg-brand px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700 disabled:opacity-60">Guardar
                    cambios</button>
                <a href="{{ route('dashboard.fidelizacion', ['current_team' => $team]) }}" wire:navigate
                    class="text-sm font-semibold text-brand hover:text-brand-700">Ver programa &rarr;</a>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="space-y-5 lg:sticky lg:top-6">
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-[0.16em] text-brand">Vista previa</p>
                    <div class="mx-auto w-full max-w-xs">
                        <div class="rounded-2xl border border-white/10 p-4 shadow-xl shadow-fidentta-purple/10"
                            style="background: {{ $this->previewPaleta['fondo'] }}; color: {{ $this->previewPaleta['texto'] }};">
                            <div class="flex items-center gap-3">
                                @if ($logoUrl)
                                    <img src="{{ $logoUrl }}" alt="Logo del negocio"
                                        class="h-8 w-8 rounded-full border border-white/20 bg-white/10 object-cover">
                                @else
                                    <div
                                        class="flex h-8 w-8 items-center justify-center rounded-full border border-white/20 bg-white/10 text-sm font-bold">
                                        {{ strtoupper(substr($name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="truncate text-sm font-semibold">{{ $name }}</span>
                            </div>
                            <div class="mt-4 flex flex-wrap gap-1.5">
                                @for ($i = 0; $i < $sellos; $i++)
                                    <div
                                        class="h-7 w-7 rounded-full border border-white/30 bg-white/10 shadow-inner shadow-white/20">
                                    </div>
                                @endfor
                            </div>
                            <p class="mt-4 text-xs opacity-85">Completa los {{ $sellos }} sellos y consigue:
                                {{ $recompensa }}</p>
                        </div>
                    </div>
                </div>

                <article class="rounded-xl border border-border bg-card p-5">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand">QR por local</p>
                    @if ($this->selectedLocation)
                        <div class="mt-3 flex items-center gap-3">
                            <label for="card-location" class="text-sm font-semibold text-ink">Local</label>
                            <select id="card-location" wire:model.live="locationId"
                                class="flex-1 rounded-lg border border-border bg-background px-3 py-2 text-sm text-ink">
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mt-4 flex items-center gap-4">
                            <img src="{{ route('location.qr', $this->selectedLocation) }}"
                                alt="QR de {{ $this->selectedLocation->name }}" width="112" height="112"
                                class="size-28 rounded-md bg-white p-2">
                            <a href="{{ route('locations.index', ['current_team' => $team, 'id' => $this->selectedLocation->id]) }}"
                                wire:navigate class="text-sm font-semibold text-brand hover:text-brand-700">Gestionar
                                local
                                &rarr;</a>
                        </div>
                    @else
                        <p class="mt-3 text-sm text-muted-foreground">Crea un local para generar su QR.</p>
                        <a href="{{ route('dashboard.locales', ['current_team' => $team]) }}" wire:navigate
                            class="mt-3 inline-flex rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white">Crear
                            local</a>
                    @endif
                </article>
            </div>
        </div>
    </section>
</div>
