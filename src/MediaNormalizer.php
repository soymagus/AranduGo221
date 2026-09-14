<?php
declare(strict_types=1);

namespace AranduGo;

final class MediaNormalizer
{
    public static function youtubeId(string $value): ?string
    {
        $value=trim($value);
        if(preg_match('/^[A-Za-z0-9_-]{11}$/',$value))return $value;
        $parts=parse_url($value);if(!is_array($parts))return null;
        $host=strtolower((string)($parts['host']??''));$host=preg_replace('/^www\./','',$host);
        if(!in_array($host,['youtube.com','m.youtube.com','youtu.be'],true))return null;
        $path=trim((string)($parts['path']??''),'/');$id='';
        if($host==='youtu.be')$id=explode('/',$path)[0]??'';
        elseif($path==='watch'){parse_str((string)($parts['query']??''),$query);$id=(string)($query['v']??'');}
        elseif(preg_match('~^(?:embed|shorts|live)/([^/]+)~',$path,$m))$id=$m[1];
        return preg_match('/^[A-Za-z0-9_-]{11}$/',$id)?$id:null;
    }

    public static function youtube(string $value): ?array
    {
        $id=self::youtubeId($value);if($id===null)return null;
        return ['id'=>$id,'embed'=>'https://www.youtube.com/embed/'.$id,'watch'=>'https://www.youtube.com/watch?v='.$id];
    }

    public static function map(string $value): ?array
    {
        $value=trim($value);if($value==='')return null;
        if(preg_match('/^(-?\d+(?:\.\d+)?)[,\s]+(-?\d+(?:\.\d+)?)$/',$value,$m)){
            $lat=(float)$m[1];$lng=(float)$m[2];if(abs($lat)>90||abs($lng)>180)return null;$query=$lat.','.$lng;
        }elseif(filter_var($value,FILTER_VALIDATE_URL)){
            $parts=parse_url($value);$host=strtolower((string)($parts['host']??''));
            if(!preg_match('/(^|\.)google\.[a-z.]+$|(^|\.)goo\.gl$|(^|\.)maps\.app\.goo\.gl$/',$host))return null;
            parse_str((string)($parts['query']??''),$args);$query=(string)($args['q']??$args['query']??'');
            if($query===''&&preg_match('~/place/([^/]+)~',(string)($parts['path']??''),$m))$query=urldecode($m[1]);
            if($query==='')$query=$value;
        }else $query=$value;
        return ['embed'=>'https://www.google.com/maps?q='.rawurlencode($query).'&output=embed','open'=>'https://www.google.com/maps/search/?api=1&query='.rawurlencode($query)];
    }
}
