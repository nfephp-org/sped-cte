<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once '../bootstrap.php';

use NFePHP\CTe\Tools;

$cteTools = new Tools('../config/config.json');

$aResposta = array();
$siglaUF = 'RS';
$tpAmb = '2';
$retorno = $cteTools->sefazStatus($siglaUF, $tpAmb, $aResposta);
echo '<br><br><pre>';
echo htmlspecialchars($cteTools->soapDebug);
echo '</pre><br><br><pre>';
print_r($aResposta);
echo "</pre><br>";