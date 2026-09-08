<?php

namespace NFePHP\CTe\Tests\Lookup;

use NFePHP\CTe\Exception\InvalidArgumentException;
use NFePHP\CTe\Exception\RuntimeException;
use NFePHP\CTe\Lookup\CpfCnpjComBrLookup;
use PHPUnit\Framework\TestCase;

/**
 * Testes do resolvedor de pessoas baseado na API da CpfCnpj.com.br.
 *
 * Todo acesso HTTP é simulado por FakeHttpGet, de modo que a suíte é executada
 * sem qualquer acesso à rede.
 */
class CpfCnpjComBrLookupTest extends TestCase
{
    /**
     * @return string
     */
    private function respostaCpf()
    {
        return json_encode([
            'status' => 1,
            'cpf' => '111.444.777-35',
            'nome' => 'Test Token',
            'endereco' => 'Rua A',
            'numero' => '100 B',
            'complemento' => 'Apto 03',
            'bairro' => 'Centro',
            'cep' => '99999-123',
            'cidade' => 'Sao Paulo',
            'uf' => 'sp',
            'ibge' => '1234567',
        ]);
    }

    /**
     * @return string
     */
    private function respostaCnpj()
    {
        return json_encode([
            'status' => 1,
            'cnpj' => '11.222.333/0001-81',
            'razao' => 'Empresa Exemplo LTDA',
            'fantasia' => 'Exemplo',
            'matrizEndereco' => [
                'cep' => '01001-000',
                'tipo' => 'Rua',
                'logradouro' => 'Praca da Se',
                'numero' => '1000',
                'complemento' => 'Sala 2',
                'bairro' => 'Se',
                'cidade' => 'Sao Paulo',
                'uf' => 'SP',
            ],
            'ibge' => [
                'estado' => ['sigla' => 'SP', 'ibge_id' => '35'],
                'cidade' => ['nome' => 'Sao Paulo', 'ibge_id' => '3550308'],
            ],
        ]);
    }

    public function testPorCpfMapeiaPessoaEEndereco(): void
    {
        $http = new FakeHttpGet($this->respostaCpf());
        $lookup = new CpfCnpjComBrLookup('token123', [], $http);
        $pessoa = $lookup->porCpf('111.444.777-35');

        $this->assertSame('Test Token', $pessoa->xNome);
        $this->assertSame('11144477735', $pessoa->CPF);
        $this->assertSame('', $pessoa->CNPJ);
        $this->assertSame('Rua A', $pessoa->xLgr);
        $this->assertSame('100 B', $pessoa->nro);
        $this->assertSame('Apto 03', $pessoa->xCpl);
        $this->assertSame('Centro', $pessoa->xBairro);
        $this->assertSame('1234567', $pessoa->cMun);
        $this->assertSame('Sao Paulo', $pessoa->xMun);
        $this->assertSame('99999123', $pessoa->CEP);
        $this->assertSame('SP', $pessoa->UF);
    }

    public function testPorCpfMontaUrlComPacotePadrao3(): void
    {
        $http = new FakeHttpGet($this->respostaCpf());
        $lookup = new CpfCnpjComBrLookup('token123', [], $http);
        $lookup->porCpf('111.444.777-35');

        $this->assertSame(
            'https://api.cpfcnpj.com.br/token123/3/11144477735',
            $http->ultimaUrl
        );
    }

    public function testPorCnpjMapeiaPessoaEEndereco(): void
    {
        $http = new FakeHttpGet($this->respostaCnpj());
        $lookup = new CpfCnpjComBrLookup('token123', [], $http);
        $pessoa = $lookup->porCnpj('11.222.333/0001-81');

        $this->assertSame('Empresa Exemplo LTDA', $pessoa->xNome);
        $this->assertSame('Exemplo', $pessoa->xFant);
        $this->assertSame('11222333000181', $pessoa->CNPJ);
        $this->assertSame('', $pessoa->CPF);
        $this->assertSame('Praca da Se', $pessoa->xLgr);
        $this->assertSame('1000', $pessoa->nro);
        $this->assertSame('Sala 2', $pessoa->xCpl);
        $this->assertSame('Se', $pessoa->xBairro);
        $this->assertSame('3550308', $pessoa->cMun);
        $this->assertSame('Sao Paulo', $pessoa->xMun);
        $this->assertSame('01001000', $pessoa->CEP);
        $this->assertSame('SP', $pessoa->UF);
    }

