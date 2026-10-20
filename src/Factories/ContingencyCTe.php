<?php

namespace NFePHP\CTe\Factories;

use DateTime;
use NFePHP\Common\Keys;
use NFePHP\Common\Signer;
use NFePHP\Common\Strings;

class ContingencyCTe
{
    /**
     * Corrects CTe fields when in contingency mode
     * @param string $xml CTe xml content
     * @param Contingency $contingency
     * @return string
     */
    public static function adjust($xml, Contingency $contingency)
    {
        if ($contingency->type == '') {
            return $xml;
        }
        $xml = Signer::removeSignature($xml);

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($xml);

        $ide = $dom->getElementsByTagName('ide')->item(0);
        $cUF = $ide->getElementsByTagName('cUF')->item(0)->nodeValue;
        $cNF = $ide->getElementsByTagName('cCT')->item(0)->nodeValue;
        $nNF = $ide->getElementsByTagName('nCT')->item(0)->nodeValue;
        $serie = $ide->getElementsByTagName('serie')->item(0)->nodeValue;
        $mod = $ide->getElementsByTagName('mod')->item(0)->nodeValue;
        $dtEmi = new DateTime($ide->getElementsByTagName('dhEmi')->item(0)->nodeValue);
        $ano = $dtEmi->format('y');
        $mes = $dtEmi->format('m');
        $tpEmis = (string)$contingency->tpEmis;
        $emit = $dom->getElementsByTagName('emit')->item(0);
        $cnpj = $emit->getElementsByTagName('CNPJ')->item(0)->nodeValue;

        $motivo = trim(Strings::replaceUnacceptableCharacters($contingency->motive));
        $dt = new DateTime();
        $dt->setTimestamp($contingency->timestamp);
        $ide->getElementsByTagName('tpEmis')
            ->item(0)
            ->nodeValue = $contingency->tpEmis;
        // dhCont e xJust sao obrigatorios com tpEmis 2 (contingencia off-line, NT CT-e 2026.004
        // regras G009/H005) e com tpEmis 5 (FS-DA, extinto pela mesma NT)
        if (in_array($tpEmis, ['2', '5'], true)) {
            $nodeDhCont = $ide->getElementsByTagName('dhCont')->item(0);
            if (!empty($nodeDhCont)) {
                $nodeDhCont->nodeValue = $dt->format('Y-m-d\TH:i:sP');
            } else {
                $nodeDhCont = $dom->createElement('dhCont', $dt->format('Y-m-d\TH:i:sP'));
                self::insertInIde($ide, $nodeDhCont);
            }
            $nodeXJust = $ide->getElementsByTagName('xJust')->item(0);
            if (!empty($nodeXJust)) {
                $nodeXJust->nodeValue = $motivo;
            } else {
                $nodeXJust = $dom->createElement('xJust', $motivo);
                if (!empty($nodeDhCont->nextSibling)) {
                    $ide->insertBefore($nodeXJust, $nodeDhCont->nextSibling);
                } else {
                    $ide->appendChild($nodeXJust);
                }
            }
        }
        //corrigir a chave
        $infCte = $dom->getElementsByTagName('infCte')->item(0);
        $chave = Keys::build(
            $cUF,
            $ano,
            $mes,
            $cnpj,
            $mod,
            $serie,
            $nNF,
            $tpEmis,
            $cNF
        );
        $ide->getElementsByTagName('cDV')->item(0)->nodeValue = substr($chave, -1);
        $infCte->setAttribute('Id', 'CTe' . $chave);
        return Strings::clearXmlString($dom->saveXML(), true);
    }

    /**
     * Insere dhCont no ide respeitando a ordem do XSD: dhCont/xJust vem antes de
     * gCompraGov, tpPagAnt e gPagAntecipado (PL_CTe_400_RTC); sem eles, fica no fim do ide
     * @param \DOMElement $ide
     * @param \DOMElement $node
     * @return void
     */
    private static function insertInIde(\DOMElement $ide, \DOMElement $node)
    {
        foreach ($ide->childNodes as $child) {
            if (in_array($child->nodeName, ['gCompraGov', 'tpPagAnt', 'gPagAntecipado'], true)) {
                $ide->insertBefore($node, $child);
                return;
            }
        }
        $ide->appendChild($node);
    }
}
