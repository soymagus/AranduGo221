
<?php
require __DIR__.'/bootstrap.php';
use AranduGo\SiteRepository; use AranduGo\Support;
$assetVersionFile=__DIR__.'/config/version.php';$versionInfo=is_file($assetVersionFile)?(require $assetVersionFile):[];$assetVersion=$versionInfo['asset_version']??$versionInfo['version']??'1.0.6';
$d=SiteRepository::published();$contactVisibility=$d['contactVisibility']??[];$showContactData=fn(string $key,string $place,bool $fallback=true):bool=>array_key_exists($place,$contactVisibility[$key]??[])?!empty($contactVisibility[$key][$place]):$fallback;$d['quickVisible']['address']=$showContactData('address','quick',!empty($d['quickVisible']['address']));$d['quickVisible']['email']=$showContactData('email','quick',!empty($d['quickVisible']['email']));$d['quickVisible']['hours']=$showContactData('hours','quick',!empty($d['quickVisible']['hours']));$d['hero']['showCategory']=!empty($d['hero']['showCategory'])&&$showContactData('category','hero',true); $modules=array_values(array_filter($d['modules']??[],fn($m)=>!empty($m['active']))); $by=[]; foreach($modules as $m)$by[$m['key']]=$m;
$style=fn(string $key)=>isset($by[$key])?'--bg:'.Support::e($by[$key]['background']).';--text:'.Support::e($by[$key]['text']).';--accent:'.Support::e($by[$key]['accent']):'';
$phoneHref=function(array $p):string{$n=preg_replace('/\D+/','',$p['number']??'');return ($p['type']??'')==='whatsapp'?'https://wa.me/'.$n:'tel:+'.$n;};
$phones=$d['phones']??[];$wa=current(array_filter($phones,fn($p)=>($p['type']??'')==='whatsapp'&&!empty($p['number'])&&(!array_key_exists('showInHero',$p)||!empty($p['showInHero']))))?:null;$call=current(array_filter($phones,fn($p)=>in_array($p['type']??'', ['mobile','landline'],true)&&!empty($p['number'])&&(!array_key_exists('showInHero',$p)||!empty($p['showInHero']))))?:null;
$freeById=[];foreach($d['freeSections']??[] as $freeSection)$freeById[$freeSection['id']??'']=$freeSection;
$menuLabels=['home'=>'Inicio','services'=>'Servicios','gallery'=>'Galería','about'=>'Nosotros','free1'=>$freeById['free1']['menuLabel']??'Más información 1','free2'=>$freeById['free2']['menuLabel']??'Más información 2','free3'=>$freeById['free3']['menuLabel']??'Más información 3','free4'=>$freeById['free4']['menuLabel']??'Más información 4','free5'=>$freeById['free5']['menuLabel']??'Más información 5','free6'=>$freeById['free6']['menuLabel']??'Más información 6','contact'=>'Contacto'];
$anchors=['home'=>'inicio','services'=>'servicios','gallery'=>'galeria','about'=>'nosotros','free1'=>'free1','free2'=>'free2','free3'=>'free3','free4'=>'free4','free5'=>'free5','free6'=>'free6','contact'=>'contacto'];
$visibleGallery=array_values(array_filter($d['gallery']??[],fn($g)=>!empty($g['visible'])&&!empty($g['image'])));$galleryPageSize=max(1,min(16,(int)($d['galleryLayout']['rows']??1)*(int)($d['galleryLayout']['columns']??3)));
$socialLabels=['facebook'=>'Facebook','instagram'=>'Instagram','x'=>'X','linkedin'=>'LinkedIn','youtube'=>'YouTube','tiktok'=>'TikTok','urlgo'=>'URLGO.me','whatsapp'=>'WhatsApp'];
$socialIconMarkup=function(string $network):string{
 $paths=[
  'facebook'=>'M13.5 22v-8h2.7l.4-3.1h-3.1v-2c0-.9.3-1.5 1.6-1.5h1.7V4.6c-.8-.1-1.8-.2-2.9-.2-2.9 0-4.9 1.8-4.9 5.1v1.4H6v3.1h3v8h4.5Z',
  'instagram'=>'M12 7.8A4.2 4.2 0 1 0 12 16.2 4.2 4.2 0 0 0 12 7.8Zm0 6.9a2.7 2.7 0 1 1 0-5.4 2.7 2.7 0 0 1 0 5.4Zm5.4-7.1a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM20 8.6c-.1-1.4-.4-2.7-1.4-3.7s-2.3-1.3-3.7-1.4c-1.5-.1-5.8-.1-7.3 0-1.4.1-2.7.4-3.7 1.4S2.6 7.2 2.5 8.6c-.1 1.5-.1 5.8 0 7.3.1 1.4.4 2.7 1.4 3.7s2.3 1.3 3.7 1.4c1.5.1 5.8.1 7.3 0 1.4-.1 2.7-.4 3.7-1.4s1.3-2.3 1.4-3.7c.1-1.5.1-5.8 0-7.3Zm-1.7 8.9a2.9 2.9 0 0 1-1.7 1.7c-1.2.5-4 .4-4.6.4s-3.4.1-4.6-.4a2.9 2.9 0 0 1-1.7-1.7c-.5-1.2-.4-4-.4-4.6s-.1-3.4.4-4.6a2.9 2.9 0 0 1 1.7-1.7c1.2-.5 4-.4 4.6-.4s3.4-.1 4.6.4a2.9 2.9 0 0 1 1.7 1.7c.5 1.2.4 4 .4 4.6s.1 3.4-.4 4.6Z',
  'x'=>'M4.2 3h4.5l4.1 5.5L17.6 3h2.2l-6 7 6.5 11h-4.5l-4.6-6.2L5.8 21H3.6l6.6-7.7L4.2 3Zm3.4 1.7L16.7 19h1.7L9.3 4.7H7.6Z',
  'linkedin'=>'M6.5 8.5H3V21h3.5V8.5ZM4.8 3A2 2 0 1 0 4.8 7a2 2 0 0 0 0-4ZM21 14c0-3.8-2-5.7-4.8-5.7-2.2 0-3.2 1.2-3.8 2.1V8.5H9V21h3.5v-6.2c0-1.6.3-3.2 2.4-3.2 2 0 2 1.9 2 3.3V21H21v-7Z',
  'youtube'=>'M21.6 7.2a3 3 0 0 0-2.1-2.1C17.6 4.6 12 4.6 12 4.6s-5.6 0-7.5.5a3 3 0 0 0-2.1 2.1A31 31 0 0 0 2 12a31 31 0 0 0 .4 4.8 3 3 0 0 0 2.1 2.1c1.9.5 7.5.5 7.5.5s5.6 0 7.5-.5a3 3 0 0 0 2.1-2.1A31 31 0 0 0 22 12a31 31 0 0 0-.4-4.8ZM10 15.5v-7l6 3.5-6 3.5Z',
  'tiktok'=>'M16.6 3c.3 2.1 1.5 3.5 3.6 3.7v3.1a8 8 0 0 1-3.6-1.1v6.1a6.2 6.2 0 1 1-5.4-6.1v3.2a3 3 0 1 0 2.2 2.9V3h3.2Z',
  'whatsapp'=>'M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2a8.2 8.2 0 0 1-4.2-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.4-.7-1.6-.8-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1-1.4-.7-2.4-1.3-3.3-2.9-.3-.4.3-.4.8-1.3.1-.2 0-.4 0-.5l-.8-1.9c-.2-.5-.4-.4-.6-.4h-.5c-.2 0-.4.1-.6.3-.2.3-.9.9-.9 2.1 0 1.3.9 2.5 1 2.6.1.2 1.8 2.8 4.4 3.9 1.6.7 2.2.7 3 .6.5-.1 1.4-.6 1.6-1.2.2-.6.2-1.1.2-1.2-.1-.2-.3-.3-.6-.4Z'
 ];
 if($network==='urlgo')return '<img class="urlgo-logo" src="'.Support::url('assets/images/URLGO_Logo1_comp.png').'" alt="" aria-hidden="true">';
 $path=$paths[$network]??'M4 4h16v16H4z';return '<svg class="social-svg" viewBox="0 0 24 24" aria-hidden="true"><path d="'.$path.'"/></svg>';
};
$icon=function(string $type):string{$paths=['whatsapp'=>'M16.75 13.96c-.25-.13-1.47-.73-1.7-.81-.23-.09-.4-.13-.57.13-.17.26-.66.81-.81.98-.15.17-.3.19-.55.06-1.48-.74-2.45-1.32-3.44-3-.26-.45.26-.42.74-1.4.08-.17.04-.32-.02-.45-.06-.13-.57-1.38-.78-1.89-.2-.49-.41-.42-.57-.43h-.48c-.17 0-.44.06-.67.32-.23.26-.88.86-.88 2.1s.9 2.44 1.03 2.61c.13.17 1.77 2.7 4.29 3.79 1.6.69 2.23.75 3.03.63.49-.07 1.47-.6 1.68-1.18.21-.58.21-1.08.15-1.18-.06-.11-.23-.17-.48-.3M12.04 21.5h-.01a9.48 9.48 0 0 1-4.83-1.32l-.35-.2-3.59.94.96-3.5-.23-.36A9.45 9.45 0 1 1 12.04 21.5','mobile'=>'M17 1.5H7A2.5 2.5 0 0 0 4.5 4v16A2.5 2.5 0 0 0 7 22.5h10a2.5 2.5 0 0 0 2.5-2.5V4A2.5 2.5 0 0 0 17 1.5M12 21a1.25 1.25 0 1 1 0-2.5A1.25 1.25 0 0 1 12 21M17.5 17h-11V4h11z','landline'=>'M6.6 10.8a15.5 15.5 0 0 0 6.6 6.6l2.2-2.2c.3-.3.8-.4 1.2-.2 1.3.5 2.7.8 4.1.8.7 0 1.3.6 1.3 1.3V21c0 .7-.6 1.3-1.3 1.3A19 19 0 0 1 1.7 3.3C1.7 2.6 2.3 2 3 2h3.9c.7 0 1.3.6 1.3 1.3 0 1.4.3 2.8.8 4.1.1.4.1.9-.2 1.2z','email'=>'M3 5h18v14H3zm9 7 8-5H4z'];$path=$paths[$type]??$paths['landline'];return'<svg class="ui-icon ui-icon-'.$type.'" viewBox="0 0 24 24" aria-hidden="true"><path d="'.$path.'"/></svg>';};
$quickCapacity=max(1,min(16,(int)($d['quickLayout']['rows']??1)*(int)($d['quickLayout']['columns']??3)));$quickOrder=array_slice($d['quickOrder']??[],0,$quickCapacity);$quickPosition=function(string $item)use($d):string{if(($d['quickLayout']['placement']??'flow')!=='manual')return'';$code=strtoupper((string)($d['quickLayout']['positions'][$item]??''));if(!preg_match('/^([A-D])([1-4])$/',$code,$m))return'';return'grid-row:'.(ord($m[1])-64).';grid-column:'.(int)$m[2].';';};
$menuEnabled=!empty($d['menuDesign']['enabled']);$menuStyle=$menuEnabled?'--menu-text:'.Support::e($d['menuDesign']['text']??'#40506a').';--menu-hover-bg:'.Support::e($d['menuDesign']['hoverBackground']??'#eef4fc').';--menu-hover-text:'.Support::e($d['menuDesign']['hoverText']??'#1769d2').';':'';
$mathA=random_int(2,9);$mathB=random_int(1,9);$mathId=bin2hex(random_bytes(12));$_SESSION['arandu_math_challenges']??=[];$_SESSION['arandu_math_challenges']=array_filter($_SESSION['arandu_math_challenges'],fn($challenge)=>(int)($challenge['expires']??0)>=time());$_SESSION['arandu_math_challenges'][$mathId]=['answer'=>$mathA+$mathB,'expires'=>time()+900];
$moduleForMenu=['services'=>'services','gallery'=>'gallery','about'=>'about','free1'=>'free1','free2'=>'free2','free3'=>'free3','free4'=>'free4','free5'=>'free5','free6'=>'free6','contact'=>'contact'];$mainMenu=[];$aboutMenu=[];foreach($d['menuOrder']??[] as $menuKey){$cfg=$d['menuSettings'][$menuKey]??['visible'=>true,'parent'=>null];if($menuKey!=='home'&&empty($cfg['visible']))continue;if(isset($moduleForMenu[$menuKey])&&!isset($by[$moduleForMenu[$menuKey]]))continue;if(in_array($menuKey,['free1','free2','free3','free4','free5','free6'],true)){foreach($d['freeSections']??[] as $free)if(($free['id']??'')===$menuKey&&empty($free['showInMenu']))continue 2;}if(($cfg['parent']??null)==='about')$aboutMenu[]=$menuKey;else $mainMenu[]=$menuKey;}
$navHtml='';foreach($mainMenu as $menuKey){if($menuKey==='about'){$navHtml.='<details class="submenu"><summary>'.Support::e($menuLabels[$menuKey]).'</summary><div><a href="#nosotros">Acerca de</a>';foreach($aboutMenu as $subKey)$navHtml.='<a href="#'.Support::e($anchors[$subKey]??$subKey).'">'.Support::e($menuLabels[$subKey]??$subKey).'</a>';if(!empty($d['legal']['showInHeaderMenu']))$navHtml.='<a href="'.Support::url('terminos.php').'">Términos de Servicio</a><a href="'.Support::url('privacidad.php').'">Políticas de Privacidad</a>';$navHtml.='</div></details>';}else{$navHtml.='<a href="#'.Support::e($anchors[$menuKey]??$menuKey).'">'.Support::e($menuLabels[$menuKey]??$menuKey).'</a>';}}
$mobile=$d['mobileDesign']??[];$mobileStyle='--mobile-menu-bg:'.Support::e($mobile['menuBackground']??'#ffffff').';--mobile-menu-text:'.Support::e($mobile['menuText']??'#172033').';--mobile-actions-bg:'.Support::e($mobile['actionsBackground']??'#ffffff').';--mobile-actions-text:'.Support::e($mobile['actionsText']??'#172033').';--mobile-actions-accent:'.Support::e($mobile['actionsAccent']??'#1769d2');$mobileClass='mobile-menu-'.preg_replace('/[^a-z]/','',(string)($mobile['menuStyle']??'card')).' mobile-toggle-'.preg_replace('/[^a-z]/','',(string)($mobile['menuShape']??'rounded'));?>
<!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"><title><?=Support::e(($d['business']['name']??'Arandu Go').' — '.($d['business']['category']??''))?>
</title><meta name="description" content="<?=Support::e($d['hero']['text']??'')?>
"><meta name="robots" content="<?=!empty($d['seo']['allowIndexing'])?'index,follow':'noindex,nofollow'?>
"><link rel="stylesheet" href="<?=Support::url('assets/site.css')?>
?v=<?=Support::e((string)$assetVersion)?>
"><link rel="stylesheet" href="<?=Support::url('assets/site-v101.css')?>
?v=<?=Support::e((string)$assetVersion)?>
">
<link rel="stylesheet" href="<?=Support::url('assets/site-221.css')?>?v=<?=Support::e((string)$assetVersion)?>">
<?php if(!empty($d['customCode']['css'])):?>
<style><?=$d['customCode']['css']?>
</style>
<?php endif;?>
</head><body id="inicio" class="<?=Support::e($mobileClass)?>
" style="<?=$mobileStyle?>
">

