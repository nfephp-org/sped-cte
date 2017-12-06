<?php

namespace NFePHP\CTe;

/**
 * Class responsible for communication with SEFAZ extends
 * NFePHP\CTe\Common\Tools
 *
 * @category  NFePHP
 * @package   NFePHP\CTe\Tools
 * @copyright NFePHP Copyright (c) 2008-2017
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-cte for the canonical source repository
 */

use NFePHP\Common\Strings;
use NFePHP\Common\Signer;
use NFePHP\Common\UFList;
use NFePHP\CTe\Factories\Events;
use NFePHP\CTe\Common\Tools as ToolsCommon;
use RuntimeException;
use InvalidArgumentException;

class Tools extends ToolsCommon
{
    const EVT_CONFIRMACAO = 210200;
    const EVT_CIENCIA = 210210;
    const EVT_DESCONHECIMENTO = 210220;
    const EVT_NAO_REALIZADA = 210240;

    /**
     * Request authorization to issue CTe in batch with one or more documents
     * @param array $aXml array of cte's xml
     * @param string $idLote lote number
     * @param bool $compactar flag to compress data with gzip
     * @return string soap response xml
     */
    public function sefazEnviaLote(
        $aXml,
        $idLote = '',
        $compactar = false,
        &$xmls = []
    ) {
        if (!is_array($aXml)) {
            throw new \InvalidArgumentException('Os XML das CTe devem ser passados em um array.');
        }
        $servico = 'CteRecepcao';
        $this->checkContingencyForWebServices($servico);
        if ($this->contingency->type != '') {
            //em modo de contingencia
            //esses xml deverão ser modificados e re-assinados e retornados
            //no parametro $xmls para serem armazenados pelo aplicativo
            //pois serão alterados
            foreach ($aXml as $doc) {
                //corrigir o xml para o tipo de contigência setado
                $xmls[] = $this->correctNFeForContingencyMode($doc);
            }
            $aXml = $xmls;
        }

        $sxml = implode("", $aXml);
        $sxml = preg_replace("/<\?xml.*?\?>/", "", $sxml);
        $this->servico(
            $servico,
            $this->config->siglaUF,
            $this->tpAmb
        );
        $request = "<enviCTe xmlns=\"$this->urlPortal\" versao=\"$this->urlVersion\">"
            . "<idLote>$idLote</idLote>"
            . "$sxml"
            . "</enviCTe>";
        $this->isValid($this->urlVersion, $request, 'enviCTe');
        $this->lastRequest = $request;
        //montagem dos dados da mensagem SOAP
        $parameters = ['cteDadosMsg' => $request];
        $body = "<cteDadosMsg xmlns=\"$this->urlNamespace\">$request</cteDadosMsg>";
        $method = $this->urlMethod;
        if ($compactar) {
            $gzdata = base64_encode(gzencode($cons, 9, FORCE_GZIP));
            $body = "<cteDadosMsgZip xmlns=\"$this->urlNamespace\">$gzdata</cteDadosMsgZip>";
            $method = $this->urlMethod."Zip";
            $parameters = ['cteDadosMsgZip' => $gzdata];
            $body = "<cteDadosMsgZip xmlns=\"$this->urlNamespace\">$gzdata</cteDadosMsgZip>";
        }
        $this->lastResponse = $this->sendRequest($body, $parameters);
        return $this->lastResponse;
    }

    /**
     * Check status of Batch of CTe sent by receipt of this shipment
     * @param string $recibo
     * @param int $tpAmb
     * @return string
     */
    public function sefazConsultaRecibo($recibo, $tpAmb = null)
    {
        if (empty($tpAmb)) {
            $tpAmb = $this->tpAmb;
        }
        //carrega serviço
        $servico = 'CteRetRecepcao';
        $this->checkContingencyForWebServices($servico);
        $this->servico(
            $servico,
            $this->config->siglaUF,
            $tpAmb
        );
        if ($this->urlService == '') {
            $msg = "A consulta de CTe não está disponível na SEFAZ {$this->config->siglaUF}!!!";
            throw new RuntimeException($msg);
        }
        $request = "<consReciCTe xmlns=\"$this->urlPortal\" versao=\"$this->urlVersion\">"
            . "<tpAmb>$tpAmb</tpAmb>"
            . "<nRec>$recibo</nRec>"
            . "</consReciCTe>";
        $this->isValid($this->urlVersion, $request, 'consReciCTe');
        $this->lastRequest = $request;
        $parameters = ['cteDadosMsg' => $request];
        $body = "<cteDadosMsg xmlns=\"$this->urlNamespace\">$request</cteDadosMsg>";
        $this->lastResponse = $this->sendRequest($body, $parameters);
        return $this->lastResponse;
    }

