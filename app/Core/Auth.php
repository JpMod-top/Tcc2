<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\User;

class Auth
{
    private const SESSION_KEY = '_auth';
    private static ?array $cachedUser = null;

    public static function init(bool $httpsOnly = false): void
    {
        unset($httpsOnly);
        self::enforceSessionIntegrity();
    }

    /**
     * @param array<string, mixed> $snapshot
     */
    public static function login(int $userId, array $snapshot = []): void
    {
        self::regenerateSession();

        $_SESSION[self::SESSION_KEY] = [
            'user_id' => $userId,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'last_regenerated' => time(),
        ];

        if (!empty($snapshot)) {
            $_SESSION[self::SESSION_KEY]['snapshot'] = [
                'id' => $snapshot['id'] ?? $userId,
                'name' => $snapshot['name'] ?? null,
                'email' => $snapshot['email'] ?? null,
            ];
        }

        self::$cachedUser = null;
    }

    public static function logout(): void
    {
        unset($_SESSION[self::SESSION_KEY]);
        self::$cachedUser = null;
        self::regenerateSession();
    }

    public static function check(): bool
    {
        return isset($_SESSION[self::SESSION_KEY]['user_id']);
    }

    public static function userId(): ?int
    {
        if (!self::check()) {
            return null;
        }

        return (int)($_SESSION[self::SESSION_KEY]['user_id'] ?? 0) ?: null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function user(): ?array
    {
        if (!self::check()) {
            return null;
        }

        if (self::$cachedUser !== null) {
            return self::$cachedUser;
        }

        $session = $_SESSION[self::SESSION_KEY];
        $snapshot = $session['snapshot'] ?? null;

        if (is_array($snapshot) && isset($snapshot['name'], $snapshot['email'])) {
            self::$cachedUser = $snapshot;
            return self::$cachedUser;
        }

        $user = User::findById((int)$session['user_id']);
        if ($user === null || !empty($user['deleted_at'])) {
            self::logout();
            return null;
        }

        self::$cachedUser = [
            'id' => (int)$user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
        ];
        $_SESSION[self::SESSION_KEY]['snapshot'] = self::$cachedUser;

        return self::$cachedUser;
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            header('Location: /login', true, 302);
            exit;
        }

        self::validateSessionContext();
        self::rotateSessionIfNeeded();
    }

    /**
     * Abort request with 404 if the current authenticated user is not the owner.
     */
    public static function authorizeOwner(int $resourceUserId): void
    {
        $current = self::userId();
        if ($current === null || $current !== $resourceUserId) {
            http_response_code(404);
            // simple 404 page
            echo '404 Not Found';
            exit;
        }
    }

    private static function enforceSessionIntegrity(): void
    {
        if (!self::check()) {
            return;
        }

        self::validateSessionContext();
        self::rotateSessionIfNeeded();
    }

    private static function validateSessionContext(): void
    {
        if (!self::check()) {
            return;
        }

        $session = $_SESSION[self::SESSION_KEY];
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        if (($session['ip'] ?? null) && $session['ip'] !== $ip) {
            self::logout();
            return;
        }

        if (($session['user_agent'] ?? null) && $session['user_agent'] !== $agent) {
            self::logout();
        }
    }

    private static function rotateSessionIfNeeded(): void
    {
        if (!self::check()) {
            return;
        }

        $last = (int)($_SESSION[self::SESSION_KEY]['last_regenerated'] ?? 0);

        if (time() - $last > 300) {
            self::regenerateSession();
            $_SESSION[self::SESSION_KEY]['last_regenerated'] = time();
        }
    }

    private static function regenerateSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
}