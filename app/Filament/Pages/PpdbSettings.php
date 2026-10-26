<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use App\Support\SiteDefaults;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Halaman "PPDB" — mengelola status pendaftaran, pengumuman, jadwal
 * gelombang, alur pendaftaran, berkas persyaratan, dan info tambahan
 * PPDB. Daftar terstruktur disimpan sebagai JSON di tabel settings
 * (kunci ppdb_waves, ppdb_steps, ppdb_requirements) dan dirender oleh
 * halaman publik /ppdb.
 *
 * Akses: hanya admin & editor — sama seperti Pengaturan Situs.
 */
class PpdbSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'PPDB';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Pengaturan PPDB';

    protected static ?string $slug = 'ppdb';

    protected string $view = 'filament.pages.ppdb-settings';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user !== null && ($user->isAdmin() || $user->isEditor());
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public function mount(): void
    {
        $this->form->fill([
            'ppdb_status' => Setting::get('ppdb_status') ?: 'ditutup',
            'ppdb_announcement' => Setting::get('ppdb_announcement'),
            'ppdb_waves' => static::decodeList(Setting::get('ppdb_waves')),
            'ppdb_steps' => static::decodeSteps(),
            'ppdb_requirements' => static::decodeRequirements(),
            'ppdb_info' => Setting::get('ppdb_info'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Status Pendaftaran')
                    ->description('Status dan pengumuman singkat yang tampil sebagai lencana di halaman PPDB.')
                    ->schema([
                        Select::make('ppdb_status')
                            ->label('Status Pendaftaran')
                            ->options([
                                'dibuka' => 'Dibuka',
                                'ditutup' => 'Ditutup',
                            ])
                            ->required(),
                        Textarea::make('ppdb_announcement')
                            ->label('Teks Pengumuman')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Section::make('Jadwal Gelombang')
                    ->description('Boleh dikosongkan; halaman PPDB akan menampilkan catatan bahwa jadwal resmi akan diumumkan.')
                    ->schema([
                        Repeater::make('ppdb_waves')
                            ->label('Gelombang Pendaftaran')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Gelombang')
                                    ->required()
                                    ->maxLength(100),
                                DatePicker::make('start')
                                    ->label('Tanggal Mulai'),
                                DatePicker::make('end')
                                    ->label('Tanggal Selesai'),
                                TextInput::make('note')
                                    ->label('Keterangan')
                                    ->maxLength(255),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                            ->addActionLabel('Tambah Gelombang')
                            ->defaultItems(0)
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),
                Section::make('Alur Pendaftaran')
                    ->schema([
                        Repeater::make('ppdb_steps')
                            ->label('Langkah Pendaftaran')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul Langkah')
                                    ->required()
                                    ->maxLength(150),
                                Textarea::make('description')
                                    ->label('Deskripsi')
                                    ->rows(2),
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->addActionLabel('Tambah Langkah')
                            ->defaultItems(0)
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),
                Section::make('Berkas Persyaratan')
                    ->schema([
                        Repeater::make('ppdb_requirements')
                            ->label('Berkas Persyaratan')
                            ->schema([
                                TextInput::make('item')
                                    ->label('Nama Berkas')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->addActionLabel('Tambah Berkas')
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ]),
                Section::make('Info Tambahan')
                    ->schema([
                        Textarea::make('ppdb_info')
                            ->label('Info Tambahan PPDB')
                            ->helperText('Opsional — misalnya keterangan biaya, kontak panitia, atau ketentuan khusus. Kosongkan bila tidak ada.')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        Setting::set('ppdb_status', ($state['ppdb_status'] ?? 'ditutup') === 'dibuka' ? 'dibuka' : 'ditutup');
        Setting::set('ppdb_announcement', $state['ppdb_announcement'] ?? null);
        Setting::set('ppdb_info', $state['ppdb_info'] ?? null);

        $waves = collect($state['ppdb_waves'] ?? [])
            ->map(fn (array $wave): array => [
                'name' => trim((string) ($wave['name'] ?? '')),
                'start' => $wave['start'] ?? null,
                'end' => $wave['end'] ?? null,
                'note' => trim((string) ($wave['note'] ?? '')),
            ])
            ->filter(fn (array $wave): bool => $wave['name'] !== '')
            ->values()
            ->all();
        Setting::set('ppdb_waves', json_encode($waves, JSON_UNESCAPED_UNICODE));

        $steps = collect($state['ppdb_steps'] ?? [])
            ->map(fn (array $step): array => [
                'title' => trim((string) ($step['title'] ?? '')),
                'description' => trim((string) ($step['description'] ?? '')),
            ])
            ->filter(fn (array $step): bool => $step['title'] !== '')
            ->values()
            ->all();
        Setting::set('ppdb_steps', json_encode($steps, JSON_UNESCAPED_UNICODE));

        $requirements = collect($state['ppdb_requirements'] ?? [])
            ->map(fn (array $row): string => trim((string) ($row['item'] ?? '')))
            ->filter(fn (string $item): bool => $item !== '')
            ->values()
            ->all();
        Setting::set('ppdb_requirements', json_encode($requirements, JSON_UNESCAPED_UNICODE));

        Notification::make()
            ->title('Pengaturan PPDB berhasil disimpan.')
            ->success()
            ->send();
    }

    /**
     * Decode daftar JSON dari settings menjadi array; [] bila kosong/rusak.
     *
     * @return array<int, mixed>
     */
    protected static function decodeList(?string $json): array
    {
        $decoded = json_decode($json ?? '', true);

        return is_array($decoded) ? array_values($decoded) : [];
    }

    /**
     * Nilai awal langkah pendaftaran: dari settings, atau bawaan situs
     * bila belum pernah disimpan.
     *
     * @return array<int, array{title: string, description: string}>
     */
    protected static function decodeSteps(): array
    {
        $steps = static::decodeList(Setting::get('ppdb_steps'));

        return $steps !== [] ? $steps : SiteDefaults::ppdbSteps();
    }

    /**
     * Nilai awal berkas persyaratan dalam bentuk baris Repeater
     * (tiap berkas = ['item' => '...']), atau bawaan situs.
     *
     * @return array<int, array{item: string}>
     */
    protected static function decodeRequirements(): array
    {
        $rows = collect(static::decodeList(Setting::get('ppdb_requirements')))
            ->map(fn ($row): array => ['item' => is_array($row) ? (string) ($row['item'] ?? '') : (string) $row])
            ->filter(fn (array $row): bool => trim($row['item']) !== '')
            ->values()
            ->all();

        if ($rows === []) {
            $rows = collect(SiteDefaults::ppdbRequirements())
                ->map(fn (string $item): array => ['item' => $item])
                ->all();
        }

        return $rows;
    }
}
