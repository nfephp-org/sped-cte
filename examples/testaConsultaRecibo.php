<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once '../bootstrap.php';

use NFePHP\CTe\Tools;

$cteTools = new Tools('../config/config.json');

$aResposta = array();
$recibo = '431000008782133';
$tpAmb = '2';
$retorno = $cteTools->sefazConsultaRecibo($recibo, $tpAmb, $aResposta);
echo '<br><br><pre>';
//echo htmlspecialchars($cteTools->soapDebug);
echo '</pre><br>';
//print_r($aResposta);
print_r($retorno);
echo "<br>";
