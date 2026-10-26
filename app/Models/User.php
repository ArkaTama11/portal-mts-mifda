<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** Peran yang dikenal sistem. */
    public const ROLES = ['admin', 'penulis', 'editor', 'peninjau'];

    /**
     * Semua peran resmi boleh masuk panel admin; izin per fitur
     * ditegakkan oleh policy masing-masing resource.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, self::ROLES, true);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPenulis(): bool
    {
        return $this->role === 'penulis';
    }

    public function isEditor(): bool
    {
        return $this->role === 'editor';
    }

    public function isPeninjau(): bool
    {
        return $this->role === 'peninjau';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
