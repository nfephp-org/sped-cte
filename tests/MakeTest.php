<?php

/**
 * Class MakeCTeTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */
use NFePHP\CTe\Make;

class MakeTest extends PHPUnit_Framework_TestCase
{
    public $cte;
    
    public function testeInstanciar()
    {
        $this->cte = new Make();
    }
}
