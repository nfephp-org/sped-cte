<?php

namespace NFePHP\CTe\Tests\Support;

use NFePHP\Common\Certificate;
use NFePHP\Common\Certificate\CertificationChain;
use NFePHP\Common\Certificate\PrivateKey;
use NFePHP\Common\Certificate\PublicKey;
use NFePHP\Common\Signer;
use NFePHP\CTe\MakeCTe;
use NFePHP\CTe\MakeCTeOS;
use NFePHP\CTe\MakeCTeSimp;
use stdClass;

/**
 * Monta CT-e, CT-e OS e CT-e Simplificado minimos pelos Make*, assina com um certificado
 * autoassinado gerado na hora e valida contra os XSD da pasta schemes/.
 * Usado pelos testes da NT CT-e 2026.004 (sem certificado real nem acesso a SEFAZ).
 */
class Fixtures
{
    const CNPJ = '11222333000181';

    /**
     * @var Certificate|null
     */
    private static $certificate;

    /**
     * Certificado autoassinado (RSA 2048) gerado uma vez por processo
     * @return Certificate
     */
    public static function certificate()
    {
        if (self::$certificate === null) {
            $config = ['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA];
            $key = openssl_pkey_new($config);
            $csr = openssl_csr_new(['commonName' => 'EMPRESA TESTE:' . self::CNPJ], $key, $config);
            $x509 = openssl_csr_sign($csr, null, $key, 1, $config);
            openssl_x509_export($x509, $certPem);
            openssl_pkey_export($key, $keyPem);
            self::$certificate = new Certificate(
                new PrivateKey($keyPem),
                new PublicKey($certPem),
                new CertificationChain('')
            );
        }
        return self::$certificate;
    }

    /**
     * Caminho do XSD de um PL da pasta schemes/
     * @param string $schema
     * @param string $file
     * @return string
     */
    public static function xsd($schema, $file)
    {
        return dirname(__DIR__, 2) . "/schemes/$schema/$file";
    }

    /**
     * Assina o infCte
     * @param string $xml
     * @return string
     */
    public static function sign($xml)
    {
        return Signer::sign(
            self::certificate(),
            $xml,
            'infCte',
            'Id',
            OPENSSL_ALGO_SHA1,
            [true, false, null, null]
        );
    }

    /**
     * Valida o XML contra o XSD e devolve a lista de erros (vazia = valido)
     * @param string $xml
     * @param string $xsd
     * @return array
     */
    public static function validate($xml, $xsd)
    {
        $previous = libxml_use_internal_errors(true);
        libxml_clear_errors();
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($xml);
        $dom->schemaValidate($xsd);
        $errors = [];
        foreach (libxml_get_errors() as $error) {
            $errors[] = trim($error->message);
        }
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        return $errors;
    }

    /**
     * Converte array em stdClass
     * @param array $data
     * @return stdClass
     */
    public static function std(array $data)
    {
        return json_decode(json_encode($data));
    }

    /**
     * Grupo ide padrao (mod 57, rodoviario, SP -> SP)
     * @param array $override
     * @param string $mod
     * @return stdClass
     */
    public static function ide(array $override = [], $mod = '57')
    {
        return self::std(array_merge([
            'cUF' => '35',
            'cCT' => '12345678',
            'CFOP' => '5353',
            'natOp' => 'PRESTACAO DE SERVICO DE TRANSPORTE',
            'mod' => $mod,
            'serie' => '1',
            'nCT' => '10',
            'dhEmi' => '2026-10-20T10:00:00-03:00',
            'tpImp' => '1',
            'tpEmis' => '1',
            'cDV' => '0',
            'tpAmb' => '2',
            'tpCTe' => '0',
            'procEmi' => '0',
            'verProc' => 'TESTE 1.0',
            'cMunEnv' => '3550308',
            'xMunEnv' => 'SAO PAULO',
            'UFEnv' => 'SP',
            'modal' => '01',
            'tpServ' => '0',
            'cMunIni' => '3550308',
            'xMunIni' => 'SAO PAULO',
            'UFIni' => 'SP',
            'cMunFim' => '3509502',
            'xMunFim' => 'CAMPINAS',
            'UFFim' => 'SP',
            'retira' => '1',
            'indIEToma' => '1',
        ], $override));
    }

    /**
     * @return stdClass
     */
    private static function emit()
    {
        return self::std([
            'CNPJ' => self::CNPJ,
            'IE' => '111222333444',
            'xNome' => 'TRANSPORTADORA TESTE LTDA',
            'CRT' => '3',
        ]);
    }

    /**
     * @return stdClass
     */
    private static function enderEmit()
    {
        return self::std([
            'xLgr' => 'RUA TESTE',
            'nro' => '100',
            'xBairro' => 'CENTRO',
            'cMun' => '3550308',
            'xMun' => 'SAO PAULO',
            'CEP' => '01001000',
            'UF' => 'SP',
        ]);
    }

    /**
     * Chama montaCTe() e, se o Make recusar, devolve os erros das tags na mensagem
     * @param MakeCTe|MakeCTeOS|MakeCTeSimp $make
     * @return void
     */
    private static function monta($make)
    {
        try {
            $make->montaCTe();
        } catch (\RuntimeException $e) {
            throw new \RuntimeException($e->getMessage() . ' ' . json_encode($make->getErrors()));
        }
    }

    /**
     * Monta um CT-e (mod 57) minimo e devolve o MakeCTe ja montado
     * Chaves de $opt: ide, vPrest, icms (arrays que sobrescrevem o padrao) e
     * pagAnt (lista de stdClass para taggPagAntecipado)
     * @param string $schema
     * @param array $opt
     * @return MakeCTe
     */
    public static function makeCTe($schema = 'PL_CTe_400_RTC', array $opt = [])
    {
        $opt = array_merge(['ide' => [], 'vPrest' => [], 'icms' => [], 'pagAnt' => []], $opt);
        $make = new MakeCTe($schema);
        $make->taginfCTe(self::std(['Id' => '', 'versao' => '4.00']));
        $make->tagide(self::ide($opt['ide']));
        $make->tagtoma3(self::std(['toma' => '0']));
        foreach ($opt['pagAnt'] as $pagAnt) {
            $make->taggPagAntecipado($pagAnt);
        }
        $make->tagemit(self::emit());
        $make->tagenderEmit(self::enderEmit());
        $make->tagvPrest(self::std(array_merge(['vTPrest' => 100, 'vRec' => 100], $opt['vPrest'])));
        $make->tagicms(self::std(array_merge(
            ['cst' => '00', 'vBC' => 100, 'pICMS' => 12, 'vICMS' => 12],
            $opt['icms']
        )));
        $make->taginfCTeNorm();
        $make->taginfCarga(self::std(['vCarga' => 1000, 'proPred' => 'CAIXAS']));
        $make->taginfQ(self::std(['cUnid' => '01', 'tpMed' => 'PESO BRUTO', 'qCarga' => 10]));
        $make->taginfNFe(self::std(['chave' => '35261011222333000181550010000000101123456789']));
        $make->taginfModal(self::std(['versaoModal' => '4.00']));
        $make->tagrodo(self::std(['RNTRC' => '12345678']));
        self::monta($make);
        return $make;
    }

    /**
     * Monta um CT-e OS (mod 67) minimo e devolve o MakeCTeOS ja montado
     * Chaves de $opt: ide, vPrest, icms e pagAnt (como em makeCTe)
     * @param string $schema
     * @param array $opt
     * @return MakeCTeOS
     */
    public static function makeCTeOS($schema = 'PL_CTe_400_RTC', array $opt = [])
    {
        $opt = array_merge(['ide' => [], 'vPrest' => [], 'icms' => [], 'pagAnt' => []], $opt);
        $make = new MakeCTeOS($schema);
        $make->taginfCTe(self::std(['Id' => '', 'versao' => '4.00']));
        $ide = (array) self::ide(array_merge([
            'CFOP' => '5357',
            'tpServ' => '6',
        ], $opt['ide']), '67');
        unset($ide['retira']);
        $make->tagide(self::std($ide));
        foreach ($opt['pagAnt'] as $pagAnt) {
            $make->taggPagAntecipado($pagAnt);
        }
        $make->tagemit(self::emit());
        $make->tagenderEmit(self::enderEmit());
        $make->tagvPrest(self::std(array_merge(['vTPrest' => 100, 'vRec' => 100], $opt['vPrest'])));
        $make->tagicms(self::std(array_merge(
            ['cst' => '00', 'vBC' => 100, 'pICMS' => 12, 'vICMS' => 12],
            $opt['icms']
        )));
        $make->taginfCTeNorm();
        $make->taginfServico(self::std(['xDescServ' => 'TRANSPORTE DE PESSOAS', 'qCarga' => '1.0000']));
        $make->taginfModal(self::std(['versaoModal' => '4.00']));
        $make->tagrodo(self::std(['TAF' => '123456789012']));
        self::monta($make);
        return $make;
    }

    /**
     * Monta um CT-e Simplificado (mod 57, tpCTe 5) minimo e devolve o MakeCTeSimp ja montado
     * Chaves de $opt: ide, det e total (arrays que sobrescrevem o padrao)
     * @param string $schema
     * @param array $opt
     * @return MakeCTeSimp
     */
    public static function makeCTeSimp($schema = 'PL_CTe_400_RTC', array $opt = [])
    {
        $opt = array_merge(['ide' => [], 'det' => [], 'total' => []], $opt);
        $make = new MakeCTeSimp($schema);
        $make->taginfCTe(self::std(['Id' => '', 'versao' => '4.00']));
        $ide = (array) self::ide(array_merge(['tpCTe' => '5'], $opt['ide']));
        $make->tagide(self::std($ide));
        $make->tagemit(self::emit());
        $make->tagenderEmit(self::enderEmit());
        $make->tagtoma(self::std([
            'toma' => '0',
            'indIEToma' => '1',
            'CNPJ' => '44555666000171',
            'IE' => '555666777888',
            'xNome' => 'TOMADOR TESTE LTDA',
        ]));
        $make->tagenderToma(self::enderEmit());
        $make->taginfCarga(self::std(['vCarga' => 1000, 'proPred' => 'CAIXAS']));
        $make->taginfQ(self::std(['cUnid' => '01', 'tpMed' => '01', 'qCarga' => 10]));
        $make->tagdet(self::std(array_merge([
            'nItem' => '1',
            'cMunIni' => '3550308',
            'xMunIni' => 'SAO PAULO',
            'cMunFim' => '3509502',
            'xMunFim' => 'CAMPINAS',
            'vPrest' => 100,
            'vRec' => 100,
        ], $opt['det'])));
        $make->taginfNFe(self::std(['chNFe' => '35261011222333000181550010000000101123456789']));
        $make->taginfModal(self::std(['versaoModal' => '4.00']));
        $make->tagrodo(self::std(['RNTRC' => '12345678']));
        $make->tagicms(self::std(['cst' => '00', 'vBC' => 100, 'pICMS' => 12, 'vICMS' => 12]));
        $make->tagtotal(self::std(array_merge(['vTPrest' => 100, 'vTRec' => 100, 'vTotDFe' => 100], $opt['total'])));
        self::monta($make);
        return $make;
    }
}
