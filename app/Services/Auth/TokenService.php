<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Collection;
use Laravel\Sanctum\PersonalAccessToken;

class TokenService
{
    public function createToken(User $user, string $deviceName, ?string $ip = null): string
    {
        $tokenName = $this->generateTokenName($deviceName, $ip);

        return $user->createToken($tokenName)->plainTextToken;
    }

    public function revokeCurrentToken(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    public function revokeAllTokens(User $user): void
    {
        $user->tokens()->delete();
    }

    public function revokeToken(User $user, int $tokenId): bool
    {
        $token = $user->tokens()->where('id', $tokenId)->first();

        if (!$token) {
            return false;
        }

        $token->delete();

        return true;
    }

    public function listTokens(User $user): Collection
    {
        return $user->tokens()
            ->orderByDesc('last_used_at')
            ->get();
    }

    public function findToken(string $token): ?PersonalAccessToken
    {
        return PersonalAccessToken::findToken($token);
    }

    public function pruneExpiredTokens(): int
    {
        return PersonalAccessToken::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->delete();
    }

    protected function generateTokenName(string $deviceName, ?string $ip): string
    {
        $timestamp = now()->format('Y-m-d H:i:s');

        return "{$deviceName}|{$ip}|{$timestamp}";
    }
}
