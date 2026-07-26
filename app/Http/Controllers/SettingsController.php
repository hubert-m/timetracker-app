<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function updatePassword(Request $request)
    {
        $user = $request->user();
        
        // Jeśli użytkownik nie ma hasła (logowanie Google)
        if (is_null($user->password)) {
            $validated = $request->validate([
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);

            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            return back()->with('status', 'password-set');
        }

        // Zmiana hasła jeśli istnieje (wymaga starego hasła)
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
