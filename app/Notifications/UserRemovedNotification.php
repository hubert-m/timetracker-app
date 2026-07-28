<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserRemovedNotification extends Notification
{
    use Queueable;

    public $resourceType;
    public $resourceName;

    public function __construct($resourceType, $resourceName)
    {
        $this->resourceType = $resourceType;
        $this->resourceName = $resourceName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $typePl = $this->resourceType === 'Project' ? 'projektu' : 'zadania';
        return (new MailMessage)
                    ->subject('Zostałeś odłączony od zasobu')
                    ->greeting("Cześć, {$notifiable->name}!")
                    ->line("Informujemy, że Twój dostęp do {$typePl} **{$this->resourceName}** został anulowany.")
                    ->line('Dziękujemy za Twój wkład.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $typePl = $this->resourceType === 'Project' ? 'projektu' : 'zadania';
        return [
            'message' => "Zostałeś usunięty z {$typePl} {$this->resourceName}.",
            'url' => route('dashboard')
        ];
    }
}
