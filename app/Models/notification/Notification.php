<?php

namespace App\Models\Notification;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'subject',
        'message',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for read notifications.
     */
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    /**
     * Scope for specific notification type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for recent notifications.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope ordered by latest.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Check if notification is read.
     */
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Check if notification is unread.
     */
    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): bool
    {
        if ($this->isRead()) {
            return true;
        }
        
        return $this->update(['read_at' => now()]);
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread(): bool
    {
        if ($this->isUnread()) {
            return true;
        }
        
        return $this->update(['read_at' => null]);
    }

    /**
     * Get notification age in human readable format.
     */
    public function getAgeAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get notification icon based on type.
     */
    public function getIconAttribute(): string
    {
        return match($this->type) {
            'reservation_created' => 'fa-calendar-plus',
            'reservation_confirmed' => 'fa-calendar-check',
            'reservation_cancelled' => 'fa-calendar-times',
            'reservation_reminder' => 'fa-bell',
            'payment_received' => 'fa-money-bill-wave',
            'payment_failed' => 'fa-exclamation-triangle',
            'creator_account_confirmation' => 'fa-user-check',
            'password_changed' => 'fa-key',
            'meeting_link_changed' => 'fa-link',
            'new_message' => 'fa-envelope',
            'system_update' => 'fa-cog',
            default => 'fa-bell',
        };
    }

    /**
     * Get notification color based on type.
     */
    public function getColorAttribute(): string
    {
        return match($this->type) {
            'reservation_created' => 'primary',
            'reservation_confirmed' => 'success',
            'reservation_cancelled' => 'danger',
            'reservation_reminder' => 'warning',
            'payment_received' => 'success',
            'payment_failed' => 'danger',
            'creator_account_confirmation' => 'success',
            'password_changed' => 'info',
            'meeting_link_changed' => 'info',
            'new_message' => 'primary',
            'system_update' => 'secondary',
            default => 'light',
        ];
    }

    /**
     * Get notification priority.
     */
    public function getPriorityAttribute(): string
    {
        return match($this->type) {
            'payment_failed' => 'high',
            'reservation_cancelled' => 'high',
            'reservation_reminder' => 'high',
            'reservation_confirmed' => 'medium',
            'reservation_created' => 'medium',
            'payment_received' => 'medium',
            'creator_account_confirmation' => 'medium',
            'password_changed' => 'medium',
            'meeting_link_changed' => 'low',
            'new_message' => 'low',
            'system_update' => 'low',
            default => 'low',
        };
    }

    /**
     * Get type label in French.
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'reservation_created' => 'Nouvelle réservation',
            'reservation_confirmed' => 'Réservation confirmée',
            'reservation_cancelled' => 'Réservation annulée',
            'reservation_reminder' => 'Rappel de réservation',
            'payment_received' => 'Paiement reçu',
            'payment_failed' => 'Échec de paiement',
            'creator_account_confirmation' => 'Compte créateur confirmé',
            'password_changed' => 'Mot de passe modifié',
            'meeting_link_changed' => 'Lien de réunion modifié',
            'new_message' => 'Nouveau message',
            'system_update' => 'Mise à jour système',
            default => 'Notification',
        ];
    }

    /**
     * Create a new notification for a user.
     */
    public static function createForUser(
        User $user, 
        string $type, 
        string $subject, 
        string $message
    ): self {
        return self::create([
            'user_id' => $user->id,
            'type' => $type,
            'subject' => $subject,
            'message' => $message,
        ]);
    }

    /**
     * Create reservation confirmation notification.
     */
    public static function reservationConfirmed(User $user, Reservation $reservation): self
    {
        return self::createForUser(
            $user,
            'reservation_confirmed',
            'Réservation confirmée',
            "Votre réservation pour \"{$reservation->eventType->name}\" le {$reservation->formatted_date_time} a été confirmée."
        );
    }

    /**
     * Create reservation reminder notification.
     */
    public static function reservationReminder(User $user, Reservation $reservation): self
    {
        return self::createForUser(
            $user,
            'reservation_reminder',
            'Rappel de réservation',
            "N'oubliez pas votre session \"{$reservation->eventType->name}\" dans 24 heures."
        );
    }

    /**
     * Create payment received notification.
     */
    public static function paymentReceived(User $user, Transaction $transaction): self
    {
        return self::createForUser(
            $user,
            'payment_received',
            'Paiement reçu',
            "Paiement de {$transaction->formatted_creator_amount} reçu pour votre réservation."
        );
    }

    /**
     * Create new reservation notification for creator.
     */
    public static function newReservationForCreator(User $creator, Reservation $reservation): self
    {
        return self::createForUser(
            $creator,
            'reservation_created',
            'Nouvelle réservation',
            "Nouvelle réservation pour \"{$reservation->eventType->name}\" le {$reservation->formatted_date_time}."
        );
    }

    /**
     * Mark all notifications as read for a user.
     */
    public static function markAllAsReadForUser(User $user): int
    {
        return self::where('user_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Get unread count for a user.
     */
    public static function getUnreadCountForUser(User $user): int
    {
        return self::where('user_id', $user->id)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Delete old notifications (cleanup).
     */
    public static function deleteOldNotifications(int $days = 90): int
    {
        return self::where('created_at', '<', now()->subDays($days))
            ->delete();
    }
}