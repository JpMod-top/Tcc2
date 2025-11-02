<?php

declare(strict_types=1);

use App\Core\View;

$component = $component ?? [];
$errors = $errors ?? [];
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">
                Editar componente
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Atualize as informações principais do componente.
            </p>
        </div>
        <a href="/components/view?id=<?php echo (int)($component['id'] ?? 0); ?>" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
            Voltar para detalhes
        </a>
    </div>

    <?php View::partial('components/_form', [
        'component' => $component,
        'errors' => $errors,
        'action' => '/components/update',
        'csrfToken' => $csrfToken ?? '',
        'isEdit' => true,
    ]); ?>
</div>
