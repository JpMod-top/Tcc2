<?php declare(strict_types=1);

use App\Config\ComponentTypeRegistry;
use App\Core\Csrf;

$type = $selectedType ?? ($_GET['type'] ?? '');
$definition = ComponentTypeRegistry::get($type);
$csrfToken = $csrfToken ?? Csrf::token('components_store');

if ($definition === null) {
    ?>
    <div class="space-y-6">
        <div class="rounded-2xl border border-amber-300 bg-amber-50 p-6 text-amber-800 dark:border-amber-500/60 dark:bg-amber-900/40 dark:text-amber-100">
            <h1 class="text-lg font-semibold">Tipo de componente não reconhecido</h1>
            <p class="mt-1 text-sm">
                Selecione novamente o tipo para que possamos preencher os campos com um modelo adequado.
            </p>
            <a href="/components/new"
               class="mt-4 inline-flex items-center gap-2 rounded-full border border-amber-300 px-4 py-2 text-sm font-medium text-amber-700 hover:bg-amber-100 dark:border-amber-400 dark:text-amber-100 dark:hover:bg-amber-900/60">
                Voltar para seleção de tipo
            </a>
        </div>
    </div>
    <?php
    return;
}

$base = $definition['base'] ?? [];
$presets = $definition['presets'] ?? [];
$defaultPresetKey = array_key_first($presets);
$defaultPreset = $defaultPresetKey ? $presets[$defaultPresetKey] : ['values' => []];
$currentValues = array_merge($base, $defaultPreset['values'] ?? []);

// Helper para inputs
function renderInput(string $name, string $label, string $value = '', string $placeholder = '', string $extra = '', ?string $hint = null): void
{
    $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
    $safeLabel = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
    $safePlaceholder = htmlspecialchars($placeholder, ENT_QUOTES, 'UTF-8');
    $safeValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    $hintMarkup = $hint ? '<p class="mt-1 text-xs text-slate-400 dark:text-slate-500">' . htmlspecialchars($hint, ENT_QUOTES, 'UTF-8') . '</p>' : '';
    echo <<<HTML
    <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
        {$safeLabel}
        <input data-field="{$safeName}" name="{$safeName}" value="{$safeValue}" placeholder="{$safePlaceholder}" {$extra}
               class="mt-1 w-full rounded-xl border border-slate-300 bg-white/90 px-3.5 py-2.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100">
        {$hintMarkup}
    </label>
HTML;
}

$presetOptions = [];
foreach ($presets as $key => $preset) {
    $presetOptions[] = [
        'key' => $key,
        'label' => $preset['label'],
        'values' => array_merge($base, $preset['values'] ?? []),
    ];
}
?>

