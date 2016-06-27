<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once '../bootstrap.php';

use NFePHP\CTe\Tools;

$cteTools = new Tools('../config/config.json');

$aResposta = array();
$chave = '43160672044530000142570010000000101000000101';
$nProt = '431000008774668';
$tpAmb = '2';
$xJust = 'Teste de cancelamento em ambiente de homologação';
$retorno = $cteTools->sefazCancela($chave, $tpAmb, $xJust, $nProt, $aResposta);
echo '<br><br><PRE>';
echo htmlspecialchars($cteTools->soapDebug);
echo '</PRE><BR>';
print_r($aResposta);
echo "<br>";