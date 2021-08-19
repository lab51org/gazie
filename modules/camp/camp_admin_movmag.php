<?php
/*
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
	  scriva   alla   Free  Software Foundation,  Inc.,   59
	  Temple Place, Suite 330, Boston, MA 02111-1307 USA Stati Uniti.
	  --------------------------------------------------------------------------
	  
	  Registro di Campagna è un modulo creato da Antonio Germani Massignano AP 
	  https://www.lacasettabio.it https://www.programmisitiweb.lacasettabio.it
	  --------------------------------------------------------------------------
*/
require ("../../library/include/datlib.inc.php");
require ("../../modules/magazz/lib.function.php");
require ("../../modules/vendit/lib.function.php");

$Cu_limit_anno=4; // Limite annuo in Kg di rame metallo ad ettaro
$lm = new lotmag;
$g2Form = new campForm();
$gForm = new magazzForm;
$admin_aziend = checkAdmin();

$msg = "";
$print_magval = "";
$dose = "";
$dose_usofito = "";
$tempo_sosp = "";
$dim_campo = "";
$rame_met_annuo = "";
$scadaut = "";
$scorta = "";
$service = "";
$instantwarning="";
$avv_conf=0;
$today = strtotime(date("Y-m-d H:i:s", time()));

if (!isset($_POST['ritorno'])) {
    $_POST['ritorno'] = $_SERVER['HTTP_REFERER'];
}
$form = array();

// Antonio Germani questo serve per la ricerca avversità
if (isset($_POST['nome_avv'])) {
    $form['mov'] = $_POST['mov'];
    $form['nmov'] = $_POST['nmov'];
    for ($m = 0;$m <= $form['nmov'];++$m) {
        $form['nome_avv'][$m] = $_POST['nome_avv' . $m];
        $form['id_avversita'][$m] = intval($form['nome_avv'][$m]);
    }
}

// se è stato premuto il pulsante di reset coltura
if (isset($_POST['erase'])) {
    $_POST['id_colture'] = 0;
    $_POST['nome_colt'] = "";
    $form['id_colture'] = 0;
    $form['nome_colt'] = "";
}

// se è stato premuto il pulsante di submit patent anagra
if (isset($_POST['patent'])) {
	// json: {"nome_modulo":{"nome_variabile":{"valore_variabile": {}}}}
	if (strlen($_POST['patent_number'])>0 AND strlen($_POST['patent_expiry'])>0){		
		if ($data=json_decode($_POST['rif_abilitazione'],true)){// se c'è un json				
			if (is_array($data['camp'])){ // se c'è il modulo "camp" lo aggiorno
				$data['camp']['numero']=$_POST['patent_number'];
				$data['camp']['scadenza']=$_POST['patent_expiry'];
				$patent = json_encode($data);
				gaz_dbi_query("UPDATE " . $gTables['anagra'] . " SET custom_field = '" . $patent . "' WHERE id = " . addslashes($_POST['adminid']));
			} else { //se non c'è il modulo "camp" lo aggiungo
				$data['camp']= array('numero' => $_POST['patent_number'], 'scadenza' => $_POST['patent_expiry']);
				$patent = json_encode($data);
				gaz_dbi_query("UPDATE " . $gTables['anagra'] . " SET custom_field = '" . $patent . "' WHERE id = " . addslashes($_POST['adminid']));
			}			
		} else { // se non c'è un json lo creo		
			$array= array('camp'=>array('numero' => $_POST['patent_number'], 'scadenza' => $_POST['patent_expiry']));
			$patent = json_encode($array);			
			gaz_dbi_query("UPDATE " . $gTables['anagra'] . " SET custom_field = '" . $patent . "' WHERE id = " . addslashes($_POST['adminid'])); 
		}
	}    
}
// se è stato premuto il pulsante di submit fase fenologica
if (isset($_POST['feno']) AND strlen($_POST['add_feno'])>0) {	
	$feno_array = ($_POST['feno_json'])?json_decode ($_POST['feno_json'],true):'';	
	if (is_array($feno_array) AND in_array($_POST['add_feno'], $feno_array)){// prima di salvare controllo che non ci sia già 
		// segnalo che è già inserito
		$instantwarning = "La fase fenologica '". $_POST['add_feno'] ."' è già presente!";
	} else {		
		if (!is_array($feno_array)){ // inserisco per la prima volta la riga in company data 			
			$feno_json = '["'.$_POST['add_feno'].'"]';			
			$query="INSERT INTO " . $gTables['company_data'] . " (description, var, data, ref) VALUES ('Fasi fenologiche', 'feno_json', '".$feno_json."', '')";
			gaz_dbi_query($query);
			$feno = gaz_dbi_get_row($gTables['company_data'], "var", "feno_json");			
			$feno_json = $feno['data'];	// carico nel form il json appena creato e salvato;
			$_POST['feno_json'] = $feno_json;
		} else { // altrimenti la modifico aggiungengo la nuova fase a quelle già presenti
			$feno_array[] = $_POST['add_feno'];
			$feno_json = json_encode ($feno_array);
			gaz_dbi_query("UPDATE " . $gTables['company_data'] . " SET data = '" . $feno_json . "' WHERE var = 'feno_json'"); 			
		}
	}	
}

// se è stato premuto il pulsante di reset produzione
if (isset($_POST['erase2']) ) {
    $_POST['description'] = "";
    $_POST['id_colture'] = 0;
    $_POST['nome_colt'] = "";
    $_POST['luogo_produzione'] = "";
	$_POST['id_orderman'] = "";
	$_POST['coseprod']="";
	$_form['coseprod']="";  
}
// Antonio Germani questo serve per la ricerca colture
if (isset($_POST['nome_colt'])) {
    $form['nome_colt'] = $_POST['nome_colt'];
    $form['id_colture'] = intval($form['nome_colt']);
    if ($form['id_colture'] == 0 && strlen($form['nome_colt']) > 0) {
        $msg.= "37+";
    }
}

// imposta se è un update o un insert
if ((isset($_POST['Update'])) or (isset($_GET['Update']))) {
    if (!isset($_GET['id_mov'])) {
        header("Location: " . $_POST['ritorno']);
        exit;
    } else {
        $_POST['id_mov'] = $_GET['id_mov'];
    }
    $toDo = 'update';
} else {
    $toDo = 'insert';
}


