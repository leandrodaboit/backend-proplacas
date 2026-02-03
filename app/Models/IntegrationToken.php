<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class IntegrationToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'token',
        'type',
        'description',
        'abilities',
        'allowed_ips',
        'is_active',
        'rate_limit_per_minute',
        'last_used_at',
        'expires_at',
    ];

    protected $hidden = [
        'token',
    ];

    protected function casts(): array
    {
        return [
            'abilities' => 'array',
            'allowed_ips' => 'array',
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function logs(): HasMany
    {
        return $this->hasMany(IntegrationLog::class);
    }

    public static function generateToken(): string
    {
        return hash('sha256', Str::random(40));
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    public function isIpAllowed(?string $ip): bool
    {
        if (empty($this->allowed_ips)) {
            return true;
        }

        return in_array($ip, $this->allowed_ips, true);
    }

    public function hasAbility(string $ability): bool
    {
        if (empty($this->abilities)) {
            return true;
        }

        return in_array('*', $this->abilities, true) || in_array($ability, $this->abilities, true);
    }

    public function touchLastUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    public static function findByToken(string $token): ?self
    {
        return static::where('token', $token)
            ->where('is_active', true)
            ->first();
    }
}
