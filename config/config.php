<?php

declare(strict_types=1);

/**
 * @return array<string, mixed>
 */
if (!function_exists('app_config_parse_env')) {
    function app_config_parse_env(string $filePath): array
    {
        $vars = [];
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                continue;
            }

            [$key, $value] = array_pad(explode('=', $trimmed, 2), 2, '');
            $key = trim($key);
            $value = trim($value);

            if ($value === '') {
                $vars[$key] = '';
                continue;
            }

            $quote = $value[0];
            if ((($quote === '"' || $quote === "'") && str_ends_with($value, (string)$quote)) && strlen($value) >= 2) {
                $value = substr($value, 1, -1);
                $value = stripcslashes($value);
            }

            if (strcasecmp($value, 'null') === 0) {
                $vars[$key] = null;
            } elseif (strcasecmp($value, 'true') === 0) {
                $vars[$key] = true;
            } elseif (strcasecmp($value, 'false') === 0) {
                $vars[$key] = false;
            } else {
                $vars[$key] = $value;
            }
        }

        return $vars;
    }
}

$rootPath = dirname(__DIR__);
$envFile = $rootPath . DIRECTORY_SEPARATOR . '.env';
$env = [];

if (is_readable($envFile)) {
    $env = app_config_parse_env($envFile);
}

foreach ($env as $key => $value) {
    $_ENV[$key] = $value;
    if (!array_key_exists($key, $_SERVER)) {
        $_SERVER[$key] = $value;
    }
    if ($value !== null) {
        putenv($key . '=' . $value);
    }
}

date_default_timezone_set('America/Sao_Paulo');

$dbConnection = $env['DB_CONNECTION'] ?? 'mysql';
$dbDatabase = $env['DB_DATABASE'] ?? ($dbConnection === 'sqlite' || ($env['APP_ENV'] ?? '') === 'local' ? $rootPath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'database.sqlite' : 'meu_estoque_eletronicos');

if ($dbConnection === 'sqlite') {
    $isAbsolutePath = preg_match('/^(?:[A-Za-z]:)?[\/\\\\]/', (string)$dbDatabase) === 1;
    if (!$isAbsolutePath) {
        $dbDatabase = $rootPath . DIRECTORY_SEPARATOR . $dbDatabase;
    }
}

return [
    'app' => [
        'env' => $env['APP_ENV'] ?? 'production',
        'debug' => filter_var($env['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
        'url' => $env['APP_URL'] ?? 'http://localhost:8000',
    ],
    'db' => [
        // connection driver: 'mysql' or 'sqlite'
        'connection' => $dbConnection,
        'host' => $env['DB_HOST'] ?? '127.0.0.1',
        'port' => (int)($env['DB_PORT'] ?? 3306),
        // For sqlite, `database` should be a file path. Default to storage/database.sqlite for local dev.
        'database' => $dbDatabase,
        'username' => $env['DB_USERNAME'] ?? 'root',
        'password' => $env['DB_PASSWORD'] ?? '',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
    ],
    'session' => [
        'name' => $env['SESSION_NAME'] ?? 'meu_estoque_session',
    ],
];
