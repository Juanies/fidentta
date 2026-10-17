<?php

use Livewire\Component;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public $location;
    public $recentTransactions;
    public bool $editing = false;
    public string $name = '';
    public string $address = '';
    public string $pageLinkName = '';
    public array $links = [];

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
        $this->pageLinkName = $this->location->page_link_name ?: $this->location->name;
        $this->links = $this->location->businessPageLinks
            ->map(
                fn($link) => [
                    'name' => $link->name,
                    'url' => $link->url,
                    'icon' => $link->icon ?: 'web',
                ],
            )
            ->all();
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

    public function addPageLink(): void
    {
        $this->links[] = ['name' => '', 'url' => '', 'icon' => 'web'];
    }

    public function removePageLink(int $index): void
    {
        unset($this->links[$index]);
        $this->links = array_values($this->links);
    }

    public function guardarPageLink(): void
    {
        $validated = $this->validate([
            'pageLinkName' => ['required', 'string', 'min:2', 'max:120'],
            'links' => ['array', 'max:12'],
            'links.*.name' => ['required', 'string', 'min:1', 'max:80'],
            'links.*.url' => ['required', 'string', 'max:2048', 'url:https'],
            'links.*.icon' => ['required', 'in:instagram,facebook,tiktok,whatsapp,web,map'],
        ]);

        DB::transaction(function () use ($validated) {
            $this->location->update(['page_link_name' => $validated['pageLinkName']]);
            $this->location->businessPageLinks()->delete();
            $this->location->businessPageLinks()->createMany(array_map(fn(array $link) => [...$link, 'team_id' => $this->location->team_id], $validated['links'] ?? []));
        });

        $this->location->refresh();
    }
};
?>

