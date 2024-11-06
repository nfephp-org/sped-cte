<?php

namespace NFePHP\CTe;

/**
 *
 * @category  Library
 * @package   nfephp-org/sped-cte
 * @copyright 2009-2024 NFePHP
 * @name      MakeCTeSimp.php
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL v.3
 * @link      http://github.com/nfephp-org/sped-cte for the canonical source repository
 * @author    Cleiton Perin <cperin20 at gmail dot com>
 * @author    Paulo Henrique de Castro <prpaulohcmg at hotmail dot com>
 * @note      Esta classe foi baseada na implementação original do CTe de Cleiton Perin
 */

use DOMElement;
use NFePHP\Common\DOMImproved as Dom;
use NFePHP\Common\Keys;
use NFePHP\Common\Strings;
use RuntimeException;
use stdClass;

class MakeCTeSimp
{
    /**
     * @var array
     */
    public $errors = [];

    /**
     * versao numero da versão do xml da CTe
     * @var string
     */
    public $versao = '4.00';
    /**
     * chave da CTe
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
     * Tipo do CTe
     * @var int
     */
    private $tpCTe = 0;
    /**
     * Tag CTe
     * @var \DOMNode
     */
    private $CTeSimp = '';
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
    private $fluxo = null;
    /**
     * Passagem
     * @var array
     */
    private $pass = [];
    /**
     * Entrega no intervalo de horário definido
     * @var \DOMNode
     */
    private $noInter = '';
    /**
     * Campo de uso livre do contribuinte
     * @var array
     */
    private $obsCont = [];
    /**
     * Campo de uso livre do contribuinte
     * @var array
     */
    private $obsFisco = [];
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
    private $toma = '';
    /**
     * Componentes do Valor da Prestação
     * @var array
     */
    private $comp = [];
    /**
     * Informações relativas aos Impostos
     * @var \DOMNode
     */
    private $imp = '';
    /**
     * Informações da Carga do CT-e
     * @var \DOMNode
     */
    private $infCarga = '';
    /**
     * Detalhamento das entregas/prestações do CTe Simplificado
     * @var array
     */
    private $det = [];
    /**
     * Informações de quantidades da Carga do CT-e
     * @var \DOMNode
     */
    private $infQ = [];
    /**
     * Informações das NF
     * @var array
     */
    private $infNF = [];
    /**
     * Informações das NF-e
     * @var array
     */
    private $infNFe = [];
    /**
     * Informações das infDocAnt
     * @var array
     */
    private $infDocAnt = [];
    /**
     * Documentos de Transporte Anterior
     * @var \DOMNode
     */
    private $docAnt = [];
    /**
     * Informações do modal
     * @var \DOMNode
     */
    private $infModal = '';
    /**
     * Preenchido quando for transporte de produtos classificados pela ONU como perigosos.
     * @var array
     */
    private $peri = [];
    /**
     * Dados da cobrança do CT-e
     * @var \DOMNode
     */
    private $cobr = '';
    /**
     * Informações do CT-e de substituição
     * @var \DOMNode
     */
    private $infCteSub = '';
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
     * Informações do modal Ferroviario
     * @var \DOMNode
     */
    private $ferrov = '';
    /**
     * Informações das Ferrovias Envolvidas
     * @var array
     */
    private $ferroEnv = [];
    /**
     * Informações do modal Aquaviario
     * @var \DOMNode
     */
    private $aquav = '';
    /**
     * Informações de Lacre para modal Aquaviario
     * @var array
     */
    private $lacre = [];
    /**
     * Informações de Balsa para modal Aquaviario
     * @var array
     */
    private $balsa = [];
    /**
     * Informações de Container para modal Aquaviario
     * @var array
     */
    private $detCont = [];
    /**
     * Informações de NF de conteiner para modal Aquaviario
     * @var array
     */
    private $infNFCont = [];
    /**
     * Informações dos documentos de conteiner para modal Aquaviario
     * @var array
     */
    private $infDocCont = [];
    /**
     * Informações de NFe de conteiner para modal Aquaviario
     * @var array
     */
    private $infNFeCont = [];
    /**
     * Informações do modal Dutoviário
     * @var \DOMNode
     */
    private $duto = '';
    /**
     * Ordens de Coleta associados
     * @var array
     */
    private $occ = [];
    /**
     * Informações do CTe Multimodal
     * @var array
     */
    private $multimodal = '';
    /**
     * Informações do seguro no CTe Multimodal
     * @var array
     */
    private $segMultim = '';
    /**
     * Autorizados para download do XML do DF-e
     * @var array
     */
    private $autXML = [];
    /**
     * @var DOMElement
     */
    protected $total;
    /**
     * @var DOMElement
     */
    protected $infRespTec;
    /**
     *
     */
    protected $cteHomologacao = 'CTE EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL';
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
     * Retorns the key number of CTe (44 digits)
     * @return string
     */
    public function getChave()
    {
        return $this->chCTe;
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
        $this->buildCTe();
        $node = $this->ide->getElementsByTagName("dhCont")->item(0);

        $this->dom->appChild($this->infCte, $this->ide, 'Falta tag "infCte"');
        if (!empty($this->compl)) {
            foreach ($this->obsCont as $obsCont) {
                $this->dom->appChild($this->compl, $obsCont, 'Falta tag "compl"');
            }
            foreach ($this->obsFisco as $obsFisco) {
                $this->dom->appChild($this->compl, $obsFisco, 'Falta tag "compl"');
            }
            $this->dom->appChild($this->infCte, $this->compl, 'Falta tag "infCte"');
        }

        // inclui o Node enderEmit dentro do emit antes da tag CRT
        $node = $this->emit->getElementsByTagName("CRT")->item(0);
        $this->emit->insertBefore($this->enderEmit, $node);

        $this->dom->appChild($this->infCte, $this->emit, 'Falta tag "infCte"');

        $this->dom->appChild($this->infCte, $this->toma, 'Falta tag "toma"');

        $this->dom->appChild($this->infCte, $this->infCarga, 'Falta tag "infCarga"');
        foreach ($this->infQ as $infQ) {
            $node = $this->infCarga->getElementsByTagName("vCargaAverb")->item(0);
            if (!empty($node)) {
                $this->infCarga->insertBefore($infQ, $node);
            } else {
                $this->dom->appChild($this->infCarga, $infQ, 'Falta tag "infQ"');
            }
        }

        // Monta o "Detalhamento das entregas/prestações do CTe Simplificado"
        foreach ($this->det as $indiceDet => $det) {
            $this->dom->appChild($this->infCte, $det, 'Falta tag "det"');

            // Monta os "Componentes do Valor da Prestação "
            if (array_key_exists($indiceDet, $this->comp)) {
                foreach ($this->comp[$indiceDet] as $comp) {
                    $this->dom->appChild($this->det[$indiceDet], $comp, 'Falta tag "Comp"');
                }
            }
            // Monta as "Informações das NF-e"
            if (array_key_exists($indiceDet, $this->infNFe)) {
                foreach ($this->infNFe[$indiceDet] as $infNFe) {
                    $this->dom->appChild($this->det[$indiceDet], $infNFe, 'Falta tag "infNFe"');
                }
            }
            // Monta os "Documentos anteriores"
            if (array_key_exists($indiceDet, $this->infDocAnt)) {
                foreach ($this->infDocAnt[$indiceDet] as $infDocAnt) {
                    $this->dom->appChild($this->det[$indiceDet], $infDocAnt, 'Falta tag "infDocAnt"');
                }
            }
        }

        // infModal
        $this->dom->appChild($this->infCte, $this->infModal, 'Falta tag "infModal"');

        if ($this->modal == '01') {
            if ($this->rodo) {
                foreach ($this->occ as $occ) {
                    $this->dom->appChild($this->rodo, $occ, 'Falta tag "occ"');
                }
                $this->dom->appChild($this->infModal, $this->rodo, 'Falta tag "rodo"');
            }
        } elseif ($this->modal == '02') {
            foreach ($this->peri as $peri) {
                $this->dom->appChild($this->aereo, $peri, 'Falta tag "aereo"');
            }
            $this->dom->appChild($this->infModal, $this->aereo, 'Falta tag "aereo"');
        } elseif ($this->modal == '03') {
            $this->dom->appChild($this->infModal, $this->aquav, 'Falta tag "aquav"');
            if ($this->detCont != []) { //Caso tenha informações de conteiner
                foreach ($this->detCont as $indice => $conteiner) {
                    $this->dom->appChild($this->aquav, $conteiner, 'Falta tag "detCont"');
                    if (array_key_exists($indice, $this->lacre)) {
                        foreach ($this->lacre[$indice] as $lacre) {
                            $this->dom->appChild($this->detCont[$indice], $lacre, 'Falta tag "lacre"');
                        }
                    }
                    if (array_key_exists($indice, $this->infNFCont)) {
                        foreach ($this->infNFCont[$indice] as $infNFCont) {
                            $this->dom->appChild($this->infDocCont[$indice], $infNFCont, 'Falta tag "infNF"');
                        }
                    }
                    if (array_key_exists($indice, $this->infNFeCont)) {
                        foreach ($this->infNFeCont[$indice] as $infNFeCont) {
                            $this->dom->appChild($this->infDocCont[$indice], $infNFeCont, 'Falta tag "infNFe"');
                        }
                    }
                    if (array_key_exists($indice, $this->infDocCont)) {
                        $this->dom->appChild(
                            $this->detCont[$indice],
                            $this->infDocCont[$indice],
                            'Falta tag "infDoc"'
                        );
                    }
                }
            }
            foreach ($this->balsa as $balsa) {
                $this->aquav->insertBefore($balsa, $this->aquav->getElementsByTagName('nViag')->item(0));
            }
        } elseif ($this->modal == '04') {
            foreach ($this->ferroEnv as $ferroEnv) {
                $node = $this->ferrov->getElementsByTagName('trafMut')->item(0);
                $this->dom->appChild($node, $ferroEnv, 'Falta tag "multimodal"');
            }
            $this->dom->appChild($this->infModal, $this->ferrov, 'Falta tag "ferrov"');
        } elseif ($this->modal == '05') {
            $this->dom->appChild($this->infModal, $this->duto, 'Falta tag "duto"');
        } elseif ($this->modal == '06') {
            if (!empty($this->segMultim)) {
                $this->dom->appChild($this->multimodal, $this->segMultim, 'Falta tag "seg"');
            }
            $this->dom->appChild($this->infModal, $this->multimodal, 'Falta tag "multimodal"');
        } else {
            throw new \Exception('Modal não informado ou não suportado.');
        }

        if (!empty($this->cobr)) {
            $this->dom->appChild($this->infCte, $this->cobr, 'Falta tag "infCte"');
        }

        if ($this->tpCTe == 6 and !empty($this->infCteSub)) {
            $this->dom->appChild($this->infCte, $this->infCteSub, 'Falta tag "infCteSub"');
        }

        $this->dom->appChild($this->infCte, $this->imp, 'Falta tag "imp"');

        $this->dom->appChild($this->infCte, $this->total, 'Falta tag "total"');

        foreach ($this->autXML as $autXML) {
            $this->dom->appChild($this->infCte, $autXML, 'Falta tag "infCte"');
        }

        $this->dom->appChild($this->infCte, $this->infRespTec, 'Falta tag "infCte"');

        //[1] tag infCTe
        $this->dom->appChild($this->CTeSimp, $this->infCte, 'Falta tag "CTe"');
        //[0] tag CTe
        $this->dom->appendChild($this->CTeSimp);
        // testa da chave
        $this->checkCTeKey($this->dom);

        $this->xml = $this->dom->saveXML();
        if (count($this->errors) > 0) {
            throw new RuntimeException('Existem erros nas tags. Obtenha os erros com getErrors().');
        }
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
            'tpAmb',
            'tpCTe',
            'procEmi',
            'verProc',
            'cMunEnv',
            'xMunEnv',
            'UFEnv',
            'modal',
            'tpServ',
            'UFIni',
            'UFFim',
            'retira',
            'xDetRetira',
            'dhCont',
            'xJust'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $this->tpAmb = $std->tpAmb;
        $this->tpCTe = $std->tpCTe;
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
            substr(trim($std->natOp), 0, 60),
            true,
            $identificador . 'Natureza da Operação'
        );
        $this->dom->addChild(
            $this->ide,
            'mod',
            '57',
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
            true,
            $identificador . 'Digito Verificador da chave de acesso do CT-e',
            true
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
        $this->dom->addChild(
            $this->ide,
            'UFIni',
            $std->UFIni,
            true,
            $identificador . 'UF do início da prestação'
        );
        $this->dom->addChild(
            $this->ide,
            'UFFim',
            $std->UFFim,
            true,
            $identificador . 'UF do término da prestação'
        );
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
            'dhCont',
            $std->dhCont,
            false,
            $identificador . 'Data e Hora da entrada em contingência'
        );
        $this->dom->addChild(
            $this->ide,
            'xJust',
            substr(trim($std->xJust), 0, 256),
            false,
            $identificador . 'Justificativa da entrada em contingência'
        );
        return $this->ide;
    }

    /**
     * Gera as tags para o elemento: "enderToma" (Dados do endereço) e adiciona ao grupo "toma"
     * #45
     * Nível: 2
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
        if (!empty($this->toma)) {
            $this->toma->insertBefore($this->enderToma, $this->toma->getElementsByTagName("email")->item(0));
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

        if (!empty($this->fluxo)) {
            foreach ($this->pass as $pass) {
                $xDest = $this->fluxo->getElementsByTagName("xDest")->item(0);
                $xRota = $this->fluxo->getElementsByTagName("xRota")->item(0);
                if (!empty($xDest)) {
                    $this->fluxo->insertBefore($pass, $xDest);
                } elseif (!empty($xRota)) {
                    $this->fluxo->insertBefore($pass, $xRota);
                } else {
                    $this->dom->appChild($this->fluxo, $pass, 'Falta tag "fluxo"');
                }
            }
            $this->dom->appChild($this->compl, $this->fluxo, 'Falta tag "infCte"');
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
     * Gera as tags para o elemento: "fluxo" (Previsão do fluxo da carga)
     * #63
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "fluxo" do
     * tipo elemento (Ele = E|CE|A) e nível 2
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
        $pass = $this->dom->createElement('pass');
        $this->dom->addChild(
            $pass,
            'xPass',
            $std->xPass,
            false,
            $identificador . 'Sigla ou código interno da Filial/Porto/Estação/Aeroporto de Passagem'
        );
        return $this->pass[] = $pass;
    }


    /**
     * Gera as tags para o elemento: toma4 (Indicador do "papel" do tomador
     * do serviço no CT-e) e adiciona ao grupo ide
     * #37
     * Nível: 2
     * @param stdClass $std
     * @return \DOMElement
     */
    public function tagtoma($std)
    {
        $possible = [
            'toma',
            'indIEToma',
            'CNPJ',
            'CPF',
            'IE',
            'xNome',
            'ISUF',
            'fone',
            'email'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#68 <toma> - ';
        $this->toma = $this->dom->createElement('toma');
        $this->dom->addChild(
            $this->toma,
            'toma',
            $std->toma,
            true,
            $identificador . 'Tomador do Serviço'
        );
        $this->dom->addChild(
            $this->toma,
            'indIEToma',
            $std->indIEToma,
            true,
            $identificador . 'Indicador do papel do tomador na prestação do serviço'
        );
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
        }
        $this->dom->addChild(
            $this->toma,
            'IE',
            $std->IE,
            false,
            $identificador . 'Inscrição Estadual'
        );
        $xNome = $std->xNome;
        if ($this->tpAmb == '2') {
            $xNome = $this->cteHomologacao;
        }
        $this->dom->addChild(
            $this->toma,
            'xNome',
            $xNome,
            true,
            $identificador . 'Razão Social ou Nome'
        );
        $this->dom->addChild(
            $this->toma,
            'ISUF',
            $std->ISUF,
            false,
            $identificador . 'Inscrição na SUFRAMA'
        );
        $this->dom->addChild(
            $this->toma,
            'fone',
            $std->fone,
            false,
            $identificador . 'Telefone'
        );
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
     * Gera as tags para o elemento: "ObsCont" (Campo de uso livre do contribuinte)
     * #91
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "ObsCont" do
     * tipo elemento (Ele = E|CE|A) e nível 2
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
            'tag' => '<ObsCont>',
            'desc' => 'Campo de uso livre do contribuinte',
            'erro' => 'Tag deve aparecer de 0 a 10 vezes'
        );
        return false;
    }

    /**
     * Gera as tags para o elemento: "ObsFisco" (Campo de uso livre do contribuinte)
     * #94
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "ObsFisco" do tipo
     * elemento (Ele = E|CE|A) e nível 2
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
            'tag' => '<ObsFisco>',
            'desc' => 'Campo de uso livre do contribuinte',
            'erro' => 'Tag deve aparecer de 0 a 10 vezes'
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
            'CPF',
            'IE',
            'IEST',
            'xNome',
            'xFant',
            'CRT'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#97 <emit> - ';
        $this->emit = $this->dom->createElement('emit');
        if (!empty($std->CNPJ)) {
            $this->dom->addChild(
                $this->emit,
                'CNPJ',
                $std->CNPJ,
                true,
                $identificador . 'CNPJ do emitente'
            );
        } else {
            $this->dom->addChild(
                $this->emit,
                'CPF',
                $std->CPF,
                true,
                $identificador . 'CPF do emitente'
            );
        }
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
        $this->dom->addChild(
            $this->emit,
            'CRT',
            $std->CRT,
            true,
            $identificador . 'Código do Regime Tributário'
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
     * Gera as tags para o elemento: "total" (Valores Totais do CTe )
     * #216
     * Nível: 1
     * @return \DOMElement
     */
    public function tagtotal($std)
    {
        $possible = [
            'vTPrest',
            'vTRec'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#216 <total> - ';
        $this->total = $this->dom->createElement('total');
        $this->dom->addChild(
            $this->total,
            'vTPrest',
            $this->conditionalNumberFormatting($std->vTPrest),
            true,
            $identificador . 'Valor Total da Prestação do Serviço'
        );
        $this->dom->addChild(
            $this->total,
            'vTRec',
            $this->conditionalNumberFormatting($std->vTRec),
            true,
            $identificador . 'Valor a Receber'
        );
        return $this->total;
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
            'cst',
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
            'vICMSUFIni',
            'vICMSDeson',
            'cBenef'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = 'N01 <ICMSxx> - ';
        switch ($std->cst) {
            case '00':
                $icms = $this->dom->createElement("ICMS00");
                $this->dom->addChild(
                    $icms,
                    'CST',
                    $std->cst,
                    true,
                    "$identificador  Tributação do ICMS = 00"
                );
                $this->dom->addChild(
                    $icms,
                    'vBC',
                    $this->conditionalNumberFormatting($std->vBC),
                    true,
                    "$identificador  Valor da BC do ICMS"
                );
                $this->dom->addChild(
                    $icms,
                    'pICMS',
                    $this->conditionalNumberFormatting($std->pICMS),
                    true,
                    "$identificador  Alíquota do imposto"
                );
                $this->dom->addChild(
                    $icms,
                    'vICMS',
                    $this->conditionalNumberFormatting($std->vICMS),
                    true,
                    "$identificador  Valor do ICMS"
                );
                break;
            case '20':
                $icms = $this->dom->createElement("ICMS20");
                $this->dom->addChild(
                    $icms,
                    'CST',
                    $std->cst,
                    true,
                    "$identificador  Tributação do ICMS = 20"
                );
                $this->dom->addChild(
                    $icms,
                    'pRedBC',
                    $this->conditionalNumberFormatting($std->pRedBC),
                    true,
                    "$identificador  Percentual da Redução de BC"
                );
                $this->dom->addChild(
                    $icms,
                    'vBC',
                    $this->conditionalNumberFormatting($std->vBC),
                    true,
                    "$identificador  Valor da BC do ICMS"
                );
                $this->dom->addChild(
                    $icms,
                    'pICMS',
                    $this->conditionalNumberFormatting($std->pICMS),
                    true,
                    "$identificador  Alíquota do imposto"
                );
                $this->dom->addChild(
                    $icms,
                    'vICMS',
                    $this->conditionalNumberFormatting($std->vICMS),
                    true,
                    "$identificador  Valor do ICMS"
                );
                if ($std->vICMSDeson > 0 && $std->cBenef) {
                    $this->dom->addChild(
                        $icms,
                        'vICMSDeson',
                        $this->conditionalNumberFormatting($std->vICMSDeson),
                        true,
                        "$identificador  Valor do ICMS Desonerado"
                    );
                    $this->dom->addChild(
                        $icms,
                        'cBenef',
                        $std->cBenef,
                        true,
                        "$identificador  Código do enefício fiscal"
                    );
                }
                break;
            case '40':
                $icms = $this->dom->createElement("ICMS45");
                $this->dom->addChild(
                    $icms,
                    'CST',
                    $std->cst,
                    true,
                    "$identificador  Tributação do ICMS = 40"
                );
                if ($std->vICMSDeson > 0 && $std->cBenef) {
                    $this->dom->addChild(
                        $icms,
                        'vICMSDeson',
                        $this->conditionalNumberFormatting($std->vICMSDeson),
                        true,
                        "$identificador  Valor do ICMS Desonerado"
                    );
                    $this->dom->addChild(
                        $icms,
                        'cBenef',
                        $std->cBenef,
                        true,
                        "$identificador  Código do enefício fiscal"
                    );
                }
                break;
            case '41':
                $icms = $this->dom->createElement("ICMS45");
                $this->dom->addChild(
                    $icms,
                    'CST',
                    $std->cst,
                    true,
                    "$identificador  Tributação do ICMS = 41"
                );
                if ($std->vICMSDeson > 0 && $std->cBenef) {
                    $this->dom->addChild(
                        $icms,
                        'vICMSDeson',
                        $this->conditionalNumberFormatting($std->vICMSDeson),
                        true,
                        "$identificador  Valor do ICMS Desonerado"
                    );
                    $this->dom->addChild(
                        $icms,
                        'cBenef',
                        $std->cBenef,
                        true,
                        "$identificador  Código do enefício fiscal"
                    );
                }
                break;
            case '51':
                $icms = $this->dom->createElement("ICMS45");
                $this->dom->addChild(
                    $icms,
                    'CST',
                    $std->cst,
                    true,
                    "$identificador  Tributação do ICMS = 51"
                );
                if ($std->vICMSDeson > 0 && $std->cBenef) {
                    $this->dom->addChild(
                        $icms,
                        'vICMSDeson',
                        $this->conditionalNumberFormatting($std->vICMSDeson),
                        true,
                        "$identificador  Valor do ICMS Desonerado"
                    );
                    $this->dom->addChild(
                        $icms,
                        'cBenef',
                        $std->cBenef,
                        true,
                        "$identificador  Código do enefício fiscal"
                    );
                }
                break;
            case '60':
                $icms = $this->dom->createElement("ICMS60");
                $this->dom->addChild(
                    $icms,
                    'CST',
                    $std->cst,
                    true,
                    "$identificador  Tributação do ICMS = 60"
                );
                $this->dom->addChild(
                    $icms,
                    'vBCSTRet',
                    $this->conditionalNumberFormatting($std->vBCSTRet),
                    true,
                    "$identificador  Valor BC do ICMS ST retido"
                );
                $this->dom->addChild(
                    $icms,
                    'vICMSSTRet',
                    $this->conditionalNumberFormatting($std->vICMSSTRet),
                    true,
                    "$identificador  Valor do ICMS ST retido"
                );
                $this->dom->addChild(
                    $icms,
                    'pICMSSTRet',
                    $this->conditionalNumberFormatting($std->pICMSSTRet),
                    true,
                    "$identificador  Valor do ICMS ST retido"
                );
                if ($std->vCred > 0) {
                    $this->dom->addChild(
                        $icms,
                        'vCred',
                        $this->conditionalNumberFormatting($std->vCred),
                        false,
                        "$identificador  Valor do Crédito"
                    );
                }
                if ($std->vICMSDeson > 0 && $std->cBenef) {
                    $this->dom->addChild(
                        $icms,
                        'vICMSDeson',
                        $this->conditionalNumberFormatting($std->vICMSDeson),
                        true,
                        "$identificador  Valor do ICMS Desonerado"
                    );
                    $this->dom->addChild(
                        $icms,
                        'cBenef',
                        $std->cBenef,
                        true,
                        "$identificador  Código do enefício fiscal"
                    );
                }
                break;
            case '90':
                if ($std->outraUF == true) {
                    $icms = $this->dom->createElement("ICMSOutraUF");
                    $this->dom->addChild(
                        $icms,
                        'CST',
                        $std->cst,
                        true,
                        "$identificador  Tributação do ICMS = 90"
                    );
                    if ($std->vICMSOutraUF > 0) {
                        $this->dom->addChild(
                            $icms,
                            'pRedBCOutraUF',
                            $this->conditionalNumberFormatting($std->pRedBCOutraUF),
                            false,
                            "$identificador Percentual Red "
                                . "BC Outra UF"
                        );
                    }
                    $this->dom->addChild(
                        $icms,
                        'vBCOutraUF',
                        $this->conditionalNumberFormatting($std->vBCOutraUF),
                        true,
                        "$identificador Valor BC ICMS Outra UF"
                    );
                    $this->dom->addChild(
                        $icms,
                        'pICMSOutraUF',
                        $this->conditionalNumberFormatting($std->pICMSOutraUF),
                        true,
                        "$identificador Alíquota do imposto Outra UF"
                    );
                    $this->dom->addChild(
                        $icms,
                        'vICMSOutraUF',
                        $this->conditionalNumberFormatting($std->vICMSOutraUF),
                        true,
                        "$identificador Valor ICMS Outra UF"
                    );
                    if ($std->vICMSDeson > 0) {
                        $this->dom->addChild(
                            $icms,
                            'vICMSDeson',
                            $this->conditionalNumberFormatting($std->vICMSDeson),
                            true,
                            "$identificador  Valor do ICMS Desonerado"
                        );
                    }
                    if ($std->cBenef) {
                        $this->dom->addChild(
                            $icms,
                            'cBenef',
                            $std->cBenef,
                            true,
                            "$identificador  Código do enefício fiscal"
                        );
                    }
                } else {
                    $icms = $this->dom->createElement("ICMS90");
                    $this->dom->addChild(
                        $icms,
                        'CST',
                        $std->cst,
                        true,
                        "$identificador Tributação do ICMS = 90"
                    );
                    if ($std->pRedBC > 0) {
                        $this->dom->addChild(
                            $icms,
                            'pRedBC',
                            $this->conditionalNumberFormatting($std->pRedBC),
                            false,
                            "$identificador Percentual Redução BC"
                        );
                    }
                    $this->dom->addChild(
                        $icms,
                        'vBC',
                        $this->conditionalNumberFormatting($std->vBC),
                        true,
                        "$identificador  Valor da BC do ICMS"
                    );
                    $this->dom->addChild(
                        $icms,
                        'pICMS',
                        $this->conditionalNumberFormatting($std->pICMS),
                        true,
                        "$identificador  Alíquota do imposto"
                    );
                    $this->dom->addChild(
                        $icms,
                        'vICMS',
                        $this->conditionalNumberFormatting($std->vICMS),
                        true,
                        "$identificador  Valor do ICMS"
                    );
                    if ($std->vCred > 0) {
                        $this->dom->addChild(
                            $icms,
                            'vCred',
                            $this->conditionalNumberFormatting($std->vCred),
                            false,
                            "$identificador  Valor do Crédido"
                        );
                    }
                    if ($std->vICMSDeson > 0) {
                        $this->dom->addChild(
                            $icms,
                            'vICMSDeson',
                            $this->conditionalNumberFormatting($std->vICMSDeson),
                            true,
                            "$identificador  Valor do ICMS Desonerado"
                        );
                    }
                    if ($std->cBenef) {
                        $this->dom->addChild(
                            $icms,
                            'cBenef',
                            $std->cBenef,
                            true,
                            "$identificador  Código do enefício fiscal"
                        );
                    }
                }
                break;
            case 'SN':
                $icms = $this->dom->createElement("ICMSSN");
                $this->dom->addChild(
                    $icms,
                    'CST',
                    90,
                    true,
                    "$identificador Tributação do ICMS = 90"
                );
                $this->dom->addChild(
                    $icms,
                    'indSN',
                    '1',
                    true,
                    "$identificador  Indica se contribuinte é SN"
                );
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
                $this->conditionalNumberFormatting($std->vTotTrib),
                false,
                "$identificador Valor Total dos Tributos"
            );
        }
        if (isset($std->infAdFisco)) {
            $this->dom->addChild(
                $this->imp,
                'infAdFisco',
                Strings::replaceUnacceptableCharacters($std->infAdFisco),
                false,
                "$identificador Informações adicionais de interesse do Fisco"
            );
        }
        if (!empty($std->vICMSUFFim) || !empty($std->vICMSUFIni)) {
            $icmsDifal = $this->dom->createElement("ICMSUFFim");
            $this->dom->addChild(
                $icmsDifal,
                'vBCUFFim',
                $this->conditionalNumberFormatting($std->vBCUFFim),
                true,
                "$identificador Valor da BC do ICMS na UF
                de término da prestação do serviço de transporte"
            );
            $this->dom->addChild(
                $icmsDifal,
                'pFCPUFFim',
                $this->conditionalNumberFormatting($std->pFCPUFFim),
                true,
                "$identificador Percentual do ICMS
                relativo ao Fundo de Combate à pobreza (FCP) na UF de término da prestação do serviço de
                transporte"
            );
            $this->dom->addChild(
                $icmsDifal,
                'pICMSUFFim',
                $this->conditionalNumberFormatting($std->pICMSUFFim),
                true,
                "$identificador Alíquota interna da UF
                de término da prestação do serviço de transporte"
            );
            $this->dom->addChild(
                $icmsDifal,
                'pICMSInter',
                $this->conditionalNumberFormatting($std->pICMSInter),
                true,
                "$identificador Alíquota interestadual
                das UF envolvidas"
            );
            $this->dom->addChild(
                $icmsDifal,
                'vFCPUFFim',
                $this->conditionalNumberFormatting($std->vFCPUFFim),
                true,
                "$identificador Valor do ICMS relativo
                ao Fundo de Combate á Pobreza (FCP) da UF de término da prestação"
            );
            $this->dom->addChild(
                $icmsDifal,
                'vICMSUFFim',
                $this->conditionalNumberFormatting($std->vICMSUFFim),
                true,
                "$identificador Valor do ICMS de
                partilha para a UF de término da prestação do serviço de transporte"
            );
            $this->dom->addChild(
                $icmsDifal,
                'vICMSUFIni',
                $this->conditionalNumberFormatting($std->vICMSUFIni),
                true,
                "$identificador Valor do ICMS de
                partilha para a UF de início da prestação do serviço de transporte"
            );
            $this->imp->appendChild($icmsDifal);
        }
        return $tagIcms;
    }

    /**
     * Tag raiz do documento xml
     * Função chamada pelo método [ monta ]
     * @return \DOMElement
     */
    private function buildCTe()
    {
        if (empty($this->CTeSimp)) {
            $this->CTeSimp = $this->dom->createElement('CTeSimp');
            $this->CTeSimp->setAttribute('xmlns', 'http://www.portalfiscal.inf.br/cte');
        }
        return $this->CTeSimp;
    }

    /**
     * Gera as tags para o elemento: "infCarga" (Informações da Carga do CT-e)
     * #89
     * Nível: 1
     *
     * @return \DOMElement
     */
    public function taginfCarga($std)
    {
        $possible = [
            'vCarga',
            'proPred',
            'xOutCat',
            'vCargaAverb'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#89 <infCarga> - ';
        $this->infCarga = $this->dom->createElement('infCarga');
        $this->dom->addChild(
            $this->infCarga,
            'vCarga',
            $this->conditionalNumberFormatting($std->vCarga),
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
        $this->dom->addChild(
            $this->infCarga,
            'vCargaAverb',
            $this->conditionalNumberFormatting($std->vCargaAverb, 2),
            false,
            $identificador . 'Valor da Carga para efeito de averbação'
        );
        return $this->infCarga;
    }

    /**
     * Gera as tags para o elemento: "det" (Detalhamento das entregas/prestações do CTe Simplificado)
     * #98
     * Nível: 1
     *
     * @return \DOMElement
     */
    public function tagdet($std)
    {
        $possible = [
            'nItem',
            'cMunIni',
            'xMunIni',
            'cMunFim',
            'xMunFim',
            'vPrest',
            'vRec',
        ];

        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#98 <det> - ';
        $this->det[] = $this->dom->createElement('det');
        $posicao = (int)count($this->det) - 1;
        $this->det[$posicao]->setAttribute('nItem', $std->nItem);
        $this->dom->addChild(
            $this->det[$posicao],
            'cMunIni',
            $std->cMunIni,
            true,
            $identificador . 'Código do Município do início da prestação'
        );
        $this->dom->addChild(
            $this->det[$posicao],
            'xMunIni',
            $std->xMunIni,
            true,
            $identificador . 'Nome do Município do início da prestação'
        );
        $this->dom->addChild(
            $this->det[$posicao],
            'cMunFim',
            $std->cMunFim,
            true,
            $identificador . 'Código do Município de término da prestação'
        );
        $this->dom->addChild(
            $this->det[$posicao],
            'xMunFim',
            $std->xMunFim,
            true,
            $identificador . 'Nome do Município do término da prestação'
        );
        $this->dom->addChild(
            $this->det[$posicao],
            'vPrest',
            $this->conditionalNumberFormatting($std->vPrest),
            true,
            $identificador . 'Valor da Prestação do Serviço'
        );
        $this->dom->addChild(
            $this->det[$posicao],
            'vRec',
            $this->conditionalNumberFormatting($std->vRec),
            true,
            $identificador . 'Valor a Receber'
        );

        return $this->det[$posicao];
    }

    /**
     * Gera as tags para o elemento: "Comp" (Componentes do Valor da Prestação)
     * #108
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
        $identificador = '#108 <pass> - ';
        $this->comp[count($this->det) - 1][] = $this->dom->createElement('Comp');
        $posicao = (int)count($this->comp[count($this->det) - 1]) - 1;
        $this->dom->addChild(
            $this->comp[count($this->det) - 1][$posicao],
            'xNome',
            $std->xNome,
            true,
            $identificador . 'Nome do componente'
        );
        $this->dom->addChild(
            $this->comp[count($this->det) - 1][$posicao],
            'vComp',
            $this->conditionalNumberFormatting($std->vComp),
            true,
            $identificador . 'Valor do componente'
        );
        return $this->comp[count($this->det) - 1][$posicao];
    }

    /**
     * Gera as tags para o elemento: "infNFe" (Informações das NF-e)
     * #111
     * Nível: 2
     * @return mixed
     */
    public function taginfNFe($std)
    {
        $possible = [
            'chNFe',
            'PIN',
            'dPrev',
            'infUnidCarga',
            'infUnidTransp'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#111 <infNFe> - ';
        $this->infNFe[count($this->det) - 1][] = $this->dom->createElement('infNFe');
        $posicao = (int)count($this->infNFe[count($this->det) - 1]) - 1;
        $infNFe = $this->infNFe[count($this->det) - 1][$posicao];
        $this->dom->addChild(
            $infNFe,
            'chNFe',
            $std->chNFe,
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

        return $infNFe;
    }

    /**
     * Gera as tags para o elemento: "infDocAnt" (Documentos anteriores)
     * #133
     * Nível: 2
     * @return DOMElement|\DOMNode
     */
    public function taginfDocAnt($std)
    {
        $possible = [
            'chCTe',
            'tpPrest',
            'infNFeTranspParcial'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#133 <infDocAnt> - ';
        /// TODO remover fazer um teste para ver se ao usar está linha abaixo não apaga os dados antes inseridos.....
        $this->infDocAnt[count($this->det) - 1][] = $this->dom->createElement('infDocAnt');
        $posicao = (int)count($this->infDocAnt[count($this->det) - 1]) - 1;
        $infDocAnt = $this->infDocAnt[count($this->det) - 1][$posicao];
        $this->dom->addChild(
            $infDocAnt,
            'chCTe',
            $std->chCTe,
            true,
            $identificador . ' Chave de acesso do CT-e'
        );
        $this->dom->addChild(
            $infDocAnt,
            'tpPrest',
            $std->tpPrest,
            true,
            $identificador . ' indica se a prestação é total ou parcial em relação as notas do documento anterior '
        );
        if ($std->infNFeTranspParcial) {
            foreach ($std->infNFeTranspParcial as $value) {
                $this->dom->appChild($infDocAnt, $this->taginfNFeTranspParcial($value), 'Falta tag "infNFeTranspParcial"');
            }
        }
        return $this->infDocAnt;
    }

    /**
     * taginfNFeTranspParcial
     * tag CTe/infCte/det/infDocAnt/infNFeTranspParcial
     *
     * @param stdClass $std
     * @return DOMElement
     */
    private function taginfNFeTranspParcial(stdClass $std)
    {
        $possible = [
            'chNFe'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $infNFeTranspParcial = $this->dom->createElement("infNFeTranspParcial");
        $this->dom->addChild(
            $infNFeTranspParcial,
            "chNFe",
            $std->chNFe,
            false,
            "Chave de acesso da NF-e"
        );

        return $infNFeTranspParcial;
    }

    /**
     * Gera as tags para o elemento: "infQ" (Informações de quantidades da Carga do CT-e)
     * #93
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "infQ"
     *
     * @return mixed
     */
    public function taginfQ($std)
    {
        $possible = [
            'cUnid',
            'tpMed',
            'qCarga'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#93 <infQ> - ';
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
            $this->conditionalNumberFormatting($std->qCarga, 4),
            true,
            $identificador . 'Quantidade'
        );
        return $this->infQ[$posicao];
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
            $this->conditionalNumberFormatting($std->vBC),
            true,
            $identificador . 'Valor da Base de Cálculo do ICMS'
        );
        $this->dom->addChild(
            $infNF,
            'vICMS',
            $this->conditionalNumberFormatting($std->vICMS),
            true,
            $identificador . 'Valor Totaldo ICMS'
        );
        $this->dom->addChild(
            $infNF,
            'vBCST',
            $this->conditionalNumberFormatting($std->vBCST),
            true,
            $identificador . 'Valor da Base de Cálculo do ICMS ST'
        );
        $this->dom->addChild(
            $infNF,
            'vST',
            $this->conditionalNumberFormatting($std->vST),
            true,
            $identificador . 'Valor Total do ICMS ST'
        );
        $this->dom->addChild(
            $infNF,
            'vProd',
            $this->conditionalNumberFormatting($std->vProd),
            true,
            $identificador . 'Valor Total dos Produtos'
        );
        $this->dom->addChild(
            $infNF,
            'vNF',
            $this->conditionalNumberFormatting($std->vNF),
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
            $this->conditionalNumberFormatting($std->nPeso, 3),
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
     * taginfUnidCarga
     * tag CTe/infCte/infDoc/(infNF/infNFe/infOutros)/infUnidCarga
     *
     * @param stdClass $std
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
            foreach ($std->lacUnidCarga as $nLacre) {
                $possible = [
                    'nLacre'
                ];
                $stdlacUnidCarga = $this->equilizeParameters($nLacre, $possible);
                $lacUnidCarga = $this->dom->createElement("lacUnidCarga");
                $this->dom->addChild(
                    $lacUnidCarga,
                    "nLacre",
                    $stdlacUnidCarga->nLacre,
                    true,
                    "Número do lacre"
                );
                $this->dom->appChild($infUnidCarga, $lacUnidCarga, 'Falta tag "infUnidCarga"');
            }
        }
        $this->dom->addChild(
            $infUnidCarga,
            "qtdRat",
            $this->conditionalNumberFormatting($std->qtdRat),
            false,
            "Quantidade rateada (Peso,Volume)"
        );
        return $infUnidCarga;
    }

    /**
     * taginfUnidTransp
     * tag CTe/infCte/infDoc/(infNF/infNFe/infOutros)/infUnidTransp
     *
     * @param stdClass $std
     * @return DOMElement
     */
    private function taginfUnidTransp(stdClass $std)
    {
        $possible = [
            'tpUnidTransp',
            'idUnidTransp',
            'lacUnidTransp',
            'infUnidCarga',
            'qtdRat',
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
        if (!empty($std->lacUnidTransp)) {
            foreach ($std->lacUnidTransp as $nLacre) {
                $possible = [
                    'nLacre'
                ];
                $stdlacUnidTransp = $this->equilizeParameters($nLacre, $possible);
                $lacUnidTransp = $this->dom->createElement("lacUnidTransp");
                $this->dom->addChild(
                    $lacUnidTransp,
                    "nLacre",
                    $stdlacUnidTransp->nLacre,
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
            $this->conditionalNumberFormatting($std->qtdRat),
            false,
            "Quantidade rateada (Peso,Volume) "
        );
        return $infUnidTransp;
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
     * @return DOMElement|\DOMNode
     * @author Uilasmar Guedes
     * #1
     * Nivel: 0
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
            $this->conditionalNumberFormatting($std->vTar),
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
     * @return DOMElement|\DOMNode
     * @author Anderson Minuto Consoni Vaz
     * #1
     * Nivel: 0
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
            $this->conditionalNumberFormatting($std->vPrest),
            true,
            $identificador . 'vPrest'
        );
        $this->dom->addChild(
            $this->aquav,
            'vAFRMM',
            $this->conditionalNumberFormatting($std->vAFRMM),
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
     * Leiaute - Aquaviario
     * Gera as tags de balsa para o elemento: "aquav" (informações do modal Aquaviario)
     * @return DOMElement|\DOMNode
     * @author Gabriel Kliemaschewsk Rondon
     * #5
     * Nivel: 1
     */
    public function tagbalsa($std)
    {
        $possible = [
            'xBalsa',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#5 <balsa> - ';
        $this->balsa[] = $this->dom->createElement('balsa');
        $this->dom->addChild(
            $this->balsa[count($this->balsa) - 1],
            'xBalsa',
            $std->xBalsa,
            true,
            $identificador . 'xBalsa'
        );
        return $this->balsa;
    }

    /**
     * Leiaute - Aquaviario
     * Gera as tags de Conteiner específicas do modal aquaviário
     * @return DOMElement|\DOMNode
     * @author Gabriel Kliemaschewsk Rondon
     * #10
     * Nivel: 1
     */
    // Aguaviario
    public function tagdetCont($std)
    {
        $possible = [
            'nCont',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#10 <detCont> - ';
        $this->detCont[] = $this->dom->createElement('detCont');
        $this->dom->addChild(
            $this->detCont[count($this->detCont) - 1],
            'nCont',
            $std->nCont,
            true,
            $identificador . 'detCont'
        );
        return $this->detCont;
    }

    /**
     * Leiaute - Aquaviario
     * Gera as tags de lacre para os containeres do elemento: "aquav" (informações do modal Aquaviario)
     * @return DOMElement|\DOMNode
     * @author Gabriel Kliemaschewsk Rondon
     * #12
     * Nivel: 2
     */
    public function taglacre($std)
    {
        $possible = [
            'nLacre',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#12 <detCont> - ';
        $this->lacre[count($this->detCont) - 1][] = $this->dom->createElement('lacre');
        $posicao = (int)count($this->lacre[count($this->detCont) - 1]) - 1;
        $this->dom->addChild(
            $this->lacre[count($this->detCont) - 1][$posicao],
            'nLacre',
            $std->nLacre,
            true,
            $identificador . 'Lacre'
        );
        return $this->lacre[count($this->detCont) - 1][$posicao];
    }

    // Aguaviario
    public function taginfDocCont()
    {
        $this->infDocCont[count($this->detCont) - 1] = $this->dom->createElement('infDoc');
        return $this->infDocCont;
    }

    // Aguaviario
    public function taginfNFCont($std)
    {
        $possible = [
            'serie',
            'nDoc',
            'unidRat',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#15 <detCont> <infNF> - ';
        $this->infNFCont[count($this->detCont) - 1][] = $this->dom->createElement('infNF');
        $posicao = (int)count($this->infNFCont[count($this->detCont) - 1]) - 1;
        $this->dom->addChild(
            $this->infNFCont[count($this->detCont) - 1][$posicao],
            'serie',
            $std->serie,
            true,
            $identificador . 'serie'
        );
        $this->dom->addChild(
            $this->infNFCont[count($this->detCont) - 1][$posicao],
            'nDoc',
            $std->nDoc,
            true,
            $identificador . 'nDoc'
        );
        $this->dom->addChild(
            $this->infNFCont[count($this->detCont) - 1][$posicao],
            'unidRat',
            $std->unidRat,
            false,
            $identificador . 'unidRat'
        );
        return $this->infNFCont[count($this->detCont) - 1][$posicao];
    }

    // Aguaviario
    public function taginfNFeCont($std)
    {
        $possible = [
            'chave',
            'unidRat',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#19 <infNFe> - ';
        $this->infNFeCont[count($this->detCont) - 1][] = $this->dom->createElement('infNFe');
        $posicao = (int)count($this->infNFeCont[count($this->detCont) - 1]) - 1;
        $this->dom->addChild(
            $this->infNFeCont[count($this->detCont) - 1][$posicao],
            'chave',
            $std->chave,
            true,
            $identificador . 'chave'
        );
        $this->dom->addChild(
            $this->infNFeCont[count($this->detCont) - 1][$posicao],
            'unidRat',
            $std->unidRat,
            false,
            $identificador . 'unidRat'
        );
    }

    /**
     * Leiaute - Aéreo
     * Gera as tags para o elemento: "aereo" (Informações do modal Aéreo)
     * @return DOMElement|\DOMNode
     * @author Newton Pasqualini Filho
     * #1
     * Nível: 0
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
            $natCarga = $this->dom->createElement('natCarga');
            $this->dom->addChild(
                $natCarga,
                'xDime',
                $std->natCarga_xDime,
                false,
                $identificador . 'Dimensões da carga, formato: 1234x1234x1234 (cm)'
            );
            if (isset($std->natCarga_cInfManu) && !is_array($std->natCarga_cInfManu)) {
                $std->natCarga_cInfManu = [$std->natCarga_cInfManu];
            }
            foreach ($std->natCarga_cInfManu as $cInfManu) {
                $this->dom->addChild(
                    $natCarga,
                    'cInfManu',
                    $cInfManu,
                    false,
                    $identificador . 'Informação de manuseio, com dois dígitos, pode ter mais de uma ocorrência.'
                );
            }
            $this->aereo->appendChild($natCarga);
        }
        $identificador = '#1 <aereo> - <tarifa> - ';
        $tarifa = $this->dom->createElement('tarifa');
        $this->dom->addChild(
            $tarifa,
            'CL',
            $std->tarifa_CL,
            true,
            $identificador . 'Classe da tarifa: M - Tarifa Mínima / G - Tarifa Geral / E - Tarifa Específica'
        );
        $this->dom->addChild(
            $tarifa,
            'cTar',
            $std->tarifa_cTar,
            false,
            $identificador . 'Código de três digítos correspondentes à tarifa.'
        );
        $this->dom->addChild(
            $tarifa,
            'vTar',
            $this->conditionalNumberFormatting($std->tarifa_vTar),
            true,
            $identificador . 'Valor da tarifa. 15 posições, sendo 13 inteiras e 2 decimais.'
        );
        $this->aereo->appendChild($tarifa);
        return $this->aereo;
    }

    /**
     * Leiaute - Aéreo
     * Gera as tags para o elemento: "aereo" (Informações do modal Aéreo)
     * #1
     * Nível: 0
     * @return DOMElement|\DOMNode
     */
    // Aerio
    public function tagperi($std)
    {
        $possible = [
            'nONU',
            'qTotEmb',
            'qTotProd',
            'uniAP'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#1 <aereo> - <peri> - ';
        $peri = $this->dom->createElement('peri');
        $this->dom->addChild(
            $peri,
            'nONU',
            $std->nONU,
            true,
            $identificador . 'Número ONU/UN'
        );
        $this->dom->addChild(
            $peri,
            'qTotEmb',
            $this->conditionalNumberFormatting($std->qTotEmb, 0),
            true,
            $identificador . 'Quantidade total de volumes contendo artigos perigosos'
        );
        $identificador = '#1 <peri> - <infTotAP> - ';
        $infTotAP = $this->dom->createElement('infTotAP');
        $this->dom->addChild(
            $infTotAP,
            'qTotProd',
            $this->conditionalNumberFormatting($std->qTotProd, 4),
            false,
            $identificador . 'Quantidade total de artigos perigosos'
        );
        $this->dom->addChild(
            $infTotAP,
            'uniAP',
            $std->uniAP,
            true,
            $identificador . 'Unidade de medida'
        );
        $peri->appendChild($infTotAP);
        $this->peri[] = $peri;
        return $peri;
    }

    // Ferroviario
    public function tagferrov($std)
    {
        $possible = [
            'tpTraf',
            'respFat',
            'ferrEmi',
            'vFrete',
            'chCTeFerroOrigem ',
            'fluxo'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#1 <ferrov> - ';
        $this->ferrov = $this->dom->createElement('ferrov');
        $this->dom->addChild(
            $this->ferrov,
            'tpTraf',
            $std->tpTraf,
            true,
            $identificador . 'Tipo de Tráfego'
        );
        $identificador = '#1 <ferrov> - <trafMut> - ';
        $trafMut = $this->dom->createElement('trafMut');
        $this->dom->addChild(
            $trafMut,
            'respFat',
            $std->respFat,
            true,
            $identificador . 'Responsável pelo Faturamento'
        );
        $this->dom->addChild(
            $trafMut,
            'ferrEmi',
            $std->ferrEmi,
            true,
            $identificador . 'Ferrovia Emitente do CTe'
        );
        $this->dom->addChild(
            $trafMut,
            'vFrete',
            $this->conditionalNumberFormatting($std->vFrete),
            true,
            $identificador . 'Valor do Frete do Tráfego Mútuo '
        );
        $this->dom->addChild(
            $trafMut,
            'chCTeFerroOrigem',
            $std->chCTeFerroOrigem,
            false,
            $identificador . 'Chave de acesso do CT-e emitido pelo ferrovia de origem'
        );
        $this->ferrov->appendChild($trafMut);
        $this->dom->addChild(
            $this->ferrov,
            'fluxo',
            $std->fluxo,
            true,
            $identificador . 'Fluxo Ferroviário '
        );
    }

    // Ferroviario
    public function tagferroEnv($std)
    {
        $possible = [
            'CNPJ',
            'cInt',
            'IE',
            'xNome',
            'xLgr',
            'nro',
            'xCpl',
            'xBairro',
            'cMun',
            'xMun',
            'CEP',
            'UF',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#1 <ferroEnv> - ';
        $ferroEnv = $this->dom->createElement('ferroEnv');
        $this->dom->addChild(
            $ferroEnv,
            'CNPJ',
            $std->CNPJ,
            true,
            $identificador . 'Número do CNPJ'
        );
        $this->dom->addChild(
            $ferroEnv,
            'cInt',
            $std->cInt,
            false,
            $identificador . 'Código interno da Ferrovia envolvida'
        );
        $this->dom->addChild(
            $ferroEnv,
            'IE',
            $std->IE,
            false,
            $identificador . 'Inscrição Estadual'
        );
        $this->dom->addChild(
            $ferroEnv,
            'xNome',
            $std->xNome,
            false,
            $identificador . 'Razão Social ou Nome'
        );
        $identificador = '#1 <ferroEnv> - <enderFerro> - ';
        $enderFerro = $this->dom->createElement('enderFerro');
        $this->dom->addChild(
            $enderFerro,
            'xLgr',
            $std->xLgr,
            true,
            $identificador . 'Logradouro'
        );
        $this->dom->addChild(
            $enderFerro,
            'nro',
            $std->nro,
            false,
            $identificador . 'Número'
        );
        $this->dom->addChild(
            $enderFerro,
            'xCpl',
            $std->xCpl,
            false,
            $identificador . 'Complemento'
        );
        $this->dom->addChild(
            $enderFerro,
            'xBairro',
            $std->xBairro,
            false,
            $identificador . 'Bairro'
        );
        $this->dom->addChild(
            $enderFerro,
            'cMun',
            $std->cMun,
            true,
            $identificador . 'Código do município'
        );
        $this->dom->addChild(
            $enderFerro,
            'xMun',
            $std->xMun,
            true,
            $identificador . 'Nome do município'
        );
        $this->dom->addChild(
            $enderFerro,
            'CEP',
            $std->CEP,
            true,
            $identificador . 'CEP'
        );
        $this->dom->addChild(
            $enderFerro,
            'UF',
            $std->UF,
            true,
            $identificador . 'Sigla da UF'
        );
        $ferroEnv->appendChild($enderFerro);
        $this->ferroEnv[] = $ferroEnv;
        return $ferroEnv;
    }

    /**
     * Leiaute - Multimodal
     * Gera as tags do leaiute específico de multimodal
     * @return DOMElement|\DOMNode
     * @author Gabriel Kliemaschewsk Rondon
     * Nivel: 1
     */
    public function tagmultimodal($std)
    {
        $possible = [
            'COTM',
            'indNegociavel',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#1 <multimodal> - ';
        $this->multimodal = $this->dom->createElement('multimodal');
        $this->dom->addChild(
            $this->multimodal,
            'COTM',
            $std->COTM,
            true,
            $identificador . 'COTM'
        );
        $this->dom->addChild(
            $this->multimodal,
            'indNegociavel',
            $std->indNegociavel,
            true,
            $identificador . 'indNegociavel'
        );
        return $this->multimodal;
    }

    public function tagSegMultimodal($std)
    {
        $possible = [
            'xSeg',
            'CNPJ',
            'nApol',
            'nAver'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#4 <multimodal> - ';
        $this->segMultim = $this->dom->createElement('seg');
        $infSeg = $this->dom->createElement('infSeg');
        $this->dom->addChild(
            $infSeg,
            'xSeg',
            $std->xSeg,
            true,
            $identificador . 'xSeg'
        );
        $this->dom->addChild(
            $infSeg,
            'CNPJ',
            $std->CNPJ,
            false,
            $identificador . 'indNegociavel'
        );
        $this->segMultim->appendChild($infSeg);
        $this->dom->addChild(
            $this->segMultim,
            'nApol',
            $std->nApol,
            true,
            $identificador . 'nApol'
        );
        $this->dom->addChild(
            $this->segMultim,
            'nAver',
            $std->nAver,
            false,
            $identificador . 'nAver'
        );
        return $this->segMultim;
    }

    /**
     * CT-e de substituição
     * @return DOMElement|\DOMNode
     */
    public function taginfCteSub($std)
    {
        $possible = [
            'chCte',
            'indAlteraToma'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#151 <infCteSub> - ';
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
            'indAlteraToma',
            $std->indAlteraToma,
            false,
            'Indicador de CT-e Alteração de Tomador'
        );
        return $this->infCteSub;
    }

    /**
     * Gera as tags para o elemento: "autXML" (Autorizados para download do XML)
     * #219
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
        $identificador = '#219 <autXML> - ';
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
     * #141
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
     * #142
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
            $this->conditionalNumberFormatting($std->vOrig),
            false,
            "Valor Original da Fatura"
        );
        if ($std->vDesc > 0) {
            $this->dom->addChild(
                $fat,
                "vDesc",
                $this->conditionalNumberFormatting($std->vDesc),
                false,
                "Valor do desconto"
            );
        }
        $this->dom->addChild(
            $fat,
            "vLiq",
            $this->conditionalNumberFormatting($std->vLiq),
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
            $this->conditionalNumberFormatting($std->vDup),
            false,
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
     * Retorna os erros detectados
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Includes missing or unsupported properties in stdClass
     * Replace all unsuported chars
     *
     * @param stdClass $std
     * @param array $possible
     * @return stdClass
     */
    private function equilizeParameters(stdClass $std, $possible)
    {
        return Strings::equilizeParameters($std, $possible, $this->replaceAccentedChars);
    }

    /**
     * Formatação numerica condicional
     * @param string|float|int|null $value
     * @param int $decimal
     * @return string
     */
    protected function conditionalNumberFormatting($value = null, $decimal = 2)
    {
        if (is_numeric($value)) {
            return number_format($value, $decimal, '.', '');
        }
        return null;
    }
}
