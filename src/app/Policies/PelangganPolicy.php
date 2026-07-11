<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Pelanggan;
use Illuminate\Auth\Access\HandlesAuthorization;

class PelangganPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_pelanggan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Pelanggan $pelanggan): bool
    {
        return $user->can('view_pelanggan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_pelanggan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Pelanggan $pelanggan): bool
    {
        return $user->can('update_pelanggan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Pelanggan $pelanggan): bool
    {
        return $user->can('delete_pelanggan');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_pelanggan');
    }
}
