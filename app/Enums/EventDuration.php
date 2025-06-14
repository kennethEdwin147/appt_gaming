<?php

namespace App\Enums;

enum EventDuration: int
{
    case MINUTES_30 = 30;
    case MINUTES_35 = 35;
    case MINUTES_40 = 40;
    case MINUTES_45 = 45;
    case MINUTES_50 = 50;
    case MINUTES_55 = 55;
    case MINUTES_60 = 60;
    case MINUTES_65 = 65;
    case MINUTES_70 = 70;
    case MINUTES_75 = 75;
    case MINUTES_80 = 80;
    case MINUTES_85 = 85;
    case MINUTES_90 = 90;
    case MINUTES_95 = 95;
    case MINUTES_100 = 100;
    case MINUTES_105 = 105;
    case MINUTES_110 = 110;
    case MINUTES_115 = 115;
    case MINUTES_120 = 120;

    /**
     * Obtenir le libellé formaté de la durée
     *
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::MINUTES_30 => '30 minutes',
            self::MINUTES_35 => '35 minutes',
            self::MINUTES_40 => '40 minutes',
            self::MINUTES_45 => '45 minutes',
            self::MINUTES_50 => '50 minutes',
            self::MINUTES_55 => '55 minutes',
            self::MINUTES_60 => '1 heure',
            self::MINUTES_65 => '1h05',
            self::MINUTES_70 => '1h10',
            self::MINUTES_75 => '1h15',
            self::MINUTES_80 => '1h20',
            self::MINUTES_85 => '1h25',
            self::MINUTES_90 => '1h30',
            self::MINUTES_95 => '1h35',
            self::MINUTES_100 => '1h40',
            self::MINUTES_105 => '1h45',
            self::MINUTES_110 => '1h50',
            self::MINUTES_115 => '1h55',
            self::MINUTES_120 => '2 heures',
        };
    }

    /**
     * Obtenir la valeur en minutes
     *
     * @return int
     */
    public function minutes(): int
    {
        return $this->value;
    }

    /**
     * Obtenir la valeur en heures (format décimal)
     *
     * @return float
     */
    public function hours(): float
    {
        return $this->value / 60;
    }

    /**
     * Obtenir la valeur formatée en heures et minutes
     *
     * @return string
     */
    public function formatted(): string
    {
        $hours = floor($this->value / 60);
        $minutes = $this->value % 60;

        if ($hours == 0) {
            return "{$this->value} minutes";
        } elseif ($minutes == 0) {
            return $hours == 1 ? "1 heure" : "{$hours} heures";
        } else {
            return "{$hours}h" . str_pad($minutes, 2, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Obtenir toutes les durées disponibles
     *
     * @return array
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $duration) {
            $options[$duration->value] = $duration->label();
        }
        return $options;
    }
}
