<?php
declare(strict_types=1);

$root = __DIR__;
if (!is_file($root . '/storage/installed.lock') && !str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/install/')) {
    header('Location: install/'); exit;
}
require $root . '/vendor/autoload.php';
\AranduGo\Auth::start();
$config = \AranduGo\Support::config();
date_default_timezone_set($config['app']['timezone'] ?? 'UTC');
