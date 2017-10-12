<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once '../bootstrap.php';

use NFePHP\CTe\Make;
use NFePHP\CTe\Tools;
use NFePHP\CTe\Complements;
use NFePHP\Common\Certificate;
use NFePHP\CTe\Common\Standardize;

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

try {
//intancia a classe tools
  $tools = new Tools($configJson, Certificate::readPfx($content, '02040608'));

  $chave = '43171099999999999999570010000001251373710320';
  $response = $tools->sefazConsultaChave($chave);
  $stdCl = new Standardize($response);
  //nesse caso o $arr irá conter uma representação em array do XML retornado
  $arr = $stdCl->toArray();
    
  echo '<pre>';
  print_r($arr);
  exit();
} catch (\Exception $e) {
  echo $e->getMessage();
  //TRATAR
}
