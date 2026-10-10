<x-layouts::auth.simple :title="$team->name">
    <div class=" max-w-xl mx-auto rounded-xl border border-border bg-card p-6 shadow-sm sm:p-8">
        <p class="text-sm font-semibold text-brand">{{ $location->name }}</p>
        <h1 class="mt-2 text-2xl font-semibold text-ink">Únete a {{ $team->name }}</h1>
        <p class="mt-2 text-sm leading-6 text-muted-foreground">Registra tu tarjeta de fidelidad para empezar a acumular
            sellos.</p>

        <form method="POST" action="{{ route('wallet.card.store', ['qr_token' => $location->qr_token]) }}"
            class="mt-6 space-y-4">
            @csrf

            @if ($mode === 'normal')
                <div>
                    <label for="email" class="mb-1.5 block text-sm font-medium text-ink">Correo electrónico</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required
                        autocomplete="email"
                        class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20">
                    @error('email')
                        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="mb-1.5 block text-sm font-medium text-ink">Contraseña</label>
                    <input id="password" name="password" type="password" required minlength="8"
                        autocomplete="new-password"
                        class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20">
                    @error('password')
                        <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-ink">Confirma la
                        contraseña</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                        minlength="8" autocomplete="new-password"
                        class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20">
                </div>
                @if ($fields->contains('field_key', 'telefono'))
                    <div>
                        <label for="telefono" class="mb-1.5 block text-sm font-medium text-ink">Teléfono
                            (opcional)</label>
                        <input id="telefono" name="telefono" type="tel" value="{{ old('telefono') }}"
                            autocomplete="tel"
                            class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20">
                    </div>
                @endif
            @elseif ($mode === 'custom')
                @foreach ($fields as $field)
                    <div>
                        <label for="field-{{ $field->id }}" class="mb-1.5 block text-sm font-medium text-ink">
                            {{ $field->label }}{{ $field->is_required ? '' : ' (opcional)' }}
                        </label>
                        <input id="field-{{ $field->id }}" name="fields[{{ $field->id }}]"
                            type="{{ $field->type }}" value="{{ old('fields.' . $field->id) }}"
                            {{ $field->is_required ? 'required' : '' }}
                            class="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm text-ink focus:border-brand focus:outline-none focus:ring-2 focus:ring-brand/20">
                        @error('fields.' . $field->id)
                            <p class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            @else
                <p class="rounded-lg bg-brand-soft p-4 text-sm leading-6 text-ink">No necesitas compartir datos
                    personales. Tu tarjeta se creará como invitado.</p>
            @endif

            <button type="submit"
                class="w-full rounded-lg bg-brand px-4 py-3 text-sm font-semibold text-white transition hover:bg-brand-700">
                {{ $mode === 'none' ? 'Crear mi tarjeta' : 'Registrarme y crear tarjeta' }}
            </button>
        </form>
    </div>
</x-layouts::auth.simple>
