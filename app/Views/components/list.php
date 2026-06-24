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
    <div class="flex flex-wrap items-center justify-between gap-4">
        <header class="app-page-header">
            <h1>Componentes</h1>
            <p>Gerencie o estoque, atualize quantidades e acompanhe movimentações.</p>
        </header>
        <div class="flex flex-wrap items-center gap-2">
            <form method="POST" action="/components/seed-test" class="inline" onsubmit="return confirm('Adicionar 100 componentes de teste a este estoque? Itens ja existentes com SKU TEST-001 a TEST-100 serao ignorados.');">
                <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfSeedTest, ENT_QUOTES, 'UTF-8'); ?>">
                <button type="submit" class="app-btn-outline-success">
                        Adicionar componentes
                </button>
            </form>
            <?php if ((int)$totalComponents > 0): ?>
                <form method="POST" action="/components/delete-all" class="inline" onsubmit="return confirm('Remover todos os componentes deste estoque? Esta acao nao pode ser desfeita pela interface.');">
                    <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfDeleteAll, ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="app-btn-outline-danger">
                        Excluir todos
                    </button>
                </form>
            <?php endif; ?>
            <a href="/components/new" data-tour="components-new" class="app-btn-primary">
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
