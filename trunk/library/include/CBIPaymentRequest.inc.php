<?php

/* $
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2021 - Antonio De Vincentiis Montesilvano (PE)
  (http://www.devincentiis.it)
  <http://gazie.sourceforge.net>
  --------------------------------------------------------------------------
  Questo programma e` free software;   e` lecito redistribuirlo  e/o
  modificarlo secondo i  termini della Licenza Pubblica Generica GNU
  come e` pubblicata dalla Free Software Foundation; o la versione 2
  della licenza o (a propria scelta) una versione successiva.

  Questo programma  e` distribuito nella speranza  che sia utile, ma
  SENZA   ALCUNA GARANZIA; senza  neppure  la  garanzia implicita di
  NEGOZIABILITA` o di  APPLICABILITA` PER UN  PARTICOLARE SCOPO.  Si
  veda la Licenza Pubblica Generica GNU per avere maggiori dettagli.

  Ognuno dovrebbe avere   ricevuto una copia  della Licenza Pubblica
  Generica GNU insieme a   questo programma; in caso  contrario,  si
  scriva   alla   Free  Software Foundation, 51 Franklin Street,
  Fifth Floor Boston, MA 02110-1335 USA Stati Uniti.
  --------------------------------------------------------------------------
 */

class CBIPaymentXMLvars {
    function setPaymentsVars($gTables,$head) {
		// qui setto tutti i valori per l'intestazione 
        $this->azienda = gaz_dbi_get_row($gTables['aziend'], 'codice', $_SESSION['company_id']);
        $this->bank = gaz_dbi_get_row($gTables['clfoco'].' LEFT JOIN '.$gTables['banapp'].' ON '.$gTables['clfoco'].'.banapp = '.$gTables['banapp'].".codice", $gTables['clfoco'].'.codice',$head['bank']);
        $this->OthrId = (intval($this->azienda['pariva'])>=100)?$this->azienda['pariva']:$this->azienda['codfis'];
		$this->CreDtTm = date('Y-m-d\TH:i:s');
        $this->MsgId = dechex(rand(100,999).date('siHdmY')).'-';
		$this->CtgyPurpCd = (isset($head['CtgyPurpCd']) && strlen($head['CtgyPurpCd']) == 4) ? $head['CtgyPurpCd'] : 'OTHR';
		// INTC  IntraCompanyPayment  Intra-company payment
		// INTE  Interest  Payment of interest. 
		// PENS  PensionPayment  Payment of pension. 
		// SALA  SalaryPayment  Payment of salaries. 
		// SSBE  SocialSecurityBenefit  Payment of child benefit, family allowance. 
		// SUPP  SupplierPayment  Payment to a supplier. 
		// TAXS  TaxPayment  Payment of taxes. 
		// TREA  TreasuryPayment  Treasury transaction
		// OTHR  Other
		$this->FileName = (isset($head['FileName']) && strlen($head['FileName']) >= 16) ? $head['FileName'] : 'XMLCBIpay'.date('Ymdhis');
    }
}

