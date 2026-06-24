<?php

declare(strict_types=1);

use App\Core\View;

$old = $old ?? [];
$errors = $errors ?? [];
?>
<div class="space-y-6">
    <header class="app-page-header">
        <h1>Novo componente</h1>
        <p>Cadastre um item para acompanhar quantidades e movimentações.</p>
    </header>

    <?php View::partial('components/_form', [
        'component' => $old,
        'errors' => $errors,
        'action' => '/components/store',
        'csrfToken' => $csrfToken ?? '',
        'isEdit' => false,
    ]); ?>
</div>