<?php if(isset($_GET['enviado'])):?>
<div class="notice form-notice">Su mensaje ha sido enviado.</div>
<?php endif;?>
<header class="site-header <?=$menuEnabled?'menu-custom menu-anim-'.Support::e($d['menuDesign']['animation']??'none'):''?>
" style="<?=$menuStyle?>
background:<?=Support::e($d['header']['background']??'#fff')?>
;color:<?=Support::e($d['header']['text']??'#172033')?>
"><a class="identity" href="#inicio">
<?php if(($d['header']['identityType']??'initial')==='logo'&&!empty($d['header']['logo'])):?>
<img src="<?=Support::e($d['header']['logo'])?>
" alt="Logo" onerror="this.style.display='none'" style="height:<?=min(250,max(28,(int)($d['header']['logoSize']??36)))?>
px;<?=!empty($d['header']['maintainAspect'])?'width:auto':'width:'.max(40,(int)($d['header']['logoWidth']??180)).'px'?>
">
<?php else:?>
<span class="identity-mark"><?=Support::e($d['header']['initial']??'A')?>
</span>
<?php endif;?>

<?php if(!empty($d['header']['showName'])):?>
<span><?=Support::e($d['header']['nameText']??$d['business']['name'])?>
</span>
<?php endif;?>
</a><button class="button alt menu-toggle" type="button" aria-controls="mainMenu" aria-expanded="false">Menú</button><nav id="mainMenu"><?=$navHtml?>
</nav>
<?php if($wa):?>
<a class="button" href="<?=$phoneHref($wa)?>
"><?=$icon('whatsapp')?>
 WhatsApp</a>
