<?php

namespace App\Policies;

use App\Models\DataSource;
use App\Models\User;

class DataSourcePolicy
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
    public function view(User $user, DataSource $dataSource): bool
    {
        return $user->id === $dataSource->user_id;
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
    public function update(User $user, DataSource $dataSource): bool
    {
        return $user->id === $dataSource->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DataSource $dataSource): bool
    {
        return $user->id === $dataSource->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DataSource $dataSource): bool
    {
        return $user->id === $dataSource->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DataSource $dataSource): bool
    {
        return $user->id === $dataSource->user_id;
    }
}
