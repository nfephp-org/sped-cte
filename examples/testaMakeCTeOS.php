<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once '../bootstrap.php';

use NFePHP\CTe\Make;
use NFePHP\CTe\Tools;
use NFePHP\CTe\Complements;
use NFePHP\CTe\Common\Standardize;
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapCurl;

//tanto o config.json como o certificado.pfx podem estar
//armazenados em uma base de dados, então não é necessário 
///trabalhar com arquivos, este script abaixo serve apenas como 
//exemplo durante a fase de desenvolvimento e testes.
$arr = [
    "atualizacao" => "2016-11-03 18:01:21",
    "tpAmb" => 2,
    "razaosocial" => "SUA RAZAO SOCIAL LTDA",
    "cnpj" => "99999999999999",//CNPJ do certificado
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

$tools->model('67');
$cte = new Make();
$dhEmi = date("Y-m-d\TH:i:s-03:00");
$numeroCTE = '1';
$chave = montaChave(
    '43', date('y', strtotime($dhEmi)), date('m', strtotime($dhEmi)), $arr['cnpj'], $tools->model('67'), '1', $numeroCTE, '1', '00000368'
);
$infCte = new stdClass();
$infCte->Id = "";
$infCte->versao = "3.00";
$cte->taginfCTe($infCte);
$cDV = substr($chave, -1);      //Digito Verificador
$ide = new stdClass();
$ide->cUF = '43'; // Codigo da UF da tabela do IBGE
$ide->cCT = '00000368'; // Codigo numerico que compoe a chave de acesso
$ide->CFOP = '5357'; // Codigo fiscal de operacoes e prestacoes
$ide->natOp = substr('Prestação de serv.', 0, 59); // Natureza da operacao
$ide->mod = '67'; // Modelo do documento fiscal: 57 para identificação do CT-e
$ide->serie = '1'; // Serie do CTe
$ide->nCT = $numeroCTE; // Numero do CTe
$ide->dhEmi = $dhEmi; // Data e hora de emissão do CT-e: Formato AAAA-MM-DDTHH:MM:DD
$ide->tpImp = '1'; // Formato de impressao do DACTE: 1-Retrato; 2-Paisagem.
$ide->tpEmis = '1'; // Forma de emissao do CTe: 1-Normal; 4-EPEC pela SVC; 5-Contingência
$ide->cDV = $cDV; // Codigo verificador
$ide->tpAmb = '2'; // 1- Producao; 2-homologacao
$ide->tpCTe = '0'; // 0- CT-e Normal; 1 - CT-e de Complemento de Valores;
$ide->procEmi = '0'; // Descricao no comentario acima
$ide->verProc = '3.0'; // versao do aplicativo emissor
//$ide->refCTE = '';             // Chave de acesso do CT-e referenciado
$ide->cMunEnv = '4319000'; // Utilizar a tabela do IBGE. Informar 9999999 para as operações com o exterior.
$ide->xMunEnv = 'SAO LEOPOLDO'; // Informar PAIS/Municipio para as operações com o exterior.
$ide->UFEnv = 'RS';             // Informar 'EX' para operações com o exterior.
$ide->modal = '01';              // Preencher com:01-Rodoviário; 02-Aéreo; 03-Aquaviário;04-
$ide->tpServ = '6';              // 0- Normal; 1- Subcontratação; 2- Redespacho;
$ide->indIEToma = '9';
// 3- Redespacho Intermediário; 4- Serviço Vinculado a Multimodal
$ide->cMunIni = '3538709';      // Utilizar a tabela do IBGE. Informar 9999999 para as operações com o exterior.
$ide->xMunIni = 'PIRACICABA'; // Informar 'EXTERIOR' para operações com o exterior.
$ide->UFIni = 'SP';             // Informar 'EX' para operações com o exterior.
$ide->cMunFim = '3106200';       // Utilizar a tabela do IBGE. Informar 9999999 para operações com o exterior.
$ide->xMunFim = 'BELO HORIZONTE';           // Informar 'EXTERIOR' para operações com o exterior.
$ide->UFFim = 'MG';
$ide->dhCont = ''; // Data e Hora da entrada em contingência; no formato AAAAMM-DDTHH:MM:SS
$ide->xJust = '';                 // Justificativa da entrada em contingência
$cte->tagideCTeOS($ide);
// Indica o "papel" do tomador: 0-Remetente; 1-Expedidor; 2-Recebedor; 3-Destinatário

$taginfServico = new stdClass();
$taginfServico->xDescServ = 'TESTE';
$taginfServico->qCarga = 6.0000;
$cte->taginfServico($taginfServico);

$comp = new stdClass();
$comp->xCaracAd = 'Caracteristicas';
$comp->xCaracSer = 'Servico';
$comp->xEmi = 'Emi';
$comp->xObs = 'obs';
$cte->tagcomplCTeOs($comp);

$emit = new stdClass();
$emit->CNPJ = $arr['cnpj']; // CNPJ do emitente
$emit->IE = '0290632153'; // Inscricao estadual
$emit->IEST = ""; // Inscricao estadual
$emit->xNome = $arr['razaosocial']; // Razao social
$emit->xFant = $arr['razaosocial']; // Nome fantasia
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


$toma = new stdClass();
$toma->toma = '4'; // 4-Outros; informar os dados cadastrais do tomador quando ele for outros
$toma->CNPJ = '11509962000197'; // CNPJ
$toma->CPF = ''; // CPF
$toma->IE = ''; // Iscricao estadual
$toma->xNome = 'RAZAO SOCIAL'; // Razao social ou Nome
$toma->xFant = ''; // Nome fantasia
$toma->fone = '5532128202'; // Telefone
$toma->email = '';   // email
$toma->xLgr = 'Avenida Independência'; // Logradouro
$toma->nro = '482'; // Numero
$toma->xCpl = ''; // COmplemento
$toma->xBairro = 'Centro'; // Bairro
$toma->cMun = '4308607'; // Codigo do municipio do IBEGE Informar 9999999 para operações com o exterior
$toma->xMun = 'Garibaldi'; // Nome do município (Informar EXTERIOR para operações com o exterior.
$toma->CEP = '95720000'; // CEP
$toma->UF = $arr['siglaUF']; // Sigla UF (Informar EX para operações com o exterior.)
$toma->cPais = '1058'; // Codigo do país ( Utilizar a tabela do BACEN )
$toma->xPais = 'Brasil';                   // Nome do pais
$cte->tagtoma4CTeOS($toma);

$vPrest = new stdClass();
$vPrest->vTPrest = 3334.32; // Valor total da prestacao do servico
$vPrest->vRec = 3334.32;      // Valor a receber
$cte->tagvPrest($vPrest);

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
$cte->tagicms($icms);

$tribFed = new stdClass();
$tribFed->vPIS = '';
$tribFed->vCOFINS = '';
$tribFed->vIR = '';
$tribFed->vCSLL = '';
$tribFed->vINSS = 0.00;
$cte->taginfTribFed($tribFed);

$cte->taginfCTeNorm();

//Modal rodoviário OS
$infModal = new stdClass();
$infModal->versaoModal = '3.00';
$cte->taginfModal($infModal);

$rodo = new stdClass();
$rodo->TAF = '000000739357';
$rodo->nroRegEstadual = '';
$cte->tagrodoOS($rodo);

$veic = new stdClass();
$veic->placa = 'YAF1223';
$veic->RENAVAM = '01093538977';
$veic->xNome = '';
$veic->uf = 'RS';
$cte->tagveicCTeOS($veic);

//Monta CT-e
$cte->montaCTe();
$chave = $cte->chCTe;
$filename = "xml/{$chave}-cteos.xml";
$xml = $cte->getXML();
file_put_contents($filename, $xml);

//Assina
$xml = $tools->signCTe($xml);
file_put_contents($filename, $xml);


//Imprime XML na tela
//header('Content-type: text/xml; charset=UTF-8');
//print_r($xml);
//exit();
//CT-e OS deve ser enviado unitário
$res = $tools->sefazEnviaCTeOS($xml);

//Converte resposta
$stdCl = new Standardize($res);
//Output array
$arr = $stdCl->toArray();
echo '<pre>';
print_r($arr);

//Output object
$std = $stdCl->toStd();

if ($std->cStat != 103) {//103 - Lote recebido com Sucesso
    //processa erros
    print_r($arr);
}

if ($std->protCTe->infProt->cStat == 100) {//Autorizado o uso do CT-e
    //adicionar protocolo
    $auth = Complements::toAuthorize($xml, $res);
    $filename = "xml/{$chave}-cteos-prot.xml";
    file_put_contents($filename, $auth);
}


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
            $num = (int) substr($chave43, $iCount, 1);
            $peso = (int) $multiplicadores[$mCount];
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
    return (string) $cDV;
}
