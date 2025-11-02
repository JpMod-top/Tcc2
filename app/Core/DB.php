<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;
use PDOStatement;
use RuntimeException;
use Throwable;

class DB
{
    private static ?PDO $pdo = null;

    /**
     * @var array<string, mixed>
     */
    private static array $config = [];

    /**
     * @param array<string, mixed> $config
     */
    public static function init(array $config): void
    {
        self::$config = $config;
    }

    public static function connection(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        if (empty(self::$config)) {
            throw new RuntimeException('Database configuration missing.');
        }


        $driver = strtolower(self::$config['connection'] ?? 'mysql');

        try {
            if ($driver === 'sqlite') {
                $path = (string) self::$config['database'];
                $dsn = "sqlite:" . $path;

                self::$pdo = new PDO($dsn);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } else {
                $charset = self::$config['charset'] ?? 'utf8mb4';
                $collation = self::$config['collation'] ?? 'utf8mb4_unicode_ci';
                $dsn = sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                    self::$config['host'],
                    self::$config['port'],
                    self::$config['database'],
                    $charset
                );

                self::$pdo = new PDO(
                    $dsn,
                    self::$config['username'],
                    self::$config['password'],
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '{$charset}' COLLATE '{$collation}'",
                        PDO::ATTR_EMULATE_PREPARES => false,
                    ]
                );
            }
        } catch (PDOException $exception) {
            throw new RuntimeException('Database connection failed: ' . $exception->getMessage(), (int)$exception->getCode(), $exception);
        }

        return self::$pdo;
    }

    /**
     * @param array<int|string, mixed> $params
     */
    public static function run(string $query, array $params = []): PDOStatement
    {
        $statement = self::connection()->prepare($query);

        foreach ($params as $key => $value) {
            $parameter = is_int($key)
                ? $key + 1
                : (str_starts_with((string)$key, ':') ? (string)$key : ':' . $key);

            $type = match (true) {
                $value === null => PDO::PARAM_NULL,
                is_int($value) => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                default => PDO::PARAM_STR,
            };

            $statement->bindValue($parameter, $value, $type);
        }

        $statement->execute();

        return $statement;
    }

    /**
     * @template TReturn
     * @param callable(PDO):TReturn $callback
     * @return TReturn
     */
    public static function transaction(callable $callback): mixed
    {
        $pdo = self::connection();

        try {
            $pdo->beginTransaction();
            $result = $callback($pdo);
            $pdo->commit();

            return $result;
        } catch (Throwable $throwable) {
            $pdo->rollBack();
            throw $throwable;
        }
    }
}
