<?php

declare(strict_types=1);

$items = $items ?? [];
?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">Componentes zerados</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Itens sem nenhuma unidade disponível em estoque.</p>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700 dark:text-slate-200">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-slate-800/60 dark:text-slate-400">
                <tr>
                    <th class="px-4 py-3 text-left">Nome</th>
                    <th class="px-4 py-3 text-left">SKU</th>
                    <th class="px-4 py-3 text-left">Categoria</th>
                    <th class="px-4 py-3 text-right">Última atualização</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="4" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">
                            Nenhum componente zerado.
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
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300"><?php echo htmlspecialchars($item['categoria'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-4 py-3 text-right text-slate-500 dark:text-slate-400"><?php echo htmlspecialchars($item['updated_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