if (!isset($_POST['Update']) and isset($_GET['Update'])) { //se è il primo accesso per UPDATE
    $form['hidden_req'] = '';
    $form['mov'] = 0;
    $form['nmov'] = 0;
    //recupero il movimento
    $result = gaz_dbi_get_row($gTables['movmag'], "id_mov", $_GET['id_mov']);
    $form['id_mov'] = $result['id_mov'];
	if ($result['id_rif']>$result['id_mov'] ){ // il movimento è connesso ad un movimento acqua, recupero anche il movimento acqua		
		$result2 = gaz_dbi_get_row($gTables['movmag'], "id_mov", $result['id_rif']);
		$form['artico2'][$form['mov']] = $result2['artico'];
		$itemart2 = gaz_dbi_get_row($gTables['artico'], "codice", $form['artico2'][$form['mov']]);
		$form['quanti2'][$form['mov']] = gaz_format_quantity($result2['quanti'], 0, $admin_aziend['decimal_quantity']);
		$form['quantiin2'] = $result2['quanti'];    
		$form['prezzo2'][$form['mov']] = number_format($result2['prezzo'], $admin_aziend['decimal_price'], '.', '');
		$form['scorig2'][$form['mov']] = $result2['scorig'];
		$form['id_mov2'] = $result2['id_mov'];
	} else {		
		$form['artico2'][$form['mov']] = "";		
		$form['quanti2'][$form['mov']] = 0;
		$form['quantiin2'] = 0;    
		$form['prezzo2'][$form['mov']] = 0;
		$form['scorig2'][$form['mov']] = 0;
		$form['id_mov2'] = '';
	}
    $form['type_mov'] = $result['type_mov'];
    $form['id_rif'] = $result['id_rif'];
    $form['caumag'] = $result['caumag'];	
    $form['operat'] = $result['operat'];
	$res_caumag['operat']=$result['operat'];
    $form['gioreg'] = substr($result['datreg'], 8, 2);
    $form['mesreg'] = substr($result['datreg'], 5, 2);
    $form['annreg'] = substr($result['datreg'], 0, 4);
    $form['luogo_produzione1'] = $result['luogo_produzione']; //campo di coltivazione
	$form['ncamp']=1;
	$n=1;
    $form['clfoco'][$form['mov']] = $result['clfoco'];
    $form['clfocoin'] = $result['clfoco'];    
    $form['staff'][$form['mov']] = $result['clfoco'];
    $form['adminid'] = $result['clfoco'];
	$form['confermapat'][$form['adminid']] = "";
	if (intval($result['clfoco'])>0){
		$rowanagra = gaz_dbi_get_row($gTables['anagra'], "id", $result['clfoco']);
		$form['adminname'] = $rowanagra['ragso1']." ".$rowanagra['ragso2'];
		$form['rif_abilitazione'] = $rowanagra['custom_field'];
		if ($data = json_decode($rowanagra['custom_field'], TRUE)){			
			if (is_array($data['camp'])){
				$form['patent_number'] = $data['camp']['numero'];
				$form['patent_expiry'] = $data['camp']['scadenza'];
			} else {
				$form['patent_number'] = "";
				$form['patent_expiry'] = "";
			}
		} else {
			$form['patent_number'] = "";
			$form['patent_expiry'] = "";
		}
		
	} else {
		$form['adminname'] = "";
		$form['rif_abilitazione'] = "";
	}
	
    $form['id_orderman'] = intval($result['id_orderman']);
    $resultorderman = gaz_dbi_get_row($gTables['orderman'], "id", $form['id_orderman']);
    If ($form['id_orderman'] > 0) {
        $form['description'] = $resultorderman['description'];
		$form['coseprod']=$form['description'];
    } else {
        $form['description'] = "";
		$form['coseprod']="";
    }
    $form['tipdoc'] = $result['tipdoc'];
    $form['desdoc'] = $result['desdoc'];
    $form['id_colt'] = $result['id_colture'];
    $form['id_avv'] = $result['id_avversita'];
    $form['id_avversita'][$form['mov']] = $result['id_avversita'];
    $form['id_colture'] = $result['id_colture'];
    $colt = gaz_dbi_get_row($gTables['camp_colture'], "id_colt", $form['id_colt']);
    $form['nome_colt'] = ($colt)?$form['id_colt'] . " - " . $colt['nome_colt']:'';
    $avv = gaz_dbi_get_row($gTables['camp_avversita'], "id_avv", $form['id_avv']);
    $form['nome_avv'][$form['mov']] = ($avv)?$form['id_avv'] . " - " . $avv['nome_avv']:'';
    $form['fase_feno']="";
	$form['scochi'] = $result['scochi'];
    $form['giodoc'] = substr($result['datdoc'], 8, 2);
    $form['mesdoc'] = substr($result['datdoc'], 5, 2);
    $form['anndoc'] = substr($result['datdoc'], 0, 4);
    $form['artico'][$form['mov']] = $result['artico'];
	$form['conferma'][$form['mov']] = "";
	$itemart = gaz_dbi_get_row($gTables['artico'], "codice", $form['artico'][$form['mov']]);
	$form['id_reg'][$form['mov']] = $itemart['id_reg'];
    $form['id_lotmag'][$form['mov']] = $result['id_lotmag'];
    $reslotmag = gaz_dbi_get_row($gTables['lotmag'], "id", $result['id_lotmag']);
    $form['identifier'][$form['mov']] = ($reslotmag)?$reslotmag['identifier']:'';
    $form['expiry'][$form['mov']] = ($reslotmag)?$reslotmag['expiry']:'';
    // Antonio Germani - se è presente, recupero il file documento lotto
    $form['filename'][$form['mov']] = "";
    if (file_exists(DATA_DIR.'files/' . $admin_aziend['company_id']) > 0) {
        // recupero il filename dal filesystem
        $dh = opendir(DATA_DIR.'files/' . $admin_aziend['company_id']);
        while (false !== ($filename = readdir($dh))) {
            $fd = pathinfo($filename);
            $r = explode('_', $fd['filename']);
            if ($r[0] == 'lotmag' && $r[1] == $result['id_lotmag']) {
                // riassegno il nome file
                $form['filename'][$form['mov']] = $fd['basename'];
            }
        }
    }
    // fine recupero file documento lotto
	$form['datdocin'] = $result['datdoc'];
    $form['quanti'][$form['mov']] = gaz_format_quantity($result['quanti'], 0, $admin_aziend['decimal_quantity']);
    $form['quantiin'] = $result['quanti'];    
    $form['prezzo'][$form['mov']] = number_format($result['prezzo'], $admin_aziend['decimal_price'], '.', '');
    $form['scorig'][$form['mov']] = $result['scorig'];
	
    $form['clfoco'][$form['mov']] = $result['clfoco'];
    $form['status'] = $result['status'];
    $form['search_partner'] = ""; //Antonio Germani
    $form['search_item'] = "";
	
	if (!$feno = gaz_dbi_get_row($gTables['company_data'], "var", "feno_json")){
		$feno_json="";
	} else {
		$feno_json = $feno['data'];
	}
	
} elseif (isset($_POST['Insert']) or isset($_POST['Update'])) {    //     ****    SE NON E' IL PRIMO ACCESSO   ****

	$feno_json = $_POST['feno_json'];
	$form['mov'] = $_POST['mov'];
	$form['nmov'] = $_POST['nmov'];
	if ($form['nmov']==$form['mov']){		
		$_POST['artico'.$form['mov']] = $_POST['codart'];
		$form['artico'][$form['mov']] = $_POST['codart'];
		$itemart = gaz_dbi_get_row($gTables['artico'], "codice", $form['artico'][$form['mov']]);		
		$form['id_reg'][$form['mov']] = ($itemart)?$itemart['id_reg']:0;
		$_POST['id_reg'.$form['mov']] = $form['id_reg'][$form['mov']];
		$_POST['artico2'.$form['mov']] = $_POST['codart2'];
		$form['artico2'][$form['mov']] = $_POST['codart2'];
	}
	
	if (isset($_POST['mov']) ) { // Antonio Germani - se è stato inserito un rigo faccio il parsing di tutti i righi presenti
		for ($m = 0;$m <= $form['nmov'];++$m) {
			
			$form['artico'][$m] = $_POST['artico' . $m];
			$form['id_reg'][$m] = $_POST['id_reg' . $m];
			if (isset($_POST['conferma' . $m])){
				$form['conferma'][$m] = $_POST['conferma' . $m];
			} else {
				$form['conferma'][$m] = "";
			}
			$form['artico2'][$m] = $_POST['artico2' . $m];
			$form['id_lotmag'][$m] = $_POST['id_lotmag' . $m];
			$form['lot_or_serial'][$m] = $_POST['lot_or_serial' . $m];
			
			$query = "SELECT " . 'SCADENZA_AUTORIZZAZIONE' . " FROM " . $gTables['camp_fitofarmaci'] . " WHERE NUMERO_REGISTRAZIONE ='" . $form['id_reg'][$m] . "'";
            $result = gaz_dbi_query($query);			
            while ($row = $result->fetch_assoc()) { // controllo scadenza autorizzazione fitofarmaco
                $scadaut = $row['SCADENZA_AUTORIZZAZIONE'];
                $scadaut = strtotime(str_replace('/', '-', $scadaut)); 
				if ($scadaut<1) {$msg.= "45+";}
				// 1 giorno è 24*60*60=86400
				
				if ($today-$scadaut > 31536000 OR $form['conferma'][$m]=="Non voglio usare ".$form['artico'][$m]){ // se è scaduto da più di un anno segnalo e blocco	
					$msg.= "27+";
				} elseif ($today > $scadaut AND $form['conferma'][$m]!=="Confermo deroga ".$form['artico'][$m]) { // altrimenti segnalo e faccio scegliere
					$avv_conf = 1;
				}
				
            }
			if ($form['lot_or_serial'][$m] == 1) {
				$form['identifier'][$m] = $_POST['identifier' . $m];
				$form['expiry'][$m] = $_POST['expiry' . $m];
				$form['filename'][$m] = $_POST['filename' . $m];
			} else {
				$form['identifier'][$m] = "";
				$form['expiry'][$m] = "";
				$form['filename'][$m] = "";
			}
			
			$form['quanti'][$m] = gaz_format_quantity($_POST['quanti' . $m], 0, $admin_aziend['decimal_quantity']);
			
			$form['scorig'][$m] = $_POST['scorig' . $m];
			$form['prezzo'][$m] = gaz_format_quantity($_POST['prezzo' . $m], 0, $admin_aziend['decimal_quantity']);
			if (isset($_POST['quanti2' . $m])){
				$form['quanti2'][$m] = gaz_format_quantity($_POST['quanti2' . $m], 0, $admin_aziend['decimal_quantity']);
			} else {
				$form['quanti2'][$m]=0;
			}
			$form['scorig2'][$m] = ($_POST['scorig2' . $m]);
			$form['prezzo2'][$m] = gaz_format_quantity($_POST['prezzo2' . $m], 0, $admin_aziend['decimal_quantity']);
			$form['clfoco'][$m] = $_POST['clfoco' . $m];
			$form['nome_avv'][$m] = $_POST['nome_avv' . $m];
			$form['fase_feno'][$m] = $_POST['fase_feno' . $m];
			$form['id_avversita'][$m] = intval($form['nome_avv'][$m]);
			if (isset($_POST['staff' . $m])) {
				$form['staff'][$m] = $_POST['staff' . $m];
			} else {
				$form['staff'][$m] = "";
			}
		}
		
		// carico i dati operaio
		$itm = gaz_dbi_get_row($gTables['staff'], "id_staff", $form['staff'][$form['mov']]); 
		
		// se è il caso carica tabella camp_uso_fitofarmaci
		$dose_usofito = "";
		if ($form['artico'][$form['mov']] <> "" && $form['nome_avv'][$form['mov']] <> "") {
			$query = "SELECT " . 'dose' . " FROM " . $gTables['camp_uso_fitofarmaci'] . " WHERE cod_art ='" . $form['artico'][$form['mov']] . "' AND id_colt ='" . $form['id_colture'] . "' AND id_avv ='" . $form['id_avversita'][$form['mov']] . "'";
			$result_uso_fito = gaz_dbi_query($query);
			while ($row = $result_uso_fito->fetch_assoc()) {
				$dose_usofito = $row['dose'];
			}
		}  
	}

    $form['hidden_req'] = htmlentities($_POST['hidden_req']);
    //ricarico i registri per il form facendo gli eventuali parsing
    $form['id_mov'] = intval($_POST['id_mov']);
	$form['id_mov2'] = intval($_POST['id_mov2']);
    $form['type_mov'] = 1;
    $form['id_rif'] = intval($_POST['id_rif']);
	
    $form['caumag'] = intval($_POST['caumag']);
	$res_caumag = gaz_dbi_get_row($gTables['caumag'], "codice", $form['caumag']);
	if (isset($res_caumag)){
		$form['operat'] = $res_caumag['operat'];
		if ($res_caumag['insdoc'] == 0) { //se la nuova causale non prevede i dati del documento
				$form['tipdoc'] = "";
				$form['desdoc'] = "";
				$form['giodoc'] = date("d");
				$form['mesdoc'] = date("m");
				$form['anndoc'] = date("Y");
				$form['scochi'] = 0;
				$form['id_rif'] = 0;
			}
	} else {
		$form['operat'] = 0;
	}
    $form['gioreg'] = intval($_POST['gioreg']);
    $form['mesreg'] = intval($_POST['mesreg']);
    $form['annreg'] = intval($_POST['annreg']);
    $form['clfocoin'] = $_POST['clfocoin'];
    $form['quantiin'] = $_POST['quantiin'];
    $form['datdocin'] = $_POST['datdocin'];
	$form['ncamp']= $_POST['ncamp'];
	$nmax=$form['ncamp'];
	$nn=0;
	for ($n = 1;$n <= $nmax;++$n) {
		if (!isset($_POST['luogo_produzione'.$n])){
			$form['luogo_produzione'.$n]=""; $form['ncamp']--;
		} else {
		if ($_POST['luogo_produzione'.$n]>0 ){
			$nn++;
			$form['luogo_produzione'.$nn] = intval($_POST['luogo_produzione'.$n]); //campo di coltivazione
		} else {
			$form['luogo_produzione'.$n]=""; $form['ncamp']--;
		}
		}
	} $n--;
	if ($form['ncamp']==0){
		$form['ncamp']=1;
	}
    $form['adminid'] = $_POST['adminid'];
	if (intval($form['adminid'])>0){
		$row_adminname = gaz_dbi_get_row($gTables['anagra'], "id", $form['adminid']);
		$form['adminname'] = $row_adminname['ragso1']." ".$row_adminname['ragso2'];
		$form['rif_abilitazione'] = $row_adminname['custom_field'];
		if ($data = json_decode($row_adminname['custom_field'], TRUE)){			
			if (is_array($data['camp'])){ // se c'è un patentino
				$form['patent_number'] = $data['camp']['numero'];
				$form['patent_expiry'] = $data['camp']['scadenza'];
				// controllo validità patentino
				$exp=strtotime($form['patent_expiry']);
				$today=strtotime(date("Y/m/d"));				
				if ($today>$exp){ //se è scaduto					
					$avv_conf=2;
				} elseif (($exp-7776000)<$today ){// se sta per scadere -> 1 Day: 86400 Seconds 3 mesi: 7776000					
					$instantwarning="L'autorizzazione per l'acquisto e l'uso di prodotti fitosanotari scadrà il ".$form['patent_expiry'];
				}				
			} else { // se non c'è un patentino segnalo la mancanza
				$form['patent_number'] = "";
				$form['patent_expiry'] = "";
				$avv_conf=3;
			}
		} else {
			$form['patent_number'] = "";
			$form['patent_expiry'] = "";
			$form['rif_abilitazione'] = "";
			$avv_conf=3;
		}
	} else {
		$form['adminname'] = "";
		$form['patent_number'] = "";
		$form['patent_expiry'] = "";
	}

	$form['confermapat'][$form['adminid']] = (isset($_POST['confermapat'.$_POST['adminid']]))?$_POST['confermapat'.$_POST['adminid']]:'';
    $form['tipdoc'] = intval($_POST['tipdoc']);
    $form['desdoc'] = substr($_POST['desdoc'], 0, 50);
    $form['giodoc'] = intval($_POST['giodoc']);
    $form['mesdoc'] = intval($_POST['mesdoc']);
    $form['anndoc'] = intval($_POST['anndoc']);
    $form['scochi'] = floatval(preg_replace("/\,/", '.', $_POST['scochi']));
    $form['id_lotmag'][$form['mov']] = $_POST['id_lotmag' . $form['mov']];
    $form['lot_or_serial'][$form['mov']] = $_POST['lot_or_serial' . $form['mov']];
    if ($form['lot_or_serial'][$form['mov']] == 1) {
        $form['identifier'][$form['mov']] = $_POST['identifier' . $form['mov']];
        $form['expiry'][$form['mov']] = $_POST['expiry' . $form['mov']];
        $form['filename'][$form['mov']] = $_POST['filename' . $form['mov']];
    } else {
        $form['identifier'][$form['mov']] = "";
        $form['expiry'][$form['mov']] = "";
        $form['filename'][$form['mov']] = "";
    }
    $form['quanti'][$form['mov']] = gaz_format_quantity($_POST['quanti' . $form['mov']], 0, $admin_aziend['decimal_quantity']);
	
    if ((isset($_POST['prezzo' . $form['mov']]) > 0) && (strlen($_POST['prezzo' . $form['mov']]) > 0)) {
        $form['prezzo'][$form['mov']] = $_POST['prezzo' . $form['mov']];
        $form['prezzo'][$form['mov']] = str_replace('.', '', $form['prezzo'][$form['mov']]);
        $form['prezzo'][$form['mov']] = str_replace(',', '.', $form['prezzo'][$form['mov']]);
    } else {
        $form['prezzo'][$form['mov']] = "";
    }
    if (isset($_POST['scorig' . $form['mov']])) {
        $form['scorig'][$form['mov']] = floatval(preg_replace("/\,/", '.', $_POST['scorig' . $form['mov']]));
    } else {
        $form['scorig'][$form['mov']] = 0;
    }
	if (isset($_POST['quanti2' . $form['mov']])){
		$form['quanti2'][$form['mov']] = gaz_format_quantity($_POST['quanti2' . $form['mov']], 0, $admin_aziend['decimal_quantity']);
    } else {
		$form['quanti2'][$form['mov']]=0;
	}
	if ((isset($_POST['prezzo2' . $form['mov']]) > 0) && (strlen($_POST['prezzo2' . $form['mov']]) > 0)) {
        $form['prezzo2'][$form['mov']] = $_POST['prezzo2' . $form['mov']];
        $form['prezzo2'][$form['mov']] = str_replace('.', '', $form['prezzo2'][$form['mov']]);
        $form['prezzo2'][$form['mov']] = str_replace(',', '.', $form['prezzo2'][$form['mov']]);
    } else {
        $form['prezzo2'][$form['mov']] = "";
    }
    if (isset($_POST['scorig2' . $form['mov']])) {
        $form['scorig2'][$form['mov']] = floatval(preg_replace("/\,/", '.', $_POST['scorig2' . $form['mov']]));
    } else {
        $form['scorig2'][$form['mov']] = 0;
    }
    $form['status'] = substr($_POST['status'], 0, 10);
	$form['coseprod']= $_POST['coseprod'];
	$form['description']= $_POST['coseprod'];
	$res = gaz_dbi_get_row($gTables['orderman'], "description", $form['coseprod']);
	if (isset($res)){
		$form['id_orderman'] = $res['id'];
	} else {
		$form['id_orderman'] = 0;
	}
    if (isset($form['id_orderman']) AND intval($form['id_orderman']) > 0 AND intval($form['luogo_produzione1']) == 0) { //se è stata inserita una produzione e non è stato inserito il primo campo
        $rs_orderman = gaz_dbi_get_row($gTables['orderman'], "id", $form['id_orderman']);
        // propongo il primo campo della produzione nel form
		$form['luogo_produzione1'] = $rs_orderman['campo_impianto'];
    } 
    $form['search_partner'] = "";
	if (isset ($form['luogo_produzione1'])){ // se inserito il primo campo ne prendo la coltura
		$item_campi = gaz_dbi_get_row($gTables['campi'], "codice", $form['luogo_produzione1']);
		if (isset($item_campi) AND $item_campi['id_colture'] > 0) { // se c'è una coltura nel campo la carico nel form
			$form['id_colture'] = $item_campi['id_colture'];
			$res = gaz_dbi_get_row($gTables['camp_colture'], "id_colt", $form['id_colture']);
			$form['nome_colt'] = $form['id_colture'] . " - " . $res['nome_colt'];
		} else { // altrimenti azzero la coltura del form
			$form['nome_colt']="";
		}
	}
	
    // Antonio Germani - se è stato inserita una coltura la inserisco anche se diversa da quella del campo di coltivazione
    if ($_POST['nome_colt'] > 0) {
        $form['nome_colt'] = $_POST['nome_colt'];
        $form['id_colture'] = intval($form['nome_colt']);
    }
    // Antonio Germani - controllo se c'è una coltura deve esserci un campo di coltivazione
    if ($form['luogo_produzione1'] < 1 && $form['id_colture'] > 0) {
        $msg.= "35+";
    }
   /*  Non dovrebbe servire in quanto l'autocomplete prende solo se artico esiste 
	$itemart = gaz_dbi_get_row($gTables['artico'], "codice", $form['artico'][$form['mov']]); // acquisisco i dati dell'articolo del rigo in questione
	
	if ($form['artico'][$form['mov']] <> "" && !isset($itemart)) {// controllo se il codice articolo inserito esiste nella tabella artico
		$msg.= "18+";
	}     
	*/
    if (isset($_POST['newpartner'])) {
        $anagrafica = new Anagrafica();
        $partner = $anagrafica->getPartner($_POST['clfoco']);
        $form['search_partner'] = substr($partner['ragso1'], 0, 4);
        $form['clfoco'] = 0;
    }
    if (isset($_POST['Return'])) {
        header("Location: " . $_POST['ritorno']);
        exit;
    }
    	
    if (!empty($_POST['Insert'])) { // 	Se viene inviata la richiesta di conferma totale ................
        $utsreg = mktime(0, 0, 0, $form['mesreg'], $form['gioreg'], $form['annreg']);
        $utsdoc = mktime(0, 0, 0, $form['mesdoc'], $form['giodoc'], $form['anndoc']);
        if (!checkdate($form['mesreg'], $form['gioreg'], $form['annreg'])) $msg.= "16+";
        if (!checkdate($form['mesdoc'], $form['giodoc'], $form['anndoc'])) $msg.= "15+";
        if ($utsdoc > $utsreg) {
            $msg.= "17+";
        }     		
		// Antonio Germani controllo se in ricerca produzione è stato impostata la voce selezionandola dal menù a tendina
		if (strlen($_POST['coseprod'])>0 && intval($form['id_orderman'])==0) {
			$msg.= "30+"; // non esiste fra quelle create
      	}
		
		// Antonio Germani creo la data di ATTUAZIONE DELL'OPERAZIONE selezionata che poi confronterò con quella di sospensione del campo
        $dt = substr("0" . $form['giodoc'], -2) . "-" . substr("0" . $form['mesdoc'], -2) . "-" . $form['anndoc'];
        $dt = strtotime($dt);
       
		$nn=0;$tot_sup=0;
		if ($form['luogo_produzione1']>0) { // se c'è almeno un campo
			for ($n = 1;$n <= $form['ncamp'];++$n) { // ciclo i campi inseriti
				$query = "SELECT " . 'giorno_decadimento' . "," . 'ricarico' . " FROM " . $gTables['campi'] . " WHERE codice ='" . $form['luogo_produzione'.$n] . "'";
				$result = gaz_dbi_query($query);
				while ($row = $result->fetch_assoc()) {				
					$form['fine_sosp'.$n] = strtotime($row['giorno_decadimento']);// prendo la data di fine sospensione dai campi di coltivazione selezionati
					$form['dim_campo'.$n] = $row['ricarico']; // prendo pure la dimensione del campo e la metto in $dim_campo
				}
				// controllo se è ammesso il raccolto sul campo di coltivazione selezionato $msg .=24+ errore tempo di sospensione
				if (isset($form['operat']) AND $form['luogo_produzione'.$n] > 0 && $form['operat'] == 1 && intval($dt) < intval($form['fine_sosp'.$n])) {
					$msg.= "24+";
				}
				$tot_sup=$tot_sup+$form['dim_campo'.$n];
			}
		}		
		
        /* inizio controlli sulle righe articoli >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> */
		
        for ($m = 0;$m <= $form['nmov'];++$m) {
			$itemart = gaz_dbi_get_row($gTables['artico'], "codice", $form['artico'][$m]); // carico i dati dell'articolo del rigo
            if ($form['quanti2'][$m] >0){ 
				$check=gaz_dbi_get_row($gTables['artico'], "codice", $form['artico2'][$m]);
				if (!$check){ // controllo se esiste l'acqua nel DB
					$msg.= "44+";
				}
			}
			// Antonio Germani controllo che, se la causale movimento non opera, non ci sia un articolo con magazzino
			if (isset($itemart)){
				$service = intval($itemart['good_or_service']);
			}
            if (isset($form['operat']) AND $service == 0 && $form['operat'] == 0) {
                $msg.= "36+";
            }
            if ($service == 2 && $form['operat'] == 0) {
                $msg.= "36+";
            }
			 if (!isset($form['operat'])) {
                $msg.= "36+";
            }
        		
			// controllo mancanza articolo
            if (empty($form['artico'][$m])) { //manca l'articolo
                $msg.= "18+";
            }
            // controllo quantità uguale a zero
            if (gaz_format_quantity($form['quanti'][$m], 0, $admin_aziend['decimal_quantity']) == 0) { //la quantità è zero
                $msg.= "19+";
            }
			if (isset($_POST['Update'])) { // se è un update carico il movimento di magazzino presente nel database per fare i confronti
                 $check_movmag = gaz_dbi_get_row($gTables['movmag'], "id_mov", $_GET['id_mov']);
			}					
            // Antonio Germani calcolo giacenza di magazzino, la metto in $print_magval e, se è uno scarico, controllo sufficiente giacenza
            
            if (isset($itemart) AND ($itemart['good_or_service'] == 0 or $itemart['good_or_service'] == 2)){ // se non è un servizio
                // controllo se sono stati inseriti articoli merci uguali in più righe /perché non è possibile altrimenti non funzionano i controlli sulle quantità per lotto/
				
                for ($mart = 0;$mart <= $form['nmov'];++$mart) {
                    if ($m <> $mart) {
                        if ((intval($form['id_lotmag'][$m]) > 0) && (intval($form['id_lotmag'][$m]) == intval($form['id_lotmag'][$mart]))) {
                            $msg.= "40+";
                        }
                    }
                }
                $mv = $gForm->getStockValue(false, $form['artico'][$m]);
                $magval = array_pop($mv);
                $print_magval = floatval(str_replace(',', '', ($magval)?$magval['q_g']:0));
                if (isset($_POST['Update'])) {
                    if ($check_movmag['artico'] == $form['artico'][$m]){// Se l'articolo inserito nel form è lo stesso precedentemente memorizzato nel db, prendo la quantità precedentemente memorizzata e la riaggiungo alla giacenza di magazzino
					$print_magval = $print_magval + $check_movmag['quanti'];
					}
                }
                if (isset ($form['operat']) AND $form['operat'] == - 1 and (floatval(str_replace(',', '', $print_magval)) - floatval(str_replace(',', '', $form['quanti'][$m])) < 0)) {
                    //Antonio Germani quantità insufficiente
                    $msg.= "23+";
                }
            }
            // Antonio Germani > controllo che non sia caricato un articolo composito
            if (isset($itemart) AND $itemart['good_or_service'] == 2 && $form['operat'] == 1) {
                $msg.= "42+"; // il carico di articolo composti si può fare solo dal modulo produzioni
                
            }
            // Antonio Germani - se l'articolo ha lotti in uscita controllo se il lotto selezionato ha quantità sufficiente
            if (isset($itemart) AND ($itemart['good_or_service'] == 0) && ($itemart['lot_or_serial'] == 1) && ($form['operat'] == - 1)) { // se è merce e ha lotti
                $lotqty = $lm->getLotQty($form['id_lotmag'][$m]);
				if ($toDo=="update" && intval ($check_movmag['id_lotmag']) == intval($form['id_lotmag'][$m])){
					$lotqty=$lotqty+$check_movmag['quanti'];
				}
                if ($lotqty < $form['quanti'][$m]) { 
                    $msg.= "38+";
                }
            }
            If ((isset($itemart) AND $itemart['good_or_service'] == 2) && ($itemart['lot_or_serial'] == 1) && ($form['operat'] == - 1)) { // se è articolo composto e ha lotti
                $lotqty = $lm->getLotQty($form['id_lotmag'][$m]);				
				if ($toDo=="update" && intval ($check_movmag['id_lotmag']) == intval($form['id_lotmag'][$m])){
					$lotqty=$lotqty+$check_movmag['quanti'];
				}
                if ($lotqty < $form['quanti'][$m]) {
                    $msg.= "38+";
                }
            }
			
            //Antonio Germani controllo se il prodotto è presente nel database fitofarmaci 
			if (isset($itemart['id_reg'])){
				$query = "SELECT " . 'SCADENZA_AUTORIZZAZIONE' . " FROM " . $gTables['camp_fitofarmaci'] . " WHERE NUMERO_REGISTRAZIONE ='" . $itemart['id_reg'] . "'";
				$result = gaz_dbi_query($query);
			}
            // se è presente nel db fitofarmaci CONTROLLO QUANDO è StATO FATTO L'ULTIMO AGGIORNAMENTO del db fitofarmaci
            If (($result->num_rows) > 0) {
                $query = "SELECT UPDATE_TIME FROM information_schema.tables WHERE TABLE_SCHEMA = '" . $Database . "' AND TABLE_NAME = '" . $gTables['camp_fitofarmaci'] . "'";
                $result = gaz_dbi_query($query);
                while ($row = $result->fetch_assoc()) {
                    $update = strtotime($row['UPDATE_TIME']);
                }
                // 1 giorno è 24*60*60=86400 - 30 giorni 30*86400=2592000
                If (intval($update) + 2592000 < $today) {
                    $msg.= "28+";;
                }
            }
			// Antonio Germani dall'articolo prendo la dose massima, il rame metallo, e NPK
				$item = gaz_dbi_get_row($gTables['artico'], "codice", $form['artico'][$m]);
				$dose_artico = 0;
				$rame_metallo = 0;
				$perc_N = 0;
				if (isset($item)){
					$dose_artico = $item['dose_massima']; // prendo la dose
					$rame_metallo = $item['rame_metallico']; // prendo anche il rame metallo del prodotto oggetto del movimento
					$perc_N = $item['perc_N'];
					$perc_P = $item['perc_P'];
					$perc_K = $item['perc_K'];
				}
				$query = "SELECT " . 'dose' . ", " . 'tempo_sosp' . " FROM " . $gTables['camp_uso_fitofarmaci'] . " WHERE cod_art ='" . $form['artico'][$m] . "' AND id_colt ='" . $form['id_colture'] . "' AND id_avv ='" . $form['id_avversita'][$m] . "'";
				$result = gaz_dbi_query($query);
				while ($row = $result->fetch_assoc()) {
					$dose_usofito = $row['dose'];
				}
			// per ogni rigo articolo devo ciclare i campi di coltivazione per i seguenti controlli  |||||||||||||||||||||||||||
			
			$nn=0;$quanti=0;
			if (isset ($form['dim_campo1'])) { // se c'è almeno un campo
				for ($n = 1;$n <= $form['ncamp'];++$n) { // ciclo i campi inseriti
					if ($form['ncamp']>1){// se c'è più di un campo
						$quanti=((($form['dim_campo'.$n]/$tot_sup)*100)*$form['quanti'][$m])/100; // questa è la dose suddivisa in percentuale per il campo 
					
					} else {
						$quanti=$form['quanti'][$m];
					}
					if ($dose_usofito > 0) { //Controllo se la quantità o dose è giusta rapportata al campo di coltivazione
						If ($dose_usofito > 0 && $quanti > $dose_usofito * $form['dim_campo'.$n] && $form['operat'] == - 1 && $form['dim_campo'.$n] > 0) {
							$msg.= "34+"; // errore dose uso fito superata
							$instantwarning="Dose superata nel prodotto ". $form['artico'][$m] ." con la coltura ". $form['nome_colt'] .". La quantità massima utilizzabile è ". gaz_format_quantity($dose_usofito * $form['dim_campo'.$n], 1, $admin_aziend['decimal_quantity']) .".";
						}
					} else {
						if ($dose_artico > 0 && $quanti > $dose_artico * $form['dim_campo'.$n] && $form['operat'] == - 1 && $form['dim_campo'.$n] > 0) {
							$msg.= "25+"; // errore dose artico superata
							$instantwarning="Dose superata nel prodotto ". $form['artico'][$m] .". La quantità massima utilizzabile è ". gaz_format_quantity($dose_artico * $form['dim_campo'.$n], 1, $admin_aziend['decimal_quantity']) .".";
						}
					}
					// Antonio Germani Calcolo quanto rame metallo e Azoto N è stato usato nell'anno di esecuzione di questo movimento
					If ($form['luogo_produzione'.$n] > 0) { // se il prodotto va in un campo di coltivazione
						$rame_met_annuo=0;$N_annuo=0;
						if ($rame_metallo > 0 OR $perc_N > 0) { //se questo prodotto contiene rame metallo o azoto
							$query = "SELECT " . 'artico' . "," . 'datdoc' . "," . 'quanti' . " FROM " . $gTables['movmag'] . " WHERE datdoc >'" . $form['anndoc'] . "' AND " . 'luogo_produzione' . " = '" . $form['luogo_produzione'.$n] . "'"; // prendo solo le righe dell'anno di esecuzione del trattamento e degli anni successivi con il campo di coltivazione selezionato nel form
							$result = gaz_dbi_query($query);
							while ($row = $result->fetch_assoc()) {
								if (substr($row['datdoc'], 0, 4) == $form['anndoc']) { // elimino dal conteggio gli eventuali anni successivi
									$item = gaz_dbi_get_row($gTables['artico'], "codice", $row['artico']);
									if ($item['rame_metallico'] > 0) {
										$rame_met_annuo = $rame_met_annuo + $item['rame_metallico'] * $row['quanti'];										
									}
									if ($item['perc_N'] > 0) {
										$N_annuo= $N_annuo + ($item['perc_N'] * $row['quanti']) /100;
									}
								}
							}
						}
					} 
					// Antonio Germani controllo se con questo movimento non si supera la doce massima annua di 6Kg ad ha di rame metallo 
					// e il limite di Azoto annuo impostato per ogni singolo campo
					
					// prendo il limite di azoto per anno per il campo da controllare
					$res_N = gaz_dbi_get_row($gTables['campi'], "codice", $form['luogo_produzione'.$n]);
					if ($res_N['zona_vulnerabile']==0){
						$limite_N=$res_N['limite_azoto_zona_non_vulnerabile'];
					} else {
						$limite_N=$res_N['limite_azoto_zona_vulnerabile'];
					}
					if ($toDo == "update" && $check_movmag['artico']==$form['artico'][$m] && $form['luogo_produzione'.$n] > 0) { // se è un update, e non è stato cambiato l'articolo tolgo il rame metallo e/o l'azoto memorizzato in precedenza
						$rame_met_annuo = $rame_met_annuo - $rame_metallo * gaz_format_quantity($quanti, 0, $admin_aziend['decimal_quantity']);
						$N_annuo = $N_annuo - ($perc_N * gaz_format_quantity($quanti, 0, $admin_aziend['decimal_quantity']))/100;
					}
					if (($quanti>0 && $form['luogo_produzione'.$n] > 0) && ($form['dim_campo'.$n] > 0) && ($rame_met_annuo + ($rame_metallo * gaz_format_quantity($quanti, 0, $admin_aziend['decimal_quantity'])) > ($Cu_limit_anno * $form['dim_campo'.$n]))) {
						$msg.= "26+"; // errore superato il limite di rame metallo ad ettaro                
						$instantwarning="ERRORE rame metallo <br> Rame metallo annuo già usato: ". gaz_format_quantity($rame_met_annuo, 1, $admin_aziend['decimal_quantity']) ." Kg  - Rame metallo che si tenta di usare: ". gaz_format_quantity($rame_metallo * $quanti, 1, $admin_aziend['decimal_quantity']) ." Kg - Limite annuo di legge per questo campo: ". gaz_format_quantity(($Cu_limit_anno * $form['dim_campo'.$n]), 1, $admin_aziend['decimal_quantity']) ." Kg";
					}
					if (($quanti>0 && $form['luogo_produzione'.$n] > 0) && ($form['dim_campo'.$n] > 0) && ($N_annuo + ($perc_N * gaz_format_quantity($quanti, 0, $admin_aziend['decimal_quantity']))/100 > ($limite_N * $form['dim_campo'.$n]))) {
						$msg.= "43+"; // errore superato il limite di azoto ad ettaro                
						$instantwarning="ERRORE azoto <br> Azoto annuo già usato: ". gaz_format_quantity($N_annuo, 1, $admin_aziend['decimal_quantity']). " Kg  - Azoto che si tenta di usare: ". gaz_format_quantity($perc_N * $quanti, 1, $admin_aziend['decimal_quantity'])/100 ."  Kg - Limite annuo per questo campo: ". gaz_format_quantity(($limite_N * $form['dim_campo'.$n]), 1, $admin_aziend['decimal_quantity']) ." Kg";
					}
				} // fine ciclo campi di coltivazione  
			}
        }/*  fine controllo righe articoli    <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< */		
	
        //  §§§§§§§§§§§§§§§§ INIZIO salvataggio sui database §§§§§§§§§§§§§§§§§§§
        if (empty($msg)) { // nessun errore
			
            if ($toDo == "update") { // se è un update cancello eventule file di certificato lotto messo sulla cartella tmp
                foreach (glob("../../modules/camp/tmp/*") as $fn) { // prima cancello eventuali precedenti file temporanei
                    unlink($fn);
                }
            }
            $upd_mm = new magazzForm;
            //formatto le date
            $form['datreg'] = $form['annreg'] . "-" . $form['mesreg'] . "-" . $form['gioreg'];
            $form['datdoc'] = $form['anndoc'] . "-" . $form['mesdoc'] . "-" . $form['giodoc'];
			
			$form['tipdoc']="CAM";
			if (strlen($form['desdoc'])<1){
				$form['desdoc']="Registro di campagna ";
			}
            $new_caumag = gaz_dbi_get_row($gTables['caumag'], "codice", $form['caumag']);
            for ($form['mov'] = 0;$form['mov'] <= $form['nmov'];++$form['mov']) { // per ogni movimento inserito
                if (!empty($form['artico'][$form['mov']])) { // se è stato inserito un articolo
					
					$nn=0;
					for ($n = 1;$n <= $form['ncamp'];++$n) { // ciclo i campi inseriti
						if (isset ($form['dim_campo'.$n]) AND $form['ncamp']>1) {
							$quanti=((($form['dim_campo'.$n]/$tot_sup)*100)*$form['quanti'][$form['mov']])/100; // questa è la dose suddivisa in percentuale per il campo 
						} else {
							$quanti=$form['quanti'][$form['mov']];
						}
						if ($form['adminid']>0){
							$form['clfoco'][$form['mov']]=$form['adminid'];
						}
						$id_movmag=$upd_mm->uploadMag($form['id_rif'], $form['tipdoc'], 0, // numdoc � in desdoc
						0, // seziva � in desdoc
						$form['datdoc'], $form['clfoco'][$form['mov']], $form['scochi'], $form['caumag'], $form['artico'][$form['mov']], $quanti, $form['prezzo'][$form['mov']], $form['scorig'][$form['mov']], $form['id_mov'], $admin_aziend['stock_eval_method'], array('datreg' => $form['datreg'], 'operat' => $form['operat'], 'desdoc' => $form['desdoc']));
						$id_rif=$id_movmag;
						If ($form['id_mov'] > 0) {
							$id_movmag = $form['id_mov'];
						} 
						
						// se è stata inserita ACQUA
						if (!empty($form['artico2'][$form['mov']]) AND $form['quanti2'][$form['mov']]>0) { // divido l'acqua per i campi e creo movimenti di uscita acqua per ogni campo
							$quanti2=((($form['dim_campo'.$n]/$tot_sup)*100)*$form['quanti2'][$form['mov']])/100; // questa è la dose suddivisa in percentuale per il campo 
							$id_movmag_acqua=$upd_mm->uploadMag($id_movmag, $form['tipdoc'], 0, 0, $form['datdoc'], $form['clfoco'][$form['mov']], 
							$form['scochi'], $form['caumag'], $form['artico2'][$form['mov']], $quanti2, $form['prezzo2'][$form['mov']], 
							$form['scorig2'][$form['mov']], $form['id_mov2'], $admin_aziend['stock_eval_method'], 
							array('datreg' => $form['datreg'], 'operat' => $form['operat'], 'desdoc' => $form['desdoc']));
							// riprendo il salvataggio del movimento acqua in movmag con i dati mancanti del quaderno di campagna
							$query = "UPDATE " . $gTables['movmag'] . " SET type_mov = '" . 1 . "', id_rif = '".$id_movmag."', tipdoc = '".$form['tipdoc']."' , luogo_produzione = '" . $form['luogo_produzione'.$n] . "' , id_avversita = '" . $form['id_avversita'][$form['mov']] . "' , id_colture = '" . $form['id_colture'] . "' , id_orderman = '" . $form['id_orderman'] . "' , id_lotmag = '" . $form['id_lotmag'][$form['mov']] . "' WHERE id_mov ='" . $id_movmag_acqua . "'";
							gaz_dbi_query($query);
							$id_rif=$id_movmag_acqua;// il movmag padre avrà il riferimento del movmag acqua						
						}
						
						// inframezzo il salvataggio del movmag con i lotti perché altrimenti, se è un movimento in entrata, non ho id_lotmag da salvare in movmag
						//Antonio Germani - >>> inizio salvo lotti -se ci sono e se il prodotto li richiede-
						if ($form['operat'] == 1) { // se il movimento è in entrata -carico-
							$idlotcontroll = gaz_dbi_get_row($gTables['lotmag'], "id", $form['id_lotmag'][$form['mov']]); // in $idlotcontroll['id_movmag'] ho l'id del movimento madre del lotto mi
							if ($form['lot_or_serial'][$form['mov']] > 0 && intval($form['id_lotmag'][$form['mov']]) == 0) { // se l'articolo prevede un lotto e non ho id_lotmag, vuol dire che non ho scelto il lotto fra gli esistenti e quindi devo creare un  nuovo lotto
								// ripulisco il numero lotto da caratteri dannosi
								$form['identifier'][$form['mov']] = (empty($form['identifier'][$form['mov']])) ? '' : filter_var($form['identifier'][$form['mov']], FILTER_SANITIZE_STRING);
								if (strlen($form['identifier'][$form['mov']]) == 0) { // se non c'è il lotto lo inserisco con data e ora in automatico
									$form['identifier'][$form['mov']] = date("Ymd Hms");
								}
								if (strlen($form['expiry'][$form['mov']]) == 0) { // se non c'è la scadenza la inserisco a zero in automatico
									$form['expiry'][$form['mov']] = "0000-00-00 00:00:00";
								}
								// è un nuovo INSERT
								if (strlen($form['identifier'][$form['mov']]) > 0) {
									/* formatto la data per il database se arriva come yyyy/mm/dd
									$arraydt = explode("/", $form['expiry'][$form['mov']]);
									$form['expiry'][$form['mov']] = $arraydt[2]."-".$arraydt[1]."-".$arraydt[0];*/
									gaz_dbi_query("INSERT INTO " . $gTables['lotmag'] . "(codart,id_movmag,identifier,expiry) VALUES ('" . $form['artico'][$form['mov']] . "','" . $id_movmag . "','" . $form['identifier'][$form['mov']] . "','" . $form['expiry'][$form['mov']] . "')");
									// vedo in quale id è stato salvato il lotto e lo metto in id_lotmag di movmag
									$query = "SHOW TABLE STATUS LIKE '" . $gTables['lotmag'] . "'";
									$result = gaz_dbi_query($query);
									$row = $result->fetch_assoc();
									$form['id_lotmag'][$form['mov']] = $row['Auto_increment'] - 1;
								}
							} else {
								if (($form['lot_or_serial'][$form['mov']] > 0) && (intval($form['id_mov']) == intval($idlotcontroll['id_movmag']))) { // se l'articolo prevede un lotto e ho id_lotmag e se il movimento movmag è la madre del lotto devo modificare il lotto
									//  è un UPDATE
									if (strlen($form['identifier'][$form['mov']]) > 0) {
										/* formatto la data per il database	se arriva come yyy/mm/dd
										$arraydt = explode("/", $form['expiry'][$form['mov']]);
										$form['expiry'][$form['mov']] = $arraydt[2]."-".$arraydt[1]."-".$arraydt[0];*/
										gaz_dbi_query("UPDATE " . $gTables['lotmag'] . " SET codart = '" . $form['artico'][$form['mov']] . "' , id_movmag = '" . $id_movmag . "' , identifier = '" . $form['identifier'][$form['mov']] . "' , expiry = '" . $form['expiry'][$form['mov']] . "' WHERE id = '" . $form['id_lotmag'][$form['mov']] . "'");
									}
								}
							}
							// Antonio Germani - inizio salvo documento/certificato
							if (substr($form['filename'][$form['mov']], 0, 7) <> 'lotmag_') { // se è stato cambiato il file, cioè il nome non inizia con lotmag e, quindi, anche se è un nuovo insert
								if (!empty($form['filename'][$form['mov']])) { // e se ha un nome impostato nel form
									$tmp_file = DATA_DIR."files/tmp/" . $admin_aziend['adminid'] . '_' . $admin_aziend['company_id'] . '_' . $form['mov'] . '_' . $form['filename'][$form['mov']];
									// sposto nella cartella definitiva, rinominandolo, il relativo file temporaneo
									$fd = pathinfo($form['filename'][$form['mov']]);
									rename($tmp_file, DATA_DIR."files/" . $admin_aziend['company_id'] . "/lotmag_" . $form['id_lotmag'][$form['mov']] . '.' . $fd['extension']);
								}
							}
						}
						// <<< fine salvo lotti
						// riprendo il salvataggio del movimento di magazzino in movmag
						$query = "UPDATE " . $gTables['movmag'] . " SET type_mov = '" . 1 . "', id_rif = '".$id_rif."', tipdoc = '".$form['tipdoc']."' , luogo_produzione = '" . $form['luogo_produzione'.$n] . "' , id_avversita = '" . $form['id_avversita'][$form['mov']] . "' , id_colture = '" . $form['id_colture'] . "' , id_orderman = '" . $form['id_orderman'] . "' , id_lotmag = '" . $form['id_lotmag'][$form['mov']] . "' WHERE id_mov ='" . $id_movmag . "'";
						gaz_dbi_query($query);
						// Antonio Germani - aggiorno la tabella campi se c'è un campo inserito (cioè >0) e se l'operazione è uno scarico (cioè operat<0) e se la data di fine sospensione già presente nel campo è inferiore alla data di sospensione del prodotto appena usato (cioè $fine_sosp<$dt)
						//Antonio Germani per prima cosa determino il codice del movimento che eventualmente andrà nella tabella del campo di coltivazione
						if (!isset($_POST['Update'])) {
							// Antonio Germani se è un iserimento vedo quale sarà il prossimo codice del movimento del magazzino che verrà utilizzato !NB il codice è incremental!
							$query = "SHOW TABLE STATUS LIKE '" . $gTables['movmag'] . "'";
							$result = gaz_dbi_query($query);
							$row = $result->fetch_assoc();
							$id_mov = $row['Auto_increment'];
							// siccome ha già registrato il movimento di magazzino devo togliere 1
							$id_mov = $id_mov - 1;
						} else { // se non è un nuovo inserimento prendo il codice del movimento di magazzino selezionato
							$id_mov = $form['id_mov'];
						}
						// adesso vedo se si deve aggiornare il campo di coltivazione
						if ($form['luogo_produzione'.$n] > 0 && $form['operat'] < 0) {
							/* Antonio Germani creo la data del trattamento selezionato a cui poi aggiungerò i giorni di sospensione. */
							$dt = substr("0" . $form['giodoc'], -2) . "-" . substr("0" . $form['mesdoc'], -2) . "-" . $form['anndoc'];
							$dt = strtotime($dt);
							// se è presente prendo il tempo di sospensione specifico
							$query = "SELECT " . 'tempo_sosp' . " FROM " . $gTables['camp_uso_fitofarmaci'] . " WHERE cod_art ='" . $form['artico'][$form['mov']] . "' AND id_colt ='" . $form['id_colture'] . "' AND id_avv ='" . $form['id_avversita'][$form['mov']] . "'";
							$result = gaz_dbi_query($query);
							while ($row = $result->fetch_assoc()) {
								$tempo_sosp = $row['tempo_sosp'];
							}
							if ($tempo_sosp == 0) { // se non è presente un tempo di sospensione specifico prendo quello generico. Lo metto in $temp_sosp
								$item = gaz_dbi_get_row($gTables['artico'], "codice", $form['artico'][$form['mov']]);
								$temp_sosp = $item['tempo_sospensione'];
							} else {
								$temp_sosp = $tempo_sosp; // se è presente in $temp_sosp ci metto quello specifico                            
							}
							$dt = $dt + (86400 * intval($temp_sosp)); //al giorno di attuazione i giorni di sospensione (Un giorno = 86400 timestamp)
							// Antonio Germani controllo se il tempo di sospensione del campo di coltivazione è inferiore a quello che si crea con questo trattamento. Se lo è aggiorno il database campi nel campo di coltivazione selezionato
							
							if ($fine_sosp < $dt) {
								$dt = date('Y/m/d', $dt);
								$query = "UPDATE " . $gTables['campi'] . " SET giorno_decadimento = '" . $dt . "' , codice_prodotto_usato = '" . $artico . "' , id_mov = '" . $id_mov . "' , id_colture = '" . $form['id_colture'] . "' WHERE codice ='" . $form['luogo_produzione'.$n] . "'";
								gaz_dbi_query($query);
							} else { // altrimenti
								if ($toDo == "update") { // se è un update, devo vedere se ci sono altri movimenti con un tempo superiore
									// prendo tutti i movimenti di magazzino che hanno interessato il campo di coltivazione escludendo il movimento oggetto di update
									$n = 0;
									$array = array();
									$query = "SELECT " . '*' . " FROM " . $gTables['movmag'] . " WHERE luogo_produzione ='" . $form['luogo_produzione'.$n] . "' AND operat ='-1' AND id_mov <> " . $id_movmag;
									$result = gaz_dbi_query($query);
									while ($row = $result->fetch_assoc()) {
										// cerco i giorni di sospensione del prodotto interessato ad ogni movimento
										$artico = $row['artico'];
										$id_avversita = $row['id_avversita'];
										$id_colture = $row['id_colture'];
										$form3 = gaz_dbi_get_row($gTables['artico'], 'codice', $artico);
										$temp_sosp = $form3['tempo_sospensione'];
										// se è presente prendo il tempo di sospensione specifico altrimenti lascio quello generico
										$query2 = "SELECT " . 'tempo_sosp' . " FROM " . $gTables['camp_uso_fitofarmaci'] . " WHERE cod_art ='" . $artico . "' AND id_colt ='" . $id_colture . "' AND id_avv ='" . $id_avversita . "'";
										$result2 = gaz_dbi_query($query2);
										while ($row2 = $result2->fetch_assoc()) {
											$temp_sosp = $row2['tempo_sosp'];
										}
										// creo un array con tempo di sospensione + codice articolo + movimento magazzino
										$temp_deca = (intval($temp_sosp) * 86400) + strtotime($row["datdoc"]);
										$array[$n] = array('temp_deca' => $temp_deca, 'datdoc' => $row["datdoc"], 'artico' => $artico, 'id_mov' => $row["id_mov"]);
										$n = $n + 1;
										// ordino l'array per tempo di sospensione
                                    
									}
									rsort($array);
									$dt_db_movmag = date('Y/m/d', $array[0]['temp_deca']);
									if ($n > 0 && $fine_sosp < $array[0]['temp_deca'] && $array[0]['temp_deca'] > $dt) { //se la data nel campo è minore della data trovata nei movimenti di magazzino che è maggiore di quella di questo movimento
										// memorizzo nel campo la data trovata nei movimenti
										$query = "UPDATE " . $gTables['campi'] . " SET giorno_decadimento = '" . $dt_db_movmag . "' , codice_prodotto_usato = '" . $array[0]['artico'] . "' , id_mov = '" . $array[0]['id_mov'] . "' WHERE codice ='" . $form['luogo_produzione'.$n] . "'";
										gaz_dbi_query($query);
									} elseif ($n > 0 && $fine_sosp > $array[0]['temp_deca'] && $array[0]['temp_deca'] > $dt) { // se la data nel campo è maggiore della data trovata nei movimenti di magazzino e la data trovata nei movimenti di magazzino è maggiore di quella di questo movimento
										// memorizzo nel campo la data trovata nei movimenti di magazzino
										$query = "UPDATE " . $gTables['campi'] . " SET giorno_decadimento = '" . date('Y/m/d', $array[0]['temp_deca']) . "' , codice_prodotto_usato = '" . $artico . "' , id_mov = '" . $array[0]['id_mov'] . "' WHERE codice ='" . $form['luogo_produzione'.$n] . "'";
										gaz_dbi_query($query);
									} elseif ($n == 1 && $dt > $array[0]['temp_deca']) { // se c'è un solo movimento di magazzino, oltre a questo in update, e la data di questo movimento è maggiore di quella del movimento di magazzino
										// memorizzo nel campo la data di questo movimento
										$query = "UPDATE " . $gTables['campi'] . " SET giorno_decadimento = '" . date('Y/m/d', $dt) . "' , codice_prodotto_usato = '" . $artico . "' , id_mov = '" . $id_mov . "' WHERE codice ='" . $form['luogo_produzione'.$n] . "'";
										gaz_dbi_query($query);
									} elseif ($n == 0) { // se non ci altri movimenti di magazzino, cioè questo è unico
										// memorizzo nel campo la data di questo movimento
										$query = "UPDATE " . $gTables['campi'] . " SET giorno_decadimento = '" . date('Y/m/d', $dt) . "' , codice_prodotto_usato = '" . $artico . "' , id_mov = '" . $id_mov . "' WHERE codice ='" . $form['luogo_produzione'.$n] . "'";
										gaz_dbi_query($query);
									} else { // altrimenti non faccio nulla perché va bene la data memorizzata in precedenza nel campo
                                    
									}
								}
							}
						}
						// fine gestione giorno di sospensione tabella campi
						// aggiornare tabella campi se è stata cambiata la coltura
						if ($form['luogo_produzione'.$n] > 0) { // se c'è un campo di coltivazione
							$result = gaz_dbi_get_row($gTables['campi'], "codice", $form['luogo_produzione'.$n]);
							if ($result['id_colture'] <> $form['id_colture']) { // se è stato cambiato lo aggiorno
								$query = "UPDATE " . $gTables['campi'] . " SET id_colture = '" . $form['id_colture'] . "' WHERE codice ='" . $form['luogo_produzione'.$n] . "'";
								gaz_dbi_query($query);
							}
						}
						// fine aggiorna campi coltura
					
					} // fine ciclo i campi inseriti
					
                    // INIZIO gestione registrazione database operai
                    If (intval($form['staff'][$form['mov']]) > 0) {
                        $id_worker = $form['staff'][$form['mov']]; //identificativo operaio
                        $form['datdocin']; // questa è la data documento iniziale
                        $work_day = $form['anndoc'] . "-" . $form['mesdoc'] . "-" . $form['giodoc']; // giorno lavorato
                        $hours_form = $form['quanti'][$form['mov']]; //ore lavorate normali del form
                        $id_orderman = $form['id_orderman'];
                        // controllo se è una variazione movimento e se è stato cambiato l'operaio
                        $res2 = gaz_dbi_get_row($gTables['staff'], "id_clfoco", $form['clfocoin']);
                        If ($toDo == "update" && $res2['id_staff'] <> $id_worker) { // se è stato cambiato l'operaio
                            If (strtotime($work_day) == strtotime($form['datdocin'])) { // se non è stata cambiata la data documento
                                // all'operaio iniziale, cioè quello che è stato sostituito, devo togliere le ore
                                $rin = gaz_dbi_get_row($gTables['staff_worked_hours'], "id_staff", $res2['id_staff'], "AND work_day = '$work_day'");
                                If (isset($rin)) { // se esiste giorno e operaio vedo se ci sono ore normali lavorate e tolgo quelle odierne
                                    $hours_normal = $rin['hours_normal'] - $form['quantiin']; // e faccio l'UPDATE - NON tocco id_orderman ma ATTENZIONE
                                    // la gestione della tabella "staff_worked_hours" sarebbe da rivedere perché non contempla che un operaio possa lavorare a più produzioni (id_orderman) nello stesso giorno !!!
                                    $query = 'UPDATE ' . $gTables['staff_worked_hours'] . ' SET id_staff =' . $res2['id_staff'] . ", work_day = '" . $work_day . "', hours_normal = '" . $hours_normal . "' WHERE id_staff = '" . $res2['id_staff'] . "' AND work_day = '" . $work_day . "'";
                                    gaz_dbi_query($query);
                                }
                                // al nuovo operaio devo aggiungere le ore lavorate
                                $r = gaz_dbi_get_row($gTables['staff_worked_hours'], "id_staff", $id_worker, "AND work_day = '$work_day'");
                                If (isset($r)) { // se esiste giorno e operaio vedo se ci sono ore normali lavorate
                                    $ore_lavorate = $r['hours_normal'];
                                } else {
                                    $ore_lavorate = 0;
                                }
                                $hours_normal = $ore_lavorate + $hours_form;
                                // salvo ore su operaio attuale
                                $exist = gaz_dbi_record_count($gTables['staff_worked_hours'], "work_day = '" . $work_day . "' AND id_staff = " . $id_worker);
                                if ($exist >= 1) { // se ho già un record del lavoratore per quella data faccio UPDATE
                                    $query = 'UPDATE ' . $gTables['staff_worked_hours'] . ' SET id_staff =' . $id_worker . ", id_orderman = '" . $id_orderman . "', work_day = '" . $work_day . "', hours_normal = '" . $hours_normal . "' WHERE id_staff = '" . $id_worker . "' AND work_day = '" . $work_day . "'";
                                    gaz_dbi_query($query);
                                } else { // altrimenti faccio l'INSERT
                                    $v = array();
                                    $v['id_staff'] = $id_worker;
                                    $v['work_day'] = $work_day;
                                    $v['hours_normal'] = $hours_normal;
                                    $v['id_orderman'] = $id_orderman;
                                    gaz_dbi_table_insert('staff_worked_hours', $v);
                                }
                            } else { // se è stata cambiata la data documento (giorno lavorato)
                                // all'operaio iniziale, cioè quello che è stato sostituito, devo togliere le ore nel giorno iniziale
                                $rin = gaz_dbi_get_row($gTables['staff_worked_hours'], "id_staff", $res2['id_staff'], "AND work_day = '{$form['datdocin']}'");
                                If (isset($rin)) { // se esiste giorno e operaio vedo se ci sono ore normali lavorate e tolgo quelle odierne
                                    $hours_normal = $rin['hours_normal'] - $form['quantiin']; // e faccio l'UPDATE - NON tocco id_orderman
                                    $query = 'UPDATE ' . $gTables['staff_worked_hours'] . ' SET id_staff =' . $res2['id_staff'] . ", work_day = '" . $form['datdocin'] . "', hours_normal = '" . $hours_normal . "' WHERE id_staff = '" . $res2['id_staff'] . "' AND work_day = '" . $form['datdocin'] . "'";
                                    gaz_dbi_query($query);
                                }
                                // al nuovo operaio devo aggiungere le ore lavorate nel giorno del documento
                                $r = gaz_dbi_get_row($gTables['staff_worked_hours'], "id_staff", $id_worker, "AND work_day = '$work_day'");
                                If (isset($r)) { // se esiste giorno e operaio vedo se ci sono ore normali lavorate
                                    $ore_lavorate = $r['hours_normal'];
                                } else {
                                    $ore_lavorate = 0;
                                }
                                $hours_normal = $ore_lavorate + $hours_form;
                                // salvo ore su operaio attuale
                                $exist = gaz_dbi_record_count($gTables['staff_worked_hours'], "work_day = '" . $work_day . "' AND id_staff = " . $id_worker);
                                if ($exist >= 1) { // se ho già un record del lavoratore per quella data faccio UPDATE
                                    $query = 'UPDATE ' . $gTables['staff_worked_hours'] . ' SET id_staff =' . $id_worker . ", id_orderman = '" . $id_orderman . "', work_day = '" . $work_day . "', hours_normal = '" . $hours_normal . "' WHERE id_staff = '" . $id_worker . "' AND work_day = '" . $work_day . "'";
                                    gaz_dbi_query($query);
                                } else { // altrimenti faccio l'INSERT
                                    $v = array();
                                    $v['id_staff'] = $id_worker;
                                    $v['work_day'] = $work_day;
                                    $v['hours_normal'] = $hours_normal;
                                    $v['id_orderman'] = $id_orderman;
                                    gaz_dbi_table_insert('staff_worked_hours', $v);
                                }
                            }
                        } else {
                            If ($toDo == "update" && $res2['id_staff'] == $id_worker) { // se è update e NON è stato cambiato l'operaio
                                If (strtotime($work_day) <> strtotime($form['datdocin'])) { // se è stata cambiata la data
                                    // devo togliere le ore al giorno iniziale e metterle nel giorno del documento
                                    // tolgo le ore al giorno iniziale
                                    $rin = gaz_dbi_get_row($gTables['staff_worked_hours'], "id_staff", $id_worker, "AND work_day = '{$form['datdocin']}'");
                                    If (isset($rin)) { // se esiste giorno e operaio gli modifico le ore
                                        $hours_normal = $rin['hours_normal'] - $form['quantiin'];
                                        $query = 'UPDATE ' . $gTables['staff_worked_hours'] . " SET hours_normal = '" . $hours_normal . "' WHERE id_staff = '" . $id_worker . "' AND work_day = '" . $form['datdocin'] . "'";
                                        gaz_dbi_query($query);
                                    }
                                    // metto le ore del form nel giorno del documento
                                    $rin = gaz_dbi_get_row($gTables['staff_worked_hours'], "id_staff", $id_worker, "AND work_day ='$work_day'");
                                    If (isset($rin)) { // se esiste giorno e operaio gli modifico le ore
                                        $hours_normal = $rin['hours_normal'] + $hours_form;
                                        $query = 'UPDATE ' . $gTables['staff_worked_hours'] . " SET hours_normal = '" . $hours_normal . "' WHERE id_staff = '" . $id_worker . "' AND work_day = '" . $work_day . "'";
                                        gaz_dbi_query($query);
                                    } else { // altrimenti faccio l'INSERT
                                        $v = array();
                                        $v['id_staff'] = $id_worker;
                                        $v['work_day'] = $work_day;
                                        $v['hours_normal'] = $hours_form;
                                        $v['id_orderman'] = $id_orderman;
                                        gaz_dbi_table_insert('staff_worked_hours', $v);
                                    }
                                } else { //se NON è stata cambiata la data
                                    // modifico le ore nello stesso giorno del documento
                                    $rin = gaz_dbi_get_row($gTables['staff_worked_hours'], "id_staff", $id_worker, "AND work_day ='$work_day'");
                                    If (isset($rin)) { // se esiste giorno e operaio gli modifico le ore
                                        $hours_normal = $rin['hours_normal'] - $form['quantiin'] + $hours_form;
                                        $query = 'UPDATE ' . $gTables['staff_worked_hours'] . " SET hours_normal = '" . $hours_normal . "' WHERE id_staff = '" . $id_worker . "' AND work_day = '" . $work_day . "'";
                                        gaz_dbi_query($query);
                                    }
                                }
                            }
                        }
                        If ($toDo <> "update") { // se non è un update
                            $r = gaz_dbi_get_row($gTables['staff_worked_hours'], "id_staff", $id_worker, "AND work_day ='$work_day'");
                            If (isset($r)) { // se esiste giorno e operaio vedo se ci sono ore normali lavorate
                                $ore_lavorate = $r['hours_normal'];
                            } else {
                                $ore_lavorate = 0;
                            }
                            If ($toDo == "update") {
                                $hours_normal = $ore_lavorate - $form['quantiin'] + $hours_form;
                            } else {
                                $hours_normal = $ore_lavorate + $hours_form;
                            }
                            // salvo ore su operaio attuale
                            $exist = gaz_dbi_record_count($gTables['staff_worked_hours'], "work_day = '" . $work_day . "' AND id_staff = " . $id_worker);
                            if ($exist >= 1) { // se ho già un record del lavoratore per quella data faccio UPDATE
                                $query = 'UPDATE ' . $gTables['staff_worked_hours'] . ' SET id_staff =' . $id_worker . ", id_orderman = '" . $id_orderman . "', work_day = '" . $work_day . "', hours_normal = '" . $hours_normal . "' WHERE id_staff = '" . $id_worker . "' AND work_day = '" . $work_day . "'";
                                gaz_dbi_query($query);
                            } else { // altrimenti faccio l'INSERT
                                $v = array();
                                $v['id_staff'] = $id_worker;
                                $v['work_day'] = $work_day;
                                $v['hours_normal'] = $hours_normal;
                                $v['id_orderman'] = $id_orderman;
                                gaz_dbi_table_insert('staff_worked_hours', $v);
                            }
                        }
                    }
                    // FINE gestione registrazione database operai
                    
                } // fine se c'è un articolo impostato nel movimento
            } //fine ciclo for mov
            header("Location:camp_report_movmag.php");
            exit;
        }
    }
    // §§§§§§§§§§§§§§§§§§§§  FINE salvataggio sui database §§§§§§§§§§§§§§§§§§§
    
} elseif (!isset($_POST['Insert'])) { //se e' il primo accesso per INSERT
    $form['hidden_req'] = '';
    //registri per il form della testata
    $form['id_mov'] = 0;
	$form['id_mov2'] = "";
    $form['type_mov'] = 1;
    $form['gioreg'] = date("d");
    $form['mesreg'] = date("m");
    $form['annreg'] = date("Y");
    $form['caumag'] = "";
	$res_caumag = gaz_dbi_get_row($gTables['caumag'], "codice", $form['caumag']);
	$form['ncamp']=1;
    $form['luogo_produzione1']= ""; //campo di coltivazione
    $form['clfocoin'] = 0;
    $form['quantiin'] = 0;
    $form['datdocin'] = "";
    $form['adminid'] = $admin_aziend['id_anagra'];
	if (intval($form['adminid'])>0){ // se l'amministratore è presente in anagrafica lo prendo
	$form['adminname']=$admin_aziend['user_firstname']." ".$admin_aziend['user_lastname'];
	} else {
		$form['adminname']="";
	}
	$form['confermapat'][$form['adminid']] = "";
	$form['rif_abilitazione'] = "";
	$form['patent_number'] = "";
	$form['patent_expiry'] = "";
    $form['tipdoc'] = "";
    $form['desdoc'] = "";
    $form['giodoc'] = date("d");
    $form['mesdoc'] = date("m");
    $form['anndoc'] = date("Y");
    $form['scochi'] = 0;
    $form['id_colture'] = 0;
    $form['nome_colt'] = "";
    $form['mov'] = 0;
    $form['operat'] = 0;
    $form['nome_avv'][$form['mov']] = "";
	$form['fase_feno'][$form['mov']] = "";
    $form['id_avversita'][$form['mov']] = 0;
    $form['artico'][$form['mov']] = "";
	$form['id_reg'][$form['mov']] = 0;
	$form['conferma'][$form['mov']] = "";
	$form['artico2'][$form['mov']] = "ACQUA";// pre imposto l'articolo acqua
    $form['id_lotmag'][$form['mov']] = 0;
    $form['identifier'][$form['mov']] = "";
    $form['expiry'][$form['mov']] = "";
    $form['filename'][$form['mov']] = "";
    $form['lot_or_serial'][$form['mov']] = "";
    $form['quanti'][$form['mov']] = 0;
    $form['prezzo'][$form['mov']] = 0;
    $form['scorig'][$form['mov']] = 0;
	$form['quanti2'][$form['mov']] = 0;
    $form['prezzo2'][$form['mov']] = 0;
    $form['scorig2'][$form['mov']] = 0;
    $form['clfoco'][$form['mov']] = 0;
    $form['status'] = "";
    $form['search_partner'] = "";
    $form['search_item'] = "";
    $form['id_rif'] = 0;
    $form['description'] = "";
    $form['id_orderman'] = 0;
	$form['coseprod']="";
    $form['nmov'] = 0;
    $form['staff'][$form['mov']] = "";
	if (!$feno = gaz_dbi_get_row($gTables['company_data'], "var", "feno_json")){
		$feno_json="";
	} else {
		
		$feno_json = $feno['data'];
	}
}

