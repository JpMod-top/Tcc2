<?php

declare(strict_types=1);

use App\Core\DB;
use App\Models\Component;
use App\Models\User;

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/config.php';

DB::init($config['db']);
$pdo = DB::connection();
$pdo->beginTransaction();

$results = [];

function assertTrue(bool $condition, string $message): void
{
    global $results;
    $results[] = [$condition, $message];
}

try {
    $userA = User::create([
        'name' => 'Usuário A',
        'email' => 'multiA+' . bin2hex(random_bytes(3)) . '@example.com',
        'password_hash' => password_hash('Senha123A', PASSWORD_ARGON2ID),
    ]);

    $userB = User::create([
        'name' => 'Usuário B',
        'email' => 'multiB+' . bin2hex(random_bytes(3)) . '@example.com',
        'password_hash' => password_hash('Senha123B', PASSWORD_ARGON2ID),
    ]);

    $compA = Component::create($userA, [
        'nome' => 'Capacitor 100nF',
        'sku' => 'ISO-A-' . bin2hex(random_bytes(2)),
        'quantidade' => 50,
        'custo_unitario' => 0.12,
        'min_estoque' => 10,
    ]);

    $compB = Component::create($userB, [
        'nome' => 'Mosfet 30V',
        'sku' => 'ISO-B-' . bin2hex(random_bytes(2)),
        'quantidade' => 30,
        'custo_unitario' => 1.25,
        'min_estoque' => 5,
    ]);

    $listA = Component::paginateForUser($userA, ['per_page' => 20]);
    $idsA = array_map(static fn($item) => (int)$item['id'], $listA['data']);
    assertTrue(in_array($compA, $idsA, true) && !in_array($compB, $idsA, true), 'Usuário A recebe apenas seus componentes.');

    $listB = Component::paginateForUser($userB, ['per_page' => 20]);
    $idsB = array_map(static fn($item) => (int)$item['id'], $listB['data']);
    assertTrue(in_array($compB, $idsB, true) && !in_array($compA, $idsB, true), 'Usuário B recebe apenas seus componentes.');

    echo "MultitenancyIsolationTest:\n";
    foreach ($results as [$passed, $message]) {
        echo ($passed ? '[OK] ' : '[FALHA] ') . $message . PHP_EOL;
    }
} catch (Throwable $throwable) {
    echo 'MultitenancyIsolationTest interrompido: ' . $throwable->getMessage() . PHP_EOL;
} finally {
    $pdo->rollBack();
}