    /**
     * Check the CTe status for the 44-digit key and retrieve the protocol
     * @param string $chave
     * @param int $tpAmb
     * @return string
     */
    public function sefazConsultaChave($chave, $tpAmb = null)
    {
        $uf = UFList::getUFByCode(substr($chave, 0, 2));
        if (empty($tpAmb)) {
            $tpAmb = $this->tpAmb;
        }
        //carrega serviço
        $servico = 'CteConsultaProtocolo';
        $this->checkContingencyForWebServices($servico);
        $this->servico(
            $servico,
            $uf,
            $tpAmb
        );
        $request = "<consSitCTe xmlns=\"$this->urlPortal\" versao=\"$this->urlVersion\">"
            . "<tpAmb>$tpAmb</tpAmb>"
            . "<xServ>CONSULTAR</xServ>"
            . "<chCTe>$chave</chCTe>"
            . "</consSitCTe>";
        $this->isValid($this->urlVersion, $request, 'consSitCTe');
        $this->lastRequest = $request;
        $parameters = ['cteDadosMsg' => $request];
        $body = "<cteDadosMsg xmlns=\"$this->urlNamespace\">$request</cteDadosMsg>";
        $this->lastResponse = $this->sendRequest($body, $parameters);
        return $this->lastResponse;
    }

    /**
     * Request to disable one or an NFe sequence of a given series
     * @param int $nSerie
     * @param int $nIni
     * @param int $nFin
     * @param string $xJust
     * @param int $tpAmb
     * @return string
     */
    public function sefazInutiliza(
        $nSerie,
        $nIni,
        $nFin,
        $xJust,
        $tpAmb = null
    ) {
        if (empty($tpAmb)) {
            $tpAmb = $this->tpAmb;
        }
        $xJust = Strings::replaceSpecialsChars($xJust);
        $nSerie = (integer) $nSerie;
        $nIni = (integer) $nIni;
        $nFin = (integer) $nFin;
        $servico = 'CteInutilizacao';
        $this->checkContingencyForWebServices($servico);
        //carrega serviço
        $this->servico(
            $servico,
            $this->config->siglaUF,
            $tpAmb
        );
        $cnpj = $this->config->cnpj;
        $strAno = (string) date('y');
        $strSerie = str_pad($nSerie, 3, '0', STR_PAD_LEFT);
        $strInicio = str_pad($nIni, 9, '0', STR_PAD_LEFT);
        $strFinal = str_pad($nFin, 9, '0', STR_PAD_LEFT);
        $idInut = "ID"
            . $this->urlcUF
            . $cnpj
            . $this->modelo
            . $strSerie
            . $strInicio
            . $strFinal;
        //limpa os caracteres indesejados da justificativa
        $xJust = Strings::replaceSpecialsChars($xJust);
        //montagem do corpo da mensagem
        $msg = "<inutCTe xmlns=\"$this->urlPortal\" versao=\"$this->urlVersion\">" .
            "<infInut Id=\"$idInut\">" .
            "<tpAmb>$tpAmb</tpAmb>" .
            "<xServ>INUTILIZAR</xServ>" .
            "<cUF>$this->urlcUF</cUF>" .
            "<ano>$strAno</ano>" .
            "<CNPJ>$cnpj</CNPJ>" .
            "<mod>$this->modelo</mod>" .
            "<serie>$nSerie</serie>" .
            "<nCTIni>$nIni</nCTIni>" .
            "<nCTFin>$nFin</nCTFin>" .
            "<xJust>$xJust</xJust>" .
            "</infInut></inutCTe>";
        //assina a solicitação
        $request = Signer::sign(
            $this->certificate,
            $msg,
            'infInut',
            'Id',
            $this->algorithm,
            $this->canonical
        );
        $request = Strings::clearXmlString($request, true);
        $this->isValid($this->urlVersion, $request, 'inutCTe');
        $this->lastRequest = $request;
        $parameters = ['cteDadosMsg' => $request];
        $body = "<cteDadosMsg xmlns=\"$this->urlNamespace\">$request</cteDadosMsg>";
        $this->lastResponse = $this->sendRequest($body, $parameters);
        return $this->lastResponse;
    }

