<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Notifications\PasswordUpdatedNotification;

class SettingsController extends Controller
{
    public function updatePassword(Request $request)
    {
        $user = $request->user();
        
        // Jeśli użytkownik nie ma hasła (logowanie Google)
        if (is_null($user->password)) {
            $validated = $request->validateWithBag('updatePassword', [
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);

            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            $user->notify(new PasswordUpdatedNotification('ustawione'));

            return back()->with('status', 'password-set');
        }

        // Zmiana hasła jeśli istnieje (wymaga starego hasła)
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $user->notify(new PasswordUpdatedNotification('zmienione'));

        return back()->with('status', 'password-updated');
    }
}
