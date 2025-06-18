<?php

namespace App\Services;

use App\Models\creator\Creator;
use App\Models\EventType\EventType;
use App\Models\availability\Availability;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AvailabilityService
{
    public function __construct()
    {
        // Manual include since autoloader has issues with dash in folder names
        require_once app_path('Models/time-slot/TimeSlot.php');
    }
    /**
     * Récupère les créneaux disponibles pour un créateur et un type d'événement
     * VERSION TIME SLOTS avec génération automatique à la demande
     */
    public function getAvailableSlots(int $creatorId, int $eventTypeId, Carbon $startDate, Carbon $endDate, string $timezone): array
    {
        $creator = Creator::findOrFail($creatorId);
        $eventType = EventType::findOrFail($eventTypeId);

        // Vérifier que le type d'événement appartient au créateur
        if ($eventType->creator_id !== $creatorId) {
            return [];
        }

        // 1. S'assurer que les slots existent pour la période demandée
        $this->ensureSlotsExist($creatorId, $startDate, $endDate);

        // 2. Récupérer les time_slots disponibles dans la période demandée
        // Exclure les slots passés et inclure une marge pour les timezones
        $utcNow = now()->utc();
        $searchStart = $startDate->copy()->subDay()->startOfDay()->utc();
        $searchEnd = $endDate->copy()->addDay()->endOfDay()->utc();
        
        $timeSlots = \App\Models\TimeSlot\TimeSlot::where('creator_id', $creatorId)
            ->where('status', 'available')
            ->where('start_time', '>=', $utcNow) // Exclure les slots passés
            ->whereBetween('start_time', [$searchStart, $searchEnd])
            ->orderBy('start_time')
            ->get();

        // Organiser par date et convertir dans le timezone de l'utilisateur
        $slots = [];
        
        foreach ($timeSlots as $timeSlot) {
            // Convertir dans le timezone utilisateur en préservant l'heure locale
            $userSlotTime = $timeSlot->start_time->copy()->setTimezone($timezone);
            $dateKey = $userSlotTime->format('Y-m-d');
            
            // Vérifier que la date convertie est dans la plage demandée
            $slotDate = Carbon::parse($dateKey);
            if ($slotDate->lt($startDate) || $slotDate->gt($endDate)) {
                continue;
            }
            
            // Vérifier que le slot peut accommoder la durée de l'événement
            $slotDuration = $timeSlot->start_time->diffInMinutes($timeSlot->end_time);
            if ($slotDuration >= $eventType->default_duration) {
                
                if (!isset($slots[$dateKey])) {
                    $slots[$dateKey] = [];
                }

                $slots[$dateKey][] = [
                    'id' => $timeSlot->id,
                    'start_time' => $userSlotTime->format('H:i'),
                    'end_time' => $userSlotTime->copy()->addMinutes($eventType->default_duration)->format('H:i'),
                    'datetime' => $timeSlot->start_time->utc()->toISOString(),
                    'display_time' => $userSlotTime->format('H:i'),
                    'custom_price' => $timeSlot->custom_price,
                    'creator_notes' => $timeSlot->creator_notes,
                ];
            }
        }

        return $slots;
    }

    /**
     * S'assure que les time slots existent pour la période demandée
     * Génère automatiquement les slots manquants
     */
    public function ensureSlotsExist(int $creatorId, Carbon $startDate, Carbon $endDate): array
    {
        // Limiter la génération à 30 jours max pour la performance
        $maxDays = 30;
        $limitedEndDate = $startDate->copy()->addDays($maxDays);
        if ($endDate->gt($limitedEndDate)) {
            $endDate = $limitedEndDate;
            Log::warning("Limited slot generation to {$maxDays} days for creator {$creatorId}");
        }

        $generated = [];
        $currentDate = $startDate->copy();

        Log::info("Ensuring slots exist for creator {$creatorId} from {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");

        while ($currentDate->lte($endDate)) {
            // Vérifier si des slots existent déjà pour cette date
            $existingSlotsCount = \App\Models\TimeSlot\TimeSlot::where('creator_id', $creatorId)
                ->whereDate('generated_for_date', $currentDate->format('Y-m-d'))
                ->count();

            if ($existingSlotsCount === 0) {
                Log::info("Generating slots for creator {$creatorId} on {$currentDate->format('Y-m-d')}");
                
                // Générer les slots pour cette date
                $daySlots = $this->generateTimeSlotsForDate($creatorId, $currentDate);
                $generated = array_merge($generated, $daySlots);
                
                Log::info("Generated " . count($daySlots) . " slots for creator {$creatorId} on {$currentDate->format('Y-m-d')}");
            }

            $currentDate->addDay();
        }

        if (count($generated) > 0) {
            Log::info("Total slots generated: " . count($generated) . " for creator {$creatorId}");
        }

        return $generated;
    }

    /**
     * Génère les time slots pour un créateur pour une date spécifique
     */
    private function generateTimeSlotsForDate(int $creatorId, Carbon $date): array
    {
        $creator = Creator::findOrFail($creatorId);
        $dayOfWeek = strtolower($date->format('l'));
        $generated = [];
        
        // Récupérer les availabilities pour ce jour
        $availabilities = Availability::where('creator_id', $creator->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->get();

        foreach ($availabilities as $availability) {
            $slots = $this->createSlotsForAvailability($creator, $availability, $date);
            $generated = array_merge($generated, $slots);
        }

        return $generated;
    }

    /**
     * Vérifie si un time slot spécifique est disponible pour réservation
     */
    public function isTimeSlotAvailable(int $timeSlotId, int $eventTypeId): bool
    {
        $timeSlot = \App\Models\TimeSlot\TimeSlot::find($timeSlotId);
        
        if (!$timeSlot || $timeSlot->status !== 'available') {
            return false;
        }

        $eventType = EventType::find($eventTypeId);
        if (!$eventType) {
            return false;
        }

        // Vérifier que le slot appartient au bon créateur
        if ($timeSlot->creator_id !== $eventType->creator_id) {
            return false;
        }

        // Vérifier que le slot peut accommoder la durée de l'événement
        $slotDuration = $timeSlot->start_time->diffInMinutes($timeSlot->end_time);
        if ($slotDuration < $eventType->default_duration) {
            return false;
        }

        // Vérifier que le slot n'est pas dans le passé
        if ($timeSlot->start_time->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Réserve un time slot (change son statut à 'booked')
     */
    public function bookTimeSlot(int $timeSlotId): bool
    {
        $timeSlot = \App\Models\TimeSlot\TimeSlot::find($timeSlotId);
        
        if (!$timeSlot || $timeSlot->status !== 'available') {
            return false;
        }

        return $timeSlot->update(['status' => 'booked']);
    }

    /**
     * Libère un time slot (remet son statut à 'available')
     */
    public function releaseTimeSlot(int $timeSlotId): bool
    {
        $timeSlot = \App\Models\TimeSlot\TimeSlot::find($timeSlotId);
        
        if (!$timeSlot || $timeSlot->status !== 'booked') {
            return false;
        }

        return $timeSlot->update(['status' => 'available']);
    }

    /**
     * Bloque un time slot manuellement (créateur peut bloquer ses propres slots)
     */
    public function blockTimeSlot(int $timeSlotId, int $creatorId, ?string $reason = null): bool
    {
        $timeSlot = \App\Models\TimeSlot\TimeSlot::where('id', $timeSlotId)
            ->where('creator_id', $creatorId)
            ->where('status', 'available')
            ->first();
        
        if (!$timeSlot) {
            return false;
        }

        return $timeSlot->update([
            'status' => 'blocked',
            'creator_notes' => $reason
        ]);
    }

    /**
     * Débloquer un time slot
     */
    public function unblockTimeSlot(int $timeSlotId, int $creatorId): bool
    {
        $timeSlot = \App\Models\TimeSlot\TimeSlot::where('id', $timeSlotId)
            ->where('creator_id', $creatorId)
            ->where('status', 'blocked')
            ->first();
        
        if (!$timeSlot) {
            return false;
        }

        return $timeSlot->update([
            'status' => 'available',
            'creator_notes' => null
        ]);
    }

    /**
     * Génère les time slots pour un créateur basé sur ses availabilities
     * (Méthode appelée par la commande artisan ou manuellement)
     */
    public function generateTimeSlotsForCreator(int $creatorId, Carbon $startDate, Carbon $endDate): array
    {
        $creator = Creator::findOrFail($creatorId);
        $generated = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dayOfWeek = strtolower($currentDate->format('l'));
            
            // Récupérer les availabilities pour ce jour
            $availabilities = Availability::where('creator_id', $creator->id)
                ->where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->get();

            foreach ($availabilities as $availability) {
                $slots = $this->createSlotsForAvailability($creator, $availability, $currentDate);
                $generated = array_merge($generated, $slots);
            }

            $currentDate->addDay();
        }
        return $generated;
    }

    /**
     * Crée les time slots pour une availability donnée sur une date précise
     */
    private function createSlotsForAvailability(Creator $creator, Availability $availability, Carbon $date): array
    {
        $createdSlots = [];
        $creatorTimezone = $creator->timezone ?? 'UTC';
        
        // Créer le datetime de début et fin dans le timezone du créateur
        // Utiliser createFromFormat pour éviter les problèmes de conversion de timezone
        $slotStart = Carbon::createFromFormat(
            'Y-m-d H:i', 
            $date->format('Y-m-d') . ' ' . $availability->start_time, 
            $creatorTimezone
        );
            
        $dayEnd = Carbon::createFromFormat(
            'Y-m-d H:i', 
            $date->format('Y-m-d') . ' ' . $availability->end_time, 
            $creatorTimezone
        );

        // Générer des créneaux de 30 minutes par défaut (configurable)
        $slotDuration = 30; // minutes
        
        while ($slotStart->copy()->addMinutes($slotDuration)->lte($dayEnd)) {
            $slotEnd = $slotStart->copy()->addMinutes($slotDuration);
            
            // Vérifier si le slot n'existe pas déjà
            $existingSlot = \App\Models\TimeSlot\TimeSlot::where('creator_id', $creator->id)
                ->where('start_time', $slotStart->utc())
                ->first();

            if (!$existingSlot) {
                $timeSlot = \App\Models\TimeSlot\TimeSlot::create([
                    'creator_id' => $creator->id,
                    'start_time' => $slotStart->utc(),
                    'end_time' => $slotEnd->utc(),
                    'timezone' => $creatorTimezone,
                    'status' => 'available',
                    'generated_for_date' => $date->format('Y-m-d'),
                    'is_recurring_slot' => true,
                ]);
                
                $createdSlots[] = $timeSlot;
            }
            
            $slotStart->addMinutes($slotDuration);
        }

        return $createdSlots;
    }

    /**
     * Nettoie les time slots passés (change leur statut à 'past')
     */
    public function cleanupPastSlots(): int
    {
        return \App\Models\TimeSlot\TimeSlot::where('end_time', '<', now())
            ->whereIn('status', ['available', 'blocked'])
            ->update(['status' => 'past']);
    }

    /**
     * Récupère les statistiques des time slots pour un créateur
     */
    public function getCreatorSlotsStats(int $creatorId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $query = \App\Models\TimeSlot\TimeSlot::where('creator_id', $creatorId);
        
        if ($startDate && $endDate) {
            $query->whereBetween('start_time', [$startDate, $endDate]);
        }

        $total = $query->count();
        $available = (clone $query)->where('status', 'available')->count();
        $booked = (clone $query)->where('status', 'booked')->count();
        $blocked = (clone $query)->where('status', 'blocked')->count();
        $past = (clone $query)->where('status', 'past')->count();

        return [
            'total' => $total,
            'available' => $available,
            'booked' => $booked,
            'blocked' => $blocked,
            'past' => $past,
            'utilization_rate' => $total > 0 ? round(($booked / $total) * 100, 2) : 0,
        ];
    }
}