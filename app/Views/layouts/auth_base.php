<?php

declare(strict_types=1);

use App\Core\View;

$title = $title ?? 'Meu Estoque Eletronicos';
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($title . ' | Meu Estoque', ENT_QUOTES, 'UTF-8'); ?></title>
    <script>
        (function () {
            var storageKey = 'meu-estoque-theme';
            try {
                var stored = localStorage.getItem(storageKey);
                if (stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (err) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full bg-slate-100 text-slate-900 antialiased dark:bg-slate-900 dark:text-slate-100">
    <div class="flex min-h-screen flex-col items-center justify-center px-4">
        <div class="mb-6 text-center">
            <a href="/" class="text-2xl font-semibold text-blue-600 dark:text-blue-400">Meu Estoque</a>
        </div>
        <div class="w-full max-w-md rounded-xl bg-white p-6 shadow-lg dark:bg-slate-800">
            <?php View::partial('partials/flash'); ?>
            <?php echo $content ?? ''; ?>
        </div>
    </div>

    <?php View::partial('partials.toasts'); ?>

    <script>
        (function () {
            var toggle = document.getElementById('darkmode-toggle');
            if (!toggle) {
                return;
            }
            var root = document.documentElement;
            var storageKey = 'meu-estoque-theme';
            toggle.addEventListener('click', function () {
                var isDark = root.classList.toggle('dark');
                try {
                    localStorage.setItem(storageKey, isDark ? 'dark' : 'light');
                } catch (err) {
                }
            });
        })();
    </script>
</body>
</html>