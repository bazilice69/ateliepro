<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'loja_id', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Papéis possíveis (coluna role - enum da migration add_loja_id_to_users).
     */
    public const ROLE_SUPER_ADMIN = 'super_admin';
    public const ROLE_ADMIN_LOJA = 'admin_loja';
    public const ROLE_FUNCIONARIO = 'funcionario';

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

    /**
     * A loja à qual este usuário pertence (null para super_admin).
     */
    public function loja(): BelongsTo
    {
        return $this->belongsTo(Loja::class);
    }

    /**
     * É o administrador do SaaS (dono do AteliêPro)?
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPER_ADMIN;
    }

    /**
     * É administrador/dono da própria loja?
     */
    public function isAdminLoja(): bool
    {
        return $this->role === self::ROLE_ADMIN_LOJA;
    }
}
