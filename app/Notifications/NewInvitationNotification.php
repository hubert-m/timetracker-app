<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewInvitationNotification extends Notification
{
    use Queueable;

    public $resourceName;
    public $resourceType;
    public $url;
    public $inviterName;
    public $projectName;

    /**
     * Create a new notification instance.
     */
    public function __construct($resourceName, $resourceType, $url, $inviterName, $projectName = null)
    {
        $this->resourceName = $resourceName;
        $this->resourceType = $resourceType;
        $this->url = $url;
        $this->inviterName = $inviterName;
        $this->projectName = $projectName;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        $typeName = $this->resourceType === 'Project' ? 'projektu' : 'zadania';
        $inProject = $this->projectName ? " w projekcie: **{$this->projectName}**" : "";
        
        return (new MailMessage)
                    ->subject('Nowe zaproszenie od ' . $this->inviterName)
                    ->greeting('Witaj ' . $notifiable->name . '!')
                    ->line("Użytkownik **{$this->inviterName}** zaprosił Cię do {$typeName}: **{$this->resourceName}**{$inProject}.")
                    ->action('Przejdź do ' . $typeName, $this->url)
                    ->line('Zaloguj się do aplikacji, aby sprawdzić szczegóły zaproszenia.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $typeName = $this->resourceType === 'Project' ? 'projektu' : 'zadania';
        $inProject = $this->projectName ? " w projekcie: {$this->projectName}" : "";
        
        return [
            'message' => "{$this->inviterName} zaprosił Cię do {$typeName}: {$this->resourceName}{$inProject}",
            'url' => $this->url,
            'icon' => $this->resourceType === 'Project' ? 'briefcase' : 'check-circle'
        ];
    }
}
