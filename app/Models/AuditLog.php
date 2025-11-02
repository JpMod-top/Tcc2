<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;
use DateTimeImmutable;

class AuditLog
{
    /**
     * @param array<string, mixed> $delta
     */
    public static function record(
        int $userId,
        string $entity,
        ?int $entityId,
        string $action,
        array $delta,
        ?string $ip,
        ?string $userAgent
    ): void {
        DB::run(
            'INSERT INTO audit_logs (user_id, entidade, entidade_id, acao, delta_json, ip, user_agent, created_at)
             VALUES (:user_id, :entidade, :entidade_id, :acao, :delta_json, :ip, :user_agent, :created_at)',
            [
                'user_id' => $userId,
                'entidade' => $entity,
                'entidade_id' => $entityId,
                'acao' => $action,
                'delta_json' => json_encode($delta, JSON_THROW_ON_ERROR),
                'ip' => $ip,
                'user_agent' => $userAgent,
                'created_at' => (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
            ]
        );
    }
}
