<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;
use DateTimeImmutable;
use PDO;

class User
{
    private const DEFAULT_PASSWORD_HASH = 'anonymous-access-disabled';

    /**
     * @return array<string, mixed>
     */
    public static function ensureAnonymousUser(string $anonymousUserId): array
    {
        self::ensureAnonymousUserColumn();

        $anonymousUserId = strtolower(trim($anonymousUserId));
        $user = self::findByAnonymousUserId($anonymousUserId);
        if ($user !== null) {
            if (!empty($user['deleted_at'])) {
                DB::run(
                    'UPDATE users SET deleted_at = NULL, updated_at = :updated_at WHERE id = :id',
                    [
                        'updated_at' => (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
                        'id' => (int)$user['id'],
                    ]
                );

                $user = self::findById((int)$user['id']) ?? array_merge($user, ['deleted_at' => null]);
            }

            return $user;
        }

        $email = self::anonymousEmail($anonymousUserId);
        $userId = self::create([
            'name' => 'Estoque anonimo',
            'email' => $email,
            'password_hash' => self::DEFAULT_PASSWORD_HASH,
            'anonymous_user_id' => $anonymousUserId,
        ]);

        return self::findById($userId) ?? [
            'id' => $userId,
            'name' => 'Estoque anonimo',
            'email' => $email,
            'anonymous_user_id' => $anonymousUserId,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function create(array $data): int
    {
        $now = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');

        self::ensureAnonymousUserColumn();

        DB::run(
            'INSERT INTO users (name, email, password_hash, anonymous_user_id, created_at, updated_at)
             VALUES (:name, :email, :password_hash, :anonymous_user_id, :created_at, :updated_at)',
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'password_hash' => $data['password_hash'],
                'anonymous_user_id' => $data['anonymous_user_id'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        return (int)DB::connection()->lastInsertId();
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function findByEmail(string $email): ?array
    {
        $statement = DB::run(
            'SELECT * FROM users WHERE email = :email LIMIT 1',
            ['email' => $email]
        );

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function findById(int $id): ?array
    {
        $statement = DB::run(
            'SELECT * FROM users WHERE id = :id LIMIT 1',
            ['id' => $id]
        );

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function findByAnonymousUserId(string $anonymousUserId): ?array
    {
        self::ensureAnonymousUserColumn();

        $statement = DB::run(
            'SELECT * FROM users WHERE anonymous_user_id = :anonymous_user_id LIMIT 1',
            ['anonymous_user_id' => $anonymousUserId]
        );

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }

    private static function anonymousEmail(string $anonymousUserId): string
    {
        return 'anonymous-' . $anonymousUserId . '@estoque.internal';
    }

    private static function ensureAnonymousUserColumn(): void
    {
        static $checked = false;

        if ($checked) {
            return;
        }

        try {
            DB::run('ALTER TABLE users ADD COLUMN anonymous_user_id VARCHAR(64) NULL');
        } catch (\Throwable) {
            // Column already exists or the current database does not allow this ALTER here.
        }

        $checked = true;
    }
}
