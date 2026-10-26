<?php

namespace App\Filament\Resources\Articles\Pages;

use App\Filament\Resources\Articles\ArticleResource;
use App\Models\Article;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Aksi "Terbitkan": hanya tampil bagi pengguna yang memiliki
            // ability publish (admin & editor) dan artikel belum terbit.
            Action::make('publish')
                ->label('Terbitkan')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Terbitkan artikel ini?')
                ->modalDescription('Artikel akan langsung tampil di halaman publik.')
                ->visible(fn (): bool => $this->record instanceof Article
                    && $this->record->status !== 'terbit'
                    && (auth()->user()?->can('publish', $this->record) ?? false))
                ->action(function (): void {
                    $this->record->update([
                        'status' => 'terbit',
                        'published_at' => $this->record->published_at ?? now(),
                    ]);

                    Notification::make()
                        ->title('Artikel diterbitkan')
                        ->success()
                        ->send();

                    $this->refreshFormData(['status', 'published_at']);
                }),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Penjaga server-side: status "terbit" hanya boleh dihasilkan oleh
        // pengguna yang memiliki ability publish (admin & editor).
        if (($data['status'] ?? null) === 'terbit'
            && ! auth()->user()?->can('publish', Article::class)
        ) {
            throw ValidationException::withMessages([
                'data.status' => 'Anda tidak memiliki izin menerbitkan artikel. Simpan sebagai Draft atau Diperiksa, lalu minta editor/admin menerbitkannya.',
            ]);
        }

        // Tanggal terbit otomatis diisi saat artikel diterbitkan tanpa tanggal.
        if (($data['status'] ?? null) === 'terbit' && empty($data['published_at'])) {
            $data['published_at'] = $this->record->published_at ?? now();
        }

        return $data;
    }
}
