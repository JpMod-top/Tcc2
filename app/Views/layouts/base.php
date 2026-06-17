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
    <link rel="icon" href="/favicon.ico">
    <script>
        (function () {
            function fallbackUuid() {
                return '10000000-1000-4000-8000-100000000000'.replace(/[018]/g, function (c) {
                    return (Number(c) ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> Number(c) / 4).toString(16);
                });
            }

            function readCookie(name) {
                return document.cookie.split('; ').reduce(function (carry, part) {
                    var pieces = part.split('=');
                    return pieces[0] === name ? decodeURIComponent(pieces.slice(1).join('=')) : carry;
                }, '');
            }

            window.getOrCreateAnonymousUserId = function () {
                var key = 'anonymousUserId';
                var id = localStorage.getItem(key);
                if (!id) {
                    id = crypto.randomUUID ? crypto.randomUUID() : fallbackUuid();
                    localStorage.setItem(key, id);
                }
                document.cookie = key + '=' + encodeURIComponent(id) + '; Max-Age=31536000; Path=/; SameSite=Lax';
                return id;
            };

            var anonymousUserId = window.getOrCreateAnonymousUserId();
            if (readCookie('anonymousUserId') !== anonymousUserId) {
                document.documentElement.style.visibility = 'hidden';
                window.location.reload();
            }
        })();
    </script>
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
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="h-full bg-slate-100 text-slate-900 antialiased dark:bg-slate-900 dark:text-slate-100">
    <div class="min-h-screen flex flex-col">
        <?php View::partial('partials/navbar'); ?>

        <div class="flex flex-1">
            <?php View::partial('partials/sidebar'); ?>
            <div id="app-sidebar-backdrop" class="fixed inset-0 z-30 hidden bg-slate-900/60 backdrop-blur-sm md:hidden" data-sidebar-backdrop></div>

            <main data-tour="page-content" class="flex-1 overflow-x-auto px-4 py-6 md:px-10 md:py-10">
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