    /**
     * Search for the registration data of an NFe issuer,
     * if in contingency mode this service will cause a
     * Exception and remember not all Sefaz have this service available,
     * so it will not work in some cases.
     * @param string $uf  federation unit
     * @param string $cnpj CNPJ number (optional)
     * @param string $iest IE number (optional)
     * @param string $cpf  CPF number (optional)
     * @return string xml soap response
     */
    public function sefazCadastro(
        $uf,
        $cnpj = '',
        $iest = '',
        $cpf = ''
    ) {
        if ($cnpj != '') {
            $filter = "<CNPJ>$cnpj</CNPJ>";
            $txtFile = "CNPJ_$cnpj";
        } elseif ($iest != '') {
            $filter = "<IE>$iest</IE>";
            $txtFile = "IE_$iest";
        } else {
            $filter = "<CPF>$cpf</CPF>";
            $txtFile = "CPF_$cpf";
        }
        //carrega serviço
        $servico = 'CteConsultaCadastro';
        $this->checkContingencyForWebServices($servico);
        $this->servico(
            $servico,
            $uf,
            $this->tpAmb,
            true
        );
        $request = "<ConsCad xmlns=\"$this->urlPortal\" versao=\"$this->urlVersion\">"
            . "<infCons>"
            . "<xServ>CONS-CAD</xServ>"
            . "<UF>$uf</UF>"
            . "$filter</infCons></ConsCad>";
        $this->isValid($this->urlVersion, $request, 'consCad');
        $this->lastRequest = $request;
        $parameters = ['cteDadosMsg' => $request];
        $body = "<cteDadosMsg xmlns=\"$this->urlNamespace\">$request</cteDadosMsg>";
        $this->lastResponse = $this->sendRequest($body, $parameters);
        return $this->lastResponse;
    }

    /**
     * Check services status SEFAZ/SVC
     * If $uf is empty use normal check with contingency
     * If $uf is NOT empty ignore contingency mode
     * @param string $uf  initials of federation unit
     * @param int $tpAmb
     * @return string xml soap response
     */
    public function sefazStatus($uf = '', $tpAmb = null)
    {
        if (empty($tpAmb)) {
            $tpAmb = $this->tpAmb;
        }
        $ignoreContingency = true;
        if (empty($uf)) {
            $uf = $this->config->siglaUF;
            $ignoreContingency = false;
        }
        $servico = 'CteStatusServico';
        $this->checkContingencyForWebServices($servico);
        $this->servico(
            $servico,
            $uf,
            $tpAmb,
            $ignoreContingency
        );
        $request = "<consStatServCte xmlns=\"$this->urlPortal\" versao=\"$this->urlVersion\">"
            . "<tpAmb>$tpAmb</tpAmb>"
            . "<xServ>STATUS</xServ></consStatServCte>";
        $this->isValid($this->urlVersion, $request, 'consStatServCte');
        $this->lastRequest = $request;
        $parameters = ['cteDadosMsg' => $request];
        $body = "<cteDadosMsg xmlns=\"$this->urlNamespace\">$request</cteDadosMsg>";
        $this->lastResponse = $this->sendRequest($body, $parameters);
        return $this->lastResponse;
    }

