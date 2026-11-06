<?php

namespace NFePHP\CTe\Lookup;

/**
 * Contrato opcional de consulta das Inscrições Estaduais de uma pessoa jurídica.
 *
 * É separado de PessoaLookup de propósito: preencher a IE é um recurso aditivo,
 * destinado ao cenário B2B em que remetente, destinatário, tomador, expedidor
 * ou recebedor é contribuinte do ICMS. Implementações de PessoaLookup que não
 * fornecem esse dado simplesmente não implementam este contrato, e o resolvedor
 * segue funcionando exatamente como antes.
 *
 * A implementação de referência (CpfCnpjComBrLookup) consulta o pacote H (ID 16)
 * da API da CpfCnpj.com.br, que devolve todas as Inscrições Estaduais do CNPJ,
 * cada uma com a UF e o indicador de atividade.
 *
 * @category  Library
 * @package   NFePHP\CTe\Lookup
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @link      https://github.com/nfephp-org/sped-cte
 */
interface InscricaoEstadualLookup
{
    /**
     * Consulta as Inscrições Estaduais de uma pessoa jurídica pelo CNPJ.
     *
     * Cada elemento devolvido é um \stdClass com as propriedades:
     *  - inscricao: string, o número da Inscrição Estadual;
     *  - ativo:     bool, se a inscrição está ativa;
     *  - uf:        string, a sigla da UF da inscrição, em maiúsculas.
     *
     * A lista pode vir vazia quando o CNPJ não possui Inscrição Estadual.
     *
     * @param string $cnpj CNPJ com ou sem máscara (numérico ou alfanumérico)
     * @return array<int,\stdClass> lista de inscrições estaduais
     * @throws \NFePHP\CTe\Exception\InvalidArgumentException CNPJ inválido
     * @throws \NFePHP\CTe\Exception\RuntimeException falha na consulta
     */
    public function inscricoesEstaduaisPorCnpj($cnpj);
}
