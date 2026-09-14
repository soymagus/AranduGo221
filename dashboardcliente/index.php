<?php
require dirname(__DIR__).'/bootstrap.php';
use AranduGo\Auth;use AranduGo\Csrf;use AranduGo\SiteRepository;use AranduGo\Support;
Auth::requireLogin();header('Cache-Control: private, no-store, max-age=0');$data=SiteRepository::draft();if(trim((string)($data['business']['name']??''))===''||count($data['modules']??[])===0)$data=SiteRepository::published();$json=json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_INVALID_UTF8_SUBSTITUTE);if($json===false)$json='{}';$versionFile=Support::basePath('config/version.php');$versionInfo=is_file($versionFile)?(require $versionFile):[];$version=$versionInfo['version']??'1.0.0';$assetVersion=$versionInfo['asset_version']??$version;$build=$versionInfo['build']??'';$dashboardCss=preg_replace('~</?style[^>]*>~i','',(string)($data['customCode']['dashboardCss']??''));
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Panel de control · Arandu Go</title>
<link rel="stylesheet" href="<?=Support::url('assets/dashboard.css')?>?v=<?=Support::e((string)$assetVersion)?>">
<link rel="stylesheet" href="<?=Support::url('assets/dashboard-210.css')?>?v=<?=Support::e((string)$assetVersion)?>">
<link rel="stylesheet" href="<?=Support::url('assets/dashboard-211.css')?>?v=<?=Support::e((string)$assetVersion)?>">
<link rel="stylesheet" href="<?=Support::url('assets/dashboard-220.css')?>?v=<?=Support::e((string)$assetVersion)?>">
<link rel="stylesheet" href="<?=Support::url('assets/dashboard-221.css')?>?v=<?=Support::e((string)$assetVersion)?>">
<style id="agDashboardCustomCss"><?=$dashboardCss?></style>
</head>
<body>
<div class="shell">
<aside class="sidebar">
<div class="brand">
<span class="mark">A</span>
<div>
<strong>Arandu Go</strong>
<small>Panel de control</small>
</div>
</div>
<div class="version">Versión estable <?=Support::e((string)$assetVersion)?> PHP</div>
<nav>
<a class="active" href="#contenido">Mi página</a>
<a href="<?=Support::url()?>" target="_blank">Ver página pública</a>
</nav>
<div class="account">
<span>
<?=Support::e($_SESSION['name']??'Usuario')?>
</span>
<small>
<?=Support::e($_SESSION['role']??'')?>
</small>
<a href="<?=Support::url('dashboardcliente/logout.php')?>">Cerrar sesión</a>
</div>
</aside>
<main class="main">
<header class="top">
<div>
<small>Panel de control</small>
<h1>Tu página, siempre al día</h1>
</div>
<div class="actions">
<span class="status" id="status">Sin cambios</span>
<button class="btn" data-save="0">Guardar</button>
<button class="btn blue" data-save="1">Publicar</button>
</div>
</header>
<div class="tabs">
<button class="btn blue" data-tab="contenido">Contenido</button>
<button class="btn" data-tab="organizar">Organizar</button>
<button class="btn" data-tab="diseno">Diseño</button>
<button class="btn" data-tab="galeria">Galería</button>
<button class="btn" data-tab="formulario">Formulario</button>
<button class="btn" data-tab="avanzado">Configuración avanzada</button>
</div>
<section class="panel active" data-panel="contenido">
<article class="card">
<h2>Información general · Identidad y contacto</h2>
<div class="grid">
<label class="field">Nombre del negocio<input data-path="business.name">
</label>
<label class="field">Categoría<input data-path="business.category">
</label>
<label class="field">Correo<input type="email" data-path="business.email">
</label>
<label class="field">Dirección<input data-path="business.address">
</label>
<label class="field">Horarios<input data-path="business.hours">
</label>
<label class="field">Google Maps<input data-path="business.mapsUrl">
</label>
<label class="field">Mapa insertable<input data-path="business.mapsEmbedUrl">
</label>
</div>
</article>
<article class="card">
<h2>Teléfonos · Líneas y canales</h2>
<p>Podés agregar hasta 12 líneas y decidir dónde se muestra cada una.</p>
<div id="phoneEditor" class="editor-list">
</div>
<button class="btn blue" id="addPhone">Agregar teléfono</button>
</article>
<article class="card">
<h2>Presentación · Portada y descripción</h2>
<div class="grid">
<label class="field">Título<input data-path="hero.title">
</label>
<label class="field">Imagen o URL<input data-path="hero.image">
</label>
<label class="field">Descripción<textarea data-path="hero.text">
</textarea>
</label>
<label class="field">Descripción del negocio<textarea data-path="business.description">
</textarea>
</label>
</div>
<div class="check-row">
<label class="check">
<input type="checkbox" data-path="hero.showCategory"> Categoría</label>
<label class="check">
<input type="checkbox" data-path="hero.showTitle"> Título</label>
<label class="check">
<input type="checkbox" data-path="hero.showText"> Descripción</label>
<label class="check">
<input type="checkbox" data-path="hero.showImage"> Foto</label>
<label class="check">
<input type="checkbox" data-path="hero.showWhatsappCta"> CTA WhatsApp</label>
<label class="check">
<input type="checkbox" data-path="hero.showCallCta"> CTA llamada</label>
<label class="check">
<input type="checkbox" data-path="hero.showMapsCta"> CTA mapa</label>
</div>
<label class="upload-button btn blue">Cargar imagen de portada<input type="file" accept="image/*" data-upload="hero.image" hidden>
</label>
<button class="btn danger" id="removeHeroImage">Quitar foto</button>
</article>
<article class="card">
<h2>Sobre el negocio</h2>
<div class="grid">
<label class="field">Título<input data-path="about.title">
</label>
<label class="field">Texto<textarea data-path="about.text">
</textarea>
</label>
</div>
</article>
<article class="card">
<h2>Servicios y productos</h2>
<div id="serviceEditor" class="editor-list">
</div>
<button class="btn blue" id="addService">Agregar servicio</button>
</article>
<article class="card">
<h2>Secciones libres</h2>
<p>Cada una admite texto preformateado, imágenes y videos de YouTube.</p>
<div id="freeEditor" class="editor-list">
</div>
</article>
<article class="card">
<h2>Redes sociales</h2>
<div id="socialEditor" class="grid">
</div>
</article>
<article class="card">
<h2>Acerca de y páginas legales</h2>
<label class="check">
<input type="checkbox" data-path="about.showInMenu"> Mostrar Nosotros en el menú</label>
<label class="check">
<input type="checkbox" data-path="legal.showInHeaderMenu"> Términos y Privacidad dentro de Nosotros</label>
<div class="grid">
<label class="field">Contenido legal<select data-path="legal.mode">
<option value="predefined">Textos estándar</option>
<option value="custom">Textos personalizados</option>
</select>
</label>
<label class="field">Términos<textarea data-path="legal.termsHtml">
</textarea>
</label>
<label class="field">Privacidad<textarea data-path="legal.privacyHtml">
</textarea>
</label>
</div>
</article>
</section>
<section class="panel" data-panel="organizar">
<article class="card">
<h2>Orden y visibilidad de módulos</h2>
<p>Arrastrá las tarjetas o utilizá las flechas. Header y footer permanecen fijos.</p>
<div id="moduleList" class="sortable-list">
</div>
</article>
<article class="card">
<h2>Orden y estructura del menú</h2>
<p>Inicio siempre permanece visible. Cada opción puede quedar en el menú principal, dentro de Nosotros u oculta.</p>
<div id="menuEditor" class="editor-list">
</div>
</article>
<article class="card">
<h2>Información rápida</h2>
<div class="grid">
<label class="field">Filas<input type="number" min="1" max="4" data-path="quickLayout.rows">
</label>
<label class="field">Columnas<input type="number" min="1" max="4" data-path="quickLayout.columns">
</label>
<label class="field">Distribución<select data-path="quickLayout.placement">
<option value="flow">Automática</option>
<option value="center">Centrada</option>
<option value="manual">Manual</option>
</select>
</label>
</div>
<div id="quickEditor" class="editor-list">
</div>
</article>
</section>
<section class="panel" data-panel="diseno">
<article class="card">
<h2>Esquemas rápidos</h2>
<div class="scheme-row">
<button class="btn" data-scheme="default">Clásico</button>
<button class="btn" data-scheme="ocean">Océano</button>
<button class="btn" data-scheme="forest">Bosque</button>
<button class="btn" data-scheme="warm">Cálido</button>
</div>
</article>
<article class="card">
<h2>Colores de Header y Footer</h2>
<div class="grid">
<label class="field">Fondo header<input type="color" data-path="header.background">
</label>
<label class="field">Texto header<input type="color" data-path="header.text">
</label>
<label class="field">Fondo footer<input type="color" data-path="footer.background">
</label>
<label class="field">Texto footer<input type="color" data-path="footer.text">
</label>
</div>
</article>
<article class="card">
<h2>Header y footer</h2>
<div class="grid">
<label class="field">Identidad<select data-path="header.identityType">
<option value="initial">Inicial</option>
<option value="logo">Logo</option>
</select>
</label>
<label class="field">Inicial<input data-path="header.initial">
</label>
<label class="field">Nombre público<input data-path="header.nameText">
</label>
<label class="field">URL del logo<input data-path="header.logo">
</label>
<label class="field">Altura (28–250 px)<input type="number" min="28" max="250" data-path="header.logoSize">
</label>
<label class="field">Ancho variable<input type="number" min="40" max="600" data-path="header.logoWidth">
</label>
</div>
<div class="check-row">
<label class="check">
<input type="checkbox" data-path="header.showName"> Mostrar nombre</label>
<label class="check">
<input type="checkbox" data-path="header.maintainAspect"> Mantener proporción</label>
<label class="check">
<input type="checkbox" data-path="footer.showLogo"> Logo en footer</label>
<label class="check">
<input type="checkbox" data-path="footer.showName"> Nombre en footer</label>
<label class="check">
<input type="checkbox" data-path="footer.showSocials"> Redes en footer</label>
<label class="check">
<input type="checkbox" data-path="footer.showLegalLinks"> Enlaces legales</label>
</div>
<label class="field">Copyright<textarea data-path="footer.legalText">
</textarea>
</label>
<label class="upload-button btn blue">Cargar logo<input type="file" accept="image/*" data-upload="header.logo" hidden>
</label>
</article>
<article class="card">
<h2>Menú</h2>
<div class="grid">
<label class="check">
<input type="checkbox" data-path="menuDesign.enabled"> Activar colores y efectos personalizados</label>
<label class="field">Animación<select data-path="menuDesign.animation">
<option value="none">Sin efectos</option>
<option value="underline">Subrayado</option>
<option value="fade">Transición suave</option>
</select>
</label>
<label class="field">Texto<input type="color" data-path="menuDesign.text">
</label>
<label class="field">Fondo al pasar<input type="color" data-path="menuDesign.hoverBackground">
</label>
</div>
</article>
<article class="card">
<h2>Colores por sección</h2>
<div id="sectionColorEditor" class="editor-list">
</div>
</article>
</section>
<section class="panel" data-panel="galeria">
<article class="card">
<div class="section-head">
<div>
<h2>Galería visual</h2>
<p id="galleryCount">
</p>
</div>
<button class="btn blue" id="addGallery">Agregar foto</button>
</div>
<div class="grid">
<label class="field">Filas<input type="number" min="1" max="4" data-path="galleryLayout.rows">
</label>
<label class="field">Columnas<input type="number" min="1" max="4" data-path="galleryLayout.columns">
</label>
</div>
<div id="galleryEditor" class="gallery-editor">
</div>
</article>
</section>
<section class="panel" data-panel="formulario">
<article class="card">
<h2>Entrega y registro</h2>
<div class="grid">
<label class="field">Correo exclusivo de destino<input type="email" data-path="contactForm.recipientEmail">
</label>
<label class="field">Zona horaria<input data-path="contactForm.timezone">
</label>
<label class="field">Protección<select data-path="contactForm.captchaType">
<option value="none">Sin protección</option>
<option value="integrated">Casilla de confirmación</option>
<option value="math">Desafío de sumas</option>
<option value="google_v2">Google reCAPTCHA v2</option>
</select>
</label>
<label class="field">Site key de reCAPTCHA<input data-path="contactForm.googleSiteKey">
</label>
</div>
<label class="check">
<input type="checkbox" data-path="contactForm.keepMessageCopy"> Conservar copia del mensaje hasta 180 días</label>
<p>El método mail(), Sendmail o SMTP se define en la configuración protegida del servidor.</p>
</article>
<article class="card">
<div class="section-head">
<h2>Registro del formulario</h2>
<a class="btn" href="<?=Support::url('dashboardcliente/contact-log.php?format=txt')?>">Descargar TXT</a>
</div>
<div id="contactLog">Cargando…</div>
</article>
</section>
<section class="panel" data-panel="avanzado">
<article class="card warning-card">
<h2>Configuración avanzada</h2>
<p>Área exclusiva para personal técnico. El CSS y la analítica se mantienen separados del contenido cotidiano.</p>
<div class="grid">
<label class="field">CSS adicional del sitio<textarea data-path="customCode.css">
</textarea>
</label>
<label class="field">Código de analítica<textarea data-path="customCode.analytics">
</textarea>
</label>
</div>
</article>
<article class="card">
<h2>Portabilidad y respaldo</h2>
<button class="btn" id="exportJson">Descargar JSON</button> <a class="btn" href="<?=Support::url('dashboardcliente/export.php?format=csv')?>">Descargar CSV</a> <a class="btn" href="<?=Support::url('dashboardcliente/export.php?format=txt')?>">Descargar TXT</a> <label class="btn blue">Importar JSON, CSV o Excel<input type="file" id="importContent" accept=".json,.csv,.xlsx,application/json,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" hidden>
</label> <a class="btn" href="<?=Support::url('dashboardcliente/backup.php')?>">Descargar respaldo ZIP</a>
</article>
<?php if(Auth::isAdmin()):?>
<article class="card">
<div class="section-head">
<div>
<h2>Actualizaciones</h2>
<p>Versión instalada: <strong>
<?=Support::e((string)$assetVersion)?>
</strong>
</p>
</div>
</div>
<p>Seleccioná un paquete oficial. Antes de aplicar cambios se creará un respaldo y se conservarán configuración, imágenes, registros y datos del cliente.</p>
<form id="updateForm" method="post" enctype="multipart/form-data" action="<?=Support::url('dashboardcliente/apply-update.php')?>">
<?=Csrf::field()?>
<label class="btn blue">Seleccionar ZIP<input type="file" name="package" accept=".zip,application/zip" required hidden>
</label> <button class="btn" type="submit">Verificar, respaldar y actualizar</button>
</form>
<div id="updateProgress" class="update-progress" hidden>
<div class="update-progress-bar">
<i>
</i>
</div>
<p>Esperando paquete…</p>
</div>
</article>
<?php endif;?>
</section>
</main>
</div>
<script>window.ARANDU_DATA=<?=$json?>;window.ARANDU_CSRF='<?=Support::e(Csrf::token())?>';window.ARANDU_URLS={save:'<?=Support::url('dashboardcliente/save.php')?>',upload:'<?=Support::url('dashboardcliente/upload.php')?>',log:'<?=Support::url('dashboardcliente/contact-log.php')?>',import:'<?=Support::url('dashboardcliente/import.php')?>',restore:'<?=Support::url('dashboardcliente/restore.php')?>'};</script>
<script src="<?=Support::url('assets/dashboard.js')?>?v=<?=Support::e((string)$assetVersion)?>">
</script>
<script src="<?=Support::url('assets/dashboard-210.js')?>?v=<?=Support::e((string)$assetVersion)?>">
</script>
<script src="<?=Support::url('assets/dashboard-211.js')?>?v=<?=Support::e((string)$assetVersion)?>">
</script>
<script src="<?=Support::url('assets/dashboard-220.js')?>?v=<?=Support::e((string)$assetVersion)?>">
</script>
</body>
</html>
