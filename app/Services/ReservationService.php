<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\TimeSlot;
use App\Models\EventType;
use App\Models\User;
use App\Traits\HandlesTimezones;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    use HandlesTimezones;

    protected $emailService;
    protected $availabilityService;

    public function __construct(EmailService $emailService, AvailabilityService $availabilityService)
    {
        $this->emailService = $emailService;
        $this->availabilityService = $availabilityService;
    }

    /**
     * Crée une nouvelle réservation à partir d'un time slot
     *
     * @param array $data Les données de la réservation (user_id, creator_id, event_type_id, time_slot_id, timezone, etc.)
     * @return Reservation La réservation créée
     * @throws \Exception Si le créneau n'est plus disponible ou invalide
     */
    public function createReservation(array $data): Reservation
    {
        return DB::transaction(function () use ($data) {
            // Vérifier que le time slot est disponible
            $timeSlot = TimeSlot::lockForUpdate()->findOrFail($data['time_slot_id']);
            
            if ($timeSlot->status !== 'available') {
                throw new \Exception('Ce créneau n\'est plus disponible.');
            }

            // Vérifier que le time slot appartient au bon créateur
            if ($timeSlot->creator_id !== $data['creator_id']) {
                throw new \Exception('Créneau invalide pour ce créateur.');
            }

            $eventType = EventType::findOrFail($data['event_type_id']);
            
            // Vérifier que l'event type appartient au créateur
            if ($eventType->creator_id !== $data['creator_id']) {
                throw new \Exception('Type d\'événement invalide pour ce créateur.');
            }

            // Calculer le prix (time slot custom price ou event type price)
            $price = $timeSlot->custom_price ?? $eventType->default_price;
            
            // Calculer les heures de fin basées sur la durée de l'événement
            $sessionEnd = $timeSlot->start_time->copy()->addMinutes($eventType->default_duration);

            // Créer la réservation
            $reservation = Reservation::create([
                'user_id' => $data['user_id'],
                'creator_id' => $data['creator_id'],
                'event_type_id' => $data['event_type_id'],
                'time_slot_id' => $data['time_slot_id'],
                'guest_first_name' => $data['guest_first_name'] ?? null,
                'guest_last_name' => $data['guest_last_name'] ?? null,
                'reserved_datetime' => $timeSlot->start_time,
                'timezone' => $data['timezone'] ?? $timeSlot->timezone,
                'meeting_link' => $eventType->meeting_link,
                'status' => 'pending',
                'payment_status' => 'pending',
                'price_paid' => $price,
                'special_requests' => $data['special_requests'] ?? null,
                'participants_count' => $data['participants_count'] ?? 1,
            ]);

            // Marquer le time slot comme réservé
            $timeSlot->update(['status' => 'booked']);

            // Envoyer les notifications
            $user = User::find($data['user_id']);
            $creator = $timeSlot->creator;

            $this->emailService->sendReservationConfirmation($user, $reservation);
            $this->emailService->sendNewReservationNotification($creator, $reservation);

            return $reservation;
        });
    }

    /**
     * Annule une réservation et libère le time slot
     *
     * @param Reservation $reservation La réservation à annuler
     * @param string|null $reason Raison de l'annulation (optionnel)
     * @return bool True si l'annulation a réussi
     */
    public function cancelReservation(Reservation $reservation, string $reason = null): bool
    {
        return DB::transaction(function () use ($reservation, $reason) {
            // Mettre à jour le statut de la réservation
            $reservation->update([
                'status' => 'cancelled',
                'cancellation_reason' => $reason,
                'cancelled_at' => now(),
            ]);

            // Libérer le time slot si la réservation n'est pas trop proche
            if ($reservation->timeSlot && $reservation->reserved_datetime->isFuture()) {
                $this->availabilityService->releaseTimeSlot($reservation->time_slot_id);
            }

            // Envoyer notification d'annulation
            $this->emailService->sendReservationCancellation($reservation->user, $reservation);

            return true;
        });
    }

    /**
     * Confirme une réservation (action du créateur)
     *
     * @param Reservation $reservation La réservation à confirmer
     * @return bool True si la confirmation a réussi, False si déjà confirmée
     */
    public function confirmReservation(Reservation $reservation): bool
    {
        if ($reservation->status !== 'pending') {
            return false;
        }

        return $reservation->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
        ]);
    }

    /**
     * Marque une réservation comme complétée
     *
     * @param Reservation $reservation La réservation à marquer comme complétée
     * @param array $sessionData Données optionnelles de la session (actual_start, actual_end, session_notes)
     * @return bool True si la mise à jour a réussi
     */
    public function completeReservation(Reservation $reservation, array $sessionData = []): bool
    {
        $updateData = [
            'status' => 'completed',
            'completed_at' => now(),
        ];

        // Ajouter les données de session si fournies
        if (isset($sessionData['actual_start'])) {
            $updateData['actual_start'] = $sessionData['actual_start'];
        }
        
        if (isset($sessionData['actual_end'])) {
            $updateData['actual_end'] = $sessionData['actual_end'];
        }
        
        if (isset($sessionData['session_notes'])) {
            $updateData['session_notes'] = $sessionData['session_notes'];
        }

        return $reservation->update($updateData);
    }

    /**
     * Reprogramme une réservation vers un nouveau time slot
     *
     * @param Reservation $reservation La réservation à reprogrammer
     * @param int $newTimeSlotId ID du nouveau créneau horaire
     * @return bool True si la reprogrammation a réussi
     * @throws \Exception Si le nouveau créneau n'est pas disponible ou invalide
     */
    public function rescheduleReservation(Reservation $reservation, int $newTimeSlotId): bool
    {
        return DB::transaction(function () use ($reservation, $newTimeSlotId) {
            // Vérifier que le nouveau time slot est disponible
            $newTimeSlot = TimeSlot::lockForUpdate()->findOrFail($newTimeSlotId);
            
            if ($newTimeSlot->status !== 'available') {
                throw new \Exception('Le nouveau créneau n\'est plus disponible.');
            }

            // Vérifier que c'est le même créateur
            if ($newTimeSlot->creator_id !== $reservation->creator_id) {
                throw new \Exception('Le nouveau créneau doit appartenir au même créateur.');
            }

            $oldTimeSlotId = $reservation->time_slot_id;
            
            // Mettre à jour la réservation
            $reservation->update([
                'time_slot_id' => $newTimeSlotId,
                'reserved_datetime' => $newTimeSlot->start_time,
                'status' => 'rescheduled',
                'rescheduled_at' => now(),
            ]);

            // Libérer l'ancien time slot
            if ($oldTimeSlotId) {
                $this->availabilityService->releaseTimeSlot($oldTimeSlotId);
            }

            // Réserver le nouveau time slot
            $this->availabilityService->bookTimeSlot($newTimeSlotId);

            // Envoyer notification de changement
            $this->emailService->sendReservationConfirmation($reservation->user, $reservation);

            return true;
        });
    }

    /**
     * Récupère les réservations pour un créateur avec filtres
     *
     * @param int $creatorId ID du créateur
     * @param array $filters Filtres optionnels (status, date_from, date_to, event_type_id, per_page)
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator Résultats paginés
     */
    public function getCreatorReservations(int $creatorId, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Reservation::where('creator_id', $creatorId)
            ->with(['user', 'eventType', 'timeSlot'])
            ->orderBy('reserved_datetime', 'desc');

        // Filtres optionnels
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->where('reserved_datetime', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('reserved_datetime', '<=', $filters['date_to']);
        }

        if (isset($filters['event_type_id'])) {
            $query->where('event_type_id', $filters['event_type_id']);
        }

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Récupère les réservations pour un utilisateur
     *
     * @param int $userId ID de l'utilisateur
     * @param array $filters Filtres optionnels (status, upcoming_only, per_page)
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator Résultats paginés
     */
    public function getUserReservations(int $userId, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Reservation::where('user_id', $userId)
            ->with(['creator.user', 'eventType', 'timeSlot'])
            ->orderBy('reserved_datetime', 'desc');

        // Filtres optionnels
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['upcoming_only']) && $filters['upcoming_only']) {
            $query->where('reserved_datetime', '>', now());
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    /**
     * Récupère les réservations à venir pour les rappels
     *
     * @param Carbon $date Date pour laquelle récupérer les réservations
     * @return \Illuminate\Support\Collection Collection des réservations confirmées pour cette date
     */
    public function getUpcomingReservationsForReminders(Carbon $date): \Illuminate\Support\Collection
    {
        return Reservation::where('status', 'confirmed')
            ->whereDate('reserved_datetime', $date)
            ->with(['user', 'creator.user', 'eventType'])
            ->get();
    }

    /**
     * Marque une réservation comme "no-show" (client absent)
     *
     * @param Reservation $reservation La réservation concernée
     * @return bool True si le marquage a réussi, False si statut invalide
     */
    public function markAsNoShowCustomer(Reservation $reservation): bool
    {
        if (!in_array($reservation->status, ['confirmed', 'pending'])) {
            return false;
        }

        return $reservation->update([
            'status' => 'no_show_customer',
            'no_show_at' => now(),
        ]);
    }

    /**
     * Marque une réservation comme "no-show" (créateur absent)
     *
     * @param Reservation $reservation La réservation concernée
     * @return bool True si le marquage a réussi, False si statut invalide
     */
    public function markAsNoShowCreator(Reservation $reservation): bool
    {
        if (!in_array($reservation->status, ['confirmed', 'pending'])) {
            return false;
        }

        return $reservation->update([
            'status' => 'no_show_creator',
            'no_show_at' => now(),
        ]);
    }

    /**
     * Vérifie si un utilisateur peut annuler une réservation
     *
     * @param Reservation $reservation La réservation à vérifier
     * @return bool True si l'utilisateur peut annuler, False sinon
     */
    public function canUserCancelReservation(Reservation $reservation): bool
    {
        // Ne peut pas annuler si déjà annulée ou complétée
        if (in_array($reservation->status, ['cancelled', 'completed'])) {
            return false;
        }

        // Vérifier le délai d'annulation (par exemple, 2 heures avant)
        $cancellationDeadline = $reservation->reserved_datetime->subHours(2);
        
        return now()->isBefore($cancellationDeadline);
    }

    /**
     * Calcule les statistiques de réservation pour un créateur
     *
     * @param int $creatorId ID du créateur
     * @param Carbon|null $startDate Date de début de la période (optionnel)
     * @param Carbon|null $endDate Date de fin de la période (optionnel)
     * @return array Statistiques avec total, completed, cancelled, rates, revenue
     */
    public function getCreatorReservationStats(int $creatorId, Carbon $startDate = null, Carbon $endDate = null): array
    {
        $query = Reservation::where('creator_id', $creatorId);
        
        if ($startDate && $endDate) {
            $query->whereBetween('reserved_datetime', [$startDate, $endDate]);
        }

        $total = $query->count();
        $completed = $query->where('status', 'completed')->count();
        $cancelled = $query->where('status', 'cancelled')->count();
        $noShowCustomer = $query->where('status', 'no_show_customer')->count();
        $totalRevenue = $query->where('payment_status', 'paid')->sum('price_paid');

        return [
            'total_reservations' => $total,
            'completed' => $completed,
            'cancelled' => $cancelled,
            'no_show_customer' => $noShowCustomer,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
            'cancellation_rate' => $total > 0 ? round(($cancelled / $total) * 100, 2) : 0,
            'total_revenue' => $totalRevenue,
        ];
    }
}