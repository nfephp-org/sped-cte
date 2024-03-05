<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once '../bootstrap.php';

use NFePHP\Common\Certificate;
use NFePHP\CTe\Common\Standardize;
use NFePHP\CTe\MakeCTe;
use NFePHP\CTe\Tools;

//tanto o config.json como o certificado.pfx podem estar
//armazenados em uma base de dados, então não é necessário
///trabalhar com arquivos, este script abaixo serve apenas como
//exemplo durante a fase de desenvolvimento e testes.
$arr = [
    "atualizacao" => "2016-11-03 18:01:21",
    "tpAmb" => 2,
    "razaosocial" => "SUA RAZAO SOCIAL LTDA",
    "cnpj" => "99999999999999",
    "cpf" => "00000000000",
    "siglaUF" => "RS",
    "schemes" => "PL_CTe_300",
    "versao" => '3.00',
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
$content = file_get_contents('fixtures/certificado.pfx');

//intancia a classe tools
$tools = new Tools($configJson, Certificate::readPfx($content, '123456'));

$tools->model('57');

$cte = new MakeCTe();

//$dhEmi = date("Y-m-d\TH:i:s-03:00"); Para obter a data com diferença de fuso usar 'P'
$dhEmi = date("Y-m-d\TH:i:sP");

$numeroCTE = '127';

// CUIDADO: Observe que mesmo os parâmetros fixados abaixo devem ser preenchidos conforme os dados do CT-e, estude a composição da CHAVE para saber o que vai em cada campo
$chave = montaChave(
    '43', date('y', strtotime($dhEmi)), date('m', strtotime($dhEmi)), $arr['cnpj'], $tools->model(), '1', $numeroCTE, '1', '10'
);

$infCte = new stdClass();
$infCte->Id = "";
$infCte->versao = "3.00";
$cte->taginfCTe($infCte);

$cDV = substr($chave, -1);      //Digito Verificador

$ide = new stdClass();
$ide->cUF = '43'; // Codigo da UF da tabela do IBGE
$ide->cCT = '99999999'; // Codigo numerico que compoe a chave de acesso
$ide->CFOP = '6932'; // Codigo fiscal de operacoes e prestacoes
$ide->natOp = 'PRESTACAO DE SERVICO DE TRANSPORTE A ESTABELECIMENTO FORA DO ESTADO DE ORIGEM'; // Natureza da operacao
//$ide->forPag = '';              // 0-Pago; 1-A pagar; 2-Outros
$ide->mod = '57'; // Modelo do documento fiscal: 57 para identificação do CT-e
$ide->serie = '1'; // Serie do CTe
$ide->nCT = $numeroCTE; // Numero do CTe
$ide->dhEmi = $dhEmi; // Data e hora de emissão do CT-e: Formato AAAA-MM-DDTHH:MM:DD
$ide->tpImp = '1'; // Formato de impressao do DACTE: 1-Retrato; 2-Paisagem.
$ide->tpEmis = '1'; // Forma de emissao do CTe: 1-Normal; 4-EPEC pela SVC; 5-Contingência
$ide->cDV = $cDV; // Codigo verificador
$ide->tpAmb = '2'; // 1- Producao; 2-homologacao
$ide->tpCTe = '0'; // 0- CT-e Normal; 1 - CT-e de Complemento de Valores;
// 2 -CT-e de Anulação; 3 - CT-e Substituto
$ide->procEmi = '0'; // Descricao no comentario acima
$ide->verProc = '3.0'; // versao do aplicativo emissor
$ide->indGlobalizado = '';
//$ide->refCTE = '';             // Chave de acesso do CT-e referenciado
$ide->cMunEnv = '4302105'; // Utilizar a tabela do IBGE. Informar 9999999 para as operações com o exterior.
$ide->xMunEnv = 'FOZ DO IGUACU'; // Informar PAIS/Municipio para as operações com o exterior.
$ide->UFEnv = 'RS'; // Informar 'EX' para operações com o exterior.
$ide->modal = '01'; // Preencher com:01-Rodoviário; 02-Aéreo; 03-Aquaviário;04-
$ide->tpServ = '0'; // 0- Normal; 1- Subcontratação; 2- Redespacho;
// 3- Redespacho Intermediário; 4- Serviço Vinculado a Multimodal
$ide->cMunIni = '4302105'; // Utilizar a tabela do IBGE. Informar 9999999 para as operações com o exterior.
$ide->xMunIni = 'FOZ DO IGUACU'; // Informar 'EXTERIOR' para operações com o exterior.
$ide->UFIni = 'RS'; // Informar 'EX' para operações com o exterior.
$ide->cMunFim = '3523909'; // Utilizar a tabela do IBGE. Informar 9999999 para operações com o exterior.
$ide->xMunFim = 'ITU'; // Informar 'EXTERIOR' para operações com o exterior.
$ide->UFFim = 'SP'; // Informar 'EX' para operações com o exterior.
$ide->retira = '1'; // Indicador se o Recebedor retira no Aeroporto; Filial,
// Porto ou Estação de Destino? 0-sim; 1-não
$ide->xDetRetira = ''; // Detalhes do retira
$ide->indIEToma = '1';
$ide->dhCont = ''; // Data e Hora da entrada em contingência; no formato AAAAMM-DDTHH:MM:SS
$ide->xJust = '';                 // Justificativa da entrada em contingência

$cte->tagide($ide);

// Indica o "papel" do tomador: 0-Remetente; 1-Expedidor; 2-Recebedor; 3-Destinatário
$toma3 = new stdClass();
$toma3->toma = '3';
$cte->tagtoma3($toma3);
//
//$toma4 = new stdClass();
//$toma4->toma = '4'; // 4-Outros; informar os dados cadastrais do tomador quando ele for outros
//$toma4->CNPJ = '11509962000197'; // CNPJ
//$toma4->CPF = ''; // CPF
//$toma4->IE = 'ISENTO'; // Iscricao estadual
//$toma4->xNome = 'RAZAO SOCIAL'; // Razao social ou Nome
//$toma4->xFant = 'NOME FANTASIA'; // Nome fantasia
//$toma4->fone = '5532128202'; // Telefone
//$toma4->email = 'email@gmail.com';   // email
//$cte->tagtoma4($toma4);


$enderToma = new stdClass();
$enderToma->xLgr = 'Avenida Independência'; // Logradouro
$enderToma->nro = '482'; // Numero
$enderToma->xCpl = ''; // COmplemento
$enderToma->xBairro = 'Centro'; // Bairro
$enderToma->cMun = '4308607'; // Codigo do municipio do IBEGE Informar 9999999 para operações com o exterior
$enderToma->xMun = 'Garibaldi'; // Nome do município (Informar EXTERIOR para operações com o exterior.
$enderToma->CEP = '95720000'; // CEP
$enderToma->UF = $arr['siglaUF']; // Sigla UF (Informar EX para operações com o exterior.)
$enderToma->cPais = '1058'; // Codigo do país ( Utilizar a tabela do BACEN )
$enderToma->xPais = 'Brasil';                   // Nome do pais
$cte->tagenderToma($enderToma);


$emit = new stdClass();
$emit->CNPJ = $arr['cnpj']; // CNPJ do emitente
$emit->IE = '0100072968'; // Inscricao estadual
$emit->IEST = ""; // Inscricao estadual
$emit->xNome = $arr['razaosocial']; // Razao social
$emit->xFant = 'Nome Fantasia'; // Nome fantasia
$cte->tagemit($emit);


$enderEmit = new stdClass();
$enderEmit->xLgr = 'RUA CARLOS LUZ'; // Logradouro
$enderEmit->nro = '123'; // Numero
$enderEmit->xCpl = ''; // Complemento
$enderEmit->xBairro = 'PARQUE PRESIDENTE'; // Bairro
$enderEmit->cMun = '4302105'; // Código do município (utilizar a tabela do IBGE)
$enderEmit->xMun = 'FOZ DO IGUACU'; // Nome do municipio
$enderEmit->CEP = '85863150'; // CEP
$enderEmit->UF = $arr['siglaUF']; // Sigla UF
$enderEmit->fone = '4535221216'; // Fone
$cte->tagenderEmit($enderEmit);


$rem = new stdClass();
$rem->CNPJ = '06539526000392'; // CNPJ
$rem->CPF = ''; // CPF
$rem->IE = '9057800426'; // Inscricao estadual
$rem->xNome = 'RAZAO SOCIAL';
$rem->xFant = 'NOME FANTASIA'; // Nome fantasia
$rem->fone = ''; // Fone
$rem->email = ''; // Email
$cte->tagrem($rem);


$enderReme = new stdClass();
$enderReme->xLgr = 'ALD TITO MUFFATO'; // Logradouro
$enderReme->nro = '290'; // Numero
$enderReme->xCpl = 'SALA 04'; // Complemento
$enderReme->xBairro = 'JARDIM ITAMARATY'; // Bairro
$enderReme->cMun = '4108304'; // Codigo Municipal (Informar 9999999 para operações com o exterior.)
$enderReme->xMun = 'Foz do Iguacu'; // Nome do municipio (Informar EXTERIOR para operações com o exterior.)
$enderReme->CEP = '85863070'; // CEP
$enderReme->UF = 'PR'; // Sigla UF (Informar EX para operações com o exterior.)
$enderReme->cPais = '1058'; // Codigo do pais ( Utilizar a tabela do BACEN )
$enderReme->xPais = 'Brasil'; // Nome do pais
$cte->tagenderReme($enderReme);


$dest = new stdClass();
$dest->CNPJ = '06539526000120'; // CNPJ
$dest->CPF = ''; // CPF
$dest->IE = '387171890111'; // Inscriao estadual
$dest->xNome = 'NOME FANTASIA';
$dest->fone = '1148869000'; // Fone
$dest->ISUF = ''; // Inscrição na SUFRAMA
$dest->email = ''; // Email
$cte->tagdest($dest);


$enderDest = new stdClass();
$enderDest->xLgr = 'RODOVIA WALDOMIRO CORREA DE CAMARGO'; // Logradouro
$enderDest->nro = '7000'; // Numero
$enderDest->xCpl = 'KM 64; SP 79'; // COmplemento
$enderDest->xBairro = 'VILA MARTINS'; // Bairro
$enderDest->cMun = '3523909'; // Codigo Municipal (Informar 9999999 para operações com o exterior.)
$enderDest->xMun = 'ITU'; // Nome do Municipio (Informar EXTERIOR para operações com o exterior.)
$enderDest->CEP = '13308200'; // CEP
$enderDest->UF = 'SP'; // Sigla UF (Informar EX para operações com o exterior.)
$enderDest->cPais = '1058'; // Codigo do Pais (Utilizar a tabela do BACEN)
$enderDest->xPais = 'Brasil'; // Nome do pais
$cte->tagenderDest($enderDest);


$vPrest = new stdClass();
$vPrest->vTPrest = 3334.32; // Valor total da prestacao do servico
$vPrest->vRec = 3334.32;      // Valor a receber
$cte->tagvPrest($vPrest);


$comp = new stdClass();
$comp->xNome = 'FRETE VALOR'; // Nome do componente
$comp->vComp = '3334.32';  // Valor do componente
$cte->tagComp($comp);

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


$cte->taginfCTeNorm();              // Grupo de informações do CT-e Normal e Substituto


$infCarga = new stdClass();
$infCarga->vCarga = 130333.31; // Valor total da carga
$infCarga->proPred = 'TUBOS PLASTICOS'; // Produto predominante
$infCarga->xOutCat = 6.00; // Outras caracteristicas da carga
$infCarga->vCargaAverb = 1.99;
$cte->taginfCarga($infCarga);

$infQ = new stdClass();
$infQ->cUnid = '01'; // Código da Unidade de Medida: ( 00-M3; 01-KG; 02-TON; 03-UNIDADE; 04-LITROS; 05-MMBTU
$infQ->tpMed = 'ESTRADO'; // Tipo de Medida
// ( PESO BRUTO; PESO DECLARADO; PESO CUBADO; PESO AFORADO; PESO AFERIDO; LITRAGEM; CAIXAS e etc)
$infQ->qCarga = 18145.0000;  // Quantidade (15 posições; sendo 11 inteiras e 4 decimais.)
$cte->taginfQ($infQ);
$infQ->cUnid = '02'; // Código da Unidade de Medida: ( 00-M3; 01-KG; 02-TON; 03-UNIDADE; 04-LITROS; 05-MMBTU
$infQ->tpMed = 'OUTROS'; // Tipo de Medida
// ( PESO BRUTO; PESO DECLARADO; PESO CUBADO; PESO AFORADO; PESO AFERIDO; LITRAGEM; CAIXAS e etc)
$infQ->qCarga = 31145.0000;  // Quantidade (15 posições; sendo 11 inteiras e 4 decimais.)
$cte->taginfQ($infQ);

$infNFe = new stdClass();
$infNFe->chave = '43160472202112000136550000000010571048440722'; // Chave de acesso da NF-e
$infNFe->PIN = ''; // PIN SUFRAMA
$infNFe->dPrev = '2016-10-30';                                       // Data prevista de entrega
$cte->taginfNFe($infNFe);

$infModal = new stdClass();
$infModal->versaoModal = '3.00';
$cte->taginfModal($infModal);

$rodo = new stdClass();
$rodo->RNTRC = '00739357';
$cte->tagrodo($rodo);

$aereo = new stdClass();
$aereo->nMinu = '123'; // Número Minuta
$aereo->nOCA = ''; // Número Operacional do Conhecimento Aéreo
$aereo->dPrevAereo = date('Y-m-d');
$aereo->natCarga_xDime = ''; // Dimensões 1234x1234x1234 em cm
$aereo->natCarga_cInfManu = []; // Informação de manuseio, com dois dígitos, pode ter mais de uma ocorrência.
$aereo->tarifa_CL = 'G'; // M - Tarifa Mínima / G - Tarifa Geral / E - Tarifa Específica
$aereo->tarifa_cTar = ''; // código da tarifa, deverão ser incluídos os códigos de três digítos correspondentes à tarifa
$aereo->tarifa_vTar = 100.00; // valor da tarifa. 15 posições, sendo 13 inteiras e 2 decimais. Valor da tarifa por kg quando for o caso
$cte->tagaereo($aereo);

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
$res = $tools->sefazEnviaCTe($xml);

//Converte resposta
$stdCl = new Standardize($res);
//Output array
$arr = $stdCl->toArray();
//print_r($arr);
//Output object
$std = $stdCl->toStd();

if ($std->cStat != 100) {//103 - Lote recebido com Sucesso
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
        $forma, $cUF, $ano, $mes, $cnpj, $mod, $serie, $numero, $tpEmis, $codigo
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
