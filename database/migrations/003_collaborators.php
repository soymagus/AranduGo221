<?php
declare(strict_types=1);
return static function(PDO $pdo,string $prefix):void{
    $pdo->exec("ALTER TABLE `{$prefix}users` MODIFY `role` VARCHAR(30) NOT NULL DEFAULT 'collaborator'");
};