<?php endif;?>
</header><main>

<?php foreach($modules as $m):$key=$m['key']; if($key==='hero'):?>
<section class="section hero" style="<?=$style('hero')?>
"><div>
<?php if(!empty($d['hero']['showCategory'])&&!empty($d['business']['category'])):?>
<p class="eyebrow"><?=Support::e($d['business']['category'])?>
</p>
<?php endif;?>

<?php if(!empty($d['hero']['showTitle'])&&!empty($d['hero']['title'])):?>
<h1><?=Support::e($d['hero']['title'])?>
</h1>
<?php endif;?>

<?php if(!empty($d['hero']['showText'])&&!empty($d['hero']['text'])):?>
<p><?=Support::e($d['hero']['text'])?>
</p>
<?php endif;?>
<div class="cta-row">
<?php if(!empty($d['hero']['showWhatsappCta'])&&$wa):?>
<a class="button" href="<?=$phoneHref($wa)?>
"><?=$icon('whatsapp')?>
 WhatsApp</a>
<?php endif;?>

<?php if(!empty($d['hero']['showCallCta'])&&$call):?>
<a class="button alt" href="<?=$phoneHref($call)?>
">☎ Llamar</a>
<?php endif;?>

<?php if(!empty($d['hero']['showMapsCta'])&&!empty($d['business']['mapsUrl'])):?>
<a class="button alt" href="#ubicacion">Cómo llegar</a>
<?php endif;?>
</div></div>
<?php if(!empty($d['hero']['showImage'])&&!empty($d['hero']['image'])):?>
<div class="hero-media"><img src="<?=Support::e($d['hero']['image'])?>
" alt="<?=Support::e($d['business']['name'])?>
" onerror="this.style.display='none'"></div>
<?php endif;?>
</section>

