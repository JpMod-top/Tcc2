<?php

declare(strict_types=1);

$summary = $summary ?? [
    'total_componentes' => 0,
    'abaixo_minimo' => 0,
    'zerados' => 0,
    'valor_total' => 0.0,
];
$csrfMoves = $csrfMoves ?? '';
?>
<div class="space-y-8">
    <header class="app-page-header">
        <h1>Relatórios</h1>
        <p>Explore métricas essenciais para o estoque e apoie a tomada de decisão.</p>
    </header>

    <section data-tour="reports-summary" class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="app-stat-card app-stat-card--brand">
            <h2 class="app-stat-label">Componentes</h2>
            <p class="app-stat-value">
                <?php echo number_format((int)$summary['total_componentes'], 0, ',', '.'); ?>
            </p>
        </article>
        <article class="app-stat-card app-stat-card--warning">
            <h2 class="app-stat-label">Abaixo do mínimo</h2>
            <p class="app-stat-value text-amber-700 dark:text-amber-300">
                <?php echo number_format((int)$summary['abaixo_minimo'], 0, ',', '.'); ?>
            </p>
        </article>
        <article class="app-stat-card app-stat-card--danger">
            <h2 class="app-stat-label">Zerados</h2>
            <p class="app-stat-value text-rose-700 dark:text-rose-300">
                <?php echo number_format((int)$summary['zerados'], 0, ',', '.'); ?>
            </p>
        </article>
        <article class="app-stat-card app-stat-card--success">
            <h2 class="app-stat-label">Valor total</h2>
            <p class="app-stat-value text-emerald-700 dark:text-emerald-300">
                R$ <?php echo number_format((float)$summary['valor_total'], 2, ',', '.'); ?>
            </p>
        </article>
    </section>

    <section data-tour="reports-options" class="grid gap-4 md:grid-cols-2">
        <a href="/reports/low-stock" class="app-report-link">
            <h3>Componentes abaixo do mínimo</h3>
            <p>Lista de componentes com estoque crítico.</p>
        </a>
        <a href="/reports/zeroed" class="app-report-link">
            <h3>Componentes zerados</h3>
            <p>Itens sem nenhuma unidade disponível.</p>
        </a>
        <a href="/reports/value-category" class="app-report-link">
            <h3>Valor por categoria</h3>
            <p>Montante investido em cada categoria.</p>
        </a>
        <div class="app-panel">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-50">Movimentações no período</h3>
            <p class="mt-2 text-sm leading-relaxed text-slate-500 dark:text-slate-400">Filtre entradas, saídas e ajustes entre datas.</p>
            <form method="GET" action="/reports/moves" class="app-field mt-4 grid gap-3 sm:grid-cols-2">
                <label class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">
                    De
                    <input type="date" name="from">
                </label>
                <label class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">
                    Até
                    <input type="date" name="to">
                </label>
                <div class="sm:col-span-2">
                    <button type="submit" class="app-btn-primary w-full">
                        Acessar relatório
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>
