<?php

declare(strict_types=1);

$preview = $preview ?? [];
$total = $total ?? 0;
$csrfProcess = $csrfProcess ?? '';
?>
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">Pré-visualização</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                <?php echo $total; ?> linha(s) encontrada(s). Revise os dados antes de confirmar a importação.
            </p>
        </div>
        <a href="/import" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">Enviar outro arquivo</a>
    </div>

    <div class="overflow-x-auto rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <table class="min-w-full divide-y divide-slate-200 text-xs dark:divide-slate-700 dark:text-slate-200">
            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-slate-800/60 dark:text-slate-400">
                <tr>
                    <?php if (!empty($preview)): ?>
                        <?php foreach (array_keys($preview[0]) as $column): ?>
                            <th class="px-3 py-2 text-left"><?php echo htmlspecialchars((string)$column, ENT_QUOTES, 'UTF-8'); ?></th>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                <?php if (empty($preview)): ?>
                    <tr>
                        <td class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">
                            Nenhum dado disponível.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($preview as $row): ?>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60">
                            <?php foreach ($row as $value): ?>
                                <td class="px-3 py-2"><?php echo htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <form method="POST" action="/import/process" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfProcess, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="flex items-center gap-3 text-sm text-slate-600 dark:text-slate-300">
            <input id="update_existing" name="update_existing" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            <label for="update_existing">Atualizar registros existentes pelo SKU (caso contrário, linhas com SKU repetido serão ignoradas).</label>
        </div>
        <div class="mt-6 flex flex-wrap gap-3">
            <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500 dark:bg-blue-500 dark:text-slate-900 dark:hover:bg-blue-400">
                Importar dados
            </button>
            <a href="/import" class="rounded border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">
                Cancelar
            </a>
        </div>
    </form>
</div>
