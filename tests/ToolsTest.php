<?php

/**
 * Class ToolsCTeTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */
use NFePHP\CTe\Tools;

class ToolsTest extends PHPUnit_Framework_TestCase
{
    public $cte;
    
    /**
     * @expectedException NFePHP\Common\Exception\InvalidArgumentException
     */
    public function testeInstanciar()
    {
        $configJson = dirname(dirname(__FILE__)) . '/fixtures/config/fakeconfig.json';
        $this->cte = new Tools($configJson);
    }
}