    /**
     * Service for the distribution of summary information and
     * electronic tax documents of interest to an actor.
     * @param integer $ultNSU  last NSU number recived
     * @param integer $numNSU  NSU number you wish to consult
     * @param string $fonte data source 'AN' and for some cases it may be 'RS'
     * @return string
     */
    public function sefazDistDFe(
        $ultNSU = 0,
        $numNSU = 0,
        $fonte = 'AN'
    ) {
        //carrega serviço
        $servico = 'CTeDistribuicaoDFe';
        $this->checkContingencyForWebServices($servico);
        $this->servico(
            $servico,
            $fonte,
            1,
            true
        );
        $cUF = UFList::getCodeByUF($this->config->siglaUF);
        $ultNSU = str_pad($ultNSU, 15, '0', STR_PAD_LEFT);
        $tagNSU = "<distNSU><ultNSU>$ultNSU</ultNSU></distNSU>";
        if ($numNSU != 0) {
            $numNSU = str_pad($numNSU, 15, '0', STR_PAD_LEFT);
            $tagNSU = "<consNSU><NSU>$numNSU</NSU></consNSU>";
        }
        //monta a consulta
        $consulta = "<distDFeInt xmlns=\"$this->urlPortal\" versao=\"$this->urlVersion\">"
            . "<tpAmb>1</tpAmb>"
            . "<cUFAutor>$cUF</cUFAutor>"
            . "<CNPJ>".$this->config->cnpj."</CNPJ>$tagNSU</distDFeInt>";
        //valida o xml da requisição
        $this->isValid($this->urlVersion, $consulta, 'distDFeInt');
        $this->lastRequest = $consulta;
        //montagem dos dados da mensagem SOAP
        $request = "<cteDadosMsg xmlns=\"$this->urlNamespace\">$consulta</cteDadosMsg>";
        $parameters = ['cteDistDFeInteresse' => $request];
        $body = "<cteDistDFeInteresse xmlns=\"$this->urlNamespace\">"
            . $request
            . "</cteDistDFeInteresse>";
        //este webservice não requer cabeçalho
        $this->objHeader = null;
        $this->lastResponse = $this->sendRequest($body, $parameters);
        return $this->lastResponse;
    }

    /**
     * Request authorization for Letter of Correction
     * @param  string $chave
     * @param  array $infCorrecao
     * @param  int $nSeqEvento
     * @return string
     */
    public function sefazCCe($chave, $infCorrecao = [], $nSeqEvento = 1)
    {
        $uf = $this->validKeyByUF($chave);
        $tpEvento = 110110;
        $tagAdic = self::serializerCCe($infCorrecao);
        return $this->sefazEvento(
            $uf,
            $chave,
            $tpEvento,
            $nSeqEvento,
            $tagAdic
        );
    }

    /**
     * Request extension of the term of return of products of an NF-e of
     * consignment for industrialization to order with suspension of ICMS
     * in interstate operations
     * @param  string  $chNFe
     * @param  string  $nProt
     * @param  integer $tipo 1-primerio prazo, 2-segundo prazo
     * @param  array   $itens
     * @param  integer $nSeqEvento
     * @return string
     */
    public function sefazEPP(
        $chNFe,
        $nProt,
        $itens = array(),
        $tipo = 1,
        $nSeqEvento = 1
    ) {
        $uf = UFList::getUFByCode(substr($chNFe, 0, 2));
        $tpEvento = 111500;
        if ($tipo == 2) {
            $tpEvento = 111501;
        }
        $tagAdic = "<nProt>$nProt</nProt>";
        foreach ($itens as $item) {
            $tagAdic .= "<itemPedido numItem=\""
                . $item[0]
                . "\"><qtdeItem>"
                . $item[1]
                ."</qtdeItem></itemPedido>";
        }
        return $this->sefazEvento(
            $uf,
            $chNFe,
            $tpEvento,
            $nSeqEvento,
            $tagAdic
        );
    }

    /**
     * Request the cancellation of the request for an extension of the term
     * of return of products of an NF-e of consignment for industrialization
     * by order with suspension of ICMS in interstate operations
     * @param  string  $chNFe
     * @param  string  $nProt
     * @param  integer $nSeqEvento
     * @return string
     */
    public function sefazECPP(
        $chNFe,
        $nProt,
        $nSeqEvento = 1
    ) {
        $uf = UFList::getUFByCode(substr($chNFe, 0, 2));
        $tpEvento = 111502;
        $origEvent = 111500;
        if ($nSeqEvento == 2) {
            $tpEvento = 111503;
            $origEvent = 111501;
        }
        $sSeqEvento = str_pad($nSeqEvento, 2, "0", STR_PAD_LEFT);
        $idPedidoCancelado = "ID$origEvent$chNFe$sSeqEvento";
        $tagAdic = "<idPedidoCancelado>"
                . "$idPedidoCancelado"
                . "</idPedidoCancelado>"
                . "<nProt>$nProt</nProt>";
        return $this->sefazEvento(
            $uf,
            $chNFe,
            $tpEvento,
            $nSeqEvento,
            $tagAdic
        );
    }

