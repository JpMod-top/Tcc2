<?php

declare(strict_types=1);

$flashes = $_SESSION['_flash'] ?? [];

if (!is_array($flashes) || empty($flashes)) {
    return;
}

unset($_SESSION['_flash']);

$toneMap = [
    'success' => 'border-green-500 bg-green-50 text-green-700 dark:border-green-400 dark:bg-green-900/40 dark:text-green-200',
    'error' => 'border-red-500 bg-red-50 text-red-700 dark:border-red-400 dark:bg-red-900/40 dark:text-red-200',
    'warning' => 'border-amber-500 bg-amber-50 text-amber-700 dark:border-amber-400 dark:bg-amber-900/40 dark:text-amber-100',
    'info' => 'border-blue-500 bg-blue-50 text-blue-700 dark:border-blue-400 dark:bg-blue-900/40 dark:text-blue-200',
];
?>
<div class="space-y-3">
    <?php foreach ($flashes as $type => $messages): ?>
        <?php
        $messages = (array)$messages;
        $classes = $toneMap[$type] ?? $toneMap['info'];
        ?>
        <?php foreach ($messages as $message): ?>
            <div class="flex items-start gap-3 rounded-lg border-l-4 px-4 py-3 text-sm shadow <?php echo $classes; ?>">
                <span class="font-semibold capitalize"><?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>:</span>
                <p><?php echo htmlspecialchars((string)$message, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>