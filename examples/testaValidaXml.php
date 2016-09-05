<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once '../bootstrap.php';

use NFePHP\CTe\Tools;

$cteTools = new Tools('../config/config.json');

$chave = '43160911004958000177570010000000101000000106';
$tpAmb = '2';
$filename = "../xml/{$chave}-cte.xml";

if (! $cteTools->validarXml($filename) || sizeof($cteTools->erros)) {
    echo "<h3>Algum erro ocorreu.... </h3>";
    foreach ($cteTools->erros as $erro) {
        if (is_array($erro)) { 
            foreach ($erro as $err) {
                echo "$err <br>";
            }
        } else {
            echo "$erro <br>";
        }
    }
    exit;
}
echo "CT-e Válido!";
