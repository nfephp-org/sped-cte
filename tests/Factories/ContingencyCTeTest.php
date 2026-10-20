<?php

namespace NFePHP\CTe\Tests\Factories;

use NFePHP\CTe\Factories\Contingency;
use NFePHP\CTe\Factories\ContingencyCTe;
use NFePHP\CTe\Tests\Support\Fixtures;
use PHPUnit\Framework\TestCase;

/**
 * Contingência off-line (tpEmis 2, NT CT-e 2026.004 §6): Contingency aceita o tipo OFFLINE
 * e ContingencyCTe insere dhCont/xJust (regras G009/H005) na ordem do XSD.
 */
class ContingencyCTeTest extends TestCase
{
    const MOTIVO = 'SEFAZ AUTORIZADORA FORA DO AR NO MOMENTO DA EMISSAO';

    public function testContingencyAtivaOffline()
    {
        $contingency = new Contingency();
        $contingency->activate('SP', self::MOTIVO, 'OFF-LINE');
        $this->assertSame('OFFLINE', $contingency->type);
        $this->assertSame(2, $contingency->tpEmis);
        $this->assertSame(self::MOTIVO, $contingency->motive);
    }

    public function testAdjustOfflineInsereDhContEXJustERefazAChave()
    {
        $contingency = new Contingency();
        $contingency->activate('SP', self::MOTIVO, 'OFFLINE');
        $xml = ContingencyCTe::adjust(Fixtures::makeCTe()->getXML(), $contingency);

        $dom = new \DOMDocument();
        $dom->loadXML($xml);
        $ide = $dom->getElementsByTagName('ide')->item(0);
        $this->assertSame('2', $ide->getElementsByTagName('tpEmis')->item(0)->nodeValue);
        $this->assertSame(self::MOTIVO, $ide->getElementsByTagName('xJust')->item(0)->nodeValue);
        $this->assertNotEmpty($ide->getElementsByTagName('dhCont')->item(0)->nodeValue);
        $chave = substr($dom->getElementsByTagName('infCte')->item(0)->getAttribute('Id'), 3);
        $this->assertSame('2', substr($chave, 34, 1), 'tpEmis na chave de acesso');
        $this->assertSame(substr($chave, -1), $ide->getElementsByTagName('cDV')->item(0)->nodeValue);

        $erros = Fixtures::validate(Fixtures::sign($xml), Fixtures::xsd('PL_CTe_400_RTC', 'cte_v4.00.xsd'));
        $this->assertSame([], $erros);
    }

    public function testAdjustOfflineRespeitaAOrdemAntesDoPagAntecipado()
    {
        $contingency = new Contingency();
        $contingency->activate('SP', self::MOTIVO, 'OFFLINE');
        $original = Fixtures::makeCTe('PL_CTe_400_RTC', [
            'ide' => ['tpPagAnt' => '3'],
            'pagAnt' => [Fixtures::std(['chDFePagAnt' => '35261011222333000181570010000000011123456780'])],
        ])->getXML();
        $xml = ContingencyCTe::adjust($original, $contingency);
        $this->assertRegExp('/<toma3>.*<\/toma3><dhCont>[^<]+<\/dhCont><xJust>[^<]+<\/xJust><tpPagAnt>3/', $xml);
        $erros = Fixtures::validate(Fixtures::sign($xml), Fixtures::xsd('PL_CTe_400_RTC', 'cte_v4.00.xsd'));
        $this->assertSame([], $erros);
    }

    public function testAdjustSvcNaoInsereDhCont()
    {
        $contingency = new Contingency();
        $contingency->activate('SP', self::MOTIVO, 'SVRS');
        $xml = ContingencyCTe::adjust(Fixtures::makeCTe()->getXML(), $contingency);
        $this->assertContains('<tpEmis>7</tpEmis>', $xml);
        $this->assertNotContains('<dhCont>', $xml);
    }
}
