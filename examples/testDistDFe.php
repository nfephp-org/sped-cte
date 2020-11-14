<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

use NFePHP\Common\Certificate;
use NFePHP\CTe\Tools;

// tanto o config.json como o certificado.pfx podem estar
// armazenados em uma base de dados, então não é necessário
// trabalhar com arquivos, este script abaixo serve apenas como
// exemplo durante a fase de desenvolvimento e testes.
$arr = [
    "atualizacao" => "2016-11-03 18:01:21",
    "tpAmb" => 2,
    "razaosocial" => "SUA RAZAO SOCIAL LTDA",
    "cnpj" => "21357379000161",
    "siglaUF" => "PR"
];

$configJson = json_encode($arr);
$pfxcontent = file_get_contents('21357379000161.pfx');

$tools = new Tools($configJson, Certificate::readPfx($pfxcontent, '1234'));

//sempre que ativar a contingência pela primeira vez essa informação deverá ser 
//gravada na base de dados ou em um arquivo para uso posterior, até que a mesma seja 
//desativada pelo usuário, essa informação não é persistida automaticamente e depende 
//de ser gravada pelo ERP
$contingencia = $tools->contingency->deactivate();

//e se necessário carregada novamente quando a classe for instanciada
$tools->contingency->load($contingencia);

//executa a busca por documentos
$response = $tools->sefazDistDFe(
    0,
    0
);

header("Content-type: text/plain");
echo $response;

//echo "<pre>";
//var_dump($response);
//echo "</pre>";
