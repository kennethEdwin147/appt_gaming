<?php

namespace App\Traits;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use DateTimeZone;

trait HandlesTimezones
{
    /**
     * Convertit une heure du fuseau horaire du créateur vers UTC
     *
     * @param string $time Heure au format H:i
     * @param string $creatorTimezone Fuseau horaire du créateur
     * @param string|null $date Date optionnelle au format Y-m-d pour le contexte
     * @return string Heure au format H:i en UTC
     */
    public function convertToUTC(string $time, string $creatorTimezone, ?string $date = null): string
    {
        $dateToUse = $date ?: Carbon::today($creatorTimezone)->format('Y-m-d');
        $datetime = $dateToUse . ' ' . $time;

        return Carbon::parse($datetime, $creatorTimezone)
            ->setTimezone('UTC')
            ->format('H:i');
    }

    /**
     * Convertit une heure UTC vers le fuseau horaire du créateur
     *
     * @param string $time Heure au format H:i
     * @param string $creatorTimezone Fuseau horaire du créateur
     * @param string|null $date Date optionnelle au format Y-m-d pour le contexte
     * @return string Heure au format H:i dans le fuseau horaire du créateur
     */
    public function convertFromUTC(string $time, string $creatorTimezone, ?string $date = null): string
    {
        $dateToUse = $date ?: Carbon::today('UTC')->format('Y-m-d');
        $datetime = $dateToUse . ' ' . $time;

        return Carbon::parse($datetime, 'UTC')
            ->setTimezone($creatorTimezone)
            ->format('H:i');
    }

    /**
     * Convertit une date et heure du fuseau horaire source vers le fuseau horaire cible
     */
    public function convertDateTime(string $datetime, string $fromTimezone, string $toTimezone): string
    {
        return Carbon::parse($datetime, $fromTimezone)
            ->setTimezone($toTimezone)
            ->format('Y-m-d H:i:s');
    }

    /**
     * Vérifie si une heure est valide dans un fuseau horaire donné
     * (gestion des changements d'heure)
     *
     * @param string $time Heure au format H:i
     * @param string $timezone Fuseau horaire à vérifier
     * @param string|null $date Date optionnelle au format Y-m-d pour le contexte (par défaut: aujourd'hui)
     * @param bool $returnReason Si true, retourne la raison de l'invalidité au lieu de false
     * @return bool|string True si l'heure est valide, false ou message d'erreur sinon
     */
    public function isValidTime(string $time, string $timezone, ?string $date = null, bool $returnReason = false)
    {
        try {
            // Utiliser la date fournie ou la date du jour
            $dateToUse = $date ?: Carbon::today($timezone)->format('Y-m-d');
            $datetime = $dateToUse . ' ' . $time;

            // Essayer de parser la date et l'heure
            $carbon = Carbon::parse($datetime, $timezone);

            // Vérifier si l'heure existe (cas du passage à l'heure d'été)
            if ($carbon->format('H:i') !== $time) {
                return $returnReason
                    ? "L'heure {$time} n'existe pas le {$dateToUse} dans ce fuseau horaire (passage à l'heure d'été)"
                    : false;
            }

            // Vérifier les transitions d'heure
            $transitions = (new DateTimeZone($timezone))->getTransitions(
                $carbon->timestamp - 3600, // 1 heure avant
                $carbon->timestamp + 3600  // 1 heure après
            );

            // S'il y a plus d'une transition dans cet intervalle,
            // l'heure pourrait être ambiguë (cas du passage à l'heure d'hiver)
            if (count($transitions) > 1) {
                foreach ($transitions as $transition) {
                    if (abs($transition['ts'] - $carbon->timestamp) < 3600) {
                        return $returnReason
                            ? "L'heure {$time} est ambiguë le {$dateToUse} dans ce fuseau horaire (passage à l'heure d'hiver)"
                            : false;
                    }
                }
            }

            return true;
        } catch (InvalidFormatException $e) {
            return $returnReason
                ? "Format d'heure invalide: {$time}"
                : false;
        }
    }

    /**
     * Obtient le décalage horaire entre deux fuseaux horaires
     */
    public function getTimezoneOffset(string $fromTimezone, string $toTimezone): string
    {
        $from = new DateTimeZone($fromTimezone);
        $to = new DateTimeZone($toTimezone);
        $now = new Carbon();

        $offset = ($to->getOffset($now) - $from->getOffset($now)) / 3600;
        $sign = $offset >= 0 ? '+' : '-';

        return sprintf('%s%d:00', $sign, abs($offset));
    }

    /**
     * Formate une heure pour l'affichage avec le fuseau horaire
     *
     * @param string $time Heure au format H:i
     * @param string $timezone Fuseau horaire
     * @param string|null $date Date optionnelle au format Y-m-d pour le contexte
     * @return string Heure formatée avec l'abréviation du fuseau horaire
     */
    public function formatTimeWithZone(string $time, string $timezone, ?string $date = null): string
    {
        $dateToUse = $date ?: Carbon::today($timezone)->format('Y-m-d');
        $datetime = $dateToUse . ' ' . $time;

        $carbon = Carbon::parse($datetime, $timezone);
        $abbreviation = $carbon->format('T'); // Abréviation du fuseau horaire (EST, PST, etc.)

        return sprintf(
            '%s (%s)',
            $carbon->format('H:i'),
            $abbreviation
        );
    }

    /**
     * Vérifie si une date tombe pendant un changement d'heure
     *
     * @param string $date Date au format Y-m-d
     * @param string $timezone Fuseau horaire à vérifier
     * @return bool|array False si pas de changement d'heure, sinon un tableau avec les détails
     */
    public function getDSTTransitionForDate(string $date, string $timezone)
    {
        try {
            // Créer un objet Carbon pour le début de la journée
            $startOfDay = Carbon::parse($date . ' 00:00:00', $timezone);
            $endOfDay = Carbon::parse($date . ' 23:59:59', $timezone);

            // Obtenir les transitions pour cette journée
            $tz = new DateTimeZone($timezone);
            $transitions = $tz->getTransitions(
                $startOfDay->timestamp,
                $endOfDay->timestamp
            );

            // S'il y a plus d'une transition, c'est qu'il y a un changement d'heure
            if (count($transitions) > 1) {
                $transition = $transitions[1]; // La deuxième entrée contient les détails du changement

                // Déterminer le type de transition (été ou hiver)
                $isDST = $transition['isdst'];
                $type = $isDST ? 'summer' : 'winter';
                $direction = $isDST ? '+1h' : '-1h';

                // Formater l'heure de transition
                $transitionTime = Carbon::createFromTimestamp($transition['ts'], $timezone);

                return [
                    'date' => $date,
                    'time' => $transitionTime->format('H:i'),
                    'type' => $type,
                    'direction' => $direction,
                    'description' => sprintf(
                        'Changement d\'heure à %s (%s)',
                        $transitionTime->format('H:i'),
                        $direction
                    )
                ];
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
