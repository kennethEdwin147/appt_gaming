<?php

namespace App\Enums;

enum GamingPlatform: string
{
    case PC = 'pc';
    case PS4 = 'ps4';
    case PS5 = 'ps5';
    case XBOX_ONE = 'xbox_one';
    case XBOX_SERIES = 'xbox_series';
    case NINTENDO_SWITCH = 'nintendo_switch';
    case MOBILE = 'mobile';

    /**
     * Get the display name for the gaming platform
     */
    public function label(): string
    {
        return match($this) {
            self::PC => 'PC',
            self::PS4 => 'PlayStation 4',
            self::PS5 => 'PlayStation 5',
            self::XBOX_ONE => 'Xbox One',
            self::XBOX_SERIES => 'Xbox Series X|S',
            self::NINTENDO_SWITCH => 'Nintendo Switch',
            self::MOBILE => 'Mobile',
        };
    }

    /**
     * Get the icon class for the gaming platform
     */
    public function icon(): string
    {
        return match($this) {
            self::PC => 'fa-desktop',
            self::PS4 => 'fa-playstation',
            self::PS5 => 'fa-playstation',
            self::XBOX_ONE => 'fa-xbox',
            self::XBOX_SERIES => 'fa-xbox',
            self::NINTENDO_SWITCH => 'fa-gamepad',
            self::MOBILE => 'fa-mobile-alt',
        };
    }

    /**
     * Get all gaming platforms as an array for select options
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(function ($platform) {
            return [$platform->value => $platform->label()];
        })->toArray();
    }

    /**
     * Get the default gaming platform
     */
    public static function default(): self
    {
        return self::PC;
    }

    /**
     * Check if a gaming platform is valid
     */
    public static function isValid(string $platform): bool
    {
        return in_array($platform, array_column(self::cases(), 'value'));
    }

    /**
     * Get a gaming platform by its value
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