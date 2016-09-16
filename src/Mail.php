<?php

namespace NFePHP\CTe;

/**
 * Classe para envio dos emails aos interessados
 *
 * @category  NFePHP
 * @package   NFePHP\CTe\MailCTe
 * @copyright Copyright (c) 2008-2015
 * @license   http://www.gnu.org/licenses/lesser.html LGPL v3
 * @author    Roberto L. Machado <linux.rlm at gmail dot com>
 * @link      http://github.com/nfephp-org/nfephp for the canonical source repository
 */

use NFePHP\Common\Dom\Dom;
use NFePHP\Common\DateTime\DateTime;
use NFePHP\Common\Base\BaseMail;
use NFePHP\Common\Exception;
use Html2Text\Html2Text;
use \DOMDocument;

class Mail extends BaseMail
{
    public $error = '';
    protected $msgHtml = '';
    protected $msgTxt = '';
    protected $aMail = array();
    
    /**
     * envia
     *
     * @param  string $pathFile
     * @param  array  $aMail
     * @param  bool   $comPdf
     * @param  string $pathPdf
     * @return bool
     */
    public function envia($pathFile = '', $aMail = array(), $comPdf = false, $pathPdf = '')
    {
        if ($comPdf && $pathPdf != '') {
            $this->addAttachment($pathPdf, '');
        }
        $assunto = $this->zMontaMessagem($pathFile);
        //cria o anexo do xml
        $this->addAttachment($pathFile, '');
        //constroi a mensagem
        $this->buildMessage($this->msgHtml, $this->msgTxt);
        if (sizeof($aMail)) {
            // Se for informado um ou mais e-mails no $aMail, utiliza eles
            $this->aMail = $aMail;
        } elseif (!sizeof($this->aMail)) {
            // Caso não seja informado nenhum e-mail e não tenha sido recuperado qualquer e-mail do xml
            throw new Exception\RuntimeException('Nenhum e-mail informado ou recuperado do XML.');
        }
        $err = $this->sendMail($assunto, $this->aMail);
        if ($err === true) {
            return true;
        } else {
            $this->error = $err;
            return false;
        }
        return true;
    }
    
    /**
     * zMontaMessagem
     *
     * @param string $pathFile
     */
    protected function zMontaMessagem($pathFile)
    {
        $dom = new Dom();
        $dom->loadXMLFile($pathFile);
        $infCTe = $dom->getNode('infCte', 0);
        $ide = $infCTe->getElementsByTagName('ide')->item(0);
        $dest = $infCTe->getElementsByTagName('dest')->item(0);
        $emit = $infCTe->getElementsByTagName('emit')->item(0);
        $vPrest = $infCTe->getElementsByTagName('vPrest')->item(0);
        $razao = $emit->getElementsByTagName('xNome')->item(0)->nodeValue;
        $nCT = $ide->getElementsByTagName('nCT')->item(0)->nodeValue;
        $serie = $ide->getElementsByTagName('serie')->item(0)->nodeValue;
        $xNome = $dest->getElementsByTagName('xNome')->item(0)->nodeValue;
        $dhEmi = ! empty($ide->getElementsByTagName('dhEmi')->item(0)->nodeValue) ?
                $ide->getElementsByTagName('dhEmi')->item(0)->nodeValue :
                $ide->getElementsByTagName('dEmi')->item(0)->nodeValue;
        $data = date('d/m/Y', DateTime::convertSefazTimeToTimestamp($dhEmi));
        $vCT = $vPrest->getElementsByTagName('vTPrest')->item(0)->nodeValue;
        $this->aMail[] = !empty($dest->getElementsByTagName('email')->item(0)->nodeValue) ?
                $dest->getElementsByTagName('email')->item(0)->nodeValue :
                '';
        
        $this->msgHtml = $this->zRenderTemplate($xNome, $data, $nCT, $serie, $vCT, $razao);
        $cHTT = new Html2Text($this->msgHtml);
        $this->msgTxt = $cHTT->getText();
        return "CTe n. $nCT - $razao";
    }
    
    /**
     * zRenderTemplate
     *
     * @param  string $xNome
     * @param  string $data
     * @param  string $nCT
     * @param  string $serie
     * @param  string $vCT
     * @param  string $razao
     * @return string
     */
    protected function zRenderTemplate($xNome, $data, $nCT, $serie, $vCT, $razao)
    {
        $this->zTemplate();
        $temp = $this->template;
        $aSearch = array(
            '{contato}',
            '{data}',
            '{numero}',
            '{serie}',
            '{valor}',
            '{emitente}'
        );
        $aReplace = array(
          $xNome,
          $data,
          $nCT,
          $serie,
          $vCT,
          $razao
        );
        $temp = str_replace($aSearch, $aReplace, $temp);
        return $temp;
    }

    /**
     * zTemplate
     * Seo template estiver vazio cria o basico
     */
    protected function zTemplate()
    {
        if (empty($this->template)) {
            $this->template = "<p><b>Prezados {contato},</b></p>".
                "<p>Você está recebendo o Conhecimento de Transporte Eletrônico emitido em {data} com o número ".
                "{numero}, série {serie} de {emitente}, no valor de R$ {valor}. ".
                "Junto com a mercadoria, você receberá também um DACTE (Documento ".
                "Auxiliar da Conhecimento de Transporte Eletrônico), que acompanha o trânsito das mercadorias.</p>".
                "<p><i>Podemos conceituar a Conhecimento de Transporte Eletrônico como um documento ".
                "de existência apenas digital, emitido e armazenado eletronicamente, ".
                "com o intuito de documentar, para fins fiscais, uma operação de ".
                "circulação de mercadorias, ocorrida entre as partes. Sua validade ".
                "jurídica garantida pela assinatura digital do remetente (garantia ".
                "de autoria e de integridade) e recepção, pelo Fisco, do documento ".
                "eletrônico, antes da ocorrência do Fato Gerador.</i></p>".
                "<p><i>Os registros fiscais e contábeis devem ser feitos, a partir ".
                "do próprio arquivo do CT-e, anexo neste e-mail, ou utilizando o ".
                "DACTE, que representa graficamente a Conhecimento de Transporte Eletrônico. ".
                "A validade e autenticidade deste documento eletrônico pode ser ".
                "verificada no site nacional do projeto (www.cte.fazenda.gov.br), ".
                "através da chave de acesso contida no DACTE.</i></p>".
                "<p><i>Para poder utilizar os dados descritos do DACTE na ".
                "escrituração do CT-e, tanto o contribuinte destinatário, ".
                "como o contribuinte emitente, terão de verificar a validade do CT-e. ".
                "Esta validade está vinculada à efetiva existência do CT-e nos ".
                "arquivos da SEFAZ, e comprovada através da emissão da Autorização de Uso.</i></p>".
                "<p><b>O DACTE não é um conhecimento de transporte, nem substitui um conhecimento de transporte, ".
                "servindo apenas como instrumento auxiliar para consulta do CT-e no ".
                "Ambiente Nacional.</b></p>".
                "<p>Para mais detalhes, consulte: <a href=\"http://www.cte.fazenda.gov.br/\">".
                "www.cte.fazenda.gov.br</a></p>".
                "<br>".
                "<p>Atenciosamente,</p>".
                "<p>{emitente}</p>";
        }
    }
}
