<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once '../bootstrap.php';

use NFePHP\CTe\Tools;

$cteTools = new Tools('../config/config.json');

$aResposta = array();
$chave = '43160672044530000142570010000000101000000101';
$tpAmb = '2';
$retorno = $cteTools->sefazConsultaChave($chave, $tpAmb, $aResposta);
echo '<br><br><PRE>';
echo htmlspecialchars($cteTools->soapDebug);
echo '</PRE><BR>';
//print_r($aResposta);
print_r($retorno);
echo "<br>";
