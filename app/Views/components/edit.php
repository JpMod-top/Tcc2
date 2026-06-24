<?php

declare(strict_types=1);

use App\Core\View;

$component = $component ?? [];
$errors = $errors ?? [];
?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <header class="app-page-header">
            <h1>Editar componente</h1>
            <p>Atualize as informações principais do componente.</p>
        </header>
        <a href="/components/view?id=<?php echo (int)($component['id'] ?? 0); ?>" class="app-link">
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
