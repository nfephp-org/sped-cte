<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

use NFePHP\CTe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapCurl;

//tanto o config.json como o certificado.pfx podem estar
//armazenados em uma base de dados, então não é necessário 
///trabalhar com arquivos, este script abaixo serve apenas como 
//exemplo durante a fase de desenvolvimento e testes.
//$arr = [
//    "atualizacao" => "2016-11-03 18:01:21",
//    "tpAmb" => 2,
//    "razaosocial" => "SUA RAZAO SOCIAL LTDA",
//    "cnpj" => "99999999999999",
//    "siglaUF" => "SP",
//    "schemes" => "PL008i2",
//    "versao" => '3.10',
//    "tokenIBPT" => "AAAAAAA",
//    "CSC" => "GPB0JBWLUR6HWFTVEAS6RJ69GPCROFPBBB8G",
//    "CSCid" => "000001",
//    "proxyConf" => [
//        "proxyIp" => "",
//        "proxyPort" => "",
//        "proxyUser" => "",
//        "proxyPass" => ""
//    ]
//];

$arr = [
    "atualizacao" => "2016-11-03 18:01:21",
    "tpAmb" => 2,
    "razaosocial" => "WALISNERIA DE JESUS SANTOS BARBOSA SENA - ME",
    "cnpj" => "06871403000192",
    "siglaUF" => "DF",
    "schemes" => "PL008i2",
    "versao" => '3.00',
    "tokenIBPT" => "AAAAAAA",
    "CSC" => "GPB0JBWLUR6HWFTVEAS6RJ69GPCROFPBBB8G",
    "CSCid" => "000001",
    "proxyConf" => [
        "proxyIp" => "",
        "proxyPort" => "",
        "proxyUser" => "",
        "proxyPass" => ""
    ]
];
$configJson = json_encode($arr);
//$pfxcontent = file_get_contents('fixtures/expired_certificate.pfx');
$certificado = "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";

//$tools = new Tools($configJson, Certificate::readPfx($pfxcontent, 'associacao'));
$tools = new Tools($configJson, Certificate::readPfx(base64_decode($certificado), 'wsena2018'));

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

echo "<pre>";
print_r($response);
echo "</pre>";
