<?php

use App\Concerns\ProfileValidationRules;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Configuración del perfil')] class extends Component {
    use ProfileValidationRules;

    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate($this->profileRules($user->id));

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        Flux::toast(variant: 'success', text: __('Perfil actualizado.'));
    }
}; ?>

<section class="w-full text-ink"
    style="--color-accent: var(--mint); --color-accent-content: var(--ink); --color-accent-foreground: var(--ink);">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Configuración del perfil') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Tu perfil')" :subheading="__('Actualiza tu nombre y correo electrónico')">
        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <div class="flex items-center gap-4 border-b border-border pb-5">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center bg-mint text-lg font-bold text-ink"
                    aria-hidden="true">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-ink">{{ auth()->user()->name }}</p>
                    <p class="mt-1 truncate text-xs text-ink2">{{ auth()->user()->email }}</p>
                </div>
            </div>

            <div class="space-y-5">
                <flux:input wire:model="name" :label="__('Nombre')" type="text" required autofocus
                    autocomplete="name" />
                <flux:input wire:model="email" :label="__('Correo electrónico')" type="email" required
                    autocomplete="email" />
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-border pt-5">
                <p class="text-xs text-ink2">{{ __('Los datos de tu cuenta son privados.') }}</p>
                <flux:button variant="primary" type="submit" data-test="update-profile-button">
                    {{ __('Guardar cambios') }}
                </flux:button>
            </div>
        </form>

        <livewire:pages::settings.delete-user-form />
    </x-pages::settings.layout>
</section>
