<?php
declare(strict_types=1);

namespace AranduGo;

final class Support
{
    public static function basePath(string $path = ''): string
    {
        return dirname(__DIR__) . ($path !== '' ? '/' . ltrim($path, '/') : '');
    }

    public static function config(): array
    {
        static $config;
        if ($config === null) {
            $file = self::basePath('config/config.php');
            if (!is_file($file)) {
                throw new \RuntimeException('Arandu Go todavía no está instalado.');
            }
            $config = require $file;
        }
        return $config;
    }

    public static function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function url(string $path = ''): string
    {
        return rtrim((string) self::config()['app']['url'], '/') . '/' . ltrim($path, '/');
    }

    public static function redirect(string $path): never
    {
        header('Location: ' . self::url($path));
        exit;
    }

    public static function json(array $payload, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
}
