<?php

namespace App\Policies;

use App\Models\Query;
use App\Models\User;

class QueryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Query $query): bool
    {
        return $user->id === $query->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Query $query): bool
    {
        return $user->id === $query->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Query $query): bool
    {
        return $user->id === $query->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Query $query): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Query $query): bool
    {
        return false;
    }
}
