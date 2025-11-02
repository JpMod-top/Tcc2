<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\ComponentController;
use App\Controllers\ComponentTypeController;
use App\Controllers\DashboardController;
use App\Controllers\ExportController;
use App\Controllers\ImportController;
use App\Controllers\ProfileController;
use App\Controllers\ReportController;
use App\Core\Auth;
use App\Core\DB;
use App\Core\Router;

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/config.php';

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

DB::init($config['db']);
Auth::init($isHttps);

$router = new Router();

$router->get('/', static function (): void {
    header('Location: /dashboard', true, 302);
    exit;
});

$router->get('/dashboard', [DashboardController::class, 'index']);

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/forgot', [AuthController::class, 'showForgot']);
$router->post('/forgot', [AuthController::class, 'forgot']);
$router->get('/reset', [AuthController::class, 'showReset']);
$router->post('/reset', [AuthController::class, 'reset']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/components', [ComponentController::class, 'index']);
$router->get('/components/new', [ComponentController::class, 'create']);
$router->post('/components/store', [ComponentController::class, 'store']);
$router->get('/components/view', [ComponentController::class, 'show']);
$router->get('/components/edit', [ComponentController::class, 'edit']);
$router->post('/components/update', [ComponentController::class, 'update']);
$router->post('/components/delete', [ComponentController::class, 'destroy']);
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
$router->post('/import/process', [ImportController::class, 'process']);

$router->get('/export', [ExportController::class, 'index']);
$router->post('/export/csv', [ExportController::class, 'exportCsv']);

$router->get('/profile', [ProfileController::class, 'index']);
$router->post('/profile/update', [ProfileController::class, 'update']);
$router->post('/profile/password', [ProfileController::class, 'updatePassword']);
$router->post('/profile/delete', [ProfileController::class, 'delete']);

$router->dispatch();
