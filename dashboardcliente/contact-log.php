<?php
require dirname(__DIR__).'/bootstrap.php';
use AranduGo\Auth;use AranduGo\Database;use AranduGo\Support;
Auth::requireLogin();$pdo=Database::connection();$table=Database::table('contact_messages');$pdo->exec("DELETE FROM {$table} WHERE created_at < DATE_SUB(NOW(), INTERVAL 180 DAY)");
$rows=$pdo->query("SELECT id,name,email,phone,subject,message,timezone,delivered,created_at FROM {$table} ORDER BY created_at DESC LIMIT 2000")->fetchAll();
if(($_GET['format']??'')==='txt'){
    header('Content-Type: text/plain; charset=utf-8');header('X-Content-Type-Options: nosniff');header('Content-Disposition: attachment; filename="arandu-go-formularios.txt"');
    $cell=static fn($value):string=>str_replace(["\t","\r","\n"],[' ',' ',' '],trim((string)$value));
    echo "ID\tFECHA_HORA\tZONA_HORARIA\tESTADO\tNOMBRE\tCORREO\tTELEFONO\tASUNTO\tMENSAJE\n";
    foreach($rows as $row){
        echo implode("\t",array_map($cell,[$row['id']??'', $row['created_at']??'', $row['timezone']??'', !empty($row['delivered'])?'ENTREGADO':'REGISTRADO', $row['name']??'', $row['email']??'', $row['phone']??'', $row['subject']??'', $row['message']??'[No conservado]']))."\n";
    }
    exit;
}
Support::json(['ok'=>true,'rows'=>$rows]);
