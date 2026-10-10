@php
    $userAgent = strtolower(request()->userAgent() ?? '');

    $isApple =
        str_contains($userAgent, 'iphone') || str_contains($userAgent, 'ipad') || str_contains($userAgent, 'ipod');

    $isAndroid = str_contains($userAgent, 'android');
@endphp


<x-layouts::auth.simple :title="$team->name">
    <div class="max-w-xl flex flex-col gap-4 mx-auto rounded-xl border border-border bg-card p-6 shadow-sm sm:p-8">

        <p class="text-sm font-semibold text-brand">{{ $location->name }}</p>
        <h1 class="mt-2 text-2xl font-semibold text-ink">{{ $team->name }}</h1>


        <p>Añade tu tarjeta a la wallet y consigue recompensas</p>

        {{-- Apple --}}
        @if ($isApple)
            <a  href="{{ route('wallet.card', ['qr_token' => $location->qr_token]) }}"
                class="flex w-full items-center justify-center gap-3 rounded-xl
              bg-black px-4 py-4 text-sm font-semibold text-white
              transition hover:bg-gray-800">
                <span>Añadir a Apple Wallet</span>
            </a>

            {{-- Android --}}
        @elseif ($isAndroid)
            <a  href="{{ route('wallet.card', ['qr_token' => $location->qr_token]) }}"
                class="flex w-full items-center justify-center gap-3 rounded-xl
              border border-border bg-card px-4 py-4 text-sm
              font-semibold text-ink transition hover:bg-brand-soft">
                <span>Añadir a Google Wallet</span>
            </a>

            {{-- Ordenador u otros dispositivos --}}
        @else
            <div class="space-y-3">
            <a  href="{{ route('wallet.card', ['qr_token' => $location->qr_token]) }}"
                    class="flex w-full items-center justify-center rounded-xl
                  bg-black px-4 py-4 text-sm font-semibold text-white">
                    Añadir a Apple Wallet
                </a>

            <a  href="{{ route('wallet.card', ['qr_token' => $location->qr_token]) }}"
                    class="flex w-full items-center justify-center rounded-xl
                  border border-border bg-card px-4 py-4 text-sm
                  font-semibold text-ink">
                    Añadir a Google Wallet
                </a>
            </div>
        @endif
    </div>
</x-layouts::auth.simple>
