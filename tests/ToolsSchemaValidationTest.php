<?php

namespace NFePHP\CTe\Tests;

use NFePHP\Common\Certificate;
use NFePHP\Common\Exception\ValidatorException;
use NFePHP\CTe\Tools;
use PHPUnit\Framework\TestCase;

class ToolsSchemaValidationTest extends TestCase
{
    protected function getConfigJson()
    {
        $config = [
            "atualizacao" => "2019-08-01 10:00:00",
            "tpAmb" => 2,
            "razaosocial" => "SUA RAZAO SOCIAL LTDA",
            "cnpj" => "99999999999999",
            "siglaUF" => "SP",
            "schemes" => "PL_CTe_400",
            "versao" => "4.00",
            "proxyConf" => [
                "proxyIp" => "",
                "proxyPort" => "",
                "proxyUser" => "",
                "proxyPass" => ""
            ]
        ];
        return json_encode($config);
    }

    /**
     * Gera um certificado autoassinado temporario e descartavel apenas
     * para instanciar Tools, sem transmissao alguma
     * @return Tools
     */
    protected function getTools()
    {
        $dn = [
            "countryName" => "BR",
            "commonName" => "sped-cte-test"
        ];
        $privkey = openssl_pkey_new();
        $csr = openssl_csr_new($dn, $privkey);
        $cert = openssl_csr_sign($csr, null, $privkey, 365);
        openssl_pkcs12_export($cert, $pfxout, $privkey, 'nfephp');
        return new Tools(
            $this->getConfigJson(),
            Certificate::readPfx($pfxout, 'nfephp')
        );
    }

    /**
     * O path dos schemas deve apontar para o diretorio schemes/ do pacote,
     * onde os XSD realmente sao distribuidos
     */
    public function testCaminhoDosSchemasExiste()
    {
        $tools = $this->getTools();
        $this->assertContains('/schemes/PL_CTe_400/', $tools->pathschemes);
        $this->assertTrue(
            is_dir($tools->pathschemes),
            'O diretorio de schemas [' . $tools->pathschemes . '] nao existe'
        );
    }

    /**
     * Um evento que viola o XSD deve ser rejeitado antes da transmissao,
     * o isValid() nao pode retornar true silenciosamente para XML invalido
     */
    public function testEventoInvalidoEhRejeitadoLocalmente()
    {
        $tools = $this->getTools();
        $method = new \ReflectionMethod($tools, 'isValid');
        $method->setAccessible(true);
        $xml = '<eventoCTe xmlns="http://www.portalfiscal.inf.br/cte" versao="4.00">'
            . '<infEvento/></eventoCTe>';
        $this->expectException(ValidatorException::class);
        $method->invoke($tools, '4.00', $xml, 'eventoCTe');
    }
}
