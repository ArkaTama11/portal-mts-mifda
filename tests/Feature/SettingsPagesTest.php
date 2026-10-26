<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsPagesTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    // --- Akses halaman Pengaturan Situs -----------------------------------

    public function test_penulis_cannot_access_site_settings_page(): void
    {
        $this->actingAs($this->userWithRole('penulis'))
            ->get('/admin/pengaturan-situs')
            ->assertForbidden();
    }

    public function test_peninjau_cannot_access_site_settings_page(): void
    {
        $this->actingAs($this->userWithRole('peninjau'))
            ->get('/admin/pengaturan-situs')
            ->assertForbidden();
    }

    public function test_admin_can_access_site_settings_page(): void
    {
        $this->actingAs($this->userWithRole('admin'))
            ->get('/admin/pengaturan-situs')
            ->assertOk();
    }

    public function test_editor_can_access_site_settings_page(): void
    {
        $this->actingAs($this->userWithRole('editor'))
            ->get('/admin/pengaturan-situs')
            ->assertOk();
    }

    // --- Akses halaman PPDB ------------------------------------------------

    public function test_penulis_cannot_access_ppdb_settings_page(): void
    {
        $this->actingAs($this->userWithRole('penulis'))
            ->get('/admin/ppdb')
            ->assertForbidden();
    }

    public function test_peninjau_cannot_access_ppdb_settings_page(): void
    {
        $this->actingAs($this->userWithRole('peninjau'))
            ->get('/admin/ppdb')
            ->assertForbidden();
    }

    public function test_admin_can_access_ppdb_settings_page(): void
    {
        $this->actingAs($this->userWithRole('admin'))
            ->get('/admin/ppdb')
            ->assertOk();
    }

    public function test_editor_can_access_ppdb_settings_page(): void
    {
        $this->actingAs($this->userWithRole('editor'))
            ->get('/admin/ppdb')
            ->assertOk();
    }

    // --- Perubahan pengaturan tampil di halaman publik ---------------------

    public function test_ppdb_settings_changes_are_reflected_on_public_ppdb_page(): void
    {
        Setting::set('ppdb_status', 'dibuka');
        Setting::set('ppdb_announcement', 'Pendaftaran tahun ajaran baru resmi dibuka.');
        Setting::set('ppdb_waves', json_encode([
            ['name' => 'Gelombang Uji', 'start' => '2026-11-02', 'end' => '2026-11-20', 'note' => 'Kuota terbatas'],
        ], JSON_UNESCAPED_UNICODE));

        $response = $this->get('/ppdb');

        $response->assertOk();
        $response->assertSee('Pendaftaran Dibuka');
        $response->assertSee('Pendaftaran tahun ajaran baru resmi dibuka.');
        $response->assertSee('Gelombang Uji');
        $response->assertSee('Kuota terbatas');
    }

    public function test_ppdb_page_shows_default_flow_and_requirements_without_settings(): void
    {
        $response = $this->get('/ppdb');

        $response->assertOk();
        $response->assertSee('Mengisi formulir pendaftaran');
        $response->assertSee('Pengumuman & daftar ulang');
        $response->assertSee('Fotokopi Kartu Keluarga (KK)');
        $response->assertSee('Jadwal resmi gelombang pendaftaran akan diumumkan');
    }

    public function test_head_name_change_is_reflected_on_home_page(): void
    {
        Setting::set('head_name', 'H. Ahmad Uji, M.Pd.');

        $this->get('/')
            ->assertOk()
            ->assertSee('H. Ahmad Uji, M.Pd.');
    }

    public function test_home_page_falls_back_to_placeholder_when_head_name_is_empty(): void
    {
        Setting::set('head_name', '');
        Setting::set('sambutan', '');

        $this->get('/')
            ->assertOk()
            ->assertSee('Kepala Madrasah')
            ->assertSee('Selamat datang di portal informasi');
    }
}
