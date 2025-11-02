<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

class View
{
    private const BASE_PATH = __DIR__ . '/../Views/';

    /**
     * @param array<string, mixed> $data
     */
    public static function render(string $view, array $data = [], ?string $layout = 'layouts/base'): void
    {
        $content = self::renderFile($view, $data);

        if ($layout === null) {
            echo $content;
            return;
        }

        $layoutData = array_merge($data, ['content' => $content]);
        echo self::renderFile($layout, $layoutData);
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function partial(string $view, array $data = []): void
    {
        echo self::renderFile($view, $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function renderFile(string $view, array $data): string
    {
        $path = self::resolvePath($view);

        if (!is_file($path)) {
            throw new RuntimeException('View not found: ' . $view);
        }

        extract($data, EXTR_SKIP);
        ob_start();
        include $path;

        return (string)ob_get_clean();
    }

    private static function resolvePath(string $view): string
    {
        $normalized = str_replace(['\\', '.'], '/', $view);

        return rtrim(self::BASE_PATH, DIRECTORY_SEPARATOR) . '/' . $normalized . '.php';
    }
}