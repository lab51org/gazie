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
$msg = "";
//Creo l'array associativo delle descrizioni dei documenti e dei relativi operatori
$TipoDocumento = array("AOR" => 0, "APR" => 0, "AFA" => 1);
if (isset($_POST['newdestin'])) {
    $_POST['id_des'] = 0;
    $_POST['destin'] = "";
}
if (!isset($_POST['ritorno'])) {
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
} else {
    $form['ritorno'] = $_POST['ritorno'];
}
// il tipo documento dev'essere settato e del tipo giusto altrimenti torna indietro
if ((isset($_GET['Update']) and ! isset($_GET['id_tes'])) or ( isset($_GET['tipdoc']) and ( !array_key_exists($_GET['tipdoc'], $TipoDocumento)))) {
    header("Location: " . $form['ritorno']);
    exit;
}

if ((isset($_POST['Update'])) or ( isset($_GET['Update']))) {
    $toDo = 'update';
} else {
    $toDo = 'insert';
}

if ((isset($_POST['Insert'])) or ( isset($_POST['Update']))) {   //se non e' il primo accesso
    //qui si dovrebbe fare un parsing di quanto arriva dal browser...
    $form['id_tes'] = $_POST['id_tes'];
    $form['hidden_req'] = $_POST['hidden_req'];
    $anagrafica = new Anagrafica();
    $fornitore = $anagrafica->getPartner($_POST['clfoco']);
    // ...e della testata
    foreach ($_POST['search'] as $k => $v) {
        $form['search'][$k] = $v;
    }
    $form['delivery_time'] = intval($_POST['delivery_time']);
    $form['day_of_validity'] = intval($_POST['day_of_validity']);
    $form['cosear'] = $_POST['cosear'];
    $form['coseprod'] = $_POST['coseprod'];
    $form['seziva'] = $_POST['seziva'];
    $form['tipdoc'] = $_POST['tipdoc'];
    $form['gioemi'] = $_POST['gioemi'];
    $form['mesemi'] = $_POST['mesemi'];
    $form['annemi'] = $_POST['annemi'];
    $form['protoc'] = $_POST['protoc'];
    $form['numdoc'] = $_POST['numdoc'];
    $form['numfat'] = $_POST['numfat'];
    $form['datfat'] = $_POST['datfat'];
    $form['clfoco'] = $_POST['clfoco'];
    //tutti i controlli su  tipo di pagamento e rate
    $form['speban'] = $_POST['speban'];
    $form['numrat'] = $_POST['numrat'];
    $form['pagame'] = $_POST['pagame'];
    $form['change_pag'] = $_POST['change_pag'];
    $form['print_total'] = intval($_POST['print_total']);

    if ($form['change_pag'] != $form['pagame']) {  //se è stato cambiato il pagamento
        $new_pag = gaz_dbi_get_row($gTables['pagame'], "codice", $form['pagame']);
        $old_pag = gaz_dbi_get_row($gTables['pagame'], "codice", $form['change_pag']);
        if (($new_pag['tippag'] == 'B' or $new_pag['tippag'] == 'T' or $new_pag['tippag'] == 'V')
                and ( $old_pag['tippag'] == 'C' or $old_pag['tippag'] == 'D')) { // se adesso devo mettere le spese e prima no
            $form['numrat'] = $new_pag['numrat'];
            if ($toDo == 'update') {  //se è una modifica mi baso sulle vecchie spese
                $old_header = gaz_dbi_get_row($gTables['tesbro'], "id_tes", $form['id_tes']);
                if ($old_header['speban'] > 0 and $fornitore['speban'] == "S") {
                    $form['speban'] = $old_header['speban'];
                } elseif ($old_header['speban'] == 0 and $fornitore['speban'] == "S") {
                    $form['speban'] = $admin_aziend['sperib'];
                } else {
                    $form['speban'] = 0.00;
                }
            } elseif ($fornitore['speban'] == 'S') { //altrimenti mi avvalgo delle nuove dell'azienda se il fornitore lo richiede
                $form['speban'] = $admin_aziend['sperib'];
            }
        } elseif (($new_pag['tippag'] == 'C' or $new_pag['tippag'] == 'D')
                and ( $old_pag['tippag'] == 'B' or $old_pag['tippag'] == 'T' or $old_pag['tippag'] == 'V')) { // se devo togliere le spese
            $form['speban'] = 0.00;
            $form['numrat'] = 1;
        }
        $form['pagame'] = $_POST['pagame'];
        $form['change_pag'] = $_POST['pagame'];
    }
    $form['banapp'] = $_POST['banapp'];
    $form['listin'] = $_POST['listin'];
    $form['giocon'] = $_POST['giocon'];
    $form['mescon'] = $_POST['mescon'];
    $form['anncon'] = $_POST['anncon'];
    $form['spediz'] = $_POST['spediz'];
    $form['portos'] = $_POST['portos'];
    $form['destin'] = '';
    $form['id_des'] = '';
    $form['traspo'] = 0;
    $form['spevar'] = $_POST['spevar'];
    $form['cauven'] = $_POST['cauven'];
    $form['caucon'] = $_POST['caucon'];
    $form['caumag'] = $_POST['caumag'];
    $form['caucon'] = $_POST['caucon'];
    $form['id_agente'] = $_POST['id_agente'];
    $form['id_parent_doc'] = $_POST['id_parent_doc'];
    $form['sconto'] = $_POST['sconto'];
    // inizio rigo di input
    $form['in_descri'] = $_POST['in_descri'];
    $form['in_tiprig'] = $_POST['in_tiprig'];
    /*    $form['in_artsea'] = $_POST['in_artsea']; Non serve più */
    $form['in_codart'] = $_POST['in_codart'];
    $form['in_codice_fornitore'] = $_POST['in_codice_fornitore'];
    $form['in_pervat'] = $_POST['in_pervat'];
    $form['in_unimis'] = $_POST['in_unimis'];
    $form['in_prelis'] = $_POST['in_prelis'];
    $form['in_sconto'] = $_POST['in_sconto'];
    $form['in_quanti'] = gaz_format_quantity($_POST['in_quanti'], 0, $admin_aziend['decimal_quantity']);
    $form['in_codvat'] = $_POST['in_codvat'];
    $form['in_codric'] = $_POST['in_codric'];
    $form['in_quality'] = $_POST['in_quality'];
    $form['in_extdoc'] = $_POST['in_extdoc'];
    $form['in_id_mag'] = $_POST['in_id_mag'];
    $form['in_id_orderman'] = $_POST['in_id_orderman'];
    $form['in_annota'] = $_POST['in_annota'];
    $form['in_larghezza'] = $_POST['in_larghezza'];
    $form['in_lunghezza'] = $_POST['in_lunghezza'];
    $form['in_spessore'] = $_POST['in_spessore'];
    $form['in_peso_specifico'] = $_POST['in_peso_specifico'];
    $form['in_pezzi'] = $_POST['in_pezzi'];
    $form['in_status'] = $_POST['in_status'];
    // fine rigo input
    $form['righi'] = array();
    $next_row = 0;
    if (isset($_POST['righi'])) {
        foreach ($_POST['righi'] as $next_row => $value) {
            $form['righi'][$next_row]['descri'] = substr($value['descri'], 0, 100);
            $form['righi'][$next_row]['tiprig'] = intval($value['tiprig']);
            $form['righi'][$next_row]['codice_fornitore'] = substr($value['codice_fornitore'], 0, 50);	// Aggiunto a Mano 
            $form['righi'][$next_row]['codart'] = substr($value['codart'], 0, 15);
            $form['righi'][$next_row]['pervat'] = preg_replace("/\,/", '.', $value['pervat']);
            $form['righi'][$next_row]['unimis'] = substr($value['unimis'], 0, 3);
            $form['righi'][$next_row]['prelis'] = number_format(floatval(preg_replace("/\,/", '.', $value['prelis'])), $admin_aziend['decimal_price'], ".", "");
            $form['righi'][$next_row]['sconto'] = floatval(preg_replace("/\,/", '.', $value['sconto']));
            $form['righi'][$next_row]['quanti'] = gaz_format_quantity($value['quanti'], 0, $admin_aziend['decimal_quantity']);
            $form['righi'][$next_row]['codvat'] = intval($value['codvat']);
            $form['righi'][$next_row]['codric'] = intval($value['codric']);
            $form['righi'][$next_row]['quality'] = substr($value['quality'],0,50);
            $form['righi'][$next_row]['id_mag'] = intval($value['id_mag']);
            $form['righi'][$next_row]['id_orderman'] = intval($value['id_orderman']);
            $form['righi'][$next_row]['annota'] = substr($value['annota'], 0, 50);
            $form['righi'][$next_row]['larghezza'] = floatval($value['larghezza']);
            $form['righi'][$next_row]['lunghezza'] = floatval($value['lunghezza']);
            $form['righi'][$next_row]['spessore'] = floatval($value['spessore']);
            $form['righi'][$next_row]['peso_specifico'] = floatval($value['peso_specifico']);
            $form['righi'][$next_row]['pezzi'] = floatval($value['pezzi']);
            $form['righi'][$next_row]['extdoc'] = filter_var($_POST['righi'][$next_row]['extdoc'], FILTER_SANITIZE_STRING);
            if (!empty($_FILES['docfile_' . $next_row]['name'])) {
                $move = false;
                $mt = substr($_FILES['docfile_' . $next_row]['name'], -3);
                $prefix = $admin_aziend['adminid'] . '_' . $admin_aziend['company_id'] . '_' . $next_row;
                if (($mt == "png" || $mt == "peg" || $mt == "jpg" || $mt == "pdf") && $_FILES['docfile_' . $next_row]['size'] > 1000) { //se c'e' un nuovo documento nel buffer
                    foreach (glob("../../data/files/tmp/" . $prefix . "_*.*") as $fn) {// prima cancello eventuali precedenti file temporanei
                        unlink($fn);
                    }
                    $move = move_uploaded_file($_FILES['docfile_' . $next_row]['tmp_name'], '../../data/files/tmp/' . $prefix . '_' . $_FILES['docfile_' . $next_row]['name']);
                    $form['righi'][$next_row]['extdoc'] = $_FILES['docfile_' . $next_row]['name'];
                }
                if (!$move) {
                    $msg .= "56+";
                }
            }
            $form['righi'][$next_row]['status'] = substr($value['status'], 0, 10);
            if (isset($_POST['upd_row'])) {
                $key_row = key($_POST['upd_row']);
                if ($key_row == $next_row) {
                    $form['in_descri'] = $form['righi'][$key_row]['descri'];
                    $form['in_tiprig'] = $form['righi'][$key_row]['tiprig'];
                    $form['in_codart'] = $form['righi'][$key_row]['codart'];
                    $form['in_codice_fornitore'] = $form['righi'][$key_row]['codice_fornitore'];
                    $form['in_pervat'] = $form['righi'][$key_row]['pervat'];
                    $form['in_unimis'] = $form['righi'][$key_row]['unimis'];
                    $form['in_prelis'] = $form['righi'][$key_row]['prelis'];
                    $form['in_sconto'] = $form['righi'][$key_row]['sconto'];
                    $form['in_quanti'] = $form['righi'][$key_row]['quanti'];
                    $form['in_codvat'] = $form['righi'][$key_row]['codvat'];
                    $form['in_codric'] = $form['righi'][$key_row]['codric'];
                    $form['in_quality'] = $form['righi'][$key_row]['quality'];
                    $form['in_id_mag'] = $form['righi'][$key_row]['id_mag'];
                    $form['in_extdoc'] = $form['righi'][$key_row]['extdoc'];
					$orderman = gaz_dbi_get_row($gTables['orderman'], "id", $form['righi'][$key_row]['id_orderman']);
                    $form['coseprod'] = $orderman['description'];
                    $form['in_id_orderman'] = $form['righi'][$key_row]['id_orderman'];
                    $form['in_annota'] = $form['righi'][$key_row]['annota'];
                    $form['in_larghezza'] = $form['righi'][$key_row]['larghezza'];
                    $form['in_lunghezza'] = $form['righi'][$key_row]['lunghezza'];
                    $form['in_spessore'] = $form['righi'][$key_row]['spessore'];
                    $form['in_peso_specifico'] = $form['righi'][$key_row]['peso_specifico'];
                    $form['in_pezzi'] = $form['righi'][$key_row]['pezzi'];
                    $form['in_status'] = "UPDROW_" . $key_row.'_'.$form['in_codart']; // ricordo il vecchio codice articolo 
                    $form['cosear'] = $form['righi'][$key_row]['codart'];
                    array_splice($form['righi'], $key_row, 1);
                    $next_row--;
                }
            }
            $next_row++;
        }
    }
    // Se viene inviata la richiesta di conferma totale ...
    if (isset($_POST['ins'])) {
        $sezione = $form['seziva'];
        $datemi = $form['annemi'] . "-" . str_pad($form['mesemi'], 2, "0", STR_PAD_LEFT). "-" .str_pad($form['gioemi'], 2, "0", STR_PAD_LEFT);
        $initra = $form['anncon'] . "-" . str_pad($form['mescon'], 2, "0", STR_PAD_LEFT). "-" .str_pad($form['giocon'], 2, "0", STR_PAD_LEFT);
        if (!isset($_POST['righi'])) {
            $msg .= "39+";
        }
        // --- inizio controllo coerenza date-numerazione
        if ($toDo == 'update') {  // controlli in caso di modifica
            $rs_query = gaz_dbi_dyn_query("*", $gTables['tesbro'], "YEAR(datemi) = " . $form['annemi'] . " and datemi < '$datemi' and ( tipdoc like 'DD_' or tipdoc = 'FAD') and seziva = $sezione", "numdoc desc", 0, 1);
            $result = gaz_dbi_fetch_array($rs_query); //giorni precedenti
            if ($result and ( $form['numdoc'] < $result['numdoc'])) {
                $msg .= "40+";
            }
            $rs_query = gaz_dbi_dyn_query("*", $gTables['tesbro'], "YEAR(datemi) = " . $form['annemi'] . " and datemi > '$datemi' and ( tipdoc like 'DD_' or tipdoc = 'FAD') and seziva = $sezione", "numdoc asc", 0, 1);
            $result = gaz_dbi_fetch_array($rs_query); //giorni successivi
            if ($result and ( $form['numdoc'] > $result['numdoc'])) {
                $msg .= "41+";
            }
        } else {    //controlli in caso di inserimento
            $rs_ultimo_ddt = gaz_dbi_dyn_query("*", $gTables['tesbro'], "YEAR(datemi) = " . $form['annemi'] . " and tipdoc like 'DD_' and seziva = $sezione", "numdoc desc, datemi desc", 0, 1);
            $ultimo_ddt = gaz_dbi_fetch_array($rs_ultimo_ddt);
            $utsUltimoDdT = mktime(0, 0, 0, substr($ultimo_ddt['datfat'], 5, 2), substr($ultimo_ddt['datfat'], 8, 2), substr($ultimo_ddt['datfat'], 0, 4));
            if ($ultimo_ddt and ( $utsUltimoDdT > $utsemi)) {
                $msg .= "44+";
            }
        }
		// se la data di consegna richiesta non è coerente, la azzera
		if (($datemi>$initra) || !checkdate($form['mescon'], $form['giocon'], $form['anncon'])) {
			$initra = 0;
		}
        // --- fine controllo coerenza date-numeri
        if (!checkdate($form['mesemi'], $form['gioemi'], $form['annemi']))
            $msg .= "46+";
        if (empty($form["clfoco"]))
            $msg .= "47+";
        if (empty($form["pagame"]) && $form['tipdoc'] != 'APR')
            $msg .= "48+";
        //controllo che i righi non abbiano descrizioni  e unita' di misura vuote in presenza di quantita diverse da 0
        foreach ($form['righi'] as $i => $value) {
            if ($value['descri'] == '' &&  $value['quanti']!=0) {
                $msgrigo = $i + 1;
                $msg .= "49+";
            }
            if ($value['unimis'] == '' && $value['tiprig']==0 ) { // con un rigo normale 
                $msgrigo = $i + 1;
                $msg .= "50+";
            }
        }
        if ($msg == "") {// nessun errore
            if (preg_match("/^id_([0-9]+)$/", $form['clfoco'], $match)) {
                $new_clfoco = $anagrafica->getPartnerData($match[1], 1);
                $form['clfoco'] = $anagrafica->anagra_to_clfoco($new_clfoco, $admin_aziend['masfor'],$form['pagame']);
            }
            if ($toDo == 'update') { // e' una modifica
                $old_rows = gaz_dbi_dyn_query("*", $gTables['rigbro'], "id_tes = " . $form['id_tes'], "id_rig asc");
                $i = 0;
                $count = count($form['righi']) - 1;
                while ($val_old_row = gaz_dbi_fetch_array($old_rows)) {
                    if ($i <= $count) { //se il vecchio rigo e' ancora presente nel nuovo lo modifico
                        $form['righi'][$i]['id_tes'] = $form['id_tes'];
                        $codice = array('id_rig', $val_old_row['id_rig']);
                        rigbroUpdate($codice, $form['righi'][$i]);
                        if ($form['righi'][$i]['tiprig']==50 && !empty($form['righi'][$i]['extdoc']) && substr($form['righi'][$i]['extdoc'],0,10)!='rigbrodoc_') {
							// se a questo rigo corrispondeva un certificato controllo che non sia stato aggiornato, altrimenti lo cambio
                            $dh = opendir('../../data/files/' . $admin_aziend['company_id']);
                            while (false !== ($filename = readdir($dh))) {
                                $fd = pathinfo($filename);
                                if ($fd['filename'] == 'rigbrodoc_' . $val_old_row['id_rig']) {
                                    // cancello il file precedente indipendentemente dall'estensione
                                    $frep = glob('../../data/files/' . $admin_aziend['company_id'] . "/rigbrodoc_" . $val_old_row['id_rig'] . ".*");
                                    foreach ($frep as $fdel) {// prima cancello eventuali precedenti file temporanei
                                        unlink($fdel);
                                    }
                                }
                            }
                            $tmp_file = "../../data/files/tmp/" . $admin_aziend['adminid'] . '_' . $admin_aziend['company_id'] . '_' . $i . '_' . $form['righi'][$i]['extdoc'];
							// sposto e rinomino il relativo file temporaneo    
                            $fn = pathinfo($form['righi'][$i]['extdoc']);
                            rename($tmp_file, "../../data/files/" . $admin_aziend['company_id'] . "/rigbrodoc_" . $val_old_row['id_rig'] . '.' . $fn['extension']);
						
						}						
                    } else { //altrimenti lo elimino
                        gaz_dbi_del_row($gTables['rigbro'], "id_rig", $val_old_row['id_rig']);
                    }
                    $i++;
                }
                //qualora i nuovi righi fossero di più dei vecchi inserisco l'eccedenza
                for ($i = $i; $i <= $count; $i++) {
                    $form['righi'][$i]['id_tes'] = $form['id_tes'];
                    $last_rigbro_id =rigbroInsert($form['righi'][$i]);
                    if (!empty($form['righi'][$i]['extdoc'])) {
                        $tmp_file = "../../data/files/tmp/" . $admin_aziend['adminid'] . '_' . $admin_aziend['company_id'] . '_' . $i . '_' . $form['righi'][$i]['extdoc'];
// sposto e rinomino il relativo file temporaneo    
                        $fd = pathinfo($form['righi'][$i]['extdoc']);
                        rename($tmp_file, "../../data/files/" . $admin_aziend['company_id'] . "/rigbrodoc_" . $last_rigbro_id . '.' . $fd['extension']);
                    }
				}
                //modifico la testata con i nuovi dati...
                $old_head = array('datfat' => '', 'geneff' => '', 'id_contract' => 0, 'id_con' => 0);
                $form['datfat'] = $old_head['datfat'];
                $form['geneff'] = $old_head['geneff'];
                $form['id_contract'] = $old_head['id_contract'];
                $form['id_con'] = $old_head['id_con'];
                $form['datemi'] = $datemi;
                $form['initra'] = $initra;
                $form['id_orderman'] = $form['in_id_orderman'];
                $codice = array('id_tes', $form['id_tes']);
                tesbroUpdate($codice, $form);
                header("Location: " . $form['ritorno']);
                exit;
            } else { // e' un'inserimento
                // ricavo i progressivi in base al tipo di documento
                $where = "numdoc desc";
                switch ($form['tipdoc']) {
                    case "AOR":
                        $sql_documento = "YEAR(datemi) = " . $form['annemi'] . " and tipdoc = 'AOR' and seziva = $sezione";
                        $where = "numdoc DESC";
                        break;
                    case "APR":
                        $sql_documento = "YEAR(datemi) = " . $form['annemi'] . " and tipdoc = 'APR' and seziva = $sezione";
                        $where = "numdoc DESC";
                        break;
                }
                $rs_ultimo_documento = gaz_dbi_dyn_query("*", $gTables['tesbro'], $sql_documento, $where, 0, 1);
                $ultimo_documento = gaz_dbi_fetch_array($rs_ultimo_documento);
                // se e' il primo documento dell'anno, resetto il contatore
                if ($ultimo_documento) {
                    $form['numdoc'] = $ultimo_documento['numdoc'] + 1;
                } else {
                    $form['numdoc'] = 1;
                }
				// Se la data consegna richiesta è minore della data dell'ordine, la azzera
				if ($datemi>$initra) {
					$initra = 0;
				}
                //inserisco la testata
                $form['protoc'] = 0;
                $form['numfat'] = 0;
                $form['datfat'] = 0;
                $form['status'] = 'GENERATO';
                $form['datemi'] = $datemi;
                $form['initra'] = $initra;
                $form['id_orderman'] = $form['in_id_orderman'];
                tesbroInsert($form);
                //recupero l'id assegnato dall'inserimento
                $ultimo_id = gaz_dbi_last_id();
                //inserisco i righi
                foreach ($form['righi'] as $i => $value) {
                    $form['righi'][$i]['id_tes'] = $ultimo_id;
                    $last_rigbro_id =rigbroInsert($form['righi'][$i]);
					// INIZIO INSERIMENTO DOCUMENTI ALLEGATI
                    if (!empty($form['righi'][$i]['extdoc'])) {
                        $tmp_file = "../../data/files/tmp/" . $admin_aziend['adminid'] . '_' . $admin_aziend['company_id'] . '_' . $i . '_' . $form['righi'][$i]['extdoc'];
						// sposto e rinomino il relativo file temporaneo    
                        $fd = pathinfo($form['righi'][$i]['extdoc']);
                        rename($tmp_file, "../../data/files/" . $admin_aziend['company_id'] . "/rigbrodoc_" . $last_rigbro_id . '.' . $fd['extension']);
                    }
					// FINE INSERIMENTO DOCUMENTI ALLEGATI
                }
                $_SESSION['print_request'] = $ultimo_id;
                header("Location: invsta_broacq.php");
                exit;
            }
        }
    }
    // Se viene inviata la richiesta di conferma fornitore
    if ($_POST['hidden_req'] == 'clfoco') {
        $anagrafica = new Anagrafica();
        if (preg_match("/^id_([0-9]+)$/", $form['clfoco'], $match)) {
            $fornitore = $anagrafica->getPartnerData($match[1], 1);
        } else {
            $fornitore = $anagrafica->getPartner($form['clfoco']);
        }
        $result = gaz_dbi_get_row($gTables['portos'], "codice", $fornitore['portos']);
        $form['portos'] = $result['descri'];
        $result = gaz_dbi_get_row($gTables['spediz'], "codice", $fornitore['spediz']);
        $form['spediz'] = $result['descri'];
        $form['destin'] = $fornitore['destin'];
        $form['id_des'] = $fornitore['id_des'];
        $form['in_codvat'] = $fornitore['aliiva'];
        $form['sconto'] = $fornitore['sconto'];
        $form['pagame'] = $fornitore['codpag'];
        $form['change_pag'] = $fornitore['codpag'];
        $form['banapp'] = $fornitore['banapp'];
        $form['listin'] = $fornitore['listin'];
        $pagame = gaz_dbi_get_row($gTables['pagame'], "codice", $form['pagame']);
        if (($pagame['tippag'] == 'B' or $pagame['tippag'] == 'T' or $pagame['tippag'] == 'V')
                and $fornitore['speban'] == 'S') {
            $form['speban'] = $admin_aziend['sperib'];
            $form['numrat'] = $pagame['numrat'];
        } else {
            $form['speban'] = 0.00;
            $form['numrat'] = 1;
        }
        $form['hidden_req'] = '';
    }

    // Se viene inviata la richiesta di conferma rigo
    //if (isset($_POST['in_submit_x'])) {
    /** ENRICO FEDELE */
    /* con button non funziona _x */
    if (isset($_POST['in_submit'])) {
        /** ENRICO FEDELE */
        $artico = gaz_dbi_get_row($gTables['artico'], $gTables['artico'].".codice", $form['in_codart']);
		$ru = explode("_", $form['in_status']);
        if ($ru[0] == "UPDROW") { //se è un rigo da modificare
            $old_key = intval($ru[1]);
			$old_codart =$ru[2];
            $form['righi'][$old_key]['tiprig'] = $form['in_tiprig'];
            $form['righi'][$old_key]['descri'] = $form['in_descri'];
            $form['righi'][$old_key]['codice_fornitore'] = $form['in_codice_fornitore'];
            $form['righi'][$old_key]['id_mag'] = $form['in_id_mag'];
            $form['righi'][$old_key]['extdoc'] = $form['in_extdoc'];
            $form['righi'][$old_key]['id_orderman'] = $form['in_id_orderman'];
            $form['righi'][$old_key]['status'] = "UPDATE";
            $form['righi'][$old_key]['unimis'] = $form['in_unimis'];
            $form['righi'][$old_key]['quanti'] = $form['in_quanti'];
            $form['righi'][$old_key]['codart'] = $form['in_codart'];
            $form['righi'][$old_key]['codric'] = $form['in_codric'];
            $form['righi'][$old_key]['quality'] = $form['in_quality'];
            $form['righi'][$old_key]['prelis'] = $form['in_prelis'];
            $form['righi'][$old_key]['sconto'] = $form['in_sconto'];
            $form['righi'][$old_key]['codvat'] = $form['in_codvat'];
            $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $form['in_codvat']);
            $form['righi'][$old_key]['pervat'] = $iva_row['aliquo'];
            $form['righi'][$old_key]['annota'] = '';
            $form['righi'][$old_key]['larghezza'] = $form['in_larghezza'];
            $form['righi'][$old_key]['lunghezza'] = $form['in_lunghezza'];
            $form['righi'][$old_key]['spessore'] = $form['in_spessore'];
            $form['righi'][$old_key]['peso_specifico'] = $form['in_peso_specifico'];
            $form['righi'][$old_key]['pezzi'] = $form['in_pezzi'];
            if ($form['in_tiprig'] == 0 && $form['in_codart'] != $old_codart) {  //rigo normale in cui è cambiato il codice articolo
                $form['righi'][$old_key]['annota'] = $artico['annota'];
                $form['righi'][$old_key]['unimis'] = $artico['uniacq'];
                $form['righi'][$old_key]['descri'] = $artico['descri'];
                $form['righi'][$old_key]['prelis'] = $artico['preacq'];
				$form['righi'][$old_key]['larghezza'] = $artico['larghezza'];
				$form['righi'][$old_key]['lunghezza'] = $artico['lunghezza'];
				$form['righi'][$old_key]['spessore'] = $artico['spessore'];
				$form['righi'][$old_key]['peso_specifico'] = $artico['peso_specifico'];
				$form['righi'][$old_key]['pezzi'] = 0;
				$form['righi'][$old_key]['quality'] = $artico['quality'];
            } elseif ($form['in_tiprig'] == 2) { //rigo descrittivo
                $form['righi'][$old_key]['codart'] = "";
                $form['righi'][$old_key]['annota'] = "";
                $form['righi'][$old_key]['unimis'] = "";
                $form['righi'][$old_key]['quanti'] = 0;
                $form['righi'][$old_key]['prelis'] = 0;
                $form['righi'][$old_key]['codric'] = 0;
                $form['righi'][$old_key]['sconto'] = 0;
                $form['righi'][$old_key]['pervat'] = 0;
                $form['righi'][$old_key]['codvat'] = 0;
            } elseif ($form['in_tiprig'] == 1) { //rigo forfait
                $form['righi'][$old_key]['codart'] = "";
                $form['righi'][$old_key]['unimis'] = "";
                $form['righi'][$old_key]['quanti'] = 0;
                $form['righi'][$old_key]['sconto'] = 0;
            } elseif ($form['in_tiprig'] == 3) {   //var.tot.fatt.
                $form['righi'][$old_key]['codart'] = "";
                $form['righi'][$old_key]['quanti'] = "";
                $form['righi'][$old_key]['unimis'] = "";
                $form['righi'][$old_key]['sconto'] = 0;
            }
            ksort($form['righi']);
        } else { //se è un rigo da inserire
            $form['righi'][$next_row]['tiprig'] = $form['in_tiprig'];
            $form['righi'][$next_row]['descri'] = $form['in_descri'];
            $form['righi'][$next_row]['id_mag'] = $form['in_id_mag'];
            $form['righi'][$next_row]['extdoc'] = 0;
			$form['righi'][$next_row]['codice_fornitore'] = 0;
            $form['righi'][$next_row]['id_orderman'] = $form['in_id_orderman'];
            $form['righi'][$next_row]['larghezza'] = 0;
            $form['righi'][$next_row]['lunghezza'] = 0;
            $form['righi'][$next_row]['spessore'] = 0;
            $form['righi'][$next_row]['peso_specifico'] = 0;
            $form['righi'][$next_row]['pezzi'] = 0;
            $form['righi'][$next_row]['status'] = "INSERT";
            if ($form['in_tiprig'] == 0) {  //rigo normale
                $form['righi'][$next_row]['codart'] = $form['in_codart'];
                $form['righi'][$next_row]['annota'] = $artico['annota'];
                $form['righi'][$next_row]['larghezza'] = $artico['larghezza'];
                $form['righi'][$next_row]['lunghezza'] = $artico['lunghezza'];
                $form['righi'][$next_row]['spessore'] = $artico['spessore'];
                $form['righi'][$next_row]['peso_specifico'] = $artico['peso_specifico'];
                $form['righi'][$next_row]['pezzi'] = 0;
                $form['righi'][$next_row]['descri'] = $artico['descri'];
				$form['righi'][$next_row]['codice_fornitore'] = $artico['codice_fornitore']; //M1 aggiunto a mano
                $form['righi'][$next_row]['unimis'] = $artico['uniacq'];
                $form['righi'][$next_row]['codric'] = $form['in_codric'];
                $form['righi'][$next_row]['quality'] = $artico['quality'];
				if ($artico['quality']==''){
					$form['righi'][$next_row]['quality'] = $form['in_quality'];
				}
                $form['righi'][$next_row]['quanti'] = $form['in_quanti'];
                $form['righi'][$next_row]['sconto'] = $form['in_sconto'];
                $form['righi'][$next_row]['prelis'] = $artico['preacq'];
                if ($form['tipdoc'] == 'APR') {  // se è un preventivo non conosco prezzo e sconto
                    $form['righi'][$next_row]['sconto'] = 0;
                    $form['righi'][$next_row]['prelis'] = 0;
                }
                $form['righi'][$next_row]['codvat'] = $admin_aziend['preeminent_vat'];
                $iva_azi = gaz_dbi_get_row($gTables['aliiva'], "codice", $admin_aziend['preeminent_vat']);
                $form['righi'][$next_row]['pervat'] = $iva_azi['aliquo'];
                if ($artico['aliiva'] > 0) {
                    $form['righi'][$next_row]['codvat'] = $artico['aliiva'];
                    $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $artico['aliiva']);
                    $form['righi'][$next_row]['pervat'] = $iva_row['aliquo'];
                }
                if ($form['in_codvat'] > 0) {
                    $form['righi'][$next_row]['codvat'] = $form['in_codvat'];
                    $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $form['in_codvat']);
                    $form['righi'][$next_row]['pervat'] = $iva_row['aliquo'];
                }
                if ($artico['id_cost'] > 0) {
                    $form['righi'][$next_row]['codric'] = $artico['id_cost'];
                    $form['in_codric'] = $artico['id_cost'];
                }
            } elseif ($form['in_tiprig'] == 2 || $form['in_tiprig'] == 51) { //descrittivo o descrittivo con allegato
                $form['righi'][$next_row]['codart'] = "";
                $form['righi'][$next_row]['annota'] = "";
                $form['righi'][$next_row]['unimis'] = "";
                $form['righi'][$next_row]['quanti'] = 0;
                $form['righi'][$next_row]['prelis'] = 0;
                $form['righi'][$next_row]['codric'] = 0;
                $form['righi'][$next_row]['sconto'] = 0;
                $form['righi'][$next_row]['pervat'] = 0;
                $form['righi'][$next_row]['codvat'] = 0;
            } elseif ($form['in_tiprig'] == 3) { // FORFAIT
                $form['righi'][$next_row]['codart'] = "";
                $form['righi'][$next_row]['annota'] = "";
                $form['righi'][$next_row]['unimis'] = "";
                $form['righi'][$next_row]['quanti'] = 0;
                $form['righi'][$next_row]['prelis'] = $form['in_prelis'];
                $form['righi'][$next_row]['codric'] = $form['in_codric'];
                $form['righi'][$next_row]['sconto'] = 0;
                $form['righi'][$next_row]['codvat'] = $form['in_codvat'];
                if ($form['in_codvat'] > 0) {
                    $form['righi'][$next_row]['codvat'] = $form['in_codvat'];
                    $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $form['in_codvat']);
                    $form['righi'][$next_row]['pervat'] = $iva_row['aliquo'];
                } else {
                    $form['righi'][$next_row]['codvat'] = $admin_aziend['preeminent_vat'];
                    $iva_azi = gaz_dbi_get_row($gTables['aliiva'], "codice", $admin_aziend['preeminent_vat']);
                    $form['righi'][$next_row]['pervat'] = $iva_azi['aliquo'];
                }
            } elseif ($form['in_tiprig'] == 50) {  // rigo normale ma con documento allegato e senza codice articolo
                $form['righi'][$next_row]['codart'] = '';
                $form['righi'][$next_row]['annota'] = '';
                $form['righi'][$next_row]['descri'] = '';
				$form['righi'][$next_row]['codice_fornitore'] = ''; //M1 aggiunto a mano
                $form['righi'][$next_row]['unimis'] = '';
                $form['righi'][$next_row]['codric'] = $form['in_codric'];
                $form['righi'][$next_row]['quanti'] = $form['in_quanti'];
                $form['righi'][$next_row]['sconto'] = $form['in_sconto'];
                $form['righi'][$next_row]['prelis'] = 0;
                if ($form['tipdoc'] == 'APR') {  // se è un preventivo non conosco prezzo e sconto
                    $form['righi'][$next_row]['sconto'] = 0;
                    $form['righi'][$next_row]['prelis'] = 0;
                }
                $form['righi'][$next_row]['codvat'] = $admin_aziend['preeminent_vat'];
                $iva_azi = gaz_dbi_get_row($gTables['aliiva'], "codice", $admin_aziend['preeminent_vat']);
                $form['righi'][$next_row]['pervat'] = $iva_azi['aliquo'];
                if ($form['in_codvat'] > 0) {
                    $form['righi'][$next_row]['codvat'] = $form['in_codvat'];
                    $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $form['in_codvat']);
                    $form['righi'][$next_row]['pervat'] = $iva_row['aliquo'];
                }
            }
        }
        // reinizializzo rigo di input tranne che per il tipo rigo e aliquota iva
        $form['in_descri'] = "";
        $form['in_codart'] = "";
        $form['in_codice_fornitore'] = "";
        $form['in_unimis'] = "";
        $form['in_prelis'] = 0.000;
        $form['in_sconto'] = 0;
        $form['in_quanti'] = 0;
        $form['in_codric'] = substr($admin_aziend['impacq'], 0, 3);
        $form['in_id_mag'] = 0;
        $form['in_annota'] = "";
        $form['in_larghezza'] = 0;
        $form['in_lunghezza'] = 0;
        $form['in_spessore'] = 0;
        $form['in_peso_specifico'] = 0;
        $form['in_pezzi'] = 0;
        $form['in_status'] = "INSERT";
        // fine reinizializzo rigo input
        $form['cosear'] = "";
        $next_row++;
    }
    // Se viene inviata la richiesta di spostamento verso l'alto del rigo
    if (isset($_POST['upper_row'])) {
        $upp_key = key($_POST['upper_row']);
        if ($upp_key > 0) {
            $new_key = $upp_key - 1;
        } else {
            $new_key = $next_row - 1;
        }
        $updated_row = $form['righi'][$new_key];
        $form['righi'][$new_key] = $form['righi'][$upp_key];
        $form['righi'][$upp_key] = $updated_row;
        ksort($form['righi']);
        unset($updated_row);
    }
    // Se viene inviata la richiesta elimina il rigo corrispondente
    if (isset($_POST['del'])) {
        $delri = key($_POST['del']);
        array_splice($form['righi'], $delri, 1);
        $next_row--;
    }
} elseif ((!isset($_POST['Update'])) and ( isset($_GET['Update']))) { //se e' il primo accesso per UPDATE
    $tesbro = gaz_dbi_get_row($gTables['tesbro'], "id_tes", $_GET['id_tes']);
    $anagrafica = new Anagrafica();
    $fornitore = $anagrafica->getPartner($tesbro['clfoco']);
    $rs_rig = gaz_dbi_dyn_query("*", $gTables['rigbro'], "id_tes = " . intval($_GET['id_tes']), "id_rig asc");
    $form['id_tes'] = intval($_GET['id_tes']);
    $form['hidden_req'] = '';
    // inizio rigo di input
    $form['in_descri'] = "";
    $form['in_tiprig'] = 0;
    /*    $form['in_artsea'] = $admin_aziend['artsea']; Non serve più */
    $form['in_codart'] = "";
    $form['in_codice_fornitore'] = "";
    $form['in_pervat'] = 0;
    $form['in_unimis'] = "";
    $form['in_prelis'] = 0.000;
    $form['in_sconto'] = 0;
    $form['in_quanti'] = 0;
    $form['in_extdoc'] = 0;
    $form['in_codvat'] = $admin_aziend['preeminent_vat'];
    $form['in_codric'] = substr($admin_aziend['impacq'], 0, 3);
    $form['in_quality'] = "";
    $form['in_id_mag'] = 0;
    $form['in_id_orderman'] = 0;
    $form['in_annota'] = "";
    $form['in_larghezza'] = 0;
    $form['in_lunghezza'] = 0;
    $form['in_spessore'] = 0;
    $form['in_peso_specifico'] = 0;
    $form['in_pezzi'] = 0;
    $form['in_status'] = "INSERT";
    // fine rigo input
    $form['righi'] = array();
    // ...e della testata
    $form['print_total'] = $tesbro['print_total'];
    $form['delivery_time'] = $tesbro['delivery_time'];
    $form['day_of_validity'] = $tesbro['day_of_validity'];
    $form['search']['clfoco'] = $fornitore['ragso1'];
    $form['cosear'] = "";
    $form['coseprod'] = "";
    $form['seziva'] = $tesbro['seziva'];
    $form['tipdoc'] = $tesbro['tipdoc'];
    if ($tesbro['tipdoc'] == 'FAD') {
        $msg .= "Vuoi modificare un D.d.T. gi&agrave; fatturato!<br />";
    }
    if ($tesbro['id_con'] > 0) {
        $msg .= "Questo documento &egrave; gi&agrave; stato contabilizzato!<br />";
    }
    $form['gioemi'] = substr($tesbro['datemi'], 8, 2);
    $form['mesemi'] = substr($tesbro['datemi'], 5, 2);
    $form['annemi'] = substr($tesbro['datemi'], 0, 4);
    $form['protoc'] = $tesbro['protoc'];
    $form['numdoc'] = $tesbro['numdoc'];
    $form['numfat'] = $tesbro['numfat'];
    $form['datfat'] = $tesbro['datfat'];
    $form['clfoco'] = $tesbro['clfoco'];
    $form['pagame'] = $tesbro['pagame'];
    $form['change_pag'] = $tesbro['pagame'];
    $form['speban'] = $tesbro['speban'];
    $pagame = gaz_dbi_get_row($gTables['pagame'], "codice", $form['pagame']);
    if (($pagame['tippag'] == 'B' or $pagame['tippag'] == 'T' or $pagame['tippag'] == 'V') and $fornitore['speban'] == 'S') {
        $form['numrat'] = $pagame['numrat'];
    } else {
        $form['speban'] = 0.00;
        $form['numrat'] = 1;
    }
    $form['banapp'] = $tesbro['banapp'];
    $form['listin'] = $tesbro['listin'];
	$form['giocon'] = substr($tesbro['initra'], 8, 2);
	$form['mescon'] = substr($tesbro['initra'], 5, 2);
	$form['anncon'] = substr($tesbro['initra'], 0, 4);
	if ($form['anncon'] == 0) {
		$form['anncon']=$form['annemi'];
	}	
    $form['spediz'] = $tesbro['spediz'];
    $form['portos'] = $tesbro['portos'];
    $form['destin'] = $tesbro['destin'];
    $form['id_des'] = $tesbro['id_des'];
    $form['traspo'] = $tesbro['traspo'];
    $form['spevar'] = $tesbro['spevar'];
    $form['cauven'] = $tesbro['cauven'];
    $form['caucon'] = $tesbro['caucon'];
    $form['caumag'] = $tesbro['caumag'];
    $form['caucon'] = $tesbro['caucon'];
    $form['id_agente'] = $tesbro['id_agente'];
    $form['id_parent_doc'] = $tesbro['id_parent_doc'];
    $form['sconto'] = $tesbro['sconto'];
    $next_row = 0;
    while ($rigo = gaz_dbi_fetch_array($rs_rig)) {
        $articolo = gaz_dbi_get_row($gTables['artico'], "codice", $rigo['codart']);
        $form['righi'][$next_row]['descri'] = $rigo['descri'];
		$form['righi'][$next_row]['codice_fornitore'] = $rigo['codice_fornitore'];//M1 aggiunto a mano
        $form['righi'][$next_row]['tiprig'] = $rigo['tiprig'];
        $form['righi'][$next_row]['codart'] = $rigo['codart'];
        $form['righi'][$next_row]['pervat'] = $rigo['pervat'];
        $form['righi'][$next_row]['unimis'] = $rigo['unimis'];
        $form['righi'][$next_row]['prelis'] = $rigo['prelis'];
        $form['righi'][$next_row]['sconto'] = $rigo['sconto'];
        $form['righi'][$next_row]['quanti'] = gaz_format_quantity($rigo['quanti'], 0, $admin_aziend['decimal_quantity']);
        $form['righi'][$next_row]['codvat'] = $rigo['codvat'];
        $form['righi'][$next_row]['codric'] = $rigo['codric'];
        $form['righi'][$next_row]['quality'] = $rigo['quality'];
        $form['in_quality'] = $rigo['quality']; // ripropongo l'ultima qualità
        $form['righi'][$next_row]['id_mag'] = $rigo['id_mag'];
        $form['in_id_orderman'] = $rigo['id_orderman'];
		$orderman = gaz_dbi_get_row($gTables['orderman'], "id", $rigo['id_orderman']);
        $form['coseprod'] = $orderman['description'];
        $form['righi'][$next_row]['id_orderman'] = $rigo['id_orderman'];
        $form['righi'][$next_row]['annota'] = $articolo['annota'];
        $form['righi'][$next_row]['larghezza'] = $rigo['larghezza'];
        $form['righi'][$next_row]['lunghezza'] = $rigo['lunghezza'];
        $form['righi'][$next_row]['spessore'] = $rigo['spessore'];
        $form['righi'][$next_row]['peso_specifico'] = $rigo['peso_specifico'];
        $form['righi'][$next_row]['pezzi'] = $rigo['pezzi'];
        $form['righi'][$next_row]['extdoc'] = '';
        $form['righi'][$next_row]['status'] = "UPDATE";
		// recupero il filename dal filesystem e lo sposto sul tmp 
		$dh = opendir('../../data/files/' . $admin_aziend['company_id']);
		while (false !== ($filename = readdir($dh))) {
				$fd = pathinfo($filename);
				$r = explode('_', $fd['filename']);
				if ($r[0] == 'rigbrodoc' && $r[1] == $rigo['id_rig']) { 
					/* 	uso id_body_text per mantenere il riferimento riferimento al file del documento esterno
					* 	e riassegno il nome file
					*/
					$form['righi'][$next_row]['extdoc'] = $fd['basename'];
				}
		}
        $next_row++;
    }
} elseif (!isset($_POST['Insert'])) { //se e' il primo accesso per INSERT
    $form['tipdoc'] = strtoupper(substr($_GET['tipdoc'], 0, 3));
    $form['id_tes'] = "";
    $form['hidden_req'] = '';
    $form['gioemi'] = date("d");
    $form['mesemi'] = date("m");
    $form['annemi'] = date("Y");
    $form['righi'] = array();
    $next_row = 0;
    // inizio rigo di input
    $form['in_descri'] = "";
    $form['in_tiprig'] = 0;
    $form['in_codice_fornitore'] = '';
    $form['in_codart'] = "";
    $form['in_extdoc'] = 0;
    $form['in_pervat'] = "";
    $form['in_unimis'] = "";
    $form['in_prelis'] = 0.000;
    $form['in_sconto'] = 0;
    $form['in_quanti'] = 0;
    $form['in_codvat'] = $admin_aziend['preeminent_vat'];
    $form['in_codric'] = substr($admin_aziend['impacq'], 0, 3);
    $form['in_quality'] = '';
    $form['in_id_mag'] = 0;
    $form['in_id_orderman'] = 0;
    $form['in_annota'] = "";
    $form['in_larghezza'] = 0;
    $form['in_lunghezza'] = 0;
    $form['in_spessore'] = 0;
    $form['in_peso_specifico'] = 0;
    $form['in_pezzi'] = 0;
    $form['in_status'] = "INSERT";
    // fine rigo input
    $form['search']['clfoco'] = '';
    $form['cosear'] = "";
    $form['coseprod'] = "";
    if (isset($_GET['seziva'])) {
        $form['seziva'] = $_GET['seziva'];
    } else {
        $form['seziva'] = 1;
    }
    $form['protoc'] = "";
    $form['numdoc'] = "";
    $form['numfat'] = "";
    $form['datfat'] = "";
    $form['clfoco'] = "";
    $form['pagame'] = "";
    $form['change_pag'] = "";
    $form['banapp'] = "";
    $form['listin'] = "";
    $form['giocon'] = date("d");
    $form['mescon'] = date("m");
    $form['anncon'] = date("Y");
    $form['destin'] = "";
    $form['id_des'] = "";
    $form['spediz'] = "";
    $form['portos'] = "";
    $form['traspo'] = 0.00;
    $form['numrat'] = 1;
    $form['speban'] = 0;
    $form['spevar'] = 0;
    $form['cauven'] = 0;
    $form['caucon'] = '';
    $form['caumag'] = 5;
    $form['id_agente'] = 0;
    $form['id_parent_doc'] = 0;
    $form['sconto'] = 0;
    $form['print_total'] = 1;
    $form['delivery_time'] = 10;
    $form['day_of_validity'] = 15;
    $fornitore['indspe'] = "";
}
require("../../library/include/header.php");
$script_transl = HeadMain(0, array('calendarpopup/CalendarPopup','custom/autocomplete'));
$gForm = new acquisForm();

