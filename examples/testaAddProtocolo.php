<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once '../bootstrap.php';

use NFePHP\CTe\Tools;

$cteTools = new Tools('../config/config.json');
//$doccte = new Dom();

$aResposta = array();

$indSinc = '0'; //0=asíncrono, 1=síncrono
$chave = '43161011004958000177570010000000281000000283';
$recibo = '431000010482077';
$pathCTeFile = "../xml/{$chave}-cte.xml";

//if (file_exists($pathCTeFile)) {
//    $doccte->loadXMLFile($pathCTeFile);
//}

if (! $indSinc) {
    $pathProtfile = "../xml/cte/homologacao/temporarias/201610/{$recibo}-retConsReciCTe.xml";
} else {
    $pathProtfile = "../xml/cte//homologacao/temporarias/201610/{$recibo}-retEnviNFe.xml";
}
$saveFile = true;
$retorno = $cteTools->addProtocolo($pathCTeFile, $pathProtfile, $saveFile);
echo '<br><br><pre>';
echo htmlspecialchars($retorno);
echo "</pre><br>";
