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

if (!function_exists('assertTrue')) {
    function assertTrue(bool $condition, string $message): void
    {
        global $results;
        $results[] = [$condition, $message];
    }
}

try {
    $userA = User::ensureAnonymousUser('aaaaaaaa-aaaa-4aaa-8aaa-aaaaaaaaaaaa');
    $userB = User::ensureAnonymousUser('bbbbbbbb-bbbb-4bbb-8bbb-bbbbbbbbbbbb');
    $userAId = (int)$userA['id'];
    $userBId = (int)$userB['id'];

    $compA = Component::create($userAId, [
        'nome' => 'Capacitor 100nF',
        'sku' => 'ISO-A-' . bin2hex(random_bytes(2)),
        'quantidade' => 50,
        'custo_unitario' => 0.12,
        'min_estoque' => 10,
    ]);

    $compB = Component::create($userBId, [
        'nome' => 'Mosfet 30V',
        'sku' => 'ISO-B-' . bin2hex(random_bytes(2)),
        'quantidade' => 30,
        'custo_unitario' => 1.25,
        'min_estoque' => 5,
    ]);

    $listA = Component::paginateForUser($userAId, ['per_page' => 20]);
    $idsA = array_map(static fn($item) => (int)$item['id'], $listA['data']);
    assertTrue(in_array($compA, $idsA, true) && !in_array($compB, $idsA, true), 'Navegador A recebe apenas seus componentes.');

    $listB = Component::paginateForUser($userBId, ['per_page' => 20]);
    $idsB = array_map(static fn($item) => (int)$item['id'], $listB['data']);
    assertTrue(in_array($compB, $idsB, true) && !in_array($compA, $idsB, true), 'Navegador B recebe apenas seus componentes.');

    echo "MultitenancyIsolationTest:\n";
    foreach ($results as [$passed, $message]) {
        echo ($passed ? '[OK] ' : '[FALHA] ') . $message . PHP_EOL;
    }
} catch (Throwable $throwable) {
    echo 'MultitenancyIsolationTest interrompido: ' . $throwable->getMessage() . PHP_EOL;
} finally {
    $pdo->rollBack();
}
