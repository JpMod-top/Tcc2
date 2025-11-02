<?php

declare(strict_types=1);

use App\Core\Auth;

$user = Auth::user();
?>
<aside
    id="app-sidebar"
    class="fixed inset-y-0 left-0 z-40 w-64 -translate-x-full transform border-r border-slate-200 bg-white/90 px-4 py-6 text-sm shadow-xl transition duration-200 ease-out dark:border-slate-800 dark:bg-slate-900/90 md:static md:z-0 md:translate-x-0 md:shadow-none"
    aria-label="Menu lateral"
>
    <div class="mb-6 flex items-center justify-between md:hidden">
        <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Menu</p>
        <button
            type="button"
            data-sidebar-close
            class="inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 text-slate-500 transition hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800 md:hidden"
            aria-label="Fechar menu"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18 18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>
    <div class="mb-6 hidden md:block">
        <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Menu</p>
    </div>
    <nav class="space-y-1">
        <a href="/dashboard" class="flex items-center rounded-lg px-3 py-2 text-slate-600 transition hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-400">
            <span class="mr-2 inline-flex h-2 w-2 rounded-full bg-blue-500"></span>Dashboard
        </a>
        <a href="/components" class="flex items-center rounded-lg px-3 py-2 text-slate-600 transition hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-400">
            <span class="mr-2 inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>Componentes
        </a>
        <a href="/reports" class="flex items-center rounded-lg px-3 py-2 text-slate-600 transition hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-400">
            <span class="mr-2 inline-flex h-2 w-2 rounded-full bg-purple-500"></span>Relatorios
        </a>
        <a href="/import" class="flex items-center rounded-lg px-3 py-2 text-slate-600 transition hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-400">
            <span class="mr-2 inline-flex h-2 w-2 rounded-full bg-amber-500"></span>Importar CSV
        </a>
        <a href="/export" class="flex items-center rounded-lg px-3 py-2 text-slate-600 transition hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-400">
            <span class="mr-2 inline-flex h-2 w-2 rounded-full bg-rose-500"></span>Exportar CSV
        </a>
        <a href="/profile" class="flex items-center rounded-lg px-3 py-2 text-slate-600 transition hover:bg-blue-50 hover:text-blue-600 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-blue-400">
            <span class="mr-2 inline-flex h-2 w-2 rounded-full bg-slate-500"></span>Perfil
        </a>
    </nav>
    <?php if ($user): ?>
        <div class="mt-10 rounded-lg bg-slate-100 px-3 py-4 text-xs text-slate-600 dark:bg-slate-800 dark:text-slate-300">
            <p class="font-semibold text-slate-800 dark:text-slate-100">Locatario atual</p>
            <p class="mt-1 break-all"><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="mt-2 text-[11px] uppercase tracking-wide text-slate-400 dark:text-slate-500">Isolamento por user_id ativo</p>
        </div>
    <?php endif; ?>
</aside>
