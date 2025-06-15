<?php

namespace App\Services;

use App\Models\User;
use App\Models\Reservation;
use App\Models\Creator;
use App\Notifications\ReservationConfirmation;
use App\Notifications\CreatorAccountConfirmation;
use App\Notifications\PasswordChanged;
use App\Notifications\ReservationReminder;
use App\Notifications\ReservationCancelled;
use App\Notifications\NewReservationCreator;
use App\Notifications\PaymentConfirmation;
use App\Notifications\MeetingLinkChanged;
use App\Models\EventType;
use Illuminate\Support\Facades\Password;

/**
 * Service pour gérer l'envoi d'emails dans l'application
 *
 * Ce service centralise toute la logique d'envoi d'emails et utilise le système
 * de notifications de Laravel pour envoyer des emails formatés.
 *
 * CONFIGURATION:
 * - En développement: utiliser MAIL_MAILER=log dans .env pour enregistrer les emails dans les logs
 * - En production: utiliser un service comme Mailgun, SendGrid ou Amazon SES:
 *   MAIL_MAILER=mailgun
 *   MAILGUN_DOMAIN=your-domain.com
 *   MAILGUN_SECRET=your-mailgun-key
 *
 * APPROCHE SIMPLIFIÉE:
 * - Les emails sont envoyés directement (de manière synchrone)
 * - Cette approche est simple et suffisante pour un volume d'emails modéré
 * - Pour un volume plus important, vous pourriez envisager d'utiliser des queues plus tard
 */

class EmailService
{
    /**
     * Envoyer un email de confirmation de compte créateur
     *
     * Cet email contient un lien avec un token pour confirmer le compte créateur.
     * Le token est généré lors de la création du compte et stocké dans la table creators.
     *
     * @param User $user L'utilisateur qui vient de s'inscrire comme créateur
     * @param string $token Le token de confirmation unique
     * @return void
     */
    public function sendCreatorAccountConfirmation(User $user, string $token)
    {
        $user->notify(new CreatorAccountConfirmation($token));
    }

    /**
     * Envoyer un email de confirmation de réservation à l'utilisateur
     *
     * Cet email confirme à l'utilisateur que sa réservation a été enregistrée
     * et lui fournit tous les détails (date, heure, type d'événement, créateur).
     *
     * @param User $user L'utilisateur qui a fait la réservation
     * @param Reservation $reservation La réservation qui vient d'être créée
     * @return void
     */
    public function sendReservationConfirmation(User $user, Reservation $reservation)
    {
        $user->notify(new ReservationConfirmation($reservation));
    }

    /**
     * Envoyer un email de notification de nouvelle réservation au créateur
     *
     * Cet email informe le créateur qu'un utilisateur a fait une réservation
     * pour l'un de ses événements et lui fournit tous les détails.
     *
     * @param Creator $creator Le créateur concerné par la réservation
     * @param Reservation $reservation La réservation qui vient d'être créée
     * @return void
     */
    public function sendNewReservationNotification(Creator $creator, Reservation $reservation)
    {
        $creator->user->notify(new NewReservationCreator($reservation));
    }

    /**
     * Envoyer un email de réinitialisation de mot de passe
     *
     * Utilise le système de réinitialisation de mot de passe intégré à Laravel.
     * L'email contient un lien pour réinitialiser le mot de passe.
     *
     * @param User $user L'utilisateur qui a demandé la réinitialisation
     * @return void
     */
    public function sendPasswordResetLink(User $user)
    {
        Password::sendResetLink(['email' => $user->email]);
    }

    /**
     * Envoyer un email de confirmation de changement de mot de passe
     *
     * Cet email informe l'utilisateur que son mot de passe a été modifié.
     * C'est une mesure de sécurité pour alerter en cas de changement non autorisé.
     *
     * @param User $user L'utilisateur dont le mot de passe a été modifié
     * @return void
     */
    public function sendPasswordChangeConfirmation(User $user)
    {
        $user->notify(new PasswordChanged());
    }

    /**
     * Envoyer un rappel de réservation
     *
     * Cet email rappelle à l'utilisateur qu'il a une réservation prévue prochainement.
     * Généralement envoyé 24h avant la réservation via la commande SendReservationReminders.
     *
     * @param User $user L'utilisateur qui a la réservation
     * @param Reservation $reservation La réservation à rappeler
     * @return void
     */
    public function sendReservationReminder(User $user, Reservation $reservation)
    {
        $user->notify(new ReservationReminder($reservation));
    }

    /**
     * Envoyer une notification d'annulation de réservation
     *
     * Cet email informe l'utilisateur que sa réservation a été annulée.
     * Peut être envoyé suite à une annulation par l'utilisateur ou par le créateur.
     *
     * @param User $user L'utilisateur concerné par l'annulation
     * @param Reservation $reservation La réservation annulée
     * @return void
     */
    public function sendReservationCancellation(User $user, Reservation $reservation)
    {
        $user->notify(new ReservationCancelled($reservation));
    }

    /**
     * Envoyer une confirmation de paiement
     *
     * Cet email confirme à l'utilisateur que son paiement a été reçu et traité.
     * Inclut les détails de la transaction et de la réservation.
     *
     * @param User $user L'utilisateur qui a effectué le paiement
     * @param Reservation $reservation La réservation concernée par le paiement
     * @return void
     */
    public function sendPaymentConfirmation(User $user, Reservation $reservation)
    {
        $user->notify(new PaymentConfirmation($reservation));
    }

    /**
     * Envoyer une notification de changement de lien de réunion
     *
     * Cet email informe l'utilisateur que le lien de réunion pour sa réservation a été modifié.
     * Envoyé lorsqu'un créateur modifie le lien de réunion d'un type d'événement.
     *
     * @param User $user L'utilisateur qui a la réservation
     * @param Reservation $reservation La réservation concernée
     * @param EventType $eventType Le type d'événement dont le lien a été modifié
     * @return void
     */
    public function sendMeetingLinkChangedNotification(User $user, Reservation $reservation, EventType $eventType)
    {
        $user->notify(new MeetingLinkChanged($reservation, $eventType));
    }
}
