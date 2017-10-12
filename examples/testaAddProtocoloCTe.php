<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once '../bootstrap.php';

use NFePHP\CTe\Make;
use NFePHP\CTe\Tools;
use NFePHP\CTe\Complements;
use NFePHP\Common\Certificate;
use NFePHP\Common\Standardize;

//tanto o config.json como o certificado.pfx podem estar
//armazenados em uma base de dados, então não é necessário 
///trabalhar com arquivos, este script abaixo serve apenas como 
//exemplo durante a fase de desenvolvimento e testes.
$arr = [
    "atualizacao" => "2016-11-03 18:01:21",
    "tpAmb" => 2,
    "razaosocial" => "SUA RAZAO SOCIAL LTDA",
    "cnpj" => "99999999999999",
    "siglaUF" => "RS",
    "schemes" => "PL_CTe_300",
    "versao" => '3.00',
    "proxyConf" => [
        "proxyIp" => "",
        "proxyPort" => "",
        "proxyUser" => "",
        "proxyPass" => ""
    ]
];
//monta o config.json
$configJson = json_encode($arr);

//carrega o conteudo do certificado.
$content = file_get_contents('fixtures/certificado.pfx');

//intancia a classe tools
$tools = new Tools($configJson, Certificate::readPfx($content, '02040608'));

$chave = '43171099999999999999570010000001261361744574';
$response = $tools->sefazConsultaChave($chave);
$ctefile = file_get_contents("xml/{$chave}-cte.xml");
$auth = Complements::toAuthorize($ctefile, $response);
//Salva CT-e com protocolo
$filename = "xml/aprovadas/{$chave}-protcte.xml";
file_put_contents($filename, $auth);

//
header('Content-type: text/xml; charset=UTF-8');
print_r($auth);
exit();
