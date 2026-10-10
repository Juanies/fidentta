<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class SubscriptionSuccess extends Controller
{
    public function success(Request $request)
    {
        $user = $request->user();
        $this->guardarLocationInicial();

        return redirect()->route('dashboard', [
            'current_team' => $user->currentTeam?->getRouteKey(),
        ]);
    }


    public function guardarLocationInicial()
    {

        // Primero crear Location que luego
        // el usuario podra editar
        // para crear ya las customer User
        Location::firstOrCreate(
            ['team_id' => Auth::user()->currentTeam->id],
            [
                'name' => 'Ubicación principal',
                'is_active' => true,
            ],
        );
    }
}
