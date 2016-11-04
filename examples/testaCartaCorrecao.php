<?php

/**
 * Arquivo de teste para transmissão de Carta de Correção
 * @copyright  Copyright (c) 2008-2015
 * @license    http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author     Maison K. Sakamoto <maison.sakamoto at gmail dot com>
 * @link       https://github.com/nfephp-org/sped-cte for the canonical source repository
 */
error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once '../bootstrap.php';

use NFePHP\CTe\Tools;

$cteTools = new Tools('../config/config.json');

$aResposta = array();

// Código do órgão de recepção do Evento.
// Utilizar a Tabela do IBGE extendida,
// utilizar 90 para identificar SUFRAMA
$siglaUF = $cteTools->aConfig['siglaUF'];

// Chave de Acesso do CT-e vinculado ao Evento
$chave = '41160981450900000132570020000000601000000106';

// Sequencial do evento para o mesmo tipo de
// evento. Para maioria dos eventos será 1,
// nos casos em que possa existir mais de um evento
// o autor do evento deve numerar de forma sequencial.
$nSeqEvento = '1';

//As informações da correção devem ser em array
//O registro de uma nova Carta de Correção substitui a Carta de Correção anterior, assim a nova
//Carta de Correção deve conter todas as correções a serem consideradas.
$infCorrecao[] = array(
    "grupoAlterado" => 'ide', // Indicar o grupo de informações que pertence
    "campoAlterado" => 'natOp', // Nome do campo modificado do CT-e Original.
    "valorAlterado" => 'teste de carta de correcao', // Valor correspondente à alteração.
    "nroItemAlterado" => '1'// Preencher com o indice do item alterado caso a alteração ocorra em uma lista.
);

// Transmite o arquivo
$cteTools->sefazCartaCorrecao(
    $chave,
    $tpAmb,
    $nSeqEvento,
    $infCorrecao,
    $aResposta
);

echo '<pre>';
//echo htmlspecialchars($cteTools->soapDebug);
print_r($aResposta);
echo "</pre>";
