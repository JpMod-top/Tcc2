<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;
use DateTimeImmutable;
use PDO;

class Image
{
    public static function add(
        int $userId,
        int $componentId,
        string $path,
        bool $primary = false
    ): int {
        if ($primary) {
            self::clearPrimary($userId, $componentId);
        }

        $now = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');

        DB::run(
            'INSERT INTO images (user_id, component_id, path, principal, created_at)
             VALUES (:user_id, :component_id, :path, :principal, :created_at)',
            [
                'user_id' => $userId,
                'component_id' => $componentId,
                'path' => $path,
                'principal' => $primary ? 1 : 0,
                'created_at' => $now,
            ]
        );

        return (int)DB::connection()->lastInsertId();
    }

    public static function clearPrimary(int $userId, int $componentId): void
    {
        DB::run(
            'UPDATE images
             SET principal = 0
             WHERE user_id = :user_id AND component_id = :component_id',
            [
                'user_id' => $userId,
                'component_id' => $componentId,
            ]
        );
    }

    public static function markAsPrimary(int $imageId, int $userId, int $componentId): void
    {
        self::clearPrimary($userId, $componentId);

        DB::run(
            'UPDATE images
             SET principal = 1
             WHERE id = :id AND user_id = :user_id AND component_id = :component_id',
            [
                'id' => $imageId,
                'user_id' => $userId,
                'component_id' => $componentId,
            ]
        );
    }

    public static function delete(int $imageId, int $userId, int $componentId): ?string
    {
        $record = DB::run(
            'SELECT path FROM images
             WHERE id = :id AND user_id = :user_id AND component_id = :component_id
             LIMIT 1',
            [
                'id' => $imageId,
                'user_id' => $userId,
                'component_id' => $componentId,
            ]
        )->fetch(PDO::FETCH_ASSOC);

        if (!$record) {
            return null;
        }

        DB::run(
            'DELETE FROM images WHERE id = :id AND user_id = :user_id AND component_id = :component_id',
            [
                'id' => $imageId,
                'user_id' => $userId,
                'component_id' => $componentId,
            ]
        );

        return (string)$record['path'];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function listByComponent(int $componentId, int $userId): array
    {
        return DB::run(
            'SELECT * FROM images
             WHERE component_id = :component_id AND user_id = :user_id
             ORDER BY principal DESC, created_at DESC',
            [
                'component_id' => $componentId,
                'user_id' => $userId,
            ]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function find(int $imageId, int $userId): ?array
    {
        $statement = DB::run(
            'SELECT * FROM images WHERE id = :id AND user_id = :user_id LIMIT 1',
            [
                'id' => $imageId,
                'user_id' => $userId,
            ]
        );

        $image = $statement->fetch(PDO::FETCH_ASSOC);

        return $image ?: null;
    }
}
