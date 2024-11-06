<?php

namespace NFePHP\CTe\Factories;

/**
 * Class QRCode create a string to make a QRCode string
 *
 * @category  NFePHP
 * @package   NFePHP\CTe\Factories\QRCode
 * @copyright NFePHP Copyright (c) 2008-2019
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Cleiton Perin <cperin20 at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-cte for the canonical source repository
 */

use DOMDocument;
use NFePHP\Common\Certificate;

class QRCode
{
    /**
     * putQRTag
     * @param DOMDocument $dom CTe
     * @param Certificate $certificate
     * @param string $url
     * @return string
     */
    public static function putQRTag(
        \DOMDocument $dom,
         $certificate,
         $url = ''
    )
    {
        $mod = $dom->getElementsByTagName('mod')->item(0)->nodeValue;
        # se for CTe-OS, pega a tag raiz correspondente
        if ($mod == 67) {
            $cte = $dom->getElementsByTagName('CTeOS')->item(0);
        } else {
            $tpCTe = (int)$dom->getElementsByTagName('tpCTe')->item(0)->nodeValue;
            if (($tpCTe == 5) || ($tpCTe == 6)) {
                // CTe simplificado
                $cte = $dom->getElementsByTagName('CTeSimp')->item(0);
            }else{
                $cte = $dom->getElementsByTagName('CTe')->item(0);
            }
        }
        $infCte = $dom->getElementsByTagName('infCte')->item(0);
        $ide = $dom->getElementsByTagName('ide')->item(0);
        $chCTe = preg_replace('/[^0-9]/', '', $infCte->getAttribute("Id"));
        $tpAmb = $ide->getElementsByTagName('tpAmb')->item(0)->nodeValue;
        $tpEmis = $ide->getElementsByTagName('tpEmis')->item(0)->nodeValue;
        $sign = '';
        if (in_array($tpEmis, [4, 5])) {
            $sign = "&sign=" . base64_encode($certificate->sign($chCTe));
        }
        $urlQRCode = "$url?chCTe=$chCTe&tpAmb=$tpAmb{$sign}";
        $infCTeSupl = $dom->createElement("infCTeSupl");
        $qrCode = $infCTeSupl->appendChild($dom->createElement('qrCodCTe'));
        $qrCode->appendChild($dom->createCDATASection($urlQRCode));
        $signature = $dom->getElementsByTagName('Signature')->item(0);
        $cte->insertBefore($infCTeSupl, $signature);
        $dom->formatOutput = false;
        return $dom->saveXML();
    }
}
