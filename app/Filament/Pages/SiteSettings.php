<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/**
 * Halaman "Pengaturan Situs" — mengubah identitas, kontak, sambutan,
 * serta visi & misi madrasah tanpa menyentuh kode. Semua nilai disimpan
 * ke tabel settings yang sama dengan yang dibaca halaman publik.
 *
 * Akses: hanya admin & editor. Penulis & peninjau ditolak (403) dan
 * menu tidak muncul untuk mereka.
 */
class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Pengaturan Situs';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Pengaturan Situs';

    protected static ?string $slug = 'pengaturan-situs';

    protected string $view = 'filament.pages.site-settings';

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
            'site_name' => Setting::get('site_name'),
            'foundation' => Setting::get('foundation'),
            'nsm' => Setting::get('nsm'),
            'npsn' => Setting::get('npsn'),
            'address' => Setting::get('address'),
            'whatsapp_number' => Setting::get('whatsapp_number'),
            'social_username' => Setting::get('social_username'),
            'head_name' => Setting::get('head_name'),
            'sambutan' => Setting::get('sambutan'),
            'visi' => Setting::get('visi'),
            'misi' => Setting::get('misi'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Pengaturan Situs')
                    ->tabs([
                        Tab::make('Identitas')
                            ->icon(Heroicon::OutlinedIdentification)
                            ->schema([
                                TextInput::make('site_name')
                                    ->label('Nama Madrasah')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('foundation')
                                    ->label('Nama Yayasan')
                                    ->maxLength(255),
                                TextInput::make('nsm')
                                    ->label('NSM')
                                    ->maxLength(50),
                                TextInput::make('npsn')
                                    ->label('NPSN')
                                    ->maxLength(50),
                                TextInput::make('address')
                                    ->label('Alamat')
                                    ->maxLength(255),
                            ]),
                        Tab::make('Kontak & Media Sosial')
                            ->icon(Heroicon::OutlinedChatBubbleLeftRight)
                            ->schema([
                                TextInput::make('whatsapp_number')
                                    ->label('Nomor WhatsApp')
                                    ->helperText('Nomor yang tampil di situs, misalnya 085182676077. Tautan wa.me dibuat otomatis dari nomor ini saat disimpan.')
                                    ->maxLength(30),
                                TextInput::make('social_username')
                                    ->label('Username Media Sosial')
                                    ->helperText('Satu username yang sama untuk Instagram, Facebook, dan TikTok, misalnya @mtsmifdadongos. Tautan profil ketiga platform dibuat otomatis dari username ini.')
                                    ->maxLength(100),
                            ]),
                        Tab::make('Sambutan')
                            ->icon(Heroicon::OutlinedMegaphone)
                            ->schema([
                                TextInput::make('head_name')
                                    ->label('Nama Kepala Madrasah')
                                    ->helperText('Kosongkan untuk menampilkan placeholder "Kepala Madrasah".')
                                    ->maxLength(255),
                                Textarea::make('sambutan')
                                    ->label('Teks Sambutan Kepala Madrasah')
                                    ->helperText('Kosongkan untuk menampilkan teks sambutan umum bawaan situs.')
                                    ->rows(8),
                            ]),
                        Tab::make('Visi & Misi')
                            ->icon(Heroicon::OutlinedEye)
                            ->schema([
                                Textarea::make('visi')
                                    ->label('Visi')
                                    ->rows(3),
                                Textarea::make('misi')
                                    ->label('Misi')
                                    ->helperText('Tulis satu poin misi per baris; halaman Profil menampilkannya sebagai daftar bernomor.')
                                    ->rows(9),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        Setting::set('site_name', $state['site_name'] ?? null);
        Setting::set('foundation', $state['foundation'] ?? null);
        Setting::set('nsm', $state['nsm'] ?? null);
        Setting::set('npsn', $state['npsn'] ?? null);
        Setting::set('address', $state['address'] ?? null);
        Setting::set('whatsapp_number', $state['whatsapp_number'] ?? null);
        Setting::set('social_username', $state['social_username'] ?? null);
        Setting::set('head_name', $state['head_name'] ?? null);
        Setting::set('sambutan', $state['sambutan'] ?? null);
        Setting::set('visi', $state['visi'] ?? null);
        Setting::set('misi', $state['misi'] ?? null);

        // Jaga konsistensi tautan: halaman publik memakai kunci
        // whatsapp_link & *_url, jadi turunkan ulang dari nilai tampil
        // setiap kali pengaturan disimpan.
        Setting::set('whatsapp_link', static::deriveWhatsappLink($state['whatsapp_number'] ?? null));
        $username = static::normalizeSocialUsername($state['social_username'] ?? null);
        Setting::set('social_username', $username);
        $handle = $username !== null ? ltrim($username, '@') : null;
        Setting::set('instagram_url', $handle ? 'https://www.instagram.com/'.$handle : null);
        Setting::set('facebook_url', $handle ? 'https://www.facebook.com/'.$handle : null);
        Setting::set('tiktok_url', $handle ? 'https://www.tiktok.com/@'.$handle : null);

        Notification::make()
            ->title('Pengaturan situs berhasil disimpan.')
            ->success()
            ->send();
    }

    /**
     * Turunkan tautan wa.me dari nomor tampilan: ambil angkanya saja,
     * lalu ubah awalan 0 menjadi kode negara 62.
     */
    protected static function deriveWhatsappLink(?string $number): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $number) ?? '';

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        }

        return 'https://wa.me/'.$digits;
    }

    /**
     * Samakan format username media sosial: selalu diawali "@",
     * atau null bila kosong.
     */
    protected static function normalizeSocialUsername(?string $username): ?string
    {
        $username = trim((string) $username);

        if ($username === '') {
            return null;
        }

        return '@'.ltrim($username, '@');
    }
}
