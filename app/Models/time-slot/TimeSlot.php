<?php

namespace App\Models\TimeSlot;

use App\Models\creator\Creator;
use App\Models\reservation\Reservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TimeSlot extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'creator_id',
        'start_time',
        'end_time',
        'timezone',
        'status',
        'generated_for_date',
        'is_recurring_slot',
        'custom_price',
        'creator_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'generated_for_date' => 'date',
            'is_recurring_slot' => 'boolean',
            'custom_price' => 'decimal:2',
        ];
    }

    /**
     * Get the creator that owns the time slot.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }

    /**
     * Get the reservations for this time slot.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Scope for available time slots.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    /**
     * Scope for booked time slots.
     */
    public function scopeBooked($query)
    {
        return $query->where('status', 'booked');
    }

    /**
     * Scope for blocked time slots.
     */
    public function scopeBlocked($query)
    {
        return $query->where('status', 'blocked');
    }

    /**
     * Scope for past time slots.
     */
    public function scopePast($query)
    {
        return $query->where('status', 'past')
                    ->orWhere('end_time', '<', now());
    }

    /**
     * Scope for upcoming time slots.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now());
    }

    /**
     * Scope for today's time slots.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('start_time', today());
    }

    /**
     * Scope for specific date.
     */
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('start_time', $date);
    }

    /**
     * Scope for specific creator.
     */
    public function scopeForCreator($query, $creatorId)
    {
        return $query->where('creator_id', $creatorId);
    }

    /**
     * Scope for recurring slots.
     */
    public function scopeRecurring($query)
    {
        return $query->where('is_recurring_slot', true);
    }

    /**
     * Scope for manual slots.
     */
    public function scopeManual($query)
    {
        return $query->where('is_recurring_slot', false);
    }

    /**
     * Scope for bookable slots (available and in future).
     */
    public function scopeBookable($query)
    {
        return $query->available()
                    ->where('start_time', '>', now());
    }

    /**
     * Check if time slot is available for booking.
     */
    public function isBookable(): bool
    {
        return $this->status === 'available' && 
               $this->start_time > now();
    }

    /**
     * Check if time slot is in the past.
     */
    public function isPast(): bool
    {
        return $this->end_time < now();
    }

    /**
     * Check if time slot is currently active.
     */
    public function isActive(): bool
    {
        $now = now();
        return $this->start_time <= $now && $this->end_time > $now;
    }

    /**
     * Get duration in minutes.
     */
    public function getDurationInMinutesAttribute(): int
    {
        return $this->start_time->diffInMinutes($this->end_time);
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
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    /**
     * Get formatted date and time range.
     */
    public function getFullTimeRangeAttribute(): string
    {
        return $this->start_time->format('d/m/Y H:i') . ' - ' . $this->end_time->format('H:i');
    }

    /**
     * Get day name in French.
     */
    public function getDayNameAttribute(): string
    {
        $days = [
            'Monday' => 'Lundi',
            'Tuesday' => 'Mardi',
            'Wednesday' => 'Mercredi',
            'Thursday' => 'Jeudi',
            'Friday' => 'Vendredi',
            'Saturday' => 'Samedi',
            'Sunday' => 'Dimanche',
        ];
        
        return $days[$this->start_time->format('l')] ?? $this->start_time->format('l');
    }

    /**
     * Mark time slot as booked.
     */
    public function markAsBooked(): bool
    {
        if (!$this->isBookable()) {
            return false;
        }
        
        return $this->update(['status' => 'booked']);
    }

    /**
     * Mark time slot as available.
     */
    public function markAsAvailable(): bool
    {
        if ($this->isPast()) {
            return false;
        }
        
        return $this->update(['status' => 'available']);
    }

    /**
     * Mark time slot as blocked.
     */
    public function markAsBlocked(): bool
    {
        if ($this->isPast()) {
            return false;
        }
        
        return $this->update(['status' => 'blocked']);
    }

    /**
     * Mark time slot as past (for cleanup).
     */
    public function markAsPast(): bool
    {
        return $this->update(['status' => 'past']);
    }

    /**
     * Get conflicts with other time slots.
     */
    public function getConflicts()
    {
        return static::where('creator_id', $this->creator_id)
            ->where('id', '!=', $this->id)
            ->where(function($query) {
                $query->where(function($q) {
                    $q->where('start_time', '<', $this->end_time)
                      ->where('end_time', '>', $this->start_time);
                });
            })
            ->get();
    }

    /**
     * Check if slot has conflicts.
     */
    public function hasConflicts(): bool
    {
        return $this->getConflicts()->isNotEmpty();
    }
}