<?php

namespace App\Enums;

enum PaymentProvider: string
{
    case STRIPE = 'stripe';
    case PAYPAL = 'paypal';
    case MANUAL = 'manual';
    
    /**
     * Get the display name for the payment provider
     */
    public function label(): string
    {
        return match($this) {
            self::STRIPE => 'Stripe',
            self::PAYPAL => 'PayPal',
            self::MANUAL => 'Paiement manuel',
        };
    }
    
    /**
     * Get the icon class for the payment provider
     */
    public function icon(): string
    {
        return match($this) {
            self::STRIPE => 'fa-stripe-s',
            self::PAYPAL => 'fa-paypal',
            self::MANUAL => 'fa-money-bill',
        };
    }
    
    /**
     * Get all payment providers as an array for select options
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(function ($provider) {
            return [$provider->value => $provider->label()];
        })->toArray();
    }
    
    /**
     * Check if a payment provider is valid
     */
    public static function isValid(string $provider): bool
    {
        return in_array($provider, array_column(self::cases(), 'value'));
    }
    
    /**
     * Get a payment provider by its value
     */
    public static function fromValue(string $value): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }
        
        return null;
    }
}
