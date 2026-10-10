<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-background text-ink antialiased">
    <div class="min-h-svh lg:grid lg:grid-cols-2">
        <aside
            class="relative hidden min-h-svh overflow-hidden bg-ink px-10 py-10 text-white lg:flex lg:flex-col xl:px-16">
            <div class="pointer-events-none absolute -right-20 top-24 size-[28rem] rotate-[28deg] border border-mint/20"
                aria-hidden="true"></div>
            <div class="pointer-events-none absolute right-24 top-48 size-28 rotate-12 bg-mint/15" aria-hidden="true">
            </div>
            <div class="pointer-events-none absolute bottom-28 right-16 size-16 bg-[#f2a38b] [clip-path:polygon(50%_0,100%_100%,0_100%)]"
                aria-hidden="true"></div>

            <a href="{{ route('home') }}" class="relative z-10 flex w-fit items-center gap-3" wire:navigate>
                <span class="flex size-11 items-center justify-center rounded-lg bg-white p-1.5">
                    <img src="{{ asset('logo.png') }}" alt="" class="size-full object-contain">
                </span>
                <span class="text-xl font-bold tracking-tight">Fidentta<span class="text-mint">.</span></span>
            </a>

            <div class="relative z-10 my-auto max-w-xl py-16">
                <p class="mb-5 flex items-center gap-3 text-sm font-semibold text-mint">
                    <span class="h-px w-8 bg-mint"></span> Fidelización para negocios locales
                </p>
                <h1 class="font-display text-5xl leading-[1.02] xl:text-6xl">Cada visita puede ser el comienzo.</h1>
                <p class="mt-6 max-w-lg text-base leading-7 text-white/70">Tu programa, tus clientes y cada recompensa,
                    reunidos en un espacio sencillo.</p>

                <div class="mt-10 max-w-md border border-white/15 bg-white/5 p-5">
                    <div class="flex items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-mint">Club de la casa</p>
                            <p class="mt-1 text-lg font-semibold">Tu café de siempre</p>
                        </div>
                        <span class="flex size-10 items-center justify-center rounded-md bg-mint text-ink">
                            <flux:icon name="gift" class="size-5" />
                        </span>
                    </div>
                    <div class="mt-5 grid grid-cols-8 gap-2" aria-label="Progreso de ejemplo: seis de ocho sellos">
                        @for ($stamp = 0; $stamp < 8; $stamp++)
                            <span
                                class="aspect-square rounded-full border {{ $stamp < 6 ? 'border-mint bg-mint' : 'border-white/30 bg-white/5' }}"></span>
                        @endfor
                    </div>
                    <p class="mt-4 border-t border-white/10 pt-4 text-sm text-white/70">Un motivo más para volver.</p>
                </div>
            </div>

            <p class="relative z-10 text-xs text-white/45">Fidelidad que se siente cercana.</p>
        </aside>

        <main class="flex min-h-svh items-center justify-center px-5 py-8 sm:px-8 lg:px-12">
            <div class="w-full max-w-md">
                <a href="{{ route('home') }}" class="mb-10 flex w-fit items-center gap-3 lg:hidden" wire:navigate>
                    <span class="flex size-10 items-center justify-center rounded-md bg-white shadow-soft">
                        <img src="{{ asset('logo.png') }}" alt="" class="size-8 object-contain">
                    </span>
                    <span class="text-lg font-bold tracking-tight">Fidentta<span class="text-mintd">.</span></span>
                </a>
                {{ $slot }}
            </div>
        </main>
    </div>

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>