if ($form['id_tes'] > 0) {
    $title = ucfirst($script_transl[$toDo] . $script_transl[0][$form['tipdoc']]) . " n." . $form['numdoc'];
} else {
    $title = ucfirst($script_transl[$toDo] . $script_transl[0][$form['tipdoc']]);
}
?>
<script language="JavaScript">
function pulldown_menu(selectName, destField)
{
    // Create a variable url to contain the value of the
    // selected option from the the form named broven and variable selectName
    var url = document.docacq[selectName].options[document.docacq[selectName].selectedIndex].value;
    document.docacq[destField].value = url;
}

function choicequality(row)
{
	$( "#search_quality"+row ).autocomplete({
		source: "../../modules/root/search.php?opt=quality",
		minLength: 2,
        html: true, // optional (jquery.ui.autocomplete.html.js required)
 
      	// optional (if other layers overlap autocomplete list)
        open: function(event, ui) {
            $(".ui-autocomplete").css("z-index", 1000);
        },
		select: function(event, ui) {
			$(this).val(ui.item.value);
			$(this).closest("form").submit();
		}
	});
}
</script>
<?php
echo "<form method=\"POST\" name=\"docacq\" enctype=\"multipart/form-data\">\n";
echo "<input type=\"hidden\" name=\"" . ucfirst($toDo) . "\" value=\"\">\n";
echo "<input type=\"hidden\" value=\"{$form['id_tes']}\" name=\"id_tes\">\n";
echo "<input type=\"hidden\" value=\"{$form['seziva']}\" name=\"seziva\">\n";
echo "<input type=\"hidden\" value=\"{$form['tipdoc']}\" name=\"tipdoc\">\n";
echo "<input type=\"hidden\" value=\"{$form['ritorno']}\" name=\"ritorno\">\n";
echo "<input type=\"hidden\" value=\"{$form['change_pag']}\" name=\"change_pag\">\n";
echo "<input type=\"hidden\" value=\"{$form['protoc']}\" name=\"protoc\">\n";
echo "<input type=\"hidden\" value=\"{$form['numdoc']}\" name=\"numdoc\">\n";
echo "<input type=\"hidden\" value=\"{$form['numfat']}\" name=\"numfat\">\n";
echo "<input type=\"hidden\" value=\"{$form['datfat']}\" name=\"datfat\">\n";
echo "<input type=\"hidden\" value=\"{$form['delivery_time']}\" name=\"delivery_time\">\n";
echo "<input type=\"hidden\" value=\"{$form['day_of_validity']}\" name=\"day_of_validity\">\n";
echo "<input type=\"hidden\" value=\"{$form['print_total']}\" name=\"print_total\">\n";
echo '<input type="hidden" value="' . (isset($_POST['last_focus']) ? $_POST['last_focus'] : "") . '" name="last_focus" />';
echo "<input type=\"hidden\" value=\"\" id=\"dialog_row_focus\" />\n";
echo "<input type=\"hidden\" value=\"" . $form['hidden_req'] . "\" name=\"hidden_req\" />\n";
echo "<div align=\"center\" class=\"FacetFormHeaderFont\">$title ";
$select_fornitore = new selectPartner("clfoco");
$select_fornitore->selectDocPartner('clfoco', $form['clfoco'], $form['search']['clfoco'], 'clfoco', $script_transl['mesg'], $admin_aziend['masfor']);
echo "</div>\n";
echo "<table class=\"Tlarge table table-striped table-bordered table-condensed table-responsive\">\n";
echo "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[4]</td><td class=\"FacetDataTD\">\n";
echo "<select name=\"seziva\" class=\"FacetSelect\">\n";
for ($counter = 1; $counter <= 9; $counter++) {
    $selected = "";
    if ($form["seziva"] == $counter) {
        $selected = " selected ";
    }
    echo "<option value=\"" . $counter . "\"" . $selected . ">" . $counter . "</option>\n";
}
echo "</select></td>\n";
if (!empty($msg)) {
    $message = "";
    $rsmsg = array_slice(explode('+', chop($msg)), 0, -1);
    foreach ($rsmsg as $value) {
        $message .= $script_transl['error'] . "! -> ";
        $rsval = explode('-', chop($value));
        foreach ($rsval as $valmsg) {
            $message .= $script_transl[$valmsg] . " ";
        }
        $message .= "<br />";
    }
    echo '<td colspan="2" class="FacetDataTDred">' . $message . "</td>\n";
} else {
    echo "<td class=\"FacetFieldCaptionTD\">$script_transl[5]</td><td>" . $fornitore['indspe'] . "<br />";
    echo "</td>\n";
}
echo "<td class=\"FacetFieldCaptionTD\">$script_transl[6]</td><td class=\"FacetDataTD\">\n";
// select del giorno
echo "\t <select name=\"gioemi\" class=\"FacetSelect\" >\n";
for ($counter = 1; $counter <= 31; $counter++) {
    $selected = "";
    if ($counter == $form['gioemi'])
        $selected = "selected";
    echo "\t\t <option value=\"$counter\" $selected >$counter</option>\n";
}
echo "\t </select>\n";
// select del mese
echo "\t <select name=\"mesemi\" class=\"FacetSelect\" >\n";
for ($counter = 1; $counter <= 12; $counter++) {
    $selected = "";
    if ($counter == $form['mesemi'])
        $selected = "selected";
    $nome_mese = ucwords(strftime("%B", mktime(0, 0, 0, $counter, 1, 0)));
    echo "\t\t <option value=\"$counter\"  $selected >$nome_mese</option>\n";
}
echo "\t </select>\n";
// select del anno
echo "\t <select name=\"annemi\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
for ($counter = $form['annemi'] - 10; $counter <= $form['annemi'] + 10; $counter++) {
    $selected = "";
    if ($counter == $form['annemi'])
        $selected = "selected";
    echo "\t\t <option value=\"$counter\"  $selected >$counter</option>\n";
}
echo "\t </select></td></tr>\n";
echo "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[7]</td><td class=\"FacetDataTD\">\n";
echo "<select name=\"listin\" class=\"FacetSelect\">\n";
for ($lis = 1; $lis <= 3; $lis++) {
    $selected = "";
    if ($form["listin"] == $lis) {
        $selected = " selected ";
    }
    echo "<option value=\"" . $lis . "\"" . $selected . ">" . $lis . "</option>\n";
}
echo "</select></td>\n";
echo "<td class=\"FacetFieldCaptionTD\">$script_transl[8]</td><td  class=\"FacetDataTD\">\n";
$select_pagame = new selectpagame("pagame");
$select_pagame->addSelected($form["pagame"]);
$select_pagame->output();
echo "</td><td class=\"FacetFieldCaptionTD\">$script_transl[9]</td><td  class=\"FacetDataTD\">\n";
$select_banapp = new selectbanapp("banapp");
$select_banapp->addSelected($form["banapp"]);
$select_banapp->output();
echo "</td></tr>\n";
// Modifica di Giorgio Zanella per gestire la data di consegna richiesta su ordine fornitore
echo "<tr><td colspan=\"3\" class=\"FacetFieldCaptionTD\">Data di consegna richiesta</td>\n";
echo "<td class=\"FacetDataTD\">\n";
// select del giorno
echo "\t <select name=\"giocon\" class=\"FacetSelect\" >\n";
for ($counter = 1; $counter <= 31; $counter++) {
    $selected = "";
    if ($counter == $form['giocon'])
        $selected = "selected";
    echo "\t\t <option value=\"$counter\" $selected >$counter</option>\n";
}
echo "\t </select>\n";
// select del mese
echo "\t <select name=\"mescon\" class=\"FacetSelect\" >\n";
for ($counter = 1; $counter <= 12; $counter++) {
    $selected = "";
    if ($counter == $form['mescon'])
        $selected = "selected";
    $nome_mese = ucwords(strftime("%B", mktime(0, 0, 0, $counter, 1, 0)));
    echo "\t\t <option value=\"$counter\"  $selected >$nome_mese</option>\n";
}
echo "\t </select>\n";
// select del anno
echo "\t <select name=\"anncon\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
for ($counter = $form['anncon'] - 10; $counter <= $form['anncon'] + 10; $counter++) {
    $selected = "";
    if ($counter == $form['anncon'])
        $selected = "selected";
    echo "\t\t <option value=\"$counter\"  $selected >$counter</option>\n";
}
echo '</td><td class="FacetFieldCaptionTD">Produzione:</td><td colspan="3">  ';
$select_prod = new selectproduction("in_id_orderman");
$select_prod->addSelected($form['in_id_orderman']);
$select_prod->output($form['coseprod']);
// fine modifiche
echo '</td></tr>';
echo "</table>\n";
echo "<div class=\"FacetSeparatorTD\" align=\"center\">$script_transl[1]</div>\n";
echo "<table class=\"Tlarge table table-striped table-bordered table-condensed table-responsive\">\n";
echo "<input type=\"hidden\" value=\"{$form['in_codice_fornitore']}\" name=\"in_codice_fornitore\" />\n";
echo "<input type=\"hidden\" value=\"{$form['in_quality']}\" name=\"in_quality\" id=\"in_quality\" />\n";
echo "<input type=\"hidden\" value=\"{$form['in_descri']}\" name=\"in_descri\" />\n";
echo "<input type=\"hidden\" value=\"{$form['in_pervat']}\" name=\"in_pervat\" />\n";
echo "<input type=\"hidden\" value=\"{$form['in_unimis']}\" name=\"in_unimis\" />\n";
echo "<input type=\"hidden\" value=\"{$form['in_prelis']}\" name=\"in_prelis\" />\n";
echo "<input type=\"hidden\" value=\"{$form['in_extdoc']}\" name=\"in_extdoc\" />\n";
echo "<input type=\"hidden\" value=\"{$form['in_id_mag']}\" name=\"in_id_mag\" />\n";
echo "<input type=\"hidden\" value=\"{$form['in_annota']}\" name=\"in_annota\" />\n";
echo "<input type=\"hidden\" value=\"{$form['in_larghezza']}\" name=\"in_larghezza\" />\n";
echo "<input type=\"hidden\" value=\"{$form['in_lunghezza']}\" name=\"in_lunghezza\" />\n";
echo "<input type=\"hidden\" value=\"{$form['in_spessore']}\" name=\"in_spessore\" />\n";
echo "<input type=\"hidden\" value=\"{$form['in_peso_specifico']}\" name=\"in_peso_specifico\" />\n";
echo "<input type=\"hidden\" value=\"{$form['in_pezzi']}\" name=\"in_pezzi\" />\n";
echo "<input type=\"hidden\" value=\"{$form['in_status']}\" name=\"in_status\" />\n";
echo '<tr><td class="FacetColumnTD">'.$script_transl[17].": ";
$gForm->selTypeRow('in_tiprig', $form['in_tiprig']);
echo $script_transl[15].': ';
$select_artico = new selectartico("in_codart");
$select_artico->addSelected($form['in_codart']);
$select_artico->output($form['cosear']);
echo '&nbsp;<a href="#" id="addmodal" href="#myModal" data-toggle="modal" data-target="#edit-modal" class="btn btn-xs btn-default"><i class="glyphicon glyphicon-export"></i> ' . $script_transl['add_article'] . '</a>';
/** ENRICO FEDELE */
echo "</td><td class=\"FacetColumnTD\">$script_transl[16]: <input type=\"text\" value=\"{$form['in_quanti']}\" maxlength=\"11\" size=\"7\" name=\"in_quanti\" tabindex=\"5\" accesskey=\"q\">\n";
/*
  echo "</td><td class=\"FacetColumnTD\" align=\"right\"><input type=\"image\" name=\"in_submit\" src=\"../../library/images/vbut.gif\" tabindex=\"6\" title=\"".$script_transl['submit'].$script_transl['thisrow']."!\">\n"; */
