<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification
{
    public function __construct(private string $code)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Dein Verifizierungscode für Dilomarkt')
            ->greeting('Hallo!')
            ->line('Vielen Dank für deine Registrierung bei Dilomarkt.')
            ->line('Dein Verifizierungscode lautet: ' . $this->code)
            ->line('Bitte gib diesen Code in der App ein, um dein Konto zu aktivieren.')
            ->salutation('Mit freundlichen Grüßen, das Dilomarkt Team');
    }
}
