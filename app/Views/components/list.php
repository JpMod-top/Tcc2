<?php

declare(strict_types=1);

use App\Core\View;

$components = $components ?? [];
$pagination = $pagination ?? [];
$filters = $filters ?? [];
$categories = $categories ?? [];
$csrfInline = $csrfInline ?? '';
$csrfDelete = $csrfDelete ?? '';
$csrfDeleteAll = $csrfDeleteAll ?? '';
$csrfSeedTest = $csrfSeedTest ?? '';
$csrfStock = $csrfStock ?? '';
$totalComponents = $totalComponents ?? 0;
?>
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">Componentes</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Gerencie o estoque, atualize quantidades e acompanhe movimentações.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <form method="POST" action="/components/seed-test" class="inline" onsubmit="return confirm('Adicionar 100 componentes de teste a este estoque? Itens ja existentes com SKU TEST-001 a TEST-100 serao ignorados.');">
                <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfSeedTest, ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit" class="inline-flex items-center rounded-lg border border-emerald-500 px-4 py-2 text-sm font-semibold text-emerald-600 transition hover:bg-emerald-50 dark:border-emerald-400 dark:text-emerald-300 dark:hover:bg-emerald-500/10">
                        Adicionar componentes
                </button>
            </form>
            <?php if ((int)$totalComponents > 0): ?>
                <form method="POST" action="/components/delete-all" class="inline" onsubmit="return confirm('Remover todos os componentes deste estoque? Esta acao nao pode ser desfeita pela interface.');">
                    <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfDeleteAll, ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="inline-flex items-center rounded-lg border border-rose-500 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-rose-400 dark:text-rose-300 dark:hover:bg-rose-500/10">
                        Excluir todos
                    </button>
                </form>
            <?php endif; ?>
            <a href="/components/new" data-tour="components-new" class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500 dark:bg-blue-500 dark:text-slate-900 dark:hover:bg-blue-400">
                Novo componente
            </a>
        </div>
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
