<?php

declare(strict_types=1);

$summary = $summary ?? [
    'total_componentes' => 0,
    'abaixo_minimo' => 0,
    'zerados' => 0,
    'valor_total' => 0.0,
];

$recent = $recent ?? [];
$valueByCategory = $valueByCategory ?? [];
?>
<div class="space-y-8">
    <header class="app-page-header">
        <h1>Dashboard</h1>
        <p>Visão geral do estoque, indicadores e últimas atualizações.</p>
    </header>

    <section data-tour="dashboard-summary" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="app-stat-card app-stat-card--brand">
            <h2 class="app-stat-label">Componentes</h2>
            <p class="app-stat-value">
                <?php echo number_format((int)$summary['total_componentes'], 0, ',', '.'); ?>
            </p>
            <p class="app-stat-hint">Itens cadastrados</p>
        </article>
        <article class="app-stat-card app-stat-card--warning">
            <h2 class="app-stat-label">Abaixo do mínimo</h2>
            <p class="app-stat-value text-amber-700 dark:text-amber-300">
                <?php echo number_format((int)$summary['abaixo_minimo'], 0, ',', '.'); ?>
            </p>
            <p class="app-stat-hint">Revise reposição urgente</p>
        </article>
        <article class="app-stat-card app-stat-card--danger">
            <h2 class="app-stat-label">Zerados</h2>
            <p class="app-stat-value text-rose-700 dark:text-rose-300">
                <?php echo number_format((int)$summary['zerados'], 0, ',', '.'); ?>
            </p>
            <p class="app-stat-hint">Sem estoque no momento</p>
        </article>
        <article class="app-stat-card app-stat-card--success">
            <h2 class="app-stat-label">Valor estimado</h2>
            <p class="app-stat-value text-emerald-700 dark:text-emerald-300">
                R$ <?php echo number_format((float)$summary['valor_total'], 2, ',', '.'); ?>
            </p>
            <p class="app-stat-hint">Estoque multiplicado por custo</p>
        </article>
    </section>

    <section data-tour="dashboard-details" class="grid gap-4 lg:grid-cols-2 lg:gap-6">
        <div class="app-panel">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-50">Valor por categoria</h2>
                <a href="/reports/value-category" class="app-link">Ver relatório</a>
            </div>
            <?php
            $chartData = array_map(static function ($item) {
                return [
                    'categoria' => $item['categoria'] ?? 'Sem categoria',
                    'valor_total' => (float)($item['valor_total'] ?? 0),
                ];
            }, $valueByCategory);
            ?>
            <canvas
                id="category-chart"
                class="mt-4 h-52 w-full sm:mt-6 sm:h-64"
                aria-label="Gráfico de valor por categoria"
                data-chart-type="category-bar"
                data-chart-values="<?php echo htmlspecialchars(json_encode($chartData, JSON_THROW_ON_ERROR), ENT_QUOTES, 'UTF-8'); ?>"
            ></canvas>
        </div>
        <div class="app-panel">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-slate-50">Últimos componentes</h2>
                <a href="/components" class="app-link">Ver todos</a>
            </div>
            <ul class="mt-4 space-y-3">
                <?php if (empty($recent)): ?>
                    <li class="app-empty-state">
                        Nenhum componente cadastrado recentemente.
                    </li>
                <?php else: ?>
                    <?php foreach ($recent as $item): ?>
                        <li class="rounded-xl border border-slate-200/80 bg-slate-50/50 p-4 transition hover:border-brand-200 hover:bg-white dark:border-slate-800 dark:bg-slate-900/50 dark:hover:border-brand-800/60">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-slate-50">
                                        <?php echo htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        SKU: <?php echo htmlspecialchars($item['sku'], ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                </div>
                                <a href="/components/view?id=<?php echo (int)$item['id']; ?>" class="app-link text-xs">
                                    Detalhes
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </section>
</div>
