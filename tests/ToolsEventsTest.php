<?php

namespace NFePHP\CTe\Tests;

use NFePHP\Common\Certificate;
use NFePHP\Common\Validator;
use NFePHP\CTe\Tests\Common\ToolsFake;
use PHPUnit\Framework\TestCase;

class ToolsEventsTest extends TestCase
{
    /**
     * @var string
     */
    public static $fixturesPath = '';

    /**
     * @var ToolsFake
     */
    public $tools;

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        self::$fixturesPath = dirname(__FILE__) . '/fixtures/';
    }

    protected function getTools()
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
        $contentpfx = file_get_contents(self::$fixturesPath . 'certs/test_certificate.pfx');
        $tools = new ToolsFake(
            json_encode($config),
            Certificate::readPfx($contentpfx, 'nfephp')
        );
        $tools->model('57');
        return $tools;
    }

    /**
     * Extrai o fragmento do evento (evCECTe ou evIECTe) da mensagem montada
     * e devolve com o namespace declarado, pronto para validação no XSD
     * @param string $request
     * @param string $tag
     * @return string
     */
    protected function eventFragment($request, $tag)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($request);
        $node = $dom->getElementsByTagName($tag)->item(0);
        $fragment = $dom->saveXML($node);
        $xmlns = 'http://www.portalfiscal.inf.br/cte';
        return str_replace(
            "<$tag>",
            "<$tag xmlns=\"$xmlns\">",
            $fragment
        );
    }

    /**
     * O dhEntrega registra quando a entrega foi concluída e o dhHashEntrega
     * quando o hash foi gerado, são instantes distintos (evCECTe_v4.00.xsd)
     */
    public function testSefazCEDataHashIndependenteDaDataDaEntrega()
    {
        $this->tools = $this->getTools();
        $this->tools->sefazCE(
            '35171099999999999999570010000001261361744574',
            '135190011347000',
            'FULANO DE TAL',
            '14666555',
            'noTbLtfaoS02PYxVm8ufox7OKww=',
            '-23.432059',
            '-46.505813',
            1,
            '2017-10-20T10:00:52-03:00',
            ['35220605730928000145550010000048661583302923']
        );
        $request = $this->tools->getRequest();
        $this->assertContains('<dhEntrega>2017-10-20T10:00:52-03:00</dhEntrega>', $request);
        $this->assertNotContains(
            '<dhHashEntrega>2017-10-20T10:00:52-03:00</dhHashEntrega>',
            $request,
            'O dhHashEntrega não pode repetir o instante da entrega, '
            . 'ele registra a geração do hash (evCECTe_v4.00.xsd)'
        );
    }

    /**
     * O evento com todos os dados deve passar na validação do XSD
     */
    public function testSefazCEEventoValidoNoSchema()
    {
        $this->tools = $this->getTools();
        $this->tools->sefazCE(
            '35171099999999999999570010000001261361744574',
            '135190011347000',
            'FULANO DE TAL',
            '14666555',
            'noTbLtfaoS02PYxVm8ufox7OKww=',
            '-23.432059',
            '-46.505813',
            1,
            '2017-10-20T10:00:52-03:00',
            ['35220605730928000145550010000048661583302923']
        );
        $fragment = $this->eventFragment(
            $this->tools->getRequest(),
            'evCECTe'
        );
        $this->assertTrue(
            $this->isValidAgainstXsd($fragment, 'evCECTe_v4.00.xsd')
        );
    }

    /**
     * Nome do recebedor com & deve ser escapado, o XML assinado não pode
     * ser rejeitado antes da transmissão
     */
    public function testSefazCENomeDoRecebedorComCaractereEspecial()
    {
        $this->tools = $this->getTools();
        try {
            $this->tools->sefazCE(
                '35171099999999999999570010000001261361744574',
                '135190011347000',
                'ACME & CIA LTDA',
                '14666555',
                'noTbLtfaoS02PYxVm8ufox7OKww=',
                null,
                null,
                1,
                '2017-10-20T10:00:52-03:00',
                []
            );
        } catch (\Exception $e) {
            $this->fail(
                'O XML do evento não deve ser rejeitado por caracteres '
                . 'especiais no nome do recebedor: ' . $e->getMessage()
            );
        }
        $request = $this->tools->getRequest();
        $this->assertContains('<xNome>ACME &amp; CIA LTDA</xNome>', $request);
    }

    /**
     * A NT permite insucesso sem imagem da tentativa: hash, data do hash e
     * número da tentativa são minOccurs="0" (evIECTe_v4.00.xsd) e não podem
     * ser emitidos vazios, o que viola base64Binary/pattern no SEFAZ
     */
    public function testSefazIESemDadosOpcionaisNaoEmiteElementosVazios()
    {
        $this->tools = $this->getTools();
        $this->tools->sefazIE(
            '35171099999999999999570010000001261361744574',
            '135190011347000',
            '',
            '1',
            null,
            '',
            null,
            null,
            1,
            '2017-10-20T10:00:52-03:00',
            []
        );
        $request = $this->tools->getRequest();
        $this->assertNotContains('<nTentativa', $request);
        $this->assertNotContains('<hashTentativaEntrega', $request);
        $this->assertNotContains('<dhHashTentativaEntrega', $request);
        $fragment = $this->eventFragment($request, 'evIECTe');
        $this->assertTrue(
            $this->isValidAgainstXsd($fragment, 'evIECTe_v4.00.xsd'),
            'O evento de insucesso sem imagem da tentativa deve validar '
            . 'contra o evIECTe_v4.00.xsd'
        );
    }

    /**
     * Evento de insucesso completo deve validar no XSD, com a data do hash
     * distinta da data da tentativa
     */
    public function testSefazIEEventoValidoNoSchema()
    {
        $this->tools = $this->getTools();
        $this->tools->sefazIE(
            '35171099999999999999570010000001261361744574',
            '135190011347000',
            '001',
            '4',
            'Destinatario ausente no local informado',
            'noTbLtfaoS02PYxVm8ufox7OKww=',
            '-23.432059',
            '-46.505813',
            1,
            '2017-10-20T10:00:52-03:00',
            ['35220605730928000145550010000048661583302923']
        );
        $request = $this->tools->getRequest();
        $this->assertContains('<dhTentativaEntrega>2017-10-20T10:00:52-03:00</dhTentativaEntrega>', $request);
        $this->assertNotContains(
            '<dhHashTentativaEntrega>2017-10-20T10:00:52-03:00</dhHashTentativaEntrega>',
            $request,
            'O dhHashTentativaEntrega não pode repetir o instante da tentativa, '
            . 'ele registra a geração do hash (evIECTe_v4.00.xsd)'
        );
        $fragment = $this->eventFragment($request, 'evIECTe');
        $this->assertTrue(
            $this->isValidAgainstXsd($fragment, 'evIECTe_v4.00.xsd')
        );
    }

    /**
     * A justificativa do motivo 4 também é texto livre e deve ser escapada
     */
    public function testSefazIEJustificativaComCaractereEspecial()
    {
        $this->tools = $this->getTools();
        try {
            $this->tools->sefazIE(
                '35171099999999999999570010000001261361744574',
                '135190011347000',
                '001',
                '4',
                'Recebedor era menor de idade & sem responsável presente',
                'noTbLtfaoS02PYxVm8ufox7OKww=',
                null,
                null,
                1,
                '2017-10-20T10:00:52-03:00',
                []
            );
        } catch (\Exception $e) {
            $this->fail(
                'O XML do evento não deve ser rejeitado por caracteres '
                . 'especiais na justificativa: ' . $e->getMessage()
            );
        }
        $request = $this->tools->getRequest();
        $this->assertContains(
            '&amp; sem responsável presente',
            $request
        );
    }

    /**
     * @param string $fragment
     * @param string $xsd
     * @return bool
     */
    protected function isValidAgainstXsd($fragment, $xsd)
    {
        try {
            return Validator::isValid(
                $fragment,
                dirname(dirname(__FILE__)) . '/schemes/PL_CTe_400/' . $xsd
            );
        } catch (\Exception $e) {
            $this->fail('Fragmento inválido contra o XSD: ' . $e->getMessage());
        }
    }
}
