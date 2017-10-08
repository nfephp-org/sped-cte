<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once '../bootstrap.php';

use NFePHP\CTe\Make;
use NFePHP\CTe\Tools;
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
    "cnpj" => "86933033000100",
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
$tools = new Tools($configJson, Certificate::readPfx($content, '02040608'));

$tools->model('57');

$cte = new Make();

$dhEmi = date("Y-m-d\TH:i:s-03:00");

$numeroCTE = '1';

$chave = montaChave(
    '43', date('y', strtotime($dhEmi)), date('m', strtotime($dhEmi)), $arr['cnpj'], $tools->model(), '1', $numeroCTE, '1', '10'
);

$resp = $cte->infCteTag($chave, $versao = '3.00');
//header('Content-type: text/xml; charset=UTF-8');
//printf ("<pre>%s</pre>", htmlentities ($resp));
//exit();

$cDV = substr($chave, -1);      //Digito Verificador

$resp = $cte->ideTag(
    $cUF = '43', // Codigo da UF da tabela do IBGE
    $cCT = rand('00000010', '99999999'), // Codigo numerico que compoe a chave de acesso
    $CFOP = '6932', // Codigo fiscal de operacoes e prestacoes
    $natOp = substr('PRESTACAO DE SERVICO DE TRANSPORTE A ESTABELECIMEN', 0, 60), // Natureza da operacao
    //$forPag = '',              // 0-Pago; 1-A pagar; 2-Outros
    $mod = '57', // Modelo do documento fiscal: 57 para identificação do CT-e
    $serie = '1', // Serie do CTe
    $nCT = $numeroCTE, // Numero do CTe
    $dhEmi, // Data e hora de emissão do CT-e: Formato AAAA-MM-DDTHH:MM:DD
    $tpImp = '1', // Formato de impressao do DACTE: 1-Retrato; 2-Paisagem.
    $tpEmis = '1', // Forma de emissao do CTe: 1-Normal; 4-EPEC pela SVC; 5-Contingência
    $cDV, // Codigo verificador
    $tpAmb = '2', // 1- Producao, 2-homologacao
    $tpCTe = '0', // 0- CT-e Normal; 1 - CT-e de Complemento de Valores;
    // 2 -CT-e de Anulação; 3 - CT-e Substituto
    $procEmi = '0', // Descricao no comentario acima
    $verProc = '3.0', // versao do aplicativo emissor
    $indGlobalizado = '',
    //$refCTE = '',             // Chave de acesso do CT-e referenciado
    $cMunEnv = '4302105', // Utilizar a tabela do IBGE. Informar 9999999 para as operações com o exterior.
    $xMunEnv = 'FOZ DO IGUACU', // Informar PAIS/Municipio para as operações com o exterior.
    $UFEnv = 'RS', // Informar 'EX' para operações com o exterior.
    $modal = '01', // Preencher com:01-Rodoviário; 02-Aéreo; 03-Aquaviário;04-
    $tpServ = '0', // 0- Normal; 1- Subcontratação; 2- Redespacho;
    // 3- Redespacho Intermediário; 4- Serviço Vinculado a Multimodal
    $cMunIni = '4302105', // Utilizar a tabela do IBGE. Informar 9999999 para as operações com o exterior.
    $xMunIni = 'FOZ DO IGUACU', // Informar 'EXTERIOR' para operações com o exterior.
    $UFIni = 'RS', // Informar 'EX' para operações com o exterior.
    $cMunFim = '3523909', // Utilizar a tabela do IBGE. Informar 9999999 para operações com o exterior.
    $xMunFim = 'ITU', // Informar 'EXTERIOR' para operações com o exterior.
    $UFFim = 'SP', // Informar 'EX' para operações com o exterior.
    $retira = '1', // Indicador se o Recebedor retira no Aeroporto, Filial,
    // Porto ou Estação de Destino? 0-sim; 1-não
    $xDetRetira = '', // Detalhes do retira
    $indIEToma = '1', $dhCont = '', // Data e Hora da entrada em contingência; no formato AAAAMM-DDTHH:MM:SS
    $xJust = ''                 // Justificativa da entrada em contingência
);

$resp = $cte->toma3Tag(
    $toma = '3'                 // Indica o "papel" do tomador: 0-Remetente; 1-Expedidor; 2-Recebedor; 3-Destinatário
);

