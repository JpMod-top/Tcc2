(function () {
    'use strict';

    var storageKey = 'meu-estoque-theme';

    function fallbackUuid() {
        return '10000000-1000-4000-8000-100000000000'.replace(/[018]/g, function (c) {
            return (Number(c) ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> Number(c) / 4).toString(16);
        });
    }

    window.getOrCreateAnonymousUserId = window.getOrCreateAnonymousUserId || function () {
        var key = 'anonymousUserId';
        var id = localStorage.getItem(key);
        if (!id) {
            id = crypto.randomUUID ? crypto.randomUUID() : fallbackUuid();
            localStorage.setItem(key, id);
        }
        document.cookie = key + '=' + encodeURIComponent(id) + '; Max-Age=31536000; Path=/; SameSite=Lax';
        return id;
    };

    function toggleDarkMode(isDark) {
        var root = document.documentElement;
        if (isDark) {
            root.classList.add('dark');
        } else {
            root.classList.remove('dark');
        }
    }

    function initDarkMode() {
        var toggle = document.getElementById('darkmode-toggle');
        if (!toggle) {
            return;
        }

        var root = document.documentElement;

        function updateState() {
            var isDark = root.classList.contains('dark');
            toggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
            var lightLabel = toggle.querySelector('[data-label-light]');
            var darkLabel = toggle.querySelector('[data-label-dark]');
            if (lightLabel) {
                lightLabel.classList.toggle('hidden', isDark);
            }
            if (darkLabel) {
                darkLabel.classList.toggle('hidden', !isDark);
            }
        }

        toggle.addEventListener('click', function () {
            var nextState = !root.classList.contains('dark');
            toggleDarkMode(nextState);
            try {
                localStorage.setItem(storageKey, nextState ? 'dark' : 'light');
            } catch (err) {
                /* noop */
            }
            updateState();
        });

        updateState();
    }

    function ensureToastStack() {
        var stack = document.getElementById('toast-stack');
        if (stack) {
            return stack;
        }
        stack = document.createElement('div');
        stack.id = 'toast-stack';
        stack.className = 'fixed bottom-6 right-6 z-50 flex w-72 flex-col';
        document.body.appendChild(stack);
        return stack;
    }

    window.showToast = function (message, type) {
        var stack = ensureToastStack();
        var el = document.createElement('div');

        var tone = {
            info: 'bg-blue-600',
            success: 'bg-green-600',
            warning: 'bg-yellow-500',
            error: 'bg-red-600'
        }[type] || 'bg-blue-600';

        el.className = tone + ' toast-item mb-2 flex items-center justify-between gap-4 rounded-md px-4 py-3 text-sm font-medium text-white shadow transition duration-300 ease-out translate-x-full opacity-0';
        el.innerHTML = '<span>' + (message || '') + '</span>';
        stack.appendChild(el);

        requestAnimationFrame(function () {
            el.classList.remove('translate-x-full', 'opacity-0');
            el.classList.add('translate-x-0', 'opacity-100');
        });

        setTimeout(function () {
            el.classList.add('opacity-0');
            setTimeout(function () {
                if (el.parentNode) {
                    el.parentNode.removeChild(el);
                }
            }, 250);
        }, 4000);
    };

    function initAutoSubmitSelects() {
        var selects = document.querySelectorAll('[data-auto-submit]');
        selects.forEach(function (select) {
            select.addEventListener('change', function () {
                var form = select.closest('form');
                if (form) {
                    form.submit();
                }
            });
        });
    }

    function initSidebarToggle() {
        var sidebar = document.getElementById('app-sidebar');
        var backdrop = document.querySelector('[data-sidebar-backdrop]');
        if (!sidebar || !backdrop) {
            return;
        }

        var openers = document.querySelectorAll('[data-sidebar-toggle]');
        var closers = document.querySelectorAll('[data-sidebar-close]');
        var mediaQuery = window.matchMedia('(min-width: 768px)');
        var resizeTimer = null;

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
            document.documentElement.classList.add('overflow-hidden');
            sidebar.setAttribute('aria-hidden', 'false');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
            document.documentElement.classList.remove('overflow-hidden');
            sidebar.setAttribute('aria-hidden', 'true');
        }

        openers.forEach(function (button) {
            button.addEventListener('click', function () {
                if (mediaQuery.matches) {
                    return;
                }
                openSidebar();
            });
        });

        closers.forEach(function (button) {
            button.addEventListener('click', closeSidebar);
        });

        backdrop.addEventListener('click', closeSidebar);

        function syncSidebarState() {
            if (mediaQuery.matches) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.add('hidden');
                document.documentElement.classList.remove('overflow-hidden');
                sidebar.setAttribute('aria-hidden', 'false');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
                document.documentElement.classList.remove('overflow-hidden');
                sidebar.setAttribute('aria-hidden', 'true');
            }
        }

        function scheduleSidebarSync() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(syncSidebarState, 80);
        }

        if (mediaQuery.addEventListener) {
            mediaQuery.addEventListener('change', syncSidebarState);
        } else if (mediaQuery.addListener) {
            mediaQuery.addListener(syncSidebarState);
        }

        window.addEventListener('resize', scheduleSidebarSync);
        window.addEventListener('orientationchange', scheduleSidebarSync);

        if (window.visualViewport) {
            window.visualViewport.addEventListener('resize', scheduleSidebarSync);
        }

        if (document.readyState === 'complete') {
            syncSidebarState();
        } else {
            window.addEventListener('load', syncSidebarState);
        }

        syncSidebarState();
    }

    function initFloatingActions() {
        var upButton = document.querySelector('[data-up-button]');
        var topButton = document.querySelector('[data-scroll-top]');

        function normalizedPath() {
            var path = window.location.pathname || '/';
            path = path.replace(/\/+$/, '');
            return path || '/';
        }

        function parentPath(path) {
            var parts;

            if (path === '/' || path === '/dashboard') {
                return '';
            }

            if (path === '/components' || path === '/reports' || path === '/import' || path === '/export') {
                return '/dashboard';
            }

            if (path.indexOf('/components/') === 0 || path === '/components/view' || path === '/components/edit') {
                return '/components';
            }

            if (path.indexOf('/reports/') === 0) {
                return '/reports';
            }

            if (path.indexOf('/import/') === 0) {
                return '/import';
            }

            parts = path.split('/').filter(Boolean);
            if (parts.length <= 1) {
                return '/dashboard';
            }

            return '/' + parts.slice(0, -1).join('/');
        }

        function updateScrollTopButton() {
            if (!topButton) {
                return;
            }

            topButton.classList.toggle('app-floating-action--hidden', window.scrollY < 320);
        }

        if (upButton) {
            var target = parentPath(normalizedPath());
            if (target) {
                upButton.setAttribute('href', target);
                upButton.classList.remove('hidden');
            } else {
                upButton.classList.add('hidden');
            }
        }

        if (topButton) {
            topButton.addEventListener('click', function () {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            window.addEventListener('scroll', updateScrollTopButton, { passive: true });
            updateScrollTopButton();
        }
    }

    function initOnboardingTour() {
        var storageKey = 'meu-estoque-onboarding-v1';
        var activeTour = null;

        var stepDefinitions = [
            {
                title: 'Bem-vindo ao Meu Estoque',
                body: 'Este tour mostra as areas principais para voce se localizar rapido.'
            },
            {
                selector: '[data-tour="brand-dashboard"]',
                title: 'Inicio',
                body: 'Clique no nome do sistema quando quiser voltar ao dashboard.'
            },
            {
                selector: '[data-tour="page-content"]',
                title: 'Area principal',
                body: 'Aqui aparecem os graficos, listas, formularios e relatorios da pagina atual.'
            },
            {
                selector: '[data-tour="dashboard-summary"]',
                title: 'Resumo do estoque',
                body: 'No dashboard, estes cards mostram total, itens criticos, zerados e valor estimado.'
            },
            {
                selector: '[data-tour="dashboard-details"]',
                title: 'Indicadores recentes',
                body: 'Acompanhe valor por categoria e os ultimos componentes cadastrados.'
            },
            {
                selector: '[data-tour="global-search"]',
                title: 'Busca rapida',
                body: 'Procure componentes por nome, SKU ou fabricante direto pelo topo.'
            },
            {
                selector: '[data-tour="main-nav"], [data-tour="sidebar-toggle"]',
                title: 'Menu lateral',
                body: 'Use o menu para navegar entre dashboard, componentes, relatorios, importacao e exportacao.'
            },
            {
                selector: '[data-tour="nav-components"]',
                title: 'Componentes',
                body: 'Aqui voce cadastra, edita, filtra e movimenta itens do estoque.'
            },
            {
                selector: '[data-tour="components-new"]',
                title: 'Novo componente',
                body: 'Use este botao para cadastrar um item manualmente.'
            },
            {
                selector: '[data-tour="components-filters"]',
                title: 'Filtros',
                body: 'Filtre por busca, categoria, ordenacao e alertas de estoque.'
            },
            {
                selector: '[data-tour="components-table"]',
                title: 'Tabela de componentes',
                body: 'Na tabela voce revisa itens, ajusta campos rapidos e registra movimentacoes.'
            },
            {
                selector: '[data-tour="nav-reports"]',
                title: 'Relatorios',
                body: 'Veja itens abaixo do minimo, zerados, valor por categoria e movimentacoes.'
            },
            {
                selector: '[data-tour="reports-summary"]',
                title: 'Metricas',
                body: 'Os cards resumem os principais numeros para apoiar a reposicao.'
            },
            {
                selector: '[data-tour="reports-options"]',
                title: 'Opcoes de relatorio',
                body: 'Abra listas prontas ou filtre movimentacoes por periodo.'
            },
            {
                selector: '[data-tour="nav-import"]',
                title: 'Importar CSV',
                body: 'Use a importacao para cadastrar ou atualizar muitos componentes de uma vez.'
            },
            {
                selector: '[data-tour="import-upload"]',
                title: 'Arquivo CSV',
                body: 'Envie um CSV com nome e SKU para iniciar a pre-visualizacao.'
            },
            {
                selector: '[data-tour="nav-export"]',
                title: 'Exportar CSV',
                body: 'Exporte seus dados para backup ou analise fora do sistema.'
            },
            {
                selector: '[data-tour="export-action"]',
                title: 'Gerar arquivo',
                body: 'Este botao baixa um CSV com os componentes deste navegador.'
            },
            {
                selector: '[data-tour="theme-toggle"]',
                title: 'Tema',
                body: 'Alterne entre modo claro e escuro quando preferir.'
            },
            {
                selector: '[data-onboarding-reopen]',
                title: 'Reabrir o tour',
                body: 'Depois de finalizar ou pular, use este botao para ver o tour novamente.'
            }
        ];

        function readStorage() {
            try {
                return localStorage.getItem(storageKey);
            } catch (err) {
                return null;
            }
        }

        function writeStorage(value) {
            try {
                localStorage.setItem(storageKey, value);
            } catch (err) {
                /* noop */
            }
        }

        function isTargetAvailable(element) {
            var style;
            var rect;

            if (!element) {
                return false;
            }

            style = window.getComputedStyle(element);
            if (style.display === 'none' || style.visibility === 'hidden') {
                return false;
            }

            rect = element.getBoundingClientRect();

            return rect.width > 0
                && rect.height > 0
                && rect.right > 0
                && rect.left < window.innerWidth;
        }

        function findTarget(selector) {
            var nodes;
            var index;

            if (!selector) {
                return null;
            }

            nodes = document.querySelectorAll(selector);
            for (index = 0; index < nodes.length; index += 1) {
                if (isTargetAvailable(nodes[index])) {
                    return nodes[index];
                }
            }

            return null;
        }

        function buildSteps() {
            var steps = [];

            stepDefinitions.forEach(function (step) {
                if (!step.selector || findTarget(step.selector)) {
                    steps.push(step);
                }
            });

            return steps;
        }

        function makeButton(label, className) {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = className;
            button.textContent = label;
            return button;
        }

        function createElements() {
            var overlay = document.createElement('div');
            var highlight = document.createElement('div');
            var popover = document.createElement('section');
            var header = document.createElement('div');
            var eyebrow = document.createElement('p');
            var close = makeButton('x', 'app-tour-close');
            var title = document.createElement('h2');
            var body = document.createElement('p');
            var footer = document.createElement('div');
            var progress = document.createElement('span');
            var actions = document.createElement('div');
            var skip = makeButton('Pular', 'app-tour-btn app-tour-btn-secondary');
            var previous = makeButton('Voltar', 'app-tour-btn app-tour-btn-secondary');
            var next = makeButton('Proximo', 'app-tour-btn app-tour-btn-primary');

            overlay.className = 'app-tour-overlay';
            highlight.className = 'app-tour-highlight';
            popover.className = 'app-tour-popover';
            popover.setAttribute('role', 'dialog');
            popover.setAttribute('aria-modal', 'true');
            popover.setAttribute('aria-labelledby', 'app-tour-title');

            header.className = 'app-tour-header';
            eyebrow.className = 'app-tour-eyebrow';
            title.id = 'app-tour-title';
            title.className = 'app-tour-title';
            body.className = 'app-tour-body';
            footer.className = 'app-tour-footer';
            progress.className = 'app-tour-progress';
            actions.className = 'app-tour-actions';
            close.setAttribute('aria-label', 'Fechar tour');

            header.appendChild(eyebrow);
            header.appendChild(close);
            actions.appendChild(skip);
            actions.appendChild(previous);
            actions.appendChild(next);
            footer.appendChild(progress);
            footer.appendChild(actions);
            popover.appendChild(header);
            popover.appendChild(title);
            popover.appendChild(body);
            popover.appendChild(footer);

            document.body.appendChild(overlay);
            document.body.appendChild(highlight);
            document.body.appendChild(popover);

            return {
                overlay: overlay,
                highlight: highlight,
                popover: popover,
                eyebrow: eyebrow,
                close: close,
                title: title,
                body: body,
                progress: progress,
                skip: skip,
                previous: previous,
                next: next
            };
        }

        function clamp(value, min, max) {
            return Math.max(min, Math.min(value, max));
        }

        function placePopover(elements, target) {
            var rect;
            var highlightRect;
            var popoverRect;
            var top;
            var left;
            var padding = 12;
            var highlightPadding = 8;
            var viewportMargin = 12;
            var minHighlightSize = 24;

            if (!target) {
                elements.highlight.classList.add('hidden');
                elements.popover.classList.add('app-tour-popover-center');
                elements.popover.style.top = '';
                elements.popover.style.left = '';
                return;
            }

            rect = target.getBoundingClientRect();
            elements.highlight.classList.remove('hidden');
            elements.popover.classList.remove('app-tour-popover-center');

            highlightRect = {
                top: clamp(rect.top - highlightPadding, viewportMargin, window.innerHeight - viewportMargin - minHighlightSize),
                left: clamp(rect.left - highlightPadding, viewportMargin, window.innerWidth - viewportMargin - minHighlightSize),
                right: clamp(rect.right + highlightPadding, viewportMargin + minHighlightSize, window.innerWidth - viewportMargin),
                bottom: clamp(rect.bottom + highlightPadding, viewportMargin + minHighlightSize, window.innerHeight - viewportMargin)
            };

            if (highlightRect.right - highlightRect.left < minHighlightSize) {
                highlightRect.right = Math.min(highlightRect.left + minHighlightSize, window.innerWidth - viewportMargin);
            }
            if (highlightRect.bottom - highlightRect.top < minHighlightSize) {
                highlightRect.bottom = Math.min(highlightRect.top + minHighlightSize, window.innerHeight - viewportMargin);
            }

            elements.highlight.style.top = highlightRect.top + 'px';
            elements.highlight.style.left = highlightRect.left + 'px';
            elements.highlight.style.width = (highlightRect.right - highlightRect.left) + 'px';
            elements.highlight.style.height = (highlightRect.bottom - highlightRect.top) + 'px';

            popoverRect = elements.popover.getBoundingClientRect();
            if (window.innerHeight - highlightRect.bottom > popoverRect.height + 24 || highlightRect.top < popoverRect.height + 24) {
                top = highlightRect.bottom + padding;
            } else {
                top = highlightRect.top - popoverRect.height - padding;
            }

            left = highlightRect.left + ((highlightRect.right - highlightRect.left) / 2) - (popoverRect.width / 2);

            elements.popover.style.top = clamp(top, padding, window.innerHeight - popoverRect.height - padding) + 'px';
            elements.popover.style.left = clamp(left, padding, window.innerWidth - popoverRect.width - padding) + 'px';
        }

        function startTour(force) {
            var steps;
            var elements;
            var currentIndex = 0;

            if (activeTour) {
                activeTour.destroy(false);
            }

            if (!force && readStorage() === 'done') {
                return;
            }

            steps = buildSteps();
            if (!steps.length) {
                return;
            }

            elements = createElements();

            function destroy(saveState) {
                window.removeEventListener('resize', refreshPosition);
                window.removeEventListener('scroll', refreshPosition, true);
                document.removeEventListener('keydown', handleKeydown);

                [elements.overlay, elements.highlight, elements.popover].forEach(function (element) {
                    if (element && element.parentNode) {
                        element.parentNode.removeChild(element);
                    }
                });

                if (saveState) {
                    writeStorage('done');
                }

                activeTour = null;
            }

            function currentTarget() {
                return findTarget(steps[currentIndex].selector);
            }

            function refreshPosition() {
                placePopover(elements, currentTarget());
            }

            function showStep(index) {
                var step;
                var target;

                currentIndex = clamp(index, 0, steps.length - 1);
                step = steps[currentIndex];
                target = currentTarget();

                elements.eyebrow.textContent = 'Passo ' + (currentIndex + 1) + ' de ' + steps.length;
                elements.title.textContent = step.title;
                elements.body.textContent = step.body;
                elements.progress.textContent = (currentIndex + 1) + '/' + steps.length;
                elements.previous.disabled = currentIndex === 0;
                elements.next.textContent = currentIndex === steps.length - 1 ? 'Finalizar' : 'Proximo';

                if (target) {
                    target.scrollIntoView({ block: 'center', inline: 'nearest', behavior: 'smooth' });
                }

                window.setTimeout(refreshPosition, 180);
            }

            function move(delta) {
                if (currentIndex + delta < 0) {
                    return;
                }
                if (currentIndex + delta >= steps.length) {
                    destroy(true);
                    return;
                }
                showStep(currentIndex + delta);
            }

            function handleKeydown(event) {
                if (event.key === 'Escape') {
                    destroy(true);
                }
                if (event.key === 'ArrowRight') {
                    move(1);
                }
                if (event.key === 'ArrowLeft') {
                    move(-1);
                }
            }

            elements.close.addEventListener('click', function () {
                destroy(true);
            });
            elements.skip.addEventListener('click', function () {
                destroy(true);
            });
            elements.previous.addEventListener('click', function () {
                move(-1);
            });
            elements.next.addEventListener('click', function () {
                move(1);
            });
            window.addEventListener('resize', refreshPosition);
            window.addEventListener('scroll', refreshPosition, true);
            document.addEventListener('keydown', handleKeydown);

            activeTour = {
                destroy: destroy
            };

            showStep(0);
            elements.next.focus();
        }

        window.reopenOnboardingTour = function () {
            startTour(true);
        };

        document.querySelectorAll('[data-onboarding-reopen]').forEach(function (button) {
            button.addEventListener('click', function () {
                startTour(true);
            });
        });

        if (readStorage() !== 'done') {
            window.setTimeout(function () {
                startTour(false);
            }, 500);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        initDarkMode();
        initAutoSubmitSelects();
        initSidebarToggle();
        initFloatingActions();
        initOnboardingTour();
    });
})();
