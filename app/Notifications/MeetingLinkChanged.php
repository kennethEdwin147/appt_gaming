<?php

namespace App\Notifications;

use App\Models\EventType;
use App\Models\Reservation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

/**
 * Notification de changement de lien de réunion
 *
 * Cette notification informe l'utilisateur que le lien de réunion pour sa réservation a été modifié.
 * L'email est envoyé directement (de manière synchrone) pour plus de simplicité.
 */
class MeetingLinkChanged extends Notification
{
    protected $reservation;
    protected $eventType;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reservation $reservation, EventType $eventType)
    {
        $this->reservation = $reservation;
        $this->eventType = $eventType;
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
        $meetingLink = $this->reservation->getMeetingLink();

        return (new MailMessage)
            ->subject('Mise à jour du lien de réunion')
            ->greeting('Bonjour ' . $notifiable->first_name . ' !')
            ->line('Le lien de réunion pour votre événement a été mis à jour.')
            ->line('Détails de la réservation :')
            ->line('- Date et heure : ' . $formattedDateTime)
            ->line('- Type d\'événement : ' . $this->eventType->name)
            ->line('- Créateur : ' . $this->eventType->creator->first_name . ' ' . $this->eventType->creator->last_name)
            ->when($meetingLink, function ($message) use ($meetingLink) {
                return $message->line('- Nouveau lien de réunion : ' . $meetingLink);
            }, function ($message) {
                return $message->line('- Le lien de réunion a été supprimé ou modifié.');
            })
            ->line('Veuillez utiliser ce nouveau lien pour rejoindre la réunion à la date et l\'heure prévues.')
            ->line('[Voir les détails de la réservation](' . url('/reservations/' . $this->reservation->id) . ')')
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
            'event_type_id' => $this->eventType->id,
            'event_type_name' => $this->eventType->name,
            'reserved_datetime' => $this->reservation->reserved_datetime,
            'meeting_link' => $this->eventType->meeting_link,
        ];
    }
}