/** ENRICO FEDELE */
/* glyph-icon */
echo '  </td>
		<td class="FacetColumnTD" align="right">
			<button type="submit" class="btn btn-default btn-sm" name="in_submit" title="' . $script_transl['submit'] . $script_transl['thisrow'] . '!" tabindex="6"><i class="glyphicon glyphicon-ok"></i></button>
		</td>
	   </tr>';
/** ENRICO FEDELE */
echo "</td></tr>\n";
echo '<tr><td class="FacetColumnTD">';
echo $script_transl[18].": ";
$select_codric = new selectconven("in_codric");
$select_codric->addSelected($form['in_codric']);
$select_codric->output(substr($form['in_codric'], 0, 1));
echo " %$script_transl[24]: <input type=\"text\" value=\"{$form['in_sconto']}\" maxlength=\"4\" size=\"1\" name=\"in_sconto\">";

echo "</td><td class=\"FacetColumnTD\"> $script_transl[19]: ";
$select_in_codvat = new selectaliiva("in_codvat");
$select_in_codvat->addSelected($form["in_codvat"]);
$select_in_codvat->output();
echo "</td><td class=\"FacetColumnTD\"></td></tr>\n";
$quatot = 0;
$totimpmer = 0.00;
$totivafat = 0.00;
$totimpfat = 0.00;
/** ENRICO FEDELE */
/* Cominciamo la transizione verso le tabelle bootstrap */
echo '</table>
	  <table class="Tlarge table table-striped table-bordered table-condensed table-responsive">
		  <thead>
			<tr>
				<th class="FacetFieldCaptionTD"></th>
				<th class="FacetFieldCaptionTD">' . $script_transl[20] . '</th>
				<th class="FacetFieldCaptionTD"> Codice Fornitore/Doc. </th>
				<th class="FacetFieldCaptionTD">' . $script_transl[21] . '</th>
				<th class="FacetFieldCaptionTD">' . $script_transl[22] . '</th>
				<th class="FacetFieldCaptionTD">' . $script_transl[16] . '</th>
				<th class="FacetFieldCaptionTD">' . $script_transl[23] . '</th>
				<th class="FacetFieldCaptionTD">%' . substr($script_transl[24], 0, 2) . '</th>
				<th class="FacetFieldCaptionTD" align="right">' . $script_transl[25] . '</th>
				<th class="FacetFieldCaptionTD">' . $script_transl[19] . '</th>
				<th class="FacetFieldCaptionTD">' . $script_transl[18] . '</th>
				<th class="FacetFieldCaptionTD"></th>
			</tr>
		   </thead>
		   <tbody>';
