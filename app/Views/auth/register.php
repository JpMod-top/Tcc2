<?php

declare(strict_types=1);

$old = $old ?? [];
?>
<div class="space-y-6">
    <div class="text-center">
        <h1 class="text-xl font-semibold text-slate-800 dark:text-slate-100">Criar conta</h1>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Cadastre-se para começar a organizar o estoque.</p>
    </div>
    <form method="POST" action="/register" class="space-y-4">
        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="space-y-1">
            <label for="name" class="text-sm font-medium text-slate-700 dark:text-slate-200">Nome completo</label>
            <input id="name" name="name" type="text" required autocomplete="name" value="<?php echo htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" placeholder="Seu nome">
        </div>
        <div class="space-y-1">
            <label for="email" class="text-sm font-medium text-slate-700 dark:text-slate-200">Email</label>
            <input id="email" name="email" type="email" required autocomplete="email" value="<?php echo htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" placeholder="voce@exemplo.com">
        </div>
        <div class="space-y-1">
            <label for="password" class="text-sm font-medium text-slate-700 dark:text-slate-200">Senha</label>
            <input id="password" name="password" type="password" required autocomplete="new-password" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" placeholder="Mínimo 8 caracteres">
        </div>
        <div class="space-y-1">
            <label for="password_confirmation" class="text-sm font-medium text-slate-700 dark:text-slate-200">Confirmar senha</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100" placeholder="Repita a senha">
        </div>
        <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500">Registrar</button>
    </form>
    <p class="text-center text-sm text-slate-500 dark:text-slate-400">
        Já tem conta? <a href="/login" class="text-blue-600 hover:text-blue-500 dark:text-blue-400">Entrar</a>
    </p>
</div>
