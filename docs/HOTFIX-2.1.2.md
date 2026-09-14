# Hotfix Arandu Go 2.1.2

Corrige el error `There is no active transaction` observado al aplicar 2.1.1 sobre una instalación donde las migraciones DDL anteriores todavía no estaban registradas.

MySQL confirma implícitamente operaciones como `CREATE TABLE` y `ALTER TABLE`. El actualizador ahora consulta `PDO::inTransaction()` antes de ejecutar `commit()` o `rollBack()`. Se mantienen el registro de migraciones, el backup preventivo y la restauración automática ante cualquier error.

La actualización puede aplicarse directamente desde 2.1.0 después del intento fallido; no requiere restauración manual.