<div class="flex h-full w-full flex-1 flex-col gap-px p-4 md:p-6" style="background: var(--border);">
    <header class="flex flex-col gap-4 bg-background px-4 py-6 sm:flex-row sm:items-center sm:justify-between md:px-6">
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
                    <button type="submit" wire:loading.attr="disabled" wire:target="guardar"
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
                <button type="button" wire:click="alternarEstado" wire:loading.attr="disabled" wire:target="alternarEstado"
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
                <button type="submit" wire:loading.attr="disabled" wire:target="guardar"
                    class="w-full rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700 sm:w-auto">Guardar
                    dirección</button>
            </div>
        </form>
    @endif

    <section class="grid gap-6 bg-card p-5 md:grid-cols-[minmax(220px,0.8fr)_minmax(0,1.2fr)] md:p-6">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-fidentta-teal">Página del QR</p>
            <h2 class="mt-2 text-2xl tracking-tight text-ink">Enlaces del local</h2>
            <p class="mt-2 text-sm leading-6 text-ink2">Personaliza la página que se abre al escanear el código.</p>
            <a href="{{ route('wallet.links', ['qr_token' => $location->qr_token]) }}" target="_blank" rel="noopener"
                class="mt-4 inline-flex text-sm font-semibold text-fidentta-teal underline decoration-2 underline-offset-4">Vista
                pública
                <span class="ml-2" aria-hidden="true">↗</span></a>
        </div>
        <form wire:submit="guardarPageLink" class="space-y-5">
            <div>
                <label for="page-link-name"
                    class="mb-1.5 block text-xs font-semibold uppercase tracking-[0.14em] text-ink2">Nombre de la
                    página</label>
                <input id="page-link-name" wire:model="pageLinkName" required maxlength="120"
                    class="w-full border-b-2 border-ink bg-transparent px-1 py-2 text-sm font-medium text-ink focus:border-fidentta-teal focus:outline-none">
                @error('pageLinkName')
                    <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                @enderror
            </div>
            <div class="space-y-3">
                @foreach ($links as $index => $link)
                    <div wire:key="page-link-{{ $index }}"
                        class="grid gap-3 border-t border-border pt-3 sm:grid-cols-[1fr_1.4fr_150px_auto]">
                        <div>
                            <label class="sr-only" for="page-link-label-{{ $index }}">Nombre del enlace</label>
                            <input id="page-link-label-{{ $index }}"
                                wire:model="links.{{ $index }}.name" placeholder="Instagram" maxlength="80"
                                class="w-full border-b border-border bg-transparent px-1 py-2 text-sm text-ink focus:border-fidentta-teal focus:outline-none">
                            @error("links.{$index}.name")
                                <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="sr-only" for="page-link-url-{{ $index }}">URL segura del
                                enlace</label>
                            <input id="page-link-url-{{ $index }}" wire:model="links.{{ $index }}.url"
                                type="url" placeholder="https://..." maxlength="2048"
                                class="w-full border-b border-border bg-transparent px-1 py-2 text-sm text-ink focus:border-fidentta-teal focus:outline-none">
                            @error("links.{$index}.url")
                                <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="sr-only" for="page-link-icon-{{ $index }}">Icono del enlace</label>
                            <select id="page-link-icon-{{ $index }}"
                                wire:model="links.{{ $index }}.icon"
                                class="w-full border-b border-border bg-background px-1 py-2 text-sm text-ink focus:border-fidentta-teal focus:outline-none">
                                <option value="web">Web ↗</option>
                                <option value="instagram">Instagram ◎</option>
                                <option value="facebook">Facebook f</option>
                                <option value="tiktok">TikTok ♪</option>
                                <option value="whatsapp">WhatsApp ☎</option>
                                <option value="map">Ubicación ⌖</option>
                            </select>
                            @error("links.{$index}.icon")
                                <p class="mt-1 text-xs text-destructive">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="button" wire:click="removePageLink({{ $index }})"
                            wire:loading.attr="disabled" wire:target="removePageLink"
                            aria-label="Eliminar enlace {{ $index + 1 }}"
                            class="px-2 text-sm font-semibold text-destructive hover:underline">Eliminar</button>
                    </div>
                @endforeach
            </div>
            @error('links')
                <p class="text-xs text-destructive">{{ $message }}</p>
            @enderror
            <div class="flex flex-wrap justify-between gap-3">
                <button type="button" wire:click="addPageLink" wire:loading.attr="disabled"
                    wire:target="addPageLink"
                    class="border border-border px-3 py-2 text-sm font-semibold text-ink hover:border-ink">+ Añadir
                    enlace</button>
                <button type="submit" wire:loading.attr="disabled" wire:target="guardarPageLink"
                    class="bg-ink px-5 py-2.5 text-sm font-bold text-paper hover:opacity-90">Guardar página</button>
            </div>
        </form>
    </section>

    <section class="grid gap-px sm:grid-cols-2 xl:grid-cols-4" style="background: var(--border);">
        <article class="bg-card p-5">
            <p class="text-sm text-muted-foreground">Clientes registrados</p>
            <p class="mt-2 text-3xl font-semibold text-ink">{{ number_format($location->customer_users_count) }}</p>
        </article>
        <article class="bg-card p-5">
            <p class="text-sm text-muted-foreground">Clientes este mes</p>
            <p class="mt-2 text-3xl font-semibold text-ink">{{ number_format($location->customers_this_month_count) }}
            </p>
        </article>
        <article class="bg-card p-5">
            <p class="text-sm text-muted-foreground">Sellos otorgados este mes</p>
            <p class="mt-2 text-3xl font-semibold text-ink">{{ number_format($location->stamps_this_month ?? 0) }}</p>
        </article>
        <article class="bg-card p-5">
            <p class="text-sm text-muted-foreground">Operaciones registradas</p>
            <p class="mt-2 text-3xl font-semibold text-ink">{{ number_format($location->card_transactions_count) }}
            </p>
        </article>
    </section>

    <section class="grid items-start gap-px xl:grid-cols-[minmax(0,1fr)_minmax(280px,360px)]"
        style="background: var(--border);">
        <div class="min-w-0 bg-card p-5 sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-semibold text-brand">QR DEL LOCAL</p>
                    <h2 class="mt-1 text-xl font-semibold text-ink">Comparte la tarjeta de fidelidad</h2>
                    <p class="mt-1 text-sm text-muted-foreground">El QR abre la página del local con su tarjeta y
                        enlaces.</p>
                </div>
                <a href="{{ route('wallet.links', ['qr_token' => $location->qr_token]) }}" target="_blank"
                    rel="noopener"
                    class="rounded-lg border border-border px-3 py-2 text-sm font-medium text-ink hover:border-brand/40">Abrir
                    enlace</a>
            </div>
            <div class="mt-5 flex flex-col items-center gap-4 bg-background p-5 sm:flex-row sm:items-center">
                <div id="qr-print" class="shrink-0 bg-white p-3">
                    <img src="{{ route('location.qr', $location) }}" alt="Código QR de {{ $location->name }}"
                        width="220" height="220" class="h-44 w-44 sm:h-52 sm:w-52">
                </div>
                <div class="min-w-0 text-center sm:text-left">
                    <p class="font-semibold text-ink">{{ $location->name }}</p>
                    <p class="mt-1 break-all text-xs text-muted-foreground">
                        {{ route('wallet.links', ['qr_token' => $location->qr_token]) }}</p>
                    <button type="button" onclick="imprimirQR()"
                        class="mt-4 rounded-lg bg-brand px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Imprimir
                        QR</button>
                </div>
            </div>
        </div>

        <aside class="bg-card p-5">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-ink">Actividad reciente</h2>
                <a href="{{ route('dashboard.clientes', ['current_team' => auth()->user()->currentTeam]) }}"
                    wire:navigate class="text-sm font-semibold text-brand hover:text-brand-700">Clientes</a>
            </div>
            @if ($recentTransactions->isEmpty())
                <p class="mt-4 text-sm leading-6 text-muted-foreground">Todavía no hay sellos registrados en este
                    local.
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

@script
<script>
    window.imprimirQR = function () {
        const qr = document.getElementById('qr-print').innerHTML;
        const ventana = window.open('', '_blank', 'width=400,height=400');

        if (!ventana) {
            alert('Permite las ventanas emergentes para imprimir.');
            return;
        }

        ventana.document.write(`<!DOCTYPE html>
<html>
<head>
    <title>Imprimir QR</title>
    <style>
        @page { margin: 0; }
        body { display: flex; justify-content: center; align-items: center; margin: 20px; }
        img, svg { width: 200px; height: 200px; }
    </style>
</head>
<body>${qr}</body>
</html>`);
        ventana.document.close();

        const imprimir = function () {
            ventana.focus();
            ventana.print();
            ventana.close();
        };
        const img = ventana.document.querySelector('img');
        if (img && !img.complete) {
            img.onload = imprimir;
        } else {
            imprimir();
        }
    };
</script>
@endscript

</div>
