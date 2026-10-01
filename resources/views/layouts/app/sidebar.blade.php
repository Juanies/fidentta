<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="flex min-h-screen gap-2 ">
    <flux:sidebar sticky collapsible="mobile" class="border-e  bg-ink">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
        </flux:sidebar.header>

        <livewire:team-switcher />

        <flux:sidebar.nav class="">
            <flux:sidebar.group :heading="__('Platform')" class="grid text-ink">
                <flux:sidebar.item icon="home"
                    :href="route('dashboard', ['current_team' => request()->route('current_team') ?? auth()->user()->currentTeam?->slug])"
                    :current="request()->routeIs('dashboard')"
                    class="text-ink hover:bg-brand-soft hover:text-brand data-current:bg-brand-soft data-current:text-brand"
                    wire:navigate>
                    {{ __('Inicio') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="users"
                    :href="route('dashboard.clientes', ['current_team' => request()->route('current_team') ?? auth()->user()->currentTeam?->slug])"
                    :current="request()->routeIs('dashboard.clientes')"
                    class="text-ink hover:bg-brand-soft hover:text-brand data-current:bg-brand-soft data-current:text-brand"
                    wire:navigate>
                    {{ __('Clientes') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="gift"
                    :href="route('dashboard.fidelizacion', ['current_team' => request()->route('current_team') ?? auth()->user()->currentTeam?->slug])"
                    :current="request()->routeIs('dashboard.fidelizacion')"
                    class="text-ink hover:bg-brand-soft hover:text-brand data-current:bg-brand-soft data-current:text-brand"
                    wire:navigate>
                    {{ __('Fidelizacion') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="credit-card"
                    :href="route('dashboard.tarjeta', ['current_team' => request()->route('current_team') ?? auth()->user()->currentTeam?->slug])"
                    :current="request()->routeIs('dashboard.tarjeta')"
                    class="text-ink hover:bg-brand-soft hover:text-brand data-current:bg-brand-soft data-current:text-brand"
                    wire:navigate>
                    {{ __('Tarjeta') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="cog"
                    :href="route('dashboard.configuracion', ['current_team' => request()->route('current_team') ?? auth()->user()->currentTeam?->slug])"
                    :current="request()->routeIs('dashboard.configuracion')"
                    class="text-ink hover:bg-brand-soft hover:text-brand data-current:bg-brand-soft data-current:text-brand"
                    wire:navigate>
                    {{ __('Configuracion') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="building-storefront" aria-hidden="true"
                    :href="route('dashboard.locales', ['current_team' => request()->route('current_team') ?? auth()->user()->currentTeam?->slug])"
                    :current="request()->routeIs('dashboard.locales', 'locations.index')"
                    class="text-ink hover:bg-brand-soft hover:text-brand data-current:bg-brand-soft data-current:text-brand"
                    wire:navigate>
                    {{ __('Locales') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>
        <flux:spacer />



            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm text-red-400 font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    <livewire:create-team-modal />

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>
