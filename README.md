# SPED-CTE v4.0 (em desenvolvimento)

[![Chat][ico-gitter]][link-gitter]

Framework para geração e comunicação das CTe com as SEFAZ autorizadoras.

*sped-cte é um framework para geração CTe e eventos na comunicação com as SEFAZ autorizadoras.*

**ATENÇÂO: Esta versão (v4.0) assim como a branch v3.0 correspondem a versão 3.00 do layout da SEFAZ. 
Esta versão 4.0 contempla o sped-common atual resolvendo os problemas com composer para quem utiliza o sped-nfe por exemplo**

[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
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
