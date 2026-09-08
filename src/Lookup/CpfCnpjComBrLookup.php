<?php

namespace NFePHP\CTe\Lookup;

use NFePHP\CTe\Exception\InvalidArgumentException;
use NFePHP\CTe\Exception\RuntimeException;

/**
 * Implementação de PessoaLookup que consulta a API pública da CpfCnpj.com.br.
 *
 * A requisição é sempre um GET com todos os parâmetros embutidos na URL:
 *
 *     https://api.cpfcnpj.com.br/{token}/{pacote}/{documento}
 *
 * O token é o primeiro segmento da URL (não é cabeçalho HTTP) e é obtido no
 * Painel de Controle em API > Tokens. O pacote define o conjunto de dados
 * retornado; por padrão usa-se o pacote 3 (CPF com nome e endereço) e o pacote
 * 5 (CNPJ com razão social, nome fantasia e endereço da matriz). O pacote 6
 * pode ser configurado para CNPJ quando o integrador quiser dados adicionais.
 *
 * A resposta traz sempre o campo status: 1 em caso de sucesso e 0 em caso de
 * falha (acompanhado de erro e erroCodigo). A partir dos dados retornados esta
 * classe monta um \stdClass com as propriedades homônimas dos campos do CT-e.
 *
 * Os métodos porCpf, porCnpj e porDocumento nunca preenchem a Inscrição
 * Estadual. Para o cenário B2B em que a pessoa é contribuinte do ICMS, a classe
 * também implementa InscricaoEstadualLookup: o método inscricoesEstaduaisPorCnpj
 * consulta o pacote H (ID 16) e devolve as Inscrições Estaduais do CNPJ. O
 * casamento da IE com a UF do endereço fica a cargo do PessoaResolver, e o
 * recurso é totalmente opcional.
 *
 * @category  Library
 * @package   NFePHP\CTe\Lookup
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @link      https://github.com/nfephp-org/sped-cte
 */
class CpfCnpjComBrLookup implements PessoaLookup, InscricaoEstadualLookup
{
    /**
     * Token de integração (primeiro segmento da URL).
     *
     * @var string
     */
    private $token;

    /**
     * Cliente HTTP usado para executar a consulta.
     *
     * @var HttpGet
     */
    private $http;

    /**
     * URL base da API, sem barra final.
     *
     * @var string
     */
    private $baseUrl;

    /**
     * IDs de pacote por tipo de documento.
     *
     * @var array<string,int>
     */
    private $pacotes = [
        'cpf' => 3,
        'cnpj' => 5,
        'ie' => 16,
    ];

    /**
     * @param string            $token   token de integração do Painel de Controle
     * @param array<string,int> $pacotes sobrescreve os pacotes padrão: ['cpf' => 3, 'cnpj' => 5, 'ie' => 16]
     * @param HttpGet|null      $http    cliente HTTP; usa CurlHttpGet quando ausente
     * @param string            $baseUrl URL base da API
     *
     * @throws InvalidArgumentException quando o token está vazio
     */
    public function __construct(
        $token,
        array $pacotes = [],
        ?HttpGet $http = null,
        $baseUrl = 'https://api.cpfcnpj.com.br'
    ) {
        $token = trim((string) $token);
        if ($token === '') {
            throw new InvalidArgumentException(
                'É obrigatório informar o token de integração da CpfCnpj.com.br.'
            );
        }
        $this->token = $token;
        $this->http = $http !== null ? $http : new CurlHttpGet();
        $this->baseUrl = rtrim((string) $baseUrl, '/');
        if (!empty($pacotes['cpf'])) {
            $this->pacotes['cpf'] = (int) $pacotes['cpf'];
        }
        if (!empty($pacotes['cnpj'])) {
            $this->pacotes['cnpj'] = (int) $pacotes['cnpj'];
        }
        if (!empty($pacotes['ie'])) {
            $this->pacotes['ie'] = (int) $pacotes['ie'];
        }
    }

    /**
     * {@inheritDoc}
     */
    public function porCpf($cpf)
    {
        $documento = $this->normaliza($cpf);
        if (strlen($documento) !== 11) {
            throw new InvalidArgumentException('Informe um CPF com 11 dígitos.');
        }
        $resposta = $this->consulta($this->pacotes['cpf'], $documento);
        return $this->montaPessoaFisica($resposta, $documento);
    }

