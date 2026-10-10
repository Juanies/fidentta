<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubscriptionSuccess extends Controller
{
    public function success(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user instanceof User, 401);

        $this->guardarLocationInicial($user);

        return redirect()->route('dashboard', [
            'current_team' => $user->currentTeam?->getRouteKey(),
        ]);
    }

    public function guardarLocationInicial(User $user): void
    {
        $team = $user->currentTeam;

        if (! $team) {
            return;
        }

        Location::firstOrCreate(
            ['team_id' => $team->id],
            [
                'name' => 'Ubicación principal',
                'is_active' => true,
            ],
        );
    }
}
