<?php

namespace NFePHP\CTe;

/**
 * Classe principal para a comunicação com a SEFAZ
 *
 * @category  Library
 * @package   nfephp-org/sped-cte
 * @copyright 2009-2016 NFePHP
 * @license   http://www.gnu.org/licenses/lesser.html LGPL v3
 * @link      http://github.com/nfephp-org/sped-cte for the canonical source repository
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
  */

use NFePHP\Common\Base\BaseTools;
use NFePHP\Common\LotNumber\LotNumber;
use NFePHP\Common\Strings\Strings;
use NFePHP\Common\Files;
use NFePHP\Common\Exception;
use NFePHP\CTe\Auxiliar\Response;
use NFePHP\CTe\Auxiliar\IdentifyCTe;

class Tools extends BaseTools
{
    /**
     * urlPortal
     * Instância do WebService
     * @var string
     */
    protected $urlPortal = 'http://www.portalfiscal.inf.br/cte';
    protected $modelo = '65';

    public function printCTe()
    {
    }

    public function mailCTe()
    {
    }

    /**
     * assina
     * @param string $xml
     * @param boolean $saveFile
     * @return string
     * @throws Exception\RuntimeException
     */
    public function assina($xml = '', $saveFile = false)
    {
        return $this->assinaDoc($xml, 'CTe', 'infCte', $saveFile);
    }

    public function sefazEnvia(
        $aXml,
        $tpAmb = '2',
        $idLote = '',
        &$aRetorno = array(),
        $indSinc = 0,
        $compactarZip = false
    ) {
        $this->modelo = '65';
        $sxml = $aXml;
        if (empty($aXml)) {
            $msg = "Pelo menos uma NFe deve ser informada.";
            throw new Exception\InvalidArgumentException($msg);
        }
        if (is_array($aXml)) {
            if (count($aXml) > 1) {
                //multiplas nfes, não pode ser sincrono
                $indSinc = 0;
            }
            $sxml = implode("", $sxml);
        }
        $sxml = preg_replace("/<\?xml.*\?>/", "", $sxml);
        $siglaUF = $this->aConfig['siglaUF'];
        $cUF = $this->getcUF($siglaUF);

        if ($tpAmb == '') {
            $tpAmb = $this->aConfig['tpAmb'];
        }
        if ($idLote == '') {
            $idLote = LotNumber::geraNumLote(15);
        }
        //carrega serviço
        $servico = 'CteRecepcao';
        $this->zLoadServico(
            'cte',
            $servico,
            $siglaUF,
            $tpAmb
        );

        if ($this->urlService == '') {
            $msg = "O envio de lote não está disponível na SEFAZ $siglaUF!!!";
            throw new Exception\RuntimeException($msg);
        }

        // Montagem do cabeçalho da comunicação SOAP
        $cabec = "<cteCabecMsg xmlns=\"$this->urlNamespace\">"
            . "<cUF>$cUF</cUF>"
            . "<versaoDados>$this->urlVersion</versaoDados>"
            . "</cteCabecMsg>";

        // Montagem dos dados da mensagem SOAP
        $dados = "<cteDadosMsg xmlns=\"$this->urlNamespace\">"
            . "<enviCTe xmlns=\"$this->urlPortal\" versao=\"$this->urlVersion\">"
            . "<idLote>$idLote</idLote>"
            . "$sxml"
            . "</enviCTe>"
            . "</cteDadosMsg>";

        // Envia dados via SOAP
        $retorno = $this->oSoap->send($this->urlService, $this->urlNamespace, $cabec, $dados, $this->urlMethod);

//        if ($compactarZip) {
//            $gzdata = base64_encode(gzencode($cons, 9, FORCE_GZIP));
//            $body = "<cteDadosMsgZip xmlns=\"$this->urlNamespace\">$gzdata</cteDadosMsgZip>";
//            $method = $this->urlMethod."Zip";
//        }


        $lastMsg = $this->oSoap->lastMsg;
        $this->soapDebug = $this->oSoap->soapDebug;
        //salva mensagens
        $filename = "$idLote-enviCTe.xml";
        $this->zGravaFile('cte', $tpAmb, $filename, $lastMsg);
        $filename = "$idLote-retEnviCTe.xml";
        $this->zGravaFile('cte', $tpAmb, $filename, $retorno);
        //tratar dados de retorno

        $aRetorno = Response::readReturnSefaz($servico, $retorno);
        //caso o envio seja recebido com sucesso mover a NFe da pasta
        //das assinadas para a pasta das enviadas
        return (string) $retorno;
    }