<?php elseif($key==='quick'):?>
<section class="section" style="<?=$style('quick')?>
"><div class="quick-grid quick-<?=Support::e($d['quickLayout']['placement']??'flow')?>
" style="--cols:<?=max(1,min(4,(int)($d['quickLayout']['columns']??3)))?>
">
<?php foreach($quickOrder as $item):if($item==='address'&&!empty($d['quickVisible']['address'])):?>
<article class="card" style="<?=$quickPosition('address')?>
"><strong><svg class="ui-icon ui-icon-location" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a7 7 0 0 0-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z"/></svg> Dirección</strong><p><?=Support::e($d['business']['address'])?>
</p></article>
<?php elseif($item==='hours'&&!empty($d['quickVisible']['hours'])):?>
<article class="card" style="<?=$quickPosition('hours')?>
"><strong>◷ Horarios</strong><p><?=Support::e($d['business']['hours'])?>
</p></article>
<?php elseif($item==='email'&&!empty($d['quickVisible']['email'])):?>
<article class="card" style="<?=$quickPosition('email')?>
"><strong><?=$icon('email')?>
 Correo</strong><p><?=Support::e($d['business']['email'])?>
</p></article>
<?php elseif(str_starts_with($item,'phone:')):$id=substr($item,6);foreach($phones as $p):if($p['id']===$id&&!empty($p['showInQuick'])&&!empty($p['number'])):?>
<a class="card" style="<?=$quickPosition($item)?>
" href="<?=$phoneHref($p)?>
"><strong><?=$icon($p['type']??'landline')?>
 <?=Support::e($p['label'])?>
