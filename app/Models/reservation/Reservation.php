<?php

namespace App\Models\Reservation;

use App\Models\User;
use App\Models\creator\Creator;
use App\Models\EventType\EventType;
use App\Models\availability\Availability;
use App\Models\TimeSlot\TimeSlot;
use App\Models\transaction\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Reservation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'creator_id',
        'event_type_id',
        'availability_id',
        'time_slot_id',
        'guest_first_name',
        'guest_last_name',
        'reserved_datetime',
        'timezone',
        'meeting_link',
        'reservation_time',
        'status',
        'price_paid',
        'special_requests',
        'participants_count',
        'payment_status',
        'payment_id',
        'cancellation_reason',
        'cancelled_at',
        'confirmed_at',
        'completed_at',
        'rescheduled_at',
        'no_show_at',
        'actual_start',
        'actual_end',
        'session_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'reserved_datetime' => 'datetime',
            'reservation_time' => 'datetime',
            'cancelled_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
            'rescheduled_at' => 'datetime',
            'no_show_at' => 'datetime',
            'actual_start' => 'datetime',
            'actual_end' => 'datetime',
            'price_paid' => 'decimal:2',
        ];
    }

    /**
     * Get the user that made the reservation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the creator for this reservation.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }

    /**
     * Get the event type for this reservation.
     */
    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class);
    }

    /**
     * Get the availability used for this reservation.
     */
    public function availability(): BelongsTo
    {
        return $this->belongsTo(Availability::class);
    }

    /**
     * Get the time slot for this reservation.
     */
    public function timeSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class);
    }

    /**
     * Get the transaction for this reservation.
     */
    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }

    /**
     * Scope for pending reservations.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for confirmed reservations.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope for cancelled reservations.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope for completed reservations.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for rescheduled reservations.
     */
    public function scopeRescheduled($query)
    {
        return $query->where('status', 'rescheduled');
    }

    /**
     * Scope for upcoming reservations.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('reserved_datetime', '>', now())
                    ->whereIn('status', ['confirmed', 'pending']);
    }

    /**
     * Scope for past reservations.
     */
    public function scopePast($query)
    {
        return $query->where('reserved_datetime', '<', now());
    }

    /**
     * Scope for today's reservations.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('reserved_datetime', today());
    }

    /**
     * Scope for this week's reservations.
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('reserved_datetime', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope for paid reservations.
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'completed');
    }

    /**
     * Scope for unpaid reservations.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'pending');
    }

    /**
     * Scope for specific creator.
     */
    public function scopeForCreator($query, $creatorId)
    {
        return $query->where('creator_id', $creatorId);
    }

    /**
     * Scope for specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Check if reservation is confirmed.
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if reservation is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if reservation is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if reservation is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if reservation is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->reserved_datetime > now() && 
               in_array($this->status, ['confirmed', 'pending']);
    }

    /**
     * Check if reservation is in the past.
     */
    public function isPast(): bool
    {
        return $this->reserved_datetime < now();
    }

    /**
     * Check if reservation is paid.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'completed';
    }

    /**
     * Check if reservation can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']) && 
               $this->reserved_datetime > now()->addHours(24); // 24h before
    }

    /**
     * Check if reservation can be rescheduled.
     */
    public function canBeRescheduled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']) && 
               $this->reserved_datetime > now()->addHours(48); // 48h before
    }

    /**
     * Get guest full name.
     */
    public function getGuestFullNameAttribute(): ?string
    {
        if ($this->guest_first_name && $this->guest_last_name) {
            return $this->guest_first_name . ' ' . $this->guest_last_name;
        }
        
        return $this->user?->full_name;
    }

    /**
     * Get reservation end time.
     */
    public function getEndTimeAttribute(): \Carbon\Carbon
    {
        return $this->reserved_datetime->addMinutes($this->eventType->default_duration);
    }

    /**
     * Get formatted date and time.
     */
    public function getFormattedDateTimeAttribute(): string
    {
        return $this->reserved_datetime->format('d/m/Y à H:i');
    }

    /**
     * Get formatted time range.
     */
    public function getTimeRangeAttribute(): string
    {
        return $this->reserved_datetime->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    /**
     * Get status label in French.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'cancelled' => 'Annulée',
            'rescheduled' => 'Reportée',
            'completed' => 'Terminée',
            default => 'Inconnu',
        };
    }

    /**
     * Get payment status label in French.
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'En attente',
            'completed' => 'Payé',
            'failed' => 'Échec',
            'refunded' => 'Remboursé',
            default => 'Inconnu',
        };
    }

    /**
     * Get days until reservation.
     */
    public function getDaysUntilAttribute(): int
    {
        return max(0, now()->diffInDays($this->reserved_datetime, false));
    }

    /**
     * Get hours until reservation.
     */
    public function getHoursUntilAttribute(): int
    {
        return max(0, now()->diffInHours($this->reserved_datetime, false));
    }

    /**
     * Confirm the reservation.
     */
    public function confirm(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }
        
        $updated = $this->update(['status' => 'confirmed']);
        
        if ($updated && $this->timeSlot) {
            $this->timeSlot->markAsBooked();
        }
        
        return $updated;
    }

    /**
     * Cancel the reservation.
     */
    public function cancel(): bool
    {
        if (!$this->canBeCancelled()) {
            return false;
        }
        
        $updated = $this->update(['status' => 'cancelled']);
        
        if ($updated && $this->timeSlot) {
            $this->timeSlot->markAsAvailable();
        }
        
        return $updated;
    }

    /**
     * Mark as completed.
     */
    public function markAsCompleted(): bool
    {
        if ($this->status !== 'confirmed' || !$this->isPast()) {
            return false;
        }
        
        return $this->update(['status' => 'completed']);
    }

    /**
     * Get duration in minutes from event type.
     */
    public function getDurationAttribute(): int
    {
        return $this->eventType->default_duration;
    }
}