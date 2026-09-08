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
}
