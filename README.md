# Portal MTs. Miftahul Huda Dongos

Website portal informasi **MTs. Miftahul Huda Dongos** (Dongos, Kedung, Jepara 59463) —
Laravel + Filament. Halaman publik membaca data dari database, dan artikel dikelola
lewat dashboard admin tanpa coding.

## Menjalankan Secara Lokal

Prasyarat: PHP 8.3+ dan Composer.

```bash
composer install
cp .env.example .env        # sesuaikan; bawaan memakai SQLite
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve            # http://127.0.0.1:8000
```

Halaman publik: `/` (beranda), `/berita`, `/berita/{slug}`, `/profil`, `/ppdb`, `/kontak`.
Dashboard: `/admin`.

## Akun Lokal

Seeder membuat **satu akun per peran** untuk mencoba panel & izin peran:

| Peran    | Email                            |
|----------|----------------------------------|
| Admin    | `admin@mtsmifdadongos.local`     |
| Penulis  | `penulis@mtsmifdadongos.local`   |
| Editor   | `editor@mtsmifdadongos.local`    |
| Peninjau | `peninjau@mtsmifdadongos.local`  |

- Password masing-masing: lihat file **`ADMIN-LOKAL.txt`** di root proyek
  (ditulis seeder saat `migrate:fresh --seed` dijalankan; file itu masuk
  `.gitignore`, jangan pernah di-commit).
- **WAJIB ganti password sebelum website online.** Password bawaan ini hanya
  untuk pemakaian lokal.

Seeder juga mengisi identitas/visi/misi madrasah ke tabel `settings` dan
2 artikel terbit (MAKESTA & HSN 2026) sebagai contoh isi awal.

## Arsitektur Singkat

- `app/Models/Article.php` — artikel: judul, slug otomatis, kategori
  (berita/pengumuman/prestasi), ringkasan, isi (HTML), foto utama, status
  (draft/diperiksa/terbit), tanggal terbit, penulis.
- `app/Models/Setting.php` — pasangan key–value untuk identitas & konten situs
  (nama, yayasan, NSM, NPSN, alamat, WhatsApp, media sosial, visi, misi, sambutan).
- `app/Filament/Resources/Articles/` — panel kelola artikel (form + tabel + filter).
  Penulis artikel otomatis = pengguna yang login saat membuat artikel.
- `users.role`: `admin`, `penulis`, `editor`, `peninjau`.
- Halaman publik: Blade di `resources/views/`, data dari database (bukan statis).
- Berkas unggahan foto artikel: disk `public`, folder `articles`
  (perlu `php artisan storage:link`).

## Tahap 2 — Kelola Pengguna & Izin Peran (server-side)

Sejak tahap 2, izin peran ditegakkan **di server** lewat policy Laravel
(`app/Policies/`), bukan hanya di tampilan form:

- **Panel Pengguna** ada di `/admin` → grup **Pengaturan → Pengguna**
  (`app/Filament/Resources/Users/`). **Hanya admin** yang melihat menu ini;
  peran lain ditolak (403) walau membuka URL-nya langsung (`UserPolicy`).
  Admin dapat menambah akun, mengubah nama/email/peran, dan mengganti
  password (kosongkan kolom password saat mengedit bila tidak diubah).
  Pengaman: admin **tidak bisa menghapus akunnya sendiri**, dan peran
  admin **tidak bisa diturunkan** bila akun itu satu-satunya admin.
- **Izin artikel** diatur `ArticlePolicy`:

  | Aksi                        | Admin | Penulis                | Editor | Peninjau |
  |-----------------------------|-------|------------------------|--------|----------|
  | Melihat daftar & artikel    | ✔     | ✔                      | ✔      | ✔        |
  | Membuat artikel             | ✔     | ✔                      | ✔      | ✘        |
  | Mengubah artikel            | ✔     | Miliknya, belum terbit | ✔      | ✘        |
  | Menghapus artikel           | ✔     | Miliknya, masih draft  | ✘      | ✘        |
  | Menerbitkan (status terbit) | ✔     | ✘                      | ✔      | ✘        |

- Status **"terbit"** dijaga berlapis: opsi form hanya muncul bagi yang
  berhak; halaman buat/edit artikel **menolak penyimpanan** berstatus
  "terbit" dari pengguna tanpa ability `publish` (penjaga server-side di
  `CreateArticle`/`EditArticle`); dan ada tombol **Terbitkan** di halaman
  edit & tabel artikel yang hanya tampil bagi admin/editor.
