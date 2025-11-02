<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\ComponentTypeRegistry;
use App\Config\ComponentTypeStore;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;
use RuntimeException;

class ComponentTypeController
{
    public function create(): void
    {
        Auth::requireAuth();

        View::render('components/type_create', [
            'title' => 'Novo tipo de componente',
            'csrfToken' => Csrf::token('component_type_store'),
            'categories' => ComponentTypeRegistry::categories(),
        ]);
    }

    public function store(): void
    {
        Auth::requireAuth();

        if (!Csrf::verify('component_type_store', $_POST['_token'] ?? null)) {
            $this->flash('error', 'Sessao expirada. Tente novamente.');
            $this->redirect('/components/types/new');
        }

        [$typeName, $data, $errors] = $this->validateInput($_POST);

        if ($errors !== []) {
            foreach ($errors as $message) {
                $this->flash('error', $message);
            }
            $_SESSION['_old_component_type'] = $_POST;
            $this->redirect('/components/types/new');
        }

        if (ComponentTypeRegistry::get($typeName) !== null) {
            $this->flash('error', 'Ja existe um tipo com esse nome.');
            $_SESSION['_old_component_type'] = $_POST;
            $this->redirect('/components/types/new');
        }

        try {
            ComponentTypeStore::add($typeName, $data);
        } catch (RuntimeException $exception) {
            $this->flash('error', $exception->getMessage());
            $_SESSION['_old_component_type'] = $_POST;
            $this->redirect('/components/types/new');
        }

        $this->flash('success', 'Novo tipo cadastrado com sucesso.');
        unset($_SESSION['_old_component_type']);
        $this->redirect('/components/new?type=' . rawurlencode($typeName));
    }

    /**
     * @return array{0:string,1:array<string,mixed>,2:list<string>}
     */
    private function validateInput(array $input): array
    {
        $errors = [];

        $typeName = trim((string)($input['type_name'] ?? ''));
        if ($typeName === '') {
            $errors[] = 'Informe o nome do tipo.';
        }

        $category = trim((string)($input['category'] ?? ''));
        if ($category === '') {
            $errors[] = 'Informe a categoria.';
        }

        $tag = trim((string)($input['tag'] ?? ''));
        $description = trim((string)($input['description'] ?? ''));

        $fieldNames = $input['field_name'] ?? [];
        $fieldLabels = $input['field_label'] ?? [];
        $fieldPlaceholders = $input['field_placeholder'] ?? [];

        $fields = [];
        if (is_array($fieldNames) && is_array($fieldLabels)) {
            $count = max(count($fieldNames), count($fieldLabels));
            for ($i = 0; $i < $count; $i++) {
                $name = trim((string)($fieldNames[$i] ?? ''));
                $label = trim((string)($fieldLabels[$i] ?? ''));
                if ($name === '' || $label === '') {
                    continue;
                }
                $placeholder = trim((string)($fieldPlaceholders[$i] ?? ''));
                $fields[] = [
                    'name' => $this->slug($name),
                    'label' => $label,
                    'placeholder' => $placeholder,
                ];
            }
        }

        if ($fields === []) {
            $errors[] = 'Adicione ao menos um campo especifico.';
        }

        $definition = [
            'category' => $category,
            'tag' => $tag !== '' ? strtoupper($tag) : strtoupper(substr($typeName, 0, 6)),
            'description' => $description !== '' ? $description : 'Tipo customizado criado pelo usuario.',
            'base' => [
                'nome' => $typeName,
                'categoria' => $category,
                'unidade' => 'un',
            ],
            'fields' => $fields,
            'presets' => [],
        ];

        return [$typeName, $definition, $errors];
    }

    private function slug(string $value): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT', $value) ?: $value;
        $value = preg_replace('/[^a-z0-9_]+/i', '_', $value);
        return strtolower(trim((string)$value, '_'));
    }

    private function redirect(string $location): void
    {
        header('Location: ' . $location, true, 302);
        exit;
    }

    private function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][$type][] = $message;
    }
}
