<?php

declare(strict_types=1);

$component = $component ?? [
    'nome' => '',
    'sku' => '',
    'fabricante' => '',
    'cod_fabricante' => '',
    'descricao' => '',
    'categoria' => '',
    'tags' => '',
    'quantidade' => 0,
    'unidade' => 'un',
    'localizacao' => '',
    'tolerancia' => '',
    'potencia' => '',
    'tensao_max' => '',
    'footprint' => '',
    'custo_unitario' => 0,
    'preco_medio' => '',
    'min_estoque' => 0,
];

$errors = $errors ?? [];
$action = $action ?? '/components/store';
$csrfToken = $csrfToken ?? '';
$isEdit = $isEdit ?? false;
?>
<?php if (!empty($errors)): ?>
    <div class="rounded-lg border border-rose-400 bg-rose-50 px-4 py-3 text-sm text-rose-700 dark:border-rose-500 dark:bg-rose-500/10 dark:text-rose-200">
        <ul class="list-disc pl-4">
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="<?php echo htmlspecialchars($action, ENT_QUOTES, 'UTF-8'); ?>" class="space-y-6">
    <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?php echo (int)($component['id'] ?? 0); ?>">
    <?php endif; ?>

    <div class="grid gap-4 md:grid-cols-2">
        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
            Nome *
            <input type="text" name="nome" required value="<?php echo htmlspecialchars((string)$component['nome'], ENT_QUOTES, 'UTF-8'); ?>" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
        </label>
        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
            SKU *
            <input type="text" name="sku" required value="<?php echo htmlspecialchars((string)$component['sku'], ENT_QUOTES, 'UTF-8'); ?>" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
        </label>
        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
            Fabricante
            <input type="text" name="fabricante" value="<?php echo htmlspecialchars((string)($component['fabricante'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
        </label>
        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
            Código do fabricante
            <input type="text" name="cod_fabricante" value="<?php echo htmlspecialchars((string)($component['cod_fabricante'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
        </label>
    </div>

    <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
        Descrição
        <textarea name="descricao" rows="3" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"><?php echo htmlspecialchars((string)($component['descricao'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></textarea>
    </label>

    <div class="grid gap-4 md:grid-cols-3">
        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
            Categoria
            <input type="text" name="categoria" value="<?php echo htmlspecialchars((string)($component['categoria'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
        </label>
        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
            Tags
            <input type="text" name="tags" value="<?php echo htmlspecialchars((string)($component['tags'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" placeholder="Separadas por vírgula" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
        </label>
        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
            Localização
            <input type="text" name="localizacao" value="<?php echo htmlspecialchars((string)($component['localizacao'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
        </label>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
            Quantidade
            <input type="number" min="0" name="quantidade" value="<?php echo (int)($component['quantidade'] ?? 0); ?>" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
        </label>
        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
            Unidade
            <input type="text" name="unidade" value="<?php echo htmlspecialchars((string)($component['unidade'] ?? 'un'), ENT_QUOTES, 'UTF-8'); ?>" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
        </label>
        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
            Estoque mínimo
            <input type="number" min="0" name="min_estoque" value="<?php echo (int)($component['min_estoque'] ?? 0); ?>" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
        </label>
        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
            Tolerância
            <input type="text" name="tolerancia" value="<?php echo htmlspecialchars((string)($component['tolerancia'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
        </label>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
            Potência
            <input type="text" name="potencia" value="<?php echo htmlspecialchars((string)($component['potencia'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
        </label>
        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
            Tensão máxima
            <input type="text" name="tensao_max" value="<?php echo htmlspecialchars((string)($component['tensao_max'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
        </label>
        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
            Footprint
            <input type="text" name="footprint" value="<?php echo htmlspecialchars((string)($component['footprint'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
        </label>
        <div class="grid grid-cols-2 gap-4">
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
                Custo unitário
                <input type="number" step="0.01" min="0" name="custo_unitario" value="<?php echo htmlspecialchars((string)($component['custo_unitario'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
            </label>
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
                Preço médio
                <input type="number" step="0.01" min="0" name="preco_medio" value="<?php echo htmlspecialchars((string)($component['preco_medio'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>" class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
            </label>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500 dark:bg-blue-500 dark:text-slate-900 dark:hover:bg-blue-400">
            Salvar
        </button>
        <a href="/components" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">
            Cancelar
        </a>
    </div>
</form>
