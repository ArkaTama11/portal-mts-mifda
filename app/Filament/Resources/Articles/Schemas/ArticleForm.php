<?php

namespace App\Filament\Resources\Articles\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ArticleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? ''))),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Select::make('category')
                    ->label('Kategori')
                    ->options([
                        'berita' => 'Berita',
                        'pengumuman' => 'Pengumuman',
                        'prestasi' => 'Prestasi',
                    ])
                    ->default('berita')
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options(function (): array {
                        $options = [
                            'draft' => 'Draft',
                            'diperiksa' => 'Diperiksa',
                        ];

                        // Opsi "Terbit" hanya untuk yang berhak publish
                        // (ArticlePolicy::publish — admin & editor). Aturan
                        // yang sama juga ditegakkan di server saat menyimpan.
                        if (auth()->user()?->can('publish', \App\Models\Article::class)) {
                            $options['terbit'] = 'Terbit';
                        }

                        return $options;
                    })
                    ->default('draft')
                    ->required(),
                DateTimePicker::make('published_at')
                    ->label('Tanggal terbit')
                    ->seconds(false),
                Textarea::make('excerpt')
                    ->label('Ringkasan')
                    ->rows(2)
                    ->columnSpanFull(),
                FileUpload::make('featured_image')
                    ->label('Foto utama')
                    ->image()
                    ->disk('public')
                    ->directory('articles')
                    ->columnSpanFull(),
                RichEditor::make('body')
                    ->label('Isi artikel')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
