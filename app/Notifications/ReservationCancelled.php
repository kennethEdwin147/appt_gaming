<?php

namespace App\Notifications;

use App\Models\Reservation\Reservation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

/**
 * Notification d'annulation de réservation
 *
 * Cette notification informe l'utilisateur que sa réservation a été annulée.
 * L'email est envoyé directement (de manière synchrone) pour plus de simplicité.
 */
class ReservationCancelled extends Notification
{

    protected $reservation;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
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
        // Formater la date et l'heure selon le fuseau horaire de l'utilisateur
        $reservedDateTime = Carbon::parse($this->reservation->reserved_datetime);
        if ($this->reservation->timezone) {
            $reservedDateTime = $reservedDateTime->setTimezone($this->reservation->timezone);
        }

        $formattedDateTime = $reservedDateTime->format('d/m/Y à H:i');

        return (new MailMessage)
            ->subject('Annulation de votre réservation')
            ->greeting('Bonjour ' . $notifiable->first_name . ' !')
            ->line('Nous vous informons que votre réservation a été annulée.')
            ->line('Détails de la réservation annulée :')
            ->line('- Date et heure : ' . $formattedDateTime)
            ->line('- Type d\'événement : ' . $this->reservation->eventType->name)
            ->line('- Créateur : ' . $this->reservation->creator->user->first_name . ' ' . $this->reservation->creator->user->last_name)
            ->line('Si vous avez des questions concernant cette annulation, n\'hésitez pas à nous contacter.')
            ->line('[Réserver à nouveau](' . url('/creators/' . $this->reservation->creator_id) . ')')
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
            'reservation_id' => $this->reservation->id,
            'event_type' => $this->reservation->eventType->name,
            'reserved_datetime' => $this->reservation->reserved_datetime,
        ];
    }
}