</strong><p><?=Support::e($p['number'])?>
</p></a>
<?php endif;endforeach;endif;endforeach;?>
</div></section>

<?php elseif($key==='about'):?>
<section id="nosotros" class="section" style="<?=$style('about')?>
"><p class="eyebrow">Sobre el negocio</p><h2><?=Support::e($d['about']['title']??'')?>
</h2><p><?=nl2br(Support::e($d['about']['text']??''))?>
</p></section>

<?php elseif($key==='services'):?>
<section id="servicios" class="section" style="<?=$style('services')?>
"><p class="eyebrow">Servicios / Productos</p><h2>Lo que ofrecemos</h2><div class="service-grid">
<?php foreach($d['services']??[] as $s):?>
<article class="card"><h3><?=Support::e($s['title'])?>
</h3><p><?=Support::e($s['description'])?>
</p></article>
<?php endforeach;?>
</div></section>

<?php elseif($key==='gallery'):?>
<section id="galeria" class="section" style="<?=$style('gallery')?>
" data-gallery data-page-size="<?=$galleryPageSize?>
"><p class="eyebrow">Galería</p><h2><?=Support::e($d['gallerySettings']['title']??'Imágenes del negocio')?>
</h2>
<?php if(trim(strip_tags((string)($d['gallerySettings']['introHtml']??'')))!==''):?>
<div class="rich-content gallery-intro"><?=$d['gallerySettings']['introHtml']?>
</div>
<?php endif;?>
<div class="gallery-grid" style="--cols:<?=max(1,min(4,(int)($d['galleryLayout']['columns']??3)))?>
">
<?php foreach($visibleGallery as $i=>$g):?>
<article class="card gallery-card" data-gallery-item="<?=$i?>
"><img src="<?=Support::e($g['image'])?>
" alt="<?=Support::e($g['title'])?>
"><div>
<?php if(!empty($g['title'])):?>
<h3><?=Support::e($g['title'])?>
</h3>
<?php endif;?>

