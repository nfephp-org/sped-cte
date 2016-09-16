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
    $cUF = '41',                // Codigo da UF da tabela do IBGE: 41-PR
    $ano = date('y', strtotime($dhEmi)),
    $mes = date('m', strtotime($dhEmi)),
    $cnpj = $cteTools->aConfig['cnpj'],
    $mod = '57',                // Modelo do documento fiscal: 57 para identificação do CT-e
    $serie = '2',               // Serie do CTe
    $numero = '58',             // Numero do CTe
    $tpEmis = '1',              // Forma de emissao do CTe: 1-Normal; 4-EPEC pela SVC; 5-Contingência
    $cCT = '10'
);               // Codigo numerico que compoe a chave de acesso (Codigo aleatorio do emitente, para evitar acessos indevidos ao documento)

$resp = $cte->infCteTag($chave, $versao = '2.00');

$cDV = substr($chave, -1);      //Digito Verificador

$resp = $cte->ideTag(
    $cUF = '41',                // Codigo da UF da tabela do IBGE
    $cCT = '00000010',          // Codigo numerico que compoe a chave de acesso (Codigo aleatorio do emitente, para evitar acessos indevidos ao documento)
    $CFOP = '6352',             // Codigo fiscal de operacoes e prestacoes
    $natOp = substr('PRESTACAO DE SERVICO DE TRANSPORTE A ESTABELECIMEN', 0, 60), // Natureza da operacao
    $forPag = '1',              // 0-Pago; 1-A pagar; 2-Outros
    $mod = '57',                // Modelo do documento fiscal: 57 para identificação do CT-e
    $serie = '2',               // Serie do CTe
    $nCT = '58',                // Numero do CTe
    $dhEmi,                     // Data e hora de emissão do CT-e: Formato AAAA-MM-DDTHH:MM:DD
    $tpImp = '1',               // Formato de impressao do DACTE: 1-Retrato; 2-Paisagem.
    $tpEmis = '1',              // Forma de emissao do CTe: 1-Normal; 4-EPEC pela SVC; 5-Contingência
    $cDV,                       // Codigo verificador
    $tpAmb = '2',               // 1- Producao, 2-homologacao
    $tpCTe = '0',               // 0- CT-e Normal; 1 - CT-e de Complemento de Valores; 2 -CT-e de Anulação; 3 - CT-e Substituto
    //$procEmi: 0- emissão de CT-e com aplicativo do contribuinte;
                                //          1- emissão de CT-e avulsa pelo Fisco;
                                //          2- emissão de CT-e avulsa, pelo contribuinte com seu certificado digital, através do site do Fisco;
                                //          3- emissão CT-e pelo contribuinte com aplicativo fornecido pelo Fisco.
    $procEmi = '0',             // Descricao no comentario acima
    //$verProc = '2.0',           // versao do aplicativo emissor
    $verProc = '1.0.47.188',
    $refCTE = '',               // Chave de acesso do CT-e referenciado
    $cMunEnv = '4108304',       // Utilizar a tabela do IBGE. Informar 9999999 para as operações com o exterior.
    $xMunEnv = 'FOZ DO IGUACU', // Informar PAIS/Municipio para as operações com o exterior.
    $UFEnv = 'PR',              // Informar 'EX' para operações com o exterior.
    $modal = '01',              // Preencher com:01-Rodoviário; 02-Aéreo; 03-Aquaviário;04-
    $tpServ = '0',              // 0- Normal; 1- Subcontratação; 2- Redespacho; 3- Redespacho Intermediário; 4- Serviço Vinculado a Multimodal
    $cMunIni = '4108304',       // Utilizar a tabela do IBGE. Informar 9999999 para as operações com o exterior.
    $xMunIni = 'FOZ DO IGUACU', // Informar 'EXTERIOR' para operações com o exterior.
    $UFIni = 'PR',              // Informar 'EX' para operações com o exterior.
    $cMunFim = '3523909',       // Utilizar a tabela do IBGE. Informar 9999999 para operações com o exterior.
    $xMunFim = 'ITU',           // Informar 'EXTERIOR' para operações com o exterior.
    $UFFim = 'SP',              // Informar 'EX' para operações com o exterior.
    $retira = '1',              // Indicador se o Recebedor retira no Aeroporto, Filial, Porto ou Estação de Destino? 0-sim; 1-não
    $xDetRetira = '',           // Detalhes do retira
    $dhCont = '',               // Data e Hora da entrada em contingência; no formato AAAAMM-DDTHH:MM:SS
    $xJust = ''                 // Justificativa da entrada em contingência
);

$resp = $cte->toma03Tag(
    $toma = '3'                 // Indica o "papel" do tomador: 0-Remetente; 1-Expedidor; 2-Recebedor; 3-Destinatário
);



$resp = $cte->toma4Tag(
    $toma = '4',                        // 4-Outros, informar os dados cadastrais do tomador quando ele for outros
    $CNPJ = '11509962000197',           // CNPJ
    $CPF = '',                          // CPF
    $IE = 'ISENTO',                     // Iscricao estadual
    $xNome = 'OTIMIZY',                 // Razao social ou Nome
    $xFant = 'OTIMIZY',                 // Nome fantasia
    $fone = '5434625522',               // Telefone
    $email = 'contato@otimizy.com.br'   // email
);

