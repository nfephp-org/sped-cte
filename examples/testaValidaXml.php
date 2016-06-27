<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
include_once '../bootstrap.php';

use NFePHP\CTe\Tools;

$cteTools = new Tools('../config/config.json');


$chave = '43160672044530000142570010000000101000000101';
$tpAmb = '2';

$filename = "/Applications/XAMPP/xamppfiles/htdocs/projetos/sped-cte/xml/cte/{$chave}-cte.xml";

if (! $cteTools->validarXml($filename) || sizeof($cteTools->errors)) {
    echo "<h3>Algum erro ocorreu.... </h3>";
    foreach ($cteTools->errors as $erro) {
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
