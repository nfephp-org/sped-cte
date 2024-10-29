<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once '../bootstrap.php';

use NFePHP\Common\Certificate;
use NFePHP\CTe\Common\Standardize;
use NFePHP\CTe\MakeCTeSimp;
use NFePHP\CTe\Tools;

//tanto o config.json como o certificado.pfx podem estar
//armazenados em uma base de dados, então não é necessário
///trabalhar com arquivos, este script abaixo serve apenas como
//exemplo durante a fase de desenvolvimento e testes.
$arr = [
    "atualizacao" => "2024-10-25 17:00:00",
    "tpAmb" => 2,
    "razaosocial" => "SUA RAZAO SOCIAL LTDA",
    "cnpj" => "05405941000129",
    "cpf" => "",
    "siglaUF" => "MG",
    "schemes" => "PL_CTe_400",
    "versao" => '4.00',
    "proxyConf" => [
        "proxyIp" => "",
        "proxyPort" => "",
        "proxyUser" => "",
        "proxyPass" => ""
    ]
];
//monta o config.json
$configJson = json_encode($arr);

//carrega o conteudo do certificado.
//$content = file_get_contents('fixtures/certificado.pfx');
$content = file_get_contents('../certificado/4a656dd1f705fed42d1bc2d13221d96471d1b28a.pfx');

//intancia a classe tools
$tools = new Tools($configJson, Certificate::readPfx($content, '999999'));

$tools->model('57');

$cte = new MakeCTeSimp();

//$dhEmi = date("Y-m-d\TH:i:s-03:00"); Para obter a data com diferença de fuso usar 'P'
$dhEmi = date("Y-m-d\TH:i:sP");

$numeroCTE = '1127';

// CUIDADO: Observe que mesmo os parâmetros fixados abaixo devem ser preenchidos conforme os dados do CT-e, estude a composição da CHAVE para saber o que vai em cada campo
$chave = montaChave(
    '31',
    date('y', strtotime($dhEmi)),
    date('m', strtotime($dhEmi)),
    $arr['cnpj'],
    $tools->model(),
    '1',
    $numeroCTE,
    '1',
    '10'
);

$infCte = new stdClass();
$infCte->Id = "";
$infCte->versao = "4.00";
$cte->taginfCTe($infCte);

$cDV = substr($chave, -1);      //Digito Verificador

$ide = new stdClass();
$ide->cUF = '31'; // Codigo da UF da tabela do IBGE
$ide->cCT = '66701958'; // Codigo numerico que compoe a chave de acesso
$ide->CFOP = '5352'; // Codigo fiscal de operacoes e prestacoes
$ide->natOp = 'PRESTACAO DE SERVICO DE TRANSPORTE A ESTABELECIMENTO FORA DO ESTADO DE ORIGEM'; // Natureza da operacao
//$ide->forPag = '';              // 0-Pago; 1-A pagar; 2-Outros
$ide->mod = '57'; // Modelo do documento fiscal: 57 para identificação do CT-e
$ide->serie = '6'; // Serie do CTe
$ide->nCT = $numeroCTE; // Numero do CTe
$ide->dhEmi = $dhEmi; // Data e hora de emissão do CT-e: Formato AAAA-MM-DDTHH:MM:DD
$ide->tpImp = '1'; // Formato de impressao do DACTE: 1-Retrato; 2-Paisagem.
$ide->tpEmis = '1'; // Forma de emissao do CTe: 1-Normal; 4-EPEC pela SVC; 5-Contingência
$ide->cDV = $cDV; // Codigo verificador
$ide->tpAmb = '2'; // 1- Producao; 2-homologacao
$ide->tpCTe = '5'; // 5 - CTe Simplificado; 6 - Substituição CTe Simplificado;
$ide->procEmi = '0'; // Descricao no comentario acima
$ide->verProc = '4.0'; // versao do aplicativo emissor
$ide->cMunEnv = '4302105'; // Utilizar a tabela do IBGE. Informar 9999999 para as operações com o exterior.
$ide->xMunEnv = 'Monte Carmelo'; // Informar PAIS/Municipio para as operações com o exterior.
$ide->UFEnv = 'MG'; // Informar 'EX' para operações com o exterior.
$ide->modal = '01'; // Preencher com:01-Rodoviário; 02-Aéreo; 03-Aquaviário;04-
$ide->tpServ = '0'; // 0- Normal; 1- Subcontratação; 2- Redespacho;
// 3- Redespacho Intermediário; 4- Serviço Vinculado a Multimodal
$ide->UFIni = 'MG'; // Informar 'EX' para operações com o exterior.
$ide->UFFim = 'MG'; // Informar 'EX' para operações com o exterior.
$ide->retira = '1'; // Indicador se o Recebedor retira no Aeroporto; Filial,
// Porto ou Estação de Destino? 0-sim; 1-não
$ide->xDetRetira = ''; // Detalhes do retira
$ide->dhCont = ''; // Data e Hora da entrada em contingência; no formato AAAAMM-DDTHH:MM:SS
$ide->xJust = ''; // Justificativa da entrada em contingência

