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
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <h2 class="text-sm font-semibold text-slate-500 dark:text-slate-400">Componentes</h2>
            <p class="mt-2 text-3xl font-bold text-slate-800 dark:text-slate-100">
                <?php echo number_format((int)$summary['total_componentes'], 0, ',', '.'); ?>
            </p>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Itens cadastrados</p>
        </article>
        <article class="rounded-xl border border-amber-200 bg-white p-6 shadow-sm dark:border-amber-500/30 dark:bg-slate-900">
            <h2 class="text-sm font-semibold text-amber-600 dark:text-amber-400">Abaixo do mínimo</h2>
            <p class="mt-2 text-3xl font-bold text-amber-700 dark:text-amber-300">
                <?php echo number_format((int)$summary['abaixo_minimo'], 0, ',', '.'); ?>
            </p>
            <p class="mt-1 text-xs text-amber-600/80 dark:text-amber-400/80">Revise reposição urgente</p>
        </article>
        <article class="rounded-xl border border-rose-200 bg-white p-6 shadow-sm dark:border-rose-500/30 dark:bg-slate-900">
            <h2 class="text-sm font-semibold text-rose-600 dark:text-rose-400">Zerados</h2>
            <p class="mt-2 text-3xl font-bold text-rose-700 dark:text-rose-300">
                <?php echo number_format((int)$summary['zerados'], 0, ',', '.'); ?>
            </p>
            <p class="mt-1 text-xs text-rose-600/80 dark:text-rose-400/80">Sem estoque no momento</p>
        </article>
        <article class="rounded-xl border border-emerald-200 bg-white p-6 shadow-sm dark:border-emerald-500/30 dark:bg-slate-900">
            <h2 class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">Valor estimado</h2>
            <p class="mt-2 text-3xl font-bold text-emerald-700 dark:text-emerald-300">
                R$ <?php echo number_format((float)$summary['valor_total'], 2, ',', '.'); ?>
            </p>
            <p class="mt-1 text-xs text-emerald-600/80 dark:text-emerald-400/80">Estoque multiplicado por custo</p>
        </article>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Valor por categoria</h2>
                <a href="/reports/value-category" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">Ver relatório</a>
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
                class="mt-6 h-64 w-full"
                aria-label="Gráfico de valor por categoria"
                data-chart-type="category-bar"
                data-chart-values="<?php echo htmlspecialchars(json_encode($chartData, JSON_THROW_ON_ERROR), ENT_QUOTES, 'UTF-8'); ?>"
            ></canvas>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Últimos componentes</h2>
                <a href="/components" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">Ver todos</a>
            </div>
            <ul class="mt-4 space-y-3">
                <?php if (empty($recent)): ?>
                    <li class="rounded-lg border border-dashed border-slate-200 p-4 text-sm text-slate-500 dark:border-slate-700 dark:text-slate-400">
                        Nenhum componente cadastrado recentemente.
                    </li>
                <?php else: ?>
                    <?php foreach ($recent as $item): ?>
                        <li class="rounded-lg border border-slate-200 p-4 transition hover:border-blue-300 dark:border-slate-700 dark:hover:border-blue-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                                        <?php echo htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        SKU: <?php echo htmlspecialchars($item['sku'], ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                </div>
                                <a href="/components/view?id=<?php echo (int)$item['id']; ?>" class="text-xs text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
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
