<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'sobrenome',
        'email',
        'telefone',
        'password',
        'tipo',
        'status',
        'ativo',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'ativo' => 'boolean',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->name} {$this->sobrenome}");
    }

    public function isActive(): bool
    {
        return $this->ativo && $this->status === 'active';
    }

    public function isAdmin(): bool
    {
        return $this->tipo === 'admin' || $this->hasRole('super-admin');
    }

    public function isOperador(): bool
    {
        return $this->tipo === 'operador' || $this->hasRole('operador');
    }

    public function isCliente(): bool
    {
        return $this->tipo === 'cliente' || $this->hasRole('cliente');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function updateLastLogin(?string $ip = null): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class, 'company_user')
            ->withPivot('is_owner', 'is_active')
            ->withTimestamps();
    }
}