<?php if(!empty($g['description'])):?>
<p><?=Support::e($g['description'])?>
</p>
<?php endif;?>

<?php if(!empty($g['ctaLabel'])&&!empty($g['ctaUrl'])):$galleryWhatsapp=str_contains(strtolower($g['ctaUrl']),'wa.me')||str_contains(strtolower($g['ctaLabel']),'whatsapp');?>
<a class="button" href="<?=Support::e($g['ctaUrl'])?>
"><?=$galleryWhatsapp?$icon('whatsapp').' ':''?>
<?=Support::e($g['ctaLabel'])?>
</a>
<?php endif;?>
</div></article>
<?php endforeach;?>
</div>
<?php if(count($visibleGallery)>$galleryPageSize):?>
<div class="gallery-nav"><button class="button alt" data-gallery-prev>← Anteriores</button><strong data-gallery-status></strong><button class="button" data-gallery-next>Siguientes →</button></div>
<?php endif;?>
</section>

<?php elseif($key==='location'):?>
<section id="ubicacion" class="section location-grid" style="<?=$style('location')?>
"><iframe class="map" src="<?=Support::e($d['business']['mapsEmbedUrl']??'')?>
" loading="lazy"></iframe><div><p class="eyebrow">Ubicación</p><h2>¿Cómo llegar?</h2><p><?=Support::e($d['business']['address']??'')?>
</p><p><?=Support::e($d['business']['hours']??'')?>
</p>
<?php if(!empty($d['business']['mapsUrl'])):?>
<a class="button" target="_blank" rel="noopener" href="<?=Support::e($d['business']['mapsUrl'])?>
">Abrir mapa</a>
<?php endif;?>
</div></section>

<?php elseif($key==='contact'):?>
<section id="contacto" class="section contact-grid" style="<?=$style('contact')?>
"><div><p class="eyebrow">Contacto</p><h2>¿Tenés alguna consulta?</h2><div class="contact-methods">
<?php foreach(array_filter($phones,fn($p)=>!empty($p['showInContact'])&&!empty($p['number'])) as $p):?>
<a class="contact-method" href="<?=$phoneHref($p)?>
"><?=$icon($p['type']??'landline')?>
<span><strong><?=Support::e($p['label'])?>
</strong><small><?=Support::e($p['number'])?>
</small></span></a>
<?php endforeach;?>

<?php if($showContactData('email','contact',true)&&!empty($d['business']['email'])):?>
<a class="contact-method" href="mailto:<?=Support::e($d['business']['email'])?>
"><?=$icon('email')?>
<span><strong>Correo</strong><small><?=Support::e($d['business']['email'])?>
</small></span></a>
<?php endif;?>

<?php if($showContactData('address','contact',false)&&!empty($d['business']['address'])):?>
<div class="contact-method"><svg class="ui-icon ui-icon-location" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a7 7 0 0 0-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 0 0-7-7Zm0 9.5A2.5 2.5 0 1 1 12 6a2.5 2.5 0 0 1 0 5.5Z"/></svg><span><strong>Dirección</strong><small><?=Support::e($d['business']['address'])?>
</small></span></div>
<?php endif;?>

