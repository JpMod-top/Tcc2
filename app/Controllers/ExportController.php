<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Models\Component;
use App\Core\View;

class ExportController
{
    public function index(): void
    {
        Auth::requireAuth();
        View::render('export/index', [
            'title' => 'Exportar dados',
            'csrfToken' => Csrf::token('export_csv'),
        ]);
    }

    public function exportCsv(): void
    {
        Auth::requireAuth();

        if (!$this->verifyCsrf('export_csv', $_POST['_token'] ?? null)) {
            $this->redirect('/export');
        }

        $userId = Auth::userId();
        if ($userId === null) {
            $this->redirect('/login');
        }

        $rows = Component::allForExport($userId);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="componentes.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, [
            'nome', 'sku', 'fabricante', 'cod_fabricante', 'descricao', 'categoria', 'tags',
            'quantidade', 'unidade', 'localizacao', 'tolerancia', 'potencia', 'tensao_max',
            'footprint', 'custo_unitario', 'preco_medio', 'min_estoque',
        ]);

        foreach ($rows as $row) {
            fputcsv($output, [
                $row['nome'] ?? '',
                $row['sku'] ?? '',
                $row['fabricante'] ?? '',
                $row['cod_fabricante'] ?? '',
                $row['descricao'] ?? '',
                $row['categoria'] ?? '',
                $row['tags'] ?? '',
                $row['quantidade'] ?? 0,
                $row['unidade'] ?? 'un',
                $row['localizacao'] ?? '',
                $row['tolerancia'] ?? '',
                $row['potencia'] ?? '',
                $row['tensao_max'] ?? '',
                $row['footprint'] ?? '',
                $row['custo_unitario'] ?? 0,
                $row['preco_medio'] ?? '',
                $row['min_estoque'] ?? 0,
            ]);
        }

        fclose($output);
        exit;
    }

    private function verifyCsrf(string $key, ?string $token): bool
    {
        return Csrf::verify($key, $token);
    }

    private function redirect(string $location): void
    {
        header('Location: ' . $location, true, 302);
        exit;
    }
}
