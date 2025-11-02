<?php

declare(strict_types=1);

use App\Core\View;

$components = $components ?? [];
$pagination = $pagination ?? [];
$filters = $filters ?? [];
$categories = $categories ?? [];
$csrfInline = $csrfInline ?? '';
$csrfDelete = $csrfDelete ?? '';
$csrfStock = $csrfStock ?? '';
?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">Componentes</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Gerencie o estoque, atualize quantidades e acompanhe movimentações.
            </p>
        </div>
        <a href="/components/new" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500 dark:bg-blue-500 dark:text-slate-900 dark:hover:bg-blue-400">
            Novo componente
        </a>
    </div>

    <?php View::partial('components/_filters', [
        'filters' => $filters,
        'categories' => $categories,
    ]); ?>

    <?php View::partial('components/_table', [
        'components' => $components,
        'csrfInline' => $csrfInline,
        'csrfDelete' => $csrfDelete,
        'csrfStock' => $csrfStock,
    ]); ?>

    <?php View::partial('partials/pagination', [
        'currentPage' => $pagination['page'] ?? 1,
        'lastPage' => $pagination['last_page'] ?? 1,
        'baseUrl' => $_SERVER['REQUEST_URI'] ?? '/components',
    ]); ?>
</div>
