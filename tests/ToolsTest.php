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

    public function testDeveSerializarGruposDeInformacoesDeCorrecaoParaXml()
    {
        $mensagemDoEventoCartaDeCorrecao = Tools::serializarMensagemDoEventoCartaDeCorrecao([
            [
                "grupoAlterado" => 'ide',
                "campoAlterado" => 'natOp',
                "valorAlterado" => 'teste de carta de correcao',
            ],
            [
                "grupoAlterado" => 'ide',
                "campoAlterado" => 'natOp',
                "valorAlterado" => 'teste de carta de correcao',
                "nroItemAlterado" => '1'
            ],
        ]);
        $expectedXml = "<evCCeCTe>
            <descEvento>Carta de Correcao</descEvento>
            <infCorrecao>
                <grupoAlterado>ide</grupoAlterado>
                <campoAlterado>natOp</campoAlterado>
                <valorAlterado>teste de carta de correcao</valorAlterado>
            </infCorrecao>
            <infCorrecao>
            <grupoAlterado>ide</grupoAlterado>
            <campoAlterado>natOp</campoAlterado>
            <valorAlterado>teste de carta de correcao</valorAlterado>
            <nroItemAlterado>1</nroItemAlterado>
            </infCorrecao>
            <xCondUso>
            A Carta de Correcao e disciplinada pelo Art. 58-B do 
            CONVENIO/SINIEF 06/89: Fica permitida a utilizacao de carta de 
            correcao, para regularizacao de erro ocorrido na emissao de 
            documentos fiscais relativos a prestacao de servico de transporte, 
            desde que o erro nao esteja relacionado com: I - as variaveis que 
            determinam o valor do imposto tais como: base de calculo, 
            aliquota, diferenca de preco, quantidade, valor da prestacao;II - 
            a correcao de dados cadastrais que implique mudanca do emitente, 
            tomador, remetente ou do destinatario;III - a data de emissao ou 
            de saida.
            </xCondUso>
        </evCCeCTe>";
        $this->assertXmlStringEqualsXmlString($expectedXml, $mensagemDoEventoCartaDeCorrecao);
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
