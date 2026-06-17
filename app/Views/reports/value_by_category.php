<?php

declare(strict_types=1);

$items = $items ?? [];
?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">Valor total por categoria</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Visao consolidada do investimento em cada categoria de componente.</p>
    </div>

    <?php if (empty($items)): ?>
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-6 text-center text-sm text-slate-500 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-400">
            Nenhum dado disponivel.
        </div>
    <?php else: ?>
        <div class="space-y-3 sm:hidden">
            <?php foreach ($items as $item): ?>
                <article class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                        <?php echo htmlspecialchars($item['categoria'] ?? 'Sem categoria', ENT_QUOTES, 'UTF-8'); ?>
                    </h2>
                    <dl class="mt-3 grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Componentes</dt>
                            <dd class="mt-1 font-semibold text-slate-800 dark:text-slate-100"><?php echo number_format((int)$item['total_itens'], 0, ',', '.'); ?></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Quantidade</dt>
                            <dd class="mt-1 font-semibold text-slate-800 dark:text-slate-100"><?php echo number_format((int)$item['quantidade_total'], 0, ',', '.'); ?></dd>
                        </div>
                        <div class="col-span-2 border-t border-slate-200 pt-3 dark:border-slate-700">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Valor estimado</dt>
                            <dd class="mt-1 text-base font-bold text-emerald-600 dark:text-emerald-300">
                                R$ <?php echo number_format((float)$item['valor_total'], 2, ',', '.'); ?>
                            </dd>
                        </div>
                    </dl>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="hidden overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900 sm:block">
            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700 dark:text-slate-200">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-slate-800/60 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Categoria</th>
                        <th class="px-4 py-3 text-right">Componentes</th>
                        <th class="px-4 py-3 text-right">Quantidade total</th>
                        <th class="px-4 py-3 text-right">Valor estimado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    <?php foreach ($items as $item): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <td class="px-4 py-3 font-medium text-slate-800 dark:text-slate-100"><?php echo htmlspecialchars($item['categoria'] ?? 'Sem categoria', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-4 py-3 text-right"><?php echo number_format((int)$item['total_itens'], 0, ',', '.'); ?></td>
                            <td class="px-4 py-3 text-right"><?php echo number_format((int)$item['quantidade_total'], 0, ',', '.'); ?></td>
                            <td class="px-4 py-3 text-right font-semibold text-emerald-600 dark:text-emerald-300">
                                R$ <?php echo number_format((float)$item['valor_total'], 2, ',', '.'); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
