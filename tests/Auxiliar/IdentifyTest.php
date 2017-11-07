<?php

/**
 * Class IdentifyCTeTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */
use NFePHP\CTe\Auxiliar\IdentifyCTe;
use PHPUnit\Framework\TestCase;

class IdentifyTest extends TestCase
{
    public function testIdentificaCte()
    {
        $aResp = array();
        $filePath = dirname(dirname(__FILE__)) . '/fixtures/xml/0008-cte.xml';
        $schem = IdentifyCTe::identificar($filePath, $aResp);
        $this->assertEquals($schem, 'cte');
        $this->assertEquals('51121006145774000197570010000000131004100705', $aResp['chave']);
    }
}
