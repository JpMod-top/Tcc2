<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\View;
use App\Models\Component;

class DashboardController
{
    public function index(): void
    {
        Auth::requireAuth();
        $userId = Auth::userId();
        if ($userId === null) {
            header('Location: /login', true, 302);
            exit;
        }

        $summary = Component::dashboardSummary($userId);
        $recent = Component::recent($userId, 5);
        $valueByCategory = array_slice(Component::valueByCategory($userId), 0, 5);

        View::render('dashboard/index', [
            'title' => 'Dashboard',
            'summary' => $summary,
            'recent' => $recent,
            'valueByCategory' => $valueByCategory,
        ]);
    }
}
