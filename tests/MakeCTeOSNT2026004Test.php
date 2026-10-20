<?php

namespace NFePHP\CTe\Tests;

use NFePHP\CTe\Tests\Support\Fixtures;
use PHPUnit\Framework\TestCase;

/**
 * NT CT-e 2026.004 v1.00 no MakeCTeOS (mod 67): vTPrestLiq, gICMSPrevistoPagtoAntecip
 * (TImpOS), chDFePagAnt e tpEmis 2. XML assinado e validado contra cteOS_v4.00.xsd.
 */
class MakeCTeOSNT2026004Test extends TestCase
{
    const CHAVE_ANT = '35261011222333000181670010000000011123456781';

    /**
     * @param string $xml
     * @param string $schema
     */
    private function assertValidoNoXsd($xml, $schema = 'PL_CTe_400_RTC')
    {
        $erros = Fixtures::validate(Fixtures::sign($xml), Fixtures::xsd($schema, 'cteOS_v4.00.xsd'));
        $this->assertSame([], $erros, "XML invalido no $schema");
    }

    public function testCTeOSMinimoValidaNoXsd()
    {
        $this->assertValidoNoXsd(Fixtures::makeCTeOS()->getXML());
    }

    public function testVTPrestLiqLogoAposVTPrest()
    {
        $xml = Fixtures::makeCTeOS('PL_CTe_400_RTC', [
            'vPrest' => ['vTPrestLiq' => 88.1],
        ])->getXML();
        $this->assertContains('<vTPrest>100.00</vTPrest><vTPrestLiq>88.10</vTPrestLiq><vRec>', $xml);
        $this->assertValidoNoXsd($xml);
    }

    public function testVTPrestLiqIgnoradoNoPlSemRtc()
    {
        $xml = Fixtures::makeCTeOS('PL_CTe_400', [
            'vPrest' => ['vTPrestLiq' => 88.1],
        ])->getXML();
        $this->assertNotContains('vTPrestLiq', $xml);
        $this->assertValidoNoXsd($xml, 'PL_CTe_400');
    }

    public function testICMSPrevistoSubstituiOGrupoDoCst()
    {
        $xml = Fixtures::makeCTeOS('PL_CTe_400_RTC', [
            'ide' => ['tpPagAnt' => '1'],
            'icms' => ['vICMSPrevisto' => 0],
        ])->getXML();
        $this->assertContains('<ICMS><gICMSPrevistoPagtoAntecip><vICMSPrevisto>0.00</vICMSPrevisto>', $xml);
        $this->assertNotContains('<ICMS00>', $xml);
        $this->assertValidoNoXsd($xml);
    }

    public function testPagAntecipadoComChaveAntigaEmiteChDFePagAnt()
    {
        $xml = Fixtures::makeCTeOS('PL_CTe_400_RTC', [
            'ide' => ['tpPagAnt' => '3'],
            'pagAnt' => [Fixtures::std(['chCTePagAnt' => self::CHAVE_ANT])],
        ])->getXML();
        $this->assertContains('<chDFePagAnt>' . self::CHAVE_ANT . '</chDFePagAnt>', $xml);
        $this->assertNotContains('chCTePagAnt', $xml);
        $this->assertValidoNoXsd($xml);
    }

    public function testContingenciaOfflineValida()
    {
        $xml = Fixtures::makeCTeOS('PL_CTe_400_RTC', [
            'ide' => [
                'tpEmis' => '2',
                'dhCont' => '2026-10-20T09:30:00-03:00',
                'xJust' => 'SEFAZ AUTORIZADORA FORA DO AR NO MOMENTO DA EMISSAO',
            ],
        ])->getXML();
        $this->assertValidoNoXsd($xml);
        $this->assertValidoNoXsd(
            Fixtures::makeCTeOS('PL_CTe_400', [
                'ide' => [
                    'tpEmis' => '2',
                    'dhCont' => '2026-10-20T09:30:00-03:00',
                    'xJust' => 'SEFAZ AUTORIZADORA FORA DO AR NO MOMENTO DA EMISSAO',
                ],
            ])->getXML(),
            'PL_CTe_400'
        );
    }
}
