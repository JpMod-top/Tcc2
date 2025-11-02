<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class Csrf
{
    private const SESSION_KEY = '_csrf_tokens';
    private const TOKEN_BYTES = 32;

    public static function token(string $formKey): string
    {
        self::ensureStorage();

        if (!isset($_SESSION[self::SESSION_KEY][$formKey])) {
            $_SESSION[self::SESSION_KEY][$formKey] = self::generateToken();
        }

        return $_SESSION[self::SESSION_KEY][$formKey];
    }

    public static function regenerate(string $formKey): string
    {
        self::ensureStorage();
        $_SESSION[self::SESSION_KEY][$formKey] = self::generateToken();

        return $_SESSION[self::SESSION_KEY][$formKey];
    }

    public static function verify(string $formKey, ?string $token): bool
    {
        self::ensureStorage();

        if ($token === null || $token === '') {
            return false;
        }

        $stored = $_SESSION[self::SESSION_KEY][$formKey] ?? null;

        if (!is_string($stored)) {
            return false;
        }

        $valid = hash_equals($stored, $token);

        if ($valid) {
            unset($_SESSION[self::SESSION_KEY][$formKey]);
        }

        return $valid;
    }

    private static function ensureStorage(): void
    {
        if (!isset($_SESSION[self::SESSION_KEY]) || !is_array($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }
    }

    private static function generateToken(): string
    {
        try {
            return bin2hex(random_bytes(self::TOKEN_BYTES));
        } catch (\Throwable $throwable) {
            throw new RuntimeException('Unable to generate CSRF token.', 0, $throwable);
        }
    }
}