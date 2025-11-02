<?php

declare(strict_types=1);

namespace App\Config;

use RuntimeException;

final class ComponentTypeStore
{
    private const FILE_PATH = __DIR__ . '/../../storage/component_types.json';

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        $path = self::ensureFile();
        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new RuntimeException('Unable to load custom component types.');
        }

        /** @var array<string, array<string, mixed>> $data */
        $data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        return $data;
    }

    /**
     * @param array<string, mixed> $definition
     */
    public static function add(string $typeName, array $definition): void
    {
        $typeName = trim($typeName);
        if ($typeName === '') {
            throw new RuntimeException('Tipo invalido.');
        }

        $all = self::all();
        if (isset($all[$typeName])) {
            throw new RuntimeException('Tipo ja existente.');
        }

        $all[$typeName] = $definition;

        $json = json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false) {
            throw new RuntimeException('Falha ao serializar tipos personalizados.');
        }

        $path = self::ensureFile();
        if (file_put_contents($path, $json, LOCK_EX) === false) {
            throw new RuntimeException('Nao foi possivel persistir o novo tipo.');
        }

        ComponentTypeRegistry::reload();
    }

    /**
     * Garantir que o arquivo exista.
     */
    private static function ensureFile(): string
    {
        $path = self::FILE_PATH;
        $dir = dirname($path);
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Nao foi possivel preparar diretório de tipos.');
        }

        if (!file_exists($path)) {
            $init = json_encode([], JSON_PRETTY_PRINT);
            if ($init === false || file_put_contents($path, $init, LOCK_EX) === false) {
                throw new RuntimeException('Nao foi possivel criar arquivo de tipos.');
            }
        }

        return $path;
    }
}