/** ENRICO FEDELE */
$castel = array();
$last_row = array();
$ctrl_orderman=0;
foreach ($form['righi'] as $key => $value) {
    //calcolo il totale del peso in kg
    switch (strtolower($value['unimis'])) {
        case "kg":
            $quatot = $value['quanti'] + $quatot;
            break;
    }
    //creo il castelletto IVA
    $codice_vat = $value['codvat'];
    $tiporigo = $value['tiprig'];
    $descrizione = $value['descri'];
    //calcolo importo rigo
    if ($tiporigo == 0 || $tiporigo ==50) {//se del tipo normale o con documento allegato
        $imprig = CalcolaImportoRigo($form['righi'][$key]['quanti'], $form['righi'][$key]['prelis'], $form['righi'][$key]['sconto']);
    } elseif ($tiporigo == 1) {//ma se del tipo forfait
        $imprig = CalcolaImportoRigo(1, $form['righi'][$key]['prelis'], 0);
    }
    if ($tiporigo <= 1 || $tiporigo ==50) { // se del tipo normale, forfait o documento  allegato
        if (!isset($castel[$codice_vat])) {
            $castel[$codice_vat] = "0.00";
        }
        $castel[$codice_vat] = number_format(($castel[$codice_vat] + $imprig), 2, '.', '');
    }
    if ($form['righi'][$key]['tiprig'] == 1)
        $imprig = number_format($form['righi'][$key]['prelis'], 2, '.', '');
    echo "<input type=\"hidden\" value=\"{$value['codart']}\" name=\"righi[{$key}][codart]\">\n";
    echo "<input type=\"hidden\" value=\"{$value['codice_fornitore']}\" name=\"righi[{$key}][codice_fornitore]\">\n";
    echo "<input type=\"hidden\" value=\"{$value['status']}\" name=\"righi[{$key}][status]\">\n";
    echo "<input type=\"hidden\" value=\"{$value['tiprig']}\" name=\"righi[{$key}][tiprig]\">\n";
    echo "<input type=\"hidden\" value=\"{$value['codvat']}\" name=\"righi[{$key}][codvat]\">\n";
    echo "<input type=\"hidden\" value=\"{$value['pervat']}\" name=\"righi[{$key}][pervat]\">\n";
    echo "<input type=\"hidden\" value=\"{$value['codric']}\" name=\"righi[{$key}][codric]\">\n";
    echo "<input type=\"hidden\" value=\"{$value['quality']}\" name=\"righi[{$key}][quality]\">\n";
    echo "<input type=\"hidden\" value=\"{$value['id_mag']}\" name=\"righi[{$key}][id_mag]\">\n";
    echo "<input type=\"hidden\" value=\"{$value['id_orderman']}\" name=\"righi[{$key}][id_orderman]\">\n";
    echo "<input type=\"hidden\" value=\"{$value['annota']}\" name=\"righi[{$key}][annota]\">\n";
    echo "<input type=\"hidden\" value=\"{$value['larghezza']}\" name=\"righi[{$key}][larghezza]\">\n";
    echo "<input type=\"hidden\" value=\"{$value['lunghezza']}\" name=\"righi[{$key}][lunghezza]\">\n";
    echo "<input type=\"hidden\" value=\"{$value['spessore']}\" name=\"righi[{$key}][spessore]\">\n";
    echo "<input type=\"hidden\" value=\"{$value['peso_specifico']}\" name=\"righi[{$key}][peso_specifico]\">\n";
    echo "<input type=\"hidden\" value=\"{$value['pezzi']}\" name=\"righi[{$key}][pezzi]\">\n";
	echo '<input type="hidden" value="' . $value['extdoc'] . '" name="righi[' . $key . '][extdoc]" />';

	// stampo l'intestazione della produzione di provenienza
    if ($ctrl_orderman<>$value['id_orderman']) { // ricordo con un rigo la produzione di riferimento
		if ($value['id_orderman']==0){
			$descri_orderman='<div class="btn btn-xs btn-warning"> Non riferiti ad una produzione <i class="glyphicon glyphicon-arrow-down"> </i></div>';
		} else {
			$orderman = gaz_dbi_get_row($gTables['orderman'], "id", $value['id_orderman']);
			$descri_orderman='<div class="btn btn-xs btn-info">Materiale per Produzione n. ' .$orderman['id'].' - '.$orderman['description'].' <i class="glyphicon glyphicon-arrow-down"> </i></div>';
		}
		echo '<tr><td colspan=12>'.$descri_orderman."</td></tr>\n";
	}
    //stampo i righi in modo diverso a secondo del tipo
    $peso = 0;
    if ($value['peso_specifico'] <> 0) {
        $peso = gaz_format_number($value['quanti'] / $value['peso_specifico']);
    }
    switch ($value['tiprig']) {
        case "0":
            echo '<tr>';
            echo '<td title="' . $script_transl['update'] . $script_transl['thisrow'] . '!">
						<button type="image" name="upper_row[' . $key . ']" class="btn btn-default btn-sm" title="' . $script_transl['3'] . '!">
							<i class="glyphicon glyphicon-arrow-up"></i>
						</button> </td> 
					  <td>
<button name="upd_row[' . $key . ']" class="btn btn-xs btn-success btn-block" type="submit">
							<i class="glyphicon glyphicon-refresh"></i>&nbsp;' . $value['codart'] . '
						</button>
					  </td>';
			echo '<td>
					<input type="text" name="righi[' . $key . '][codice_fornitore]" value="' . $value['codice_fornitore'] . '" maxlength="15" size="15" />
					<button class="btn btn-default btn-sm" type="button" data-toggle="collapse" data-target="#quality_'.$key.'" aria-expanded="false" aria-controls="quality_'.$key.'" title="Descrizione qualità" title="Scegli la qualità del prodotto"><i class="glyphicon glyphicon-tags"></i> '.substr($value['quality'],0,10).'</button><div class="collapse" id="quality_'.$key.'">Qualità: <input id="search_quality'.$key.'" onClick="choicequality(\''.$key.'\');"  name="righi[' . $key . '][quality]" value="'. $value['quality'] .'" rigo="'. $key .'" type="text" /></div>
					</td>';
            echo '<td>
						<input type="text" name="righi[' . $key . '][descri]" value="' . $descrizione . '" maxlength="50" size="50" />
					  </td>';
            /* Peso */
            /* <input class="myTooltip" data-type="product" data-id="firefox" data-title=""  /> */
            echo '<td>
				<input class="gazie-tooltip" data-type="weight" data-id="' . $peso . '" data-title="' . $script_transl['weight'] . '" type="text" name="righi[' . $key . '][unimis]" value="' . $value['unimis'] . '" maxlength="3" size="1" />
				</td>
				<td>
				<input class="gazie-tooltip" data-type="weight" data-id="' . $peso . '" data-title="' . $script_transl['weight'] . '" type="text" name="righi[' . $key . '][quanti]" value="' . $value['quanti'] . '" align="right" maxlength="11" size="4" onchange="document.docacq.last_focus.value=this.id; this.form.submit();" />';
            echo ' <button class="btn btn-default btn-sm" type="image" data-toggle="collapse" onclick="weightfromdim(\''.$key.'\');" title="Calcola peso, superficie, volume"><i class="glyphicon glyphicon-scale"></i></button> ';
		    echo '</td>';
            /** ENRICO FEDELE */
            echo "<td><input type=\"text\" name=\"righi[{$key}][prelis]\" value=\"{$value['prelis']}\" align=\"right\" maxlength=\"11\" size=\"7\" onchange=\"document.docacq.last_focus.value=this.id; this.form.submit()\" /></td>\n";
            echo "<td><input type=\"text\" name=\"righi[{$key}][sconto]\" value=\"{$value['sconto']}\" maxlength=\"4\" size=\"1\" onchange=\"this.form.submit()\" /></td>\n";
            echo "<td class=\"text-right\">" . gaz_format_number($imprig) . "</td>\n";
            echo "<td>{$value['pervat']}%</td>\n";
            echo "<td>" . $value['codric'] . "</td>\n";

            $last_row[] = array_unshift($last_row, '' . $value['codart'] . ', ' . $value['descri'] . ', ' . $value['quanti'] . $value['unimis'] . ', <strong>' . $script_transl[23] . '</strong>: ' . gaz_format_number($value['prelis']) . ', %<strong>' . substr($script_transl[24], 0, 2) . '</strong>: ' . gaz_format_number($value['sconto']) . ', <strong>' . $script_transl[25] . '</strong>: ' . gaz_format_number($imprig) . ', <strong>' . $script_transl[19] . '</strong>: ' . $value['pervat'] . '%, <strong>' . $script_transl[18] . '</strong>: ' . $value['codric']);
            break;
        case "1":
			echo "<td><button type=\"image\" name=\"upper_row[" . $key . "]\" class=\"btn btn-default btn-sm\" title=\"" . $script_transl['3'] . "!\"><i class=\"glyphicon glyphicon-arrow-up\"></i></button></td>";
            echo "<td title=\"" . $script_transl['update'] . $script_transl['thisrow'] . "!\"><input class=\"FacetDataTDsmall\" type=\"submit\" name=\"upd_row[{$key}]\" value=\"* forfait *\" /></td>\n";
            echo "<td></td><td><input type=\"text\" name=\"righi[{$key}][descri]\" value=\"$descrizione\" maxlength=\"50\" size=\"50\" /></td>\n";
            echo "<td><input type=\"hidden\" name=\"righi[{$key}][unimis]\" value=\"\" /></td>\n";
            echo "<td><input type=\"hidden\" name=\"righi[{$key}][quanti]\" value=\"\" /></td>\n";
            echo "<td><input type=\"hidden\" name=\"righi[{$key}][sconto]\" value=\"\" /></td>\n";
            echo "<td></td>\n";
            echo "<td class=\"text-right\"><input type=\"text\" name=\"righi[{$key}][prelis]\" value=\"{$value['prelis']}\" align=\"right\" maxlength=\"11\" size=\"7\" onchange=\"this.form.submit()\" /></td>\n";
            echo "<td>{$value['pervat']}%</td>\n";
            echo "<td>" . $value['codric'] . "</td>\n";
            $last_row[] = array_unshift($last_row, $script_transl['typerow'][$value['tiprig']]);
            break;
        case "2":
			echo "<td><button type=\"image\" name=\"upper_row[" . $key . "]\" class=\"btn btn-default btn-sm\" title=\"" . $script_transl['3'] . "!\"><i class=\"glyphicon glyphicon-arrow-up\"></i></button></td>";
            echo "<td title=\"" . $script_transl['update'] . $script_transl['thisrow'] . "!\"><input class=\"FacetDataTDsmall\" type=\"submit\" name=\"upd_row[{$key}]\" value=\"* descrittivo *\" /></td>\n";
            echo "<td></td>\n";
            echo "<td><input type=\"text\"   name=\"righi[{$key}][descri]\" value=\"$descrizione\" maxlength=\"50\" size=\"50\" /></td>\n";
            echo "<td><input type=\"hidden\" name=\"righi[{$key}][unimis]\" value=\"\" /></td>\n";
            echo "<td><input type=\"hidden\" name=\"righi[{$key}][quanti]\" value=\"\" /></td>\n";
            echo "<td><input type=\"hidden\" name=\"righi[{$key}][prelis]\" value=\"\" /></td>\n";
            echo "<td><input type=\"hidden\" name=\"righi[{$key}][sconto]\" value=\"\" /></td>\n";
            echo "<td></td>\n";
            echo "<td></td>\n";
            echo "<td></td>\n";
            $last_row[] = array_unshift($last_row, $script_transl['typerow'][$value['tiprig']]);
            break;
        case "3":
            echo "<td title=\"" . $script_transl['update'] . $script_transl['thisrow'] . "!\"><input class=\"FacetDataTDsmall\" type=\"submit\" name=\"upd_row[{$key}]\" value=\"* var.tot.fattura *\" /></td>\n";
            echo "	<td><input type=\"text\"   name=\"righi[{$key}][descri]\" value=\"$descrizione\" maxlength=\"50\" size=\"50\"></td>
						<td>
							<button type=\"image\" name=\"upper_row[" . $key . "]\" class=\"btn btn-default btn-sm\" title=\"" . $script_transl['3'] . "!\">
								<i class=\"glyphicon glyphicon-arrow-up\"></i>
							</button>
						</td>\n";
            echo "<td><input type=\"hidden\" name=\"righi[{$key}][unimis]\" value=\"\" /></td>\n";
            echo "<td><input type=\"hidden\" name=\"righi[{$key}][quanti]\" value=\"\" /></td>\n";
            echo "<td><input type=\"hidden\" name=\"righi[{$key}][sconto]\" value=\"\" /></td>\n";
            echo "<td></td>\n";
            echo "<td class=\"text-right\"><input type=\"text\" name=\"righi[{$key}][prelis]\" value=\"{$value['prelis']}\" align=\"right\" maxlength=\"11\" size=\"7\" /></td>\n";
            echo "<td></td>\n";
            echo "<td></td>\n";
            $last_row[] = array_unshift($last_row, $script_transl['typerow'][$value['tiprig']]);
            break;
        case "50":
			echo "<td><button type=\"image\" name=\"upper_row[" . $key . "]\" class=\"btn btn-default btn-sm\" title=\"" . $script_transl['3'] . "!\"><i class=\"glyphicon glyphicon-arrow-up\"></i></button></td>";
            echo "<td title=\"" . $script_transl['update'] . $script_transl['thisrow'] . "!\"><input class=\"FacetDataTDsmall\" type=\"submit\" name=\"upd_row[{$key}]\" value=\"* documento allegato *\" /></td>\n";
                echo '<td>';
                if (empty($form['righi'][$key]['extdoc'])) {
                    echo '<div><button class="btn btn-xs btn-danger" type="image" data-toggle="collapse" href="#extdoc_dialog' . $key . '">'
                    . $script_transl['insert'] . ' documento esterno <i class="glyphicon glyphicon-tag"></i>'
                    . '</button></div>';
                } else {
                    echo '<div>documento esterno:<button class="btn btn-xs btn-success" type="image" data-toggle="collapse" href="#extdoc_dialog' . $key . '">'
                    . $form['righi'][$key]['extdoc'] . ' <i class="glyphicon glyphicon-tag"></i>'
                    . '</button></div>';
                }
				echo '<div id="extdoc_dialog' . $key . '" class="collapse" >
                        <div class="form-group">
                          <div>';

                echo '<input type="file" onchange="this.form.submit();" name="docfile_' . $key . '"> 
                            <label>File: </label><input type="text" name="righi[' . $key . '][extdoc]" value="' . $form['righi'][$key]['extdoc'] . '" >
			</div>
		     </div>
              </div>' . "</td>\n";
            echo "<td><input type=\"text\" name=\"righi[{$key}][descri]\" value=\"$descrizione\" maxlength=\"50\" size=\"50\" /></td>\n";
            echo '<td>
						<input class="gazie-tooltip" data-type="weight" data-id="' . $peso . '" data-title="' . $script_transl['weight'] . '" type="text" name="righi[' . $key . '][unimis]" value="' . $value['unimis'] . '" maxlength="3" size="1" />
					  </td>
					  <td>
						<input class="gazie-tooltip" data-type="weight" data-id="' . $peso . '" data-title="' . $script_transl['weight'] . '" type="text" name="righi[' . $key . '][quanti]" value="' . $value['quanti'] . '" align="right" maxlength="11" size="4" onchange="this.form.submit();" />
					  </td>';
            /** ENRICO FEDELE */
            echo "<td><input type=\"text\" name=\"righi[{$key}][prelis]\" value=\"{$value['prelis']}\" align=\"right\" maxlength=\"11\" size=\"7\" onchange=\"this.form.submit()\" /></td>\n";
            echo "<td><input type=\"text\" name=\"righi[{$key}][sconto]\" value=\"{$value['sconto']}\" maxlength=\"4\" size=\"1\" onchange=\"this.form.submit()\" /></td>\n";
            echo "<td class=\"text-right\">" . gaz_format_number($imprig) . "</td>\n";
            echo "<td>{$value['pervat']}%</td>\n";
            echo "<td>" . $value['codric'] . "</td>\n";
            $last_row[] = array_unshift($last_row, $script_transl['typerow'][$value['tiprig']]);
            break;
        case "51":
			echo "<td><button type=\"image\" name=\"upper_row[" . $key . "]\" class=\"btn btn-default btn-sm\" title=\"" . $script_transl['3'] . "!\"><i class=\"glyphicon glyphicon-arrow-up\"></i></button></td>";
            echo "<td title=\"" . $script_transl['update'] . $script_transl['thisrow'] . "!\"><input class=\"FacetDataTDsmall\" type=\"submit\" name=\"upd_row[{$key}]\" value=\"* documento allegato *\" /></td>\n";
                echo '<td>';
                if (empty($form['righi'][$key]['extdoc'])) {
                    echo '<div><button class="btn btn-xs btn-danger" type="image" data-toggle="collapse" href="#extdoc_dialog' . $key . '">'
                    . $script_transl['insert'] . ' documento esterno <i class="glyphicon glyphicon-tag"></i>'
                    . '</button></div>';
                } else {
                    echo '<div>documento esterno:<button class="btn btn-xs btn-success" type="image" data-toggle="collapse" href="#extdoc_dialog' . $key . '">'
                    . $form['righi'][$key]['extdoc'] . ' <i class="glyphicon glyphicon-tag"></i>'
                    . '</button></div>';
                }
				echo '<div id="extdoc_dialog' . $key . '" class="collapse" >
                        <div class="form-group">
                          <div>';

                echo '<input type="file" onchange="this.form.submit();" name="docfile_' . $key . '"> 
                            <label>File: </label><input type="text" name="righi[' . $key . '][extdoc]" value="' . $form['righi'][$key]['extdoc'] . '" >
			</div>
		     </div>
              </div>' . "</td>\n";
            echo "<td><input type=\"text\"   name=\"righi[{$key}][descri]\" value=\"$descrizione\" maxlength=\"50\" size=\"50\" /></td>\n";
            echo "<td><input type=\"hidden\" name=\"righi[{$key}][unimis]\" value=\"\" /></td>\n";
            echo "<td><input type=\"hidden\" name=\"righi[{$key}][quanti]\" value=\"\" /></td>\n";
            echo "<td><input type=\"hidden\" name=\"righi[{$key}][prelis]\" value=\"\" /></td>\n";
            echo "<td><input type=\"hidden\" name=\"righi[{$key}][sconto]\" value=\"\" /></td>\n";
            echo "<td></td>\n";
            echo "<td></td>\n";
            echo "<td></td>\n";
            $last_row[] = array_unshift($last_row, $script_transl['typerow'][$value['tiprig']]);
            break;
    }
    echo '  <td class="FacetColumnTD" align="right">
			  <button type="submit" class="btn btn-default btn-sm" name="del[' . $key . ']" title="' . $script_transl['delete'] . $script_transl['thisrow'] . '!"><i class="glyphicon glyphicon-remove"></i></button>
			</td>
		  </tr>';
	$ctrl_orderman=$value['id_orderman'];
}

