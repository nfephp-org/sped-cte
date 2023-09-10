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
    "cnpj" => "29917809000164",
    "siglaUF" => "SP",
    "schemes" => "PL_CTe_400",
    "versao" => '4.00'
];
//monta o config.json
$configJson = json_encode($arr);

//carrega o conteudo do certificado.
$content = file_get_contents('certificado.pfx');

//intancia a classe tools
$tools = new Tools($configJson, Certificate::readPfx($content, 'operador'));

$tools->model('57');

$cte = new MakeCTe();

//$dhEmi = date("Y-m-d\TH:i:s-03:00"); Para obter a data com diferença de fuso usar 'P'
$dhEmi = date("Y-m-d\TH:i:sP");

$numeroCTE = '127';

$infCte = new stdClass();
$infCte->Id = "";
$infCte->versao = "4.00";
$cte->taginfCTe($infCte);

$ide = new stdClass();
$ide->cUF = '35'; // Codigo da UF da tabela do IBGE
$ide->cCT = '99999999'; // Codigo numerico que compoe a chave de acesso
$ide->CFOP = '6932'; // Codigo fiscal de operacoes e prestacoes
$ide->natOp = 'PRESTACAO DE SERVICO DE TRANSPORTE A ESTABELECIMENTO FORA DO ESTADO DE ORIGEM'; // Natureza da operacao
$ide->serie = '1'; // Serie do CTe
$ide->nCT = $numeroCTE; // Numero do CTe
$ide->dhEmi = $dhEmi; // Data e hora de emissão do CT-e: Formato AAAA-MM-DDTHH:MM:DD
$ide->tpImp = '1'; // Formato de impressao do DACTE: 1-Retrato; 2-Paisagem.
$ide->tpEmis = '1'; // Forma de emissao do CTe: 1-Normal; 4-EPEC pela SVC; 5-Contingência
$ide->cDV = null; // Codigo verificador
$ide->tpAmb = '2'; // 1- Producao; 2-homologacao
$ide->tpCTe = '3'; // 0- CT-e Normal; 1 - CT-e de Complemento de Valores; 3 - CTe de Substituição
$ide->procEmi = '0'; // Descricao no comentario acima
$ide->verProc = '3.0'; // versao do aplicativo emissor
$ide->indGlobalizado = '';
$ide->cMunEnv = '4302105'; // Utilizar a tabela do IBGE. Informar 9999999 para as operações com o exterior.
$ide->xMunEnv = 'FOZ DO IGUACU'; // Informar PAIS/Municipio para as operações com o exterior.
$ide->UFEnv = 'RS'; // Informar 'EX' para operações com o exterior.
$ide->modal = '06'; // Preencher com:01-Rodoviário; 02-Aéreo; 03-Aquaviário;04-
$ide->tpServ = '0'; // 0- Normal; 1- Subcontratação; 2- Redespacho; 3- Redespacho Intermediário; 4- Serviço Vinculado a Multimodal
$ide->indIEToma = '9';
$ide->cMunIni = '4302105'; // Utilizar a tabela do IBGE. Informar 9999999 para as operações com o exterior.
$ide->xMunIni = 'FOZ DO IGUACU'; // Informar 'EXTERIOR' para operações com o exterior.
$ide->UFIni = 'RS'; // Informar 'EX' para operações com o exterior.
$ide->cMunFim = '3523909'; // Utilizar a tabela do IBGE. Informar 9999999 para operações com o exterior.
$ide->xMunFim = 'ITU'; // Informar 'EXTERIOR' para operações com o exterior.
$ide->UFFim = 'SP'; // Informar 'EX' para operações com o exterior.
$ide->retira = '1'; // Indicador se o Recebedor retira no Aeroporto; Filial, Porto ou Estação de Destino? 0-sim; 1-não
$ide->xDetRetira = ''; // Detalhes do retira
//$ide->dhCont = date("Y-m-d\TH:i:sP"); // Data e Hora da entrada em contingência; no formato AAAAMM-DDTHH:MM:SS
//$ide->xJust = 'teste de entrada em contingencia';                 // Justificativa da entrada em contingência
$cte->tagide($ide);

// Indica o "papel" do tomador: 0-Remetente; 1-Expedidor; 2-Recebedor; 3-Destinatário
$toma3 = new stdClass();
$toma3->toma = '3';
$cte->tagtoma3($toma3);

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

