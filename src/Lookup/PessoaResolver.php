<?php

namespace NFePHP\CTe\Lookup;

use NFePHP\CTe\Exception\RuntimeException;

/**
 * Fachada de conveniência sobre um PessoaLookup.
 *
 * Além de expor a consulta por CPF, CNPJ ou documento, oferece atalhos com o
 * nome de cada papel do CT-e (remetente, destinatário, tomador, expedidor e
 * recebedor). Todos devolvem o mesmo formato de grupo, já que os grupos de
 * pessoa do layout compartilham os mesmos campos; os atalhos existem apenas
 * para deixar o código de integração mais legível.
 *
 * O objeto devolvido é passado tanto ao método de pessoa quanto ao método de
 * endereço correspondente, por exemplo:
 *
 *     $pessoa = $resolver->remetente('11144477735');
 *     $make->tagrem($pessoa);
 *     $make->tagenderReme($pessoa);
 *
 * Preenchimento da Inscrição Estadual (opcional, cenário B2B): quando ligado e
 * a fonte de dados também implementa InscricaoEstadualLookup, o resolvedor
 * consulta as Inscrições Estaduais da pessoa jurídica e seleciona a IE cuja UF
 * coincide com a UF do endereço retornado, preferindo inscrições ativas. Sem a
 * opção ligada, o comportamento é idêntico ao anterior: a IE nunca é tocada.
 * A IE nunca é adivinhada; se não houver inscrição ativa para a UF, a
 * propriedade não é preenchida.
 *
 * Por ser um enriquecimento aditivo, uma falha na consulta da IE (indisponível,
 * CNPJ sem inscrição, limite de uso) nunca derruba a resolução da pessoa: a
 * consulta primária (dados e endereço) é preservada e a pessoa é devolvida sem
 * a IE. A consulta direta por InscricaoEstadualLookup, por sua vez, continua
 * propagando o erro para quem quiser tratá-lo.
 *
 * @category  Library
 * @package   NFePHP\CTe\Lookup
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @link      https://github.com/nfephp-org/sped-cte
 */
class PessoaResolver
{
    /**
     * @var PessoaLookup
     */
    private $lookup;

    /**
     * Se o resolvedor deve preencher a Inscrição Estadual das pessoas jurídicas.
     *
     * @var bool
     */
    private $preencherIe;

    /**
     * @param PessoaLookup $lookup      fonte de dados que será consultada
     * @param bool         $preencherIe liga o preenchimento opcional da IE (B2B)
     */
    public function __construct(PessoaLookup $lookup, $preencherIe = false)
    {
        $this->lookup = $lookup;
        $this->preencherIe = (bool) $preencherIe;
    }

    /**
     * Liga ou desliga o preenchimento da Inscrição Estadual (encadeável).
     *
     * @param bool $ativar
     * @return self
     */
    public function comInscricaoEstadual($ativar = true)
    {
        $this->preencherIe = (bool) $ativar;
        return $this;
    }

    /**
     * Consulta por CPF.
     *
     * @param string $cpf
     * @return \stdClass
     */
    public function porCpf($cpf)
    {
        return $this->talvezPreencheIe($this->lookup->porCpf($cpf));
    }

    /**
     * Consulta por CNPJ.
     *
     * @param string $cnpj
     * @return \stdClass
     */
    public function porCnpj($cnpj)
    {
        return $this->talvezPreencheIe($this->lookup->porCnpj($cnpj));
    }

    /**
     * Consulta por documento, detectando CPF ou CNPJ pelo tamanho.
     *
     * @param string $documento
     * @return \stdClass
     */
    public function porDocumento($documento)
    {
        return $this->talvezPreencheIe($this->lookup->porDocumento($documento));
    }

    /**
     * Atalho semântico para o remetente (grupo rem/enderReme).
     *
     * @param string $documento
     * @return \stdClass
     */
    public function remetente($documento)
    {
        return $this->porDocumento($documento);
    }

    /**
     * Atalho semântico para o destinatário (grupo dest/enderDest).
     *
     * @param string $documento
     * @return \stdClass
     */
    public function destinatario($documento)
    {
        return $this->porDocumento($documento);
    }

    /**
     * Atalho semântico para o tomador do serviço (grupo toma4/enderToma).
     *
     * @param string $documento
     * @return \stdClass
     */
    public function tomador($documento)
    {
        return $this->porDocumento($documento);
    }

    /**
     * Atalho semântico para o expedidor (grupo exped/enderExped).
     *
     * @param string $documento
     * @return \stdClass
     */
    public function expedidor($documento)
    {
        return $this->porDocumento($documento);
    }

    /**
     * Atalho semântico para o recebedor (grupo receb/enderReceb).
     *
     * @param string $documento
     * @return \stdClass
     */
    public function recebedor($documento)
    {
        return $this->porDocumento($documento);
    }

    /**
     * Preenche a Inscrição Estadual da pessoa quando o recurso está ligado.
     *
     * Só age sobre pessoa jurídica (com CNPJ e UF conhecidos) e apenas quando a
     * fonte de dados também implementa InscricaoEstadualLookup. Sem essas
     * condições devolve a pessoa intacta, garantindo o comportamento anterior.
     *
     * Por ser aditivo, uma falha na consulta da IE é degradada com elegância: a
     * pessoa já resolvida (dados e endereço) é devolvida sem a IE, em vez de
     * abortar toda a montagem por causa de um dado opcional.
     *
     * @param \stdClass $pessoa
     * @return \stdClass
     */
    private function talvezPreencheIe($pessoa)
    {
        if (!$this->preencherIe || !$this->lookup instanceof InscricaoEstadualLookup) {
            return $pessoa;
        }
        $cnpj = isset($pessoa->CNPJ) ? trim((string) $pessoa->CNPJ) : '';
        $uf = isset($pessoa->UF) ? strtoupper(trim((string) $pessoa->UF)) : '';
        if ($cnpj === '' || $uf === '') {
            return $pessoa;
        }
        try {
            $lista = $this->lookup->inscricoesEstaduaisPorCnpj($cnpj);
        } catch (RuntimeException $e) {
            return $pessoa;
        }
        $inscricao = $this->selecionaInscricao($lista, $uf);
        if ($inscricao !== '') {
            $pessoa->IE = $inscricao;
        }
        return $pessoa;
    }

    /**
     * Escolhe, entre a lista de inscrições, a IE ativa da UF informada.
     *
     * Inscrições de outras UFs e inscrições inativas são ignoradas; a IE nunca
     * é adivinhada. O esperado é haver no máximo uma inscrição ativa por UF; se
     * a fonte devolver mais de uma ativa para a mesma UF, a primeira da lista é
     * escolhida (ordem preservada da resposta da API).
     *
     * @param array<int,\stdClass> $lista lista de {inscricao, ativo, uf}
     * @param string               $uf    UF do endereço, em maiúsculas
     * @return string
     */
    private function selecionaInscricao(array $lista, $uf)
    {
        foreach ($lista as $item) {
            $itemUf = isset($item->uf) ? strtoupper(trim((string) $item->uf)) : '';
            if ($itemUf !== $uf || empty($item->ativo)) {
                continue;
            }
            $inscricao = isset($item->inscricao) ? (string) $item->inscricao : '';
            /* O campo TIe do layout do CT-e é numérico; mantém apenas dígitos. */
            $inscricao = preg_replace('/\D/', '', $inscricao);
            if ($inscricao !== '') {
                return $inscricao;
            }
        }
        return '';
    }
}