/** ENRICO FEDELE */
/* Nuovo alert per scontistica, da visualizzare rigorosamente dopo l'ultima riga inserita */
if (count($form['righi']) > 0) {

    require("../../modules/magazz/lib.function.php");
    $upd_mm = new magazzForm;

    if (isset($_POST['in_submit']) && count($form['righi']) > 5) {
        /* for($i=0;$i<3;$i++) {	//	Predisposizione per mostrare gli ultimi n articoli inseriti (in ordine inverso ovviamente)
          $msgtoast .= $last_row[$i].'<br />';
          } */
        //$msgtoast .= $last_row[0];
        $msgtoast = $upd_mm->toast($script_transl['last_row'] . ': ' . $last_row[0], 'alert-last-row', 'alert-success');  //lo mostriamo
    }
} else {
    echo '<tr id="alert-zerorows">
			<td colspan="12" class="alert alert-danger">' . $script_transl['zero_rows'] . '</td>
		  </tr>';
}
echo '	</tbody>
	  </table>';
echo "<div class=\"FacetSeparatorTD\" align=\"center\">$script_transl[2]</div>
		<table class=\"Tlarge table table-striped table-bordered table-condensed table-responsive\">
			<input type=\"hidden\" value=\"{$form['speban']}\" name=\"speban\" />
			<input type=\"hidden\" value=\"{$form['numrat']}\" name=\"numrat\" />
			<input type=\"hidden\" value=\"{$form['spevar']}\" name=\"spevar\" />
			<input type=\"hidden\" value=\"{$form['cauven']}\" name=\"cauven\" />
			<input type=\"hidden\" value=\"{$form['caucon']}\" name=\"caucon\" />
			<input type=\"hidden\" value=\"{$form['caumag']}\" name=\"caumag\" />
			<input type=\"hidden\" value=\"{$form['id_agente']}\" name=\"id_agente\" />
			<input type=\"hidden\" value=\"{$form['id_parent_doc']}\" name=\"id_parent_doc\" />
			<tr>
				<td class=\"FacetFieldCaptionTD\">$script_transl[27]</td>
				<td class=\"FacetDataTD\">
					<input type=\"text\" name=\"spediz\" value=\"" . $form["spediz"] . "\" maxlength=\"50\" size=\"25\" class=\"FacetInput\" />\n";
