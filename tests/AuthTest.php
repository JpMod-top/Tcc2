<?php

declare(strict_types=1);

use App\Core\DB;
use App\Models\User;

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/config.php';

session_name('meu_estoque_test');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

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
    $email = 'test+' . bin2hex(random_bytes(4)) . '@example.com';
    $password = 'Teste123A';

    $userId = User::create([
        'name' => 'Usuario Teste',
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_ARGON2ID),
    ]);

    assertTrue($userId > 0, 'Usuario deve ser criado com ID valido.');

    $user = User::findByEmail($email);
    assertTrue($user !== null, 'Usuario recem-criado deve ser localizado pelo e-mail.');
    assertTrue(password_verify($password, $user['password_hash']), 'Senha deve ser validada com sucesso.');

    $newPassword = 'NovaSenha123';
    $newHash = password_hash($newPassword, PASSWORD_ARGON2ID);
    User::updatePassword($userId, $newHash);

    $updated = User::findById($userId);
    assertTrue($updated !== null && password_verify($newPassword, $updated['password_hash']), 'Senha atualizada deve ser valida.');

    echo "AuthTest:\n";
    foreach ($results as [$passed, $message]) {
        echo ($passed ? '[OK] ' : '[FALHA] ') . $message . PHP_EOL;
    }
} catch (Throwable $throwable) {
    echo 'AuthTest interrompido: ' . $throwable->getMessage() . PHP_EOL;
} finally {
    $pdo->rollBack();
}
