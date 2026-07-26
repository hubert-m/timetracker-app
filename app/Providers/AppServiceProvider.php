<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Auth\Notifications\ResetPassword::toMailUsing(function ($notifiable, $token) {
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Powiadomienie o resecie hasła')
                ->line('Otrzymujesz tego e-maila, ponieważ dostaliśmy prośbę o reset hasła dla Twojego konta.')
                ->action('Zresetuj hasło', url(route('password.reset', [
                    'token' => $token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ], false)))
                ->line('Ten link do resetu wygaśnie za ' . config('auth.passwords.'.config('auth.defaults.passwords').'.expire') . ' minut.')
                ->line('Jeśli nie żądałeś resetu hasła, zignoruj tę wiadomość.');
        });
    }
}