$select_spediz = new SelectValue("spedizione");
$select_spediz->output('spediz', 'spediz');
echo "		</td>
  				<td class=\"FacetFieldCaptionTD\">$script_transl[29]</td>
				<td colspan=\"2\" class=\"FacetDataTD\">
					<input type=\"text\" name=\"portos\" value=\"" . $form["portos"] . "\" maxlength=\"50\" size=\"25\" class=\"FacetInput\" />\n";
$select_spediz = new SelectValue("portoresa");
$select_spediz->output('portos', 'portos');
echo "		</td>
				<td class=\"FacetFieldCaptionTD\">" . $script_transl[51] . "</td>
  				<td colspan=\"2\" class=\"FacetDataTD\">
					<select name=\"caumag\" class=\"FacetSelect\">\n";
$result = gaz_dbi_dyn_query("*", $gTables['caumag'], " clifor = 1 AND operat = " . $TipoDocumento[$form['tipdoc']], "codice asc, descri asc");
while ($row = gaz_dbi_fetch_array($result)) {
    $selected = "";
    if ($form["caumag"] == $row['codice']) {
        $selected = ' selected=""';
    }
    echo "				<option value=\"" . $row['codice'] . "\"" . $selected . ">" . $row['codice'] . "-" . substr($row['descri'], 0, 20) . "</option>\n";
}
echo "			</select>
  				</td>
			</tr>
			<tr>
				<td class=\"FacetFieldCaptionTD text-right\">$script_transl[32]</td>
				<td class=\"FacetFieldCaptionTD text-right\">$script_transl[33]</td>
				<td class=\"FacetFieldCaptionTD text-right\">$script_transl[34]</td>
				<td class=\"FacetFieldCaptionTD text-right\">
					%$script_transl[24]<input type=\"text\" name=\"sconto\" value=\"" . $form["sconto"] . "\" maxlength=\"6\" size=\"1\" onchange=\"this.form.submit()\" />
				</td>
				<td class=\"FacetFieldCaptionTD text-right\">$script_transl[32]</td>
				<td class=\"FacetFieldCaptionTD text-right\">$script_transl[19]</td>
				<td class=\"FacetFieldCaptionTD text-right\">$script_transl[35]</td>
				<td class=\"FacetFieldCaptionTD text-right\">$script_transl[36] " . $admin_aziend['symbol'] . "</td>
			 </tr>\n";
