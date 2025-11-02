<?php

declare(strict_types=1);

if (!isset($currentPage, $lastPage) || $lastPage <= 1) {
    return;
}

$baseUrl = $baseUrl ?? ($_SERVER['REQUEST_URI'] ?? '/');
$parsed = parse_url($baseUrl);
$path = $parsed['path'] ?? '/';
$params = [];

if (!empty($parsed['query'])) {
    parse_str($parsed['query'], $params);
}

if (isset($query) && is_array($query)) {
    $params = array_merge($params, $query);
}

$buildUrl = static function (int $page) use ($path, $params): string {
    $params['page'] = $page;
    return $path . '?' . http_build_query($params);
};
?>
<nav class="mt-6 flex items-center justify-between" aria-label="Navegacao de paginas">
    <div>
        <p class="text-xs text-slate-500 dark:text-slate-400">
            Pagina <?php echo (int)$currentPage; ?> de <?php echo (int)$lastPage; ?>
        </p>
    </div>
    <div class="flex items-center gap-2">
        <?php if ($currentPage > 1): ?>
            <a href="<?php echo htmlspecialchars($buildUrl($currentPage - 1), ENT_QUOTES, 'UTF-8'); ?>" class="rounded-md border border-slate-200 px-3 py-1 text-sm text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Anterior</a>
        <?php endif; ?>
        <?php if ($currentPage < $lastPage): ?>
            <a href="<?php echo htmlspecialchars($buildUrl($currentPage + 1), ENT_QUOTES, 'UTF-8'); ?>" class="rounded-md border border-slate-200 px-3 py-1 text-sm text-slate-600 transition hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Proxima</a>
        <?php endif; ?>
    </div>
</nav>