//$enderToma = new stdClass();
//$enderToma->xLgr = 'Avenida Independência'; // Logradouro
//$enderToma->nro = '482'; // Numero
//$enderToma->xCpl = ''; // COmplemento
//$enderToma->xBairro = 'Centro'; // Bairro
//$enderToma->cMun = '4308607'; // Codigo do municipio do IBEGE Informar 9999999 para operações com o exterior
//$enderToma->xMun = 'Garibaldi'; // Nome do município (Informar EXTERIOR para operações com o exterior.
//$enderToma->CEP = '95720000'; // CEP
//$enderToma->UF = $arr['siglaUF']; // Sigla UF (Informar EX para operações com o exterior.)
//$enderToma->cPais = '1058'; // Codigo do país ( Utilizar a tabela do BACEN )
//$enderToma->xPais = 'Brasil';                   // Nome do pais
//$cte->tagenderToma($enderToma);

$emit = new stdClass();
$emit->CNPJ = $arr['cnpj']; // CNPJ do emitente
$emit->IE = '119458165112'; // Inscricao estadual
$emit->IEST = ""; // Inscricao estadual
$emit->xNome = $arr['razaosocial']; // Razao social
$emit->xFant = 'Nome Fantasia'; // Nome fantasia
$emit->CRT = '1';
$cte->tagemit($emit);

$enderEmit = new stdClass();
$enderEmit->xLgr = 'RUA CARLOS LUZ'; // Logradouro
$enderEmit->nro = '123'; // Numero
$enderEmit->xCpl = ''; // Complemento
$enderEmit->xBairro = 'PARQUE PRESIDENTE'; // Bairro
$enderEmit->cMun = '3550308'; // Código do município (utilizar a tabela do IBGE)
$enderEmit->xMun = 'FOZ DO IGUACU'; // Nome do municipio
$enderEmit->CEP = '85863150'; // CEP
$enderEmit->UF = $arr['siglaUF']; // Sigla UF
$enderEmit->fone = '4535221216'; // Fone
$cte->tagenderEmit($enderEmit);

$fluxo = new stdClass();
$fluxo->xOrig = '11';
$fluxo->xDest = '22';
$fluxo->xRota = '44';
$cte->tagfluxo($fluxo);

$semData = new stdClass();
$semData->tpPer = '0';
$cte->tagsemData($semData);

$semHora = new stdClass();
$semHora->tpHor = '0';
$cte->tagsemHora($semHora);

$compl = new stdClass();
$compl->xCaracAd = '11';
$compl->xCaracSer = '22';
$compl->xEmi = '44';
$compl->origCalc = '44';
$compl->destCalc = '44';
$compl->xObs = '44';
$cte->tagcompl($compl);

$rem = new stdClass();
$rem->CNPJ = ''; // CNPJ
$rem->CPF = '34106079860'; // CPF
$rem->IE = 'ISENTO'; // Inscricao estadual
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
$enderReme->cMun = '3520509'; // Codigo Municipal (Informar 9999999 para operações com o exterior.)
$enderReme->xMun = 'Foz do Iguacu'; // Nome do municipio (Informar EXTERIOR para operações com o exterior.)
$enderReme->CEP = '90480001'; // CEP
$enderReme->UF = 'SP'; // Sigla UF (Informar EX para operações com o exterior.)
$enderReme->cPais = '1058'; // Codigo do pais ( Utilizar a tabela do BACEN )
$enderReme->xPais = 'Brasil'; // Nome do pais
$cte->tagenderReme($enderReme);

$exped = new stdClass();
$exped->CNPJ = ''; // CNPJ
$exped->CPF = '34106079860'; // CPF
$exped->IE = 'ISENTO'; // Inscricao estadual
$exped->xNome = 'RAZAO SOCIAL';
$exped->fone = ''; // Fone
$exped->email = ''; // Email
$cte->tagexped($exped);

