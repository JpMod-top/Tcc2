<?php

declare(strict_types=1);

$csrfToken = $csrfToken ?? '';
$hasPending = $hasPending ?? false;
?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">Importar componentes</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Envie um arquivo CSV para cadastrar ou atualizar componentes rapidamente. O arquivo deve conter pelo menos as colunas <strong>nome</strong> e <strong>sku</strong>.
        </p>
    </div>

    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Estrutura recomendada</h2>
        <ul class="mt-3 list-disc space-y-1 pl-5 text-sm text-slate-600 dark:text-slate-300">
            <li><code>nome</code> (obrigatório)</li>
            <li><code>sku</code> (obrigatório e único por usuário)</li>
            <li><code>fabricante, cod_fabricante, descricao, categoria, tags</code></li>
            <li><code>quantidade, unidade, localizacao, tolerancia, potencia, tensao_max, footprint</code></li>
            <li><code>custo_unitario, preco_medio, min_estoque</code></li>
        </ul>
    </section>

    <form method="POST" action="/import/preview" enctype="multipart/form-data" class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center text-sm text-slate-600 transition hover:border-blue-400 dark:border-slate-600 dark:bg-slate-800/60 dark:text-slate-300">
        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
        <p>Selecione um arquivo CSV (.csv). Máximo de 500 linhas será considerado por importação.</p>
        <label class="mt-4 inline-flex cursor-pointer items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500 dark:bg-blue-500 dark:text-slate-900 dark:hover:bg-blue-400">
            <input type="file" name="file" accept=".csv,text/csv" class="hidden" onchange="this.form.submit()">
            Escolher arquivo
        </label>
    </form>

    <?php if ($hasPending): ?>
        <div class="rounded-lg border border-amber-400 bg-amber-50 px-4 py-3 text-sm text-amber-700 dark:border-amber-500 dark:bg-amber-500/10 dark:text-amber-200">
            Há uma pré-visualização pendente. Conclua o processo ou envie um novo arquivo para sobrescrever.
        </div>
    <?php endif; ?>
</div>
