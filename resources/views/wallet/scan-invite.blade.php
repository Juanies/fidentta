<x-layouts::auth.simple :title="$team->name">
    <div class="w-full max-w-xl rounded-xl border border-border bg-card p-6 text-center shadow-sm sm:p-8">
        <p class="text-sm font-semibold text-brand">{{ $team->name }}</p>
        <h1 class="mt-2 text-2xl font-semibold text-ink">Te han invitado a su tarjeta de fidelidad</h1>
        <p class="mt-2 text-sm leading-6 text-muted-foreground">
            @if ($team->customer_registration_type === 'none')
                Este negocio no pide registro, así que esta invitación no da sellos. Los referidos solo suman
                sellos en negocios con registro de clientes.
            @else
                Añade tu tarjeta a tu wallet y los dos conseguiréis un sello gratis.
            @endif
        </p>
        <a href="{{ $registerUrl }}"
            class="mt-6 inline-flex w-full items-center justify-center rounded-lg bg-ink px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-700">
            @if ($team->customer_registration_type === 'none')
                Obtener mi tarjeta
            @else
                Obtener mi tarjeta y ganar un sello
            @endif
        </a>
        <p class="mt-6 text-xs text-muted-foreground">
            ¿Eres del equipo de {{ $team->name }}?
            <a href="{{ route('login') }}" class="font-semibold text-brand underline">Inicia sesión</a>
            para añadir un sello a esta tarjeta.
        </p>
    </div>
</x-layouts::auth.simple>
