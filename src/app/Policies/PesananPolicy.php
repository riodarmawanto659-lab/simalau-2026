<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Pesanan;
use Illuminate\Auth\Access\HandlesAuthorization;

class PesananPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_pesanan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Pesanan $pesanan): bool
    {
        return $user->can('view_pesanan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_pesanan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Pesanan $pesanan): bool
    {
        return $user->can('update_pesanan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Pesanan $pesanan): bool
    {
        return $user->can('delete_pesanan');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_pesanan');
    }
}
