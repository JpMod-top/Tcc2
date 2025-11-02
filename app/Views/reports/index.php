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
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">Relatórios</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Explore métricas essenciais para o estoque.</p>
    </div>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <h2 class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Componentes</h2>
            <p class="mt-2 text-2xl font-bold text-slate-800 dark:text-slate-100">
                <?php echo number_format((int)$summary['total_componentes'], 0, ',', '.'); ?>
            </p>
        </article>
        <article class="rounded-xl border border-amber-200 bg-white p-6 shadow-sm dark:border-amber-500/30 dark:bg-slate-900">
            <h2 class="text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">Abaixo do mínimo</h2>
            <p class="mt-2 text-2xl font-bold text-amber-700 dark:text-amber-300">
                <?php echo number_format((int)$summary['abaixo_minimo'], 0, ',', '.'); ?>
            </p>
        </article>
        <article class="rounded-xl border border-rose-200 bg-white p-6 shadow-sm dark:border-rose-500/30 dark:bg-slate-900">
            <h2 class="text-xs font-semibold uppercase tracking-wide text-rose-600 dark:text-rose-400">Zerados</h2>
            <p class="mt-2 text-2xl font-bold text-rose-700 dark:text-rose-300">
                <?php echo number_format((int)$summary['zerados'], 0, ',', '.'); ?>
            </p>
        </article>
        <article class="rounded-xl border border-emerald-200 bg-white p-6 shadow-sm dark:border-emerald-500/30 dark:bg-slate-900">
            <h2 class="text-xs font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-400">Valor total</h2>
            <p class="mt-2 text-2xl font-bold text-emerald-700 dark:text-emerald-300">
                R$ <?php echo number_format((float)$summary['valor_total'], 2, ',', '.'); ?>
            </p>
        </article>
    </section>

    <section class="grid gap-4 md:grid-cols-2">
        <a href="/reports/low-stock" class="group rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-blue-400 hover:shadow dark:border-slate-700 dark:bg-slate-900 dark:hover:border-blue-400/70">
            <h3 class="text-lg font-semibold text-slate-800 transition group-hover:text-blue-600 dark:text-slate-100 dark:group-hover:text-blue-300">Componentes abaixo do mínimo</h3>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Lista de componentes com estoque crítico.</p>
        </a>
        <a href="/reports/zeroed" class="group rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-blue-400 hover:shadow dark:border-slate-700 dark:bg-slate-900 dark:hover:border-blue-400/70">
            <h3 class="text-lg font-semibold text-slate-800 transition group-hover:text-blue-600 dark:text-slate-100 dark:group-hover:text-blue-300">Componentes zerados</h3>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Itens sem nenhuma unidade disponível.</p>
        </a>
        <a href="/reports/value-category" class="group rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition hover:border-blue-400 hover:shadow dark:border-slate-700 dark:bg-slate-900 dark:hover:border-blue-400/70">
            <h3 class="text-lg font-semibold text-slate-800 transition group-hover:text-blue-600 dark:text-slate-100 dark:group-hover:text-blue-300">Valor por categoria</h3>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Montante investido em cada categoria.</p>
        </a>
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Movimentações no período</h3>
            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Filtre entradas, saídas e ajustes entre datas.</p>
            <form method="GET" action="/reports/moves" class="mt-4 grid gap-3 sm:grid-cols-2">
                <label class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">
                    De
                    <input type="date" name="from" class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                </label>
                <label class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">
                    Até
                    <input type="date" name="to" class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
                </label>
                <div class="sm:col-span-2">
                    <button type="submit" class="w-full rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500 dark:bg-blue-500 dark:text-slate-900 dark:hover:bg-blue-400">
                        Acessar relatório
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>