- Aturan ini teruji otomatis: `tests/Feature/ArticlePolicyTest.php` dan
  `tests/Feature/PanelAccessTest.php` (jalankan `php artisan test`).

## Tahap 3 — Pengaturan Situs & PPDB di Dashboard

Isi situs kini dapat diubah dari dashboard tanpa menyentuh kode, lewat dua
halaman di grup **Pengaturan** (`app/Filament/Pages/`). Keduanya hanya bisa
dibuka **admin & editor** — penulis & peninjau ditolak (403) dan menunya
tidak muncul untuk mereka. (Panel Pengguna tetap khusus admin.)

- **Pengaturan Situs** (`/admin/pengaturan-situs`): empat tab yang tersimpan
  ke tabel `settings` —
  - **Identitas**: nama madrasah, nama yayasan, NSM, NPSN, alamat.
  - **Kontak & Media Sosial**: nomor WhatsApp tampilan dan satu username
    media sosial. Saat disimpan, tautan `wa.me` (dari angka nomor, awalan 0
    → 62) dan URL Instagram/Facebook/TikTok diturunkan ulang otomatis dari
    nilai tersebut, sehingga tautan yang dipakai halaman publik selalu
    konsisten dengan nomor/username tampil.
  - **Sambutan**: nama Kepala Madrasah dan teks sambutan. Bila dikosongkan,
    beranda menampilkan placeholder bawaan ("Kepala Madrasah" + teks umum).
  - **Visi & Misi**: visi, dan misi ditulis satu poin per baris (format
    penyimpanan yang sama seperti sebelumnya).
- **PPDB** (`/admin/ppdb`): status pendaftaran (Dibuka/Ditutup) + teks
  pengumuman, **jadwal gelombang** terstruktur (nama, tanggal mulai/selesai,
  keterangan — disimpan sebagai JSON), **alur pendaftaran** (daftar langkah
  judul + deskripsi, JSON), **berkas persyaratan** (daftar baris, JSON),
  dan info tambahan opsional. Halaman publik `/ppdb` merender semuanya dari
  settings: lencana status + pengumuman, tabel jadwal (bila jadwal kosong
  tampil catatan "jadwal resmi akan diumumkan"), alur, dan daftar berkas.
  Konten bawaan (4 langkah & 5 berkas yang sama seperti versi awal) ada di
  `app/Support/SiteDefaults.php` dan dipakai seeder serta halaman publik
  sebagai fallback.
- Sambutan di beranda dan seluruh isi `/ppdb` membaca settings, jadi
  perubahan dari dashboard langsung tampil di situs.
- Aturan ini teruji otomatis: `tests/Feature/SettingsPagesTest.php`
  (akses per peran + perubahan nilai tampil di halaman publik).

## Penyederhanaan v1 (disadari)

- Tailwind dimuat lewat CDN dan font lewat Google Fonts untuk tahap prototipe.
  Untuk produksi, aset sebaiknya dikompilasi (Vite) agar website tidak bergantung
  pada CDN pihak ketiga.
- Belum ada: galeri, agenda, unduhan, pendaftaran PPDB online, dan halaman
  Akademik terpisah (sesuai blueprint v1 — ditunda ke tahap berikutnya).

## Masih Placeholder

- Nama Kepala Madrasah (sementara tampil "Kepala Madrasah") dan teks sambutan
  resmi — menunggu naskah dari madrasah; sudah dapat diganti dari dashboard
  (Pengaturan → Pengaturan Situs → tab Sambutan).
- Jadwal PPDB resmi — status pendaftaran bawaan masih "Ditutup" dengan
  pengumuman menunggu penetapan; jadwal gelombang diisi dan status dibuka
  dari dashboard (Pengaturan → PPDB) begitu madrasah menetapkannya.

## Langkah Berikutnya (menuju online)

1. Siapkan hosting yang mendukung PHP 8.3+ & MySQL (atau tetap SQLite bila dizinkan hosting).
2. Buat database, salin proyek, atur `.env` produksi (`APP_ENV=production`, `APP_DEBUG=false`, kredensial database).
3. Jalankan `composer install --no-dev`, `php artisan migrate --seed` (atau impor data lokal), `php artisan storage:link`.
4. Login ke `/admin`, **ganti password admin**, lalu buat akun penulis/editor untuk guru.
5. Arahkan domain madrasah ke hosting tersebut.