    public function sefazConsultaRecibo($recibo = '', $tpAmb = '2', &$aRetorno = array())
    {
        if ($recibo == '') {
            $msg = "Deve ser informado um recibo.";
            throw new Exception\InvalidArgumentException($msg);
        }
        if ($tpAmb == '') {
            $tpAmb = $this->aConfig['tpAmb'];
        }
        $siglaUF = $this->aConfig['siglaUF'];
        //carrega serviço
        $servico = 'CteRetRecepcao';
        $this->zLoadServico(
            'cte',
            $servico,
            $siglaUF,
            $tpAmb
        );
        if ($this->urlService == '') {
            $msg = "A consulta de NFe não está disponível na SEFAZ $siglaUF!!!";
            throw new Exception\RuntimeException($msg);
        }
        $cons = "<consReciCTe xmlns=\"$this->urlPortal\" versao=\"$this->urlVersion\">"
            . "<tpAmb>$tpAmb</tpAmb>"
            . "<nRec>$recibo</nRec>"
            . "</consReciCTe>";
        //validar mensagem com xsd
        //if (! $this->validarXml($cons)) {
        //    $msg = 'Falha na validação. '.$this->error;
        //    throw new Exception\RuntimeException($msg);
        //}
        //montagem dos dados da mensagem SOAP
        $body = "<cteDadosMsg xmlns=\"$this->urlNamespace\">$cons</cteDadosMsg>";
        //envia a solicitação via SOAP
        $retorno = $this->oSoap->send(
            $this->urlService,
            $this->urlNamespace,
            $this->urlHeader,
            $body,
            $this->urlMethod
        );
        $lastMsg = $this->oSoap->lastMsg;
        $this->soapDebug = $this->oSoap->soapDebug;
        //salva mensagens
        $filename = "$recibo-consReciCTe.xml";
        $this->zGravaFile('cte', $tpAmb, $filename, $lastMsg);
        $filename = "$recibo-retConsReciCTe.xml";
        $this->zGravaFile('cte', $tpAmb, $filename, $retorno);
        //tratar dados de retorno
        $aRetorno = Response::readReturnSefaz($servico, $retorno);
        //podem ser retornados nenhum, um ou vários protocolos
        //caso existam protocolos protocolar as NFe e movelas-las para a
        //pasta enviadas/aprovadas/anomes
        return (string) $retorno;
    }

    public function sefazConsultaChave($chave = '', $tpAmb = '2', &$aRetorno = array())
    {
        $chNFe = preg_replace('/[^0-9]/', '', $chave);
        if (strlen($chNFe) != 44) {
            $msg = "Uma chave de 44 dígitos da NFe deve ser passada.";
            throw new Exception\InvalidArgumentException($msg);
        }
        if ($tpAmb == '') {
            $tpAmb = $this->aConfig['tpAmb'];
        }
        $cUF = substr($chNFe, 0, 2);
        $siglaUF = $this->zGetSigla($cUF);
        //carrega serviço
        $servico = 'CteConsultaProtocolo';
        $this->zLoadServico(
            'cte',
            $servico,
            $siglaUF,
            $tpAmb
        );
        if ($this->urlService == '') {
            $msg = "A consulta de NFe não está disponível na SEFAZ $siglaUF!!!";
            throw new Exception\RuntimeException($msg);
        }
        $cons = "<consSitCTe xmlns=\"$this->urlPortal\" versao=\"$this->urlVersion\">"
            . "<tpAmb>$tpAmb</tpAmb>"
            . "<xServ>CONSULTAR</xServ>"
            . "<chCTe>$chNFe</chCTe>"
            . "</consSitCTe>";
        //validar mensagem com xsd
        //if (! $this->validarXml($cons)) {
        //    $msg = 'Falha na validação. '.$this->error;
        //    throw new Exception\RuntimeException($msg);
        //}
        //montagem dos dados da mensagem SOAP
        $body = "<cteDadosMsg xmlns=\"$this->urlNamespace\">$cons</cteDadosMsg>";
        //envia a solicitação via SOAP
        $retorno = $this->oSoap->send(
            $this->urlService,
            $this->urlNamespace,
            $this->urlHeader,
            $body,
            $this->urlMethod
        );
        $lastMsg = $this->oSoap->lastMsg;
        $this->soapDebug = $this->oSoap->soapDebug;
        //salva mensagens
        $filename = "$chNFe-consSitCTe.xml";
        $this->zGravaFile('cte', $tpAmb, $filename, $lastMsg);
        $filename = "$chNFe-retConsSitNFe.xml";
        $this->zGravaFile('cte', $tpAmb, $filename, $retorno);
        //tratar dados de retorno
        $aRetorno = Response::readReturnSefaz($servico, $retorno);
        return (string) $retorno;
    }

