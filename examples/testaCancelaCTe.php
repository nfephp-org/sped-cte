<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once '../bootstrap.php';

use NFePHP\CTe\Tools;

$cteTools = new Tools('../config/config.json');

$aResposta = array();
$chave = '41160981450900000132570020000000581000000108';
$nProt = '141160000041280';
$tpAmb = '2';
$xJust = 'teste da BTR feito por Maison e Marcio'; // MINIMO DE 15 DIGITOS
$retorno = $cteTools->sefazCancela($chave, $tpAmb, $xJust, $nProt, $aResposta);
echo '<pre>';
//echo htmlspecialchars($cteTools->soapDebug);
print_r($aResposta);
echo "</pre>";