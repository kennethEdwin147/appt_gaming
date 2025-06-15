<?php

namespace App\Enums;

enum MeetingPlatform: string
{
    case ZOOM = 'zoom';
    case TEAMS = 'teams';
    case GOOGLE_MEET = 'google_meet';
    case DISCORD = 'discord';
    case SKYPE = 'skype';
    case JITSI = 'jitsi';
    case WEBEX = 'webex';
    case CUSTOM = 'custom';

    /**
     * Get the display name for the meeting platform
     */
    public function label(): string
    {
        return match($this) {
            self::ZOOM => 'Zoom',
            self::TEAMS => 'Microsoft Teams',
            self::GOOGLE_MEET => 'Google Meet',
            self::DISCORD => 'Discord',
            self::SKYPE => 'Skype',
            self::JITSI => 'Jitsi Meet',
            self::WEBEX => 'Cisco Webex',
            self::CUSTOM => 'Lien personnalisé',
        };
    }

    /**
     * Get the icon class for the meeting platform
     */
    public function icon(): string
    {
        return match($this) {
            self::ZOOM => 'fa-video',
            self::TEAMS => 'fa-microsoft',
            self::GOOGLE_MEET => 'fa-google',
            self::DISCORD => 'fa-discord',
            self::SKYPE => 'fa-skype',
            self::JITSI => 'fa-video',
            self::WEBEX => 'fa-video',
            self::CUSTOM => 'fa-link',
        };
    }

    /**
     * Get all meeting platforms as an array for select options
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(function ($platform) {
            return [$platform->value => $platform->label()];
        })->toArray();
    }

    /**
     * Get the default meeting platform
     */
    public static function default(): self
    {
        return self::ZOOM;
    }

    /**
     * Check if a meeting platform is valid
     */
    public static function isValid(string $platform): bool
    {
        return in_array($platform, array_column(self::cases(), 'value'));
    }

    /**
     * Get a meeting platform by its value
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
