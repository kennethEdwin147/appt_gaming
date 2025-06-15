<?php

namespace App\Models\customer;

use App\Models\User;
use App\Models\reservation\Reservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'phone',
        'phone_verified_at',
        'date_of_birth',
        'timezone',
        'language',
        'status',
        'last_activity_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'phone_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'last_activity_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the customer profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reservations for the customer.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'user_id', 'user_id');
    }

    /**
     * Scope for active customers.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for inactive customers.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope for suspended customers.
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    /**
     * Scope for customers with verified phone.
     */
    public function scopePhoneVerified($query)
    {
        return $query->whereNotNull('phone_verified_at');
    }

    /**
     * Scope for customers with recent activity.
     */
    public function scopeRecentlyActive($query, $days = 30)
    {
        return $query->where('last_activity_at', '>=', now()->subDays($days));
    }

    /**
     * Check if customer is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if customer has verified phone.
     */
    public function hasVerifiedPhone(): bool
    {
        return !is_null($this->phone_verified_at);
    }

    /**
     * Get customer's full name from user.
     */
    public function getFullNameAttribute(): string
    {
        return $this->user->full_name;
    }

    /**
     * Get customer's age.
     */
    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth?->age;
    }

    /**
     * Get total spent by customer.
     */
    public function getTotalSpentAttribute(): float
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
     * Get upcoming reservations count.
     */
    public function getUpcomingReservationsCountAttribute(): int
    {
        return $this->reservations()
            ->where('status', 'confirmed')
            ->where('reserved_datetime', '>', now())
            ->count();
    }

    /**
     * Get completed reservations count.
     */
    public function getCompletedReservationsCountAttribute(): int
    {
        return $this->reservations()
            ->where('status', 'completed')
            ->count();
    }

    /**
     * Update last activity timestamp.
     */
    public function updateActivity(): void
    {
        $this->update(['last_activity_at' => now()]);
    }
}