    /**
     * {@inheritDoc}
     */
    public function porCnpj($cnpj)
    {
        $documento = $this->normaliza($cnpj);
        if (strlen($documento) !== 14) {
            throw new InvalidArgumentException('Informe um CNPJ com 14 caracteres.');
        }
        $resposta = $this->consulta($this->pacotes['cnpj'], $documento);
        return $this->montaPessoaJuridica($resposta, $documento);
    }

    /**
     * {@inheritDoc}
     */
    public function porDocumento($documento)
    {
        $limpo = $this->normaliza($documento);
        if (strlen($limpo) === 11) {
            return $this->porCpf($limpo);
        }
        if (strlen($limpo) === 14) {
            return $this->porCnpj($limpo);
        }
        throw new InvalidArgumentException(
            'O documento deve ter 11 dígitos (CPF) ou 14 caracteres (CNPJ).'
        );
    }

    /**
     * {@inheritDoc}
     */
    public function inscricoesEstaduaisPorCnpj($cnpj)
    {
        $documento = $this->normaliza($cnpj);
        if (strlen($documento) !== 14) {
            throw new InvalidArgumentException('Informe um CNPJ com 14 caracteres.');
        }
        $resposta = $this->consulta($this->pacotes['ie'], $documento);
        return $this->montaInscricoes($resposta);
    }

    /**
     * Converte o bloco inscricoesEstaduais da resposta em uma lista normalizada.
     *
     * @param \stdClass $resposta
     * @return array<int,\stdClass> lista de {inscricao, ativo, uf}
     */
    private function montaInscricoes($resposta)
    {
        $lista = [];
        if (!isset($resposta->inscricoesEstaduais) || !is_array($resposta->inscricoesEstaduais)) {
            return $lista;
        }
        foreach ($resposta->inscricoesEstaduais as $item) {
            if (!$item instanceof \stdClass) {
                continue;
            }
            $entrada = new \stdClass();
            $entrada->inscricao = $this->texto($item, 'inscricao_estadual');
            $entrada->ativo = isset($item->ativo)
                && filter_var($item->ativo, FILTER_VALIDATE_BOOLEAN);
            $uf = '';
            if (isset($item->estado) && $item->estado instanceof \stdClass) {
                $uf = strtoupper($this->texto($item->estado, 'sigla'));
            }
            $entrada->uf = $uf;
            $lista[] = $entrada;
        }
        return $lista;
    }

    /**
     * Normaliza o documento removendo máscara e caracteres inválidos.
     *
     * Aceita o CNPJ alfanumérico (IN RFB nº 2.229/2024): mantém letras e
     * dígitos e converte para maiúsculas; para o CPF sobram apenas dígitos.
     *
     * @param string $documento
     * @return string
     */
    private function normaliza($documento)
    {
        $limpo = preg_replace('/[^0-9A-Za-z]/', '', (string) $documento);
        return strtoupper($limpo);
    }

    /**
     * Executa a consulta e devolve o objeto decodificado da resposta.
     *
     * @param int    $pacote
     * @param string $documento já normalizado
     * @return \stdClass
     *
     * @throws RuntimeException quando a resposta é inválida ou traz status 0
     */
    private function consulta($pacote, $documento)
    {
        $url = $this->baseUrl . '/' . rawurlencode($this->token)
            . '/' . (int) $pacote . '/' . rawurlencode($documento);
        $corpo = $this->http->get($url);
        $dados = json_decode($corpo);
        if (!$dados instanceof \stdClass) {
            throw new RuntimeException('A resposta da consulta não é um JSON válido.');
        }
        if (!isset($dados->status) || (int) $dados->status !== 1) {
            $mensagem = (isset($dados->erro) && is_scalar($dados->erro))
                ? (string) $dados->erro
                : 'consulta sem sucesso';
            $codigo = (isset($dados->erroCodigo) && is_scalar($dados->erroCodigo))
                ? (string) $dados->erroCodigo
                : '0';
            throw new RuntimeException(
                'Falha na consulta CpfCnpj.com.br: ' . $mensagem . ' (código ' . $codigo . ').'
            );
        }
        return $dados;
    }

