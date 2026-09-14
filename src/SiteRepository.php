<?php
declare(strict_types=1);

namespace AranduGo;

final class SiteRepository
{
    public static function draft(): array
    {
        $draft=self::load('draft_json');
        return self::isProfile($draft)?$draft:self::published();
    }
    public static function published(): array { return self::load('published_json'); }

    private static function load(string $column): array
    {
        $table = Database::table('site_profiles');
        $row = Database::connection()->query("SELECT {$column} FROM {$table} WHERE id=1")->fetch();
        return json_decode((string) ($row[$column] ?? '{}'), true) ?: [];
    }

    private static function isProfile(array $data): bool
    {
        return trim((string)($data['business']['name']??''))!==''
            && count($data['modules']??[])>0;
    }

    public static function save(array $data, bool $publish): void
    {
        foreach($data['freeSections']??[] as $index=>$section)if(is_array($section)){
            $data['freeSections'][$index]['html']=self::sanitizeRichHtml((string)($section['html']??''));
            foreach($section['media']??[] as $mediaIndex=>$media){
                $type=(string)($media['type']??'image');$url=trim((string)($media['url']??''));
                if(!in_array($type,['image','gallery','youtube','video','audio','playlist'],true))throw new \InvalidArgumentException('La sección libre '.($index+1).' contiene un tipo multimedia no permitido.');
                if($url==='')continue;
                if(!preg_match('~^(?:https?://|/|uploads/)~i',$url))throw new \InvalidArgumentException('La sección libre '.($index+1).' contiene una URL multimedia inválida.');
                if($type==='youtube'){$normalized=MediaNormalizer::youtube($url);if($normalized===null)throw new \InvalidArgumentException('La sección libre '.($index+1).' contiene un enlace de YouTube inválido.');$data['freeSections'][$index]['media'][$mediaIndex]['url']=$normalized['watch'];}
            }
        }
        if(!empty($data['business']['mapsUrl'])){$map=MediaNormalizer::map((string)$data['business']['mapsUrl']);if($map===null)throw new \InvalidArgumentException('El mapa no es válido.');$data['business']['mapsUrl']=$map['open'];$data['business']['mapsEmbedUrl']=$map['embed'];}
        if(!empty($data['demo']['active'])){$data['seo']['allowIndexing']=false;$data['contactForm']['demoMode']=true;$data['contactForm']['recipientEmail']='';}
        $data['gallerySettings']??=['title'=>'Imágenes del negocio','introHtml'=>''];
        $data['gallerySettings']['introHtml']=self::sanitizeRichHtml((string)($data['gallerySettings']['introHtml']??''));
        if(isset($data['legal']['termsHtml']))$data['legal']['termsHtml']=self::sanitizeRichHtml((string)$data['legal']['termsHtml']);
        if(isset($data['legal']['privacyHtml']))$data['legal']['privacyHtml']=self::sanitizeRichHtml((string)$data['legal']['privacyHtml']);
        $table = Database::table('site_profiles'); $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($publish) {
            $stmt=Database::connection()->prepare("UPDATE {$table} SET draft_json=?, published_json=?, published_at=NOW(), updated_at=NOW() WHERE id=1"); $stmt->execute([$json,$json]);
        } else {
            $stmt=Database::connection()->prepare("UPDATE {$table} SET draft_json=?, updated_at=NOW() WHERE id=1"); $stmt->execute([$json]);
        }
    }

    private static function sanitizeRichHtml(string $html):string
    {
        if($html===''||!class_exists(\DOMDocument::class))return strip_tags($html,'<p><br><strong><b><em><i><u><s><strike><sub><sup><ul><ol><li><h1><h2><h3><h4><h5><h6><blockquote><a><table><thead><tbody><tfoot><tr><th><td><hr><span><div>');
        $dom=new \DOMDocument('1.0','UTF-8');$previous=libxml_use_internal_errors(true);$dom->loadHTML('<?xml encoding="utf-8" ?><div id="arandu-root">'.$html.'</div>',LIBXML_HTML_NOIMPLIED|LIBXML_HTML_NODEFDTD|LIBXML_NONET);libxml_clear_errors();libxml_use_internal_errors($previous);
        $allowed=['div','span','p','br','strong','b','em','i','u','s','strike','sub','sup','ul','ol','li','h1','h2','h3','h4','h5','h6','blockquote','a','table','thead','tbody','tfoot','tr','th','td','hr'];
        $walker=function(\DOMNode $node)use(&$walker,$allowed):void{for($child=$node->firstChild;$child;){$next=$child->nextSibling;if($child instanceof \DOMElement){$tag=strtolower($child->tagName);if(!in_array($tag,$allowed,true)){$walker($child);while($child->firstChild)$node->insertBefore($child->firstChild,$child);$node->removeChild($child);}else{foreach(iterator_to_array($child->attributes) as $attr){$name=strtolower($attr->name);if(str_starts_with($name,'on')){$child->removeAttribute($attr->name);continue;}if(!in_array($name,['href','title','target','rel','style','colspan','rowspan'],true)){$child->removeAttribute($attr->name);continue;}if($name==='href'){ $value=trim($attr->value);if($value!==''&&!preg_match('~^(?:https?://|mailto:|tel:|/|#)~i',$value))$child->removeAttribute($attr->name);}if($name==='style'){preg_match_all('/(?:^|;)\s*(text-align|color|background-color)\s*:\s*([^;]+)/i',$attr->value,$matches,PREG_SET_ORDER);$safe=[];foreach($matches as $match){$property=strtolower($match[1]);$value=trim($match[2]);if($property==='text-align'&&in_array(strtolower($value),['left','center','right','justify'],true))$safe[]=$property.':'.strtolower($value);elseif(in_array($property,['color','background-color'],true)&&preg_match('/^(?:#[0-9a-f]{3,8}|rgb\(\s*\d{1,3}\s*,\s*\d{1,3}\s*,\s*\d{1,3}\s*\)|[a-z]{3,20})$/i',$value))$safe[]=$property.':'.$value;}if($safe)$child->setAttribute('style',implode(';',$safe));else$child->removeAttribute('style');}}if($tag==='a'&&$child->getAttribute('target')==='_blank')$child->setAttribute('rel','noopener noreferrer');$walker($child);}}$child=$next;}};$root=$dom->getElementById('arandu-root');if(!$root)return'';$walker($root);$out='';foreach($root->childNodes as $child)$out.=$dom->saveHTML($child);return $out;
    }
}