// Antonio Germani questo serve per aggiungere un movimento
if (isset($_POST['Add_mov'])) { 
    $form['nmov'] = $_POST['nmov'];
    for ($m = 0;$m <= $form['nmov'];++$m) {
        $form['artico'][$m] = $_POST['artico' . $m];
		$form['id_reg'][$m] = $_POST['id_reg' . $m];
		$form['conferma'][$m] = $_POST['conferma' . $m];
		$form['artico2'][$m] = $_POST['artico2' . $m];
        $form['id_lotmag'][$m] = $_POST['id_lotmag' . $m];
        $form['lot_or_serial'][$m] = $_POST['lot_or_serial' . $m];
        if ($form['lot_or_serial'][$m] == 1) {
            $form['identifier'][$m] = $_POST['identifier' . $m];
            $form['expiry'][$m] = $_POST['expiry' . $m];
            $form['filename'][$m] = $_POST['filename' . $m];
        } else {
            $form['identifier'][$m] = "";
            $form['expiry'][$m] = "";
            $form['filename'][$m] = "";
        }
        $form['quanti'][$m] = gaz_format_quantity($_POST['quanti' . $m], 0, $admin_aziend['decimal_quantity']);
        $form['prezzo'][$m] = gaz_format_quantity($_POST['prezzo' . $m], 0, $admin_aziend['decimal_quantity']);
        $form['scorig'][$m] = $_POST['scorig' . $m];
		if (isset($_POST['quanti2' . $m])){
			$form['quanti2'][$m] = gaz_format_quantity($_POST['quanti2' . $m], 0, $admin_aziend['decimal_quantity']);
		} else {
			$form['quanti2'][$m] = 0;
		}
        $form['prezzo2'][$m] = gaz_format_quantity($_POST['prezzo2' . $m], 0, $admin_aziend['decimal_quantity']);
        $form['scorig2'][$m] = $_POST['scorig2' . $m];
        $form['staff'][$m] = intval($_POST['staff' . $m]);
        $form['clfoco'][$m] = $_POST['clfoco' . $m];
        $form['nome_avv'][$m] = $_POST['nome_avv' . $m];
		$form['fase_feno'][$m] = $_POST['fase_feno' . $m];
        $form['id_avversita'][$m] = $_POST['id_avversita' . $m];
    }
    $form['nmov'] = $form['nmov'] + 1;
    $form['artico'][$form['nmov']] = "";
	$form['id_reg'][$form['nmov']] = 0;
	$form['conferma'][$form['nmov']] = "";
	$form['artico2'][$form['nmov']] = "";
    $form['id_lotmag'][$form['nmov']] = 0;
    $form['identifier'][$form['nmov']] = "";
    $form['expiry'][$form['nmov']] = "";
    $form['filename'][$form['nmov']] = "";
    $form['lot_or_serial'][$form['nmov']] = "";
    $form['quanti'][$form['nmov']] = 0;    
    $form['prezzo'][$form['nmov']] = 0;
    $form['scorig'][$form['nmov']] = 0;
	$form['quanti2'][$form['nmov']] = 0;    
    $form['prezzo2'][$form['nmov']] = 0;
    $form['scorig2'][$form['nmov']] = 0;
    $form['staff'][$form['nmov']] = "";
    $form['clfoco'][$form['nmov']] = 0;
    $form['nome_avv'][$form['nmov']] = "";
	$form['fase_feno'][$form['nmov']] = "";
    $form['id_avversita'][$form['nmov']] = 0;
}
// Antonio Germani questo serve per togliere un movimento
if (isset($_POST['Del_mov'])) {
    $form['artico'][$form['nmov']] = "";
	$form['conferma'][$form['nmov']] = "";
	$form['artico2'][$form['nmov']] = "";
    $form['id_lotmag'][$form['nmov']] = 0;
    $form['identifier'][$form['nmov']] = "";
    $form['expiry'][$form['nmov']] = "";
    $form['filename'][$form['nmov']] = "";
    $form['lot_or_serial'][$form['nmov']] = "";
    $form['quanti'][$form['nmov']] = 0;    
    $form['prezzo'][$form['nmov']] = 0;
    $form['scorig'][$form['nmov']] = 0;
	$form['quanti2'][$form['nmov']] = 0;    
    $form['prezzo2'][$form['nmov']] = 0;
    $form['scorig2'][$form['nmov']] = 0;
    $form['staff'][$form['nmov']] = "";
    $form['clfoco'][$form['nmov']] = 0;
    $form['nome_avv'][$form['nmov']] = "";
    $form['id_avversita'][$form['nmov']] = 0;
    If ($_POST['nmov'] > 0) {
        $form['nmov'] = $form['nmov'] - 1;
        $form['mov'] = $form['mov'] - 1;
    }
}
if (!empty($_FILES['docfile_' . $form['mov']]['name'])) { // Antonio Germani - se c'è un nome in $_FILES
    $prefix = $admin_aziend['adminid'] . '_' . $admin_aziend['company_id'] . '_' . $form['mov'];
    foreach (glob(DATA_DIR."files/tmp/" . $prefix . "_*.*") as $fn) { // prima cancello eventuali precedenti file temporanei
        unlink($fn);
    }
    $mt = substr($_FILES['docfile_' . $form['mov']]['name'], -3);
    if (($mt == "png" || $mt == "odt" || $mt == "peg" || $mt == "jpg" || $mt == "pdf") && $_FILES['docfile_' . $form['mov']]['size'] > 1000) { // se rispetta limiti e parametri lo salvo nella cartella tmp
        move_uploaded_file($_FILES['docfile_' . $form['mov']]['tmp_name'], DATA_DIR.'files/tmp/' . $prefix . '_' . $_FILES['docfile_' . $form['mov']]['name']);
        $form['filename'][$form['mov']] = $_FILES['docfile_' . $form['mov']]['name'];
    } else {
        $msg.= "39+";
    }
}
if (isset($_POST['acquis']) AND isset($fornitore) ) { //compilazione ordine a fornitore
    $item = gaz_dbi_get_row($gTables['artico'], "codice", $form['artico'][$form['mov']]);
    $scorta = $item['scorta']; // prendo la scorta minima
    $fornitore = $item['clfoco']; //prendo codice clfoco per codice fornitore per ordine a fornitore
    ?>
	<form action="../../modules/acquis/admin_broacq.php?tipdoc=AOR" method="post" name="docacq">
		<input type="hidden" name="Insert" value="insert">
		<input type="hidden" value="AOR" name="tipdoc">
		<input type="hidden" name="clfoco" value="<?php echo $fornitore; ?>"> <!-- questo è il fornitore che devo prendere dalla tabella atico, colonna clfoco se è zero devo bloccare perché non è stato inserito il fornitore nell'articolo -->
		<input type="hidden" name="search[clfoco]" value="<?php echo $fornitore; ?>">
		<input type="hidden" name="gioemi" value="<?php echo date('d'); ?>"> <!-- giorno -->
		<input type="hidden" name="mesemi" value="<?php echo date('m'); ?>"> <!-- mese -->
		<input type="hidden" name="annemi" value="<?php echo date('Y'); ?>"><!-- anno -->
		<input type="hidden" value="INSERT" name="in_status">
		<input type="hidden" name="in_codart" value="<?php echo $form['artico'][$form['mov']]; ?>">
		
		<input type="hidden" value="<?php echo $scorta; ?>"  name="in_quanti"> 
		<input type="hidden" name="in_codric" value="330000004">
		<input type="hidden" value="<?php echo $form['artico'][$form['mov']]; ?>" name="codart">
		<script type="text/javascript" >
			document.forms["docacq"].submit(); 
		</script>
	</form>
	<?php    
}

