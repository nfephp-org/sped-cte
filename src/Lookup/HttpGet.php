<?php

namespace NFePHP\CTe\Lookup;

/**
 * Contrato mínimo de transporte HTTP GET usado pela consulta de pessoas.
 *
 * Existe para manter o núcleo da biblioteca desacoplado de qualquer cliente
 * HTTP concreto: a implementação padrão (CurlHttpGet) usa a extensão cURL, mas
 * o integrador pode injetar qualquer cliente (Guzzle, PSR-18, mock de teste)
 * que respeite este contrato. Também é o ponto de injeção usado nos testes para
 * simular respostas sem acesso à rede.
 *
 * @category  Library
 * @package   NFePHP\CTe\Lookup
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @link      https://github.com/nfephp-org/sped-cte
 */
interface HttpGet
{
    /**
     * Executa uma requisição GET e devolve o corpo da resposta.
     *
     * @param string $url URL completa da requisição
     * @return string corpo da resposta
     * @throws \NFePHP\CTe\Exception\RuntimeException em falha de transporte
     */
    public function get($url);
}
