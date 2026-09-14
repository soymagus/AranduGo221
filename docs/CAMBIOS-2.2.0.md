# Arandu Go 2.2.0

## Galería del panel

- Nueva estructura administrativa `.ag-admin-gallery-*`, independiente de la galería pública.
- Filas cerradas de aproximadamente 108 px con miniaturas de 120 × 90 px.
- Edición horizontal: referencia visual izquierda de hasta 220 px y formulario completo a la derecha.
- Acordeón exclusivo, sin pérdida de valores al abrir o cerrar.
- Conserva URL, título, descripción, CTA, enlace, visibilidad, reemplazo, eliminación, guardado y orden.
- Carga múltiple hasta completar el límite global de 24; cada fotografía genera una fila cerrada.
- Adaptación móvil con imagen reducida y formulario apilado.

## CSS adicional

- `CSS adicional del sitio` conserva el comportamiento público anterior.
- `CSS adicional del panel` se guarda separadamente como `customCode.dashboardCss`.
- El CSS administrativo se carga solamente en `dashboardcliente`, después de los estilos nativos.
- Guardar utiliza el borrador persistente habitual; Restablecer elimina únicamente la personalización del panel.
- El campo forma parte de exportaciones, backups y restauraciones al estar almacenado en el perfil JSON.

## Compatibilidad

La versión se construyó sobre 2.1.2. No modifica el marcado, CSS ni JavaScript de la galería pública. Mantiene actualización transaccional, rollback, captcha, formulario, seis contenidos libres, multimedia, Jodit, roles y datos existentes.
