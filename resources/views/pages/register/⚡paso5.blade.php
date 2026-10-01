<?php

use Livewire\Component;
use Livewire\Attributes\Modelable;
use App\Actions\Fortify\CreateNewUser;
use App\Concerns\PasswordValidationRules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\cardDesign;
use App\Models\CustomerUser;
use App\Models\Location;
use App\Services\TeamRegistrationService;
new class extends Component {
    use PasswordValidationRules;

    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    #[Modelable]
    public array $registro = [
        'name' => 'Cafe laté',
        'logo' => ['type' => 'text', 'content' => 'C', 'url' => null],
        'sellos' => 8,
        'error' => false,

        'recompensa' => 'Bebida gratis',
        'registro_usuarios' => [
            'modo' => 'basico',
            'campos' => ['email', 'password'],
        ],
    ];

    public function registrarse(CreateNewUser $createNewUser, TeamRegistrationService $teamRegistrationService): mixed
    {
        $validated = $this->validate([
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => $this->passwordRules(),
            'password_confirmation' => ['required', 'same:password'],
        ]);

        $user = $createNewUser->create([
            'name' => $this->registro['name'] ?? 'Nuevo negocio',
            'email' => $validated['email'],
            'logo' => $this->registro['logo'] ?? [
                'type' => 'text',
                'content' => strtoupper(substr($this->registro['name'] ?? 'C', 0, 1)),
                'url' => null,
            ],
            'password' => $validated['password'],
            'password_confirmation' => $validated['password_confirmation'],
        ]);

        Auth::login($user, true);

        request()->session()->regenerate();

        $teamRegistrationService->setup($user, $this->registro);

        return redirect()->route('dashboard', [
            'current_team' => $user->currentTeam?->getRouteKey(),
        ]);
    }

    public function google()
    {
        session()->put('registro_google', $this->registro);

        return redirect()->route('google.redirect');
    }
};

?>

