<?php
declare(strict_types=1);
namespace AranduGo;

use PDO;
use RuntimeException;
use ZipArchive;

final class UpdateManager
{
    private const PROTECTED = ['config/config.php','storage/installed.lock','storage/logs','storage/backups','storage/updates','uploads'];
    private const ROOT_FILES = ['.htaccess','bootstrap.php','composer.json','config/system.php','config/version.php','contact.php','index.php','manifest.json','privacidad.php','README.md','terminos.php'];
    private const ROOT_DIRS = ['assets','dashboardcliente','database/migrations','partials','src','update','vendor'];

    public function __construct(private string $root, private array $config) {$this->root=rtrim($this->root,'/');}

    public static function installedVersion(string $root,array $config=[]):string
    {
        $file=rtrim($root,'/').'/config/version.php';
        if(is_file($file)){ $v=require $file; if(is_array($v)&&!empty($v['version']))return (string)$v['version']; }
        return (string)($config['app']['version']??'1.0.0');
    }

    public function applyArchive(string $archive,bool $allowDowngrade=false):array
    {
        if(!class_exists(ZipArchive::class))throw new RuntimeException('La extensión ZIP no está habilitada.');
        if(!is_file($archive))throw new RuntimeException('No se encontró el paquete.');
        $stage=$this->root.'/storage/updates/'.date('Ymd-His').'-'.bin2hex(random_bytes(4));
        if(!is_dir($stage)&&!mkdir($stage,0750,true))throw new RuntimeException('No se pudo preparar la actualización.');
        $zip=new ZipArchive();if($zip->open($archive)!==true)throw new RuntimeException('El ZIP no es válido.');
        if($zip->numFiles<1||$zip->numFiles>5000){$zip->close();throw new RuntimeException('El paquete contiene una cantidad de archivos no permitida.');}
        $expanded=0;for($i=0;$i<$zip->numFiles;$i++){$name=(string)$zip->getNameIndex($i);$stat=$zip->statIndex($i);$expanded+=(int)($stat['size']??0);if($expanded>300*1024*1024){$zip->close();throw new RuntimeException('El paquete descomprimido supera los 300 MB.');}if($name===''||str_contains($name,"\0")||str_starts_with($name,'/')||preg_match('~(^|/)\.\.(/|$)~',$name)){ $zip->close();throw new RuntimeException('El paquete contiene una ruta insegura.');}if(method_exists($zip,'getExternalAttributesIndex')){$opsys=0;$attr=0;if($zip->getExternalAttributesIndex($i,$opsys,$attr)&&(($attr>>16)&0170000)===0120000){$zip->close();throw new RuntimeException('El paquete no puede contener enlaces simbólicos.');}}}
        if(!$zip->extractTo($stage)){ $zip->close();throw new RuntimeException('No se pudo descomprimir el paquete.');}$zip->close();
        try{$package=$this->findPackageRoot($stage);return $this->applyDirectory($package,$allowDowngrade);}finally{$this->removeTree($stage);@unlink($archive);}
    }

    public function applyDirectory(string $package,bool $allowDowngrade=false):array
    {
        $package=rtrim(realpath($package)?:$package,'/');$manifest=json_decode((string)file_get_contents($package.'/manifest.json'),true);
        if(!is_array($manifest)||($manifest['product']??'')!=='arandu-go'||!in_array(($manifest['package_type']??''),['update','rollback'],true))throw new RuntimeException('El archivo no es una actualización de Arandu Go.');
        $from=self::installedVersion($this->root,$this->config);$to=(string)($manifest['version']??'');
        if(!version_compare(PHP_VERSION,(string)($manifest['minimum_php']??'8.1.0'),'>='))throw new RuntimeException('La versión de PHP no cumple el requisito del paquete.');
        if($to===''||version_compare($to,$from,'='))throw new RuntimeException("El paquete {$to} coincide con la versión instalada {$from}.");
        $downgrade=version_compare($to,$from,'<');
        if($downgrade&&!$allowDowngrade)throw new RuntimeException("El paquete {$to} es anterior a la versión instalada {$from}. Elegí explícitamente Rollback para revertir.");
        if(!$downgrade&&!version_compare($from,(string)($manifest['minimum_installed_version']??'0.0.0'),'>='))throw new RuntimeException('La versión instalada es demasiado antigua para esta actualización.');
        $backup=$this->backup($from,$to);$existing=$this->managedFiles();
        try{if($downgrade)$this->removeManagedFilesMissingFromPackage($package);$this->copyPackage($package);$migrations=$downgrade?[]:$this->runMigrations($package);$status=$downgrade?'rollback_completed':'completed';$this->record($from,$to,$status,$backup,implode(', ',$migrations));return ['from'=>$from,'to'=>$to,'backup'=>$backup,'migrations'=>$migrations,'operation'=>$downgrade?'rollback':'update'];}
        catch(\Throwable $e){$this->restoreFiles($backup);$this->restoreDatabase($backup);$this->removeNewManagedFiles($existing);$this->record($from,$to,'failed',$backup,$e->getMessage());throw $e;}
    }

