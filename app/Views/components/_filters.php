<?php

declare(strict_types=1);

$filters = $filters ?? [];
$categories = $categories ?? [];
?>
<form method="GET" action="/components" class="space-y-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
            Busca
            <input
                type="search"
                name="q"
                value="<?php echo htmlspecialchars((string)($filters['q'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                placeholder="Nome, SKU, fabricante..."
                class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
            >
        </label>
        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
            Categoria
            <select name="category" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                <option value="">Todas</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo htmlspecialchars((string)$category, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($filters['category'] ?? '') === $category ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars((string)$category, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
            Ordenar por
            <select name="sort" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                <?php
                $sortOptions = [
                    'nome' => 'Nome',
                    'sku' => 'SKU',
                    'categoria' => 'Categoria',
                    'quantidade' => 'Quantidade',
                    'updated_at' => 'Atualizado em',
                ];
                foreach ($sortOptions as $value => $label): ?>
                    <option value="<?php echo $value; ?>" <?php echo ($filters['sort'] ?? 'nome') === $value ? 'selected' : ''; ?>>
                        <?php echo $label; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
            Direção
            <select name="direction" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                <option value="asc" <?php echo ($filters['direction'] ?? 'asc') === 'asc' ? 'selected' : ''; ?>>Crescente</option>
                <option value="desc" <?php echo ($filters['direction'] ?? 'asc') === 'desc' ? 'selected' : ''; ?>>Decrescente</option>
            </select>
        </label>
        <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
            Por página
            <select name="per_page" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100">
                <?php foreach ([10, 15, 25, 50] as $qty): ?>
                    <option value="<?php echo $qty; ?>" <?php echo (int)($filters['per_page'] ?? 15) === $qty ? 'selected' : ''; ?>>
                        <?php echo $qty; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
    </div>
    <div class="flex flex-wrap items-center gap-4 text-sm text-slate-600 dark:text-slate-300">
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="zeroed" value="1" <?php echo !empty($filters['zeroed']) ? 'checked' : ''; ?> class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            Estoque zerado
        </label>
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="below_min" value="1" <?php echo !empty($filters['below_min']) ? 'checked' : ''; ?> class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
            Abaixo do mínimo
        </label>
    </div>
    <div class="flex flex-wrap gap-3">
        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500 dark:bg-blue-500 dark:text-slate-900 dark:hover:bg-blue-400">Aplicar filtros</button>
        <a href="/components" class="rounded-lg border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Limpar</a>
    </div>
</form>