$resp = $cte->enderTomaTag(
    $xLgr = 'Avenida Independência',    // Logradouro
    $nro = '482',                       // Numero
    $xCpl = '',                         // COmplemento
    $xBairro = 'Centro',                // Bairro
    $cMun = '4308607',                  // Codigo do municipio do IBEGE Informar 9999999 para operações com o exterior.
    $xMun = 'Garibaldi',                // Nome do município (Informar EXTERIOR para operações com o exterior.
    $CEP = '95720000',                  // CEP
    $UF = $cteTools->aConfig['siglaUF'], // Sigla UF (Informar EX para operações com o exterior.)
    $cPais = '1058',                    // Codigo do país ( Utilizar a tabela do BACEN )
    $xPais = 'Brasil'                   // Nome do pais
);

$resp = $cte->emitTag(
    $CNPJ = $cteTools->aConfig['cnpj'],         // CNPJ do emitente
    $IE = $cteTools->aConfig['ie'],             // Inscricao estadual
    $xNome = $cteTools->aConfig['razaosocial'], // Razao social
    $xFant = $cteTools->aConfig['nomefantasia'] // Nome fantasia
);

$resp = $cte->enderEmitTag(
    $xLgr = 'RUA CARLOS LUZ',            // Logradouro
    $nro = '33',                               // Numero
    $xCpl = '',                         // Complemento
    $xBairro = 'PARQUE PRESIDENTE',                        // Bairro
    $cMun = '4108304',                          // Código do município (utilizar a tabela do IBGE)
    $xMun = 'FOZ DO IGUACU',                        // Nome do municipio
    $CEP = '85863150',                          // CEP
    $UF = $cteTools->aConfig['siglaUF'],        // Sigla UF
    $fone = '4535221216'                        // Fone
);

$resp = $cte->remTag(
    $CNPJ = '06539526000392',                                   // CNPJ
    $CPF = '',                                                  // CPF
    $IE = '9057800426',                                         // Inscricao estadual
    $xNome = 'CT-E EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL',
    $Fant = 'INPET',                                          // Nome fantasia
    $fone = '',                                         // Fone
    $email = ''                           // Email
);

$resp = $cte->enderRemeTag(
    $xLgr = 'ALD TITO MUFFATO',                                        // Logradouro
    $nro = '290',                                               // Numero
    $xCpl = 'SALA 04',                                                 // Complemento
    $xBairro = 'JARDIM ITAMARATY',                          // Bairro
    $cMun = '4108304',                                          // Codigo Municipal (Informar 9999999 para operações com o exterior.)
    $xMun = 'Foz do Iguacu',                                    // Nome do municipio (Informar EXTERIOR para operações com o exterior.)
    $CEP = '85863070',                                          // CEP
    $UF = 'PR',                                                 // Sigla UF (Informar EX para operações com o exterior.)
    $cPais = '1058',                                            // Codigo do pais ( Utilizar a tabela do BACEN )
    $xPais = 'Brasil'                                           // Nome do pais
);

$resp = $cte->destTag(
    $CNPJ = '06539526000120',                                   // CNPJ
    $CPF = '',                                                  // CPF
    $IE = '387171890111',                                       // Inscriao estadual
    $xNome = 'CT-E EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL',
    $fone = '1148869000',                                       // Fone
    $ISUF = '',                                                 // Inscrição na SUFRAMA
    $email = ''                           // Email
);

$resp = $cte->enderDestTag(
    $xLgr = 'RODOVIA WALDOMIRO CORREA DE CAMARGO',                        // Logradouro
    $nro = '7000',                                                // Numero
    $xCpl = 'KM 64, SP 79',                                          // COmplemento
    $xBairro = 'VILA MARTINS',                               // Bairro
    $cMun = '3523909',                                          // Codigo Municipal (Informar 9999999 para operações com o exterior.)
    $xMun = 'ITU',                                          // Nome do Municipio (Informar EXTERIOR para operações com o exterior.)
    $CEP = '13308200',                                          // CEP
    $UF = 'SP',                                                 // Sigla UF (Informar EX para operações com o exterior.)
    $cPais = '1058',                                            // Codigo do Pais (Utilizar a tabela do BACEN)
    $xPais = 'Brasil'                                           // Nome do pais
);

$resp = $cte->vPrestTag(
    $vTPrest = 3334.32,  // Valor total da prestacao do servico
    $vRec = 3334.32      // Valor a receber
);

$resp = $cte->compTag(
    $xNome = 'FRETE VALOR',   // Nome do componente
    $vComp = '3334.32'  // Valor do componente
);

