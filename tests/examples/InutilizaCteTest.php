<?php

/**
 * Class InutilizaTest
 * @author Maison K. Sakamoto <maison.sakamoto at gmail dot com>
 */
use NFePHP\CTe\Tools;

class InutilizaCteTest extends PHPUnit_Framework_TestCase
{
    public $cteTools;
    public function testeInstanciar()
    {
        $this->cteTools = new Tools('../../config/config.json');         
    }       
    public function testeInutilizar(){
        $this->cteTools = new Tools('../../config/config.json'); 
        $aResposta = array();
        $ano = date("y");
        $serie = '2';
        $inicio = '2';
        $fim = '2';
        $tpAmb = '2';
        $xJust = 'teste de Inutilizacao em ambiente de homologacao(teste)'; // MINIMO DE 15 DIGITOS

        $this->cteTools->aConfig['cnpj']='81450900000132'; // CNPJ DA EMPRESA SERA USADO DENTRO DA CLASSE
        $this->cteTools->aConfig['siglaUF']='PR';
        $retorno = $this->cteTools->sefazInutiliza($ano, $serie, $inicio, $fim, $xJust, $tpAmb, $aResposta);
        echo '<pre>';
        echo htmlspecialchars($this->cteTools->soapDebug);
        print_r($aResposta);
        //print_r($retorno);
        echo "</pre>";
    }
}