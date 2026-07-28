<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserAcceptedInvitationNotification extends Notification
{
    use Queueable;

    public $acceptedUserName;
    public $resourceType;
    public $resourceName;
    public $url;

    public function __construct($acceptedUserName, $resourceType, $resourceName, $url)
    {
        $this->acceptedUserName = $acceptedUserName;
        $this->resourceType = $resourceType;
        $this->resourceName = $resourceName;
        $this->url = $url;
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
                    ->subject('Zaproszenie zostało przyjęte!')
                    ->greeting("Cześć, {$notifiable->name}!")
                    ->line("Użytkownik **{$this->acceptedUserName}** utworzył konto i pomyślnie dołączył do {$typePl} **{$this->resourceName}**.")
                    ->action('Zobacz', $this->url)
                    ->line('Dziękujemy za budowanie zespołu razem z nami!');
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
            'message' => "Użytkownik {$this->acceptedUserName} dołączył do {$typePl} {$this->resourceName}.",
            'url' => $this->url
        ];
    }
}
