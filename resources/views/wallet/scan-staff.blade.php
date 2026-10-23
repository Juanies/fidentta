<x-layouts::auth.simple :title="$team->name">
    <div class="w-full max-w-xl rounded-xl border border-border bg-card p-6 text-center shadow-sm sm:p-8">
        <p class="text-sm font-semibold text-brand">{{ $team->name }}</p>
        <h1 class="mt-2 text-2xl font-semibold text-ink">Tarjeta de cliente</h1>
        @if (session('status'))
            <p role="status"
                class="mt-4 rounded-lg border border-green-300 bg-green-50 p-3 text-sm font-medium text-green-800">
                {{ session('status') }}</p>
        @endif
        <div class="mt-6 rounded-lg bg-background p-4 text-left">
            <p class="text-xs font-medium uppercase text-muted-foreground">Sellos actuales</p>
            <p class="mt-1 text-2xl font-semibold text-ink">{{ $card->stamps_collected }} / {{ $required }}</p>
            @if ($card->customer->email)
                <p class="mt-1 break-all text-xs text-muted-foreground">{{ $card->customer->email }}</p>
            @endif
        </div>
        <form method="POST" action="{{ route('wallet.scan.stamp', $card->referral_token) }}" class="mt-6">
            @csrf
            <input type="hidden" name="request_key" value="{{ $requestKey }}">
            <button type="submit"
                class="inline-flex w-full items-center justify-center rounded-lg bg-brand px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-700">
                Añadir 1 sello
            </button>
        </form>
    </div>
</x-layouts::auth.simple>
