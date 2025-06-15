<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notification de confirmation de compte créateur
 *
 * Cette notification envoie un email au créateur pour confirmer son compte.
 * L'email est envoyé directement (de manière synchrone) pour plus de simplicité.
 */
class CreatorAccountConfirmation extends Notification
{

    protected $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
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
        $url = route('creator.confirm', ['token' => $this->token]);

        return (new MailMessage)
            ->subject('Confirmation de votre compte créateur')
            ->greeting('Bonjour ' . $notifiable->first_name . ' !')
            ->line('Merci de vous être inscrit comme créateur sur notre plateforme.')
            ->line('Veuillez confirmer votre compte en cliquant sur le lien ci-dessous :')
            ->line('[Confirmer mon compte](' . $url . ')')
            ->line('Si vous n\'avez pas créé de compte, aucune action n\'est requise.')
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
            'token' => $this->token,
        ];
    }
}
