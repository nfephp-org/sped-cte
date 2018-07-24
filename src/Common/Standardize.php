<?php

namespace NFePHP\CTe\Common;

/**
 * Class for identification and convertion of eletronic documents in xml
 * for documents used in sped-nfe, sped-esocial, sped-cte, sped-mdfe, etc.
 *
 * @category  NFePHP
 * @package   NFePHP\Common\Standardize
 * @copyright NFePHP Copyright (c) 2008-2017
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/sped-nfe for the canonical source repository
 */

use NFePHP\Common\Validator;
use NFePHP\CTe\Exception\DocumentsException;
use Symfony\Component\Yaml\Yaml;
use DOMDocument;
use stdClass;

class Standardize
{
    /**
     * @var string
     */
    public $node = '';
    /**
     * @var string
     */
    public $json = '';
    /**
     * @var string
     */
    public $key = '';
    /**
     * @var array
     */
    public $rootTagList = [
        'enviCTe',
        'retEnviCte',
        'retConsReciCTe',
        'consSitCTe',
        'retConsSitCTe',
        'eventoCTe',
        'retEventoCTe',
        'cteRecepcaoOSResult',
        'evCancCTe',
        'inutCTe',
        'retInutCTe',
        'procInutCTe',
        'retConsStatServCte',
        'CTe',
        'CTeOS'
    ];

    /**
     * Constructor
     * @param string $xml
     */
    public function __construct($xml = null)
    {
        $this->toStd($xml);
    }

    /**
     * Identify node and extract from XML for convertion type
     * @param string $xml
     * @return string identificated node name
     * @throws InvalidArgumentException
     */
    public function whichIs($xml)
    {
        if (!Validator::isXML($xml)) {
            //invalid document is not a XML
            throw DocumentsException::wrongDocument(6);
        }
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($xml);
        foreach ($this->rootTagList as $key) {
            $node = !empty($dom->getElementsByTagName($key)->item(0))
                ? $dom->getElementsByTagName($key)->item(0)
                : '';
            if (!empty($node)) {
                $this->node = $dom->saveXML($node);
                return $key;
            }
        }
        //documento does not belong to the SPED-NFe project
        throw DocumentsException::wrongDocument(7);
    }

    /**
     * Returns extract node from XML
     * @return string
     */
    public function __toString()
    {
        return $this->node;
    }

    /**
     * Returns stdClass converted from xml
     * @param string $xml
     * @return stdClass
     */
    public function toStd($xml = null)
    {
        if (!empty($xml)) {
            $this->key = $this->whichIs($xml);
        }

        $sxml = simplexml_load_string($this->node);
        $this->json = str_replace(
            '@attributes',
            'attributes',
            json_encode($sxml, JSON_PRETTY_PRINT)
        );
        return json_decode($this->json);
    }

    /**
     * Retruns JSON string form XML
     * @param string $xml
     * @return string
     */
    public function toJson($xml = null)
    {
        if (!empty($xml)) {
            $this->toStd($xml);
        }
        return $this->json;
    }

    /**
     * Returns array from XML
     * @param string $xml
     * @return array
     */
    public function toArray($xml = null)
    {
        if (!empty($xml)) {
            $this->toStd($xml);
        }
        return json_decode($this->json, true);
    }

    /**
     * Returns YAML from XML
     * @param string $xml
     * @return string
     */
    public function toYaml($xml = null)
    {
        if (!empty($xml)) {
            $this->toStd($xml);
        }
        $array = $this->toArray();
        return Yaml::dump($array, 6, 4);
    }
}
