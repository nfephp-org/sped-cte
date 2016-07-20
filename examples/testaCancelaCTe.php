<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once '../bootstrap.php';

use NFePHP\CTe\Tools;

$cteTools = new Tools('../config/config.json');

$aResposta = array();
$chave = '43160711004958000177570010000000101000000109';
$nProt = '431000008846409';
$tpAmb = '2';
$xJust = 'Teste de cancelamento em ambiente de homologação';
$retorno = $cteTools->sefazCancela($chave, $tpAmb, $xJust, $nProt, $aResposta);
echo '<br><br><PRE>';
echo htmlspecialchars($cteTools->soapDebug);
echo '</PRE><BR>';
print_r($aResposta);
echo "<br>";