    public function sefazStatus($siglaUF = '', $tpAmb = '2', &$aRetorno = array())
    {
        if ($tpAmb == '') {
            $tpAmb = $this->aConfig['tpAmb'];
        }
        if ($siglaUF == '') {
            $siglaUF = $this->aConfig['siglaUF'];
        }
        //carrega serviço
        $servico = 'CteStatusServico';
        $this->zLoadServico(
            'cte',
            $servico,
            $siglaUF,
            $tpAmb
        );
        if ($this->urlService == '') {
            $msg = "O status não está disponível na SEFAZ $siglaUF!!!";
            throw new Exception\RuntimeException($msg);
        }
        $cons = "<consStatServCte xmlns=\"$this->urlPortal\" versao=\"$this->urlVersion\">"
            . "<tpAmb>$tpAmb</tpAmb>"
            . "<xServ>STATUS</xServ></consStatServCte>";
        //valida mensagem com xsd
        //validar mensagem com xsd
        //if (! $this->validarXml($cons)) {
        //    $msg = 'Falha na validação. '.$this->error;
        //    throw new Exception\RuntimeException($msg);
        //}
        //montagem dos dados da mensagem SOAP
        $body = "<cteDadosMsg xmlns=\"$this->urlNamespace\">$cons</cteDadosMsg>";
        //consome o webservice e verifica o retorno do SOAP
        $retorno = $this->oSoap->send(
            $this->urlService,
            $this->urlNamespace,
            $this->urlHeader,
            $body,
            $this->urlMethod
        );
        $lastMsg = $this->oSoap->lastMsg;
        $this->soapDebug = $this->oSoap->soapDebug;
        $datahora = date('Ymd_His');
        $filename = $siglaUF."_"."$datahora-consStatServCte.xml";
        $this->zGravaFile('cte', $tpAmb, $filename, $lastMsg);
        $filename = $siglaUF."_"."$datahora-retConsStatServCte.xml";
        $this->zGravaFile('cte', $tpAmb, $filename, $retorno);
        //tratar dados de retorno
        $aRetorno = Response::readReturnSefaz($servico, $retorno);
        return (string) $retorno;
    }

    public function sefazCancela($chCTe = '', $tpAmb = '2', $xJust = '', $nProt = '', &$aRetorno = array())
    {
        $chCTe = preg_replace('/[^0-9]/', '', $chCTe);
        $nProt = preg_replace('/[^0-9]/', '', $nProt);
        $xJust = Strings::cleanString($xJust);
        //validação dos dados de entrada
        if (strlen($chCTe) != 44) {
            $msg = "Uma chave de CTe válida não foi passada como parâmetro $chCTe.";
            throw new Exception\InvalidArgumentException($msg);
        }
        if ($nProt == '') {
            $msg = "Não foi passado o numero do protocolo!!";
            throw new Exception\InvalidArgumentException($msg);
        }
        if (strlen($xJust) < 15 || strlen($xJust) > 255) {
            $msg = "A justificativa deve ter pelo menos 15 digitos e no máximo 255!!";
            throw new Exception\InvalidArgumentException($msg);
        }
        $siglaUF = $this->zGetSigla(substr($chCTe, 0, 2));

        //estabelece o codigo do tipo de evento CANCELAMENTO
        $tpEvento = '110111';
        $descEvento = 'Cancelamento';
        $nSeqEvento = 1;
        //monta mensagem
        $tagAdic = "<evCancCTe>"
            . "<descEvento>$descEvento</descEvento>"
            . "<nProt>$nProt</nProt>"
            . "<xJust>$xJust</xJust>"
            . "</evCancCTe>";
        $retorno = $this->zSefazEvento($siglaUF, $chCTe, $tpAmb, $tpEvento, $nSeqEvento, $tagAdic);
        $aRetorno = $this->aLastRetEvent;
        return $retorno;
    }

