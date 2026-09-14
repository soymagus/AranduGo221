<?php
declare(strict_types=1);

namespace AranduGo;

use PDO;

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) return self::$connection;
        $db = Support::config()['database'];
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $db['host'], $db['port'], $db['name'], $db['charset']);
        self::$connection = new PDO($dsn, $db['user'], $db['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return self::$connection;
    }

    public static function table(string $name): string
    {
        $prefix = (string) Support::config()['database']['prefix'];
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $prefix . $name)) throw new \InvalidArgumentException('Nombre de tabla inválido');
        return '`' . $prefix . $name . '`';
    }
}