<div class="space-y-8">
    <header class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white/80 p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 sm:flex-row sm:items-center sm:justify-between">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-700 dark:bg-blue-500/20 dark:text-blue-200">
                <?php echo htmlspecialchars($definition['tag'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">Novo componente</h1>
            <p class="max-w-2xl text-sm text-slate-500 dark:text-slate-300">
                <?php echo htmlspecialchars($definition['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
            </p>
        </div>
        <a href="/components/new"
           class="inline-flex items-center gap-2 rounded-full border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 transition hover:bg-slate-100 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">
            Trocar tipo
        </a>
    </header>

    <form id="dynamic-component-form" method="POST" action="/components/store" class="space-y-8">
        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>">

        <section class="rounded-2xl border border-slate-200 bg-white/80 p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div class="max-w-xl space-y-1.5">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Modelos rápidos</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-300">
                        Comece com um preset típico e ajuste os campos abaixo como preferir.
                    </p>
                </div>
                <div class="relative w-full max-w-md">
                    <input
                        id="quick-model-input"
                        type="text"
                        role="combobox"
                        aria-expanded="false"
                        aria-controls="quick-model-list"
                        autocomplete="off"
                        data-current="<?php echo htmlspecialchars($defaultPresetKey ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        value="<?php echo htmlspecialchars($defaultPreset['label'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                        class="w-full rounded-xl border border-slate-300 bg-white/90 px-3.5 py-2.5 text-sm text-slate-700 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-100"
                        placeholder="Selecionar modelo…"
                    >
                    <ul
                        id="quick-model-list"
                        role="listbox"
                        class="absolute left-0 right-0 z-20 mt-2 hidden max-h-64 overflow-auto rounded-xl border border-slate-200 bg-white shadow-xl dark:border-slate-700 dark:bg-slate-900"
                    ></ul>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white/80 p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
            <div class="mb-6 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        Informações principais
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-300">
                        Nome e identificação do componente no estoque.
                    </p>
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <?php
                renderInput('nome', 'Nome *', (string)($currentValues['nome'] ?? ''));
                renderInput('sku', 'SKU *', (string)($currentValues['sku'] ?? ''));
                renderInput('fabricante', 'Fabricante', (string)($currentValues['fabricante'] ?? ''));
                renderInput('localizacao', 'Localização', (string)($currentValues['localizacao'] ?? ''), 'ex.: Gaveta A1');
                ?>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white/80 p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
            <div class="mb-6 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        Estoque e custo
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-300">
                        Quantidade atual, unidade padrão e custo médio.
                    </p>
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-3">
                <?php
                renderInput('quantidade', 'Quantidade', (string)($currentValues['quantidade'] ?? 0), '', 'type="number" min="0" step="1"');
                renderInput('unidade', 'Unidade', (string)($currentValues['unidade'] ?? 'un'), 'ex.: un / rolo / pc');
                renderInput('min_estoque', 'Estoque mínimo', (string)($currentValues['min_estoque'] ?? 0), '', 'type="number" min="0" step="1"');
                renderInput('footprint', 'Footprint / Encapsulamento', (string)($currentValues['footprint'] ?? ''), 'ex.: 0603 / TQFP-32');
                renderInput('custo_unitario', 'Custo unitário', (string)($currentValues['custo_unitario'] ?? 0), '', 'type="number" min="0" step="0.01"');
                renderInput('preco_medio', 'Preço médio', (string)($currentValues['preco_medio'] ?? ''), '', 'type="number" min="0" step="0.01"');
                ?>
            </div>
        </section>

        <?php if (!empty($definition['fields'])): ?>
            <section class="rounded-2xl border border-slate-200 bg-white/80 p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <div>
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Específicos de <?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>
                        </h2>
                        <p class="text-sm text-slate-500 dark:text-slate-300">
                            Campos relevantes pré-preenchidos pelo modelo escolhido.
                        </p>
                    </div>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <?php foreach ($definition['fields'] as $field): ?>
                        <?php
                        $name = $field['name'];
                        $placeholder = $field['placeholder'] ?? '';
                        $hint = $field['hint'] ?? null;
                        $value = (string)($currentValues[$name] ?? '');
                        renderInput($name, $field['label'], $value, $placeholder, '', $hint);
                        ?>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <footer class="flex flex-wrap gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/70 dark:bg-blue-500 dark:text-slate-900 dark:hover:bg-blue-400">
                Salvar componente
            </button>
            <button type="submit" name="create_another" value="1"
                    class="inline-flex items-center gap-2 rounded-full border border-blue-200 px-5 py-2.5 text-sm font-semibold text-blue-600 transition hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-blue-200 dark:border-blue-400/40 dark:text-blue-200 dark:hover:bg-blue-500/10">
                Salvar e cadastrar outro
            </button>
            <a href="/components"
               class="inline-flex items-center gap-2 rounded-full border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-200 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">
                Cancelar
            </a>
        </footer>
    </form>
</div>

<script>
(function () {
    const presets = <?php echo json_encode($presetOptions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    const comboInput = document.getElementById('quick-model-input');
    const comboList = document.getElementById('quick-model-list');
    const form = document.getElementById('dynamic-component-form');

    const fields = Array.from(form.querySelectorAll('[data-field]')).reduce((acc, input) => {
        acc[input.dataset.field] = input;
        return acc;
    }, {});

    function applyPreset(values) {
        Object.entries(values).forEach(([key, value]) => {
            if (fields[key]) {
                fields[key].value = value;
            }
        });
    }

    function renderList(items) {
        if (!items.length) {
            comboList.innerHTML = '<li class="px-4 py-3 text-sm text-slate-500 dark:text-slate-300">Nenhum modelo encontrado</li>';
            return;
        }

        comboList.innerHTML = items.map((item, index) => `
            <li role="option"
                id="quick-model-option-${index}"
                data-key="${item.key}"
                class="cursor-pointer border-b border-slate-100 px-4 py-3 text-sm text-slate-700 transition hover:bg-blue-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-blue-500/10">
                ${item.label}
            </li>
        `).join('');
    }

    function filterList(term) {
        const lower = term.trim().toLowerCase();
        if (!lower) {
            return presets;
    }
        return presets.filter((preset) => preset.label.toLowerCase().includes(lower));
    }

    function openList(items) {
        renderList(items);
        comboList.classList.remove('hidden');
        comboInput.setAttribute('aria-expanded', 'true');
    }

    function closeList() {
        comboList.classList.add('hidden');
        comboInput.setAttribute('aria-expanded', 'false');
    }

    comboInput.addEventListener('focus', () => {
        openList(presets);
    });

    comboInput.addEventListener('input', (event) => {
        const results = filterList(event.target.value);
        openList(results);
    });

    comboList.addEventListener('click', (event) => {
        const option = event.target.closest('[data-key]');
        if (!option) {
            return;
        }
        const key = option.dataset.key;
        const preset = presets.find((item) => item.key === key);
        if (!preset) {
            return;
        }
        comboInput.value = preset.label;
        comboInput.dataset.current = key;
        applyPreset(preset.values);
        closeList();
    });

    document.addEventListener('click', (event) => {
        if (!comboList.contains(event.target) && event.target !== comboInput) {
            closeList();
        }
    });

    if (presets.length > 0) {
        applyPreset(presets[0].values);
    }
})();
</script>