<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
        Blade::directive('userTime', function ($expression) {
            // $expression format: ($date, $format)
            return "<?php 
                if (auth()->check()) {
                    \$timezone = auth()->user()->timezone ?? 'Europe/Warsaw';
                } else {
                    \$timezone = 'Europe/Warsaw';
                }
                \$args = explode(',', \$expression);
                \$date = trim(\$args[0] ?? 'null');
                \$format = isset(\$args[1]) ? trim(\$args[1], \" '\"\"\") : 'Y-m-d H:i:s';
                
                if (\$date !== 'null' && \$date) {
                    echo \Carbon\Carbon::parse(\$date)->timezone(\$timezone)->format(\$format);
                }
            ?>";
        });

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
