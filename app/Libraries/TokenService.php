<?php

namespace App\Libraries;

use App\Models\PersonalAccessToken;
use App\Models\User;

class TokenService
{
    protected $tokenModel;
    protected $userModel;

    public function __construct()
    {
        $this->tokenModel = new PersonalAccessToken();
        $this->userModel = new User();
    }

    /**
     * Create a new token for user
     */
    public function createToken(int $userId, string $name = 'default', array $abilities = [], int $expiresInMinutes = null): array
    {
        $user = $this->userModel->find($userId);
        if (!$user || !$user['is_active']) {
            throw new \InvalidArgumentException('User not found or inactive');
        }

        $expiresAt = null;
        if ($expiresInMinutes) {
            $expiresAt = date('Y-m-d H:i:s', time() + ($expiresInMinutes * 60));
        }

        $tokenResult = $this->tokenModel->createToken($userId, $name, $abilities, $expiresAt);
        
        if (empty($tokenResult)) {
            throw new \RuntimeException('Failed to create token');
        }

        return [
            'token' => $tokenResult['plain_text_token'],
            'token_id' => $tokenResult['id'],
            'expires_at' => $expiresAt,
            'user' => $user
        ];
    }

    /**
     * Validate token and return user data
     */
    public function validateToken(string $token): ?array
    {
        return $this->tokenModel->validateToken($token);
    }

    /**
     * Revoke a specific token
     */
    public function revokeToken(string $token): bool
    {
        return $this->tokenModel->revokeToken($token);
    }

    /**
     * Revoke all user tokens
     */
    public function revokeAllUserTokens(int $userId): bool
    {
        return $this->tokenModel->revokeAllUserTokens($userId);
    }

    /**
     * Get user's active tokens
     */
    public function getUserTokens(int $userId): array
    {
        $tokens = $this->tokenModel->getUserTokens($userId);
        
        // Filter out expired tokens
        return array_filter($tokens, function($token) {
            return !$token['expires_at'] || strtotime($token['expires_at']) > time();
        });
    }

    /**
     * Check if token has specific ability
     */
    public function hasAbility(array $tokenData, string $ability): bool
    {
        return $this->tokenModel->hasAbility($tokenData, $ability);
    }

    /**
     * Extract token from request header
     */
    public function extractTokenFromHeader(string $authHeader): ?string
    {
        // Support both "Bearer token" and "token" formats
        if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $matches[1];
        }
        
        if (preg_match('/^[a-f0-9]{64}$/i', $authHeader)) {
            return $authHeader;
        }

        return null;
    }

    /**
     * Clean expired tokens (should be run periodically)
     */
    public function cleanExpiredTokens(): int
    {
        return $this->tokenModel->cleanExpiredTokens();
    }

    /**
     * Get token statistics for user
     */
    public function getTokenStats(int $userId): array
    {
        $allTokens = $this->tokenModel->getUserTokens($userId);
        $activeTokens = array_filter($allTokens, function($token) {
            return !$token['expires_at'] || strtotime($token['expires_at']) > time();
        });

        return [
            'total_tokens' => count($allTokens),
            'active_tokens' => count($activeTokens),
            'expired_tokens' => count($allTokens) - count($activeTokens),
            'last_used' => !empty($allTokens) ? max(array_column($allTokens, 'last_used_at')) : null
        ];
    }

    /**
     * Create token with default settings
     */
    public function createDefaultToken(int $userId, string $deviceName = 'Unknown Device'): array
    {
        // Default token expires in 30 days
        return $this->createToken($userId, $deviceName, ['*'], 43200); // 30 days in minutes
    }

    /**
     * Create temporary token (for password reset, email verification, etc.)
     */
    public function createTemporaryToken(int $userId, string $purpose, int $expiresInMinutes = 60): array
    {
        $abilities = [$purpose]; // Limited abilities
        return $this->createToken($userId, "Temporary: {$purpose}", $abilities, $expiresInMinutes);
    }

    /**
     * Validate token and check ability
     */
    public function validateTokenWithAbility(string $token, string $requiredAbility): ?array
    {
        $result = $this->validateToken($token);
        
        if (!$result) {
            return null;
        }

        if (!$this->hasAbility($result['token'], $requiredAbility)) {
            return null;
        }

        return $result;
    }

    /**
     * Get token info without validating user
     */
    public function getTokenInfo(string $token): ?array
    {
        return $this->tokenModel->findByToken($token);
    }

    /**
     * Update token name
     */
    public function updateTokenName(string $token, string $newName): bool
    {
        $tokenData = $this->tokenModel->findByToken($token);
        if (!$tokenData) {
            return false;
        }

        return $this->tokenModel->update($tokenData['id'], ['name' => $newName]);
    }

    /**
     * Extend token expiration
     */
    public function extendToken(string $token, int $additionalMinutes): bool
    {
        $tokenData = $this->tokenModel->findByToken($token);
        if (!$tokenData) {
            return false;
        }

        $currentExpiry = $tokenData['expires_at'] ? strtotime($tokenData['expires_at']) : time();
        $newExpiry = date('Y-m-d H:i:s', $currentExpiry + ($additionalMinutes * 60));

        return $this->tokenModel->update($tokenData['id'], ['expires_at' => $newExpiry]);
    }
}
