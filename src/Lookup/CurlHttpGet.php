<?php

namespace NFePHP\CTe\Lookup;

use NFePHP\CTe\Exception\RuntimeException;

/**
 * Implementação padrão de HttpGet baseada na extensão cURL.
 *
 * É a única classe do pacote de consulta que toca a rede. Fica isolada atrás
 * do contrato HttpGet, de modo que o restante do recurso (e todo o núcleo da
 * biblioteca) permaneça offline e testável sem acesso externo.
 *
 * @category  Library
 * @package   NFePHP\CTe\Lookup
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @link      https://github.com/nfephp-org/sped-cte
 */
class CurlHttpGet implements HttpGet
{
    /**
     * Tempo máximo, em segundos, para a requisição completa.
     *
     * @var int
     */
    private $timeout;

    /**
     * @param int $timeout tempo máximo da requisição, em segundos
     */
    public function __construct($timeout = 60)
    {
        $this->timeout = (int) $timeout;
    }

    /**
     * {@inheritDoc}
     */
    public function get($url)
    {
        if (!function_exists('curl_init')) {
            throw new RuntimeException(
                'A extensão cURL é necessária para a implementação padrão de HttpGet.'
            );
        }
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($handle, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($handle, CURLOPT_HTTPHEADER, ['Accept: application/json']);
        $body = curl_exec($handle);
        $erro = curl_error($handle);
        $codigo = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);
        if ($body === false || $erro !== '') {
            throw new RuntimeException(
                'Falha na requisição HTTP de consulta: ' . $erro
            );
        }
        if ($codigo >= 400) {
            throw new RuntimeException(
                'A consulta respondeu com código HTTP ' . $codigo . '.'
            );
        }
        return (string) $body;
    }
}
