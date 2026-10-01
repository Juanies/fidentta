<?php

use Livewire\Component;

new class extends Component {
    public $location;
    public $recentTransactions;
    public bool $editing = false;
    public string $name = '';
    public string $address = '';

    public function mount(int $id)
    {
        $team = auth()->user()->currentTeam;
        abort_unless($team !== null, 404);

        $this->location = $team
            ->locations()
            ->withCount(['customerUsers', 'customerUsers as customers_this_month_count' => fn($query) => $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]), 'cardTransactions'])
            ->withSum(
                [
                    'cardTransactions as stamps_this_month' => fn($query) => $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]),
                ],
                'stamps_added',
            )
            ->findOrFail($id);

        $this->name = $this->location->name;
        $this->address = $this->location->address ?? '';
        $this->recentTransactions = $this->location->cardTransactions()->with('customer')->latest()->limit(6)->get();
    }

    public function guardar(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
        ]);

        $this->location->update($validated);
        $this->editing = false;
    }

    public function alternarEstado(): void
    {
        $this->location->update(['is_active' => !$this->location->is_active]);
        $this->location->refresh();
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-2 md:p-5">
    <header class="flex flex-col gap-4 border-b border-border pb-5 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <a href="{{ route('dashboard.locales', ['current_team' => auth()->user()->currentTeam]) }}" wire:navigate
                class="text-sm font-medium text-brand hover:text-brand-700">&larr; Todos los locales</a>
            @if ($editing)
                <form wire:submit="guardar" class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-start">
                    <div>
                        <label for="location-name" class="sr-only">Nombre del local</label>
                        <input id="location-name" wire:model="name" required maxlength="120"
                            class="w-full rounded-lg border border-border bg-card px-3 py-2 text-xl font-semibold text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20">
                        @error('name')
                            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit"
                        class="rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white hover:bg-brand-700">Guardar</button>
                    <button type="button" wire:click="$set('editing', false)"
                        class="rounded-lg border border-border px-4 py-2 text-sm font-medium text-ink">Cancelar</button>
                </form>
            @else
                <div class="mt-2 flex flex-wrap items-center gap-3">
                    <h1 class="truncate text-3xl font-semibold text-ink">{{ $location->name }}</h1>
                    <span
                        class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $location->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                        {{ $location->is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{ $location->address ?: 'Añade una dirección para identificar este local.' }}</p>
            @endif
        </div>
        <div class="flex shrink-0 gap-2">
            @unless ($editing)
                <button type="button" wire:click="$set('editing', true)"
                    class="rounded-lg border border-border bg-card px-4 py-2.5 text-sm font-semibold text-ink hover:border-brand/40">Editar
                    local</button>
                <button type="button" wire:click="alternarEstado"
                    class="rounded-lg border border-border px-4 py-2.5 text-sm font-semibold {{ $location->is_active ? 'text-red-700 hover:bg-red-50' : 'text-green-800 hover:bg-green-50' }}">
                    {{ $location->is_active ? 'Desactivar' : 'Activar' }}
                </button>
            @endunless
        </div>
    </header>

    @if ($editing)
        <form wire:submit="guardar"
            class="grid gap-4 rounded-xl border border-border bg-card p-5 sm:grid-cols-[1fr_auto]">
            <div>
                <label for="location-address" class="mb-1.5 block text-sm font-medium text-ink">Dirección</label>
                <input id="location-address" wire:model="address" maxlength="255" placeholder="Calle y número"
                    class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20">
                @error('address')
                    <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-end">
                <button type="submit"
                    class="w-full rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 sm:w-auto">Guardar
                    dirección</button>
            </div>
        </form>
    @endif

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-xl border border-border bg-card p-5">
            <p class="text-sm text-muted-foreground">Clientes registrados</p>
            <p class="mt-2 text-3xl font-semibold text-ink">{{ number_format($location->customer_users_count) }}</p>
        </article>
        <article class="rounded-xl border border-border bg-card p-5">
            <p class="text-sm text-muted-foreground">Clientes este mes</p>
            <p class="mt-2 text-3xl font-semibold text-ink">{{ number_format($location->customers_this_month_count) }}
            </p>
        </article>
        <article class="rounded-xl border border-border bg-card p-5">
            <p class="text-sm text-muted-foreground">Sellos otorgados este mes</p>
            <p class="mt-2 text-3xl font-semibold text-ink">{{ number_format($location->stamps_this_month ?? 0) }}</p>
        </article>
        <article class="rounded-xl border border-border bg-card p-5">
            <p class="text-sm text-muted-foreground">Operaciones registradas</p>
            <p class="mt-2 text-3xl font-semibold text-ink">{{ number_format($location->card_transactions_count) }}</p>
        </article>
    </section>

    <section class="grid items-start gap-5 xl:grid-cols-[minmax(0,1fr)_minmax(280px,360px)]">
        <div class="min-w-0 rounded-xl border border-border bg-card p-5 sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-brand">QR DEL LOCAL</p>
                    <h2 class="mt-1 text-xl font-semibold text-ink">Comparte la tarjeta de fidelidad</h2>
                    <p class="mt-1 text-sm text-muted-foreground">Los clientes pueden escanear este código para abrir su
                        tarjeta.</p>
                </div>
                <a href="{{ route('wallet.card', ['qr_token' => $location->qr_token]) }}" target="_blank"
                    rel="noopener"
                    class="rounded-lg border border-border px-3 py-2 text-sm font-medium text-ink hover:border-brand/40">Abrir
                    enlace</a>
            </div>
            <div class="mt-5 flex flex-col items-center gap-4 rounded-lg bg-background p-5 sm:flex-row sm:items-center">
                <div class="shrink-0 rounded-lg bg-white p-3 shadow-sm">
                    <img src="{{ route('location.qr', $location) }}" alt="Código QR de {{ $location->name }}"
                        width="220" height="220" class="h-44 w-44 sm:h-52 sm:w-52">
                </div>
                <div class="min-w-0 text-center sm:text-left">
                    <p class="font-semibold text-ink">{{ $location->name }}</p>
                    <p class="mt-1 break-all text-xs text-muted-foreground">
                        {{ route('wallet.card', ['qr_token' => $location->qr_token]) }}</p>
                    <button type="button" onclick="window.print()"
                        class="mt-4 rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Imprimir
                        QR</button>
                </div>
            </div>
        </div>

        <aside class="rounded-xl border border-border bg-card p-5">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-ink">Actividad reciente</h2>
                <a href="{{ route('dashboard.clientes', ['current_team' => auth()->user()->currentTeam]) }}"
                    wire:navigate class="text-sm font-semibold text-brand hover:text-brand-700">Clientes</a>
            </div>
            @if ($recentTransactions->isEmpty())
                <p class="mt-4 text-sm leading-6 text-muted-foreground">Todavía no hay sellos registrados en este local.
                </p>
            @else
                <ul class="mt-4 divide-y divide-border">
                    @foreach ($recentTransactions as $transaction)
                        <li class="flex items-center justify-between gap-3 py-3 first:pt-0">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-ink">
                                    {{ $transaction->customer?->email ?? 'Cliente' }}</p>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    {{ $transaction->created_at?->diffForHumans() }}</p>
                            </div>
                            <span class="shrink-0 text-sm font-semibold text-brand">+{{ $transaction->stamps_added }}
                                sellos</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </aside>
    </section>
</div>
</div>

</div>
