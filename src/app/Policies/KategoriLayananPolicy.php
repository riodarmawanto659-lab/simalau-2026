<?php

namespace App\Policies;

use App\Models\User;
use App\Models\KategoriLayanan;
use Illuminate\Auth\Access\HandlesAuthorization;

class KategoriLayananPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_kategori::layanan');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, KategoriLayanan $kategoriLayanan): bool
    {
        return $user->can('view_kategori::layanan');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_kategori::layanan');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, KategoriLayanan $kategoriLayanan): bool
    {
        return $user->can('update_kategori::layanan');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, KategoriLayanan $kategoriLayanan): bool
    {
        return $user->can('delete_kategori::layanan');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_kategori::layanan');
    }
}
