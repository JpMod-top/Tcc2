<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\ComponentTypeRegistry;
use App\Core\Auth;
use App\Core\Csrf;
use App\Core\DB;
use App\Core\View;
use App\Models\AuditLog;
use App\Models\Component;
use App\Models\Image;
use App\Models\StockMove;

use RuntimeException;

class ComponentController
{
    private const STORAGE_PATH = __DIR__ . '/../../storage/uploads';

    public function index(): void
    {
        Auth::requireAuth();
        $user = Auth::user();
        $userId = Auth::userId();
        if ($userId === null) {
            $this->redirect('/login');
        }

        $params = [
            'page' => $_GET['page'] ?? 1,
            'per_page' => $_GET['per_page'] ?? 15,
            'q' => $_GET['q'] ?? '',
            'category' => $_GET['category'] ?? '',
            'zeroed' => isset($_GET['zeroed']) ? (bool)$_GET['zeroed'] : false,
            'below_min' => isset($_GET['below_min']) ? (bool)$_GET['below_min'] : false,
            'sort' => $_GET['sort'] ?? 'Nãome',
            'direction' => $_GET['direction'] ?? 'asc',
        ];

        $result = Component::paginateForUser($userId, $params);

        View::render('components/list', [
            'title' => 'Componentes',
            'components' => $result['data'],
            'pagination' => $result['pagination'],
            'filters' => $params,
            'categories' => Component::categories($userId),
            'csrfInline' => Csrf::token('components_inline'),
            'csrfDelete' => Csrf::token('components_delete'),
            'csrfStock' => Csrf::token('components_stock'),
            'user' => $user,
        ]);
    }

    public function create(): void
    {
        Auth::requireAuth();
        $selectedType = $_GET['type'] ?? null;

        if ($selectedType === null) {
            View::render('components/select_type', [
                'title' => 'Selecione o tipo de componente',
            ]);
            return;
        }

        View::render('components/new_dynamic', [
            'title' => 'Nãovo componente',
            'csrfToken' => Csrf::token('components_store'),
            'selectedType' => $selectedType,
        ]);
    }

    public function selectType(): void
    {
        $this->create();
    }