$cte->tagide($ide);

// Dados complementares
$compl = new stdClass();
$compl->xObs = '';
$cte->tagcompl($compl);

$emit = new stdClass();
$emit->CNPJ = $arr['cnpj']; // CNPJ do emitente
$emit->IE = '0100072968'; // Inscricao estadual
$emit->IEST = ""; // Inscricao estadual
$emit->xNome = $arr['razaosocial']; // Razao social
$emit->xFant = 'Nome Fantasia'; // Nome fantasia
$emit->CRT = 1;
$cte->tagemit($emit);

$enderEmit = new stdClass();
$enderEmit->xLgr = 'RUA CARLOS LUZ'; // Logradouro
$enderEmit->nro = '123'; // Numero
$enderEmit->xCpl = ''; // Complemento
$enderEmit->xBairro = 'PARQUE PRESIDENTE'; // Bairro
$enderEmit->cMun = '3143104'; // Código do município (utilizar a tabela do IBGE)
$enderEmit->xMun = 'Monte Carmelo'; // Nome do municipio
$enderEmit->CEP = '38500000'; // CEP
$enderEmit->UF = $arr['siglaUF']; // Sigla UF
$enderEmit->fone = '34999999999'; // Fone
$cte->tagenderEmit($enderEmit);


//tomador
$toma = new stdClass();
$toma->toma = '3'; // 0-Remetente; 1-Expedidor; 2-Recebedor; 3-Destinatário 4-Terceiro
$toma->indIEToma = '1'; // 1 – Contribuinte ICMS; 2 – Contribuinte isento de inscrição; 9 – Não Contribuinte
$toma->IE = '138960135';
$toma->CNPJ = '05405941000129';
$toma->CPF = ''; // Doc. tomador
$toma->xNome =  'Paulo Henrique';
$toma->fone = '34999999999';
$toma->email = 'prpaulohcmg@gmail.com';
$cte->tagtoma($toma);

// Endereço do Tomador
$enderToma = new stdClass();
$enderToma->xLgr = 'Italia';
$enderToma->nro = '1370';
$enderToma->xCpl = '';
$enderToma->xBairro = 'Primavera';
$enderToma->cMun = '3143104';
$enderToma->xMun = 'Monte Carmelo';
$enderToma->CEP = '38500000';
$enderToma->UF = 'MG';
$enderToma->cPais = '1058';
$enderToma->xPais = 'Brasil';
$cte->tagenderToma($enderToma);

$infCarga = new stdClass();
$infCarga->vCarga = 10000; // Valor total da carga
$infCarga->proPred = 'Cafe'; // Produto predominante
$infCarga->xOutCat = 6.00; // Outras caracteristicas da carga
//$infCarga->vCargaAverb = 1.99;
$cte->taginfCarga($infCarga);

