<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification de changement de mot de passe
 *
 * Cette notification informe l'utilisateur que son mot de passe a été modifié.
 * L'email est envoyé directement (de manière synchrone) pour plus de simplicité.
 */
class PasswordChanged extends Notification
{

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
        return (new MailMessage)
            ->subject('Votre mot de passe a été modifié')
            ->greeting('Bonjour ' . $notifiable->first_name . ' !')
            ->line('Nous vous informons que votre mot de passe a été modifié avec succès.')
            ->line('Si vous n\'avez pas effectué cette modification, veuillez [contacter notre support](' . url('/contact') . ') immédiatement.')
            ->salutation('Cordialement, l\'équipe de la plateforme');
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
