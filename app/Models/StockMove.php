<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;
use DateTimeImmutable;
use PDO;

class StockMove
{
    public const TYPES = ['entrada', 'saida', 'ajuste'];

    public static function record(
        int $userId,
        int $componentId,
        string $type,
        int $quantity,
        ?string $reason = null
    ): int {
        if (!in_array($type, self::TYPES, true)) {
            throw new \InvalidArgumentException('Tipo de movimentação inválido.');
        }

        if ($type === 'entrada' && $quantity < 0) {
            $quantity = abs($quantity);
        }

        if ($type === 'saida' && $quantity > 0) {
            $quantity *= -1;
        }

        if ($type === 'ajuste' && $quantity === 0) {
            throw new \InvalidArgumentException('Quantidade inválida para ajuste.');
        }

        $now = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');

        DB::run(
            'INSERT INTO stock_moves (user_id, component_id, tipo, quantidade, motivo, created_at)
             VALUES (:user_id, :component_id, :tipo, :quantidade, :motivo, :created_at)',
            [
                'user_id' => $userId,
                'component_id' => $componentId,
                'tipo' => $type,
                'quantidade' => $quantity,
                'motivo' => $reason,
                'created_at' => $now,
            ]
        );

        return (int)DB::connection()->lastInsertId();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function listForComponent(int $userId, int $componentId, int $limit = 10): array
    {
        $limit = max(1, min(50, $limit));

        return DB::run(
            'SELECT * FROM stock_moves
             WHERE user_id = :user_id AND component_id = :component_id
             ORDER BY created_at DESC
             LIMIT :limit',
            [
                'user_id' => $userId,
                'component_id' => $componentId,
                'limit' => $limit,
            ]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function listForPeriod(
        int $userId,
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        ?int $componentId = null
    ): array {
        $bindings = [
            'user_id' => $userId,
            'from' => $from->format('Y-m-d 00:00:00'),
            'to' => $to->format('Y-m-d 23:59:59'),
        ];

        $where = 'user_id = :user_id AND created_at BETWEEN :from AND :to';

        if ($componentId !== null) {
            $where .= ' AND component_id = :component_id';
            $bindings['component_id'] = $componentId;
        }

        return DB::run(
            'SELECT * FROM stock_moves WHERE ' . $where . ' ORDER BY created_at DESC',
            $bindings
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array<string, mixed>
     */
    public static function totalsByType(
        int $userId,
        DateTimeImmutable $from,
        DateTimeImmutable $to
    ): array {
        $rows = DB::run(
            'SELECT tipo, SUM(quantidade) AS total
             FROM stock_moves
             WHERE user_id = :user_id AND created_at BETWEEN :from AND :to
             GROUP BY tipo',
            [
                'user_id' => $userId,
                'from' => $from->format('Y-m-d 00:00:00'),
                'to' => $to->format('Y-m-d 23:59:59'),
            ]
        )->fetchAll(PDO::FETCH_KEY_PAIR);

        $result = [
            'entrada' => 0,
            'saida' => 0,
            'ajuste' => 0,
        ];

        foreach ($rows as $type => $total) {
            $result[$type] = (int)$total;
        }

        return $result;
    }
}

