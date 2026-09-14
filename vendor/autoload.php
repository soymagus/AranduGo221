<?php
declare(strict_types=1);
spl_autoload_register(static function(string $class):void{
 $prefix='AranduGo\\';
 if(str_starts_with($class,$prefix)){$file=dirname(__DIR__).'/src/'.substr($class,strlen($prefix)).'.php';if(is_file($file))require $file;return;}
 $mailPrefix='PHPMailer\\PHPMailer\\';
 if(str_starts_with($class,$mailPrefix)){$file=__DIR__.'/phpmailer/phpmailer/src/'.substr($class,strlen($mailPrefix)).'.php';if(is_file($file))require $file;}
});
