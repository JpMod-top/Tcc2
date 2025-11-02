<?php

declare(strict_types=1);

$options = getopt('', ['db::']);

if ($argc < 2) {
    fwrite(STDERR, "Usage: php run_sql.php <path-to-sql> [--db=database]\n");
    exit(1);
}

$sqlFile = $argv[1];
if (!is_file($sqlFile)) {
    fwrite(STDERR, "SQL file not found: {$sqlFile}\n");
    exit(1);
}

$config = require __DIR__ . '/../config/config.php';
$db = $config['db'];

if (isset($options['db'])) {
    $db['database'] = (string)$options['db'];
}

$dsn = sprintf(
    'mysql:host=%s;port=%d;charset=%s',
    $db['host'],
    $db['port'],
    $db['charset'] ?? 'utf8mb4'
);

$optionsPdo = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $db['username'], $db['password'], $optionsPdo);
    $pdo->exec('USE `' . str_replace('`', '``', $db['database']) . '`');
} catch (PDOException $e) {
    fwrite(STDERR, 'Connection failed: ' . $e->getMessage() . "\n");
    exit(1);
}

$sql = file_get_contents($sqlFile);
if ($sql === false) {
    fwrite(STDERR, "Unable to read SQL file: {$sqlFile}\n");
    exit(1);
}

$sql = preg_replace('/\-\-[^\n]*\n/', "\n", $sql);
$sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

$statements = array_filter(array_map('trim', explode(';', $sql)), static fn($stmt) => $stmt !== '');

$pdo->beginTransaction();
try {
    foreach ($statements as $statement) {
        $pdo->exec($statement);
    }
    $pdo->commit();
    fwrite(STDOUT, 'Executed ' . count($statements) . " statements from {$sqlFile}\n");
} catch (Throwable $throwable) {
    $pdo->rollBack();
    fwrite(STDERR, 'Error executing statements: ' . $throwable->getMessage() . "\n");
    exit(1);
}
