<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

class Auth
{
    private const COOKIE_KEY = 'anonymousUserId';
    private const FALLBACK_USER_ID = '00000000-0000-4000-8000-000000000000';

    /**
     * @var array{id:int,name:string,email:string}|null
     */
    private static ?array $cachedUser = null;

    public static function init(bool $httpsOnly = false): void
    {
        unset($httpsOnly);
        self::user();
    }

    /**
     * Kept for compatibility with old controllers/tests. The local app no longer
     * authenticates users with credentials.
     *
     * @param array<string, mixed> $snapshot
     */
    public static function login(int $userId, array $snapshot = []): void
    {
        unset($userId, $snapshot);
        self::$cachedUser = null;
    }

    public static function logout(): void
    {
        self::$cachedUser = null;
    }

    public static function check(): bool
    {
        return true;
    }

    public static function userId(): int
    {
        return (int)self::user()['id'];
    }

    /**
     * @return array{id:int,name:string,email:string}
     */
    public static function user(): array
    {
        if (self::$cachedUser !== null) {
            return self::$cachedUser;
        }

        $anonymousUserId = self::anonymousUserId();
        $user = User::ensureAnonymousUser($anonymousUserId);
        self::$cachedUser = [
            'id' => (int)$user['id'],
            'name' => (string)($user['name'] ?? 'Estoque anonimo'),
            'email' => (string)($user['email'] ?? ''),
        ];

        return self::$cachedUser;
    }

    public static function requireAuth(): void
    {
        self::user();
    }

    public static function anonymousUserId(): string
    {
        $value = (string)($_COOKIE[self::COOKIE_KEY] ?? '');

        if (self::isValidAnonymousUserId($value)) {
            return strtolower($value);
        }

        if (PHP_SAPI === 'cli') {
            return self::FALLBACK_USER_ID;
        }

        return self::FALLBACK_USER_ID;
    }

    /**
     * Abort request with 404 if the current local user is not the owner.
     */
    public static function authorizeOwner(int $resourceUserId): void
    {
        if (self::userId() !== $resourceUserId) {
            http_response_code(404);
            echo '404 Not Found';
            exit;
        }
    }

    private static function isValidAnonymousUserId(string $value): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $value) === 1;
    }
}
