<?php
declare(strict_types=1);

$json=file_get_contents(__DIR__.'/demo-content.json');
$site=json_decode((string)$json,true,512,JSON_THROW_ON_ERROR);
$relative=static fn(string $path):string=>ltrim($path,'/');
$site['header']['logo']='uploads/demo-contador/Estudio-Contable-Horizonte.png';
$site['hero']['image']=$relative((string)($site['hero']['image']??''));
foreach($site['gallery']??[] as $index=>$photo)$site['gallery'][$index]['image']=$relative((string)($photo['image']??''));
foreach($site['freeSections']??[] as $index=>$section){
    $site['freeSections'][$index]['mediaPosition']=$section['mediaPosition']??'left';
    $site['freeSections'][$index]['mediaFit']='cover';
    $site['freeSections'][$index]['mediaFocus']='center';
    foreach($section['media']??[] as $mediaIndex=>$media)$site['freeSections'][$index]['media'][$mediaIndex]['url']=$relative((string)($media['url']??''));
}
$site['freeSections'][2]['media']=[['type'=>'youtube','url'=>'https://youtu.be/qfUTe03pDVQ','caption'=>'Video demostrativo']];
$site['freeSections'][3]['media']=[['type'=>'image','url'=>'uploads/demo-contador/FAQ.png','caption'=>'Preguntas frecuentes']];
$site['gallerySettings']=['title'=>'Una forma más clara de trabajar','introHtml'=>'<p>Conocé algunos momentos ficticios que representan la planificación, el acompañamiento y la organización del estudio.</p>'];
$site['wizardState']=['currentStep'=>1,'completed'=>[],'skipped'=>[],'finished'=>false];
$site['demo']=['active'=>true,'notice'=>'Este sitio contiene datos de demostración. Reemplazá la información antes de publicarlo.','fields'=>['identity','contact','form','legal','content'],'progress'=>['identity'=>false,'contact'=>false,'location'=>false,'services'=>false,'images'=>false,'socials'=>false,'form'=>false,'legal'=>false]];
$site['seo']['allowIndexing']=false;
$site['contactForm']['demoMode']=true;
$site['contactForm']['recipientEmail']='';
return $site;
