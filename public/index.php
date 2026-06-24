<?php

declare(strict_types=1);

use App\Controllers\ComponentController;
use App\Controllers\ComponentTypeController;
use App\Controllers\DashboardController;
use App\Controllers\ExportController;
use App\Controllers\ImportController;
use App\Controllers\ReportController;
use App\Core\Auth;
use App\Core\DB;
use App\Core\Router;

$projectRoot = dirname(__DIR__);
$repositoryRoot = realpath(__DIR__ . '/../repositories/Tcc2') ?: __DIR__ . '/../repositories/Tcc2';

if (is_file($repositoryRoot . '/vendor/autoload.php')) {
    $projectRoot = $repositoryRoot;
} elseif (!is_file($projectRoot . '/vendor/autoload.php') && is_file(__DIR__ . '/../estoque/vendor/autoload.php')) {
    $projectRoot = realpath(__DIR__ . '/../estoque') ?: __DIR__ . '/../estoque';
}

require $projectRoot . '/vendor/autoload.php';

$config = require $projectRoot . '/config/config.php';

$debug = (bool)($config['app']['debug'] ?? false);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

set_exception_handler(static function (Throwable $throwable) use ($debug): void {
    error_log((string)$throwable);

    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
    }

    if ($debug) {
        echo '<pre>' . htmlspecialchars((string)$throwable, ENT_QUOTES, 'UTF-8') . '</pre>';
        return;
    }

    echo 'Erro interno. Tente novamente em instantes.';
});

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (($_SERVER['SERVER_PORT'] ?? '80') === '443');

if (session_status() === PHP_SESSION_NONE) {
    session_name($config['session']['name']);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    ini_set('session.cookie_httponly', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');

    session_start();
}

function has_valid_anonymous_user_cookie(): bool
{
    $value = (string)($_COOKIE['anonymousUserId'] ?? '');

    return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $value) === 1;
}

function render_anonymous_user_bootstrap(): void
{
    header('Content-Type: text/html; charset=utf-8');
    echo <<<'HTML'
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Preparando estoque</title>
    <script>
        (function () {
            function fallbackUuid() {
                return '10000000-1000-4000-8000-100000000000'.replace(/[018]/g, function (c) {
                    return (Number(c) ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> Number(c) / 4).toString(16);
                });
            }

            window.getOrCreateAnonymousUserId = function () {
                var key = 'anonymousUserId';
                var id = localStorage.getItem(key);
                if (!id) {
                    id = crypto.randomUUID ? crypto.randomUUID() : fallbackUuid();
                    localStorage.setItem(key, id);
                }
                document.cookie = key + '=' + encodeURIComponent(id) + '; Max-Age=31536000; Path=/; SameSite=Lax';
                return id;
            };

            window.getOrCreateAnonymousUserId();
            window.location.replace(window.location.href);
        })();
    </script>
</head>
<body>Preparando seu estoque...</body>
</html>
HTML;
}

if (!has_valid_anonymous_user_cookie()) {
    render_anonymous_user_bootstrap();
    exit;
}

DB::init($config['db']);
Auth::init($isHttps);

$router = new Router();

$router->get('/', static function (): void {
    header('Location: /dashboard', true, 302);
    exit;
});

$router->get('/dashboard', [DashboardController::class, 'index']);

$legacyRedirect = static function (): void {
    header('Location: /dashboard', true, 302);
    exit;
};

$router->get('/login', $legacyRedirect);
$router->get('/register', $legacyRedirect);
$router->get('/forgot', $legacyRedirect);
$router->get('/reset', $legacyRedirect);
$router->get('/profile', $legacyRedirect);

$router->get('/components', [ComponentController::class, 'index']);
$router->get('/components/new', [ComponentController::class, 'create']);
$router->post('/components/store', [ComponentController::class, 'store']);
$router->get('/components/view', [ComponentController::class, 'show']);
$router->get('/components/edit', [ComponentController::class, 'edit']);
$router->post('/components/update', [ComponentController::class, 'update']);
$router->post('/components/delete', [ComponentController::class, 'destroy']);
$router->post('/components/delete-all', [ComponentController::class, 'destroyAll']);
$router->post('/components/seed-test', [ComponentController::class, 'seedTestComponents']);
$router->post('/components/inline', [ComponentController::class, 'inlineUpdate']);
$router->post('/components/stock-move', [ComponentController::class, 'stockMove']);
$router->post('/components/upload-image', [ComponentController::class, 'uploadImage']);
$router->post('/components/delete-image', [ComponentController::class, 'deleteImage']);
$router->post('/components/set-cover', [ComponentController::class, 'setCover']);
$router->post('/components/upload-datasheet', [ComponentController::class, 'uploadDatasheet']);
$router->get('/components/datasheet', [ComponentController::class, 'downloadDatasheet']);
$router->get('/components/image', [ComponentController::class, 'serveImage']);
$router->get('/components/types/new', [ComponentTypeController::class, 'create']);
$router->post('/components/types', [ComponentTypeController::class, 'store']);

$router->get('/reports', [ReportController::class, 'index']);
$router->get('/reports/low-stock', [ReportController::class, 'lowStock']);
$router->get('/reports/zeroed', [ReportController::class, 'zeroed']);
$router->get('/reports/value-category', [ReportController::class, 'valueByCategory']);
$router->get('/reports/moves', [ReportController::class, 'moves']);
$router->post('/reports/moves/export', [ReportController::class, 'exportMovesCsv']);

$router->get('/import', [ImportController::class, 'index']);
$router->post('/import/preview', [ImportController::class, 'preview']);
$router->post('/import/cancel', [ImportController::class, 'cancel']);
$router->post('/import/process', [ImportController::class, 'process']);

$router->get('/export', [ExportController::class, 'index']);
$router->post('/export/csv', [ExportController::class, 'exportCsv']);

$router->dispatch();
