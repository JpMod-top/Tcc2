<?php

declare(strict_types=1);

$moves = $moves ?? [];
$totals = $totals ?? ['entrada' => 0, 'saida' => 0, 'ajuste' => 0];
$filters = $filters ?? [];
$components = $components ?? [];
$csrfExport = $csrfExport ?? '';
?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">Movimentações</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Entradas, saídas e ajustes no período selecionado.</p>
    </div>

    <form method="GET" action="/reports/moves" class="grid gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900 md:grid-cols-4">
        <label class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">
            De
            <input type="date" name="from" value="<?php echo htmlspecialchars($filters['from'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
        </label>
        <label class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400">
            Até
            <input type="date" name="to" value="<?php echo htmlspecialchars($filters['to'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
        </label>
        <label class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400 md:col-span-2">
            Componente
            <select name="component_id" class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                <option value="">Todos</option>
                <?php foreach ($components as $component): ?>
                    <option value="<?php echo (int)$component['id']; ?>" <?php echo ((int)($filters['component_id'] ?? 0) === (int)$component['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($component['nome'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <div class="md:col-span-2">
            <button type="submit" class="w-full rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500 dark:bg-blue-500 dark:text-slate-900 dark:hover:bg-blue-400">
                Aplicar filtros
            </button>
        </div>
        <div class="md:col-span-2">
            <a href="/reports/moves" class="inline-flex w-full items-center justify-center rounded border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">
                Limpar filtros
            </a>
        </div>
    </form>

    <section class="grid gap-4 md:grid-cols-3">
        <div class="rounded-xl border border-emerald-200 bg-white p-6 shadow-sm dark:border-emerald-500/30 dark:bg-slate-900">
            <h2 class="text-xs font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-400">Entradas</h2>
            <p class="mt-2 text-2xl font-bold text-emerald-700 dark:text-emerald-300"><?php echo number_format((int)$totals['entrada'], 0, ',', '.'); ?></p>
        </div>
        <div class="rounded-xl border border-rose-200 bg-white p-6 shadow-sm dark:border-rose-500/30 dark:bg-slate-900">
            <h2 class="text-xs font-semibold uppercase tracking-wide text-rose-600 dark:text-rose-400">Saídas</h2>
            <p class="mt-2 text-2xl font-bold text-rose-700 dark:text-rose-300"><?php echo number_format((int)$totals['saida'], 0, ',', '.'); ?></p>
        </div>
        <div class="rounded-xl border border-amber-200 bg-white p-6 shadow-sm dark:border-amber-500/30 dark:bg-slate-900">
            <h2 class="text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-400">Ajustes</h2>
            <p class="mt-2 text-2xl font-bold text-amber-700 dark:text-amber-300"><?php echo number_format((int)$totals['ajuste'], 0, ',', '.'); ?></p>
        </div>
    </section>

    <div class="flex justify-end">
        <form method="POST" action="/reports/moves/export" class="flex items-center gap-3">
            <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfExport, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="from" value="<?php echo htmlspecialchars($filters['from'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="to" value="<?php echo htmlspecialchars($filters['to'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="component_id" value="<?php echo htmlspecialchars((string)($filters['component_id'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="inline-flex items-center rounded border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">
                Exportar CSV
            </button>
        </form>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700 dark:text-slate-200">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-slate-800/60 dark:text-slate-400">
                <tr>
                    <th class="px-4 py-3 text-left">Data</th>
                    <th class="px-4 py-3 text-left">Tipo</th>
                    <th class="px-4 py-3 text-left">Componente</th>
                    <th class="px-4 py-3 text-right">Quantidade</th>
                    <th class="px-4 py-3 text-left">Motivo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php if (empty($moves)): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">
                            Nenhuma movimentação para o período.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($moves as $move): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <td class="px-4 py-3"><?php echo htmlspecialchars($move['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-4 py-3 capitalize"><?php echo htmlspecialchars($move['tipo'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="px-4 py-3">
                                <?php
                                $componentName = '';
                                foreach ($components as $component) {
                                    if ((int)$component['id'] === (int)$move['component_id']) {
                                        $componentName = $component['nome'];
                                        break;
                                    }
                                }
                                ?>
                                <?php echo htmlspecialchars($componentName ?: 'ID ' . $move['component_id'], ENT_QUOTES, 'UTF-8'); ?>
                            </td>
                            <td class="px-4 py-3 text-right <?php echo ((int)$move['quantidade']) < 0 ? 'text-rose-500 dark:text-rose-300' : 'text-emerald-600 dark:text-emerald-300'; ?>">
                                <?php echo (int)$move['quantidade']; ?>
                            </td>
                            <td class="px-4 py-3 text-slate-500 dark:text-slate-400"><?php echo htmlspecialchars($move['motivo'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
