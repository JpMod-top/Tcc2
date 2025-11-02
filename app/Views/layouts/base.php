<?php

declare(strict_types=1);

use App\Core\View;

$title = $title ?? 'Meu Estoque Eletronicos';
$appUrl = $_ENV['APP_URL'] ?? 'http://localhost:8000';
?>
<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($title . ' | Meu Estoque', ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="icon" href="<?php echo htmlspecialchars(rtrim($appUrl, '/') . '/favicon.ico', ENT_QUOTES, 'UTF-8'); ?>">
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
    <div class="min-h-screen flex flex-col">
        <?php View::partial('partials/navbar'); ?>

        <div class="flex flex-1">
            <?php View::partial('partials/sidebar'); ?>
            <div id="app-sidebar-backdrop" class="fixed inset-0 z-30 hidden bg-slate-900/60 backdrop-blur-sm md:hidden" data-sidebar-backdrop></div>

            <main class="flex-1 overflow-x-auto px-4 py-6 md:px-10 md:py-10">
                <?php View::partial('partials/flash'); ?>
                <div class="mt-4 space-y-6">
                    <?php echo $content ?? ''; ?>
                </div>
            </main>
        </div>
    </div>

    <?php View::partial('partials/toasts'); ?>

    <script src="/assets/js/app.js" defer></script>
    <script src="/assets/js/charts.js" defer></script>
</body>
</html>