$resp = $cte->toma4Tag(
    $toma = '4', // 4-Outros, informar os dados cadastrais do tomador quando ele for outros
    $CNPJ = '11509962000197', // CNPJ
    $CPF = '', // CPF
    $IE = 'ISENTO', // Iscricao estadual
    $xNome = 'RAZAO SOCIAL', // Razao social ou Nome
    $xFant = 'NOME FANTASIA', // Nome fantasia
    $fone = '5532128202', // Telefone
    $email = 'email@gmail.com'   // email
);

$resp = $cte->enderTomaTag(
    $xLgr = 'Avenida Independência', // Logradouro
    $nro = '482', // Numero
    $xCpl = '', // COmplemento
    $xBairro = 'Centro', // Bairro
    $cMun = '4308607', // Codigo do municipio do IBEGE Informar 9999999 para operações com o exterior
    $xMun = 'Garibaldi', // Nome do município (Informar EXTERIOR para operações com o exterior.
    $CEP = '95720000', // CEP
    $UF = $arr['siglaUF'], // Sigla UF (Informar EX para operações com o exterior.)
    $cPais = '1058', // Codigo do país ( Utilizar a tabela do BACEN )
    $xPais = 'Brasil'                   // Nome do pais
);

$resp = $cte->emitTag(
    $CNPJ = $arr['cnpj'], // CNPJ do emitente
    $IE = '0100072968', // Inscricao estadual
    $IEST = "", // Inscricao estadual
    $xNome = $arr['razaosocial'], // Razao social
    $xFant = 'Nome Fantasia' // Nome fantasia
);

$resp = $cte->enderEmitTag(
    $xLgr = 'RUA CARLOS LUZ', // Logradouro
    $nro = '123', // Numero
    $xCpl = '', // Complemento
    $xBairro = 'PARQUE PRESIDENTE', // Bairro
    $cMun = '4302105', // Código do município (utilizar a tabela do IBGE)
    $xMun = 'FOZ DO IGUACU', // Nome do municipio
    $CEP = '85863150', // CEP
    $UF = $arr['siglaUF'], // Sigla UF
    $fone = '4535221216'                        // Fone
);

$resp = $cte->remTag(
    $CNPJ = '06539526000392', // CNPJ
    $CPF = '', // CPF
    $IE = '9057800426', // Inscricao estadual
    $xNome = 'CT-E EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL', $Fant = 'INPET', // Nome fantasia
    $fone = '', // Fone
    $email = ''                           // Email
);

$resp = $cte->enderRemeTag(
    $xLgr = 'ALD TITO MUFFATO', // Logradouro
    $nro = '290', // Numero
    $xCpl = 'SALA 04', // Complemento
    $xBairro = 'JARDIM ITAMARATY', // Bairro
    $cMun = '4108304', // Codigo Municipal (Informar 9999999 para operações com o exterior.)
    $xMun = 'Foz do Iguacu', // Nome do municipio (Informar EXTERIOR para operações com o exterior.)
    $CEP = '85863070', // CEP
    $UF = 'PR', // Sigla UF (Informar EX para operações com o exterior.)
    $cPais = '1058', // Codigo do pais ( Utilizar a tabela do BACEN )
    $xPais = 'Brasil'                   // Nome do pais
);

$resp = $cte->destTag(
    $CNPJ = '06539526000120', // CNPJ
    $CPF = '', // CPF
    $IE = '387171890111', // Inscriao estadual
    $xNome = 'CT-E EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL', $fone = '1148869000', // Fone
    $ISUF = '', // Inscrição na SUFRAMA
    $email = ''                           // Email
);

$resp = $cte->enderDestTag(
    $xLgr = 'RODOVIA WALDOMIRO CORREA DE CAMARGO', // Logradouro
    $nro = '7000', // Numero
    $xCpl = 'KM 64, SP 79', // COmplemento
    $xBairro = 'VILA MARTINS', // Bairro
    $cMun = '3523909', // Codigo Municipal (Informar 9999999 para operações com o exterior.)
    $xMun = 'ITU', // Nome do Municipio (Informar EXTERIOR para operações com o exterior.)
    $CEP = '13308200', // CEP
    $UF = 'SP', // Sigla UF (Informar EX para operações com o exterior.)
    $cPais = '1058', // Codigo do Pais (Utilizar a tabela do BACEN)
    $xPais = 'Brasil'               // Nome do pais
);

