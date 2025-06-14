<?php

namespace App\Services;

use App\Models\Availability;
use App\Models\Reservation;
use App\Traits\HandlesTimezones;
use Carbon\Carbon;

class ReservationService
{
    use HandlesTimezones;

    /**
     * Stocke la dernière disponibilité vérifiée
     * @var Availability|null
     */
    protected ?Availability $lastCheckedAvailability = null;

    /**
     * Vérifie si un créneau est disponible dans le fuseau horaire du client
     */
    public function isSlotAvailable(
        string $requestedDateTime,
        string $clientTimezone,
        int $eventTypeId,
        int $creatorId
    ): bool {
        // Convertir l'heure demandée du fuseau du client vers UTC
        $requestedUTC = Carbon::parse($requestedDateTime, $clientTimezone)->setTimezone('UTC');

        // Extraire le jour de la semaine et l'heure
        $dayOfWeek = strtolower($requestedUTC->format('l'));
        $timeRequested = $requestedUTC->format('H:i');

        // Vérifier si une disponibilité existe pour ce créneau
        $availability = Availability::where('creator_id', $creatorId)
            ->where('event_type_id', $eventTypeId)
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', '<=', $timeRequested)
            ->where('end_time', '>', $timeRequested)
            ->where('is_active', true)
            ->where(function ($query) use ($requestedUTC) {
                $query->whereNull('effective_from')
                    ->orWhere('effective_from', '<=', $requestedUTC->format('Y-m-d'));
            })
            ->where(function ($query) use ($requestedUTC) {
                $query->whereNull('effective_until')
                    ->orWhere('effective_until', '>=', $requestedUTC->format('Y-m-d'));
            })
            ->first();

        if (!$availability) {
            return false;
        }

        // Vérifier s'il n'y a pas déjà une réservation
        $existingReservation = Reservation::where('creator_id', $creatorId)
            ->where('event_type_id', $eventTypeId)
            ->where('reserved_datetime', $requestedUTC)
            ->where(function($query) {
                $query->where('status', '!=', 'cancelled')
                      ->orWhere('payment_status', '!=', 'cancelled');
            })
            ->exists();

        // Si une disponibilité a été trouvée, on la stocke pour une utilisation ultérieure
        if ($availability) {
            $this->lastCheckedAvailability = $availability;
        }

        return !$existingReservation;
    }

    /**
     * Retourne les créneaux disponibles pour une date donnée
     */
    public function getAvailableSlots(
        string $date,
        string $clientTimezone,
        int $eventTypeId,
        int $creatorId
    ): array {
        $dateObj = Carbon::parse($date, $clientTimezone);
        $dayOfWeek = strtolower($dateObj->format('l'));

        // Récupérer les disponibilités pour ce jour
        $availabilities = Availability::where('creator_id', $creatorId)
            ->where('event_type_id', $eventTypeId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->get();

        $slots = [];
        foreach ($availabilities as $availability) {
            // Générer les créneaux pour cette disponibilité
            $start = Carbon::parse($date . ' ' . $availability->start_time, 'UTC');
            $end = Carbon::parse($date . ' ' . $availability->end_time, 'UTC');

            // Convertir en fuseau horaire du client
            $startClient = $start->setTimezone($clientTimezone);
            $endClient = $end->setTimezone($clientTimezone);

            while ($startClient < $endClient) {
                if ($this->isSlotAvailable($startClient->format('Y-m-d H:i:s'), $clientTimezone, $eventTypeId, $creatorId)) {
                    $slots[] = $startClient->format('H:i');
                }
                $startClient->addMinutes(30); // Intervalle de 30 minutes
            }
        }

        return $slots;
    }

    /**
     * Récupère la dernière disponibilité vérifiée
     */
    public function getLastCheckedAvailability(): ?Availability
    {
        return $this->lastCheckedAvailability;
    }
}
