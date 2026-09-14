<?php
require dirname(__DIR__).'/bootstrap.php';
use AranduGo\Auth;use AranduGo\BackupManager;use AranduGo\Csrf;use AranduGo\Support;
Auth::requireAdmin();Csrf::verify();$error='';$result=null;
try{
 if(($_POST['confirmation']??'')!=='RESTAURAR')throw new RuntimeException('Escribí RESTAURAR para confirmar.');
 if(empty($_FILES['backup'])||$_FILES['backup']['error']!==UPLOAD_ERR_OK)throw new RuntimeException('No se recibió el ZIP.');
 if($_FILES['backup']['size']>300*1024*1024||strtolower(pathinfo((string)$_FILES['backup']['name'],PATHINFO_EXTENSION))!=='zip')throw new RuntimeException('El archivo no es un ZIP permitido.');
 $dir=Support::basePath('storage/backups');if(!is_dir($dir)&&!mkdir($dir,0750,true))throw new RuntimeException('No se pudo preparar la restauración.');$archive=$dir.'/restore-'.date('Ymd-His').'-'.bin2hex(random_bytes(4)).'.zip';if(!move_uploaded_file($_FILES['backup']['tmp_name'],$archive))throw new RuntimeException('No se pudo guardar el archivo temporal.');
 $components=$_POST['components']??[];if(!is_array($components)||!$components)$components=['content_design','uploads','users_roles','form_log'];$result=(new BackupManager(Support::basePath(),Support::config()))->restore($archive,$components);@unlink($archive);
}catch(Throwable $e){$error=$e->getMessage();}
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Restauración · Arandu Go</title><link rel="stylesheet" href="<?=Support::url('assets/dashboard.css')?>"></head><body><main class="login"><section class="login-card"><div class="brand"><span class="mark">A</span><strong>Arandu Go</strong></div><?php if($result):?><h1>Restauración completada</h1><p class="notice">Se restauraron los componentes seleccionados y se creó el respaldo preventivo <?=Support::e($result['preventive_backup'])?>.</p><?php else:?><h1>No se pudo restaurar</h1><p class="error"><?=Support::e($error)?></p><p>La base de datos fue revertida si la operación no pudo completarse.</p><?php endif;?><a class="btn blue" href="<?=Support::url('dashboardcliente/#avanzado')?>">Volver al dashboard</a></section></main></body></html>
