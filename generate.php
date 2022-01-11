<?php
require_once __DIR__ . '/vendor/autoload.php';

require_once 'snipeit-config.php';
require_once 'classes.php';

$client = new APIRequest($token);

$type = $_POST['type'];
$assets = preg_split('/\r\n|\r|\n/', $_POST['assetid']);
$assets = str_replace(' ',"%20",$assets);
$item0 = $client->CallAPI('GET','hardware/bytag/'.$assets[0]);
$person = $client->CallAPI('GET','users/'.$item0['assigned_to']['id']);

#echo var_dump($item);
#echo var_dump($person);

// 1 dla przyjęcia, 0 dla oddania

$doc = new DocumentSnipeit($type, $person);
foreach ($assets as $asset) {
    if ($asset != null){
        $item = $client->CallAPI('GET','hardware/bytag/'.$asset);
        if ($item != null){
            $doc->addItem($item);
        }
    } 
    
}
$doc->prepare();

$mpdf = new \Mpdf\Mpdf();
$css = file_get_contents('style.css');
$mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($doc->html, \Mpdf\HTMLParserMode::HTML_BODY);
#$mpdf->Output();
$mpdf->Output("pdfs/".$doc->filename, \Mpdf\Output\Destination::FILE);
header('Location: http://snipeit.riokrakow.local/docs/pdfs/'.$doc->filename);

?>