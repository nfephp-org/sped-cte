<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once '../bootstrap.php';

use NFePHP\CTe\Tools;

$cteTools = new Tools('../config/config.json');

$aResposta = array();
$cteTools->aConfig['siglaUF']='SC';
$recibo = '423000010444201'; 
//$recibo = '411000000741429';
$tpAmb = '2';
$retorno = $cteTools->sefazConsultaRecibo($recibo, $tpAmb, $aResposta);
echo '<pre>';
//echo htmlspecialchars($cteTools->soapDebug);
print_r($aResposta);
echo "</pre>";
