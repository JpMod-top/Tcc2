<?php

declare(strict_types=1);

$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$currentPath = rtrim($currentPath, '/') ?: '/';

$isActive = static function (string $path) use ($currentPath): bool {
    if ($path === '/dashboard') {
        return $currentPath === '/' || $currentPath === '/dashboard';
    }

    return str_starts_with($currentPath, $path);
};

$navClass = static function (string $path) use ($isActive): string {
    $base = 'app-nav-link';

    return $isActive($path) ? $base . ' app-nav-link--active' : $base;
};
?>
<aside
    id="app-sidebar"
    data-tour="main-nav"
    class="app-sidebar fixed inset-y-0 left-0 z-50 w-64 -translate-x-full transform px-4 py-6 text-sm shadow-xl transition duration-200 ease-out md:static md:z-0 md:translate-x-0 md:shadow-none"
    aria-label="Menu lateral"
>
    <div class="mb-6 flex items-center justify-between md:hidden">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Menu</p>
        <button
            type="button"
            data-sidebar-close
            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-500 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-900 md:hidden"
            aria-label="Fechar menu"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18 18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <div class="mb-6 hidden md:block">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Navegação</p>
    </div>
    <nav class="space-y-1">
        <a href="/dashboard" data-tour="nav-dashboard" class="<?php echo $navClass('/dashboard'); ?>">
            <span class="app-nav-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0 7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            </span>
            Dashboard
        </a>
        <a href="/components" data-tour="nav-components" class="<?php echo $navClass('/components'); ?>">
            <span class="app-nav-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </span>
            Componentes
        </a>
        <a href="/reports" data-tour="nav-reports" class="<?php echo $navClass('/reports'); ?>">
            <span class="app-nav-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </span>
            Relatorios
        </a>
        <a href="/import" data-tour="nav-import" class="<?php echo $navClass('/import'); ?>">
            <span class="app-nav-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
            </span>
            Importar CSV
        </a>
        <a href="/export" data-tour="nav-export" class="<?php echo $navClass('/export'); ?>">
            <span class="app-nav-icon" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            </span>
            Exportar CSV
        </a>
    </nav>
</aside>