if (isset($_POST['cancel'])) {// se è stato premuto annulla
    $form['hidden_req'] = ''; 
    //registri per il form della testata
    $form['id_mov'] = 0;
	$form['id_mov2'] = "";
    $form['type_mov'] = 1;
    $form['gioreg'] = date("d");
    $form['mesreg'] = date("m");
    $form['annreg'] = date("Y");
    $form['caumag'] = "";
    $form['luogo_produzione'] = ""; //campo di coltivazione
    $form['adminid'] = "Utente connesso";
	$form['confermapat'][$form['adminid']] = "";
    $form['tipdoc'] = "";
    $form['desdoc'] = "";
    $form['giodoc'] = date("d");
    $form['mesdoc'] = date("m");
    $form['anndoc'] = date("Y");
    $form['scochi'] = 0;
    $form['id_avversita'][$form['mov']] = 0;
    $form['nome_avv'][$form['mov']] = "";
	$form['fase_feno'][$form['mov']] = "";
    $form['id_colture'] = 0;
    $form['nome_colt'] = "";
    $form['nmov'] = 0;
    $form['mov'] = 0;
    $form['operat'] = "";
    $form['artico'][$form['mov']] = "";
	$form['conferma'][$form['mov']] = "";
	$form['artico2'][$form['mov']] = "";
    $form['id_lotmag'][$form['mov']] = 0;
    $form['identifier'][$form['mov']] = "";
    $form['expiry'][$form['mov']] = "";
    $form['filename'][$form['mov']] = "";
    $form['lot_or_serial'][$form['mov']] = "";
    $form['prezzo'][$form['mov']] = 0;
    $form['scorig'][$form['mov']] = 0;
    $form['quanti'][$form['mov']] = 0;
	$form['prezzo2'][$form['mov']] = 0;
    $form['scorig2'][$form['mov']] = 0;
    $form['quanti2'][$form['mov']] = 0;
    $form['staff'][$form['mov']] = "";
    $form['clfoco'][$form['mov']] = 0;
    $form['clfocoin'] = 0;
    $form['quantiin'] = 0;
    $form['datdocin'] = "";
    $form['status'] = "";
    $form['search_partner'] = "";
    $form['search_item'] = "";
    $form['id_rif'] = 0;
    $form['description'] = "";
    $form['id_orderman'] = 0;
	$form['coseprod']="";
    $fornitore = "";
}
// Antonio Germani controllo e avviso se è stata cambiata la coltura nel campo di coltivazione
if (isset($_POST['nome_colt'])) {
	if ($form['luogo_produzione1'] > 0) { // se c'è un campo di coltivazione
		$result = gaz_dbi_get_row($gTables['campi'], "codice", $form['luogo_produzione1']);
		if (isset($result) AND $result['id_colture'] <> $form['id_colture']) { // se è stata cambiata la coltura avviso
			$err = gaz_dbi_get_row($gTables['camp_colture'], "id_colt", $result['id_colture']);
			if (!isset($err)){
				$err['nome_colt']="Nessuna coltura";
			}
			$instantwarning="Nel campo di coltivazione è presente la coltura ". $result['id_colture'] ." - ". $err['nome_colt']. " che è diversa da quella inserita. Se si conferma, verrà modificata la coltura nel campo di coltivazione!";
		}
	}
}
require ("../../library/include/header.php");
$script_transl = HeadMain(0,array('custom/autocomplete',));
require ("./lang." . $admin_aziend['lang'] . ".php");
?>
<!-- spengo pannello gestione patentino -->
<style>#gestpatent { display:none; }</style>
<!-- spengo pannello gestione fasi fenologiche -->
<style>#gestfeno { display:none; }</style>
<?php


