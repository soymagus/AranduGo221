<?php
require dirname(__DIR__).'/bootstrap.php';
use AranduGo\Auth;use AranduGo\SiteRepository;
Auth::requireAdmin();$data=SiteRepository::draft();$format=$_GET['format']??'json';
if($format==='txt'){header('Content-Type: text/plain; charset=UTF-8');header('Content-Disposition: attachment; filename="arandu-go-contenido.txt"');echo "ARANDU GO\n===========\n";echo "Negocio: ".($data['business']['name']??'')."\nCorreo: ".($data['business']['email']??'')."\nDirección: ".($data['business']['address']??'')."\n\n";foreach($data['services']??[] as $s)echo "SERVICIO: {$s['title']}\n{$s['description']}\n\n";exit;}
if($format==='csv'){header('Content-Type: text/csv; charset=UTF-8');header('Content-Disposition: attachment; filename="arandu-go-contenido.csv"');$out=fopen('php://output','wb');fwrite($out,"\xEF\xBB\xBF");fputcsv($out,['tipo','titulo','descripcion','url']);foreach($data['services']??[] as $s)fputcsv($out,['servicio',$s['title']??'',$s['description']??'','']);foreach($data['gallery']??[] as $g)fputcsv($out,['galeria',$g['title']??'',$g['description']??'',$g['image']??'']);fclose($out);exit;}
header('Content-Type: application/json; charset=UTF-8');header('Content-Disposition: attachment; filename="arandu-go-contenido.json"');echo json_encode($data,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
