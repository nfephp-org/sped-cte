<?php

namespace NFePHP\CTe\Tests;

use NFePHP\CTe\Tests\Support\Fixtures;
use PHPUnit\Framework\TestCase;

/**
 * NT CT-e 2026.004 v1.00 no MakeCTeSimp: det/vPrestLiq, total/vTPrestLiq,
 * gICMSPrevistoPagtoAntecip e tpEmis 2. XML assinado e validado contra cteSimp_v4.00.xsd.
 */
class MakeCTeSimpNT2026004Test extends TestCase
{
    /**
     * @param string $xml
     * @param string $schema
     */
    private function assertValidoNoXsd($xml, $schema = 'PL_CTe_400_RTC')
    {
        $erros = Fixtures::validate(Fixtures::sign($xml), Fixtures::xsd($schema, 'cteSimp_v4.00.xsd'));
        $this->assertSame([], $erros, "XML invalido no $schema");
    }

    public function testCTeSimpMinimoValidaNoXsd()
    {
        $this->assertValidoNoXsd(Fixtures::makeCTeSimp()->getXML());
    }

    public function testVPrestLiqNoDetEVTPrestLiqNoTotal()
    {
        $xml = Fixtures::makeCTeSimp('PL_CTe_400_RTC', [
            'det' => ['vPrestLiq' => 90],
            'total' => ['vTPrestLiq' => 90],
        ])->getXML();
        $this->assertContains('<vPrest>100.00</vPrest><vPrestLiq>90.00</vPrestLiq><vRec>', $xml);
        $this->assertContains('<vTPrest>100.00</vTPrest><vTPrestLiq>90.00</vTPrestLiq><vTRec>', $xml);
        $this->assertValidoNoXsd($xml);
    }

    public function testValoresLiquidosIgnoradosNoPlSemRtc()
    {
        $xml = Fixtures::makeCTeSimp('PL_CTe_400', [
            'det' => ['vPrestLiq' => 90],
            'total' => ['vTPrestLiq' => 90],
        ])->getXML();
        $this->assertNotContains('PrestLiq', $xml);
    }

    public function testICMSPrevistoNoCTeSimp()
    {
        $make = new \NFePHP\CTe\MakeCTeSimp('PL_CTe_400_RTC');
        $icms = $make->tagicms(Fixtures::std(['cst' => '00', 'vICMSPrevisto' => 3.4]));
        $this->assertSame('gICMSPrevistoPagtoAntecip', $icms->firstChild->nodeName);
        $this->assertSame('3.40', $icms->firstChild->firstChild->nodeValue);
    }

    public function testContingenciaOfflineValida()
    {
        $xml = Fixtures::makeCTeSimp('PL_CTe_400_RTC', [
            'ide' => [
                'tpEmis' => '2',
                'dhCont' => '2026-10-20T09:30:00-03:00',
                'xJust' => 'SEFAZ AUTORIZADORA FORA DO AR NO MOMENTO DA EMISSAO',
            ],
        ])->getXML();
        $this->assertValidoNoXsd($xml);
    }
}