// Antonio Germani segnalo i warning immediati
if (strlen($instantwarning)>0) {
	?>
	<div class="alert alert-warning alert-dismissible">
		<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<strong>Warning!</strong> <?php echo $instantwarning; ?>
	</div>
	<?php
}
if ($avv_conf==1) { // segnalo autorizzazione fitofarmaco scaduta con scelta
	echo "<script type='text/javascript'> $(window).load(function(){ $('#scadaut').modal('show'); }); </script>";	
}
if ($avv_conf==2 AND $form['confermapat'][$form['adminid']]!=="Confermo deroga" ) { // segnalo autorizzazione patentinoscaduta con scelta
	echo "<script type='text/javascript'> $(window).load(function(){ $('#patexp').modal('show'); }); </script>";	
}
if ($avv_conf==3 AND $form['confermapat'][$form['adminid']]!=="Confermo deroga" ) { // segnalo mancanza autorizzazione patentino con scelta
	echo "<script type='text/javascript'> $(window).load(function(){ $('#patempty').modal('show'); }); </script>";	
}
?>

<script>
<!-- Antonio Germani inizio script autocompletamento per fasi fenologiche	-->	
	$(document).ready(function() {
	$("input#autocomplete5").autocomplete({
		source: [<?php
$stringa = "";
$result = json_decode ($feno_json);
foreach ($result as $row) {
    $stringa.= "\"" . $row . "\", ";
}
$stringa = substr($stringa, 0, -1);
echo $stringa;
?>],
		minLength:1,
	select: function(event, ui) {
	       //assign value back to the form element
	       if(ui.item){
	           $(event.target).val(ui.item.value);
	       }
	       //submit the form
	       $(event.target.form).submit();
	   }
	});
	});
