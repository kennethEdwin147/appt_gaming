<?php

namespace App\Services;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * Service pour gérer les rappels de réservation
 * 
 * Ce service centralise la logique de gestion des rappels de réservation.
 * Il peut être utilisé à la fois par la commande Artisan et par le contrôleur API.
 */
class ReminderService
{
    protected $emailService;

    /**
     * Constructeur
     */
    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Envoie des rappels pour les réservations prévues dans les prochaines 24 heures
     * 
     * @return array Informations sur les rappels envoyés
     */
    public function sendDailyReminders(): array
    {
        $tomorrow = Carbon::tomorrow();
        $dayAfterTomorrow = Carbon::tomorrow()->addDay();

        // Trouver toutes les réservations prévues pour demain
        $reservations = Reservation::where('status', 'confirmed')
            ->whereBetween('reserved_datetime', [$tomorrow, $dayAfterTomorrow])
            ->get();

        $sent = 0;
        $failed = 0;
        $details = [];

        foreach ($reservations as $reservation) {
            try {
                // Envoyer l'email de rappel
                $this->emailService->sendReservationReminder($reservation->user, $reservation);
                
                $details[] = [
                    'id' => $reservation->id,
                    'user' => $reservation->user->email,
                    'datetime' => $reservation->reserved_datetime,
                    'status' => 'sent'
                ];
                
                $sent++;
            } catch (\Exception $e) {
                $details[] = [
                    'id' => $reservation->id,
                    'user' => $reservation->user->email,
                    'datetime' => $reservation->reserved_datetime,
                    'status' => 'failed',
                    'error' => $e->getMessage()
                ];
                
                $failed++;
            }
        }

        return [
            'total' => $reservations->count(),
            'sent' => $sent,
            'failed' => $failed,
            'details' => $details
        ];
    }
}