    /**
     * Requires cte cancellation
     * @param  string $chave key of CTe
     * @param  string $xJust justificative 255 characters max
     * @param  string $nProt protocol number
     * @return string
     */
    public function sefazCancela($chave, $xJust, $nProt)
    {
        $uf = $this->validKeyByUF($chave);
        $xJust = Strings::replaceSpecialsChars(
            substr(trim($xJust), 0, 255)
        );
        $tpEvento = 110111;
        $nSeqEvento = 1;
        $tagAdic = "<evCancCTe>"
            . "<descEvento>Cancelamento</descEvento>"
            . "<nProt>$nProt</nProt>"
            . "<xJust>$xJust</xJust>"
            . "</evCancCTe>";
        return $this->sefazEvento(
            $uf,
            $chave,
            $tpEvento,
            $nSeqEvento,
            $tagAdic
        );
    }

    /**
     * Request the registration of the manifestation of recipient
     * @param string $chNFe
     * @param int $tpEvento
     * @param string $xJust Justification for not carrying out the operation
     * @param int $nSeqEvento
     * @return string
     */
    public function sefazManifesta(
        $chNFe,
        $tpEvento,
        $xJust = '',
        $nSeqEvento = 1
    ) {
        $tagAdic = '';
        if ($tpEvento == 210240) {
            $xJust = Strings::replaceSpecialsChars(substr(trim($xJust), 0, 255));
            $tagAdic = "<xJust>$xJust</xJust>";
        }
        return $this->sefazEvento(
            'AN',
            $chNFe,
            $tpEvento,
            $nSeqEvento,
            $tagAdic
        );
    }

    /**
     * Request authorization for issuance in contingency EPEC
     * @param  string $xml
     * @return string
     */
    public function sefazEPEC(&$xml)
    {
        $tagAdic = '';
        $tpEvento = 110140;
        $nSeqEvento = 1;
        if ($this->contingency->type !== 'EPEC') {
            throw new \RuntimeException('A contingência EPEC deve estar ativada.');
        }
        $xml = $this->correctNFeForContingencyMode($xml);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($xml);
        $infNFe = $dom->getElementsByTagName('infNFe')->item(0);
        $emit = $dom->getElementsByTagName('emit')->item(0);
        $dest = $dom->getElementsByTagName('dest')->item(0);
        $cOrgaoAutor = UFList::getCodeByUF($this->config->siglaUF);
        $chNFe = substr($infNFe->getAttribute('Id'), 3, 44);
        // EPEC
        $dhEmi = $dom->getElementsByTagName('dhEmi')->item(0)->nodeValue;
        $tpNF = $dom->getElementsByTagName('tpNF')->item(0)->nodeValue;
        $emitIE = $emit->getElementsByTagName('IE')->item(0)->nodeValue;
        $destUF = $dest->getElementsByTagName('UF')->item(0)->nodeValue;
        $total = $dom->getElementsByTagName('total')->item(0);
        $vNF = $total->getElementsByTagName('vNF')->item(0)->nodeValue;
        $vICMS = $total->getElementsByTagName('vICMS')->item(0)->nodeValue;
        $vST = $total->getElementsByTagName('vST')->item(0)->nodeValue;
        $dID = $dest->getElementsByTagName('CNPJ')->item(0)->nodeValue;
        if (!empty($dID)) {
            $destID = "<CNPJ>$dID</CNPJ>";
        } else {
            $dID = $dest->getElementsByTagName('CPF')->item(0)->nodeValue;
            if (!empty($dID)) {
                $destID = "<CPF>$dID</CPF>";
            } else {
                $dID = $dest->getElementsByTagName('idEstrangeiro')
                    ->item(0)
                    ->nodeValue;
                $destID = "<idEstrangeiro>$dID</idEstrangeiro>";
            }
        }
        $dIE = !empty($dest->getElementsByTagName('IE')->item(0)->nodeValue) ?
                $dest->getElementsByTagName('IE')->item(0)->nodeValue : '';
        $destIE = '';
        if (!empty($dIE)) {
            $destIE = "<IE>$dIE</IE>";
        }
        $tagAdic = "<cOrgaoAutor>$cOrgaoAutor</cOrgaoAutor>"
            . "<tpAutor>1</tpAutor>"
            . "<verAplic>$this->verAplic</verAplic>"
            . "<dhEmi>$dhEmi</dhEmi>"
            . "<tpNF>$tpNF</tpNF>"
            . "<IE>$emitIE</IE>"
            . "<dest>"
            . "<UF>$destUF</UF>"
            . $destID
            . $destIE
            . "<vNF>$vNF</vNF>"
            . "<vICMS>$vICMS</vICMS>"
            . "<vST>$vST</vST>"
            . "</dest>";

        return $this->sefazEvento(
            'AN',
            $chNFe,
            $tpEvento,
            $nSeqEvento,
            $tagAdic
        );
    }

