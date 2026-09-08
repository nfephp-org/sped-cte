<?php

namespace NFePHP\CTe\Tests\Lookup;

use NFePHP\CTe\Lookup\HttpGet;

/**
 * Cliente HTTP falso que devolve corpos distintos conforme o pacote presente na
 * URL (penúltimo segmento de .../token/pacote/documento). Permite simular, sem
 * rede, o cenário em que o resolvedor faz duas consultas: uma para os dados da
 * pessoa (pacote 5) e outra para as Inscrições Estaduais (pacote 16).
 */
class MapaHttpGet implements HttpGet
{
    /**
     * @var array<int,string> corpo indexado pelo ID do pacote
     */
    private $porPacote;

    /**
     * @var array<int,string> todas as URLs solicitadas, na ordem
     */
    public $urls = [];

    /**
     * @param array<int,string> $porPacote corpo a devolver para cada pacote
     */
    public function __construct(array $porPacote)
    {
        $this->porPacote = $porPacote;
    }

    /**
     * {@inheritDoc}
     */
    public function get($url)
    {
        $this->urls[] = $url;
        $partes = explode('/', $url);
        $pacote = (int) $partes[count($partes) - 2];
        if (isset($this->porPacote[$pacote])) {
            return $this->porPacote[$pacote];
        }
        return json_encode(['status' => 0, 'erro' => 'pacote sem mock', 'erroCodigo' => 0]);
    }
}
