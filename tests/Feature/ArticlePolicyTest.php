<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticlePolicyTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $penulis;

    private User $penulisLain;

    private User $editor;

    private User $peninjau;

    private Article $draftMilikPenulis;

    private Article $terbitMilikPenulis;

    private Article $draftMilikLain;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->penulis = User::factory()->create(['role' => 'penulis']);
        $this->penulisLain = User::factory()->create(['role' => 'penulis']);
        $this->editor = User::factory()->create(['role' => 'editor']);
        $this->peninjau = User::factory()->create(['role' => 'peninjau']);

        $this->draftMilikPenulis = $this->makeArticle($this->penulis, 'draft');
        $this->terbitMilikPenulis = $this->makeArticle($this->penulis, 'terbit');
        $this->draftMilikLain = $this->makeArticle($this->penulisLain, 'draft');
    }

    private function makeArticle(User $author, string $status): Article
    {
        return Article::query()->create([
            'title' => 'Artikel Uji '.$status.' '.$author->id.' '.uniqid(),
            'category' => 'berita',
            'body' => '<p>Isi artikel uji.</p>',
            'status' => $status,
            'published_at' => $status === 'terbit' ? now() : null,
            'author_id' => $author->id,
        ]);
    }

    public function test_only_admin_and_editor_can_publish(): void
    {
        $this->assertFalse($this->penulis->can('publish', $this->draftMilikPenulis));
        $this->assertFalse($this->penulis->can('publish', Article::class));
        $this->assertFalse($this->peninjau->can('publish', $this->draftMilikPenulis));
        $this->assertTrue($this->editor->can('publish', $this->draftMilikPenulis));
        $this->assertTrue($this->admin->can('publish', $this->draftMilikPenulis));
    }

    public function test_peninjau_cannot_create_articles_but_writers_can(): void
    {
        $this->assertFalse($this->peninjau->can('create', Article::class));
        $this->assertTrue($this->penulis->can('create', Article::class));
        $this->assertTrue($this->editor->can('create', Article::class));
        $this->assertTrue($this->admin->can('create', Article::class));
    }

    public function test_penulis_can_update_only_own_unpublished_articles(): void
    {
        $this->assertTrue($this->penulis->can('update', $this->draftMilikPenulis));
        $this->assertFalse($this->penulis->can('update', $this->draftMilikLain));
        $this->assertFalse($this->penulis->can('update', $this->terbitMilikPenulis));
    }

    public function test_editor_and_admin_can_update_any_article_peninjau_none(): void
    {
        $this->assertTrue($this->editor->can('update', $this->terbitMilikPenulis));
        $this->assertTrue($this->admin->can('update', $this->draftMilikLain));
        $this->assertFalse($this->peninjau->can('update', $this->draftMilikPenulis));
    }

    public function test_delete_rules_per_role(): void
    {
        $this->assertTrue($this->admin->can('delete', $this->terbitMilikPenulis));
        $this->assertTrue($this->penulis->can('delete', $this->draftMilikPenulis));
        $this->assertFalse($this->penulis->can('delete', $this->terbitMilikPenulis));
        $this->assertFalse($this->penulis->can('delete', $this->draftMilikLain));
        $this->assertFalse($this->editor->can('delete', $this->draftMilikPenulis));
        $this->assertFalse($this->peninjau->can('delete', $this->draftMilikPenulis));
    }

    public function test_all_panel_roles_can_view_articles(): void
    {
        foreach ([$this->admin, $this->penulis, $this->editor, $this->peninjau] as $user) {
            $this->assertTrue($user->can('viewAny', Article::class));
            $this->assertTrue($user->can('view', $this->draftMilikPenulis));
        }
    }

    public function test_only_admin_can_manage_users(): void
    {
        foreach ([$this->penulis, $this->editor, $this->peninjau] as $user) {
            $this->assertFalse($user->can('viewAny', User::class));
            $this->assertFalse($user->can('create', User::class));
            $this->assertFalse($user->can('update', $this->admin));
            $this->assertFalse($user->can('delete', $this->admin));
        }

        $this->assertTrue($this->admin->can('viewAny', User::class));
        $this->assertTrue($this->admin->can('create', User::class));
        $this->assertTrue($this->admin->can('update', $this->penulis));
        $this->assertTrue($this->admin->can('delete', $this->penulis));
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $this->assertFalse($this->admin->can('delete', $this->admin));
    }
}
