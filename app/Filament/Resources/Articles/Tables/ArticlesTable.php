<?php

namespace App\Filament\Resources\Articles\Tables;

use App\Models\Article;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ArticlesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(60),
                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'berita' => 'Berita',
                        'pengumuman' => 'Pengumuman',
                        'prestasi' => 'Prestasi',
                        default => $state,
                    }),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'diperiksa' => 'warning',
                        'terbit' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'diperiksa' => 'Diperiksa',
                        'terbit' => 'Terbit',
                        default => $state,
                    }),
                TextColumn::make('author.name')
                    ->label('Penulis')
                    ->sortable(),
                TextColumn::make('published_at')
                    ->label('Tanggal terbit')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'diperiksa' => 'Diperiksa',
                        'terbit' => 'Terbit',
                    ]),
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'berita' => 'Berita',
                        'pengumuman' => 'Pengumuman',
                        'prestasi' => 'Prestasi',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                // Aksi "Terbitkan": hanya tampil bagi yang memiliki ability
                // publish (admin & editor) dan artikel belum terbit.
                Action::make('publish')
                    ->label('Terbitkan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Terbitkan artikel ini?')
                    ->modalDescription('Artikel akan langsung tampil di halaman publik.')
                    ->visible(fn (Article $record): bool => $record->status !== 'terbit'
                        && (auth()->user()?->can('publish', $record) ?? false))
                    ->action(function (Article $record): void {
                        $record->update([
                            'status' => 'terbit',
                            'published_at' => $record->published_at ?? now(),
                        ]);

                        Notification::make()
                            ->title('Artikel diterbitkan')
                            ->success()
                            ->send();
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
