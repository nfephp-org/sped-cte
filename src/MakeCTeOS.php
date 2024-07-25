<?php

namespace NFePHP\CTe;

/**
 *
 * @category  Library
 * @package   nfephp-org/sped-cte
 * @copyright 2009-2023 NFePHP
 * @name      Make.php
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL v.3
 * @link      http://github.com/nfephp-org/sped-cte for the canonical source repository
 * @author    Cleiton Perin <cperin20 at gmail dot com>
 */

use DOMElement;
use NFePHP\Common\DOMImproved as Dom;
use NFePHP\Common\Keys;
use NFePHP\Common\Strings;
use RuntimeException;
use stdClass;

class MakeCTeOS
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
     * Valores da Prestação de Serviço
     * @var \DOMNode
     */
    private $vPrest = '';
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
     * Grupo de informações do CT-e Normal e Substituto
     * @var \DOMNode
     */
    private $infCTeNorm = '';
    /**
     * Informações da Prestação do Serviço
     * @var \DOMNode
     */
    private $infServico = '';
    /**
     * Informações dos demais documentos
     * @var array
     */
    private $infDocRef = [];
    /**
     * Informações de Seguro da Carga
     * @var array
     */
    private $seg = [];
    /**
     * Informações do modal
     * @var \DOMNode
     */
    private $infModal = '';
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
     * Informação da NF ou CT emitido pelo Tomador
     * @var \DOMNode
     */
    private $infCteComp = '';
    /**
     * Informações do modal Rodoviário
     * @var \DOMNode
     */
    private $rodo = '';
    /**
     * Dados dos Veículos
     * @var array
     */
    private $veic = [];
    /**
     * Proprietários do Veículo. Só preenchido quando o veículo não pertencer à empresa emitente do CT-e
     * @var array
     */
    private $prop = [];
    /**
     * Informações das GTVe relacionadas ao CTe OS
     * @var array
     */
    private $infGTVe = [];
    /**
     * Autorizados para download do XML do DF-e
     * @var array
     */
    private $autXML = [];
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
        if (!empty($this->infPercurso)) {
            foreach ($this->infPercurso as $perc) {
                $this->dom->appChild($this->ide, $perc, 'Falta tag "infPercurso"');
            }
        }
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
        if (!empty($this->toma)) {
            $this->dom->appChild($this->infCte, $this->toma, 'Falta tag "infCte"');
        }
        foreach ($this->comp as $comp) {
            $this->dom->appChild($this->vPrest, $comp, 'Falta tag "vPrest"');
        }
        $this->dom->appChild($this->infCte, $this->vPrest, 'Falta tag "infCte"');
        $this->dom->appChild($this->infCte, $this->imp, 'Falta tag "imp"');
        if (!empty($this->infCteComp)) { // Caso seja um CTe tipo complemento de valores
            $this->dom->appChild($this->infCte, $this->infCteComp, 'Falta tag "infCteComp"');
        } elseif (!empty($this->infCTeNorm)) { // Caso seja um CTe tipo normal
            $this->dom->appChild($this->infCte, $this->infCTeNorm, 'Falta tag "infCTeNorm"');
            $this->dom->appChild($this->infCTeNorm, $this->infServico, 'Falta tag "infServico"');
            foreach ($this->infDocRef as $infDocRef) {
                $this->dom->appChild($this->infCTeNorm, $infDocRef, 'Falta tag "infDocRef"');
            }
            foreach ($this->seg as $seg) {
                $this->dom->appChild($this->infCTeNorm, $seg, 'Falta tag "seg"');
            }
            if (!empty($this->infModal)) {
                $this->dom->appChild($this->infCTeNorm, $this->infModal, 'Falta tag "infModal"');
                if (!empty($this->veic)) {
                    $this->dom->appChild($this->rodo, $this->veic, 'Falta tag "veic"');
                }
                $this->dom->appChild($this->rodo, $this->infFretamento, 'Falta tag "infFretamento"');
                $this->dom->appChild($this->infModal, $this->rodo, 'Falta tag "rodo"');
            }
            if (!empty($this->infCteSub)) { // Caso seja um CT-e OS tipo substituição
                $this->dom->appChild($this->infCTeNorm, $this->infCteSub, 'Falta tag "infCteSub"');
            }
        }
        if (!empty($this->cobr)) {
            $this->dom->appChild($this->infCTeNorm, $this->cobr, 'Falta tag "cobr"');
        }
        foreach ($this->infGTVe as $infGTVe) {
            $this->dom->appChild($this->infCTeNorm, $infGTVe, 'Falta tag "infGTVe"');
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
        if (count($this->errors) > 0) {
            throw new RuntimeException('Existem erros nas tags. Obtenha os erros com getErrors().');
        }
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
            'indIEToma',
            'cMunIni',
            'xMunIni',
            'UFIni',
            'cMunFim',
            'xMunFim',
            'UFFim',
            'dhCont',
            'xJust'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $this->tpAmb = $std->tpAmb;
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
            '67',
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
        $this->dom->addChild(
            $this->ide,
            'tpServ',
            $std->tpServ,
            true,
            $identificador . 'Tipo do Serviço'
        );
        $this->dom->addChild(
            $this->ide,
            'indIEToma',
            $std->indIEToma,
            true,
            $identificador . 'Indicador do papel do tomador na prestação do serviço'
        );
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
            $std->UFPer,
            true,
            $identificador . 'Código da UF do percurso'
        );
        return $this->infPercurso[$posicao];
    }

    /**
     * Gera as tags para o elemento: "compl" (Dados complementares do CT-e OS para fins operacionais ou comerciais)
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
            'IE',
            'IEST',
            'xNome',
            'xFant',
            'CRT'
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
            $this->conditionalNumberFormatting($std->vTPrest),
            true,
            $identificador . 'Valor Total da Prestação do Serviço'
        );
        $this->dom->addChild(
            $this->vPrest,
            'vRec',
            $this->conditionalNumberFormatting($std->vRec),
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
        $identificador = '#65 <Comp> - ';
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
            $this->conditionalNumberFormatting($std->vComp),
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
            'cst',
            'vBC',
            'pICMS',
            'vICMS',
            'pRedBC',
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
        if ($std->vICMSUFFim != '' || $std->vICMSUFIni != '') {
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
     * tagInfTribFed
     * Informações do Impostos Federais
     * CTe OS
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
        $this->dom->addChild(
            $tagInfTribFed,
            'vPIS',
            $this->conditionalNumberFormatting($std->vPIS),
            false,
            "$identificador  Valor de PIS"
        );
        $this->dom->addChild(
            $tagInfTribFed,
            'vCOFINS',
            $this->conditionalNumberFormatting($std->vCOFINS),
            false,
            "$identificador  Valor de COFINS"
        );
        $this->dom->addChild(
            $tagInfTribFed,
            'vIR',
            $this->conditionalNumberFormatting($std->vIR),
            false,
            "$identificador  Valor de IR"
        );
        $this->dom->addChild(
            $tagInfTribFed,
            'vINSS',
            $this->conditionalNumberFormatting($std->vINSS),
            false,
            "$identificador  Valor de INSS"
        );
        $this->dom->addChild(
            $tagInfTribFed,
            'vCSLL',
            $this->conditionalNumberFormatting($std->vCSLL),
            false,
            "$identificador  Valor de CSLL"
        );
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
            $this->CTe = $this->dom->createElement('CTeOS');
            $this->CTe->setAttribute('versao', '4.00');
            $this->CTe->setAttribute('xmlns', 'http://www.portalfiscal.inf.br/cte');
        }
        return $this->CTe;
    }

    /**
     * #241
     * @return \DOMElement
     */
    public function taginfCTeNorm()
    {
        $this->infCTeNorm = $this->dom->createElement('infCTeNorm');
        return $this->infCTeNorm;
    }

    /**
     * Gera as tags para o elemento: "taginfServico" (Informações da Carga do CT-e OS)
     * #253
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "infServico"
     *
     * @return \DOMElement
     */
    public function taginfServico($std)
    {
        $possible = [
            'xDescServ',
            'qCarga'
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
        if (isset($std->qCarga)) {
            $infQ = $this->dom->createElement('infQ');
            $this->dom->addChild(
                $infQ,
                'qCarga',
                $std->qCarga,
                false,
                $identificador . 'Quantidade'
            );
            $this->infServico->appendChild($infQ);
        }
        return $this->infServico;
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
            'vDoc',
            'chBPe'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $ident = '#319 <infDocRef> - ';
        $this->infDocRef[] = $this->dom->createElement('infDocRef');
        $posicao = (int)count($this->infDocRef) - 1;
        if (!empty($std->chBPe)) {
            $this->dom->addChild(
                $this->infDocRef[$posicao],
                'chBPe',
                $std->chBPe,
                true,
                $ident . 'Chave de acesso do BP-e que possui eventos excesso de bagagem'
            );
        } else {
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
                $this->conditionalNumberFormatting($std->vDoc),
                false,
                $ident . 'Valor do documento'
            );
        }
        return $this->infDocRef[$posicao];
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
     * Gera as tags para o elemento: "rodo" (Informações do modal Rodoviário) CT-e OS
     * #1
     * Nível: 0
     * @return DOMElement|\DOMNode
     */
    public function tagrodo($std)
    {
        $possible = [
            'TAF',
            'NroRegEstadual'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#1 <rodoOS> - ';
        $this->rodo = $this->dom->createElement('rodoOS');
        if (!empty($std->TAF)) {
            $this->dom->addChild(
                $this->rodo,
                'TAF',
                $std->TAF,
                true,
                $identificador . 'Termo de Autorização de Fretamento - TAF'
            );
        } else {
            $this->dom->addChild(
                $this->rodo,
                'NroRegEstadual',
                $std->NroRegEstadual,
                true,
                $identificador . 'Número do Registro Estadual'
            );
        }
        return $this->rodo;
    }

    /**
     * CT-e de substituição
     * @return DOMElement|\DOMNode
     */
    public function taginfCteSub($std)
    {
        $possible = [
            'chCte',
            'refCTeCanc'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#149 <infCteSub> - ';
        $this->infCteSub = $this->dom->createElement('infCteSub');
        $this->dom->addChild(
            $this->infCteSub,
            'chCte',
            $std->chCte,
            false,
            "$identificador Chave de acesso do CTe a ser substituído (original)"
        );
        $this->dom->addChild(
            $this->infCteSub,
            'refCTeCanc',
            $std->refCTeCanc,
            false,
            "$identificador Chave de acesso do CTe Cancelado Somente para Transporte de Valores"
        );
        return $this->infCteSub;
    }

    /**
     * Leiaute - Rodoviário
     * Gera as tags para o elemento: "veic" (Dados dos Veículos)
     * #21
     * Nível: 1
     * @return mixed
     */
    public function tagveic($std)
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
            if (!empty($std->TAF)) {
                $this->dom->addChild(
                    $this->prop,
                    'TAF',
                    $std->TAF,
                    true,
                    $identificador . 'TAF'
                );
            } else {
                $this->dom->addChild(
                    $this->prop,
                    'NroRegEstadual',
                    $std->NroRegEstadual,
                    true,
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
            false,
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
     * Gera as tags para o elemento: "Comp" (Componentes do Valor da GTVe)
     * #170
     * Nível: 2
     * Os parâmetros para esta função são todos os elementos da tag "Comp" do
     * tipo elemento (Ele = E|CE|A) e nível 3
     *
     * @return \DOMElement
     */
    public function taginfGTVe($std)
    {
        $possible = [
            'chCTe'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#65 <comp> - ';
        $this->infGTVe[] = $this->dom->createElement('infGTVe');
        $posicao = (int)count($this->infGTVe) - 1;
        $this->dom->addChild(
            $this->infGTVe[$posicao],
            'chCTe',
            $std->chCTe,
            true,
            $identificador . 'Tipo do Componente'
        );
        return $this->infGTVe[$posicao];
    }

    /**
     * Gera as tags para o elemento: "Comp" (Componentes do Valor da GTVe)
     * #172
     * Nível: 3
     * Os parâmetros para esta função são todos os elementos da tag "Comp" do
     * tipo elemento (Ele = E|CE|A) e nível 3
     *
     * @return \DOMElement
     */
    public function tagCompGTVe($std)
    {
        $possible = [
            'tpComp',
            'vComp',
            'xComp'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#172 <Comp> - ';
        $comp = $this->dom->createElement('Comp');
        $this->dom->addChild(
            $comp,
            'tpComp',
            $std->tpComp,
            true,
            $identificador . 'Tipo do Componente'
        );
        $this->dom->addChild(
            $comp,
            'vComp',
            $this->conditionalNumberFormatting($std->vComp),
            true,
            $identificador . 'Valor do componente'
        );
        $this->dom->addChild(
            $comp,
            'xComp',
            $std->xComp,
            false,
            $identificador . 'Nome do componente (informar apenas para outros)'
        );
        $posicao = (int)count($this->infGTVe) - 1;
        $this->dom->appChild($this->infGTVe[$posicao], $comp, 'Inclui Comp na tag infGTVe');
        return $comp;
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
