<?php
require dirname(__DIR__).'/bootstrap.php';
use AranduGo\Auth;use AranduGo\Csrf;use AranduGo\Support;use AranduGo\UpdateManager;
Auth::requireLogin();if(!Auth::isAdmin())Support::json(['ok'=>false,'error'=>'Solo un administrador puede actualizar.'],403);Csrf::verify();
$error='';$result=null;
try{
 if(empty($_FILES['package'])||$_FILES['package']['error']!==UPLOAD_ERR_OK)throw new RuntimeException('No se recibió el paquete ZIP.');
 if($_FILES['package']['size']>100*1024*1024)throw new RuntimeException('El paquete supera los 100 MB.');
 $name=(string)$_FILES['package']['name'];if(strtolower(pathinfo($name,PATHINFO_EXTENSION))!=='zip')throw new RuntimeException('Seleccioná un archivo ZIP de Arandu Go.');
 $dir=Support::basePath('storage/updates');if(!is_dir($dir)&&!mkdir($dir,0750,true))throw new RuntimeException('No se pudo preparar la actualización.');
 $archive=$dir.'/upload-'.date('Ymd-His').'-'.bin2hex(random_bytes(4)).'.zip';if(!move_uploaded_file($_FILES['package']['tmp_name'],$archive))throw new RuntimeException('No se pudo guardar temporalmente el paquete.');
 $operation=(string)($_POST['operation']??'update');if(!in_array($operation,['update','rollback'],true))throw new RuntimeException('Operación de actualización inválida.');
 if($operation==='rollback'&&($_POST['confirm_rollback']??'')!=='ROLLBACK')throw new RuntimeException('Para revertir, confirmá expresamente el rollback.');
 $result=(new UpdateManager(Support::basePath(),Support::config()))->applyArchive($archive,$operation==='rollback');
}catch(Throwable $e){$error=$e->getMessage();}
?><!doctype html><html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Resultado de actualización</title><link rel="stylesheet" href="<?=Support::url('assets/dashboard.css')?>"></head><body><main class="login"><section class="login-card"><div class="brand"><span class="mark">A</span><strong>Arandu Go</strong></div><?php if($result):?><h1><?=($result['operation']??'update')==='rollback'?'Rollback completado':'Actualización completada'?></h1><p class="notice">Arandu Go pasó de <?=Support::e($result['from'])?> a <?=Support::e($result['to'])?>.</p><p>Se creó un respaldo previo automáticamente.</p><?php else:?><h1>No se pudo completar</h1><p class="error"><?=Support::e($error)?></p><p>No se modificaron los datos del cliente. Si se habían copiado archivos, fueron restaurados.</p><?php endif;?><a class="btn blue" href="<?=Support::url('dashboardcliente/#avanzado')?>">Volver al dashboard</a></section></main></body></html>
