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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
                if (stored === 'dark') {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (err) {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="h-full bg-slate-50 text-slate-800 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="flex min-h-screen flex-col">
        <?php View::partial('partials/navbar'); ?>

        <div class="flex flex-1">
            <?php View::partial('partials/sidebar'); ?>
            <div id="app-sidebar-backdrop" class="fixed inset-0 z-30 hidden bg-slate-900/50 backdrop-blur-sm md:hidden" data-sidebar-backdrop></div>

            <main data-tour="page-content" class="app-main min-w-0 flex-1 overflow-x-hidden px-4 py-6 md:px-8 md:py-8 lg:px-10 lg:py-10">
                <div class="app-content-width">
                    <?php View::partial('partials/flash'); ?>
                    <div class="mt-4 space-y-6 md:space-y-8">
                        <?php echo $content ?? ''; ?>
                    </div>
                </div>
            </main>
        </div>

        <footer class="app-footer">
            Sistema de Gestão de Estoque de Componentes Eletrônicos — Trabalho de Conclusão de Curso
        </footer>
    </div>

    <?php View::partial('partials/toasts'); ?>

    <script src="/assets/js/app.js" defer></script>
    <script src="/assets/js/charts.js" defer></script>
</body>
</html>
