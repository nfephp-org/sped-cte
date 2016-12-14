<?php
/**@category  Teste
 * @package   Spedcteexamples
 * @copyright 2009-2016 NFePHP
 * @name      testaMakeCTe.php
 * @license   http://www.gnu.org/licenses/gpl.html GNU/GPL v.3
 * @link      http://github.com/nfephp-org/sped-cte for the canonical source repository
 * @author    Samuel M. Basso <samuelbasso@gmail.com>
 * Adaptado por Maison K. Sakamoto <maison.sakamoto@gmail.com>
 *
 * Teste para a versão 2.0 do CT-e
 **/
error_reporting(E_ALL);
ini_set('display_errors', 'On');
require_once '../bootstrap.php';

use NFePHP\CTe\Make;
use NFePHP\CTe\Tools;

try {
    $cte = new Make();

    $cteTools = new Tools('../config/config.json');

    $dhEmi = date("Y-m-d\TH:i:s");
    $numeroCTE = rand();
    //
    $chave = $cte->montaChave(
        $cUF = '35',                // Codigo da UF da tabela do IBGE: 41-PR
        $ano = date('y', strtotime($dhEmi)),
        $mes = date('m', strtotime($dhEmi)),
        $cnpj = $cteTools->aConfig['cnpj'],
        $mod = '57',                // Modelo do documento fiscal: 57 para identificação do CT-e
        $serie = '2',               // Serie do CTe
        $numero = $numeroCTE,             // Numero do CTe
        $tpEmis = '1',              // Forma de emissao do CTe: 1-Normal; 4-EPEC pela SVC; 5-Contingência
        $cCT = '10'
    );               // Codigo numerico que compoe a chave de acesso (Codigo aleatorio do emitente, para evitar acessos indevidos ao documento)
    $resp = $cte->infCteTag($chave, $versao = '2.00');
    $cDV = substr($chave, -1);      //Digito Verificador
    $resp = $cte->ideTag(
        $cUF = '35',                // Codigo da UF da tabela do IBGE
        $cCT = '00000010',          // Codigo numerico que compoe a chave de acesso (Codigo aleatorio do emitente, para evitar acessos indevidos ao documento)
        $CFOP = '5353',             // Codigo fiscal de operacoes e prestacoes
        $natOp = substr('Servico de Transporte a Estab Coml', 0, 60), // Natureza da operacao
        $forPag = '0',              // 0-Pago; 1-A pagar; 2-Outros
        $mod = '57',                // Modelo do documento fiscal: 57 para identificação do CT-e
        $serie = '2',               // Serie do CTe
        $nCT = $numeroCTE,                // Numero do CTe
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
        $cMunEnv = '3529401',       // Utilizar a tabela do IBGE. Informar 9999999 para as operações com o exterior.
        $xMunEnv = 'MAUA', // Informar PAIS/Municipio para as operações com o exterior.
        $UFEnv = 'SP',              // Informar 'EX' para operações com o exterior.
        $modal = '01',              // Preencher com:01-Rodoviário; 02-Aéreo; 03-Aquaviário;04-
        $tpServ = '0',              // 0- Normal; 1- Subcontratação; 2- Redespacho; 3- Redespacho Intermediário; 4- Serviço Vinculado a Multimodal
        $cMunIni = '3529401',       // Utilizar a tabela do IBGE. Informar 9999999 para as operações com o exterior.
        $xMunIni = 'MAUA', // Informar 'EXTERIOR' para operações com o exterior.
        $UFIni = 'SP',              // Informar 'EX' para operações com o exterior.
        $cMunFim = '3529401',       // Utilizar a tabela do IBGE. Informar 9999999 para operações com o exterior.
        $xMunFim = 'MAUA',           // Informar 'EXTERIOR' para operações com o exterior.
        $UFFim = 'SP',              // Informar 'EX' para operações com o exterior.
        $retira = '1',              // Indicador se o Recebedor retira no Aeroporto, Filial, Porto ou Estação de Destino? 0-sim; 1-não
        $xDetRetira = '',           // Detalhes do retira
        $dhCont = '',               // Data e Hora da entrada em contingência; no formato AAAAMM-DDTHH:MM:SS
        $xJust = ''                 // Justificativa da entrada em contingência
    );
    $resp = $cte->toma03Tag(
        $toma = '0'                 // Indica o "papel" do tomador: 0-Remetente; 1-Expedidor; 2-Recebedor; 3-Destinatário
    );
//$resp = $cte->toma4Tag(
//    $toma = '4',                        // 4-Outros, informar os dados cadastrais do tomador quando ele for outros
//    $CNPJ = '11509962000197',           // CNPJ
//    $CPF = '',                          // CPF
//    $IE = 'ISENTO',                     // Iscricao estadual
//    $xNome = 'OTIMIZY',                 // Razao social ou Nome
//    $xFant = 'OTIMIZY',                 // Nome fantasia
//    $fone = '5434625522',               // Telefone
//    $email = 'contato@otimizy.com.br'   // email
//);
//$resp = $cte->enderTomaTag(
//    $xLgr = 'Avenida Independência',    // Logradouro
//    $nro = '482',                       // Numero
//    $xCpl = '',                         // COmplemento
//    $xBairro = 'Centro',                // Bairro
//    $cMun = '4308607',                  // Codigo do municipio do IBEGE Informar 9999999 para operações com o exterior.
//    $xMun = 'Garibaldi',                // Nome do município (Informar EXTERIOR para operações com o exterior.
//    $CEP = '95720000',                  // CEP
//    $UF = $cteTools->aConfig['siglaUF'], // Sigla UF (Informar EX para operações com o exterior.)
//    $cPais = '1058',                    // Codigo do país ( Utilizar a tabela do BACEN )
//    $xPais = 'Brasil'                   // Nome do pais
//);
    $resp = $cte->emitTag(
        $CNPJ = $cteTools->aConfig['cnpj'],         // CNPJ do emitente
        $IE = $cteTools->aConfig['ie'],             // Inscricao estadual
        $xNome = $cteTools->aConfig['razaosocial'], // Razao social
        $xFant = $cteTools->aConfig['nomefantasia'] // Nome fantasia
    );
    $resp = $cte->enderEmitTag(
        $xLgr = 'AV. PAPA JOAO XXIII',            // Logradouro
        $nro = '2105',                               // Numero
        $xCpl = 'A',                         // Complemento
        $xBairro = 'VILA NOEMIA',                        // Bairro
        $cMun = '3529401',                          // Código do município (utilizar a tabela do IBGE)
        $xMun = 'MAUA',                        // Nome do municipio
        $CEP = '09370800',                          // CEP
        $UF = $cteTools->aConfig['siglaUF'],        // Sigla UF
        $fone = '1145436311'                        // Fone
    );
    $resp = $cte->remTag(
        $CNPJ = '09514658000196',                                   // CNPJ
        $CPF = '',                                                  // CPF
        $IE = '442224578116',                                         // Inscricao estadual
        $xNome = 'CT-E EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL',  //CT-E EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL    COMERCIAL DE BEBIDAS GRANDE MIX LTDA.
        $Fant = 'COMERCIAL DE BEBIDAS GRANDE MIX LTDA.',                                          // Nome fantasia
        $fone = '1145436311',                                         // Fone
        $email = ''                           // Email
    );
    $resp = $cte->enderRemeTag(
        $xLgr = 'AVENIDA PAPA JOAO XXIII',                                        // Logradouro
        $nro = '2105',                                               // Numero
        $xCpl = '',                                                 // Complemento
        $xBairro = 'VILA NOEMIA',                          // Bairro
        $cMun = '3529401',                                          // Codigo Municipal (Informar 9999999 para operações com o exterior.)
        $xMun = 'MAUA',                                    // Nome do municipio (Informar EXTERIOR para operações com o exterior.)
        $CEP = '09370800',                                          // CEP
        $UF = 'SP',                                                 // Sigla UF (Informar EX para operações com o exterior.)
        $cPais = '1058',                                            // Codigo do pais ( Utilizar a tabela do BACEN )
        $xPais = 'Brasil'                                           // Nome do pais
    );
    $resp = $cte->destTag(
        $CNPJ = '',                                   // CNPJ
        $CPF = '95303049515',                                                  // CPF
        $IE = '',                                       // Inscriao estadual
        $xNome = 'CT-E EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL',    //CT-E EMITIDO EM AMBIENTE DE HOMOLOGACAO - SEM VALOR FISCAL     ERONICE GONCALVES CARDOSO
        $fone = '0974253592',                                       // Fone
        $ISUF = '',                                                 // Inscrição na SUFRAMA
        $email = ''                           // Email
    );
    $resp = $cte->enderDestTag(
        $xLgr = 'AVENIDA VALDEMAR JESUINO DA SILVA',                        // Logradouro
        $nro = '449',                                                // Numero
        $xCpl = '',                                          // COmplemento
        $xBairro = 'JARDIM MARINGA',                               // Bairro
        $cMun = '3529401',                                          // Codigo Municipal (Informar 9999999 para operações com o exterior.)
        $xMun = 'MAUA',                                          // Nome do Municipio (Informar EXTERIOR para operações com o exterior.)
        $CEP = '09341280',                                          // CEP
        $UF = 'SP',                                                 // Sigla UF (Informar EX para operações com o exterior.)
        $cPais = '1058',                                            // Codigo do Pais (Utilizar a tabela do BACEN)
        $xPais = 'Brasil'                                           // Nome do pais
    );
    $resp = $cte->vPrestTag(
        $vTPrest = 6.76,  // Valor total da prestacao do servico
        $vRec = 6.76      // Valor a receber
    );
    $resp = $cte->compTag(
        $xNome = 'FRETE',   // Nome do componente
        $vComp = 6.76  // Valor do componente
    );
    $resp = $cte->icmsTag(
        $cst = '00',        // 00 - Tributacao normal ICMS
        $pRedBC = '',       // Percentual de redução da BC (3 inteiros e 2 decimais)
        $vBC = 6.76,     // Valor da BC do ICMS
        $pICMS = 0.00,        // Alícota do ICMS
        $vICMS = 0.00,    // Valor do ICMS
        $vBCSTRet = '',     // Valor da BC do ICMS ST retido
        $vICMSSTRet = '',   // Valor do ICMS ST retido
        $pICMSSTRet = '',   // Alíquota do ICMS
        $vCred = '',        // Valor do Crédito Outorgado/Presumido
        $vTotTrib = '', // Valor de tributos federais, estaduais e municipais
        $outraUF = false    // ICMS devido à UF de origem da prestação, quando diferente da UF do emitente
    );
    $resp = $cte->infCTeNormTag();              // Grupo de informações do CT-e Normal e Substituto
    $resp = $cte->infCargaTag(
        $vCarga = 75.07,                     // Valor total da carga
        $prodPred = 'Bebidas / Alimentos',  // Produto predominante
        $xOutCat = ''                           // Outras caracteristicas da carga
    );
    $resp = $cte->infQTag(
        $cUnid = '03',                          // Código da Unidade de Medida: ( 00-M3; 01-KG; 02-TON; 03-UNIDADE; 04-LITROS; 05-MMBTU
        $tpMed = 'UNIDADE',                        // Tipo de Medida ( PESO BRUTO, PESO DECLARADO, PESO CUBADO, PESO AFORADO, PESO AFERIDO, LITRAGEM, CAIXAS e etc)
        $qCarga = 28.0000                     // Quantidade (15 posições, sendo 11 inteiras e 4 decimais.)
    );
    $resp = $cte->infQTag(
        $cUnid = '03',                          // Código da Unidade de Medida: ( 00-M3; 01-KG; 02-TON; 03-UNIDADE; 04-LITROS; 05-MMBTU
        $tpMed = 'UNIDADE',                        // Tipo de Medida ( PESO BRUTO, PESO DECLARADO, PESO CUBADO, PESO AFORADO, PESO AFERIDO, LITRAGEM, CAIXAS e etc)
        $qCarga = 23.0000                     // Quantidade (15 posições, sendo 11 inteiras e 4 decimais.)
    );
    $resp = $cte->infDocTag();
//    $resp = $cte->infNFeTag(
//        $pChave = '43160472202112000136550000000010571048440722',   // Chave de acesso da NF-e
//        $PIN = '',                                                  // PIN SUFRAMA
//        $dPrev = '2016-10-30'                                       // Data prevista de entrega
//    );
    $resp = $cte->infNFTag(
        $nRoma      = ''
        , $nPed     = ''
        , $mod      = '01'
        , $serie    = '55'
        , $nDoc     = '687654'
        , $dEmi     = date('Y-m-d', strtotime($dhEmi))
        , $vBC      = '0.00'
        , $vICMS    = '0.00'
        , $vBCST    = '0.00'
        , $vST      = '0.00'
        , $vProd    = '75.07'
        , $vNF      = '75.07'
        , $nCFOP    = '5102'
        , $nPeso    = '28.000'
        , $PIN      = ''
        , $dPrev    = ''
    );
    $resp = $cte->segTag(
        $respSeg = 0,                           // Responsavel pelo seguro (0-Remetente; 1-Expedidor; 2-Recebedor; 3-Destinatário; 4-Emitente do CT-e; 5-Tomador de Serviço)
        $xSeg = '',  // Nomeda da Seguradora
        $nApol = ''  // Numero da Apolice
    );


    $resp = $cte->infModalTag($versaoModal = '2.00');
    $resp = $cte->rodoTag(
        $RNTRC = '11282505',    // Registro Nacional de Transportadores Rodoviários de Carga
        $dPrev = '2016-11-30',  // Data prevista para entrega da carga no recebedor formato ( aaaa-mm-dd )
        $lota = '0',            // Indicador de lotacao ( 0-nao; 1-sim) Será lotação quando houver um único crt por veículo, ou combinação veicular, e por viagem.
        $CIOT = ''
    );

//$resp = $cte->veicTag(
//    $RENAVAM = '172414148',   // RENAVAM do veículo
//    $placa = 'BAR0585',       // Placa do veiculo
//    $tara = '17100',          // Tara em KG
//    $capKG = '24900',         // Capacidade em KG
//    $capM3 = '100',           // Capacidade em M3
//    $tpProp = 'P',            // Tipo de Propriedade de veiculo ( P- Próprio; T- terceiro. Será próprio quando proprietario do veículo for o Emitente do CT-e )
//    $tpVeic = '0',            // Tipo de veículo ( 0-Tração; 1-Reboque )
//    $tpRod = '03',            // Tipo de Rodaddo ( 00 - não aplicável; 01 - Truck; 02 - Toco; 03 - Cavalo Mecânico; 04 - VAN; 05 - Utilitário; 06 - Outros.)
//    $tpCar = '00',            // Tipo de carroceria ( 00 - não aplicável; 01 - Aberta; 02 - Fechada/Baú; 03 - Granelera; 04 - Porta Container; 05 - Sider)
//    $UF = 'PR'                // Sigla UF de licenciamento do veiculo
//);
//$resp = $cte->veicTag(
//    $RENAVAM = '828262659',   // RENAVAM do veículo
//    $placa = 'BAR0186',       // Placa do veiculo
//    $tara = '17100',          // Tara em KG
//    $capKG = '24900',         // Capacidade em KG
//    $capM3 = '100',           // Capacidade em M3
//    $tpProp = 'P',            // Tipo de Propriedade de veiculo ( P- Próprio; T- terceiro. Será próprio quando proprietario do veículo for o Emitente do CT-e )
//    $tpVeic = '0',            // Tipo de veículo ( 0-Tração; 1-Reboque )
//    $tpRod = '00',            // Tipo de Rodaddo ( 00 - não aplicável; 01 - Truck; 02 - Toco; 03 - Cavalo Mecânico; 04 - VAN; 05 - Utilitário; 06 - Outros.)
//    $tpCar = '00',            // Tipo de carroceria ( 00 - não aplicável; 01 - Aberta; 02 - Fechada/Baú; 03 - Granelera; 04 - Porta Container; 05 - Sider)
//    $UF = 'PR'                // Sigla UF de licenciamento do veiculo
//);
//$resp = $cte->motoTag(
//    $xNome = 'JOAO MARIA GONCAVELVES DA CRUZ',  // Nome do motorista
//    $CPF = '59393025991'                      // CPF do motorista
//);

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
            echo 'tag: &lt;' . $err['tag'] . '&gt; ---- ' . $err['desc'] . '<br>';
        }
    }
    $xml = file_get_contents($filename);
    $xml = $cteTools->assina($xml);

    file_put_contents($filename, $xml);

    $aRetorno = array();
    $tpAmb = '2';
    $idLote = '';
    $indSinc = '1';
    $flagZip = false;

    $msg='';
    if (! $cteTools->validarXml($filename) || sizeof($cteTools->erros)) {
        $msg .= "<h3>Algum erro ocorreu.... </h3>";
        foreach ($cteTools->erros as $erro) {
            if (is_array($erro)) {
                foreach ($erro as $err) {
                    $msg .= "$err <br>";
                }
            } else {
                $msg .= "$erro <br>";
            }
        }
        throw new Exception($msg,0);
    }

    $retorno = $cteTools->sefazEnvia($xml, $tpAmb = '2', $idLote, $aRetorno, $indSinc, $flagZip);
//echo '<pre>';
//echo htmlspecialchars($cteTools->soapDebug);
//print_r($aRetorno);
//echo "</pre>";

    $aResposta = array();
    $recibo = $aRetorno['nRec'];
    $retorno = $cteTools->sefazConsultaRecibo($recibo, $tpAmb, $aResposta);
    //
    echo '<pre>';
    echo htmlspecialchars($cteTools->soapDebug);
    print_r($aResposta);
    echo "</pre>";
} catch (Exception $e) {
    echo $e->getMessage();
}