foreach ($castel as $key => $value) {
    $result = gaz_dbi_get_row($gTables['aliiva'], "codice", $key);
    $impcast = CalcolaImportoRigo(1, $value, $form['sconto']);
    $ivacast = round($impcast * $result['aliquo']) / 100;
    $totimpmer += $value;
    $totimpfat += $impcast;
    $totivafat += $ivacast;
    if ($next_row > 0) {
        echo "<tr>
				<td class=\"text-right\">" . number_format($impcast, 2, '.', '') . "</td>
				<td class=\"text-right\">" . $result['descri'] . " " . number_format($ivacast, 2, '.', '') . "</td>
				<td colspan=\"6\"></td>
			  </tr>\n";
    }
}

if ($next_row > 0) {
    echo '	<tr>
					<td colspan="2"></td>
					<td class="text-right">' . number_format($totimpmer, 2, '.', '') . '</td>
					<td class="text-right">' . gaz_format_number(($totimpfat - $totimpmer - $form['traspo'] - $form['spevar']), 2, '.', '') . '</td>
					<td class="text-right">' . number_format($totimpfat, 2, '.', '') . '</td>
					<td class="text-right">' . number_format($totivafat, 2, '.', '') . '</td>
					<td class="text-right">' . $quatot . '</td>
					<td class="text-right">' . number_format(($totimpfat + $totivafat), 2, '.', '') . '</td>
				  </tr>';

    if ($toDo == 'update') {
        echo '<tr>
		   			<td colspan="8" class="text-right alert alert-success">
		   				<input type="submit" accesskey="m" name="ins" id="preventDuplicate" onClick="chkSubmit();" value="MODIFICA !" />
					</td>
				 </tr>';
    } else {
        echo '<tr>
		   			<td colspan="8" class="text-right alert alert-success">
		   				<input type="submit" accesskey="i" name="ins" id="preventDuplicate" onClick="chkSubmit();" value="INSERISCI !" />
					</td>
				</tr>';
    }
}
/* l'ho dovuto eliminare perché se si va a modificare un preventivo lo trasforma SEMPRE!?!?! in un ordine !!!!
  if ($toDo == 'update' and $form['tipdoc'] == 'APR') {
  echo '			<tr>
  <td colspan="8" class="text-right alert alert-info">
  <script type="text/javascript">
  $("input[name=tipdoc]").val("AOR");
  </script>
  <input type="submit" accesskey="o" name="ord" value="GENERA ORDINE!" />
  </td>
  </tr>';
  }
  /*
  Per sviluppi futuri: l'idea è quella di permettere il seguente flusso:
  1. richiesta preventivo
  2. accettazione preventivo e dunque conversione dello stesso in ordine (vedi if qui sopra)
  3. alla ricezione del ddt o fattura, conversione del preventivo in acquisto 8e quindi carico in magazzino)
  IL putno 3. è un pò più complesso da realizzare, occorre pensarci su
  elseif($toDo == 'update' and $form['tipdoc'] == 'AOR') {
  echo '			<tr>
  <td colspan="8" class="text-right alert alert-warning">
  <input type="submit" accesskey="o" name="ord" value="GENERA ACQUISTO!" />
  </td>
  </tr>';
  } */
