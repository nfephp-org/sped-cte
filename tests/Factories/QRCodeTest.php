<?php

namespace NFePHP\CTe\Tests\Factories;

use NFePHP\CTe\Factories\QRCode;
use NFePHP\CTe\Tests\Support\Fixtures;
use PHPUnit\Framework\TestCase;

/**
 * QR Code do CT-e: com tpEmis 2 (contingência off-line, NT CT-e 2026.004 regras
 * G215/G217, cStat 853/855) a URL leva o parâmetro sign = RSA-SHA1 da chave em Base64.
 */
class QRCodeTest extends TestCase
{
    const URL = 'https://homologacao.nfe.fazenda.sp.gov.br/CTeConsulta/qrCode';

    /**
     * @param string $tpEmis
     * @return array [url do QR Code, chave]
     */
    private function qrCode($tpEmis)
    {
        $ide = ['tpEmis' => $tpEmis];
        if ($tpEmis != '1') {
            $ide['dhCont'] = '2026-10-20T09:30:00-03:00';
            $ide['xJust'] = 'SEFAZ AUTORIZADORA FORA DO AR NO MOMENTO DA EMISSAO';
        }
        $make = Fixtures::makeCTe('PL_CTe_400', ['ide' => $ide]);
        $dom = new \DOMDocument();
        $dom->loadXML(Fixtures::sign($make->getXML()));
        $xml = QRCode::putQRTag($dom, Fixtures::certificate(), self::URL);
        $out = new \DOMDocument();
        $out->loadXML($xml);
        return [$out->getElementsByTagName('qrCodCTe')->item(0)->nodeValue, $make->chCTe];
    }

    public function testTpEmisNormalNaoAssina()
    {
        list($url, $chave) = $this->qrCode('1');
        $this->assertSame(self::URL . "?chCTe=$chave&tpAmb=2", $url);
    }

    public function testTpEmisOfflineAssinaAChave()
    {
        list($url, $chave) = $this->qrCode('2');
        $this->assertSame(0, strpos($url, self::URL . "?chCTe=$chave&tpAmb=2&sign="));
        $sign = base64_decode(substr($url, strpos($url, '&sign=') + 6));
        $this->assertTrue(Fixtures::certificate()->verify($chave, $sign), 'sign confere com a chave');
    }

    public function testEpecEFsdaContinuamAssinando()
    {
        foreach (['4', '5'] as $tpEmis) {
            list($url) = $this->qrCode($tpEmis);
            $this->assertContains('&sign=', $url, "tpEmis $tpEmis");
        }
    }
}
