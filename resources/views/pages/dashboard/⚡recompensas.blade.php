<?php

use App\Models\CardReward;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $team;
    public string $locationId = '';
    public string $status = 'pending';
    public string $search = '';
    public ?string $statusMessage = null;

    public function mount(): void
    {
        $this->team = auth()->user()->currentTeam;
        abort_unless($this->team !== null, 404);
    }

    public function updatedLocationId(): void
    {
        if ($this->locationId !== '' && !$this->team->locations()->whereKey($this->locationId)->exists()) {
            $this->locationId = '';
        }

        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        if (!in_array($this->status, ['pending', 'delivered', 'all'], true)) {
            $this->status = 'pending';
        }

        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function markRewardDelivered(int $rewardId): void
    {
        $updated = DB::transaction(function () use ($rewardId): bool {
            $reward = CardReward::query()->where('team_id', $this->team->id)->lockForUpdate()->findOrFail($rewardId);

            if ($reward->delivered_at !== null) {
                return false;
            }

            $reward->update([
                'delivered_at' => now(),
                'delivered_by' => auth()->id(),
            ]);

            return true;
        });

        $this->statusMessage = $updated ? 'Recompensa marcada como entregada.' : 'Esta recompensa ya estaba entregada.';

        if ($updated) {
            $this->dispatch('rewards-updated');
        }
    }

    public function getLocationsProperty()
    {
        return $this->team->locations()->orderBy('name')->get();
    }

    public function getStatsProperty(): array
    {
        $rewards = CardReward::query()->where('team_id', $this->team->id);

        return [
            'pending' => (clone $rewards)->whereNull('delivered_at')->count(),
            'delivered' => (clone $rewards)->whereNotNull('delivered_at')->count(),
        ];
    }

    public function getRewardsProperty()
    {
        return CardReward::query()
            ->where('team_id', $this->team->id)
            ->with(['customer.registrationValues.field', 'customer.location', 'card', 'earnedBy', 'deliveredBy'])
            ->when($this->locationId !== '', fn($query) => $query->whereHas('customer', fn($customers) => $customers->where('location_id', $this->locationId)))
            ->when($this->status === 'pending', fn($query) => $query->whereNull('delivered_at'))
            ->when($this->status === 'delivered', fn($query) => $query->whereNotNull('delivered_at'))
            ->when(trim($this->search) !== '', function ($query) {
                $term = '%' . trim($this->search) . '%';
                $query->whereHas('customer', fn($customers) => $customers->where('email', 'like', $term)->orWhereHas('registrationValues', fn($values) => $values->where('value', 'like', $term)));
            })
            ->orderByRaw('delivered_at IS NOT NULL')
            ->orderByDesc('earned_at')
            ->paginate(20);
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-px p-4 md:p-6" style="background: var(--border);">
    <header class="flex flex-col gap-4 bg-background px-4 py-6 md:flex-row md:items-end md:justify-between md:px-6">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-fidentta-teal">Fidelización</p>
            <h1 class="mt-1 text-4xl tracking-tight text-ink">Recompensas</h1>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <label class="sr-only" for="rewards-search">Buscar cliente</label>
            <input id="rewards-search" wire:model.live.debounce.300ms="search" type="search" placeholder="Buscar cliente"
                class="w-52 border-b-2 border-ink bg-transparent px-1 py-2 text-sm font-medium text-ink placeholder:text-ink2/60 focus:border-fidentta-teal focus:outline-none">
            <label class="sr-only" for="rewards-location">Filtrar por local</label>
            <select id="rewards-location" wire:model.live="locationId"
                class="border-b-2 border-ink bg-transparent px-1 py-2 text-sm font-medium text-ink focus:border-fidentta-teal focus:outline-none">
                <option value="">Todos los locales</option>
                @foreach ($this->locations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
            </select>
            <label class="sr-only" for="rewards-status">Estado</label>
            <select id="rewards-status" wire:model.live="status"
                class="border-b-2 border-ink bg-transparent px-1 py-2 text-sm font-medium text-ink focus:border-fidentta-teal focus:outline-none">
                <option value="pending">Pendientes</option>
                <option value="delivered">Entregadas</option>
                <option value="all">Todas</option>
            </select>
        </div>
    </header>

    <section class="grid grid-cols-2 gap-px" style="background: var(--border);" aria-label="Resumen de recompensas">
        <article class="bg-card p-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">Pendientes de entregar</p>
            <p class="mt-3 text-4xl leading-none tracking-tight text-fidentta-teal tabular-nums">
                {{ number_format($this->stats['pending']) }}</p>
        </article>
        <article class="bg-card p-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">Entregadas</p>
            <p class="mt-3 text-4xl leading-none tracking-tight text-ink tabular-nums">
                {{ number_format($this->stats['delivered']) }}</p>
        </article>
    </section>

    <section class="bg-card p-5 md:p-6">
        @if ($statusMessage)
            <p role="status"
                class="mb-4 border border-fidentta-teal/30 bg-mint/40 px-4 py-3 text-sm font-medium text-ink">
                {{ $statusMessage }}</p>
        @endif

        <ul class="divide-y divide-border">
            @forelse ($this->rewards as $reward)
                @php
                    $name =
                        $reward->customer->registrationValues->first(
                            fn($value) => $value->field?->field_key === 'nombre',
                        )?->value ?:
                        ($reward->customer->email ?:
                        'Invitado #' . $reward->customer->id);
                @endphp
                <li wire:key="reward-{{ $reward->id }}"
                    class="flex flex-col gap-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                            <p class="text-sm font-semibold text-ink">{{ $name }}</p>
                            <span class="text-xs text-ink2">{{ $reward->reward_name }}</span>
                            <span class="text-xs font-semibold text-ink2">Ciclo {{ $reward->cycle_number }}</span>
                        </div>
                        <p class="mt-1 text-xs text-ink2">
                            {{ $reward->customer->email ?: 'Sin correo' }} ·
                            {{ $reward->customer->location?->name ?? 'Sin local' }}
                            · Completada {{ $reward->earned_at->format('d/m/Y H:i') }}
                        </p>
                        @if ($reward->delivered_at)
                            <p class="mt-1 text-xs text-fidentta-teal">
                                Entregada {{ $reward->delivered_at->format('d/m/Y H:i') }}
                                @if ($reward->deliveredBy)
                                    por {{ $reward->deliveredBy->name }}
                                @endif
                            </p>
                        @endif
                    </div>
                    @if (!$reward->delivered_at)
                        <button type="button" wire:click="markRewardDelivered({{ $reward->id }})"
                            wire:loading.attr="disabled" wire:target="markRewardDelivered({{ $reward->id }})"
                            class="inline-flex shrink-0 items-center justify-center gap-2 bg-ink px-4 py-2.5 text-xs font-bold text-paper transition hover:opacity-90 disabled:opacity-60">
                            <span aria-hidden="true">✓</span> Marcar entregada
                        </button>
                    @endif
                </li>
            @empty
                <li class="py-12 text-center text-sm text-ink2">
                    {{ $status === 'pending' ? 'No hay recompensas pendientes.' : 'No hay recompensas para mostrar.' }}
                </li>
            @endforelse
        </ul>

        <div class="mt-4">{{ $this->rewards->links('components.pagination.numeric') }}</div>
    </section>
</div>
