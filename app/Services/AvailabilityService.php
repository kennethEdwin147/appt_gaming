<?php

namespace App\Services;

use App\Models\Creator;
use App\Models\EventType;
use App\Models\Availability;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AvailabilityService
{
    /**
     * Récupère les créneaux disponibles pour un créateur et un type d'événement
     */
    public function getAvailableSlots(int $creatorId, int $eventTypeId, Carbon $startDate, Carbon $endDate, string $timezone): array
    {
        $creator = Creator::findOrFail($creatorId);
        $eventType = EventType::findOrFail($eventTypeId);

        // Vérifier que le type d'événement appartient au créateur
        if ($eventType->creator_id !== $creatorId) {
            return [];
        }

        $creatorTimezone = $creator->timezone ?? 'UTC';
        $duration = $eventType->default_duration; // en minutes

        $slots = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $daySlots = $this->getSlotsForDay($creator, $eventType, $currentDate, $duration, $timezone, $creatorTimezone);
            if (!empty($daySlots)) {
                $slots[$currentDate->format('Y-m-d')] = $daySlots;
            }
            $currentDate->addDay();
        }

        return $slots;
    }

    /**
     * Récupère les créneaux disponibles pour une journée donnée
     */
    private function getSlotsForDay(Creator $creator, EventType $eventType, Carbon $date, int $duration, string $userTimezone, string $creatorTimezone): array
    {
        $dayOfWeek = strtolower($date->format('l'));

        // Récupérer les disponibilités pour ce jour de la semaine
        $availabilities = Availability::whereHas('schedule', function ($query) use ($creator) {
                $query->where('creator_id', $creator->id);
            })
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->where(function ($query) use ($date) {
                $query->whereNull('effective_from')
                      ->orWhere('effective_from', '<=', $date->format('Y-m-d'));
            })
            ->where(function ($query) use ($date) {
                $query->whereNull('effective_until')
                      ->orWhere('effective_until', '>=', $date->format('Y-m-d'));
            })
            ->get();

        if ($availabilities->isEmpty()) {
            return [];
        }

        $slots = [];

        foreach ($availabilities as $availability) {
            $slotTime = Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') . ' ' . $availability->start_time->format('H:i:s'), $creatorTimezone);
            $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') . ' ' . $availability->end_time->format('H:i:s'), $creatorTimezone);

            // Générer les créneaux de la durée spécifiée
            while ($slotTime->copy()->addMinutes($duration)->lte($endTime)) {
                $slotEndTime = $slotTime->copy()->addMinutes($duration);

                // Vérifier si le créneau n'est pas déjà réservé
                if ($this->isSlotAvailable($slotTime, $slotEndTime, $eventType->id, $creator->id)) {
                    // Convertir dans le fuseau horaire de l'utilisateur
                    $userSlotTime = $slotTime->copy()->setTimezone($userTimezone);

                    $slots[] = [
                        'start_time' => $userSlotTime->format('H:i'),
                        'end_time' => $userSlotTime->copy()->addMinutes($duration)->format('H:i'),
                        'datetime' => $slotTime->utc()->toISOString(),
                        'display_time' => $userSlotTime->format('H:i'),
                        'availability_id' => $availability->id,
                    ];
                }

                $slotTime->addMinutes($duration);
            }
        }

        return $slots;
    }

    /**
     * Vérifie si un créneau est disponible (pas de réservation existante)
     */
    private function isSlotAvailable(Carbon $startTime, Carbon $endTime, int $eventTypeId, int $creatorId): bool
    {
        // Récupérer la durée de l'événement
        $eventType = EventType::find($eventTypeId);
        if (!$eventType) {
            return false;
        }

        $duration = $eventType->default_duration;

        $conflictingReservations = Reservation::where('creator_id', $creatorId)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startTime, $endTime, $duration) {
                // Vérifier les chevauchements de créneaux
                $query->where(function ($subQuery) use ($startTime, $endTime) {
                    // Le créneau demandé chevauche avec une réservation existante
                    $subQuery->where('reserved_datetime', '<', $endTime->utc())
                             ->where('reserved_datetime', '>=', $startTime->utc());
                });
            })
            ->exists();

        return !$conflictingReservations;
    }

    /**
     * Vérifie si un créneau spécifique est disponible pour réservation
     */
    public function isSlotAvailableForReservation(string $datetime, string $timezone, int $eventTypeId, int $creatorId): bool
    {
        $eventType = EventType::findOrFail($eventTypeId);
        $duration = $eventType->default_duration;

        $startTime = Carbon::parse($datetime, $timezone);
        $endTime = $startTime->copy()->addMinutes($duration);

        return $this->isSlotAvailable($startTime, $endTime, $eventTypeId, $creatorId);
    }
}