    public function store(): void
    {
        Auth::requireAuth();

        if (!$this->verifyCsrf('components_store', $_POST['_token'] ?? null)) {
            $this->flash('error', 'Sess+ï¿½o expirada. Tente Nãovamente.');
            $this->redirect('/components/new');
        }

        [$data, $errors] = $this->validateComponentInput($_POST);
        if ($errors !== []) {
            $_SESSION['_component_errors'] = $errors;
            $_SESSION['_old_component'] = $data;
            $this->redirect('/components/new');
        }

        $userId = Auth::userId();
        if ($userId === null) {
            $this->redirect('/login');
        }

        if (Component::findBySku($data['sku'], $userId) !== null) {
            $_SESSION['_component_errors'] = ['SKU j+ï¿½ utilizado por outro componente.'];
            $_SESSION['_old_component'] = $data;
            $this->redirect('/components/new');
        }

        $componentId = Component::create($userId, $data);

        AuditLog::record(
            $userId,
            'components',
            $componentId,
            'create',
            $data,
            $this->clientIp(),
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        $this->flash('success', 'Componente criado com sucesso.');
        $this->redirect('/components/view?id=' . $componentId);
    }

    public function show(): void
    {
        Auth::requireAuth();
        $component = $this->resolveComponentFromQuery();
        if ($component === null) {
            $this->abort404();
        }

        $userId = Auth::userId();
        if ($userId === null) {
            $this->redirect('/login');
        }

        View::render('components/show', [
            'title' => 'Detalhes do componente',
            'component' => $component,
            'images' => Image::listByComponent((int)$component['id'], $userId),
            'stockMoves' => StockMove::listForComponent($userId, (int)$component['id'], 15),
            'csrfUploadImage' => Csrf::token('components_upload_image'),
            'csrfDeleteImage' => Csrf::token('components_delete_image'),
            'csrfSetCover' => Csrf::token('components_set_cover'),
            'csrfDatasheet' => Csrf::token('components_datasheet'),
            'csrfStock' => Csrf::token('components_stock'),
            'csrfDelete' => Csrf::token('components_delete'),
        ]);
    }

    public function edit(): void
    {
        Auth::requireAuth();
        $component = $this->resolveComponentFromQuery();
        if ($component === null) {
            $this->abort404();
        }

        View::render('components/edit', [
            'title' => 'Editar componente',
            'component' => $component,
            'csrfToken' => Csrf::token('components_update'),
            'errors' => $_SESSION['_component_errors'] ?? [],
        ]);
        unset($_SESSION['_component_errors']);
    }

    public function update(): void
    {
        Auth::requireAuth();

        if (!$this->verifyCsrf('components_update', $_POST['_token'] ?? null)) {
            $this->flash('error', 'Sess+ï¿½o expirada. Tente Nãovamente.');
            $this->redirect('/components');
        }

        $componentId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $component = $this->findComponent($componentId);
        if ($component === null) {
            $this->abort404();
        }

        [$data, $errors] = $this->validateComponentInput($_POST, true);
        if ($errors !== []) {
            $_SESSION['_component_errors'] = $errors;
            $this->redirect('/components/edit?id=' . $componentId);
        }

        $userId = (int)$component['user_id'];
        if ($data['sku'] !== $component['sku'] && Component::findBySku($data['sku'], $userId, $componentId) !== null) {
            $_SESSION['_component_errors'] = ['SKU j+ï¿½ utilizado por outro componente.'];
            $this->redirect('/components/edit?id=' . $componentId);
        }

        $diff = $this->diffComponent($component, $data);

        Component::update($componentId, $userId, $data);

        if ($diff !== []) {
            AuditLog::record(
                $userId,
                'components',
                $componentId,
                'update',
                $diff,
                $this->clientIp(),
                $_SERVER['HTTP_USER_AGENT'] ?? null
            );
        }

        $this->flash('success', 'Componente atualizado.');
        $this->redirect('/components/view?id=' . $componentId);
    }

    public function destroy(): void
    {
        Auth::requireAuth();

        if (!$this->verifyCsrf('components_delete', $_POST['_token'] ?? null)) {
            $this->flash('error', 'Sess+ï¿½o expirada.');
            $this->redirect('/components');
        }

        $componentId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $component = $this->findComponent($componentId);
        if ($component === null) {
            $this->abort404();
        }

        Component::softDelete($componentId, (int)$component['user_id']);

        AuditLog::record(
            (int)$component['user_id'],
            'components',
            $componentId,
            'delete',
            ['id' => $componentId],
            $this->clientIp(),
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        $this->flash('success', 'Componente removido.');
        $this->redirect('/components');
    }

    public function inlineUpdate(): void
    {
        Auth::requireAuth();

        if (!$this->verifyCsrf('components_inline', $_POST['_token'] ?? null)) {
            $this->jsonResponse(['success' => false, 'message' => 'Sess+ï¿½o expirada.'], 419);
        }

        $componentId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $field = $_POST['field'] ?? '';
        $value = $_POST['value'] ?? null;

        $component = $this->findComponent($componentId);
        if ($component === null) {
            $this->jsonResponse(['success' => false, 'message' => 'Componente n+ï¿½o encontrado.'], 404);
        }

        $allowed = ['quantidade', 'localizacao', 'tags'];
        if (!in_array($field, $allowed, true)) {
            $this->jsonResponse(['success' => false, 'message' => 'Campo n+ï¿½o permitido.'], 400);
        }

        $newValue = $value;
        if ($field === 'quantidade') {
            $newValue = max(0, (int)$value);
        } elseif ($value !== null) {
            $newValue = trim((string)$value);
        }

        Component::updateFields($componentId, (int)$component['user_id'], [$field => $newValue]);

        AuditLog::record(
            (int)$component['user_id'],
            'components',
            $componentId,
            'inline_update',
            [$field => ['old' => $component[$field] ?? null, 'new' => $newValue]],
            $this->clientIp(),
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        $this->jsonResponse(['success' => true, 'value' => $newValue]);
    }

    public function stockMove(): void
    {
        Auth::requireAuth();

        if (!$this->verifyCsrf('components_stock', $_POST['_token'] ?? null)) {
            $this->flash('error', 'Sess+ï¿½o expirada.');
            $this->redirect('/components');
        }

        $componentId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $component = $this->findComponent($componentId);
        if ($component === null) {
            $this->abort404();
        }

        $type = $_POST['type'] ?? '';
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
        $reason = trim((string)($_POST['reason'] ?? ''));

        if (!in_array($type, StockMove::TYPES, true)) {
            $this->flash('error', 'Movimentaï¿½ï¿½o invï¿½lida.');
            $this->redirect('/components/view?id=' . $componentId);
        }

        $currentQty = (int)$component['quantidade'];
        $absQuantity = abs($quantity);
        $delta = 0;
        $newQty = $currentQty;

        switch ($type) {
            case 'entrada':
                if ($absQuantity === 0) {
                    $this->flash('error', 'Informe uma quantidade vï¿½lida.');
                    $this->redirect('/components/view?id=' . $componentId);
                }
                $delta = $absQuantity;
                $newQty = $currentQty + $delta;
                break;
            case 'saida':
                if ($absQuantity === 0) {
                    $this->flash('error', 'Informe uma quantidade vï¿½lida.');
                    $this->redirect('/components/view?id=' . $componentId);
                }
                if ($absQuantity >= $currentQty) {
                    $delta = -$currentQty;
                    $newQty = 0;
                } else {
                    $delta = -$absQuantity;
                    $newQty = $currentQty + $delta;
                }
                break;
            case 'ajuste':
                $target = max(0, (int)$quantity);
                if ($target === $currentQty) {
                    $this->flash('warning', 'Nenhuma alteraï¿½ï¿½o detectada.');
                    $this->redirect('/components/view?id=' . $componentId);
                }
                $delta = $target - $currentQty;
                $newQty = $target;
                break;
        }

        $userId = (int)$component['user_id'];
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        DB::transaction(static function () use ($componentId, $userId, $type, $delta, $reason, $newQty, $currentQty, $ip, $agent): void {
            $moveId = StockMove::record($userId, $componentId, $type, $delta, $reason);

            Component::updateFields($componentId, $userId, [
                'quantidade' => $newQty,
            ]);

            AuditLog::record(
                $userId,
                'stock_moves',
                $moveId,
                'create',
                [
                    'component_id' => $componentId,
                    'tipo' => $type,
                    'quantidade' => $delta,
                    'motivo' => $reason,
                    'estoque_anterior' => $currentQty,
                    'estoque_atual' => $newQty,
                ],
                $ip,
                $agent
            );
        });

        $this->flash('success', 'Movimenta+ï¿½+ï¿½o registrada.');
        $this->redirect('/components/view?id=' . $componentId);
    }

    public function uploadImage(): void
    {
        Auth::requireAuth();

        if (!$this->verifyCsrf('components_upload_image', $_POST['_token'] ?? null)) {
            $this->flash('error', 'Sess+ï¿½o expirada.');
            $this->redirect('/components');
        }

        $componentId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $component = $this->findComponent($componentId);
        if ($component === null) {
            $this->abort404();
        }

        if (empty($_FILES['images'])) {
            $this->flash('error', 'Nenhuma imagem enviada.');
            $this->redirect('/components/view?id=' . $componentId . '#imagens');
        }

        $this->ensureUploadsDirectory();

        $files = $_FILES['images'];
        $count = is_array($files['name']) ? count($files['name']) : 0;
        $uploads = 0;
        for ($i = 0; $i < $count; $i++) {
            $tmpName = $files['tmp_name'][$i];
            $name = $files['name'][$i];
            $error = $files['error'][$i];
            if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($tmpName)) {
                continue;
            }

            $extension = strtolower((string)pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                continue;
            }

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($tmpName) ?: '';
            if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/bmp', 'image/x-ms-bmp'], true)) {
                continue;
            }

            $hashName = bin2hex(random_bytes(16)) . '.' . $extension;
            $destination = rtrim(self::STORAGE_PATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $hashName;

            if (!move_uploaded_file($tmpName, $destination)) {
                continue;
            }

            Image::add((int)$component['user_id'], $componentId, $hashName, $uploads === 0);
            $uploads++;
        }

        if ($uploads === 0) {
            $this->flash('warning', 'Nenhuma imagem v+ï¿½lida foi enviada.');
        } else {
            $this->flash('success', 'Imagem(s) adicionada(s) com sucesso.');
        }

        $this->redirect('/components/view?id=' . $componentId . '#imagens');
    }

    public function deleteImage(): void
    {
        Auth::requireAuth();

        if (!$this->verifyCsrf('components_delete_image', $_POST['_token'] ?? null)) {
            $this->flash('error', 'Sess+ï¿½o expirada.');
            $this->redirect('/components');
        }

        $componentId = isset($_POST['component_id']) ? (int)$_POST['component_id'] : 0;
        $imageId = isset($_POST['image_id']) ? (int)$_POST['image_id'] : 0;
        $component = $this->findComponent($componentId);
        if ($component === null) {
            $this->abort404();
        }

        $path = Image::delete($imageId, (int)$component['user_id'], $componentId);
        if ($path !== null) {
            $fullPath = rtrim(self::STORAGE_PATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $path;
            if (is_file($fullPath)) {
                @unlink($fullPath);
            }

            AuditLog::record(
                (int)$component['user_id'],
                'images',
                $imageId,
                'delete',
                ['component_id' => $componentId, 'path' => $path],
                $this->clientIp(),
                $_SERVER['HTTP_USER_AGENT'] ?? null
            );

            $this->flash('success', 'Imagem removida.');
        } else {
            $this->flash('warning', 'Imagem n+ï¿½o encontrada.');
        }

        $this->redirect('/components/view?id=' . $componentId . '#imagens');
    }

    public function setCover(): void
    {
        Auth::requireAuth();

        if (!$this->verifyCsrf('components_set_cover', $_POST['_token'] ?? null)) {
            $this->flash('error', 'Sess+ï¿½o expirada.');
            $this->redirect('/components');
        }

        $componentId = isset($_POST['component_id']) ? (int)$_POST['component_id'] : 0;
        $imageId = isset($_POST['image_id']) ? (int)$_POST['image_id'] : 0;
        $component = $this->findComponent($componentId);
        if ($component === null) {
            $this->abort404();
        }

        Image::markAsPrimary($imageId, (int)$component['user_id'], $componentId);

        AuditLog::record(
            (int)$component['user_id'],
            'images',
            $imageId,
            'set_cover',
            ['component_id' => $componentId],
            $this->clientIp(),
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        $this->flash('success', 'Imagem definida como capa.');
        $this->redirect('/components/view?id=' . $componentId . '#imagens');
    }

    public function uploadDatasheet(): void
    {
        Auth::requireAuth();

        if (!$this->verifyCsrf('components_datasheet', $_POST['_token'] ?? null)) {
            $this->flash('error', 'Sess+ï¿½o expirada.');
            $this->redirect('/components');
        }

        $componentId = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        $component = $this->findComponent($componentId);
        if ($component === null) {
            $this->abort404();
        }

        if (empty($_FILES['datasheet']) || $_FILES['datasheet']['error'] !== UPLOAD_ERR_OK) {
            $this->flash('error', 'Nenhum arquivo v+ï¿½lido foi enviado.');
            $this->redirect('/components/view?id=' . $componentId . '#arquivos');
        }

        $file = $_FILES['datasheet'];
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']) ?: '';
        if ($mime !== 'application/pdf') {
            $this->flash('error', 'Apenas arquivos PDF s+ï¿½o permitidos.');
            $this->redirect('/components/view?id=' . $componentId . '#arquivos');
        }

        $this->ensureUploadsDirectory();

        $hashName = bin2hex(random_bytes(16)) . '.pdf';
        $destination = rtrim(self::STORAGE_PATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $hashName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $this->flash('error', 'Falha ao salvar o arquivo.');
            $this->redirect('/components/view?id=' . $componentId . '#arquivos');
        }

        if (!empty($component['datasheet_path'])) {
            $old = rtrim(self::STORAGE_PATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $component['datasheet_path'];
            if (is_file($old)) {
                @unlink($old);
            }
        }

        Component::updateFields($componentId, (int)$component['user_id'], [
            'datasheet_path' => $hashName,
        ]);

        AuditLog::record(
            (int)$component['user_id'],
            'components',
            $componentId,
            'upload_datasheet',
            ['datasheet_path' => $hashName],
            $this->clientIp(),
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );

        $this->flash('success', 'Datasheet atualizado.');
        $this->redirect('/components/view?id=' . $componentId . '#arquivos');
    }

    public function downloadDatasheet(): void
    {
        Auth::requireAuth();
        $component = $this->resolveComponentFromQuery();
        if ($component === null || empty($component['datasheet_path'])) {
            $this->abort404();
        }

        $path = rtrim(self::STORAGE_PATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $component['datasheet_path'];
        if (!is_file($path)) {
            $this->abort404();
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($component['datasheet_path']) . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function serveImage(): void
    {
        Auth::requireAuth();
        $imageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $userId = Auth::userId();
        if ($imageId <= 0 || $userId === null) {
            $this->abort404();
        }

        $image = Image::find($imageId, $userId);
        if ($image === null) {
            $this->abort404();
        }

        $path = rtrim(self::STORAGE_PATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $image['path'];
        if (!is_file($path)) {
            $this->abort404();
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($path) ?: 'image/jpeg';
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }
    private function resolveComponentFromQuery(): ?array
    {
        $componentId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        return $this->findComponent($componentId);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function findComponent(int $componentId): ?array
    {
        if ($componentId <= 0) {
            return null;
        }

        $userId = Auth::userId();
        if ($userId === null) {
            return null;
        }

        return Component::findById($componentId, $userId);
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<int, string>}
     */
    private function validateComponentInput(array $input, bool $isUpdate = false): array
    {
        $errors = [];

        $Nãome = trim((string)($input['Nãome'] ?? ''));
        if ($Nãome === '') {
            $errors[] = 'Informe o Nãome do componente.';
        }

        $sku = trim((string)($input['sku'] ?? ''));
        if ($sku === '') {
            $errors[] = 'Informe o SKU.';
        }

        $quantidade = max(0, (int)($input['quantidade'] ?? 0));
        $minEstoque = max(0, (int)($input['min_estoque'] ?? 0));

        $custoUnitario = (float)($input['custo_unitario'] ?? 0);
        $precoMedioRaw = $input['preco_medio'] ?? null;
        $precoMedio = $precoMedioRaw !== null && $precoMedioRaw !== '' ? (float)$precoMedioRaw : null;

        if ($custoUnitario < 0) {
            $errors[] = 'Custo unit+ï¿½rio inv+ï¿½lido.';
        }

        if ($precoMedio !== null && $precoMedio < 0) {
            $errors[] = 'Pre+ï¿½o m+ï¿½dio inv+ï¿½lido.';
        }

        $data = [
            'Nãome' => $Nãome,
            'sku' => $sku,
            'fabricante' => $this->nullable($input['fabricante'] ?? null),
            'cod_fabricante' => $this->nullable($input['cod_fabricante'] ?? null),
            'descricao' => $this->nullable($input['descricao'] ?? null),
            'categoria' => $this->nullable($input['categoria'] ?? null),
            'tags' => $this->nullable($input['tags'] ?? null),
            'quantidade' => $quantidade,
            'unidade' => $input['unidade'] ?? 'un',
            'localizacao' => $this->nullable($input['localizacao'] ?? null),
            'tolerancia' => $this->nullable($input['tolerancia'] ?? null),
            'potencia' => $this->nullable($input['potencia'] ?? null),
            'tensao_max' => $this->nullable($input['tensao_max'] ?? null),
            'footprint' => $this->nullable($input['footprint'] ?? null),
            'custo_unitario' => $custoUnitario,
            'preco_medio' => $precoMedio,
            'min_estoque' => $minEstoque,
            'datasheet_path' => $input['datasheet_path'] ?? null,
        ];

        return [$data, $errors];
    }

    /**
     * @param array<string, mixed> $current
     * @param array<string, mixed> $new
     * @return array<string, mixed>
     */
    private function diffComponent(array $current, array $new): array
    {
        $diff = [];
        foreach ($new as $key => $value) {
            if (!array_key_exists($key, $current)) {
                continue;
            }

            if ($current[$key] === $value) {
                continue;
            }

            $diff[$key] = [
                'old' => $current[$key],
                'new' => $value,
            ];
        }

        return $diff;
    }

    private function verifyCsrf(string $key, ?string $token): bool
    {
        return Csrf::verify($key, $token);
    }

    private function nullable(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }
        $trimmed = trim((string)$value);
        return $trimmed === '' ? null : $trimmed;
    }

    private function clientIp(): ?string
    {
        return $_SERVER['REMOTE_ADDR'] ?? null;
    }

    private function ensureUploadsDirectory(): void
    {
        if (!is_dir(self::STORAGE_PATH)) {
            if (!mkdir(self::STORAGE_PATH, 0755, true) && !is_dir(self::STORAGE_PATH)) {
                throw new RuntimeException('N+ï¿½o foi poss+ï¿½vel criar diret+ï¿½rio de uploads.');
            }
        }
    }

    private function abort404(): void
    {
        http_response_code(404);
        echo 'Recurso n+ï¿½o encontrado.';
        exit;
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

    /**
     * @param array<string, mixed> $payload
     */
    private function jsonResponse(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload, JSON_THROW_ON_ERROR);
        exit;
    }
}




