<?php

namespace App\Enums;

enum Timezone: string
{
    // Amérique du Nord
    case EASTERN = 'America/Toronto';
    case PACIFIC = 'America/Vancouver';
    case CENTRAL = 'America/Chicago';
    case MOUNTAIN = 'America/Denver';
    case ATLANTIC = 'America/Halifax';
    case NEWFOUNDLAND = 'America/St_Johns';
    
    // Europe
    case PARIS = 'Europe/Paris';
    case LONDON = 'Europe/London';
    case BERLIN = 'Europe/Berlin';
    case MADRID = 'Europe/Madrid';
    case ROME = 'Europe/Rome';
    case AMSTERDAM = 'Europe/Amsterdam';
    case BRUSSELS = 'Europe/Brussels';
    
    // Autres régions
    case TOKYO = 'Asia/Tokyo';
    case SHANGHAI = 'Asia/Shanghai';
    case SYDNEY = 'Australia/Sydney';
    case AUCKLAND = 'Pacific/Auckland';
    case DUBAI = 'Asia/Dubai';
    case SAO_PAULO = 'America/Sao_Paulo';
    case MEXICO_CITY = 'America/Mexico_City';

    public function label(): string
    {
        return match($this) {
            // Amérique du Nord
            self::EASTERN => 'Heure de l\'Est (Toronto, Montréal, New York)',
            self::PACIFIC => 'Heure du Pacifique (Vancouver)',
            self::CENTRAL => 'Heure Centrale (Winnipeg, Chicago)',
            self::MOUNTAIN => 'Heure des Rocheuses (Calgary, Edmonton)',
            self::ATLANTIC => 'Heure de l\'Atlantique (Halifax)',
            self::NEWFOUNDLAND => 'Heure de Terre-Neuve (St. John\'s)',
            
            // Europe
            self::PARIS => 'Heure de Paris (France)',
            self::LONDON => 'Heure de Londres (Royaume-Uni)',
            self::BERLIN => 'Heure de Berlin (Allemagne)',
            self::MADRID => 'Heure de Madrid (Espagne)',
            self::ROME => 'Heure de Rome (Italie)',
            self::AMSTERDAM => 'Heure d\'Amsterdam (Pays-Bas)',
            self::BRUSSELS => 'Heure de Bruxelles (Belgique)',
            
            // Autres régions
            self::TOKYO => 'Heure de Tokyo (Japon)',
            self::SHANGHAI => 'Heure de Shanghai (Chine)',
            self::SYDNEY => 'Heure de Sydney (Australie)',
            self::AUCKLAND => 'Heure d\'Auckland (Nouvelle-Zélande)',
            self::DUBAI => 'Heure de Dubaï (ÉAU)',
            self::SAO_PAULO => 'Heure de São Paulo (Brésil)',
            self::MEXICO_CITY => 'Heure de Mexico (Mexique)',
        };
    }

    /**
     * Obtenir les fuseaux horaires groupés par région
     */
    public static function getTimezonesByRegion(): array
    {
        return [
            'Amérique du Nord' => [
                self::EASTERN->value => self::EASTERN->label(),
                self::PACIFIC->value => self::PACIFIC->label(),
                self::CENTRAL->value => self::CENTRAL->label(),
                self::MOUNTAIN->value => self::MOUNTAIN->label(),
                self::ATLANTIC->value => self::ATLANTIC->label(),
                self::NEWFOUNDLAND->value => self::NEWFOUNDLAND->label(),
            ],
            'Europe' => [
                self::PARIS->value => self::PARIS->label(),
                self::LONDON->value => self::LONDON->label(),
                self::BERLIN->value => self::BERLIN->label(),
                self::MADRID->value => self::MADRID->label(),
                self::ROME->value => self::ROME->label(),
                self::AMSTERDAM->value => self::AMSTERDAM->label(),
                self::BRUSSELS->value => self::BRUSSELS->label(),
            ],
            'Autres régions' => [
                self::TOKYO->value => self::TOKYO->label(),
                self::SHANGHAI->value => self::SHANGHAI->label(),
                self::SYDNEY->value => self::SYDNEY->label(),
                self::AUCKLAND->value => self::AUCKLAND->label(),
                self::DUBAI->value => self::DUBAI->label(),
                self::SAO_PAULO->value => self::SAO_PAULO->label(),
                self::MEXICO_CITY->value => self::MEXICO_CITY->label(),
            ],
        ];
    }

    public static function getTimezones(): array
    {
        $allTimezones = [];
        foreach (self::getTimezonesByRegion() as $region => $timezones) {
            $allTimezones = array_merge($allTimezones, $timezones);
        }
        return $allTimezones;
    }
}
