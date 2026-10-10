<?php

use App\Concerns\PasswordValidationRules;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Seguridad de la cuenta')] class extends Component {
    use PasswordValidationRules;

    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Mount the component.
     */
    public function mount(DisableTwoFactorAuthentication $disableTwoFactorAuthentication): void {}

    /**
     * Update the password for the currently authenticated user.
     */
    public function updatePassword(): void
    {
        try {
            $validated = $this->validate([
                'current_password' => $this->currentPasswordRules(),
                'password' => $this->passwordRules(),
            ]);
        } catch (ValidationException $e) {
            $this->reset('current_password', 'password', 'password_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        $this->reset('current_password', 'password', 'password_confirmation');

        Flux::toast(variant: 'success', text: __('Contraseña actualizada.'));
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Seguridad de la cuenta') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Cambiar contraseña')" :subheading="__('Usa una contraseña segura que no utilices en otros sitios')">
        <form method="POST" wire:submit="updatePassword" class="mt-6 space-y-6 border border-border bg-card p-5 sm:p-6">
            <flux:input wire:model="current_password" :label="__('Contraseña actual')" type="password" required
                autocomplete="current-password" viewable />
            <flux:input wire:model="password" :label="__('Nueva contraseña')" type="password" required
                autocomplete="new-password"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable />
            <flux:input wire:model="password_confirmation" :label="__('Confirmar contraseña')" type="password" required
                autocomplete="new-password"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable />

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit" data-test="update-password-button">
                    {{ __('Guardar contraseña') }}
                </flux:button>
            </div>
        </form>


    </x-pages::settings.layout>

</section>