<!-- fine autocompletamento -->

<!-- Antonio Germani inizio script autocompletamento dalla tabella mysql camp_colture	-->	
	$(document).ready(function() {
	$("input#autocomplete4").autocomplete({
		source: [<?php
$stringa = "";
$query = "SELECT * FROM " . $gTables['camp_colture'];
$result = gaz_dbi_query($query);
while ($row = $result->fetch_assoc()) {
    $stringa.= "\"" . $row['id_colt'] . " - " . $row['nome_colt'] . "\", ";
}
$stringa = substr($stringa, 0, -1);
echo $stringa;
?>],
		minLength:1,
	select: function(event, ui) {
	       //assign value back to the form element
	       if(ui.item){
	           $(event.target).val(ui.item.value);
	       }
	       //submit the form
	       $(event.target.form).submit();
	   }
	});
	});
<!-- fine autocompletamento -->
	
<!-- inizio popup orderman -->	
	var stile = "top=10, left=10, width=600, height=800 status=no, menubar=no, toolbar=no scrollbar=no";
	   function Popup(apri) {
	      window.open(apri, "", stile);
	   }
<!-- fine popup orderman -->

<!-- Antonio Germani inizio script autocompletamento dalla tabella mysql camp_avversita	-->	
	$(document).ready(function() {
	$("input#autocomplete3").autocomplete({
		source: [<?php
        $stringa = "";
        $query = "SELECT * FROM " . $gTables['camp_avversita'];
        $result = gaz_dbi_query($query);
        while ($row = $result->fetch_assoc()) {
            $stringa.= "\"" . $row['id_avv'] . " - " . $row['nome_avv'] . "\", ";
        }
        $stringa = substr($stringa, 0, -1);
        echo $stringa;
?>],
		minLength:1,
	select: function(event, ui) {
	       //assign value back to the form element
	       if(ui.item){
	           $(event.target).val(ui.item.value);
	       }
	       //submit the form
	       $(event.target.form).submit();
	   }
	});
	});
<!-- fine autocompletamento avversita -->

