<?php

namespace NFePHP\CTe\Common;

/**
 * Class base responsible for communication with SEFAZ
 *
 * @category  NFePHP
 * @package   NFePHP\CTe\Common\Tools
 * @copyright NFePHP Copyright (c) 2008-2017
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-c for the canonical source repository
 */

use DOMDocument;
use InvalidArgumentException;
use NFePHP\Common\Certificate;
use NFePHP\Common\Signer;
use NFePHP\Common\Soap\SoapCurl;
use NFePHP\Common\Soap\SoapInterface;
use NFePHP\Common\Strings;
use NFePHP\Common\TimeZoneByUF;
use NFePHP\Common\UFList;
use NFePHP\Common\Validator;
use NFePHP\CTe\Factories\Contingency;
use NFePHP\CTe\Factories\ContingencyCTe;
use NFePHP\CTe\Factories\Header;
use NFePHP\CTe\Factories\QRCode;
use RuntimeException;

class Tools
{
    /**
     * config class
     * @var \stdClass
     */
    public $config;
    /**
     * Path to storage folder
     * @var string
     */
    public $pathwsfiles = '';
    /**
     * Path to schemes folder
     * @var string
     */
    public $pathschemes = '';
    /**
     * ambiente
     * @var string
     */
    public $ambiente = 'homologacao';
    /**
     * Environment
     * @var int
     */
    public $tpAmb = 2;
    /**
     * contingency class
     * @var Contingency
     */
    public $contingency;
    /**
     * soap class
     * @var SoapInterface
     */
    public $soap;
    /**
     * Application version
     * @var string
     */
    public $verAplic = '';
    /**
     * last soap request
     * @var string
     */
    public $lastRequest = '';
    /**
     * last soap response
     * @var string
     */
    public $lastResponse = '';
    /**
     * certificate class
     * @var Certificate
     */
    protected $certificate;
    /**
     * Sign algorithm from OPENSSL
     * @var int
     */
    protected $algorithm = OPENSSL_ALGO_SHA1;
    /**
     * Canonical conversion options
     * @var array
     */
    protected $canonical = [true, false, null, null];
    /**
     * Model of CTe 57 or 67
     * @var int
     */
    protected $modelo = 57;
    /**
     * Version of layout
     * @var string
     */
    protected $versao = '4.00';
    /**
     * urlPortal
     * Instância do WebService
     *
     * @var string
     */
    protected $urlPortal = 'http://www.portalfiscal.inf.br/cte';
    /**
     * urlcUF
     * @var string
     */
    protected $urlcUF = '';
    /**
     * urlVersion
     * @var string
     */
    protected $urlVersion = '';
    /**
     * urlService
     * @var string
     */
    protected $urlService = '';
    /**
     * @var string
     */
    protected $urlMethod = '';
    /**
     * @var string
     */
    protected $urlOperation = '';
    /**
     * @var string
     */
    protected $urlNamespace = '';
    /**
     * @var string
     */
    protected $urlAction = '';
    /**
     * @var \SOAPHeader
     */
    protected $objHeader;
    /**
     * @var string
     */
    protected $urlHeader = '';
    /**
     * @var array
     */
    protected $soapnamespaces = [
        'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
        'xmlns:xsd' => "http://www.w3.org/2001/XMLSchema",
        'xmlns:soap' => "http://www.w3.org/2003/05/soap-envelope"
    ];
    /**
     * @var array
     */
    protected $availableVersions = [
        '4.00' => 'PL_CTe_400'
    ];
    /**
     * @var string
     */
    protected $timezone;

    /**
     * Constructor
     * load configurations,
     * load Digital Certificate,
     * map all paths,
     * set timezone and
     * and instanciate Contingency::class
     * @param string $configJson content of config in json format
     * @param Certificate $certificate
     */
    public function __construct($configJson, Certificate $certificate)
    {
        $this->config = json_decode($configJson);
        $this->pathwsfiles = realpath(
            __DIR__ . '/../../storage'
        ) . '/';
        $this->version($this->config->versao);
        $this->setEnvironmentTimeZone($this->config->siglaUF);
        $this->certificate = $certificate;
        $this->setEnvironment($this->config->tpAmb);
        $this->setEnvironmentHttpVersion();
        $this->contingency = new Contingency();
    }

    /**
     * set version in http
     *
     * @return void
     */
    private function setEnvironmentHttpVersion()
    {
        $soap = new SoapCurl();
        $soap->httpVersion('1.1');
        $this->loadSoapClass($soap);
    }