    /**
     * Send event to SEFAZ
     * @param string $uf
     * @param string $chave
     * @param int $tpEvento
     * @param int $nSeqEvento
     * @param string $tagAdic
     * @return string
     */
    public function sefazEvento(
        $uf,
        $chave,
        $tpEvento,
        $nSeqEvento = 1,
        $tagAdic = ''
    ) {
        $ignore = false;
        if ($tpEvento == 110140) {
            $ignore = true;
        }
        $servico = 'CteRecepcaoEvento';
        $this->checkContingencyForWebServices($servico);
        $this->servico(
            $servico,
            $uf,
            $this->tpAmb,
            $ignore
        );
        $ev = $this->tpEv($tpEvento);
        $aliasEvento = $ev->alias;
        $descEvento = $ev->desc;
        $cnpj = $this->config->cnpj;
        $dt = new \DateTime();
        $dhEvento = $dt->format('Y-m-d\TH:i:sP');
        $sSeqEvento = str_pad($nSeqEvento, 2, "0", STR_PAD_LEFT);
        $eventId = "ID".$tpEvento.$chave.$sSeqEvento;
        $cOrgao = UFList::getCodeByUF($uf);
        $request = "<eventoCTe xmlns=\"$this->urlPortal\" versao=\"$this->urlVersion\">"
            . "<infEvento Id=\"$eventId\">"
            . "<cOrgao>$cOrgao</cOrgao>"
            . "<tpAmb>$this->tpAmb</tpAmb>"
            . "<CNPJ>$cnpj</CNPJ>"
            . "<chCTe>$chave</chCTe>"
            . "<dhEvento>$dhEvento</dhEvento>"
            . "<tpEvento>$tpEvento</tpEvento>"
            . "<nSeqEvento>$nSeqEvento</nSeqEvento>"
            . "<detEvento versaoEvento=\"$this->urlVersion\">"
            . "$tagAdic"
            . "</detEvento>"
            . "</infEvento>"
            . "</eventoCTe>";
        //assinatura dos dados
        $request = Signer::sign(
            $this->certificate,
            $request,
            'infEvento',
            'Id',
            $this->algorithm,
            $this->canonical
        );
        $request = Strings::clearXmlString($request, true);

        $this->isValid($this->urlVersion, $request, 'eventoCTe');
        $this->lastRequest = $request;
        $parameters = ['cteDadosMsg' => $request];
        $body = "<cteDadosMsg xmlns=\"$this->urlNamespace\">$request</cteDadosMsg>";
        $this->lastResponse = $this->sendRequest($body, $parameters);
        return $this->lastResponse;
    }

    /**
     * Request the NFe download already manifested by its recipient, by the key
     * using new service in CTeDistribuicaoDFe
     * @param  string $chave
     * @return string
     */
    public function sefazDownload($chave)
    {
        //carrega serviço
        $servico = 'CteDistribuicaoDFe';
        $this->checkContingencyForWebServices($servico);
        $this->servico(
            $servico,
            'AN',
            $this->tpAmb,
            true
        );
        $cUF = UFList::getCodeByUF($this->config->siglaUF);
        $tagChave = "<consChNFe><chNFe>$chave</chNFe></consChNFe>";
        //monta a consulta
        $consulta = "<distDFeInt xmlns=\"$this->urlPortal\" versao=\"$this->urlVersion\">"
            . "<tpAmb>".$this->tpAmb."</tpAmb>"
            . "<cUFAutor>$cUF</cUFAutor>"
            . "<CNPJ>".$this->config->cnpj."</CNPJ>$tagChave</distDFeInt>";
        //valida o xml da requisição
        $this->isValid($this->urlVersion, $consulta, 'distDFeInt');
        $this->lastRequest = $consulta;
        //montagem dos dados da mensagem SOAP
        $request = "<cteDadosMsg xmlns=\"$this->urlNamespace\">$consulta</cteDadosMsg>";
        $parameters = ['cteDistDFeInteresse' => $request];
        $body = "<cteDistDFeInteresse xmlns=\"$this->urlNamespace\">"
            . $request
            . "</cteDistDFeInteresse>";
        //este webservice não requer cabeçalho
        $this->objHeader = null;
        $this->lastResponse = $this->sendRequest($body, $parameters);
        return $this->lastResponse;
    }

