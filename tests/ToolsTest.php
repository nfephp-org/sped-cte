<?php

/**
 * Class ToolsCTeTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */
use NFePHP\Common\Files\FilesFolders;
use NFePHP\CTe\Tools;

class ToolsTest extends PHPUnit_Framework_TestCase
{
    public $cte;
    private $xmlFilepath;
    private $xmlContent;

    /**
     * @expectedException NFePHP\Common\Exception\InvalidArgumentException
     */
    public function testDeveLancarInvalidargumentexceptionAoInstanciarComParametroVazio()
    {
        $configJson = '';
        $this->cte = new Tools($configJson);
    }

    public function testDeveRetornarArrayVazioSeXmlForValido()
    {
        $retorno = Tools::validarXmlCte($this->xmlContent, Tools::$PL_CTE_200);
        $this->assertInternalType('array', $retorno);
        $this->assertEmpty($retorno);
    }

    public function testDeveRetornarArrayComErrosDeValizacaoSeXmlForInvalido()
    {
        $invalidXmlContent = str_replace('<forPag>0</forPag>', '', $this->xmlContent);
        $retorno = Tools::validarXmlCte($invalidXmlContent, Tools::$PL_CTE_200);
        $this->assertInternalType('array', $retorno);
        $this->assertNotEmpty($retorno);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->xmlFilepath = implode(DIRECTORY_SEPARATOR, [__DIR__, 'fixtures', 'xml', 'cte_v200.xml']);
        $this->xmlContent = FilesFolders::readFile($this->xmlFilepath);
    }

    protected function tearDown()
    {
        parent::tearDown();

        unset($this->xmlFilepath, $this->xmlContent);
    }
}
