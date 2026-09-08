<?php

namespace NFePHP\CTe\Lookup;

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
 *     $make->tagenderReme($pessoa); // o IE deve ser suprido pelo integrador
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
     * @param PessoaLookup $lookup fonte de dados que será consultada
     */
    public function __construct(PessoaLookup $lookup)
    {
        $this->lookup = $lookup;
    }

    /**
     * Consulta por CPF.
     *
     * @param string $cpf
     * @return \stdClass
     */
    public function porCpf($cpf)
    {
        return $this->lookup->porCpf($cpf);
    }

    /**
     * Consulta por CNPJ.
     *
     * @param string $cnpj
     * @return \stdClass
     */
    public function porCnpj($cnpj)
    {
        return $this->lookup->porCnpj($cnpj);
    }

    /**
     * Consulta por documento, detectando CPF ou CNPJ pelo tamanho.
     *
     * @param string $documento
     * @return \stdClass
     */
    public function porDocumento($documento)
    {
        return $this->lookup->porDocumento($documento);
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
}