    private function findPackageRoot(string $stage):string
    {
        if(is_file($stage.'/manifest.json'))return $stage;
        $items=array_values(array_filter(scandir($stage)?:[],fn($x)=>$x!=='.'&&$x!=='..'));
        foreach($items as $item)if(is_file($stage.'/'.$item.'/manifest.json'))return $stage.'/'.$item;
        throw new RuntimeException('No se encontró manifest.json en el paquete.');
    }

    private function backup(string $from,string $to):string
    {
        $dir=$this->root.'/storage/backups';if(!is_dir($dir)&&!mkdir($dir,0750,true))throw new RuntimeException('No se pudo crear la carpeta de respaldos.');
        $file=$dir.'/pre-update-'.$from.'-to-'.$to.'-'.date('Ymd-His').'.zip';$zip=new ZipArchive();if($zip->open($file,ZipArchive::CREATE|ZipArchive::OVERWRITE)!==true)throw new RuntimeException('No se pudo crear el respaldo.');
        foreach(self::ROOT_FILES as $path)if(is_file($this->root.'/'.$path))$zip->addFile($this->root.'/'.$path,'files/'.$path);
        foreach(self::ROOT_DIRS as $dirPath)$this->addDirectory($zip,$this->root.'/'.$dirPath,'files/'.$dirPath);
        $this->addDirectory($zip,$this->root.'/uploads','protected/uploads');
        if(is_file($this->root.'/config/config.php'))$zip->addFile($this->root.'/config/config.php','protected/config/config.php');
        $pdo=Database::connection();foreach(['users','site_profiles','contact_messages','password_resets','migrations','update_history'] as $table){$rows=$pdo->query('SELECT * FROM '.Database::table($table))->fetchAll();$json=json_encode($rows,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR);if(!$zip->addFromString('database/'.$table.'.json',$json))throw new RuntimeException('No se pudo respaldar la tabla '.$table.'.');}
        if(!$zip->close())throw new RuntimeException('No se pudo finalizar el respaldo.');return $file;
    }

