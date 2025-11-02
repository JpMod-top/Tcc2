<?php

declare(strict_types=1);

$user = $user ?? ['name' => '', 'email' => ''];
$csrfUpdate = $csrfUpdate ?? '';
$csrfPassword = $csrfPassword ?? '';
$csrfDelete = $csrfDelete ?? '';
?>
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">Perfil</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Atualize seus dados pessoais e de acesso.</p>
    </div>

    <section class="grid gap-6 md:grid-cols-2">
        <form method="POST" action="/profile/update" class="space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Dados pessoais</h2>
            <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfUpdate, ENT_QUOTES, 'UTF-8'); ?>">
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
                Nome
                <input type="text" name="name" required value="<?php echo htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
            </label>
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
                E-mail
                <input type="email" name="email" required value="<?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
            </label>
            <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500 dark:bg-blue-500 dark:text-slate-900 dark:hover:bg-blue-400">
                Salvar alterações
            </button>
        </form>

        <form method="POST" action="/profile/password" class="space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Alterar senha</h2>
            <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfPassword, ENT_QUOTES, 'UTF-8'); ?>">
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
                Senha atual
                <input type="password" name="current_password" required class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
            </label>
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
                Nova senha
                <input type="password" name="password" required class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
            </label>
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
                Confirmar nova senha
                <input type="password" name="password_confirmation" required class="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
            </label>
            <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500 dark:bg-blue-500 dark:text-slate-900 dark:hover:bg-blue-400">
                Atualizar senha
            </button>
        </form>
    </section>

    <section class="rounded-xl border border-rose-400 bg-rose-50 p-6 shadow-sm dark:border-rose-500/40 dark:bg-rose-500/10">
        <h2 class="text-lg font-semibold text-rose-700 dark:text-rose-300">Encerrar conta</h2>
        <p class="mt-2 text-sm text-rose-600 dark:text-rose-300">
            A conta será marcada como removida e você será desconectado imediatamente. Esta ação pode ser revertida apenas pelo suporte.
        </p>
        <form method="POST" action="/profile/delete" onsubmit="return confirm('Tem certeza que deseja remover sua conta?');" class="mt-4">
            <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfDelete, ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="rounded border border-rose-500 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-100 dark:border-rose-400 dark:text-rose-300 dark:hover:bg-rose-500/10">
                Remover conta
            </button>
        </form>
    </section>
</div>