echo '	</table>';
?>
</form>
<!-- ENRICO FEDELE - INIZIO FINESTRA MODALE -->
<div id="edit-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header active">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $script_transl['add_article']; ?></h4>
            </div>
            <div class="modal-body edit-content small"></div>
            <!--<div class="modal-footer"></div>-->
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        //twitter bootstrap script
        $("#addmodal").click(function () {
            $.ajax({
                type: "POST",
                url: "../../modules/magazz/admin_artico.php",
                data: 'mode=modal',
                success: function (msg) {
                    $("#edit-modal .modal-sm").css('width', '100%');
                    $("#edit-modal .modal-body").html(msg);
                },
                error: function () {
                    alert("failure");
                }
            });
        });
    });
	// calcolo peso da dimensioni
	function weightfromdim(row) {
		var descri = $("[name='righi["+row+"][descri]']").val();
		var larghezza = $("[name='righi["+row+"][larghezza]']").val();
		var lunghezza = $("[name='righi["+row+"][lunghezza]']").val();
		var spessore = $("[name='righi["+row+"][spessore]']").val();
		var peso_specifico = $("[name='righi["+row+"][peso_specifico]']").val();
		var pezzi = $("[name='righi["+row+"][pezzi]']").val();
		$("#dialog_row_focus").val(row);
		$("#dialog_larghezza").val(larghezza);
		$("#dialog_lunghezza").val(lunghezza);
		$("#dialog_spessore").val(spessore);
		$("#dialog_peso_specifico").val(peso_specifico);
		$("#dialog_pezzi").val(pezzi);
		$("#weight-from-dim").prop('title', descri+' - CALCOLO DEL PESO');
		weightfromdimCalc();
		$("#weight-from-dim").dialog({
			width: 500,
			position: {
				my: "bottom-30",
				at: "center"
				},
			modal: true
		});
	};
	
	function weightfromdimCalc() {
		/* 
		ANTONIO DE VICENTIIS
		Non ho guardato in giro sulla rete per vedere se qualcuno lo ha fatto prima di me, ma questo 
		metodo - interfaccia per il calcolo delle dimensioni e del peso sarebbe meritevole di brevetto :)))))
		*/
		var larghezza = ($("#dialog_larghezza").val()).replace(',', '.');
		var lunghezza = ($("#dialog_lunghezza").val()).replace(',', '.');
		var spessore = ($("#dialog_spessore").val()).replace(',', '.');
		var peso_specifico = ($("#dialog_peso_specifico").val()).replace(',', '.');
		var pezzi = ($("#dialog_pezzi").val()).replace(',', '.');
		var res_ps=''; var res_a=''; var res_b=''; var res_c=''; var res_d=''; var res_kg='';
		if (parseFloat(pezzi)>=0.001) {
			res_ps='kg/pz';
			res_a = parseFloat(pezzi).toFixed(3).toString();
			res_kg = (parseFloat(pezzi)*parseFloat(peso_specifico)).toFixed(3).toString();
			if (parseFloat(lunghezza)>=0.001) {
				res_ps='kg/m';
				res_b = (parseFloat(lunghezza)/10**3*parseFloat(pezzi)).toFixed(3).toString();
				res_kg = (res_b*parseFloat(peso_specifico)).toFixed(3).toString();
				res_c = ''; res_d = '';
				$("#btn_ml").text('m '+ res_b);
				if (parseFloat(larghezza)>=0.001) {
					res_ps='kg/m²';
					res_c = (parseFloat(larghezza)*res_b/10**3).toFixed(3).toString();
					res_kg = (res_c*parseFloat(peso_specifico)).toFixed(3).toString();
					res_d = '';
					$("#btn_mq").text('m² '+ res_c);
					if (parseFloat(spessore)>=0.001) {
						res_ps='kg/l';
						res_d = res_c*parseFloat(spessore).toFixed(3).toString();
						res_kg = (res_d*parseFloat(peso_specifico)).toFixed(3).toString();
						$("#btn_lt").text('l '+ res_d);
					} else {
						$("#btn_lt").text('l ');
					}
				} else {
					$("#btn_mq").text('m² ');
				}
			} else {
				$("#btn_ml").text('m ');
			}
			if (parseFloat(res_kg)>=0.001){
				$("#btn_kg").text('kg '+ res_kg);
			}
		} else {
			res_a=''; res_b=''; res_c=''; res_d=''; res_kg='';
			$("#btn_kg").text('kg ');
		}
		$("#res_ps").text('Peso specifico '+res_ps);

	}

	function weightfromdimSet(mu) {
		var row=$("#dialog_row_focus").val();
		var res_ps=''; var res_a=''; var res_b=''; var res_c=''; var res_d=''; var res_kg='';
		var larghezza = $("#dialog_larghezza").val();
		var lunghezza = $("#dialog_lunghezza").val();
		var spessore = $("#dialog_spessore").val();
		var peso_specifico = $("#dialog_peso_specifico").val();
		var pezzi = $("#dialog_pezzi").val();
		$("[name='righi["+row+"][larghezza]']").val(larghezza);
		$("[name='righi["+row+"][lunghezza]']").val(lunghezza);
		$("[name='righi["+row+"][spessore]']").val(spessore);
		$("[name='righi["+row+"][peso_specifico]']").val(peso_specifico);
		$("[name='righi["+row+"][pezzi]']").val(pezzi);
		if (parseFloat(pezzi)>=0.001) {
			res_a = parseFloat(pezzi).toFixed(3).toString();
			res_kg = (parseFloat(pezzi)*parseFloat(peso_specifico)).toFixed(3).toString();
			res_b = ''; res_c = ''; res_d = '';
			if (parseFloat(lunghezza)>=0.001) {
				res_b = (parseFloat(lunghezza)/10**3*parseFloat(pezzi)).toFixed(3).toString();
				res_kg = (res_b*parseFloat(peso_specifico)).toFixed(3).toString();
				res_c = ''; res_d = '';
				if (parseFloat(larghezza)>=0.001) {
					res_c = (parseFloat(larghezza)*res_b/10**3).toFixed(3).toString();
					res_kg = (res_c*parseFloat(peso_specifico)).toFixed(3).toString();
					res_d = '';
					if (parseFloat(spessore)>=0.001) {
						res_d = res_c*parseFloat(spessore).toFixed(3).toString();
						res_kg = (res_d*parseFloat(peso_specifico)).toFixed(3).toString();
					}
				}
			}
		} else {
			res_a=''; res_b=''; res_c=''; res_d=''; res_kg='';
		}
		var close_dial=false;
		if (mu=='kg' && res_kg>=0.00001){
			$("[name='righi["+row+"][unimis]']").val('KG');
			$("[name='righi["+row+"][quanti]']").val(res_kg);
			close_dial=true;
		} else if (mu=='ml' && res_a>=1) {
			$("[name='righi["+row+"][unimis]']").val('ML');
			$("[name='righi["+row+"][quanti]']").val(res_b);
			close_dial=true;
		} else if (mu=='mq' && res_b>=1) {
			$("[name='righi["+row+"][unimis]']").val('MQ');
			$("[name='righi["+row+"][quanti]']").val(res_c);
			close_dial=true;
		} else if (mu=='lt' && res_c>=1) {
			$("[name='righi["+row+"][unimis]']").val('LT');
			$("[name='righi["+row+"][quanti]']").val(res_d);
			close_dial=true;
		} else if (mu=='pz' && pezzi>=1) {
			$("[name='righi["+row+"][unimis]']").val('PZ');
			$("[name='righi["+row+"][quanti]']").val(pezzi);
			close_dial=true;
		}
		if (close_dial){
			$("#dialog_row_focus").val('');
			$("#weight-from-dim").dialog('close');
		}
	}	
	
	var last_focus_value;
	var last_focus;
	last_focus_value = document.docacq.last_focus.value;
	if (last_focus_value != "") {
		last_focus = document.getElementById(last_focus_value);
		if (last_focus != undefined) {
			last_focus.focus();
		}
	}
	last_focus_value = "";	
</script>
<!-- ENRICO FEDELE - FINE FINESTRA MODALE -->
<div class="modal" id="weight-from-dim" TITLE='CALC'>
<div class="col-lg-12"  style="margin-bottom: 10px; background-color: #92a8d1;">
	<div class="col-lg-4">GRANDEZZA</div>	
	<div class="col-lg-3">VALORE</div>
	<div class="col-lg-5 text-right">INSERISCI</div>
</div>
<div class="col-lg-12">
	<div class="col-lg-4">Pezzi: </div>	
	<div class="col-lg-3"><input type="number" min="0" id="dialog_pezzi" tabindex="100" maxlength="11" onkeyup="weightfromdimCalc();" /></div>
	<div class="col-lg-5 text-right"><button style="margin-bottom: 10px;"  id="btn_pz" onclick="weightfromdimSet('pz');" /> pz </button></div>
</div>
<div class="col-lg-12">
	<div class="col-lg-4">Lunghezza mm:</div>
	<div class="col-lg-3"><input type="number" min="0" id="dialog_lunghezza" tabindex="102" maxlength="11" onkeyup="weightfromdimCalc();" /></div>
	<div class="col-lg-5 text-right"><button style="margin-bottom: 10px;"  id="btn_kg" onclick="weightfromdimSet('kg');" /> kg </button></div>
</div>
<div class="col-lg-12">
	<div class="col-lg-4">Larghezza mm:</div>
	<div class="col-lg-3"><input type="number" min="0" id="dialog_larghezza" tabindex="103" maxlength="11" onkeyup="weightfromdimCalc();" /></div>
	<div class="col-lg-5 text-right"><button style="margin-bottom: 10px"  id="btn_ml" onclick="weightfromdimSet('ml');" /> m </button></div>
</div>
<div class="col-lg-12">
	<div class="col-lg-4">Spessore mm:</div>
	<div class="col-lg-3"><input type="number" step="0.01" min="0" id="dialog_spessore" tabindex="104" maxlength="11" onkeyup="weightfromdimCalc();" /></div>
	<div class="col-lg-5 text-right"><button style="margin-bottom: 10px" id="btn_mq" onclick="weightfromdimSet('mq');" /> m² </button></div>
</div>
<div class="col-lg-12">
	<div class="col-lg-4" id="res_ps"></div>
	<div class="col-lg-3"><input type="number"  step="0.01" min="0" id="dialog_peso_specifico" tabindex="105" maxlength="11" onkeyup="weightfromdimCalc();" />	</div>
	<div class="col-lg-5 text-right"><button id="btn_lt" onclick="weightfromdimSet('lt');" /> l </button></div>
</div>
</div>

<?php
require("../../library/include/footer.php");
?>