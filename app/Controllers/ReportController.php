<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\View;
use App\Models\Component;
use App\Models\StockMove;
use DateInterval;
use DateTimeImmutable;

class ReportController
{
    public function index(): void
    {
        Auth::requireAuth();
        $userId = Auth::userId();
        if ($userId === null) {
            $this->redirect('/login');
        }

        $summary = Component::dashboardSummary($userId);

        View::render('reports/index', [
            'title' => 'Relatórios',
            'summary' => $summary,
            'csrfMoves' => Csrf::token('reports_moves_export'),
        ]);
    }

    public function lowStock(): void
    {
        Auth::requireAuth();
        $userId = Auth::userId();
        if ($userId === null) {
            $this->redirect('/login');
        }

        View::render('reports/low_stock', [
            'title' => 'Componentes abaixo do mínimo',
            'items' => Component::lowStock($userId),
        ]);
    }

    public function zeroed(): void
    {
        Auth::requireAuth();
        $userId = Auth::userId();
        if ($userId === null) {
            $this->redirect('/login');
        }

        View::render('reports/zeroed', [
            'title' => 'Componentes zerados',
            'items' => Component::zeroed($userId),
        ]);
    }

    public function valueByCategory(): void
    {
        Auth::requireAuth();
        $userId = Auth::userId();
        if ($userId === null) {
            $this->redirect('/login');
        }

        View::render('reports/value_by_category', [
            'title' => 'Valor total por categoria',
            'items' => Component::valueByCategory($userId),
        ]);
    }

    public function moves(): void
    {
        Auth::requireAuth();
        $userId = Auth::userId();
        if ($userId === null) {
            $this->redirect('/login');
        }

        $defaultFrom = (new DateTimeImmutable('now'))->sub(new DateInterval('P30D'));
        $defaultTo = new DateTimeImmutable('now');

        $fromInput = $_GET['from'] ?? $defaultFrom->format('Y-m-d');
        $toInput = $_GET['to'] ?? $defaultTo->format('Y-m-d');

        $from = $this->parseDate($fromInput) ?? $defaultFrom;
        $to = $this->parseDate($toInput) ?? $defaultTo;

        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        $componentId = isset($_GET['component_id']) ? (int)$_GET['component_id'] : null;
        if ($componentId !== null && $componentId <= 0) {
            $componentId = null;
        }

        $moves = StockMove::listForPeriod($userId, $from, $to, $componentId);
        $totals = StockMove::totalsByType($userId, $from, $to);

        View::render('reports/moves', [
            'title' => 'Movimentações',
            'moves' => $moves,
            'totals' => $totals,
            'filters' => [
                'from' => $from->format('Y-m-d'),
                'to' => $to->format('Y-m-d'),
                'component_id' => $componentId,
            ],
            'components' => Component::allForExport($userId),
            'csrfExport' => Csrf::token('reports_moves_export'),
        ]);
    }

    public function exportMovesCsv(): void
    {
        Auth::requireAuth();
        if (!$this->verifyCsrf('reports_moves_export', $_POST['_token'] ?? null)) {
            $this->redirect('/reports/moves');
        }

        $userId = Auth::userId();
        if ($userId === null) {
            $this->redirect('/login');
        }

        $from = $this->parseDate($_POST['from'] ?? '') ?? new DateTimeImmutable('first day of this month');
        $to = $this->parseDate($_POST['to'] ?? '') ?? new DateTimeImmutable('now');
        if ($from > $to) {
            [$from, $to] = [$to, $from];
        }

        $componentId = isset($_POST['component_id']) ? (int)$_POST['component_id'] : null;
        if ($componentId !== null && $componentId <= 0) {
            $componentId = null;
        }

        $moves = StockMove::listForPeriod($userId, $from, $to, $componentId);

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="movimentacoes.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Data', 'Tipo', 'Componente', 'Quantidade', 'Motivo']);

        $components = [];
        foreach (Component::allForExport($userId) as $component) {
            $components[$component['id']] = $component['nome'];
        }

        foreach ($moves as $move) {
            $componentName = $components[$move['component_id']] ?? ('ID ' . $move['component_id']);
            fputcsv($output, [
                $move['created_at'],
                $move['tipo'],
                $componentName,
                $move['quantidade'],
                $move['motivo'],
            ]);
        }

        fclose($output);
        exit;
    }

    private function parseDate(string $value): ?DateTimeImmutable
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);
        return $date ?: null;
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
