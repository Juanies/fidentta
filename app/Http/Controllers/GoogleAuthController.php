<?php

namespace App\Http\Controllers;

use App\Actions\Teams\CreateTeam;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(CreateTeam $createTeam): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();

        $user = DB::transaction(function () use ($googleUser, $createTeam) {
            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if (! $user) {
                $user = User::create([
                    'name' => $googleUser->getName() ?: 'Nuevo negocio',
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(Str::random(40)),
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken ?? null,
                ]);

                $createTeam->handle($user, $user->name . "'s Team", isPersonal: false);
            } else {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken ?? $user->google_refresh_token,
                ]);
            }

            return $user;
        });

        Auth::login($user, remember: true);
        request()->session()->regenerate();

        return redirect()->route('dashboard', [
            'current_team' => $user->currentTeam?->getRouteKey(),
        ]);
    }
}