$enderExped = new stdClass();
$enderExped->xLgr = 'ALD TITO MUFFATO'; // Logradouro
$enderExped->nro = '290'; // Numero
$enderExped->xCpl = 'SALA 04'; // Complemento
$enderExped->xBairro = 'JARDIM ITAMARATY'; // Bairro
$enderExped->cMun = '3520509'; // Codigo Municipal (Informar 9999999 para operações com o exterior.)
$enderExped->xMun = 'Foz do Iguacu'; // Nome do municipio (Informar EXTERIOR para operações com o exterior.)
$enderExped->CEP = '90480001'; // CEP
$enderExped->UF = 'SP'; // Sigla UF (Informar EX para operações com o exterior.)
$enderExped->cPais = '1058'; // Codigo do pais ( Utilizar a tabela do BACEN )
$enderExped->xPais = 'Brasil'; // Nome do pais
$cte->tagenderExped($enderExped);

$receb = new stdClass();
$receb->CNPJ = ''; // CNPJ
$receb->CPF = '34106079860'; // CPF
$receb->IE = 'ISENTO'; // Inscricao estadual
$receb->xNome = 'RAZAO SOCIAL';
$receb->fone = ''; // Fone
$receb->email = ''; // Email
$cte->tagreceb($receb);

$enderReceb = new stdClass();
$enderReceb->xLgr = 'ALD TITO MUFFATO'; // Logradouro
$enderReceb->nro = '290'; // Numero
$enderReceb->xCpl = 'SALA 04'; // Complemento
$enderReceb->xBairro = 'JARDIM ITAMARATY'; // Bairro
$enderReceb->cMun = '3520509'; // Codigo Municipal (Informar 9999999 para operações com o exterior.)
$enderReceb->xMun = 'Foz do Iguacu'; // Nome do municipio (Informar EXTERIOR para operações com o exterior.)
$enderReceb->CEP = '90480001'; // CEP
$enderReceb->UF = 'SP'; // Sigla UF (Informar EX para operações com o exterior.)
$enderReceb->cPais = '1058'; // Codigo do pais ( Utilizar a tabela do BACEN )
$enderReceb->xPais = 'Brasil'; // Nome do pais
$cte->tagenderReceb($enderReceb);


$dest = new stdClass();
$dest->CNPJ = '07654824000396'; // CNPJ
$dest->CPF = ''; // CPF
$dest->IE = 'ISENTO'; // Inscriao estadual
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
$enderDest->cMun = '4314902'; // Codigo Municipal (Informar 9999999 para operações com o exterior.)
$enderDest->xMun = 'ITU'; // Nome do Municipio (Informar EXTERIOR para operações com o exterior.)
$enderDest->CEP = '90480001'; // CEP
$enderDest->UF = 'RS'; // Sigla UF (Informar EX para operações com o exterior.)
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

$comp = new stdClass();
$comp->xNome = 'VALOR'; // Nome do componente
$comp->vComp = '1.32';  // Valor do componente
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

$infNF = new stdClass();
$infNF->nRoma = '1';
$infNF->nPed = '2';
$infNF->mod = '01';
$infNF->serie = '4';
$infNF->nDoc = '5';
$infNF->dEmi = '2023-05-25';
$infNF->vBC = '7';
$infNF->vICMS = '8';
$infNF->vBCST = '9';
$infNF->vST = '0';
$infNF->vProd = '1';
$infNF->vNF = '2';
$infNF->nCFOP = '1110';
$infNF->nPeso = '4';
$infNF->PIN = '51';
$infNF->dPrev = '2023-05-25';

$lacUnidCarga = new stdClass();
$lacUnidCarga->nLacre = '1';

$infUnidCarga = new stdClass();
$infUnidCarga->tpUnidCarga = 1;
$infUnidCarga->idUnidCarga = '11111';
$infUnidCarga->lacUnidCarga[] = $lacUnidCarga;
$infUnidCarga->lacUnidCarga[] = $lacUnidCarga;
$infUnidCarga->qtdRat = '111';

$lacUnidTransp = new stdClass();
$lacUnidTransp->nLacre = '1';

$infUnidTransp = new stdClass();
$infUnidTransp->tpUnidTransp = 1;
$infUnidTransp->idUnidTransp = '11111';
$infUnidTransp->lacUnidTransp[] = $lacUnidTransp;
$infUnidTransp->infUnidCarga[] = $infUnidCarga;
$infUnidTransp->qtdRat = '111';

// um dos dois
//$infNF->infUnidCarga[] = $infUnidCarga;
$infNF->infUnidTransp[] = $infUnidTransp;

//$cte->taginfNF($infNF);

