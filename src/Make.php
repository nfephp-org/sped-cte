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
use NFePHP\Common\Base\BaseMake;

class Make extends BaseMake
{
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
     * Informações relativas ao ICMS
     * @var \DOMNode
     */
    private $ICMS = '';
    /**
     * Prestação sujeito à tributação normal do ICMS
     * @var \DOMNode
     */
    private $ICMS00 = '';
    /**
     * Prestação sujeito à tributação com redução de BC do ICMS
     * @var \DOMNode
     */
    private $ICMS20 = '';
    /**
     * ICMS Isento, não Tributado ou diferido
     * @var \DOMNode
     */
    private $ICMS45 = '';
    /**
     * Tributação pelo ICMS60 - ICMS cobrado por substituição tributária.
     * Responsabilidade do recolhimento do ICMS atribuído ao tomador ou 3º por ST
     * @var \DOMNode
     */
    private $ICMS60 = '';
    /**
     * ICMS Outros
     * @var \DOMNode
     */
    private $ICMS90 = '';
    /**
     * ICMS devido à UF de origem da prestação, quando diferente da UF do emitente
     * @var \DOMNode
     */
    private $ICMSOutraUF = '';
    /**
     * Simples Nacional
     * @var \DOMNode
     */
    private $ICMSSN = '';
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
     * Tomador não é contribuinte do ICMS
     * @var \DOMNode
     */
    private $tomaNaoICMS = '';
    /**
     * Informação da NF ou CT emitido pelo Tomador
     * @var \DOMNode
     */
    private $refNF = '';
    /**
     * Informação do CTe emitido pelo Tomador
     * @var \DOMNode
     */
    private $refCte = '';
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
     * Dados dos Veículos
     * @var array
     */
    private $lacRodo = array();
    /**
     * Informações do(s) Motorista(s)
     * @var array
     */
    private $moto = array();

    /**
     * Monta o arquivo XML usando as tag's já preenchidas
     *
     * @return bool
     */
    public function montaCTe()
    {
        if (count($this->erros) > 0) {
            return false;
        }
        $this->zCTeTag();
        if ($this->toma3 != '') {
            $this->dom->appChild($this->ide, $this->toma3, 'Falta tag "ide"');
        } else {
            $this->dom->appChild($this->toma4, $this->enderToma, 'Falta tag "toma4"');
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
                $this->zEntregaTag();
                $this->dom->appChild($this->entrega, $this->semData, 'Falta tag "Entrega"');
            } elseif ($this->comData != '') {
                $this->zEntregaTag();
                $this->dom->appChild($this->entrega, $this->comData, 'Falta tag "Entrega"');
            } elseif ($this->noPeriodo != '') {
                $this->zEntregaTag();
                $this->dom->appChild($this->entrega, $this->noPeriodo, 'Falta tag "Entrega"');
            } elseif ($this->semHora != '') {
                $this->zEntregaTag();
                $this->dom->appChild($this->entrega, $this->semHora, 'Falta tag "Entrega"');
            } elseif ($this->comHora != '') {
                $this->zEntregaTag();
                $this->dom->appChild($this->entrega, $this->comHora, 'Falta tag "Entrega"');
            } elseif ($this->noInter != '') {
                $this->zEntregaTag();
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

            $this->dom->appChild($this->infCTeNorm, $this->infDoc, 'Falta tag "infDoc"');
            foreach ($this->infNF as $infNF) {
                $this->dom->appChild($this->infDoc, $infNF, 'Falta tag "infNF"');
            }
            foreach ($this->infNFe as $infNFe) {
                $this->dom->appChild($this->infDoc, $infNFe, 'Falta tag "infNFe"');
            }
            foreach ($this->infOutros as $infOutros) {
                $this->dom->appChild($this->infDoc, $infOutros, 'Falta tag "infOutros"');
            }

            if ($this->idDocAntEle != []) { //Caso tenha CT-es Anteriores viculados
                $this->dom->appChild($this->infCTeNorm, $this->docAnt, 'Falta tag "docAnt"');

                foreach ($this->emiDocAnt as $emiDocAnt) {
                    $this->dom->appChild($this->docAnt, $emiDocAnt, 'Falta tag "emiDocAnt"');
                    $this->dom->appChild($emiDocAnt, $this->idDocAnt, 'Falta tag "idDocAnt"');

                    foreach ($this->idDocAntEle as $idDocAntEle) {
                        $this->dom->appChild($this->idDocAnt, $idDocAntEle, 'Falta tag "emiDocAnt"');
                    }
                }
            }

            foreach ($this->seg as $seg) {
                $this->dom->appChild($this->infCTeNorm, $seg, 'Falta tag "seg"');
            }

            $this->dom->appChild($this->infCTeNorm, $this->infModal, 'Falta tag "infModal"');
            $this->dom->appChild($this->infModal, $this->rodo, 'Falta tag "rodo"');
        }
        foreach ($this->veic as $veic) {
            $this->dom->appChild($this->rodo, $veic, 'Falta tag "veic"');
        }

        $this->dom->appChild($this->CTe, $this->infCte, 'Falta tag "CTe"');
        $this->dom->appChild($this->dom, $this->CTe, 'Falta tag "DOMDocument"');
        $this->xml = $this->dom->saveXML();
        return true;
    }

    /**
     * Gera o grupo básico: Informações do CT-e
     * #1
     * Nível: 0
     *
     * @param string $chave  Chave do CTe
     * @param string $versao Versão do CTe
     *
     * @return \DOMElement
     */
    public function infCteTag($chave = '', $versao = '')
    {
        $this->infCte = $this->dom->createElement('infCte');
        $this->infCte->setAttribute('Id', 'CTe' . $chave);
        $this->infCte->setAttribute('versao', $versao);
        return $this->infCte;
    }

    /**
     * Gera as tags para o elemento: Identificação do CT-e
     * #4
     * Nível: 1
     * Os parâmetros para esta função são todos os elementos da tag "ide" do tipo elemento (Ele = E|CE|A) e nível 2
     * @param string $cUF Código da UF do emitente do CT-e
     * @param string $cCT Código numérico que compõe a Chave de Acesso
     * @param string $CFOP Código Fiscal de Operações e Prestações
     * @param string $natOp Natureza da Operação
     * @param string $mod Modelo do documento fiscal
     * @param string $serie Série do CT-e
     * @param string $nCT Número do CT-e
     * @param string $dhEmi Data e hora de emissão do CT-e
     * @param string $tpImp Formato de impressão do DACTE
     * @param string $tpEmis Forma de emissão do CT-e
     * @param string $cDV Digito Verificador da chave de acesso do CT-e
     * @param string $tpAmb Tipo do Ambiente
     * @param string $tpCTe Tipo do CT-e
     * @param string $procEmi Identificador do processo de emissão do CT-e
     * @param string $verProc Versão do processo de emissão
     * @param string $refCTE Chave de acesso do CT-e referenciado
     * @param string $cMunEnv Código do Município de envio do CT-e (de onde o documento foi transmitido)
     * @param string $xMunEnv Nome do Município de envio do CT-e (de onde o documento foi transmitido)
     * @param string $UFEnv Sigla da UF de envio do CT-e (de onde o documento foi transmitido)
     * @param string $modal Modal
     * @param string $tpServ Tipo do Serviço
     * @param string $cMunIni Código do Município de início da prestação
     * @param string $xMunIni Nome do Município do início da prestação
     * @param string $UFIni UF do início da prestação
     * @param string $cMunFim Código do Município de término da prestação
     * @param string $xMunFim Nome do Município do término da prestação
     * @param string $UFFim UF do término da prestação
     * @param string $retira Indicador se o Recebedor retira no Aeroporto, Filial, Porto ou Estação de Destino?
     * @param string $xDetRetira Detalhes do retira
     * @param string $dhCont Data e Hora da entrada em contingência
     * @param string $xJust Justificativa da entrada em contingência
     * @return DOMElement|\DOMNode
     */
    public function ideTag(
        $cUF = '',
        $cCT = '',
        $CFOP = '',
        $natOp = '',
        $mod = '',
        $serie = '',
        $nCT = '',
        $dhEmi = '',
        $tpImp = '',
        $tpEmis = '',
        $cDV = '',
        $tpAmb = '',
        $tpCTe = '',
        $procEmi = '',
        $verProc = '',
        $indGlobalizado = '',
        $cMunEnv = '',
        $xMunEnv = '',
        $UFEnv = '',
        $modal = '',
        $tpServ = '',
        $cMunIni = '',
        $xMunIni = '',
        $UFIni = '',
        $cMunFim = '',
        $xMunFim = '',
        $UFFim = '',
        $retira = '',
        $xDetRetira = '',
        $indIEToma = '',
        $dhCont = '',
        $xJust = ''
    ) {
        $this->tpAmb = $tpAmb;
        $identificador = '#4 <ide> - ';
        $this->ide = $this->dom->createElement('ide');
        $this->dom->addChild(
            $this->ide,
            'cUF',
            $cUF,
            true,
            $identificador . 'Código da UF do emitente do CT-e'
        );
        $this->dom->addChild(
            $this->ide,
            'cCT',
            $cCT,
            true,
            $identificador . 'Código numérico que compõe a Chave de Acesso'
        );
        $this->dom->addChild(
            $this->ide,
            'CFOP',
            $CFOP,
            true,
            $identificador . 'Código Fiscal de Operações e Prestações'
        );
        $this->dom->addChild(
            $this->ide,
            'natOp',
            $natOp,
            true,
            $identificador . 'Natureza da Operação'
        );
        $this->dom->addChild(
            $this->ide,
            'mod',
            $mod,
            true,
            $identificador . 'Modelo do documento fiscal'
        );
        $this->mod = $mod;
        $this->dom->addChild(
            $this->ide,
            'serie',
            $serie,
            true,
            $identificador . 'Série do CT-e'
        );
        $this->dom->addChild(
            $this->ide,
            'nCT',
            $nCT,
            true,
            $identificador . 'Número do CT-e'
        );
        $this->dom->addChild(
            $this->ide,
            'dhEmi',
            $dhEmi,
            true,
            $identificador . 'Data e hora de emissão do CT-e'
        );
        $this->dom->addChild(
            $this->ide,
            'tpImp',
            $tpImp,
            true,
            $identificador . 'Formato de impressão do DACTE'
        );
        $this->dom->addChild(
            $this->ide,
            'tpEmis',
            $tpEmis,
            true,
            $identificador . 'Forma de emissão do CT-e'
        );
        $this->dom->addChild(
            $this->ide,
            'cDV',
            $cDV,
            true,
            $identificador . 'Digito Verificador da chave de acesso do CT-e'
        );
        $this->dom->addChild(
            $this->ide,
            'tpAmb',
            $tpAmb,
            true,
            $identificador . 'Tipo do Ambiente'
        );
        $this->dom->addChild(
            $this->ide,
            'tpCTe',
            $tpCTe,
            true,
            $identificador . 'Tipo do CT-e'
        );
        $this->dom->addChild(
            $this->ide,
            'procEmi',
            $procEmi,
            true,
            $identificador . 'Identificador do processo de emissão do CT-e'
        );
        $this->dom->addChild(
            $this->ide,
            'verProc',
            $verProc,
            true,
            $identificador . 'Versão do processo de emissão'
        );
        $this->dom->addChild(
            $this->ide,
            'indGlobalizado',
            $indGlobalizado,
            false,
            $identificador . 'Indicador de CT-e Globalizado'
        );
        $this->dom->addChild(
            $this->ide,
            'cMunEnv',
            $cMunEnv,
            true,
            $identificador . 'Código do Município de envio do CT-e (de onde o documento foi transmitido)'
        );
        $this->dom->addChild(
            $this->ide,
            'xMunEnv',
            $xMunEnv,
            true,
            $identificador . 'Nome do Município de envio do CT-e (de onde o documento foi transmitido)'
        );
        $this->dom->addChild(
            $this->ide,
            'UFEnv',
            $UFEnv,
            true,
            $identificador . 'Sigla da UF de envio do CT-e (de onde o documento foi transmitido)'
        );
        $this->dom->addChild(
            $this->ide,
            'modal',
            $modal,
            true,
            $identificador . 'Modal'
        );
        $this->modal = $modal;
        $this->dom->addChild(
            $this->ide,
            'tpServ',
            $tpServ,
            true,
            $identificador . 'Tipo do Serviço'
        );
        $this->dom->addChild(
            $this->ide,
            'cMunIni',
            $cMunIni,
            true,
            $identificador . 'Nome do Município do início da prestação'
        );
        $this->dom->addChild(
            $this->ide,
            'xMunIni',
            $xMunIni,
            true,
            $identificador . 'Nome do Município do início da prestação'
        );
        $this->dom->addChild(
            $this->ide,
            'UFIni',
            $UFIni,
            true,
            $identificador . 'UF do início da prestação'
        );
        $this->dom->addChild(
            $this->ide,
            'cMunFim',
            $cMunFim,
            true,
            $identificador . 'Código do Município de término da prestação'
        );
        $this->dom->addChild(
            $this->ide,
            'xMunFim',
            $xMunFim,
            true,
            $identificador . 'Nome do Município do término da prestação'
        );
        $this->dom->addChild(
            $this->ide,
            'UFFim',
            $UFFim,
            true,
            $identificador . 'UF do término da prestação'
        );
        $this->dom->addChild(
            $this->ide,
            'retira',
            $retira,
            true,
            $identificador . 'Indicador se o Recebedor retira no Aeroporto, Filial, Porto ou Estação de Destino'
        );
        $this->dom->addChild(
            $this->ide,
            'xDetRetira',
            $xDetRetira,
            false,
            $identificador . 'Detalhes do retira'
        );
        $this->dom->addChild(
            $this->ide,
            'indIEToma',
            $indIEToma,
            true,
            $identificador . 'Indicador do papel do tomador na prestação do serviço'
        );
        $this->dom->addChild(
            $this->ide,
            'dhCont',
            $dhCont,
            false,
            $identificador . 'Data e Hora da entrada em contingência'
        );
        $this->dom->addChild(
            $this->ide,
            'xJust',
            $xJust,
            false,
            $identificador . 'Justificativa da entrada em contingência'
        );
        $this->tpServ = $tpServ;
        return $this->ide;
    }

