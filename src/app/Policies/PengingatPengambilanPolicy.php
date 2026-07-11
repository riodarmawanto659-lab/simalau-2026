<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PengingatPengambilan;
use Illuminate\Auth\Access\HandlesAuthorization;

class PengingatPengambilanPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_pengingat::pengambilan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PengingatPengambilan $pengingatPengambilan): bool
    {
        return $user->can('view_pengingat::pengambilan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_pengingat::pengambilan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PengingatPengambilan $pengingatPengambilan): bool
    {
        return $user->can('update_pengingat::pengambilan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PengingatPengambilan $pengingatPengambilan): bool
    {
        return $user->can('delete_pengingat::pengambilan');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_pengingat::pengambilan');
    }
}