    /**
     * Sets environment time zone
     * @param string $acronym (ou seja a sigla do estado)
     * @return void
     */
    public function setEnvironmentTimeZone($acronym)
    {
        $this->timezone = TimeZoneByUF::get($acronym);
    }

    /**
     * Set application version
     * @param string $ver
     */
    public function setVerAplic($ver)
    {
        $this->verAplic = $ver;
    }

    /**
     * Load Soap Class
     * Soap Class may be \NFePHP\Common\Soap\SoapNative
     * or \NFePHP\Common\Soap\SoapCurl
     * @param SoapInterface $soap
     * @return void
     */
    public function loadSoapClass(SoapInterface $soap)
    {
        $this->soap = $soap;
        $this->soap->loadCertificate($this->certificate);
    }

    /**
     * Set OPENSSL Algorithm using OPENSSL constants
     * @param int $algorithm
     * @return void
     */
    public function setSignAlgorithm($algorithm = OPENSSL_ALGO_SHA1)
    {
        $this->algorithm = $algorithm;
    }

    /**
     * Set or get model of document CTe = 57 or CTeOS = 67
     * @param int $model
     * @return int modelo class parameter
     */
    public function model($model = null)
    {
        if ($model == 57 || $model == 67) {
            $this->modelo = $model;
        }
        return $this->modelo;
    }

    /**
     * Set or get parameter layout version
     * @param string $version
     * @return string
     * @throws InvalidArgumentException
     */
    public function version($version = '')
    {
        if (!empty($version)) {
            if (!array_key_exists($version, $this->availableVersions)) {
                throw new \InvalidArgumentException('Essa versão de layout não está disponível');
            }
            $this->versao = $version;
            $this->config->schemes = $this->availableVersions[$version];
            $this->pathschemes = realpath(
                __DIR__ . '/../../schemes/' . $this->config->schemes
            ) . '/';
        }
        return $this->versao;
    }

    /**
     * Recover cUF number from state acronym
     * @param string $acronym Sigla do estado
     * @return int number cUF
     */
    public function getcUF($acronym)
    {
        return UFlist::getCodeByUF($acronym);
    }

    /**
     * Recover state acronym from cUF number
     * @param int $cUF
     * @return string acronym sigla
     */
    public function getAcronym($cUF)
    {
        return UFlist::getUFByCode($cUF);
    }

    /**
     * Validate cUF from the key content and returns the state acronym
     * @param string $chave
     * @return string
     * @throws InvalidArgumentException
     */
    public function validKeyByUF($chave)
    {
        $uf = $this->config->siglaUF;
        if ($uf != UFList::getUFByCode(substr($chave, 0, 2))) {
            throw new \InvalidArgumentException(
                "A chave do CTe indicado [$chave] não pertence a [$uf]."
            );
        }
        return $uf;
    }

    /**
     * Sign CTe
     * @param string $xml CTe xml content
     * @return string singed CTe xml
     * @throws RuntimeException
     */
    public function signCTe($xml)
    {
        //remove all invalid strings
        $xml = Strings::clearXmlString($xml);
        $signed = Signer::sign(
            $this->certificate,
            $xml,
            'infCte',
            'Id',
            $this->algorithm,
            $this->canonical
        );
        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($signed);
        //exception will be throw if CTe is not valid
        $modelo = $dom->getElementsByTagName('mod')->item(0)->nodeValue;

        $tpCTe = (int)$dom->getElementsByTagName('tpCTe')->item(0)->nodeValue;
        if (($tpCTe == 5) || ($tpCTe == 6)) {
            $method = 'cteSimp';
        } else {
            $method = 'cte';
        }

        if ($modelo == 67) {
            $method = 'cteOS';
        }
        $isInfCTeSupl = !empty($dom->getElementsByTagName('infCTeSupl')->item(0));
        if (!$isInfCTeSupl) {
            $signed = $this->addQRCode($dom);
        }
        $this->isValid($this->versao, $signed, $method);
        $modal = (int)$dom->getElementsByTagName('modal')->item(0)->nodeValue;
        if ($modelo == 57) {
            switch ($modal) {
                case 1:
                    //Rodoviário
                    $rodo = $this->getModalXML($dom, 'rodo');
                    if ($rodo) {
                        $this->isValid($this->versao, $rodo, 'cteModalRodoviario');
                    }
                    break;
                case 2:
                    //Aéreo
                    $aereo = $this->getModalXML($dom, 'aereo');
                    if ($aereo) {
                        $this->isValid($this->versao, $aereo, 'cteModalAereo');
                    }
                    break;
                case 3:
                    //Aquaviário
                    $aquav = $this->getModalXML($dom, 'aquav');
                    if ($aquav) {
                        $this->isValid($this->versao, $aquav, 'cteModalAquaviario');
                    }
                    break;
                case 4:
                    //Ferroviário
                    $ferrov = $this->getModalXML($dom, 'ferrov');
                    if ($ferrov) {
                        $this->isValid($this->versao, $ferrov, 'cteModalFerroviario');
                    }
                    break;
                case 5:
                    //Dutoviário
                    $duto = $this->getModalXML($dom, 'duto');
                    if ($duto) {
                        $this->isValid($this->versao, $duto, 'cteModalDutoviario');
                    }
                    break;
                case 6:
                    //Multimodal
                    $multimodal = $this->getModalXML($dom, 'multimodal');
                    if ($multimodal) {
                        $this->isValid($this->versao, $multimodal, 'cteMultiModal');
                    }
                    break;
            }
        } else if ($modelo == 67) {
            //Rodoviário
            $rodoOS = $this->getModalXML($dom, 'rodoOS');
            if ($rodoOS) {
                $this->isValid($this->versao, $rodoOS, 'cteModalRodoviarioOS');
            }
        }
        return $signed;
    }

