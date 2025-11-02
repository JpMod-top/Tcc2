<?php

declare(strict_types=1);

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;

$user = Auth::user();
?>
<header class="border-b border-slate-200 bg-white/80 backdrop-blur dark:border-slate-700 dark:bg-slate-900/80">
    <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 py-3 md:flex-row md:items-center md:justify-between md:px-8 md:py-4">
        <div class="flex items-center justify-between md:justify-start md:gap-3">
            <button
                type="button"
                data-sidebar-toggle
                class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-slate-200 text-slate-600 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-400/60 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800 md:hidden"
                aria-label="Abrir menu"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
            <a href="/dashboard" class="text-lg font-semibold text-blue-600 dark:text-blue-400">Meu Estoque</a>
            <form method="GET" action="/components" class="hidden items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600 shadow-sm focus-within:border-blue-400 focus-within:bg-white focus-within:text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 md:flex">
                <input name="q" type="search" placeholder="Buscar componentes..." class="w-48 bg-transparent outline-none" aria-label="Buscar componentes">
                <button type="submit" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Buscar</button>
            </form>
        </div>
        <div class="flex items-center gap-4 md:justify-end">
            <?php View::partial('partials.darkmode_toggle'); ?>
            <?php if ($user): ?>
                <div class="hidden flex-col text-right text-xs text-slate-500 dark:text-slate-400 sm:flex">
                    <span class="font-semibold text-slate-700 dark:text-slate-100"><?php echo htmlspecialchars($user['name'] ?? 'Usuario', ENT_QUOTES, 'UTF-8'); ?></span>
                    <span><?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <form method="POST" action="/logout" class="flex">
                    <input type="hidden" name="_token" value="<?php echo htmlspecialchars(Csrf::token('auth_logout'), ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-slate-300">Sair</button>
                </form>
            <?php else: ?>
                <a href="/login" class="rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500 dark:bg-blue-500 dark:hover:bg-blue-400">Entrar</a>
            <?php endif; ?>
        </div>
        <form method="GET" action="/components" class="flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600 shadow-sm focus-within:border-blue-400 focus-within:bg-white focus-within:text-slate-800 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 md:hidden">
            <input name="q" type="search" placeholder="Buscar componentes..." class="flex-1 bg-transparent outline-none" aria-label="Buscar componentes">
            <button type="submit" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">Buscar</button>
        </form>
    </div>
</header>
