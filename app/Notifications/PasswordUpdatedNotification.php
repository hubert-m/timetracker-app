<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordUpdatedNotification extends Notification
{
    use Queueable;

    public $actionType;

    /**
     * Create a new notification instance.
     */
    public function __construct($actionType = 'zmienione')
    {
        $this->actionType = $actionType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $action = $this->actionType === 'ustawione' ? 'ustawione' : 'zmienione';
        return (new MailMessage)
            ->subject('Twoje hasło zostało ' . $action)
            ->greeting('Cześć ' . $notifiable->name . '!')
            ->line('Otrzymujesz tę wiadomość, ponieważ hasło do Twojego konta w naszym systemie zostało pomyślnie ' . $action . '.')
            ->line('Jeśli to nie Ty dokonałeś tej zmiany, skontaktuj się natychmiast z administratorem systemu.')
            ->action('Przejdź do aplikacji', url('/'))
            ->line('Pozdrawiamy, Twój Zespół');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
