<?php
/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2018 - Antonio De Vincentiis Montesilvano (PE)
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
require("../../library/include/datlib.inc.php");
$admin_aziend = checkAdmin();
$msg = array('err' => array(), 'war' => array());
$toDo = 'upload';
$f_ex=false; // visualizza file
function removeSignature($string, $filename) {
    $string = substr($string, strpos($string, '<?xml '));
    preg_match_all('/<\/.+?>/', $string, $matches, PREG_OFFSET_CAPTURE);
    $lastMatch = end($matches[0]);
	// trovo l'ultimo carattere del tag di chiusura per eliminare la coda
	$f_end = $lastMatch[1]+strlen($lastMatch[0]);
    $string = substr($string, 0, $f_end);
	// elimino le sequenze di caratteri aggiunti dalla firma (ancora da testare approfonditamente)
	$string = preg_replace ('/[\x{0004}]{1}[\x{0082}]{1}[\x{0002}\x{0003}\x{0004}]{1}[\s\S]{1}/i', '', $string);
	return preg_replace ('/[\x{0004}]{1}[\x{0081}]{1}[\s\S]{1}/i', '', $string);
}

function getLastProtocol($type, $year, $sezione) {  
	/* 	questa funzione trova l'ultimo numero di protocollo 
	*	controllando sia l'archivio documenti che il registro IVA acquisti 
	*/
	global $gTables;                      
    $rs_ultimo_tesdoc = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datfat) = ".$year." AND tipdoc LIKE '" . substr($type, 0, 2) . "_' AND seziva = $sezione", "protoc DESC", 0, 1);
    $ultimo_tesdoc = gaz_dbi_fetch_array($rs_ultimo_tesdoc);
    $rs_ultimo_tesmov = gaz_dbi_dyn_query("*", $gTables['tesmov'], "YEAR(datreg) = ".$year." AND regiva = 6 AND seziva = $sezione", "protoc DESC", 0, 1);
    $ultimo_tesmov = gaz_dbi_fetch_array($rs_ultimo_tesmov);
    $lastProtocol = 0;
    if ($ultimo_tesdoc) {
        $lastProtocol = $ultimo_tesdoc['protoc'];
    }
    if ($ultimo_tesmov) {
        if ($ultimo_tesmov['protoc'] > $lastProtocol) {
            $lastProtocol = $ultimo_tesmov['protoc'];
        }
    }
    return $lastProtocol + 1;
}

