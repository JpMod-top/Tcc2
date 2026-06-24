<?php

declare(strict_types=1);

$csrfToken = $csrfToken ?? '';
?>
<div class="space-y-6">
    <header class="app-page-header">
        <h1>Exportar dados</h1>
        <p>Gere um arquivo CSV com todos os componentes cadastrados para este usuário.</p>
    </header>

    <form method="POST" action="/export/csv" data-tour="export-action" class="app-panel">
        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
        <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-300">
            O arquivo conterá campos como nome, SKU, categoria, quantidade, custo unitário e demais atributos. Nenhum dado de outros usuários será incluído.
        </p>
        <button type="submit" class="app-btn-primary mt-4">
            Exportar CSV
        </button>
    </form>
</div>
