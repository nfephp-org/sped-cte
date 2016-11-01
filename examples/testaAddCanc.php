<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once '../bootstrap.php';

use NFePHP\CTe\Tools;

$cteTools = new Tools('../config/config.json');
$aResposta = array();

$chave = '43161011004958000177570010000000281000000283';

$pathNFefile = "../xml/cte/homologacao/enviadas/aprovadas/201610/{$chave}-protCTe.xml";
$pathProtfile = "../xml/cte/homologacao/temporarias/201610/{$chave}-CancCTe-retEnvEvento.xml";
$saveFile = true;
$retorno = $cteTools->addCancelamento($pathNFefile, $pathProtfile, $saveFile);
echo '<br><br><PRE>';
echo htmlspecialchars($retorno);
echo '</PRE><BR>';
echo "<br>";