    /**
     * Monta o grupo de pessoa e endereço para uma pessoa física.
     *
     * @param \stdClass $resposta
     * @param string    $documento CPF normalizado
     * @return \stdClass
     */
    private function montaPessoaFisica($resposta, $documento)
    {
        $pessoa = $this->grupoVazio();
        $pessoa->CPF = $documento;
        $pessoa->xNome = $this->texto($resposta, 'nome');
        $pessoa->xLgr = $this->texto($resposta, 'endereco');
        $pessoa->nro = $this->texto($resposta, 'numero');
        $pessoa->xCpl = $this->texto($resposta, 'complemento');
        $pessoa->xBairro = $this->texto($resposta, 'bairro');
        $pessoa->cMun = $this->texto($resposta, 'ibge');
        $pessoa->xMun = $this->texto($resposta, 'cidade');
        $pessoa->CEP = $this->digitos($this->texto($resposta, 'cep'));
        $pessoa->UF = strtoupper($this->texto($resposta, 'uf'));
        return $pessoa;
    }

    /**
     * Monta o grupo de pessoa e endereço para uma pessoa jurídica.
     *
     * @param \stdClass $resposta
     * @param string    $documento CNPJ normalizado
     * @return \stdClass
     */
    private function montaPessoaJuridica($resposta, $documento)
    {
        $pessoa = $this->grupoVazio();
        $pessoa->CNPJ = $documento;
        $pessoa->xNome = $this->texto($resposta, 'razao');
        $pessoa->xFant = $this->texto($resposta, 'fantasia');
        $endereco = isset($resposta->matrizEndereco) ? $resposta->matrizEndereco : null;
        if ($endereco instanceof \stdClass) {
            $pessoa->xLgr = $this->texto($endereco, 'logradouro');
            $pessoa->nro = $this->texto($endereco, 'numero');
            $pessoa->xCpl = $this->texto($endereco, 'complemento');
            $pessoa->xBairro = $this->texto($endereco, 'bairro');
            $pessoa->xMun = $this->texto($endereco, 'cidade');
            $pessoa->CEP = $this->digitos($this->texto($endereco, 'cep'));
            $pessoa->UF = strtoupper($this->texto($endereco, 'uf'));
        }
        $pessoa->cMun = $this->codigoIbge($resposta);
        return $pessoa;
    }

    /**
     * Extrai o código IBGE do município (7 dígitos) do bloco ibge do CNPJ.
     *
     * @param \stdClass $resposta
     * @return string
     */
    private function codigoIbge($resposta)
    {
        if (isset($resposta->ibge->cidade->ibge_id)) {
            return (string) $resposta->ibge->cidade->ibge_id;
        }
        return '';
    }

    /**
     * Devolve o grupo com todas as propriedades esperadas em branco.
     *
     * A propriedade IE é deliberadamente omitida: deve ser suprida pelo
     * integrador quando a pessoa for contribuinte.
     *
     * @return \stdClass
     */
    private function grupoVazio()
    {
        $pessoa = new \stdClass();
        $pessoa->xNome = '';
        $pessoa->xFant = '';
        $pessoa->CNPJ = '';
        $pessoa->CPF = '';
        $pessoa->xLgr = '';
        $pessoa->nro = '';
        $pessoa->xCpl = '';
        $pessoa->xBairro = '';
        $pessoa->cMun = '';
        $pessoa->xMun = '';
        $pessoa->CEP = '';
        $pessoa->UF = '';
        return $pessoa;
    }

    /**
     * Lê uma propriedade de texto do objeto, devolvendo string limpa.
     *
     * @param \stdClass $objeto
     * @param string    $campo
     * @return string
     */
    private function texto($objeto, $campo)
    {
        if (isset($objeto->$campo) && is_scalar($objeto->$campo)) {
            return trim((string) $objeto->$campo);
        }
        return '';
    }

    /**
     * Mantém apenas os dígitos de um valor (usado no CEP).
     *
     * @param string $valor
     * @return string
     */
    private function digitos($valor)
    {
        return preg_replace('/\D/', '', (string) $valor);
    }
}