$infNFe = new stdClass();
$infNFe->chave = '43160472202112000136550000000010571048440722'; // Chave de acesso da NF-e
$infNFe->PIN = ''; // PIN SUFRAMA
$infNFe->dPrev = '2016-10-30';      // Data prevista de entrega

// um dos dois
$infNF->infUnidCarga[] = $infUnidCarga;
//$infNF->infUnidTransp[] = $infUnidTransp;

//$cte->taginfNFe($infNFe);

$infOutros = new stdClass();
$infOutros->tpDoc = '00';
$infOutros->descOutros = 'teste';
$infOutros->nDoc = '1';
$infOutros->dEmi = '2016-10-30';
$infOutros->vDocFisc = '10';
$infOutros->dPrev = '2016-10-30';

// um dos dois
$infNF->infUnidCarga[] = $infUnidCarga;
//$infNF->infUnidTransp[] = $infUnidTransp;

$cte->taginfOutros($infOutros);

$cte->tagdocAnt();

$emiDocAnt = new stdClass();
$emiDocAnt->CNPJ = '07654824000396';
$emiDocAnt->IE = '12346587';
$emiDocAnt->UF = 'PR';
$emiDocAnt->CPF = '85602400';
$emiDocAnt->xNome = 'JOAO';
$cte->tagemiDocAnt($emiDocAnt);

$cte->tagidDocAnt();

// um dos dois

$idDocAntPap = new stdClass();
$idDocAntPap->tpDoc = '07';
$idDocAntPap->serie = '00';
$idDocAntPap->subser = '00';
$idDocAntPap->nDoc = '1';
$idDocAntPap->dEmi = '2016-10-30';
//$cte->tagidDocAntPap($idDocAntPap);

$idDocAntEle = new stdClass();
$idDocAntEle->chCTe = '43160472202112000136550000000010571048440722';
$cte->tagidDocAntEle($idDocAntEle);

$infModal = new stdClass();
$infModal->versaoModal = '4.00';
$cte->taginfModal($infModal);

// modal = 01
$rodo = new stdClass();
$rodo->RNTRC = '00739357';
$cte->tagrodo($rodo);

$occ = new stdClass();
$occ->serie = '1';
$occ->nOcc = '2';
$occ->dEmi = '2016-10-30';
$occ->CNPJ = '07654824000396';
$occ->cInt = '3';
$occ->IE = '00739357';
$occ->UF = 'PR';
$occ->fone = '00739357';
$cte->tagocc($occ);

$cte->tagocc($occ);

// modal = 02
$aereo = new stdClass();
$aereo->nMinu = '123123123'; // Número Minuta
$aereo->nOCA = ''; // Número Operacional do Conhecimento Aéreo
$aereo->dPrevAereo = date('Y-m-d');
$aereo->natCarga_xDime = ''; // Dimensões 1234x1234x1234 em cm
$aereo->natCarga_cInfManu = [
    '01',
    '02'
]; // Informação de manuseio, com dois dígitos, pode ter mais de uma ocorrência.
$aereo->tarifa_CL = 'G'; // M - Tarifa Mínima / G - Tarifa Geral / E - Tarifa Específica
$aereo->tarifa_cTar = ''; // código da tarifa, deverão ser incluídos os códigos de três digítos correspondentes à tarifa
$aereo->tarifa_vTar = 100.00; // valor da tarifa. 15 posições, sendo 13 inteiras e 2 decimais. Valor da tarifa por kg quando for o caso
$cte->tagaereo($aereo);

$peri = new stdClass();
$peri->nONU = '1231';
$peri->qTotEmb = '100';
$peri->qTotProd = '105';
$peri->uniAP = '1';
$cte->tagperi($peri);

$cte->tagperi($peri);

// modal = 03
$aquav = new stdClass();
$aquav->vPrest = '1';
$aquav->vAFRMM = '1';
$aquav->xNavio = '1';
$aquav->nViag = '1';
$aquav->direc = '1';
$aquav->irin = '1';
$aquav->tpNav = '1';
$cte->tagaquav($aquav);

$balsa = new stdClass();
$balsa->xBalsa = 'teste de balsa';
$cte->tagbalsa($balsa);
$cte->tagbalsa($balsa);

$detCont = new stdClass();
$detCont->nCont = '1';
$cte->tagdetCont($detCont);

