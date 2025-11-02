<?php

declare(strict_types=1);

$items = $items ?? [];
?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">Componentes abaixo do mínimo</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Itens cuja quantidade atual é menor ou igual ao estoque mínimo configurado.</p>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700 dark:text-slate-200">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-slate-800/60 dark:text-slate-400">
                <tr>
                    <th class="px-4 py-3 text-left">Nome</th>
                    <th class="px-4 py-3 text-left">SKU</th>
                    <th class="px-4 py-3 text-right">Quantidade</th>
                    <th class="px-4 py-3 text-right">Mínimo</th>
                    <th class="px-4 py-3 text-right">Custo unitário</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">
                            Nenhum componente abaixo do mínimo.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <td class="px-4 py-3 font-medium text-slate-800 dark:text-slate-100">
                                <a href="/components/view?id=<?php echo (int)$item['id']; ?>" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                    <?php echo htmlspecialchars($item['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300"><?php echo htmlspecialchars($item['sku'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-4 py-3 text-right text-rose-600 dark:text-rose-400"><?php echo (int)$item['quantidade']; ?></td>
                            <td class="px-4 py-3 text-right"><?php echo (int)$item['min_estoque']; ?></td>
                            <td class="px-4 py-3 text-right">R$ <?php echo number_format((float)$item['custo_unitario'], 2, ',', '.'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
