<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DetailPesanan;
use Illuminate\Auth\Access\HandlesAuthorization;

class DetailPesananPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_detail::pesanan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DetailPesanan $detailPesanan): bool
    {
        return $user->can('view_detail::pesanan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_detail::pesanan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DetailPesanan $detailPesanan): bool
    {
        return $user->can('update_detail::pesanan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DetailPesanan $detailPesanan): bool
    {
        return $user->can('delete_detail::pesanan');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_detail::pesanan');
    }
}
