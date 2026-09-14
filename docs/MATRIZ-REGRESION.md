# Matriz de regresión · 2.0.1 → 2.0.2

Estados: `OK ESTÁTICO`, `PENDIENTE CPANEL`, `CONSERVADO`, `CREADO`, `CORREGIDO`.

| Área | Tratamiento | Estado | Evidencia / validación |
|---|---|---|---|
| Instalador raíz y subcarpeta | Conservar | PENDIENTE CPANEL | Detección relativa y URL sugerida conservadas |
| Base y prefijo exclusivo | Conservar | OK ESTÁTICO | Rechazo de prefijo ocupado presente |
| Login, sesiones y CSRF | Conservar | CONSERVADO | Módulos sin reescritura |
| Guardar y publicar | Conservar | CONSERVADO | Mismo endpoint y modelo de datos |
| Backup y restauración ZIP | Conservar | CONSERVADO | Archivos y controles presentes |
| Actualización y rollback inferior | Conservar | CONSERVADO | Opción, confirmación `ROLLBACK` y respaldo preventivo presentes |
| Jodit local en libres 1–4 | Crear | CREADO | Assets locales 4.14.6 y sincronización previa al guardado |
| Jodit en introducción de galería | Crear | CREADO | Campo, editor, persistencia y salida condicional |
| Saneamiento HTML | Corregir | OK ESTÁTICO | Allowlist de etiquetas, atributos, estilos y esquemas |
| Tarjetas colapsables | Crear | CREADO | Una abierta, sesión, badges y cierre de bloque |
| Accesos y filtros de Contenidos | Crear | CREADO | Todos/Activos/Inactivos/Incompletos |
| Asistente de 11 pasos | Crear | CREADO | Reanudar, omitir, anterior, guardar y salir |
| Sin publicación automática | Conservar | OK ESTÁTICO | El asistente invoca guardado de borrador |
| Galería hasta 24 imágenes | Conservar | CONSERVADO | Límite y editor existentes |
| Imagen lateral equilibrada | Corregir | CORREGIDO | Grid escritorio; apilado móvil; `object-fit` |
| Ajuste y foco multimedia | Crear | CREADO | 3 ajustes y 5 puntos de enfoque |
| YouTube responsive 16:9 | Conservar | OK ESTÁTICO | Regla de aspecto independiente |
| URLGO.me oficial | Corregir | CORREGIDO | PNG local y contenedor social uniforme |
| Demo Horizonte | Crear | CREADO | JSON, logo, portada, Valentina, FAQ y 4 fotos |
| Demo noindex y formulario bloqueado | Crear | OK ESTÁTICO | Reglas de repositorio y datos iniciales |
| Instalación vacía sin ficticios | Corregir | OK ESTÁTICO | Limpieza explícita de contenido inicial |
| CSS móvil aprobado | Conservar | CONSERVADO | Reglas distribuidas en assets del producto |
| Header, footer y páginas legales | Conservar | CONSERVADO | Renderizadores compartidos sin reescritura |
| Formulario, asunto, captcha y log | Conservar | CONSERVADO | Componentes existentes preservados |
| Mapas y normalización YouTube | Conservar | CONSERVADO | `MediaNormalizer` sin reescritura |
| Vista pública sin cambios ajenos | Conservar | PENDIENTE CPANEL | Requiere comparación visual servida |
| Responsive y navegadores actuales | Corregir | PENDIENTE CPANEL | Requiere QA visual real |
