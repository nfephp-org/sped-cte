<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once '../bootstrap.php';

use NFePHP\CTe\Tools;

$cteTools = new Tools('../config/config.json');

$aResposta = array();
$recibo = '431000008820916';
$tpAmb = '2';
$retorno = $cteTools->sefazConsultaRecibo($recibo, $tpAmb, $aResposta);
echo '<br><br><pre>';
echo htmlspecialchars($cteTools->soapDebug);
echo '</pre><br>';

//foreach ($aResposta as $key => $value) {
//    print_r($key . " = " . $value . "</br>");
//}

//print_r($aResposta);
//print_r($retorno);
echo "<br>";
