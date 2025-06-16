<?php

namespace App\Models\EventType;

use App\Models\creator\Creator;
use App\Models\reservation\Reservation;
use App\Enums\MeetingPlatform;
use App\Enums\EventDuration;
use App\Enums\MaxParticipants;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'default_duration',
        'default_price',
        'default_max_participants',
        'meeting_platform',
        'meeting_link',
        'session_type',
        'is_active',
        'creator_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'default_price' => 'decimal:2',
            'is_active' => 'boolean',
            'meeting_platform' => MeetingPlatform::class,
        ];
    }

    /**
     * Get the creator that owns the event type.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }


    /**
     * Get the reservations for this event type.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Scope for active event types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive event types.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for individual session types.
     */
    public function scopeIndividual($query)
    {
        return $query->where('session_type', 'individual');
    }

    /**
     * Scope for group session types.
     */
    public function scopeGroup($query)
    {
        return $query->where('session_type', 'group');
    }

    /**
     * Scope for specific creator.
     */
    public function scopeForCreator($query, $creatorId)
    {
        return $query->where('creator_id', $creatorId);
    }

    /**
     * Scope for bookable event types (active with active creator).
     */
    public function scopeBookable($query)
    {
        return $query->active()
            ->whereHas('creator', function($q) {
                $q->where('confirmed_at', '!=', null);
            });
    }

    /**
     * Scope by price range.
     */
    public function scopePriceRange($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice !== null) {
            $query->where('default_price', '>=', $minPrice);
        }
        
        if ($maxPrice !== null) {
            $query->where('default_price', '<=', $maxPrice);
        }
        
        return $query;
    }

    /**
     * Scope by duration range.
     */
    public function scopeDurationRange($query, $minDuration = null, $maxDuration = null)
    {
        if ($minDuration !== null) {
            $query->where('default_duration', '>=', $minDuration);
        }
        
        if ($maxDuration !== null) {
            $query->where('default_duration', '<=', $maxDuration);
        }
        
        return $query;
    }

    /**
     * Check if event type is individual session.
     */
    public function isIndividual(): bool
    {
        return $this->session_type === 'individual';
    }

    /**
     * Check if event type is group session.
     */
    public function isGroup(): bool
    {
        return $this->session_type === 'group';
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDurationAttribute(): string
    {
        if ($duration = EventDuration::tryFrom($this->default_duration)) {
            return $duration->formatted();
        }
        
        $hours = floor($this->default_duration / 60);
        $minutes = $this->default_duration % 60;
        
        if ($hours == 0) {
            return "{$this->default_duration} minutes";
        } elseif ($minutes == 0) {
            return $hours == 1 ? "1 heure" : "{$hours} heures";
        } else {
            return "{$hours}h" . str_pad($minutes, 2, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Get formatted price.
     */
    public function getFormattedPriceAttribute(): string
    {
        if ($this->default_price === null) {
            return 'Gratuit';
        }
        
        return number_format($this->default_price, 2, ',', ' ') . ' CAD';
    }

    /**
     * Get formatted participants.
     */
    public function getFormattedParticipantsAttribute(): string
    {
        if ($this->default_max_participants === null) {
            return 'Illimité';
        }
        
        if ($participants = MaxParticipants::tryFrom($this->default_max_participants)) {
            return $participants->label();
        }
        
        return $this->default_max_participants . ' participants max';
    }

    /**
     * Get meeting platform label.
     */
    public function getMeetingPlatformLabelAttribute(): string
    {
        return $this->meeting_platform?->label() ?? 'Non défini';
    }

    /**
     * Get meeting platform icon.
     */
    public function getMeetingPlatformIconAttribute(): string
    {
        return $this->meeting_platform?->icon() ?? 'fa-link';
    }

    /**
     * Get session type label.
     */
    public function getSessionTypeLabelAttribute(): string
    {
        return match($this->session_type) {
            'individual' => 'Session individuelle',
            'group' => 'Session de groupe',
            default => 'Non défini',
        };
    }

    /**
     * Get total reservations count.
     */
    public function getTotalReservationsAttribute(): int
    {
        return $this->reservations()->count();
    }

    /**
     * Get completed reservations count.
     */
    public function getCompletedReservationsAttribute(): int
    {
        return $this->reservations()
            ->where('status', 'completed')
            ->count();
    }

    /**
     * Get upcoming reservations count.
     */
    public function getUpcomingReservationsAttribute(): int
    {
        return $this->reservations()
            ->where('status', 'confirmed')
            ->where('reserved_datetime', '>', now())
            ->count();
    }

    /**
     * Get average rating (if you have a rating system).
     */
    public function getAverageRatingAttribute(): ?float
    {
        // This would need a ratings table/system
        // return $this->reservations()->avg('rating');
        return null;
    }

    /**
     * Get total revenue from this event type.
     */
    public function getTotalRevenueAttribute(): float
    {
        return $this->reservations()
            ->whereHas('transaction', function($q) {
                $q->where('status', 'completed');
            })
            ->with('transaction')
            ->get()
            ->sum('transaction.amount');
    }

    /**
     * Check if event type can be booked.
     */
    public function canBeBooked(): bool
    {
        return $this->is_active && 
               $this->creator->isConfirmed();
    }

    /**
     * Get next available slots for this event type.
     */
    public function getNextAvailableSlots(int $limit = 10)
    {
        // This would integrate with TimeSlot model
        return collect(); // Placeholder
    }
}