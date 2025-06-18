<?php

namespace App\Notifications;

use App\Models\Reservation\Reservation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

/**
 * Notification de rappel de réservation
 *
 * Cette notification rappelle à l'utilisateur qu'il a une réservation prévue prochainement.
 * L'email est envoyé directement (de manière synchrone) pour plus de simplicité.
 */
class ReservationReminder extends Notification
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
        $url = url('/reservations/' . $this->reservation->id);

        // Formater la date et l'heure selon le fuseau horaire de l'utilisateur
        $reservedDateTime = Carbon::parse($this->reservation->reserved_datetime);
        if ($this->reservation->timezone) {
            $reservedDateTime = $reservedDateTime->setTimezone($this->reservation->timezone);
        }

        $formattedDateTime = $reservedDateTime->format('d/m/Y à H:i');

        $mail = (new MailMessage)
            ->subject('Rappel : Votre réservation approche')
            ->greeting('Bonjour ' . $notifiable->first_name . ' !')
            ->line('Nous vous rappelons que vous avez une réservation prévue pour demain.')
            ->line('Détails de la réservation :')
            ->line('- Date et heure : ' . $formattedDateTime)
            ->line('- Type d\'événement : ' . $this->reservation->eventType->name)
            ->line('- Créateur : ' . $this->reservation->creator->user->first_name . ' ' . $this->reservation->creator->user->last_name);

        // Ajouter le lien de réunion s'il existe
        if ($this->reservation->meeting_link) {
            $mail->line('- Lien de réunion : ' . $this->reservation->meeting_link);
        } elseif ($this->reservation->eventType->meeting_link) {
            $mail->line('- Lien de réunion : ' . $this->reservation->eventType->meeting_link);
        }

        return $mail->line('[Voir les détails](' . $url . ')')
            ->line('N\'hésitez pas à nous contacter si vous avez des questions.')
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