$infQ = new stdClass();
$infQ->cUnid = '01'; // Código da Unidade de Medida: ( 00-M3; 01-KG; 02-TON; 03-UNIDADE; 04-LITROS; 05-MMBTU
$infQ->tpMed = '07'; // Tipo de Medida
// ( PESO BRUTO; PESO DECLARADO; PESO CUBADO; PESO AFORADO; PESO AFERIDO; LITRAGEM; CAIXAS e etc)
$infQ->qCarga = 18145.0000;  // Quantidade (15 posições; sendo 11 inteiras e 4 decimais.)
$cte->taginfQ($infQ);

$infQ->cUnid = '02'; // Código da Unidade de Medida: ( 00-M3; 01-KG; 02-TON; 03-UNIDADE; 04-LITROS; 05-MMBTU
$infQ->tpMed = '99'; // Tipo de Medida
// ( PESO BRUTO; PESO DECLARADO; PESO CUBADO; PESO AFORADO; PESO AFERIDO; LITRAGEM; CAIXAS e etc)
$infQ->qCarga = 31145.0000;  // Quantidade (15 posições; sendo 11 inteiras e 4 decimais.)
$cte->taginfQ($infQ);


$det = new stdClass();
$det->nItem = 1;
$det->cMunIni = '5107040';
$det->xMunIni = 'Primavera do Leste';
$det->cMunFim = '3170206';
$det->xMunFim = 'Uberlandia';
$det->vPrest = 100.00;
$det->vRec = 100.00;
$testeDet = $cte->tagdet($det);

$Comp = new stdClass();
$Comp->xNome = 'FRETE VALOR';
$Comp->vComp = 100.00;
$cte->tagComp($Comp);

$Comp = new stdClass();
$Comp->xNome = 'FRETE VALOR';
$Comp->vComp = 100.00;
$cte->tagComp($Comp);

$infNFe = new stdClass();
$infNFe->chNFe = '51241043461062000103550010000000011616237828';
$infNFe->PIN = '123456';
$infNFe->dPrev = '2024-10-15';

/* $infUnidCarga = new stdClass();*/

$array_infUnidTransp = [];
$infUnidTransp = new stdClass();
$infUnidTransp->tpUnidTransp = '2';
$infUnidTransp->idUnidTransp = 'MGE1245';

$array_infUnidTransp[] = $infUnidTransp;
$infNFe->infUnidTransp = $array_infUnidTransp;

$cte->taginfNFe($infNFe);

/*
$array_infNFeTranspParcial = [];
$infNFeTranspParcial = new stdClass();
$infNFeTranspParcial->chNFe = '51241043461062000103550010000000011616237828';
$array_infNFeTranspParcial[] = $infNFeTranspParcial;
$infDocAnt = new stdClass();
    $infDocAnt->chCTe = '31241005405941000129570060000010081293704576';
    $infDocAnt->tpPrest = 1;
    $infDocAnt->infNFeTranspParcial = $array_infNFeTranspParcial;
    $cte->taginfDocAnt($infDocAnt);
*/

//  Informações do modal
$infModal = new stdClass();
$infModal->versaoModal = '4.00';
$cte->taginfModal($infModal);

$rodo = new stdClass();
$rodo->RNTRC = '00739357';
$cte->tagrodo($rodo);

// Dados da cobrança do CTe
$fat = new stdClass();
$fat->nFat = '123456';
$fat->vOrig = 100.00;
$fat->vDesc = 0.00;
$fat->vLiq = 100.00;
$cte->tagfat($fat);

// Utilizar em caso for um CTe tipo 6 - Substituição CTe Simplificado
/* $infctesub = new stdClass();
$infctesub->chCte = '';
$infctesub->indAlteraToma = 1;
$cte->taginfCteSub($infctesub); */

