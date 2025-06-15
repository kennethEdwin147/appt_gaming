<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any reservations.
     */
    public function viewAny(User $user): bool
    {
        return true; // Tous les utilisateurs connectés peuvent voir leurs réservations
    }

    /**
     * Determine whether the user can view the reservation.
     */
    public function view(User $user, Reservation $reservation): bool
    {
        // L'utilisateur peut voir sa propre réservation ou le créateur peut voir les réservations qui lui sont destinées
        return $user->id === $reservation->user_id || 
               ($user->creator && $user->creator->id === $reservation->creator_id);
    }

    /**
     * Determine whether the user can create reservations.
     */
    public function create(User $user): bool
    {
        return true; // Tous les utilisateurs connectés peuvent créer des réservations
    }

    /**
     * Determine whether the user can update the reservation.
     */
    public function update(User $user, Reservation $reservation): bool
    {
        // Seul le créateur peut mettre à jour une réservation
        return $user->creator && $user->creator->id === $reservation->creator_id;
    }

    /**
     * Determine whether the user can cancel the reservation.
     */
    public function cancel(User $user, Reservation $reservation): bool
    {
        // L'utilisateur peut annuler sa propre réservation ou le créateur peut annuler les réservations qui lui sont destinées
        return $user->id === $reservation->user_id || 
               ($user->creator && $user->creator->id === $reservation->creator_id);
    }

    /**
     * Determine whether the user can confirm the reservation.
     */
    public function confirm(User $user, Reservation $reservation): bool
    {
        // Seul le créateur peut confirmer une réservation
        return $user->creator && $user->creator->id === $reservation->creator_id;
    }

    /**
     * Determine whether the user can delete the reservation.
     */
    public function delete(User $user, Reservation $reservation): bool
    {
        // Seul le créateur peut supprimer une réservation
        return $user->creator && $user->creator->id === $reservation->creator_id;
    }

    /**
     * Determine whether the user can restore the reservation.
     */
    public function restore(User $user, Reservation $reservation): bool
    {
        // Seul le créateur peut restaurer une réservation
        return $user->creator && $user->creator->id === $reservation->creator_id;
    }

    /**
     * Determine whether the user can permanently delete the reservation.
     */
    public function forceDelete(User $user, Reservation $reservation): bool
    {
        // Seul le créateur peut supprimer définitivement une réservation
        return $user->creator && $user->creator->id === $reservation->creator_id;
    }
}
