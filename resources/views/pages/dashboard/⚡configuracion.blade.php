<?php

use App\Enums\TeamPermission;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component {
    public $team;
    public $design;

    public string $shopDomain = '';
    public string $webhookSecret = '';
    public int $stampsPerOrder = 1;
    public string $minOrderAmount = '';
    public bool $shopifyActive = true;
    public ?string $shopifyMessage = null;

    public function mount(): void
    {
        $this->team = auth()->user()->currentTeam;
        abort_unless($this->team !== null, 404);
        $this->design = $this->team->cardDesign()->where('is_active', true)->first();
        $this->loadShopify();
    }

    private function loadShopify(): void
    {
        $integration = $this->team->shopifyIntegration;

        $this->shopDomain = $integration?->shop_domain ?? '';
        $this->stampsPerOrder = $integration?->stamps_per_order ?? 1;
        $this->minOrderAmount = $integration?->min_order_amount ?? '';
        $this->shopifyActive = $integration?->is_active ?? true;
        $this->webhookSecret = '';
    }

    public function saveShopify(): void
    {
        abort_unless(auth()->user()->hasTeamPermission($this->team, TeamPermission::UpdateTeam), 403);

        $this->shopDomain = strtolower(trim((string) preg_replace('#^https?://#i', '', trim($this->shopDomain)), '/ '));
        $existing = $this->team->shopifyIntegration;

        if ($existing?->usesOAuth()) {
            $this->shopDomain = $existing->shop_domain;
        }

        $this->validate([
            'shopDomain' => ['required', 'regex:/^[a-z0-9][a-z0-9\-]*\.myshopify\.com$/'],
            'webhookSecret' => [$existing ? 'nullable' : 'required', 'string', 'min:8', 'max:255'],
            'stampsPerOrder' => ['required', 'integer', 'min:1', 'max:20'],
            'minOrderAmount' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
        ], [
            'shopDomain.regex' => 'Usa el dominio de tu tienda, por ejemplo mitienda.myshopify.com.',
            'webhookSecret.required' => 'Pega la clave secreta de los webhooks de Shopify.',
        ]);

        $data = [
            'shop_domain' => $this->shopDomain,
            'stamps_per_order' => $this->stampsPerOrder,
            'min_order_amount' => filled($this->minOrderAmount) ? $this->minOrderAmount : null,
            'is_active' => $this->shopifyActive,
        ];

            if (filled($this->webhookSecret) && ! $existing?->usesOAuth()) {
            $data['webhook_secret'] = $this->webhookSecret;
        }

        if ($existing) {
            $existing->update($data);
        } else {
            $this->team->shopifyIntegration()->create($data + ['webhook_token' => Str::random(48)]);
        }

        $this->team->unsetRelation('shopifyIntegration');
        $this->loadShopify();
        $this->shopifyMessage = 'Integración de Shopify guardada.';
    }

    public function disconnectShopify(): void
    {
        abort_unless(auth()->user()->hasTeamPermission($this->team, TeamPermission::UpdateTeam), 403);

        $this->team->shopifyIntegration?->delete();
        $this->team->unsetRelation('shopifyIntegration');
        $this->loadShopify();
        $this->shopifyMessage = 'Shopify desconectado.';
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-2 md:p-5">
    <header class="flex flex-col gap-3 border-b border-border pb-6">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-brand">Configuración del negocio</p>
        <h1 class="text-3xl font-semibold tracking-tight text-ink">Perfil y programa</h1>
        <p class="max-w-2xl text-sm leading-6 text-muted-foreground">Consulta los datos actuales y continúa la
            configuración desde la sección correspondiente.</p>
    </header>

    <section class="grid gap-5 lg:grid-cols-2">
        <article class="rounded-xl border border-border bg-card p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand">Perfil del equipo</p>
                    <h2 class="mt-2 text-lg font-semibold text-ink">{{ $team->name }}</h2>
                </div>
                <span
                    class="rounded-lg bg-brand-soft px-3 py-2 text-xs font-semibold text-brand">{{ $team->is_active ? 'Activo' : 'Inactivo' }}</span>
            </div>
            <dl class="mt-5 divide-y divide-border">
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-sm text-muted-foreground">Cuenta propietaria</dt>
                    <dd class="truncate text-sm font-medium text-ink">{{ auth()->user()->email }}</dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-sm text-muted-foreground">Locales activos</dt>
                    <dd class="text-sm font-medium text-ink">{{ $team->locations()->where('is_active', true)->count() }}
                    </dd>
                </div>
                <div class="flex items-center justify-between gap-4 py-3">
                    <dt class="text-sm text-muted-foreground">Registro de clientes</dt>
                    <dd class="text-sm font-medium text-ink">
                        {{ match ($team->customer_registration_type) {'none' => 'Sin datos','normal' => 'Email y contraseña','custom' => 'Personalizado',default => 'No configurado'} }}
                    </dd>
                </div>
            </dl>
            <a href="{{ route('profile.edit') }}" wire:navigate
                class="mt-4 inline-flex text-sm font-semibold text-brand hover:text-brand-700">Cuenta y seguridad <span
                    class="ml-1" aria-hidden="true">&rarr;</span></a>
        </article>

        <article class="rounded-xl border border-border bg-card p-5 sm:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand">Tarjeta de fidelidad</p>
                    <h2 class="mt-2 text-lg font-semibold text-ink">
                        {{ $design ? 'Programa configurado' : 'Programa pendiente' }}</h2>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-accent/20 text-accent"
                    aria-hidden="true">&#9733;</span>
            </div>
            @if ($design)
                <dl class="mt-5 divide-y divide-border">
                    <div class="flex items-center justify-between gap-4 py-3">
                        <dt class="text-sm text-muted-foreground">Sellos para completar</dt>
                        <dd class="text-sm font-semibold text-ink">{{ $design->stamps_required }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-3">
                        <dt class="text-sm text-muted-foreground">Recompensa</dt>
                        <dd class="max-w-[60%] text-right text-sm font-semibold text-ink">{{ $design->reward }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-4 py-3">
                        <dt class="text-sm text-muted-foreground">Estado</dt>
                        <dd
                            class="text-sm font-semibold {{ $design->is_active ? 'text-green-700' : 'text-muted-foreground' }}">
                            {{ $design->is_active ? 'Publicado' : 'Pausado' }}</dd>
                    </div>
                </dl>
            @else
                <p class="mt-4 text-sm leading-6 text-muted-foreground">Completa la configuración de sellos y recompensa
                    para publicar tu programa.</p>
            @endif
            <a href="{{ route('dashboard.fidelizacion', ['current_team' => $team]) }}" wire:navigate
                class="mt-4 inline-flex rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700">Gestionar
                fidelización</a>
        </article>
    </section>

    <section class="rounded-xl border border-border bg-card p-5 sm:p-6" aria-labelledby="shopify-title">
        @php($integration = $team->shopifyIntegration)
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-brand">Integraciones</p>
                <h2 id="shopify-title" class="mt-2 text-lg font-semibold text-ink">Shopify</h2>
                <p class="mt-1 max-w-2xl text-sm leading-6 text-muted-foreground">Suma sellos automáticamente cuando un
                    cliente registrado paga un pedido en tu tienda. Se identifica por el email del pedido.</p>
            </div>
            <span
                class="rounded-lg px-3 py-2 text-xs font-semibold {{ $integration?->is_active ? 'bg-brand-soft text-brand' : 'bg-background text-muted-foreground' }}">
                {{ $integration ? ($integration->is_active ? 'Conectado' : 'Pausado') : 'No conectado' }}</span>
        </div>

        @if ($team->customer_registration_type === 'none')
            <p class="mt-4 rounded-lg border border-amber-300 bg-amber-50 p-3 text-sm text-amber-900">
                Tu tarjeta está en modo «Sin registro»: sin email de cliente no se pueden asignar sellos por pedido.
                Cambia a «Básico» o «Personalizado» en la página Tarjeta.
            </p>
        @endif

        @if ($shopifyMessage)
            <p role="status" class="mt-4 rounded-lg border border-green-300 bg-green-50 p-3 text-sm text-green-800">
                {{ $shopifyMessage }}</p>
        @endif
        @if (session('shopify_status'))
            <p role="status" class="mt-4 rounded-lg border border-green-300 bg-green-50 p-3 text-sm text-green-800">
                {{ session('shopify_status') }}</p>
        @endif
        @if (session('shopify_error'))
            <p role="alert" class="mt-4 rounded-lg border border-red-300 bg-red-50 p-3 text-sm text-red-800">
                {{ session('shopify_error') }}</p>
        @endif

        @php($oauth = (bool) $integration?->usesOAuth())

        @if (!$integration)
            <form method="GET" action="{{ route('shopify.connect') }}" class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <label for="shopify-connect-shop" class="text-sm font-medium text-ink">Tu tienda de Shopify</label>
                    <input id="shopify-connect-shop" name="shop" type="text" required placeholder="mitienda.myshopify.com"
                        class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-ink">
                </div>
                <button type="submit"
                    class="rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700">
                    Conectar con Shopify</button>
            </form>
            <p class="mt-2 text-xs text-muted-foreground">Te llevaremos a Shopify para aprobar el acceso a los pedidos.
                No tienes que copiar ninguna clave ni URL.</p>
        @endif

        @if ($integration && !$oauth)
            <div class="mt-4 rounded-lg bg-background p-4">
                <p class="text-xs font-semibold uppercase text-muted-foreground">URL del webhook</p>
                <p class="mt-1 break-all font-mono text-xs text-ink">{{ route('webhooks.shopify', $integration->webhook_token) }}</p>
                <p class="mt-2 text-xs leading-5 text-muted-foreground">En Shopify: Ajustes → Notificaciones →
                    Webhooks → Crear webhook. Evento «Pago del pedido» (orders/paid), formato JSON y esta URL. Copia
                    la «clave secreta» que aparece al pie de esa pantalla y pégala abajo.</p>
            </div>
        @endif

        @if (!$integration)
            <details class="mt-5">
                <summary class="cursor-pointer text-sm font-medium text-muted-foreground">Configuración manual (avanzado)
                </summary>
        @endif
        <form wire:submit="saveShopify" class="mt-5 grid gap-4 sm:grid-cols-2">
            <div>
                <label for="shop-domain" class="text-sm font-medium text-ink">Dominio de la tienda</label>
                <input id="shop-domain" type="text" wire:model="shopDomain" placeholder="mitienda.myshopify.com"
                    @readonly($oauth)
                    class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-ink">
                @error('shopDomain')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            @if (!$oauth)
                <div>
                    <label for="shop-secret" class="text-sm font-medium text-ink">Clave secreta del webhook</label>
                    <input id="shop-secret" type="password" wire:model="webhookSecret" autocomplete="off"
                        placeholder="{{ $integration ? 'Dejar vacío para mantener la actual' : 'Pega la clave de Shopify' }}"
                        class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-ink">
                    @error('webhookSecret')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            @endif
            <div>
                <label for="shop-stamps" class="text-sm font-medium text-ink">Sellos por pedido</label>
                <input id="shop-stamps" type="number" min="1" max="20" wire:model="stampsPerOrder"
                    class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-ink">
                @error('stampsPerOrder')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="shop-min" class="text-sm font-medium text-ink">Importe mínimo del pedido (opcional)</label>
                <input id="shop-min" type="number" min="0" step="0.01" wire:model="minOrderAmount"
                    class="mt-1 w-full rounded-lg border border-border bg-background px-3 py-2 text-sm text-ink">
                @error('minOrderAmount')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <label class="flex items-center gap-2 text-sm text-ink sm:col-span-2">
                <input type="checkbox" wire:model="shopifyActive" class="rounded border-border">
                Integración activa
            </label>
            <div class="flex flex-wrap gap-3 sm:col-span-2">
                <button type="submit"
                    class="rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700">
                    {{ $integration ? 'Guardar cambios' : 'Conectar Shopify' }}</button>
                @if ($integration)
                    <button type="button" wire:click="disconnectShopify"
                        wire:confirm="¿Desconectar Shopify? Los pedidos nuevos dejarán de sumar sellos."
                        class="rounded-lg border border-border px-4 py-2.5 text-sm font-medium text-ink hover:border-brand/40">
                        Desconectar</button>
                @endif
            </div>
        </form>
        @if (!$integration)
            </details>
        @endif
        <p class="mt-4 text-xs text-muted-foreground">TPV: próximamente.</p>
    </section>

    <section class="grid gap-4 sm:grid-cols-2">
        <a href="{{ route('dashboard.locales', ['current_team' => $team]) }}" wire:navigate
            class="flex items-center justify-between gap-4 rounded-xl border border-border bg-card p-5 transition hover:border-brand/40">
            <div>
                <p class="text-sm font-semibold text-ink">Gestionar locales</p>
                <p class="mt-1 text-sm text-muted-foreground">Direcciones, QR y actividad por local.</p>
            </div><span class="text-xl text-brand" aria-hidden="true">&rarr;</span>
        </a>
        <a href="{{ route('dashboard.clientes', ['current_team' => $team]) }}" wire:navigate
            class="flex items-center justify-between gap-4 rounded-xl border border-border bg-card p-5 transition hover:border-brand/40">
            <div>
                <p class="text-sm font-semibold text-ink">Ver clientes</p>
                <p class="mt-1 text-sm text-muted-foreground">Filtra por local y revisa el progreso.</p>
            </div><span class="text-xl text-brand" aria-hidden="true">&rarr;</span>
        </a>
    </section>
</div>
