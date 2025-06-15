<?php

namespace App\Models\availability;

use App\Models\schedule\Schedule;
use App\Models\reservation\Reservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Availability extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'schedule_id',
        'day_of_week',
        'start_time',
        'end_time',
        'effective_from',
        'effective_until',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_time' => 'datetime:H:i:s',
            'end_time' => 'datetime:H:i:s',
            'effective_from' => 'date',
            'effective_until' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the schedule that owns the availability.
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Get the reservations for this availability.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Scope for active availabilities.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for current effective period.
     */
    public function scopeCurrentlyEffective($query)
    {
        return $query->where(function($q) {
            $q->whereNull('effective_from')
              ->orWhere('effective_from', '<=', now()->toDateString());
        })->where(function($q) {
            $q->whereNull('effective_until')
              ->orWhere('effective_until', '>=', now()->toDateString());
        });
    }

    /**
     * Scope for specific day of week.
     */
    public function scopeForDay($query, string $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    /**
     * Scope for specific time range.
     */
    public function scopeForTimeRange($query, string $startTime, string $endTime)
    {
        return $query->where('start_time', '<=', $startTime)
                    ->where('end_time', '>=', $endTime);
    }

    /**
     * Scope for availabilities with no conflicts.
     */
    public function scopeWithoutConflicts($query, $scheduleId, $dayOfWeek, $startTime, $endTime, $excludeId = null)
    {
        return $query->where('schedule_id', $scheduleId)
                    ->where('day_of_week', $dayOfWeek)
                    ->where(function($q) use ($startTime, $endTime) {
                        $q->where(function($subQ) use ($startTime, $endTime) {
                            $subQ->where('start_time', '<', $endTime)
                                 ->where('end_time', '>', $startTime);
                        });
                    })
                    ->when($excludeId, function($q) use ($excludeId) {
                        $q->where('id', '!=', $excludeId);
                    });
    }

    /**
     * Check if availability is currently effective.
     */
    public function isCurrentlyEffective(): bool
    {
        $now = now()->toDateString();
        
        $fromValid = is_null($this->effective_from) || $this->effective_from <= $now;
        $untilValid = is_null($this->effective_until) || $this->effective_until >= $now;
        
        return $fromValid && $untilValid;
    }

    /**
     * Check if availability is active and effective.
     */
    public function isAvailable(): bool
    {
        return $this->is_active && $this->isCurrentlyEffective();
    }

    /**
     * Get duration in minutes.
     */
    public function getDurationInMinutesAttribute(): int
    {
        $start = \Carbon\Carbon::createFromTimeString($this->start_time);
        $end = \Carbon\Carbon::createFromTimeString($this->end_time);
        
        return $start->diffInMinutes($end);
    }

    /**
     * Get duration in hours.
     */
    public function getDurationInHoursAttribute(): float
    {
        return $this->duration_in_minutes / 60;
    }

    /**
     * Get formatted time range.
     */
    public function getTimeRangeAttribute(): string
    {
        return \Carbon\Carbon::createFromTimeString($this->start_time)->format('H:i') . 
               ' - ' . 
               \Carbon\Carbon::createFromTimeString($this->end_time)->format('H:i');
    }

    /**
     * Get day name in French.
     */
    public function getDayNameAttribute(): string
    {
        $days = [
            'monday' => 'Lundi',
            'tuesday' => 'Mardi',
            'wednesday' => 'Mercredi',
            'thursday' => 'Jeudi',
            'friday' => 'Vendredi',
            'saturday' => 'Samedi',
            'sunday' => 'Dimanche',
        ];
        
        return $days[$this->day_of_week] ?? $this->day_of_week;
    }

    /**
     * Check if there are conflicts with another availability.
     */
    public function hasConflictWith(string $startTime, string $endTime): bool
    {
        return $this->start_time < $endTime && $this->end_time > $startTime;
    }

    /**
     * Get reserved time slots for a specific date.
     */
    public function getReservedTimeSlotsForDate(\Carbon\Carbon $date): array
    {
        return $this->reservations()
            ->whereDate('reserved_datetime', $date->toDateString())
            ->whereIn('status', ['confirmed', 'pending'])
            ->get()
            ->map(function($reservation) {
                return [
                    'start' => $reservation->reserved_datetime->format('H:i:s'),
                    'end' => $reservation->reserved_datetime->addMinutes($reservation->eventType->default_duration)->format('H:i:s'),
                    'reservation_id' => $reservation->id,
                ];
            })
            ->toArray();
    }
}