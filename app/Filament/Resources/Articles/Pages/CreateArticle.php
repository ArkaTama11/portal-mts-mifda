<?php

namespace App\Filament\Resources\Articles\Pages;

use App\Filament\Resources\Articles\ArticleResource;
use App\Models\Article;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Penjaga server-side: status "terbit" hanya boleh dihasilkan oleh
        // pengguna yang memiliki ability publish (admin & editor) — tidak
        // cukup mengandalkan opsi form yang disembunyikan.
        if (($data['status'] ?? null) === 'terbit'
            && ! auth()->user()?->can('publish', Article::class)
        ) {
            throw ValidationException::withMessages([
                'data.status' => 'Anda tidak memiliki izin menerbitkan artikel. Simpan sebagai Draft atau Diperiksa, lalu minta editor/admin menerbitkannya.',
            ]);
        }

        // Penulis otomatis = pengguna yang sedang login.
        $data['author_id'] = auth()->id();

        // Tanggal terbit otomatis diisi saat artikel diterbitkan tanpa tanggal.
        if (($data['status'] ?? null) === 'terbit' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        return $data;
    }
}
