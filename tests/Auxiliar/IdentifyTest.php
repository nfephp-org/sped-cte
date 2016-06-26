<?php

/**
 * Class IdentifyCTeTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */
use NFePHP\CTe\Auxiliar\Identify;

class IdentifyTest extends PHPUnit_Framework_TestCase
{
    public function testeIdentificaCTe()
    {
        $aResp = array();
        $filePath = dirname(dirname(__FILE__)) . '/fixtures/xml/0008-cte.xml';
        $schem = Identify::identificar($filePath, $aResp);
        $this->assertEquals($schem, 'mdfe');
    }
}
