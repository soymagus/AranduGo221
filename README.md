# Arandu Go PHP para cPanel

Versión estable: **2.2.1**

Edición independiente de Arandu Go, diseñada para PHP 8.1+, MySQL/MariaDB y almacenamiento local. No modifica ni reemplaza la aplicación original basada en Workers.

La versión 2.2.1 conserva la base estable 2.2.0 y sus flujos de instalación, actualización, rollback, backup y restauración. Integra permanentemente los ajustes visuales del panel y del contacto público, y corrige la visibilidad granular del footer sin alterar los datos existentes.

La documentación detallada se encuentra en `docs/`.

## Instalación preliminar

1. Subir el contenido de esta carpeta al document root del dominio o subdominio.
2. Verificar permisos de escritura en `config`, `storage` y `uploads` durante la instalación.
3. Abrir `/install/` y completar el asistente.
4. Al finalizar, ingresar en `/dashboardcliente/` con el administrador creado.

## Actualizar una instalación existente

El mismo ZIP sirve para instalar y actualizar. Extraelo en una subcarpeta temporal dentro de `public_html` y abrí `/subcarpeta/update/`. El asistente detecta la instalación superior, solicita una cuenta administradora, crea un respaldo y aplica solamente archivos de aplicación y migraciones pendientes. No reemplaza `config/config.php`, `uploads`, registros, respaldos ni datos del cliente.

Después de esta primera actualización, los siguientes paquetes se pueden cargar desde **Panel de control → Configuración avanzada → Actualizaciones**. Si una actualización falla, el sistema restaura los archivos respaldados y conserva el ZIP de seguridad en `storage/backups`.

PHPMailer y el cargador de clases ya están incluidos: el paquete instalable no necesita Composer ni acceso SSH.

El wizard puede conectarse a una base previamente creada o intentar crear base, usuario y permisos mediante la API UAPI de cPanel. El token de cPanel se utiliza solamente durante el proceso y no se guarda.

## Correo

La configuración protegida admite:

- PHP `mail()`.
- Sendmail local.
- SMTP propio, incluido el SMTP de una cuenta del dominio o Gmail con contraseña de aplicación.

## Seguridad y mantenimiento

- Eliminá o renombrá la carpeta `install` después de confirmar la instalación; además queda bloqueada por `storage/installed.lock`.
- Conservá fuera de copias públicas `config/config.php`, que contiene la contraseña MySQL, SMTP y la clave secreta reCAPTCHA.
- La clave pública reCAPTCHA se define en el dashboard; la secreta se define durante la instalación.
- Hacé respaldos desde Configuración avanzada y también desde el sistema de copias de cPanel.

## Funciones incluidas

Página modular responsive, borrador/publicación, teléfonos diferenciados, galería de 24 imágenes con paginación real, servicios, redes con SVG locales, URLGO.me, cuatro secciones libres con editor visual, texto/imagen/YouTube, páginas legales, mapa, formulario con registro rotativo, CSS/analítica aislados, importación JSON/CSV/XLSX, exportación JSON/CSV/TXT y respaldo ZIP sanitizado. Incluye administración de hasta seis colaboradores con permisos restringidos.
