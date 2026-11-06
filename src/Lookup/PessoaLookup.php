<?php

namespace NFePHP\CTe\Lookup;

/**
 * Contrato de consulta de pessoas (remetente, destinatário, tomador, expedidor
 * e recebedor) para preenchimento dos grupos de pessoa e endereço do CT-e.
 *
 * O contrato é propositalmente leve e independente de rede: uma implementação
 * pode consultar uma API, um cadastro local, um banco de dados ou um cache.
 * Assim o núcleo da biblioteca continua offline por design e o recurso de
 * consulta permanece totalmente opcional e aditivo.
 *
 * Cada método devolve um \stdClass com propriedades públicas homônimas dos
 * campos do layout do CT-e, pronto para ser passado tanto ao grupo de pessoa
 * (tagrem, tagdest, tagtoma4, tagexped, tagreceb) quanto ao grupo de endereço
 * (tagenderReme, tagenderDest, tagenderToma, tagenderExped, tagenderReceb),
 * já que cada método tag* consome apenas as propriedades que reconhece.
 *
 * Propriedades preenchidas: xNome, xFant, CNPJ, CPF, xLgr, nro, xCpl, xBairro,
 * cMun, xMun, CEP, UF.
 *
 * Os métodos deste contrato nunca preenchem a Inscrição Estadual (IE). O
 * preenchimento da IE é um recurso separado e opcional, exposto pelo contrato
 * InscricaoEstadualLookup e aplicado pelo PessoaResolver no cenário B2B; uma
 * fonte que não implemente aquele contrato deixa a IE inteiramente a cargo do
 * integrador quando a pessoa for contribuinte.
 *
 * @category  Library
 * @package   NFePHP\CTe\Lookup
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @link      https://github.com/nfephp-org/sped-cte
 */
interface PessoaLookup
{
    /**
     * Consulta uma pessoa física pelo CPF.
     *
     * @param string $cpf CPF com ou sem máscara
     * @return \stdClass grupo de pessoa e endereço pronto para os métodos tag*
     */
    public function porCpf($cpf);

    /**
     * Consulta uma pessoa jurídica pelo CNPJ.
     *
     * @param string $cnpj CNPJ com ou sem máscara (numérico ou alfanumérico)
     * @return \stdClass grupo de pessoa e endereço pronto para os métodos tag*
     */
    public function porCnpj($cnpj);

    /**
     * Consulta uma pessoa pelo documento, detectando CPF ou CNPJ pelo tamanho.
     *
     * @param string $documento CPF (11) ou CNPJ (14) com ou sem máscara
     * @return \stdClass grupo de pessoa e endereço pronto para os métodos tag*
     */
    public function porDocumento($documento);
}