$icms = new stdClass();
$icms->cst = '00'; // 00 - Tributacao normal ICMS
$icms->pRedBC = ''; // Percentual de redução da BC (3 inteiros e 2 decimais)
$icms->vBC = 3334.32; // Valor da BC do ICMS
$icms->pICMS = 12; // Alícota do ICMS
$icms->vICMS = 400.12; // Valor do ICMS
$icms->vBCSTRet = ''; // Valor da BC do ICMS ST retido
$icms->vICMSSTRet = ''; // Valor do ICMS ST retido
$icms->pICMSSTRet = ''; // Alíquota do ICMS
$icms->vCred = ''; // Valor do Crédito Outorgado/Presumido
$icms->vTotTrib = 754.38; // Valor de tributos federais; estaduais e municipais
$icms->outraUF = false;    // ICMS devido à UF de origem da prestação; quando diferente da UF do emitente
$icms->vICMSUFIni = 0;
$icms->vICMSUFFim = 0;
$icms->infAdFisco = 'Informações ao fisco';
$cte->tagicms($icms);

//  Total
$total = new stdClass();
$total->vTPrest = '1000.00';
$total->vTRec = '1000.00';
$cte->tagTotal($total);

/* $aereo = new stdClass();
$aereo->nMinu = '123'; // Número Minuta
$aereo->nOCA = ''; // Número Operacional do Conhecimento Aéreo
$aereo->dPrevAereo = date('Y-m-d');
$aereo->natCarga_xDime = ''; // Dimensões 1234x1234x1234 em cm
$aereo->natCarga_cInfManu = []; // Informação de manuseio, com dois dígitos, pode ter mais de uma ocorrência.
$aereo->tarifa_CL = 'G'; // M - Tarifa Mínima / G - Tarifa Geral / E - Tarifa Específica
$aereo->tarifa_cTar = ''; // código da tarifa, deverão ser incluídos os códigos de três digítos correspondentes à tarifa
$aereo->tarifa_vTar = 100.00; // valor da tarifa. 15 posições, sendo 13 inteiras e 2 decimais. Valor da tarifa por kg quando for o caso
$cte->tagaereo($aereo); */

$autXML = new stdClass();
$autXML->CPF = '59195248471'; // CPF ou CNPJ dos autorizados para download do XML
$cte->tagautXML($autXML);

//Monta CT-e
$cte->montaCTe();
$chave = $cte->chCTe;
$filename = "xml/{$chave}-cte.xml";
$xml = $cte->getXML();
file_put_contents($filename, $xml);

//Assina
$xml = $tools->signCTe($xml);

//Imprime XML na tela
header('Content-type: text/xml; charset=UTF-8');
print_r($xml);
exit();


//Envia lote e autoriza
$res = $tools->sefazEnviaCTeSimp($xml);

//Converte resposta
$stdCl = new Standardize($res);
//Output array
$arr = $stdCl->toArray();
//print_r($arr);
//Output object
$std = $stdCl->toStd();

if ($std->cStat != 100) { //103 - Lote recebido com Sucesso
    //processa erros
    print_r($arr);
}
echo '<pre>';
print_r($arr);


exit();

function montaChave($cUF, $ano, $mes, $cnpj, $mod, $serie, $numero, $tpEmis, $codigo = '')
{
    if ($codigo == '') {
        $codigo = $numero;
    }
    $forma = "%02d%02d%02d%s%02d%03d%09d%01d%08d";
    $chave = sprintf(
        $forma,
        $cUF,
        $ano,
        $mes,
        $cnpj,
        $mod,
        $serie,
        $numero,
        $tpEmis,
        $codigo
    );
    return $chave . calculaDV($chave);
}

function calculaDV($chave43)
{
    $multiplicadores = array(2, 3, 4, 5, 6, 7, 8, 9);
    $iCount = 42;
    $somaPonderada = 0;
    while ($iCount >= 0) {
        for ($mCount = 0; $mCount < count($multiplicadores) && $iCount >= 0; $mCount++) {
            $num = (int)substr($chave43, $iCount, 1);
            $peso = (int)$multiplicadores[$mCount];
            $somaPonderada += $num * $peso;
            $iCount--;
        }
    }
    $resto = $somaPonderada % 11;
    if ($resto == '0' || $resto == '1') {
        $cDV = 0;
    } else {
        $cDV = 11 - $resto;
    }
    return (string)$cDV;
}
