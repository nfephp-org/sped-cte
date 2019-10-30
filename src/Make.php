<?php

namespace NFePHP\CTe;

/**
 *
 * @category  Library
 * @package   nfephp-org/sped-cte
 * @copyright 2009-2016 NFePHP
 * @name      Make.php
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL v.3
 * @link      http://github.com/nfephp-org/sped-cte for the canonical source repository
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 */

use DOMElement;
use NFePHP\Common\DOMImproved as Dom;
use NFePHP\Common\Keys;
use NFePHP\Common\Strings;
use RuntimeException;
use stdClass;

class Make
{
    /**
     * @var array
     */
    public $errors = [];

    /**
     * versao
     * numero da versão do xml da CTe
     * @var string
     */
    public $versao = '3.00';
    /**
     * mod
     * modelo da CTe 57
     * @var integer
     */
    public $mod = 57;
    /**
     * chave da MDFe
     * @var string
     */
    public $chCTe = '';
    /**
     * xml
     * String com o xml do documento fiscal montado
     * @var string
     */
    public $xml = '';
    /**
     * dom
     * Variável onde será montado o xml do documento fiscal
     * @var \NFePHP\Common\Dom\Dom
     */
    public $dom;
    /**
     * tpAmb
     * tipo de ambiente
     * @var string
     */
    public $tpAmb = '2';
    /**
     * Modal do Cte
     * @var integer
     */
    private $modal = 0;
    /**
     * Tag CTe
     * @var \DOMNode
     */
    private $CTe = '';
    /**
     * Informações do CT-e
     * @var \DOMNode
     */
    private $infCte = '';
    /**
     * Identificação do CT-e
     * @var \DOMNode
     */
    private $ide = '';
    /**
     * Percurso do CT-e OS
     * @var \DOMNode
     */
    private $infPercurso = [];
    /**
     * Tipo do Serviço
     * @var integer
     */
    private $tpServ = 0;
    /**
     * Indicador do "papel" do tomador do serviço no CT-e
     * @var \DOMNode
     */
    private $toma3 = '';
    /**
     * Indicador do "papel" do tomador do serviço no CT-e
     * @var \DOMNode
     */
    private $toma4 = '';
    /**
     * Indicador do "papel" do tomador do serviço no CT-e OS
     * @var \DOMNode
     */
    private $toma = '';
    /**
     * Dados do endereço
     * @var \DOMNode
     */
    private $enderToma = '';
    /**
     * Dados complementares do CT-e para fins operacionais ou comerciais
     * @var \DOMNode
     */
    private $compl = '';
    /**
     * Previsão do fluxo da carga
     * @var \DOMNode
     */
    private $fluxo = '';
    /**
     * Passagem
     * @var array
     */
    private $pass = array();
    /**
     * Informações ref. a previsão de entrega
     * @var \DOMNode
     */
    private $entrega = '';
    /**
     * Entrega sem data definida
     * @var \DOMNode
     */
    private $semData = '';
    /**
     * Entrega com data definida
     * @var \DOMNode
     */
    private $comData = '';
    /**
     * Entrega no período definido
     * @var \DOMNode
     */
    private $noPeriodo = '';
    /**
     * Entrega sem hora definida
     * @var \DOMNode
     */
    private $semHora = '';
    /**
     * Entrega com hora definida
     * @var \DOMNode
     */
    private $comHora = '';
    /**
     * Entrega no intervalo de horário definido
     * @var \DOMNode
     */
    private $noInter = '';
    /**
     * Campo de uso livre do contribuinte
     * @var array
     */
    private $obsCont = array();
    /**
     * Campo de uso livre do contribuinte
     * @var array
     */
    private $obsFisco = array();
    /**
     * Identificação do Emitente do CT-e
     * @var \DOMNode
     */
    private $emit = '';
    /**
     * Endereço do emitente
     * @var \DOMNode
     */
    private $enderEmit = '';
    /**
     * Informações do Remetente das mercadorias transportadas pelo CT-e
     * @var \DOMNode
     */
    private $rem = '';
    /**
     * Dados do endereço
     * @var \DOMNode
     */
    private $enderReme = '';
    /**
     * Informações do Expedidor da Carga
     * @var \DOMNode
     */
    private $exped = '';
    /**
     * Dados do endereço
     * @var \DOMNode
     */
    private $enderExped = '';
    /**
     * Informações do Recebedor da Carga
     * @var \DOMNode
     */
    private $receb = '';
    /**
     * Dados do endereço
     * @var \DOMNode
     */
    private $enderReceb = '';
    /**
     * Informações do Destinatário do CT-e
     * @var \DOMNode
     */
    private $dest = '';
    /**
     * Dados do endereço
     * @var \DOMNode
     */
    private $enderDest = '';
    /**
     * Valores da Prestação de Serviço
     * @var \DOMNode
     */
    private $vPrest = '';
    /**
     * Componentes do Valor da Prestação
     * @var array
     */
    private $comp = array();
    /**
     * Informações relativas aos Impostos
     * @var \DOMNode
     */
    private $imp = '';
    /**
     * Observações adicionais da CT-e
     * @var string
     */
    private $xObs = '';
    /**
     * Grupo de informações do CT-e Normal e Substituto
     * @var \DOMNode
     */
    private $infCTeNorm = '';
    /**
     * Informações da Carga do CT-e
     * @var \DOMNode
     */
    private $infCarga = '';
    /**
     * Informações da Prestação do Serviço
     * @var \DOMNode
     */
    private $infServico = '';
    /**
     * Informações de quantidades da Carga do CT-e
     * @var \DOMNode
     */
    private $infQ = array();
    /**
     * Informações dos documentos transportados pelo CT-e Opcional para Redespacho Intermediario
     * e Serviço vinculado a multimodal.
     * @var \DOMNode
     */
    private $infDoc = array();
    /**
     * Informações das NF
     * @var array
     */
    private $infNF = array();
    /**
     * Informações das NF-e
     * @var array
     */
    private $infNFe = array();
    /**
     * Informações dos demais documentos
     * @var array
     */
    private $infOutros = array();
    /**
     * Informações dos demais documentos
     * @var array
     */
    private $infDocRef = array();
    /**
     * Informações das Unidades de Transporte (Carreta/Reboque/Vagão)
     * @var array
     */
    private $infUnidTransp = array();
    /**
     * Lacres das Unidades de Transporte
     * @var array
     */
    private $lacUnidTransp = array();
    /**
     * Informações das Unidades de Carga (Containeres/ULD/Outros)
     * @var array
     */
    private $infUnidCarga = array();
    /**
     * Lacres das Unidades de Carga
     * @var array
     */
    private $lacUnidCarga = array();
    /**
     * Documentos de Transporte Anterior
     * @var \DOMNode
     */
    private $docAnt = array();
    /**
     * Emissor do documento anterior
     * @var array
     */
    private $emiDocAnt = array();
    /**
     * Informações de identificação dos documentos de Transporte Anterior
     * @var array
     */
    private $idDocAnt = array();
    /**
     * Documentos de transporte anterior em papel
     * @var array
     */
    private $idDocAntPap = array();
    /**
     * Documentos de transporte anterior eletrônicos
     * @var array
     */
    private $idDocAntEle = array();
    /**
     * Informações de Seguro da Carga
     * @var array
     */
    private $seg = array();
    /**
     * Informações do modal
     * @var \DOMNode
     */
    private $infModal = '';
    /**
     * Preenchido quando for transporte de produtos classificados pela ONU como perigosos.
     * @var array
     */
    private $peri = array();
    /**
     * informações dos veículos transportados
     * @var array
     */
    private $veicNovos = array();
    /**
     * Dados da cobrança do CT-e
     * @var \DOMNode
     */
    private $cobr = '';
    /**
     * Dados da fatura
     * @var \DOMNode
     */
    private $fat = '';
    /**
     * Dados das duplicatas
     * @var array
     */
    private $dup = array();
    /**
     * Informações do CT-e de substituição
     * @var \DOMNode
     */
    private $infCteSub = '';
    /**
     * Tomador é contribuinte do ICMS
     * @var \DOMNode
     */
    private $tomaICMS = '';
    /**
     * Informação da NFe emitida pelo Tomador
     * @var \DOMNode
     */
    private $refNFe = '';
    /**
     * Informação da NF ou CT emitido pelo Tomador
     * @var \DOMNode
     */
    private $refNF = '';
    /**
     * Informação da NF ou CT emitido pelo Tomador
     * @var \DOMNode
     */
    private $infCteComp = '';
    /**
     * Detalhamento do CT-e do tipo Anulação
     * @var \DOMNode
     */
    private $infCteAnu = '';
    /**
     * Informações do modal Rodoviário
     * @var \DOMNode
     */
    private $rodo = '';
    /**
     * Informações do modal Aéreo
     * @var \DOMNode
     */
    private $aereo = '';
    /**
     * Informações do modal Aquaviario
     * @var \DOMNode
     */
    private $aquav = '';
    /**
     * Informações do modal Dutoviário
     * @var \DOMNode
     */
    private $duto = '';
    /**
     * Informações do modal Aéreo -> Dados da Carga
     * @var \DOMNode
     */
    private $natCarga = '';
    /**
     * Informações do modal Aéreo -> Tarifas
     * @var \DOMNode
     */
    private $tarifa = '';
    /**
     * Ordens de Coleta associados
     * @var array
     */
    private $occ = array();
    /**
     * @var \DOMNode
     */
    private $emiOcc = array();
    /**
     * Informações de Vale Pedágio
     * @var array
     */
    private $valePed = array();
    /**
     * Dados dos Veículos
     * @var array
     */
    private $veic = array();
    /**
     * Proprietários do Veículo. Só preenchido quando o veículo não pertencer à empresa emitente do CT-e
     * @var array
     */
    private $prop = array();
    /**
     * Autorizados para download do XML do DF-e
     * @var array
     */
    private $autXML = array();
    /**
     * Dados do Fretamento - CTe-OS
     * @var
     */
    private $infFretamento;
    /**
     * @var DOMElement
     */
    protected $infRespTec;
    /**
     * @var boolean
     */
    protected $replaceAccentedChars = false;

    public function __construct()
    {
        $this->dom = new Dom('1.0', 'UTF-8');
        $this->dom->preserveWhiteSpace = false;
        $this->dom->formatOutput = false;
    }

    /**
     * Returns xml string and assembly it is necessary
     * @return string
     */
    public function getXML()
    {
        if (empty($this->xml)) {
            $this->montaCTe();
        }
        return $this->xml;
    }

    /**
     * Retorns the key number of NFe (44 digits)
     * @return string
     */
    public function getChave()
    {
        return $this->chCTe;
    }

    /**
     * Returns the model of CTe 57 or 67
     * @return int
     */
    public function getModelo()
    {
        return $this->mod;
    }

    /**
     * Set character convertion to ASCII only ou not
     * @param bool $option
     */
    public function setOnlyAscii($option = false)
    {
        $this->replaceAccentedChars = $option;
    }

    /**
     * Call method of xml assembly. For compatibility only.
     * @return boolean
     */
    public function montaCTe()
    {
        return $this->monta();
    }

    /**
     * Monta o arquivo XML usando as tag's já preenchidas
     *
     * @return bool
     */
    public function monta()
    {
        $this->errors = $this->dom->errors;
        if (count($this->errors) > 0) {
            return false;
        }
        if ($this->mod == 57) {
            $this->buildCTe();
        } else {
            return $this->montaCTeOS();
        }
        if ($this->toma3 != '') {
            $this->dom->appChild($this->ide, $this->toma3, 'Falta tag "ide"');
        } else {
            $this->dom->appChild($this->ide, $this->toma4, 'Falta tag "ide"');
        }
        $this->dom->appChild($this->infCte, $this->ide, 'Falta tag "infCte"');
        if ($this->compl != '') {
            if ($this->fluxo != '') {
                foreach ($this->pass as $pass) {
                    $this->dom->appChild($this->fluxo, $pass, 'Falta tag "fluxo"');
                }
                $this->dom->appChild($this->compl, $this->fluxo, 'Falta tag "infCte"');
            }
            if ($this->semData != '') {
                $this->tagEntrega();
                $this->dom->appChild($this->entrega, $this->semData, 'Falta tag "Entrega"');
            }
            if ($this->comData != '') {
                $this->tagEntrega();
                $this->dom->appChild($this->entrega, $this->comData, 'Falta tag "Entrega"');
            }
            if ($this->noPeriodo != '') {
                $this->tagEntrega();
                $this->dom->appChild($this->entrega, $this->noPeriodo, 'Falta tag "Entrega"');
            }
            if ($this->semHora != '') {
                $this->tagEntrega();
                $this->dom->appChild($this->entrega, $this->semHora, 'Falta tag "Entrega"');
            }
            if ($this->comHora != '') {
                $this->tagEntrega();
                $this->dom->appChild($this->entrega, $this->comHora, 'Falta tag "Entrega"');
            }
            if ($this->noInter != '') {
                $this->tagEntrega();
                $this->dom->appChild($this->entrega, $this->noInter, 'Falta tag "Entrega"');
            }
            foreach ($this->obsCont as $obsCont) {
                $this->dom->appChild($this->compl, $obsCont, 'Falta tag "compl"');
            }
            foreach ($this->obsFisco as $obsFisco) {
                $this->dom->appChild($this->compl, $obsFisco, 'Falta tag "compl"');
            }
            $this->dom->appChild($this->infCte, $this->compl, 'Falta tag "infCte"');
        }
        $this->dom->appChild($this->emit, $this->enderEmit, 'Falta tag "emit"');
        $this->dom->appChild($this->infCte, $this->emit, 'Falta tag "infCte"');
        if ($this->rem != '') {
            $this->dom->appChild($this->infCte, $this->rem, 'Falta tag "infCte"');
        }
        if ($this->exped != '') {
            $this->dom->appChild($this->infCte, $this->exped, 'Falta tag "infCte"');
        }
        if ($this->receb != '') {
            $this->dom->appChild($this->infCte, $this->receb, 'Falta tag "infCte"');
        }
        if ($this->dest != '') {
            $this->dom->appChild($this->infCte, $this->dest, 'Falta tag "infCte"');
        }
        foreach ($this->comp as $comp) {
            $this->dom->appChild($this->vPrest, $comp, 'Falta tag "vPrest"');
        }
        $this->dom->appChild($this->infCte, $this->vPrest, 'Falta tag "infCte"');
        $this->dom->appChild($this->infCte, $this->imp, 'Falta tag "imp"');
        if ($this->infCteComp != '') { // Caso seja um CTe tipo complemento de valores
            $this->dom->appChild($this->infCte, $this->infCteComp, 'Falta tag "infCteComp"');
        }
        if ($this->infCteAnu != '') { // Caso seja um CTe tipo anulação
            $this->dom->appChild($this->infCte, $this->infCteAnu, 'Falta tag "infCteAnu"');
        }
        if ($this->infCTeNorm != '') { // Caso seja um CTe tipo normal
            $this->dom->appChild($this->infCte, $this->infCTeNorm, 'Falta tag "infCTeNorm"');
            $this->dom->appChild($this->infCTeNorm, $this->infCarga, 'Falta tag "infCarga"');
            foreach ($this->infQ as $infQ) {
                $this->dom->appChild($this->infCarga, $infQ, 'Falta tag "infQ"');
            }
            foreach ($this->infNF as $infNF) {
                $this->dom->appChild($this->infDoc, $infNF, 'Falta tag "infNF"');
            }
            foreach ($this->infNFe as $infNFe) {
                $this->dom->appChild($this->infDoc, $infNFe, 'Falta tag "infNFe"');
            }
            foreach ($this->infOutros as $infOutros) {
                $this->dom->appChild($this->infDoc, $infOutros, 'Falta tag "infOutros"');
            }
            $this->dom->appChild($this->infCTeNorm, $this->infDoc, 'Falta tag "infCTeNorm"');
            if ($this->idDocAntEle != [] || $this->idDocAntPap != []) { //Caso tenha CT-es Anteriores viculados
                $this->dom->appChild($this->infCTeNorm, $this->docAnt, 'Falta tag "docAnt"');
                foreach ($this->emiDocAnt as $indice => $emiDocAnt) {
                    $this->dom->appChild($this->docAnt, $emiDocAnt, 'Falta tag "emiDocAnt"');
                    $this->dom->appChild($emiDocAnt, $this->idDocAnt[$indice], 'Falta tag "idDocAnt"');
                    if (array_key_exists($indice, $this->idDocAntEle)) {
                        foreach ($this->idDocAntEle[$indice] as $idDocAntEle) {
                            $this->dom->appChild($this->idDocAnt[$indice], $idDocAntEle, 'Falta tag "emiDocAnt"');
                        }
                    }
                    if (array_key_exists($indice, $this->idDocAntPap)) {
                        foreach ($this->idDocAntPap[$indice] as $idDocAntPap) {
                            $this->dom->appChild($this->idDocAnt[$indice], $idDocAntPap, 'Falta tag "idDocAntEle"');
                        }
                    }
                }
            }
            foreach ($this->seg as $seg) {
                $this->dom->appChild($this->infCTeNorm, $seg, 'Falta tag "seg"');
            }
            $this->dom->appChild($this->infCTeNorm, $this->infModal, 'Falta tag "infModal"');
            if ($this->modal == '01') {
                if ($this->rodo) {
                    foreach ($this->occ as $occ) {
                        $this->dom->appChild($this->rodo, $occ, 'Falta tag "occ"');
                    }
                    $this->dom->appChild($this->infModal, $this->rodo, 'Falta tag "rodo"');
                }
            } elseif ($this->modal == '02') {
                $this->dom->appChild($this->infModal, $this->aereo, 'Falta tag "aereo"');
            } elseif ($this->modal == '03') {
                $this->dom->appChild($this->infModal, $this->aquav, 'Falta tag "aquav"');
            } elseif ($this->modal == '05') {
                $this->dom->appChild($this->infModal, $this->duto, 'Falta tag "duto"');
            } else {
                throw new Exception('Modal não informado ou não suportado.');
            }
            foreach ($this->veicNovos as $veicNovos) {
                $this->dom->appChild($this->infCTeNorm, $veicNovos, 'Falta tag "infCte"');
            }
            if ($this->infCteSub != '') {
                $this->dom->appChild($this->infCTeNorm, $this->infCteSub, 'Falta tag "infCteSub"');

                if ($this->tomaICMS != '') {
                    $this->dom->appChild($this->infCteSub, $this->tomaICMS, 'Falta tag "infCteSub"');
                }
            }
        }
        if ($this->cobr != '') {
            $this->dom->appChild($this->infCte, $this->cobr, 'Falta tag "infCte"');
        }
        foreach ($this->autXML as $autXML) {
            $this->dom->appChild($this->infCte, $autXML, 'Falta tag "infCte"');
        }
        $this->dom->appChild($this->infCte, $this->infRespTec, 'Falta tag "infCte"');
        //[1] tag infCTe
        $this->dom->appChild($this->CTe, $this->infCte, 'Falta tag "CTe"');
        //[0] tag CTe
        $this->dom->appendChild($this->CTe);
        // testa da chave
        $this->checkCTeKey($this->dom);
        $this->xml = $this->dom->saveXML();
        return true;
    }

