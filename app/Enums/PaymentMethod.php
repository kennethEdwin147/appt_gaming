<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CREDIT_CARD = 'credit_card';
    case PAYPAL = 'paypal';
    case BANK_TRANSFER = 'bank_transfer';
    case STRIPE = 'stripe';
    case CASH = 'cash';
    case OTHER = 'other';

    /**
     * Get the display name for the payment method
     */
    public function label(): string
    {
        return match($this) {
            self::CREDIT_CARD => 'Carte de crédit',
            self::PAYPAL => 'PayPal',
            self::BANK_TRANSFER => 'Virement bancaire',
            self::STRIPE => 'Stripe',
            self::CASH => 'Espèces',
            self::OTHER => 'Autre',
        };
    }

    /**
     * Get the icon class for the payment method
     */
    public function icon(): string
    {
        return match($this) {
            self::CREDIT_CARD => 'fa-credit-card',
            self::PAYPAL => 'fa-paypal',
            self::BANK_TRANSFER => 'fa-university',
            self::STRIPE => 'fa-stripe-s',
            self::CASH => 'fa-money-bill',
            self::OTHER => 'fa-money-check',
        };
    }

    /**
     * Get all payment methods as an array for select options
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(function ($method) {
            return [$method->value => $method->label()];
        })->toArray();
    }

    /**
     * Check if a payment method is valid
     */
    public static function isValid(string $method): bool
    {
        return in_array($method, array_column(self::cases(), 'value'));
    }

    /**
     * Get a payment method by its value
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
