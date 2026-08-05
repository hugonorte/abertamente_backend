<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Author;
use App\Models\User;

class AuthorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true; // Todos podem ver a listagem de autores
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Author $author): bool
    {
        return true; // Todos podem ver o perfil de um autor
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(UserRole::Admin);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Author $author): bool
    {
        if ($user->hasRole(UserRole::Admin)) {
            return true;
        }

        if ($user->hasRole(UserRole::Author)) {
            return $user->id === $author->user_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Author $author): bool
    {
        return $user->hasRole(UserRole::Admin);
    }
}
