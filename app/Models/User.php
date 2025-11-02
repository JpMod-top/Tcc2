<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;
use DateTimeImmutable;
use PDO;

class User
{
    /**
     * @param array<string, mixed> $data
     */
    public static function create(array $data): int
    {
        $now = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');

        DB::run(
            'INSERT INTO users (name, email, password_hash, created_at, updated_at)
             VALUES (:name, :email, :password_hash, :created_at, :updated_at)',
            [
                'name' => $data['name'],
                'email' => $data['email'],
                'password_hash' => $data['password_hash'],
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

    public static function existsWithEmail(string $email): bool
    {
        $statement = DB::run(
            'SELECT 1 FROM users WHERE email = :email LIMIT 1',
            ['email' => $email]
        );

        return (bool)$statement->fetchColumn();
    }

    public static function updateProfile(int $userId, string $name, string $email): void
    {
        DB::run(
            'UPDATE users SET name = :name, email = :email, updated_at = :updated_at WHERE id = :id',
            [
                'name' => $name,
                'email' => $email,
                'updated_at' => (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
                'id' => $userId,
            ]
        );
    }

    public static function updatePassword(int $userId, string $passwordHash): void
    {
        DB::run(
            'UPDATE users SET password_hash = :password_hash, updated_at = :updated_at WHERE id = :id',
            [
                'password_hash' => $passwordHash,
                'updated_at' => (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
                'id' => $userId,
            ]
        );
    }

    public static function softDelete(int $userId): void
    {
        DB::run(
            'UPDATE users SET deleted_at = :deleted_at, updated_at = :updated_at WHERE id = :id',
            [
                'deleted_at' => (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
                'updated_at' => (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
                'id' => $userId,
            ]
        );
    }

    public static function createPasswordResetToken(
        int $userId,
        string $email,
        string $token,
        DateTimeImmutable $expiresAt,
        ?string $ip,
        ?string $userAgent
    ): void {
        $hash = password_hash($token, PASSWORD_BCRYPT);
        $now = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');

        DB::run(
            'INSERT INTO password_resets (user_id, email, token_hash, expires_at, used_at, ip, user_agent, created_at)
             VALUES (:user_id, :email, :token_hash, :expires_at, NULL, :ip, :user_agent, :created_at)',
            [
                'user_id' => $userId,
                'email' => $email,
                'token_hash' => $hash,
                'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
                'ip' => $ip,
                'user_agent' => $userAgent,
                'created_at' => $now,
            ]
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function findValidPasswordReset(string $email, string $token): ?array
    {
        $statement = DB::run(
            'SELECT * FROM password_resets
             WHERE email = :email AND used_at IS NULL
             ORDER BY created_at DESC',
            ['email' => $email]
        );

        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            if (!password_verify($token, $row['token_hash'])) {
                continue;
            }

            if (strtotime((string)$row['expires_at']) < time()) {
                continue;
            }

            return $row;
        }

        return null;
    }

    public static function markPasswordResetUsed(int $resetId): void
    {
        DB::run(
            'UPDATE password_resets SET used_at = :used_at WHERE id = :id',
            [
                'used_at' => (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
                'id' => $resetId,
            ]
        );
    }
}