if (!isset($_POST['fattura_elettronica_original_name'])) { // primo accesso nessun upload
	$form['fattura_elettronica_original_name'] = '';
} else { // accessi successivi  

	$form['fattura_elettronica_original_name'] = filter_var($_POST['fattura_elettronica_original_name'], FILTER_SANITIZE_STRING);
	if (!isset($_POST['datreg'])){
		$form['datreg'] = date("d/m/Y");
	} else {
		$form['datreg'] = substr($_POST['datreg'],0,10);
	}
	if (isset($_POST['Submit_file'])) { // conferma invio upload file
        if (!empty($_FILES['userfile']['name'])) {
            if (!( $_FILES['userfile']['type'] == "application/pkcs7-mime" || $_FILES['userfile']['type'] == "text/xml")) {
				$msg['err'][] = 'filmim';
			} else {
                if (move_uploaded_file($_FILES['userfile']['tmp_name'], '../../data/files/' . $admin_aziend['codice'] . '/' . $_FILES['userfile']['name'])) { // nessun errore
					$form['fattura_elettronica_original_name'] = $_FILES['userfile']['name'];
				} else { // no upload
					$msg['err'][] = 'no_upload';
				}
			}
		} else {
			$msg['err'][] = 'no_upload';
		}
	} else if (isset($_POST['Submit_form'])) { // ho  confermato l'inserimento
		$form['pagame'] = intval($_POST['pagame']);
        if ($form['pagame'] <= 0 ) {  // ma non ho selezionato il pagamento
			$msg['err'][] = 'no_pagame';
		}
	} else if (isset($_POST['Download'])) { // faccio il download dell'allegato
		$name = filter_var($_POST['Download'], FILTER_SANITIZE_STRING);
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment;  filename="'.$name.'"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header("Content-Length: " . filesize('../../data/files/tmp/'.$name));
		readfile('../../data/files/tmp/'.$name);
		exit;
	}

	$tesdoc = gaz_dbi_get_row($gTables['tesdoc'], 'fattura_elettronica_original_name', $form["fattura_elettronica_original_name"]);
	if ($tesdoc && 	!empty($form['fattura_elettronica_original_name'])) { // c'è anche sul database, è una modifica
		$toDo = 'update';
		$form['datreg'] = gaz_format_date($tesdoc['datfat'], false, false);
		$msg['err'][] = 'file_exists';
	} elseif (!empty($form['fattura_elettronica_original_name'])) { // non c'è sul database è un inserimento
		$toDo = 'insert';
		// INIZIO acquisizione e pulizia file xml o p7m
		$file_name = '../../data/files/' . $admin_aziend['codice'] . '/' . $form['fattura_elettronica_original_name'];
		$p7mContent = @file_get_contents($file_name);
		$invoiceContent = removeSignature($p7mContent,$file_name);
		$doc = new DOMDocument;
		$doc->preserveWhiteSpace = false;
		$doc->formatOutput = true;
		$doc->loadXML(utf8_encode($invoiceContent));
		$xpath = new DOMXpath($doc);
		$f_ex=true;
	} else {
		$toDo = 'upload';
	}

	// definisco l'array dei righi 
	$form['rows'] = array();

	$anagra_with_same_pi = false; // sarà true se è una anagrafica esistente ma non è un fornitore sul piano dei conti 
		
 	
	if ($f_ex) { // non ho errori relativi di file, ne faccio altri controlli sul contenuto del file
		
		// INIZIO CONTROLLI CORRETTEZZA FILE
		$val_err = libxml_get_errors(); // se l'xml è valido restituisce 1
		libxml_clear_errors();
		if (empty($val_err)){
			if ($doc->getElementsByTagName("FatturaElettronicaHeader")->length < 1) { // non esiste il nodo <FatturaElettronicaHeader>
				$msg['err'][] = 'invalid_fae';
				$f_ex=false; // non è visualizzabile
			} else if (@$xpath->query("//FatturaElettronicaHeader/CessionarioCommittente/DatiAnagrafici/IdFiscaleIVA/IdCodice")->item(0)->nodeValue <> $admin_aziend['pariva'] ) { // la partita IVA del cliente non coincide con la mia 
				$msg['err'][] = 'not_mine';
			} else {
				// controllo se ho il fornitore in archivio
				$form['partner_cost']=$admin_aziend['impacq']; 
				$form['partner_vat']=$admin_aziend['preeminent_vat']; 
				$form['pariva'] = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdCodice")->item(0)->nodeValue;
				$anagrafica = new Anagrafica();
                $partner_with_same_pi = $anagrafica->queryPartners('*', "codice BETWEEN " . $admin_aziend['masfor'] . "000000 AND " . $admin_aziend['masfor'] . "999999 AND pariva = '" . $form['pariva']. "'", "pariva DESC", 0, 1);
                if ($partner_with_same_pi) { // ho già il fornitore sul piano dei conti
					$form['clfoco'] = $partner_with_same_pi[0]['codice'];
					if ($partner_with_same_pi[0]['cosric']>100000000) { // ho un costo legato al fornitore 
						$form['partner_cost'] = $partner_with_same_pi[0]['cosric']; // costo legato al fornitore
					}				
					$form['pagame'] = $partner_with_same_pi[0]['codpag']; // condizione di pagamento
					if ( $partner_with_same_pi[0]['aliiva'] > 0 ){
						$form['partner_vat'] = $partner_with_same_pi[0]['aliiva']; 
					}
                } else { // se non ho già un fornitore sul piano dei conti provo a vedere nelle anagrafiche
                    $rs_anagra_with_same_pi = gaz_dbi_query_anagra(array("*"), $gTables['anagra'], array("pariva" => "='" . $form['pariva'] . "'"), array("pariva" => "DESC"), 0, 1);
                    $anagra_with_same_pi = gaz_dbi_fetch_array($rs_anagra_with_same_pi);
                    if ($anagra_with_same_pi) { // c'è già un'anagrafica con la stessa PI non serve reinserirlo ma dovrò metterlo sul piano dei conti
						$msg['war'][] = 'no_suppl';
                    } else { // non c'è nemmeno nelle anagrafiche allora attingerò i dati da questa fattura
						$msg['war'][] = 'no_anagr';
						
					}
                }
			}
		} else {
			$msg['err'][] = 'invalid_xml';
			$f_ex=false; // non è visualizzabile
		}
		// FINE CONTROLLI SU FILE

	}

	if ($f_ex) { // non ho errori  vincolanti sul file posso proporre la visualizzazione
		// INIZIO creazione array dei righi con la stessa nomenclatura usata sulla tabella rigdoc
		$DettaglioLinee = $doc->getElementsByTagName('DettaglioLinee');
		foreach ($DettaglioLinee as $item) {
			$nl=$item->getElementsByTagName('NumeroLinea')->item(0)->nodeValue;
			if ($item->getElementsByTagName("CodiceTipo")->length >= 1) {
				$form['rows'][$nl]['codart'] = trim($item->getElementsByTagName('CodiceTipo')->item(0)->nodeValue).'_'.trim($item->getElementsByTagName('CodiceValore')->item(0)->nodeValue); 
			} else {
				$form['rows'][$nl]['codart'] = ($item->getElementsByTagName("CodiceArticolo")->length >= 1 ? $item->getElementsByTagName('CodiceArticolo')->item(0)->nodeValue : '' );
			}
			$form['rows'][$nl]['descri'] = $item->getElementsByTagName('Descrizione')->item(0)->nodeValue; 
			if ($item->getElementsByTagName("Quantita")->length >= 1) {
				$form['rows'][$nl]['quanti'] = $item->getElementsByTagName('Quantita')->item(0)->nodeValue; 
				$form['rows'][$nl]['tiprig'] = 0;
			} else {
				$form['rows'][$nl]['quanti'] = '';
				$form['rows'][$nl]['tiprig'] = 1; // rigo forfait
			}
			$form['rows'][$nl]['unimis'] =  ($item->getElementsByTagName("UnitaMisura")->length >= 1 ? $item->getElementsByTagName('UnitaMisura')->item(0)->nodeValue :	'');
			$form['rows'][$nl]['prelis'] = $item->getElementsByTagName('PrezzoUnitario')->item(0)->nodeValue; 
			if ($item->getElementsByTagName("Tipo")->length >= 1) { // ho uno sconto/maggiorazione
				$form['rows'][$nl]['sconto'] = ($item->getElementsByTagName('Percentuale')->item(0)->nodeValue == 'S' ? -$item->getElementsByTagName('Percentuale')->item(0)->nodeValue : $item->getElementsByTagName('Percentuale')->item(0)->nodeValue); 
			} else {
				$form['rows'][$nl]['sconto'] = '';
			}
			$form['rows'][$nl]['pervat'] = $item->getElementsByTagName('AliquotaIVA')->item(0)->nodeValue;
			$post_nl = $nl-1;
			if (empty($_FILES['userfile']['name'])) { // l'upload del file è già avvenuto e sono nei refresh successivi quindi riprendo i valori scelti e postati dall'utente
				$form['codric_'.$post_nl] = intval($_POST['codric_'.$post_nl]);
				$form['codvat_'.$post_nl] = intval($_POST['codvat_'.$post_nl]);
			} else { // al primo accesso dopo l'upload del file propongo dei costi e delle aliquote in base a quanto trovato sul database 
				$form['codric_'.$post_nl] = $form['partner_cost'];
				if (preg_match('/TRASP/i',strtoupper($form['rows'][$nl]['descri']))) { // se sulla descrizione ho un trasporto lo propongo come costo d'acquisto
					$form['codric_'.$post_nl] = $admin_aziend['cost_tra'];
				}
				$expect_vat = gaz_dbi_get_row($gTables['aliiva'], 'codice', $form['partner_vat']);
				// analizzo le possibilità 
				if ( $expect_vat['aliquo'] == $form['rows'][$nl]['pervat']) { // coincide con le aspettative
					$form['codvat_'.$post_nl] = $expect_vat['codice'];
				} else { // non è quella che mi aspettavo allora provo a trovarne una tra quelle con la stessa aliquota
					$form['codvat_'.$post_nl] = 'non trovata';
				}
			}
		}

		// ricavo l'allegato, e se presente metterò un bottone per permettere il download
		$nf = $doc->getElementsByTagName('NomeAttachment')->item(0);
		if ($nf){
			$name_file = $nf->textContent;
			$att = $doc->getElementsByTagName('Attachment')->item(0);
			$base64 = $att->textContent;
			$bin = base64_decode($base64);
			file_put_contents('../../data/files/tmp/'.$name_file, $bin);
		}

		if (isset($_POST['Submit_form']) && count($msg['err'])==0) { // confermo le scelte sul form, inserisco i dati sul db ma solo se non ho errori
			if (!$anagra_with_same_pi && !$partner_with_same_pi) { // non ho nulla: devo inserire tutto (anagrafica e fornitore) basandomi sul pagamento e sui conti di costo scelti dall'utente
				$new_partner = array_merge(gaz_dbi_fields('clfoco'), gaz_dbi_fields('anagra'));
				$new_partner['codpag'] = $form['pagame'];
				$new_partner['sexper'] = 'G';
				// setto le colonne in base ai dati di questa fattura elettronica
				$new_partner['pariva'] = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdCodice")->item(0)->nodeValue;
				if (@$xpath->query("//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/CodiceFiscale")->item(0)){
					$new_partner['codfis'] = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/CodiceFiscale")->item(0)->nodeValue;
					// ho un codice fiscale posso vedere se è una persona fisica e di quale sesso
					preg_match('/^[a-z]{6}[0-9]{2}[a-z]([0-9]{2})[a-z][0-9]{3}[a-z]$/i',trim($new_partner['codfis']),$match);
					if (count($match)>1 && $match[1] > 40 ){  // è un codice fiscale femminile
						$new_partner['sexper'] = 'F';
					} else { // maschio
						$new_partner['sexper'] = 'M';
					}
				}
				if (@$xpath->query("//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Nome")->item(0)){
					$new_partner['legrap_pf_nome'] = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Nome")->item(0)->nodeValue;
					$new_partner['legrap_pf_cognome'] = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Cognome")->item(0)->nodeValue;
					$new_partner['descri'] = $new_partner['legrap_pf_cognome']. ' '.$new_partner['legrap_pf_nome'];
					if (strlen($new_partner['descri'])>50){
						$new_partner['ragso1'] = $new_partner['legrap_pf_cognome'];
						$new_partner['ragso2'] = $new_partner['legrap_pf_nome'];
					} else {
						$new_partner['ragso1'] = $new_partner['descri'];							
					}
				}
				if (@$xpath->query("//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Denominazione")->item(0)){
					$new_partner['descri'] = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Denominazione")->item(0)->nodeValue;
					if (strlen($new_partner['descri'])>50){
						$new_partner['ragso1'] = substr($new_partner['descri'],0,50);
						$new_partner['ragso2'] = substr($new_partner['descri'],50,100);
					} else {
						$new_partner['ragso1'] = $new_partner['descri'];							
					}
				}
				$new_partner['indspe'] = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Sede/Indirizzo")->item(0)->nodeValue;
				if (@$xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Sede/NumeroCivico")->item(0)){
					$new_partner['indspe'] .= ', '.$xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Sede/NumeroCivico")->item(0)->nodeValue;
				}
				$new_partner['capspe'] = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Sede/CAP")->item(0)->nodeValue;
				$new_partner['citspe'] = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Sede/Comune")->item(0)->nodeValue;
				if (@$xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Sede/Provincia")->item(0)){
					$new_partner['prospe'] = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Sede/Provincia")->item(0)->nodeValue;
				}
				$new_partner['country'] = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Sede/Nazione")->item(0)->nodeValue;
				$new_partner['counas'] = $new_partner['country'];
				$new_partner['id_currency'] =1;
				$new_partner['id_language'] =1;
				if (@$xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Contatti/Telefono")->item(0)){
					$new_partner['telefo'] = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Contatti/Telefono")->item(0)->nodeValue;
				}
				if (@$xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Contatti/Fax")->item(0)){
					$new_partner['fax'] = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Contatti/Fax")->item(0)->nodeValue;
				}
				if (@$xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Contatti/Email")->item(0)){
					$new_partner['e_mail'] = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Contatti/Email")->item(0)->nodeValue;
				}
				// trovo l'ultimo codice disponibile sul piano dei conti
				$rs_last_partner = gaz_dbi_dyn_query("*", $gTables['clfoco'], 'codice BETWEEN ' . $admin_aziend['masfor'] . '000001 AND ' . $admin_aziend['masfor'] . '999999', "codice DESC", 0, 1);
				$last_partner = gaz_dbi_fetch_array($rs_last_partner);
				$new_partner['codice'] =$last_partner['codice']+1;
				// inserisco il partner
				$anagrafica->insertPartner($new_partner);
				$form['clfoco']=$new_partner['codice'];
			} else if ($anagra_with_same_pi) { // devo inserire il fornitore, ho già l'anagrafica 
				$anagra_with_same_pi['id_anagra']=$anagra_with_same_pi['id'];
                $form['clfoco'] = $anagrafica->anagra_to_clfoco($anagra_with_same_pi, $admin_aziend['masfor'], $form['pagame']);
			}
			$form['tipdoc'] = 'AFA'; 
			$form['seziva'] = 1; 
			$form['protoc']=getLastProtocol($form['tipdoc'],substr($form['datreg'],-4),$form['seziva']);
			$form['numfat']= $xpath->query("//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Numero")->item(0)->nodeValue;
			$form['datfat']= $xpath->query("//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Data")->item(0)->nodeValue;
			$form['fattura_elettronica_original_content'] = utf8_encode($invoiceContent);
			$form['datreg']=gaz_format_date($form['datreg'],true);
            tesdocInsert($form);
            //recupero l'id assegnato dall'inserimento
            $ultimo_id = gaz_dbi_last_id();
            foreach ($form['rows'] as $i => $v) { // inserisco i righi 
                $form['rows'][$i]['id_tes'] = $ultimo_id;
				// i righi postati hanno un indice diverso
				$post_nl=$i-1;
				$form['rows'][$i]['codric'] = intval($_POST['codric_'.$post_nl]);
				$form['rows'][$i]['codvat'] = intval($_POST['codvat_'.$post_nl]);
                rigdocInsert($form['rows'][$i]);
			}
            header("Location: report_docacq.php");
			exit;
		} else { // non ho confermato, sono alla prima entrata dopo l'upload del file
			if (!isset($form['pagame'])){
				$form['pagame']=0;
			}
		}
	}
}

