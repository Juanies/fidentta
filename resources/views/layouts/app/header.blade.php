<!DOCTYPE html>
<html lang="{{ request()->routeIs('home') ? 'es' : str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="">
    @if (request()->routeIs('dashboard'))
        <header class="flex p-4 justify-between items-center max-w-7xl mx-auto">
            <a href="/" class="text-lg">Fidentta</a>
            <nav>
                <a href="">Inicio</a>
            </nav>
        </header>
    @endif
    <main class="m-0 p-0">
        {{ $slot }}
    </main>
    <footer></footer>

    @include('partials.chatwoot')
</body>

</html>
