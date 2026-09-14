<?php
require dirname(__DIR__).'/bootstrap.php';
use AranduGo\Auth;use AranduGo\Csrf;use AranduGo\Database;use AranduGo\Support;
Auth::requireAdmin();$pdo=Database::connection();$table=Database::table('users');
if($_SERVER['REQUEST_METHOD']==='GET'){$rows=$pdo->query("SELECT id,name,username,email,active,last_login_at FROM {$table} WHERE role='collaborator' ORDER BY id")->fetchAll();Support::json(['ok'=>true,'rows'=>$rows,'limit'=>6]);}
Csrf::verify();$body=json_decode((string)file_get_contents('php://input'),true)?:$_POST;$action=(string)($body['action']??'');$id=(int)($body['id']??0);
if($action==='create'){
    if((int)$pdo->query("SELECT COUNT(*) FROM {$table} WHERE role='collaborator'")->fetchColumn()>=6)Support::json(['ok'=>false,'error'=>'Se alcanzó el máximo de 6 colaboradores.'],422);
    $name=trim((string)($body['name']??''));$username=trim((string)($body['username']??''));$email=filter_var($body['email']??'',FILTER_VALIDATE_EMAIL);$password=(string)($body['password']??'');
    if($name===''||!preg_match('/^[A-Za-z0-9._-]{3,60}$/',$username)||!$email||strlen($password)<10)Support::json(['ok'=>false,'error'=>'Revisá nombre, usuario, correo y contraseña (mínimo 10 caracteres).'],422);
    try{$stmt=$pdo->prepare("INSERT INTO {$table}(name,username,email,password_hash,role,active) VALUES(?,?,?,?, 'collaborator',1)");$stmt->execute([$name,$username,$email,password_hash($password,PASSWORD_DEFAULT)]);}catch(Throwable){Support::json(['ok'=>false,'error'=>'El usuario o correo ya está registrado.'],409);}Support::json(['ok'=>true,'id'=>(int)$pdo->lastInsertId()]);
}
$stmt=$pdo->prepare("SELECT id FROM {$table} WHERE id=? AND role='collaborator'");$stmt->execute([$id]);if(!$stmt->fetchColumn())Support::json(['ok'=>false,'error'=>'Colaborador no encontrado.'],404);
if($action==='update'){$name=trim((string)($body['name']??''));$username=trim((string)($body['username']??''));$email=filter_var($body['email']??'',FILTER_VALIDATE_EMAIL);if($name===''||!preg_match('/^[A-Za-z0-9._-]{3,60}$/',$username)||!$email)Support::json(['ok'=>false,'error'=>'Datos inválidos.'],422);try{$pdo->prepare("UPDATE {$table} SET name=?,username=?,email=? WHERE id=? AND role='collaborator'")->execute([$name,$username,$email,$id]);}catch(Throwable){Support::json(['ok'=>false,'error'=>'El usuario o correo ya está registrado.'],409);}Support::json(['ok'=>true]);}
if($action==='block'){$pdo->prepare("UPDATE {$table} SET active=? WHERE id=? AND role='collaborator'")->execute([empty($body['active'])?0:1,$id]);Support::json(['ok'=>true]);}
if($action==='delete'){$pdo->prepare("DELETE FROM {$table} WHERE id=? AND role='collaborator'")->execute([$id]);Support::json(['ok'=>true]);}
if($action==='reset'){$password=bin2hex(random_bytes(8));$pdo->prepare("UPDATE {$table} SET password_hash=?,failed_attempts=0,failed_at=NULL WHERE id=? AND role='collaborator'")->execute([password_hash($password,PASSWORD_DEFAULT),$id]);Support::json(['ok'=>true,'temporaryPassword'=>$password]);}
Support::json(['ok'=>false,'error'=>'Acción no válida.'],422);
