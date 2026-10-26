<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Mengelola pengguna adalah hak khusus admin.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Admin tidak boleh menghapus akunnya sendiri.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->isAdmin() && (int) $user->id !== (int) $model->id;
    }

    public function deleteAny(User $user): bool
    {
        return $user->isAdmin();
    }
}
