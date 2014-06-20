<?php

require_once('../../library/php-imap/ImapMailbox.php');
require("../../library/include/datlib.inc.php");

$admin_aziend=checkAdmin();

global $gTables;
// IMAP  
define('CEMAIL', gaz_dbi_get_row($gTables['company_config'],'var','cemail'));
define('CPASSWORD', gaz_dbi_get_row($gTables['company_config'],'var','cpassword'));
define('CFILTRO', gaz_dbi_get_row($gTables['company_config'],'var','cfiltro'));
define('CPOPIMAP', gaz_dbi_get_row($gTables['company_config'],'var','cpopimap'));
define('CATTACHMENTS_DIR',  '../../data/files/ricevutesdi');

echo "-----" . gaz_dbi_get_row($gTables['company_config'],'var','cemail');

$mailbox = new ImapMailbox(CPOPIMAP, CEMAIL, CPASSWORD, CATTACHMENTS_DIR, 'utf-8');
$mails = array();

// Get some mail
$mailsIds = $mailbox->searchMailBox(CFILTRO);
if(!$mailsIds) {
	die('Mailbox is empty');
}

//$mailId = reset($mailsIds);
//$mail = $mailbox->getMail($mailId);

//var_dump($mailbox->getMailsInfo($mailsIds));
//var_dump($mailId);
//var_dump($mail);

//$aaa= $mail->getAttachments();
//var_dump($aaa);

$bbb = new IncomingMailAttachment();
$domDoc = new DOMDocument;


foreach($mailsIds as $mailId) {
  $mail = $mailbox->getMail($mailId);
  
  $aaa= $mail->getAttachments();
  $ccc = array_values($aaa);  
  $bbb = $ccc[0];
  
    
     $domDoc->load($bbb->filePath);
    

    $xpath = new DOMXPath($domDoc);	
	$result = $xpath->query("//IdentificativoSdI")->item(0);
	
    print $result->textContent . " ";
	$result = $xpath->query("//NomeFile")->item(0);
	print $result->textContent . " ";
	
	$result = $xpath->query("//ListaErrori/Errore/Descrizione")->item(0);
	if ($result)
     	echo $result->textContent . " ";
	
    echo "<br/>";	
  
  
}


?>