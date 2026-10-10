<x-layouts::auth.simple :title="$pageTitle">
    <div class="min-h-screen bg-background text-ink">
        <header class="flex items-center justify-between border-b border-border px-5 py-5 sm:px-10">
            <span class="text-xs font-bold uppercase tracking-[0.18em] text-ink2">FIDENTTA</span>
            <span class="text-sm font-semibold">{{ $location->name }}</span>
        </header>
        <main class="mx-auto max-w-3xl px-5 pb-16 pt-12 sm:px-10 sm:pt-20">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-fidentta-teal">{{ $team->name }}</p>
            <h1 class="mt-3 max-w-2xl text-4xl leading-tight text-ink sm:text-5xl">{{ $pageTitle }}</h1>
            <p class="mt-4 max-w-xl text-base leading-7 text-ink2">Únete al programa de fidelidad y convierte cada visita
                en una recompensa.</p>
            <a href="{{ route('wallet.card', ['qr_token' => $location->qr_token]) }}"
                class="mt-8 inline-flex min-h-14 items-center justify-between gap-8 bg-ink px-5 py-4 text-sm font-bold text-paper transition hover:bg-fidentta-teal hover:text-ink">
                Crear mi tarjeta <span aria-hidden="true">&rarr;</span>
            </a>

            @if ($location->businessPageLinks->isNotEmpty())
                <section class="mt-16 border-t border-border pt-6">
                    <h2 class="text-xs font-bold uppercase tracking-[0.16em] text-ink2">También estamos aquí</h2>
                    <ul class="mt-4 divide-y divide-border">
                        @foreach ($location->businessPageLinks as $link)
                            @php
                                $icon = match ($link->icon) {
                                    'instagram' => '◎',
                                    'facebook' => 'f',
                                    'tiktok' => '♪',
                                    'whatsapp' => '◉',
                                    'map' => '⌖',
                                    default => '↗',
                                };
                            @endphp
                            <li>
                                <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                                    class="flex items-center gap-4 py-4 text-sm font-semibold text-ink transition hover:text-fidentta-teal">
                                    <span aria-hidden="true"
                                        class="flex h-10 w-10 items-center justify-center border border-border text-lg">{{ $icon }}</span>
                                    <span class="min-w-0 flex-1 truncate">{{ $link->name }}</span>
                                    <span aria-hidden="true" class="text-ink2">↗</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
            @if ($location->address)
                <footer class="mt-14 border-t border-border pt-5 text-sm text-ink2">{{ $location->address }}</footer>
            @endif
        </main>
    </div>
</x-layouts::auth.simple>