    /**
     * Gera as tags para o elemento: toma3 (Indicador do "papel" do tomador do serviço no CT-e)
     * e adiciona ao grupo ide
     * #35
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "toma3" do
     * tipo elemento (Ele = E|CE|A) e nível 3
     *
     * @param string $toma Tomador do Serviço
     *
     * @return \DOMElement
     */
    public function toma3Tag($toma = '')
    {
        $identificador = '#35 <toma3> - ';
        $this->toma3 = $this->dom->createElement('toma3');
        $this->dom->addChild(
            $this->toma3,
            'toma',
            $toma,
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
     * Os parâmetros para esta função são todos os elementos da tag "toma4" do
     * tipo elemento (Ele = E|CE|A) e nível 3
     *
     * @param string $toma  Tomador do Serviço
     * @param string $CNPJ  Número do CNPJ
     * @param string $CPF   Número do CPF
     * @param string $IE    Inscrição Estadual
     * @param string $xNome Razão Social ou Nome
     * @param string $xFant Nome Fantasia
     * @param string $fone  Telefone
     * @param string $email Endereço de email
     *
     * @return \DOMElement
     */
    public function toma4Tag(
        $toma = '',
        $CNPJ = '',
        $CPF = '',
        $IE = '',
        $xNome = '',
        $xFant = '',
        $fone = '',
        $email = ''
    ) {
        $identificador = '#37 <toma4> - ';
        $this->toma4 = $this->dom->createElement('toma4');
        $this->dom->addChild(
            $this->toma4,
            'toma',
            $toma,
            true,
            $identificador . 'Tomador do Serviço'
        );
        if ($CNPJ != '') {
            $this->dom->addChild(
                $this->toma4,
                'CNPJ',
                $CNPJ,
                true,
                $identificador . 'Número do CNPJ'
            );
        } elseif ($CPF != '') {
            $this->dom->addChild(
                $this->toma4,
                'CPF',
                $CPF,
                true,
                $identificador . 'Número do CPF'
            );
        } else {
            $this->dom->addChild(
                $this->toma4,
                'CNPJ',
                $CNPJ,
                true,
                $identificador . 'Número do CNPJ'
            );
            $this->dom->addChild(
                $this->toma4,
                'CPF',
                $CPF,
                true,
                $identificador . 'Número do CPF'
            );
        }
        $this->dom->addChild(
            $this->toma4,
            'IE',
            $IE,
            false,
            $identificador . 'Inscrição Estadual'
        );
        $this->dom->addChild(
            $this->toma4,
            'xNome',
            $xNome,
            true,
            $identificador . 'Razão Social ou Nome'
        );
        $this->dom->addChild(
            $this->toma4,
            'xFant',
            $xFant,
            false,
            $identificador . 'Nome Fantasia'
        );
        $this->dom->addChild(
            $this->toma4,
            'fone',
            $fone,
            false,
            $identificador . 'Telefone'
        );
        $this->dom->addChild(
            $this->toma4,
            'email',
            $email,
            false,
            $identificador . 'Endereço de email'
        );
        return $this->toma4;
    }

    /**
     * Gera as tags para o elemento: "enderToma" (Dados do endereço) e adiciona ao grupo "toma4"
     * #45
     * Nível: 3
     * Os parâmetros para esta função são todos os elementos da tag "enderToma"
     * do tipo elemento (Ele = E|CE|A) e nível 4
     *
     * @param string $xLgr    Logradouro
     * @param string $nro     Número
     * @param string $xCpl    Complemento
     * @param string $xBairro Bairro
     * @param string $cMun    Código do município (utilizar a tabela do IBGE)
     * @param string $xMun    Nome do município
     * @param string $CEP     CEP
     * @param string $UF      Sigla da UF
     * @param string $cPais   Código do país
     * @param string $xPais   Nome do país
     *
     * @return \DOMElement
     */
    public function enderTomaTag(
        $xLgr = '',
        $nro = '',
        $xCpl = '',
        $xBairro = '',
        $cMun = '',
        $xMun = '',
        $CEP = '',
        $UF = '',
        $cPais = '',
        $xPais = ''
    ) {
        $identificador = '#45 <enderToma> - ';
        $this->enderToma = $this->dom->createElement('enderToma');
        $this->dom->addChild(
            $this->enderToma,
            'xLgr',
            $xLgr,
            true,
            $identificador . 'Logradouro'
        );
        $this->dom->addChild(
            $this->enderToma,
            'nro',
            $nro,
            true,
            $identificador . 'Número'
        );
        $this->dom->addChild(
            $this->enderToma,
            'xCpl',
            $xCpl,
            false,
            $identificador . 'Complemento'
        );
        $this->dom->addChild(
            $this->enderToma,
            'xBairro',
            $xBairro,
            true,
            $identificador . 'Bairro'
        );
        $this->dom->addChild(
            $this->enderToma,
            'cMun',
            $cMun,
            true,
            $identificador . 'Código do município (utilizar a tabela do IBGE)'
        );
        $this->dom->addChild(
            $this->enderToma,
            'xMun',
            $xMun,
            true,
            $identificador . 'Nome do município'
        );
        $this->dom->addChild(
            $this->enderToma,
            'CEP',
            $CEP,
            false,
            $identificador . 'CEP'
        );
        $this->dom->addChild(
            $this->enderToma,
            'UF',
            $UF,
            true,
            $identificador . 'Sigla da UF'
        );
        $this->dom->addChild(
            $this->enderToma,
            'cPais',
            $cPais,
            false,
            $identificador . 'Código do país'
        );
        $this->dom->addChild(
            $this->enderToma,
            'xPais',
            $xPais,
            false,
            $identificador . 'Nome do país'
        );
        return $this->enderToma;
    }

    /**
     * Gera as tags para o elemento: "compl" (Dados complementares do CT-e para fins operacionais ou comerciais)
     * #59
     * Nível: 1
     * Os parâmetros para esta função são todos os elementos da tag "compl" do
     * tipo elemento (Ele = E|CE|A) e nível 2
     *
     * @param string $xCaracAd  Característica adicional do transporte
     * @param string $xCaracSer Característica adicional do serviço
     * @param string $xEmi      Funcionário emissor do CTe
     * @param string $origCalc  Município de origem para efeito de cálculo do frete
     * @param string $destCalc  Município de destino para efeito de cálculo do frete
     * @param string $xObs      Observações Gerais
     *
     * @return \DOMElement
     */
    public function complTag($xCaracAd = '', $xCaracSer = '', $xEmi = '', $origCalc = '', $destCalc = '', $xObs = '')
    {
        $identificador = '#59 <compl> - ';
        $this->compl = $this->dom->createElement('compl');
        $this->dom->addChild(
            $this->compl,
            'xCaracAd',
            $xCaracAd,
            false,
            $identificador . 'Característica adicional do transporte'
        );
        $this->dom->addChild(
            $this->compl,
            'xCaracSer',
            $xCaracSer,
            false,
            $identificador . 'Característica adicional do serviço'
        );
        $this->dom->addChild(
            $this->compl,
            'xEmi',
            $xEmi,
            false,
            $identificador . 'Funcionário emissor do CTe'
        );
        $this->dom->addChild(
            $this->compl,
            'origCalc',
            $origCalc,
            false,
            $identificador . 'Município de origem para efeito de cálculo do frete'
        );
        $this->dom->addChild(
            $this->compl,
            'destCalc',
            $destCalc,
            false,
            $identificador . 'Município de destino para efeito de cálculo do frete'
        );
        $this->dom->addChild(
            $this->compl,
            'xObs',
            $xObs,
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
     * @param string $xOrig Sigla ou código interno da Filial/Porto/Estação/ Aeroporto de Origem
     * @param string $xDest Sigla ou código interno da Filial/Porto/Estação/Aeroporto de Destino
     * @param string $xRota Código da Rota de Entrega
     *
     * @return \DOMElement
     */
    public function fluxoTag($xOrig = '', $xDest = '', $xRota = '')
    {
        $identificador = '#63 <fluxo> - ';
        $this->fluxo = $this->dom->createElement('fluxo');
        $this->dom->addChild(
            $this->fluxo,
            'xOrig',
            $xOrig,
            false,
            $identificador . 'Sigla ou código interno da Filial/Porto/Estação/ Aeroporto de Origem'
        );
        $this->dom->addChild(
            $this->fluxo,
            'xDest',
            $xDest,
            false,
            $identificador . 'Sigla ou código interno da Filial/Porto/Estação/Aeroporto de Destino'
        );
        $this->dom->addChild(
            $this->fluxo,
            'xRota',
            $xRota,
            false,
            $identificador . 'Código da Rota de Entrega'
        );
        return $this->fluxo;
    }

    /**
     * Gera as tags para o elemento: "pass"
     * #65
     * Nível: 3
     * Os parâmetros para esta função são todos os elementos da tag "pass" do
     * tipo elemento (Ele = E|CE|A) e nível 4
     *
     * @param string $xPass Sigla ou código interno da Filial/Porto/Estação/Aeroporto de Passagem
     *
     * @return \DOMElement
     */
    public function passTag($xPass = '')
    {
        $identificador = '#65 <pass> - ';
        $this->pass[] = $this->dom->createElement('pass');
        $posicao = (integer) count($this->pass) - 1;
        $this->dom->addChild(
            $this->pass[$posicao],
            'xPass',
            $xPass,
            false,
            $identificador . 'Sigla ou código interno da Filial/Porto/Estação/Aeroporto de Passagem'
        );
        return $this->pass[$posicao];
    }

    /**
     * Gera as tags para o elemento: "semData" (Entrega sem data definida)
     * #70
     * Nível: 3
     * Os parâmetros para esta função são todos os elementos da tag "semData" do
     * tipo elemento (Ele = E|CE|A) e nível 4
     *
     * @param string $tpPer Tipo de data/período programado para entrega
     *
     * @return \DOMElement
     */
    public function semDataTag($tpPer = '')
    {
        $identificador = '#70 <semData> - ';
        $this->semData = $this->dom->createElement('semData');
        $this->dom->addChild(
            $this->semData,
            'tpPer',
            $tpPer,
            true,
            $identificador . 'Tipo de data/período programado para entrega'
        );
        return $this->semData;
    }

    /**
     * Gera as tags para o elemento: "comData" (Entrega com data definida)
     * #72
     * Nível: 3
     * Os parâmetros para esta função são todos os elementos da tag "comData" do
     * tipo elemento (Ele = E|CE|A) e nível 4
     *
     * @param string $tpPer Tipo de data/período programado para entrega
     * @param string $dProg Data programada
     *
     * @return \DOMElement
     */
    public function comDataTag($tpPer = '', $dProg = '')
    {
        $identificador = '#72 <comData> - ';
        $this->comData = $this->dom->createElement('comData');
        $this->dom->addChild(
            $this->comData,
            'tpPer',
            $tpPer,
            true,
            $identificador . 'Tipo de data/período programado para entrega'
        );
        $this->dom->addChild(
            $this->comData,
            'dProg',
            $dProg,
            true,
            $identificador . 'Data programada'
        );
        return $this->comData;
    }

    /**
     * Gera as tags para o elemento: "noPeriodo" (Entrega no período definido)
     * #75
     * Nível: 3
     * Os parâmetros para esta função são todos os elementos da tag "noPeriodo" do tipo
     * elemento (Ele = E|CE|A) e nível 4
     *
     * @param string $tpPer Tipo de data/período programado para entrega
     * @param string $dIni  Data inicial
     * @param string $dFim  Data final
     *
     * @return \DOMElement
     */
    public function noPeriodoTag($tpPer = '', $dIni = '', $dFim = '')
    {
        $identificador = '#75 <noPeriodo> - ';
        $this->noPeriodo = $this->dom->createElement('noPeriodo');
        $this->dom->addChild(
            $this->noPeriodo,
            'tpPer',
            $tpPer,
            true,
            $identificador . 'Tipo de data/período programado para entrega'
        );
        $this->dom->addChild(
            $this->noPeriodo,
            'dIni',
            $dIni,
            true,
            $identificador . 'Data inicial'
        );
        $this->dom->addChild(
            $this->noPeriodo,
            'dFim',
            $dFim,
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
     * @param string $tpHor Tipo de hora
     *
     * @return \DOMElement
     */
    public function semHoraTag($tpHor = '')
    {
        $identificador = '#79 <semHora> - ';
        $this->semHora = $this->dom->createElement('semHora');
        $this->dom->addChild(
            $this->semHora,
            'tpHor',
            $tpHor,
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
     * @param string $tpHor Tipo de hora
     * @param string $hProg Hora programada
     *
     * @return \DOMElement
     */
    public function comHoraTag($tpHor = '', $hProg = '')
    {
        $identificador = '#81 <comHora> - ';
        $this->comHora = $this->dom->createElement('comHora');
        $this->dom->addChild(
            $this->comHora,
            'tpHor',
            $tpHor,
            true,
            $identificador . 'Tipo de hora'
        );
        $this->dom->addChild(
            $this->comHora,
            'hProg',
            $hProg,
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
     * @param string $tpHor Tipo de hora
     * @param string $hIni  Hora inicial
     * @param string $hFim  Hora final
     *
     * @return \DOMElement
     */
    public function noInterTag($tpHor = '', $hIni = '', $hFim = '')
    {
        $identificador = '#84 <noInter> - ';
        $this->noInter = $this->dom->createElement('noInter');
        $this->dom->addChild(
            $this->noInter,
            'tpHor',
            $tpHor,
            true,
            $identificador . 'Tipo de hora'
        );
        $this->dom->addChild(
            $this->noInter,
            'hIni',
            $hIni,
            true,
            $identificador . 'Hora inicial'
        );
        $this->dom->addChild(
            $this->noInter,
            'hFim',
            $hFim,
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
     * @param string $xCampo Identificação do campo
     * @param string $xTexto Conteúdo do campo
     *
     * @return boolean
     */
    public function obsContTag($xCampo = '', $xTexto = '')
    {
        $identificador = '#91 <ObsCont> - ';
        $posicao = (integer) count($this->obsCont) - 1;
        if (count($this->obsCont) <= 10) {
            $this->obsCont[] = $this->dom->createElement('ObsCont');
            $this->obsCont[$posicao]->setAttribute('xCampo', $xCampo);
            $this->dom->addChild(
                $this->obsCont[$posicao],
                'xTexto',
                $xTexto,
                true,
                $identificador . 'Conteúdo do campo'
            );
            return true;
        }
        $this->erros[] = array(
            'tag' => (string) '<ObsCont>',
            'desc' => (string) 'Campo de uso livre do contribuinte',
            'erro' => (string) 'Tag deve aparecer de 0 a 10 vezes'
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
     * @param string $xCampo Identificação do campo
     * @param string $xTexto Conteúdo do campo
     *
     * @return boolean
     */
    public function obsFiscoTag($xCampo = '', $xTexto = '')
    {
        $identificador = '#94 <ObsFisco> - ';
        $posicao = (integer) count($this->obsFisco) - 1;
        if (count($this->obsFisco) <= 10) {
            $this->obsFisco[] = $this->dom->createElement('obsFisco');
            $this->obsFisco[$posicao]->setAttribute('xCampo', $xCampo);
            $this->dom->addChild(
                $this->obsFisco[$posicao],
                'xTexto',
                $xTexto,
                true,
                $identificador . 'Conteúdo do campo'
            );
            return true;
        }
        $this->erros[] = array(
            'tag' => (string) '<ObsFisco>',
            'desc' => (string) 'Campo de uso livre do contribuinte',
            'erro' => (string) 'Tag deve aparecer de 0 a 10 vezes'
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
     * @param string $CNPJ  CNPJ do emitente
     * @param string $IE    Inscrição Estadual do Emitente
     * @param string $xNome Razão social ou Nome do emitente
     * @param string $xFant Nome fantasia
     *
     * @return \DOMElement
     */
    public function emitTag($CNPJ = '', $IE = '', $IEST = '', $xNome = '', $xFant = '')
    {
        $identificador = '#97 <emit> - ';
        $this->emit = $this->dom->createElement('emit');
        $this->dom->addChild(
            $this->emit,
            'CNPJ',
            $CNPJ,
            true,
            $identificador . 'CNPJ do emitente'
        );
        $this->dom->addChild(
            $this->emit,
            'IE',
            $IE,
            true,
            $identificador . 'Inscrição Estadual do Emitente'
        );
        $this->dom->addChild(
            $this->emit,
            'IEST',
            $IEST,
            false,
            $identificador . 'Inscrição Estadual do Substituto Tributário'
        );
        $this->dom->addChild(
            $this->emit,
            'xNome',
            $xNome,
            true,
            $identificador . 'Razão social ou Nome do emitente'
        );
        $this->dom->addChild(
            $this->emit,
            'xFant',
            $xFant,
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
     * @param string $xLgr    Logradouro
     * @param string $nro     Número
     * @param string $xCpl    Complemento
     * @param string $xBairro Bairro
     * @param string $cMun    Código do município (utilizar a tabela do IBGE)
     * @param string $xMun    Nome do município
     * @param string $CEP     CEP
     * @param string $UF      Sigla da UF
     * @param string $fone    Telefone
     *
     * @return \DOMElement
     */
    public function enderEmitTag(
        $xLgr = '',
        $nro = '',
        $xCpl = '',
        $xBairro = '',
        $cMun = '',
        $xMun = '',
        $CEP = '',
        $UF = '',
        $fone = ''
    ) {
        $identificador = '#102 <enderEmit> - ';
        $this->enderEmit = $this->dom->createElement('enderEmit');
        $this->dom->addChild(
            $this->enderEmit,
            'xLgr',
            $xLgr,
            true,
            $identificador . 'Logradouro'
        );
        $this->dom->addChild(
            $this->enderEmit,
            'nro',
            $nro,
            true,
            $identificador . 'Número'
        );
        $this->dom->addChild(
            $this->enderEmit,
            'xCpl',
            $xCpl,
            false,
            $identificador . 'Complemento'
        );
        $this->dom->addChild(
            $this->enderEmit,
            'xBairro',
            $xBairro,
            true,
            $identificador . 'Bairro'
        );
        $this->dom->addChild(
            $this->enderEmit,
            'cMun',
            $cMun,
            true,
            $identificador . 'Código do município'
        );
        $this->dom->addChild(
            $this->enderEmit,
            'xMun',
            $xMun,
            true,
            $identificador . 'Nome do município'
        );
        $this->dom->addChild(
            $this->enderEmit,
            'CEP',
            $CEP,
            false,
            $identificador . 'CEP'
        );
        $this->dom->addChild(
            $this->enderEmit,
            'UF',
            $UF,
            true,
            $identificador . 'Sigla da UF'
        );
        $this->dom->addChild(
            $this->enderEmit,
            'fone',
            $fone,
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
     * @param string $CNPJ  Número do CNPJ
     * @param string $CPF   Número do CPF
     * @param string $IE    Inscrição Estadual
     * @param string $xNome Razão social ou nome do remetente
     * @param string $xFant Nome fantasia
     * @param string $fone  Telefone
     * @param string $email Endereço de email
     *
     * @return \DOMElement
     */
    public function remTag($CNPJ = '', $CPF = '', $IE = '', $xNome = '', $xFant = '', $fone = '', $email = '')
    {
        $identificador = '#97 <rem> - ';
        $this->rem = $this->dom->createElement('rem');
        if ($CNPJ != '') {
            $this->dom->addChild(
                $this->rem,
                'CNPJ',
                $CNPJ,
                true,
                $identificador . 'CNPJ do Remente'
            );
        } elseif ($CPF != '') {
            $this->dom->addChild(
                $this->rem,
                'CPF',
                $CPF,
                true,
                $identificador . 'CPF do Remente'
            );
        } else {
            $this->dom->addChild(
                $this->rem,
                'CNPJ',
                $CNPJ,
                true,
                $identificador . 'CNPJ do Remente'
            );
            $this->dom->addChild(
                $this->rem,
                'CPF',
                $CPF,
                true,
                $identificador . 'CPF do remente'
            );
        }
        $this->dom->addChild(
            $this->rem,
            'IE',
            $IE,
            true,
            $identificador . 'Inscrição Estadual do remente'
        );
        $this->dom->addChild(
            $this->rem,
            'xNome',
            $xNome,
            true,
            $identificador . 'Razão social ou Nome do remente'
        );
        $this->dom->addChild(
            $this->rem,
            'xFant',
            $xFant,
            false,
            $identificador . 'Nome fantasia'
        );
        $this->dom->addChild(
            $this->rem,
            'fone',
            $fone,
            false,
            $identificador . 'Telefone'
        );
        $this->dom->addChild(
            $this->rem,
            'email',
            $email,
            false,
            $identificador . 'Endereço de email'
        );
        return $this->rem;
    }

    /**
     * Gera as tags para o elemento: "enderReme" (Dados do endereço)
     * #119
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "enderReme" do
     * tipo elemento (Ele = E|CE|A) e nível 3
     *
     * @param string $xLgr    Logradouro
     * @param string $nro     Número
     * @param string $xCpl    Complemento
     * @param string $xBairro Bairro
     * @param string $cMun    Código do município (utilizar a tabela do IBGE)
     * @param string $xMun    Nome do município
     * @param string $CEP     CEP
     * @param string $UF      Sigla da UF
     * @param string $cPais   Código do país
     * @param string $xPais   Nome do país
     *
     * @return \DOMElement
     */
    public function enderRemeTag(
        $xLgr = '',
        $nro = '',
        $xCpl = '',
        $xBairro = '',
        $cMun = '',
        $xMun = '',
        $CEP = '',
        $UF = '',
        $cPais = '',
        $xPais = ''
    ) {
        $identificador = '#119 <enderReme> - ';
        $this->enderReme = $this->dom->createElement('enderReme');
        $this->dom->addChild(
            $this->enderReme,
            'xLgr',
            $xLgr,
            true,
            $identificador . 'Logradouro'
        );
        $this->dom->addChild(
            $this->enderReme,
            'nro',
            $nro,
            true,
            $identificador . 'Número'
        );
        $this->dom->addChild(
            $this->enderReme,
            'xCpl',
            $xCpl,
            false,
            $identificador . 'Complemento'
        );
        $this->dom->addChild(
            $this->enderReme,
            'xBairro',
            $xBairro,
            true,
            $identificador . 'Bairro'
        );
        $this->dom->addChild(
            $this->enderReme,
            'cMun',
            $cMun,
            true,
            $identificador . 'Código do município (utilizar a tabela do IBGE)'
        );
        $this->dom->addChild(
            $this->enderReme,
            'xMun',
            $xMun,
            true,
            $identificador . 'Nome do município'
        );
        $this->dom->addChild(
            $this->enderReme,
            'CEP',
            $CEP,
            false,
            $identificador . 'CEP'
        );
        $this->dom->addChild(
            $this->enderReme,
            'UF',
            $UF,
            true,
            $identificador . 'Sigla da UF'
        );
        $this->dom->addChild(
            $this->enderReme,
            'cPais',
            $cPais,
            false,
            $identificador . 'Código do país'
        );
        $this->dom->addChild(
            $this->enderReme,
            'xPais',
            $xPais,
            false,
            $identificador . 'Nome do país'
        );

        $node = $this->rem->getElementsByTagName("email")->item(0);
        $this->rem->insertBefore($this->enderReme, $node);
        return $this->enderReme;
    }

    /**
     * Gera as tags para o elemento: "exped" (Informações do Expedidor da Carga)
     * #142
     * Nível: 1
     * Os parâmetros para esta função são todos os elementos da tag "exped" do
     * tipo elemento (Ele = E|CE|A) e nível 2
     *
     * @param string $CNPJ Número do CNPJ
     * @param string $CPF Número do CPF
     * @param string $IE Inscrição Estadual
     * @param string $xNome Razão Social ou Nome
     * @param string $fone Telefone
     * @param string $email Endereço de email
     *
     * @return \DOMElement
     */
    public function expedTag($CNPJ = '', $CPF = '', $IE = '', $xNome = '', $fone = '', $email = '')
    {
        $identificador = '#142 <exped> - ';
        $this->exped = $this->dom->createElement('exped');
        if ($CNPJ != '') {
            $this->dom->addChild(
                $this->exped,
                'CNPJ',
                $CNPJ,
                true,
                $identificador . 'Número do CNPJ'
            );
        } elseif ($CPF != '') {
            $this->dom->addChild(
                $this->exped,
                'CPF',
                $CPF,
                true,
                $identificador . 'Número do CPF'
            );
        } else {
            $this->dom->addChild(
                $this->exped,
                'CNPJ',
                $CNPJ,
                true,
                $identificador . 'Número do CNPJ'
            );
            $this->dom->addChild(
                $this->exped,
                'CPF',
                $CPF,
                true,
                $identificador . 'Número do CPF'
            );
        }
        $this->dom->addChild(
            $this->exped,
            'IE',
            $IE,
            true,
            $identificador . 'Inscrição Estadual'
        );
        $this->dom->addChild(
            $this->exped,
            'xNome',
            $xNome,
            true,
            $identificador . 'Razão social ou Nome'
        );
        $this->dom->addChild(
            $this->exped,
            'fone',
            $fone,
            false,
            $identificador . 'Telefone'
        );
        $this->dom->addChild(
            $this->exped,
            'email',
            $email,
            false,
            $identificador . 'Endereço de email'
        );
        return $this->exped;
    }

    /**
     * Gera as tags para o elemento: "enderExped" (Dados do endereço)
     * #148
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "enderExped" do
     * tipo elemento (Ele = E|CE|A) e nível 3
     *
     * @param string $xLgr Logradouro
     * @param string $nro Número
     * @param string $xCpl Complemento
     * @param string $xBairro Bairro
     * @param string $cMun Código do município (utilizar a tabela do IBGE)
     * @param string $xMun Nome do município
     * @param string $CEP CEP
     * @param string $UF Sigla da UF
     * @param string $cPais Código do país
     * @param string $xPais Nome do país
     *
     * @return \DOMElement
     */
    public function enderExpedTag(
        $xLgr = '',
        $nro = '',
        $xCpl = '',
        $xBairro = '',
        $cMun = '',
        $xMun = '',
        $CEP = '',
        $UF = '',
        $cPais = '',
        $xPais = ''
    ) {
        $identificador = '#148 <enderExped> - ';
        $this->enderExped = $this->dom->createElement('enderExped');
        $this->dom->addChild(
            $this->enderExped,
            'xLgr',
            $xLgr,
            true,
            $identificador . 'Logradouro'
        );
        $this->dom->addChild(
            $this->enderExped,
            'nro',
            $nro,
            true,
            $identificador . 'Número'
        );
        $this->dom->addChild(
            $this->enderExped,
            'xCpl',
            $xCpl,
            false,
            $identificador . 'Complemento'
        );
        $this->dom->addChild(
            $this->enderExped,
            'xBairro',
            $xBairro,
            true,
            $identificador . 'Bairro'
        );
        $this->dom->addChild(
            $this->enderExped,
            'cMun',
            $cMun,
            true,
            $identificador . 'Código do município (utilizar a tabela do IBGE)'
        );
        $this->dom->addChild(
            $this->enderExped,
            'xMun',
            $xMun,
            true,
            $identificador . 'Nome do município'
        );
        $this->dom->addChild(
            $this->enderExped,
            'CEP',
            $CEP,
            false,
            $identificador . 'CEP'
        );
        $this->dom->addChild(
            $this->enderExped,
            'UF',
            $UF,
            true,
            $identificador . 'Sigla da UF'
        );
        $this->dom->addChild(
            $this->enderExped,
            'cPais',
            $cPais,
            false,
            $identificador . 'Código do país'
        );
        $this->dom->addChild(
            $this->enderExped,
            'xPais',
            $xPais,
            false,
            $identificador . 'Nome do país'
        );
        $node = $this->exped->getElementsByTagName("email")->item(0);
        $this->exped->insertBefore($this->enderExped, $node);
        return $this->enderExped;
    }

    /**
     * Gera as tags para o elemento: "receb" (Informações do Recebedor da Carga)
     * #160
     * Nível: 1
     * Os parâmetros para esta função são todos os elementos da tag "receb" do
     * tipo elemento (Ele = E|CE|A) e nível 2
     *
     * @param string $CNPJ Número do CNPJ
     * @param string $CPF Número do CPF
     * @param string $IE Inscrição Estadual
     * @param string $xNome Razão Social ou Nome
     * @param string $fone Telefone
     * @param string $email Endereço de email
     *
     * @return \DOMElement
     */
    public function recebTag($CNPJ = '', $CPF = '', $IE = '', $xNome = '', $fone = '', $email = '')
    {
        $identificador = '#160 <receb> - ';
        $this->receb = $this->dom->createElement('receb');
        if ($CNPJ != '') {
            $this->dom->addChild(
                $this->receb,
                'CNPJ',
                $CNPJ,
                true,
                $identificador . 'Número do CNPJ'
            );
        } elseif ($CPF != '') {
            $this->dom->addChild(
                $this->receb,
                'CPF',
                $CPF,
                true,
                $identificador . 'Número do CPF'
            );
        } else {
            $this->dom->addChild(
                $this->receb,
                'CNPJ',
                $CNPJ,
                true,
                $identificador . 'Número do CNPJ'
            );
            $this->dom->addChild(
                $this->receb,
                'CPF',
                $CPF,
                true,
                $identificador . 'Número do CPF'
            );
        }
        $this->dom->addChild(
            $this->receb,
            'IE',
            $IE,
            true,
            $identificador . 'Inscrição Estadual'
        );
        $this->dom->addChild(
            $this->receb,
            'xNome',
            $xNome,
            true,
            $identificador . 'Razão social ou Nome'
        );
        $this->dom->addChild(
            $this->receb,
            'fone',
            $fone,
            false,
            $identificador . 'Telefone'
        );
        $this->dom->addChild(
            $this->receb,
            'email',
            $email,
            false,
            $identificador . 'Endereço de email'
        );
        return $this->receb;
    }

    /**
     * Gera as tags para o elemento: "enderReceb" (Informações do Recebedor da Carga)
     * #166
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "enderReceb" do
     * tipo elemento (Ele = E|CE|A) e nível 3
     *
     * @param string $xLgr Logradouro
     * @param string $nro Número
     * @param string $xCpl Complemento
     * @param string $xBairro Bairro
     * @param string $cMun Código do município (utilizar a tabela do IBGE)
     * @param string $xMun Nome do município
     * @param string $CEP CEP
     * @param string $UF Sigla da UF
     * @param string $cPais Código do país
     * @param string $xPais Nome do país
     *
     * @return \DOMElement
     */
    public function enderRecebTag(
        $xLgr = '',
        $nro = '',
        $xCpl = '',
        $xBairro = '',
        $cMun = '',
        $xMun = '',
        $CEP = '',
        $UF = '',
        $cPais = '',
        $xPais = ''
    ) {
        $identificador = '#160 <enderReceb> - ';
        $this->enderReceb = $this->dom->createElement('enderReceb');
        $this->dom->addChild(
            $this->enderReceb,
            'xLgr',
            $xLgr,
            true,
            $identificador . 'Logradouro'
        );
        $this->dom->addChild(
            $this->enderReceb,
            'nro',
            $nro,
            true,
            $identificador . 'Número'
        );
        $this->dom->addChild(
            $this->enderReceb,
            'xCpl',
            $xCpl,
            false,
            $identificador . 'Complemento'
        );
        $this->dom->addChild(
            $this->enderReceb,
            'xBairro',
            $xBairro,
            true,
            $identificador . 'Bairro'
        );
        $this->dom->addChild(
            $this->enderReceb,
            'cMun',
            $cMun,
            true,
            $identificador . 'Código do município (utilizar a tabela do IBGE)'
        );
        $this->dom->addChild(
            $this->enderReceb,
            'xMun',
            $xMun,
            true,
            $identificador . 'Nome do município'
        );
        $this->dom->addChild(
            $this->enderReceb,
            'CEP',
            $CEP,
            false,
            $identificador . 'CEP'
        );
        $this->dom->addChild(
            $this->enderReceb,
            'UF',
            $UF,
            true,
            $identificador . 'Sigla da UF'
        );
        $this->dom->addChild(
            $this->enderReceb,
            'cPais',
            $cPais,
            false,
            $identificador . 'Código do país'
        );
        $this->dom->addChild(
            $this->enderReceb,
            'xPais',
            $xPais,
            false,
            $identificador . 'Nome do país'
        );
        $node = $this->receb->getElementsByTagName("email")->item(0);
        $this->receb->insertBefore($this->enderReceb, $node);
        return $this->enderReceb;
    }

    /**
     * Gera as tags para o elemento: "dest" (Informações do Destinatário do CT-e)
     * #178
     * Nível: 1
     * Os parâmetros para esta função são todos os elementos da tag "dest" do
     * tipo elemento (Ele = E|CE|A) e nível 2
     *
     * @param string $CNPJ Número do CNPJ
     * @param string $CPF Número do CPF
     * @param string $IE Inscrição Estadual
     * @param string $xNome Razão Social ou Nome
     * @param string $fone Telefone
     * @param string $ISUF Inscrição na SUFRAMA
     * @param string $email Endereço de email
     *
     * @return \DOMElement
     */
    public function destTag($CNPJ = '', $CPF = '', $IE = '', $xNome = '', $fone = '', $ISUF = '', $email = '')
    {
        $identificador = '#178 <dest> - ';
        $this->dest = $this->dom->createElement('dest');
        if ($CNPJ != '') {
            $this->dom->addChild(
                $this->dest,
                'CNPJ',
                $CNPJ,
                true,
                $identificador . 'Número do CNPJ'
            );
        } elseif ($CPF != '') {
            $this->dom->addChild(
                $this->dest,
                'CPF',
                $CPF,
                true,
                $identificador . 'Número do CPF'
            );
        } else {
            $this->dom->addChild(
                $this->dest,
                'CNPJ',
                $CNPJ,
                true,
                $identificador . 'Número do CNPJ'
            );
            $this->dom->addChild(
                $this->dest,
                'CPF',
                $CPF,
                true,
                $identificador . 'Número do CPF'
            );
        }
        $this->dom->addChild(
            $this->dest,
            'IE',
            $IE,
            true,
            $identificador . 'Inscrição Estadual'
        );
        $this->dom->addChild(
            $this->dest,
            'xNome',
            $xNome,
            true,
            $identificador . 'Razão social ou Nome'
        );
        $this->dom->addChild(
            $this->dest,
            'fone',
            $fone,
            false,
            $identificador . 'Telefone'
        );
        $this->dom->addChild(
            $this->dest,
            'ISUF',
            $ISUF,
            false,
            $identificador . 'Inscrição na SUFRAMA'
        );
        $this->dom->addChild(
            $this->dest,
            'email',
            $email,
            false,
            $identificador . 'Endereço de email'
        );
        return $this->dest;
    }

    /**
     * Gera as tags para o elemento: "enderDest" (Informações do Recebedor da Carga)
     * # = 185
     * Nível = 2
     * Os parâmetros para esta função são todos os elementos da tag "enderDest" do
     * tipo elemento (Ele = E|CE|A) e nível 3
     *
     * @param string $xLgr Logradouro
     * @param string $nro Número
     * @param string $xCpl Complemento
     * @param string $xBairro Bairro
     * @param string $cMun Código do município (utilizar a tabela do IBGE)
     * @param string $xMun Nome do município
     * @param string $CEP CEP
     * @param string $UF Sigla da UF
     * @param string $cPais Código do país
     * @param string $xPais Nome do país
     *
     * @return \DOMElement
     */
    public function enderDestTag(
        $xLgr = '',
        $nro = '',
        $xCpl = '',
        $xBairro = '',
        $cMun = '',
        $xMun = '',
        $CEP = '',
        $UF = '',
        $cPais = '',
        $xPais = ''
    ) {
        $identificador = '#185 <enderDest> - ';
        $this->enderDest = $this->dom->createElement('enderDest');
        $this->dom->addChild(
            $this->enderDest,
            'xLgr',
            $xLgr,
            true,
            $identificador . 'Logradouro'
        );
        $this->dom->addChild(
            $this->enderDest,
            'nro',
            $nro,
            true,
            $identificador . 'Número'
        );
        $this->dom->addChild(
            $this->enderDest,
            'xCpl',
            $xCpl,
            false,
            $identificador . 'Complemento'
        );
        $this->dom->addChild(
            $this->enderDest,
            'xBairro',
            $xBairro,
            true,
            $identificador . 'Bairro'
        );
        $this->dom->addChild(
            $this->enderDest,
            'cMun',
            $cMun,
            true,
            $identificador . 'Código do município (utilizar a tabela do IBGE)'
        );
        $this->dom->addChild(
            $this->enderDest,
            'xMun',
            $xMun,
            true,
            $identificador . 'Nome do município'
        );
        $this->dom->addChild(
            $this->enderDest,
            'CEP',
            $CEP,
            false,
            $identificador . 'CEP'
        );
        $this->dom->addChild(
            $this->enderDest,
            'UF',
            $UF,
            true,
            $identificador . 'Sigla da UF'
        );
        $this->dom->addChild(
            $this->enderDest,
            'cPais',
            $cPais,
            false,
            $identificador . 'Código do país'
        );
        $this->dom->addChild(
            $this->enderDest,
            'xPais',
            $xPais,
            false,
            $identificador . 'Nome do país'
        );
        $node = $this->dest->getElementsByTagName("email")->item(0);
        $this->dest->insertBefore($this->enderDest, $node);
        return $this->enderDest;
    }

    /**
     * Gera as tags para o elemento: "vPrest" (Valores da Prestação de Serviço)
     * #208
     * Nível: 1
     * Os parâmetros para esta função são todos os elementos da tag "vPrest" do
     * tipo elemento (Ele = E|CE|A) e nível 2
     *
     * @param string $vTPrest Valor Total da Prestação do Serviço
     * @param string $vRec Valor a Receber
     *
     * @return \DOMElement
     */
    public function vPrestTag($vTPrest = '', $vRec = '')
    {
        $identificador = '#208 <vPrest> - ';
        $this->vPrest = $this->dom->createElement('vPrest');
        $this->dom->addChild(
            $this->vPrest,
            'vTPrest',
            $vTPrest,
            true,
            $identificador . 'Valor Total da Prestação do Serviço'
        );
        $this->dom->addChild(
            $this->vPrest,
            'vRec',
            $vRec,
            true,
            $identificador . 'Valor a Receber'
        );
        return $this->vPrest;
    }

    /**
     * tagICMS
     * Informações do ICMS da Operação própria e ST N01 pai M01
     * tag NFe/infNFe/det[]/imposto/ICMS
     * @param string $cst
     * @param string $pRedBC
     * @param string $vBC
     * @param string $pICMS
     * @param string $vICMS
     * @param string $vBCSTRet
     * @param string $vICMSSTRet
     * @param string $pICMSSTRet
     * @param string $vCred
     * @param string $vTotTrib
     * @param bool $outraUF
     * @param string $vBCUFFim
     * @param string $pFCPUFFim
     * @param string $pICMSUFFim
     * @param string $pICMSInter
     * @param string $pICMSInterPart
     * @param string $vFCPUFFim
     * @param string $vICMSUFFim
     * @param string $vICMSUFIni
     * @return DOMElement
     */
    public function icmsTag(
        $cst = '',
        $pRedBC = '',
        $vBC = '',
        $pICMS = '',
        $vICMS = '',
        $vBCSTRet = '',
        $vICMSSTRet = '',
        $pICMSSTRet = '',
        $vCred = '',
        $vTotTrib = 0,
        $outraUF = false,
        $vBCUFFim = '',
        $pFCPUFFim = '',
        $pICMSUFFim = '',
        $pICMSInter = '',
        $pICMSInterPart = '',
        $vFCPUFFim = '',
        $vICMSUFFim = 0,
        $vICMSUFIni = 0
    ) {
        $identificador = 'N01 <ICMSxx> - ';
        switch ($cst) {
            case '00':
                $icms = $this->dom->createElement("ICMS00");
                $this->dom->addChild($icms, 'CST', $cst, true, "$identificador  Tributação do ICMS = 00");
                $this->dom->addChild($icms, 'vBC', $vBC, true, "$identificador  Valor da BC do ICMS");
                $this->dom->addChild($icms, 'pICMS', $pICMS, true, "$identificador  Alíquota do imposto");
                $this->dom->addChild($icms, 'vICMS', $vICMS, true, "$identificador  Valor do ICMS");
                break;
            case '20':
                $icms = $this->dom->createElement("ICMS20");
                $this->dom->addChild($icms, 'CST', $cst, true, "$identificador  Tributação do ICMS = 20");
                $this->dom->addChild($icms, 'pRedBC', $pRedBC, true, "$identificador  Percentual da Redução de BC");
                $this->dom->addChild($icms, 'vBC', $vBC, true, "$identificador  Valor da BC do ICMS");
                $this->dom->addChild($icms, 'pICMS', $pICMS, true, "$identificador  Alíquota do imposto");
                $this->dom->addChild($icms, 'vICMS', $vICMS, true, "$identificador  Valor do ICMS");
                break;
            case '40':
                $icms = $this->dom->createElement("ICMS45");
                $this->dom->addChild($icms, 'CST', $cst, true, "$identificador  Tributação do ICMS = 40");
                break;
            case '41':
                $icms = $this->dom->createElement("ICMS45");
                $this->dom->addChild($icms, 'CST', $cst, true, "$identificador  Tributação do ICMS = 41");
                break;
            case '51':
                $icms = $this->dom->createElement("ICMS45");
                $this->dom->addChild($icms, 'CST', $cst, true, "$identificador  Tributação do ICMS = 51");
                break;
            case '60':
                $icms = $this->dom->createElement("ICMS60");
                $this->dom->addChild($icms, 'CST', $cst, true, "$identificador  Tributação do ICMS = 60");
                $this->dom->addChild($icms, 'vBCSTRet', $vBCSTRet, true, "$identificador  Valor BC do ICMS ST retido");
                $this->dom->addChild($icms, 'vICMSSTRet', $vICMSSTRet, true, "$identificador  Valor do ICMS ST retido");
                $this->dom->addChild($icms, 'pICMSSTRet', $pICMSSTRet, true, "$identificador  Valor do ICMS ST retido");
                if ($vCred > 0) {
                    $this->dom->addChild($icms, 'vCred', $vCred, false, "$identificador  Valor do Crédito");
                }
                break;
            case '90':
                if ($outraUF == true) {
                    $icms = $this->dom->createElement("ICMSOutraUF");
                    $this->dom->addChild($icms, 'CST', $cst, true, "$identificador  Tributação do ICMS = 90");
                    if ($pRedBC > 0) {
                        $this->dom->addChild($icms, 'pRedBCOutraUF', $pRedBC, false, "$identificador Percentual Red "
                            . "BC Outra UF");
                    }
                    $this->dom->addChild($icms, 'vBCOutraUF', $vBC, true, "$identificador Valor BC ICMS Outra UF");
                    $this->dom->addChild($icms, 'pICMSOutraUF', $pICMS, true, "$identificador Alíquota do "
                        . "imposto Outra UF");
                    $this->dom->addChild($icms, 'vICMSOutraUF', $vICMS, true, "$identificador Valor ICMS Outra UF");
                } else {
                    $icms = $this->dom->createElement("ICMS90");
                    $this->dom->addChild($icms, 'CST', $cst, true, "$identificador Tributação do ICMS = 90");
                    if ($pRedBC > 0) {
                        $this->dom->addChild($icms, 'pRedBC', $pRedBC, false, "$identificador Percentual Redução BC");
                    }
                    $this->dom->addChild($icms, 'vBC', $vBC, true, "$identificador  Valor da BC do ICMS");
                    $this->dom->addChild($icms, 'pICMS', $pICMS, true, "$identificador  Alíquota do imposto");
                    $this->dom->addChild($icms, 'vICMS', $vICMS, true, "$identificador  Valor do ICMS");
                    if ($vCred > 0) {
                        $this->dom->addChild($icms, 'vCred', $vCred, false, "$identificador  Valor do Crédido");
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
        if ($vTotTrib > 0) {
            $this->dom->addChild($this->imp, 'vTotTrib', $vTotTrib, false, "$identificador Valor Total dos Tributos");
        }
        
        if ($vICMSUFFim > 0 || $vICMSUFIni > 0) {
            $icmsDifal = $this->dom->createElement("ICMSUFFim");
            $this->dom->addChild($icmsDifal, 'vBCUFFim', $vBCUFFim, true, "$identificador Valor da BC do ICMS na UF 
                de término da prestação do serviço de transporte");
            $this->dom->addChild($icmsDifal, 'pFCPUFFim', $pFCPUFFim, true, "$identificador Percentual do ICMS 
                relativo ao Fundo de Combate à pobreza (FCP) na UF de término da prestação do serviço de 
                transporte");
            $this->dom->addChild($icmsDifal, 'pICMSUFFim', $pICMSUFFim, true, "$identificador Alíquota interna da UF 
                de término da prestação do serviço de transporte");
            $this->dom->addChild($icmsDifal, 'pICMSInter', $pICMSInter, true, "$identificador Alíquota interestadual 
                das UF envolvidas");
            $this->dom->addChild($icmsDifal, 'pICMSInterPart', $pICMSInterPart, true, "$identificador Percentual 
                provisório de partilha entre os estados");
            $this->dom->addChild($icmsDifal, 'vFCPUFFim', $vFCPUFFim, true, "$identificador Valor do ICMS relativo 
                ao Fundo de Combate á Pobreza (FCP) da UF de término da prestação");
            $this->dom->addChild($icmsDifal, 'vICMSUFFim', $vICMSUFFim, true, "$identificador Valor do ICMS de 
                partilha para a UF de término da prestação do serviço de transporte");
            $this->dom->addChild($icmsDifal, 'vICMSUFIni', $vICMSUFIni, true, "$identificador Valor do ICMS de 
                partilha para a UF de início da prestação do serviço de transporte");
            
            $this->imp->appendChild($icmsDifal);
        }

        return $tagIcms;
    }

    /**
     * Gera as tags para o elemento: "Comp" (Componentes do Valor da Prestação)
     * #211
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "Comp" do
     * tipo elemento (Ele = E|CE|A) e nível 3
     *
     * @param string $xNome Nome do componente
     * @param string $vComp Valor do componente
     *
     * @return \DOMElement
     */
    public function compTag($xNome = '', $vComp = '')
    {
        $identificador = '#65 <pass> - ';
        $this->comp[] = $this->dom->createElement('Comp');
        $posicao = (integer)count($this->comp) - 1;
        $this->dom->addChild(
            $this->comp[$posicao],
            'xNome',
            $xNome,
            false,
            $identificador . 'Nome do componente'
        );
        $this->dom->addChild(
            $this->comp[$posicao],
            'vComp',
            $vComp,
            false,
            $identificador . 'Valor do componente'
        );
        return $this->comp[$posicao];
    }

    /**
     * Tag raiz do documento xml
     * Função chamada pelo método [ monta ]
     * @return \DOMElement
     */
    private function zCTeTag()
    {
        if (empty($this->CTe)) {
            $this->CTe = $this->dom->createElement('CTe');
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
    private function zEntregaTag()
    {
        $this->entrega = $this->dom->createElement('Entrega');
        return $this->entrega;
    }

    public function infCTeNormTag()
    {
        $this->infCTeNorm = $this->dom->createElement('infCTeNorm');
        return $this->infCTeNorm;
    }

    /**
     * Gera as tags para o elemento: "Comp" (Informações da Carga do CT-e)
     * #253
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "infCarga"
     * @param string $vCarga Valor total da carga
     * @param string $proPred Produto predominante
     * @param string $xOutCat Outras características da carga
     * @param string $vCargaAverb
     *
     * @return \DOMElement
     */
    public function infCargaTag($vCarga = '', $proPred = '', $xOutCat = '', $vCargaAverb = '')
    {
        $identificador = '#253 <infCarga> - ';
        $this->infCarga = $this->dom->createElement('infCarga');
        $this->dom->addChild($this->infCarga, 'vCarga', $vCarga, false, $identificador . 'Valor Total da Carga');
        $this->dom->addChild($this->infCarga, 'proPred', $proPred, true, $identificador . 'Produto Predominante');
        $this->dom->addChild($this->infCarga, 'xOutCat', $xOutCat, false, $identificador . 'Outras Caract. da Carga');
        $this->dom->addChild($this->infCarga, 'vCargaAverb', $vCargaAverb, false, $identificador . 'Valor da Carga para 
            efeito de averbação');

        return $this->infCarga;
    }

    /**
     * Gera as tags para o elemento: "infQ" (Informações de quantidades da Carga do CT-e)
     * #257
     * Nível: 3
     * Os parâmetros para esta função são todos os elementos da tag "infQ"
     * @param string $cUnid Código da Unidade de Medida
     * @param string $tpMed Tipo da Medida
     * @param string $qCarga Quantidade
     * @return mixed
     */
    public function infQTag($cUnid = '', $tpMed = '', $qCarga = '')
    {
        $identificador = '#257 <infQ> - ';
        $this->infQ[] = $this->dom->createElement('infQ');
        $posicao = (integer)count($this->infQ) - 1;
        $this->dom->addChild($this->infQ[$posicao], 'cUnid', $cUnid, true, $identificador . 'Código da 
            Unidade de Medida');
        $this->dom->addChild($this->infQ[$posicao], 'tpMed', $tpMed, true, $identificador . 'Tipo da Medida');
        $this->dom->addChild($this->infQ[$posicao], 'qCarga', $qCarga, true, $identificador . 'Quantidade');

        return $this->infQ[$posicao];
    }

    public function infDocTag()
    {
        $this->infDoc = $this->dom->createElement('infDoc');
        return $this->infDoc;
    }

    /**
     * Documentos de Transporte Anterior
     * @return DOMElement|\DOMNode
     */
    public function docAntTag()
    {
        $this->docAnt = $this->dom->createElement('docAnt');
        return $this->docAnt;
    }

    /**
     * Informações de identificação dos documentos de Transporte Anterior
     * @return array|DOMElement
     */
    public function idDocAntTag()
    {
        $this->idDocAnt = $this->dom->createElement('idDocAnt');
        return $this->idDocAnt;
    }

    /**
     * Gera as tags para o elemento: "infNF" (Informações das NF)
     * #262
     * Nível: 3
     * @param string $nRoma
     * @param string $nPed
     * @param string $mod
     * @param string $serie
     * @param string $nDoc
     * @param string $dEmi
     * @param string $vBC
     * @param string $vICMS
     * @param string $vBCST
     * @param string $vST
     * @param string $vProd
     * @param string $vNF
     * @param string $nCFOP
     * @param string $nPeso
     * @param string $PIN
     * @param string $dPrev
     * @return mixed
     */
    public function infNFTag(
        $nRoma = '',
        $nPed = '',
        $mod = '',
        $serie = '',
        $nDoc = '',
        $dEmi = '',
        $vBC = '',
        $vICMS = '',
        $vBCST = '',
        $vST = '',
        $vProd = '',
        $vNF = '',
        $nCFOP = '',
        $nPeso = '',
        $PIN = '',
        $dPrev = ''
    ) {
        $identificador = '#262 <infNF> - ';
        $this->infNF[] = $this->dom->createElement('infNF');
        $posicao = (integer)count($this->infNF) - 1;

        $this->dom->addChild($this->infNF[$posicao], 'nRoma', $nRoma, false, $identificador . 'Número do 
            Romaneio da NF');
        $this->dom->addChild($this->infNF[$posicao], 'nPed', $nPed, false, $identificador . 'Número do 
            Pedido da NF');
        $this->dom->addChild($this->infNF[$posicao], 'mod', $mod, true, $identificador . 'Modelo da 
            Nota Fiscal');
        $this->dom->addChild($this->infNF[$posicao], 'serie', $serie, true, $identificador . 'Série');
        $this->dom->addChild($this->infNF[$posicao], 'nDoc', $nDoc, true, $identificador . 'Número');
        $this->dom->addChild($this->infNF[$posicao], 'dEmi', $dEmi, true, $identificador . 'Data de Emissão');
        $this->dom->addChild($this->infNF[$posicao], 'vBC', $vBC, true, $identificador . 'Valor da Base 
            de Cálculo do ICMS');
        $this->dom->addChild($this->infNF[$posicao], 'vICMS', $vICMS, true, $identificador . 'Valor Total 
            do ICMS');
        $this->dom->addChild($this->infNF[$posicao], 'vBCST', $vBCST, true, $identificador . 'Valor da 
            Base de Cálculo do ICMS ST');
        $this->dom->addChild($this->infNF[$posicao], 'vST', $vST, true, $identificador . 'Valor Total 
            do ICMS ST');
        $this->dom->addChild($this->infNF[$posicao], 'vProd', $vProd, true, $identificador . 'Valor Total
            dos Produtos');
        $this->dom->addChild($this->infNF[$posicao], 'vNF', $vNF, true, $identificador . 'Valor Total da NF');
        $this->dom->addChild($this->infNF[$posicao], 'nCFOP', $nCFOP, true, $identificador . 'CFOP Predominante');
        $this->dom->addChild($this->infNF[$posicao], 'nPeso', $nPeso, false, $identificador . 'Peso total em Kg');
        $this->dom->addChild($this->infNF[$posicao], 'PIN', $PIN, false, $identificador . 'PIN SUFRAMA');
        $this->dom->addChild($this->infNF[$posicao], 'dPrev', $dPrev, false, $identificador . 'Data prevista
            de entrega');

        return $this->infNF[$posicao];
    }

    /**
     * Gera as tags para o elemento: "infNFe" (Informações das NF-e)
     * #297
     * Nível: 3
     * @param string $chave
     * @param string $PIN
     * @param string $dPrev
     * @return mixed
     */
    public function infNFeTag($chave = '', $PIN = '', $dPrev = '')
    {
        $identificador = '#297 <infNFe> - ';
        $this->infNFe[] = $this->dom->createElement('infNFe');
        $posicao = (integer)count($this->infNFe) - 1;
        $this->dom->addChild(
            $this->infNFe[$posicao],
            'chave',
            $chave,
            true,
            $identificador . 'Chave de acesso da NF-e'
        );
        $this->dom->addChild(
            $this->infNFe[$posicao],
            'PIN',
            $PIN,
            false,
            $identificador . 'PIN SUFRAMA'
        );
        $this->dom->addChild(
            $this->infNFe[$posicao],
            'dPrev',
            $dPrev,
            false,
            $identificador . 'Data prevista de entrega'
        );
        return $this->infNFe[$posicao];
    }

    /**
     * Gera as tags para o elemento: "infOutros" (Informações dos demais documentos)
     * #319
     * Nível: 3
     * @param string $tpDoc
     * @param string $descOutros
     * @param string $nDoc
     * @param string $dEmi
     * @param string $vDocFisc
     * @param string $dPrev
     * @return mixed
     */
    public function infOutrosTag($tpDoc = '', $descOutros = '', $nDoc = '', $dEmi = '', $vDocFisc = '', $dPrev = '')
    {
        $ident = '#319 <infOutros> - ';
        $this->infOutros[] = $this->dom->createElement('infOutros');
        $posicao = (integer)count($this->infOutros) - 1;
        $this->dom->addChild($this->infOutros[$posicao], 'tpDoc', $tpDoc, true, $ident . 'Tipo '
            . 'de documento originário');
        $this->dom->addChild($this->infOutros[$posicao], 'descOutros', $descOutros, false, $ident . 'Descrição '
            . 'do documento');
        $this->dom->addChild($this->infOutros[$posicao], 'nDoc', $nDoc, false, $ident . 'Número '
            . 'do documento');
        $this->dom->addChild($this->infOutros[$posicao], 'dEmi', $dEmi, false, $ident . 'Data de Emissão');
        $this->dom->addChild($this->infOutros[$posicao], 'vDocFisc', $vDocFisc, false, $ident . 'Valor '
            . 'do documento');
        $this->dom->addChild($this->infOutros[$posicao], 'dPrev', $dPrev, false, $ident . 'Data '
            . 'prevista de entrega');
        return $this->infOutros[$posicao];
    }

    /**
     * Gera as tags para o elemento: "emiDocAnt" (Informações dos CT-es Anteriores)
     * #345
     * Nível: 3
     * @param string $CNPJ
     * @param string $CPF
     * @param string $IE
     * @param string $UF
     * @param string $xNome
     * @return mixed
     */
    public function emiDocAntTag($CNPJ = '', $CPF = '', $IE = '', $UF = '', $xNome = '')
    {
        $identificador = '#345 <emiDocAnt> - ';
        $this->emiDocAnt[] = $this->dom->createElement('emiDocAnt');
        $posicao = (integer)count($this->emiDocAnt) - 1;
        if ($CNPJ != '') {
            $this->dom->addChild($this->emiDocAnt[$posicao], 'CNPJ', $CNPJ, true, $identificador . 'Número do CNPJ');
            $this->dom->addChild($this->emiDocAnt[$posicao], 'IE', $IE, true, $identificador . 'Inscrição Estadual');
            $this->dom->addChild($this->emiDocAnt[$posicao], 'UF', $UF, true, $identificador . 'Sigla da UF');
        } else {
            $this->dom->addChild($this->emiDocAnt[$posicao], 'CPF', $CPF, true, $identificador . 'Número do CPF');
        }
        $this->dom->addChild($this->emiDocAnt[$posicao], 'xNome', $xNome, true, $identificador . 'Razão Social ou '
            . ' Nome do Expedidor');

        return $this->emiDocAnt[$posicao];
    }

    /**
     * Gera as tags para o elemento: "idDocAntEle" (Informações dos CT-es Anteriores)
     * #358
     * Nível: 4
     * @param string $chCTe
     * @return mixed
     */
    public function idDocAntEleTag($chCTe = '')
    {
        $identificador = '#358 <idDocAntEle> - ';
        $this->idDocAntEle[] = $this->dom->createElement('idDocAntEle');
        $posicao = (integer)count($this->idDocAntEle) - 1;
        $this->dom->addChild($this->idDocAntEle[$posicao], 'chCTe', $chCTe, true, $identificador . 'Chave de '
            . 'Acesso do CT-e');

        return $this->idDocAntEle[$posicao];
    }


    /**
     * Gera as tags para o elemento: "seg" (Informações de Seguro da Carga)
     * #360
     * Nível: 2
     * @param int $respSeg
     * @param string $xSeg
     * @param string $nApol
     * @return mixed
     */
    public function segTag($respSeg = 4, $xSeg = '', $nApol = '')
    {
        $identificador = '#360 <seg> - ';
        $this->seg[] = $this->dom->createElement('seg');
        $posicao = (integer)count($this->seg) - 1;

        $this->dom->addChild($this->seg[$posicao], 'respSeg', $respSeg, true, $identificador . 'Responsável 
            pelo Seguro');
        $this->dom->addChild($this->seg[$posicao], 'xSeg', $xSeg, false, $identificador . 'Nome da 
            Seguradora');
        $this->dom->addChild($this->seg[$posicao], 'nApol', $nApol, false, $identificador . 'Número da Apólice');
        return $this->seg[$posicao];
    }

    /**
     * Gera as tags para o elemento: "infModal" (Informações do modal)
     * #366
     * Nível: 2
     * @param string $versaoModal
     * @return DOMElement|\DOMNode
     */
    public function infModalTag($versaoModal = '')
    {
        $identificador = '#366 <infModal> - ';
        $this->infModal = $this->dom->createElement('infModal');
        $this->infModal->setAttribute('versaoModal', $versaoModal);
        return $this->infModal;
    }

    /**
     * Leiaute - Rodoviário
     * Gera as tags para o elemento: "rodo" (Informações do modal Rodoviário)
     * #1
     * Nível: 0
     * @param string $RNTRC
     * @return DOMElement|\DOMNode
     */
    public function rodoTag($RNTRC = '')
    {
        $identificador = '#1 <rodo> - ';
        $this->rodo = $this->dom->createElement('rodo');
        $this->dom->addChild($this->rodo, 'RNTRC', $RNTRC, true, $identificador . 'Registro nacional de transportadores
            rodoviários de carga');

        return $this->rodo;
    }

    /**
     * Leiaute - Rodoviário
     * Gera as tags para o elemento: "veic" (Dados dos Veículos)
     * #21
     * Nível: 1
     * @param string $cInt
     * @param string $RENAVAM
     * @param string $placa
     * @param string $tara
     * @param string $capKG
     * @param string $capM3
     * @param string $tpProp
     * @param string $tpVeic
     * @param string $tpRod
     * @param string $tpCar
     * @param string $UF
     * @param string $CPF
     * @param string $CNPJ
     * @param string $RNTRC
     * @param string $xNome
     * @param string $IE
     * @param string $propUF
     * @param string $tpPropProp
     * @return mixed
     */
    public function veicTag(
        $cInt = '',
        $RENAVAM = '',
        $placa = '',
        $tara = '',
        $capKG = '',
        $capM3 = '',
        $tpProp = '',
        $tpVeic = '',
        $tpRod = '',
        $tpCar = '',
        $UF = '',
        $CPF = '',
        // Informar os zeros não significativos.
        $CNPJ = '',
        // Informar os zeros não significativos.
        $RNTRC = '',
        // Registro obrigatório do proprietário
        $xNome = '',
        // Nome do proprietário
        $IE = '',
        // Inscrição estadual caso seja Pessoa Jurídica
        $propUF = '',
        // Sigla da UF,
        $tpPropProp = ''
    ) {
        $identificador = '#21 <veic> - ';
        $this->veic[] = $this->dom->createElement('veic');
        $posicao = (integer)count($this->veic) - 1;
        if ($cInt != '') {
            $this->dom->addChild(
                $this->veic[$posicao],
                'cInt',
                $cInt,
                false,
                $identificador . 'Código interno do veículo'
            );
        }
        $this->dom->addChild(
            $this->veic[$posicao],
            'RENAVAM',
            $RENAVAM,
            false,
            $identificador . 'RENAVAM do veículo'
        );
        $this->dom->addChild(
            $this->veic[$posicao],
            'placa',
            $placa,
            false,
            $identificador . 'Placa do veículo'
        );
        $this->dom->addChild(
            $this->veic[$posicao],
            'tara',
            $tara,
            false,
            $identificador . 'Tara em KG'
        );
        $this->dom->addChild(
            $this->veic[$posicao],
            'capKG',
            $capKG,
            false,
            $identificador . 'Capacidade em KG'
        );
        $this->dom->addChild(
            $this->veic[$posicao],
            'capM3',
            $capM3,
            false,
            $identificador . 'Capacidade em M3'
        );
        $this->dom->addChild(
            $this->veic[$posicao],
            'tpProp',
            $tpProp,
            false,
            $identificador . 'Tipo de Propriedade de veículo'
        );
        $this->dom->addChild(
            $this->veic[$posicao],
            'tpVeic',
            $tpVeic,
            false,
            $identificador . 'Tipo do veículo'
        );
        $this->dom->addChild(
            $this->veic[$posicao],
            'tpRod',
            $tpRod,
            false,
            $identificador . 'Tipo do Rodado'
        );
        $this->dom->addChild(
            $this->veic[$posicao],
            'tpCar',
            $tpCar,
            false,
            $identificador . 'Tipo de Carroceria'
        );
        $this->dom->addChild(
            $this->veic[$posicao],
            'UF',
            $UF,
            false,
            $identificador . 'UF em que veículo está licenciado'
        );
        if ($tpProp == 'T') { // CASO FOR VEICULO DE TERCEIRO
            $this->prop[] = $this->dom->createElement('prop');
            $p = (integer)count($this->prop) - 1;
            if ($CNPJ != '') {
                $this->dom->addChild(
                    $this->prop[$p],
                    'CNPJ',
                    $CNPJ,
                    true,
                    $identificador . 'CNPJ do proprietario'
                );
            } elseif ($CPF != '') {
                $this->dom->addChild(
                    $this->prop[$p],
                    'CPF',
                    $CPF,
                    true,
                    $identificador . 'CPF do proprietario'
                );
            }
            $this->dom->addChild(
                $this->prop[$p],
                'RNTRC',
                $RNTRC,
                true,
                $identificador . 'RNTRC do proprietario'
            );
            $this->dom->addChild(
                $this->prop[$p],
                'xNome',
                $xNome,
                true,
                $identificador . 'Nome do proprietario'
            );
            $this->dom->addChild(
                $this->prop[$p],
                'IE',
                $IE,
                true,
                $identificador . 'IE do proprietario'
            );
            $this->dom->addChild(
                $this->prop[$p],
                'UF',
                $propUF,
                true,
                $identificador . 'UF do proprietario'
            );
            $this->dom->addChild(
                $this->prop[$p],
                'tpProp',
                $tpPropProp,
                true,
                $identificador . 'Tipo Proprietário'
            );
            $this->dom->appChild($this->veic[$posicao], $this->prop[$p], 'Falta tag "prop"');
        }
        return $this->veic[$posicao];
    }


    /**
     * Leiaute - Rodoviário
     * Gera as tags para o elemento: "moto" (Informações do(s) Motorista(s))
     * #43
     * Nível: 1
     * @param string $xNome
     * @param string $CPF
     * @return mixed
     */
    public function motoTag($xNome = '', $CPF = '')
    {
        $identificador = '#43 <moto> - ';
        $this->moto[] = $this->dom->createElement('moto');
        $posicao = (integer)count($this->moto) - 1;
        $this->dom->addChild(
            $this->moto[$posicao],
            'xNome',
            $xNome,
            false,
            $identificador . 'Nome do motorista'
        );
        $this->dom->addChild(
            $this->moto[$posicao],
            'CPF',
            $CPF,
            false,
            $identificador . 'CPF do motorista'
        );
        return $this->moto[$posicao];
    }

    /**
     * Gera as tags para o elemento: "infCteComp" (Detalhamento do CT-e complementado)
     * #410
     * Nível: 1
     * @param string $chave
     * @return DOMElement|\DOMNode
     */
    public function infCTeComp($chave = '')
    {
        $identificador = '#410 <infCteComp> - ';
        $this->infCteComp = $this->dom->createElement('infCteComp');
        $this->dom->addChild(
            $this->infCteComp,
            'chave',
            $chave,
            true,
            $identificador . ' Chave do CT-e complementado'
        );
        return $this->infCteComp;
    }

    /**
     * Gera as tags para o elemento: "infCteAnu" (Detalhamento do CT-e de Anulação)
     * #411
     * Nível: 1
     * @param string $chave
     * @param string $data
     * @return DOMElement|\DOMNode
     */
    public function infCteAnuTag($chave = '', $data = '')
    {
        $identificador = '#411 <infCteAnu> - ';
        $this->infCteAnu = $this->dom->createElement('infCteAnu');
        $this->dom->addChild(
            $this->infCteAnu,
            'chCte',
            $chave,
            true,
            $identificador . ' Chave do CT-e anulado'
        );
        $this->dom->addChild(
            $this->infCteAnu,
            'dEmi',
            $data,
            true,
            $identificador . ' Data de Emissão do CT-e anulado'
        );
        return $this->infCteAnu;
    }
}
