<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // UserPolicy menolak admin menghapus akunnya sendiri, sehingga
            // aksi ini otomatis tersembunyi untuk akun sendiri.
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Pengaman: peran admin tidak boleh diturunkan bila akun yang
        // diedit adalah satu-satunya admin yang tersisa — situs akan
        // kehilangan pengelola utamanya.
        $record = $this->record;

        if ($record instanceof User
            && $record->isAdmin()
            && ($data['role'] ?? 'admin') !== 'admin'
            && User::query()->where('role', 'admin')->count() <= 1
        ) {
            throw ValidationException::withMessages([
                'data.role' => 'Peran tidak dapat diubah: ini satu-satunya akun admin yang tersisa. Buat atau jadikan akun lain sebagai admin terlebih dahulu.',
            ]);
        }

        return $data;
    }
}