<?php if($showContactData('hours','contact',false)&&!empty($d['business']['hours'])):?>
<div class="contact-method"><svg class="ui-icon ui-icon-hours" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Zm1 11h5v-2h-4V6h-2v7Z"/></svg><span><strong>Horarios</strong><small><?=Support::e($d['business']['hours'])?>
</small></span></div>
<?php endif;?>
</div></div>
<?php if(!empty($d['contactForm']['enabled'])):?>
<form class="contact-form" method="post" action="<?=Support::url('contact.php')?>
"><input name="name" placeholder="Nombre" required><input type="email" name="email" placeholder="Correo" required><input name="phone" placeholder="Teléfono"><input name="subject" maxlength="180" placeholder="Asunto" required><textarea name="message" placeholder="Mensaje" required></textarea><input name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px">
<?php if(($d['contactForm']['captchaType']??'none')==='math'):?>
<label>Resolvé: <?=$mathA?>
 + <?=$mathB?>
 = <input type="hidden" name="math_id" value="<?=Support::e($mathId)?>
"><input name="math_answer" inputmode="numeric" pattern="[0-9]+" autocomplete="off" required></label>
<?php endif;?>

<?php if(($d['contactForm']['captchaType']??'none')==='integrated'):?>
<label><input type="checkbox" name="human" value="1" required> Soy una persona</label>
<?php elseif(($d['contactForm']['captchaType']??'none')==='google_v2'&&!empty($d['contactForm']['googleSiteKey'])):?>
<div class="g-recaptcha" data-sitekey="<?=Support::e($d['contactForm']['googleSiteKey'])?>
"></div><script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif;?>
<button class="button" type="submit">Enviar consulta</button></form>
<?php endif;?>
</section>

<?php elseif($key==='social'):?>
<section class="section social-section" style="<?=$style('social')?>
"><p class="eyebrow">Redes sociales</p><h2>Seguinos</h2><div class="social-row">
<?php $socialOrder=$d['socialSettings']['order']??array_keys($socialLabels);foreach($socialOrder as $network):$url=$d['socials'][$network]??'';$visible=$d['socialSettings']['visible'][$network]??true;if($url&&$visible):?>
<a class="social-icon social-<?=Support::e($network)?>
" href="<?=Support::e($url)?>
" target="_blank" rel="noopener" aria-label="<?=Support::e($socialLabels[$network]??ucfirst($network))?>
"><?=$socialIconMarkup($network)?>
<span><?=Support::e($socialLabels[$network]??ucfirst($network))?>
</span></a>
<?php endif;endforeach;?>

<?php if(!empty($d['customSocial']['url'])):?>
<a class="social-icon social-custom" href="<?=Support::e($d['customSocial']['url'])?>
" target="_blank" rel="noopener" aria-label="<?=Support::e($d['customSocial']['label']??'Otra red')?>
">
<?php if(!empty($d['customSocial']['iconUrl'])):?>
<img src="<?=Support::e($d['customSocial']['iconUrl'])?>
" alt="">
<?php endif;?>
<span><?=Support::e($d['customSocial']['label']??'Otra red')?>
</span></a>
<?php endif;?>
</div></section>

<?php elseif(in_array($key,['free1','free2','free3','free4','free5','free6'],true)):$free=current(array_filter($d['freeSections']??[],fn($f)=>$f['id']===$key));if($free):$hasFreeMedia=(bool)array_filter($free['media']??[],fn($item)=>!empty($item['url']));$freePosition=in_array($free['mediaPosition']??'right',['left','right'],true)?$free['mediaPosition']:'right';$freeFit=in_array($free['mediaFit']??'cover',['cover','contain','auto'],true)?$free['mediaFit']:'cover';$freeFocus=in_array($free['mediaFocus']??'center',['center','top','bottom','left','right'],true)?$free['mediaFocus']:'center';?>
<section id="<?=$key?>
" class="section free-section free-media-<?=Support::e($freePosition)?>
 free-fit-<?=Support::e($freeFit)?>
 free-focus-<?=Support::e($freeFocus)?>
 <?=$hasFreeMedia?'has-free-media':'free-no-media'?>
