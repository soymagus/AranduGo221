# Informe de pruebas · Arandu Go 2.1.0

Fecha: 2026-09-09

## Base y preservación

- Base autoritativa: `AranduGo-2.0.2-install.zip`.
- SHA-256 comprobado: `160405ebe137b78deeda4d18dd3d4b14bc0d116f63a0f8b61f30cf76d15a578a`.
- Se preservaron rutas protegidas, actualización transaccional, backup preventivo y rollback del panel.

## Controles ejecutados

- Sintaxis JavaScript de la aplicación y de la capa 2.1.0.
- JSON válido para manifiesto y contenido demo.
- Presencia de Jodit local y recursos demo.
- Presencia y checksum del icono URLGO adjunto.
- Seis secciones libres en panel, datos predeterminados, menús y render público.
- Video y audio HTML5, YouTube e imágenes en el render público.
- Lista blanca MIME y límites de carga multimedia.
- Desafíos matemáticos con identificador único y vencimiento.
- Controles de visibilidad del footer y colores de WhatsApp.
- Estructura segura de los ZIP, sin rutas absolutas ni recorridos `..`.

## Limitación del entorno

El entorno de construcción no incluye intérprete PHP ni MySQL. Por ello se ejecutaron controles estáticos y de estructura; la validación final en cPanel debe incluir instalación limpia, actualización desde 2.0.2, envío SMTP/mail y restauración real sobre una base de prueba.