function create_XML_CBIPayment($gTables,$head,$data) {
	// in $data dovrò passare tutti i dati necessari per la creazione degli elementi <CdtTrfTxInf>
	// le chiavi sono: EndToEndId (id univoco) ,InstdAmt (importo), Nm (descrizione creditore), IBAN (iban accredito), Ustrd (descrizione debito pagato) ognuno creerà il relativo elemento dentro <CdtTrfTxInf>
    $CtrlSum = 0.00;
    $NbOfTxs = 1;
    $XMLvars = new CBIPaymentXMLvars();
    $domDoc = new DOMDocument;
	$domDoc->preserveWhiteSpace = false;
	$domDoc->formatOutput = true;
    $XMLvars->setPaymentsVars($gTables,$head);
	$domDoc->load("../../library/include/template_CBIPaymentRequest.xml");
	$xpath = new DOMXPath($domDoc);
	$results = $xpath->query("//GrpHdr/MsgId")->item(0);
	$attrVal = $domDoc->createTextNode($XMLvars->MsgId);
	$results->appendChild($attrVal);
	$results = $xpath->query("//GrpHdr/CreDtTm")->item(0);
	$attrVal = $domDoc->createTextNode($XMLvars->CreDtTm);
	$results->appendChild($attrVal);
	$results = $xpath->query("//GrpHdr/InitgPty/Nm")->item(0);
	$attrVal = $domDoc->createTextNode($XMLvars->bank['descri']);
	$results->appendChild($attrVal);
	$results = $xpath->query("//GrpHdr/InitgPty/Id/OrgId/Othr/Id")->item(0);
	$attrVal = $domDoc->createTextNode($XMLvars->bank['cuc_code']);
	$results->appendChild($attrVal);
	$results = $xpath->query("//PmtInf/PmtInfId")->item(0);
	$attrVal = $domDoc->createTextNode($XMLvars->MsgId);
	$results->appendChild($attrVal);
	$results = $xpath->query("//PmtInf/ReqdExctnDt")->item(0);
	$attrVal = $domDoc->createTextNode(substr($XMLvars->CreDtTm,0,10));
	$results->appendChild($attrVal);
	$results = $xpath->query("//PmtInf/Dbtr/Nm")->item(0);
	$attrVal = $domDoc->createTextNode($XMLvars->bank['descri']);
	$results->appendChild($attrVal);
	$results = $xpath->query("//PmtInf/Dbtr/Id/OrgId/Othr/Id")->item(0);
	$attrVal = $domDoc->createTextNode($XMLvars->OthrId);
	$results->appendChild($attrVal);
	$results = $xpath->query("//PmtInf/DbtrAcct/Id/IBAN")->item(0);
	$attrVal = $domDoc->createTextNode($XMLvars->bank['iban']);
	$results->appendChild($attrVal);
	$results = $xpath->query("//PmtInf/DbtrAgt/FinInstnId/ClrSysMmbId/MmbId")->item(0);
	$attrVal = $domDoc->createTextNode(str_pad($XMLvars->bank['codabi'],5,'0',STR_PAD_LEFT));
	$results->appendChild($attrVal);
	// creo gli elementi dei singoli bonifici
	foreach($data as $v){
		$PmtInf = $xpath->query("//PmtInf")->item(0);
        $el = $domDoc->createElement("CdtTrfTxInf", "");
            $el1 = $domDoc->createElement("PmtId", "");
				$el2 = $domDoc->createElement("InstrId", $NbOfTxs);
				$el1->appendChild($el2);
				$el2 = $domDoc->createElement("EndToEndId", $XMLvars->MsgId.$NbOfTxs);
				$el1->appendChild($el2);
			$el->appendChild($el1);
            $el1 = $domDoc->createElement("PmtTpInf", "");
				$el2 = $domDoc->createElement("CtgyPurp", "");
					$el3 = $domDoc->createElement("Cd", $XMLvars->CtgyPurpCd);
					$el2->appendChild($el3);
				$el1->appendChild($el2);
			$el->appendChild($el1);
            $el1 = $domDoc->createElement("Amt", "");
				$el2 = $domDoc->createElement("InstdAmt", $v['InstdAmt']);
				$newel2 = $el1->appendChild($el2);
				$newel2->setAttribute("Ccy", "EUR");
			$el->appendChild($el1);
            $el1 = $domDoc->createElement("Cdtr", "");
				$el2 = $domDoc->createElement("Nm", $v['Nm']);
				$el1->appendChild($el2);
			$el->appendChild($el1);
            $el1 = $domDoc->createElement("CdtrAcct", "");
				$el2 = $domDoc->createElement("Id", "");
					$el3 = $domDoc->createElement("IBAN", $v['IBAN']);
					$el2->appendChild($el3);
				$el1->appendChild($el2);
			$el->appendChild($el1);
            $el1 = $domDoc->createElement("RmtInf", "");
				$el2 = $domDoc->createElement("Ustrd", $v['Ustrd']);
				$el1->appendChild($el2);
			$el->appendChild($el1);
		$PmtInf->appendChild($el);
		$NbOfTxs++;	
        $CtrlSum += $v['InstdAmt'];
	}
	$results = $xpath->query("//GrpHdr/NbOfTxs")->item(0);
	$attrVal = $domDoc->createTextNode($NbOfTxs-1);
	$results->appendChild($attrVal);
	$results = $xpath->query("//GrpHdr/CtrlSum")->item(0);
	$attrVal = $domDoc->createTextNode(number_format(round($CtrlSum,2),2,'.',''));
	$results->appendChild($attrVal);

	header("Content-type: text/plain");
	header("Content-Disposition: attachment; filename=".$XMLvars->FileName.".xml");
	print $domDoc->saveXML();
}
?>
