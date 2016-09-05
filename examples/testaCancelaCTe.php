<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once '../bootstrap.php';

use NFePHP\CTe\Tools;

$cteTools = new Tools('../config/config.json');

$aResposta = array();
$chave = '43160911004958000177570010000000111000000103';
$nProt = '143160000453731';
$tpAmb = '2';
$xJust = 'testandohahahahhahahahahahhahahahhahaha';
$retorno = $cteTools->sefazCancela($chave, $tpAmb, $xJust, $nProt, $aResposta);
echo '<pre>';
//echo htmlspecialchars($cteTools->soapDebug);
print_r($aResposta);
echo "</pre>";