$lacre = new stdClass();
$lacre->nLacre = '1';
$cte->taglacre($lacre);
$cte->taglacre($lacre);

$cte->taginfDocCont();

$infNF = new stdClass();
$infNF->chave = '1';
$infNF->unidRat = '1';
$cte->taginfNFeCont($infNF);

$infNF = new stdClass();
$infNF->serie = '1';
$infNF->nDoc = '1';
$infNF->unidRat = '1';
$cte->taginfNFCont($infNF);

// modal = 04
$ferrov = new stdClass();
$ferrov->tpTraf = '1';
$ferrov->respFat = '1';
$ferrov->ferrEmi = '1';
$ferrov->vFrete = '1';
$ferrov->chCTeFerroOrigem = '43160472202112000136550000000010571048440722';
$ferrov->fluxo = 'teste';
$cte->tagferrov($ferrov);

$ferroEnv = new stdClass();
$ferroEnv->CNPJ = '07654824000396';
$ferroEnv->cInt = '1';
$ferroEnv->IE = '1234648';
$ferroEnv->xNome = 'teste';
$ferroEnv->xLgr = 'teste';
$ferroEnv->nro = '1';
$ferroEnv->xCpl = 'teste';
$ferroEnv->xBairro = 'teste';
$ferroEnv->cMun = '4308607';
$ferroEnv->xMun = 'teste';
$ferroEnv->CEP = '85602500';
$ferroEnv->UF = 'PR';
$cte->tagferroEnv($ferroEnv);

// modal = 05
$duto = new stdClass();
$duto->vTar = '100';
$duto->dIni = '2016-10-30';
$duto->dFim = '2016-10-30';
$cte->tagduto($duto);

// modal = 06
$multimodal = new stdClass();
$multimodal->COTM = '100';
$multimodal->indNegociavel = '1';
$cte->tagmultimodal($multimodal);

$segMultimodal = new stdClass();
$segMultimodal->xSeg = 'teste';
$segMultimodal->CNPJ = '07654824000396';
$segMultimodal->nApol = '100';
$segMultimodal->nAver = '2';
$cte->tagSegMultimodal($segMultimodal);

$veicNovos = new stdClass();
$veicNovos->chassi = '00739357007393570';
$veicNovos->cCor = '00';
$veicNovos->xCor = 'teste';
$veicNovos->cMod = '10';
$veicNovos->vUnit = '10';
$veicNovos->vFrete = '10';
$cte->tagveicNovos($veicNovos);

$fat = new stdClass();
$fat->nFat = '1';
$fat->vOrig = '10';
$fat->vDesc = '';
$fat->vLiq = '10';
$cte->tagfat($fat);

$dup = new stdClass();
$dup->nDup = '1';
$dup->dVenc = '2016-10-30';
$dup->vDup = '10';
$cte->tagdup($dup);

$dup = new stdClass();
$dup->nDup = '2';
$dup->dVenc = '2016-10-30';
$dup->vDup = '10';
$cte->tagdup($dup);

$infCteSub = new stdClass();
$infCteSub->chCte = '43160472202112000136550000000010571048440722';
$infCteSub->indAlteraToma = '0';
$cte->taginfCteSub($infCteSub);

$infCteSub = new stdClass();
$infCteSub->chCTe = '43160472202112000136550000000010571048440722';
$cte->taginfCTeComp($infCteSub);

$infGlobalizado = new stdClass();
$infGlobalizado->xObs = 'teste teste teste teste ';
$cte->taginfGlobalizado($infGlobalizado);

$infServVinc = new stdClass();
$infServVinc->chCTeMultimodal = '43160472202112000136550000000010571048440722';
$cte->taginfCTeMultimodal($infServVinc);

$autXML = new stdClass();
$autXML->CPF = '59195248471'; // CPF ou CNPJ dos autorizados para download do XML
$cte->tagautXML($autXML);

//Monta CT-e
$cte->montaCTe();
$chave = $cte->chCTe;
$xml = $cte->getXML();

try {
    //Assina
    $xml = $tools->signCTe($xml);
    header('Content-type: text/xml; charset=UTF-8');
    echo $xml;
} catch (Exception $e) {
    header('Content-type: text/plain; charset=UTF-8');
    echo $xml;
    throw $e;
}
