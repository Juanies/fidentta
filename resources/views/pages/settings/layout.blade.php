<div class="flex items-start gap-8 max-md:flex-col">
    <div class="w-full border-b border-border pb-4 md:w-55 md:border-b-0 md:border-e md:pe-6">
        <flux:navlist aria-label="{{ __('Secciones de configuración') }}" class="gap-1">
            <flux:navlist.item :href="route('profile.edit')" :current="request()->routeIs('profile.edit')"
                class="text-ink2 hover:bg-mint/15 hover:text-ink data-current:bg-mint/20 data-current:text-ink"
                wire:navigate>{{ __('Perfil') }}</flux:navlist.item>
            <flux:navlist.item :href="route('security.edit')" :current="request()->routeIs('security.edit')"
                class="text-ink2 hover:bg-mint/15 hover:text-ink data-current:bg-mint/20 data-current:text-ink"
                wire:navigate>{{ __('Seguridad') }}</flux:navlist.item>
            <flux:navlist.item :href="route('teams.index')" :current="request()->routeIs('teams.*')"
                class="text-ink2 hover:bg-mint/15 hover:text-ink data-current:bg-mint/20 data-current:text-ink"
                wire:navigate>{{ __('Equipos') }}</flux:navlist.item>
            <flux:navlist.item :href="route('appearance.edit')" :current="request()->routeIs('appearance.edit')"
                class="text-ink2 hover:bg-mint/15 hover:text-ink data-current:bg-mint/20 data-current:text-ink"
                wire:navigate>{{ __('Apariencia') }}</flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="min-w-0 flex-1 self-stretch max-md:pt-2">
        <flux:heading class="text-ink">{{ $heading ?? '' }}</flux:heading>
        <flux:subheading class="text-ink2">{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full max-w-xl">
            {{ $slot }}
        </div>
    </div>
</div>
