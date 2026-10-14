<?php

use App\Actions\Teams\CreateTeam;
use App\Data\UserTeam;
use App\Models\Team;
use App\Rules\TeamName;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Equipos')] class extends Component {
    public string $name = '';

    public function createTeam(CreateTeam $createTeam): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', new TeamName()],
        ]);

        $team = $createTeam->handle(Auth::user(), $validated['name']);

        $this->dispatch('close-modal', name: 'create-team');

        $this->reset('name');

        Flux::toast(variant: 'success', text: __('Equipo creado.'));

        $this->redirectRoute('teams.edit', ['team' => $team->slug], navigate: true);
    }

    public function leaveTeam(int $teamId): void
    {
        $team = Team::findOrFail($teamId);
        $user = Auth::user();

        Gate::authorize('leave', $team);

        $fallbackTeam = $user->isCurrentTeam($team) ? $user->fallbackTeam($team) : null;

        $team->memberships()->where('user_id', $user->id)->delete();

        if ($fallbackTeam) {
            $user->switchTeam($fallbackTeam);
        }

        $this->dispatch('close-modal', name: "leave-team-{$teamId}");

        Flux::toast(variant: 'success', text: __('Has salido del equipo ":name"', ['name' => $team->name]));

        $this->redirectRoute('teams.index', navigate: true);
    }

    /**
     * @return Collection<int, UserTeam>
     */
    #[Computed]
    public function teams(): Collection
    {
        return Auth::user()->toUserTeams(includeCurrent: true);
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Equipos') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Tus equipos')" :subheading="__('Administra los equipos y las personas que colaboran contigo')">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-4 border-b border-border pb-5">
            <p class="max-w-sm text-sm leading-6 text-ink2">Crea un espacio de trabajo o gestiona los equipos a los que
                perteneces.</p>
            <flux:modal.trigger name="create-team">
                <flux:button variant="primary" icon="plus" x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'create-team')" data-test="teams-new-team-button">
                    {{ __('Crear equipo') }}
                </flux:button>
            </flux:modal.trigger>
        </div>

        <div class="mt-6 divide-y divide-border border-y border-border">
            @forelse ($this->teams as $team)
                <div class="flex items-center justify-between gap-4 bg-card p-4 sm:px-5" data-test="team-row">
                    <div class="flex items-center gap-4">
                        <span class="flex size-10 shrink-0 items-center justify-center bg-mint/20 text-ink"
                            aria-hidden="true">
                            <flux:icon name="building-office-2" />
                        </span>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="font-medium">{{ $team->name }}</span>
                                @if ($team->isPersonal)
                                    <flux:badge color="lime">{{ __('Personal') }}</flux:badge>
                                @endif
                            </div>
                            <flux:text class="text-sm text-ink2">{{ $team->roleLabel }}</flux:text>
                        </div>
                    </div>

                    <div class="flex items-center gap-1">
                        @if (!$team->isPersonal && $team->role !== 'owner')
                            <flux:modal.trigger :name="'leave-team-'.$team->id">
                                <flux:tooltip :content="__('Salir del equipo')">
                                    <flux:button variant="ghost" size="sm" icon="arrow-right-start-on-rectangle"
                                        x-data=""
                                        x-on:click.prevent="$dispatch('open-modal', 'leave-team-{{ $team->id }}')"
                                        data-test="team-leave-button" />
                                </flux:tooltip>
                            </flux:modal.trigger>
                        @endif

                        <flux:tooltip :content="$team->role === 'member' ? __('Ver equipo') : __('Editar equipo')">
                            <flux:button variant="ghost" size="sm"
                                :icon="$team->role === 'member' ? 'eye' : 'pencil'"
                                :href="route('teams.edit', $team->slug)" wire:navigate
                                :data-test="$team->role === 'member' ? 'team-view-button' : 'team-edit-button'" />
                        </flux:tooltip>
                    </div>
                </div>

                @if (!$team->isPersonal && $team->role !== 'owner')
                    <flux:modal :name="'leave-team-'.$team->id" focusable class="max-w-lg">
                        <form wire:submit="leaveTeam({{ $team->id }})" class="space-y-6">
                            <div>
                                <flux:heading size="lg">{{ __('Salir del equipo') }}</flux:heading>
                                <flux:subheading>
                                    {{ __('¿Seguro que quieres salir de :name?', ['name' => $team->name]) }}
                                </flux:subheading>
                            </div>

                            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                                <flux:modal.close>
                                    <flux:button variant="filled">{{ __('Cancelar') }}</flux:button>
                                </flux:modal.close>

                                <flux:button variant="danger" type="submit" data-test="leave-team-confirm">
                                    {{ __('Salir del equipo') }}
                                </flux:button>
                            </div>
                        </form>
                    </flux:modal>
                @endif
            @empty
                <flux:text class="py-8 text-center text-ink2">
                    {{ __('Todavía no perteneces a ningún equipo.') }}
                </flux:text>
            @endforelse
        </div>
    </x-pages::settings.layout>

    <flux:modal name="create-team" :show="$errors->isNotEmpty()" focusable class="max-w-lg">
        <form wire:submit="createTeam" class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Crear un equipo') }}</flux:heading>
                <flux:subheading>{{ __('Ponle un nombre para empezar a colaborar.') }}</flux:subheading>
            </div>

            <flux:input wire:model="name" :label="__('Nombre del equipo')" type="text" required autofocus
                data-test="create-team-name" />

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Cancelar') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="primary" type="submit" wire:loading.attr="disabled" wire:target="createTeam"
                    data-test="create-team-submit">
                    <span wire:loading.remove wire:target="createTeam">{{ __('Crear equipo') }}</span>
                    <span wire:loading wire:target="createTeam">{{ __('Creando equipo...') }}</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>
</section>
