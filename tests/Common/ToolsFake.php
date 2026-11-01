<?php

namespace NFePHP\CTe\Tests\Common;

use NFePHP\CTe\Tools;

/**
 * Classe substituta de Tools para os testes: captura a mensagem
 * montada antes do envio ao webservice, sem qualquer acesso à rede.
 */
class ToolsFake extends Tools
{
    /**
     * @var string
     */
    public $sendRequestContent = '';

    /**
     * Sobrescreve o envio SOAP apenas para capturar o request
     * @param string $request
     * @param array $parameters
     * @return string
     */
    protected function sendRequest($request, array $parameters = [])
    {
        $this->sendRequestContent = $request;
        return '';
    }

    /**
     * Recupera a última mensagem montada por Tools
     * @return string
     */
    public function getRequest()
    {
        return $this->sendRequestContent;
    }
}
