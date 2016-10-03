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
 *
 *        CONTRIBUIDORES (em ordem alfabetica):
 *
 *          Maison K. Sakamoto <maison.sakamoto at gmail do com>
 */

use NFePHP\Common\Base\BaseTools;
use NFePHP\Common\LotNumber\LotNumber;
use NFePHP\Common\Strings\Strings;
use NFePHP\Common\Files;
use NFePHP\Common\Exception;
use NFePHP\CTe\Auxiliar\Response;
use NFePHP\CTe\Auxiliar\IdentifyCTe;
use NFePHP\Common\Dom\ValidXsd;

if (!defined('NFEPHP_ROOT')) {
    define('NFEPHP_ROOT', dirname(dirname(__FILE__)));
}

class Tools extends BaseTools
{
    /**
     * urlPortal
     * Instância do WebService
     * @var string
     */
    protected $urlPortal = 'http://www.portalfiscal.inf.br/cte';

    /**
     * errrors
     * @var string
     */
    public $erros = array();
    
    protected $modelo = '57';
    
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
        return $this->assinaDoc($xml, 'cte', 'infCte', $saveFile);
    }

    public function sefazEnvia(
        $aXml,
        $tpAmb = '2',
        $idLote = '',
        &$aRetorno = array(),
        $indSinc = 0,
        $compactarZip = false
    ) {
        $sxml = $aXml;
        if (empty($aXml)) {
            $msg = "Pelo menos uma NFe deve ser informada.";
            throw new Exception\InvalidArgumentException($msg);
        }
        if (is_array($aXml)) {
            if (count($aXml) > 1) {
                //multiplas cte, não pode ser sincrono
                $indSinc = 0;
            }
            $sxml = implode("", $sxml);
        }
        $sxml = preg_replace("/<\?xml.*\?>/", "", $sxml);
        $siglaUF = $this->aConfig['siglaUF'];
        
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
        
        // Montagem dos dados da mensagem SOAP
        $dados = "<cteDadosMsg xmlns=\"$this->urlNamespace\">"
            . "<enviCTe xmlns=\"$this->urlPortal\" versao=\"$this->urlVersion\">"
            . "<idLote>$idLote</idLote>"
            . "$sxml"
            . "</enviCTe>"
            . "</cteDadosMsg>";
        
        // Envia dados via SOAP
        $retorno = $this->oSoap->send(
            $this->urlService,
            $this->urlNamespace,
            $this->urlHeader,
            $dados,
            $this->urlMethod
        );

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
        $dhEvento = (string) str_replace(' ', 'T', date('Y-m-d H:i:s'));
//        $dhEvento = (string) str_replace(' ', 'T', date('Y-m-d H:i:sP'));
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
            . "<nSeqEvento>$nSeqEvento</nSeqEvento>"
            //. "<nSeqEvento>$sSeqEvento</nSeqEvento>"
            . "<detEvento versaoEvento=\"$this->urlVersion\">"
            . "$tagAdic"
            . "</detEvento>"
            . "</infEvento>";

        $cons = "<eventoCTe xmlns=\"$this->urlPortal\" versao=\"$this->urlVersion\">"
            . "$mensagem"
            . "</eventoCTe>";

        $signedMsg = $this->oCertificate->signXML($cons, 'infEvento');
        $signedMsg = preg_replace("/<\?xml.*\?>/", "", $signedMsg);

        //$signedMsg = Strings::clearXml($signedMsg, true);

//        if (! $this->zValidMessage($signedMsg, 'cte', 'envEvento', $this->urlVersion)) {
//            $msg = 'Falha na validação. '.$this->error;
//            throw new Exception\RuntimeException($msg);
//        }


//        $filename = "../cancelamento.xml";
//        $xml = file_get_contents($filename);


        //$body = "<cteDadosMsg xmlns=\"$this->urlNamespace\">$xml</cteDadosMsg>";
        $body = "<cteDadosMsg xmlns=\"$this->urlNamespace\">$signedMsg</cteDadosMsg>";

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
                $this->zGravaFile('cte', $tpAmb, $filename, $retorno, $pasta);
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
            'schemas' .
            DIRECTORY_SEPARATOR .
            $this->aConfig['schemasCTe'] .
            DIRECTORY_SEPARATOR .
            $xsdFile;
        if (! is_file($xsdPath)) {
            $this->erros[] = "O arquivo XSD $xsdFile não foi localizado.";
            return false;
        }
        if (! ValidXsd::validar($aResp['xml'], $xsdPath)) {
            $this->erros[] = ValidXsd::$errors;
            return false;
        }
        return true;
    }
    
    public function sefazInutiliza(
        $nAno = '',
        $nSerie = '1',
        $nIni = '',
        $nFin = '',
        $xJust = '',
        $tpAmb = '2',
        &$aRetorno = array()
    ) {
        // Variavel de retorno do metodo
        $aRetorno = array (
            'bStat' => false,
            'cStat' => '',
            'xMotivo' => '',
            'dhRecbto' => '',
            'nProt' => '');
        // Valida dos dados de entrada
        if ($nAno == '' || $nIni == '' || $nFin == '' || $xJust == '') {
            $this->errStatus = true;
            $this->errMsg = "Não foi passado algum dos parametos necessários "
                    . "ANO=$nAno inicio=$nIni fim=$nFin justificativa=$xJust.";
            return false;
        }
        
        $siglaUF = $this->aConfig['siglaUF'];
        $cnpj = $this->aConfig['cnpj'];
        $this->urlOperation = 'CteInutilizacao';
        
        $this->zLoadServico('cte', 'CteInutilizacao', $siglaUF, $tpAmb);
        
        
        if ($this->urlService == '') {
            $msg = "A recepção de eventos não está disponível na SEFAZ $siglaUF!!!";
            throw new Exception\RuntimeException($msg);
        }
        if (strlen($nAno) != 2) {
            $msg = "Informe o ano com 2 digitos";
            throw new Exception\InvalidArgumentException($msg);
        }
        if (strlen($nSerie) == 0 || strlen($nSerie) > 3) {
            $msg = "O campo serie está errado: $nSerie. usar 3 digitos";
            throw new Exception\InvalidArgumentException($msg);
        }
        if (strlen($nIni) < 1 || strlen($nIni) > 9) {
            $msg = "O campo numero inicial está errado: $nIni. Corrija e refaça o processo!!";
            throw new Exception\InvalidArgumentException($msg);
        }
        if (strlen($nFin) < 1 || strlen($nFin) > 9) {
            $msg = "O campo numero final está errado: $nFin. Corrija e refaça o processo!!";
            throw new Exception\InvalidArgumentException($msg);
        }
        if (strlen($xJust) < 15 || strlen($xJust) > 255) {
            $msg = "O campo justificativa deve ter no minimo 15 chars e no maximo 255, verifique!";
            throw new Exception\InvalidArgumentException($msg);
        }
        
        // Identificador da TAG a ser assinada formada com Código da UF +
        // Ano (2 posições) + CNPJ + modelo + série + nro inicial e nro final
        // precedida do literal “ID”
        // 43 posições
        //     2      4       6       20      22    25       34      43
        //     2      2       2       14       2     3        9       9
        $id = 'ID' . $this->urlcUF .  $cnpj . '57' .
                str_pad($nSerie, 3, '0', STR_PAD_LEFT) .
                str_pad($nIni, 9, '0', STR_PAD_LEFT) .
                str_pad($nFin, 9, '0', STR_PAD_LEFT);
        
        // Montagem do corpo da mensagem
        $dXML = '<inutCTe xmlns="' . $this->urlPortal . '" versao="' . $this->urlVersion . '">';
        $dXML .= '<infInut Id="' . $id . '">';
        $dXML .= '<tpAmb>' . $tpAmb . '</tpAmb>';
        $dXML .= '<xServ>INUTILIZAR</xServ>';
        $dXML .= '<cUF>' . $this->urlcUF . '</cUF>';
        $dXML .= '<ano>' . $nAno . '</ano>';
        $dXML .= '<CNPJ>' . $cnpj . '</CNPJ>';
        $dXML .= '<mod>57</mod>';
        $dXML .= '<serie>' . $nSerie . '</serie>';
        $dXML .= '<nCTIni>' . $nIni . '</nCTIni>';
        $dXML .= '<nCTFin>' . $nFin . '</nCTFin>';
        $dXML .= '<xJust>' . $xJust . '</xJust>';
        $dXML .= '</infInut>';
        $dXML .= '</inutCTe>';
        
        // Assina a lsolicitação de inutilização
        $dXML = $this->oCertificate->signXML($dXML, 'infInut');
        $dados = '<cteDadosMsg xmlns="' . $this->urlNamespace . '">' . $dXML . '</cteDadosMsg>';
    
        // Remove as tags xml que porventura tenham sido inclusas
        $dados = str_replace('<?xml version="1.0"?>', '', $dados);
        $dados = str_replace('<?xml version="1.0" encoding="utf-8"?>', '', $dados);
        $dados = str_replace('<?xml version="1.0" encoding="UTF-8"?>', '', $dados);
        $dados = str_replace(array("\r","\n","\s"), "", $dados);
        
        // Envia a solicitação via SOAP
        $retorno = $this->oSoap->send(
            $this->urlService,
            $this->urlNamespace,
            $this->urlHeader,
            $dados,
            $this->urlMethod
        );
        // Verifica o retorno
        $lastMsg = $this->oSoap->lastMsg;
        $this->soapDebug = $this->oSoap->soapDebug;
        $aRetorno = Response::readReturnSefaz($this->urlOperation, $retorno);
        return (string) $retorno;
    }
        
    /**
     * Maison K. Sakamoto
     * Sobrecarga de metodos da Classe BaseTools.php,
     * NOTA: necessario pois por hora não é possível fazer o commit do projeto
     * diretamente na Classe BaseTools.php
     * @param string $tipo
     * @param string $service
     * @param string $siglaUF
     * @param string $tpAmb
     * @return boolean
     */
    protected function zLoadServico(
        $tipo,
        $service,
        $siglaUF,
        $tpAmb
    ) {
        if (empty($tipo) || empty($service) || empty($siglaUF)) {
            $this->urlVersion = '';
            $this->urlService = '';
            $this->urlMethod = '';
            $this->urlOperation = '';
            $this->urlNamespace = '';
            $this->urlHeader = '';
            return false;
        }
        
        $this->urlcUF = $this->getcUF($siglaUF);
        $pathXmlUrlFile = $this->zGetXmlUrlPath($tipo);
        
        if ($this->enableSVCAN) {
            $aURL = self::zLoadSEFAZ($pathXmlUrlFile, $tpAmb, 'SVCAN');
        } elseif ($this->enableSVCRS) {
            $aURL = self::zLoadSEFAZ($pathXmlUrlFile, $tpAmb, 'SVCRS');
        } else {
            $aURL = self::zLoadSEFAZ($pathXmlUrlFile, $tpAmb, $siglaUF, $tipo);
        }
        
        //recuperação da versão
        $this->urlVersion = $aURL[$service]['version'];
        //recuperação da url do serviço
        $this->urlService = $aURL[$service]['URL'];
        //recuperação do método
        $this->urlMethod = $aURL[$service]['method'];
        //recuperação do operation
        $this->urlOperation = $aURL[$service]['operation'];
        //montagem do namespace do serviço
        $this->urlNamespace = sprintf("%s/wsdl/%s", $this->urlPortal, $this->urlOperation);
                
        //montagem do cabeçalho da comunicação SOAP
        $this->urlHeader = $this->zMountHeader($tipo, $this->urlNamespace, $this->urlcUF, $this->urlVersion);
       
        return true;
    }
    
    /**
     * Maison K. Sakamoto
     * Sobrecarga de metodos da Classe BaseTools.php,
     * NOTA: necessario pois por hora não é possível fazer o commit do projeto
     * diretamente na Classe BaseTools.php
     *
     * @param type $tipo
     * @return string
     */
    protected function zGetXmlUrlPath($tipo)
    {
        $path = '';
        if ($tipo == 'nfe') {
            $path = $this->aConfig['pathXmlUrlFileNFe'];
            if ($this->modelo == '65') {
                $path = str_replace('55', '65', $path);
            } else {
                $path = str_replace('65', '55', $path);
            }
        } elseif ($tipo == 'cte') {
            $path = $this->aConfig['pathXmlUrlFileCTe'];
        } elseif ($tipo == 'mdfe') {
            $path = $this->aConfig['pathXmlUrlFileMDFe'];
        } elseif ($tipo == 'cle') {
            $path = $this->aConfig['pathXmlUrlFileCLe'];
        }
        
        $pathXmlUrlFile = NFEPHP_ROOT
            . DIRECTORY_SEPARATOR
            . 'config'
            . DIRECTORY_SEPARATOR
            . $path;
        
        return $pathXmlUrlFile;
    }
    
    /**
     * Maison K. Sakamoto
     * Sobrecarga de metodos da Classe BaseTools.php,
     * NOTA: necessario pois por hora não é possível fazer o commit do projeto
     * diretamente na Classe BaseTools.php
     *
     * @param type $tipo
     * @param type $namespace
     * @param type $cUF
     * @param type $version
     * @return string
     */
    protected function zMountHeader($tipo, $namespace, $cUF, $version)
    {
        $header = '';
        if ($tipo == 'nfe') {
            $header = "<nfeCabecMsg "
                . "xmlns=\"$namespace\">"
                . "<cUF>$cUF</cUF>"
                . "<versaoDados>$version</versaoDados>"
                . "</nfeCabecMsg>";
        } elseif ($tipo == 'cte') {
            $header = "<cteCabecMsg "
                . "xmlns=\"$namespace\">"
                . "<cUF>$cUF</cUF>"
                . "<versaoDados>$version</versaoDados>"
                . "</cteCabecMsg>";
        } elseif ($tipo == 'mdfe') {
            $header = "<mdfeCabecMsg "
                . "xmlns=\"$namespace\">"
                . "<cUF>$cUF</cUF>"
                . "<versaoDados>$version</versaoDados>"
                . "</mdfeCabecMsg>";
        }
        return $header;
    }
    
    /**
     * Maison K. Sakamoto
     * Sobrecarga de metodos da Classe BaseTools.php,
     * NOTA: necessario pois por hora não é possível fazer o commit do projeto
     * diretamente na Classe BaseTools.php
     *
     * @param type $pathXmlUrlFile
     * @param type $tpAmb
     * @param type $siglaUF
     * @param type $tipo
     * @return type
     * @throws Exception\RuntimeException
     */
    protected function zLoadSEFAZ($pathXmlUrlFile = '', $tpAmb = '2', $siglaUF = 'SP', $tipo = 'nfe')
    {
        //verifica se o arquivo xml pode ser encontrado no caminho indicado
        if (! file_exists($pathXmlUrlFile)) {
            throw new Exception\RuntimeException(
                "Arquivo $pathXmlUrlFile não encontrado."
            );
        }
        //carrega o xml
        if (!$xmlWS = simplexml_load_file($pathXmlUrlFile)) {
            throw new Exception\RuntimeException(
                "Arquivo $pathXmlUrlFile parece ser invalido ou está corrompido."
            );
        }
        $autorizadores = array();
        $autorizadores['65'] = array(
            'AC'=>'SVRS',
            'AL'=>'SVRS',
            'AM'=>'AM',
            'AN'=>'AN',
            'AP'=>'SVRS',
            'BA'=>'SVRS',
            'CE'=>'CE',
            'DF'=>'SVRS',
            'ES'=>'SVRS',
            'GO'=>'SVRS',
            'MA'=>'SVRS',
            'MG'=>'MG',
            'MS'=>'MS',
            'MT'=>'MT',
            'PA'=>'SVRS',
            'PB'=>'SVRS',
            'PE'=>'PE',
            'PI'=>'SVRS',
            'PR'=>'PR',
            'RJ'=>'SVRS',
            'RN'=>'SVRS',
            'RO'=>'SVRS',
            'RR'=>'SVRS',
            'RS'=>'RS',
            'SC'=>'SVRS',
            'SE'=>'SVRS',
            'SP'=>'SP',
            'TO'=>'SVRS',
            'SVAN'=>'SVAN',
            'SVRS'=>'SVRS',
            'SVCAN'=>'SVCAN',
        );
        
        $autorizadores['55'] = array(
            'AC'=>'SVRS',
            'AL'=>'SVRS',
            'AM'=>'AM',
            'AN'=>'AN',
            'AP'=>'SVRS',
            'BA'=>'BA',
            'CE'=>'CE',
            'DF'=>'SVRS',
            'ES'=>'SVRS',
            'GO'=>'GO',
            'MA'=>'SVAN',
            'MG'=>'MG',
            'MS'=>'MS',
            'MT'=>'MT',
            'PA'=>'SVAN',
            'PB'=>'SVRS',
            'PE'=>'PE',
            'PI'=>'SVAN',
            'PR'=>'PR',
            'RJ'=>'SVRS',
            'RN'=>'SVRS',
            'RO'=>'SVRS',
            'RR'=>'SVRS',
            'RS'=>'RS',
            'SC'=>'SVRS',
            'SE'=>'SVRS',
            'SP'=>'SP',
            'TO'=>'SVRS',
            'SVAN'=>'SVAN',
            'SVRS'=>'SVRS',
            'SVCAN'=>'SVCAN',
            'SVCRS'=>'SVCRS'
        );
        
        //Estados que utilizam a SVSP - Sefaz Virtual de São Paulo: AP, PE, RR
        //Estados que utilizam a SVRS - Sefaz Virtual do RS:
        //AC, AL, AM, BA, CE, DF, ES, GO, MA,
        //PA, PB, PI, RJ, RN, RO, SC, SE, TO
        //Autorizadores: MT - MS - MG - PR - RS - SP - SVRS - SVSP
        $autorizadores['57'] = array(
            'AC'=>'SVRS',
            'AL'=>'SVRS',
            'AM'=>'SVRS',
            'AN'=>'AN',
            'AP'=>'SVSP',
            'BA'=>'SVRS',
            'CE'=>'SVRS',
            'DF'=>'SVRS',
            'ES'=>'SVRS',
            'GO'=>'SVRS',
            'MA'=>'SVRS',
            'MG'=>'MG',
            'MS'=>'MS',
            'MT'=>'MT',
            'PA'=>'SVSP',
            'PB'=>'SVRS',
            'PE'=>'PE',
            'PI'=>'SVRS',
            'PR'=>'PR',
            'RJ'=>'SVRS',
            'RN'=>'SVRS',
            'RO'=>'SVRS',
            'RR'=>'SVSP',
            'RS'=>'RS',
            'SC'=>'SVRS',
            'SE'=>'SVRS',
            'SP'=>'SP',
            'TO'=>'SVRS',
            'SVAN'=>'SVAN',
            'SVRS'=>'SVRS',
            'SVCAN'=>'SVCAN',
        );
        //variável de retorno do método
        $aUrl = array();
        //testa parametro tpAmb
        $sAmbiente = 'homologacao';
        if ($tpAmb == '1') {
            $sAmbiente = 'producao';
        }
        $alias = $autorizadores[$this->modelo][$siglaUF];
        if ($tipo == 'mdfe') {
            $alias = 'RS';
        }
        //estabelece a expressão xpath de busca
        $xpathExpression = "/WS/UF[sigla='$alias']/$sAmbiente";
        $aUrl = $this->zExtractUrl($xmlWS, $aUrl, $xpathExpression);
        //verifica se existem outros serviços exclusivos para esse estado
        if ($alias == 'SVAN' || $alias == 'SVRS') {
            $xpathExpression = "/WS/UF[sigla='$siglaUF']/$sAmbiente";
            $aUrl = $this->zExtractUrl($xmlWS, $aUrl, $xpathExpression);
        }
        
        return $aUrl;
    }
}
