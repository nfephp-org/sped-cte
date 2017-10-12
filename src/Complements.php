<?php

namespace NFePHP\CTe;

use NFePHP\Common\Strings;
use NFePHP\CTe\Common\Standardize;
use NFePHP\CTe\Exception\DocumentsException;
use DOMDocument;

class Complements
{
    protected static $urlPortal = 'http://www.portalfiscal.inf.br/cte';

    /**
     * Authorize document adding his protocol
     * @param string $request
     * @param string $response
     * @return string
     */
    public static function toAuthorize($request, $response)
    {
        $st = new Standardize();
        $key = ucfirst($st->whichIs($request));
        if ($key != 'CTe' && $key != 'EventoCTe' && $key != 'InutCTe') {
            //wrong document, this document is not able to recieve a protocol
            throw DocumentsException::wrongDocument(0, $key);
        }
        $func = "add".$key."Protocol";
        return self::$func($request, $response);
    }
    
    /**
     * Authorize Inutilization of numbers
     * @param string $request
     * @param string $response
     * @return string
     * @throws InvalidArgumentException
     */
    protected static function addInutCTeProtocol($request, $response)
    {
        $req = new DOMDocument('1.0', 'UTF-8');
        $req->preserveWhiteSpace = false;
        $req->formatOutput = false;
        $req->loadXML($request);
        $inutCTe = $req->getElementsByTagName('inutCTe')->item(0);
        $versao = $inutCTe->getAttribute("versao");
        $infInut = $req->getElementsByTagName('infInut')->item(0);
        $tpAmb = $infInut->getElementsByTagName('tpAmb')->item(0)->nodeValue;
        $cUF = $infInut->getElementsByTagName('cUF')->item(0)->nodeValue;
        $ano = $infInut->getElementsByTagName('ano')->item(0)->nodeValue;
        $cnpj = $infInut->getElementsByTagName('CNPJ')->item(0)->nodeValue;
        $mod = $infInut->getElementsByTagName('mod')->item(0)->nodeValue;
        $serie = $infInut->getElementsByTagName('serie')->item(0)->nodeValue;
        $nCTIni = $infInut->getElementsByTagName('nCTIni')->item(0)->nodeValue;
        $nCTFin = $infInut->getElementsByTagName('nCTFin')->item(0)->nodeValue;
        
        $ret = new DOMDocument('1.0', 'UTF-8');
        $ret->preserveWhiteSpace = false;
        $ret->formatOutput = false;
        $ret->loadXML($response);
        $retInutCTe = $ret->getElementsByTagName('retInutCTe')->item(0);
        if (!isset($retInutCTe)) {
            throw DocumentsException::wrongDocument(3, "&lt;retInutCTe;");
        }
        $retversao = $retInutCTe->getAttribute("versao");
        $retInfInut = $ret->getElementsByTagName('infInut')->item(0);
        $cStat = $retInfInut->getElementsByTagName('cStat')->item(0)->nodeValue;
        $xMotivo = $retInfInut->getElementsByTagName('xMotivo')->item(0)->nodeValue;
        if ($cStat != 102) {
            throw DocumentsException::wrongDocument(4, "[$cStat] $xMotivo.");
        }
        $rettpAmb = $retInfInut->getElementsByTagName('tpAmb')->item(0)->nodeValue;
        $retcUF = $retInfInut->getElementsByTagName('cUF')->item(0)->nodeValue;
        $retano = $retInfInut->getElementsByTagName('ano')->item(0)->nodeValue;
        $retcnpj = $retInfInut->getElementsByTagName('CNPJ')->item(0)->nodeValue;
        $retmod = $retInfInut->getElementsByTagName('mod')->item(0)->nodeValue;
        $retserie = $retInfInut->getElementsByTagName('serie')->item(0)->nodeValue;
        $retnCTIni = $retInfInut->getElementsByTagName('nCTIni')->item(0)->nodeValue;
        $retnCTFin = $retInfInut->getElementsByTagName('nCTFin')->item(0)->nodeValue;
        if ($versao != $retversao ||
            $tpAmb != $rettpAmb ||
            $cUF != $retcUF ||
            $ano != $retano ||
            $cnpj != $retcnpj ||
            $mod != $retmod ||
            $serie != $retserie ||
            $nCTIni != $retnCTIni ||
            $nCTFin != $retnCTFin
        ) {
            throw DocumentsException::wrongDocument(5);
        }
        return self::join(
            $req->saveXML($inutCTe),
            $ret->saveXML($retInutCTe),
            'procInutCTe',
            $versao
        );
    }

