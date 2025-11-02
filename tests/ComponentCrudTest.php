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
    $userId = User::create([
        'name' => 'Tester CRUD',
        'email' => 'crud+' . bin2hex(random_bytes(4)) . '@example.com',
        'password_hash' => password_hash('Teste123A', PASSWORD_ARGON2ID),
    ]);

    $componentId = Component::create($userId, [
        'nome' => 'Resistor de teste',
        'sku' => 'TEST-' . bin2hex(random_bytes(3)),
        'quantidade' => 10,
        'custo_unitario' => 0.15,
        'min_estoque' => 5,
    ]);

    assertTrue($componentId > 0, 'Componente deve ser criado com ID valido.');

    $component = Component::findById($componentId, $userId);
    assertTrue($component !== null && $component['nome'] === 'Resistor de teste', 'Componente criado deve ser localizado.');

    Component::updateFields($componentId, $userId, [
        'nome' => 'Resistor atualizado',
        'quantidade' => 20,
        'custo_unitario' => 0.2,
        'min_estoque' => 8,
    ]);

    $updated = Component::findById($componentId, $userId);
    assertTrue($updated !== null && (int)$updated['quantidade'] === 20, 'Quantidade deve refletir atualizacao.');

    Component::softDelete($componentId, $userId);
    $deleted = Component::findById($componentId, $userId);
    assertTrue($deleted === null, 'Componente excluido logicamente nao deve aparecer nas consultas padrao.');

    echo "ComponentCrudTest:\n";
    foreach ($results as [$passed, $message]) {
        echo ($passed ? '[OK] ' : '[FALHA] ') . $message . PHP_EOL;
    }
} catch (Throwable $throwable) {
    echo 'ComponentCrudTest interrompido: ' . $throwable->getMessage() . PHP_EOL;
} finally {
    $pdo->rollBack();
}
