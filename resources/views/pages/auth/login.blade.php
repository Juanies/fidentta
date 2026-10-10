<x-layouts::auth.split :title="__('Iniciar sesión')">
    <div class="flex flex-col gap-6">
        <div>
            <p class="text-sm font-semibold text-mintd">Bienvenido de nuevo</p>
            <h2 class="mt-2 font-display text-4xl leading-tight text-ink">Inicia sesión</h2>
            <p class="mt-2 text-sm leading-6 text-ink2">Accede a tu espacio de Fidentta y gestiona tu negocio.</p>
        </div>

        <x-auth-session-status class="text-center" :status="session('status')" />

        @if ($teamInvitation)
            <x-team-invitation-alert :invitation="$teamInvitation" :action="__('Inicia sesión')" />
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5">
            @csrf

            <flux:input name="email" :label="__('Correo electrónico')" :value="old('email')" type="email" required
                autofocus autocomplete="email" placeholder="tu@negocio.com" />

            <div class="relative">
                <flux:input name="password" :label="__('Contraseña')" type="password" required
                    autocomplete="current-password" :placeholder="__('Tu contraseña')" viewable />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-xs font-medium text-mintd inset-e-0"
                        :href="route('password.request')" wire:navigate>
                        {{ __('¿Olvidaste tu contraseña?') }}
                    </flux:link>
                @endif
            </div>

            <flux:checkbox name="remember" :label="__('Mantener sesión iniciada')" :checked="old('remember')" />

            <div class="pt-1">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Entrar a mi cuenta') }}
                </flux:button>
            </div>
        </form>

        <div class="flex items-center gap-3 text-xs text-ink2">
            <span class="h-px flex-1 bg-border"></span>
            <span>{{ __('O continúa con') }}</span>
            <span class="h-px flex-1 bg-border"></span>
        </div>

        <a href="{{ route('google.redirect') }}"
            class="flex w-full items-center justify-center gap-3 rounded-md border border-border bg-white px-4 py-3 text-sm font-semibold text-ink transition hover:border-mintd hover:bg-mint/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-mintd focus-visible:ring-offset-2">
            <svg class="size-5" viewBox="0 0 24 24" aria-hidden="true">
                <path fill="#4285F4"
                    d="M21.35 12.23c0-.79-.07-1.55-.22-2.27H12v4.3h5.24a4.48 4.48 0 0 1-1.94 2.94v2.45h3.14c1.84-1.69 2.91-4.18 2.91-7.42Z" />
                <path fill="#34A853"
                    d="M12 21.5c2.63 0 4.84-.87 6.45-2.35l-3.14-2.45c-.87.58-1.98.92-3.31.92-2.54 0-4.7-1.72-5.47-4.03H3.29v2.53A9.74 9.74 0 0 0 12 21.5Z" />
                <path fill="#FBBC05"
                    d="M6.53 13.59A5.86 5.86 0 0 1 6.22 12c0-.55.1-1.09.31-1.59V7.88H3.29A9.5 9.5 0 0 0 2.25 12c0 1.52.36 2.95 1.04 4.12l3.24-2.53Z" />
                <path fill="#EA4335"
                    d="M12 6.38c1.43 0 2.71.49 3.72 1.45l2.79-2.79C16.84 3.46 14.63 2.5 12 2.5a9.74 9.74 0 0 0-8.71 5.38l3.24 2.53C7.3 8.1 9.46 6.38 12 6.38Z" />
            </svg>
            {{ __('Continuar con Google') }}
        </a>

        <div class="text-center text-sm text-ink2">
            <span>{{ __('¿Aún no tienes cuenta?') }}</span>
            <flux:link
                :href="$teamInvitation ? route('register', ['invitation' => $teamInvitation['code']]) : route('register')"
                data-test="register-link" wire:navigate class="ms-1 font-semibold text-mintd">
                {{ __('Crear cuenta') }}
            </flux:link>
        </div>
    </div>
    </x-layouts::auth>
