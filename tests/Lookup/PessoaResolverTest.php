<?php

namespace NFePHP\CTe\Tests\Lookup;

use NFePHP\CTe\Lookup\CpfCnpjComBrLookup;
use NFePHP\CTe\Lookup\PessoaResolver;
use PHPUnit\Framework\TestCase;

/**
 * Testes da fachada PessoaResolver e de seus atalhos por papel.
 */
class PessoaResolverTest extends TestCase
{
    /**
     * @return PessoaResolver
     */
    private function resolverCpf()
    {
        $corpo = json_encode([
            'status' => 1,
            'cpf' => '111.444.777-35',
            'nome' => 'Test Token',
            'endereco' => 'Rua A',
            'numero' => '100 B',
            'complemento' => 'Apto 03',
            'bairro' => 'Centro',
            'cep' => '99999-123',
            'cidade' => 'Sao Paulo',
            'uf' => 'SP',
            'ibge' => '1234567',
        ]);
        $lookup = new CpfCnpjComBrLookup('token123', [], new FakeHttpGet($corpo));
        return new PessoaResolver($lookup);
    }

    public function testRemetenteDelegaParaOLookup(): void
    {
        $pessoa = $this->resolverCpf()->remetente('111.444.777-35');
        $this->assertSame('Test Token', $pessoa->xNome);
        $this->assertSame('11144477735', $pessoa->CPF);
    }

    public function testAtalhosPorPapelDevolvemOMesmoFormato(): void
    {
        $resolver = $this->resolverCpf();
        $papeis = [
            $resolver->remetente('11144477735'),
            $resolver->destinatario('11144477735'),
            $resolver->tomador('11144477735'),
            $resolver->expedidor('11144477735'),
            $resolver->recebedor('11144477735'),
        ];
        foreach ($papeis as $pessoa) {
            $this->assertSame('Test Token', $pessoa->xNome);
            $this->assertSame('Rua A', $pessoa->xLgr);
            $this->assertSame('1234567', $pessoa->cMun);
        }
    }

    public function testPorDocumentoNaFachada(): void
    {
        $pessoa = $this->resolverCpf()->porDocumento('11144477735');
        $this->assertSame('Test Token', $pessoa->xNome);
    }

    /**
     * Corpo do pacote 5 (dados da pessoa jurídica) com UF configurável.
     *
     * @param string $uf
     * @return string
     */
    private function corpoCnpj($uf)
    {
        return json_encode([
            'status' => 1,
            'cnpj' => '11.222.333/0001-81',
            'razao' => 'Empresa Exemplo LTDA',
            'fantasia' => 'Exemplo',
            'matrizEndereco' => [
                'cep' => '74000-000',
                'logradouro' => 'Avenida Central',
                'numero' => '10',
                'bairro' => 'Centro',
                'cidade' => 'Cidade',
                'uf' => $uf,
            ],
            'ibge' => [
                'estado' => ['sigla' => $uf, 'ibge_id' => '52'],
                'cidade' => ['nome' => 'Cidade', 'ibge_id' => '5208707'],
            ],
        ]);
    }

    /**
     * Corpo do pacote 16 com uma lista arbitrária de inscrições estaduais.
     *
     * @param array<int,array<string,mixed>> $inscricoes
     * @return string
     */
    private function corpoInscricoes(array $inscricoes)
    {
        return json_encode([
            'status' => 1,
            'cnpj' => '11.222.333/0001-81',
            'razao' => 'Empresa Exemplo LTDA',
            'inscricoesEstaduais' => $inscricoes,
        ]);
    }

    /**
     * @param string $uf
     * @param array<int,array<string,mixed>> $inscricoes
     * @param bool $preencherIe
     * @return PessoaResolver
     */
    private function resolverCnpj($uf, array $inscricoes, $preencherIe = true)
    {
        $http = new MapaHttpGet([
            5 => $this->corpoCnpj($uf),
            16 => $this->corpoInscricoes($inscricoes),
        ]);
        $lookup = new CpfCnpjComBrLookup('token123', [], $http);
        return new PessoaResolver($lookup, $preencherIe);
    }

    public function testPreencheIeAtivaDaUf(): void
    {
        $resolver = $this->resolverCnpj('GO', [
            ['inscricao_estadual' => '10.987.654-3', 'ativo' => true, 'estado' => ['sigla' => 'GO']],
        ]);
        $pessoa = $resolver->remetente('11.222.333/0001-81');

        $this->assertTrue(property_exists($pessoa, 'IE'));
        $this->assertSame('109876543', $pessoa->IE);
    }

