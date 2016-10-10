<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once '../bootstrap.php';

use NFePHP\CTe\Tools;

$cteTools = new Tools('../config/config.json');

$aResposta = array();
$serie = '2';
$inicio = '2';
$fim = '2';
$tpAmb = '2';
$xJust = 'teste de Inutilizacao em ambiente de homologacao(teste)'; // MINIMO DE 15 DIGITOS

$cteTools->aConfig['cnpj']='81450900000132'; // CNPJ DA EMPRESA SERA USADO DENTRO DA CLASSE
$cteTools->aConfig['siglaUF']='PR';
$retorno = $cteTools->sefazInutiliza($serie, $inicio, $fim, $xJust, $tpAmb, $aResposta);
echo '<pre>';
echo htmlspecialchars($cteTools->soapDebug);
//print_r($aResposta);
print_r($retorno);
echo "</pre>";
