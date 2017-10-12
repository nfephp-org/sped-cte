<?php

/**
 * Class MakeCTeTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */
use NFePHP\CTe\Make;

class MakeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @todo Refazer teste nova Make
     * @var Make
     */
    private $cte;

    
    public function testATagInfcteDeveConterOsAtributosIdEVersao()
    {
        $std = new stdClass();
        $std->Id = '0123456789';
        $std->versao = '3.00';
        $infCte = $this->cte->taginfCTe($std);

        $this->assertEquals('CTe' . $std->Id, $infCte->getAttribute('Id'));
        $this->assertEquals($std->versao, $infCte->getAttribute('versao'));
    }
//
//    public function testATagEmitNaoDeveAdicionarElementoOpcionalQuandoOValorForVazio()
//    {
//        $CNPJ = '52349850000101';
//        $IE = '867601021145';
//        $IEST = null;
//        $xNome = 'Pedro e Marcelo Financeira Ltda';
//        $xFant = null;
//        $emit = $this->cte->tagemit($CNPJ, $IE, $IEST, $xNome, $xFant);
//
//        $this->assertEquals($CNPJ, $emit->getElementsByTagName('CNPJ')->item(0)->nodeValue);
//        $this->assertEquals($IE, $emit->getElementsByTagName('IE')->item(0)->nodeValue);
//        $this->assertEquals($xNome, $emit->getElementsByTagName('xNome')->item(0)->nodeValue);
//        $this->assertEquals(0, $emit->getElementsByTagName('IEST')->length);
//        $this->assertEquals(0, $emit->getElementsByTagName('xFant')->length);
//
//        $IEST = '72759362';
//        $xFant = 'P e M Financeira';
//        $emit = $this->cte->emitTag($CNPJ, $IE, $IEST, $xNome, $xFant);
//
//        $this->assertEquals($IEST, $emit->getElementsByTagName('IEST')->item(0)->nodeValue);
//        $this->assertEquals($xFant, $emit->getElementsByTagName('xFant')->item(0)->nodeValue);
//    }
//
//    public function testATagInfcargaNaoDeveAdicionarElementoOpcionalQuandoOValorForVazio()
//    {
//        $vCarga = null;
//        $proPred = 'Livro';
//        $xOutCat = null;
//        $vCargaAverb = null;
//        $infCargaTag = $this->cte->infCargaTag($vCarga, $proPred, $xOutCat, $vCargaAverb);
//
//        $this->assertEquals($proPred, $infCargaTag->getElementsByTagName('proPred')->item(0)->nodeValue);
//        $this->assertEquals(0, $infCargaTag->getElementsByTagName('vCarga')->length);
//        $this->assertEquals(0, $infCargaTag->getElementsByTagName('xOutCat')->length);
//        //$this->assertEquals(0, $infCargaTag->getElementsByTagName('vCargaAverb')->length);
//
//        $vCarga = 100.00;
//        $xOutCat = 'FRIA';
//        $vCargaAverb = $vCarga;
//        $infCargaTag = $this->cte->infCargaTag($vCarga, $proPred, $xOutCat, $vCargaAverb);
//
//        $this->assertEquals($vCarga, $infCargaTag->getElementsByTagName('vCarga')->item(0)->nodeValue);
//        $this->assertEquals($xOutCat, $infCargaTag->getElementsByTagName('xOutCat')->item(0)->nodeValue);
//        // abaixo é opcional
//        //$this->assertEquals($vCargaAverb, $infCargaTag->getElementsByTagName('vCargaAverb')->item(0)->nodeValue);
//    }
//
//    public function testATagIdeNaoDeveAdicionarElementoOpcionalQuandoOValorForVazio()
//    {
//        $cUF = '41';
//        $cCT = '00000010';
//        $CFOP = '6352';
//        $natOp = substr('PRESTACAO DE SERVICO DE TRANSPORTE A ESTABELECIMEN', 0, 60);
//        $mod = '57';
//        $serie = '2';
//        $nCT = '58';
//        $dhEmi = '2016';
//        $tpImp = '1';
//        $tpEmis = '1';
//        $cDV = '1';
//        $tpAmb = '2';
//        $tpCTe = '0';
//        $procEmi = '0';
//        $verProc = '1.0.47.188';
//        $indGlobalizado = null;
//        $cMunEnv = '4108304';
//        $xMunEnv = 'FOZ DO IGUACU';
//        $UFEnv = 'PR';
//        $modal = '01';
//        $tpServ = '0';
//        $cMunIni = '4108304';
//        $xMunIni = 'FOZ DO IGUACU';
//        $UFIni = 'PR';
//        $cMunFim = '3523909';
//        $xMunFim = 'ITU';
//        $UFFim = 'SP';
//        $retira = '1';
//        $xDetRetira = null;
//        $indIEToma = 9;
//        $dhCont = '';
//        $xJust = '';
//        $ideTag = $this->cte->ideTag($cUF, $cCT, $CFOP, $natOp, $mod, $serie, $nCT, $dhEmi, $tpImp, $tpEmis, $cDV, $tpAmb, $tpCTe, $procEmi, $verProc, $indGlobalizado, $cMunEnv, $xMunEnv, $UFEnv, $modal, $tpServ, $cMunIni, $xMunIni, $UFIni, $cMunFim, $xMunFim, $UFFim, $retira, $xDetRetira, $indIEToma, $dhCont, $xJust);
//
//        $this->assertEquals($cUF, $ideTag->getElementsByTagName('cUF')->item(0)->nodeValue);
//        $this->assertEquals($indIEToma, $ideTag->getElementsByTagName('indIEToma')->item(0)->nodeValue);
//        $this->assertEquals(0, $ideTag->getElementsByTagName('indGlobalizado')->length);
//        $this->assertEquals(0, $ideTag->getElementsByTagName('xDetRetira')->length);
//
//        $indGlobalizado = 1;
//        $xDetRetira = 'A/C Caesar Cardini';
//        $ideTag = $this->cte->ideTag($cUF, $cCT, $CFOP, $natOp, $mod, $serie, $nCT, $dhEmi, $tpImp, $tpEmis, $cDV, $tpAmb, $tpCTe, $procEmi, $verProc, $indGlobalizado, $cMunEnv, $xMunEnv, $UFEnv, $modal, $tpServ, $cMunIni, $xMunIni, $UFIni, $cMunFim, $xMunFim, $UFFim, $retira, $xDetRetira, $indIEToma, $dhCont, $xJust);
//
//        $this->assertEquals($indGlobalizado, $ideTag->getElementsByTagName('indGlobalizado')->item(0)->nodeValue);
//        $this->assertEquals($xDetRetira, $ideTag->getElementsByTagName('xDetRetira')->item(0)->nodeValue);
//    }
//
//    public function testONomeDaTagDeveSerToma3AoInvesDeToma03()
//    {
//        $toma = 0;
//        $toma3Tag = $this->cte->toma3Tag($toma);
//
//        $this->assertEquals($toma, $toma3Tag->getElementsByTagName('toma')->item(0)->nodeValue);
//        $this->assertEquals('toma3', $toma3Tag->nodeName);
//    }
//
//    public function testRemocaoDeElementosDoModalRodoviario()
//    {
//        $RNTRC = '99999999';
//        $rodoTag = $this->cte->rodoTag($RNTRC);
//
//        $this->assertEquals("<rodo><RNTRC>{$RNTRC}</RNTRC></rodo>", $rodoTag->ownerDocument->saveXML($rodoTag));
//    }

    protected function setUp()
    {
        parent::setUp();
        $this->cte = new Make();
    }

    protected function tearDown()
    {
        parent::tearDown();
        unset($this->cte);
    }
}
