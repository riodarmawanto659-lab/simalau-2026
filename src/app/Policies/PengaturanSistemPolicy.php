<?php

namespace App\Policies;

use App\Models\User;
use App\Models\PengaturanSistem;
use Illuminate\Auth\Access\HandlesAuthorization;

class PengaturanSistemPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_pengaturan::sistem');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PengaturanSistem $pengaturanSistem): bool
    {
        return $user->can('view_pengaturan::sistem');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_pengaturan::sistem');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PengaturanSistem $pengaturanSistem): bool
    {
        return $user->can('update_pengaturan::sistem');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PengaturanSistem $pengaturanSistem): bool
    {
        return $user->can('delete_pengaturan::sistem');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_pengaturan::sistem');
    }
}
