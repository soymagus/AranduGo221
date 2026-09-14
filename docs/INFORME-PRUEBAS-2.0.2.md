# Informe de pruebas · Arandu Go 2.0.2

Fecha: 2026-09-09 UTC

## Resultado de construcción

- Fuente 2.0.1 congelada y verificada por SHA-256.
- Jodit Community 4.14.6 autohospedado, licencia MIT incluida.
- JavaScript validado sintácticamente con Node.js y 51 archivos PHP analizados sin errores mediante parser independiente.
- JSON demo validado y adaptado en tiempo de instalación.
- Recursos PNG requeridos presentes y no vacíos.
- ZIP de instalación validado mediante listado y prueba de integridad.
- Rutas absolutas de demo eliminadas para conservar instalaciones en subcarpetas.
- Referencias de versión, manifiesto y assets verificadas como 2.0.2.
- Controles existentes de backup, restauración, importación, actualización y rollback conservados.

## Seguridad verificada por revisión estática

- Saneamiento con lista permitida para estructura, tablas, alineación y colores.
- Eliminación de atributos de eventos y esquemas peligrosos.
- `iframe`, `object`, `embed`, `script` e imágenes arbitrarias no están permitidos en HTML enriquecido.
- YouTube continúa por el normalizador específico.
- Protección de ejecución PHP y scripts dentro de `uploads` conservada.
- Consultas preparadas, CSRF, autenticación y prefijo exclusivo conservados desde 2.0.1.

## Pruebas pendientes de entorno real

El entorno de construcción no incluye PHP/MySQL ni navegador PHP servido. Por ello, instalación real en dominio raíz/subcarpeta, envío SMTP, reCAPTCHA, backup-restauración y rollback deben ejecutarse en un cPanel de homologación antes de producción. Estas pruebas no se declaran aprobadas de forma ficticia.

## Criterio de entrega

El paquete supera controles estáticos y de integridad. Su promoción a producción queda condicionada a completar la matriz marcada como `PENDIENTE CPANEL`.
