<?php

declare(strict_types=1);

$csrfToken = $csrfToken ?? '';
?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">Exportar dados</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Gere um arquivo CSV com todos os componentes cadastrados para este usuário.
        </p>
    </div>

    <form method="POST" action="/export/csv" class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
        <p class="text-sm text-slate-600 dark:text-slate-300">
            O arquivo conterá campos como nome, SKU, categoria, quantidade, custo unitário e demais atributos. Nenhum dado de outros usuários será incluído.
        </p>
        <button type="submit" class="mt-4 inline-flex items-center rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500 dark:bg-blue-500 dark:text-slate-900 dark:hover:bg-blue-400">
            Exportar CSV
        </button>
    </form>
</div>
