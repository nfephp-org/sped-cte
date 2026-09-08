# SPED-CTE v3.00a

[![Chat][ico-gitter]][link-gitter]

Biblioteca para geração e comunicação das CTe com as SEFAZ autorizadoras.

## Nova versão 4.00 (a ser inclusa na biblioteca)

### Homologação a partir de 04/2023
### Produção a partir de 06/2023

>**Numa análise preliminar existem poucas alterações no modelo 57**

#### Mod. 57 - Campos NOVOS ou com alteração na versão 4.00
- CRT  (em emit)
- infCteComp 
- infPAA

#### Mod. 57 - Campos Removidos na versão 4.00
- refCteAnu
- tomaICMS
- infCteAnu

*sped-cte é um framework para geração CTe e eventos na comunicação com as SEFAZ autorizadoras.*

[![Build Status][ico-travis]][link-travis]
[![Latest Version on Packagist][ico-version]][link-packagist]
[![License][ico-license]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

[![Issues][ico-issues]][link-issues]
[![Forks][ico-forks]][link-forks]
[![Stars][ico-stars]][link-stars]


## Objetivo

Este pacote visa fornecer os meios para gerar, assinar e enviar os dados relativos ao projeto Sped CTe.

Este pacote faz parte da API NFePHP e atende aos parâmetros das PSR2 e PSR4, bem como é desenvolvida para de adequar as versões ATIVAS do PHP e aos layouts da CTe em vigor.

## Install

```sh
composer require nfephp-org/sped-cte:dev-master
```

## Consulta de pessoas (recurso opcional)

O namespace `NFePHP\CTe\Lookup` oferece um recurso **opcional e aditivo** para
preencher os grupos de pessoa e endereço do CT-e (remetente, destinatário,
tomador, expedidor e recebedor) a partir de um CPF ou CNPJ, consultando a API da
[CpfCnpj.com.br](https://www.cpfcnpj.com.br/dev/). O núcleo da biblioteca
continua **offline por design**: nada em `src` faz requisições de terceiros, e
todo o acesso de rede fica isolado atrás do contrato `HttpGet`. Quem não usa a
consulta não carrega nenhuma dependência nova.

O resolvedor devolve um `stdClass` com as propriedades homônimas dos campos do
layout, pronto para ser passado tanto ao método de pessoa quanto ao de endereço:

```php
use NFePHP\CTe\Lookup\CpfCnpjComBrLookup;
use NFePHP\CTe\Lookup\PessoaResolver;

$resolver = new PessoaResolver(new CpfCnpjComBrLookup('SEU_TOKEN'));

$rem = $resolver->remetente('11144477735');
$make->tagrem($rem);
$make->tagenderReme($rem);

$dest = $resolver->destinatario('11222333000181');
$make->tagdest($dest);
$make->tagenderDest($dest);
```

Cada `stdClass` traz `xNome`, `xFant` (quando PJ), `CNPJ` ou `CPF`, `xLgr`,
`nro`, `xCpl`, `xBairro`, `cMun` (código IBGE de 7 dígitos), `xMun`, `CEP` e
`UF`. Os métodos `tag*` consomem apenas as propriedades que reconhecem, por isso
o mesmo objeto serve para o grupo de pessoa e para o grupo de endereço.

### Token

O token é o primeiro segmento da URL de cada requisição (não é cabeçalho HTTP) e
é gerado no Painel de Controle, em **API > Tokens**. Para experimentar sem
consumir créditos, use o token público de testes
`5ae973d7a997af13f0aaf2bf60e65803`, que devolve respostas simuladas.

### Cobertura e pacotes

Os dados são atualizados em tempo real (D+0) com cobertura integral dos
documentos consultados. Por padrão a consulta usa o pacote 3 (CPF com nome e
endereço) e o pacote 5 (CNPJ com razão social, nome fantasia e endereço da
matriz). É possível apontar outro pacote de CNPJ, por exemplo o pacote 6, quando
o integrador quiser dados adicionais:

```php
$lookup = new CpfCnpjComBrLookup('SEU_TOKEN', ['cnpj' => 6]);
```

### Inscrição Estadual

O resolvedor **nunca** preenche a Inscrição Estadual: a consulta não fornece esse
dado. O encaixe é completo para pessoa física e para não contribuintes (onde a
IE não se aplica) e para preencher o endereço de pessoa jurídica, inclusive o
`cMun`. Para pessoa jurídica contribuinte, o integrador deve suprir a IE a partir
de sua própria fonte (cadastro interno, SINTEGRA ou SEFAZ) antes de gerar o XML.

### Cliente HTTP próprio

A implementação padrão usa cURL. Para injetar outro cliente (Guzzle, PSR-18 ou um
dublê de teste), basta implementar `NFePHP\CTe\Lookup\HttpGet` e passá-lo ao
construtor de `CpfCnpjComBrLookup`.

## Change log

Acompanhe o [CHANGELOG](CHANGELOG.md) para maiores informações sobre as alterações recentes.


## Contributing

Para contribuir por favor observe o [CONTRIBUTING](CONTRIBUTING.md) e o  [Código de Conduta](CONDUCT.md) parea detalhes.

## Versionamento

Para fins de transparência e discernimento sobre nosso ciclo de lançamento, e procurando manter compatibilidade com versões anteriores, o número de versão da NFePHP 
será mantida, tanto quanto possível, respeitando o padrão abaixo.

As liberações serão numeradas com o seguinte formato:

`<major>.<minor>.<patch>`

E serão construídas com as seguintes orientações:

* Quebra de compatibilidade com versões anteriores, avança o `<major>`.
* Adição de novas funcionalidades sem quebrar compatibilidade com versões anteriores, avança o `<minor>`.
* Correção de bugs e outras alterações, avança `<patch>`.

Para mais informações, por favor visite <http://semver.org/>.

## Desenvolvimento

Para todo o desenvolvimento, correções de bugs, inclusões e testes deverá ser usada branch `develop`. 
Na branch `master`estarão os códigos considerados como estáveis.
Novas branches poderão surgir em função das necessidades que se apresentarem, seja para manter versionamentos anteriores seja para estabelecer correções de bugs. Mas apenas essas duas branches estabelecidas é que serão permanentente mantidas. 

## Pull Request

Para que seu Pull Request seja aceito ele deve estar seguindo os padrões descritos neste documento <http://www.walkeralencar.com/PHPCodeStandards.pdf>


## Security

Caso você encontre algum problema relativo a segurança, por favor envie um email diretamente aos mantenedores do pacote ao invés de abrir um ISSUE.

## Credits

- Cleiton Perin (Owner)
- Roberto L. Machado (Owner)
- Samuel Basso (Mantenedor)
- Gleidson Brito (Colaborador)
- Giovani Paseto (Colaborador)
- Maison Kendi Sakamoto (Colaborador)
- Everton Xavier (Colaborador)

## License

Este pacote está diponibilizado sob GPLv3 ou LGPLv3 ou MIT License (MIT). Leia  [Arquivo de Licença](LICENSE.md) para maiores informações.


[ico-stars]: https://img.shields.io/github/stars/nfephp-org/sped-cte.svg?style=flat-square
[ico-forks]: https://img.shields.io/github/forks/nfephp-org/sped-cte.svg?style=flat-square
[ico-issues]: https://img.shields.io/github/issues/nfephp-org/sped-cte.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/nfephp-org/sped-cte/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/nfephp-org/sped-cte.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/nfephp-org/sped-cte.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/nfephp-org/sped-cte.svg?style=flat-square
[ico-version]: https://img.shields.io/packagist/v/nfephp-org/sped-cte.svg?style=flat-square
[ico-license]: https://poser.pugx.org/nfephp-org/nfephp/license.svg?style=flat-square
[ico-gitter]: https://img.shields.io/badge/GITTER-4%20users%20online-green.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/nfephp-org/sped-cte
[link-travis]: https://travis-ci.org/nfephp-org/sped-cte
[link-scrutinizer]: https://scrutinizer-ci.com/g/nfephp-org/sped-cte/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/nfephp-org/sped-cte
[link-downloads]: https://packagist.org/packages/nfephp-org/sped-cte
[link-author]: https://github.com/nfephp-org
[link-issues]: https://github.com/nfephp-org/sped-cte/issues
[link-forks]: https://github.com/nfephp-org/sped-cte/network
[link-stars]: https://github.com/nfephp-org/sped-cte/stargazers
[link-gitter]: https://gitter.im/nfephp-org/sped-cte?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge
