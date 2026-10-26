<?php

namespace App\Support;

/**
 * Nilai bawaan (fallback) untuk konten situs yang dikelola lewat
 * dashboard. Dipakai bersama oleh DatabaseSeeder, halaman publik,
 * dan halaman pengaturan PPDB — supaya konten yang tampil selalu
 * sama seperti versi awal situs bila pengaturan belum pernah diubah.
 */
class SiteDefaults
{
    /**
     * Teks sambutan umum yang tampil di beranda bila kolom sambutan
     * di Pengaturan Situs dikosongkan.
     */
    public static function sambutan(): string
    {
        return 'Assalamu’alaikum warahmatullahi wabarakatuh. Selamat datang di portal informasi MTs. Miftahul Huda Dongos. Portal ini kami hadirkan sebagai sarana informasi bagi siswa, orang tua, dan masyarakat — berisi berita kegiatan, pengumuman, serta informasi PPDB madrasah kami. (Teks sambutan resmi masih menunggu naskah dari Kepala Madrasah.)';
    }

    /**
     * Teks pengumuman PPDB bawaan (pengumuman yang selama ini tampil
     * di halaman PPDB saat jadwal resmi belum ditetapkan).
     */
    public static function ppdbAnnouncement(): string
    {
        return 'Jadwal PPDB resmi untuk tahun ajaran berjalan masih menunggu penetapan madrasah. Informasi pada halaman ini adalah alur umum pendaftaran; jadwal dan ketentuan final akan diumumkan melalui halaman Berita & Pengumuman serta WhatsApp madrasah.';
    }

    /**
     * Empat langkah alur pendaftaran bawaan.
     *
     * @return array<int, array{title: string, description: string}>
     */
    public static function ppdbSteps(): array
    {
        return [
            ['title' => 'Mengisi formulir pendaftaran', 'description' => 'Formulir dapat diambil di madrasah atau melalui panitia PPDB.'],
            ['title' => 'Menyerahkan berkas persyaratan', 'description' => 'Serahkan berkas lengkap kepada panitia untuk diperiksa.'],
            ['title' => 'Verifikasi berkas', 'description' => 'Panitia memeriksa kelengkapan dan keabsahan berkas calon peserta didik.'],
            ['title' => 'Pengumuman & daftar ulang', 'description' => 'Hasil diumumkan oleh madrasah, dilanjutkan daftar ulang bagi yang diterima.'],
        ];
    }

    /**
     * Daftar berkas persyaratan bawaan.
     *
     * @return array<int, string>
     */
    public static function ppdbRequirements(): array
    {
        return [
            'Fotokopi ijazah / surat keterangan lulus (SKL) jenjang sebelumnya',
            'Fotokopi Kartu Keluarga (KK)',
            'Fotokopi Akta Kelahiran',
            'Pas foto terbaru',
            'Fotokopi KTP kedua orang tua/wali',
        ];
    }
}
