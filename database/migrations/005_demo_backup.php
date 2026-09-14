<?php
return static function(PDO $pdo,string $prefix):void{
    if(!preg_match('/^[A-Za-z0-9_]+$/',$prefix))throw new RuntimeException('Prefijo inválido.');
    $pdo->exec("ALTER TABLE `{$prefix}update_history` MODIFY details MEDIUMTEXT NULL");
};
