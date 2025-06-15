<?php

namespace App\Models\Schedule;

use App\Models\User;
use App\Models\availability\Availability;
use App\Models\event_type\EventType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'creator_id',
        'name',
        'effective_from',
        'effective_until',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'effective_from' => 'date',
            'effective_until' => 'date',
        ];
    }

    /**
     * Get the creator that owns the schedule.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * Get the availabilities for the schedule.
     */
    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    /**
     * Get the event types using this schedule.
     */
    public function eventTypes(): HasMany
    {
        return $this->hasMany(EventType::class);
    }

    /**
     * Scope for active schedules.
     */
    public function scopeActive($query)
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
     * Scope for schedules with active availabilities.
     */
    public function scopeWithActiveAvailabilities($query)
    {
        return $query->whereHas('availabilities', function($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Scope for schedules by creator.
     */
    public function scopeForCreator($query, $creatorId)
    {
        return $query->where('creator_id', $creatorId);
    }

    /**
     * Check if schedule is currently active.
     */
    public function isActive(): bool
    {
        $now = now()->toDateString();
        
        $fromValid = is_null($this->effective_from) || $this->effective_from <= $now;
        $untilValid = is_null($this->effective_until) || $this->effective_until >= $now;
        
        return $fromValid && $untilValid;
    }

    /**
     * Get active availabilities for a specific day.
     */
    public function getAvailabilitiesForDay(string $dayOfWeek)
    {
        return $this->availabilities()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('effective_from')
                  ->orWhere('effective_from', '<=', now()->toDateString());
            })
            ->where(function($q) {
                $q->whereNull('effective_until')
                  ->orWhere('effective_until', '>=', now()->toDateString());
            })
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Check if schedule has availabilities for a specific day.
     */
    public function hasAvailabilityForDay(string $dayOfWeek): bool
    {
        return $this->getAvailabilitiesForDay($dayOfWeek)->isNotEmpty();
    }

    /**
     * Get all active days of the week for this schedule.
     */
    public function getActiveDaysAttribute(): array
    {
        return $this->availabilities()
            ->where('is_active', true)
            ->distinct('day_of_week')
            ->pluck('day_of_week')
            ->toArray();
    }

    /**
     * Get total weekly hours for this schedule.
     */
    public function getTotalWeeklyHoursAttribute(): float
    {
        $totalMinutes = 0;
        
        foreach ($this->availabilities()->where('is_active', true)->get() as $availability) {
            $start = \Carbon\Carbon::createFromTimeString($availability->start_time);
            $end = \Carbon\Carbon::createFromTimeString($availability->end_time);
            $totalMinutes += $start->diffInMinutes($end);
        }
        
        return $totalMinutes / 60;
    }
}