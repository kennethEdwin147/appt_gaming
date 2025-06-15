<?php

namespace App\Enums;

enum MaxParticipants: int
{
    case ONE = 1;
    case TWO = 2;
    case THREE = 3;
    case FOUR = 4;

    /**
     * Obtenir le libellé formaté du nombre de participants
     *
     * @return string
     */
    public function label(): string
    {
        return match($this) {
            self::ONE => '1 participant',
            self::TWO => '2 participants',
            self::THREE => '3 participants',
            self::FOUR => '4 participants',
        };
    }

    /**
     * Obtenir toutes les options disponibles
     *
     * @return array
     */
    public static function getOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }
}
