@if (request()->routeIs('*dashboard*', 'locations.index'))
    <x-layouts::app.sidebar :title="$title ?? null">
        {{ $slot }}
    </x-layouts::app.sidebar>
@else
    <x-layouts::app.header :title="$title ?? null">
        {{ $slot }}
    </x-layouts::app.header>
@endif
