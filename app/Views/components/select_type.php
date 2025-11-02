<?php declare(strict_types=1);

use App\Config\ComponentTypeRegistry;

$catalog = ComponentTypeRegistry::groupedByCategory();
?>
<div class="space-y-8">
    <header class="space-y-3">
        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-blue-400 dark:text-blue-300/80">
            Novo componente
        </p>
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-3xl font-semibold text-slate-800 dark:text-slate-100">
                    Selecione o tipo de componente
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-300">
                    Comece escolhendo a família. Depois refinamos os campos do formulário com presets úteis.
                </p>
            </div>
            <a href="/components"
               class="inline-flex items-center gap-2 rounded-full border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">
                Voltar para lista
            </a>
        </div>
    </header>

    <div class="rounded-2xl border border-slate-200 bg-white/70 shadow-sm backdrop-blur dark:border-slate-700 dark:bg-slate-900/60">
        <div class="flex flex-col gap-4 border-b border-slate-200 p-6 dark:border-slate-700 sm:flex-row sm:items-center sm:justify-between">
            <label class="relative flex-1">
                <span class="sr-only">Pesquisar</span>
                <input
                    id="component-type-search"
                    type="text"
                    placeholder="Filtrar por nome ou característica…"
                    class="w-full rounded-xl border border-slate-300 bg-white/90 pl-10 pr-4 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100 dark:placeholder:text-slate-400"
                >
                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z"/></svg>
                </span>
            </label>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button"
                        data-filter="all"
                        class="filter-chip active inline-flex items-center gap-2 rounded-full border border-blue-200 bg-blue-100 px-3 py-1.5 text-xs font-medium uppercase tracking-wide text-blue-700 transition hover:bg-blue-200 dark:border-blue-500/40 dark:bg-blue-500/20 dark:text-blue-200">
                    Todos
                </button>
                <?php foreach (array_keys($catalog) as $category): ?>
                    <button type="button"
                            data-filter="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>"
                            class="filter-chip inline-flex items-center gap-2 rounded-full border border-slate-200 px-3 py-1.5 text-xs font-medium uppercase tracking-wide text-slate-600 transition hover:border-blue-300 hover:text-blue-600 dark:border-slate-600 dark:text-slate-300 dark:hover:border-blue-400 dark:hover:text-blue-300">
                        <?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>
                    </button>
                <?php endforeach; ?>
                <a href="/components/types/new"
                   class="inline-flex items-center gap-2 rounded-full border border-emerald-300 px-3 py-1.5 text-xs font-semibold uppercase tracking-wide text-emerald-600 transition hover:bg-emerald-50 dark:border-emerald-500/60 dark:text-emerald-300 dark:hover:bg-emerald-500/10">
                    <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4"/></svg>
                    Novo tipo
                </a>
            </div>
        </div>

        <div class="space-y-8 p-6" id="component-type-catalog">
            <section class="space-y-3" data-category="personalizado">
                <header>
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Personalizados</h2>
                </header>
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    <a href="/components/types/new"
                       class="group flex h-full flex-col justify-between rounded-2xl border border-dashed border-emerald-400 bg-emerald-50/60 p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-500 hover:bg-emerald-100/70 hover:shadow-lg dark:border-emerald-500/60 dark:bg-emerald-900/20 dark:hover:border-emerald-400 dark:hover:bg-emerald-900/40">
                        <div>
                            <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-emerald-600 transition group-hover:bg-emerald-200 dark:bg-emerald-500/20 dark:text-emerald-200">
                                Custom
                            </span>
                            <p class="mt-3 text-base font-semibold text-emerald-700 transition group-hover:text-emerald-600 dark:text-emerald-200 dark:group-hover:text-emerald-100">
                                Criar novo tipo
                            </p>
                            <p class="mt-1 text-sm text-emerald-600/80 dark:text-emerald-200/70">
                                Defina campos específicos de acordo com a necessidade do seu estoque.
                            </p>
                        </div>
                        <span class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-emerald-600 transition group-hover:translate-x-0.5 dark:text-emerald-200">
                            Configurar agora
                            <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 18l6-6-6-6"/></svg>
                        </span>
                    </a>
                </div>
            </section>
            <?php foreach ($catalog as $category => $items): ?>
                <section data-category="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>" class="space-y-3">
                    <header class="flex items-center justify-between">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            <?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>
                        </h2>
                    </header>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        <?php foreach ($items as $item): ?>
                            <button
                                type="button"
                                data-type="<?php echo htmlspecialchars($item['type'], ENT_QUOTES, 'UTF-8'); ?>"
                                data-category="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>"
                                class="type-card group flex h-full flex-col justify-between rounded-2xl border border-slate-200 bg-white/90 p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-lg dark:border-slate-700 dark:bg-slate-900/80 dark:hover:border-blue-500/60"
                            >
                                <div>
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-blue-600 transition group-hover:bg-blue-200 dark:bg-blue-500/20 dark:text-blue-200">
                                        <?php echo htmlspecialchars($item['tag'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                    <p class="mt-3 text-base font-semibold text-slate-800 transition group-hover:text-blue-600 dark:text-slate-100 dark:group-hover:text-blue-200">
                                        <?php echo htmlspecialchars($item['type'], ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-300">
                                        <?php echo htmlspecialchars($item['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                </div>
                                <span class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-blue-600 transition group-hover:translate-x-0.5 dark:text-blue-300">
                                    Selecionar
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                                </span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
(function () {
    const searchInput = document.getElementById('component-type-search');
    const filterChips = Array.from(document.querySelectorAll('.filter-chip'));
    const cards = Array.from(document.querySelectorAll('.type-card'));

    function navigate(type) {
        window.location.href = '/components/new?type=' + encodeURIComponent(type);
    }

    cards.forEach((card) => {
        card.addEventListener('click', () => navigate(card.dataset.type));
        card.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                navigate(card.dataset.type);
            }
        });
        card.setAttribute('tabindex', '0');
        card.setAttribute('role', 'button');
        card.setAttribute('aria-label', card.dataset.type);
    });

    function applyFilter() {
        const term = searchInput.value.trim().toLowerCase();
        const activeChip = filterChips.find((chip) => chip.classList.contains('active'));
        const activeCategory = activeChip ? activeChip.dataset.filter : 'all';

        cards.forEach((card) => {
            const matchesSearch = term === '' || card.dataset.type.toLowerCase().includes(term);
            const matchesCategory = activeCategory === 'all' || card.dataset.category === activeCategory;
            card.classList.toggle('hidden', !(matchesSearch && matchesCategory));
        });
    }

    filterChips.forEach((chip) => {
        chip.addEventListener('click', () => {
            filterChips.forEach((c) => c.classList.remove('active'));
            chip.classList.add('active');
            applyFilter();
        });
    });

    searchInput.addEventListener('input', applyFilter);
})();
</script>
