<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

/**
 * Notification de confirmation de paiement
 *
 * Cette notification confirme à l'utilisateur que son paiement a été reçu et traité.
 * L'email est envoyé directement (de manière synchrone) pour plus de simplicité.
 */
class PaymentConfirmation extends Notification
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

        // Récupérer les informations de transaction
        $transaction = $this->reservation->transaction;
        $amount = $transaction ? number_format($transaction->amount, 2) . ' ' . $transaction->currency : 'N/A';

        // Formater la date et l'heure selon le fuseau horaire de l'utilisateur
        $reservedDateTime = Carbon::parse($this->reservation->reserved_datetime);
        if ($this->reservation->timezone) {
            $reservedDateTime = $reservedDateTime->setTimezone($this->reservation->timezone);
        }

        $formattedDateTime = $reservedDateTime->format('d/m/Y à H:i');

        return (new MailMessage)
            ->subject('Confirmation de paiement')
            ->greeting('Bonjour ' . $notifiable->first_name . ' !')
            ->line('Nous vous confirmons que votre paiement a été traité avec succès.')
            ->line('Détails de la transaction :')
            ->line('- Montant : ' . $amount)
            ->line('- Date : ' . now()->format('d/m/Y à H:i'))
            ->line('- Réservation : ' . $this->reservation->eventType->name . ' le ' . $formattedDateTime)
            ->line('[Voir les détails](' . $url . ')')
            ->line('Merci d\'utiliser notre plateforme !')
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
            'transaction_id' => $this->reservation->transaction ? $this->reservation->transaction->id : null,
            'amount' => $this->reservation->transaction ? $this->reservation->transaction->amount : null,
        ];
    }
}
