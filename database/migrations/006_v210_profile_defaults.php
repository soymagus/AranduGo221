<?php
declare(strict_types=1);

/* Los perfiles se normalizan de forma no destructiva desde el panel.
 * Esta migración marca el cambio de esquema sin alterar JSON ni datos existentes. */
return static function (PDO $pdo, string $prefix): void {
    /* Intencionalmente sin DDL: el perfil se guarda como JSON y se amplía
       sin reemplazar valores anteriores. La firma conserva el contrato. */
};