$resp = $cte->vPrestTag(
    $vTPrest = 3334.32, // Valor total da prestacao do servico
    $vRec = 3334.32      // Valor a receber
);

$resp = $cte->compTag(
    $xNome = 'FRETE VALOR', // Nome do componente
    $vComp = '3334.32'  // Valor do componente
);

$resp = $cte->icmsTag(
    $cst = '00', // 00 - Tributacao normal ICMS
    $pRedBC = '', // Percentual de redução da BC (3 inteiros e 2 decimais)
    $vBC = 3334.32, // Valor da BC do ICMS
    $pICMS = 12, // Alícota do ICMS
    $vICMS = 400.12, // Valor do ICMS
    $vBCSTRet = '', // Valor da BC do ICMS ST retido
    $vICMSSTRet = '', // Valor do ICMS ST retido
    $pICMSSTRet = '', // Alíquota do ICMS
    $vCred = '', // Valor do Crédito Outorgado/Presumido
    $vTotTrib = 754.38, // Valor de tributos federais, estaduais e municipais
    $outraUF = false    // ICMS devido à UF de origem da prestação, quando diferente da UF do emitente
);

$resp = $cte->infCTeNormTag();              // Grupo de informações do CT-e Normal e Substituto

$resp = $cte->infCargaTag(
    $vCarga = 130333.31, // Valor total da carga
    $prodPred = 'TUBOS PLASTICOS', // Produto predominante
    $xOutCat = ''                           // Outras caracteristicas da carga
);

$resp = $cte->infQTag(
    $cUnid = '01', // Código da Unidade de Medida: ( 00-M3; 01-KG; 02-TON; 03-UNIDADE; 04-LITROS; 05-MMBTU
    $tpMed = 'ESTRADO', // Tipo de Medida
    // ( PESO BRUTO, PESO DECLARADO, PESO CUBADO, PESO AFORADO, PESO AFERIDO, LITRAGEM, CAIXAS e etc)
    $qCarga = 18145.0000  // Quantidade (15 posições, sendo 11 inteiras e 4 decimais.)
);

$resp = $cte->infDocTag();

$resp = $cte->infNFeTag(
    $pChave = '43160472202112000136550000000010571048440722', // Chave de acesso da NF-e
    $PIN = '', // PIN SUFRAMA
    $dPrev = '2016-10-30'                                       // Data prevista de entrega
);

$resp = $cte->infModalTag($versaoModal = '3.00');

$resp = $cte->rodoTag(
    $RNTRC = '00739357'    // Registro Nacional de Transportadores Rodoviários de Carga
);

$resp = $cte->montaCTe();

$filename = "xml/{$chave}-cte.xml";

if ($resp) {
  //header('Content-type: text/xml; charset=UTF-8');
  $xml = $cte->getXML();
  file_put_contents($filename, $xml);
  //chmod($filename, 0777);
  //echo $xml;
} else {
  header('Content-type: text/html; charset=UTF-8');
  foreach ($cte->erros as $err) {
    echo 'tag: &lt;' . $err['tag'] . '&gt; ---- ' . $err['desc'] . '<br>';
  }
}

//exit();
//$xml = file_get_contents($filename);
//Assina
$xml = $tools->signCTe($xml);
//header('Content-type: text/xml; charset=UTF-8');
//print_r($xml);
//exit();
//Salva xml assinado
$filename = "xml/{$chave}-cte.xml";
file_put_contents($filename, $xml);


$axmls[] = $xml;
$lote = substr(str_replace(',', '', number_format(microtime(true) * 1000000, 0)), 0, 15);
$res = $tools->sefazEnviaLote($axmls, $lote);

//Converte resposta
$stdCl = new Standardize($res);
//Output array
$arr = $stdCl->toArray();
//print_r($arr);
//Output object
$std = $stdCl->toStd();

if ($std->cStat != 103) {//103 - Lote recebido com Sucesso
  //processa erros
  print_r($arr);
}

echo "recibo: " . $std->infRec->nRec;
//Consulta Recibo
$res = $tools->sefazConsultaRecibo($std->infRec->nRec);
$stdCl = new Standardize($res);
$arr = $stdCl->toArray();
$std = $stdCl->toStd();
if ($std->protCTe->infProt->cStat == 100) {//Autorizado o uso do CT-e
  $filename = "xml/aprovadas/{$chave}-protcte.xml";
  file_put_contents($filename, $xml);
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
