<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin'; // Seuls les admins peuvent lister tous les utilisateurs
    }

    /**
     * Determine whether the user can view the user.
     */
    public function view(User $currentUser, User $user): bool
    {
        return $currentUser->id === $user->id || $currentUser->role === 'admin'; // L'utilisateur peut voir son propre profil ou si c'est un admin
    }

    /**
     * Determine whether the user can update the user.
     */
    public function update(User $currentUser, User $user): bool
    {
        return $currentUser->id === $user->id || $currentUser->role === 'admin'; // L'utilisateur peut modifier son propre profil ou si c'est un admin
    }

    /**
     * Determine whether the user can delete the user.
     */
    public function delete(User $currentUser, User $user): bool
    {
        return $currentUser->role === 'admin' && $currentUser->id !== $user->id; // Seuls les admins peuvent supprimer d'autres utilisateurs
    }

    /**
     * Determine whether the user can view their own dashboard.
     */
    public function dashboard(User $user): bool
    {
        return true; // Tous les utilisateurs connectés peuvent voir leur dashboard
    }

    /**
     * Determine whether the user can view their own profile.
     */
    public function profile(User $user): bool
    {
        return true; // Tous les utilisateurs connectés peuvent voir leur profil
    }

    /**
     * Determine whether the user can edit their own profile.
     */
    public function editProfile(User $user): bool
    {
        return true; // Tous les utilisateurs connectés peuvent modifier leur profil
    }
}