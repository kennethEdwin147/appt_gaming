<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\creator\Creator;
use App\Models\customer\Customer;
use App\Models\notification\Notification;
use App\Models\reservation\Reservation;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the creator profile associated with the user.
     */
    public function creator(): HasOne
    {
        return $this->hasOne(Creator::class);
    }

    /**
     * Get the customer profile associated with the user.
     */
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the reservations made by the user.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }


    /**
     * Scope for active users.
     */
    public function scopeActive($query)
    {
        return $query->whereHas('customer', function($q) {
            $q->where('status', 'active');
        })->orWhereHas('creator', function($q) {
            $q->whereNotNull('confirmed_at');
        });
    }

    /**
     * Scope for creators.
     */
    public function scopeCreators($query)
    {
        return $query->where('role', 'creator')->has('creator');
    }

    /**
     * Scope for customers.
     */
    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer')->has('customer');
    }

    /**
     * Get full name accessor.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Check if user is a creator.
     */
    public function isCreator(): bool
    {
        return $this->role === 'creator' && $this->creator()->exists();
    }

    /**
     * Check if user is a customer.
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer' && $this->customer()->exists();
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
