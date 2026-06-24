<?php

declare(strict_types=1);

$csrfToken = $csrfToken ?? '';
$csrfCancel = $csrfCancel ?? '';
$hasPending = $hasPending ?? false;
?>
<div class="space-y-6">
    <header class="app-page-header">
        <h1>Importar componentes</h1>
        <p>
            Envie um arquivo CSV para cadastrar ou atualizar componentes rapidamente. O arquivo deve conter pelo menos as colunas <strong>nome</strong> e <strong>sku</strong>.
        </p>
    </header>

    <section class="app-panel">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Estrutura recomendada</h2>
        <ul class="mt-3 list-disc space-y-1 pl-5 text-sm text-slate-600 dark:text-slate-300">
            <li><code>nome</code> (obrigatorio)</li>
            <li><code>sku</code> (obrigatorio e unico por usuario)</li>
            <li><code>fabricante, cod_fabricante, descricao, categoria, tags</code></li>
            <li><code>quantidade, unidade, localizacao, tolerancia, potencia, tensao_max, footprint</code></li>
            <li><code>custo_unitario, preco_medio, min_estoque</code></li>
        </ul>
    </section>

    <form method="POST" action="/import/preview" enctype="multipart/form-data" data-tour="import-upload" class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-600 transition hover:border-blue-400 dark:border-slate-600 dark:bg-slate-800/60 dark:text-slate-300">
        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
        <p>Selecione um arquivo CSV (.csv). Maximo de 500 linhas sera considerado por importacao.</p>
        <label class="mt-4 inline-flex cursor-pointer items-center app-btn-primary">
            <input type="file" name="file" accept=".csv,text/csv" class="hidden" onchange="this.form.submit()">
            Escolher arquivo
        </label>
    </form>

    <?php if ($hasPending): ?>
        <div class="rounded-lg border border-amber-400 bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:border-amber-500 dark:bg-amber-500/10 dark:text-amber-200">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p>Ha uma pre-visualizacao pendente. Conclua o processo, envie um novo arquivo para sobrescrever ou cancele a previa atual.</p>
                <form method="POST" action="/import/cancel">
                    <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfCancel, ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="rounded border border-amber-500 px-3 py-1.5 text-xs font-semibold text-amber-700 transition hover:bg-amber-100 dark:border-amber-500/60 dark:text-amber-200 dark:hover:bg-amber-900/60">
                        Cancelar previa
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
