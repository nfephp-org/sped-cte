<?php

namespace NFePHP\CTe\Tests;

use NFePHP\CTe\Factories\QRCode;
use NFePHP\CTe\Tests\Support\Fixtures;
use PHPUnit\Framework\TestCase;

/**
 * NT CT-e 2026.004 v1.00 no MakeCTe (mod 57): vTPrestLiq, gICMSPrevistoPagtoAntecip,
 * chDFePagAnt e tpEmis 2. Todo XML gerado é assinado e validado contra o XSD do PL.
 */
class MakeCTeNT2026004Test extends TestCase
{
    const CHAVE_ANT = '35261011222333000181570010000000011123456780';

    /**
     * @param string $xml
     * @param string $schema
     */
    private function assertValidoNoXsd($xml, $schema = 'PL_CTe_400_RTC')
    {
        $erros = Fixtures::validate(Fixtures::sign($xml), Fixtures::xsd($schema, 'cte_v4.00.xsd'));
        $this->assertSame([], $erros, "XML invalido no $schema");
    }

    /**
     * @param string $xml
     * @return \DOMDocument
     */
    private function dom($xml)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        return $dom;
    }

    public function testCTeMinimoSemCamposNovosValidaNoXsd()
    {
        $xml = Fixtures::makeCTe()->getXML();
        $this->assertValidoNoXsd($xml);
        $this->assertNotContains('vTPrestLiq', $xml);
        $this->assertNotContains('gICMSPrevistoPagtoAntecip', $xml);
    }

    public function testVTPrestLiqLogoAposVTPrestEValida()
    {
        $xml = Fixtures::makeCTe('PL_CTe_400_RTC', [
            'vPrest' => ['vTPrest' => 100, 'vTPrestLiq' => 79.25, 'vRec' => 100],
        ])->getXML();
        $vPrest = $this->dom($xml)->getElementsByTagName('vPrest')->item(0);
        $filhos = [];
        foreach ($vPrest->childNodes as $no) {
            $filhos[] = $no->nodeName;
        }
        $this->assertSame(['vTPrest', 'vTPrestLiq', 'vRec'], $filhos);
        $this->assertSame('79.25', $vPrest->getElementsByTagName('vTPrestLiq')->item(0)->nodeValue);
        $this->assertValidoNoXsd($xml);
    }

    public function testVTPrestLiqZeroEInformado()
    {
        $xml = Fixtures::makeCTe('PL_CTe_400_RTC', [
            'vPrest' => ['vTPrestLiq' => 0],
        ])->getXML();
        $this->assertContains('<vTPrestLiq>0.00</vTPrestLiq>', $xml);
        $this->assertValidoNoXsd($xml);
    }

    public function testVTPrestLiqIgnoradoNoPlSemRtc()
    {
        $xml = Fixtures::makeCTe('PL_CTe_400', [
            'vPrest' => ['vTPrestLiq' => 79.25],
        ])->getXML();
        $this->assertNotContains('vTPrestLiq', $xml);
        $this->assertValidoNoXsd($xml, 'PL_CTe_400');
    }

    public function testICMSPrevistoSubstituiOGrupoDoCstEValida()
    {
        $xml = Fixtures::makeCTe('PL_CTe_400_RTC', [
            'ide' => ['tpPagAnt' => '1'],
            'icms' => ['vICMSPrevisto' => 12.5],
        ])->getXML();
        $icms = $this->dom($xml)->getElementsByTagName('ICMS')->item(0);
        $this->assertSame(1, $icms->childNodes->length);
        $this->assertSame('gICMSPrevistoPagtoAntecip', $icms->firstChild->nodeName);
        $this->assertSame('12.50', $icms->firstChild->getElementsByTagName('vICMSPrevisto')->item(0)->nodeValue);
        $this->assertNotContains('<ICMS00>', $xml);
        $this->assertValidoNoXsd($xml);
    }

    public function testICMSPrevistoZeroEInformado()
    {
        $xml = Fixtures::makeCTe('PL_CTe_400_RTC', [
            'ide' => ['tpPagAnt' => '1'],
            'icms' => ['vICMSPrevisto' => 0],
        ])->getXML();
        $this->assertContains('<gICMSPrevistoPagtoAntecip><vICMSPrevisto>0.00</vICMSPrevisto>', $xml);
        $this->assertValidoNoXsd($xml);
    }

    public function testICMSPrevistoIgnoradoNoPlSemRtc()
    {
        $xml = Fixtures::makeCTe('PL_CTe_400', [
            'icms' => ['vICMSPrevisto' => 12.5],
        ])->getXML();
        $this->assertNotContains('gICMSPrevistoPagtoAntecip', $xml);
        $this->assertContains('<ICMS00>', $xml);
        $this->assertValidoNoXsd($xml, 'PL_CTe_400');
    }

    public function testPagAntecipadoComChaveAntigaEmiteChDFePagAntNoRtc()
    {
        $std = Fixtures::std(['chCTePagAnt' => self::CHAVE_ANT]);
        $xml = Fixtures::makeCTe('PL_CTe_400_RTC', [
            'ide' => ['tpPagAnt' => '3'],
            'pagAnt' => [$std],
        ])->getXML();
        $this->assertContains('<gPagAntecipado><chDFePagAnt>' . self::CHAVE_ANT . '</chDFePagAnt>', $xml);
        $this->assertNotContains('chCTePagAnt', $xml);
        $this->assertValidoNoXsd($xml);
    }

    public function testPagAntecipadoComChaveNovaEVariasChaves()
    {
        $outra = '35261011222333000181570010000000021123456785';
        $xml = Fixtures::makeCTe('PL_CTe_400_RTC', [
            'ide' => ['tpPagAnt' => '3'],
            'pagAnt' => [
                Fixtures::std(['chDFePagAnt' => self::CHAVE_ANT]),
                Fixtures::std(['chCTePagAnt' => $outra]),
            ],
        ])->getXML();
        $grupo = $this->dom($xml)->getElementsByTagName('gPagAntecipado')->item(0);
        $chaves = [];
        foreach ($grupo->getElementsByTagName('chDFePagAnt') as $no) {
            $chaves[] = $no->nodeValue;
        }
        $this->assertSame([self::CHAVE_ANT, $outra], $chaves);
        $this->assertValidoNoXsd($xml);
    }

    public function testPagAntecipadoNoPlSemRtcMantemChCTePagAnt()
    {
        $make = new \NFePHP\CTe\MakeCTe('PL_CTe_400');
        $grupo = $make->taggPagAntecipado(Fixtures::std(['chDFePagAnt' => self::CHAVE_ANT]));
        $this->assertSame('chCTePagAnt', $grupo->firstChild->nodeName);
        $this->assertSame(self::CHAVE_ANT, $grupo->firstChild->nodeValue);
    }

    public function testContingenciaOfflineComDhContXJustEQrCodeAssinadoValida()
    {
        $xml = Fixtures::makeCTe('PL_CTe_400_RTC', [
            'ide' => [
                'tpEmis' => '2',
                'dhCont' => '2026-10-20T09:30:00-03:00',
                'xJust' => 'SEFAZ AUTORIZADORA FORA DO AR NO MOMENTO DA EMISSAO',
            ],
        ])->getXML();
        $chave = substr($this->dom($xml)->getElementsByTagName('infCte')->item(0)->getAttribute('Id'), 3);
        $this->assertSame('2', substr($chave, 34, 1), 'tpEmis na chave de acesso');
        $dom = $this->dom(Fixtures::sign($xml));
        $assinado = QRCode::putQRTag(
            $dom,
            Fixtures::certificate(),
            'https://homologacao.nfe.fazenda.sp.gov.br/CTeConsulta/qrCode'
        );
        $this->assertContains('&sign=', $assinado);
        $erros = Fixtures::validate($assinado, Fixtures::xsd('PL_CTe_400_RTC', 'cte_v4.00.xsd'));
        $this->assertSame([], $erros);
    }

    public function testTpEmis2TambemValidaNoPlSemRtc()
    {
        $xml = Fixtures::makeCTe('PL_CTe_400', [
            'ide' => [
                'tpEmis' => '2',
                'dhCont' => '2026-10-20T09:30:00-03:00',
                'xJust' => 'SEFAZ AUTORIZADORA FORA DO AR NO MOMENTO DA EMISSAO',
            ],
        ])->getXML();
        $this->assertValidoNoXsd($xml, 'PL_CTe_400');
    }
}
