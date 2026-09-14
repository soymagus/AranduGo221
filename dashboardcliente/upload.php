<?php
require dirname(__DIR__).'/bootstrap.php';
use AranduGo\Auth;use AranduGo\Csrf;use AranduGo\Support;
Auth::requireLogin();Csrf::verify();
$field=isset($_FILES['media'])?'media':'image';
if(empty($_FILES[$field])||$_FILES[$field]['error']!==UPLOAD_ERR_OK)Support::json(['ok'=>false,'error'=>'Carga inválida'],422);
$file=$_FILES[$field];$isMedia=$field==='media';$limit=$isMedia?50*1024*1024:5*1024*1024;
if((int)$file['size']<=0||(int)$file['size']>$limit)Support::json(['ok'=>false,'error'=>$isMedia?'El archivo debe pesar hasta 50 MB':'La imagen debe pesar hasta 5 MB'],422);
$tmp=(string)$file['tmp_name'];$mime=(new finfo(FILEINFO_MIME_TYPE))->file($tmp);
$types=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif','video/mp4'=>'mp4','video/webm'=>'webm','audio/mpeg'=>'mp3','audio/mp4'=>'m4a','audio/ogg'=>'ogg','audio/wav'=>'wav','audio/x-wav'=>'wav'];
if(!isset($types[$mime]))Support::json(['ok'=>false,'error'=>'Formato no permitido. Usá JPG, PNG, WebP, GIF, MP4, WebM, MP3, M4A, OGG o WAV.'],422);
if(!$isMedia&&!str_starts_with($mime,'image/'))Support::json(['ok'=>false,'error'=>'El campo requiere una imagen válida'],422);
if(str_starts_with($mime,'image/')){$info=@getimagesize($tmp);if(!$info||($info['mime']??'')!==$mime)Support::json(['ok'=>false,'error'=>'El archivo no contiene una imagen válida'],422);$width=(int)$info[0];$height=(int)$info[1];if($width<1||$height<1||$width>10000||$height>10000||$width*$height>40000000)Support::json(['ok'=>false,'error'=>'La resolución de la imagen es demasiado grande'],422);}
$area=preg_replace('/[^a-z0-9_-]/','',(string)($_POST['area']??'general'))?:'general';if(!in_array($area,['general','gallery','logo','hero','free'],true))$area='general';
$name=$area.'/'.date('Y/m');$dir=dirname(__DIR__).'/uploads/'.$name;if(!is_dir($dir)&&!mkdir($dir,0755,true))Support::json(['ok'=>false,'error'=>'No se pudo crear la carpeta'],500);
$stored=bin2hex(random_bytes(16)).'.'.$types[$mime];if(!move_uploaded_file($tmp,$dir.'/'.$stored))Support::json(['ok'=>false,'error'=>'No se pudo guardar'],500);@chmod($dir.'/'.$stored,0644);
Support::json(['ok'=>true,'url'=>Support::url('uploads/'.$name.'/'.$stored),'mime'=>$mime,'mediaType'=>strtok($mime,'/')]);
