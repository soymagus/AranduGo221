# Informe histórico de base · Arandu Go 2.0.1

Este documento conserva la evidencia de la base congelada. Para la entrega actual consulte `INFORME-PRUEBAS-2.0.2.md` y `MATRIZ-REGRESION.md`.

## Pruebas completadas

- Integridad CRC de las fuentes 1.0.15 y 1.0.24.
- Comparación recursiva de estructura y archivos.
- Validación JSON de manifiestos.
- Validación sintáctica de JavaScript con Node.
- Ausencia de enlaces simbólicos y uso de `DOCUMENT_ROOT`.
- Revisión de rutas relativas para raíz y subcarpetas.
- Tipo inequívoco de paquetes `install`, `update` y `rollback`.
- Presencia de las cuatro secciones libres, colaboradores, menú, footer, galería, formulario e importación/exportación.
- Demo ficticia con `example.invalid`, teléfono no operativo, noindex, formulario bloqueado y video requerido.
- Selector izquierda/derecha persistente para multimedia de cada sección libre.
- Secciones libres sin multimedia a una columna y ancho completo.
- Sincronización de visibilidad entre “Nosotros”, menú y las cuatro secciones libres.
- CSS aprobado de acciones inferiores y hamburguesa incorporado al estilo nativo.
- Backup con inventario/checksums y exclusión de credenciales, sesiones y hashes de contraseña.
- Restauración con límites, prevención de Zip Slip, rechazo de enlaces simbólicos/ejecutables, transacción de DB y respaldo preventivo.

## Pruebas que requieren servidor de aceptación

El entorno de construcción no incluye PHP ni MySQL. Por ello, las pruebas de instalación HTTP y persistencia deben ejecutarse en el cPanel objetivo antes de promoción a producción: raíz, `/panaderia`, `/nailstudio` y una cuarta carpeta de restauración portable. La matriz de aceptación operativa incluida debajo debe marcarse con evidencia del servidor.

| Caso | Estado de construcción | Evidencia pendiente en cPanel |
|---|---|---|
| Instalación raíz y subcarpetas | Preparado | URL, captura y prefijo por instalación |
| Actualizar B sin afectar A/C | Preparado | hashes y conteos antes/después |
| Rollback C sin afectar A/B | Preparado | historial y hashes |
| Backup/restauración B | Preparado | manifiesto y resultado |
| Copia portable a cuarta carpeta | Preparado | URLs y archivos adaptados |
| Roles administrador/colaborador | Conservado de 1.0.24 | sesión de prueba |
| Escritorio y móvil | Conservado de 1.0.24 | capturas navegadores |

No se declara aprobada la ejecución en cPanel hasta completar esta última tabla en un servidor PHP 8.1+ con MySQL y ZIP.
