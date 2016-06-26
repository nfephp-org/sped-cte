<?php

/**
 * Class ConvertNFeTest
 * @author Roberto L. Machado <linux.rlm at gmail dot com>
 */
use NFePHP\CTe\Convert;

class ConvertTest extends PHPUnit_Framework_TestCase
{
    public $cte;
    
    public function testeInstanciar()
    {
        $this->cte = new Convert();
    }
}
