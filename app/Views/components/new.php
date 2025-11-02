<?php

declare(strict_types=1);

use App\Core\View;

$old = $old ?? [];
$errors = $errors ?? [];
?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">Novo componente</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Cadastre um item para acompanhar quantidades e movimentações.</p>
    </div>

    <?php View::partial('components/_form', [
        'component' => $old,
        'errors' => $errors,
        'action' => '/components/store',
        'csrfToken' => $csrfToken ?? '',
        'isEdit' => false,
    ]); ?>
</div>
