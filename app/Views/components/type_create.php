<?php declare(strict_types=1);

use App\Config\ComponentTypeRegistry;

$categories = $categories ?? [];
$csrfToken = $csrfToken ?? '';
$old = $_SESSION['_old_component_type'] ?? [];
unset($_SESSION['_old_component_type']);

function oldValue(array $old, string $key, string $default = ''): string
{
    return htmlspecialchars((string)($old[$key] ?? $default), ENT_QUOTES, 'UTF-8');
}
?>

<div class="mx-auto w-full max-w-3xl space-y-8">
    <header class="space-y-2">
        <p class="text-xs font-semibold uppercase tracking-[0.35em] text-blue-500 dark:text-blue-300">Tipos personalizados</p>
        <h1 class="text-3xl font-semibold text-slate-800 dark:text-slate-100">Cadastrar novo tipo de componente</h1>
        <p class="text-sm text-slate-500 dark:text-slate-300">
            Defina um conjunto de campos especificos para reutilizar no cadastro de componentes. O tipo ficará disponível para todos os novos cadastros.
        </p>
    </header>

    <form method="POST" action="/components/types" id="component-type-form" class="space-y-8">
        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

        <section class="space-y-4 rounded-2xl border border-slate-200 bg-white/90 p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/80">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Informações básicas</h2>
            <div class="grid gap-4 md:grid-cols-2">
                <label class="block text-sm font-medium text-slate-600 dark:text-slate-200">
                    Nome do tipo *
                    <input name="type_name" value="<?php echo oldValue($old, 'type_name'); ?>" required maxlength="120"
                           class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                </label>
                <label class="block text-sm font-medium text-slate-600 dark:text-slate-200">
                    Categoria *
                    <input list="component-type-categories" name="category" value="<?php echo oldValue($old, 'category'); ?>" required maxlength="120"
                           class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    <datalist id="component-type-categories">
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php endforeach; ?>
                    </datalist>
                </label>
            </div>
            <div class="grid gap-4 md:grid-cols-2">
                <label class="block text-sm font-medium text-slate-600 dark:text-slate-200">
                    Tag (opcional)
                    <input name="tag" value="<?php echo oldValue($old, 'tag'); ?>" maxlength="12"
                           class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm uppercase tracking-wide text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                </label>
            </div>
            <label class="block text-sm font-medium text-slate-600 dark:text-slate-200">
                Descrição
                <textarea name="description" rows="3"
                          class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"
                          placeholder="Explique rapidamente para que serve este tipo."><?php echo oldValue($old, 'description'); ?></textarea>
            </label>
        </section>

        <section class="space-y-4 rounded-2xl border border-slate-200 bg-white/90 p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/80">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Campos específicos</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-300">
                        Adicione os campos que aparecerão na etapa de preenchimento do componente. Use nomes sem espaços para o identificador.
                    </p>
                </div>
                <button type="button" data-field-add
                        class="inline-flex items-center gap-2 rounded-full border border-blue-300 px-3 py-1.5 text-xs font-semibold text-blue-600 transition hover:bg-blue-50 dark:border-blue-500/60 dark:text-blue-300 dark:hover:bg-blue-500/10">
                    <svg class="h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4"/></svg>
                    Adicionar campo
                </button>
            </div>
            <div id="type-fields" class="space-y-4">
                <?php
                $fieldRows = [];
                if (!empty($old['field_name']) && is_array($old['field_name'])) {
                    foreach ($old['field_name'] as $index => $name) {
                        $label = $old['field_label'][$index] ?? '';
                        $placeholder = $old['field_placeholder'][$index] ?? '';
                        if (trim((string)$name) !== '' && trim((string)$label) !== '') {
                            $fieldRows[] = [
                                'name' => (string)$name,
                                'label' => (string)$label,
                                'placeholder' => (string)$placeholder,
                            ];
                        }
                    }
                }
                if ($fieldRows === []) {
                    $fieldRows[] = ['name' => '', 'label' => '', 'placeholder' => ''];
                }
                ?>
                <?php foreach ($fieldRows as $row): ?>
                    <div class="field-row grid gap-3 rounded-xl border border-slate-200 px-4 py-4 dark:border-slate-700 md:grid-cols-[1fr,1fr,1fr,auto]">
                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Identificador
                            <input name="field_name[]" value="<?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?>" required
                                   class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"
                                   placeholder="ex.: capacitancia">
                        </label>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Rótulo
                            <input name="field_label[]" value="<?php echo htmlspecialchars($row['label'], ENT_QUOTES, 'UTF-8'); ?>" required
                                   class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"
                                   placeholder="ex.: Capacitância">
                        </label>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Placeholder
                            <input name="field_placeholder[]" value="<?php echo htmlspecialchars($row['placeholder'], ENT_QUOTES, 'UTF-8'); ?>"
                                   class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"
                                   placeholder="ex.: 100nF / 0603">
                        </label>
                        <div class="flex items-end justify-end">
                            <button type="button" data-field-remove
                                    class="inline-flex h-9 items-center justify-center rounded-full border border-rose-300 px-3 text-xs font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-rose-500/60 dark:text-rose-300 dark:hover:bg-rose-500/10">
                                Remover
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <footer class="flex flex-wrap gap-3">
            <button type="submit"
                    class="inline-flex items-center gap-2 rounded-full bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/70 dark:bg-blue-500 dark:text-slate-900 dark:hover:bg-blue-400">
                Salvar tipo
            </button>
            <a href="/components/new"
               class="inline-flex items-center gap-2 rounded-full border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-200 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">
                Cancelar
            </a>
        </footer>
    </form>
</div>

<template id="component-type-field-template">
    <div class="field-row grid gap-3 rounded-xl border border-slate-200 px-4 py-4 dark:border-slate-700 md:grid-cols-[1fr,1fr,1fr,auto]">
        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
            Identificador
            <input name="field_name[]" required
                   class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"
                   placeholder="ex.: parametro">
        </label>
        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
            Rótulo
            <input name="field_label[]" required
                   class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"
                   placeholder="ex.: Valor nominal">
        </label>
        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
            Placeholder
            <input name="field_placeholder[]"
                   class="mt-1 w-full rounded-lg border border-slate-300 bg-white px-2 py-1.5 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"
                   placeholder="ex.: 10uF">
        </label>
        <div class="flex items-end justify-end">
            <button type="button" data-field-remove
                    class="inline-flex h-9 items-center justify-center rounded-full border border-rose-300 px-3 text-xs font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-rose-500/60 dark:text-rose-300 dark:hover:bg-rose-500/10">
                Remover
            </button>
        </div>
    </div>
</template>

<script>
(function () {
    var addBtn = document.querySelector('[data-field-add]');
    var container = document.getElementById('type-fields');
    var template = document.getElementById('component-type-field-template');

    if (!addBtn || !container || !template) {
        return;
    }

    function registerRemoveButtons(scope) {
        var buttons = scope.querySelectorAll('[data-field-remove]');
        buttons.forEach(function (button) {
            button.addEventListener('click', function () {
                var row = button.closest('.field-row');
                if (row && container.children.length > 1) {
                    row.remove();
                } else {
                    window.showToast && window.showToast('Mantenha pelo menos um campo.', 'warning');
                }
            });
        });
    }

    registerRemoveButtons(container);

    addBtn.addEventListener('click', function () {
        var content = template.content.cloneNode(true);
        container.appendChild(content);
        registerRemoveButtons(container);
    });
})();
</script>
