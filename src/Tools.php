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

use InvalidArgumentException;
use NFePHP\Common\Signer;
use NFePHP\Common\Strings;
use NFePHP\Common\UFList;
use NFePHP\CTe\Common\Tools as ToolsCommon;
use RuntimeException;

class Tools extends ToolsCommon
{
    /**
     * Request authorization to issue CTe in batch with one or more documents
     * @param string $xml string do xml
     * @return string soap response xml
     */
    public function sefazEnviaCTe($xml)
    {
        $servico = 'CteRecepcao';
        $this->checkContingencyForWebServices($servico);
        if ($this->contingency->type != '') {
            //em modo de contingencia
            //esses xml deverão ser modificados e re-assinados e retornados
            $xml = $this->correctCTeForContingencyMode($xml);
        }
        $this->servico(
            $servico,
            $this->config->siglaUF,
            $this->tpAmb
        );
        $request = preg_replace("/<\?xml.*\?>/", "", $xml);
        $this->isValid($this->urlVersion, $request, 'cte');
        $this->lastRequest = $request;
        $gzdata = base64_encode(gzencode($request, 9));
        //montagem dos dados da mensagem SOAP
        $parameters = ['cteDadosMsg' => $gzdata];
        $body = "<cteDadosMsg xmlns=\"$this->urlNamespace\">$gzdata</cteDadosMsg>";
        $this->lastResponse = $this->sendRequest($body, $parameters);
        return $this->lastResponse;
    }

    /**
     * Envia CTe Simplificado
     * @param string $xml string do xml
     * @return string soap response xml
     */
    public function sefazEnviaCTeSimp($xml)
    {
        $servico = 'CteRecepcaoSimp';
        $this->checkContingencyForWebServices($servico);
        if ($this->contingency->type != '') {
            //em modo de contingencia
            //esses xml deverão ser modificados e re-assinados e retornados
            $xml = $this->correctCTeForContingencyMode($xml);
        }
        $this->servico(
            $servico,
            $this->config->siglaUF,
            $this->tpAmb
        );
        $request = preg_replace("/<\?xml.*\?>/", "", $xml);
        $this->isValid($this->urlVersion, $request, 'cteSimp');
        $this->lastRequest = $request;
        $gzdata = base64_encode(gzencode($request, 9));
        //montagem dos dados da mensagem SOAP
        $parameters = ['cteDadosMsg' => $gzdata];
        $body = "<cteDadosMsg xmlns=\"$this->urlNamespace\">$gzdata</cteDadosMsg>";
        $this->lastResponse = $this->sendRequest($body, $parameters);
        return $this->lastResponse;
    }