    public function testPorCnpjMontaUrlComPacotePadrao5(): void
    {
        $http = new FakeHttpGet($this->respostaCnpj());
        $lookup = new CpfCnpjComBrLookup('token123', [], $http);
        $lookup->porCnpj('11.222.333/0001-81');

        $this->assertSame(
            'https://api.cpfcnpj.com.br/token123/5/11222333000181',
            $http->ultimaUrl
        );
    }

    public function testPacotePodeSerSobrescrito(): void
    {
        $http = new FakeHttpGet($this->respostaCnpj());
        $lookup = new CpfCnpjComBrLookup('token123', ['cnpj' => 6], $http);
        $lookup->porCnpj('11.222.333/0001-81');

        $this->assertSame(
            'https://api.cpfcnpj.com.br/token123/6/11222333000181',
            $http->ultimaUrl
        );
    }

    public function testNuncaPreencheInscricaoEstadual(): void
    {
        $http = new FakeHttpGet($this->respostaCnpj());
        $lookup = new CpfCnpjComBrLookup('token123', [], $http);
        $pessoa = $lookup->porCnpj('11.222.333/0001-81');

        $this->assertFalse(property_exists($pessoa, 'IE'));
    }

    public function testPorDocumentoDetectaCpfPeloTamanho(): void
    {
        $http = new FakeHttpGet($this->respostaCpf());
        $lookup = new CpfCnpjComBrLookup('token123', [], $http);
        $lookup->porDocumento('11144477735');

        $this->assertSame(
            'https://api.cpfcnpj.com.br/token123/3/11144477735',
            $http->ultimaUrl
        );
    }

    public function testPorDocumentoDetectaCnpjPeloTamanho(): void
    {
        $http = new FakeHttpGet($this->respostaCnpj());
        $lookup = new CpfCnpjComBrLookup('token123', [], $http);
        $lookup->porDocumento('11222333000181');

        $this->assertSame(
            'https://api.cpfcnpj.com.br/token123/5/11222333000181',
            $http->ultimaUrl
        );
    }

    public function testTokenVazioLancaExcecao(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CpfCnpjComBrLookup('   ');
    }

    public function testDocumentoComTamanhoInvalidoLancaExcecao(): void
    {
        $http = new FakeHttpGet($this->respostaCpf());
        $lookup = new CpfCnpjComBrLookup('token123', [], $http);
        $this->expectException(InvalidArgumentException::class);
        $lookup->porDocumento('123');
    }

    public function testStatusZeroLancaRuntimeException(): void
    {
        $corpo = json_encode([
            'status' => 0,
            'erro' => 'CPF inválido!',
            'erroCodigo' => 100,
        ]);
        $http = new FakeHttpGet($corpo);
        $lookup = new CpfCnpjComBrLookup('token123', [], $http);
        $this->expectException(RuntimeException::class);
        $lookup->porCpf('11144477735');
    }

    public function testRespostaNaoJsonLancaRuntimeException(): void
    {
        $http = new FakeHttpGet('<html>indisponivel</html>');
        $lookup = new CpfCnpjComBrLookup('token123', [], $http);
        $this->expectException(RuntimeException::class);
        $lookup->porCpf('11144477735');
    }

    public function testStatusZeroComErroEstruturadoLancaRuntimeException(): void
    {
        $corpo = json_encode([
            'status' => 0,
            'erro' => ['campo' => 'documento', 'msg' => 'invalido'],
            'erroCodigo' => ['id' => 100],
        ]);
        $http = new FakeHttpGet($corpo);
        $lookup = new CpfCnpjComBrLookup('token123', [], $http);
        $this->expectException(RuntimeException::class);
        $lookup->porCpf('11144477735');
    }
}
