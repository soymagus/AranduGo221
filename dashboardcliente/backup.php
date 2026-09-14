<?php
require dirname(__DIR__).'/bootstrap.php';
use AranduGo\Auth;use AranduGo\BackupManager;use AranduGo\Support;
Auth::requireAdmin();
try{$file=(new BackupManager(Support::basePath(),Support::config()))->create();header('Content-Type: application/zip');header('X-Content-Type-Options: nosniff');header('Content-Disposition: attachment; filename="'.basename($file).'"');header('Content-Length: '.filesize($file));readfile($file);unlink($file);exit;}catch(Throwable $e){http_response_code(500);exit('No se pudo generar el respaldo completo: '.Support::e($e->getMessage()));}