require("../../library/include/header.php");
$script_transl = HeadMain();
$gForm = new acquisForm();
?>
<script type="text/javascript">
    $(function () {
        $("#datreg").datepicker({showButtonPanel: true, showOtherMonths: true, selectOtherMonths: true});
        $("#datreg").change(function () {
            this.form.submit();
        });
    });
</script>
<form method="POST" name="form" enctype="multipart/form-data" id="add-invoice">
    <input type="hidden" name="fattura_elettronica_original_name" value="<?php echo $form['fattura_elettronica_original_name']; ?>">
<?php
	// INIZIO form che permetterà all'utente di interagire per (es.) imputare i vari costi al piano dei conti (contabilità) ed anche le eventuali merci al magazzino
    if (count($msg['err']) > 0) { // ho un errore
        $gForm->gazHeadMessage($msg['err'], $script_transl['err'], 'err');
    }
    if (count($msg['war']) > 0) { // ho un alert
        $gForm->gazHeadMessage($msg['war'], $script_transl['war'], 'war');
    }
if ($toDo=='insert' || $toDo=='update' ) {
	if ($f_ex){
 ?>    
<div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12"><?php echo $script_transl['head_text1']. $form['fattura_elettronica_original_name'] .$script_transl['head_text2']; ?>
            </div>                    
        </div> <!-- chiude row  -->
    </div>                    
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12 col-md-6 col-lg-6">
                <div class="form-group">
                    <label for="datreg" class="col-sm-6 control-label"><?php echo $script_transl['datreg']; ?></label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="datreg" name="datreg" value="<?php echo $form['datreg']; ?>">
                    </div>
                </div>
            </div>                    
            <div class="col-sm-12 col-md-6 col-lg-6">
                <div class="form-group">
                    <label for="pagame" class="col-sm-4 control-label" ><?php echo $script_transl['pagame']; ?></label>
                    <div>
                        <?php
                        $select_pagame = new selectpagame("pagame");
                        $select_pagame->addSelected($form["pagame"]);
                        $select_pagame->output(false, "col-sm-8 small");
                        ?>                
                    </div>
                </div>
            </div>                    
        </div> <!-- chiude row  -->
    </div>
</div>                    
<?php		
		foreach ($form['rows'] as $k => $v) {
			$k--;
            $codric_dropdown = $gForm->selectAccount('codric_'.$k, $form['codric_'.$k], array('sub',1,3), '', false, "col-sm-8 small",'style="max-width: 350px;"', false, true);
			$codvat_dropdown = $gForm->selectFromDB('aliiva', 'codvat_'.$k, 'codice', $form['codvat_'.$k], 'aliquo', true, '-', 'descri', '', 'col-sm-8 small', null, 'style="max-width: 350px;"', false, true);            
			// creo l'array da passare alla funzione per la creazione della tabella responsive
            $resprow[$k] = array(
                array('head' => $script_transl["nrow"], 'class' => '',
                    'value' => $k+1),
                array('head' => $script_transl["codart"], 'class' => '',
                    'value' => $v['codart']),
                array('head' => $script_transl["descri"], 'class' => '',
                    'value' => $v['descri']),
                array('head' => $script_transl["unimis"], 'class' => '',
                    'value' => $v['unimis']),
                array('head' => $script_transl["quanti"], 'class' => 'text-right numeric',
                    'value' => $v['quanti']),
                array('head' => $script_transl["prezzo"], 'class' => 'text-right numeric',
                    'value' => $v['prelis']),
                array('head' => $script_transl["sconto"], 'class' => 'text-right numeric',
                    'value' => $v['sconto']),
                array('head' => $script_transl["amount"], 'class' => 'text-right numeric', 
					'value' => '', 'type' => ''),
                array('head' => $script_transl["tax"], 'class' => 'text-center numeric', 
					'value' => $codvat_dropdown, 'type' => ''),
                array('head' => '%', 'class' => 'text-center numeric', 
					'value' => $v['pervat'], 'type' => ''),
                array('head' => $script_transl["conto"], 'class' => 'text-center numeric', 
					'value' => $codric_dropdown, 'type' => '')
            );
		}
		$gForm->gazResponsiveTable($resprow, 'gaz-responsive-table');
?>	   <div class="col-sm-6">
<?php			
		if ($nf){
?>		
		Allegato: <input name="Download" type="submit" class="btn btn-default" value="<?php echo $name_file; ?>" />
<?php 
		} 
?>
	   </div>		   
	   <div class="col-sm-6 text-right">
		<input name="Submit_form" type="submit" class="btn btn-warning" value="<?php echo $script_transl['submit']; ?>" />
	   </div>		   
</form>
<?php			
	}
	if ($f_ex) {	// visualizzo la fattura elettronica in calce
		$xslDoc = new DOMDocument();
		$xslDoc->load("../../library/include/fatturaordinaria_v1.2.1.xsl");
		$xslt = new XSLTProcessor();
		$xslt->importStylesheet($xslDoc);
		require("../../library/include/footer.php");
		echo $xslt->transformToXML($doc);
	}
} else { // all'inizio chiedo l'upload di un file xml o p7m 
?>
<div class="panel panel-default gaz-table-form">
	<div class="container-fluid">
       <div class="row">
           <div class="col-md-12">
               <div class="form-group">
                   <label for="image" class="col-sm-4 control-label">Seleziona il file xml o p7m</label>
                   <div class="col-sm-8">File: <input type="file" accept=".xml,.p7m" name="userfile" />
				   </div>
               </div>
           </div>
       </div><!-- chiude row  -->
	   <div class="col-sm-12 text-right"><input name="Submit_file" type="submit" class="btn btn-warning" value="<?php echo $script_transl['btn_acquire']; ?>" />
	   </div>		   
	</div> <!-- chiude container -->
</div><!-- chiude panel -->
<?php
	require("../../library/include/footer.php");
}
?>

