<?php
require dirname(__DIR__).'/bootstrap.php';
use AranduGo\Auth;use AranduGo\Csrf;use AranduGo\SiteRepository;use AranduGo\Support;
Auth::requireLogin();Csrf::verify();$body=json_decode(file_get_contents('php://input'),true);$data=$body['data']??null;if(!is_array($data))Support::json(['ok'=>false,'error'=>'Datos inválidos'],422);if(count($data['gallery']??[])>24)Support::json(['ok'=>false,'error'=>'La galería admite hasta 24 imágenes'],422);
if(Auth::isCollaborator()){
    $current=SiteRepository::draft();
    $current['gallery']=array_values($data['gallery']??[]);
    $current['galleryLayout']=$data['galleryLayout']??($current['galleryLayout']??[]);
    $current['freeSections']=array_values($data['freeSections']??[]);
    foreach($current['modules']??[] as &$module){$key=(string)($module['key']??'');if(str_starts_with($key,'free')||$key==='gallery'){foreach($data['modules']??[] as $incoming)if(($incoming['key']??'')===$key){$module['active']=!empty($incoming['active']);break;}}}unset($module);
    foreach(['free1','free2','free3','free4','free5','free6'] as $key)if(isset($data['menuSettings'][$key]))$current['menuSettings'][$key]['visible']=!empty($data['menuSettings'][$key]['visible']);
    $data=$current;
}
SiteRepository::save($data,!empty($body['publish']));Support::json(['ok'=>true]);
