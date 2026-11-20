<?php

use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapFake;
use NFePHP\CTe\Tools;
use PHPUnit\Framework\TestCase;

class ToolsEventosEntregaTest extends TestCase
{
    const CHAVE = '43241106157250000116570010000000751000000001';
    const HASH = 'AAAAAAAAAAAAAAAAAAAAAAAAAAA=';
    const DH_FATO = '2026-11-20T10:00:00-03:00';
    const DH_HASH = '2026-11-20T09:30:00-03:00';

    /** @var Tools */
    private $tools;

    protected function setUp()
    {
        $config = json_encode([
            'atualizacao' => '2026-11-20 00:00:00',
            'tpAmb' => 2,
            'razaosocial' => 'EMITENTE TESTE',
            'siglaUF' => 'RS',
            'cnpj' => '06157250000116',
            'schemes' => 'PL_CTe_400',
            'versao' => '4.00',
        ]);
        $this->tools = new Tools($config, Certificate::readPfx($this->pfxAutoassinado(), 'teste'));
        $this->tools->loadSoapClass(new SoapFake());
    }

    public function test_sefazCE_usaDataPropriaParaOHashQuandoInformada()
    {
        $this->sefazCE('ACME', self::DH_HASH);

        $this->assertSame(self::DH_FATO, $this->valor('dhEntrega'));
        $this->assertSame(self::DH_HASH, $this->valor('dhHashEntrega'));
    }

    public function test_sefazCE_mantemDataDoFatoNoHashQuandoNaoInformada()
    {
        $this->sefazCE('ACME');

        $this->assertSame(self::DH_FATO, $this->valor('dhHashEntrega'));
    }

    public function test_sefazCE_escapaTextoLivre()
    {
        $this->sefazCE('ACME & Cia');

        $this->assertSame('ACME & Cia', $this->valor('xNome'));
    }

    public function test_sefazIE_omiteGruposOpcionaisSemValor()
    {
        $this->tools->sefazIE(self::CHAVE, '123456789012345', '', '1', '', '', '', '', 1, self::DH_FATO);

        $this->assertNull($this->valor('nTentativa'));
        $this->assertNull($this->valor('hashTentativaEntrega'));
        $this->assertNull($this->valor('dhHashTentativaEntrega'));
    }

    public function test_sefazIE_usaDataPropriaParaOHashQuandoInformada()
    {
        $this->tools->sefazIE(
            self::CHAVE,
            '123456789012345',
            '001',
            '4',
            'Cliente & portaria fechada',
            self::HASH,
            '',
            '',
            1,
            self::DH_FATO,
            [],
            self::DH_HASH
        );

        $this->assertSame('001', $this->valor('nTentativa'));
        $this->assertSame('Cliente & portaria fechada', $this->valor('xJustMotivo'));
        $this->assertSame(self::DH_FATO, $this->valor('dhTentativaEntrega'));
        $this->assertSame(self::DH_HASH, $this->valor('dhHashTentativaEntrega'));
    }

    private function sefazCE($xNome, $dhHashEntrega = null)
    {
        $this->tools->sefazCE(
            self::CHAVE,
            '123456789012345',
            $xNome,
            '12345678901',
            self::HASH,
            '',
            '',
            1,
            self::DH_FATO,
            [],
            $dhHashEntrega
        );
    }

    private function pfxAutoassinado()
    {
        $chave = openssl_pkey_new(['private_key_bits' => 2048]);
        $csr = openssl_csr_new(['commonName' => 'EMITENTE TESTE:06157250000116'], $chave);
        $cert = openssl_csr_sign($csr, null, $chave, 1);
        openssl_pkcs12_export($cert, $pfx, $chave, 'teste');

        return $pfx;
    }

    private function valor($tag)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($this->tools->lastRequest);
        $node = $dom->getElementsByTagName($tag)->item(0);

        return $node ? $node->nodeValue : null;
    }
}
