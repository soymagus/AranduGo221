# Backup y restauración

En **Configuración avanzada → Portabilidad y respaldo** podés descargar un ZIP completo. Incluye borrador, publicación, tablas del prefijo, usuarios, registro y uploads, con manifiesto e integridad SHA-256. Excluye credenciales MySQL/SMTP, claves internas, sesiones y temporales.

Para restaurar, seleccioná el mismo ZIP, elegí componentes y escribí `RESTAURAR`. Se valida la estructura, se rechazan rutas peligrosas y ejecutables, y se crea un respaldo preventivo. La configuración protegida del destino nunca se reemplaza.