    /**
     * @param string $Dom CTe xml content
     * @param string $xml CTe xml content
     * @return string|bool
     * @todo
     * Retorna o xml do modal especifico
     */
    public function getModalXML($dom, $modal)
    {
        $modal = $dom->getElementsByTagName($modal)->item(0);
        if (!empty($modal)) {
            $modal->setAttribute("xmlns", "http://www.portalfiscal.inf.br/cte");
            return $dom->saveXML($modal);
        }
        return false;
    }

    /**
     * @param string $xml CTe xml content
     * @return string
     * @todo
     * Corret CTe fields when in contingency mode is set
     */
    protected function correctCTeForContingencyMode($xml)
    {
        if ($this->contingency->type == '') {
            return $xml;
        }
        $xml = ContingencyCTe::adjust($xml, $this->contingency);
        return $this->signCTe($xml);
    }

    /**
     * Performs xml validation with its respective
     * XSD structure definition document
     * NOTE: if dont exists the XSD file will return true
     * @param string $version layout version
     * @param string $body
     * @param string $method
     * @return boolean
     */
    protected function isValid($version, $body, $method)
    {
        $schema = $this->pathschemes . $method . "_v$version.xsd";
        if (!is_file($schema)) {
            return true;
        }
        return Validator::isValid(
            $body,
            $schema
        );
    }

    /**
     * Verifies the existence of the service
     * @param string $service
     * @throws RuntimeException
     */
    protected function checkContingencyForWebServices($service)
    {
        $permit = [
            57 => ['SVSP', 'SVRS'],
            67 => ['SVSP', 'SVRS']
        ];

        $type = $this->contingency->type;
        $mod = $this->modelo;
        if (!empty($type)) {
            if (array_search($type, $permit[$mod]) === false) {
                throw new RuntimeException(
                    "Esse modo de contingência [$type] não é aceito "
                        . "para o modelo [$mod]"
                );
            }
        }
    }

    /**
     * Alter environment from "homologacao" to "producao" and vice-versa
     * @param int $tpAmb
     * @return void
     */
    public function setEnvironment($tpAmb = 2)
    {
        if (!empty($tpAmb) && ($tpAmb == 1 || $tpAmb == 2)) {
            $this->tpAmb = $tpAmb;
            $this->ambiente = ($tpAmb == 1) ? 'producao' : 'homologacao';
        }
    }

    /**
     * Set option for canonical transformation see C14n
     * @param array $opt
     * @return array
     */
    public function canonicalOptions($opt = [true, false, null, null])
    {
        if (!empty($opt) && is_array($opt)) {
            $this->canonical = $opt;
        }
        return $this->canonical;
    }

