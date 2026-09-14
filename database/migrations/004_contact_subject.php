<?php
return static function(PDO $pdo,string $prefix):void{
    $table=$prefix.'contact_messages';
    $stmt=$pdo->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND COLUMN_NAME=?');
    $stmt->execute([$table,'subject']);
    if((int)$stmt->fetchColumn()===0)$pdo->exec("ALTER TABLE `{$table}` ADD COLUMN `subject` VARCHAR(180) NOT NULL DEFAULT '' AFTER `phone`");
};
