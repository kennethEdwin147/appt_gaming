<?php

namespace App\Models\Transaction;

use App\Models\reservation\Reservation;
use App\Enums\PaymentProvider;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'reservation_id',
        'payment_provider',
        'payment_id',
        'amount',
        'currency',
        'status',
        'payment_details',
        'platform_commission_rate',
        'platform_commission_amount',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'platform_commission_rate' => 'decimal:2',
            'platform_commission_amount' => 'decimal:2',
            'payment_details' => 'array',
            'payment_provider' => PaymentProvider::class,
        ];
    }

    /**
     * Get the reservation that owns the transaction.
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Scope for completed transactions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for failed transactions.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope for refunded transactions.
     */
    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    /**
     * Scope for specific payment provider.
     */
    public function scopeByProvider($query, PaymentProvider $provider)
    {
        return $query->where('payment_provider', $provider);
    }

    /**
     * Scope for Stripe transactions.
     */
    public function scopeStripe($query)
    {
        return $query->where('payment_provider', PaymentProvider::STRIPE);
    }

    /**
     * Scope for PayPal transactions.
     */
    public function scopePaypal($query)
    {
        return $query->where('payment_provider', PaymentProvider::PAYPAL);
    }

    /**
     * Scope for manual transactions.
     */
    public function scopeManual($query)
    {
        return $query->where('payment_provider', PaymentProvider::MANUAL);
    }

    /**
     * Scope for specific currency.
     */
    public function scopeByCurrency($query, string $currency)
    {
        return $query->where('currency', $currency);
    }

    /**
     * Scope for amount range.
     */
    public function scopeAmountRange($query, $minAmount = null, $maxAmount = null)
    {
        if ($minAmount !== null) {
            $query->where('amount', '>=', $minAmount);
        }
        
        if ($maxAmount !== null) {
            $query->where('amount', '<=', $maxAmount);
        }
        
        return $query;
    }

    /**
     * Check if transaction is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if transaction is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if transaction failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if transaction is refunded.
     */
    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2, ',', ' ') . ' ' . strtoupper($this->currency);
    }

    /**
     * Get formatted platform commission.
     */
    public function getFormattedCommissionAttribute(): string
    {
        if (!$this->platform_commission_amount) {
            return '0,00 ' . strtoupper($this->currency);
        }
        
        return number_format($this->platform_commission_amount, 2, ',', ' ') . ' ' . strtoupper($this->currency);
    }

    /**
     * Get creator amount (after commission).
     */
    public function getCreatorAmountAttribute(): float
    {
        return $this->amount - ($this->platform_commission_amount ?? 0);
    }

    /**
     * Get formatted creator amount.
     */
    public function getFormattedCreatorAmountAttribute(): string
    {
        return number_format($this->creator_amount, 2, ',', ' ') . ' ' . strtoupper($this->currency);
    }

    /**
     * Get commission percentage.
     */
    public function getCommissionPercentageAttribute(): float
    {
        if (!$this->platform_commission_rate) {
            return 0.0;
        }
        
        return $this->platform_commission_rate * 100;
    }

    /**
     * Get payment provider label.
     */
    public function getPaymentProviderLabelAttribute(): string
    {
        return $this->payment_provider?->label() ?? 'Non défini';
    }

    /**
     * Get payment provider icon.
     */
    public function getPaymentProviderIconAttribute(): string
    {
        return $this->payment_provider?->icon() ?? 'fa-money-bill';
    }

    /**
     * Get status label in French.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'En attente',
            'completed' => 'Terminé',
            'failed' => 'Échec',
            'refunded' => 'Remboursé',
            'cancelled' => 'Annulé',
            default => 'Inconnu',
        };
    }

    /**
     * Get status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'completed' => 'success',
            'failed' => 'danger',
            'refunded' => 'info',
            'cancelled' => 'secondary',
            default => 'light',
        };
    }

    /**
     * Calculate commission amount based on amount and rate.
     */
    public function calculateCommission(): float
    {
        if (!$this->platform_commission_rate) {
            return 0.0;
        }
        
        return $this->amount * $this->platform_commission_rate;
    }

    /**
     * Update commission amount.
     */
    public function updateCommission(): bool
    {
        $commissionAmount = $this->calculateCommission();
        
        return $this->update([
            'platform_commission_amount' => $commissionAmount
        ]);
    }

    /**
     * Mark transaction as completed.
     */
    public function markAsCompleted(): bool
    {
        return $this->update(['status' => 'completed']);
    }

    /**
     * Mark transaction as failed.
     */
    public function markAsFailed(): bool
    {
        return $this->update(['status' => 'failed']);
    }

    /**
     * Mark transaction as refunded.
     */
    public function markAsRefunded(): bool
    {
        return $this->update(['status' => 'refunded']);
    }

    /**
     * Get transaction reference for display.
     */
    public function getReferenceAttribute(): string
    {
        return 'TXN-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Check if transaction can be refunded.
     */
    public function canBeRefunded(): bool
    {
        return $this->isCompleted() && 
               $this->created_at >= now()->subDays(30); // 30 days refund policy
    }

    /**
     * Get payment details safely.
     */
    public function getPaymentDetail(string $key, $default = null)
    {
        return data_get($this->payment_details, $key, $default);
    }

    /**
     * Add payment detail.
     */
    public function addPaymentDetail(string $key, $value): bool
    {
        $details = $this->payment_details ?? [];
        $details[$key] = $value;
        
        return $this->update(['payment_details' => $details]);
    }
}