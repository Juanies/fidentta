<?php

use Livewire\Component;
use App\Models\CardTransaction;
use App\Models\CustomerUser;
use App\Models\Location;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public $team;
    public $locations;
    public string $locationId = '';

    public function mount()
    {
        $this->team = auth()->user()->currentTeam;
        abort_unless($this->team !== null, 404);
        $this->locations = $this->team->locations()->orderBy('name')->get();
    }

    public function updatedLocationId(): void
    {
        if ($this->locationId !== '' && !$this->team->locations()->whereKey($this->locationId)->exists()) {
            $this->locationId = '';
        }
    }

    private function customersQuery()
    {
        $query = CustomerUser::query()->where('team_id', $this->team->id);

        if ($this->locationId !== '') {
            $query->where('location_id', $this->locationId);
        }

        return $query;
    }

    private function transactionsQuery()
    {
        $query = CardTransaction::query()->whereHas('location', fn($locations) => $locations->where('team_id', $this->team->id));

        if ($this->locationId !== '') {
            $query->where('location_id', $this->locationId);
        }

        return $query;
    }

    private function customersWithWalletAddsQuery($start, $end)
    {
        return $this->customersQuery()->whereHas('mobilePasses', function ($passes) use ($start, $end) {
            $passes->where(function ($platforms) use ($start, $end) {
                $platforms
                    ->where(function ($apple) use ($start, $end) {
                        $apple->where('platform', 'apple')->whereHas('registrations', fn($registrations) => $registrations->whereBetween('created_at', [$start, $end]));
                    })
                    ->orWhere(function ($google) use ($start, $end) {
                        $google->where('platform', 'google')->whereHas('googleEvents', fn($events) => $events->where('event_type', 'save')->whereBetween('received_at', [$start, $end]));
                    });
            });
        });
    }

    private function comparison(int|float $current, int|float $previous): array
    {
        if ($previous === 0.0 || $previous === 0) {
            $change = $current === 0.0 || $current === 0 ? '0%' : 'Nuevo';
        } else {
            $percent = (($current - $previous) / $previous) * 100;
            $change = sprintf('%s%.1f%%', $percent > 0 ? '+' : '', $percent);
        }

        return [
            'value' => $current,
            'change' => $change,
            'direction' => $current >= $previous ? 'up' : 'down',
        ];
    }

    public function getStatsProperty(): array
    {
        $currentStart = now()->startOfDay()->subDays(6);
        $currentEnd = now()->endOfDay();
        $previousStart = $currentStart->subDays(7);
        $previousEnd = $currentStart->subSecond();

        $currentCustomers = $this->customersQuery()
            ->whereBetween('created_at', [$currentStart, $currentEnd])
            ->count();
        $previousCustomers = $this->customersQuery()
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->count();

        $currentStamps = (int) $this->transactionsQuery()
            ->whereBetween('created_at', [$currentStart, $currentEnd])
            ->sum('stamps_added');
        $previousStamps = (int) $this->transactionsQuery()
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->sum('stamps_added');

        $currentWalletAdds = $this->customersWithWalletAddsQuery($currentStart, $currentEnd)->count();
        $previousWalletAdds = $this->customersWithWalletAddsQuery($previousStart, $previousEnd)->count();

        $currentVisits = $this->transactionsQuery()
            ->whereBetween('created_at', [$currentStart, $currentEnd])
            ->count();
        $previousVisits = $this->transactionsQuery()
            ->whereBetween('created_at', [$previousStart, $previousEnd])
            ->count();

        return [
            'customers' => $this->comparison($currentCustomers, $previousCustomers),
            'stamps' => $this->comparison($currentStamps, $previousStamps),
            'wallet_adds' => $this->comparison($currentWalletAdds, $previousWalletAdds),
            'visits' => $this->comparison($currentVisits, $previousVisits),
        ];
    }

    public function getActivityProperty()
    {
        $start = now()->startOfDay()->subDays(6);
        $stampsByDay = $this->transactionsQuery()->where('created_at', '>=', $start)->selectRaw('DATE(created_at) as activity_date, SUM(stamps_added) as stamps')->groupBy('activity_date')->pluck('stamps', 'activity_date');

        $days = collect(range(0, 6))->map(function (int $offset) use ($start, $stampsByDay) {
            $date = $start->copy()->addDays($offset);
            $key = $date->toDateString();

            return [
                'label' => $date->format('d/m'),
                'stamps' => (int) ($stampsByDay[$key] ?? 0),
            ];
        });
        $maximum = max(1, (int) $days->max('stamps'));

        return $days->map(fn(array $day) => [...$day, 'height' => max(4, (int) round(($day['stamps'] / $maximum) * 100))]);
    }

    public function getRecentTransactionsProperty()
    {
        return $this->transactionsQuery()
            ->with(['customer', 'location'])
            ->latest()
            ->limit(6)
            ->get();
    }

    public function getProgramProperty(): array
    {
        $design = $this->team->cardDesign()->where('is_active', true)->first();
        $activeLocations = $this->team->locations()->where('is_active', true)->count();
        $stepsComplete = (int) ($design !== null) + (int) ($activeLocations > 0);

        return [
            'design' => $design,
            'active_locations' => $activeLocations,
            'progress' => (int) round(($stepsComplete / 2) * 100),
        ];
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-6 p-2 md:p-5">
    <header
        class="relative overflow-hidden rounded-4xl border border-white/10 bg-fidentta-gradient-soft p-6 shadow-2xl shadow-fidentta-purple/10 md:p-8">
        <div class="absolute -right-20 -top-24 h-72 w-72 rounded-full bg-fidentta-cyan/15 blur-3xl"></div>
        <div class="relative flex flex-col gap-8 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-2xl">
                <div
                    class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-3 py-1.5 text-[10px] font-semibold uppercase tracking-[0.2em] text-fidentta-cyan">
                    <span class="h-2 w-2 rounded-full bg-fidentta-teal"></span> Panel de negocio
                </div>
                <p class="text-sm text-text-secondary/70">Buenos días, {{ auth()->user()->name }}</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-text sm:text-4xl">Resumen del programa</h1>
                <p class="mt-3 max-w-xl text-sm leading-7 text-text-secondary/80 sm:text-base">Actividad y clientes de
                    {{ $locationId !== '' ? $locations->firstWhere('id', (int) $locationId)?->name : $team->name }}.</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <label class="sr-only" for="dashboard-location">Filtrar por local</label>
                <select id="dashboard-location" wire:model.live="locationId"
                    class="rounded-xl border border-white/15 bg-fidentta-navy px-4 py-3 text-sm text-text focus:border-fidentta-cyan focus:outline-none focus:ring-2 focus:ring-fidentta-cyan/20">
                    <option value="">Todos los locales</option>
                    @foreach ($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
                <a href="{{ route('dashboard.locales', ['current_team' => $team]) }}" wire:navigate
                    class="inline-flex items-center justify-center gap-2 rounded-full bg-fidentta-cyan px-5 py-3 text-sm font-bold text-fidentta-navy transition hover:bg-white">Gestionar
                    locales</a>
            </div>
        </div>
    </header>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10">
            <div class="flex items-center justify-between"><span
                    class="flex h-10 w-10 items-center justify-center rounded-2xl bg-fidentta-cyan/12 text-fidentta-cyan">&#9673;</span><span
                    class="text-xs font-semibold text-fidentta-teal">Últimos 7 días</span></div>
            <p class="mt-5 text-xs uppercase tracking-[0.16em] text-text-secondary/60">Clientes nuevos</p>
            <p class="mt-2 text-3xl font-semibold text-text">
                {{ number_format((int) $this->stats['customers']['value']) }}</p>
            <p
                class="mt-1 text-xs {{ $this->stats['customers']['direction'] === 'up' ? 'text-fidentta-teal' : 'text-rose-700' }}">
                {{ $this->stats['customers']['change'] }} vs. 7 días anteriores</p>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10">
            <div class="flex items-center justify-between"><span
                    class="flex h-10 w-10 items-center justify-center rounded-2xl bg-fidentta-teal/12 text-fidentta-teal">&#10003;</span><span
                    class="text-xs font-semibold text-fidentta-teal">Últimos 7 días</span></div>
            <p class="mt-5 text-xs uppercase tracking-[0.16em] text-text-secondary/60">Sellos entregados</p>
            <p class="mt-2 text-3xl font-semibold text-text">{{ number_format((int) $this->stats['stamps']['value']) }}
            </p>
            <p
                class="mt-1 text-xs {{ $this->stats['stamps']['direction'] === 'up' ? 'text-fidentta-teal' : 'text-rose-700' }}">
                {{ $this->stats['stamps']['change'] }} vs. 7 días anteriores</p>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10">
            <div class="flex items-center justify-between"><span
                    class="flex h-10 w-10 items-center justify-center rounded-2xl bg-fidentta-purple/12 text-fidentta-purple">&#9733;</span><span
                    class="text-xs font-semibold text-fidentta-purple">Últimos 7 días</span></div>
            <p class="mt-5 text-xs uppercase tracking-[0.16em] text-text-secondary/60">Pases añadidos a Wallet</p>
            <p class="mt-2 text-3xl font-semibold text-text">
                {{ number_format((int) $this->stats['wallet_adds']['value']) }}</p>
            <p
                class="mt-1 text-xs {{ $this->stats['wallet_adds']['direction'] === 'up' ? 'text-fidentta-teal' : 'text-rose-700' }}">
                {{ $this->stats['wallet_adds']['change'] }} vs. 7 días anteriores</p>
        </div>
        <div class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10">
            <div class="flex items-center justify-between"><span
                    class="flex h-10 w-10 items-center justify-center rounded-2xl bg-fidentta-blue/12 text-fidentta-blue">&#8599;</span><span
                    class="text-xs font-semibold text-fidentta-cyan">Últimos 7 días</span></div>
            <p class="mt-5 text-xs uppercase tracking-[0.16em] text-text-secondary/60">Visitas registradas</p>
            <p class="mt-2 text-3xl font-semibold text-text">{{ number_format((int) $this->stats['visits']['value']) }}
            </p>
            <p
                class="mt-1 text-xs {{ $this->stats['visits']['direction'] === 'up' ? 'text-fidentta-teal' : 'text-rose-700' }}">
                {{ $this->stats['visits']['change'] }} vs. 7 días anteriores</p>
        </div>
    </section>

    <section class="grid gap-4 xl:grid-cols-[1.55fr,0.85fr]">
        <div class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10 md:p-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-fidentta-cyan">Actividad diaria</p>
                    <h2 class="mt-2 text-xl font-semibold text-text">Sellos entregados por día</h2>
                    <p class="mt-1 text-sm text-text-secondary/60">Últimos siete días</p>
                </div><span
                    class="rounded-full border border-white/10 bg-white/4 px-3 py-1.5 text-xs text-text-secondary/70">{{ $this->activity->sum('stamps') }}
                    sellos</span>
            </div>
            <div class="mt-6 flex h-60 items-end gap-3 rounded-2xl border border-white/10 bg-fidentta-navy/40 p-4"
                role="img" aria-label="Sellos entregados en los últimos siete días">
                @foreach ($this->activity as $day)
                    <div class="flex h-full min-w-0 flex-1 flex-col items-center justify-end gap-2">
                        <span class="text-xs text-text-secondary/70">{{ $day['stamps'] }}</span>
                        <div class="w-full rounded-t bg-fidentta-cyan" style="height: {{ $day['height'] }}%"
                            title="{{ $day['stamps'] }} sellos"></div>
                        <span class="text-[10px] text-text-secondary/60">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10 md:p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-fidentta-teal">Estado del programa</p>
                    <h2 class="mt-2 text-xl font-semibold text-text">Todo listo para crecer</h2>
                </div><span
                    class="flex h-10 w-10 items-center justify-center rounded-2xl bg-fidentta-teal/12 text-fidentta-teal">&#10003;</span>
            </div>
            <div class="mt-6 flex items-center gap-4">
                <div class="relative flex h-24 w-24 items-center justify-center rounded-full"
                    style="background: conic-gradient(#18b981 0 {{ $this->program['progress'] }}%, rgba(255,255,255,.08) {{ $this->program['progress'] }}% 100%);">
                    <div
                        class="flex h-16 w-16 items-center justify-center rounded-full bg-[#18233f] text-xl font-bold text-text">
                        {{ $this->program['progress'] }}%</div>
                </div>
                <div>
                    <p class="font-semibold text-text">Configuración del programa</p>
                    <p class="mt-1 text-sm leading-6 text-text-secondary/65">{{ $this->program['active_locations'] }}
                        locales activos</p>
                </div>
            </div>
            <div class="mt-6 space-y-3">
                <div
                    class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/2 p-3 text-sm">
                    <span class="text-text-secondary/75">Diseño de tarjeta</span><span
                        class="{{ $this->program['design'] ? 'text-fidentta-teal' : 'text-text-secondary/60' }}">{{ $this->program['design'] ? 'Activo' : 'Pendiente' }}</span>
                </div>
                <div
                    class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/2 p-3 text-sm">
                    <span class="text-text-secondary/75">Recompensa</span><span
                        class="text-fidentta-teal">{{ $this->program['design']?->reward ?? 'Sin configurar' }}</span>
                </div><a href="{{ route('dashboard.locales', ['current_team' => $team]) }}" wire:navigate
                    class="flex items-center justify-between rounded-2xl border border-fidentta-cyan/30 bg-fidentta-cyan/10 p-3 text-sm font-semibold text-fidentta-cyan transition hover:bg-fidentta-cyan/20"><span>Gestionar
                        locales</span><span>&rarr;</span></a>
            </div>
        </div>
    </section>

    <section class="grid gap-4 lg:grid-cols-[1.1fr,0.9fr]">
        <div class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-xl shadow-black/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.18em] text-fidentta-cyan">Actividad reciente</p>
                    <h2 class="mt-2 text-xl font-semibold text-text">Últimas interacciones</h2>
                </div><a href="{{ route('dashboard.clientes', ['current_team' => $team]) }}" wire:navigate
                    class="text-xs font-semibold text-fidentta-cyan hover:text-white">Ver clientes &rarr;</a>
            </div>
            <div class="mt-5 space-y-3">
                @forelse ($this->recentTransactions as $transaction)
                    <div
                        class="flex items-center justify-between gap-4 rounded-2xl border border-white/10 bg-white/2 p-3.5">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-medium text-text">
                                {{ $transaction->customer?->email ?: 'Invitado #' . $transaction->customer_id }}</p>
                            <p class="text-xs text-text-secondary/60">{{ $transaction->location?->name }} ·
                                {{ $transaction->created_at?->diffForHumans() }}</p>
                        </div>
                        <span
                            class="shrink-0 rounded-full bg-fidentta-teal/15 px-2 py-1 text-[10px] font-semibold text-fidentta-teal">+{{ $transaction->stamps_added }}
                            sellos</span>
                    </div>
                @empty
                    <p class="rounded-xl border border-dashed border-white/15 p-5 text-sm text-text-secondary/70">
                        Todavía no hay sellos registrados{{ $locationId !== '' ? ' en este local' : '' }}.</p>
                @endforelse
            </div>
        </div>
        <div class="rounded-3xl border border-fidentta-cyan/20 bg-fidentta-cyan/5 p-5">
            <p class="text-xs uppercase tracking-[0.18em] text-fidentta-cyan">Siguiente mejor acción</p>
            <h2 class="mt-3 text-xl font-semibold text-text">Comparte el QR con tu equipo</h2>
            <p class="mt-2 text-sm leading-6 text-text-secondary/70">Tus empleados podrán reconocer cada visita
                y
                entregar sellos sin complicar el proceso.</p><a
                href="{{ route('dashboard.fidelizacion', ['current_team' => $team]) }}" wire:navigate
                class="mt-5 inline-flex rounded-full bg-fidentta-cyan px-4 py-2.5 text-sm font-bold text-fidentta-navy transition hover:bg-white">Abrir
                fidelización &rarr;</a>
        </div>
    </section>
</div>
