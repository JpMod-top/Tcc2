<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;
use App\Models\Component;

class ImportController
{
    private const SESSION_KEY = '_import_rows';

    public function index(): void
    {
        Auth::requireAuth();
        View::render('import/index', [
            'title' => 'Importar CSV',
            'csrfToken' => Csrf::token('import_preview'),
            'hasPending' => !empty($_SESSION[self::SESSION_KEY]),
        ]);
    }

    public function preview(): void
    {
        Auth::requireAuth();

        if (!$this->verifyCsrf('import_preview', $_POST['_token'] ?? null)) {
            $this->flash('error', 'Sessao expirada.');
            $this->redirect('/import');
        }

        if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $this->flash('error', 'Arquivo invalido.');
            $this->redirect('/import');
        }

        $file = $_FILES['file']['tmp_name'];
        $rows = $this->parseCsv($file);

        if (empty($rows)) {
            $this->flash('error', 'Nao foi possivel ler dados validos do CSV.');
            $this->redirect('/import');
        }

        $_SESSION[self::SESSION_KEY] = $rows;

        $preview = array_slice($rows, 0, 5);

        View::render('import/preview', [
            'title' => 'Pre-visualizacao da importacao',
            'preview' => $preview,
            'total' => count($rows),
            'csrfProcess' => Csrf::token('import_process'),
        ]);
    }

    public function process(): void
    {
        Auth::requireAuth();

        if (!$this->verifyCsrf('import_process', $_POST['_token'] ?? null)) {
            $this->flash('error', 'Sessao expirada.');
            $this->redirect('/import');
        }

        $userId = Auth::userId();

        $rows = $_SESSION[self::SESSION_KEY] ?? [];
        unset($_SESSION[self::SESSION_KEY]);

        if (empty($rows)) {
            $this->flash('error', 'Nenhuma linha para importar.');
            $this->redirect('/import');
        }

        $updateExisting = !empty($_POST['update_existing']);

        $result = Component::bulkUpsert($userId, $rows, $updateExisting);

        if ($result['errors'] !== []) {
            foreach ($result['errors'] as $error) {
                $this->flash('warning', $error);
            }
        }

        if ($result['inserted'] > 0) {
            $this->flash('success', "{$result['inserted']} componentes inseridos.");
        }

        if ($result['updated'] > 0) {
            $this->flash('info', "{$result['updated']} componentes atualizados.");
        }

        $this->redirect('/components');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function parseCsv(string $file): array
    {
        $handle = fopen($file, 'r');
        if ($handle === false) {
            return [];
        }

        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            return [];
        }

        $delimiter = str_contains($firstLine, ';') ? ';' : ',';
        rewind($handle);

        $header = fgetcsv($handle, 0, $delimiter);
        if ($header === false) {
            fclose($handle);
            return [];
        }

        $header = array_map(
            static fn($value) => strtolower(trim((string)$value, " \t\n\r\0\x0B\xEF\xBB\xBF")),
            $header
        );

        if (!in_array('sku', $header, true) || !in_array('nome', $header, true)) {
            fclose($handle);
            return [];
        }

        $rows = [];
        $rowCount = 0;

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($data === [null] || $data === []) {
                continue;
            }

            if (count($data) !== count($header)) {
                continue;
            }

            $row = [];
            foreach ($header as $index => $column) {
                if ($column === '') {
                    continue;
                }

                $value = isset($data[$index]) ? trim((string)$data[$index]) : null;
                $row[$column] = $value === '' ? null : $value;
            }

            if (($row['sku'] ?? null) === null && ($row['nome'] ?? null) === null) {
                continue;
            }

            $rows[] = $row;
            $rowCount++;

            if ($rowCount >= 500) {
                break;
            }
        }

        fclose($handle);

        return $rows;
    }

    private function verifyCsrf(string $key, ?string $token): bool
    {
        return Csrf::verify($key, $token);
    }

    private function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][$type][] = $message;
    }

    private function redirect(string $location): void
    {
        header('Location: ' . $location, true, 302);
        exit;
    }
}

