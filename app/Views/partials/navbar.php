<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\View;

$user = Auth::user();
?>
<header data-tour="topbar" class="app-topbar md:ml-64">
    <div class="mx-auto flex w-full max-w-7xl flex-col gap-3 px-4 py-3 md:flex-row md:items-center md:justify-between md:px-8 md:py-3.5 lg:px-10">
        <div class="flex items-center justify-between gap-3 md:justify-start">
            <button
                type="button"
                data-sidebar-toggle
                data-tour="sidebar-toggle"
                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 text-slate-600 transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-brand-400/40 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-900 md:hidden"
                aria-label="Abrir menu"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <a href="/dashboard" data-tour="brand-dashboard" class="app-brand">
                <span class="app-brand-title">Meu Estoque</span>
                <span class="app-brand-subtitle">Gestão de componentes eletrônicos</span>
            </a>
            <form method="GET" action="/components" data-tour="global-search" class="hidden items-center gap-2 rounded-xl border border-slate-200 bg-slate-50/80 px-3 py-2 text-sm text-slate-600 shadow-sm focus-within:border-brand-300 focus-within:bg-white focus-within:text-slate-800 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 md:flex">
                <input name="q" type="search" placeholder="Buscar componentes..." class="w-48 bg-transparent outline-none" aria-label="Buscar componentes">
                <button type="submit" class="font-medium text-brand-600 hover:text-brand-500 dark:text-brand-300 dark:hover:text-brand-200">Buscar</button>
            </form>
        </div>
        <div class="flex flex-wrap items-center gap-3 md:justify-end">
            <?php View::partial('partials/darkmode_toggle'); ?>
            <button type="button" data-onboarding-reopen class="app-btn-secondary px-3 py-1.5 text-xs">
                Tour
            </button>
            <div class="hidden flex-col items-end text-right sm:flex">
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400">Sessão</span>
                <span class="text-sm font-semibold text-slate-800 dark:text-slate-100"><?php echo htmlspecialchars($user['name'] ?? 'Estoque anonimo', ENT_QUOTES, 'UTF-8'); ?></span>
                <span
                    class="app-notice mt-2"
                    title="Os dados ficam salvos no banco, mas este estoque e vinculado ao identificador deste navegador. Se voce limpar cookies/localStorage, trocar de navegador ou usar outro dispositivo, pode perder o acesso a este estoque."
                >
                    <span class="font-semibold">Aviso:</span>
                    <span>dados vinculados a este navegador.</span>
                </span>
            </div>
        </div>
        <form method="GET" action="/components" data-tour="global-search" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50/80 px-3 py-2 text-sm text-slate-600 shadow-sm focus-within:border-brand-300 focus-within:bg-white focus-within:text-slate-800 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 md:hidden">
            <input name="q" type="search" placeholder="Buscar componentes..." class="flex-1 bg-transparent outline-none" aria-label="Buscar componentes">
            <button type="submit" class="font-medium text-brand-600 hover:text-brand-500 dark:text-brand-300 dark:hover:text-brand-200">Buscar</button>
        </form>
    </div>
</header>
