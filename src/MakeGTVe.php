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

class MakeGTVe
{
    /**
     * @var array
     */
    public $errors = [];

    /**
     * versao numero da versão do xml da GTVe
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
     * Indicador do "papel" do tomador do serviço no CT-e
     * @var \DOMNode
     */
    private $toma = '';
    /**
     * Indicador do "papel" do tomador do serviço no CT-e
     * @var \DOMNode
     */
    private $tomaTerceiro = '';
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
     * Autorizados para download do XML do DF-e
     * @var array
     */
    private $autXML = array();
    /**
     * @var DOMElement
     */
    protected $infRespTec;
    /**
     * Informações do endereço da origem do serviço
     * @var array
     */
    private $origem = '';
    /**
     * Informações do endereço da destino do serviço
     * @var array
     */
    private $destino = '';
    /**
     * Grupo de informações detalhadas da GTVe
     * @var string
     */
    protected $detGTV = '';
    /**
     * Informações das Espécies transportadas
     * @var array
     */
    protected $infEspecie = [];
    /**
     * Grupo de informações dos veículos utilizados no transporte de valores
     * @var array
     */
    protected $infVeiculo = [];
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
     * Retorns the key number of GTVe (44 digits)
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
        if (!empty($this->toma)) {
            if (!empty($node)) {
                $this->ide->insertBefore($this->toma, $node);
            } else {
                $this->dom->appChild($this->ide, $this->toma, 'Falta tag "ide"');
            }
        } else {
            if (!empty($node)) {
                $this->ide->insertBefore($this->tomaTerceiro, $node);
            } else {
                $this->dom->appChild($this->ide, $this->tomaTerceiro, 'Falta tag "ide"');
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
        $this->dom->appChild($this->emit, $this->enderEmit, 'Falta tag "emit"');
        $this->dom->appChild($this->infCte, $this->emit, 'Falta tag "infCte"');
        $this->dom->appChild($this->infCte, $this->rem, 'Falta tag "infCte"');
        $this->dom->appChild($this->infCte, $this->dest, 'Falta tag "infCte"');
        if (!empty($this->origem)) {
            $this->dom->appChild($this->infCte, $this->origem, 'Falta tag "infCte"');
        }
        if (!empty($this->destino)) {
            $this->dom->appChild($this->infCte, $this->destino, 'Falta tag "infCte"');
        }
        foreach ($this->infEspecie as $infEspecie) {
            $this->dom->appChild($this->detGTV, $infEspecie, 'Falta tag "detGTV"');
        }
        foreach ($this->infVeiculo as $infVeiculo) {
            $this->dom->appChild($this->detGTV, $infVeiculo, 'Falta tag "detGTV"');
        }
        $this->dom->appChild($this->infCte, $this->detGTV, 'Falta tag "infCte"');
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
            'verProc',
            'cMunEnv',
            'xMunEnv',
            'UFEnv',
            'modal',
            'tpServ',
            'indIEToma',
            'dhSaidaOrig',
            'dhChegadaDest',
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
            substr(trim($std->natOp), 0, 60),
            true,
            $identificador . 'Natureza da Operação'
        );
        $this->dom->addChild(
            $this->ide,
            'mod',
            '64',
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
            'dhSaidaOrig',
            $std->dhSaidaOrig,
            true,
            $identificador . 'Data e hora de saída da origem'
        );
        $this->dom->addChild(
            $this->ide,
            'dhChegadaDest',
            $std->dhChegadaDest,
            true,
            $identificador . 'Data e hora de chegada no destino'
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
     * Gera as tags para o elemento: toma (Indicador do "papel" do tomador do serviço no CT-e)
     * e adiciona ao grupo ide
     * #35
     * Nível: 2
     * @param string $toma Tomador do Serviço
     * @param stdClass $std
     * @return \DOMElement
     */
    public function tagtoma($std)
    {
        $possible = [
            'toma'
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#35 <toma> - ';
        $this->toma3 = $this->dom->createElement('toma');
        $this->dom->addChild(
            $this->toma,
            'toma',
            $std->toma,
            true,
            $identificador . 'Tomador do Serviço'
        );
        return $this->toma;
    }

    /**
     * Gera as tags para o elemento: tomaTerceiro (Indicador do "papel" do tomador
     * do serviço no CT-e) e adiciona ao grupo ide
     * #37
     * Nível: 2
     *
     * @return \DOMElement
     */
    public function tagtomaTerceiro($std)
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
        $identificador = '#37 <tomaTerceiro> - ';
        $this->tomaTerceiro = $this->dom->createElement('tomaTerceiro');
        if ($std->CNPJ != '') {
            $this->dom->addChild(
                $this->tomaTerceiro,
                'CNPJ',
                $std->CNPJ,
                true,
                $identificador . 'Número do CNPJ'
            );
        } elseif ($std->CPF != '') {
            $this->dom->addChild(
                $this->tomaTerceiro,
                'CPF',
                $std->CPF,
                true,
                $identificador . 'Número do CPF'
            );
        } else {
            $this->dom->addChild(
                $this->tomaTerceiro,
                'CNPJ',
                $std->CNPJ,
                true,
                $identificador . 'Número do CNPJ'
            );
            $this->dom->addChild(
                $this->tomaTerceiro,
                'CPF',
                $std->CPF,
                true,
                $identificador . 'Número do CPF'
            );
        }
        $this->dom->addChild(
            $this->tomaTerceiro,
            'IE',
            $std->IE,
            false,
            $identificador . 'Inscrição Estadual'
        );
        $this->dom->addChild(
            $this->tomaTerceiro,
            'xNome',
            $std->xNome,
            true,
            $identificador . 'Razão Social ou Nome'
        );
        $this->dom->addChild(
            $this->tomaTerceiro,
            'xFant',
            $std->xFant,
            false,
            $identificador . 'Nome Fantasia'
        );
        $this->dom->addChild(
            $this->tomaTerceiro,
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
        $this->dom->appChild($this->tomaTerceiro, $this->enderToma, 'Falta tag "enderToma"');
        $this->dom->addChild(
            $this->tomaTerceiro,
            'email',
            $std->email,
            false,
            $identificador . 'Endereço de email'
        );
        return $this->tomaTerceiro;
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
            true,
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
            substr(trim($xNome), 0, 60),
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
        if (!empty($node)) {
            $this->rem->insertBefore($this->enderReme, $node);
        } else {
            $this->dom->appChild($this->rem, $this->enderReme, 'Falta tag "ide"');
        }
        return $this->enderReme;
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
        }
        $this->dom->addChild(
            $this->dest,
            'IE',
            $std->IE,
            false,
            $identificador . 'Inscrição Estadual'
        );
        $xNome = $std->xNome;
        if ($this->tpAmb == '2') {
            $xNome = 'CT-E EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL';
        }
        $this->dom->addChild(
            $this->dest,
            'xNome',
            substr(trim($xNome), 0, 60),
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
        if (!empty($node)) {
            $this->dest->insertBefore($this->enderDest, $node);
        } else {
            $this->dom->appChild($this->dest, $this->enderDest, 'Falta tag "ide"');
        }
        return $this->enderDest;
    }

    /**
     * Gera as tags para o elemento: "origem" (Informações do endereço da origem do serviço)
     * #118
     * Nível: 1
     *
     * @return \DOMElement
     */
    public function tagorigem($std)
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
            'fone',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#118 <origem> - ';
        $this->origem = $this->dom->createElement('origem');
        $this->dom->addChild(
            $this->origem,
            'xLgr',
            $std->xLgr,
            true,
            $identificador . 'Logradouro'
        );
        $this->dom->addChild(
            $this->origem,
            'nro',
            $std->nro,
            true,
            $identificador . 'Número'
        );
        $this->dom->addChild(
            $this->origem,
            'xCpl',
            $std->xCpl,
            false,
            $identificador . 'Complemento'
        );
        $this->dom->addChild(
            $this->origem,
            'xBairro',
            $std->xBairro,
            true,
            $identificador . 'Bairro'
        );
        $this->dom->addChild(
            $this->origem,
            'cMun',
            $std->cMun,
            true,
            $identificador . 'Código do município (utilizar a tabela do IBGE)'
        );
        $this->dom->addChild(
            $this->origem,
            'xMun',
            $std->xMun,
            true,
            $identificador . 'Nome do município'
        );
        $this->dom->addChild(
            $this->origem,
            'CEP',
            $std->CEP,
            false,
            $identificador . 'CEP'
        );
        $this->dom->addChild(
            $this->origem,
            'UF',
            $std->UF,
            true,
            $identificador . 'Sigla da UF'
        );
        $this->dom->addChild(
            $this->origem,
            'fone',
            $std->cPais,
            false,
            $identificador . 'Telefone'
        );
        return $this->origem;
    }

    /**
     * Gera as tags para o elemento: "origem" (Informações do endereço da destino do serviço)
     * #128
     * Nível: 1
     *
     * @return \DOMElement
     */
    public function tagdestino($std)
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
            'fone',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#128 <destino> - ';
        $this->destino = $this->dom->createElement('destino');
        $this->dom->addChild(
            $this->destino,
            'xLgr',
            $std->xLgr,
            true,
            $identificador . 'Logradouro'
        );
        $this->dom->addChild(
            $this->destino,
            'nro',
            $std->nro,
            true,
            $identificador . 'Número'
        );
        $this->dom->addChild(
            $this->destino,
            'xCpl',
            $std->xCpl,
            false,
            $identificador . 'Complemento'
        );
        $this->dom->addChild(
            $this->destino,
            'xBairro',
            $std->xBairro,
            true,
            $identificador . 'Bairro'
        );
        $this->dom->addChild(
            $this->destino,
            'cMun',
            $std->cMun,
            true,
            $identificador . 'Código do município (utilizar a tabela do IBGE)'
        );
        $this->dom->addChild(
            $this->destino,
            'xMun',
            $std->xMun,
            true,
            $identificador . 'Nome do município'
        );
        $this->dom->addChild(
            $this->destino,
            'CEP',
            $std->CEP,
            false,
            $identificador . 'CEP'
        );
        $this->dom->addChild(
            $this->destino,
            'UF',
            $std->UF,
            true,
            $identificador . 'Sigla da UF'
        );
        $this->dom->addChild(
            $this->destino,
            'fone',
            $std->cPais,
            false,
            $identificador . 'Telefone'
        );
        return $this->destino;
    }

    /**
     * tagdetGTV
     * Grupo de informações detalhadas da GTVe
     * @return DOMElement
     */
    public function tagdetGTV($std)
    {
        $possible = [
            'qCarga',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '138 <detGTV> - ';
        $this->detGTV = $this->dom->createElement('detGTV');
        $this->dom->addChild(
            $this->detGTV,
            'qCarga',
            $this->conditionalNumberFormatting($std->qCarga),
            true,
            "$identificador  Quantidade de volumes/malotes"
        );
        return $this->detGTV;
    }

    /**
     * Gera as tags para o elemento: "Comp" (Componentes do Valor da Prestação)
     * #211
     * Nível: 2
     *
     * @return \DOMElement
     */
    public function taginfEspecie($std)
    {
        $possible = [
            'tpEspecie',
            'vEspecie',
            'tpNumerario',
            'xMoedaEstr',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#139 <infEspecie> - ';
        $this->infEspecie[] = $this->dom->createElement('infEspecie');
        $posicao = (int)count($this->infEspecie) - 1;
        $this->dom->addChild(
            $this->infEspecie[$posicao],
            'tpEspecie',
            $std->tpEspecie,
            true,
            $identificador . 'Tipo da Espécie'
        );
        $this->dom->addChild(
            $this->infEspecie[$posicao],
            'vEspecie',
            $this->conditionalNumberFormatting($std->vEspecie),
            true,
            $identificador . 'Valor Transportada em Espécie indicada'
        );
        $this->dom->addChild(
            $this->infEspecie[$posicao],
            'tpNumerario',
            $std->tpNumerario,
            true,
            $identificador . 'Nacionalidade do Numerário'
        );
        $this->dom->addChild(
            $this->infEspecie[$posicao],
            'xMoedaEstr',
            $std->xMoedaEstr,
            false,
            $identificador . 'Nome da Moeda'
        );
        return $this->infEspecie[$posicao];
    }

    /**
     * Gera as tags para o elemento: "Comp" (Grupo de informações dos veículos utilizados no transporte de valores)
     * #145
     * Nível: 2
     *
     * @return \DOMElement
     */
    public function taginfVeiculo($std)
    {
        $possible = [
            'placa',
            'UF',
            'RNTRC',
        ];
        $std = $this->equilizeParameters($std, $possible);
        $identificador = '#139 <infVeiculo> - ';
        $this->infVeiculo[] = $this->dom->createElement('infVeiculo');
        $posicao = (int)count($this->infVeiculo) - 1;
        $this->dom->addChild(
            $this->infVeiculo[$posicao],
            'placa',
            $std->placa,
            true,
            $identificador . 'Placa do veículo'
        );
        $this->dom->addChild(
            $this->infVeiculo[$posicao],
            'UF',
            $std->UF,
            false,
            $identificador . 'UF em que veículo está licenciado'
        );
        $this->dom->addChild(
            $this->infVeiculo[$posicao],
            'RNTRC',
            $std->RNTRC,
            false,
            $identificador . 'RNTRC do transportador'
        );
        return $this->infVeiculo[$posicao];
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
     * Calcula hash sha1 retornando Base64Binary
     */
    protected function hashCSRT(string $CSRT): string
    {
        $comb = $CSRT . $this->chCTe;
        return base64_encode(sha1($comb, true));
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