<div>
    @php
        $previewBackground =
            $registro['paleta']['fondo'] ?? 'linear-gradient(135deg, #7C2D12 0%, #B45309 38%, #F59E0B 100%)';
        $previewText = $registro['paleta']['texto'] ?? '#F8FAFC';
        $logo = $registro['logo'] ?? ['type' => 'text', 'content' => 'C', 'url' => null];
        $logoInitial = strtoupper(substr($logo['content'] ?? ($registro['name'] ?? 'C'), 0, 1));
        $sellos = $registro['sellos'] ?? 8;
    @endphp

    <div class="max-w-3xl">
        <p class="mb-2 text-xs font-semibold uppercase tracking-[0.18em] text-brand">Paso 5 · Tu cuenta</p>
        <h2 class="text-2xl font-semibold tracking-tight text-ink sm:text-3xl">Crea tu cuenta para continuar</h2>
        <p class="mt-3 text-base leading-7 text-muted-foreground">Revisa la experiencia antes de publicar tu
            tarjeta de fidelidad.</p>
    </div>

    <div class="mt-10 grid grid-cols-1 gap-5 lg:grid-cols-12 lg:items-start">
        <div class="lg:col-span-7">
            <form wire:submit="registrarse"
                class="rounded-3xl border border-white/10 bg-white/3 p-5 shadow-[0_20px_50px_rgba(15,23,42,0.18)] sm:p-7">
                <div class="flex items-start justify-between gap-5">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-fidentta-cyan">Crear cuenta
                        </p>
                        <h3 class="mt-2 text-2xl font-semibold text-text">Administra
                            {{ $registro['name'] ?? 'tu negocio' }}</h3>
                    </div>
                    <span
                        class="rounded-full border border-fidentta-cyan/25 bg-fidentta-cyan/10 px-3 py-1 text-xs font-semibold text-fidentta-cyan">Fiddenta</span>
                </div>

                <div class="mt-7 space-y-5">
                    <div>
                        <label for="preview-email" class="mb-2 block text-sm font-medium text-text">Email</label>
                        <input id="preview-email" wire:model="email" type="email" placeholder="tu@email.com"
                            class="w-full rounded-xl border border-white/15 bg-white/4 px-4 py-3 text-sm text-text outline-none placeholder:text-text-secondary/45 focus:border-fidentta-cyan focus:ring-2 focus:ring-fidentta-cyan/20">
                        @error('email')
                            <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3">
                            <label for="preview-password" class="block text-sm font-medium text-text">Contraseña</label>
                            <span class="text-xs text-text-secondary/50">Mínimo 8 caracteres</span>
                        </div>
                        <input id="preview-password" wire:model="password" type="password"
                            placeholder="Crea una contraseña"
                            class="w-full rounded-xl border border-white/15 bg-white/4 px-4 py-3 text-sm text-text outline-none placeholder:text-text-secondary/45 focus:border-fidentta-cyan focus:ring-2 focus:ring-fidentta-cyan/20">
                        @error('password')
                            <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="preview-password-confirmation"
                            class="mb-2 block text-sm font-medium text-text">Confirmar contraseña</label>
                        <input id="preview-password-confirmation" wire:model="password_confirmation" type="password"
                            placeholder="Repite tu contraseña"
                            class="w-full rounded-xl border border-white/15 bg-white/4 px-4 py-3 text-sm text-text outline-none placeholder:text-text-secondary/45 focus:border-fidentta-cyan focus:ring-2 focus:ring-fidentta-cyan/20">
                        @error('password_confirmation')
                            <p class="mt-2 text-xs text-rose-300">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit" wire:loading.attr="disabled"
                    class="mt-7 w-full rounded-xl bg-fidentta-cyan px-5 py-3.5 text-sm font-bold text-fidentta-navy shadow-lg shadow-fidentta-cyan/15">Crear
                    mi tarjeta</button>

                <div class="my-5 flex items-center gap-3 text-xs text-text-secondary/50">
                    <span class="h-px flex-1 bg-white/10"></span>
                    <span>o continúa con</span>
                    <span class="h-px flex-1 bg-white/10"></span>
                </div>

                <button wire:click='google'
                    class="flex w-full items-center justify-center gap-3 rounded-xl border border-white/15 bg-white/4 px-5 py-3.5 text-sm font-semibold text-text transition hover:border-white/30 hover:bg-white/8">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
                        <path fill="#4285F4"
                            d="M21.35 12.23c0-.79-.07-1.55-.22-2.27H12v4.3h5.24a4.48 4.48 0 0 1-1.94 2.94v2.45h3.14c1.84-1.69 2.91-4.18 2.91-7.42Z" />
                        <path fill="#34A853"
                            d="M12 21.5c2.63 0 4.84-.87 6.45-2.35l-3.14-2.45c-.87.58-1.98.92-3.31.92-2.54 0-4.7-1.72-5.47-4.03H3.29v2.53A9.74 9.74 0 0 0 12 21.5Z" />
                        <path fill="#FBBC05"
                            d="M6.53 13.59A5.86 5.86 0 0 1 6.22 12c0-.55.1-1.09.31-1.59V7.88H3.29A9.5 9.5 0 0 0 2.25 12c0 1.52.36 2.95 1.04 4.12l3.24-2.53Z" />
                        <path fill="#EA4335"
                            d="M12 6.38c1.43 0 2.71.49 3.72 1.45l2.79-2.79C16.84 3.46 14.63 2.5 12 2.5a9.74 9.74 0 0 0-8.71 5.38l3.24 2.53C7.3 8.1 9.46 6.38 12 6.38Z" />
                    </svg>
                    Continuar con Google
                </button>

                <p class="mt-4 text-center text-xs leading-5 text-text-secondary/50">Al continuar aceptas las
                    condiciones de uso y la política de privacidad.</p>
            </form>
        </div>

        <div class="space-y-5 lg:col-span-5">
            <div class="rounded-3xl border border-white/10 bg-white/3 p-5 sm:p-6">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-fidentta-cyan">Resumen</p>
                        <h3 class="mt-2 text-xl font-semibold text-text">Datos esenciales</h3>
                    </div>
                    <span
                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/6 text-fidentta-cyan">✓</span>
                </div>
                <div class="mt-5 divide-y divide-white/10">
                    <div class="flex items-center justify-between gap-4 py-3 first:pt-0"><span
                            class="text-sm text-text-secondary/65">Identificación</span><span
                            class="text-right text-sm font-medium text-text">Email</span></div>
                    <div class="flex items-center justify-between gap-4 py-3"><span
                            class="text-sm text-text-secondary/65">Acceso</span><span
                            class="text-right text-sm font-medium text-text">Contraseña</span></div>
                    <div class="flex items-center justify-between gap-4 py-3"><span
                            class="text-sm text-text-secondary/65">Programa</span><span
                            class="text-right text-sm font-medium text-text">{{ $sellos }} sellos</span></div>
                    <div class="flex items-center justify-between gap-4 py-3 last:pb-0"><span
                            class="text-sm text-text-secondary/65">Recompensa</span><span
                            class="max-w-[55%] text-right text-sm font-medium text-text">{{ $registro['recompensa'] ?? 'Bebida gratis' }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-white/10 p-5 shadow-2xl shadow-black/20 sm:p-6"
                style="background: {{ $previewBackground }}; color: {{ $previewText }};">
                <div class="flex items-center justify-between gap-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] opacity-75">Vista previa</p><span
                        class="rounded-full border border-white/20 bg-white/10 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider">Tarjeta</span>
                </div>
                <div class="mt-6 flex items-center gap-3">
                    @if ($logo['url'] ?? null)
                        <img src="{{ $logo['url'] }}" alt="Logo del negocio"
                            class="h-11 w-11 rounded-full border border-white/20 object-cover" />
                    @else
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-full border border-white/20 bg-white/10 text-lg font-bold">
                            {{ $logoInitial }}</div>
                    @endif
                    <div>
                        <p class="font-semibold">{{ $registro['name'] ?? 'Tu negocio' }}</p>
                        <p class="text-xs opacity-70">Programa de fidelidad</p>
                    </div>
                </div>
                <div class="mt-6 grid grid-cols-4 gap-2">
                    @for ($i = 0; $i < $sellos; $i++)
                        <span class="aspect-square rounded-full border border-white/25 bg-white/10"></span>
                    @endfor
                </div>
                <div class="mt-5 border-t border-white/15 pt-4">
                    <p class="text-sm opacity-80">Completa {{ $sellos }} sellos y consigue</p>
                    <p class="mt-1 text-lg font-semibold">{{ $registro['recompensa'] ?? 'tu recompensa' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