    public function testEscolheIeDaUfCorretaEntreMultiplas(): void
    {
        $resolver = $this->resolverCnpj('SP', [
            ['inscricao_estadual' => '111111111', 'ativo' => true, 'estado' => ['sigla' => 'GO']],
            ['inscricao_estadual' => '222222222', 'ativo' => true, 'estado' => ['sigla' => 'SP']],
        ]);
        $pessoa = $resolver->destinatario('11.222.333/0001-81');

        $this->assertSame('222222222', $pessoa->IE);
    }

    public function testPrefereIeAtivaNaMesmaUf(): void
    {
        $resolver = $this->resolverCnpj('GO', [
            ['inscricao_estadual' => '111111111', 'ativo' => false, 'estado' => ['sigla' => 'GO']],
            ['inscricao_estadual' => '999999999', 'ativo' => true, 'estado' => ['sigla' => 'GO']],
        ]);
        $pessoa = $resolver->tomador('11.222.333/0001-81');

        $this->assertSame('999999999', $pessoa->IE);
    }

    public function testIeInativaEhIgnorada(): void
    {
        $resolver = $this->resolverCnpj('GO', [
            ['inscricao_estadual' => '111111111', 'ativo' => false, 'estado' => ['sigla' => 'GO']],
        ]);
        $pessoa = $resolver->remetente('11.222.333/0001-81');

        $this->assertFalse(property_exists($pessoa, 'IE'));
    }

    public function testSemIeParaUfNaoPreenche(): void
    {
        $resolver = $this->resolverCnpj('MG', [
            ['inscricao_estadual' => '111111111', 'ativo' => true, 'estado' => ['sigla' => 'GO']],
            ['inscricao_estadual' => '222222222', 'ativo' => true, 'estado' => ['sigla' => 'SP']],
        ]);
        $pessoa = $resolver->remetente('11.222.333/0001-81');

        $this->assertFalse(property_exists($pessoa, 'IE'));
    }

    public function testSemFlagNaoConsultaIe(): void
    {
        $http = new MapaHttpGet([
            5 => $this->corpoCnpj('GO'),
            16 => $this->corpoInscricoes([
                ['inscricao_estadual' => '109876543', 'ativo' => true, 'estado' => ['sigla' => 'GO']],
            ]),
        ]);
        $resolver = new PessoaResolver(new CpfCnpjComBrLookup('token123', [], $http));
        $pessoa = $resolver->remetente('11.222.333/0001-81');

        $this->assertFalse(property_exists($pessoa, 'IE'));
        $this->assertCount(1, $http->urls);
    }

    public function testPrimeiraIeAtivaVenceEntreDuasNaMesmaUf(): void
    {
        $resolver = $this->resolverCnpj('GO', [
            ['inscricao_estadual' => '100000001', 'ativo' => true, 'estado' => ['sigla' => 'GO']],
            ['inscricao_estadual' => '200000002', 'ativo' => true, 'estado' => ['sigla' => 'GO']],
        ]);
        $pessoa = $resolver->remetente('11.222.333/0001-81');

        $this->assertSame('100000001', $pessoa->IE);
    }

    public function testErroNoPacote16NaoQuebraResolucaoDaPessoa(): void
    {
        $http = new MapaHttpGet([
            5 => $this->corpoCnpj('GO'),
            16 => json_encode(['status' => 0, 'erro' => 'indisponivel', 'erroCodigo' => 500]),
        ]);
        $resolver = new PessoaResolver(new CpfCnpjComBrLookup('token123', [], $http), true);
        $pessoa = $resolver->remetente('11.222.333/0001-81');

        $this->assertSame('Empresa Exemplo LTDA', $pessoa->xNome);
        $this->assertSame('GO', $pessoa->UF);
        $this->assertFalse(property_exists($pessoa, 'IE'));
    }

    public function testIeNaoSeAplicaAPessoaFisica(): void
    {
        $resolver = new PessoaResolver($this->resolverCpfLookup(), true);
        $pessoa = $resolver->remetente('11144477735');

        $this->assertFalse(property_exists($pessoa, 'IE'));
    }

    /**
     * @return CpfCnpjComBrLookup
     */
    private function resolverCpfLookup()
    {
        $corpo = json_encode([
            'status' => 1,
            'cpf' => '111.444.777-35',
            'nome' => 'Test Token',
            'endereco' => 'Rua A',
            'numero' => '100 B',
            'complemento' => 'Apto 03',
            'bairro' => 'Centro',
            'cep' => '99999-123',
            'cidade' => 'Sao Paulo',
            'uf' => 'SP',
            'ibge' => '1234567',
        ]);
        return new CpfCnpjComBrLookup('token123', [], new FakeHttpGet($corpo));
    }
}
