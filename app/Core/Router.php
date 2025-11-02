<?php

declare(strict_types=1);

namespace App\Core;

use Closure;
use RuntimeException;

class Router
{
    /**
     * @var array<string, array<string, callable|array<int, mixed>|string>>
     */
    private array $routes = [
        'GET' => [],
        'POST' => [],
    ];

    /**
     * @param callable|array<int, mixed>|string $handler
     */
    public function get(string $path, callable|array|string $handler): void
    {
        $this->addRoute('GET', $path, $handler);
    }

    /**
     * @param callable|array<int, mixed>|string $handler
     */
    public function post(string $path, callable|array|string $handler): void
    {
        $this->addRoute('POST', $path, $handler);
    }

    /**
     * @param callable|array<int, mixed>|string $handler
     */
    private function addRoute(string $method, string $path, callable|array|string $handler): void
    {
        $normalizedPath = $this->normalizePath($path);
        $this->routes[$method][$normalizedPath] = $handler;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = $this->normalizePath(parse_url($uri, PHP_URL_PATH) ?: '/');

        $handler = $this->routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo '404 Not Found';
            return;
        }

        $response = $this->invokeHandler($handler);

        if ($response === null) {
            return;
        }

        if (is_string($response)) {
            echo $response;
            return;
        }

        if (is_array($response) || is_object($response)) {
            header('Content-Type: application/json');
            echo json_encode($response, JSON_THROW_ON_ERROR);
        }
    }

    /**
     * @param callable|array<int, mixed>|string $handler
     */
    private function invokeHandler(callable|array|string $handler): mixed
    {
        if ($handler instanceof Closure) {
            return $handler();
        }

        if (is_array($handler) && count($handler) === 2) {
            [$class, $method] = $handler;
            $instance = is_string($class) ? new $class() : $class;
            if (!method_exists($instance, (string)$method)) {
                throw new RuntimeException('Controller method not found.');
            }

            return $instance->{$method}();
        }

        if (is_string($handler) && function_exists($handler)) {
            return $handler();
        }

        if (is_callable($handler)) {
            return $handler();
        }

        throw new RuntimeException('Invalid route handler.');
    }

    private function normalizePath(string $path): string
    {
        $trimmed = '/' . trim($path, '/');
        return $trimmed === '//' ? '/' : $trimmed;
    }
}