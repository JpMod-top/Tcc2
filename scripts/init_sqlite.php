<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$dbFile = $root . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'database.sqlite';
$schemaFile = $root . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'sqlite_schema.sql';

if (!is_readable($schemaFile)) {
    fwrite(STDERR, "Schema file not found: $schemaFile\n");
    exit(1);
}

$schema = file_get_contents($schemaFile);
if ($schema === false) {
    fwrite(STDERR, "Unable to read schema file\n");
    exit(1);
}

$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec('PRAGMA foreign_keys = ON;');

$statements = array_filter(array_map('trim', explode(';', $schema)), static fn($s) => $s !== '');
$pdo->beginTransaction();
try {
    foreach ($statements as $stmt) {
        $pdo->exec($stmt);
    }
    $pdo->commit();
    fwrite(STDOUT, "SQLite schema applied to $dbFile\n");
} catch (Throwable $t) {
    $pdo->rollBack();
    fwrite(STDERR, "Failed to apply schema: " . $t->getMessage() . "\n");
    exit(1);
}