    /**
     * zSefazEvento
     * @param string $siglaUF
     * @param string $chCTe
     * @param string $tpAmb
     * @param string $tpEvento
     * @param string $nSeqEvento
     * @param string $tagAdic
     * @return string
     * @throws Exception\RuntimeException
     * @internal function zLoadServico (Common\Base\BaseTools)
     */
    protected function zSefazEvento(
        $siglaUF = '',
        $chCTe = '',
        $tpAmb = '2',
        $tpEvento = '',
        $nSeqEvento = '1',
        $tagAdic = ''
    ) {
        if ($tpAmb == '') {
            $tpAmb = $this->aConfig['tpAmb'];
        }
        //carrega serviço
        $servico = 'CteRecepcaoEvento';
        $this->zLoadServico(
            'cte',
            $servico,
            $siglaUF,
            $tpAmb
        );
        if ($this->urlService == '') {
            $msg = "A recepção de eventos não está disponível na SEFAZ $siglaUF!!!";
            throw new Exception\RuntimeException($msg);
        }
        $aRet = $this->zTpEv($tpEvento);
        $aliasEvento = $aRet['alias'];
        $descEvento = $aRet['desc'];
        $cnpj = $this->aConfig['cnpj'];
        $dhEvento = (string) str_replace(' ', 'T', date('Y-m-d H:i:sP'));
        $sSeqEvento = str_pad($nSeqEvento, 2, "0", STR_PAD_LEFT);
        $eventId = "ID".$tpEvento.$chCTe.$sSeqEvento;
        $cOrgao = $this->urlcUF;
        if ($siglaUF == 'AN') {
            $cOrgao = '91';
        }
        $mensagem = "<infEvento Id=\"$eventId\">"
            . "<cOrgao>$cOrgao</cOrgao>"
            . "<tpAmb>$tpAmb</tpAmb>"
            . "<CNPJ>$cnpj</CNPJ>"
            . "<chCTe>$chCTe</chCTe>"
            . "<dhEvento>$dhEvento</dhEvento>"
            . "<tpEvento>$tpEvento</tpEvento>"
            . "<nSeqEvento>$sSeqEvento</nSeqEvento>"
            . "<detEvento versaoEvento=\"$this->urlVersion\">"
            . "$tagAdic"
            . "</detEvento>"
            . "</infEvento>";


        //assinatura dos dados
        //$signedMsg = $this->oCertificate->signXML($mensagem, 'infEvento');
        //$signedMsg = Strings::clearXml($signedMsg, true);
        $numLote = LotNumber::geraNumLote();


        $cons = "<eventoCTe xmlns=\"$this->urlPortal\" versao=\"$this->urlVersion\">"
            . "$mensagem"
            . "</eventoCTe>";


        $signedMsg = $this->oCertificate->signXML($cons, 'eventoCTe');
        $signedMsg = Strings::clearXml($signedMsg, true);




        //valida mensagem com xsd
        //no caso do evento nao tem xsd organizado, esta fragmentado
        //e por vezes incorreto por isso essa validação está desabilitada
        //if (! $this->zValidMessage($cons, 'nfe', 'envEvento', $version)) {
        //    $msg = 'Falha na validação. '.$this->error;
        //    throw new Exception\RuntimeException($msg);
        //}
        $body = "<cteDadosMsg xmlns=\"$this->urlNamespace\">$signedMsg</cteDadosMsg>";
        //envia a solicitação via SOAP
        $retorno = $this->oSoap->send(
            $this->urlService,
            $this->urlNamespace,
            $this->urlHeader,
            $body,
            $this->urlMethod
        );
        $lastMsg = $this->oSoap->lastMsg;
        $this->soapDebug = $this->oSoap->soapDebug;
        //salva mensagens
        $filename = "$chCTe-$aliasEvento-envEvento.xml";
        $this->zGravaFile('cte', $tpAmb, $filename, $lastMsg);
        $filename = "$chCTe-$aliasEvento-retEnvEvento.xml";
        $this->zGravaFile('cte', $tpAmb, $filename, $retorno);
        //tratar dados de retorno
        $this->aLastRetEvent = Response::readReturnSefaz($servico, $retorno);
        if ($this->aLastRetEvent['cStat'] == '128') {
            if ($this->aLastRetEvent['evento'][0]['cStat'] == '135' ||
                $this->aLastRetEvent['evento'][0]['cStat'] == '136' ||
                $this->aLastRetEvent['evento'][0]['cStat'] == '155'
            ) {
                $pasta = 'eventos'; //default
                if ($aliasEvento == 'CanCTe') {
                    $pasta = 'canceladas';
                    $filename = "$chCTe-$aliasEvento-procEvento.xml";
                } elseif ($aliasEvento == 'CCe') {
                    $pasta = 'cartacorrecao';
                    $filename = "$chCTe-$aliasEvento-$nSeqEvento-procEvento.xml";
                }
                $retorno = $this->zAddProtMsg('procEventoCTe', 'evento', $signedMsg, 'retEvento', $retorno);
                $this->zGravaFile('nfe', $tpAmb, $filename, $retorno, $pasta);
            }
        }
        return (string) $retorno;
    }

