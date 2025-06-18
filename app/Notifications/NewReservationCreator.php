<?php

namespace App\Notifications;

use App\Models\Reservation\Reservation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

/**
 * Notification de nouvelle réservation pour le créateur
 *
 * Cette notification informe le créateur qu'un utilisateur a fait une réservation.
 * L'email est envoyé directement (de manière synchrone) pour plus de simplicité.
 */
class NewReservationCreator extends Notification
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
        $url = url('/creator/reservations/' . $this->reservation->id);

        // Formater la date et l'heure selon le fuseau horaire du créateur
        $reservedDateTime = Carbon::parse($this->reservation->reserved_datetime);
        $creatorTimezone = $notifiable->creator->timezone ?? config('app.timezone');
        $reservedDateTime = $reservedDateTime->setTimezone($creatorTimezone);

        $formattedDateTime = $reservedDateTime->format('d/m/Y à H:i');

        $userName = $this->reservation->user->first_name . ' ' . $this->reservation->user->last_name;

        return (new MailMessage)
            ->subject('Nouvelle réservation')
            ->greeting('Bonjour ' . $notifiable->first_name . ' !')
            ->line('Vous avez reçu une nouvelle réservation.')
            ->line('Détails de la réservation :')
            ->line('- Date et heure : ' . $formattedDateTime)
            ->line('- Type d\'événement : ' . $this->reservation->eventType->name)
            ->line('- Utilisateur : ' . $userName)
            ->line('[Voir les détails](' . $url . ')')
            ->line('Vous pouvez confirmer ou annuler cette réservation depuis votre tableau de bord.')
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
            'user_name' => $this->reservation->user->first_name . ' ' . $this->reservation->user->last_name,
            'event_type' => $this->reservation->eventType->name,
            'reserved_datetime' => $this->reservation->reserved_datetime,
        ];
    }
}
