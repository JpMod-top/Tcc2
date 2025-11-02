<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;
use DateTimeImmutable;
use PDO;

class Component
{
    /**
     * @param array<string, mixed> $data
     */
    public static function create(int $userId, array $data): int
    {
        $now = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');

        DB::run(
            'INSERT INTO components (
                user_id, nome, sku, fabricante, cod_fabricante, descricao, categoria, tags,
                quantidade, unidade, localizacao, tolerancia, potencia, tensao_max, footprint,
                custo_unitario, preco_medio, min_estoque, datasheet_path,
                created_at, updated_at
            ) VALUES (
                :user_id, :nome, :sku, :fabricante, :cod_fabricante, :descricao, :categoria, :tags,
                :quantidade, :unidade, :localizacao, :tolerancia, :potencia, :tensao_max, :footprint,
                :custo_unitario, :preco_medio, :min_estoque, :datasheet_path,
                :created_at, :updated_at
            )',
            [
                'user_id' => $userId,
                'nome' => $data['nome'],
                'sku' => $data['sku'],
                'fabricante' => $data['fabricante'] ?? null,
                'cod_fabricante' => $data['cod_fabricante'] ?? null,
                'descricao' => $data['descricao'] ?? null,
                'categoria' => $data['categoria'] ?? null,
                'tags' => $data['tags'] ?? null,
                'quantidade' => (int)($data['quantidade'] ?? 0),
                'unidade' => $data['unidade'] ?? 'un',
                'localizacao' => $data['localizacao'] ?? null,
                'tolerancia' => $data['tolerancia'] ?? null,
                'potencia' => $data['potencia'] ?? null,
                'tensao_max' => $data['tensao_max'] ?? null,
                'footprint' => $data['footprint'] ?? null,
                'custo_unitario' => (float)($data['custo_unitario'] ?? 0),
                'preco_medio' => array_key_exists('preco_medio', $data) && $data['preco_medio'] !== null ? (float)$data['preco_medio'] : null,
                'min_estoque' => (int)($data['min_estoque'] ?? 0),
                'datasheet_path' => $data['datasheet_path'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        return (int)DB::connection()->lastInsertId();
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function update(int $componentId, int $userId, array $data): void
    {
        $now = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');

        DB::run(
            'UPDATE components SET
                nome = :nome,
                sku = :sku,
                fabricante = :fabricante,
                cod_fabricante = :cod_fabricante,
                descricao = :descricao,
                categoria = :categoria,
                tags = :tags,
                quantidade = :quantidade,
                unidade = :unidade,
                localizacao = :localizacao,
                tolerancia = :tolerancia,
                potencia = :potencia,
                tensao_max = :tensao_max,
                footprint = :footprint,
                custo_unitario = :custo_unitario,
                preco_medio = :preco_medio,
                min_estoque = :min_estoque,
                datasheet_path = :datasheet_path,
                updated_at = :updated_at
             WHERE id = :id AND user_id = :user_id AND deleted_at IS NULL',
            [
                'nome' => $data['nome'],
                'sku' => $data['sku'],
                'fabricante' => $data['fabricante'] ?? null,
                'cod_fabricante' => $data['cod_fabricante'] ?? null,
                'descricao' => $data['descricao'] ?? null,
                'categoria' => $data['categoria'] ?? null,
                'tags' => $data['tags'] ?? null,
                'quantidade' => (int)($data['quantidade'] ?? 0),
                'unidade' => $data['unidade'] ?? 'un',
                'localizacao' => $data['localizacao'] ?? null,
                'tolerancia' => $data['tolerancia'] ?? null,
                'potencia' => $data['potencia'] ?? null,
                'tensao_max' => $data['tensao_max'] ?? null,
                'footprint' => $data['footprint'] ?? null,
                'custo_unitario' => (float)($data['custo_unitario'] ?? 0),
                'preco_medio' => $data['preco_medio'] !== null ? (float)$data['preco_medio'] : null,
                'min_estoque' => (int)($data['min_estoque'] ?? 0),
                'datasheet_path' => $data['datasheet_path'] ?? null,
                'updated_at' => $now,
                'id' => $componentId,
                'user_id' => $userId,
            ]
        );
    }

    public static function softDelete(int $componentId, int $userId): void
    {
        $timestamp = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');

        DB::run(
            'UPDATE components
             SET deleted_at = :deleted_at
             WHERE id = :id AND user_id = :user_id AND deleted_at IS NULL',
            [
                'deleted_at' => $timestamp,
                'id' => $componentId,
                'user_id' => $userId,
            ]
        );
    }

    /**
     * @param array<string, mixed> $fields
     */
    public static function updateFields(int $componentId, int $userId, array $fields): void
    {
        if (empty($fields)) {
            return;
        }

        $allowed = [
            'nome', 'sku', 'fabricante', 'cod_fabricante', 'descricao', 'categoria', 'tags',
            'quantidade', 'unidade', 'localizacao', 'tolerancia', 'potencia', 'tensao_max',
            'footprint', 'custo_unitario', 'preco_medio', 'min_estoque', 'datasheet_path',
        ];

        $setParts = [];
        $bindings = [
            'id' => $componentId,
            'user_id' => $userId,
            'updated_at' => (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
        ];

        foreach ($fields as $key => $value) {
            if (!in_array($key, $allowed, true)) {
                continue;
            }

            $setParts[] = "{$key} = :{$key}";
            $bindings[$key] = $value;
        }

        if (empty($setParts)) {
            return;
        }

        $setParts[] = 'updated_at = :updated_at';

        DB::run(
            'UPDATE components SET ' . implode(', ', $setParts)
            . ' WHERE id = :id AND user_id = :user_id AND deleted_at IS NULL',
            $bindings
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function findById(int $componentId, int $userId): ?array
    {
        $statement = DB::run(
            'SELECT * FROM components WHERE id = :id AND user_id = :user_id AND deleted_at IS NULL LIMIT 1',
            [
                'id' => $componentId,
                'user_id' => $userId,
            ]
        );

        $component = $statement->fetch(PDO::FETCH_ASSOC);

        return $component ?: null;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function findBySku(string $sku, int $userId, ?int $excludeId = null): ?array
    {
        $params = [
            'sku' => $sku,
            'user_id' => $userId,
        ];

        $query = 'SELECT * FROM components WHERE sku = :sku AND user_id = :user_id AND deleted_at IS NULL';

        if ($excludeId !== null) {
            $query .= ' AND id <> :exclude_id';
            $params['exclude_id'] = $excludeId;
        }

        $query .= ' LIMIT 1';

        $statement = DB::run($query, $params);

        $component = $statement->fetch(PDO::FETCH_ASSOC);

        return $component ?: null;
    }

    /**
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public static function paginateForUser(int $userId, array $params): array
    {
        $page = max(1, (int)($params['page'] ?? 1));
        $perPage = (int)($params['per_page'] ?? 15);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        $bindings = ['user_id' => $userId];
        $where = ['user_id = :user_id', 'deleted_at IS NULL'];

        $search = trim((string)($params['q'] ?? ''));
        if ($search !== '') {
            $where[] = '(nome LIKE :search_nome OR sku LIKE :search_sku OR fabricante LIKE :search_fabricante OR categoria LIKE :search_categoria OR tags LIKE :search_tags)';
            $like = '%' . $search . '%';
            $bindings['search_nome'] = $like;
            $bindings['search_sku'] = $like;
            $bindings['search_fabricante'] = $like;
            $bindings['search_categoria'] = $like;
            $bindings['search_tags'] = $like;
        }

        if (!empty($params['category'])) {
            $where[] = 'categoria = :categoria';
            $bindings['categoria'] = (string)$params['category'];
        }

        if (!empty($params['zeroed'])) {
            $where[] = 'quantidade = 0';
        }

        if (!empty($params['below_min'])) {
            $where[] = 'quantidade <= min_estoque';
        }

        $whereSql = implode(' AND ', $where);

        $allowedSorts = [
            'nome' => 'nome',
            'sku' => 'sku',
            'categoria' => 'categoria',
            'quantidade' => 'quantidade',
            'updated_at' => 'updated_at',
        ];

        $sort = $allowedSorts[$params['sort'] ?? 'nome'] ?? 'nome';
        $direction = strtoupper((string)($params['direction'] ?? 'asc')) === 'DESC' ? 'DESC' : 'ASC';

        $total = (int)DB::run(
            'SELECT COUNT(*) FROM components WHERE ' . $whereSql,
            $bindings
        )->fetchColumn();

        $items = DB::run(
            'SELECT * FROM components WHERE ' . $whereSql . " ORDER BY {$sort} {$direction} LIMIT :limit OFFSET :offset",
            array_merge(
                $bindings,
                [
                    'limit' => $perPage,
                    'offset' => $offset,
                ]
            )
        )->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $items,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => (int)max(1, (int)ceil($total / $perPage)),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function dashboardSummary(int $userId): array
    {
        $row = DB::run(
            'SELECT
                COUNT(*) AS total_componentes,
                SUM(CASE WHEN quantidade <= min_estoque AND min_estoque > 0 THEN 1 ELSE 0 END) AS abaixo_minimo,
                SUM(CASE WHEN quantidade = 0 THEN 1 ELSE 0 END) AS zerados,
                SUM(quantidade * custo_unitario) AS valor_total
             FROM components
             WHERE user_id = :user_id AND deleted_at IS NULL',
            ['user_id' => $userId]
        )->fetch(PDO::FETCH_ASSOC);

        return [
            'total_componentes' => (int)($row['total_componentes'] ?? 0),
            'abaixo_minimo' => (int)($row['abaixo_minimo'] ?? 0),
            'zerados' => (int)($row['zerados'] ?? 0),
            'valor_total' => (float)($row['valor_total'] ?? 0),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function recent(int $userId, int $limit = 5): array
    {
        $limit = max(1, min(20, $limit));

        return DB::run(
            'SELECT * FROM components
             WHERE user_id = :user_id AND deleted_at IS NULL
             ORDER BY created_at DESC
             LIMIT :limit',
            [
                'user_id' => $userId,
                'limit' => $limit,
            ]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array<int, string>
     */
    public static function categories(int $userId): array
    {
        $rows = DB::run(
            'SELECT DISTINCT categoria
             FROM components
             WHERE user_id = :user_id AND categoria IS NOT NULL AND categoria <> "" AND deleted_at IS NULL
             ORDER BY categoria ASC',
            ['user_id' => $userId]
        )->fetchAll(PDO::FETCH_COLUMN);

        return array_map(static fn($category) => (string)$category, $rows ?: []);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function lowStock(int $userId): array
    {
        return DB::run(
            'SELECT * FROM components
             WHERE user_id = :user_id AND deleted_at IS NULL
               AND min_estoque > 0 AND quantidade <= min_estoque
             ORDER BY quantidade ASC, nome ASC',
            ['user_id' => $userId]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function zeroed(int $userId): array
    {
        return DB::run(
            'SELECT * FROM components
             WHERE user_id = :user_id AND deleted_at IS NULL
               AND quantidade = 0
             ORDER BY nome ASC',
            ['user_id' => $userId]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function valueByCategory(int $userId): array
    {
        return DB::run(
            'SELECT categoria,
                    COUNT(*) AS total_itens,
                    SUM(quantidade) AS quantidade_total,
                    SUM(quantidade * custo_unitario) AS valor_total
             FROM components
             WHERE user_id = :user_id AND deleted_at IS NULL
             GROUP BY categoria
             ORDER BY valor_total DESC',
            ['user_id' => $userId]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function allForExport(int $userId): array
    {
        return DB::run(
            'SELECT *
             FROM components
             WHERE user_id = :user_id AND deleted_at IS NULL
             ORDER BY nome ASC',
            ['user_id' => $userId]
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param array<int, array<string, mixed>> $rows
     * @return array{inserted:int, updated:int, errors:array<int, string>}
     */
    public static function bulkUpsert(int $userId, array $rows, bool $updateExisting = false): array
    {
        $inserted = 0;
        $updated = 0;
        $errors = [];

        DB::transaction(static function () use ($userId, $rows, $updateExisting, &$inserted, &$updated, &$errors): void {
            foreach ($rows as $index => $row) {
                $sku = $row['sku'] ?? null;
                if (!is_string($sku) || trim($sku) === '') {
                    $errors[] = 'Linha ' . ($index + 1) . ': SKU ausente';
                    continue;
                }

                $sku = trim($sku);
                $existing = self::findBySku($sku, $userId);

                $data = [
                    'nome' => $row['nome'] ?? $existing['nome'] ?? null,
                    'sku' => $sku,
                    'fabricante' => $row['fabricante'] ?? $existing['fabricante'] ?? null,
                    'cod_fabricante' => $row['cod_fabricante'] ?? $existing['cod_fabricante'] ?? null,
                    'descricao' => $row['descricao'] ?? $existing['descricao'] ?? null,
                    'categoria' => $row['categoria'] ?? $existing['categoria'] ?? null,
                    'tags' => $row['tags'] ?? $existing['tags'] ?? null,
                    'quantidade' => isset($row['quantidade']) ? (int)$row['quantidade'] : ($existing['quantidade'] ?? 0),
                    'unidade' => $row['unidade'] ?? $existing['unidade'] ?? 'un',
                    'localizacao' => $row['localizacao'] ?? $existing['localizacao'] ?? null,
                    'tolerancia' => $row['tolerancia'] ?? $existing['tolerancia'] ?? null,
                    'potencia' => $row['potencia'] ?? $existing['potencia'] ?? null,
                    'tensao_max' => $row['tensao_max'] ?? $existing['tensao_max'] ?? null,
                    'footprint' => $row['footprint'] ?? $existing['footprint'] ?? null,
                    'custo_unitario' => isset($row['custo_unitario']) ? (float)$row['custo_unitario'] : ($existing['custo_unitario'] ?? 0),
                    'preco_medio' => isset($row['preco_medio']) && $row['preco_medio'] !== '' ? (float)$row['preco_medio'] : ($existing['preco_medio'] ?? null),
                    'min_estoque' => isset($row['min_estoque']) ? (int)$row['min_estoque'] : ($existing['min_estoque'] ?? 0),
                    'datasheet_path' => $existing['datasheet_path'] ?? null,
                ];

                if ($data['nome'] === null) {
                    $errors[] = 'Linha ' . ($index + 1) . ': nome ausente';
                    continue;
                }

                if ($existing) {
                    if ($updateExisting) {
                        self::updateFields((int)$existing['id'], $userId, $data);
                        $updated++;
                    }
                    continue;
                }

                self::create($userId, $data);
                $inserted++;
            }
        });

        return [
            'inserted' => $inserted,
            'updated' => $updated,
            'errors' => $errors,
        ];
    }
}
