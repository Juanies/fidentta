<x-layouts::auth.simple :title="$team->name">
    <div class="w-full max-w-xl rounded-xl border border-border bg-card p-6 text-center shadow-sm sm:p-8">
        <span
            class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100 text-xl font-bold text-green-800"
            aria-hidden="true">✓</span>
        <p class="mt-5 text-sm font-semibold text-brand">{{ $location->name }}</p>
        <h1 class="mt-2 text-2xl font-semibold text-ink">Tu tarjeta está lista</h1>
        <p class="mt-2 text-sm leading-6 text-muted-foreground">Ya puedes empezar a acumular sellos en
            {{ $team->name }}.</p>
        <div class="mt-6 rounded-lg bg-background p-4 text-left">
            <p class="text-xs font-medium uppercase text-muted-foreground">Sellos actuales</p>
            <p class="mt-1 text-2xl font-semibold text-ink">{{ $card->stamps_collected }}</p>
        </div>
        @forelse ($walletUrls as $platform => $walletUrl)
            <a href="{{ $walletUrl }}"
                class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-ink px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-5"
                    aria-hidden="true">
                    <path
                        d="M12 2a1 1 0 0 1 1 1v9.59l2.3-2.3a1 1 0 1 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-4-4a1 1 0 1 1 1.4-1.42l2.3 2.3V3a1 1 0 0 1 1-1ZM5 18a1 1 0 0 1 1 1v1h12v-1a1 1 0 1 1 2 0v2a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-2a1 1 0 0 1 1-1Z" />
                </svg>
                Añadir a {{ $platform === 'apple' ? 'Apple Wallet' : 'Google Wallet' }}
            </a>
        @empty
            <p
                class="mt-5 rounded-lg border border-amber-300 bg-amber-50 p-3 text-left text-sm leading-5 text-amber-900">
                @if ($appleWalletNeedsPublicHttps)
                    El registro está guardado. Apple Wallet necesita que la app use una URL pública con HTTPS y que
                    abras este enlace desde Safari en un iPhone. `localhost` no es accesible desde el teléfono.
                @else
                    Tu registro está guardado, pero no se pudo preparar el pase. El equipo del local puede revisar la
                    configuración de sus wallets.
                @endif
            </p>
        @endforelse
    </div>
</x-layouts::auth.simple>