" style="<?=$style($key)?>
"><div class="free-copy"><p class="eyebrow"><?=Support::e($free['publicLabel']??'Información')?>
</p><h2><?=Support::e($free['title'])?>
</h2><div class="rich-content"><?=$free['html']?>
</div>
<?php if(!empty($free['ctaLabel'])&&!empty($free['ctaUrl'])):?>
<a class="button" href="<?=Support::e($free['ctaUrl'])?>
"><?=Support::e($free['ctaLabel'])?>
</a>
<?php endif;?>
</div>
<?php if($hasFreeMedia):?>
<div class="free-media">
<?php foreach($free['media']??[] as $media):if(empty($media['url']))continue;?>

<?php $mediaType=$media['type']??'image';$mediaUrl=Support::e($media['url']);$mediaCaption=Support::e($media['caption']??'');if($mediaType==='youtube'):preg_match('~(?:youtu.be/|youtube.com/(?:watch\?v=|embed/|shorts/|live/))([\w-]{11})~',$media['url'],$match);$videoId=$match[1]??'';if($videoId):?>
<figure class="free-video"><iframe src="https://www.youtube.com/embed/<?=Support::e($videoId)?>
" title="<?=Support::e($media['caption']??'Video')?>
" loading="lazy" referrerpolicy="strict-origin-when-cross-origin" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe><a target="_blank" rel="noopener" href="https://www.youtube.com/watch?v=<?=Support::e($videoId)?>
">Ver video en YouTube</a></figure>
<?php endif;elseif($mediaType==='video'):?>
<figure class="free-video native-media"><video controls preload="metadata" src="<?=$mediaUrl?>"></video><?php if($mediaCaption!==''):?><figcaption><?=$mediaCaption?></figcaption><?php endif;?></figure>
<?php elseif(in_array($mediaType,['audio','playlist'],true)):?>
<figure class="free-audio"><audio controls preload="metadata" src="<?=$mediaUrl?>"></audio><?php if($mediaCaption!==''):?><figcaption><?=$mediaCaption?></figcaption><?php endif;?></figure>
<?php else:?>
<figure class="free-image"><img src="<?=Support::e($media['url'])?>
" alt="<?=Support::e($media['caption']??'')?>
">
<?php if(!empty($media['caption'])):?>
<figcaption><?=Support::e($media['caption'])?>
</figcaption>
<?php endif;?>
</figure>
<?php endif;endforeach;?>
</div>
<?php endif;?>
</section>
<?php endif;endif;endforeach;?>
</main>

<?php require __DIR__.'/partials/site-footer.php';?>
<div class="mobile-actions">
<?php if($wa):?>
<a href="<?=$phoneHref($wa)?>
"><?=$icon('whatsapp')?>
 WhatsApp</a>
<?php endif;?>

<?php if($call):?>
<a href="<?=$phoneHref($call)?>
">☎ Llamar</a>
<?php endif;?>
<a href="#ubicacion">⌖ Cómo llegar</a></div><script>const toggle=document.querySelector('.menu-toggle'),menu=document.querySelector('#mainMenu');toggle?.addEventListener('click',()=>{const open=menu.classList.toggle('open');toggle.setAttribute('aria-expanded',String(open))});document.querySelectorAll('[data-gallery]').forEach(g=>{const items=[...g.querySelectorAll('[data-gallery-item]')],size=Number(g.dataset.pageSize)||3,status=g.querySelector('[data-gallery-status]');let page=0;const render=()=>{const start=page*size,end=Math.min(start+size,items.length);items.forEach((item,i)=>item.hidden=i<start||i>=end);if(status)status.textContent=items.length?`${start+1}–${end} / ${items.length}`:'0 / 0';const prev=g.querySelector('[data-gallery-prev]'),next=g.querySelector('[data-gallery-next]');if(prev)prev.disabled=page===0;if(next)next.disabled=end>=items.length};g.querySelector('[data-gallery-prev]')?.addEventListener('click',()=>{page=Math.max(0,page-1);render()});g.querySelector('[data-gallery-next]')?.addEventListener('click',()=>{page=Math.min(Math.ceil(items.length/size)-1,page+1);render()});render()});</script><?=$d['customCode']['analytics']??''?>
</body></html>