$resp = $cte->icmsTag(
    $cst = '00',        // 00 - Tributacao normal ICMS
    $pRedBC = '',       // Percentual de redução da BC (3 inteiros e 2 decimais)
    $vBC = 3334.32,     // Valor da BC do ICMS
    $pICMS = 12,        // Alícota do ICMS
    $vICMS = 400.12,    // Valor do ICMS
    $vBCSTRet = '',     // Valor da BC do ICMS ST retido
    $vICMSSTRet = '',   // Valor do ICMS ST retido
    $pICMSSTRet = ''      // Alíquota do ICMS
);

/*$rep = $cte->vTotTribTag(
        $vTotTrib=754.38 // Valor de tributos federais, estaduais e municipais
);*/

$resp = $cte->infCTeNormTag();              // Grupo de informações do CT-e Normal e Substituto

$resp = $cte->infCargaTag(
    $vCarga = 130333.31,                     // Valor total da carga
    $prodPred = 'TUBOS PLASTICOS',  // Produto predominante
    $xOutCat = ''                           // Outras caracteristicas da carga
);

$resp = $cte->infQTag(
    $cUnid = '01',                          // Código da Unidade de Medida: ( 00-M3; 01-KG; 02-TON; 03-UNIDADE; 04-LITROS; 05-MMBTU
    $tpMed = 'ESTRADO',                        // Tipo de Medida ( PESO BRUTO, PESO DECLARADO, PESO CUBADO, PESO AFORADO, PESO AFERIDO, LITRAGEM, CAIXAS e etc)
    $qCarga = 18145.0000                     // Quantidade (15 posições, sendo 11 inteiras e 4 decimais.)
);

$resp = $cte->infDocTag();

$resp = $cte->infNFeTag(
    $pChave = '43160472202112000136550000000010571048440722',   // Chave de acesso da NF-e
    $PIN = '',                                                  // PIN SUFRAMA
    $dPrev = '2016-10-30'                                       // Data prevista de entrega
);

$resp = $cte->segTag(
    $respSeg = 4,                           // Responsavel pelo seguro (0-Remetente; 1-Expedidor; 2-Recebedor; 3-Destinatário; 4-Emitente do CT-e; 5-Tomador de Serviço)
    $xSeg = 'ACE SEG.SOLUÇÕES CORPORATIVAS',  // Nomeda da Seguradora
    $nApol = '17.54.0009951.14'               // Numero da Apolice
);

$resp = $cte->infModalTag($versaoModal = '2.00');

$resp = $cte->rodoTag(
    $RNTRC = '00739357',    // Registro Nacional de Transportadores Rodoviários de Carga
    $dPrev = '2016-11-30',  // Data prevista para entrega da carga no recebedor formato ( aaaa-mm-dd )
    $lota = '1',            // Indicador de lotacao ( 0-nao; 1-sim) Será lotação quando houver um único crt por veículo, ou combinação veicular, e por viagem.
    $CIOT = ''
);

$resp = $cte->veicTag(
    $RENAVAM = '172414148',   // RENAVAM do veículo
    $placa = 'BAR0585',       // Placa do veiculo
    $tara = '17100',          // Tara em KG
    $capKG = '24900',         // Capacidade em KG
    $capM3 = '100',           // Capacidade em M3
    $tpProp = 'P',            // Tipo de Propriedade de veiculo ( P- Próprio; T- terceiro. Será próprio quando proprietario do veículo for o Emitente do CT-e )
    $tpVeic = '0',            // Tipo de veículo ( 0-Tração; 1-Reboque )
    $tpRod = '03',            // Tipo de Rodaddo ( 00 - não aplicável; 01 - Truck; 02 - Toco; 03 - Cavalo Mecânico; 04 - VAN; 05 - Utilitário; 06 - Outros.)
    $tpCar = '00',            // Tipo de carroceria ( 00 - não aplicável; 01 - Aberta; 02 - Fechada/Baú; 03 - Granelera; 04 - Porta Container; 05 - Sider)
    $UF = 'PR'                // Sigla UF de licenciamento do veiculo
);

$resp = $cte->veicTag(
    $RENAVAM = '828262659',   // RENAVAM do veículo
    $placa = 'BAR0186',       // Placa do veiculo
    $tara = '17100',          // Tara em KG
    $capKG = '24900',         // Capacidade em KG
    $capM3 = '100',           // Capacidade em M3
    $tpProp = 'P',            // Tipo de Propriedade de veiculo ( P- Próprio; T- terceiro. Será próprio quando proprietario do veículo for o Emitente do CT-e )
    $tpVeic = '0',            // Tipo de veículo ( 0-Tração; 1-Reboque )
    $tpRod = '00',            // Tipo de Rodaddo ( 00 - não aplicável; 01 - Truck; 02 - Toco; 03 - Cavalo Mecânico; 04 - VAN; 05 - Utilitário; 06 - Outros.)
    $tpCar = '00',            // Tipo de carroceria ( 00 - não aplicável; 01 - Aberta; 02 - Fechada/Baú; 03 - Granelera; 04 - Porta Container; 05 - Sider)
    $UF = 'PR'                // Sigla UF de licenciamento do veiculo
);

$resp = $cte->motoTag(
    $xNome = 'JOAO MARIA GONCAVELVES DA CRUZ',  // Nome do motorista
    $CPF = '59393025991'                      // CPF do motorista
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