    /**
     * Maintenance of the Taxpayer Security Code - CSC (Old Token)
     * @param int $indOp Identificador do tipo de operação:
     *                   1 - Consulta CSC Ativos;
     *                   2 - Solicita novo CSC;
     *                   3 - Revoga CSC Ativo
     * @return string
     */
    public function sefazCsc($indOp)
    {
        if ($this->modelo != 65) {
            throw new RuntimeException(
                "Esta operação é exclusiva de NFCe modelo [65], "
                . "você está usando modelo [55]."
            );
        }
        $raizCNPJ = substr($this->config->cnpj, 0, -6);
        //carrega serviço
        $servico = 'CscNFCe';
        $this->checkContingencyForWebServices($servico);
        $this->servico(
            $servico,
            $this->config->siglaUF,
            $this->tpAmb
        );
        $request = "<admCscNFCe versao=\"$this->urlVersion\" xmlns=\"$this->urlPortal\">"
            . "<tpAmb>$this->tpAmb</tpAmb>"
            . "<indOp>$indOp</indOp>"
            . "<raizCNPJ>$raizCNPJ</raizCNPJ>"
            . "</admCscNFCe>";
        if ($indOp == 3) {
            $request = "<admCscNFCe versao=\"$this->urlVersion\" xmlns=\"$this->urlPortal\">"
            . "<tpAmb>$this->tpAmb</tpAmb>"
            . "<indOp>$indOp</indOp>"
            . "<raizCNPJ>$raizCNPJ</raizCNPJ>"
            . "<dadosCsc>"
            . "<idCsc>".$this->config->CSCid."</idCsc>"
            . "<codigoCsc>".$this->config->CSC."</codigoCsc>"
            . "</dadosCsc>"
            . "</admCscNFCe>";
        }
        //o xsd não está disponivel
        //$this->isValid($this->urlVersion, $request, 'cscNFCe');
        $this->lastRequest = $request;
        $parameters = ['cteDadosMsg' => $request];
        $body = "<cteDadosMsg xmlns=\"$this->urlNamespace\">$request</cteDadosMsg>";
        $this->lastResponse = $this->sendRequest($body, $parameters);
        return $this->lastResponse;
    }

    /**
     * Checks the validity of an NFe, normally used for received NFe
     * @param  string $cte
     * @return boolean
     */
    public function sefazValidate($cte)
    {
        //verifica a assinatura da NFe, exception caso de falha
        Signer::isSigned($cte);
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = false;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($cte);
        //verifica a validade no webservice da SEFAZ
        $tpAmb = $dom->getElementsByTagName('tpAmb')->item(0)->nodeValue;
        $infNFe  = $dom->getElementsByTagName('infNFe')->item(0);
        $chNFe = preg_replace('/[^0-9]/', '', $infNFe->getAttribute("Id"));
        $protocol = $dom->getElementsByTagName('nProt')->item(0)->nodeValue;
        $digval = $dom->getElementsByTagName('DigestValue')->item(0)->nodeValue;
        //consulta a NFe
        $response = $this->sefazConsultaChave($chNFe, $tpAmb);
        $ret = new \DOMDocument('1.0', 'UTF-8');
        $ret->preserveWhiteSpace = false;
        $ret->formatOutput = false;
        $ret->loadXML($response);
        $retProt = $ret->getElementsByTagName('protNFe')->item(0);
        if (!isset($retProt)) {
            throw new InvalidArgumentException(
                'O documento de resposta não contêm o NODE "protNFe".'
            );
        }
        $infProt = $ret->getElementsByTagName('infProt')->item(0);
        $cStat  = $infProt->getElementsByTagName('cStat')->item(0)->nodeValue;
        $xMotivo = $infProt->getElementsByTagName('xMotivo')->item(0)->nodeValue;
        $dig = $infProt->getElementsByTagName("digVal")->item(0);
        $digProt = '000';
        if (isset($dig)) {
            $digProt = $dig->nodeValue;
        }
        $chProt = $infProt->getElementsByTagName("chNFe")->item(0)->nodeValue;
        $nProt = $infProt->getElementsByTagName("nProt")->item(0)->nodeValue;
        if ($protocol == $nProt
            && $digval == $digProt
            && $chNFe == $chProt
        ) {
            return true;
        }
        return false;
    }

