<?php

use Livewire\Component;
use Livewire\Attributes\Title;

new #[Title('Apariencia')] class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading level="2" class="sr-only">{{ __('Apariencia') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Tema de la aplicación')" :subheading="__('La aplicación utiliza siempre el tema claro')">
        <div class="flex items-center gap-3 border border-border bg-card p-5 sm:p-6">
            <flux:icon name="sun" class="size-5 text-amber-600" />
            <p class="text-sm font-semibold text-ink">{{ __('Tema claro activado') }}</p>
        </div>
    </x-pages::settings.layout>
</section>
