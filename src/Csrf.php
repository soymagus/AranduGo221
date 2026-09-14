<?php
declare(strict_types=1);

namespace AranduGo;

final class Csrf
{
    public static function token(): string
    {
        if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf'];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="csrf" value="' . Support::e(self::token()) . '">';
    }

    public static function verify(): void
    {
        $token = (string) ($_POST['csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ''));
        if (!hash_equals((string) ($_SESSION['csrf'] ?? ''), $token)) {
            Support::json(['ok' => false, 'error' => 'Sesión vencida o solicitud inválida.'], 419);
        }
    }
}
