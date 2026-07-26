<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class VerifyEmailWithPin extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $pin = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $notifiable->forceFill(['activation_pin' => $pin])->save();

        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Aktywacja konta w TimeTracker')
            ->greeting('Witaj ' . $notifiable->name . '!')
            ->line('Dziękujemy za rejestrację. Poniżej znajduje się Twój 6-cyfrowy kod PIN, za pomocą którego aktywujesz konto:')
            ->line(new \Illuminate\Support\HtmlString('<div style="background-color: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 8px; padding: 24px; text-align: center; font-size: 32px; letter-spacing: 6px; font-weight: 800; margin: 30px 0; color: #4f46e5;">' . $pin . '</div>'))
            ->line('Możesz również użyć szybkiej aktywacji, klikając bezpośrednio w poniższy przycisk:')
            ->action('Aktywuj konto', $verificationUrl)
            ->line('Jeśli nie rejestrowałeś się w naszym serwisie, zignoruj tę wiadomość.');
    }

    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
