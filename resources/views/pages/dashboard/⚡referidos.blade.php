<?php

use App\Models\Referral;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $team;
    public string $search = '';

    public function mount(): void
    {
        $this->team = auth()->user()->currentTeam;
        abort_unless($this->team !== null, 404);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function getStatsProperty(): array
    {
        $referrals = Referral::query()->where('team_id', $this->team->id);

        return [
            'total' => (clone $referrals)->count(),
            'month' => (clone $referrals)->where('created_at', '>=', now()->startOfMonth())->count(),
            'referrers' => (clone $referrals)->distinct()->count('referrer_card_id'),
        ];
    }

    public function getReferralsProperty()
    {
        return Referral::query()
            ->where('team_id', $this->team->id)
            ->with(['referrerCard.customer', 'referredCard.customer'])
            ->when(trim($this->search) !== '', function ($query) {
                $term = '%' . trim($this->search) . '%';
                $query->where(function ($query) use ($term) {
                    $query->whereHas('referrerCard.customer', fn($customers) => $customers->where('email', 'like', $term))
                        ->orWhereHas('referredCard.customer', fn($customers) => $customers->where('email', 'like', $term));
                });
            })
            ->latest()
            ->paginate(8);
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-px p-4 md:p-6" style="background: var(--border);">
    <header class="flex flex-col gap-4 bg-background px-4 py-6 md:flex-row md:items-end md:justify-between md:px-6">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-fidentta-teal">Fidelización</p>
            <h1 class="mt-1 text-4xl tracking-tight text-ink">Referidos</h1>
        </div>
        <div>
            <label class="sr-only" for="referrals-search">Buscar cliente</label>
            <input id="referrals-search" wire:model.live.debounce.300ms="search" type="search"
                placeholder="Buscar por email"
                class="w-52 border-b-2 border-ink bg-transparent px-1 py-2 text-sm font-medium text-ink placeholder:text-ink2/60 focus:border-fidentta-teal focus:outline-none">
        </div>
    </header>

    <section class="grid grid-cols-3 gap-px" style="background: var(--border);" aria-label="Resumen de referidos">
        <article class="bg-card p-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">Referidos totales</p>
            <p class="mt-3 text-4xl leading-none tracking-tight text-fidentta-teal tabular-nums">
                {{ number_format($this->stats['total']) }}</p>
        </article>
        <article class="bg-card p-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">Este mes</p>
            <p class="mt-3 text-4xl leading-none tracking-tight text-ink tabular-nums">
                {{ number_format($this->stats['month']) }}</p>
        </article>
        <article class="bg-card p-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">Clientes que invitan</p>
            <p class="mt-3 text-4xl leading-none tracking-tight text-ink tabular-nums">
                {{ number_format($this->stats['referrers']) }}</p>
        </article>
    </section>

    @if ($team->customer_registration_type === 'none')
        <p role="alert" class="bg-amber-50 px-5 py-3 text-sm text-amber-900">
            Tu tarjeta está en modo «Sin registro»: los referidos no dan sellos. Cambia a «Básico» o «Personalizado»
            en la página Tarjeta para activarlos.
        </p>
    @endif

    <section class="bg-card p-5 md:p-6">
        <ul class="divide-y divide-border">
            @forelse ($this->referrals as $referral)
                <li wire:key="referral-{{ $referral->id }}"
                    class="flex flex-col gap-1 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-ink">
                            {{ $referral->referrerCard?->customer?->email ?: 'Cliente #' . $referral->referrerCard?->customer_id }}
                            <span class="font-normal text-ink2">invitó a</span>
                            {{ $referral->referredCard?->customer?->email ?: 'Cliente #' . $referral->referredCard?->customer_id }}
                        </p>
                        <p class="mt-1 text-xs text-ink2">+1 sello para cada uno</p>
                    </div>
                    <p class="shrink-0 text-xs text-ink2">{{ $referral->created_at?->diffForHumans() }}</p>
                </li>
            @empty
                <li class="py-10 text-center text-sm text-ink2">
                    Aún no hay referidos. Tus clientes invitan a amigos desde el QR de su tarjeta en la Wallet.
                </li>
            @endforelse
        </ul>

        <div class="mt-4">{{ $this->referrals->links() }}</div>
    </section>
</div>
