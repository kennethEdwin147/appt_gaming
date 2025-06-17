<?php

namespace App\Models\creator;

use App\Models\User;
use App\Models\availability\Availability;
use App\Models\event_type\EventType;
use App\Models\reservation\Reservation;
use App\Models\public_profile\CreatorProfile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Creator extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'gaming_pseudo',
        'bio',
        'platform_name',
        'platform_url',
        'type',
        'timezone',
        'confirmation_token',
        'confirmed_at',
        'setup_completed_at',
        'platform_commission_rate',
        'main_game',
        'rank_info',
        'default_hourly_rate',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'confirmed_at' => 'datetime',
            'setup_completed_at' => 'datetime',
            'platform_commission_rate' => 'decimal:2',
            'default_hourly_rate' => 'decimal:2',
        ];
    }

    /**
     * Get the user that owns the creator profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the creator profile.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(CreatorProfile::class);
    }

    /**
     * Get the availabilities for the creator.
     */
    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    /**
     * Get the event types for the creator.
     */
    public function eventTypes(): HasMany
    {
        return $this->hasMany(EventType::class);
    }

    /**
     * Get the reservations for the creator.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Scope for confirmed creators.
     */
    public function scopeConfirmed($query)
    {
        return $query->whereNotNull('confirmed_at');
    }

    /**
     * Scope for active creators.
     */
    public function scopeActive($query)
    {
        return $query->confirmed()->whereHas('user', function($q) {
            $q->where('role', 'creator');
        });
    }

    /**
     * Scope for creators with available event types.
     */
    public function scopeAvailable($query)
    {
        return $query->active()->whereHas('eventTypes', function($q) {
            $q->where('is_active', true);
        });
    }

    /**
     * Check if creator is confirmed.
     */
    public function isConfirmed(): bool
    {
        return !is_null($this->confirmed_at);
    }

    /**
     * Get creator's full name from user.
     */
    public function getFullNameAttribute(): string
    {
        return $this->user->full_name;
    }

    /**
     * Get creator's display name (platform name or full name).
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->platform_name ?: $this->full_name;
    }

    /**
     * Get total earnings for the creator.
     */
    public function getTotalEarningsAttribute(): float
    {
        return $this->reservations()
            ->whereHas('transaction', function($q) {
                $q->where('status', 'completed');
            })
            ->with('transaction')
            ->get()
            ->sum(function($reservation) {
                $transaction = $reservation->transaction;
                return $transaction->amount - $transaction->platform_commission_amount;
            });
    }

    /**
     * Get active reservations count.
     */
    public function getActiveReservationsCountAttribute(): int
    {
        return $this->reservations()
            ->whereIn('status', ['confirmed', 'pending'])
            ->count();
    }
}