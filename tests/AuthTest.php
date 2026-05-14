<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\DB;
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
    $_COOKIE['anonymousUserId'] = '11111111-1111-4111-8111-111111111111';

    $user = User::ensureAnonymousUser($_COOKIE['anonymousUserId']);
    $authUser = Auth::user();

    $sameUser = User::ensureAnonymousUser($_COOKIE['anonymousUserId']);
    $otherUser = User::ensureAnonymousUser('22222222-2222-4222-8222-222222222222');

    assertTrue((int)$user['id'] > 0, 'Usuario anonimo deve ser criado com ID valido.');
    assertTrue($user['anonymous_user_id'] === $_COOKIE['anonymousUserId'], 'Usuario deve guardar o anonymousUserId.');
    assertTrue(Auth::check(), 'Acesso anonimo deve estar sempre liberado.');
    assertTrue(Auth::userId() === (int)$user['id'], 'Auth deve retornar o usuario anonimo do cookie atual.');
    assertTrue((int)$sameUser['id'] === (int)$user['id'], 'Recarregar com o mesmo anonymousUserId deve manter o mesmo estoque.');
    assertTrue((int)$otherUser['id'] !== (int)$user['id'], 'Outro anonymousUserId deve gerar outro estoque.');
    assertTrue($authUser['name'] === 'Estoque anonimo', 'Snapshot anonimo deve ter o nome esperado.');

    echo "AuthTest:\n";
    foreach ($results as [$passed, $message]) {
        echo ($passed ? '[OK] ' : '[FALHA] ') . $message . PHP_EOL;
    }
} catch (Throwable $throwable) {
    echo 'AuthTest interrompido: ' . $throwable->getMessage() . PHP_EOL;
} finally {
    $pdo->rollBack();
}