    /**
     * Authorize NFe
     * @param string $request
     * @param string $response
     * @return string
     * @throws InvalidArgumentException
     */
    protected static function addCTeProtocol($request, $response)
    {
        $req = new DOMDocument('1.0', 'UTF-8');
        $req->preserveWhiteSpace = false;
        $req->formatOutput = false;
        $req->loadXML($request);
        
        $cte = $req->getElementsByTagName('CTe')->item(0);
        $infCTe = $req->getElementsByTagName('infCte')->item(0);
        $versao = $infCTe->getAttribute("versao");
        $chave = preg_replace('/[^0-9]/', '', $infCTe->getAttribute("Id"));
        $digCTe = $req->getElementsByTagName('DigestValue')
            ->item(0)
            ->nodeValue;
                
        $ret = new DOMDocument('1.0', 'UTF-8');
        $ret->preserveWhiteSpace = false;
        $ret->formatOutput = false;
        $ret->loadXML($response);
        $retProt = $ret->getElementsByTagName('protCTe')->item(0);
        if (!isset($retProt)) {
            throw DocumentsException::wrongDocument(3, "&lt;protCTe&gt;");
        }
        $infProt = $ret->getElementsByTagName('infProt')->item(0);
        $cStat  = $infProt->getElementsByTagName('cStat')->item(0)->nodeValue;
        $xMotivo = $infProt->getElementsByTagName('xMotivo')->item(0)->nodeValue;
        $dig = $infProt->getElementsByTagName("digVal")->item(0);
        $digProt = '000';
        if (isset($dig)) {
            $digProt = $dig->nodeValue;
        }
        //100 Autorizado
        //150 Autorizado fora do prazo
        //110 Uso Denegado
        //205 CTe Denegada
        $cstatpermit = ['100', '150', '110', '205'];
        if (!in_array($cStat, $cstatpermit)) {
            throw DocumentsException::wrongDocument(4, "[$cStat] $xMotivo");
        }
        if ($digCTe !== $digProt) {
            throw DocumentsException::wrongDocument(5, "O digest é diferente");
        }
        return self::join(
            $req->saveXML($cte),
            $ret->saveXML($retProt),
            'cteProc',
            $versao
        );
    }
    
    /**
     * Authorize Event
     * @param string $request
     * @param string $response
     * @return string
     * @throws InvalidArgumentException
     */
    protected static function addEventoCTeProtocol($request, $response)
    {
        $ev = new \DOMDocument('1.0', 'UTF-8');
        $ev->preserveWhiteSpace = false;
        $ev->formatOutput = false;
        $ev->loadXML($request);
        //extrai numero do lote do envio
        //$envLote = $ev->getElementsByTagName('idLote')->item(0)->nodeValue;
        //extrai tag evento do xml origem (solicitação)
        $event = $ev->getElementsByTagName('eventoCTe')->item(0);
        $versao = $event->getAttribute('versao');
        
        $ret = new \DOMDocument('1.0', 'UTF-8');
        $ret->preserveWhiteSpace = false;
        $ret->formatOutput = false;
        $ret->loadXML($response);
        //extrai numero do lote da resposta
//        $resLote = $ret->getElementsByTagName('idLote')->item(0)->nodeValue;
        //extrai a rag retEvento da resposta (retorno da SEFAZ)
        $retEv = $ret->getElementsByTagName('retEventoCTe')->item(0);
        $cStat  = $retEv->getElementsByTagName('cStat')->item(0)->nodeValue;
        $xMotivo = $retEv->getElementsByTagName('xMotivo')->item(0)->nodeValue;
        $tpEvento = $retEv->getElementsByTagName('tpEvento')->item(0)->nodeValue;
        $cStatValids = ['135', '136'];
        if ($tpEvento == '110111') {
            $cStatValids[] = '155';
        }
        if (!in_array($cStat, $cStatValids)) {
            throw DocumentsException::wrongDocument(4, "[$cStat] $xMotivo");
        }
        
        return self::join(
            $ev->saveXML($event),
            $ret->saveXML($retEv),
            'procEventoCTe',
            $versao
        );
    }
    
    /**
     * Join the pieces of the source document with those of the answer
     * @param string $first
     * @param string $second
     * @param string $nodename
     * @param string $versao
     * @return string
     */
    protected static function join($first, $second, $nodename, $versao)
    {
        $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
                . "<$nodename versao=\"$versao\" "
                . "xmlns=\"".self::$urlPortal."\">";
        $xml .= $first;
        $xml .= $second;
        $xml .= "</$nodename>";
        return $xml;
    }
}
