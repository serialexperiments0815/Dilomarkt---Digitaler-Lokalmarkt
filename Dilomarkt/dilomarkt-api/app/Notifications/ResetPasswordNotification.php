<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    public function __construct(private string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

        return (new MailMessage)
            ->subject('Passwort zurücksetzen bei Dilomarkt')
            ->greeting('Hallo!')
            ->line('Klicke auf den Link unten, um dein Passwort zurückzusetzen.')
            ->action('Passwort zurücksetzen', $frontendUrl . '/reset-password/' . $this->token)
            ->salutation('Mit freundlichen Grüßen, das Dilomarkt Team');
    }
}
