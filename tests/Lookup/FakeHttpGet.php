<?php

namespace NFePHP\CTe\Tests\Lookup;

use NFePHP\CTe\Lookup\HttpGet;

/**
 * Cliente HTTP falso para os testes: não acessa a rede, apenas devolve um corpo
 * predefinido e registra a última URL solicitada, permitindo verificar a
 * montagem da requisição sem qualquer dependência externa.
 */
class FakeHttpGet implements HttpGet
{
    /**
     * @var string
     */
    private $corpo;

    /**
     * @var string|null
     */
    public $ultimaUrl;

    /**
     * @param string $corpo corpo que será devolvido em toda chamada get()
     */
    public function __construct($corpo)
    {
        $this->corpo = $corpo;
    }

    /**
     * {@inheritDoc}
     */
    public function get($url)
    {
        $this->ultimaUrl = $url;
        return $this->corpo;
    }
}
