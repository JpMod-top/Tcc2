<?php

declare(strict_types=1);

$components = $components ?? [];
$csrfInline = $csrfInline ?? '';
$csrfDelete = $csrfDelete ?? '';
$csrfStock = $csrfStock ?? '';
?>
<div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-slate-900">
    <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-700">
        <thead class="bg-slate-50 dark:bg-slate-800/60">
            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                <th class="px-4 py-3">Nome</th>
                <th class="px-4 py-3">SKU</th>
                <th class="px-4 py-3">Categoria</th>
                <th class="px-4 py-3">Quantidade</th>
                <th class="px-4 py-3">Localização</th>
                <th class="px-4 py-3">Tags</th>
                <th class="px-4 py-3 text-right">Ações</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200 text-sm dark:divide-slate-700 dark:text-slate-100">
            <?php if (empty($components)): ?>
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">
                        Nenhum componente encontrado.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($components as $component): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60">
                        <td class="px-4 py-3 font-medium">
                            <div class="flex flex-col">
                                <a href="/components/view?id=<?php echo (int)$component['id']; ?>" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                    <?php echo htmlspecialchars($component['nome'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                                <span class="text-xs text-slate-500 dark:text-slate-400">
                                    Atualizado em <?php echo htmlspecialchars((string)$component['updated_at'], ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            <?php echo htmlspecialchars($component['sku'], ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                            <?php echo htmlspecialchars($component['categoria'] ?? '—', ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                        <td class="px-4 py-3">
                            <input
                                type="number"
                                class="inline-editor w-24 rounded border border-slate-300 bg-white px-2 py-1 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"
                                data-id="<?php echo (int)$component['id']; ?>"
                                data-field="quantidade"
                                data-token="<?php echo htmlspecialchars($csrfInline, ENT_QUOTES, 'UTF-8'); ?>"
                                value="<?php echo (int)$component['quantidade']; ?>"
                            >
                        </td>
                        <td class="px-4 py-3">
                            <input
                                type="text"
                                class="inline-editor w-full rounded border border-slate-300 bg-white px-2 py-1 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"
                                data-id="<?php echo (int)$component['id']; ?>"
                                data-field="localizacao"
                                data-token="<?php echo htmlspecialchars($csrfInline, ENT_QUOTES, 'UTF-8'); ?>"
                                value="<?php echo htmlspecialchars((string)($component['localizacao'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                            >
                        </td>
                        <td class="px-4 py-3">
                            <input
                                type="text"
                                class="inline-editor w-full rounded border border-slate-300 bg-white px-2 py-1 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100"
                                data-id="<?php echo (int)$component['id']; ?>"
                                data-field="tags"
                                data-token="<?php echo htmlspecialchars($csrfInline, ENT_QUOTES, 'UTF-8'); ?>"
                                value="<?php echo htmlspecialchars((string)($component['tags'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>"
                            >
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button
                                    type="button"
                                    data-component="<?php echo (int)$component['id']; ?>"
                                    data-name="<?php echo htmlspecialchars($component['nome'], ENT_QUOTES, 'UTF-8'); ?>"
                                    data-token="<?php echo htmlspecialchars($csrfStock, ENT_QUOTES, 'UTF-8'); ?>"
                                    class="stock-move-btn inline-flex items-center rounded border border-slate-300 px-3 py-1 text-xs font-semibold text-slate-600 transition hover:border-blue-400 hover:text-blue-500 dark:border-slate-600 dark:text-slate-300 dark:hover:border-blue-400 dark:hover:text-blue-300"
                                >Movimentar</button>
                                <a href="/components/edit?id=<?php echo (int)$component['id']; ?>" class="inline-flex items-center rounded border border-emerald-500 px-3 py-1 text-xs font-semibold text-emerald-600 transition hover:bg-emerald-50 dark:border-emerald-400 dark:text-emerald-300 dark:hover:bg-emerald-500/10">Editar</a>
                                <form method="POST" action="/components/delete" class="inline">
                                    <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfDelete, ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="id" value="<?php echo (int)$component['id']; ?>">
                                    <button type="submit" class="inline-flex items-center rounded border border-rose-500 px-3 py-1 text-xs font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-rose-400 dark:text-rose-300 dark:hover:bg-rose-500/10" onclick="return confirm('Remover componente?')">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="stock-move-modal" class="fixed inset-0 z-50 hidden">
    <div data-stock-move-backdrop class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
    <div class="relative mx-auto flex h-full max-h-screen w-full max-w-md items-center justify-center px-4 py-8">
        <div class="w-full rounded-2xl border border-slate-200 bg-white/95 shadow-xl dark:border-slate-700 dark:bg-slate-900/90">
            <div class="flex items-start justify-between gap-3 border-b border-slate-200 px-6 pt-6 pb-4 dark:border-slate-700">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-blue-500 dark:text-blue-300">Movimentar estoque</p>
                    <h2 class="mt-1 text-lg font-semibold text-slate-800 dark:text-slate-100" id="stock-move-component-name"></h2>
                </div>
                <button type="button" class="stock-move-close inline-flex h-8 w-8 items-center justify-center rounded-full border border-slate-200 text-slate-500 transition hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700" aria-label="Fechar modal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="stock-move-form" method="POST" action="/components/stock-move" class="space-y-5 px-6 py-6">
                <input type="hidden" name="_token" id="stock-move-token" value="">
                <input type="hidden" name="id" id="stock-move-id" value="">

                <div class="grid gap-4">
                    <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
                        Tipo de movimentacao
                        <select id="stock-move-type" name="type" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                            <option value="entrada">Entrada</option>
                            <option value="saida">Saida</option>
                            <option value="ajuste">Ajuste</option>
                        </select>
                    </label>
                    <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
                        Quantidade
                        <input id="stock-move-quantity" name="quantity" type="number" min="1" step="1" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" placeholder="Informe a quantidade">
                    </label>
                    <label class="block text-sm font-medium text-slate-600 dark:text-slate-300">
                        Motivo (opcional)
                        <textarea id="stock-move-reason" name="reason" rows="3" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/40 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100" placeholder="Ex.: ajuste de inventario, pedido do cliente, etc."></textarea>
                    </label>
                </div>

                <div class="flex flex-col gap-3 border-t border-slate-200 pt-4 dark:border-slate-700 sm:flex-row sm:justify-end">
                    <button type="button" class="stock-move-close inline-flex items-center justify-center rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-800">Cancelar</button>
                    <button type="submit" class="inline-flex items-center justify-center rounded-full bg-blue-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-400/70 dark:bg-blue-500 dark:text-slate-900 dark:hover:bg-blue-400">
                        Registrar movimentacao
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    (function () {
        var editors = document.querySelectorAll('.inline-editor');
        var timeoutId = null;

        function sendUpdate(input) {
            var id = input.getAttribute('data-id');
            var field = input.getAttribute('data-field');
            var token = input.getAttribute('data-token');
            var value = input.value;

            var payload = new FormData();
            payload.append('_token', token);
            payload.append('id', id);
            payload.append('field', field);
            payload.append('value', value);

            fetch('/components/inline', {
                method: 'POST',
                body: payload,
                credentials: 'same-origin'
            })
                .then(function (response) {
                    return response.json();
                })
                .then(function (data) {
                    if (!data.success) {
                        window.showToast && window.showToast(data.message || 'Não foi possível atualizar.', 'error');
                        return;
                    }
                    if (field === 'quantidade') {
                        input.value = data.value;
                    }
                    window.showToast && window.showToast('Atualizado!', 'success');
                })
                .catch(function () {
                    window.showToast && window.showToast('Falha ao atualizar.', 'error');
                });
        }

        editors.forEach(function (input) {
            input.addEventListener('change', function () {
                sendUpdate(input);
            });
            input.addEventListener('keyup', function (event) {
                clearTimeout(timeoutId);
                timeoutId = setTimeout(function () {
                    if (event.key === 'Enter') {
                        input.blur();
                    }
                }, 200);
            });
        });

        var moveButtons = document.querySelectorAll('.stock-move-btn');
        var moveModal = document.getElementById('stock-move-modal');
        var moveForm = document.getElementById('stock-move-form');
        var moveType = document.getElementById('stock-move-type');
        var moveQuantity = document.getElementById('stock-move-quantity');
        var moveReason = document.getElementById('stock-move-reason');
        var moveName = document.getElementById('stock-move-component-name');
        var moveToken = document.getElementById('stock-move-token');
        var moveId = document.getElementById('stock-move-id');
        var moveClose = document.querySelectorAll('.stock-move-close');
        var moveBackdrop = moveModal ? moveModal.querySelector('[data-stock-move-backdrop]') : null;

        function openMoveModal(data) {
            if (!moveModal) {
                return;
            }
            moveModal.classList.remove('hidden');
            document.documentElement.classList.add('overflow-hidden');

            moveName.textContent = data.name;
            moveToken.value = data.token;
            moveId.value = data.id;
            moveType.value = 'entrada';
            moveQuantity.value = '1';
            moveReason.value = '';

            setTimeout(function () {
                moveType.focus();
            }, 50);
        }

        function closeMoveModal() {
            if (!moveModal) {
                return;
            }
            moveModal.classList.add('hidden');
            document.documentElement.classList.remove('overflow-hidden');
        }

        moveButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                openMoveModal({
                    id: button.getAttribute('data-component'),
                    name: button.getAttribute('data-name'),
                    token: button.getAttribute('data-token'),
                });
            });
        });

        moveClose.forEach(function (trigger) {
            trigger.addEventListener('click', closeMoveModal);
        });

        if (moveBackdrop) {
            moveBackdrop.addEventListener('click', closeMoveModal);
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && moveModal && !moveModal.classList.contains('hidden')) {
                closeMoveModal();
            }
        });

        if (moveForm) {
            moveForm.addEventListener('submit', function (event) {
                var typeValue = (moveType.value || '').toLowerCase();
                if (['entrada', 'saida', 'ajuste'].indexOf(typeValue) === -1) {
                    event.preventDefault();
                    window.showToast && window.showToast('Tipo de movimentacao invalido.', 'error');
                    moveType.focus();
                    return;
                }

                var quantityValue = parseInt(moveQuantity.value, 10);
                if (Number.isNaN(quantityValue) || quantityValue <= 0) {
                    event.preventDefault();
                    window.showToast && window.showToast('Informe uma quantidade valida.', 'error');
                    moveQuantity.focus();
                    return;
                }

                moveType.value = typeValue;
                moveQuantity.value = quantityValue.toString();
                closeMoveModal();
            });
        }
    })();
</script>