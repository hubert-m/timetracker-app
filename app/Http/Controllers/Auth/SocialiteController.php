<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Wystąpił błąd podczas logowania przez Google.');
        }

        $user = User::where('google_id', $googleUser->id)->first();

        if ($user) {
            $this->syncGoogleAvatar($user, $googleUser);
            Auth::login($user);
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $user = User::where('email', $googleUser->email)->first();

        if ($user) {
            $user->update([
                'google_id' => $googleUser->id,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
            $this->syncGoogleAvatar($user, $googleUser);
            Auth::login($user);
            return redirect()->intended(route('dashboard', absolute: false));
        }

        // Stwórz nowego użytkownika z pustym hasłem i statusem zweryfikowanym
        $user = User::create([
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'google_id' => $googleUser->id,
            'password' => null,
            'email_verified_at' => now(),
        ]);

        $this->syncGoogleAvatar($user, $googleUser);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }

    private function syncGoogleAvatar(User $user, $googleUser): void
    {
        $avatarUrl = $googleUser->getAvatar();
        if (!$avatarUrl) {
            return;
        }

        try {
            $response = Http::get($avatarUrl);
            if ($response->successful()) {
                $extension = 'jpg';
                $filename = 'avatars/' . $user->id . '_' . time() . '.' . $extension;

                // Usuń stary avatar jeśli istnieje i jest lokalny
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                Storage::disk('public')->put($filename, $response->body());
                $user->update(['avatar' => $filename]);
            }
        } catch (\Exception $e) {
            // Nie blokuj logowania jeśli avatar się nie pobierze
        }
    }
}

