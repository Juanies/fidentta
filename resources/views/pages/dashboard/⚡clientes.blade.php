<?php

use Livewire\Component;
use App\Models\CustomerUser;
use App\Models\Location;
use App\Services\AddStampsToCard;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

new class extends Component {
    public $team;
    public string $locationId = '';
    public string $search = '';
    public ?string $statusMessage = null;
    public int $rewardGoal = 8;

    public function mount()
    {
        $this->team = auth()->user()->currentTeam;
        abort_unless($this->team !== null, 404);
        $this->rewardGoal = max(1, (int) ($this->team->cardDesign?->stamps_required ?? 8));
    }

    public function updatedLocationId(): void
    {
        if ($this->locationId !== '' && !$this->team->locations()->whereKey($this->locationId)->exists()) {
            $this->locationId = '';
        }
    }

    public function addStamp(int $customerId, AddStampsToCard $addStamps): void
    {
        $this->resetErrorBag('stamp');
        $this->statusMessage = null;

        $customer = $this->customersQuery()->findOrFail($customerId);
        $locationId = $this->locationId ?: $customer->location_id;
        $location = Location::query()->where('team_id', $this->team->id)->where('is_active', true)->findOrFail($locationId);
        $card = $customer->cards()->where('is_active', true)->latest()->firstOrFail();

        try {
            $addStamps->add($card, auth()->user(), $location);
            $customerLabel = $customer->email ?: 'cliente ' . $customer->id;
            $this->statusMessage = 'Sello añadido a la tarjeta de ' . $customerLabel . ' en ' . $location->name . '.';
        } catch (ValidationException $exception) {
            $this->addError('stamp', $exception->validator->errors()->first('amount') ?: $exception->getMessage());
        }
    }

    public function getLocationsProperty()
    {
        return $this->team->locations()->where('is_active', true)->orderBy('name')->get();
    }

    private function customersQuery()
    {
        $query = CustomerUser::query()->where('team_id', $this->team->id);

        if ($this->locationId !== '') {
            $query->where('location_id', $this->locationId);
        }

        if (trim($this->search) !== '') {
            $term = '%' . trim($this->search) . '%';
            $query->where(function ($searchable) use ($term) {
                $searchable->where('email', 'like', $term)->orWhereHas('registrationValues', fn($values) => $values->where('value', 'like', $term));
            });
        }

        return $query;
    }

    private function customersInWalletQuery()
    {
        $latestGoogleSaves = DB::table('mobile_pass_google_events as wallet_events')
            ->select('wallet_events.mobile_pass_id')
            ->where('wallet_events.event_type', 'save')
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')->from('mobile_pass_google_events as newer_events')->whereColumn('newer_events.mobile_pass_id', 'wallet_events.mobile_pass_id')->whereColumn('newer_events.received_at', '>', 'wallet_events.received_at');
            });

        return $this->customersQuery()->where(function ($customers) use ($latestGoogleSaves) {
            $customers->whereHas('mobilePasses', fn($passes) => $passes->where('platform', 'apple')->whereHas('registrations'))->orWhereHas('mobilePasses', fn($passes) => $passes->where('platform', 'google')->whereIn('id', $latestGoogleSaves));
        });
    }

    private function walletStatus(CustomerUser $customer): string
    {
        $passes = $customer->mobilePasses;
        $installed = $passes->contains(fn($pass) => $pass->isApple() ? $pass->registrations->isNotEmpty() : $pass->googleEvents->sortByDesc('received_at')->first()?->event_type === 'save');

        if ($installed) {
            return 'En Wallet';
        }

        if ($passes->isEmpty()) {
            return 'Sin pase';
        }

        if ($passes->contains(fn($pass) => $pass->wallet_added_at !== null || $pass->wallet_removed_at !== null)) {
            return 'Desactivada';
        }

        return 'Pendiente';
    }

    public function getStatsProperty(): array
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        return [
            'total' => $this->customersQuery()->count(),
            'new_this_month' => $this->customersQuery()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->count(),
            'in_wallet' => $this->customersInWalletQuery()->count(),
            'close_to_reward' => $this->customersQuery()
                ->whereHas(
                    'cards',
                    fn($cards) => $cards
                        ->where('is_active', true)
                        ->where('stamps_collected', '>=', $this->rewardGoal - 2)
                        ->where('stamps_collected', '<', $this->rewardGoal),
                )
                ->count(),
        ];
    }

    public function getCustomersProperty()
    {
        return $this->customersQuery()
            ->withCount(['cards', 'cards as active_cards_count' => fn($cards) => $cards->where('is_active', true)])
            ->withSum('cards as stamps_total', 'stamps_collected')
            ->with(['registrationValues.field', 'location', 'cards' => fn($cards) => $cards->where('is_active', true), 'mobilePasses.registrations', 'mobilePasses.googleEvents'])
            ->latest()
            ->limit(50)
            ->get()
            ->each(fn(CustomerUser $customer) => $customer->setAttribute('wallet_status', $this->walletStatus($customer)));
    }
}; ?>
<div class="flex h-full w-full flex-1 flex-col gap-px p-4 md:p-6" style="background: var(--border);">
    <header class="flex flex-col gap-4 bg-background px-4 py-6 md:flex-row md:items-end md:justify-between md:px-6">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-fidentta-teal">Clientes</p>
            <h1 class="mt-1 text-4xl tracking-tight text-ink">Base de clientes</h1>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <label class="sr-only" for="customers-search">Buscar cliente</label>
                <input id="customers-search" wire:model.live.debounce.300ms="search" type="search"
                    placeholder="Buscar por nombre o email"
                    class="w-56 border-b-2 border-ink bg-transparent px-1 py-2 text-sm font-medium text-ink placeholder:text-ink2/60 focus:border-fidentta-teal focus:outline-none">
            </div>
            <label class="sr-only" for="customers-location">Filtrar por local</label>
            <select id="customers-location" wire:model.live="locationId"
                class="border-b-2 border-ink bg-transparent px-1 py-2 text-sm font-medium text-ink focus:border-fidentta-teal focus:outline-none">
                <option value="">Todos los locales</option>
                @foreach ($this->locations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
            </select>
        </div>
    </header>

    <section class="grid grid-cols-2 gap-px lg:grid-cols-4" style="background: var(--border);" aria-label="Resumen">
        <article class="bg-card p-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">Registrados</p>
            <p class="mt-3 text-4xl leading-none tracking-tight text-ink tabular-nums">
                {{ number_format($this->stats['total']) }}</p>
            <p class="mt-2 text-xs text-ink2">en este filtro</p>
        </article>
        <article class="bg-card p-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">Nuevos este mes</p>
            <p class="mt-3 text-4xl leading-none tracking-tight text-ink tabular-nums">
                {{ number_format($this->stats['new_this_month']) }}</p>
            <p class="mt-2 text-xs text-fidentta-teal">altas de clientes</p>
        </article>
        <article class="bg-card p-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">En Wallet</p>
            <p class="mt-3 text-4xl leading-none tracking-tight text-ink tabular-nums">
                {{ number_format($this->stats['in_wallet']) }}</p>
            <p class="mt-2 text-xs text-fidentta-teal">pases añadidos</p>
        </article>
        <article class="bg-card p-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">A punto del premio</p>
            <p class="mt-3 text-4xl leading-none tracking-tight text-fidentta-teal tabular-nums">
                {{ number_format($this->stats['close_to_reward']) }}</p>
            <p class="mt-2 text-xs text-ink2">a 1–2 sellos de la recompensa</p>
        </article>
    </section>

    <section class="bg-card p-5 md:p-6">
        @if ($statusMessage)
            <p role="status"
                class="mb-4 border border-fidentta-teal/30 bg-mint/40 px-4 py-3 text-sm font-medium text-ink">
                {{ $statusMessage }}</p>
        @endif
        @error('stamp')
            <p role="alert"
                class="mb-4 border border-destructive/30 bg-destructive/5 px-4 py-3 text-sm font-medium text-destructive">
                {{ $message }}</p>
        @enderror
        <div class="flex items-baseline justify-between gap-4">
            <h2 class="text-2xl tracking-tight text-ink">Clientes recientes</h2>
            <span class="text-xs font-semibold uppercase tracking-[0.16em] text-ink2">50 más recientes</span>
        </div>

        <ul class="mt-4 divide-y divide-border">
            @forelse ($this->customers as $cliente)
                @php
                    $nameValue = $cliente->registrationValues->first(
                        fn($value) => $value->field?->field_key === 'nombre',
                    )?->value;
                    $displayName = $nameValue ?: ($cliente->email ?: 'Invitado #' . $cliente->id);
                    $activeCard = $cliente->cards->first();
                    $stamps = (int) ($activeCard?->stamps_collected ?? 0);
                    $progress = min(100, (int) round(($stamps / $rewardGoal) * 100));
                    $completed = $activeCard !== null && $stamps >= $rewardGoal;
                @endphp
                <li class="flex items-center justify-between gap-4 py-4">
                    <div class="flex min-w-0 items-center gap-3">
                        <span
                            class="flex h-10 w-10 shrink-0 items-center justify-center bg-mint text-sm font-bold text-ink">{{ strtoupper(substr($displayName, 0, 1)) }}</span>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="truncate text-sm font-semibold text-ink">{{ $displayName }}</p>
                                @if ($completed)
                                    <span
                                        class="shrink-0 rounded-sm bg-fidentta-teal px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-white">Premio
                                        listo</span>
                                @endif
                            </div>
                            <p class="text-xs text-ink2">{{ $cliente->email ?: 'Sin correo' }} ·
                                {{ $cliente->location?->name ?? 'Sin local' }}</p>
                            @if ($activeCard)
                                <div class="mt-2 flex items-center gap-2">
                                    <div class="h-1.5 w-28 bg-border" role="img"
                                        aria-label="{{ $stamps }} de {{ $rewardGoal }} sellos">
                                        <div class="h-1.5 bg-fidentta-teal" style="width: {{ $progress }}%"></div>
                                    </div>
                                    <span
                                        class="text-[11px] font-semibold text-ink2 tabular-nums">{{ $stamps }}/{{ $rewardGoal }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-3">
                        <span
                            class="hidden text-[10px] font-semibold uppercase tracking-[0.14em] sm:inline {{ $cliente->wallet_status === 'En Wallet' ? 'text-fidentta-teal' : 'text-ink2/60' }}">
                            {{ $cliente->wallet_status }}
                        </span>
                        @if ($cliente->active_cards_count > 0)
                            <button type="button" wire:click="addStamp({{ $cliente->id }})"
                                wire:loading.attr="disabled" wire:target="addStamp({{ $cliente->id }})"
                                class="inline-flex items-center gap-1.5 bg-ink px-4 py-2.5 text-xs font-bold text-paper transition hover:opacity-90 disabled:opacity-60"
                                aria-label="Añadir un sello a {{ $displayName }}">
                                <span aria-hidden="true">+</span><span class="hidden sm:inline">Dar sello</span>
                            </button>
                        @endif
                    </div>
                </li>
            @empty
                <li class="py-10 text-center text-sm text-ink2">
                    No hay clientes que
                    coincidan{{ trim($search) !== '' ? ' con la búsqueda' : ($locationId !== '' ? ' en este local' : '') }}.
                </li>
            @endforelse
        </ul>
    </section>
</div>
