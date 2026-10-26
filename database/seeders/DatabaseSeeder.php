<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Setting;
use App\Models\User;
use App\Support\SiteDefaults;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- Akun lokal per peran -----------------------------------------
        // Satu akun per peran untuk mencoba panel & izin peran. Password
        // admin dapat diambil dari env ADMIN_SEED_PASSWORD; sisanya dibuat
        // acak kuat. Semua email + password ditulis ke ADMIN-LOKAL.txt
        // (file itu masuk .gitignore, jangan pernah di-commit).
        $accountDefinitions = [
            ['label' => 'Admin', 'name' => 'Admin Madrasah', 'email' => 'admin@mtsmifdadongos.local', 'role' => 'admin', 'password' => env('ADMIN_SEED_PASSWORD') ?: Str::password(24)],
            ['label' => 'Penulis', 'name' => 'Penulis Uji', 'email' => 'penulis@mtsmifdadongos.local', 'role' => 'penulis', 'password' => Str::password(24)],
            ['label' => 'Editor', 'name' => 'Editor Uji', 'email' => 'editor@mtsmifdadongos.local', 'role' => 'editor', 'password' => Str::password(24)],
            ['label' => 'Peninjau', 'name' => 'Peninjau Uji', 'email' => 'peninjau@mtsmifdadongos.local', 'role' => 'peninjau', 'password' => Str::password(24)],
        ];

        $accounts = [];
        $admin = null;

        foreach ($accountDefinitions as $definition) {
            $user = User::query()->updateOrCreate(
                ['email' => $definition['email']],
                [
                    'name' => $definition['name'],
                    'password' => $definition['password'],
                    'role' => $definition['role'],
                ]
            );

            $accounts[] = $definition + ['user' => $user];

            if ($definition['role'] === 'admin') {
                $admin = $user;
            }
        }

        $credentialLines = "Akun lokal portal MTs. Miftahul Huda Dongos\n"
            ."==========================================\n"
            ."URL login : /admin\n\n";

        foreach ($accounts as $account) {
            $credentialLines .= "[{$account['label']}] {$account['name']}\n"
                ."Email     : {$account['email']}\n"
                ."Password  : {$account['password']}\n\n";
        }

        $credentialLines .= "WAJIB ganti password ini sebelum website online.\n"
            ."File ini masuk .gitignore — jangan di-commit ke Git.\n";

        file_put_contents(base_path('ADMIN-LOKAL.txt'), $credentialLines);

        // --- Identitas & konten situs (dari data resmi madrasah) ---------
        $settings = [
            'site_name' => 'MTs. Miftahul Huda Dongos',
            'foundation' => 'Yayasan Pendidikan Islam Miftahul Huda Dongos',
            'nsm' => '121233200056',
            'npsn' => '20364239',
            'address' => 'Dongos, Kedung, Jepara 59463',
            'whatsapp_number' => '085182676077',
            'whatsapp_link' => 'https://wa.me/6285182676077',
            'social_username' => '@mtsmifdadongos',
            'tiktok_url' => 'https://www.tiktok.com/@mtsmifdadongos',
            'instagram_url' => 'https://www.instagram.com/mtsmifdadongos',
            'facebook_url' => 'https://www.facebook.com/mtsmifdadongos',
            'visi' => 'Terwujudnya Generasi yang Relegius Berkualitas Dalam Iptek, berakhlakul karimah dan cinta lingkungan',
            'misi' => implode("\n", [
                'Meningkatkan kegiatan keagamaan dengan meng efektifkan sholat berjamaah dan membaca Al-Qur’an',
                'Mewujudkan madrasah sebagai pusat transformasi IMTAQ dan IPTEK',
                'Meningkatkan proses belajar mengajar dan bimbingan belajar agar siswa dapat berkembang maksimal sesuai dengan bakat dan kemapuannya',
                'Mengembangkan strategi kompetitif yang positif dilingkungan madrasah baik antar siswa maupun tenaga pengajar secara demokratis dan terbuka',
                'Mengembangkan kratifitas minat baca dan pengembangan diri peserta didik',
                'Mengoptimalkan pembelajaran dan penggunaan sarana dan prasarana pendidikan',
                'Mewujudkan peserta didik yang berprestasi dalam bidang akademik dan non akademik',
            ]),
            'head_name' => 'Kepala Madrasah',
            'sambutan' => 'Assalamu’alaikum warahmatullahi wabarakatuh. Selamat datang di portal informasi MTs. Miftahul Huda Dongos. Portal ini kami hadirkan sebagai sarana informasi bagi siswa, orang tua, dan masyarakat — berisi berita kegiatan, pengumuman, serta informasi PPDB madrasah kami. (Teks sambutan resmi masih menunggu naskah dari Kepala Madrasah.)',
            // PPDB (tahap 3) — daftar terstruktur disimpan sebagai JSON.
            // Status bawaan "ditutup": jadwal resmi memang belum ditetapkan;
            // admin/editor membukanya dari dashboard → Pengaturan → PPDB.
            'ppdb_status' => 'ditutup',
            'ppdb_announcement' => SiteDefaults::ppdbAnnouncement(),
            'ppdb_waves' => '[]',
            'ppdb_steps' => json_encode(SiteDefaults::ppdbSteps(), JSON_UNESCAPED_UNICODE),
            'ppdb_requirements' => json_encode(SiteDefaults::ppdbRequirements(), JSON_UNESCAPED_UNICODE),
            'ppdb_info' => '',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }

        // --- Artikel awal --------------------------------------------------
        Article::query()->updateOrCreate(
            ['slug' => 'pelantikan-ketua-ipnu-ippnu-terpilih-dan-kegiatan-makesta'],
            [
                'title' => 'Pelantikan Ketua IPNU & IPPNU Terpilih dan Kegiatan MAKESTA',
                'category' => 'berita',
                'excerpt' => 'Pelantikan ketua IPNU & IPPNU terpilih serta kegiatan MAKESTA (Masa Kesetiaan Anggota) di MTs. Miftahul Huda Dongos, Minggu, 18 Oktober 2026.',
                'body' => '<p>MTs. Miftahul Huda Dongos melaksanakan kegiatan MAKESTA (Masa Kesetiaan Anggota) IPNU–IPPNU yang dirangkaikan dengan pelantikan ketua terpilih, pada hari Minggu, 18 Oktober 2026.</p>'
                    .'<p>Ketua IPNU terpilih: <strong>M Rifqi Zauhar Prasetya</strong> (kelas 8B). Ketua IPPNU terpilih: <strong>Wardah Zahira Aulia</strong> (kelas 8 Unggulan).</p>'
                    .'<p><img src="/storage/articles/makesta-putri.jpg" alt="Foto kegiatan MAKESTA IPPNU MTs. Miftahul Huda Dongos"></p>',
                'featured_image' => 'articles/makesta-putra.jpg',
                'status' => 'terbit',
                'published_at' => '2026-10-18 08:00:00',
                'author_id' => $admin->id,
            ]
        );

        Article::query()->updateOrCreate(
            ['slug' => 'upacara-peringatan-hari-santri-nasional-2026'],
            [
                'title' => 'Upacara Peringatan Hari Santri Nasional 2026',
                'category' => 'berita',
                'excerpt' => 'Upacara peringatan Hari Santri Nasional 2026 di halaman MTs. Miftahul Huda Dongos, Kamis, 22 Oktober 2026.',
                'body' => '<p>MTs. Miftahul Huda Dongos melaksanakan upacara peringatan Hari Santri Nasional 2026 pada hari Kamis, 22 Oktober 2026. Upacara dilaksanakan di halaman madrasah dan diikuti oleh siswa serta guru.</p>',
                'featured_image' => 'articles/hsn-2026.jpg',
                'status' => 'terbit',
                'published_at' => '2026-10-22 07:00:00',
                'author_id' => $admin->id,
            ]
        );
    }
}