    /**
     * Gera as tags para o elemento: "occ" (ordem de coletas)
     * #3
     * Nível:1
     * Os parâmetros para esta função são todos os elementos da tag "occ" do
     * tipo elemento (Ele = E|CE|A) e nível 1
     *
     * @return \DOMElement
     */

    public function tagocc($std)
    {

        $possible = [
            'serie',
            'nOcc',
            'dEmi',
            'CNPJ',
            'cInt',
            'IE',
            'UF',
            'fone'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#3 <occ> - ';
        $occ = $this->dom->createElement('occ');
        $this->dom->addChild(
            $occ,
            'serie',
            $std->serie,
            false,
            $identificador . 'Série da OCC'
        );
        $this->dom->addChild(
            $occ,
            'nOcc',
            $std->nOcc,
            true,
            $identificador . 'Número da Ordem de coleta'
        );
        $this->dom->addChild(
            $occ,
            'dEmi',
            $std->dEmi,
            true,
            $identificador . 'Data de emissão da ordem de coleta'
        );
        //emitente
        $identificador = '#7 <emiOcc> - ';
        $emiOcc = $this->dom->createElement('emiOcc');
        $this->dom->addChild(
            $emiOcc,
            'CNPJ',
            $std->CNPJ,
            true,
            $identificador . 'Número do CNPJ'
        );
        $this->dom->addChild(
            $emiOcc,
            'cInt',
            $std->cInt,
            false,
            $identificador . 'Código interno de uso da transportadora'
        );
        $this->dom->addChild(
            $emiOcc,
            'IE',
            $std->IE,
            true,
            $identificador . 'Inscrição Estadual'
        );
        $this->dom->addChild(
            $emiOcc,
            'UF',
            $std->UF,
            true,
            $identificador . 'Sigla da UF'
        );
        $this->dom->addChild(
            $emiOcc,
            'fone',
            $std->fone,
            false,
            $identificador . 'Telefone'
        );
        $this->dom->appChild($occ, $emiOcc, 'Falta tag "emiOcc"');
        $this->occ[] = $occ;
        return $occ;
    }


    /**
     * Monta o arquivo XML usando as tag's já preenchidas
     *
     * @return bool
     */
    public function montaCTeOS()
    {
        $this->errors = $this->dom->errors;
        if (count($this->errors) > 0) {
            return false;
        }
        $this->buildCTeOS();
        if ($this->infPercurso != '') {
            foreach ($this->infPercurso as $perc) {
                $this->dom->appChild($this->ide, $perc, 'Falta tag "infPercurso"');
            }
        }
        $this->dom->appChild($this->infCte, $this->ide, 'Falta tag "infCte"');
        if ($this->compl != '') {
            $this->dom->appChild($this->infCte, $this->compl, 'Falta tag "infCte"');
        }
        $this->dom->appChild($this->emit, $this->enderEmit, 'Falta tag "emit"');
        $this->dom->appChild($this->infCte, $this->emit, 'Falta tag "infCte"');
        if ($this->toma != '') {
            $this->dom->appChild($this->infCte, $this->toma, 'Falta tag "infCte"');
        }
        foreach ($this->comp as $comp) {
            $this->dom->appChild($this->vPrest, $comp, 'Falta tag "vPrest"');
        }
        $this->dom->appChild($this->infCte, $this->vPrest, 'Falta tag "infCte"');
        $this->dom->appChild($this->infCte, $this->imp, 'Falta tag "imp"');
        if ($this->infCteComp != '') { // Caso seja um CTe tipo complemento de valores
            $this->dom->appChild($this->infCte, $this->infCteComp, 'Falta tag "infCteComp"');
        } elseif ($this->infCteAnu != '') { // Caso seja um CTe tipo anulação
            $this->dom->appChild($this->infCte, $this->infCteAnu, 'Falta tag "infCteAnu"');
        } elseif ($this->infCTeNorm != '') { // Caso seja um CTe tipo normal
            $this->dom->appChild($this->infCte, $this->infCTeNorm, 'Falta tag "infCTeNorm"');
            $this->dom->appChild($this->infCTeNorm, $this->infServico, 'Falta tag "infServico"');
            foreach ($this->infDocRef as $infDocRef) {
                $this->dom->appChild($this->infCTeNorm, $infDocRef, 'Falta tag "infDocRef"');
            }
            foreach ($this->seg as $seg) {
                $this->dom->appChild($this->infCTeNorm, $seg, 'Falta tag "seg"');
            }
            if ($this->infModal != '') {
                $this->dom->appChild($this->infCTeNorm, $this->infModal, 'Falta tag "infModal"');
                if (!empty($this->veic)) {
                    $this->dom->appChild($this->rodo, $this->veic, 'Falta tag "veic"');
                }
                $this->dom->appChild($this->rodo, $this->infFretamento, 'Falta tag "infFretamento"');
                $this->dom->appChild($this->infModal, $this->rodo, 'Falta tag "rodo"');
            }
        }
        if ($this->cobr != '') {
            $this->dom->appChild($this->infCte, $this->cobr, 'Falta tag "infCte"');
        }
        foreach ($this->autXML as $autXML) {
            $this->dom->appChild($this->infCte, $autXML, 'Falta tag "infCte"');
        }
        $this->dom->appChild($this->infCte, $this->infRespTec, 'Falta tag "infCte"');
        $this->dom->appChild($this->CTe, $this->infCte, 'Falta tag "CTe"');
        $this->dom->appendChild($this->CTe);
        // testa da chave
        $this->checkCTeKey($this->dom);
        $this->xml = $this->dom->saveXML();
        return true;
    }

    /**
     * Gera o grupo básico: Informações do CT-e
     * #1
     * Nível: 0
     * @param stdClass $std
     * @return \DOMElement
     */
    public function taginfCTe($std)
    {
        $chave = preg_replace('/[^0-9]/', '', $std->Id);
        $this->infCte = $this->dom->createElement('infCte');
        $this->infCte->setAttribute('Id', 'CTe' . $chave);
        $this->infCte->setAttribute('versao', $std->versao);
        return $this->infCte;
    }

    /**
     * Gera as tags para o elemento: Identificação do CT-e
     * #4
     * Nível: 1
     * @param stdClass $std
     * @return DOMElement|\DOMNode
     */
    public function tagide($std)
    {

        $possible = [
            'cUF',
            'cCT',
            'CFOP',
            'natOp',
            'mod',
            'serie',
            'nCT',
            'dhEmi',
            'tpImp',
            'tpEmis',
            'cDV',
            'tpEmis',
            'tpAmb',
            'tpCTe',
            'procEmi',
            'verProc',
            'indGlobalizado',
            'cMunEnv',
            'xMunEnv',
            'UFEnv',
            'modal',
            'tpServ',
            'indIEToma',
            'cMunIni',
            'xMunIni',
            'UFIni',
            'cMunFim',
            'xMunFim',
            'UFFim',
            'retira',
            'xDetRetira',
            'indIEToma',
            'dhCont',
            'xJust'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $this->tpAmb = $std->tpAmb;
        $this->mod = $std->mod;
        $identificador = '#4 <ide> - ';
        $this->ide = $this->dom->createElement('ide');
        $this->dom->addChild(
            $this->ide,
            'cUF',
            $std->cUF,
            true,
            $identificador . 'Código da UF do emitente do CT-e'
        );
        $this->dom->addChild(
            $this->ide,
            'cCT',
            str_pad($std->cCT, 8, '0', STR_PAD_LEFT),
            true,
            $identificador . 'Código numérico que compõe a Chave de Acesso'
        );
        $this->dom->addChild(
            $this->ide,
            'CFOP',
            $std->CFOP,
            true,
            $identificador . 'Código Fiscal de Operações e Prestações'
        );
        $this->dom->addChild(
            $this->ide,
            'natOp',
            Strings::replaceSpecialsChars(substr(trim($std->natOp), 0, 60)),
            true,
            $identificador . 'Natureza da Operação'
        );
        $this->dom->addChild(
            $this->ide,
            'mod',
            $std->mod,
            true,
            $identificador . 'Modelo do documento fiscal'
        );
        $this->dom->addChild(
            $this->ide,
            'serie',
            $std->serie,
            true,
            $identificador . 'Série do CT-e'
        );
        $this->dom->addChild(
            $this->ide,
            'nCT',
            $std->nCT,
            true,
            $identificador . 'Número do CT-e'
        );
        $this->dom->addChild(
            $this->ide,
            'dhEmi',
            $std->dhEmi,
            true,
            $identificador . 'Data e hora de emissão do CT-e'
        );
        $this->dom->addChild(
            $this->ide,
            'tpImp',
            $std->tpImp,
            true,
            $identificador . 'Formato de impressão do DACTE'
        );
        $this->dom->addChild(
            $this->ide,
            'tpEmis',
            $std->tpEmis,
            true,
            $identificador . 'Forma de emissão do CT-e'
        );
        $this->dom->addChild(
            $this->ide,
            'cDV',
            $std->cDV,
            false,
            $identificador . 'Digito Verificador da chave de acesso do CT-e'
        );
        $this->dom->addChild(
            $this->ide,
            'tpAmb',
            $std->tpAmb,
            true,
            $identificador . 'Tipo do Ambiente'
        );
        $this->dom->addChild(
            $this->ide,
            'tpCTe',
            $std->tpCTe,
            true,
            $identificador . 'Tipo do CT-e'
        );
        $this->dom->addChild(
            $this->ide,
            'procEmi',
            $std->procEmi,
            true,
            $identificador . 'Identificador do processo de emissão do CT-e'
        );
        $this->dom->addChild(
            $this->ide,
            'verProc',
            $std->verProc,
            true,
            $identificador . 'Versão do processo de emissão'
        );
        if ($this->mod == 57) {
            $this->dom->addChild(
                $this->ide,
                'indGlobalizado',
                $std->indGlobalizado,
                false,
                $identificador . 'Indicador de CT-e Globalizado'
            );
        }
        $this->dom->addChild(
            $this->ide,
            'cMunEnv',
            $std->cMunEnv,
            true,
            $identificador . 'Código do Município de envio do CT-e (de onde o documento foi transmitido)'
        );
        $this->dom->addChild(
            $this->ide,
            'xMunEnv',
            $std->xMunEnv,
            true,
            $identificador . 'Nome do Município de envio do CT-e (de onde o documento foi transmitido)'
        );
        $this->dom->addChild(
            $this->ide,
            'UFEnv',
            $std->UFEnv,
            true,
            $identificador . 'Sigla da UF de envio do CT-e (de onde o documento foi transmitido)'
        );
        $this->dom->addChild(
            $this->ide,
            'modal',
            $std->modal,
            true,
            $identificador . 'Modal'
        );
        $this->modal = $std->modal;
        $this->dom->addChild(
            $this->ide,
            'tpServ',
            $std->tpServ,
            true,
            $identificador . 'Tipo do Serviço'
        );
        if ($this->mod == 67) {
            $this->dom->addChild(
                $this->ide,
                'indIEToma',
                $std->indIEToma,
                true,
                $identificador . 'Indicador do papel do tomador na prestação do serviço'
            );
        }
        $this->dom->addChild(
            $this->ide,
            'cMunIni',
            $std->cMunIni,
            true,
            $identificador . 'Nome do Município do início da prestação'
        );
        $this->dom->addChild(
            $this->ide,
            'xMunIni',
            $std->xMunIni,
            true,
            $identificador . 'Nome do Município do início da prestação'
        );
        $this->dom->addChild(
            $this->ide,
            'UFIni',
            $std->UFIni,
            true,
            $identificador . 'UF do início da prestação'
        );
        $this->dom->addChild(
            $this->ide,
            'cMunFim',
            $std->cMunFim,
            true,
            $identificador . 'Código do Município de término da prestação'
        );
        $this->dom->addChild(
            $this->ide,
            'xMunFim',
            $std->xMunFim,
            true,
            $identificador . 'Nome do Município do término da prestação'
        );
        $this->dom->addChild(
            $this->ide,
            'UFFim',
            $std->UFFim,
            true,
            $identificador . 'UF do término da prestação'
        );
        if ($this->mod == 57) {
            $this->dom->addChild(
                $this->ide,
                'retira',
                $std->retira,
                true,
                $identificador . 'Indicador se o Recebedor retira no Aeroporto, Filial, Porto ou Estação de Destino'
            );
            $this->dom->addChild(
                $this->ide,
                'xDetRetira',
                $std->xDetRetira,
                false,
                $identificador . 'Detalhes do retira'
            );
            $this->dom->addChild(
                $this->ide,
                'indIEToma',
                $std->indIEToma,
                true,
                $identificador . 'Indicador do papel do tomador na prestação do serviço'
            );
        }
        $this->dom->addChild(
            $this->ide,
            'dhCont',
            $std->dhCont,
            false,
            $identificador . 'Data e Hora da entrada em contingência'
        );
        $this->dom->addChild(
            $this->ide,
            'xJust',
            Strings::replaceSpecialsChars(substr(trim($std->xJust), 0, 256)),
            false,
            $identificador . 'Justificativa da entrada em contingência'
        );
        $this->tpServ = $std->tpServ;
        return $this->ide;
    }

    public function taginfPercurso($std)
    {

        $possible = [
            'UFPer'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#4 <infPercurso> - ';
        $this->infPercurso[] = $this->dom->createElement('infPercurso');
        $posicao = (int)count($this->infPercurso) - 1;
        $this->dom->addChild(
            $this->infPercurso[$posicao],
            'UFPer',
            $std->uf,
            true,
            $identificador . 'Código da UF do percurso'
        );

        return $this->infPercurso[$posicao];
    }

    /**
     * Gera as tags para o elemento: toma3 (Indicador do "papel" do tomador do serviço no CT-e)
     * e adiciona ao grupo ide
     * #35
     * Nível: 2
     * @param string $toma Tomador do Serviço
     * @param stdClass $std
     * @return \DOMElement
     */
    public function tagtoma3($std)
    {

        $possible = [
            'toma'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#35 <toma3> - ';
        $this->toma3 = $this->dom->createElement('toma3');
        $this->dom->addChild(
            $this->toma3,
            'toma',
            $std->toma,
            true,
            $identificador . 'Tomador do Serviço'
        );
        return $this->toma3;
    }

    /**
     * Gera as tags para o elemento: toma4 (Indicador do "papel" do tomador
     * do serviço no CT-e) e adiciona ao grupo ide
     * #37
     * Nível: 2
     * @param stdClass $std
     * @return \DOMElement
     */
    public function tagtoma4($std)
    {

        $possible = [
            'toma',
            'CNPJ',
            'CPF',
            'IE',
            'xNome',
            'xFant',
            'fone',
            'email'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#37 <toma4> - ';
        $this->toma4 = $this->dom->createElement('toma4');
        $this->dom->addChild(
            $this->toma4,
            'toma',
            $std->toma,
            true,
            $identificador . 'Tomador do Serviço'
        );
        if ($std->CNPJ != '') {
            $this->dom->addChild(
                $this->toma4,
                'CNPJ',
                $std->CNPJ,
                true,
                $identificador . 'Número do CNPJ'
            );
        } elseif ($std->CPF != '') {
            $this->dom->addChild(
                $this->toma4,
                'CPF',
                $std->CPF,
                true,
                $identificador . 'Número do CPF'
            );
        } else {
            $this->dom->addChild(
                $this->toma4,
                'CNPJ',
                $std->CNPJ,
                true,
                $identificador . 'Número do CNPJ'
            );
            $this->dom->addChild(
                $this->toma4,
                'CPF',
                $std->CPF,
                true,
                $identificador . 'Número do CPF'
            );
        }
        $this->dom->addChild(
            $this->toma4,
            'IE',
            $std->IE,
            false,
            $identificador . 'Inscrição Estadual'
        );
        $this->dom->addChild(
            $this->toma4,
            'xNome',
            $std->xNome,
            true,
            $identificador . 'Razão Social ou Nome'
        );
        $this->dom->addChild(
            $this->toma4,
            'xFant',
            $std->xFant,
            false,
            $identificador . 'Nome Fantasia'
        );
        $this->dom->addChild(
            $this->toma4,
            'fone',
            $std->fone,
            false,
            $identificador . 'Telefone'
        );
        $this->dom->addChild(
            $this->toma4,
            'email',
            $std->email,
            false,
            $identificador . 'Endereço de email'
        );
        return $this->toma4;
    }

    /**
     * Gera as tags para o elemento: toma4 (Indicador do "papel" do tomador
     * do serviço no CT-e OS) e adiciona ao grupo ide
     * #37
     * Nível: 2
     *
     * @return \DOMElement
     */
    public function tagtomador($std)
    {

        $possible = [
            'CNPJ',
            'CPF',
            'IE',
            'xNome',
            'xFant',
            'fone',
            'xLgr',
            'nro',
            'xCpl',
            'xBairro',
            'cMun',
            'xMun',
            'CEP',
            'UF',
            'cPais',
            'xPais',
            'email'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#37 <toma> - ';
        $this->toma = $this->dom->createElement('toma');
        if ($std->CNPJ != '') {
            $this->dom->addChild(
                $this->toma,
                'CNPJ',
                $std->CNPJ,
                true,
                $identificador . 'Número do CNPJ'
            );
        } elseif ($std->CPF != '') {
            $this->dom->addChild(
                $this->toma,
                'CPF',
                $std->CPF,
                true,
                $identificador . 'Número do CPF'
            );
        } else {
            $this->dom->addChild(
                $this->toma,
                'CNPJ',
                $std->CNPJ,
                true,
                $identificador . 'Número do CNPJ'
            );
            $this->dom->addChild(
                $this->toma,
                'CPF',
                $std->CPF,
                true,
                $identificador . 'Número do CPF'
            );
        }
        $this->dom->addChild(
            $this->toma,
            'IE',
            $std->IE,
            false,
            $identificador . 'Inscrição Estadual'
        );
        $this->dom->addChild(
            $this->toma,
            'xNome',
            $std->xNome,
            true,
            $identificador . 'Razão Social ou Nome'
        );
        $this->dom->addChild(
            $this->toma,
            'xFant',
            $std->xFant,
            false,
            $identificador . 'Nome Fantasia'
        );
        $this->dom->addChild(
            $this->toma,
            'fone',
            $std->fone,
            false,
            $identificador . 'Telefone'
        );
        //Endereço Tomador
        $this->enderToma = $this->dom->createElement('enderToma');
        $this->dom->addChild(
            $this->enderToma,
            'xLgr',
            $std->xLgr,
            true,
            $identificador . 'Logradouro'
        );
        $this->dom->addChild(
            $this->enderToma,
            'nro',
            $std->nro,
            true,
            $identificador . 'Número'
        );
        $this->dom->addChild(
            $this->enderToma,
            'xCpl',
            $std->xCpl,
            false,
            $identificador . 'Complemento'
        );
        $this->dom->addChild(
            $this->enderToma,
            'xBairro',
            $std->xBairro,
            true,
            $identificador . 'Bairro'
        );
        $this->dom->addChild(
            $this->enderToma,
            'cMun',
            $std->cMun,
            true,
            $identificador . 'Código do município (utilizar a tabela do IBGE)'
        );
        $this->dom->addChild(
            $this->enderToma,
            'xMun',
            $std->xMun,
            true,
            $identificador . 'Nome do município'
        );
        $this->dom->addChild(
            $this->enderToma,
            'CEP',
            $std->CEP,
            false,
            $identificador . 'CEP'
        );
        $this->dom->addChild(
            $this->enderToma,
            'UF',
            $std->UF,
            true,
            $identificador . 'Sigla da UF'
        );
        $this->dom->addChild(
            $this->enderToma,
            'cPais',
            $std->cPais,
            false,
            $identificador . 'Código do país'
        );
        $this->dom->addChild(
            $this->enderToma,
            'xPais',
            $std->xPais,
            false,
            $identificador . 'Nome do país'
        );
        $this->dom->appChild($this->toma, $this->enderToma, 'Falta tag "enderToma"');
        $this->dom->addChild(
            $this->toma,
            'email',
            $std->email,
            false,
            $identificador . 'Endereço de email'
        );
        return $this->toma;
    }

    /**
     * Gera as tags para o elemento: "enderToma" (Dados do endereço) e adiciona ao grupo "toma4"
     * #45
     * Nível: 3
     *
     * @return \DOMElement
     */
    public function tagenderToma($std)
    {

        $possible = [
            'xLgr',
            'nro',
            'xCpl',
            'xBairro',
            'cMun',
            'xMun',
            'CEP',
            'UF',
            'cPais',
            'xPais'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#45 <enderToma> - ';
        $this->enderToma = $this->dom->createElement('enderToma');
        $this->dom->addChild(
            $this->enderToma,
            'xLgr',
            $std->xLgr,
            true,
            $identificador . 'Logradouro'
        );
        $this->dom->addChild(
            $this->enderToma,
            'nro',
            $std->nro,
            true,
            $identificador . 'Número'
        );
        $this->dom->addChild(
            $this->enderToma,
            'xCpl',
            $std->xCpl,
            false,
            $identificador . 'Complemento'
        );
        $this->dom->addChild(
            $this->enderToma,
            'xBairro',
            $std->xBairro,
            true,
            $identificador . 'Bairro'
        );
        $this->dom->addChild(
            $this->enderToma,
            'cMun',
            $std->cMun,
            true,
            $identificador . 'Código do município (utilizar a tabela do IBGE)'
        );
        $this->dom->addChild(
            $this->enderToma,
            'xMun',
            $std->xMun,
            true,
            $identificador . 'Nome do município'
        );
        $this->dom->addChild(
            $this->enderToma,
            'CEP',
            $std->CEP,
            false,
            $identificador . 'CEP'
        );
        $this->dom->addChild(
            $this->enderToma,
            'UF',
            $std->UF,
            true,
            $identificador . 'Sigla da UF'
        );
        $this->dom->addChild(
            $this->enderToma,
            'cPais',
            $std->cPais,
            false,
            $identificador . 'Código do país'
        );
        $this->dom->addChild(
            $this->enderToma,
            'xPais',
            $std->xPais,
            false,
            $identificador . 'Nome do país'
        );

        if (!empty($this->toma4)) {
            $this->toma4->insertBefore($this->enderToma, $this->toma4->getElementsByTagName("email")->item(0));
        }
        return $this->enderToma;
    }

    /**
     * Gera as tags para o elemento: "compl" (Dados complementares do CT-e para fins operacionais ou comerciais)
     * #59
     * Nível: 1
     *
     * @return \DOMElement
     */
    public function tagcompl($std)
    {

        $possible = [
            'xCaracAd',
            'xCaracSer',
            'xEmi',
            'origCalc',
            'destCalc',
            'xObs'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#59 <compl> - ';
        if ($this->compl == '') {
            $this->compl = $this->dom->createElement('compl');
        }
        $this->dom->addChild(
            $this->compl,
            'xCaracAd',
            $std->xCaracAd,
            false,
            $identificador . 'Característica adicional do transporte'
        );
        $this->dom->addChild(
            $this->compl,
            'xCaracSer',
            $std->xCaracSer,
            false,
            $identificador . 'Característica adicional do serviço'
        );
        $this->dom->addChild(
            $this->compl,
            'xEmi',
            $std->xEmi,
            false,
            $identificador . 'Funcionário emissor do CTe'
        );
        if ($this->mod == 57) {
            $this->dom->addChild(
                $this->compl,
                'origCalc',
                $std->origCalc,
                false,
                $identificador . 'Município de origem para efeito de cálculo do frete'
            );
            $this->dom->addChild(
                $this->compl,
                'destCalc',
                $std->destCalc,
                false,
                $identificador . 'Município de destino para efeito de cálculo do frete'
            );
        }
        $this->dom->addChild(
            $this->compl,
            'xObs',
            $std->xObs,
            false,
            $identificador . 'Observações Gerais'
        );
        return $this->compl;
    }

    /**
     * Gera as tags para o elemento: "compl" (Dados complementares do CT-e OS para fins operacionais ou comerciais)
     * #59
     * Nível: 1
     *
     * @return \DOMElement
     */
    public function tagcomplCTeOs($std)
    {

        $possible = [
            'xCaracAd',
            'xCaracSer',
            'xEmi',
            'xObs'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#59 <compl> - ';
        $this->compl = $this->dom->createElement('compl');
        $this->dom->addChild(
            $this->compl,
            'xCaracAd',
            $std->xCaracAd,
            false,
            $identificador . 'Característica adicional do transporte'
        );
        $this->dom->addChild(
            $this->compl,
            'xCaracSer',
            $std->xCaracSer,
            false,
            $identificador . 'Característica adicional do serviço'
        );
        $this->dom->addChild(
            $this->compl,
            'xEmi',
            $std->xEmi,
            false,
            $identificador . 'Funcionário emissor do CTe'
        );
        $this->dom->addChild(
            $this->compl,
            'xObs',
            $std->xObs,
            false,
            $identificador . 'Observações Gerais'
        );
        return $this->compl;
    }

    /**
     * Gera as tags para o elemento: "fluxo" (Previsão do fluxo da carga)
     * #63
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "fluxo" do
     * tipo elemento (Ele = E|CE|A) e nível 3
     *
     * @return \DOMElement
     */
    public function tagfluxo($std)
    {

        $possible = [
            'xOrig',
            'xDest',
            'xRota'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#63 <fluxo> - ';
        $this->fluxo = $this->dom->createElement('fluxo');
        $this->dom->addChild(
            $this->fluxo,
            'xOrig',
            $std->xOrig,
            false,
            $identificador . 'Sigla ou código interno da Filial/Porto/Estação/ Aeroporto de Origem'
        );
        $this->dom->addChild(
            $this->fluxo,
            'xDest',
            $std->xDest,
            false,
            $identificador . 'Sigla ou código interno da Filial/Porto/Estação/Aeroporto de Destino'
        );
        $this->dom->addChild(
            $this->fluxo,
            'xRota',
            $std->xRota,
            false,
            $identificador . 'Código da Rota de Entrega'
        );
        return $this->fluxo;
    }

    /**
     * Gera as tags para o elemento: "pass"
     * #65
     * Nível: 3
     *
     * @return \DOMElement
     */
    public function tagpass($std)
    {

        $possible = [
            'xPass'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#65 <pass> - ';
        $this->pass[] = $this->dom->createElement('pass');
        $posicao = (int)count($this->pass) - 1;
        $this->dom->addChild(
            $this->pass[$posicao],
            'xPass',
            $std->xPass,
            false,
            $identificador . 'Sigla ou código interno da Filial/Porto/Estação/Aeroporto de Passagem'
        );
        return $this->pass[$posicao];
    }

    /**
     * Gera as tags para o elemento: "semData" (Entrega sem data definida)
     * #70
     * Nível: 3
     *
     * @return \DOMElement
     */
    public function tagsemData($std)
    {

        $possible = [
            'tpPer'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#70 <semData> - ';
        $this->semData = $this->dom->createElement('semData');
        $this->dom->addChild(
            $this->semData,
            'tpPer',
            $std->tpPer,
            true,
            $identificador . 'Tipo de data/período programado para entrega'
        );
        return $this->semData;
    }

    /**
     * Gera as tags para o elemento: "comData" (Entrega com data definida)
     * #72
     * Nível: 3
     *
     * @return \DOMElement
     */
    public function tagcomData($std)
    {

        $possible = [
            'tpPer',
            'dProg'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#72 <comData> - ';
        $this->comData = $this->dom->createElement('comData');
        $this->dom->addChild(
            $this->comData,
            'tpPer',
            $std->tpPer,
            true,
            $identificador . 'Tipo de data/período programado para entrega'
        );
        $this->dom->addChild(
            $this->comData,
            'dProg',
            $std->dProg,
            true,
            $identificador . 'Data programada'
        );
        return $this->comData;
    }

    /**
     * Gera as tags para o elemento: "noPeriodo" (Entrega no período definido)
     * #75
     * Nível: 3
     *
     * @return \DOMElement
     */
    public function tagnoPeriodo($std)
    {

        $possible = [
            'tpPer',
            'dIni',
            'dFim'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#75 <noPeriodo> - ';
        $this->noPeriodo = $this->dom->createElement('noPeriodo');
        $this->dom->addChild(
            $this->noPeriodo,
            'tpPer',
            $std->tpPer,
            true,
            $identificador . 'Tipo de data/período programado para entrega'
        );
        $this->dom->addChild(
            $this->noPeriodo,
            'dIni',
            $std->dIni,
            true,
            $identificador . 'Data inicial'
        );
        $this->dom->addChild(
            $this->noPeriodo,
            'dFim',
            $std->dFim,
            true,
            $identificador . 'Data final'
        );
        return $this->noPeriodo;
    }

    /**
     * Gera as tags para o elemento: "semHora" (Entrega sem hora definida)
     * #79
     * Nível: 3
     * Os parâmetros para esta função são todos os elementos da tag "semHora" do
     * tipo elemento (Ele = E|CE|A) e nível 4
     *
     * @return \DOMElement
     */
    public function tagsemHora($std)
    {

        $possible = [
            'tpHor'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#79 <semHora> - ';
        $this->semHora = $this->dom->createElement('semHora');
        $this->dom->addChild(
            $this->semHora,
            'tpHor',
            $std->tpHor,
            true,
            $identificador . 'Tipo de hora'
        );
        return $this->semHora;
    }

    /**
     * Gera as tags para o elemento: "comHora" (Entrega sem hora definida)
     * # = 81
     * Nível = 3
     * Os parâmetros para esta função são todos os elementos da tag "comHora" do
     * tipo elemento (Ele = E|CE|A) e nível 4
     *
     * @return \DOMElement
     */
    public function tagcomHora($std)
    {

        $possible = [
            'tpHor',
            'hProg'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#81 <comHora> - ';
        $this->comHora = $this->dom->createElement('comHora');
        $this->dom->addChild(
            $this->comHora,
            'tpHor',
            $std->tpHor,
            true,
            $identificador . 'Tipo de hora'
        );
        $this->dom->addChild(
            $this->comHora,
            'hProg',
            $std->hProg,
            true,
            $identificador . 'Hora programada'
        );
        return $this->comHora;
    }

    /**
     * Gera as tags para o elemento: "noInter" (Entrega no intervalo de horário definido)
     * #84
     * Nível: 3
     * Os parâmetros para esta função são todos os elementos da tag "noInter" do
     * tipo elemento (Ele = E|CE|A) e nível 4
     *
     * @return \DOMElement
     */
    public function tagnoInter($std)
    {

        $possible = [
            'tpHor',
            'hIni',
            'hFim'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#84 <noInter> - ';
        $this->noInter = $this->dom->createElement('noInter');
        $this->dom->addChild(
            $this->noInter,
            'tpHor',
            $std->tpHor,
            true,
            $identificador . 'Tipo de hora'
        );
        $this->dom->addChild(
            $this->noInter,
            'hIni',
            $std->hIni,
            true,
            $identificador . 'Hora inicial'
        );
        $this->dom->addChild(
            $this->noInter,
            'hFim',
            $std->hFim,
            true,
            $identificador . 'Hora final'
        );
        return $this->noInter;
    }

    /**
     * Gera as tags para o elemento: "ObsCont" (Campo de uso livre do contribuinte)
     * #91
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "ObsCont" do
     * tipo elemento (Ele = E|CE|A) e nível 3
     *
     * @return boolean
     */
    public function tagobsCont($std)
    {

        $possible = [
            'xCampo',
            'xTexto'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#91 <ObsCont> - ';
        if (count($this->obsCont) <= 10) {
            $this->obsCont[] = $this->dom->createElement('ObsCont');
            $posicao = (int)count($this->obsCont) - 1;
            $this->obsCont[$posicao]->setAttribute('xCampo', $std->xCampo);
            $this->dom->addChild(
                $this->obsCont[$posicao],
                'xTexto',
                $std->xTexto,
                true,
                $identificador . 'Conteúdo do campo'
            );
            return true;
        }
        $this->errors[] = array(
            'tag' => (string)'<ObsCont>',
            'desc' => (string)'Campo de uso livre do contribuinte',
            'erro' => (string)'Tag deve aparecer de 0 a 10 vezes'
        );
        return false;
    }

    /**
     * Gera as tags para o elemento: "ObsFisco" (Campo de uso livre do contribuinte)
     * #94
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "ObsFisco" do tipo
     * elemento (Ele = E|CE|A) e nível 3
     *
     * @return boolean
     */
    public function tagobsFisco($std)
    {

        $possible = [
            'xCampo',
            'xTexto'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#94 <ObsFisco> - ';
        if (count($this->obsFisco) <= 10) {
            $this->obsFisco[] = $this->dom->createElement('ObsFisco');
            $posicao = (int)count($this->obsFisco) - 1;
            $this->obsFisco[$posicao]->setAttribute('xCampo', $std->xCampo);
            $this->dom->addChild(
                $this->obsFisco[$posicao],
                'xTexto',
                $std->xTexto,
                true,
                $identificador . 'Conteúdo do campo'
            );
            return true;
        }
        $this->errors[] = array(
            'tag' => (string)'<ObsFisco>',
            'desc' => (string)'Campo de uso livre do contribuinte',
            'erro' => (string)'Tag deve aparecer de 0 a 10 vezes'
        );
        return false;
    }

    /**
     * Gera as tags para o elemento: "emit" (Identificação do Emitente do CT-e)
     * #97
     * Nível: 1
     * Os parâmetros para esta função são todos os elementos da tag "emit" do
     * tipo elemento (Ele = E|CE|A) e nível 2
     *
     * @return \DOMElement
     */
    public function tagemit($std)
    {

        $possible = [
            'CNPJ',
            'IE',
            'IEST',
            'xNome',
            'xFant'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#97 <emit> - ';
        $this->emit = $this->dom->createElement('emit');
        $this->dom->addChild(
            $this->emit,
            'CNPJ',
            $std->CNPJ,
            true,
            $identificador . 'CNPJ do emitente'
        );
        $this->dom->addChild(
            $this->emit,
            'IE',
            Strings::onlyNumbers($std->IE),
            false,
            $identificador . 'Inscrição Estadual do Emitente'
        );
        $this->dom->addChild(
            $this->emit,
            'IEST',
            Strings::onlyNumbers($std->IEST),
            false,
            $identificador . 'Inscrição Estadual do Substituto Tributário'
        );
        $this->dom->addChild(
            $this->emit,
            'xNome',
            $std->xNome,
            true,
            $identificador . 'Razão social ou Nome do emitente'
        );
        $this->dom->addChild(
            $this->emit,
            'xFant',
            $std->xFant,
            false,
            $identificador . 'Nome fantasia'
        );
        return $this->emit;
    }

    /**
     * Gera as tags para o elemento: "enderEmit" (Endereço do emitente)
     * #102
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "enderEmit" do
     * tipo elemento (Ele = E|CE|A) e nível 3
     *
     * @return \DOMElement
     */
    public function tagenderEmit($std)
    {

        $possible = [
            'xLgr',
            'nro',
            'xCpl',
            'xBairro',
            'cMun',
            'xMun',
            'CEP',
            'UF',
            'fone'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#102 <enderEmit> - ';
        $this->enderEmit = $this->dom->createElement('enderEmit');
        $this->dom->addChild(
            $this->enderEmit,
            'xLgr',
            $std->xLgr,
            true,
            $identificador . 'Logradouro'
        );
        $this->dom->addChild(
            $this->enderEmit,
            'nro',
            $std->nro,
            true,
            $identificador . 'Número'
        );
        $this->dom->addChild(
            $this->enderEmit,
            'xCpl',
            $std->xCpl,
            false,
            $identificador . 'Complemento'
        );
        $this->dom->addChild(
            $this->enderEmit,
            'xBairro',
            $std->xBairro,
            true,
            $identificador . 'Bairro'
        );
        $this->dom->addChild(
            $this->enderEmit,
            'cMun',
            $std->cMun,
            true,
            $identificador . 'Código do município'
        );
        $this->dom->addChild(
            $this->enderEmit,
            'xMun',
            $std->xMun,
            true,
            $identificador . 'Nome do município'
        );
        $this->dom->addChild(
            $this->enderEmit,
            'CEP',
            $std->CEP,
            false,
            $identificador . 'CEP'
        );
        $this->dom->addChild(
            $this->enderEmit,
            'UF',
            $std->UF,
            true,
            $identificador . 'Sigla da UF'
        );
        $this->dom->addChild(
            $this->enderEmit,
            'fone',
            $std->fone,
            false,
            $identificador . 'Telefone'
        );
        return $this->enderEmit;
    }

    /**
     * Gera as tags para o elemento: "rem" (Informações do Remetente das mercadorias
     * transportadas pelo CT-e)
     * #112
     * Nível = 1
     * Os parâmetros para esta função são todos os elementos da tag "rem" do
     * tipo elemento (Ele = E|CE|A) e nível 2
     *
     * @return \DOMElement
     */
    public function tagrem($std)
    {

        $possible = [
            'CNPJ',
            'CPF',
            'IE',
            'xNome',
            'xFant',
            'fone',
            'email'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#97 <rem> - ';
        $this->rem = $this->dom->createElement('rem');
        if ($std->CNPJ != '') {
            $this->dom->addChild(
                $this->rem,
                'CNPJ',
                $std->CNPJ,
                true,
                $identificador . 'CNPJ do Remente'
            );
        } elseif ($std->CPF != '') {
            $this->dom->addChild(
                $this->rem,
                'CPF',
                $std->CPF,
                true,
                $identificador . 'CPF do Remente'
            );
        } else {
            $this->dom->addChild(
                $this->rem,
                'CNPJ',
                $std->CNPJ,
                true,
                $identificador . 'CNPJ do Remente'
            );
            $this->dom->addChild(
                $this->rem,
                'CPF',
                $std->CPF,
                true,
                $identificador . 'CPF do remente'
            );
        }
        $this->dom->addChild(
            $this->rem,
            'IE',
            $std->IE,
            false,
            $identificador . 'Inscrição Estadual do remente'
        );
        $xNome = $std->xNome;
        if ($this->tpAmb == '2') {
            $xNome = 'CT-E EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL';
        }
        $this->dom->addChild(
            $this->rem,
            'xNome',
            Strings::replaceSpecialsChars(substr(trim($xNome), 0, 60)),
            true,
            $identificador . 'Razão social ou Nome do remente'
        );
        $this->dom->addChild(
            $this->rem,
            'xFant',
            $std->xFant,
            false,
            $identificador . 'Nome fantasia'
        );
        $this->dom->addChild(
            $this->rem,
            'fone',
            $std->fone,
            false,
            $identificador . 'Telefone'
        );
        $this->dom->addChild(
            $this->rem,
            'email',
            $std->email,
            false,
            $identificador . 'Endereço de email'
        );
        return $this->rem;
    }

    /**
     * Gera as tags para o elemento: "enderReme" (Dados do endereço)
     * #120
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "enderReme" do
     * tipo elemento (Ele = E|CE|A) e nível 3
     *
     * @return \DOMElement
     */
    public function tagenderReme($std)
    {

        $possible = [
            'xLgr',
            'nro',
            'xCpl',
            'xBairro',
            'cMun',
            'xMun',
            'CEP',
            'UF',
            'cPais',
            'xPais'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#119 <enderReme> - ';
        $this->enderReme = $this->dom->createElement('enderReme');
        $this->dom->addChild(
            $this->enderReme,
            'xLgr',
            $std->xLgr,
            true,
            $identificador . 'Logradouro'
        );
        $this->dom->addChild(
            $this->enderReme,
            'nro',
            $std->nro,
            true,
            $identificador . 'Número'
        );
        $this->dom->addChild(
            $this->enderReme,
            'xCpl',
            $std->xCpl,
            false,
            $identificador . 'Complemento'
        );
        $this->dom->addChild(
            $this->enderReme,
            'xBairro',
            $std->xBairro,
            true,
            $identificador . 'Bairro'
        );
        $this->dom->addChild(
            $this->enderReme,
            'cMun',
            $std->cMun,
            true,
            $identificador . 'Código do município (utilizar a tabela do IBGE)'
        );
        $this->dom->addChild(
            $this->enderReme,
            'xMun',
            $std->xMun,
            true,
            $identificador . 'Nome do município'
        );
        $this->dom->addChild(
            $this->enderReme,
            'CEP',
            $std->CEP,
            false,
            $identificador . 'CEP'
        );
        $this->dom->addChild(
            $this->enderReme,
            'UF',
            $std->UF,
            true,
            $identificador . 'Sigla da UF'
        );
        $this->dom->addChild(
            $this->enderReme,
            'cPais',
            $std->cPais,
            false,
            $identificador . 'Código do país'
        );
        $this->dom->addChild(
            $this->enderReme,
            'xPais',
            $std->xPais,
            false,
            $identificador . 'Nome do país'
        );

        $node = $this->rem->getElementsByTagName("email")->item(0);
        $this->rem->insertBefore($this->enderReme, $node);
        return $this->enderReme;
    }

    /**
     * Gera as tags para o elemento: "exped" (Informações do Expedidor da Carga)
     * #132
     * Nível: 1
     * Os parâmetros para esta função são todos os elementos da tag "exped" do
     * tipo elemento (Ele = E|CE|A) e nível 2
     *
     * @return \DOMElement
     */
    public function tagexped($std)
    {

        $possible = [
            'CNPJ',
            'CPF',
            'IE',
            'xNome',
            'fone',
            'email'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#142 <exped> - ';
        $this->exped = $this->dom->createElement('exped');
        if ($std->CNPJ != '') {
            $this->dom->addChild(
                $this->exped,
                'CNPJ',
                $std->CNPJ,
                true,
                $identificador . 'Número do CNPJ'
            );
        } elseif ($std->CPF != '') {
            $this->dom->addChild(
                $this->exped,
                'CPF',
                $std->CPF,
                true,
                $identificador . 'Número do CPF'
            );
        } else {
            $this->dom->addChild(
                $this->exped,
                'CNPJ',
                $std->CNPJ,
                true,
                $identificador . 'Número do CNPJ'
            );
            $this->dom->addChild(
                $this->exped,
                'CPF',
                $std->CPF,
                true,
                $identificador . 'Número do CPF'
            );
        }
        if (!empty($std->IE)) {
            $this->dom->addChild(
                $this->exped,
                'IE',
                $std->IE,
                true,
                $identificador . 'Inscrição Estadual'
            );
        }
        $xNome = $std->xNome;
        if ($this->tpAmb == '2') {
            $xNome = 'CT-E EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL';
        }
        $this->dom->addChild(
            $this->exped,
            'xNome',
            Strings::replaceSpecialsChars(substr(trim($xNome), 0, 60)),
            true,
            $identificador . 'Razão social ou Nome'
        );
        $this->dom->addChild(
            $this->exped,
            'fone',
            $std->fone,
            false,
            $identificador . 'Telefone'
        );
        $this->dom->addChild(
            $this->exped,
            'email',
            $std->email,
            false,
            $identificador . 'Endereço de email'
        );
        return $this->exped;
    }

    /**
     * Gera as tags para o elemento: "enderExped" (Dados do endereço)
     * #138
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "enderExped" do
     * tipo elemento (Ele = E|CE|A) e nível 3
     *
     * @return \DOMElement
     */
    public function tagenderExped($std)
    {

        $possible = [
            'xLgr',
            'nro',
            'xCpl',
            'xBairro',
            'cMun',
            'xMun',
            'CEP',
            'UF',
            'cPais',
            'xPais'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#148 <enderExped> - ';
        $this->enderExped = $this->dom->createElement('enderExped');
        $this->dom->addChild(
            $this->enderExped,
            'xLgr',
            $std->xLgr,
            true,
            $identificador . 'Logradouro'
        );
        $this->dom->addChild(
            $this->enderExped,
            'nro',
            $std->nro,
            true,
            $identificador . 'Número'
        );
        $this->dom->addChild(
            $this->enderExped,
            'xCpl',
            $std->xCpl,
            false,
            $identificador . 'Complemento'
        );
        $this->dom->addChild(
            $this->enderExped,
            'xBairro',
            $std->xBairro,
            true,
            $identificador . 'Bairro'
        );
        $this->dom->addChild(
            $this->enderExped,
            'cMun',
            $std->cMun,
            true,
            $identificador . 'Código do município (utilizar a tabela do IBGE)'
        );
        $this->dom->addChild(
            $this->enderExped,
            'xMun',
            $std->xMun,
            true,
            $identificador . 'Nome do município'
        );
        $this->dom->addChild(
            $this->enderExped,
            'CEP',
            $std->CEP,
            false,
            $identificador . 'CEP'
        );
        $this->dom->addChild(
            $this->enderExped,
            'UF',
            $std->UF,
            true,
            $identificador . 'Sigla da UF'
        );
        $this->dom->addChild(
            $this->enderExped,
            'cPais',
            $std->cPais,
            false,
            $identificador . 'Código do país'
        );
        $this->dom->addChild(
            $this->enderExped,
            'xPais',
            $std->xPais,
            false,
            $identificador . 'Nome do país'
        );
        $node = $this->exped->getElementsByTagName("email")->item(0);
        $this->exped->insertBefore($this->enderExped, $node);
        return $this->enderExped;
    }

    /**
     * Gera as tags para o elemento: "receb" (Informações do Recebedor da Carga)
     * #150
     * Nível: 1
     * Os parâmetros para esta função são todos os elementos da tag "receb" do
     * tipo elemento (Ele = E|CE|A) e nível 2
     *
     * @return \DOMElement
     */
    public function tagreceb($std)
    {

        $possible = [
            'CNPJ',
            'CPF',
            'IE',
            'xNome',
            'fone',
            'email'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#160 <receb> - ';
        $this->receb = $this->dom->createElement('receb');
        if ($std->CNPJ != '') {
            $this->dom->addChild(
                $this->receb,
                'CNPJ',
                $std->CNPJ,
                true,
                $identificador . 'Número do CNPJ'
            );
        } elseif ($std->CPF != '') {
            $this->dom->addChild(
                $this->receb,
                'CPF',
                $std->CPF,
                true,
                $identificador . 'Número do CPF'
            );
        } else {
            $this->dom->addChild(
                $this->receb,
                'CNPJ',
                $std->CNPJ,
                true,
                $identificador . 'Número do CNPJ'
            );
            $this->dom->addChild(
                $this->receb,
                'CPF',
                $std->CPF,
                true,
                $identificador . 'Número do CPF'
            );
        }
        if (!empty($std->IE)) {
            $this->dom->addChild(
                $this->receb,
                'IE',
                $std->IE,
                true,
                $identificador . 'Inscrição Estadual'
            );
        }
        $xNome = $std->xNome;
        if ($this->tpAmb == '2') {
            $xNome = 'CT-E EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL';
        }
        $this->dom->addChild(
            $this->receb,
            'xNome',
            Strings::replaceSpecialsChars(substr(trim($xNome), 0, 60)),
            true,
            $identificador . 'Razão social ou Nome'
        );
        $this->dom->addChild(
            $this->receb,
            'fone',
            $std->fone,
            false,
            $identificador . 'Telefone'
        );
        $this->dom->addChild(
            $this->receb,
            'email',
            $std->email,
            false,
            $identificador . 'Endereço de email'
        );
        return $this->receb;
    }

    /**
     * Gera as tags para o elemento: "enderReceb" (Informações do Recebedor da Carga)
     * #156
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "enderReceb" do
     * tipo elemento (Ele = E|CE|A) e nível 3
     *
     * @return \DOMElement
     */
    public function tagenderReceb($std)
    {

        $possible = [
            'xLgr',
            'nro',
            'xCpl',
            'xBairro',
            'cMun',
            'xMun',
            'CEP',
            'UF',
            'cPais',
            'xPais'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#160 <enderReceb> - ';
        $this->enderReceb = $this->dom->createElement('enderReceb');
        $this->dom->addChild(
            $this->enderReceb,
            'xLgr',
            $std->xLgr,
            true,
            $identificador . 'Logradouro'
        );
        $this->dom->addChild(
            $this->enderReceb,
            'nro',
            $std->nro,
            true,
            $identificador . 'Número'
        );
        $this->dom->addChild(
            $this->enderReceb,
            'xCpl',
            $std->xCpl,
            false,
            $identificador . 'Complemento'
        );
        $this->dom->addChild(
            $this->enderReceb,
            'xBairro',
            $std->xBairro,
            true,
            $identificador . 'Bairro'
        );
        $this->dom->addChild(
            $this->enderReceb,
            'cMun',
            $std->cMun,
            true,
            $identificador . 'Código do município (utilizar a tabela do IBGE)'
        );
        $this->dom->addChild(
            $this->enderReceb,
            'xMun',
            $std->xMun,
            true,
            $identificador . 'Nome do município'
        );
        $this->dom->addChild(
            $this->enderReceb,
            'CEP',
            $std->CEP,
            false,
            $identificador . 'CEP'
        );
        $this->dom->addChild(
            $this->enderReceb,
            'UF',
            $std->UF,
            true,
            $identificador . 'Sigla da UF'
        );
        $this->dom->addChild(
            $this->enderReceb,
            'cPais',
            $std->cPais,
            false,
            $identificador . 'Código do país'
        );
        $this->dom->addChild(
            $this->enderReceb,
            'xPais',
            $std->xPais,
            false,
            $identificador . 'Nome do país'
        );
        $node = $this->receb->getElementsByTagName("email")->item(0);
        $this->receb->insertBefore($this->enderReceb, $node);
        return $this->enderReceb;
    }

    /**
     * Gera as tags para o elemento: "dest" (Informações do Destinatário do CT-e)
     * #168
     * Nível: 1
     * Os parâmetros para esta função são todos os elementos da tag "dest" do
     * tipo elemento (Ele = E|CE|A) e nível 2
     *
     * @return \DOMElement
     */
    public function tagdest($std)
    {

        $possible = [
            'CNPJ',
            'CPF',
            'IE',
            'xNome',
            'fone',
            'ISUF',
            'email'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#178 <dest> - ';
        $this->dest = $this->dom->createElement('dest');
        if ($std->CNPJ != '') {
            $this->dom->addChild(
                $this->dest,
                'CNPJ',
                $std->CNPJ,
                true,
                $identificador . 'Número do CNPJ'
            );
        } elseif ($std->CPF != '') {
            $this->dom->addChild(
                $this->dest,
                'CPF',
                $std->CPF,
                true,
                $identificador . 'Número do CPF'
            );
        } else {
            $this->dom->addChild(
                $this->dest,
                'CNPJ',
                $std->CNPJ,
                true,
                $identificador . 'Número do CNPJ'
            );
            $this->dom->addChild(
                $this->dest,
                'CPF',
                $std->CPF,
                true,
                $identificador . 'Número do CPF'
            );
        }
        if (!empty($std->IE)) {
            $this->dom->addChild(
                $this->dest,
                'IE',
                $std->IE,
                true,
                $identificador . 'Inscrição Estadual'
            );
        }
        $xNome = $std->xNome;
        if ($this->tpAmb == '2') {
            $xNome = 'CT-E EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL';
        }
        $this->dom->addChild(
            $this->dest,
            'xNome',
            Strings::replaceSpecialsChars(substr(trim($xNome), 0, 60)),
            true,
            $identificador . 'Razão social ou Nome'
        );
        $this->dom->addChild(
            $this->dest,
            'fone',
            $std->fone,
            false,
            $identificador . 'Telefone'
        );
        $this->dom->addChild(
            $this->dest,
            'ISUF',
            $std->ISUF,
            false,
            $identificador . 'Inscrição na SUFRAMA'
        );
        $this->dom->addChild(
            $this->dest,
            'email',
            $std->email,
            false,
            $identificador . 'Endereço de email'
        );
        return $this->dest;
    }

    /**
     * Gera as tags para o elemento: "enderDest" (Informações do Recebedor da Carga)
     * # = 175
     * Nível = 2
     * Os parâmetros para esta função são todos os elementos da tag "enderDest" do
     * tipo elemento (Ele = E|CE|A) e nível 3
     *
     * @return \DOMElement
     */
    public function tagenderDest($std)
    {

        $possible = [
            'xLgr',
            'nro',
            'xCpl',
            'xBairro',
            'cMun',
            'xMun',
            'CEP',
            'UF',
            'cPais',
            'xPais'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#185 <enderDest> - ';
        $this->enderDest = $this->dom->createElement('enderDest');
        $this->dom->addChild(
            $this->enderDest,
            'xLgr',
            $std->xLgr,
            true,
            $identificador . 'Logradouro'
        );
        $this->dom->addChild(
            $this->enderDest,
            'nro',
            $std->nro,
            true,
            $identificador . 'Número'
        );
        $this->dom->addChild(
            $this->enderDest,
            'xCpl',
            $std->xCpl,
            false,
            $identificador . 'Complemento'
        );
        $this->dom->addChild(
            $this->enderDest,
            'xBairro',
            $std->xBairro,
            true,
            $identificador . 'Bairro'
        );
        $this->dom->addChild(
            $this->enderDest,
            'cMun',
            $std->cMun,
            true,
            $identificador . 'Código do município (utilizar a tabela do IBGE)'
        );
        $this->dom->addChild(
            $this->enderDest,
            'xMun',
            $std->xMun,
            true,
            $identificador . 'Nome do município'
        );
        $this->dom->addChild(
            $this->enderDest,
            'CEP',
            $std->CEP,
            false,
            $identificador . 'CEP'
        );
        $this->dom->addChild(
            $this->enderDest,
            'UF',
            $std->UF,
            true,
            $identificador . 'Sigla da UF'
        );
        $this->dom->addChild(
            $this->enderDest,
            'cPais',
            $std->cPais,
            false,
            $identificador . 'Código do país'
        );
        $this->dom->addChild(
            $this->enderDest,
            'xPais',
            $std->xPais,
            false,
            $identificador . 'Nome do país'
        );
        $node = $this->dest->getElementsByTagName("email")->item(0);
        $this->dest->insertBefore($this->enderDest, $node);
        return $this->enderDest;
    }

    /**
     * Gera as tags para o elemento: "vPrest" (Valores da Prestação de Serviço)
     * #187
     * Nível: 1
     * Os parâmetros para esta função são todos os elementos da tag "vPrest" do
     * tipo elemento (Ele = E|CE|A) e nível 2
     *
     * @return \DOMElement
     */
    public function tagvPrest($std)
    {

        $possible = [
            'vTPrest',
            'vRec'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#208 <vPrest> - ';
        $this->vPrest = $this->dom->createElement('vPrest');
        $this->dom->addChild(
            $this->vPrest,
            'vTPrest',
            $std->vTPrest,
            true,
            $identificador . 'Valor Total da Prestação do Serviço'
        );
        $this->dom->addChild(
            $this->vPrest,
            'vRec',
            $std->vRec,
            true,
            $identificador . 'Valor a Receber'
        );
        return $this->vPrest;
    }

    /**
     * Gera as tags para o elemento: "Comp" (Componentes do Valor da Prestação)
     * #211
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "Comp" do
     * tipo elemento (Ele = E|CE|A) e nível 3
     *
     * @return \DOMElement
     */
    public function tagComp($std)
    {

        $possible = [
            'xNome',
            'vComp'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#65 <pass> - ';
        $this->comp[] = $this->dom->createElement('Comp');
        $posicao = (int)count($this->comp) - 1;
        $this->dom->addChild(
            $this->comp[$posicao],
            'xNome',
            $std->xNome,
            false,
            $identificador . 'Nome do componente'
        );
        $this->dom->addChild(
            $this->comp[$posicao],
            'vComp',
            $std->vComp,
            false,
            $identificador . 'Valor do componente'
        );
        return $this->comp[$posicao];
    }

    /**
     * tagICMS
     * Informações relativas ao ICMS
     * #194
     *
     * @return DOMElement
     */
    public function tagicms($std)
    {

        $possible = [
            'CST',
            'vBC',
            'pICMS',
            'vICMS',
            'pRedBC',
            'vBCSTRet',
            'vICMSSTRet',
            'pICMSSTRet',
            'vCred',
            'pRedBCOutraUF',
            'vBCOutraUF',
            'pICMSOutraUF',
            'vICMSOutraUF',
            'pRedBC',
            'vTotTrib',
            'infAdFisco',
            'vBCUFFim',
            'pFCPUFFim',
            'pICMSUFFim',
            'pICMSInter',
            'vFCPUFFim',
            'vICMSUFFim',
            'vICMSUFIni'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = 'N01 <ICMSxx> - ';
        switch ($std->cst) {
            case '00':
                $icms = $this->dom->createElement("ICMS00");
                $this->dom->addChild($icms, 'CST', $std->cst, true, "$identificador  Tributação do ICMS = 00");
                $this->dom->addChild($icms, 'vBC', $std->vBC, true, "$identificador  Valor da BC do ICMS");
                $this->dom->addChild($icms, 'pICMS', $std->pICMS, true, "$identificador  Alíquota do imposto");
                $this->dom->addChild($icms, 'vICMS', $std->vICMS, true, "$identificador  Valor do ICMS");
                break;
            case '20':
                $icms = $this->dom->createElement("ICMS20");
                $this->dom->addChild($icms, 'CST', $std->cst, true, "$identificador  Tributação do ICMS = 20");
                $this->dom->addChild(
                    $icms,
                    'pRedBC',
                    $std->pRedBC,
                    true,
                    "$identificador  Percentual da Redução de BC"
                );
                $this->dom->addChild($icms, 'vBC', $std->vBC, true, "$identificador  Valor da BC do ICMS");
                $this->dom->addChild($icms, 'pICMS', $std->pICMS, true, "$identificador  Alíquota do imposto");
                $this->dom->addChild($icms, 'vICMS', $std->vICMS, true, "$identificador  Valor do ICMS");
                break;
            case '40':
                $icms = $this->dom->createElement("ICMS45");
                $this->dom->addChild($icms, 'CST', $std->cst, true, "$identificador  Tributação do ICMS = 40");
                break;
            case '41':
                $icms = $this->dom->createElement("ICMS45");
                $this->dom->addChild($icms, 'CST', $std->cst, true, "$identificador  Tributação do ICMS = 41");
                break;
            case '51':
                $icms = $this->dom->createElement("ICMS45");
                $this->dom->addChild($icms, 'CST', $std->cst, true, "$identificador  Tributação do ICMS = 51");
                break;
            case '60':
                $icms = $this->dom->createElement("ICMS60");
                $this->dom->addChild($icms, 'CST', $std->cst, true, "$identificador  Tributação do ICMS = 60");
                $this->dom->addChild(
                    $icms,
                    'vBCSTRet',
                    $std->vBCSTRet,
                    true,
                    "$identificador  Valor BC do ICMS ST retido"
                );
                $this->dom->addChild(
                    $icms,
                    'vICMSSTRet',
                    $std->vICMSSTRet,
                    true,
                    "$identificador  Valor do ICMS ST retido"
                );
                $this->dom->addChild(
                    $icms,
                    'pICMSSTRet',
                    $std->pICMSSTRet,
                    true,
                    "$identificador  Valor do ICMS ST retido"
                );
                if ($std->vCred > 0) {
                    $this->dom->addChild($icms, 'vCred', $std->vCred, false, "$identificador  Valor do Crédito");
                }
                break;
            case '90':
                if ($std->outraUF == true) {
                    $icms = $this->dom->createElement("ICMSOutraUF");
                    $this->dom->addChild($icms, 'CST', $std->cst, true, "$identificador  Tributação do ICMS = 90");
                    if ($std->pRedBC > 0) {
                        $this->dom->addChild(
                            $icms,
                            'pRedBCOutraUF',
                            $std->pRedBCOutraUF,
                            false,
                            "$identificador Percentual Red "
                            . "BC Outra UF"
                        );
                    }
                    $this->dom->addChild($icms, 'vBCOutraUF', $std->vBC, true, "$identificador Valor BC ICMS Outra UF");
                    $this->dom->addChild($icms, 'pICMSOutraUF', $std->pICMS, true, "$identificador Alíquota do "
                        . "imposto Outra UF");
                    $this->dom->addChild(
                        $icms,
                        'vICMSOutraUF',
                        $std->vICMS,
                        true,
                        "$identificador Valor ICMS Outra UF"
                    );
                } else {
                    $icms = $this->dom->createElement("ICMS90");
                    $this->dom->addChild($icms, 'CST', $std->cst, true, "$identificador Tributação do ICMS = 90");
                    if ($std->pRedBC > 0) {
                        $this->dom->addChild(
                            $icms,
                            'pRedBC',
                            $std->pRedBC,
                            false,
                            "$identificador Percentual Redução BC"
                        );
                    }
                    $this->dom->addChild($icms, 'vBC', $std->vBC, true, "$identificador  Valor da BC do ICMS");
                    $this->dom->addChild($icms, 'pICMS', $std->pICMS, true, "$identificador  Alíquota do imposto");
                    $this->dom->addChild($icms, 'vICMS', $std->vICMS, true, "$identificador  Valor do ICMS");
                    if ($std->vCred > 0) {
                        $this->dom->addChild($icms, 'vCred', $std->vCred, false, "$identificador  Valor do Crédido");
                    }
                }
                break;
            case 'SN':
                $icms = $this->dom->createElement("ICMSSN");
                $this->dom->addChild($icms, 'CST', 90, true, "$identificador Tributação do ICMS = 90");
                $this->dom->addChild($icms, 'indSN', '1', true, "$identificador  Indica se contribuinte é SN");
                break;
        }
        $this->imp = $this->dom->createElement('imp');
        $tagIcms = $this->dom->createElement('ICMS');
        if (isset($icms)) {
            $this->imp->appendChild($tagIcms);
        }
        if (isset($icms)) {
            $tagIcms->appendChild($icms);
        }
        if ($std->vTotTrib > 0) {
            $this->dom->addChild(
                $this->imp,
                'vTotTrib',
                $std->vTotTrib,
                false,
                "$identificador Valor Total dos Tributos"
            );
        }
        if (isset($std->infAdFisco)) {
            $this->dom->addChild(
                $this->imp,
                'infAdFisco',
                $std->infAdFisco,
                false,
                "$identificador Informações adicionais de interesse do Fisco"
            );
        }

        if ($std->vICMSUFFim != '' || $std->vICMSUFIni != '') {
            $icmsDifal = $this->dom->createElement("ICMSUFFim");
            $this->dom->addChild(
                $icmsDifal,
                'vBCUFFim',
                $std->vBCUFFim,
                true,
                "$identificador Valor da BC do ICMS na UF
                de término da prestação do serviço de transporte"
            );
            $this->dom->addChild(
                $icmsDifal,
                'pFCPUFFim',
                $std->pFCPUFFim,
                true,
                "$identificador Percentual do ICMS
                relativo ao Fundo de Combate à pobreza (FCP) na UF de término da prestação do serviço de
                transporte"
            );
            $this->dom->addChild(
                $icmsDifal,
                'pICMSUFFim',
                $std->pICMSUFFim,
                true,
                "$identificador Alíquota interna da UF
                de término da prestação do serviço de transporte"
            );
            $this->dom->addChild(
                $icmsDifal,
                'pICMSInter',
                $std->pICMSInter,
                true,
                "$identificador Alíquota interestadual
                das UF envolvidas"
            );
            $this->dom->addChild(
                $icmsDifal,
                'vFCPUFFim',
                $std->vFCPUFFim,
                true,
                "$identificador Valor do ICMS relativo
                ao Fundo de Combate á Pobreza (FCP) da UF de término da prestação"
            );
            $this->dom->addChild(
                $icmsDifal,
                'vICMSUFFim',
                $std->vICMSUFFim,
                true,
                "$identificador Valor do ICMS de
                partilha para a UF de término da prestação do serviço de transporte"
            );
            $this->dom->addChild(
                $icmsDifal,
                'vICMSUFIni',
                $std->vICMSUFIni,
                true,
                "$identificador Valor do ICMS de
                partilha para a UF de início da prestação do serviço de transporte"
            );

            $this->imp->appendChild($icmsDifal);
        }

        return $tagIcms;
    }

    /**
     * tagInfTribFed
     * Informações do Impostos Federais
     * CTe OS
     * @return DOMElement
     */
    public function taginfTribFed($std)
    {

        $possible = [
            'vPIS',
            'vCOFINS',
            'vIR',
            'vINSS',
            'vCSLL'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = 'N02 <imp> - ';
        $tagInfTribFed = $this->dom->createElement('infTribFed');
        $this->dom->addChild($tagInfTribFed, 'vPIS', $std->vPIS, false, "$identificador  Valor de PIS");
        $this->dom->addChild($tagInfTribFed, 'vCOFINS', $std->vCOFINS, false, "$identificador  Valor de COFINS");
        $this->dom->addChild($tagInfTribFed, 'vIR', $std->vIR, false, "$identificador  Valor de IR");
        $this->dom->addChild($tagInfTribFed, 'vINSS', $std->vINSS, false, "$identificador  Valor de INSS");
        $this->dom->addChild($tagInfTribFed, 'vCSLL', $std->vCSLL, false, "$identificador  Valor de CSLL");
        $this->imp->appendChild($tagInfTribFed);
    }


    /**
     * Tag raiz do documento xml
     * Função chamada pelo método [ monta ]
     * @return \DOMElement
     */
    private function buildCTe()
    {
        if (empty($this->CTe)) {
            $this->CTe = $this->dom->createElement('CTe');
            $this->CTe->setAttribute('xmlns', 'http://www.portalfiscal.inf.br/cte');
        }
        return $this->CTe;
    }

    /**
     * Tag raiz do documento xml
     * Função chamada pelo método [ monta ]
     * @return \DOMElement
     */
    private function buildCTeOS()
    {
        if (empty($this->CTe)) {
            $this->CTe = $this->dom->createElement('CTeOS');
            $this->CTe->setAttribute('versao', '3.00');
            $this->CTe->setAttribute('xmlns', 'http://www.portalfiscal.inf.br/cte');
        }
        return $this->CTe;
    }

    /**
     * Gera as tags para o elemento: "Entrega" (Informações ref. a previsão de entrega)
     * #69
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "Entrega" do
     * tipo elemento (Ele = E|CE|A) e nível 3
     *
     * @return \DOMElement
     */
    private function tagEntrega()
    {
        if ($this->compl == '') {
            $this->compl = $this->dom->createElement('compl');
        }
        if ($this->entrega == '') {
            $this->entrega = $this->dom->createElement('Entrega');
            $this->dom->appChild($this->compl, $this->entrega, 'Falta tag "compl"');
        }
        return $this->entrega;
    }

    /**
     * #241
     * @return type
     */
    public function taginfCTeNorm()
    {
        $this->infCTeNorm = $this->dom->createElement('infCTeNorm');
        return $this->infCTeNorm;
    }

    /**
     * Gera as tags para o elemento: "infCarga" (Informações da Carga do CT-e)
     * #242
     * Nível: 2
     *
     * @return \DOMElement
     */
    public function taginfCarga($std)
    {

        $possible = [
            'vCarga',
            'proPred',
            'xOutCat'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#242 <infCarga> - ';
        $this->infCarga = $this->dom->createElement('infCarga');
        $this->dom->addChild(
            $this->infCarga,
            'vCarga',
            $std->vCarga,
            false,
            $identificador . 'Valor Total da Carga'
        );
        $this->dom->addChild(
            $this->infCarga,
            'proPred',
            $std->proPred,
            true,
            $identificador . 'Produto Predominante'
        );
        $this->dom->addChild(
            $this->infCarga,
            'xOutCat',
            $std->xOutCat,
            false,
            $identificador . 'Outras Caract. da Carga'
        );
        return $this->infCarga;
    }

    /**
     * Gera as tags para o elemento: "infCTeNorm" (Informações da Carga do CT-e OS)
     * #253
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "infServico"
     *
     * @return \DOMElement
     */
    public function taginfServico($std)
    {

        $possible = [
            'xDescServ'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#253 <infServico> - ';
        $this->infServico = $this->dom->createElement('infServico');
        $this->dom->addChild(
            $this->infServico,
            'xDescServ',
            $std->xDescServ,
            true,
            $identificador . 'Descrição do Serviço Prestado'
        );
        $infQ = $this->dom->createElement('infQ');
        $this->dom->addChild($infQ, 'qCarga', $std->qCarga, false, $identificador . 'Quantidade');
        $this->infServico->appendChild($infQ);
        return $this->infServico;
    }

    /**
     * Gera as tags para o elemento: "infQ" (Informações de quantidades da Carga do CT-e)
     * #246
     * Nível: 3
     * Os parâmetros para esta função são todos os elementos da tag "infQ"
     *
     * @return mixed
     */
    public function taginfQ($std)
    {

        $possible = [
            'cUnid',
            'tpMed',
            'qCarga',
            'vCargaAverb'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#257 <infQ> - ';
        $this->infQ[] = $this->dom->createElement('infQ');
        $posicao = (int)count($this->infQ) - 1;
        $this->dom->addChild(
            $this->infQ[$posicao],
            'cUnid',
            $std->cUnid,
            true,
            $identificador . 'Código da Unidade de Medida'
        );
        $this->dom->addChild(
            $this->infQ[$posicao],
            'tpMed',
            $std->tpMed,
            true,
            $identificador . 'Tipo da Medida'
        );
        $this->dom->addChild(
            $this->infQ[$posicao],
            'qCarga',
            $std->qCarga,
            true,
            $identificador . 'Quantidade'
        );
        $this->dom->addChild(
            $this->infQ[$posicao],
            'vCargaAverb',
            $std->vCargaAverb,
            false,
            $identificador . 'Valor da Carga para efeito de averbação'
        );
        return $this->infQ[$posicao];
    }

    private function taginfDoc()
    {
        if (empty($this->infDoc)) {
            $this->infDoc = $this->dom->createElement('infDoc');
        }
        return $this->infDoc;
    }

    /**
     * Documentos de Transporte Anterior
     * @return DOMElement|\DOMNode
     */
    public function tagdocAnt()
    {
        $this->docAnt = $this->dom->createElement('docAnt');
        return $this->docAnt;
    }

    /**
     * Informações de identificação dos documentos de Transporte Anterior
     * @return array|DOMElement
     */
    public function tagidDocAnt()
    {
        $this->idDocAnt[count($this->emiDocAnt) - 1] = $this->dom->createElement('idDocAnt');
        return $this->idDocAnt;
    }

    /**
     * Gera as tags para o elemento: "infNF" (Informações das NF)
     * #262
     * Nível: 3
     * @return mixed
     */
    public function taginfNF($std)
    {

        $possible = [
            'nRoma',
            'nPed',
            'mod',
            'serie',
            'nDoc',
            'dEmi',
            'vBC',
            'vICMS',
            'vBCST',
            'vST',
            'vProd',
            'vNF',
            'nCFOP',
            'nPeso',
            'PIN',
            'dPrev',
            'infUnidCarga',
            'infUnidTransp'
        ];
        $this->taginfDoc();
        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#262 <infNF> - ';
        $infNF = $this->dom->createElement('infNF');
        $this->dom->addChild(
            $infNF,
            'nRoma',
            $std->nRoma,
            false,
            $identificador . 'Número do Romaneio da NF'
        );
        $this->dom->addChild(
            $infNF,
            'nPed',
            $std->nPed,
            false,
            $identificador . 'Número do Pedido da NF'
        );
        $this->dom->addChild(
            $infNF,
            'mod',
            $std->mod,
            true,
            $identificador . 'Modelo da Nota Fiscal'
        );
        $this->dom->addChild(
            $infNF,
            'serie',
            $std->serie,
            true,
            $identificador . 'Série'
        );
        $this->dom->addChild(
            $infNF,
            'nDoc',
            $std->nDoc,
            true,
            $identificador . 'Número'
        );
        $this->dom->addChild(
            $infNF,
            'dEmi',
            $std->dEmi,
            true,
            $identificador . 'Data de Emissão'
        );
        $this->dom->addChild(
            $infNF,
            'vBC',
            $std->vBC,
            true,
            $identificador . 'Valor da Base de Cálculo do ICMS'
        );
        $this->dom->addChild(
            $infNF,
            'vICMS',
            $std->vICMS,
            true,
            $identificador . 'Valor Totaldo ICMS'
        );
        $this->dom->addChild(
            $infNF,
            'vBCST',
            $std->vBCST,
            true,
            $identificador . 'Valor da Base de Cálculo do ICMS ST'
        );
        $this->dom->addChild(
            $infNF,
            'vST',
            $std->vST,
            true,
            $identificador . 'Valor Total do ICMS ST'
        );
        $this->dom->addChild(
            $infNF,
            'vProd',
            $std->vProd,
            true,
            $identificador . 'Valor Total dos Produtos'
        );
        $this->dom->addChild(
            $infNF,
            'vNF',
            $std->vNF,
            true,
            $identificador . 'Valor Total da NF'
        );
        $this->dom->addChild(
            $infNF,
            'nCFOP',
            $std->nCFOP,
            true,
            $identificador . 'CFOP Predominante'
        );
        $this->dom->addChild(
            $infNF,
            'nPeso',
            $std->nPeso,
            false,
            $identificador . 'Peso total em Kg'
        );
        $this->dom->addChild(
            $infNF,
            'PIN',
            $std->PIN,
            false,
            $identificador . 'PIN SUFRAMA'
        );
        $this->dom->addChild(
            $infNF,
            'dPrev',
            $std->dPrev,
            false,
            $identificador . 'Data prevista de entrega'
        );
        if ($std->infUnidCarga) {
            foreach ($std->infUnidCarga as $value) {
                $this->dom->appChild($infNF, $this->taginfUnidCarga($value), 'Falta tag "infUnidCarga"');
            }
        }
        if ($std->infUnidTransp) {
            foreach ($std->infUnidTransp as $value) {
                $this->dom->appChild($infNF, $this->taginfUnidTransp($value), 'Falta tag "infUnidTransp"');
            }
        }
        $this->infNF[] = $infNF;
        return $infNF;
    }

    /**
     * Gera as tags para o elemento: "infNFe" (Informações das NF-e)
     * #297
     * Nível: 3
     * @return mixed
     */
    public function taginfNFe($std)
    {

        $possible = [
            'chave',
            'PIN',
            'dPrev',
            'infUnidCarga',
            'infUnidTransp'
        ];
        $this->taginfDoc();
        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#297 <infNFe> - ';
        $infNFe = $this->dom->createElement('infNFe');
        $this->dom->addChild(
            $infNFe,
            'chave',
            $std->chave,
            true,
            $identificador . 'Chave de acesso da NF-e'
        );
        $this->dom->addChild(
            $infNFe,
            'PIN',
            $std->PIN,
            false,
            $identificador . 'PIN SUFRAMA'
        );
        $this->dom->addChild(
            $infNFe,
            'dPrev',
            $std->dPrev,
            false,
            $identificador . 'Data prevista de entrega'
        );
        if ($std->infUnidCarga) {
            foreach ($std->infUnidCarga as $value) {
                $this->dom->appChild($infNFe, $this->taginfUnidCarga($value), 'Falta tag "infUnidCarga"');
            }
        }
        if ($std->infUnidTransp) {
            foreach ($std->infUnidTransp as $value) {
                $this->dom->appChild($infNFe, $this->taginfUnidTransp($value), 'Falta tag "infUnidTransp"');
            }
        }
        $this->infNFe[] = $infNFe;
        return $infNFe;
    }

    /**
     * Gera as tags para o elemento: "infOutros" (Informações dos demais documentos)
     * #319
     * Nível: 3
     * @return mixed
     */
    public function taginfOutros($std)
    {

        $possible = [
            'tpDoc',
            'descOutros',
            'nDoc',
            'dEmi',
            'vDocFisc',
            'dPrev',
            'infUnidCarga',
            'infUnidTransp'
        ];
        $this->taginfDoc();
        $std = $this->equilizeParameters($std, $possible);

        $ident = '#319 <infOutros> - ';
        $infOutros = $this->dom->createElement('infOutros');
        $this->dom->addChild(
            $infOutros,
            'tpDoc',
            $std->tpDoc,
            true,
            $ident . 'Tipo de documento originário'
        );
        $this->dom->addChild(
            $infOutros,
            'descOutros',
            $std->descOutros,
            false,
            $ident . 'Descrição do documento'
        );
        $this->dom->addChild(
            $infOutros,
            'nDoc',
            $std->nDoc,
            false,
            $ident . 'Número do documento'
        );
        $this->dom->addChild(
            $infOutros,
            'dEmi',
            $std->dEmi,
            false,
            $ident . 'Data de Emissão'
        );
        $this->dom->addChild(
            $infOutros,
            'vDocFisc',
            $std->vDocFisc,
            false,
            $ident . 'Valor do documento'
        );
        $this->dom->addChild(
            $infOutros,
            'dPrev',
            $std->dPrev,
            false,
            $ident . 'Data ista de entrega'
        );
        if ($std->infUnidCarga) {
            foreach ($std->infUnidCarga as $value) {
                $this->dom->appChild($infOutros, $this->taginfUnidCarga($value), 'Falta tag "infUnidCarga"');
            }
        }
        if ($std->infUnidTransp) {
            foreach ($std->infUnidTransp as $value) {
                $this->dom->appChild($infOutros, $this->taginfUnidTransp($value), 'Falta tag "infUnidTransp"');
            }
        }
        $this->infOutros[] = $infOutros;
        return $infOutros;
    }

    /**
     * taginfUnidCarga
     * tag CTe/infCte/infDoc/(infNF/infNFe/infOutros)/infUnidCarga
     *
     * @param  stdClass $std
     * @return DOMElement
     */
    private function taginfUnidCarga(stdClass $std)
    {
        $possible = [
            'tpUnidCarga',
            'idUnidCarga',
            'lacUnidCarga',
            'qtdRat'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $infUnidCarga = $this->dom->createElement("infUnidCarga");
        $this->dom->addChild(
            $infUnidCarga,
            "tpUnidCarga",
            $std->tpUnidCarga,
            false,
            "Tipo da Unidade de Carga"
        );
        $this->dom->addChild(
            $infUnidCarga,
            "idUnidCarga",
            $std->idUnidCarga,
            false,
            "Identificação da Unidade de Carga "
        );
        if ($std->lacUnidCarga != null) {
            $possible = [
                'nLacre'
            ];
            $stdlacUnidCarga = $this->equilizeParameters($std->lacUnidCarga, $possible);
            foreach ($stdlacUnidCarga->nLacre as $nLacre) {
                $lacUnidCarga = $this->dom->createElement("lacUnidCarga");
                $this->dom->addChild(
                    $lacUnidCarga,
                    "nLacre",
                    $nLacre,
                    true,
                    "Número do lacre"
                );
                $this->dom->appChild($infUnidCarga, $lacUnidCarga, 'Falta tag "infUnidCarga"');
            }
        }
        $this->dom->addChild(
            $infUnidCarga,
            "qtdRat",
            $std->qtdRat,
            false,
            "Quantidade rateada (Peso,Volume)"
        );
        return $infUnidCarga;
    }

    /**
     * taginfUnidTransp
     * tag CTe/infCte/infDoc/(infNF/infNFe/infOutros)/infUnidTransp
     *
     * @param  stdClass $std
     * @return DOMElement
     */
    private function taginfUnidTransp(stdClass $std)
    {
        $possible = [
            'tpUnidTransp',
            'idUnidTransp',
            'qtdRat',
            'lacUnidTransp',
            'infUnidCarga'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $infUnidTransp = $this->dom->createElement("infUnidTransp");
        $this->dom->addChild(
            $infUnidTransp,
            "tpUnidTransp",
            $std->tpUnidTransp,
            true,
            "Tipo da Unidade de Transporte"
        );
        $this->dom->addChild(
            $infUnidTransp,
            "idUnidTransp",
            $std->idUnidTransp,
            false,
            "Identificação da Unidade de Transporte"
        );
        if ($std->lacUnidTransp != null) {
            $possible = [
                'nLacre'
            ];
            $stdlacUnidTransp = $this->equilizeParameters($std->lacUnidTransp, $possible);
            foreach ($stdlacUnidTransp->nLacre as $nLacre) {
                $lacUnidTransp = $this->dom->createElement("lacUnidTransp");
                $this->dom->addChild(
                    $lacUnidTransp,
                    "nLacre",
                    $nLacre,
                    true,
                    "Número do lacre"
                );
                $this->dom->appChild($infUnidTransp, $lacUnidTransp, 'Falta tag "infUnidTransp"');
            }
        }
        if ($std->infUnidCarga) {
            foreach ($std->infUnidCarga as $value) {
                $this->dom->appChild($infUnidTransp, $this->taginfUnidCarga($value), 'Falta tag "infUnidCarga"');
            }
        }
        $this->dom->addChild(
            $infUnidTransp,
            "qtdRat",
            $std->qtdRat,
            false,
            "Quantidade rateada (Peso,Volume) "
        );
        return $infUnidTransp;
    }

    /**
     * Gera as tags para o elemento: "infDocRef" (Informações dos demais documentos)
     * #319
     * Nível: 3
     * @return mixed
     */
    public function taginfDocRef($std)
    {

        $possible = [
            'nDoc',
            'serie',
            'subserie',
            'dEmi',
            'vDoc'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $ident = '#319 <infDocRef> - ';
        $this->infDocRef[] = $this->dom->createElement('infDocRef');
        $posicao = (int)count($this->infDocRef) - 1;
        $this->dom->addChild(
            $this->infDocRef[$posicao],
            'nDoc',
            $std->nDoc,
            false,
            $ident . 'Número do documento'
        );
        $this->dom->addChild(
            $this->infDocRef[$posicao],
            'serie',
            $std->serie,
            false,
            $ident . 'Série do documento'
        );
        $this->dom->addChild(
            $this->infDocRef[$posicao],
            'subserie',
            $std->subserie,
            false,
            $ident . 'Subserie do documento'
        );
        $this->dom->addChild(
            $this->infDocRef[$posicao],
            'dEmi',
            $std->dEmi,
            false,
            $ident . 'Data de Emissão'
        );
        $this->dom->addChild(
            $this->infDocRef[$posicao],
            'vDoc',
            $std->vDoc,
            false,
            $ident . 'Valor do documento'
        );
        return $this->infDocRef[$posicao];
    }

    /**
     * Gera as tags para o elemento: "emiDocAnt" (Informações dos CT-es Anteriores)
     * #345
     * Nível: 3
     * @return mixed
     */
    public function tagemiDocAnt($std)
    {

        $possible = [
            'CNPJ',
            'IE',
            'UF',
            'CPF',
            'xNome'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#345 <emiDocAnt> - ';
        $this->emiDocAnt[] = $this->dom->createElement('emiDocAnt');
        $posicao = (int)count($this->emiDocAnt) - 1;
        if ($std->CNPJ != '') {
            $this->dom->addChild(
                $this->emiDocAnt[$posicao],
                'CNPJ',
                $std->CNPJ,
                true,
                $identificador . 'Número do CNPJ'
            );
            $this->dom->addChild(
                $this->emiDocAnt[$posicao],
                'IE',
                Strings::onlyNumbers($std->IE),
                true,
                $identificador . 'Inscrição Estadual'
            );
            $this->dom->addChild(
                $this->emiDocAnt[$posicao],
                'UF',
                $std->UF,
                true,
                $identificador . 'Sigla da UF'
            );
        } else {
            $this->dom->addChild(
                $this->emiDocAnt[$posicao],
                'CPF',
                $std->CPF,
                true,
                $identificador . 'Número do CPF'
            );
        }
        $this->dom->addChild(
            $this->emiDocAnt[$posicao],
            'xNome',
            $std->xNome,
            true,
            $identificador . 'Razão Social ou Nome do Expedidor'
        );
        return $this->emiDocAnt[$posicao];
    }

    /**
     * Gera as tags para o elemento: "idDocAntEle" (Informações dos CT-es Anteriores)
     * #348
     * Nível: 4
     * @return mixed
     */
    public function tagidDocAntEle($std)
    {

        $possible = [
            'chCTe'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#358 <idDocAntEle> - ';
        $this->idDocAntEle[count($this->emiDocAnt) - 1][] = $this->dom->createElement('idDocAntEle');
        $posicao = (int)count($this->idDocAntEle[count($this->emiDocAnt) - 1]) - 1;
        $this->dom->addChild(
            $this->idDocAntEle[count($this->emiDocAnt) - 1][$posicao],
            'chCTe',
            $std->chCTe,
            true,
            $identificador . 'Chave de Acesso do CT-e'
        );
        return $this->idDocAntEle[count($this->emiDocAnt) - 1][$posicao];
    }


    /**
     * Gera as tags para o elemento: "seg" (Informações de Seguro da Carga)
     * #360
     * Nível: 2
     * @return mixed
     */
    public function tagseg($std)
    {

        $possible = [
            'respSeg',
            'xSeg',
            'nApol'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#360 <seg> - ';
        $this->seg[] = $this->dom->createElement('seg');
        $posicao = (int)count($this->seg) - 1;
        $this->dom->addChild(
            $this->seg[$posicao],
            'respSeg',
            $std->respSeg,
            true,
            $identificador . 'Responsável pelo Seguro'
        );
        $this->dom->addChild(
            $this->seg[$posicao],
            'xSeg',
            $std->xSeg,
            false,
            $identificador . 'Nome da Seguradora'
        );
        $this->dom->addChild(
            $this->seg[$posicao],
            'nApol',
            $std->nApol,
            false,
            $identificador . 'Número da Apólice'
        );
        return $this->seg[$posicao];
    }

    /**
     * Gera as tags para o elemento: "idDocAntEle" (Informações dos CT-es Anteriores)
     * #348
     * Nível: 4
     * @return mixed
     */
    public function tagidDocAntPap($std)
    {

        $possible = [
            'tpDoc',
            'serie',
            'subser',
            'nDoc',
            'dEmi'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#358 <idDocAntPap> - ';
        $this->idDocAntPap[count($this->emiDocAnt) - 1][] = $this->dom->createElement('idDocAntPap');
        $posicao = (int)count($this->idDocAntPap[count($this->emiDocAnt) - 1]) - 1;
        $this->dom->addChild(
            $this->idDocAntPap[count($this->emiDocAnt) - 1][$posicao],
            'tpDoc',
            $std->tpDoc,
            true,
            $identificador . 'Tipo do Documento de Transporte Anterior'
        );
        $this->dom->addChild(
            $this->idDocAntPap[count($this->emiDocAnt) - 1][$posicao],
            'serie',
            $std->serie,
            true,
            $identificador . 'Série do Documento Fiscal'
        );
        $this->dom->addChild(
            $this->idDocAntPap[count($this->emiDocAnt) - 1][$posicao],
            'subser',
            $std->subser,
            false,
            $identificador . 'Série do Documento Fiscal'
        );
        $this->dom->addChild(
            $this->idDocAntPap[count($this->emiDocAnt) - 1][$posicao],
            'nDoc',
            $std->nDoc,
            true,
            $identificador . 'Número do Documento Fiscal'
        );
        $this->dom->addChild(
            $this->idDocAntPap[count($this->emiDocAnt) - 1][$posicao],
            'dEmi',
            $std->dEmi,
            true,
            $identificador . 'Data de emissão (AAAA-MM-DD)'
        );
        return $this->idDocAntPap[count($this->emiDocAnt) - 1][$posicao];
    }


    /**
     * Gera as tags para o elemento: "infModal" (Informações do modal)
     * #366
     * Nível: 2
     * @param string $versaoModal
     * @return DOMElement|\DOMNode
     */
    public function taginfModal($std)
    {
        $this->infModal = $this->dom->createElement('infModal');
        $this->infModal->setAttribute('versaoModal', $std->versaoModal);
        return $this->infModal;
    }

    /**
     * Leiaute - Rodoviário
     * Gera as tags para o elemento: "rodo" (Informações do modal Rodoviário)
     * #1
     * Nível: 0
     * @return DOMElement|\DOMNode
     */
    public function tagrodo($std)
    {

        $possible = [
            'RNTRC'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#1 <rodo> - ';
        $this->rodo = $this->dom->createElement('rodo');
        $this->dom->addChild(
            $this->rodo,
            'RNTRC',
            $std->RNTRC,
            true,
            $identificador . 'Registro nacional de transportadores
            rodoviários de carga'
        );
        return $this->rodo;
    }

    /**
     * Leiaute - Dutoviário
     * Gera as tags para o elemento: "duto" (informações do modal Dutoviário)
     * @author Uilasmar Guedes
     * #1
     * Nivel: 0
     * @return DOMElement|\DOMNode
     */
    public function tagduto($std)
    {

        $possible = [
            'vTar',
            'dIni',
            'dFim'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#1 <duto> - ';
        $this->duto = $this->dom->createElement('duto');
        $this->dom->addChild(
            $this->duto,
            'vTar',
            $std->vTar,
            false,
            $identificador . 'Valor da tarifa '
        );
        $this->dom->addChild(
            $this->duto,
            'dIni',
            $std->dIni,
            true,
            $identificador . 'Data de Início da prestação do serviço'
        );
        $this->dom->addChild(
            $this->duto,
            'dFim',
            $std->dFim,
            true,
            $identificador . 'Data de Fim da prestação do serviço'
        );
        return $this->duto;
    }

    /**
     * Leiaute - Aquaviario
     * Gera as tags para o elemento: "aquav" (informações do modal Aquaviario)
     * @author Anderson Minuto Consoni Vaz
     * #1
     * Nivel: 0
     * @return DOMElement|\DOMNode
     */
    public function tagaquav($std)
    {

        $possible = [
            'vPrest',
            'vAFRMM',
            'xNavio',
            'nViag',
            'direc',
            'irin',
            'tpNav'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#1 <aquav> - ';
        $this->aquav = $this->dom->createElement('aquav');
        $this->dom->addChild(
            $this->aquav,
            'vPrest',
            $std->vPrest,
            true,
            $identificador . 'vPrest'
        );
        $this->dom->addChild(
            $this->aquav,
            'vAFRMM',
            $std->vAFRMM,
            true,
            $identificador . 'vAFRMM'
        );
        $this->dom->addChild(
            $this->aquav,
            'xNavio',
            $std->xNavio,
            true,
            $identificador . 'xNavio'
        );
        $this->dom->addChild(
            $this->aquav,
            'nViag',
            $std->nViag,
            true,
            $identificador . 'nViag'
        );
        $this->dom->addChild(
            $this->aquav,
            'direc',
            $std->direc,
            true,
            $identificador . 'direc'
        );
        $this->dom->addChild(
            $this->aquav,
            'irin',
            $std->irin,
            true,
            $identificador . 'irin'
        );
        $this->dom->addChild(
            $this->aquav,
            'tpNav',
            $std->tpNav,
            false,
            $identificador . 'tpNav'
        );
        return $this->aquav;
    }

    /**
     * Leiaute - Rodoviário
     * Gera as tags para o elemento: "rodo" (Informações do modal Rodoviário) CT-e OS
     * #1
     * Nível: 0
     * @return DOMElement|\DOMNode
     */
    public function tagrodoOS($std)
    {

        $possible = [
            'vPrest',
            'vAFRMM',
            'xNavio',
            'nViag',
            'direc',
            'irin',
            'tpNav'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#1 <rodoOS> - ';
        $this->rodo = $this->dom->createElement('rodoOS');
        $this->dom->addChild(
            $this->rodo,
            'TAF',
            $std->TAF,
            false,
            $identificador . 'Termo de Autorização de Fretamento - TAF'
        );
        $this->dom->addChild(
            $this->rodo,
            'NroRegEstadual',
            $std->nroRegEstadual,
            false,
            $identificador . 'Número do Registro Estadual'
        );
        return $this->rodo;
    }

    /**
     * Leiaute - Aéreo
     * Gera as tags para o elemento: "aereo" (Informações do modal Aéreo)
     * @author Newton Pasqualini Filho
     * #1
     * Nível: 0
     * @return DOMElement|\DOMNode
     */
    public function tagaereo($std)
    {

        $possible = [
            'nMinu',
            'nOCA',
            'dPrevAereo',
            'natCarga_xDime',
            'natCarga_cInfManu',
            'tarifa_CL',
            'tarifa_cTar',
            'tarifa_vTar'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#1 <aereo> - ';
        $this->aereo = $this->dom->createElement('aereo');
        $this->dom->addChild(
            $this->aereo,
            'nMinu',
            $std->nMinu,
            false,
            $identificador . 'Número da Minuta'
        );
        $this->dom->addChild(
            $this->aereo,
            'nOCA',
            $std->nOCA,
            false,
            $identificador . 'Número Operacional do Conhecimento Aéreo'
        );
        $this->dom->addChild(
            $this->aereo,
            'dPrevAereo',
            $std->dPrevAereo,
            true,
            $identificador . 'Data prevista da entrega'
        );
        if (isset($std->natCarga_xDime) || isset($std->natCarga_cInfManu)) {
            $identificador = '#1 <aereo> - <natCarga> - ';
            $this->natCarga = $this->dom->createElement('natCarga');
            $this->dom->addChild(
                $this->natCarga,
                'xDime',
                $std->natCarga_xDime,
                false,
                $identificador . 'Dimensões da carga, formato: 1234x1234x1234 (cm)'
            );
            if (isset($std->natCarga_cInfManu) && !is_array($std->natCarga_cInfManu)) {
                $std->natCarga_cInfManu = [$std->natCarga_cInfManu];
            }
            $cInfManuX = 0;
            foreach ($std->natCarga_cInfManu as $cInfManu) {
                $cInfManuX++;
                $this->dom->addChild(
                    $this->natCarga,
                    'cInfManu',
                    $cInfManu,
                    false,
                    $identificador . 'Informação de manuseio, com dois dígitos, pode ter mais de uma ocorrência.'
                );
            }
            $this->aereo->appendChild($this->natCarga);
        }
        $identificador = '#1 <aereo> - <tarifa> - ';
        $this->tarifa = $this->dom->createElement('tarifa');
        $this->dom->addChild(
            $this->tarifa,
            'CL',
            $std->tarifa_CL,
            true,
            $identificador . 'Classe da tarifa: M - Tarifa Mínima / G - Tarifa Geral / E - Tarifa Específica'
        );
        $this->dom->addChild(
            $this->tarifa,
            'cTar',
            $std->tarifa_cTar,
            false,
            $identificador . 'Código de três digítos correspondentes à tarifa.'
        );
        $this->dom->addChild(
            $this->tarifa,
            'vTar',
            $std->tarifa_vTar,
            true,
            $identificador . 'Valor da tarifa. 15 posições, sendo 13 inteiras e 2 decimais.'
        );
        $this->aereo->appendChild($this->tarifa);
        return $this->aereo;
    }

    /**
     * CT-e de substituição
     * @return type
     */
    public function taginfCteSub($std)
    {

        $possible = [
            'chCte',
            'refCteAnu'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#149 <infCteSub> - ';
        $this->infCteSub = $this->dom->createElement('infCteSub');
        $this->dom->addChild(
            $this->infCteSub,
            'chCte',
            $std->chCte,
            false,
            "$identificador  Chave de acesso do CTe a ser substituído (original)"
        );
        $this->dom->addChild(
            $this->infCteSub,
            'refCteAnu',
            $std->refCteAnu,
            false,
            "$identificador  Chave de acesso do CT-e de Anulação"
        );
        return $this->infCteSub;
    }


    /**
     * CT-e de substituição - tomaICMS
     * @param type $std
     * @return type
     */
    public function tagtomaICMS()
    {
        $this->tomaICMS = $this->dom->createElement('tomaICMS');
        return $this->tomaICMS;
    }

    /**
     * CT-e de substituição - NF-e
     * @param type $std
     * @return type
     */
    public function tagrefNFe($std)
    {

        $possible = [
            'refNFe'
        ];

        $std = $this->equilizeParameters($std, $possible);

        if (empty($this->tomICMS)) {
            $this->tomaICMS = $this->dom->createElement('tomaICMS');
        }
        $identificador = '#153 <refNFe> - ';
        $this->dom->addChild(
            $this->tomaICMS,
            'refNFe',
            $std->refNFe,
            false,
            "$identificador  Chave de acesso da NF-e emitida pelo tomador"
        );

        return $this->tomaICMS;
    }

    /**
     * CT-e de substituição - NF
     * @param type $std
     * @return type
     */
    public function tagrefNF($std)
    {

        $possible = [
            'CNPJ',
            'CPF',
            'mod',
            'serie',
            'subserie',
            'nro',
            'valor',
            'dEmi'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#154 <refNFe> - ';
        if (empty($this->tomICMS)) {
            $this->tomaICMS = $this->dom->createElement('tomaICMS');
        }
        $this->refNF = $this->dom->createElement('refNF');
        if ($std->CNPJ != '') {
            $this->dom->addChild(
                $this->refNF,
                'CNPJ',
                $std->CNPJ,
                true,
                $identificador . 'CNPJ do emitente'
            );
        } elseif ($std->CPF != '') {
            $this->dom->addChild(
                $this->refNF,
                'CPF',
                $std->CPF,
                true,
                $identificador . 'CPF do emitente'
            );
        }
        $this->dom->addChild(
            $this->refNF,
            'mod',
            $std->mod,
            false,
            $identificador . 'Modelo'
        );
        $this->dom->addChild(
            $this->refNF,
            'serie',
            $std->serie,
            false,
            $identificador . 'Série do documento'
        );
        $this->dom->addChild(
            $this->refNF,
            'subserie',
            $std->subserie,
            false,
            $identificador . 'Subserie do documento'
        );
        $this->dom->addChild(
            $this->refNF,
            'nro',
            $std->nro,
            false,
            $identificador . 'Número'
        );
        $this->dom->addChild(
            $this->refNF,
            'valor',
            $std->valor,
            false,
            $identificador . 'Valor'
        );
        $this->dom->addChild(
            $this->refNF,
            'dEmi',
            $std->dEmi,
            false,
            $identificador . 'Emissão'
        );
        $this->tomaICMS->appendChild($this->refNF);
        return $this->tomaICMS;
    }

    /**
     * CT-e de substituição - CT-e
     * @param type $std
     * @return type
     */
    public function tagrefCTe($std)
    {

        $possible = [
            'refCTe'
        ];

        $std = $this->equilizeParameters($std, $possible);

        if (empty($this->tomICMS)) {
            $this->tomaICMS = $this->dom->createElement('tomaICMS');
        }
        $identificador = '#163 <refCTe> - ';
        $this->dom->addChild(
            $this->tomaICMS,
            'refCTe',
            $std->refCTe,
            false,
            "$identificador  Chave de acesso do CT-e emitida pelo tomador"
        );

        return $this->tomaICMS;
    }

    /**
     * Leiaute - Rodoviário
     * Gera as tags para o elemento: "veic" (Dados dos Veículos)
     * #21
     * Nível: 1
     * @return mixed
     */
    public function tagveicCTeOS($std)
    {

        $possible = [
            'placa',
            'RENAVAM',
            'CNPJ',
            'CPF',
            'TAF',
            'NroRegEstadual',
            'xNome',
            'IE',
            'ufProp',
            'tpProp',
            'uf'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#21 <veic> - ';
        $this->veic = $this->dom->createElement('veic');
        $this->dom->addChild(
            $this->veic,
            'placa',
            $std->placa,
            false,
            $identificador . 'Placa do veículo'
        );
        $this->dom->addChild(
            $this->veic,
            'RENAVAM',
            $std->RENAVAM,
            false,
            $identificador . 'RENAVAM do veículo'
        );
        if ($std->xNome != '') { // CASO FOR VEICULO DE TERCEIRO
            $this->prop = $this->dom->createElement('prop');
            if ($std->CNPJ != '') {
                $this->dom->addChild(
                    $this->prop,
                    'CNPJ',
                    $std->CNPJ,
                    true,
                    $identificador . 'CNPJ do proprietario'
                );
            } elseif ($std->CPF != '') {
                $this->dom->addChild(
                    $this->prop,
                    'CPF',
                    $std->CPF,
                    true,
                    $identificador . 'CPF do proprietario'
                );
            }
            if ($std->taf != '') {
                $this->dom->addChild(
                    $this->prop,
                    'TAF',
                    $std->taf,
                    false,
                    $identificador . 'TAF'
                );
            } else {
                $this->dom->addChild(
                    $this->prop,
                    'NroRegEstadual',
                    $std->nroRegEstadual,
                    false,
                    $identificador . 'Número do Registro Estadual'
                );
            }
            $this->dom->addChild(
                $this->prop,
                'xNome',
                $std->xNome,
                true,
                $identificador . 'Nome do proprietario'
            );
            $this->dom->addChild(
                $this->prop,
                'IE',
                Strings::onlyNumbers($std->IE),
                false,
                $identificador . 'IE do proprietario'
            );
            $this->dom->addChild(
                $this->prop,
                'UF',
                $std->ufProp,
                true,
                $identificador . 'UF do proprietario'
            );
            $this->dom->addChild(
                $this->prop,
                'tpProp',
                $std->tpProp,
                true,
                $identificador . 'Tipo Proprietário'
            );
            $this->dom->appChild($this->veic, $this->prop, 'Falta tag "prop"');
        }
        $this->dom->addChild(
            $this->veic,
            'UF',
            $std->uf,
            true,
            $identificador . 'UF em que veículo está licenciado'
        );
        return $this->veic;
    }

    public function infFretamento($std)
    {

        $possible = [
            'tpFretamento',
            'dhViagem'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#21 <infFretamento> - ';
        $this->infFretamento = $this->dom->createElement('infFretamento');
        $this->dom->addChild(
            $this->infFretamento,
            'tpFretamento',
            $std->tpFretamento,
            true,
            $identificador . 'Tipo do Fretamento de Pessoas'
        );
        $this->dom->addChild(
            $this->infFretamento,
            'dhViagem',
            $std->dhViagem,
            false,
            $identificador . 'Data e hora da viagem'
        );
        return $this->infFretamento;
    }

    /**
     * Gera as tags para o elemento: "infCteComp" (Detalhamento do CT-e complementado)
     * #410
     * Nível: 1
     * @return DOMElement|\DOMNode
     */
    public function taginfCTeComp($std)
    {

        $possible = [
            'chCTe'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#410 <infCteComp> - ';
        $this->infCteComp = $this->dom->createElement('infCteComp');
        $this->dom->addChild(
            $this->infCteComp,
            'chCTe',
            $std->chCTe,
            true,
            $identificador . ' Chave do CT-e complementado'
        );
        return $this->infCteComp;
    }

    /**
     * Gera as tags para o elemento: "infCteAnu" (Detalhamento do CT-e de Anulação)
     * #411
     * Nível: 1
     * @return DOMElement|\DOMNode
     */
    public function taginfCteAnu($std)
    {

        $possible = [
            'chave',
            'dEmi'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#411 <infCteAnu> - ';
        $this->infCteAnu = $this->dom->createElement('infCteAnu');
        $this->dom->addChild(
            $this->infCteAnu,
            'chCte',
            $std->chave,
            true,
            $identificador . ' Chave do CT-e anulado'
        );
        $this->dom->addChild(
            $this->infCteAnu,
            'dEmi',
            $std->data,
            true,
            $identificador . ' Data de Emissão do CT-e anulado'
        );
        return $this->infCteAnu;
    }

    /**
     * Gera as tags para o elemento: "autXML" (Autorizados para download do XML)
     * #396
     * Nível: 1
     * Os parâmetros para esta função são todos os elementos da tag "autXML"
     *
     * @return boolean
     */
    public function tagveicNovos($std)
    {

        $possible = [
            'chassi',
            'cCor',
            'xCor',
            'cMod',
            'vUnit',
            'vFrete'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#396 <veicNovos> - ';
        $veicNovos = $this->dom->createElement('veicNovos');
        $this->dom->addChild(
            $veicNovos,
            'chassi',
            $std->chassi,
            true,
            $identificador . 'Chassi do veículo '
        );
        $this->dom->addChild(
            $veicNovos,
            'cCor',
            $std->cCor,
            true,
            $identificador . 'Cor do veículo '
        );
        $this->dom->addChild(
            $veicNovos,
            'xCor',
            $std->xCor,
            true,
            $identificador . 'Descrição da cor '
        );
        $this->dom->addChild(
            $veicNovos,
            'cMod',
            $std->cMod,
            true,
            $identificador . 'Código Marca Modelo '
        );
        $this->dom->addChild(
            $veicNovos,
            'vUnit',
            $std->vUnit,
            true,
            $identificador . 'Valor Unitário do Veículo '
        );
        $this->dom->addChild(
            $veicNovos,
            'vFrete',
            $std->vFrete,
            true,
            $identificador . 'Frete Unitário '
        );
        $this->veicNovos[] = $veicNovos;
        return $veicNovos;
    }

    /**
     * Gera as tags para o elemento: "autXML" (Autorizados para download do XML)
     * #396
     * Nível: 1
     * Os parâmetros para esta função são todos os elementos da tag "autXML"
     *
     * @return boolean
     */
    public function tagautXML($std)
    {

        $possible = [
            'CNPJ',
            'CPF'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $identificador = '#396 <autXML> - ';
        $autXML = $this->dom->createElement('autXML');
        if (isset($std->CNPJ) && $std->CNPJ != '') {
            $this->dom->addChild(
                $autXML,
                'CNPJ',
                $std->CNPJ,
                true,
                $identificador . 'CNPJ do Cliente Autorizado'
            );
        } elseif (isset($std->CPF) && $std->CPF != '') {
            $this->dom->addChild(
                $autXML,
                'CPF',
                $std->CPF,
                true,
                $identificador . 'CPF do Cliente Autorizado'
            );
        }
        $this->autXML[] = $autXML;
        return $autXML;
    }

    /**
     * #359
     * tag CTe/infCTe/cobr (opcional)
     * Depende de fat
     */
    protected function buildCobr()
    {
        if (empty($this->cobr)) {
            $this->cobr = $this->dom->createElement("cobr");
        }
    }

    /**
     * #360
     * tag CTe/infCTe/cobr/fat (opcional)
     * @param stdClass $std
     * @return DOMElement
     */
    public function tagfat(stdClass $std)
    {

        $possible = [
            'nFat',
            'vOrig',
            'vDesc',
            'vLiq'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $this->buildCobr();
        $fat = $this->dom->createElement("fat");
        $this->dom->addChild(
            $fat,
            "nFat",
            $std->nFat,
            false,
            "Número da Fatura"
        );
        $this->dom->addChild(
            $fat,
            "vOrig",
            $std->vOrig,
            false,
            "Valor Original da Fatura"
        );
        $this->dom->addChild(
            $fat,
            "vDesc",
            $std->vDesc,
            false,
            "Valor do desconto"
        );
        $this->dom->addChild(
            $fat,
            "vLiq",
            $std->vLiq,
            false,
            "Valor Líquido da Fatura"
        );
        $this->dom->appChild($this->cobr, $fat);
        return $fat;
    }

    /**
     * #365
     * tag CTe/infCTe/cobr/fat/dup (opcional)
     * É necessário criar a tag fat antes de criar as duplicatas
     * @param stdClass $std
     * @return DOMElement
     */
    public function tagdup(stdClass $std)
    {

        $possible = [
            'nDup',
            'dVenc',
            'vDup'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $this->buildCobr();
        $dup = $this->dom->createElement("dup");
        $this->dom->addChild(
            $dup,
            "nDup",
            $std->nDup,
            false,
            "Número da Duplicata"
        );
        $this->dom->addChild(
            $dup,
            "dVenc",
            $std->dVenc,
            false,
            "Data de vencimento"
        );
        $this->dom->addChild(
            $dup,
            "vDup",
            $std->vDup,
            true,
            "Valor da duplicata"
        );
        $this->dom->appChild($this->cobr, $dup, 'Inclui duplicata na tag cobr');
        return $dup;
    }

    /**
     * Informações do Responsável técnico
     * tag CTe/infCte/infRespTec (opcional)
     * @return DOMElement
     * @throws RuntimeException
     */
    public function taginfRespTec(stdClass $std)
    {

        $possible = [
            'CNPJ',
            'xContato',
            'email',
            'fone',
            'idCSRT',
            'CSRT'
        ];

        $std = $this->equilizeParameters($std, $possible);

        $infRespTec = $this->dom->createElement("infRespTec");
        $this->dom->addChild(
            $infRespTec,
            "CNPJ",
            $std->CNPJ,
            true,
            "Informar o CNPJ da pessoa jurídica responsável pelo sistema "
            . "utilizado na emissão do documento fiscal eletrônico"
        );
        $this->dom->addChild(
            $infRespTec,
            "xContato",
            $std->xContato,
            true,
            "Informar o nome da pessoa a ser contatada na empresa desenvolvedora "
            . "do sistema utilizado na emissão do documento fiscal eletrônico"
        );
        $this->dom->addChild(
            $infRespTec,
            "email",
            $std->email,
            true,
            "Informar o e-mail da pessoa a ser contatada na empresa "
            . "desenvolvedora do sistema."
        );
        $this->dom->addChild(
            $infRespTec,
            "fone",
            $std->fone,
            true,
            "Informar o telefone da pessoa a ser contatada na empresa "
            . "desenvolvedora do sistema."
        );
        if (!empty($std->CSRT) && !empty($std->idCSRT)) {
            $this->csrt = $std->CSRT;
            $this->dom->addChild(
                $infRespTec,
                "idCSRT",
                $std->idCSRT,
                true,
                "Identificador do CSRT utilizado para montar o hash do CSRT"
            );
            $this->dom->addChild(
                $infRespTec,
                "hashCSRT",
                $this->hashCSRT($std->CSRT),
                true,
                "hash do CSRT"
            );
        }
        $this->infRespTec = $infRespTec;
        return $infRespTec;
    }

    protected function checkCTeKey(Dom $dom)
    {
        $infCTe = $dom->getElementsByTagName("infCte")->item(0);
        $ide = $dom->getElementsByTagName("ide")->item(0);
        $emit = $dom->getElementsByTagName("emit")->item(0);
        $cUF = $ide->getElementsByTagName('cUF')->item(0)->nodeValue;
        $dhEmi = $ide->getElementsByTagName('dhEmi')->item(0)->nodeValue;
        $cnpj = $emit->getElementsByTagName('CNPJ')->item(0)->nodeValue;
        $mod = $ide->getElementsByTagName('mod')->item(0)->nodeValue;
        $serie = $ide->getElementsByTagName('serie')->item(0)->nodeValue;
        $nNF = $ide->getElementsByTagName('nCT')->item(0)->nodeValue;
        $tpEmis = $ide->getElementsByTagName('tpEmis')->item(0)->nodeValue;
        $cCT = $ide->getElementsByTagName('cCT')->item(0)->nodeValue;
        $chave = str_replace('CTe', '', $infCTe->getAttribute("Id"));

        $dt = new \DateTime($dhEmi);

        $chaveMontada = Keys::build(
            $cUF,
            $dt->format('y'),
            $dt->format('m'),
            $cnpj,
            $mod,
            $serie,
            $nNF,
            $tpEmis,
            $cCT
        );
        //caso a chave contida no CTe esteja errada
        //substituir a chave
        if ($chaveMontada != $chave) {
            $ide->getElementsByTagName('cDV')->item(0)->nodeValue = substr($chaveMontada, -1);
            $infCTe = $dom->getElementsByTagName("infCte")->item(0);
            $infCTe->setAttribute("Id", "CTe" . $chaveMontada);
            $this->chCTe = $chaveMontada;
        }
    }

    /**
     * Includes missing or unsupported properties in stdClass
     * Replace all unsuported chars
     *
     * @param  stdClass $std
     * @param  array $possible
     * @return stdClass
     */
    private function equilizeParameters(stdClass $std, $possible)
    {
        return Strings::equilizeParameters($std, $possible, $this->replaceAccentedChars);
    }
}
