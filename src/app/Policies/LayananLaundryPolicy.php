<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LayananLaundry;
use Illuminate\Auth\Access\HandlesAuthorization;

class LayananLaundryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_layanan::laundry');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LayananLaundry $layananLaundry): bool
    {
        return $user->can('view_layanan::laundry');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_layanan::laundry');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LayananLaundry $layananLaundry): bool
    {
        return $user->can('update_layanan::laundry');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LayananLaundry $layananLaundry): bool
    {
        return $user->can('delete_layanan::laundry');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_layanan::laundry');
    }
}