    /**
     * Assembles all the necessary parameters for soap communication
     * @param string $service
     * @param string $uf
     * @param string $tpAmb
     * @param bool $ignoreContingency
     * @return void
     */
    protected function servico(
        $service,
        $uf,
        $tpAmb,
        $ignoreContingency = false
    ) {
        $ambiente = $tpAmb == 1 ? "producao" : "homologacao";
        $webs = new Webservices($this->getXmlUrlPath());
        $sigla = $uf;
        if (!$ignoreContingency) {
            $contType = $this->contingency->type;
            if (!empty($contType) && ($contType == 'SVRS' || $contType == 'SVSP')) {
                $sigla = $contType;
            }
        }
        $stdServ = $webs->get($sigla, $ambiente, $this->modelo);
        if ($stdServ === false) {
            throw new \RuntimeException(
                "Nenhum serviço foi localizado para esta unidade "
                    . "da federação [$sigla], com o modelo [$this->modelo]."
            );
        }
        if (empty($stdServ->$service->url)) {
            throw new \RuntimeException(
                "Este serviço [$service] não está disponivel para esta "
                    . "unidade da federação [$uf] ou para este modelo de Nota ["
                    . $this->modelo
                    . "]."
            );
        }
        //recuperação do cUF
        $this->urlcUF = $this->getcUF($uf);
        if ($this->urlcUF > 91) {
            //foi solicitado dado de SVRS ou SVSP
            $this->urlcUF = $this->getcUF($this->config->siglaUF);
        }
        //recuperação da versão
        $this->urlVersion = $stdServ->$service->version;
        //recuperação da url do serviço
        $this->urlService = $stdServ->$service->url;
        //recuperação do método
        $this->urlMethod = $stdServ->$service->method;
        //recuperação da operação
        $this->urlOperation = $stdServ->$service->operation;
        //montagem do namespace do serviço
        $this->urlNamespace = sprintf(
            "%s/wsdl/%s",
            $this->urlPortal,
            $this->urlOperation
        );
        //montagem do cabeçalho da comunicação SOAP
        $this->urlHeader = Header::get(
            $this->urlNamespace,
            $this->urlcUF,
            $this->urlVersion
        );
        $this->urlAction = "\""
            . $this->urlNamespace
            . "/"
            . $this->urlMethod
            . "\"";
        //montagem do SOAP Header
        $this->objHeader = new \SOAPHeader(
            $this->urlNamespace,
            'cteCabecMsg',
            ['cUF' => $this->urlcUF, 'versaoDados' => $this->urlVersion]
        );
    }

    /**
     * Send request message to webservice
     * @param array $parameters
     * @param string $request
     * @return string
     */
    protected function sendRequest($request, array $parameters = [])
    {
        $this->checkSoap();
        return (string)$this->soap->send(
            $this->urlService,
            $this->urlMethod,
            $this->urlAction,
            SOAP_1_2,
            $parameters,
            $this->soapnamespaces,
            $request,
            $this->objHeader
        );
    }

    /**
     * Recover path to xml data base with list of soap services
     * @return string
     */
    protected function getXmlUrlPath()
    {
        $file = $this->pathwsfiles
            . DIRECTORY_SEPARATOR
            . "wscte_{$this->versao}_mod{$this->modelo}.xml";
        if (!file_exists($file)) {
            return '';
        }
        return file_get_contents($file);
    }

    /**
     * Add QRCode Tag to signed XML from a NFCe
     * @param DOMDocument $dom
     * @return string
     */
    protected function addQRCode(DOMDocument $dom)
    {
        $tpAmb = $this->config->tpAmb == 1 ? 'producao' : 'homologacao';
        $sigla = $this->config->siglaUF;
        $webs = new Webservices($this->getXmlUrlPath());
        $std = $webs->get($sigla, $tpAmb, $this->modelo);
        if ($std === false) {
            throw new \RuntimeException(
                "Nenhum serviço foi localizado para esta unidade "
                    . "da federação [$sigla], com o modelo [$this->modelo]."
            );
        }
        if (empty($std->QRCode->url)) {
            throw new \RuntimeException(
                "Este serviço [QRCode] não está disponivel para esta "
                    . "unidade da federação [$sigla] ou para este modelo de Nota ["
                    . $this->modelo
                    . "]."
            );
        }
        $signed = QRCode::putQRTag(
            $dom,
            $this->certificate,
            $std->QRCode->url
        );
        return Strings::clearXmlString($signed);
    }

    /**
     * Verify if SOAP class is loaded, if not, force load SoapCurl
     */
    protected function checkSoap()
    {
        if (empty($this->soap)) {
            $this->soap = new SoapCurl($this->certificate);
        }
    }
}
