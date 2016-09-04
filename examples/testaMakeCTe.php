<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once '../bootstrap.php';

use NFePHP\CTe\Make;
use NFePHP\CTe\Tools;

$cte = new Make();
$cteTools = new Tools('../config/config.json');

$dhEmi = date("Y-m-d\TH:i:s");
//$dhEmi = date("d/m/Y");

$chave = $cte->montaChave(
    $cUF = '43',
    $ano = date('y',strtotime($dhEmi)),
    $mes = date('m',strtotime($dhEmi)),
    $cnpj = $cteTools->aConfig['cnpj'],
    $mod = '57',
    $serie = '1',
    $numero = '11',
    $tpEmis = '1',
    $cNF = '10');

$resp = $cte->infCteTag(
    $chave,
    $versao = '2.00');

$cDV = substr($chave, -1); //Digito Verificador

$resp = $cte->ideTag(
    $cUF = '43',
    $cCT = '00000010',
    $CFOP = '5351',
    $natOp = substr('Prestação de serviço de transporte para execução de serviço da mesma natureza', 0, 60),
    $forPag = '0',
    $mod = '57',
    $serie = '1',
    $nCT = '10',
    $dhEmi,
    $tpImp = '1',
    $tpEmis = '1',
    $cDV,
    $tpAmb = '2', //homologacao
    $tpCTe = '0',
    $procEmi = '0',
    $verProc = '2.0',
    $refCTE = '',
    $cMunEnv = '4308607',
    $xMunEnv = 'Garibaldi',
    $UFEnv = 'RS',
    $modal = '01',
    $tpServ = '0',
    $cMunIni = '4308607',
    $xMunIni = 'Garibaldi',
    $UFIni = 'RS',
    $cMunFim = '4308607',
    $xMunFim = 'Garibaldi',
    $UFFim = 'RS',
    $retira = '1',
    $xDetRetira = '',
    $dhCont = '',
    $xJust = ''
);

$resp = $cte->toma03Tag(
    $toma = '3'
);

$resp = $cte->toma4Tag(
    $toma = '4',
    $CNPJ = '11509962000197',
    $CPF = '',
    $IE = 'ISENTO',
    $xNome = 'OTIMIZY',
    $xFant = 'OTIMIZY',
    $fone = '5434625522',
    $email = 'contato@otimizy.com.br'
);


$resp = $cte->enderTomaTag(
    $xLgr = 'Avenida Independência',
    $nro = '482',
    $xCpl = '',
    $xBairro = 'Centro',
    $cMun = '4308607',
    $xMun = 'Garibaldi',
    $CEP = '95720000',
    $UF = $cteTools->aConfig['siglaUF'],
    $cPais = '1058',
    $xPais = 'Brasil'
);

$resp = $cte->emitTag(
    $CNPJ = $cteTools->aConfig['cnpj'],
    $IE = $cteTools->aConfig['ie'],
    $xNome = $cteTools->aConfig['razaosocial'],
    $xFant = $cteTools->aConfig['nomefantasia']
);


$resp = $cte->enderEmitTag(
    $xLgr = 'Avenida Independência',
    $nro = '482',
    $xCpl = 'Sala 109',
    $xBairro = 'Centro',
    $cMun = '4308607',
    $xMun = 'Garibaldi',
    $CEP = '95720000',
    $UF = $cteTools->aConfig['siglaUF'],
    $fone = '5434621111'
);

$resp = $cte->remTag(
    $CNPJ = '',
    $CPF = '00670965065',
    $IE = $cteTools->aConfig['ie'],
    $xNome = 'CT-E EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL',
    $Fant = 'Otimizy',
    $fone = '99887766',
    $email = 'contato@otimizy.com.br'
);


$resp = $cte->enderRemeTag(
    $xLgr = 'Avenida Independência',
    $nro = '482',
    $xCpl = 'Sala 109',
    $xBairro = 'Centro',
    $cMun = '4308607',
    $xMun = 'Garibaldi',
    $CEP = '95720000',
    $UF = $cteTools->aConfig['siglaUF'],
    $cPais = '1058',
    $xPais = 'Brasil'
);

$resp = $cte->destTag(
    $CNPJ = $cteTools->aConfig['cnpj'],
    $CPF = '',
    $IE = $cteTools->aConfig['ie'],
    $xNome = 'CT-E EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL',
    $fone = '5434627788',
    $ISUF = '',
    $email = 'contato@otimizy.com.br'
);


$resp = $cte->enderDestTag(
    $xLgr = 'Avenida Independência',
    $nro = '482',
    $xCpl = 'Sala 109',
    $xBairro = 'Centro',
    $cMun = '4308607',
    $xMun = 'Garibaldi',
    $CEP = '95720000',
    $UF = $cteTools->aConfig['siglaUF'],
    $cPais = '1058',
    $xPais = 'Brasil'
);

$resp = $cte->vPrestTag(
    $vTPrest = 200.00,
    $vRec = 200.00
);

$resp = $cte->icmsTag(
    $cst = '00',
    $pRedBC = '',
    $pRedBC = 200.00,
    $pRedBC = 17,
    $pRedBC = 34.00,
    $pRedBC = '',
    $pRedBC = ''
);

$resp = $cte->infCTeNormTag();

$resp = $cte->infCargaTag(
    $vCarga = 200.00,
    $prodPred = 'ESPETOS',
    $xOutCat = 'OUTRAS CARACTERÍSTICAS'
);

$resp = $cte->infQTag(
    $cUnid = '00',
    $tpMed = 'PESO BRUTO',
    $qCarga = 1
);

$resp = $cte->infDocTag();

$resp = $cte->infNFeTag(
    $pChave = '43160472202112000136550000000010571048440722',
    $PIN = '',
    $dPrev = '2016-06-30'
);

$resp = $cte->segTag(
    $respSeg = 4
);

$resp = $cte->infModalTag(
    $versaoModal = '2.00'
);

$resp = $cte->rodoTag(
    $RNTRC = '43160672',
    $dPrev = '2016-06-30',
    $lota = '0',
    $CIOT = ''
);

$resp = $cte->montaCTe();

$filename = "../xml/{$chave}-cte.xml";

if ($resp) {
    //header('Content-type: text/xml; charset=UTF-8');
    $xml = $cte->getXML();
    file_put_contents($filename, $xml);
    //chmod($filename, 0777);
    //echo $xml;
} else {
    header('Content-type: text/html; charset=UTF-8');
    foreach ($cte->erros as $err) {
        echo 'tag: &lt;'.$err['tag'].'&gt; ---- '.$err['desc'].'<br>';
    }
}


$xml = file_get_contents($filename);
$xml = $cteTools->assina($xml);
$filename = "../xml/{$chave}-cte.xml";
file_put_contents($filename, $xml);
//chmod($filename, 0777);
//echo $xml;

$aRetorno = array();
$tpAmb = '2';
$idLote = '';
$indSinc = '1';
$flagZip = false;

$retorno = $cteTools->sefazEnvia($xml, $tpAmb = '2', $idLote, $aRetorno, $indSinc, $flagZip);
echo '<pre>';
//echo htmlspecialchars($cteTools->soapDebug);
print_r($aRetorno);
echo "</pre>";
