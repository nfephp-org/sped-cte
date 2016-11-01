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
       
// Indicar o grupo de informações que pertence
// o campoAlterado. Ex: ide
$grupoAlterado = 'ide';

// Nome do campo modificado do CT-e Original.
// Ex: natOp
$campoAlterado = 'natOp';

// Valor correspondente à alteração.
$valorAlterado = 'teste de carta de correcao';

// Preencher com o indice do item alterado caso a alteração ocorra em uma lista.
// Por exemplo: Se corrigir uma das NF-e do remetente, 
// esta tag deverá indicar a posição da NF-e alterada na lista.
// OBS: O indice inicia sempre em 01
$nroItemAlterado='01';

// Transmite o arquivo
$cteTools->sefazCartaCorrecao(
    $chave,
    $tpAmb,        
    $nSeqEvento,
    $grupoAlterado,
    $campoAlterado,
    $valorAlterado,
    $nroItemAlterado,
    $aResposta
);

echo '<pre>';
//echo htmlspecialchars($cteTools->soapDebug);
print_r($aResposta);
echo "</pre>";
