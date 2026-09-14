<?php
declare(strict_types=1);
return static function(PDO $pdo,string $prefix):void{
 $pdo->exec("CREATE TABLE IF NOT EXISTS `{$prefix}migrations` (migration VARCHAR(190) PRIMARY KEY, executed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
 $pdo->exec("CREATE TABLE IF NOT EXISTS `{$prefix}update_history` (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, from_version VARCHAR(30) NOT NULL, to_version VARCHAR(30) NOT NULL, status VARCHAR(30) NOT NULL, backup_file VARCHAR(255) NULL, details TEXT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
};
