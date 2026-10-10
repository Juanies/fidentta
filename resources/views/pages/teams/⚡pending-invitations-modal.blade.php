<?php

use App\Models\TeamInvitation;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component {
    public bool $showPendingInvitationsModal = true;

    public function mount(): void
    {
        if (session()->pull('team-invitation-accepted')) {
            Flux::toast(variant: 'success', text: __('Invitación aceptada.'));
        }
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{code: string, inviter_name: string, team_name: string}>
     */
    #[Computed]
    public function pendingInvitations(): \Illuminate\Support\Collection
    {
        $email = Str::lower(Auth::user()->email);

        return TeamInvitation::query()
            ->with(['inviter', 'team'])
            ->whereRaw('LOWER(email) = ?', [$email])
            ->whereNull('accepted_at')
            ->where(fn($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
            ->latest()
            ->get()
            ->map(
                fn(TeamInvitation $invitation) => [
                    'code' => $invitation->code,
                    'inviter_name' => $invitation->inviter->name,
                    'team_name' => $invitation->team->name,
                ],
            );
    }

    public function acceptInvitation(string $code): void
    {
        $invitation = $this->findPendingInvitation($code);

        $user = Auth::user();

        DB::transaction(function () use ($user, $invitation) {
            $team = $invitation->team;

            $team->memberships()->firstOrCreate(['user_id' => $user->id], ['role' => $invitation->role]);

            $invitation->update(['accepted_at' => now()]);

            $user->switchTeam($team);
        });

        session()->flash('team-invitation-accepted', true);

        $this->redirectRoute('dashboard', navigate: true);
    }

    public function declineInvitation(string $code): void
    {
        $invitation = $this->findPendingInvitation($code);

        $invitation->delete();

        Flux::toast(variant: 'success', text: __('Invitación rechazada.'));
    }

    private function findPendingInvitation(string $code): TeamInvitation
    {
        $invitation = TeamInvitation::query()->where('code', $code)->whereNull('accepted_at')->firstOrFail();

        if ($invitation->isExpired()) {
            throw ValidationException::withMessages([
                'invitation' => [__('Esta invitación ha caducado.')],
            ]);
        }

        if (Str::lower($invitation->email) !== Str::lower(Auth::user()->email)) {
            throw ValidationException::withMessages([
                'invitation' => [__('Esta invitación se envió a otra dirección de correo electrónico.')],
            ]);
        }

        return $invitation;
    }
}; ?>

<div>
    @if ($this->pendingInvitations->isNotEmpty())
        <flux:modal name="pending-invitations" wire:model="showPendingInvitationsModal" focusable class="max-w-lg">
            <div data-test="pending-invitations-modal" class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Invitaciones pendientes') }}</flux:heading>
                    <flux:subheading>{{ __('Puedes aceptar o rechazar las invitaciones que has recibido.') }}
                    </flux:subheading>
                </div>

                <div class="grid gap-4">
                    @foreach ($this->pendingInvitations as $invitation)
                        <div data-test="pending-invitation-row" class="border border-border bg-card p-4">
                            <div class="space-y-1">
                                <p class="font-medium">{{ $invitation['team_name'] }}</p>
                                <flux:text class="text-sm text-ink2">
                                    {{ __(':inviter te ha invitado a unirte a este equipo.', ['inviter' => $invitation['inviter_name']]) }}
                                </flux:text>
                            </div>

                            <div class="mt-4 flex justify-end gap-2">
                                <flux:button variant="filled"
                                    wire:click="declineInvitation('{{ $invitation['code'] }}')"
                                    wire:loading.attr="disabled" data-test="pending-invitation-decline">
                                    {{ __('Rechazar') }}
                                </flux:button>

                                <flux:button variant="primary"
                                    wire:click="acceptInvitation('{{ $invitation['code'] }}')"
                                    wire:loading.attr="disabled" data-test="pending-invitation-accept">
                                    {{ __('Aceptar') }}
                                </flux:button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </flux:modal>
    @endif
</div>
