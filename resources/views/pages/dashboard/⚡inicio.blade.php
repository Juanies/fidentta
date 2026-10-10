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
            ->with(['customer.registrationValues.field', 'location', 'user'])
            ->latest()
            ->limit(6)
            ->get();
    }

    public function getTodayProperty(): string
    {
        return now()->translatedFormat('l, j \\d\\e F');
    }

    public function getPendingTasksProperty(): array
    {
        $design = $this->program['design'];
        $tasks = [];

        if (!$design) {
            $tasks[] = [
                'title' => 'Configura tu tarjeta de fidelidad',
                'detail' => 'Define sellos, colores y recompensa para empezar.',
                'route' => route('dashboard.tarjeta', ['current_team' => $this->team->slug]),
                'cta' => 'Configurar tarjeta',
                'priority' => 'Alta',
            ];
        }

        if ($this->program['active_locations'] === 0) {
            $tasks[] = [
                'title' => 'Crea tu primer local',
                'detail' => 'Cada local tiene su propio QR de registro.',
                'route' => route('dashboard.locales', ['current_team' => $this->team->slug]),
                'cta' => 'Crear local',
                'priority' => 'Alta',
            ];
        }

        if ($this->insights['total_customers'] === 0 && $design && $this->program['active_locations'] > 0) {
            $tasks[] = [
                'title' => 'Consigue tu primer cliente',
                'detail' => 'Imprime el QR del local y compártelo en el mostrador.',
                'route' => route('dashboard.fidelizacion', ['current_team' => $this->team->slug]),
                'cta' => 'Ver QR',
                'priority' => 'Media',
            ];
        }

        if ($this->insights['close_to_reward'] > 0) {
            $tasks[] = [
                'title' => $this->insights['close_to_reward'] . ($this->insights['close_to_reward'] === 1 ? ' cliente está' : ' clientes están') . ' a 1–2 sellos del premio',
                'detail' => 'Un recordatorio en caja puede cerrar la recompensa hoy.',
                'route' => route('dashboard.clientes', ['current_team' => $this->team->slug]),
                'cta' => 'Ver clientes',
                'priority' => 'Media',
            ];
        }

        if ($this->insights['total_customers'] >= 3 && $this->insights['wallet_rate'] < 50) {
            $tasks[] = [
                'title' => 'La adopción de Wallet está por debajo del 50%',
                'detail' => 'Animar a guardar la tarjeta aumenta las visitas de retorno.',
                'route' => route('dashboard.fidelizacion', ['current_team' => $this->team->slug]),
                'cta' => 'Revisar programa',
                'priority' => 'Baja',
            ];
        }

        if ($this->insights['completed_cards'] > 0) {
            $tasks[] = [
                'title' => $this->insights['completed_cards'] . ($this->insights['completed_cards'] === 1 ? ' tarjeta completada espera' : ' tarjetas completadas esperan') . ' canje',
                'detail' => 'Entrega la recompensa y renueva el ciclo del cliente.',
                'route' => route('dashboard.clientes', ['current_team' => $this->team->slug]),
                'cta' => 'Ver clientes',
                'priority' => 'Alta',
            ];
        }

        return array_slice($tasks, 0, 4);
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

    public function getGreetingProperty(): string
    {
        $hour = (int) now()->format('G');

        return match (true) {
            $hour >= 6 && $hour < 14 => 'Buenos días',
            $hour >= 14 && $hour < 21 => 'Buenas tardes',
            default => 'Buenas noches',
        };
    }

    public function getTopCustomersProperty()
    {
        return $this->customersQuery()
            ->withSum(['cards as total_stamps' => fn($cards) => $cards->where('is_active', true)], 'stamps_collected')
            ->with('registrationValues.field')
            ->orderByDesc('total_stamps')
            ->limit(5)
            ->get()
            ->map(function (CustomerUser $customer) {
                $name = $customer->registrationValues->first(fn($value) => $value->field?->field_key === 'nombre')?->value;

                return [
                    'id' => $customer->id,
                    'name' => $name ?: ($customer->email ?: 'Invitado #' . $customer->id),
                    'location' => $customer->location?->name ?? 'Sin local',
                    'stamps' => (int) ($customer->total_stamps ?? 0),
                ];
            })
            ->filter(fn($customer) => $customer['stamps'] > 0)
            ->values();
    }

    public function getLocationBreakdownProperty()
    {
        return $this->team
            ->locations()
            ->where('is_active', true)
            ->withCount('customerUsers as customers_count')
            ->withSum(['cardTransactions as stamps' => fn($transactions) => $transactions->where('created_at', '>=', now()->startOfDay()->subDays(29))], 'stamps_added')
            ->orderByDesc('stamps')
            ->get()
            ->map(
                fn($location) => [
                    'name' => $location->name,
                    'customers' => (int) $location->customers_count,
                    'stamps' => (int) ($location->stamps ?? 0),
                ],
            );
    }

    public function getCycleInsightProperty(): array
    {
        $goal = max(1, (int) ($this->program['design']?->stamps_required ?? 8));
        $days = $this->activity;
        $bestDay = $days->sortByDesc('stamps')->first();

        $firstTransaction = $this->transactionsQuery()->min('created_at');
        $daysActive = $firstTransaction
            ? max(
                1,
                now()
                    ->startOfDay()
                    ->diffInDays(\Illuminate\Support\Carbon::parse($firstTransaction)->startOfDay()) + 1,
            )
            : 0;
        $totalStamps = (int) $this->transactionsQuery()->sum('stamps_added');
        $pace = $daysActive > 0 ? round($totalStamps / $daysActive, 1) : 0;

        return [
            'goal' => $goal,
            'reward' => $this->program['design']?->reward ?? '—',
            'best_day_label' => ($bestDay['stamps'] ?? 0) > 0 ? $bestDay['label'] : null,
            'best_day_stamps' => (int) ($bestDay['stamps'] ?? 0),
            'pace' => $pace,
            'days_active' => $daysActive,
        ];
    }

    private function activeCardsQuery()
    {
        return $this->team
            ->cards()
            ->where('is_active', true)
            ->whereHas('customer', function ($customers) {
                $customers->when($this->locationId !== '', fn($query) => $query->where('location_id', $this->locationId));
            });
    }

    public function getInsightsProperty(): array
    {
        $cards = $this->activeCardsQuery()->with('cardDesign')->get();
        $required = max(1, (int) ($this->program['design']?->stamps_required ?? 8));

        $totalCustomers = $this->customersQuery()->count();
        $totalStamps = (int) $this->transactionsQuery()->sum('stamps_added');
        $completed = $cards->filter(fn($card) => $card->cardDesign && (int) $card->stamps_collected >= (int) $card->cardDesign->stamps_required)->count();
        $closeToReward = $cards
            ->filter(function ($card) {
                $goal = (int) ($card->cardDesign?->stamps_required ?? 0);

                return $goal > 0 && $card->stamps_collected < $goal && $card->stamps_collected >= $goal - 2;
            })
            ->count();
        $walletInstalled = $this->customersInWalletQuery()->count();

        return [
            'total_customers' => $totalCustomers,
            'total_stamps' => $totalStamps,
            'completed_cards' => $completed,
            'close_to_reward' => $closeToReward,
            'wallet_installed' => $walletInstalled,
            'avg_stamps' => $totalCustomers > 0 ? round($totalStamps / $totalCustomers, 1) : 0,
            'wallet_rate' => $totalCustomers > 0 ? (int) round(($walletInstalled / $totalCustomers) * 100) : 0,
            'reward_goal' => $required,
        ];
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
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-px p-4 md:p-6" style="background: var(--border);">
    <header class="flex flex-col gap-4 bg-background px-4 py-6 sm:flex-row sm:items-end sm:justify-between md:px-6">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-fidentta-teal">Panel de negocio ·
                {{ $this->today }}</p>
            <h1 class="mt-1 text-4xl tracking-tight text-ink sm:text-5xl">
                {{ $this->greeting }}, {{ auth()->user()->name }}</h1>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <label class="sr-only" for="dashboard-location">Filtrar por local</label>
            <select id="dashboard-location" wire:model.live="locationId"
                class="border-b-2 border-ink bg-transparent px-1 py-2 text-sm font-medium text-ink focus:border-fidentta-teal focus:outline-none">
                <option value="">Todos los locales</option>
                @foreach ($locations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
            </select>
            <span class="px-2 py-2 text-xs font-semibold uppercase tracking-[0.16em] text-ink2">Últimos 7 días</span>
            <a href="{{ route('dashboard.clientes', ['current_team' => $team]) }}" wire:navigate
                class="inline-flex items-center gap-2 bg-ink px-5 py-3 text-sm font-bold text-paper transition hover:opacity-90">Dar
                un sello <span aria-hidden="true">&rarr;</span></a>
        </div>
    </header>

    <section class="grid grid-cols-2 gap-px lg:grid-cols-4" style="background: var(--border);" aria-label="Rendimiento">
        @php
            $kpis = [
                ['label' => 'Clientes nuevos', 'stat' => $this->stats['customers']],
                ['label' => 'Sellos entregados', 'stat' => $this->stats['stamps']],
                ['label' => 'Pases en Wallet', 'stat' => $this->stats['wallet_adds']],
                ['label' => 'Visitas', 'stat' => $this->stats['visits']],
            ];
        @endphp
        @foreach ($kpis as $kpi)
            <article class="flex flex-col justify-between bg-card p-5">
                <div class="flex items-baseline justify-between gap-2">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">{{ $kpi['label'] }}</p>
                    <span
                        class="text-xs font-semibold tabular-nums {{ $kpi['stat']['direction'] === 'up' ? 'text-fidentta-teal' : 'text-destructive' }}"
                        title="vs. periodo anterior">
                        {{ $kpi['stat']['direction'] === 'up' ? '↑' : '↓' }} {{ $kpi['stat']['change'] }}
                    </span>
                </div>
                <p class="mt-4 text-5xl leading-none tracking-tight text-ink tabular-nums">
                    {{ number_format((int) $kpi['stat']['value']) }}</p>
            </article>
        @endforeach
    </section>

    <section class="grid grid-cols-2 gap-px lg:grid-cols-4" style="background: var(--border);"
        aria-label="Salud del programa">
        <article class="bg-card p-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">Tarjetas completadas</p>
            <p class="mt-3 text-4xl leading-none tracking-tight text-ink tabular-nums">
                {{ number_format($this->insights['completed_cards']) }}</p>
            <p class="mt-2 text-xs text-ink2">premios ya desbloqueados</p>
        </article>
        <article class="bg-card p-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">A punto del premio</p>
            <p class="mt-3 text-4xl leading-none tracking-tight text-fidentta-teal tabular-nums">
                {{ number_format($this->insights['close_to_reward']) }}</p>
            <p class="mt-2 text-xs text-ink2">les faltan 1–2 sellos</p>
        </article>
        <article class="bg-card p-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">Sellos por cliente</p>
            <p class="mt-3 text-4xl leading-none tracking-tight text-ink tabular-nums">
                {{ $this->insights['avg_stamps'] }}</p>
            <p class="mt-2 text-xs text-ink2">media histórica</p>
        </article>
        <article class="bg-card p-5">
            <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">Adopción Wallet</p>
            <p class="mt-3 text-4xl leading-none tracking-tight text-ink tabular-nums">
                {{ $this->insights['wallet_rate'] }}<span class="text-xl">%</span></p>
            <p class="mt-2 text-xs text-ink2">{{ number_format($this->insights['wallet_installed']) }} de
                {{ number_format($this->insights['total_customers']) }} clientes</p>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-px lg:grid-cols-12" style="background: var(--border);">
        <article class="bg-card p-5 md:p-6 lg:col-span-8">
            <div class="flex items-baseline justify-between gap-4">
                <h2 class="text-2xl tracking-tight text-ink">Actividad diaria</h2>
                <p class="text-xs font-semibold uppercase tracking-[0.16em] text-ink2">Sellos entregados · últimos 7
                    días</p>
            </div>
            <div class="mt-6 flex h-52 items-end gap-px border-b-2 border-ink" role="img"
                aria-label="Sellos entregados en los últimos siete días">
                @foreach ($this->activity as $day)
                    <div class="flex h-full min-w-0 flex-1 flex-col items-stretch justify-end">
                        <div class="flex flex-1 items-end justify-center pb-1">
                            <span
                                class="text-xs font-semibold tabular-nums {{ $day['stamps'] > 0 ? 'text-ink' : 'text-ink2/50' }}">{{ $day['stamps'] }}</span>
                        </div>
                        <div class="mx-1 {{ $day['stamps'] > 0 ? 'bg-fidentta-teal' : 'bg-border' }}"
                            style="height: {{ $day['height'] }}%"
                            title="{{ $day['stamps'] }} sellos el {{ $day['label'] }}"></div>
                    </div>
                @endforeach
            </div>
            <div class="flex gap-px pt-2">
                @foreach ($this->activity as $day)
                    <span
                        class="flex-1 text-center text-[11px] font-medium uppercase tracking-wide text-ink2">{{ $day['label'] }}</span>
                @endforeach
            </div>
        </article>

        <article class="bg-ink p-5 text-paper md:p-6 lg:col-span-4">
            <div class="flex items-baseline justify-between gap-4">
                <h2 class="text-2xl tracking-tight">Estado del programa</h2>
                <span class="text-xs font-semibold uppercase tracking-[0.16em] text-paper/60">Configuración</span>
            </div>
            <div class="mt-6 flex items-end gap-4">
                <p class="text-6xl leading-none tracking-tight tabular-nums">{{ $this->program['progress'] }}<span
                        class="text-2xl">%</span></p>
                <p class="pb-1 text-xs font-medium uppercase tracking-[0.16em] text-paper/60">completada</p>
            </div>
            <div class="mt-3 h-1 w-full bg-paper/15" role="img"
                aria-label="Configuración completada al {{ $this->program['progress'] }}%">
                <div class="h-1 bg-fidentta-cyan" style="width: {{ $this->program['progress'] }}%"></div>
            </div>
            <ul class="mt-6 space-y-3 text-sm">
                <li class="flex items-center justify-between border-b border-paper/10 pb-3">
                    <span class="text-paper/70">Locales activos</span>
                    <span class="font-semibold tabular-nums">{{ $this->program['active_locations'] }}</span>
                </li>
                <li class="flex items-center justify-between border-b border-paper/10 pb-3">
                    <span class="text-paper/70">Diseño de tarjeta</span>
                    <span
                        class="font-semibold {{ $this->program['design'] ? 'text-fidentta-cyan' : 'text-paper/50' }}">{{ $this->program['design'] ? 'Activo' : 'Pendiente' }}</span>
                </li>
                <li class="flex items-center justify-between border-b border-paper/10 pb-3">
                    <span class="text-paper/70">Recompensa</span>
                    <span
                        class="max-w-[55%] truncate text-right font-semibold">{{ $this->program['design']?->reward ?? '—' }}</span>
                </li>
            </ul>
            <a href="{{ route('dashboard.locales', ['current_team' => $team]) }}" wire:navigate
                class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-fidentta-cyan underline decoration-2 underline-offset-4 transition hover:text-paper">Gestionar
                locales <span aria-hidden="true">&rarr;</span></a>
        </article>
    </section>

    <section class="grid grid-cols-1 gap-px lg:grid-cols-12" style="background: var(--border);">
        <article class="bg-card p-5 md:p-6 lg:col-span-7">
            <div class="flex items-baseline justify-between gap-4">
                <h2 class="text-2xl tracking-tight text-ink">Actividad reciente</h2>
                <a href="{{ route('dashboard.clientes', ['current_team' => $team]) }}" wire:navigate
                    class="text-sm font-semibold text-ink underline decoration-fidentta-teal decoration-2 underline-offset-4 transition hover:text-fidentta-teal">Ver
                    clientes &rarr;</a>
            </div>
            <ul class="mt-4 divide-y divide-border">
                @forelse ($this->recentTransactions as $transaction)
                    @php
                        $nameValue = $transaction->customer?->registrationValues->first(
                            fn($value) => $value->field?->field_key === 'nombre',
                        )?->value;
                        $customerName =
                            $nameValue ?: ($transaction->customer?->email ?: 'Invitado #' . $transaction->customer_id);
                    @endphp
                    <li class="flex items-center justify-between gap-4 py-3.5">
                        <div class="flex min-w-0 items-center gap-3">
                            <span
                                class="flex h-9 w-9 shrink-0 items-center justify-center bg-mint text-sm font-bold text-ink">{{ strtoupper(substr($customerName, 0, 1)) }}</span>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-ink">{{ $customerName }}</p>
                                <p class="text-xs text-ink2">{{ $transaction->location?->name }} ·
                                    {{ $transaction->created_at?->diffForHumans() }}@if ($transaction->user)
                                        · por {{ $transaction->user->name }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <span
                            class="shrink-0 text-sm font-semibold text-fidentta-teal tabular-nums">+{{ $transaction->stamps_added }}
                            {{ $transaction->stamps_added === 1 ? 'sello' : 'sellos' }}</span>
                    </li>
                    @empty
                        <li class="py-8 text-center text-sm text-ink2">Todavía no hay sellos
                            registrados{{ $locationId !== '' ? ' en este local' : '' }}.</li>
                    @endforelse
                </ul>
            </article>

            <article class="flex flex-col justify-between bg-mint p-5 md:p-6 lg:col-span-5">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-ink">Siguiente mejor acción</p>
                    <h2 class="mt-3 text-3xl leading-tight tracking-tight text-ink">Comparte el QR con tu equipo</h2>
                    <p class="mt-3 max-w-md text-sm leading-6 text-ink/75">Tus empleados podrán reconocer cada visita y
                        entregar sellos sin complicar el proceso.</p>
                </div>
                <a href="{{ route('dashboard.fidelizacion', ['current_team' => $team]) }}" wire:navigate
                    class="mt-8 inline-flex w-fit items-center gap-2 bg-ink px-6 py-3.5 text-sm font-bold text-paper transition hover:opacity-90">Abrir
                    fidelización <span aria-hidden="true">&rarr;</span></a>
            </article>
        </section>

        <section class="grid grid-cols-1 gap-px lg:grid-cols-12" style="background: var(--border);">
            <article class="bg-card p-5 md:p-6 lg:col-span-5">
                <div class="flex items-baseline justify-between gap-4">
                    <h2 class="text-2xl tracking-tight text-ink">Pendientes</h2>
                    <span class="text-xs font-semibold uppercase tracking-[0.16em] text-ink2">
                        {{ count($this->pendingTasks) }}
                        {{ count($this->pendingTasks) === 1 ? 'sugerencia' : 'sugerencias' }}</span>
                </div>
                <ul class="mt-4 divide-y divide-border">
                    @forelse ($this->pendingTasks as $task)
                        <li class="flex items-start justify-between gap-4 py-3.5">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="rounded-sm px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide {{ $task['priority'] === 'Alta' ? 'bg-destructive/10 text-destructive' : ($task['priority'] === 'Media' ? 'bg-accent/15 text-accent-foreground' : 'bg-border text-ink2') }}">{{ $task['priority'] }}</span>
                                    <p class="text-sm font-semibold text-ink">{{ $task['title'] }}</p>
                                </div>
                                <p class="mt-1 text-xs leading-5 text-ink2">{{ $task['detail'] }}</p>
                            </div>
                            <a href="{{ $task['route'] }}" wire:navigate
                                class="shrink-0 text-xs font-semibold text-fidentta-teal underline decoration-2 underline-offset-4 transition hover:text-ink">{{ $task['cta'] }}
                                &rarr;</a>
                        </li>
                    @empty
                        <li class="flex items-center gap-3 py-6">
                            <span
                                class="flex h-8 w-8 items-center justify-center bg-mint text-sm font-bold text-ink">✓</span>
                            <p class="text-sm text-ink2">Todo en orden. El programa está funcionando sin avisos.</p>
                        </li>
                    @endforelse
                </ul>
            </article>

            <article class="bg-card p-5 md:p-6 lg:col-span-4">
                <div class="flex items-baseline justify-between gap-4">
                    <h2 class="text-2xl tracking-tight text-ink">Mejores clientes</h2>
                    <span class="text-xs font-semibold uppercase tracking-[0.16em] text-ink2">Top 5</span>
                </div>
                <ol class="mt-4 divide-y divide-border">
                    @forelse ($this->topCustomers as $index => $customer)
                        <li class="flex items-center justify-between gap-3 py-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <span
                                    class="w-6 text-sm font-bold text-ink2 tabular-nums">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-ink">{{ $customer['name'] }}</p>
                                    <p class="text-xs text-ink2">{{ $customer['location'] }}</p>
                                </div>
                            </div>
                            <span class="shrink-0 text-sm font-semibold text-ink tabular-nums">{{ $customer['stamps'] }}
                                <span class="text-xs font-medium text-ink2">sellos</span></span>
                        </li>
                    @empty
                        <li class="py-8 text-center text-sm text-ink2">Aún no hay clientes con sellos.</li>
                    @endforelse
                </ol>
            </article>

            <article class="bg-card p-5 md:p-6 lg:col-span-3">
                <div class="flex items-baseline justify-between gap-4">
                    <h2 class="text-2xl tracking-tight text-ink">Por local</h2>
                    <span class="text-xs font-semibold uppercase tracking-[0.16em] text-ink2">30 días</span>
                </div>
                @php($maxLocationStamps = max(1, (int) $this->locationBreakdown->max('stamps')))
                <ul class="mt-4 space-y-4">
                    @forelse ($this->locationBreakdown as $location)
                        <li>
                            <div class="flex items-baseline justify-between gap-2">
                                <p class="truncate text-sm font-semibold text-ink">{{ $location['name'] }}</p>
                                <p class="text-xs font-semibold text-ink tabular-nums">{{ $location['stamps'] }} <span
                                        class="font-medium text-ink2">sellos</span></p>
                            </div>
                            <div class="mt-1.5 h-1.5 w-full bg-border">
                                <div class="h-1.5 bg-fidentta-teal"
                                    style="width: {{ (int) round(($location['stamps'] / $maxLocationStamps) * 100) }}%">
                                </div>
                            </div>
                            <p class="mt-1 text-[11px] text-ink2">{{ $location['customers'] }}
                                {{ $location['customers'] === 1 ? 'cliente' : 'clientes' }}</p>
                        </li>
                    @empty
                        <li class="py-8 text-center text-sm text-ink2">Crea un local para ver su rendimiento.</li>
                    @endforelse
                </ul>
                @if ($this->cycleInsight['best_day_label'])
                    <p class="mt-5 border-t border-border pt-4 text-xs leading-5 text-ink2">Tu mejor día reciente:
                        <span class="font-semibold text-ink">{{ $this->cycleInsight['best_day_label'] }}</span> con
                        {{ $this->cycleInsight['best_day_stamps'] }}
                        {{ $this->cycleInsight['best_day_stamps'] === 1 ? 'sello' : 'sellos' }}. Ritmo medio:
                        {{ $this->cycleInsight['pace'] }}/día.
                    </p>
                @endif
            </article>
        </section>

        <section class="grid grid-cols-2 gap-px lg:grid-cols-4" style="background: var(--border);"
            aria-label="Acciones rápidas">
            <a href="{{ route('dashboard.clientes', ['current_team' => $team]) }}" wire:navigate
                class="group flex flex-col justify-between bg-card p-5 transition hover:bg-mint/40">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">Operación diaria</p>
                <div class="mt-4 flex items-center justify-between gap-2">
                    <span class="text-xl tracking-tight text-ink">Dar un sello</span>
                    <span class="text-ink transition group-hover:translate-x-1" aria-hidden="true">&rarr;</span>
                </div>
            </a>
            <a href="{{ route('dashboard.fidelizacion', ['current_team' => $team]) }}" wire:navigate
                class="group flex flex-col justify-between bg-card p-5 transition hover:bg-mint/40">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">Captación</p>
                <div class="mt-4 flex items-center justify-between gap-2">
                    <span class="text-xl tracking-tight text-ink">Ver QR del local</span>
                    <span class="text-ink transition group-hover:translate-x-1" aria-hidden="true">&rarr;</span>
                </div>
            </a>
            <a href="{{ route('dashboard.tarjeta', ['current_team' => $team]) }}" wire:navigate
                class="group flex flex-col justify-between bg-card p-5 transition hover:bg-mint/40">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">Programa</p>
                <div class="mt-4 flex items-center justify-between gap-2">
                    <span class="text-xl tracking-tight text-ink">Editar tarjeta</span>
                    <span class="text-ink transition group-hover:translate-x-1" aria-hidden="true">&rarr;</span>
                </div>
            </a>
            <a href="{{ route('dashboard.locales', ['current_team' => $team]) }}" wire:navigate
                class="group flex flex-col justify-between bg-card p-5 transition hover:bg-mint/40">
                <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-ink2">Presencia</p>
                <div class="mt-4 flex items-center justify-between gap-2">
                    <span class="text-xl tracking-tight text-ink">Añadir local</span>
                    <span class="text-ink transition group-hover:translate-x-1" aria-hidden="true">&rarr;</span>
                </div>
            </a>
        </section>
    </div>
