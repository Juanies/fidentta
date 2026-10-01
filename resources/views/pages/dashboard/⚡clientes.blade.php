<?php

use Livewire\Component;
use App\Models\CustomerUser;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public $team;
    public string $locationId = '';

    public function mount()
    {
        $this->team = auth()->user()->currentTeam;
        abort_unless($this->team !== null, 404);
    }

    public function updatedLocationId(): void
    {
        if ($this->locationId !== '' && !$this->team->locations()->whereKey($this->locationId)->exists()) {
            $this->locationId = '';
        }
    }

    public function getLocationsProperty()
    {
        return $this->team->locations()->orderBy('name')->get();
    }

    private function customersQuery()
    {
        $query = CustomerUser::query()->where('team_id', $this->team->id);

        if ($this->locationId !== '') {
            $query->where('location_id', $this->locationId);
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
        ];
    }

    public function getCustomersProperty()
    {
        return $this->customersQuery()
            ->withCount(['cards', 'cards as active_cards_count' => fn($cards) => $cards->where('is_active', true)])
            ->withSum('cards as stamps_total', 'stamps_collected')
            ->with(['registrationValues.field', 'location', 'mobilePasses.registrations', 'mobilePasses.googleEvents'])
            ->latest()
            ->limit(50)
            ->get()
            ->each(fn(CustomerUser $customer) => $customer->setAttribute('wallet_status', $this->walletStatus($customer)));
    }
}; ?>
<div class="flex h-full w-full flex-1 flex-col gap-6 p-2 md:p-5">
    <header
        class="rounded-4xl border border-white/10 bg-fidentta-gradient-soft p-6 shadow-2xl shadow-fidentta-purple/10 md:p-8">
        <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-[0.2em] text-fidentta-cyan">Clientes</p>
                <h1 class="text-3xl font-semibold tracking-tight text-text">Base de clientes</h1>
            </div>
            <div class="w-full md:w-64">
                <label for="customers-location"
                    class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-text-secondary/70">Filtrar por
                    local</label>
                <select id="customers-location" wire:model.live="locationId"
                    class="w-full rounded-xl border border-white/15 bg-fidentta-navy px-4 py-3 text-sm text-text focus:border-fidentta-cyan focus:outline-none focus:ring-2 focus:ring-fidentta-cyan/20">
                    <option value="">Todos los locales</option>
                    @foreach ($this->locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </header>

    <section class="grid gap-4 md:grid-cols-3">
        <div class="rounded-[1.75rem] border border-white/10 bg-white/3 p-5">
            <p class="text-xs uppercase tracking-[0.18em] text-text-secondary/60">Registrados</p>
            <p class="mt-3 text-3xl font-semibold text-text">{{ number_format($this->stats['total']) }}</p>
            <p class="mt-2 text-sm text-text-secondary/60">en este filtro</p>
        </div>
        <div class="rounded-[1.75rem] border border-white/10 bg-white/3 p-5">
            <p class="text-xs uppercase tracking-[0.18em] text-text-secondary/60">Nuevos este mes</p>
            <p class="mt-3 text-3xl font-semibold text-text">{{ number_format($this->stats['new_this_month']) }}</p>
            <p class="mt-2 text-sm text-fidentta-cyan">altas de clientes</p>
        </div>
        <div class="rounded-[1.75rem] border border-white/10 bg-white/3 p-5">
            <p class="text-xs uppercase tracking-[0.18em] text-text-secondary/60">En Wallet</p>
            <p class="mt-3 text-3xl font-semibold text-text">{{ number_format($this->stats['in_wallet']) }}</p>
            <p class="mt-2 text-sm text-fidentta-teal">pases añadidos</p>
        </div>
    </section>

    <section class="rounded-[1.75rem] border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10">
        <div class="mb-5 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.18em] text-fidentta-cyan">Listado</p>
                <h2 class="mt-2 text-xl font-semibold text-text">Clientes recientes</h2>
            </div>
            <span class="rounded-full border border-white/10 bg-white/4 px-3 py-1 text-xs text-text-secondary/80">50 más
                recientes</span>
        </div>

        <div class="space-y-3">
            @forelse ($this->customers as $cliente)
                @php
                    $nameValue = $cliente->registrationValues->first(
                        fn($value) => $value->field?->field_key === 'nombre',
                    )?->value;
                    $displayName = $nameValue ?: ($cliente->email ?: 'Invitado #' . $cliente->id);
                @endphp
                <div class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/2 p-3.5">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-fidentta-cyan/12 text-sm font-bold text-fidentta-cyan">
                            {{ strtoupper(substr($displayName, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium text-text">{{ $displayName }}</p>
                            <p class="text-xs text-text-secondary/60">{{ $cliente->email ?: 'Sin correo' }} ·
                                {{ $cliente->location?->name ?? 'Sin local' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <span
                            class="text-sm text-text-secondary/80">{{ number_format((int) ($cliente->stamps_total ?? 0)) }}
                            sellos</span>
                        <span
                            class="rounded-full {{ $cliente->wallet_status === 'En Wallet' ? 'bg-fidentta-teal/15 text-fidentta-teal' : ($cliente->wallet_status === 'Desactivada' ? 'bg-rose-400/15 text-rose-300' : 'bg-white/5 text-text-secondary/70') }} px-2 py-1 text-[10px] font-semibold uppercase tracking-[0.14em]">
                            {{ $cliente->wallet_status }}
                        </span>
                    </div>
                </div>
            @empty
                <p
                    class="rounded-xl border border-dashed border-white/15 p-8 text-center text-sm text-text-secondary/70">
                    No hay clientes registrados{{ $locationId !== '' ? ' en este local' : ' todavía' }}.</p>
            @endforelse
        </div>
    </section>
</div>
