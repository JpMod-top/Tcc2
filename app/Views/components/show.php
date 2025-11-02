<?php

declare(strict_types=1);

$component = $component ?? [];
$images = $images ?? [];
$stockMoves = $stockMoves ?? [];
$csrfUploadImage = $csrfUploadImage ?? '';
$csrfDeleteImage = $csrfDeleteImage ?? '';
$csrfSetCover = $csrfSetCover ?? '';
$csrfDatasheet = $csrfDatasheet ?? '';
$csrfStock = $csrfStock ?? '';
$csrfDelete = $csrfDelete ?? '';
?>
<div class="space-y-8">
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800 dark:text-slate-100">
                <?php echo htmlspecialchars($component['nome'] ?? 'Componente', ENT_QUOTES, 'UTF-8'); ?>
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                SKU: <?php echo htmlspecialchars($component['sku'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="/components/edit?id=<?php echo (int)($component['id'] ?? 0); ?>" class="inline-flex items-center rounded-lg border border-emerald-500 px-4 py-2 text-sm font-semibold text-emerald-600 transition hover:bg-emerald-50 dark:border-emerald-400 dark:text-emerald-300 dark:hover:bg-emerald-500/10">Editar</a>
            <form method="POST" action="/components/delete" onsubmit="return confirm('Confirma remover o componente?');">
                <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfDelete, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="id" value="<?php echo (int)($component['id'] ?? 0); ?>">
                <button type="submit" class="inline-flex items-center rounded-lg border border-rose-500 px-4 py-2 text-sm font-semibold text-rose-600 transition hover:bg-rose-50 dark:border-rose-400 dark:text-rose-300 dark:hover:bg-rose-500/10">
                    Excluir
                </button>
            </form>
        </div>
    </div>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <h2 class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Quantidade</h2>
            <p class="mt-2 text-2xl font-bold text-slate-800 dark:text-slate-100">
                <?php echo number_format((int)($component['quantidade'] ?? 0), 0, ',', '.'); ?>
                <span class="text-xs font-medium text-slate-500 dark:text-slate-400"><?php echo htmlspecialchars($component['unidade'] ?? 'un', ENT_QUOTES, 'UTF-8'); ?></span>
            </p>
        </article>
        <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <h2 class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Estoque mínimo</h2>
            <p class="mt-2 text-2xl font-bold text-slate-800 dark:text-slate-100">
                <?php echo number_format((int)($component['min_estoque'] ?? 0), 0, ',', '.'); ?>
            </p>
        </article>
        <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <h2 class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Custo unitário</h2>
            <p class="mt-2 text-2xl font-bold text-slate-800 dark:text-slate-100">
                R$ <?php echo number_format((float)($component['custo_unitario'] ?? 0), 2, ',', '.'); ?>
            </p>
        </article>
        <article class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <h2 class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Valor estimado</h2>
            <p class="mt-2 text-2xl font-bold text-slate-800 dark:text-slate-100">
                R$ <?php echo number_format((float)($component['quantidade'] ?? 0) * (float)($component['custo_unitario'] ?? 0), 2, ',', '.'); ?>
            </p>
        </article>
    </section>

    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
        <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Informações detalhadas</h2>
        <dl class="mt-4 grid gap-4 md:grid-cols-2">
            <?php
            $fields = [
                'Fabricante' => $component['fabricante'] ?? '—',
                'Código do fabricante' => $component['cod_fabricante'] ?? '—',
                'Categoria' => $component['categoria'] ?? '—',
                'Tags' => $component['tags'] ?? '—',
                'Localização' => $component['localizacao'] ?? '—',
                'Tolerância' => $component['tolerancia'] ?? '—',
                'Potência' => $component['potencia'] ?? '—',
                'Tensão máx.' => $component['tensao_max'] ?? '—',
                'Footprint' => $component['footprint'] ?? '—',
                'Preço médio' => $component['preco_medio'] !== null && $component['preco_medio'] !== '' ? 'R$ ' . number_format((float)$component['preco_medio'], 2, ',', '.') : '—',
            ];
            foreach ($fields as $label => $value): ?>
                <div>
                    <dt class="text-xs font-semibold uppercase text-slate-500 dark:text-slate-400"><?php echo $label; ?></dt>
                    <dd class="mt-1 text-sm text-slate-700 dark:text-slate-200"><?php echo htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); ?></dd>
                </div>
            <?php endforeach; ?>
        </dl>
        <?php if (!empty($component['descricao'])): ?>
            <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 dark:border-slate-700 dark:bg-slate-800/60 dark:text-slate-300">
                <?php echo nl2br(htmlspecialchars((string)$component['descricao'], ENT_QUOTES, 'UTF-8')); ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="grid gap-6 md:grid-cols-2">
        <div class="space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900" id="imagens">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Imagens</h2>
                <form method="POST" action="/components/upload-image" enctype="multipart/form-data" class="flex items-center gap-3">
                    <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfUploadImage, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="id" value="<?php echo (int)($component['id'] ?? 0); ?>">
                    <label class="inline-flex cursor-pointer items-center rounded-lg border border-dashed border-slate-300 px-3 py-2 text-xs font-semibold text-slate-600 hover:border-blue-400 hover:text-blue-500 dark:border-slate-600 dark:text-slate-300 dark:hover:border-blue-400 dark:hover:text-blue-300">
                        <input type="file" name="images[]" multiple accept="image/*" class="hidden" onchange="this.form.submit()">
                        Adicionar imagens
                    </label>
                </form>
            </div>
            <?php if (empty($images)): ?>
                <p class="text-sm text-slate-500 dark:text-slate-400">Nenhuma imagem cadastrada.</p>
            <?php else: ?>
                <div class="grid gap-4 sm:grid-cols-2">
                    <?php foreach ($images as $image): ?>
                        <figure class="rounded-lg border border-slate-200 p-3 dark:border-slate-700">
                            <img src="/components/image?id=<?php echo (int)$image['id']; ?>" alt="Imagem do componente" class="h-32 w-full rounded object-cover">
                            <figcaption class="mt-3 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                                <?php echo $image['principal'] ? '<span class="rounded bg-blue-100 px-2 py-1 text-blue-600 dark:bg-blue-500/10 dark:text-blue-300">Capa</span>' : ''; ?>
                                <div class="flex gap-2">
                                    <?php if (!$image['principal']): ?>
                                        <form method="POST" action="/components/set-cover">
                                            <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfSetCover, ENT_QUOTES, 'UTF-8'); ?>">
                                            <input type="hidden" name="component_id" value="<?php echo (int)($component['id'] ?? 0); ?>">
                                            <input type="hidden" name="image_id" value="<?php echo (int)$image['id']; ?>">
                                            <button type="submit" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                                                Definir capa
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" action="/components/delete-image" onsubmit="return confirm('Remover imagem?');">
                                        <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfDeleteImage, ENT_QUOTES, 'UTF-8'); ?>">
                                        <input type="hidden" name="component_id" value="<?php echo (int)($component['id'] ?? 0); ?>">
                                        <input type="hidden" name="image_id" value="<?php echo (int)$image['id']; ?>">
                                        <button type="submit" class="text-rose-600 hover:text-rose-500 dark:text-rose-400 dark:hover:text-rose-300">
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </figcaption>
                        </figure>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="space-y-4 rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900" id="arquivos">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Datasheet</h2>
            <?php if (!empty($component['datasheet_path'])): ?>
                <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm dark:border-slate-600 dark:bg-slate-800/60 dark:text-slate-200">
                    <span><?php echo htmlspecialchars($component['datasheet_path'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <a href="/components/datasheet?id=<?php echo (int)($component['id'] ?? 0); ?>" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300">
                        Baixar
                    </a>
                </div>
            <?php else: ?>
                <p class="text-sm text-slate-500 dark:text-slate-400">Nenhum datasheet anexado.</p>
            <?php endif; ?>
            <form method="POST" action="/components/upload-datasheet" enctype="multipart/form-data" class="rounded-lg border border-dashed border-slate-300 p-4 text-center dark:border-slate-600">
                <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfDatasheet, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="id" value="<?php echo (int)($component['id'] ?? 0); ?>">
                <label class="flex cursor-pointer flex-col items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <span>Arraste um PDF ou clique para selecionar</span>
                    <input type="file" name="datasheet" accept="application/pdf" class="hidden" onchange="this.form.submit()">
                </label>
            </form>
        </div>
    </section>

    <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900" id="movimentacoes">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Movimentações</h2>
            <form method="POST" action="/components/stock-move" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="_token" value="<?php echo htmlspecialchars($csrfStock, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="id" value="<?php echo (int)($component['id'] ?? 0); ?>">
                <select name="type" class="rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                    <option value="entrada">Entrada</option>
                    <option value="saida">Saída</option>
                    <option value="ajuste">Ajuste (define estoque)</option>
                </select>
                <input type="number" name="quantity" min="0" required placeholder="Quantidade" class="w-24 rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                <input type="text" name="reason" placeholder="Motivo (opcional)" class="w-48 rounded border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 focus:border-blue-500 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
                <button type="submit" class="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-500 dark:bg-blue-500 dark:text-slate-900 dark:hover:bg-blue-400">
                    Registrar
                </button>
            </form>
        </div>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700 dark:text-slate-200">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:bg-slate-800/60 dark:text-slate-400">
                    <tr>
                        <th class="px-4 py-3 text-left">Data</th>
                        <th class="px-4 py-3 text-left">Tipo</th>
                        <th class="px-4 py-3 text-left">Quantidade</th>
                        <th class="px-4 py-3 text-left">Motivo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    <?php if (empty($stockMoves)): ?>
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-slate-500 dark:text-slate-400">
                                Nenhuma movimentação registrada.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($stockMoves as $move): ?>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/60">
                                <td class="px-4 py-3"><?php echo htmlspecialchars($move['created_at'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td class="px-4 py-3 capitalize">
                                    <?php echo htmlspecialchars($move['tipo'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td class="px-4 py-3">
                                    <?php echo (int)$move['quantidade']; ?>
                                </td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($move['motivo'] ?? '—', ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>
</div>