    /**
     * Request authorization to issue CTe OS with one document only
     * @param string $xml
     * @return string
     */
    public function sefazEnviaCTeOS($xml)
    {
        //carrega serviço
        $servico = 'CteRecepcao';
        $this->checkContingencyForWebServices($servico);
        $this->servico(
            $servico,
            $this->config->siglaUF,
            $this->tpAmb
        );
        $request = preg_replace("/<\?xml.*\?>/", "", $xml);
        $this->isValid($this->urlVersion, $request, 'cteOS');
        $this->lastRequest = $request;
        $gzdata = base64_encode(gzencode($request, 9));
        $parameters = ['cteDadosMsg' => $gzdata];
        $body = "<cteDadosMsg xmlns=\"$this->urlNamespace\">$gzdata</cteDadosMsg>";
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
     * Check services status SEFAZ/SVC
     * If $uf is empty use normal check with contingency
     * If $uf is NOT empty ignore contingency mode
     * @param string $uf initials of federation unit
     * @param int $tpAmb
     * @return string xml soap response
     */
    public function sefazStatus($uf = '', $tpAmb = null)
    {
        if (empty($tpAmb)) {
            $tpAmb = $this->tpAmb;
        }
        if (empty($uf)) {
            $uf = $this->config->siglaUF;
        }
        $servico = 'CteStatusServico';
        $this->checkContingencyForWebServices($servico);
        $this->servico(
            $servico,
            $uf,
            $tpAmb
        );
        $cUF = $this->getcUF($uf);
        $request = "<consStatServCTe xmlns=\"$this->urlPortal\" versao=\"$this->urlVersion\">"
            . "<tpAmb>$tpAmb</tpAmb>"
            . "<cUF>$cUF</cUF>"
            . "<xServ>STATUS</xServ></consStatServCTe>";
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
     * @param integer $ultNSU last NSU number recived
     * @param integer $numNSU NSU number you wish to consult
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
            $this->tpAmb,
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
            . "<tpAmb>" . $this->tpAmb . "</tpAmb>"
            . "<cUFAutor>$cUF</cUFAutor>"
            . ((strlen($this->config->cnpj) == 14) ?
                "<CNPJ>{$this->config->cnpj}</CNPJ>" :
                "<CPF>{$this->config->cnpj}</CPF>"
            )
            . $tagNSU . "</distDFeInt>";
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
     * @param string $chave
     * @param array $infCorrecao
     * @param int $nSeqEvento
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
     * @param string $chNFe
     * @param string $nProt
     * @param integer $tipo 1-primerio prazo, 2-segundo prazo
     * @param array $itens
     * @param integer $nSeqEvento
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
                . "</qtdeItem></itemPedido>";
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
     * @param string $chNFe
     * @param string $nProt
     * @param integer $nSeqEvento
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
     * @param string $chave key of CTe
     * @param string $xJust justificative 255 characters max
     * @param string $nProt protocol number
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
        $nSeqEvento = 1,
        $ufEvento = 'RS'
    ) {
        $tagAdic = '';
        if ($tpEvento == 610110) {
            $xJust = Strings::replaceSpecialsChars(substr(trim($xJust), 0, 255));
            $tagAdic = "<evPrestDesacordo>"
                . "<descEvento>Prestacao do Servico em Desacordo</descEvento>"
                . "<indDesacordoOper>1</indDesacordoOper>"
                . "<xObs>$xJust</xObs>"
                . "</evPrestDesacordo>";
        }
        if ($tpEvento == 610111) {
            $tagAdic = "<evCancPrestDesacordo>"
                . "<descEvento>Cancelamento Prestacao do Servico em Desacordo</descEvento>"
                . "<nProtEvPrestDes>$xJust</nProtEvPrestDes>"
                . "</evCancPrestDesacordo>";
        }
        return $this->sefazEvento(
            $ufEvento,
            $chNFe,
            $tpEvento,
            $nSeqEvento,
            $tagAdic
        );
    }

    /**
     * Request authorization for issuance in contingency EPEC
     * @param string $xml
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
        $xml = $this->correctCTeForContingencyMode($xml);
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
    private function sefazEvento(
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
        $dt = new \DateTime('now', new \DateTimeZone($this->timezone));
        $dhEvento = $dt->format('Y-m-d\TH:i:sP');
        $sSeqEvento = str_pad($nSeqEvento, 3, "0", STR_PAD_LEFT);
        $eventId = "ID" . $tpEvento . $chave . $sSeqEvento;
        $cOrgao = UFList::getCodeByUF($uf);
        $request = "<eventoCTe xmlns=\"$this->urlPortal\" versao=\"$this->urlVersion\">"
            . "<infEvento Id=\"$eventId\">"
            . "<cOrgao>$cOrgao</cOrgao>"
            . "<tpAmb>$this->tpAmb</tpAmb>";
        if (strlen($this->config->cnpj) == 14) {
            $request .= "<CNPJ>{$this->config->cnpj}</CNPJ>";
        } else {
            $request .= "<CPF>{$this->config->cnpj}</CPF>";
        }
        $request .= "<chCTe>$chave</chCTe>"
            . "<dhEvento>$dhEvento</dhEvento>"
            . "<tpEvento>$tpEvento</tpEvento>"
            . "<nSeqEvento>$sSeqEvento</nSeqEvento>"
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
     * @param string $chave
     * @return string
     */
    public function sefazDownload($chave)
    {
        //carrega serviço
        $servico = 'CTeDistribuicaoDFe';
        $this->checkContingencyForWebServices($servico);
        $this->servico(
            $servico,
            'AN',
            $this->tpAmb,
            true
        );
        //monta a consulta
        $consulta = "<consChNFe><chNFe>$chave</chNFe></consChNFe>"
            . ((strlen($this->config->cnpj) == 14) ?
                "<CNPJ>{$this->config->cnpj}</CNPJ>" :
                "<CPF>{$this->config->cnpj}</CPF>"
            )
            . "</distDFeInt>";
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
                . "<idCsc>" . $this->config->CSCid . "</idCsc>"
                . "<codigoCsc>" . $this->config->CSC . "</codigoCsc>"
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
     * Checks the validity of an CTe, normally used for received CTe
     * @param string $cte
     * @return boolean
     */
    public function sefazValidate($cte)
    {
        if (empty($cte)) {
            throw new InvalidArgumentException('Validacao CT-e: a string do CT-e esta vazio!');
        }
        //verifica a assinatura do CTe, exception caso de falha
        Signer::isSigned($cte, 'infCte');
        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->formatOutput = false;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($cte);
        //verifica a validade no webservice da SEFAZ
        $tpAmb = $dom->getElementsByTagName('tpAmb')->item(0)->nodeValue;
        $infCTe = $dom->getElementsByTagName('infCte')->item(0);
        $chCTe = preg_replace('/[^0-9]/', '', $infCTe->getAttribute("Id"));
        $protocol = $dom->getElementsByTagName('nProt')->item(0)->nodeValue;
        $digval = $dom->getElementsByTagName('DigestValue')->item(0)->nodeValue;
        //consulta o CTe
        $response = $this->sefazConsultaChave($chCTe, $tpAmb);
        $ret = new \DOMDocument('1.0', 'UTF-8');
        $ret->preserveWhiteSpace = false;
        $ret->formatOutput = false;
        $ret->loadXML($response);
        $retProt = $ret->getElementsByTagName('protCTe')->item(0);
        if (!isset($retProt)) {
            $xMotivo = $ret->getElementsByTagName('xMotivo')->item(0);
            if (isset($xMotivo)) {
                throw new InvalidArgumentException('Validacao CT-e: ' . $xMotivo->nodeValue);
            } else {
                throw new InvalidArgumentException('O documento de resposta nao contem o node "protCTe".');
            }
        }
        $infProt = $ret->getElementsByTagName('infProt')->item(0);
        $dig = $infProt->getElementsByTagName("digVal")->item(0);
        $digProt = '000';
        if (isset($dig)) {
            $digProt = $dig->nodeValue;
        }
        $chProt = $infProt->getElementsByTagName("chCTe")->item(0)->nodeValue;
        $nProt = $infProt->getElementsByTagName("nProt")->item(0)->nodeValue;
        if ($protocol == $nProt && $digval == $digProt && $chCTe == $chProt) {
            return true;
        }
        return false;
    }

    /**
     * Requires CE
     * @param string $chave key of CTe
     * @param string $nProt Protocolo do CTe
     * @param string $xNome Nome de quem recebeu a entrega
     * @param string $nDoc Documento de quem recebeu a entrega
     * @param string $hash Hash da Chave de acesso do CT-e + Imagem da assinatura no formato Base64
     * @param string|null $latitude Latitude do ponto da entrega
     * @param string|null $longitude Longitude do ponto da entrega
     * @param int $nSeqEvento No. sequencial do evento
     * @param string $dhEventoEntrega Data e hora da geração do hash da entrega
     * @param array $aNFes Chave das NFes entregues
     * @return string
     */
    public function sefazCE(
        $chave,
        $nProt,
        $xNome,
        $nDoc,
        $hash,
        $latitude,
        $longitude,
        $nSeqEvento,
        $dhEventoEntrega,
        $aNFes = []
    ) {
        $uf = $this->validKeyByUF($chave);
        $tpEvento = 110180;

        /* relaciona as chaves das NFes */
        $infEntrega = '';
        foreach ($aNFes as $NFe) {
            $infEntrega .= "<infEntrega>"
                . "<chNFe>$NFe</chNFe>"
                . "</infEntrega>";
        }

        $infLatitude = '';
        if ($latitude) {
            $infLatitude = "<latitude>$latitude</latitude>";
        }

        $infLongitude = '';
        if ($longitude) {
            $infLongitude = "<longitude>$longitude</longitude>";
        }

        $tagAdic = "<evCECTe>"
            . "<descEvento>Comprovante de Entrega do CT-e</descEvento>"
            . "<nProt>$nProt</nProt>"
            . "<dhEntrega>$dhEventoEntrega</dhEntrega>"
            . "<nDoc>$nDoc</nDoc>"
            . "<xNome>$xNome</xNome>"
            . $infLatitude
            . $infLongitude
            . "<hashEntrega>$hash</hashEntrega>"
            . "<dhHashEntrega>$dhEventoEntrega</dhHashEntrega>"
            . $infEntrega
            . "</evCECTe>";
        return $this->sefazEvento(
            $uf,
            $chave,
            $tpEvento,
            $nSeqEvento,
            $tagAdic
        );
    }

    /**
     * Requires CE cancellation
     * @param string $chave key of CTe
     * @param string $nProt protocolo do CTe
     * @param string $nProtCE protocolo do CE
     * @param int $nSeqEvento No. sequencial do evento
     * @return string
     */
    public function sefazCancelaCE($chave, $nProt, $nProtCE, $nSeqEvento)
    {
        $uf = $this->validKeyByUF($chave);
        $tpEvento = 110181;
        $tagAdic = "<evCancCECTe>"
            . "<descEvento>Cancelamento do Comprovante de Entrega do CT-e</descEvento>"
            . "<nProt>$nProt</nProt>"
            . "<nProtCE>$nProtCE</nProtCE>"
            . "</evCancCECTe>";
        return $this->sefazEvento(
            $uf,
            $chave,
            $tpEvento,
            $nSeqEvento,
            $tagAdic
        );
    }

    /**
     * Requires IE
     * @param string $chave key of CTe
     * @param string $nProt Protocolo do CTe
     * @param string $nTentativa Número da tentativa de entrega que não teve sucesso
     * @param string $tpMotivo Motivo do Insucesso
     * @param string|null $xJustMotivo Justificativa do Motivo de insucesso
     * @param string $hash Hash da Chave de acesso do CT-e + Imagem da assinatura no formato Base64
     * @param string|null $latitude Latitude do ponto da entrega
     * @param string|null $longitude Longitude do ponto da entrega
     * @param int $nSeqEvento No. sequencial do evento
     * @param string $dhTentativaEntrega Data e hora da geração do hash da tentativa de entrega
     * @param array $aNFes Chave das NFes entregues
     * @return string
     */
    public function sefazIE(
        $chave,
        $nProt,
        $nTentativa,
        $tpMotivo,
        $xJustMotivo,
        $hash,
        $latitude,
        $longitude,
        $nSeqEvento,
        $dhTentativaEntrega,
        $aNFes = []
    ) {
        $uf = $this->validKeyByUF($chave);
        $tpEvento = 110190;

        /* relaciona as chaves das NFes */
        $infEntrega = '';
        foreach ($aNFes as $NFe) {
            $infEntrega .= "<infEntrega>"
                . "<chNFe>$NFe</chNFe>"
                . "</infEntrega>";
        }

        $infLatitude = '';
        if ($latitude) {
            $infLatitude = "<latitude>$latitude</latitude>";
        }

        $infLongitude = '';
        if ($longitude) {
            $infLongitude = "<longitude>$longitude</longitude>";
        }

        $infJustMotivo = '';
        if ($xJustMotivo) {
            $infJustMotivo = "<xJustMotivo>$xJustMotivo</xJustMotivo>";
        }

        $tagAdic = "<evIECTe>"
            . "<descEvento>Insucesso na Entrega do CT-e</descEvento>"
            . "<nProt>$nProt</nProt>"
            . "<dhTentativaEntrega>$dhTentativaEntrega</dhTentativaEntrega>"
            . "<nTentativa>$nTentativa</nTentativa>"
            . "<tpMotivo>$tpMotivo</tpMotivo>"
            . $infJustMotivo
            . $infLatitude
            . $infLongitude
            . "<hashTentativaEntrega>$hash</hashTentativaEntrega>"
            . "<dhHashTentativaEntrega>$dhTentativaEntrega</dhHashTentativaEntrega>"
            . $infEntrega
            . "</evIECTe>";

        return $this->sefazEvento(
            $uf,
            $chave,
            $tpEvento,
            $nSeqEvento,
            $tagAdic
        );
    }

    /**
     * Requires IE cancellation
     * @param string $chave key of CTe
     * @param string $nProt protocolo do CTe
     * @param string $nProtIE protocolo do IE
     * @param int $nSeqEvento No. sequencial do evento
     * @return string
     */
    public function sefazCancelaIE($chave, $nProt, $nProtIE, $nSeqEvento)
    {
        $uf = $this->validKeyByUF($chave);
        $tpEvento = 110191;
        $tagAdic = "<evCancIECTe>"
            . "<descEvento>Cancelamento do Insucesso de Entrega do CT-e</descEvento>"
            . "<nProt>$nProt</nProt>"
            . "<nProtIE>$nProtIE</nProtIE>"
            . "</evCancIECTe>";
        return $this->sefazEvento(
            $uf,
            $chave,
            $tpEvento,
            $nSeqEvento,
            $tagAdic
        );
    }

    /**
     *
     * @param int $tpEvento
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
            case 110113:
                //EPEC
                //emissão em contingência EPEC
                $std->alias = 'EPEC';
                $std->desc = 'EPEC';
                break;
            case 110180:
                //comprovante de entrega
                $std->alias = 'evCECTe';
                $std->desc = 'Comprovante de Entrega';
                break;
            case 110181:
                //cancelamento do comprovante de entrega
                $std->alias = 'evCancCECTe';
                $std->desc = 'Cancelamento do Comprovante de Entrega';
                break;
            case 110190:
                //insucesso na entrega
                $std->alias = 'evIECTe';
                $std->desc = 'Insucesso na Entrega';
                break;
            case 610110:
                //Serviço em desacordo
                $std->alias = 'EvPrestDesacordo';
                $std->desc = 'Servico em desacordo';
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