    /**
     * zTpEv
     * @param string $tpEvento
     * @return array
     * @throws Exception\RuntimeException
     */
    private function zTpEv($tpEvento = '')
    {
        //montagem dos dados da mensagem SOAP
        switch ($tpEvento) {
            case '110110':
                //CCe
                $aliasEvento = 'CCe';
                $descEvento = 'Carta de Correcao';
                break;
            case '110111':
                //cancelamento
                $aliasEvento = 'CancNFe';
                $descEvento = 'Cancelamento';
                break;
            case '110140':
                //EPEC
                //emissão em contingência EPEC
                $aliasEvento = 'EPEC';
                $descEvento = 'EPEC';
                break;
            case '111500':
            case '111501':
                //EPP
                //Pedido de prorrogação
                $aliasEvento = 'EPP';
                $descEvento = 'Pedido de Prorrogacao';
                break;
            case '111502':
            case '111503':
                //ECPP
                //Cancelamento do Pedido de prorrogação
                $aliasEvento = 'ECPP';
                $descEvento = 'Cancelamento de Pedido de Prorrogacao';
                break;
            case '210200':
                //Confirmacao da Operacao
                $aliasEvento = 'EvConfirma';
                $descEvento = 'Confirmacao da Operacao';
                break;
            case '210210':
                //Ciencia da Operacao
                $aliasEvento = 'EvCiencia';
                $descEvento = 'Ciencia da Operacao';
                break;
            case '210220':
                //Desconhecimento da Operacao
                $aliasEvento = 'EvDesconh';
                $descEvento = 'Desconhecimento da Operacao';
                break;
            case '210240':
                //Operacao não Realizada
                $aliasEvento = 'EvNaoRealizada';
                $descEvento = 'Operacao nao Realizada';
                break;
            default:
                $msg = "O código do tipo de evento informado não corresponde a "
                    . "nenhum evento estabelecido.";
                throw new Exception\RuntimeException($msg);
        }
        return array('alias' => $aliasEvento, 'desc' => $descEvento);
    }

    /**
     * validarXml
     * Valida qualquer xml do sistema NFe com seu xsd
     * NOTA: caso não exista um arquivo xsd apropriado retorna false
     * @param string $xml path ou conteudo do xml
     * @return boolean
     */
    public function validarXml($xml = '')
    {
        $aResp = array();
        $schem = IdentifyCTe::identificar($xml, $aResp);
        if ($schem == '') {
            return true;
        }
        $xsdFile = $aResp['Id'].'_v'.$aResp['versao'].'.xsd';
        $xsdPath = NFEPHP_ROOT.DIRECTORY_SEPARATOR .
            'schemes' .
            DIRECTORY_SEPARATOR .
            'CTe' .
            DIRECTORY_SEPARATOR .
            $this->aConfig['schemesCTe'] .
            DIRECTORY_SEPARATOR .
            $xsdFile;
        if (! is_file($xsdPath)) {
            $this->errors[] = "O arquivo XSD $xsdFile não foi localizado.";
            return false;
        }
        if (! ValidXsd::validar($aResp['xml'], $xsdPath)) {
            $this->errors[] = ValidXsd::$errors;
            return false;
        }
        return true;
    }
}
