<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once '../bootstrap.php';

use NFePHP\CTe\Tools;

$cteTools = new Tools('../config/config.json');

$aResposta = array();
$siglaUF = 'PR';
$tpAmb = '2';
$retorno = $cteTools->sefazStatus($siglaUF, $tpAmb, $aResposta);
echo '<pre>';
//echo htmlspecialchars($cteTools->soapDebug);
print_r($aResposta);
echo "</pre>";