<!-- inizio datepicker -->
	$(function() {
	  $( ".datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
	});
<!-- fine datepicker -->

<!-- Antonio Germani inizio script autocompletamento dalla tabella mysql artico	-->
	$(document).ready(function(){
	//Autocomplete search using PHP, MySQLi, Ajax and jQuery
	//generate suggestion on keyup
		$('#codart').keyup(function(e){
			e.preventDefault();
			var form = $('#mov-camp').serialize();
			$.ajax({
				type: 'POST',
				url: 'do_search.php',
				data: form,
				dataType: 'json',
				success: function(response){
					if(response.error){
						$('#codart_search').hide();
					}
					else{
						$('#codart_search').show().html(response.data);
					}
				}
			});
		});
		//fill the input
		$(document).on('click', '.dropdown-item2', function(e){
			e.preventDefault();
			$('#codart_search').hide();
			var fullname = $(this).data('fullname');
			$('#codart').val(fullname);
			$('#mov-camp').submit();
		});
	});
<!-- fine autocompletamento -->	
<!-- Antonio Germani inizio script autocompletamento dalla tabella mysql artico PER ACQUA	-->
	$(document).ready(function(){
	//Autocomplete search using PHP, MySQLi, Ajax and jQuery
	//generate suggestion on keyup
		$('#codart2').keyup(function(e){
			e.preventDefault();
			var form = $('#mov-camp').serialize();
			$.ajax({
				type: 'POST',
				url: 'do_search2.php',
				data: form,
				dataType: 'json',
				success: function(response){
					if(response.error){
						$('#codart_search2').hide();
					}
					else{
						$('#codart_search2').show().html(response.data);
					}
				}
			});
		});
		//fill the input
		$(document).on('click', '.dropdown-item3', function(e){
			e.preventDefault();
			$('#codart_search2').hide();
			var fullname = $(this).data('fullname');
			$('#codart2').val(fullname);
			$('#mov-camp').submit();
		});
	});
<!-- fine autocompletamento -->

</script>

<?php
if ($form['id_mov'] > 0) {
    $title = ucfirst($script_transl[$toDo] . $script_transl[0]) . " n." . $form['id_mov'];
} else {
    $title = ucfirst($script_transl[$toDo] . $script_transl[0]);
}
if (intval($form['nome_colt']) == 0) {
    $form['nome_colt'] = "";
} 

?>

<!--   >>>>>>>>>>>    inizio FORM            >>>>>>>>>>  -->
<form method="POST" name="myform" enctype="multipart/form-data" id="mov-camp">
	<input type="hidden" name="<?php echo ucfirst($toDo) ?>" value="">
	<input type="hidden" value="<?php echo $form['hidden_req'] ?>" name="hidden_req" >
	<input type="hidden" name="ritorno" value="<?php echo $_POST['ritorno']; ?> ">
	<input type="hidden" name="id_mov" value="<?php echo $form['id_mov']; ?> ">
	<input type="hidden" name="id_mov2" value="<?php echo $form['id_mov2']; ?> ">
	<input type="hidden" name="nmov" value="<?php echo $form['nmov']; ?> ">
	<input type="hidden" name="id_rif" value="<?php echo $form['id_rif']; ?> ">
	<input type="hidden" name="tipdoc" value="<?php echo $form['tipdoc']; ?> ">
	<input type="hidden" name="status" value="<?php echo $form['status']; ?> ">
	<input type="hidden" name="clfocoin" value="<?php echo $form['clfocoin']; ?> ">
	<input type="hidden" name="quantiin" value="<?php echo $form['quantiin']; ?> ">
	<input type="hidden" name="datdocin" value="<?php echo $form['datdocin']; ?> ">
	<input type="hidden" name="confermapat<?php echo $form['adminid']; ?>" value="<?php echo $form['confermapat'][$form['adminid']]; ?>">
	<input type="hidden" name="feno_json" value="<?php echo htmlspecialchars($feno_json); ?> ">
	<div align="center" class="FacetFormHeaderFont"><?php echo $title; ?>
	</div>
	
	<div class="panel panel-default gaz-table-form div-bordered">
		<div class="container-fluid">
			<?php
			$importo_rigo = CalcolaImportoRigo($form['quanti'][$form['mov']], $form['prezzo'][$form['mov']], $form['scorig'][$form['mov']]);
			$importo_rigo = $importo_rigo+CalcolaImportoRigo($form['quanti2'][$form['mov']], $form['prezzo2'][$form['mov']], $form['scorig2'][$form['mov']]);

			$importo_totale = CalcolaImportoRigo(1, $importo_rigo, $form['scochi']);

			if (!empty($msg)) {
				$message = "";
				$rsmsg = array_slice(explode('+', chop($msg)), 0, -1);
				foreach ($rsmsg as $value) {
					$message.= $script_transl['error'] . "! &#8658; ";
					$rsval = explode('-', chop($value));
					foreach ($rsval as $valmsg) {
						$message.= $script_transl[$valmsg] . " ";
					}
					$message.= "<br />";
				}
				?>
				<div class="row bg-info"><td colspan="3" class="FacetDataTDred">
					<div class="row bg-danger"><!-- CAMPO coltivazione -->
						<p>
						<?php echo $message; ?>
						</p>
					</div>
				</div>				
				<?php
			}
			
			?>
			<div class="row"><!-- Inserimento produzione -->
				<div class="col-md-12">
					<div class="form-group">
					<label class="FacetFieldCaptionTD">
						<?php echo $script_transl[29]; ?>
					</label>
				
					<?php
					$select_production = new selectproduction("description");
					$select_production->addSelected($form['description']);			
					$select_production->output($form['coseprod']);
					?>				  
					<button type="submit" name="erase2" title="Reset produzione" class="btn btn-default"  style="border-radius= 85px; "> <i class="glyphicon glyphicon-remove-circle"></i></button>
					<br>
					<a href="javascript:Popup('../../modules/orderman/admin_orderman.php?Insert&popup=1&type=AGR')"> Crea nuova produzione <i class="glyphicon glyphicon-plus-sign" style="color:green" ></i></a>
					</div>
				</div>
			</div><!-- chiude row  -->
			<div class="row bg-info"><!-- CAMPO coltivazione -->
				<div class="col-md-12">
					<div class="form-group">
					<label class="FacetFieldCaptionTD">
						<?php echo $script_transl[3];?> 
					</label>
					
						<?php
						
						for ($n = 1;$n <= $form['ncamp'];++$n){ // ciclo i campi inseriti
							$gForm->selectFromDB('campi', 'luogo_produzione'.$n ,'codice', $form['luogo_produzione'.$n], 'codice', 1, ' - ','descri','TRUE','FacetSelect' , null, '');
						}
						$form['luogo_produzione'.$n]="";
						if ($n>1 AND $form['luogo_produzione'.($n-1)]>0){ // permetto di inserire un nuovo campo
							$gForm->selectFromDB('campi', 'luogo_produzione'.$n,'codice', $form['luogo_produzione'.$n], 'codice', 1, ' - ','descri','TRUE','FacetSelect' , null, '');
						}
						$form['ncamp']=$n;
						?>
						<input type="hidden" name="ncamp" value="<?php echo $form['ncamp']; ?>">
					</div>
				</div>
			</div><!-- chiude row  -->
			
			<div class="row bg-info"><!--  COLTURA -->
				<div class="col-md-12">
					<div class="form-group">
					<label class="FacetFieldCaptionTD"><?php echo $script_transl[33];?></label>
						<!-- per funzionare autocomplete, id dell'input deve essere autocomplete4 -->
						<input class="FacetSelect" id="autocomplete4" type="text" value="<?php echo $form['nome_colt']; ?>" name="nome_colt" size="30">
						<input type="hidden" value="<?php echo intval($form['nome_colt']); ?>" name="id_colture">
						<button type="submit" name="erase" title="Reset coltura" class="btn btn-default"  style="border-radius= 85px; "> <i class="glyphicon glyphicon-remove-circle"></i></button>
					</div>
				</div>
			</div><!-- chiude row  -->
			
			<div class="row bg-info"><!--   CAUSALE    -->
				<div class="col-md-12">
					<div class="form-group">
					<label class="FacetFieldCaptionTD"><?php echo $script_transl[2]; ?></label>
					<?php 
					$gForm->selectFromDB('caumag', 'caumag','codice', $form['caumag'], 'codice', 1, ' - ','descri','TRUE','FacetSelect' , null, '', '(type_cau = 1 OR type_cau = 9) AND codice < 80');
					if (isset($res_caumag['operat']) AND $res_caumag['operat'] == 0) {
						echo " Non opera";
					}
					if (isset($res_caumag['operat']) AND $res_caumag['operat'] == 1) {
						echo " Carico";
					}
					if (isset($res_caumag['operat']) AND $res_caumag['operat'] == - 1) {
						echo " Scarico";
					}
					?>
					</div>
				</div>
			</div><!-- chiude row  -->
			
			<div class="row bg-info"><!-- DATA della REGISTRAZIONE  -->
				<div class="col-md-12">
					<div class="form-group">
						<label class="FacetFieldCaptionTD">
							<?php echo $script_transl[1]; ?>
						</label>
					
						<select name="gioreg" class="FacetSelect" onchange="this.form.submit()">
						<?php
						for ($counter = 1;$counter <= 31;$counter++) {
							$selected = "";
							if ($counter == $form['gioreg']) $selected = "selected";
							echo "\t <option value=\"$counter\"  $selected >$counter</option>\n";
						}
						echo "\t </select>\n";
						echo "\t <select name=\"mesreg\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
						for ($counter = 1;$counter <= 12;$counter++) {
							$selected = "";
							if ($counter == $form['mesreg']) $selected = "selected";
							$nome_mese = ucwords(strftime("%B", mktime(0, 0, 0, $counter, 1, 0)));
							echo "\t <option value=\"$counter\"  $selected >$nome_mese</option>\n";
						}
						echo "\t </select>\n";
						echo "\t <select name=\"annreg\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
						for ($counter = date("Y") - 10;$counter <= date("Y") + 10;$counter++) {
							$selected = "";
							if ($counter == $form['annreg']) $selected = "selected";
							echo "\t <option value=\"$counter\"  $selected >$counter</option>\n";
						}
						?>
						</select>
					</div>
				</div>
			</div><!-- chiude row  -->
			
			<div class="row bg-info"><!--  DATA di ATTUAZIONE  -->
				<div class="col-md-12">
					<div class="form-group">
						<label class="FacetFieldCaptionTD">
							<?php echo $script_transl[8]; ?>
						</label>
					
						<select name="giodoc" class="FacetSelect" onchange="this.form.submit()">
						<?php
						for ($counter = 1;$counter <= 31;$counter++) {
							$selected = "";
							if ($counter == $form['giodoc']) $selected = "selected";
							echo "\t <option value=\"$counter\"  $selected >$counter</option>\n";
						}
						echo "\t </select>\n";
						echo "\t <select name=\"mesdoc\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
						for ($counter = 1;$counter <= 12;$counter++) {
							$selected = "";
							if ($counter == $form['mesdoc']) $selected = "selected";
							$nome_mese = ucwords(strftime("%B", mktime(0, 0, 0, $counter, 1, 0)));
							echo "\t <option value=\"$counter\"  $selected >$nome_mese</option>\n";
						}
						echo "\t </select>\n";
						echo "\t <select name=\"anndoc\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
						for ($counter = date("Y") - 10;$counter <= date("Y") + 10;$counter++) {
							$selected = "";
							if ($counter == $form['anndoc']) $selected = "selected";
							echo "\t <option value=\"$counter\"  $selected >$counter</option>\n";
						}
						?>
						</select>
					</div>
				</div>
			</div><!-- chiude row  -->
			
			<div class="row bg-info"><!-- ANNOTAZIONE o DESCRIZIONE DOCUMENTO -->
				<div class="col-md-12">
					<div class="form-group">
						<label class="FacetFieldCaptionTD">
							<?php echo $script_transl[9]; ?>
						</label>				
						<input class="FacetSelect" type="text" value="<?php echo $form['desdoc']; ?>" maxlength="50"  name="desdoc">
					</div>
				</div>
			</div><!-- chiude row  -->
			<div class="row">
				<label style="font-size:5pt;" colspan="4">
					Movimenti:				
				</label>
			</div><!-- chiude row  -->	
			<!-- Modal content patentino scaduto  -->
			<div id="patexp" class="modal fade" role="dialog">    
				<div class="modal-dialog modal-content bg-warning" style="background-color: #f35454;">
					<div class="modal-header" align="left">
						
						<h4 class="modal-title">ATTENZIONE patentino scaduto!</h4>
					</div>
					<div class="modal-body">
						<p>L'autorizzazione all'acquisto e all'uso di prodotti fitosanitari è scaduta. <br>Puoi continuare solo se sei a conoscenza che c'è una deroga. <br>Sei sicuro di volerlo fare?</p>
					</div>
					<div class="modal-footer">
						<input type="submit" class="btn btn-default pull-left" name="confermapat<?php echo $form['adminid']; ?>"  value="Non voglio continuare">
						<input type="submit" class="btn btn-danger pull-right" name="confermapat<?php echo $form['adminid']; ?>"  value="Confermo deroga">
					</div>
				</div>
			</div>
			<!-- Modal content patentino mancante  -->
			<div id="patempty" class="modal fade" role="dialog">    
				<div class="modal-dialog modal-content bg-warning" style="background-color: #f35454;">
					<div class="modal-header" align="left">
						
						<h4 class="modal-title">ATTENZIONE patentino mancante!</h4>
					</div>
					<div class="modal-body">
						<p>E' necessario fornire i dati dell'autorizzazione all'acquisto e all'uso di prodotti fitosanitari. <br>Puoi continuare solo se sei sicuro. <br>Sei sicuro di volerlo fare?</p>
					</div>
					<div class="modal-footer">
						<input type="submit" class="btn btn-default pull-left" name="confermapat<?php echo $form['adminid']; ?>"  value="Non voglio continuare">
						<input type="submit" class="btn btn-danger pull-right" name="confermapat<?php echo $form['adminid']; ?>"  value="Confermo deroga">
					</div>
				</div>
			</div>
			<?php
			// >>>>>>>>>> Inizio ciclo righi mov   <<<<<<<<<<<<<<<<<
			for ($form['mov'] = 0;$form['mov'] <= $form['nmov'];++$form['mov']) {
				$anchor["num"] = $form['mov']; // Antonio Germani imposto la riga che dovrà essere ancorata allo scroll
				$importo_rigo = CalcolaImportoRigo($form['quanti'][$form['mov']], $form['prezzo'][$form['mov']], $form['scorig'][$form['mov']]);
				$importo_rigo = $importo_rigo+CalcolaImportoRigo($form['quanti2'][$form['mov']], $form['prezzo2'][$form['mov']], $form['scorig2'][$form['mov']]);
				$importo_totale = CalcolaImportoRigo(1, $importo_rigo, $form['scochi']);
				?>			
				
				<div class="row"><!-- Articolo -->
					<div class="col-md-12">
						<div class="form-group">
							<label class="FacetFieldCaptionTD">
								<?php echo $script_transl[7]; ?>
							</label>
												

							<input type="hidden" name="mov" value="<?php echo $form['mov']; ?>">
							<input type="hidden" name="scochi" value="<?php echo $form['scochi']; ?>">
							<input type="hidden" name="conferma<?php echo $form['mov']; ?>" value="<?php echo $form['conferma'][$form['mov']]; ?>">
							<input type="hidden" name="id_reg<?php echo $form['mov']; ?>" value="<?php echo $form['id_reg'][$form['mov']]; ?>">
							<?php
							$messaggio = "";
							$print_unimis = "";
							
							
							if ($form['mov']==$form['nmov']){ // se è l'ultimo rigo attivo l'autocomplete					
								?>
								<div class="row">
									<div class="col-md-12">														
										<div class="form-group">									
											<input class="col-sm-7 FacetSelect" type="text" id="codart" name="codart" value="<?php echo $form['artico'][$form['mov']]; ?>" placeholder="Ricerca nome o descrizione" autocomplete="off">
											<input type="hidden" name="artico<?php echo $form['mov']; ?>" value="<?php echo $form['artico'][$form['mov']]; ?>" />
											<input type="hidden" name="artico2<?php echo $form['mov']; ?>" value="<?php echo $form['artico2'][$form['mov']]; ?>" />
											<?php 
											if (isset($res_caumag['operat']) AND $res_caumag['operat'] == - 1) {
												?>
												<input class="col-sm-2" type="text" id="codart2" name="codart2" value="<?php echo $form['artico2'][$form['mov']]; ?>" placeholder="Ricerca nome o descrizione" autocomplete="off">
												<input type="hidden" name="artico2<?php echo $form['mov']; ?>" value="<?php echo $form['artico2'][$form['mov']]; ?>" />
												<?php
																					
												if ($form['artico2'][$form['mov']]!==""){ // se è stata inserita l'acqua carico unità di misura e visualizzo input q.tà
													$itemart2 = gaz_dbi_get_row($gTables['artico'], "codice", $form['artico2'][$form['mov']]);
													$print_unimis2="";
													if (isset($itemart2)){
														$print_unimis2 = $itemart2['unimis'];
													}
													?>
													<span class="col-sm-1"><?php echo $print_unimis2;?></span>
													<input class="col-sm-2" type="text" value="<?php echo gaz_format_quantity($form['quanti2'][$form['mov']], 1, $admin_aziend['decimal_quantity']); ?>" maxlength="10" name="quanti2<?php echo $form['mov']; ?>" >
													<?php 
												}
											} else { 
												?>
												<input type="hidden" id="codart2" name="codart2" value="<?php echo $form['artico2'][$form['mov']]; ?>">										
												<input type="hidden" value="" name="quanti2<?php echo $form['mov']; ?>" >
												<?php
											}	
											?>					
										</div>
										<ul class="dropdown-menu" style="left: 10%; padding: 0px;" id="codart_search"></ul>	
										<ul class="dropdown-menu" style="left: 10%; padding: 0px;" id="codart_search2"></ul>									
									</div>
								</div><!-- chiude row  -->
						
								<!-- Modal content scadenza autorizzazione fitofarmaco -->
								<div id="scadaut" class="modal fade" role="dialog">    
									<div class="modal-dialog modal-content">
										<div class="modal-header" align="left">
											
											<h4 class="modal-title">ATTENZIONE !</h4>
										</div>
										<div class="modal-body">
											<p>Hai scelto un fitofarmaco con autorizzazione scaduta. <br>Puoi usarlo solo se sei a conoscenza che c'è una deroga. <br>Sei sicuro di volerlo fare?</p>
										</div>
										<div class="modal-footer">
											<input type="submit" class="btn btn-default pull-left" name="conferma<?php echo $form['mov']; ?>"  value="Non voglio usare <?php echo $form['artico'][$form['mov']]; ?>">
											<input type="submit" class="btn btn-danger pull-right" name="conferma<?php echo $form['mov']; ?>"  value="Confermo deroga <?php echo $form['artico'][$form['mov']]; ?>">
										</div>
									</div>
								</div>	
								
								<?php						
							} else {		
								?>
								<input type="hidden" name="artico<?php echo $form['mov']; ?>" value="<?php echo $form['artico'][$form['mov']]; ?>" />
								<input type="hidden" name="artico2<?php echo $form['mov']; ?>" value="<?php echo $form['artico2'][$form['mov']]; ?>" />
								<?php
								echo $form['artico'][$form['mov']]," - ";
							}
							
							if ($form['artico'][$form['mov']] != "") { // SE C'è UN ARTICOLO
								// carico l'articolo dell'attuale mov in itemart
								$itemart = gaz_dbi_get_row($gTables['artico'], "codice", $form['artico'][$form['mov']]);
								if (isset($itemart)){
									$print_unimis = $itemart['unimis'];						
									$dose = $itemart['dose_massima']; // prendo anche la dose
									$scorta = $itemart['scorta']; // prendo la scorta minima
									$descri = $itemart['descri']; //prendo descrizione articolo
									$form['lot_or_serial'][$form['mov']] = $itemart['lot_or_serial']; // prendo il lotto
									$form['prezzo'][$form['mov']] = $itemart['preacq']; // prendo il prezzo di acquisto
									$service = intval($itemart['good_or_service']); // carico $service per vedere se è articolo o servizio
								}
								if (isset($itemart['descri']) AND $form['mov']!=$form['nmov']){ // se non è il movimento attivo visualizzo la descrizione
										echo " ", substr($itemart['descri'], 0, 25), " ";
									}
								If ($service == 0 or $service == 2) { //Antonio Germani se è un articolo con magazzino
									// Antonio Germani calcolo giacenza di magazzino e la metto in $print_magval
									if (isset($itemart['codice'])){
										$mv = $gForm->getStockValue(false, $itemart['codice']);
										$magval = array_pop($mv);
										$print_magval = str_replace(",", "", ($magval)?$magval['q_g']:0);
									}
									if (isset($_POST['Update']) or $toDo == "update") { // se è un update
										$qta = gaz_dbi_get_row($gTables['movmag'], "id_mov", $_GET['id_mov']);
										if ($qta['artico'] == $form['artico'][$form['mov']]) { // se l'update è per lo stesso articolo
											$print_magval = $print_magval + $qta['quanti']; // prendo la quantità precedentemente memorizzata e la riaggiungo alla giacenza di magazzino altrimenti il controllo quantità non funziona bene
										}
									}
									if ($dose > 0) {
										echo "<br>Dose generica: ", gaz_format_quantity($dose, 1, $admin_aziend['decimal_quantity']), " ", $print_unimis, "/ha";
									}
								}
								if ($service == 2 && $form['operat'] == 1) { // se è articolo composito avviso che non è possibile il carico
									echo '<div><button class="btn btn-xs btn-danger" type="image" >';
									echo $script_transl[42];
									echo '</button></div>';
								}
							}
							?>
							<input type="hidden" name="id_lotmag<?php echo $form['mov']; ?>" value="<?php echo $form['id_lotmag'][$form['mov']]; ?>" />
							<input type="hidden" name="lot_or_serial<?php echo $form['mov']; ?>" value="<?php echo $form['lot_or_serial'][$form['mov']]; ?>" />
						</div>
					</div>
				</div><!-- chiude row  -->
				<div class="row"><!-- Quantità -->
					<div class="col-md-12">
						<div class="form-group">
							<label class="FacetFieldCaptionTD">
								<?php echo $script_transl[12]; ?>
							</label>						
												
							<input class="FacetSelect" type="text" value="<?php echo gaz_format_quantity($form['quanti'][$form['mov']], 1, $admin_aziend['decimal_quantity']); ?>" maxlength="10" name="quanti<?php echo $form['mov']; ?>" onChange="this.form.submit()">
							
							<?php echo "&nbsp;" . $print_unimis;
							
							if ($service == 0 or $service == 2) { // se è un articolo con magazzino
								
								echo " - " . $script_transl[22] . " " . gaz_format_quantity($print_magval, 1, $admin_aziend['decimal_quantity']) . " " . $print_unimis . "&nbsp;&nbsp;";
								// Antonio Germani se sottoscorta si attiva il pulsante di allerta e riordino. Al click si apre il popup con l'ordine compilato. >>> NB: al ritorno dall'ordine e dopo un submit, c'è un problema DA RISOLVERE: si apre una nuova finestra. <<< preferisco questo problema a quello che c'era prima, cioè si apriva la pagina dell'ordine annullando quanto già inserito nei movimenti.
								if ($print_magval < $scorta and $service == 0 and $scorta > 0) {
									echo "<button type=\"submit\" name=\"acquis\"  class=\"btn btn-default btn-lg\" title=\"Sottoscorta, riordinare\" onclick=\"myform.target='POPUPW'; POPUPW = window.open(
									  'about:blank','POPUPW','width=800,height=400');\" style=\"background-color:red\"><span class=\"glyphicon glyphicon-alert\" aria-hidden=\"true\"></span></button>";
								}
								if ($print_magval < $scorta and $service == 2 and $scorta > 0) {
									echo "<button type=\"submit\" name=\"acquis\"  class=\"btn btn-default btn-lg\" title=\"Sottoscorta, riordinare\" onclick=\"myform.target='POPUPW'; POPUPW = window.open(
									  'about:blank','POPUPW','width=800,height=400');\" style=\"background-color:red\"><span class=\"glyphicon glyphicon-alert\" aria-hidden=\"true\"></span></button>";
								}
							}
								?>
						</div>
					</div>	
				</div><!-- chiude row  -->
				<?php 
				if ($service == 0 or $service == 2) { //Antonio Germani se è un articolo con magazzino
					if (($form['lot_or_serial'][$form['mov']] > 0) && ($form['operat'] == - 1)) {
						?>
						<div class="row bg-info"><!-- inizio gestione form  LOTTI in uscita  -->
							<div class="col-md-12">
								<div class="form-group">
									<label class="FacetFieldCaptionTD">
										<?php echo $script_transl[41]; ?>
									</label>
								
									<input type="hidden" name="filename<?php echo $form['mov']; ?>" value="<?php echo $form['filename'][$form['mov']]; ?>">			
									<?php
									$lm->getAvailableLots($form['artico'][$form['mov']], $form['id_mov']);
									$ld = $lm->divideLots($form['quanti'][$form['mov']]);
									if ($ld > 0) {
										echo "ERRORE ne mancano: ", $ld, "<br>"; // >>>>> quantità insufficiente - riaggiorno form[quanti] con quello che c'è !!
										$form['quanti'][$form['mov']] = $form['quanti'][$form['mov']] - $ld;
										?>
										<input type="hidden" name="quanti<?php echo $form['mov']; ?>" value="<?php echo $form['quanti'][$form['mov']]; ?>">
										<?php
									}
									
									// Antonio Germani - calcolo delle giacenze per ogni singolo lotto in $count['identifier']
									$count=array();
									foreach ($lm->available as $v_lm) {
										$key=$v_lm['identifier']; // chiave per il conteggio dei totali raggruppati per lotto 
										if( !array_key_exists($key, $count) ){ // se la chiave ancora non c'è nell'array
											// Aggiungo la chiave con il rispettivo valore iniziale
											$count[$key] = $v_lm['rest'];
										} else {
											// Altrimenti, aggiorno il valore della chiave
											$count[$key] += $v_lm['rest'];
										}
									}
									
									if (isset($form['id_lotmag'][$form['mov']]) && $form['id_lotmag'][$form['mov']] == 0)  {
										$l = $form['mov']; // ripartisco la quantità introdotta tra i vari lotti disponibili per l'articolo
										// e se è il caso creo più righe
										foreach ($lm->divided as $k => $v) {
											if ($v['qua'] >= 0.00001) {
												$form['id_lotmag'][$l] = $v['id']; // setto il lotto
												$form['quanti'][$l] = $v['qua']; // e la quantità in base al riparto
												?>
												<input type="hidden" name="id_lotmag<?php echo $l; ?>" value="<?php echo $form['id_lotmag'][$l]; ?>">
												<input type="hidden" name="quanti<?php echo $l; ?>" value="<?php echo $form['quanti'][$l]; ?>">
												<input type="hidden" name="artico<?php echo $l; ?>" value="<?php echo $form['artico'][$form['mov']]; ?>">
												<input type="hidden" name="scorig<?php echo $l; ?>" value="<?php echo $form['scorig'][$form['mov']]; ?>">
												<input type="hidden" name="clfoco<?php echo $l; ?>" value="<?php echo $form['clfoco'][$form['mov']]; ?>">
												<input type="hidden" name="nome_avv<?php echo $l; ?>" value="<?php echo $form['nome_avv'][$form['mov']]; ?>">
												<input type="hidden" name="id_avversita<?php echo $l; ?>" value="<?php echo $form['id_avversita'][$form['mov']]; ?>">
												<input type="hidden" name="staff<?php echo $l; ?>" value="<?php echo $form['staff'][$form['mov']]; ?>">
												<input type="hidden" name="prezzo<?php echo $l; ?>" value="<?php echo gaz_format_quantity($form['prezzo'][$form['mov']], 1, $admin_aziend['decimal_quantity']); ?>">
												<input type="hidden" name="identifier<?php echo $l; ?>" value="">
												<input type="hidden" name="expiry<?php echo $l; ?>" value="">
												<input type="hidden" name="lot_or_serial<?php echo $l; ?>" value="<?php echo $form['lot_or_serial'][$form['mov']]; ?>">
												<input type="hidden" name="filename<?php echo $l; ?>" value="<?php echo $form['filename'][$form['mov']]; ?>">
												<?php
												$l++;$rig_ripart_lot=$l-$form['mov']-1;
											}
										}						
									}
								
									if (isset($form['id_lotmag'][$form['mov']]) && $form['id_lotmag'][$form['mov']] > 0) {
										$selected_lot = $lm->getLot($form['id_lotmag'][$form['mov']]);
										?>
										<div>
											<button class="btn btn-xs btn-success" title="clicca per cambiare lotto" type="image"  data-toggle="collapse" href="#lm_dialog<?php echo $form['mov']; ?>"><?php echo $selected_lot['id'] . ' lotto n.:' . $selected_lot['identifier'];?>
											<?php
											if (intval($form['expiry'][$form['mov']]) > 0) {
												echo ' scadenza:' . gaz_format_date($selected_lot['expiry']);
											}
											if (!isset($count[$selected_lot['identifier']])) {
												echo "<br><b> LOTTO IN ERRORE!!!</b><br>Probabilmente manca il movimento madre.";
											} else {
												echo ' - disponibili: ' . gaz_format_quantity($count[$selected_lot['identifier']]) . '<i class="glyphicon glyphicon-tag"></i></button>';
											}
											?>
											<input type="hidden" name="id_lotmag<?php echo $form['mov']; ?>" value="<?php echo $form['id_lotmag'][$form['mov']]; ?> ">
										
											<!-- Antonio Germani - Cambio lotto solo se c'è una sola riga di movimento altrimenti si rischia un errore inserendo due volte lo stesso lotto -->
											<div id="lm_dialog<?php echo $form['mov']; ?>" class="collapse" >
												<div class="form-group">
													<?php 
												if ((count($lm->available) > 1) && (intval($form['nmov']) == 0)) {
													foreach ($lm->available as $v_lm) {
														if ($v_lm['id'] <> $form['id_lotmag'][$form['mov']]) {
															echo '<div>change to:<button class="btn btn-xs btn-warning" type="image" onclick="this.form.submit();" name="id_lotmag' . $form['mov'] . '" value="' . $v_lm['id'] . '">' . $v_lm['id'] . ' lotto n.:' . $v_lm['identifier'] . ' scadenza:' . gaz_format_date($v_lm['expiry']) . ' - disponibili: ' . gaz_format_quantity($count[$v_lm['identifier']]) . '</button></div>';
														}
													}
												} else {
													echo '<div><button class="btn btn-xs btn-danger" type="image" >Non sono disponibili altri lotti, <br> oppure non è possibile cambiare lotto negli inserimenti multipli</button></div>';
												}
												?>
												</div>
											</div>
										</div>
										<?php
									} 
									?>
								</div>
							</div>
						</div><!-- chiude row  -->
						<?php
					}// Fine  LOTTI	in uscita
									
					// Inizio LOTTI in entrata
					if (($form['lot_or_serial'][$form['mov']] > 0) && ($form['operat'] == 1)) {
						$idlotcontroll = gaz_dbi_get_row($gTables['lotmag'], "id", $form['id_lotmag'][$form['mov']]); // in $idlotcontroll['id_movmag'] ho l id del movimento madre che ha generato il lotto
						?>
						<div class="row bg-info">
							<div class="col-md-12">
								<div class="form-group">
									<label class="FacetFieldCaptionTD"><?php echo $script_transl[41];
											echo "<br><p>N.b.: è possibile modificare <br> il lotto solo dal movimento<br> che lo ha creato</p>"; ?>
									</label>
								
									<input type="hidden" name="filename<?php echo $form['mov']; ?>" value="<?php echo $form['filename'][$form['mov']]; ?>">			  
									<?php
									if (intval($form['id_mov']) > 0 or intval($form['id_mov']) == intval($idlotcontroll['id_movmag'])) { // attiva inserimento certificato in alcuni casi
										if (strlen($form['filename'][$form['mov']]) == 0) {
											echo '<div><button class="btn btn-xs btn-danger" type="image" data-toggle="collapse" href="#lm_dialog' . $form['mov'] . '">' . 'Inserire nuovo certificato' . ' ' . '<i class="glyphicon glyphicon-tag"></i>' . '</button></div>';
										} else {
											echo '<div><button class="btn btn-xs btn-success" type="image" data-toggle="collapse" href="#lm_dialog' . $form['mov'] . '">' . $form['filename'][$form['mov']] . ' ' . '<i class="glyphicon glyphicon-tag"></i>' . '</button>';
											if ($toDo == "update") {
												foreach (glob("../../modules/camp/tmp/*") as $fn) { // prima cancello eventuali precedenti file temporanei
													unlink($fn);
												}
												if (strlen($form['filename'][$form['mov']]) > 0) {
													$tmp_file = DATA_DIR."files/" . $admin_aziend['company_id'] . "/" . $form['filename'][$form['mov']];
													// sposto nella cartella di lettura il relativo file temporaneo
													copy($tmp_file, "../../modules/camp/tmp/" . $form['filename'][$form['mov']]);
												}
												?>
												<a  class="btn btn-info btn-md" href="javascript:;" onclick="window.open('<?php echo "../../modules/camp/tmp/" . ($form['filename'][$form['mov']]) ?>', 'titolo', 'width=800, height=400, left=80%, top=80%, resizable, status, scrollbars=1, location');">
												<span class="glyphicon glyphicon-eye-open"></span></a>
												<?php
											} 
											?>
											</div>
											<?php
										}
									}
									// Esclusione del movimento Madre dalla scelta fra i lotti esistenti
									// se questo è il movimento che ha generato il lotto in lotmag (movimento madre), allora escludo la scelta fra quelli esistenti, così si ha la possibilità di modificare il lotto su lotmag
									$idlotcontroll = gaz_dbi_get_row($gTables['lotmag'], "id", $form['id_lotmag'][$form['mov']]);
									if ((intval($form['id_mov']) == 0) or (intval($form['id_mov']) <> intval($idlotcontroll['id_movmag']))) {
										/*Antonio Germani scelta lotto fra quelli esistenti  */
										$query = "SELECT " . '*' . " FROM " . $gTables['lotmag'] . " WHERE codart ='" . $form['artico'][$form['mov']] . "'";
										$result = gaz_dbi_query($query);
										if ($result->num_rows > 0) { // se ci sono lotti attivo la selezione
											echo '<select name="id_lotmag' . $form['mov'] . '" class="FacetSelect" onchange="this.form.submit()">\n';
											echo "<option value=\"\">-seleziona fra lotti esistenti-</option>\n";
											$sel = 0;
											while ($rowlot = gaz_dbi_fetch_array($result)) {
												$selected = "";
												if ($form['id_lotmag'][$form['mov']] == $rowlot['id']) {
													$selected = " selected ";
													$sel = 1;
												}
												echo "<option value=\"" . $rowlot['id'] . "\"" . $selected . ">" . $rowlot['id'] . " - " . $rowlot['identifier'] . " - " . gaz_format_date($rowlot['expiry']) . "</option>\n";
											}
											echo "</select>&nbsp;";
											If ((intval($form['id_lotmag'][$form['mov']]) > 0) && (intval($sel) == 1)) { // se è stato selezionato un lotto
												$rowlot = gaz_dbi_get_row($gTables['lotmag'], "id", $form['id_lotmag'][$form['mov']]);
												$form['identifier'][$form['mov']] = $rowlot['identifier'];
												$form['expiry'][$form['mov']] = $rowlot['expiry'];
											}
										}// fine scelta lotto fra esistenti							
									} // fine (esclusione del movimento madre)
									if (strlen($form['identifier'][$form['mov']]) == 0) {
										echo '<div><button class="btn btn-xs btn-danger" type="image" data-toggle="collapse" href="#lm_dialog_lot' . $form['mov'] . '">' . 'Inserire nuovo Lotto' . " " . '<i class="glyphicon glyphicon-tag"></i></button></div>';
									} else {
										if (intval($form['expiry'][$form['mov']]) > 0) {
											echo '<div><button class="btn btn-xs btn-success" type="image" data-toggle="collapse" href="#lm_dialog_lot' . $form['mov'] . '">' . $form['identifier'][$form['mov']] . " " . gaz_format_date($form['expiry'][$form['mov']]) . " " . '<i class="glyphicon glyphicon-tag"></i></button></div>';
										} else {
											echo '<div><button class="btn btn-xs btn-success" type="image" data-toggle="collapse" href="#lm_dialog_lot' . $form['mov'] . '">' . $form['identifier'][$form['mov']] . " " . '<i class="glyphicon glyphicon-tag"></i></button></div>';
										}
									}
									if (intval($form['id_mov']) == intval($idlotcontroll['id_movmag'])) {
										?>
										<div id="lm_dialog<?php echo $form['mov']; ?>" class="collapse" >
											<div class="form-group">
												<div>								
													<input type="file" onchange="this.form.submit();" name="docfile_<?php echo $form['mov']; ?>">
												</div>
											</div>
										</div>
										<?php
									}
									?>
									<div id="lm_dialog_lot<?php echo $form['mov']; ?>" class="collapse" >
										<div class="form-group">
											<div>
												<label>Numero: </label>
												<input type="text" name="identifier<?php echo $form['mov']; ?>" value="<?php echo $form['identifier'][$form['mov']]; ?>" >
												<br>
												<label>Scadenza: </label>
												<input class="datepicker" type="text" onchange="this.form.submit();" name="expiry<?php echo $form['mov']; ?>"  value="<?php echo $form['expiry'][$form['mov']]; ?>">
											</div>
										</div>
									</div>
								</div>
							</div>
						</div><!-- chiude row  -->
							<?php
					}// fine LOTTI in entrata
										
					?>					
							
					<input type="hidden" name="clfoco<?php echo $form['mov']; ?>" value="<?php $form['clfoco'][$form['mov']]; ?>">
					<input type="hidden" name="staff<?php echo $form['mov']; ?>" value="">						
									
					<?php
				} else { // se è articolo senza magazzino
					?>
					<div class="row bg-info">
						
						<?php
						/*Antonio Germani se l'unità di misura è oraria attiva Operaio */
						if ($print_unimis == "h") {
							?>
							<div class="col-md-12">
								<div class="form-group">
									<label class="FacetFieldCaptionTD">
										<?php echo $script_transl[32]; ?>
									</label>
								
									<?php
									$g2Form->selectFrom2DB('staff','clfoco','codice','descri', 'staff'.$form['mov'],'id_staff', $form['staff'][$form['mov']], 'id_staff', 1, ' - ','id_clfoco','TRUE','FacetSelect' , null, '');
									?>
								</div>
							</div>
							<?php
						} else {		
						?>	
						
							<input type="hidden" name="clfoco<?php echo $form['mov']; ?>" value="<?php echo $itm['id_clfoco']; ?>">
							<input type="hidden" name="staff<?php echo $form['mov']; ?>" value="<?php echo $form['staff'][$form['mov']]; ?>">
						
						<?php
						}
						?>
					</div><!-- chiude row  -->
					<?php
				}
			
				if ($print_unimis <> "h") { // se è una lavorazione agricola disattivare avversità
					if (intval($form['nome_avv'][$form['mov']]) == 0) {
						$form['nome_avv'][$form['mov']] = "";
					}
					?>
					<div class="row"><!-- AVVERSITà -->
						<div class="col-md-12">
							<div class="form-group">
								<label class="FacetFieldCaptionTD"><?php echo $script_transl[20]; ?>
								</label>
								
								<!-- per funzionare autocomplete, id dell'input deve essere autocomplete3 -->
								<input class="FacetSelect" id="autocomplete3" type="text" value="<?php echo $form['nome_avv'][$form['mov']]; ?>" name="nome_avv<?php echo $form['mov']; ?>" maxlength="15" />
								<input type="hidden" value="<?php echo intval($form['nome_avv'][$form['mov']]); ?>" name="id_avversita<?php echo $form['mov']; ?>"/>
								<?php 
								if ($dose_usofito > 0) {
									echo "Dose specifica: ", gaz_format_quantity($dose_usofito, 1, $admin_aziend['decimal_quantity']), " ", $print_unimis, "/ha";
								}					
								?>
								
							</div>
						</div>
					</div><!-- chiude row  --> 
					<div class="row"><!-- FASE FENOLOGICA -->
						<div class="col-md-12">
							<div class="form-group">
								<label class="FacetFieldCaptionTD">Fase fenologica
								</label>
								
								<!-- per funzionare autocomplete, id dell'input deve essere autocomplete5 -->
								<input class="FacetSelect" id="autocomplete5" type="text" value="<?php echo $form['fase_feno'][$form['mov']]; ?>" name="fase_feno<?php echo $form['mov']; ?>" maxlength="15" />
								<input class="col-sm-1" title="Gestione fasi fenologiche" type="button" name="button2" id="feno" rel="gestfeno" value="&#9776" onclick="buttonToggle(this,'&#9776','&#9746');" style="float: right;">
							</div>
						</div>
					</div><!-- chiude row  --> 
					<?php
				} else {
					?>
					<div class="row bg-info">
						
							<input type="hidden" value="" name="nome_avv<?php echo $form['mov']; ?>"/>
							<input type="hidden" value="" name="id_avversita<?php echo $form['mov']; ?>"/>
						
					</div><!-- chiude row  -->
					<?php
				}
				/* fine avversità */
				$print_magval = "";
				$scorta = "";
				$dose = ""; // le azzero perché altrimenti me le ritrovo nell'eventuale movimento/riga successivo

				/* Antonio Germani  prezzo e sconto del rigo movimento */
				$importo_totale = ($form['prezzo'][$form['mov']] * floatval(preg_replace("/\,/", '.', $form['quanti'][$form['mov']]))) - ((($form['prezzo'][$form['mov']] * floatval(preg_replace("/\,/", '.', $form['quanti'][$form['mov']]))) * $form['scorig'][$form['mov']]) / 100);
				?>			
				<div class="row"><!-- COSTO MOVIMENTO  -->
					<div class="col-md-12">
						<div class="form-group">
							<label class="FacetFieldCaptionTD"><?php echo $script_transl[13]; ?>
							</label>
						
							<input type="text" class="FacetFieldCaptionTD" value="<?php echo number_format($importo_totale, $admin_aziend['decimal_price'], ',', ''); ?>" name="total" readonly>
							<?php echo "&nbsp;" . $admin_aziend['symbol'] . "&nbsp;&nbsp;&nbsp;&nbsp;" . $script_transl[31]; ?>
							<input type="text" class="FacetFieldCaptionTD" value="<?php echo number_format($form['prezzo'][$form['mov']], $admin_aziend['decimal_price'], ',', '') ?>" maxlength="12" name="prezzo<?php echo $form['mov'] ?>" readonly>
							<input type="hidden" class="FacetFieldCaptionTD" value="<?php echo number_format($form['prezzo2'][$form['mov']], $admin_aziend['decimal_price'], ',', '') ?>" maxlength="12" name="prezzo2<?php echo $form['mov'] ?>" readonly>
							<?php echo " " . $admin_aziend['symbol']; ?>
							<input type="hidden" value="<?php echo $form['scorig'][$form['mov']]; ?>" maxlength="4" name="scorig<?php echo $form['mov'] ?>" onChange="this.form.submit()">
							<input type="hidden" value="<?php echo $form['scorig2'][$form['mov']]; ?>" maxlength="4" name="scorig2<?php echo $form['mov'] ?>" onChange="this.form.submit()">
							</div>
					</div>
				</div><!-- chiude row  -->
				<div class="row bg-info">
					<div class="col-md-4" style="font-size:5pt;" >
						<?php
						if (($form['mov'] + 1) <= $form['nmov']){
							echo $form['mov'] + 2; 
						}
						?>
						<a name="<?php echo $form['mov']; ?>"></a> <!-- Antonio Germani Questa è l'ancora dello scroll -->
					</div>
				</div><!-- chiude row  -->	
				<?php
			}
			$form['mov'] = $form['nmov'];
			
			if (isset($l) && $l - 1 > $form['mov']) { // se la suddivisione dei lotti ha creato nuovi righi aggiorno il numero totale dei righi
				$form['nmov'] = $form['nmov'] + $rig_ripart_lot;
			}
			?>			
			<div class="row">
				
					<input type="hidden" name="nmov" onchange="this.form.submit();" value="<?php echo $form['nmov']; ?>">
					<?php
					if ($toDo == 'insert') {
						if ($form['artico'][$form['mov']] <> "") {
							echo "<input type=\"submit\" name=\"Add_mov\" value=\"+ " . $script_transl['add'] . "\">\n";
						}
						if ($form['nmov'] > 0) {
							echo "<input type=\"submit\" title=\"Togli ultimo movimento\" name=\"Del_mov\" value=\"X Togli ultimo movimento inserito\">\n";
						}
					}
					?>
			</div><!-- chiude row  -->
			<div class="row bg-info">			
			</div><!-- chiude row  -->
			<div id="gestfeno" class="row">							
				<div  align="center" >
					<h3>Gestione fasi fenologiche</h3>
				</div>
				<div>
					<label>Nome fase fenologica: </label>
					<input type="text" name="add_feno" value="" placeholder="scrivi il nome della fase fenologica" onfocus="if (this.placeholder == 'scrivi il nome della fase fenologica') {this.placeholder = '';}">										
				</div>
				<div class="row">
					<input type="submit" class="btn btn-warning" name="feno" value="Aggiungi fase">
				</div>
			</div>
			<?php
			if (isset($l) && $l - 1 > $form['mov']) { // se la suddivisione dei lotti ha creato nuovi righi ricarico il form
				?>
				<script>				
					document.myform.submit();
				</script>
				<?php
			}
			?>		
			<!-- <<<<<<<<<<<<<<<<<<<<<<       Fine ciclo righi mov     <<<<<<<<<<<<<<<<<<<  -->
			
			<div class="row bg-info">
				<div class="col-md-12">
					<div class="form-group">
						<label class="FacetFieldCaptionTD">
							<?php echo $script_transl[21]; ?>
						</label>
						<!-- visualizzo l'operatore -->
						<?php echo $form['adminid']," - ",$form['adminname']; ?>
						<select name="adminid" onchange="this.form.submit()">
							<?php 		
							$sql = gaz_dbi_dyn_query ($gTables['anagra'].".* ",
							 $gTables['anagra']."
							 LEFT JOIN ".$gTables['clfoco']." on (".$gTables['clfoco'].".id_anagra = ".$gTables['anagra'].".id)
							 LEFT JOIN ".$gTables['staff']." on (".$gTables['staff'].".id_clfoco = ".$gTables['clfoco'].".codice)
							 LEFT JOIN ".$gTables['admin']." on (".$gTables['admin'].".id_anagra = ".$gTables['anagra'].".id)",
							 $gTables['staff'].".id_clfoco > 0 OR ". $gTables['admin'] .".id_anagra > 0
							 " );
							$sel=0;
							while ($row = $sql->fetch_assoc()){ 
								$selected = "";
								if ($row['id'] == $form['adminid']) {
									$selected = "selected";
									$sel=1;									
								}
								echo "<option ".$selected." value=\"".$row['id']."\">" . $row['ragso1'] ." ".$row['ragso2']. "</option>";
							}							
							if ($sel==0){						
								echo "<option selected value=\"".$form['adminid']."\">".$form['adminname']."</option>";
							} 					
							?>				
						</select>					
						<input class="col-sm-1" title="Gestione autorizzazione acquisto e uso fitosanitari" type="button" name="button1" id="patent" rel="gestpatent" value="&#9776" onclick="buttonToggle(this,'&#9776','&#9746');" style="float: right;">
						<input type="hidden" value="<?php echo $form['adminname']; ?>" name="adminname"/>
					</div>
				</div>			
			</div><!-- chiude row  -->
			
			<div id="gestpatent" class="col-sm-12 bg-info">							
				<div  align="center" >
					<h3>Gestione Abilitazione acquisto e uso fitosanitari (Patentino)</h3>
				</div>
				<div>
					<label>Numero: </label>
					<input type="text" name="patent_number" value="<?php echo $form['patent_number']; ?>" >
					<input type="hidden" name="rif_abilitazione" value="<?php echo htmlspecialchars($form['rif_abilitazione']); ?>" >
					<label>Scadenza: </label>
					<input class="datepicker" type="datetime-local" name="patent_expiry"  value="<?php echo $form['patent_expiry']; ?>">					
				</div>
				<div class="row">
					<input type="submit" class="btn btn-warning" name="patent" value="Save patent">
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-12">
				<label colspan="1">
					<input type="submit" name="cancel" value="<?php echo $script_transl['cancel']; ?>">
					<input type="submit" name="Return" value="<?php echo $script_transl['return']; ?>">
				</label>
				
					<?php
					if ($toDo !== 'update') {						
						echo '<input class="pull-right" type="submit" accesskey="i" name="Insert" value="' . ucfirst($script_transl['insert']) . '!">';
					} else {				
						echo '<input class="pull-right" type="submit" accesskey="m" name="Insert" value="' . ucfirst($script_transl['update']) . '!">';
					}				
					?>
				</div>
			</div><!-- chiude row  -->		
		</div><!-- chiude container -->
	</div>	<!-- chiude panel -->	
</form>
<!-- >>>>>>>>>>>>>>>>>>    Fine FORM    <<<<<<<<<<<<<<<<<<< -->
<script type="text/javascript">	
	function buttonToggle(where, pval, nval) {
		var table = document.getElementById(where.attributes.rel.value);
		if (where.value == pval){
			where.value = nval;
			table.style.display = 'unset';
		} else if (where.value == nval) {
			where.value = pval;
			table.style.display = 'none';
		}		
	}
</script>
<?php
// Antonio Germani questo serve per fare lo scroll all'ultimo movimento inserito
if (isset($anchor["num"]) AND $anchor["num"]>0){ 
	echo "<script type='text/javascript'>\n" . "window.location.hash = '#{$anchor["num"]}';" . //◄■■■ JUMP TO LOCAL ANCHOR.
	"</script>\n";
}
require ("../../library/include/footer.php");
?>