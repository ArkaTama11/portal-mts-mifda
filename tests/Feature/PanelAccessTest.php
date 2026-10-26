<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role): User
    {
        return User::factory()->create(['role' => $role]);
    }

    public function test_guest_is_redirected_to_login_when_opening_users_page(): void
    {
        $this->get('/admin/users')->assertRedirect('/admin/login');
    }

    public function test_penulis_cannot_access_users_page(): void
    {
        $this->actingAs($this->userWithRole('penulis'))
            ->get('/admin/users')
            ->assertForbidden();
    }

    public function test_editor_cannot_access_users_page(): void
    {
        $this->actingAs($this->userWithRole('editor'))
            ->get('/admin/users')
            ->assertForbidden();
    }

    public function test_admin_can_access_users_page(): void
    {
        $this->actingAs($this->userWithRole('admin'))
            ->get('/admin/users')
            ->assertOk();
    }

    public function test_peninjau_cannot_open_create_article_page(): void
    {
        $this->actingAs($this->userWithRole('peninjau'))
            ->get('/admin/articles/create')
            ->assertForbidden();
    }

    public function test_editor_can_access_articles_page(): void
    {
        $this->actingAs($this->userWithRole('editor'))
            ->get('/admin/articles')
            ->assertOk();
    }
}
