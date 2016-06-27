<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

use NFePHP\CTe\Make;

$cte = new Make();



$cUF = '51';
$cCT = '00410070';
$CFOP = '5357';
$natOp = 'PRESTACAO DE SERVICO DE TRANSPORTE INTERMUNICIPAIS';
$mod = '57';
$serie = '1';
$nCT = '13';
$dhEmi = '2016-06-02T16:13:00';
$tpImp = '1';
$tpEmis = '1';
$cDV = '';
$tpAmb = '2';
$tpCTe = '0';
$procEmi = '0';
$verProc = '1.2.3';
$refCTE = '';
$cMunEnv = '5107909';
$xMunEnv = 'Sinop';
$UFEnv = 'MT';
$modal = '01';
$tpServ = '0';
$cMunIni = '5103205';
$xMunIni = 'Colider';
$UFIni = 'MT';
$cMunFim = '5103205';
$xMunFim = 'Colider';
$UFFim = 'MT';
$retira = '1';
$xDetRetira = '';
$dhCont = '';
$xJust = '';

$ano = '2016';
$mes = '06';
$cnpj = '06145774000197';
$numero = $nCT;
$codigo = $nCT;

$chave = $cte->montaChave($cUF, $ano, $mes, $cnpj, $mod, $serie, $numero, $tpEmis, $codigo);
$cDV = substr($chave, -1);

$dados = [
    $cUF,
    $cCT,
    $CFOP,
    $natOp,
    $mod,
    $serie,
    $nCT,
    $dhEmi,
    $tpImp,
    $tpEmis,
    $cDV,
    $tpAmb,
    $tpCTe,
    $procEmi,
    $verProc,
    $refCTE,
    $cMunEnv,
    $xMunEnv,
    $UFEnv,
    $modal,
    $tpServ,
    $cMunIni,
    $xMunIni,
    $UFIni,
    $cMunFim,
    $xMunFim,
    $UFFim,
    $retira,
    $xDetRetira,
    $dhCont,
    $xJust
];

$resp = $cte->ideTag(...$dados);

var_dump($resp);

$resp = $cte->infCteTag($chave, '2.00');

