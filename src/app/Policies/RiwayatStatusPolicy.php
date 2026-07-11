<?php

namespace App\Policies;

use App\Models\User;
use App\Models\RiwayatStatus;
use Illuminate\Auth\Access\HandlesAuthorization;

class RiwayatStatusPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_riwayat::status');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, RiwayatStatus $riwayatStatus): bool
    {
        return $user->can('view_riwayat::status');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_riwayat::status');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, RiwayatStatus $riwayatStatus): bool
    {
        return $user->can('update_riwayat::status');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, RiwayatStatus $riwayatStatus): bool
    {
        return $user->can('delete_riwayat::status');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_riwayat::status');
    }
}
