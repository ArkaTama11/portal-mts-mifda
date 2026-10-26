<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    /**
     * Semua peran yang bisa masuk panel boleh melihat daftar artikel.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, User::ROLES, true);
    }

    /**
     * Semua peran yang bisa masuk panel boleh melihat detail artikel.
     */
    public function view(User $user, Article $article): bool
    {
        return in_array($user->role, User::ROLES, true);
    }

    /**
     * Membuat artikel: admin, penulis, editor. Peninjau tidak bisa.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isPenulis() || $user->isEditor();
    }

    /**
     * Mengubah artikel: admin & editor untuk artikel siapa pun; penulis
     * hanya artikel miliknya dan hanya bila belum berstatus "terbit";
     * peninjau tidak bisa mengubah apa pun.
     */
    public function update(User $user, Article $article): bool
    {
        if ($user->isAdmin() || $user->isEditor()) {
            return true;
        }

        if ($user->isPenulis()) {
            return (int) $article->author_id === (int) $user->id
                && $article->status !== 'terbit';
        }

        return false;
    }

    /**
     * Menghapus artikel: admin untuk semua artikel; penulis hanya artikel
     * miliknya yang masih draft; editor & peninjau tidak bisa.
     */
    public function delete(User $user, Article $article): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isPenulis()) {
            return (int) $article->author_id === (int) $user->id
                && $article->status === 'draft';
        }

        return false;
    }

    /**
     * Aksi massal hapus di tabel — dibatasi pada peran yang memang
     * memiliki kemampuan hapus per artikel.
     */
    public function deleteAny(User $user): bool
    {
        return $user->isAdmin() || $user->isPenulis();
    }

    /**
     * Menerbitkan artikel (ability kustom): hanya admin & editor.
     * Dipakai untuk membatasi opsi status "terbit" AND penjaga
     * server-side di halaman create/edit artikel.
     */
    public function publish(User $user): bool
    {
        return $user->isAdmin() || $user->isEditor();
    }
}
