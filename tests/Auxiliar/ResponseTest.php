<?php

/**
 * Class ReturnCTeTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */
use NFePHP\CTe\Auxiliar\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public $mdfe;

    public function testInstanciar()
    {
        $this->mdfe = new Response();
    }
}