    private function addDirectory(ZipArchive $zip,string $source,string $inside):void
    {
        if(!is_dir($source))return;$it=new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source,\FilesystemIterator::SKIP_DOTS));
        foreach($it as $file)if($file->isFile())$zip->addFile($file->getPathname(),$inside.'/'.substr($file->getPathname(),strlen($source)+1));
    }

    private function copyPackage(string $package):void
    {
        foreach(self::ROOT_FILES as $path)if(is_file($package.'/'.$path))$this->copyFile($package.'/'.$path,$this->root.'/'.$path);
        foreach(self::ROOT_DIRS as $path)if(is_dir($package.'/'.$path))$this->copyDirectory($package.'/'.$path,$this->root.'/'.$path);
    }

    private function copyDirectory(string $source,string $dest):void
    {
        if(!is_dir($dest)&&!mkdir($dest,0755,true))throw new RuntimeException('No se pudo crear '.$dest);$it=new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source,\FilesystemIterator::SKIP_DOTS));
        foreach($it as $file){if(!$file->isFile())continue;$relative=substr($file->getPathname(),strlen($source)+1);$this->copyFile($file->getPathname(),$dest.'/'.$relative);}
    }

    private function copyFile(string $source,string $dest):void
    {
        $relative=ltrim(substr($dest,strlen($this->root)),'/');foreach(self::PROTECTED as $p)if($relative===$p||str_starts_with($relative,$p.'/'))return;
        $dir=dirname($dest);if(!is_dir($dir)&&!mkdir($dir,0755,true))throw new RuntimeException('No se pudo crear '.$dir);$tmp=$dest.'.arandu-new';if(!copy($source,$tmp)||!rename($tmp,$dest))throw new RuntimeException('No se pudo actualizar '.$relative);
    }

    private function runMigrations(string $package):array
    {
        $pdo=Database::connection();$prefix=(string)$this->config['database']['prefix'];if(!preg_match('/^[A-Za-z0-9_]+$/',$prefix))throw new RuntimeException('Prefijo de base inválido.');
        $bootstrap=$package.'/database/migrations/002_update_history.php';if(is_file($bootstrap)){($migration=require $bootstrap)($pdo,$prefix);}
        $done=[];$files=glob($package.'/database/migrations/*.php')?:[];sort($files,SORT_NATURAL);
        foreach($files as $file){
            $name=basename($file);$stmt=$pdo->prepare("SELECT COUNT(*) FROM `{$prefix}migrations` WHERE migration=?");$stmt->execute([$name]);if((int)$stmt->fetchColumn()>0)continue;
            $pdo->beginTransaction();
            try{
                $migration=require $file;if(!is_callable($migration))throw new RuntimeException('Migración inválida: '.$name);$migration($pdo,$prefix);
                /* MySQL confirma implícitamente CREATE/ALTER TABLE. Solo se
                   confirma aquí cuando la transacción continúa activa. */
                $pdo->prepare("INSERT IGNORE INTO `{$prefix}migrations`(migration) VALUES(?)")->execute([$name]);
                if($pdo->inTransaction())$pdo->commit();
                $done[]=$name;
            }catch(\Throwable $e){if($pdo->inTransaction())$pdo->rollBack();throw $e;}
        }
        return $done;
    }

    private function restoreFiles(string $backup):void
    {
        $zip=new ZipArchive();if($zip->open($backup)!==true)return;for($i=0;$i<$zip->numFiles;$i++){$name=(string)$zip->getNameIndex($i);if(!str_starts_with($name,'files/')||str_ends_with($name,'/'))continue;$relative=substr($name,6);$dest=$this->root.'/'.$relative;$dir=dirname($dest);if(!is_dir($dir))mkdir($dir,0755,true);copy('zip://'.$backup.'#'.$name,$dest);}$zip->close();
    }

    private function restoreDatabase(string $backup):void
    {
        $zip=new ZipArchive();if($zip->open($backup)!==true)throw new RuntimeException('No se pudo abrir el respaldo para restaurar la base.');$pdo=Database::connection();$tables=['users','site_profiles','contact_messages','password_resets','migrations','update_history'];
        $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
        try{foreach($tables as $name){$json=$zip->getFromName('database/'.$name.'.json');if($json===false)continue;$rows=json_decode($json,true,512,JSON_THROW_ON_ERROR);$table=Database::table($name);$pdo->exec('DELETE FROM '.$table);foreach($rows as $row){if(!is_array($row)||!$row)continue;$columns=array_keys($row);foreach($columns as $column)if(!preg_match('/^[A-Za-z0-9_]+$/',(string)$column))throw new RuntimeException('Columna inválida en respaldo.');$quoted=implode(',',array_map(fn($c)=>'`'.$c.'`',$columns));$marks=implode(',',array_fill(0,count($columns),'?'));$pdo->prepare("INSERT INTO {$table} ({$quoted}) VALUES ({$marks})")->execute(array_values($row));}}}finally{$pdo->exec('SET FOREIGN_KEY_CHECKS=1');$zip->close();}
    }

    private function managedFiles():array
    {
        $files=[];foreach(self::ROOT_FILES as $path)if(is_file($this->root.'/'.$path))$files[$path]=true;foreach(self::ROOT_DIRS as $dir){$base=$this->root.'/'.$dir;if(!is_dir($base))continue;$it=new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($base,\FilesystemIterator::SKIP_DOTS));foreach($it as $file)if($file->isFile())$files[ltrim(substr($file->getPathname(),strlen($this->root)),'/')]=true;}return $files;
    }

    private function removeNewManagedFiles(array $before):void
    {
        foreach(array_keys($this->managedFiles()) as $relative)if(!isset($before[$relative]))@unlink($this->root.'/'.$relative);
    }

    private function removeManagedFilesMissingFromPackage(string $package):void
    {
        foreach(array_keys($this->managedFiles()) as $relative)if(!is_file($package.'/'.$relative))@unlink($this->root.'/'.$relative);
    }

    private function removeTree(string $dir):void
    {
        if(!is_dir($dir))return;$it=new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir,\FilesystemIterator::SKIP_DOTS),\RecursiveIteratorIterator::CHILD_FIRST);foreach($it as $item){if($item->isDir()&&!$item->isLink())@rmdir($item->getPathname());else @unlink($item->getPathname());}@rmdir($dir);
    }

    private function record(string $from,string $to,string $status,string $backup,string $details):void
    {
        try{$table=Database::table('update_history');$stmt=Database::connection()->prepare("INSERT INTO {$table}(from_version,to_version,status,backup_file,details) VALUES(?,?,?,?,?)");$stmt->execute([$from,$to,$status,basename($backup),$details]);}catch(\Throwable){}
    }
}