    /**
     *
     * @param  int $tpEvento
     * @return \stdClass
     * @throws Exception
     */
    private function tpEv($tpEvento)
    {
        $std = new \stdClass();
        $std->alias = '';
        $std->desc = '';
        switch ($tpEvento) {
            case 110110:
                //CCe
                $std->alias = 'CCe';
                $std->desc = 'Carta de Correcao';
                break;
            case 110111:
                //cancelamento
                $std->alias = 'CancCTe';
                $std->desc = 'Cancelamento';
                break;
            case 110140:
                //EPEC
                //emissão em contingência EPEC
                $std->alias = 'EPEC';
                $std->desc = 'EPEC';
                break;
            case 111500:
            case 111501:
                //EPP
                //Pedido de prorrogação
                $std->alias = 'EPP';
                $std->desc = 'Pedido de Prorrogacao';
                break;
            case 111502:
            case 111503:
                //ECPP
                //Cancelamento do Pedido de prorrogação
                $std->alias = 'ECPP';
                $std->desc = 'Cancelamento de Pedido de Prorrogacao';
                break;
            case 210200:
                //Confirmacao da Operacao
                $std->alias = 'EvConfirma';
                $std->desc = 'Confirmacao da Operacao';
                break;
            case 210210:
                //Ciencia da Operacao
                $std->alias = 'EvCiencia';
                $std->desc = 'Ciencia da Operacao';
                $std->tpAutor = 2;
                break;
            case 210220:
                //Desconhecimento da Operacao
                $std->alias = 'EvDesconh';
                $std->desc = 'Desconhecimento da Operacao';
                break;
            case 210240:
                //Operacao não Realizada
                $std->alias = 'EvNaoRealizada';
                $std->desc = 'Operacao nao Realizada';
                break;
            default:
                $msg = "O código do tipo de evento informado não corresponde a "
                . "nenhum evento estabelecido.";
                throw new RuntimeException($msg);
        }
        return $std;
    }
    
    private static function serializerCCe(array $infCorrecoes)
    {
        // Grupo de Informações de Correção
        $correcoes = '';
        foreach ($infCorrecoes as $info) {
            $nroItemAlteradoOptionalElement = '';
            if (key_exists('nroItemAlterado', $info)) {
                $nroItemAlteradoOptionalElement = "<nroItemAlterado>{$info['nroItemAlterado']}</nroItemAlterado>";
            }
            $correcoes .= "<infCorrecao>" .
                "<grupoAlterado>{$info['grupoAlterado']}</grupoAlterado>" .
                "<campoAlterado>{$info['campoAlterado']}</campoAlterado>" .
                "<valorAlterado>{$info['valorAlterado']}</valorAlterado>" .
                "{$nroItemAlteradoOptionalElement}" .
                "</infCorrecao>";
        }
        //monta mensagem
        return "<evCCeCTe>" .
            "<descEvento>Carta de Correcao</descEvento>" .
            "{$correcoes}" .
            "<xCondUso>" .
            "A Carta de Correcao e disciplinada pelo Art. 58-B do " .
            "CONVENIO/SINIEF 06/89: Fica permitida a utilizacao de carta de " .
            "correcao, para regularizacao de erro ocorrido na emissao de " .
            "documentos fiscais relativos a prestacao de servico de transporte, " .
            "desde que o erro nao esteja relacionado com: I - as variaveis que " .
            "determinam o valor do imposto tais como: base de calculo, " .
            "aliquota, diferenca de preco, quantidade, valor da prestacao;II - " .
            "a correcao de dados cadastrais que implique mudanca do emitente, " .
            "tomador, remetente ou do destinatario;III - a data de emissao ou " .
            "de saida." .
            "</xCondUso>" .
        "</evCCeCTe>";
    }
}
