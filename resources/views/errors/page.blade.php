<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $status }} · {{ $heading }} · Fidentta</title>
    <meta name="description" content="{{ $description }}">
    <meta name="robots" content="noindex, nofollow">
    @fonts
    @vite(['resources/css/app.css'])
    @fluxAppearance
</head>

<body class="min-h-svh bg-[#f3f6f4] text-ink antialiased">
    <div class="mx-auto flex min-h-svh max-w-6xl flex-col px-5 py-7 sm:px-8 lg:px-10">
        <a href="{{ route('home') }}" class="flex w-fit items-center gap-3" aria-label="Fidentta, inicio">
            <span class="flex size-10 items-center justify-center rounded-md bg-white p-1 shadow-sm">
                <img src="{{ asset('logo.png') }}" alt="" class="size-full object-contain">
            </span>
            <span class="text-lg font-bold">Fidentta<span class="text-mintd">.</span></span>
        </a>

        <main class="my-auto py-20">
            <div class="max-w-2xl">
                <p class="flex items-center gap-3 text-sm font-semibold text-mintd">
                    <span class="h-px w-8 bg-mintd" aria-hidden="true"></span>
                    Error {{ $status }}
                </p>
                <h1 class="mt-5 font-display text-4xl leading-tight sm:text-5xl">{{ $heading }}</h1>
                <p class="mt-5 max-w-xl text-base leading-7 text-ink2 sm:text-lg">{{ $description }}</p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center justify-center gap-3 rounded-md bg-ink px-5 py-3 font-semibold text-white transition hover:bg-mint hover:text-ink">
                        Ir al inicio <span aria-hidden="true">&rarr;</span>
                    </a>
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center justify-center rounded-md border border-border bg-white px-5 py-3 font-semibold text-ink transition hover:border-ink">
                        Iniciar sesión
                    </a>
                </div>
            </div>
        </main>

        <footer class="border-t border-border pt-5 text-sm text-ink2">
            <p>&copy; {{ date('Y') }} Fidentta · Fidelidad digital para negocios locales.</p>
        </footer>
    </div>
    @include('partials.chatwoot')
</body>

</html>
