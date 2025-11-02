(function () {
    'use strict';

    var storageKey = 'meu-estoque-theme';

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

        mediaQuery.addEventListener('change', function (event) {
            if (event.matches) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.add('hidden');
                document.documentElement.classList.remove('overflow-hidden');
                sidebar.setAttribute('aria-hidden', 'false');
            } else {
                sidebar.classList.add('-translate-x-full');
                sidebar.setAttribute('aria-hidden', 'true');
            }
        });

        if (mediaQuery.matches) {
            sidebar.setAttribute('aria-hidden', 'false');
        } else {
            sidebar.setAttribute('aria-hidden', 'true');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        initDarkMode();
        initAutoSubmitSelects();
        initSidebarToggle();
    });
})();
