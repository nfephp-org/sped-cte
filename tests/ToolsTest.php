<?php

/**
 * Class ToolsCTeTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */
use NFePHP\CTe\Tools;
use PHPUnit\Framework\TestCase;

class ToolsTest extends TestCase
{
    public $cte;

    /**
     * @expectedException NFePHP\Common\Exception\InvalidArgumentException
     */
    public function testDeveLancarInvalidargumentexceptionAoInstanciarComParametroVazio()
    {
        $configJson = '';
        $this->cte = new Tools($configJson);
    }
}
