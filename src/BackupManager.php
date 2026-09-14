<?php
declare(strict_types=1);

namespace AranduGo;

use PDO;
use RuntimeException;
use ZipArchive;

final class BackupManager
{
    private const FORMAT='arandu-go-portable-backup';
    private const TABLES=['users','site_profiles','contact_messages','password_resets','migrations','update_history'];
    public function __construct(private string $root,private array $config){$this->root=rtrim((string)(realpath($root)?:$root),'/');}

    public function create(?string $destination=null):string
    {
        if(!class_exists(ZipArchive::class))throw new RuntimeException('La extensión ZIP no está habilitada.');
        $destination??=$this->root.'/storage/backups/arandu-go-completo-'.date('Ymd-His').'.zip';
        $tmp=$destination.'.part';$zip=new ZipArchive();if($zip->open($tmp,ZipArchive::CREATE|ZipArchive::OVERWRITE)!==true)throw new RuntimeException('No se pudo crear el respaldo.');
        $inventory=[];$add=function(string $name,string $bytes)use($zip,&$inventory):void{if(!$zip->addFromString($name,$bytes))throw new RuntimeException('No se pudo respaldar '.$name);$inventory[$name]=hash('sha256',$bytes);};
        $pdo=Database::connection();foreach(self::TABLES as $name){try{$rows=$pdo->query('SELECT * FROM '.Database::table($name))->fetchAll(PDO::FETCH_ASSOC);}catch(\Throwable){continue;}if($name==='users')foreach($rows as &$row)unset($row['password_hash'],$row['last_ip_hash'],$row['failed_attempts'],$row['failed_at']);unset($row);$add('database/'.$name.'.json',json_encode($rows,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR));}
        $uploads=$this->root.'/uploads';if(is_dir($uploads)){$it=new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($uploads,\FilesystemIterator::SKIP_DOTS));foreach($it as $file){if(!$file->isFile()||$file->isLink())continue;$relative='uploads/'.str_replace('\\','/',substr($file->getPathname(),strlen($uploads)+1));$bytes=(string)file_get_contents($file->getPathname());$add($relative,$bytes);}}
        $url=rtrim((string)($this->config['app']['url']??''),'/');$path=(string)(parse_url($url,PHP_URL_PATH)??'');
        $manifest=['format'=>self::FORMAT,'format_version'=>1,'product'=>'arandu-go','app_version'=>UpdateManager::installedVersion($this->root,$this->config),'created_at'=>date(DATE_ATOM),'timezone'=>date_default_timezone_get(),'origin'=>['host'=>(string)(parse_url($url,PHP_URL_HOST)??''),'base_url'=>$url,'subdirectory'=>$path],'table_prefix'=>(string)$this->config['database']['prefix'],'components'=>['content_design','uploads','users_roles','form_log'],'inventory'=>$inventory];
        $manifest['manifest_sha256']=hash('sha256',json_encode($manifest,JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR));$zip->addFromString('manifest.json',json_encode($manifest,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR));$zip->addFromString('README.txt',"Respaldo portable Arandu Go. No contiene contraseñas de MySQL, SMTP, sesiones ni claves privadas.\n");
        if(!$zip->close()||!rename($tmp,$destination))throw new RuntimeException('No se pudo finalizar el respaldo.');return $destination;
    }

    public function inspect(string $archive):array
    {
        $zip=$this->openSafe($archive);$raw=$zip->getFromName('manifest.json');if($raw===false){$zip->close();throw new RuntimeException('No es un respaldo completo de Arandu Go.');}$manifest=json_decode($raw,true);if(!is_array($manifest)||($manifest['format']??'')!==self::FORMAT){$zip->close();throw new RuntimeException('El ZIP no tiene el formato de respaldo completo.');}
        foreach($manifest['inventory']??[] as $name=>$hash){$bytes=$zip->getFromName((string)$name);if($bytes===false||!hash_equals((string)$hash,hash('sha256',$bytes))){$zip->close();throw new RuntimeException('Falló la integridad de '.$name);}}$zip->close();return $manifest;
    }

    public function restore(string $archive,array $components):array
    {
        $manifest=$this->inspect($archive);$preventive=$this->create();$zip=$this->openSafe($archive);$pdo=Database::connection();$pdo->beginTransaction();$changed=[];
        try{
            $tableMap=[];if(in_array('content_design',$components,true))$tableMap[]='site_profiles';if(in_array('users_roles',$components,true))$tableMap[]='users';if(in_array('form_log',$components,true))$tableMap[]='contact_messages';
            foreach($tableMap as $name){$raw=$zip->getFromName('database/'.$name.'.json');if($raw===false)continue;$rows=json_decode($raw,true,512,JSON_THROW_ON_ERROR);$table=Database::table($name);$passwords=[];if($name==='users')foreach($pdo->query('SELECT username,password_hash FROM '.$table)->fetchAll(PDO::FETCH_ASSOC) as $local)$passwords[(string)$local['username']]=(string)$local['password_hash'];$pdo->exec('DELETE FROM '.$table);foreach($rows as $row){if($name==='users'){if(!isset($passwords[(string)($row['username']??'')]))continue;$row['password_hash']=$passwords[(string)$row['username']];}if(!$row)continue;$columns=array_keys($row);foreach($columns as $column)if(!preg_match('/^[A-Za-z0-9_]+$/',(string)$column))throw new RuntimeException('Columna inválida.');$sql='INSERT INTO '.$table.' ('.implode(',',array_map(fn($v)=>'`'.$v.'`',$columns)).') VALUES ('.implode(',',array_fill(0,count($columns),'?')).')';$pdo->prepare($sql)->execute(array_values($row));}$changed[]=$name;}
            if(in_array('uploads',$components,true)){for($i=0;$i<$zip->numFiles;$i++){$name=(string)$zip->getNameIndex($i);if(!str_starts_with($name,'uploads/')||str_ends_with($name,'/'))continue;$relative=substr($name,8);$dest=$this->root.'/uploads/'.$relative;$realParent=realpath(dirname($dest));if($realParent!==false&&!str_starts_with($realParent,$this->root.'/uploads'))throw new RuntimeException('Ruta de archivo no autorizada.');if(!is_dir(dirname($dest))&&!mkdir(dirname($dest),0755,true))throw new RuntimeException('No se pudo crear una carpeta de archivos.');if(file_put_contents($dest,$zip->getFromName($name),LOCK_EX)===false)throw new RuntimeException('No se pudo restaurar '.$relative);}$changed[]='uploads';}
            $pdo->commit();$zip->close();return ['manifest'=>$manifest,'preventive_backup'=>basename($preventive),'components'=>$changed];
        }catch(\Throwable $e){if($pdo->inTransaction())$pdo->rollBack();$zip->close();throw $e;}
    }

    private function openSafe(string $archive):ZipArchive
    {
        if(!is_file($archive)||filesize($archive)>300*1024*1024)throw new RuntimeException('Respaldo ausente o demasiado grande.');$zip=new ZipArchive();if($zip->open($archive)!==true)throw new RuntimeException('El ZIP no es válido.');if($zip->numFiles<2||$zip->numFiles>5000){$zip->close();throw new RuntimeException('Cantidad de archivos no permitida.');}$total=0;for($i=0;$i<$zip->numFiles;$i++){$name=(string)$zip->getNameIndex($i);$stat=$zip->statIndex($i);$total+=(int)($stat['size']??0);if($total>500*1024*1024||$name===''||str_contains($name,"\0")||str_starts_with($name,'/')||preg_match('~(^|/)\.\.(/|$)~',$name)){ $zip->close();throw new RuntimeException('El ZIP contiene una ruta o tamaño inseguro.');}if(preg_match('/\.(?:php\d*|phtml|phar|cgi|pl|py|sh)$/i',$name)){ $zip->close();throw new RuntimeException('El respaldo contiene un ejecutable inesperado.');}if(method_exists($zip,'getExternalAttributesIndex')){$os=0;$attr=0;if($zip->getExternalAttributesIndex($i,$os,$attr)&&(($attr>>16)&0170000)===0120000){$zip->close();throw new RuntimeException('No se permiten enlaces simbólicos.');}}}return $zip;
    }
}
