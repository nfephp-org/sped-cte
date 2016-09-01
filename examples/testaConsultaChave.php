<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once '../bootstrap.php';

use NFePHP\CTe\Tools;

$cteTools = new Tools('../config/config.json');

$aResposta = array();
$chave = '43160911004958000177570010000000101000000106';
$tpAmb = '2';
$retorno = $cteTools->sefazConsultaChave($chave, $tpAmb, $aResposta);
echo '<pre>';
//echo htmlspecialchars($cteTools->soapDebug);
print_r($aResposta);
//print_r($retorno);
echo '</pre>';

