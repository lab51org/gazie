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
require("../../modules/magazz/lib.function.php");
$admin_aziend = checkAdmin();
$msg = array('err' => array(), 'war' => array());
$anagrafica = new Anagrafica();
$gForm = new acquisForm();
$magazz = new magazzForm;
$docOperat = $magazz->getOperators();

function get_tmp_doc($i) {
    global $admin_aziend;
    return true;
}

if (isset($_POST['newdestin'])) {
    $_POST['id_des'] = 0;
    $_POST['id_des_same_company'] = 0;
    $_POST['destin'] = "";
}

if (!isset($_POST['ritorno'])) {
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
} else {
    $form['ritorno'] = $_POST['ritorno'];
}

if ((isset($_GET['Update']) and ! isset($_GET['id_tes'])) and ! isset($_GET['tipdoc'])) {
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
    $form['id_tes'] = intval($_POST['id_tes']);
    $anagrafica = new Anagrafica();
    $fornitore = $anagrafica->getPartner(intval($_POST['clfoco']));
    $form['hidden_req'] = $_POST['hidden_req'];
// ...e della testata
    foreach ($_POST['search'] as $k => $v) {
        $form['search'][$k] = $v;
    }
    $form['cosear'] = $_POST['cosear'];
    $form['seziva'] = $_POST['seziva'];
    $form['id_con'] = intval($_POST['id_con']);
    $form['tipdoc'] = $_POST['tipdoc'];
    $form['giotra'] = $_POST['giotra'];
    $form['mestra'] = $_POST['mestra'];
    $form['anntra'] = $_POST['anntra'];
    $form['oratra'] = $_POST['oratra'];
    $form['mintra'] = $_POST['mintra'];
	$form['datreg'] = substr($_POST['datreg'],0,10);
	$form['datfat'] = substr($_POST['datfat'],0,10);
    $form['datemi'] = substr($_POST['datemi'],0,10);
    $form['protoc'] = intval($_POST['protoc']);
    $form['numdoc'] = $_POST['numdoc'];
    $form['numfat'] = $_POST['numfat'];
	if ($form['tipdoc']=='AFA' || $form['tipdoc']=='AFC'){ // sulle fatture-n.c. forzo datemi e numdoc agli stessi valori di datfat e numfat
	    $form['datemi'] = $form['datfat'];
	    $form['numdoc'] = $form['numfat'];
	}
    $form['clfoco'] = $_POST['clfoco'];
    $form['address'] = $_POST['address'];
//tutti i controlli su  tipo di pagamento e rate
    $form['speban'] = $_POST['speban'];
    $form['numrat'] = $_POST['numrat'];
    $form['pagame'] = $_POST['pagame'];
    $form['change_pag'] = $_POST['change_pag'];
    if ($form['change_pag'] != $form['pagame']) {  //se è stato cambiato il pagamento
        $new_pag = gaz_dbi_get_row($gTables['pagame'], "codice", $form['pagame']);
        $old_pag = gaz_dbi_get_row($gTables['pagame'], "codice", $form['change_pag']);
        if (($new_pag['tippag'] == 'B' or $new_pag['tippag'] == 'T' or $new_pag['tippag'] == 'V')
                and ( $old_pag['tippag'] == 'C' or $old_pag['tippag'] == 'D')) { // se adesso devo mettere le spese e prima no
            $form['numrat'] = $new_pag['numrat'];
            if ($toDo == 'update') {  //se è una modifica mi baso sulle vecchie spese
                $old_header = gaz_dbi_get_row($gTables['tesdoc'], "id_tes", $form['id_tes']);
                if ($old_header['speban'] > 0 and $fornitore['speban'] == "S") {
                    $form['speban'] = 0;
                } elseif ($old_header['speban'] == 0 and $fornitore['speban'] == "S") {
                    $form['speban'] = 0;
                } else {
                    $form['speban'] = 0.00;
                }
            } else { //altrimenti mi avvalgo delle nuove dell'azienda
                $form['speban'] = 0;
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
    $form['vettor'] = $_POST['vettor'];
    $form['listin'] = $_POST['listin'];
    $form['spediz'] = $_POST['spediz'];
    $form['portos'] = $_POST['portos'];
    $form['imball'] = $_POST['imball'];
    $form['destin'] = $_POST['destin'];
    $form['id_des'] = substr($_POST['id_des'], 3);
    $form['id_des_same_company'] = intval($_POST['id_des_same_company']);

    /** inizio modifica FP 09/01/2016
     * modifica piede DDT
     */
    $form['net_weight'] = floatval($_POST['net_weight']);
    $form['gross_weight'] = floatval($_POST['gross_weight']);
    $form['units'] = intval($_POST['units']);
    $form['volume'] = floatval($_POST['volume']);
    $strArrayDest = $_POST['rs_destinazioni'];
    $array_destinazioni = unserialize(base64_decode($strArrayDest)); // recupero l'array delle destinazioni
    /** fine modifica FP */
    $form['traspo'] = $_POST['traspo'];
    $form['spevar'] = $_POST['spevar'];
    $form['ivaspe'] = $_POST['ivaspe'];
    $form['pervat'] = $_POST['pervat'];
    $form['cauven'] = $_POST['cauven'];
    $form['caucon'] = $_POST['caucon'];
    $form['caumag'] = $_POST['caumag'];
    $form['caucon'] = $_POST['caucon'];
    $form['id_parent_doc'] = $_POST['id_parent_doc'];
    $form['sconto'] = $_POST['sconto'];
// inizio rigo di input
    $form['in_descri'] = $_POST['in_descri'];
    $form['in_tiprig'] = $_POST['in_tiprig'];
    /*    $form['in_artsea'] = $_POST['in_artsea']; Non serve più */
    $form['in_codart'] = $_POST['in_codart'];
    $form['in_codice_fornitore'] = $_POST['in_codice_fornitore'];
    $form['in_pervat'] = $_POST['in_pervat'];
    $form['in_ritenuta'] = $_POST['in_ritenuta'];
    $form['in_unimis'] = $_POST['in_unimis'];
    $form['in_prelis'] = $_POST['in_prelis'];
    $form['in_sconto'] = $_POST['in_sconto'];
    $form['in_quanti'] = floatval($_POST['in_quanti']);
    $form['in_codvat'] = $_POST['in_codvat'];
    $form['in_codric'] = $_POST['in_codric'];
    $form['in_id_mag'] = $_POST['in_id_mag'];
    $form['in_id_orderman'] = $_POST['in_id_orderman'];
    $form['in_annota'] = $_POST['in_annota'];
    $form['in_pesosp'] = $_POST['in_pesosp'];
    $form['in_gooser'] = intval($_POST['in_gooser']);
    $form['in_quamag'] = $_POST['in_quamag'];
    $form['in_scorta'] = intval($_POST['in_scorta']);
    $form['in_lot_or_serial'] = intval($_POST['in_lot_or_serial']);
    $form['in_status'] = $_POST['in_status'];
// fine rigo input
    $form['rows'] = array();
    $i = 0;
    if (isset($_POST['rows'])) {
        foreach ($_POST['rows'] as $i => $value) {
            if (isset($_POST["row_$i"])) { //se ho un rigo testo
                $form["row_$i"] = $_POST["row_$i"];
            }
            $form['rows'][$i]['descri'] = substr($value['descri'], 0, 100);
            $form['rows'][$i]['tiprig'] = intval($value['tiprig']);
            $form['rows'][$i]['codart'] = substr($value['codart'], 0, 15);
            $form['rows'][$i]['codice_fornitore'] = substr($value['codice_fornitore'], 0, 50);	// Aggiunto a Mano 
            $form['rows'][$i]['pervat'] = preg_replace("/\,/", '.', $value['pervat']);
            $form['rows'][$i]['ritenuta'] = preg_replace("/\,/", '.', $value['ritenuta']);
            $form['rows'][$i]['unimis'] = substr($value['unimis'], 0, 3);
            $form['rows'][$i]['prelis'] = floatval(preg_replace("/\,/", '.', $value['prelis']));
            $form['rows'][$i]['sconto'] = floatval(preg_replace("/\,/", '.', $value['sconto']));
            $form['rows'][$i]['quanti'] = gaz_format_quantity($value['quanti'], 0, $admin_aziend['decimal_quantity']);
            $form['rows'][$i]['codvat'] = intval($value['codvat']);
            $form['rows'][$i]['codric'] = intval($value['codric']);
            $form['rows'][$i]['id_mag'] = intval($value['id_mag']);
            $form['rows'][$i]['id_orderman'] = intval($value['id_orderman']);
            $form['rows'][$i]['annota'] = substr($value['annota'], 0, 50);
            $form['rows'][$i]['pesosp'] = floatval($value['pesosp']);
            $form['rows'][$i]['gooser'] = intval($value['gooser']);
            $form['rows'][$i]['quamag'] = floatval($value['quamag']);
            $form['rows'][$i]['scorta'] = floatval($value['scorta']);
            $form['rows'][$i]['lot_or_serial'] = intval($value['lot_or_serial']);
            if ($value['lot_or_serial'] == 2) {
// se è prevista la gestione per numero seriale/matricola la quantità non può essere diversa da 1 
                if ($form['rows'][$i]['quanti'] <> 1) {
                    $msg['err'][] = "forceone";
                }
                $form['rows'][$i]['quanti'] = 1;
            }
            $form['rows'][$i]['identifier'] = (empty($_POST['rows'][$i]['identifier'])) ? '' : filter_var($_POST['rows'][$i]['identifier'], FILTER_SANITIZE_STRING);
            $form['rows'][$i]['expiry'] = (empty($_POST['rows'][$i]['expiry'])) ? '' : filter_var($_POST['rows'][$i]['expiry'], FILTER_SANITIZE_STRING);
            $form['rows'][$i]['filename'] = filter_var($_POST['rows'][$i]['filename'], FILTER_SANITIZE_STRING);
            if (!empty($_FILES['docfile_' . $i]['name'])) {
                $move = false;
                $mt = substr($_FILES['docfile_' . $i]['name'], -3);
                $prefix = $admin_aziend['adminid'] . '_' . $admin_aziend['company_id'] . '_' . $i;
                if (($mt == "png" || $mt == "peg" || $mt == "jpg" || $mt == "pdf") && $_FILES['docfile_' . $i]['size'] > 1000) { //se c'e' una nuova immagine nel buffer
                    foreach (glob("../../data/files/tmp/" . $prefix . "_*.*") as $fn) {// prima cancello eventuali precedenti file temporanei
                        unlink($fn);
                    }
                    $move = move_uploaded_file($_FILES['docfile_' . $i]['tmp_name'], '../../data/files/tmp/' . $prefix . '_' . $_FILES['docfile_' . $i]['name']);
                    $form['rows'][$i]['filename'] = $_FILES['docfile_' . $i]['name'];
                }
                if (!$move) {
                    $msg['err'][] = "notrack";
                }
            }
            $form['rows'][$i]['status'] = substr($value['status'], 0, 10);

            if (isset($_POST['upd_row'])) {
                $key_row = key($_POST['upd_row']);
                if ($key_row == $i) {
                    $form['in_descri'] = $form['rows'][$key_row]['descri'];
                    $form['in_tiprig'] = $form['rows'][$key_row]['tiprig'];
                    $form['in_codart'] = $form['rows'][$key_row]['codart'];
                    $form['in_pervat'] = $form['rows'][$key_row]['pervat'];
                    $form['in_ritenuta'] = $form['rows'][$key_row]['ritenuta'];
                    $form['in_unimis'] = $form['rows'][$key_row]['unimis'];
                    $form['in_prelis'] = $form['rows'][$key_row]['prelis'];
                    $form['in_sconto'] = $form['rows'][$key_row]['sconto'];
                    $form['in_quanti'] = $form['rows'][$key_row]['quanti'];
                    $form['in_codvat'] = $form['rows'][$key_row]['codvat'];
                    $form['in_codric'] = $form['rows'][$key_row]['codric'];
                    $form['in_id_mag'] = $form['rows'][$key_row]['id_mag'];
					$orderman = gaz_dbi_get_row($gTables['orderman'], "id", $form['rows'][$key_row]['id_orderman']);
                    $form['coseprod'] = $orderman['description'];
                    $form['in_id_orderman'] = $form['rows'][$key_row]['id_orderman'];
                    $form['in_annota'] = $form['rows'][$key_row]['annota'];
                    $form['in_pesosp'] = $form['rows'][$key_row]['pesosp'];
                    $form['in_gooser'] = $form['rows'][$key_row]['gooser'];
                    $form['in_scorta'] = $form['rows'][$key_row]['scorta'];
                    $form['in_quamag'] = $form['rows'][$key_row]['quamag'];
                    $form['in_lot_or_serial'] = $form['rows'][$key_row]['lot_or_serial'];
                    $form['in_status'] = "UPDROW" . $key_row;

                    /** inizio modifica FP 09/01/2016
                     * descrizione modificabile
                     */
// sottrazione ai totali peso,pezzi,volume
                    $artico = gaz_dbi_get_row($gTables['artico'], "codice", $form['rows'][$key_row]['codart']);
                    $form['net_weight'] -= $form['rows'][$key_row]['quanti'] * $artico['peso_specifico'];
                    $form['gross_weight'] -= $form['rows'][$key_row]['quanti'] * $artico['peso_specifico'];
                    if ($artico['pack_units'] > 0) {
                        $form['units'] -= intval(round($form['rows'][$key_row]['quanti'] / $artico['pack_units']));
                    }
                    $form['volume'] -= $form['rows'][$key_row]['quanti'] * $artico['volume_specifico'];
                    $form['cosear'] = $form['rows'][$key_row]['codart'];
                    array_splice($form['rows'], $key_row, 1);
                    $i--;
                }
            } elseif ($_POST['hidden_req'] == 'ROW') {
                if (!empty($form['hidden_req'])) { // al primo ciclo azzero ma ripristino il lordo
                    $form['gross_weight'] -= $form['net_weight'];
                    $form['net_weight'] = 0;
                    $form['units'] = 0;
                    $form['volume'] = 0;
                    $form['hidden_req'] = '';
                }
                $artico = gaz_dbi_get_row($gTables['artico'], "codice", $form['rows'][$next_row]['codart']);
                $form['net_weight'] += $form['rows'][$next_row]['quanti'] * $artico['peso_specifico'];
                $form['gross_weight'] += $form['rows'][$next_row]['quanti'] * $artico['peso_specifico'];
                if ($artico['pack_units'] > 0) {
                    $form['units'] += intval(round($form['rows'][$next_row]['quanti'] / $artico['pack_units']));
                }
                $form['volume'] += $form['rows'][$next_row]['quanti'] * $artico['volume_specifico'];
            }
            $i++;
        }
    }
// Se viene inviata la richiesta di conferma totale ...
    if (isset($_POST['ins'])) {
        $sezione = $form['seziva'];
        $datemi = gaz_format_date($form['datemi'],true);
        $utsemi = mktime(0, 0, 0, substr($form['datemi'],3,2), substr($form['datemi'],0,2), substr($form['datemi'],6,4) );
        $utsreg = mktime(0, 0, 0, substr($form['datreg'],3,2), substr($form['datreg'],0,2), substr($form['datreg'],6,4) );
        $initra = $form['anntra'] . "-" . $form['mestra'] . "-" . $form['giotra'];
        $utstra = mktime(0, 0, 0, $form['mestra'], $form['giotra'], $form['anntra']);
        if ($form['tipdoc'] == 'DDR' || $form['tipdoc'] == 'DDL' || $form['tipdoc'] == 'RDL') {  //se è un DDT vs Fattura differita
            if ($utstra < $utsemi) {
               $msg['err'][] = "dtintr";
            }
            if (!checkdate($form['mestra'], $form['giotra'], $form['anntra'])) {
               $msg['err'][] = "dttrno";
            }
        } elseif ($form['tipdoc'] == 'ADT') { // è un ddt ricevuto da fornitore non effettuo controlli su date e numeri
        } else {
			$utsfat = mktime(0, 0, 0, substr($form['datfat'],3,2), substr($form['datfat'],0,2), substr($form['datfat'],6,4));
            if ($utsfat > $utsreg) {
               $msg['err'][] = "dregpr";
            }
            if (empty($form['numfat'])) {
               $msg['err'][] = "nonudo";
            }
        }
        if (!isset($_POST['rows'])) {
            $msg['err'][] = "norows";
        }
// --- inizio controllo coerenza date-numerazione
        if ($toDo == 'update') {  // controlli in caso di modifica
            if ($form['tipdoc'] == 'DDR' or $form['tipdoc'] == 'DDL') {  //se è un DDL o DDR
                $rs_query = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = " . substr($datemi,0,4) . " and datemi < '$datemi' and ( tipdoc like 'DD_' or tipdoc = 'FAD') and seziva = $sezione", "numdoc desc", 0, 1);
                $result = gaz_dbi_fetch_array($rs_query); //giorni precedenti
                if ($result and ( $form['numdoc'] < $result['numdoc'])) {
                    $msg['err'][]= "dtnuan";
                }
                $rs_query = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = " . substr($datemi,0,4) . " and datemi > '$datemi' and ( tipdoc like 'DD_' or tipdoc = 'FAD') and seziva = $sezione", "numdoc asc", 0, 1);
                $result = gaz_dbi_fetch_array($rs_query); //giorni successivi
                if ($result and ( $form['numdoc'] > $result['numdoc'])) {
                    $msg['err'][]= "dtnusc";
                }
            } elseif ($form['tipdoc'] == 'ADT') { //se è un DDT acquisto non faccio controlli
            } else { //se sono altri documenti - AFA AFC
                $rs_query = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datreg) = " . substr($form['datreg'],-4) . " AND datreg < '".gaz_format_date($form['datreg'],true)."' AND tipdoc LIKE '" . substr($form['tipdoc'], 0, 1) . "__' and seziva = ".$sezione, "protoc desc", 0, 1);
                $result = gaz_dbi_fetch_array($rs_query); //giorni precedenti
                if ($result && ($form['protoc'] < $result['protoc'])) {
                    $msg['err'][] = "dtante";
                }
                $rs_query = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datreg) = " . substr($form['datreg'],-4) . " AND datreg > '".gaz_format_date($form['datreg'],true)."' AND tipdoc LIKE '" . substr($form['tipdoc'], 0, 1) . "__' AND seziva = ".$sezione, "protoc asc", 0, 1);
                $result = gaz_dbi_fetch_array($rs_query); //giorni successivi
                if ($result && ($form['protoc'] > $result['protoc'])) {
                    $msg['err'][] = "dtsucc";
                }
            }
        } else {    //controlli in caso di inserimento
            if ($form['tipdoc'] == 'DDR' or $form['tipdoc'] == 'DDL') {  //se è un DDT
                $rs_ultimo_ddt = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = " . substr($datemi,0,4) . " and tipdoc like 'DD_' and seziva = $sezione", "numdoc desc, datemi desc", 0, 1);
                $ultimo_ddt = gaz_dbi_fetch_array($rs_ultimo_ddt);
                $utsUltimoDdT = mktime(0, 0, 0, substr($ultimo_ddt['datemi'], 5, 2), substr($ultimo_ddt['datemi'], 8, 2), substr($ultimo_ddt['datemi'], 0, 4));
                if ($ultimo_ddt and ( $utsUltimoDdT > $utsemi)) {
                    $msg['err'][] = "ddtpre";
                }
            } elseif ($form['tipdoc'] == 'ADT') {  //se è un DDT d'acquisto non effettuo controlli sulle date
            } else { //se sono altri documenti AFA AFC
                $rs_ultimo_tipo = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datreg) = " . substr($form['datreg'],-4) . " AND tipdoc LIKE '" . substr($form['tipdoc'], 0, 1) . "%' and seziva = ".$sezione, "protoc desc, datreg desc, datfat desc", 0, 1);
                $ultimo_tipo = gaz_dbi_fetch_array($rs_ultimo_tipo);
				if ($ultimo_tipo){
					$utsUltimoProtocollo = mktime(0, 0, 0, substr($ultimo_tipo['datreg'], 5, 2), substr($ultimo_tipo['datreg'], 8, 2), substr($ultimo_tipo['datreg'], 0, 4));
					if ($utsUltimoProtocollo > $utsreg) {
						$msg['err'][] = "docpre";
					}
                }
            }
			if (!empty($form["clfoco"])) {
				if (!preg_match("/^id_([0-9]+)$/", $form['clfoco'], $match)) {
					//controllo se ci sono altri documenti con lo stesso numero fornitore
					$rs_stesso_numero = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = " .substr($datemi,0,4) . " and tipdoc like '" . substr($form['tipdoc'], 0, 1) . "%' and clfoco = " . $form['clfoco'] . " and numfat = '" . $form['numfat'] . "'", "protoc desc, datfat desc, datemi desc", 0, 1);
					$stesso_numero = gaz_dbi_fetch_array($rs_stesso_numero);
					if ($stesso_numero) {
						$msg['err'][] = "samedoc";
					}
				}
			}
        }
// --- fine controllo coerenza date-numeri
        if (empty($form["clfoco"]))
            $msg['err'][] = "noforn";
        if (empty($form["pagame"]))
            $msg['err'][] = "nopaga";
//controllo che i righi non abbiano descrizioni  e unita' di misura vuote in presenza di quantita diverse da 0
        foreach ($form['rows'] as $i => $value) {
            if ($value['descri'] == '' &&
                    $value['quanti']) {
                $msgrigo = $i + 1;
                $msg['err'][] = "norwde";
            }
            if ($value['unimis'] == '' &&
                    $value['quanti'] &&
                    $value['tiprig'] == 0) {
                $msgrigo = $i + 1;
                $msg['err'][] = "norwum";
            }
        }
        if (count($msg['err']) == 0) {// nessun errore
            if (preg_match("/^id_([0-9]+)$/", $form['clfoco'], $match)) {
                $new_clfoco = $anagrafica->getPartnerData($match[1], 1);
                $form['clfoco'] = $anagrafica->anagra_to_clfoco($new_clfoco, $admin_aziend['masfor'],$form['pagame']);
            }

            function getProtocol($type, $year, $sezione) {  // questa funzione trova l'ultimo numero di protocollo                                           // controllando sia l'archivio documenti che il
                global $gTables;                      // registro IVA acquisti
                $rs_ultimo_tesdoc = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datreg) = $year AND tipdoc LIKE '" . substr($type, 0, 2) . "_' AND seziva = ".$sezione, "protoc DESC", 0, 1);
                $ultimo_tesdoc = gaz_dbi_fetch_array($rs_ultimo_tesdoc);
                $rs_ultimo_tesmov = gaz_dbi_dyn_query("*", $gTables['tesmov'], "YEAR(datreg) = $year AND regiva = 6 AND seziva = $sezione", "protoc DESC", 0, 1);
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

            $initra .= " " . $form['oratra'] . ":" . $form['mintra'] . ":00";
            $form['spediz'] = addslashes($form['spediz']);
            $form['portos'] = addslashes($form['portos']);
            $form['imball'] = addslashes($form['imball']);
            $form['destin'] = addslashes($form['destin']);
			$form['datreg'] = gaz_format_date($form['datreg'],true);
			$form['datfat'] = gaz_format_date($form['datfat'],true);
            if ($toDo == 'update') { // e' una modifica
                $old_rows = gaz_dbi_dyn_query("*", $gTables['rigdoc'], "id_tes = " . $form['id_tes'], "id_rig asc");
                $i = 0;
                $count = count($form['rows']) - 1;
                while ($val_old_row = gaz_dbi_fetch_array($old_rows)) {
                    if (substr($form['tipdoc'], 0, 2) <> 'DD') {
                        $form['numdoc'] = $form['numfat'];
                    }
                    if ($i <= $count) { //se il vecchio rigo e' ancora presente nel nuovo lo modifico
                        $form['rows'][$i]['id_tes'] = $form['id_tes'];
                        $codice = array('id_rig', $val_old_row['id_rig']);
                        rigdocUpdate($codice, $form['rows'][$i]);
                        if (isset($form["row_$i"]) && $val_old_row['id_body_text'] > 0) { //se è un rigo testo già presente lo modifico
                            bodytextUpdate(array('id_body', $val_old_row['id_body_text']), array('table_name_ref' => 'rigdoc', 'id_ref' => $val_old_row['id_rig'], 'body_text' => $form["row_$i"], 'lang_id' => $admin_aziend['id_language']));
                            gaz_dbi_put_row($gTables['rigdoc'], 'id_rig', $val_old_row['id_rig'], 'id_body_text', $val_old_row['id_body_text']);
                        } elseif (isset($form["row_$i"]) && $val_old_row['id_body_text'] == 0) { //prima era un rigo diverso da testo
                            bodytextInsert(array('table_name_ref' => 'rigdoc', 'id_ref' => $val_old_row['id_rig'], 'body_text' => $form["row_$i"], 'lang_id' => $admin_aziend['id_language']));
                            gaz_dbi_put_row($gTables['rigdoc'], 'id_rig', $val_old_row['id_rig'], 'id_body_text', gaz_dbi_last_id());
                        } elseif (!isset($form["row_$i"]) && $val_old_row['id_body_text'] > 0) { //un rigo che prima era testo adesso non lo è più
                            gaz_dbi_del_row($gTables['body_text'], "table_name_ref = 'rigdoc' AND id_ref", $val_old_row['id_rig']);
                        }
                        if ($form['rows'][$i]['id_mag'] > 0) {
							// riprendo il vecchio movimento per non perdere il riferimento al lotto/matricola
							$old_movmag = gaz_dbi_get_row($gTables['movmag'], "id_mov", $val_old_row['id_mag']);
// se il rigo ha un movimento di magazzino associato lo aggiorno
                            $magazz->uploadMag($val_old_row['id_rig'], $form['tipdoc'], $form['numdoc'], $form['seziva'], $datemi, $form['clfoco'], $form['sconto'], $form['caumag'], $form['rows'][$i]['codart'], $form['rows'][$i]['quanti'], $form['rows'][$i]['prelis'], $form['rows'][$i]['sconto'], $val_old_row['id_mag'], $admin_aziend['stock_eval_method'], false, $form['protoc'],$old_movmag['id_lotmag']);
// aggiorno pure i documenti relativi ai lotti
                            $old_lm = gaz_dbi_get_row($gTables['lotmag'], 'id_rigdoc', $val_old_row['id_rig']);
                            if ($old_lm && substr($form['rows'][$i]['filename'], 0, 7) <> 'lotmag_') {
// se a questo rigo corrispondeva un certificato controllo che però è stato aggiornato lo cambio
                                $dh = opendir('../../data/files/' . $admin_aziend['company_id']);
                                while (false !== ($filename = readdir($dh))) {
                                    $fd = pathinfo($filename);
                                    if ($fd['filename'] == 'lotmag_' . $old_lm['id']) {
                                        // cancello il file precedente indipendentemente dall'estensione
                                        $frep = glob('../../data/files/' . $admin_aziend['company_id'] . "/lotmag_" . $old_lm['id'] . ".*");
                                        foreach ($frep as $fdel) {// prima cancello eventuali precedenti file temporanei
                                            unlink($fdel);
                                        }
                                    }
                                }
                                $tmp_file = "../../data/files/tmp/" . $admin_aziend['adminid'] . '_' . $admin_aziend['company_id'] . '_' . $i . '_' . $form['rows'][$i]['filename'];
// sposto e rinomino il relativo file temporaneo    
                                $fn = pathinfo($form['rows'][$i]['filename']);
                                rename($tmp_file, "../../data/files/" . $admin_aziend['company_id'] . "/lotmag_" . $old_lm['id'] . '.' . $fn['extension']);
                            }
                        }
                    } else { //altrimenti lo elimino
                        if ($val_old_row['id_mag'] > 0) {  //se c'è stato un movimento di magazzino lo azzero
                            $magazz->uploadMag('DEL', $form['tipdoc'], '', '', '', '', '', '', '', '', '', '', $val_old_row['id_mag'], $admin_aziend['stock_eval_method']);
                        }
                        gaz_dbi_del_row($gTables['rigdoc'], "id_rig", $val_old_row['id_rig']);
                    }
                    $i++;
                }
//qualora i nuovi righi fossero di più dei vecchi inserisco l'eccedenza
                for ($i = $i; $i <= $count; $i++) {
                    $form['rows'][$i]['id_tes'] = $form['id_tes'];
                    rigdocInsert($form['rows'][$i]);
                    $last_rigdoc_id = gaz_dbi_last_id();
                    if ($admin_aziend['conmag'] == 2 &&
                            $form['rows'][$i]['tiprig'] == 0 &&
                            $form['rows'][$i]['gooser'] == 0 &&
                            !empty($form['rows'][$i]['codart'])) { //se l'impostazione in azienda prevede l'aggiornamento automatico dei movimenti di magazzino
                        $last_movmag_id = $magazz->uploadMag(gaz_dbi_last_id(), $form['tipdoc'], $form['numdoc'], $form['seziva'], $datemi, $form['clfoco'], $form['sconto'], $form['caumag'], $form['rows'][$i]['codart'], $form['rows'][$i]['quanti'], $form['rows'][$i]['prelis'], $form['rows'][$i]['sconto'], 0, $admin_aziend['stock_eval_method'], false, $form['protoc']
                        );
                    }
// se l'articolo prevede la gestione dei  lotti o della matricola/numero seriale creo un rigo in lotmag 
// ed eventualmente sposto e rinomino il relativo documento dalla dir temporanea a quella definitiva 
                    if ($form['rows'][$i]['lot_or_serial'] > 0) {
                        $form['rows'][$i]['id_rigdoc'] = $last_rigdoc_id;
                        $form['rows'][$i]['id_movmag'] = $last_movmag_id;
                        $form['rows'][$i]['expiry'] = gaz_format_date($form['rows'][$i]['expiry'], true);
                        if (empty($form['rows'][$i]['identifier'])) {
// creo un identificativo del lotto/matricola interno                            
                            $form['rows'][$i]['identifier'] = $form['datemi'] . '_' . $form['rows'][$i]['id_rigdoc'];
                        }
                        $last_lotmag_id = lotmagInsert($form['rows'][$i]);
                        // inserisco il rifermineto anche sul relativo movimento di magazzino
                        gaz_dbi_put_row($gTables['movmag'], 'id_mov', $last_movmag_id, 'id_lotmag', $last_lotmag_id);
                        if (!empty($form['rows'][$i]['filename'])) {
                            $tmp_file = "../../data/files/tmp/" . $admin_aziend['adminid'] . '_' . $admin_aziend['company_id'] . '_' . $i . '_' . $form['rows'][$i]['filename'];
// sposto e rinomino il relativo file temporaneo    
                            $fd = pathinfo($form['rows'][$i]['filename']);
                            rename($tmp_file, "../../data/files/" . $admin_aziend['company_id'] . "/lotmag_" . $last_lotmag_id . '.' . $fd['extension']);
                        }
                    }
                }
//modifico la testata con i nuovi dati...
                $old_head = gaz_dbi_get_row($gTables['tesdoc'], 'id_tes', $form['id_tes']);
                if (substr($form['tipdoc'], 0, 2) == 'DD') { //se è un DDT non fatturato
                    $form['numfat'] = 0;
                }
                $form['geneff'] = $old_head['geneff'];
                $form['id_contract'] = $old_head['id_contract'];
                $form['id_con'] = $old_head['id_con'];
                $form['status'] = $old_head['status'];
                $form['initra'] = $initra;
                $form['datemi'] = $datemi;
                $form['id_orderman'] = $form['in_id_orderman'];
                $codice = array('id_tes', $form['id_tes']);
                tesdocUpdate($codice, $form);
                $prefix = $admin_aziend['adminid'] . '_' . $admin_aziend['company_id'];
// prima di uscire cancello eventuali precedenti file temporanei
                foreach (glob("../../data/files/tmp/" . $prefix . "_*.*") as $fn) {
                    unlink($fn);
                }
                header("Location: " . $form['ritorno']);
                exit;
            } else { // e' un'inserimento
// ricavo i progressivi in base al tipo di documento
                $where = "numdoc desc";
                switch ($form['tipdoc']) {
                    case "DDR":
                        $sql_documento = "YEAR(datemi) = " . substr($datemi,0,4) . " and ( tipdoc like 'DD_' or tipdoc = 'FAD') and seziva = $sezione";
                        break;
                    case "DDL":
                        $sql_documento = "YEAR(datemi) = " . substr($datemi,0,4) . " and ( tipdoc like 'DD_' or tipdoc = 'FAD') and seziva = $sezione";
                        break;
                    case "AFA":
                        $sql_documento = "YEAR(datemi) = " . substr($datemi,0,4) . " and tipdoc like 'AFA' and seziva = $sezione";
                        $where = "numfat desc";
                        break;
                    case "ADT":
                        $sql_documento = "YEAR(datemi) = " . substr($datemi,0,4) . " and tipdoc like 'ADT' and seziva = $sezione";
                        break;
                    case "AFC":
                        $sql_documento = "YEAR(datemi) = " . substr($datemi,0,4) . " and tipdoc = 'AFC' and seziva = $sezione";
                        $where = "numfat desc";
                        break;
                }
                $rs_ultimo_documento = gaz_dbi_dyn_query("*", $gTables['tesdoc'], $sql_documento, $where, 0, 1);
                $ultimo_documento = gaz_dbi_fetch_array($rs_ultimo_documento);
// se e' il primo documento dell'anno, resetto il contatore
                if ($ultimo_documento) {
                    $form['numdoc'] = $ultimo_documento['numdoc'] + 1;
                } else {
                    $form['numdoc'] = 1;
                }
                if (substr($form['tipdoc'], 0, 2) == 'DD') {  //ma se e' un ddt a fornitore il protocollo è 0 così come il numero e data fattura
                    $form['protoc'] = 0;
                    $form['numfat'] = 0;
                } else { //in tutti gli altri casi si deve prendere quanto inserito nel form
                    $form['protoc'] = getProtocol($form['tipdoc'], substr($datemi,0,4), $sezione);
                }
//inserisco la testata
                $form['status'] = '';
                $form['initra'] = $initra;
                $form['datemi'] = $datemi;
                $form['id_orderman'] = $form['in_id_orderman'];
               tesdocInsert($form);
//recupero l'id assegnato dall'inserimento
                $ultimo_id = gaz_dbi_last_id();
//inserisco i righi
                foreach ($form['rows'] as $i => $value) {
                    $form['rows'][$i]['id_tes'] = $ultimo_id;
                    rigdocInsert($form['rows'][$i]);
                    $last_rigdoc_id = gaz_dbi_last_id();
                    if (isset($form["row_$i"])) { //se è un rigo testo lo inserisco il contenuto in body_text
                        bodytextInsert(array('table_name_ref' => 'rigdoc', 'id_ref' => $last_rigdoc_id, 'body_text' => $form["row_$i"], 'lang_id' => $admin_aziend['id_language']));
                        gaz_dbi_put_row($gTables['rigdoc'], 'id_rig', $last_rigdoc_id, 'id_body_text', gaz_dbi_last_id());
                    }
                    if ($admin_aziend['conmag'] == 2 &&
                            $form['rows'][$i]['tiprig'] == 0 &&
                            $form['rows'][$i]['gooser'] != 1 &&
                            !empty($form['rows'][$i]['codart'])) { //se l'impostazione in azienda prevede l'aggiornamento automatico dei movimenti di magazzino
                        $last_movmag_id = $magazz->uploadMag(gaz_dbi_last_id(), $form['tipdoc'], $form['numdoc'], $form['seziva'], $datemi, $form['clfoco'], $form['sconto'], $form['caumag'], $form['rows'][$i]['codart'], $form['rows'][$i]['quanti'], $form['rows'][$i]['prelis'], $form['rows'][$i]['sconto'], 0, $admin_aziend['stock_eval_method'], false, $form['protoc']);
                    }
// se l'articolo prevede la gestione dei  lotti o della matricola/numero seriale creo un rigo in lotmag 
// ed eventualmente sposto e rinomino il relativo documento dalla dir temporanea a quella definitiva 
                    if ($form['rows'][$i]['lot_or_serial'] > 0) {
                        $form['rows'][$i]['id_rigdoc'] = $last_rigdoc_id;
                        $form['rows'][$i]['id_movmag'] = $last_movmag_id;
                        $form['rows'][$i]['expiry'] = gaz_format_date($form['rows'][$i]['expiry'], true);
                        if (empty($form['rows'][$i]['identifier'])) {
// creo un identificativo del lotto/matricola interno                            
                            $form['rows'][$i]['identifier'] = $form['datemi'] . '_' . $form['rows'][$i]['id_rigdoc'];
                        }
                        $last_lotmag_id = lotmagInsert($form['rows'][$i]);
                        // inserisco il rifermineto anche sul relativo movimento di magazzino
                        gaz_dbi_put_row($gTables['movmag'], 'id_mov', $last_movmag_id, 'id_lotmag', $last_lotmag_id);
                        if (!empty($form['rows'][$i]['filename'])) {
                            $tmp_file = "../../data/files/tmp/" . $admin_aziend['adminid'] . '_' . $admin_aziend['company_id'] . '_' . $i . '_' . $form['rows'][$i]['filename'];
// sposto e rinomino il relativo file temporaneo    
                            $fd = pathinfo($form['rows'][$i]['filename']);
                            rename($tmp_file, "../../data/files/" . $admin_aziend['company_id'] . "/lotmag_" . $last_lotmag_id . '.' . $fd['extension']);
                        }
                    }
                }
                $prefix = $admin_aziend['adminid'] . '_' . $admin_aziend['company_id'];
// prima di uscire cancello eventuali precedenti file temporanei
                foreach (glob("../../data/files/tmp/" . $prefix . "_*.*") as $fn) {
                    unlink($fn);
                }
                $_SESSION['print_request'] = $ultimo_id;
                header("Location: invsta_docacq.php");
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
        if (substr($form['tipdoc'], 0, 1) != 'A') {
            $result = gaz_dbi_get_row($gTables['imball'], "codice", $fornitore['imball']);
            $form['imball'] = $result['descri'];
        }
        $result = gaz_dbi_get_row($gTables['portos'], "codice", $fornitore['portos']);
        $form['portos'] = $result['descri'];
        $result = gaz_dbi_get_row($gTables['spediz'], "codice", $fornitore['spediz']);
        $form['spediz'] = $result['descri'];
        $form['destin'] = $fornitore['destin'];
        $form['id_des'] = $fornitore['id_des'];
        $id_des = $anagrafica->getPartner($form['id_des']);
        $form['search']['id_des'] = substr($id_des['ragso1'], 0, 10);
        if ($fornitore['aliiva'] > 0) {
            $form['ivaspe'] = $fornitore['aliiva'];
            $result = gaz_dbi_get_row($gTables['aliiva'], 'codice', $fornitore['aliiva']);
            $form['pervat'] = $result['aliquo'];
        }
        $form['in_codvat'] = $fornitore['aliiva'];
        $form['sconto'] = $fornitore['sconto'];
        $form['pagame'] = $fornitore['codpag'];
        $form['change_pag'] = $fornitore['codpag'];
        $form['banapp'] = $fornitore['banapp'];
        $form['listin'] = $fornitore['listin'];
        $form['address'] = $fornitore['indspe'] . ' ' . $fornitore['citspe'];
        $pagame = gaz_dbi_get_row($gTables['pagame'], "codice", $form['pagame']);
        if (($pagame['tippag'] == 'B' or $pagame['tippag'] == 'T' or $pagame['tippag'] == 'V')
                and $fornitore['speban'] == 'S') {
            $form['speban'] = 0;
            $form['numrat'] = $pagame['numrat'];
        } else {
            $form['speban'] = 0.00;
            $form['numrat'] = 1;
        }
        if ($fornitore['cosric'] > 0) {
            $form['in_codric'] = $fornitore['cosric'];
        }
        if ($fornitore['ritenuta'] > 0 ) { // carico la ritenuta se previsto
            $form['in_ritenuta'] = $fornitore['ritenuta'];
        }
        $form['hidden_req'] = '';
    }

// Se viene inviata la richiesta di conferma rigo
//if (isset($_POST['in_submit_x'])) {
    /** ENRICO FEDELE */
    /* con button non funziona _x */
    if (isset($_POST['in_submit'])) {
        /** ENRICO FEDELE */
        $artico = gaz_dbi_get_row($gTables['artico'], "codice", $form['in_codart']);

        /** inizio modifica FP 09/01/2016
         * modifica piede ddt
         */
// addizione ai totali peso,pezzi,volume
        $form['net_weight'] += $form['in_quanti'] * $artico['peso_specifico'];
        $form['gross_weight'] += $form['in_quanti'] * $artico['peso_specifico'];
        if ($artico['pack_units'] > 0) {
            $form['units'] += intval(round($form['in_quanti'] / $artico['pack_units']));
        }
        $form['volume'] += $form['in_quanti'] * $artico['volume_specifico'];
// fine addizione peso,pezzi,volume
        /** fine modifica FP */
        /** inizio modifica FP 27/10/2015
         * carico gli indirizzi di destinazione dalla tabella gaz_destina
         */
        $idAnagrafe = $fornitore['id_anagra'];
        $rs_query_destinazioni = gaz_dbi_dyn_query("*", $gTables['destina'], "id_anagra='$idAnagrafe'");
        $array_destinazioni = gaz_dbi_fetch_all($rs_query_destinazioni);
        /** fine modifica FP */
        if (substr($form['in_status'], 0, 6) == "UPDROW") { //se è un rigo da modificare
            $old_key = intval(substr($form['in_status'], 6));
            $form['rows'][$old_key]['tiprig'] = $form['in_tiprig'];
            $form['rows'][$old_key]['descri'] = $form['in_descri'];
            $form['rows'][$old_key]['id_mag'] = $form['in_id_mag'];
            $form['rows'][$old_key]['id_orderman'] = $form['in_id_orderman'];
            $form['rows'][$old_key]['status'] = "UPDATE";
            $form['rows'][$old_key]['unimis'] = $form['in_unimis'];
            $form['rows'][$old_key]['quanti'] = $form['in_quanti'];
            $form['rows'][$old_key]['codart'] = $form['in_codart'];
            $form['rows'][$old_key]['codric'] = $form['in_codric'];
            $form['rows'][$old_key]['ritenuta'] = $form['in_ritenuta'];
            $form['rows'][$old_key]['prelis'] = floatval(preg_replace("/\,/", '.', $form['in_prelis']));
            $form['rows'][$old_key]['sconto'] = $form['in_sconto'];
            $form['rows'][$old_key]['codvat'] = $form['in_codvat'];
            $form['rows'][$old_key]['codice_fornitore'] = $form['in_codice_fornitore'];
            $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $form['in_codvat']);
            $form['rows'][$old_key]['pervat'] = $iva_row['aliquo'];
            $form['rows'][$old_key]['annota'] = '';
            $form['rows'][$old_key]['pesosp'] = 0;
            $form['rows'][$old_key]['gooser'] = 0;
            $form['rows'][$old_key]['scorta'] = 0;
            $form['rows'][$old_key]['quamag'] = 0;
            $form['rows'][$old_key]['lot_or_serial'] = $form['in_lot_or_serial'];
            $form['rows'][$old_key]['identifier'] = '';
            $form['rows'][$old_key]['expiry'] = '';
            $form['rows'][$old_key]['filename'] = '';
            if ($form['in_tiprig'] == 0 and ! empty($form['in_codart'])) {  //rigo normale
                $form['rows'][$old_key]['annota'] = $artico['annota'];
                $form['rows'][$old_key]['pesosp'] = $artico['peso_specifico'];
                $form['rows'][$old_key]['gooser'] = $artico['good_or_service'];
                $form['rows'][$old_key]['unimis'] = $artico['uniacq'];
                $form['rows'][$old_key]['descri'] = $artico['descri'];
                $mv = $magazz->getStockValue(false, $form['in_codart'], gaz_format_date($form['datemi'], true), $admin_aziend['stock_eval_method']);
                $magval = array_pop($mv);
                $form['rows'][$i]['scorta'] = $artico['scorta'];
                $form['rows'][$i]['quamag'] = $magval['q_g'];
                $form['rows'][$old_key]['lot_or_serial'] = $artico['lot_or_serial'];
                if ($artico['lot_or_serial'] == 2) {
// se è prevista la gestione per numero seriale/matricola la quantità non può essere diversa da 1 
                    if ($form['rows'][$old_key]['quanti'] <> 1) {
                        $msg['err'][] = "forceone";
                    }
                    $form['rows'][$old_key]['quanti'] = 1;
                    $msg['err'][] = "forceone";
                }
                $form['rows'][$old_key]['prelis'] = floatval(preg_replace("/\,/", '.', $artico['preacq']));
            } elseif ($form['in_tiprig'] == 2) { //rigo descrittivo
                $form['rows'][$old_key]['codart'] = "";
                $form['rows'][$old_key]['annota'] = "";
                $form['rows'][$old_key]['pesosp'] = "";
                $form['rows'][$old_key]['gooser'] = 0;
                $form['rows'][$old_key]['unimis'] = "";
                $form['rows'][$old_key]['quanti'] = 0;
                $form['rows'][$old_key]['prelis'] = 0;
                $form['rows'][$old_key]['codric'] = 0;
                $form['rows'][$old_key]['sconto'] = 0;
                $form['rows'][$old_key]['pervat'] = 0;
                $form['rows'][$old_key]['codvat'] = 0;
            } elseif ($form['in_tiprig'] == 1) { //rigo forfait
                $form['rows'][$old_key]['codart'] = "";
                $form['rows'][$old_key]['unimis'] = "";
                $form['rows'][$old_key]['quanti'] = 0;
                $form['rows'][$old_key]['sconto'] = 0;
            } elseif ($form['in_tiprig'] == 3) {   //var.tot.fatt.
                $form['rows'][$old_key]['codart'] = "";
                $form['rows'][$old_key]['quanti'] = "";
                $form['rows'][$old_key]['unimis'] = "";
                $form['rows'][$old_key]['sconto'] = 0;
            }
            ksort($form['rows']);
        } else { //se è un rigo da inserire
            $form['rows'][$i]['tiprig'] = $form['in_tiprig'];
            $form['rows'][$i]['descri'] = $form['in_descri'];
            $form['rows'][$i]['id_mag'] = $form['in_id_mag'];
            $form['rows'][$i]['id_orderman'] = $form['in_id_orderman'];
            $form['rows'][$i]['status'] = "INSERT";
            $form['rows'][$i]['ritenuta'] = $form['in_ritenuta'];
            $form['rows'][$i]['identifier'] = '';
            $form['rows'][$i]['expiry'] = '';
            $form['rows'][$i]['filename'] = '';
            if ($form['in_tiprig'] == 0) {  //rigo normale
                $form['rows'][$i]['codart'] = $form['in_codart'];
				$form['rows'][$i]['codice_fornitore'] = $artico['codice_fornitore']; //M1 aggiunto a mano
                $form['rows'][$i]['annota'] = $artico['annota'];
                $form['rows'][$i]['pesosp'] = $artico['peso_specifico'];
                $form['rows'][$i]['gooser'] = $artico['good_or_service'];
                $form['rows'][$i]['descri'] = $artico['descri'];
                $form['rows'][$i]['unimis'] = $artico['uniacq'];
                $form['rows'][$i]['lot_or_serial'] = $artico['lot_or_serial'];
                $form['rows'][$i]['codric'] = $form['in_codric'];
                $form['rows'][$i]['quanti'] = $form['in_quanti'];
                if ($artico['lot_or_serial'] == 2) {
// se è prevista la gestione per numero seriale/matricola la quantità non può essere diversa da 1 
                    if ($form['rows'][$i]['quanti'] <> 1) {
                        $msg['err'][] = "forceone";
                    }
                    $form['rows'][$i]['quanti'] = 1;
                }
                $form['rows'][$i]['sconto'] = $form['in_sconto'];
                /** inizio modifica FP 09/10/2015
                 * se non ho inserito uno sconto nella maschera prendo quello standard registrato nell'articolo 
                 */
                $in_sconto = $form['in_sconto'];
                if ($in_sconto != "#") {
                    $form['rows'][$i]['sconto'] = $in_sconto;
                } else {
                    $form['rows'][$i]['sconto'] = $artico['sconto'];
                    if ($artico['sconto'] != 0) {
                        $msgtoast = $form['rows'][$i]['codart'] . ": sconto da anagrafe articoli";
                    }
                }
                /* fine modifica FP */

                $form['rows'][$i]['prelis'] = floatval(preg_replace("/\,/", '.', $artico['preacq']));
                $form['rows'][$i]['codvat'] = $admin_aziend['preeminent_vat'];
                $iva_azi = gaz_dbi_get_row($gTables['aliiva'], "codice", $admin_aziend['preeminent_vat']);
                $form['rows'][$i]['pervat'] = $iva_azi['aliquo'];
                if ($artico['aliiva'] > 0) {
                    $form['rows'][$i]['codvat'] = $artico['aliiva'];
                    $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $artico['aliiva']);
                    $form['rows'][$i]['pervat'] = $iva_row['aliquo'];
                }
                if ($form['in_codvat'] > 0) {
                    $form['rows'][$i]['codvat'] = $form['in_codvat'];
                    $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $form['in_codvat']);
                    $form['rows'][$i]['pervat'] = $iva_row['aliquo'];
                }
                if ($artico['id_cost'] > 0) {
                    $form['rows'][$i]['codric'] = $artico['id_cost'];
                    $form['in_codric'] = $artico['id_cost'];
                }
                if ($form['tipdoc'] == 'AFC') { // nel caso che si tratti di nota di credito
                    $form['in_codric'] = $admin_aziend['purchases_return'];
                }
                $mv = $magazz->getStockValue(false, $form['in_codart'], gaz_format_date($form['datemi'], true), $admin_aziend['stock_eval_method']);
                $magval = array_pop($mv);
                $form['rows'][$i]['scorta'] = $artico['scorta'];
                $form['rows'][$i]['quamag'] = $magval['q_g'];
                if ($artico['lot_or_serial'] > 0) {
                    $lm->getAvailableLots($form['in_codart'], $form['in_id_mag']);
                    $ld = $lm->divideLots($form['in_quanti']);
                    /* ripartisco la quantità introdotta tra i vari lotti disponibili per l'articolo
                     * e se è il caso creo più righi  
                     */
                    $j = $i;
                    foreach ($lm->divided as $k => $v) {
                        if ($v['qua'] >= 0.00001) {
                            $form['rows'][$j] = $form['rows'][$i]; // copio il rigo di origine
                            $form['rows'][$j]['id_lotmag'] = $k; // setto il lotto 
                            $form['rows'][$j]['quanti'] = $v['qua']; // e la quantità in base al riparto
                            $j++;
                        }
                    }
                }
            } elseif ($form['in_tiprig'] == 1) { //forfait
                $form['rows'][$i]['codart'] = "";
                $form['rows'][$i]['annota'] = "";
                $form['rows'][$i]['pesosp'] = 0;
                $form['rows'][$i]['gooser'] = 0;
                $form['rows'][$i]['unimis'] = "";
                $form['rows'][$i]['lot_or_serial'] = '';
                $form['rows'][$i]['quanti'] = 0;
                $form['rows'][$i]['prelis'] = 0;
                $form['rows'][$i]['codric'] = $form['in_codric'];
                $form['rows'][$i]['sconto'] = 0;
                $form['rows'][$i]['codvat'] = $admin_aziend['preeminent_vat'];
                $iva_azi = gaz_dbi_get_row($gTables['aliiva'], "codice", $admin_aziend['preeminent_vat']);
                $form['rows'][$i]['pervat'] = $iva_azi['aliquo'];
                $form['rows'][$i]['tipiva'] = $iva_azi['tipiva'];
                if ($form['in_codvat'] > 0) {
                    $form['rows'][$i]['codvat'] = $form['in_codvat'];
                    $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $form['in_codvat']);
                    $form['rows'][$i]['pervat'] = $iva_row['aliquo'];
                    $form['rows'][$i]['tipiva'] = $iva_row['tipiva'];
                }
            } elseif ($form['in_tiprig'] == 2) { //descrittivo
                $form['rows'][$i]['codart'] = "";
                $form['rows'][$i]['annota'] = "";
                $form['rows'][$i]['pesosp'] = 0;
                $form['rows'][$i]['gooser'] = 0;
                $form['rows'][$i]['lot_or_serial'] = '';
                $form['rows'][$i]['unimis'] = "";
                $form['rows'][$i]['quanti'] = 0;
                $form['rows'][$i]['prelis'] = 0;
                $form['rows'][$i]['codric'] = 0;
                $form['rows'][$i]['sconto'] = 0;
                $form['rows'][$i]['pervat'] = 0;
                $form['rows'][$i]['codvat'] = 0;
            } elseif ($form['in_tiprig'] == 3) {
                $form['rows'][$i]['codart'] = "";
                $form['rows'][$i]['annota'] = "";
                $form['rows'][$i]['pesosp'] = 0;
                $form['rows'][$i]['gooser'] = 0;
                $form['rows'][$i]['lot_or_serial'] = '';
                $form['rows'][$i]['unimis'] = "";
                $form['rows'][$i]['quanti'] = 0;
                $form['rows'][$i]['prelis'] = $form['in_prelis'];
                $form['rows'][$i]['codric'] = $form['in_codric'];
                $form['rows'][$i]['sconto'] = 0;
                $form['rows'][$i]['ritenuta'] = 0;
                $form['rows'][$i]['codvat'] = $form['in_codvat'];
                $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $form['in_codvat']);
                $form['rows'][$i]['pervat'] = $iva_row['aliquo'];
            } elseif ($form['in_tiprig'] == 4) { // cassa previdenziale
                $form['rows'][$i]['codart'] = $admin_aziend['fae_tipo_cassa'];// propongo quella aziendale uso il codice articolo
                $form['rows'][$i]['annota'] = "";
                $form['rows'][$i]['pesosp'] = "";
                $form['rows'][$i]['gooser'] = 0;
                $form['rows'][$i]['unimis'] = "";
                $form['rows'][$i]['quanti'] = 0;
                $form['rows'][$i]['prelis'] = 0;
                $form['rows'][$i]['codric'] = $admin_aziend['c_payroll_tax'];
                $form['rows'][$i]['sconto'] = 0;
                $form['rows'][$i]['codvat'] = $admin_aziend['preeminent_vat'];
                $iva_azi = gaz_dbi_get_row($gTables['aliiva'], "codice", $admin_aziend['preeminent_vat']);
                $form['rows'][$i]['pervat'] = $iva_azi['aliquo'];
                if ($form['in_codvat'] > 0) {
                    $form['rows'][$i]['codvat'] = $form['in_codvat'];
                    $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $form['in_codvat']);
                    $form['rows'][$i]['pervat'] = $iva_row['aliquo'];
                    $form['rows'][$i]['tipiva'] = $iva_row['tipiva'];
                }
				$form['rows'][$i]['ritenuta'] = $form['in_ritenuta'];
				// carico anche la descrizione corrispondente dal file xml
	            $xml = simplexml_load_file('../../library/include/fae_tipo_cassa.xml');
				foreach ($xml->record as $v) {
					$selected = '';
					if ($v->field[0] == $form['rows'][$i]['codart']) {
						$form['rows'][$i]['descri']= 'Contributo '.strtolower($v->field[1]);
					}
				}
            } elseif ($form['in_tiprig'] > 5 && $form['in_tiprig'] < 9) { //testo
                $form["row_$i"] = "";
                $form['rows'][$i]['codart'] = "";
                $form['rows'][$i]['annota'] = "";
                $form['rows'][$i]['pesosp'] = 0;
                $form['rows'][$i]['gooser'] = 0;
                $form['rows'][$i]['lot_or_serial'] = '';
                $form['rows'][$i]['unimis'] = "";
                $form['rows'][$i]['quanti'] = 0;
                $form['rows'][$i]['prelis'] = 0;
                $form['rows'][$i]['codric'] = 0;
                $form['rows'][$i]['sconto'] = 0;
                $form['rows'][$i]['pervat'] = 0;
                $form['rows'][$i]['tipiva'] = 0;
                $form['rows'][$i]['ritenuta'] = 0;
                $form['rows'][$i]['codvat'] = 0;
            }
        }
// reinizializzo rigo di input tranne che per il tipo rigo e aliquota iva
        $form['in_descri'] = "";
        $form['in_codart'] = "";
        $form['in_unimis'] = "";
        $form['in_prelis'] = 0.000;
        $form['in_sconto'] = 0;
        /** inizio modifica FP 09/10/2015
         * inizializzo il campo con '#' per indicare che voglio lo sconto standard dell'articolo
         */
        /* carico gli indirizzi di destinazione dalla tabella gaz_destina */
        $idAnagrafe = $fornitore['id_anagra'];
        $rs_query_destinazioni = gaz_dbi_dyn_query("*", $gTables['destina'], "id_anagra='$idAnagrafe'");
        $array_destinazioni = gaz_dbi_fetch_all($rs_query_destinazioni);
        /* fine modifica FP */
        $form['in_quanti'] = 0;
        $form['in_id_mag'] = 0;
        $form['in_annota'] = "";
        $form['in_pesosp'] = 0;
        $form['in_gooser'] = 0;
        $form['in_scorta'] = 0;
        $form['in_quamag'] = 0;
        $form['in_status'] = "INSERT";
// fine reinizializzo rigo input
        $form['cosear'] = "";
        $i++;
    }
// Se viene inviata la richiesta di spostamento verso l'alto del rigo
    if (isset($_POST['upper_row'])) {
        $upp_key = key($_POST['upper_row']);
        $k_next = $upp_key - 1;
        if (isset($form["row_$k_next"])) { //se ho un rigo testo prima gli cambio l'index
            $form["row_$upp_key"] = $form["row_$k_next"];
            unset($form["row_$k_next"]);
        }
        if ($upp_key > 0) {
            $new_key = $upp_key - 1;
        } else {
            $new_key = $i - 1;
        }
        $tmp_path = "../../data/files/tmp/" . $admin_aziend['adminid'] . '_' . $admin_aziend['company_id'] . '_';
        // rinomino prima il documento della linea target new key ( se esiste )
        @rename($tmp_path . $new_key . '_' . $form['rows'][$new_key]['filename'], $tmp_path . '_tmp_' . $new_key . '_' . $form['rows'][$new_key]['filename']);
        // rinomino il documento della linea spostata verso l'alto dandogli gli indici di quello precedente
        @rename($tmp_path . $upp_key . '_' . $form['rows'][$upp_key]['filename'], $tmp_path . $new_key . '_' . $form['rows'][$upp_key]['filename']);
        // rinomino nuovamente il documento della linea target dandogli gli indici di quella spostata
        @rename($tmp_path . '_tmp_' . $new_key . '_' . $form['rows'][$new_key]['filename'], $tmp_path . $upp_key . '_' . $form['rows'][$new_key]['filename']);
        $updated_row = $form['rows'][$new_key];
        $form['rows'][$new_key] = $form['rows'][$upp_key];
        $form['rows'][$upp_key] = $updated_row;
        ksort($form['rows']);
        unset($updated_row);
    }
	
//Antonio Germani - Se viene richiesto di aggiornare il prezzo delll'articolo sulla tabella artico
	if (isset($_POST['updateprice'])){
		$updri = key($_POST['updateprice']);
		If ($form['rows'][$updri]['codart']==""){
			$msg['err'][] = "noartupd";
			} else {		
		$artico = gaz_dbi_get_row($gTables['artico'], "codice", $form['rows'][$updri]['codart']);
		If ($artico['preacq']==$form['rows'][$updri]['prelis']){
			$msg['err'][] = "sampri";
		} else {
			$query="UPDATE " . $gTables['artico'] . " SET preacq = '" . $form['rows'][$updri]['prelis'] . "' WHERE codice ='". $form['rows'][$updri]['codart']."'";
			gaz_dbi_query ($query) ;
			}
			}
		unset ($_POST['updateprice']);
	}
// Fine modifica prezzo su artico

// Se viene inviata la richiesta elimina il rigo corrispondente
    if (isset($_POST['del'])) {
        $delri = key($_POST['del']);

        /** inizio modifica FP 09/01/2016
         * modifica piede ddt
         */
// sottrazione ai totali peso,pezzi,volume
        $artico = gaz_dbi_get_row($gTables['artico'], "codice", $form['rows'][$delri]['codart']);
        $form['net_weight'] -= $form['rows'][$delri]['quanti'] * $artico['peso_specifico'];
        $form['gross_weight'] -= $form['rows'][$delri]['quanti'] * $artico['peso_specifico'];
        if ($artico['pack_units'] > 0) {
            $form['units'] -= intval(round($form['rows'][$delri]['quanti'] / $artico['pack_units']));
        }
        $form['volume'] -= $form['rows'][$delri]['quanti'] * $artico['volume_specifico'];
// fine sottrazione peso,pezzi,volume
        /** fine modifica FP */
// diminuisco o lascio inalterati gli index dei testi
        foreach ($form['rows'] as $k => $val) {
            if (isset($form["row_$k"])) { //se ho un rigo testo
                if ($k > $delri) { //se ho un rigo testo dopo
                    $new_k = $k - 1;
                    $form["row_$new_k"] = $form["row_$k"];
                    unset($form["row_$k"]);
                }
            }
        }

        array_splice($form['rows'], $delri, 1);
        $i--;
    }
} elseif ((!isset($_POST['Update'])) and ( isset($_GET['Update'])) or ( isset($_GET['Duplicate']))) { //se e' il primo accesso per UPDATE
    $tesdoc = gaz_dbi_get_row($gTables['tesdoc'], "id_tes", intval($_GET['id_tes']));
    $anagrafica = new Anagrafica();
    $fornitore = $anagrafica->getPartner($tesdoc['clfoco']);
    $id_des = $anagrafica->getPartner($tesdoc['id_des']);
    $rs_rig = gaz_dbi_dyn_query("*", $gTables['rigdoc'], "id_tes = " . $tesdoc['id_tes'], "id_rig asc");
    $form['id_tes'] = $tesdoc['id_tes'];
    $form['hidden_req'] = '';
// inizio rigo di input
    $form['in_descri'] = "";
    $form['in_tiprig'] = 0;
    /*    $form['in_artsea'] = $admin_aziend['artsea']; */
    $form['in_codart'] = "";
    $form['in_codice_fornitore'] = '';
    $form['in_pervat'] = 0;
    $form['in_ritenuta'] = 0;
    $form['in_unimis'] = "";
    $form['in_prelis'] = 0.000;
    $form['in_sconto'] = 0;
    $form['in_quanti'] = 0;
    $form['in_codvat'] = $admin_aziend['preeminent_vat'];
    if ($fornitore['cosric'] > 0) {
        $form['in_codric'] = $fornitore['cosric'];
    } else {
        $form['in_codric'] = $admin_aziend['impacq'];
    }
    if ($tesdoc['tipdoc'] == 'AFC') { // nel caso che si tratti di nota di credito
        $form['in_codric'] = $admin_aziend['purchases_return'];
        if ($form['in_codric'] < 300000000) {
            $form['in_codric'] = '3';
        }
    }
    $form['in_id_mag'] = 0;
    $form['in_id_orderman'] = 0;
    $form['in_annota'] = "";
    $form['in_pesosp'] = 0;
    $form['in_gooser'] = 0;
    $form['in_scorta'] = 0;
    $form['in_quamag'] = 0;
    $form['in_lot_or_serial'] = 0;
    $form['in_status'] = "INSERT";
// fine rigo input
    $form['rows'] = array();
// ...e della testata
    $form['search']['clfoco'] = substr($fornitore['ragso1'], 0, 10);
    $form['cosear'] = "";
    $form['address'] = $fornitore['indspe'] . ' ' . $fornitore['citspe'];
    $form['seziva'] = $tesdoc['seziva'];
    $form['id_con'] = $tesdoc['id_con'];
    $form['tipdoc'] = $tesdoc['tipdoc'];
    if ($tesdoc['id_con'] > 0) {
        $msg['war'][] = 'accounted';
    }
	$form['datreg']=gaz_format_date($tesdoc['datreg'], false, false);
	$form['datfat']=gaz_format_date($tesdoc['datfat'], false, false);
	$form['datemi']=gaz_format_date($tesdoc['datemi'], false, false);
    $form['giotra'] = substr($tesdoc['initra'], 8, 2);
    $form['mestra'] = substr($tesdoc['initra'], 5, 2);
    $form['anntra'] = substr($tesdoc['initra'], 0, 4);
    $form['oratra'] = substr($tesdoc['initra'], 11, 2);
    $form['mintra'] = substr($tesdoc['initra'], 14, 2);
    $form['protoc'] = $tesdoc['protoc'];
    $form['numdoc'] = $tesdoc['numdoc'];
    $form['numfat'] = $tesdoc['numfat'];
    $form['clfoco'] = $tesdoc['clfoco'];
    $form['pagame'] = $tesdoc['pagame'];
    $form['change_pag'] = $tesdoc['pagame'];
    $form['speban'] = 0;
    $pagame = gaz_dbi_get_row($gTables['pagame'], "codice", $form['pagame']);
    if (($pagame['tippag'] == 'B' or $pagame['tippag'] == 'T' or $pagame['tippag'] == 'V') and $fornitore['speban'] == 'S') {
        $form['numrat'] = $pagame['numrat'];
    } else {
        $form['speban'] = 0.00;
        $form['numrat'] = 1;
    }
    $form['banapp'] = $tesdoc['banapp'];
    $form['vettor'] = $tesdoc['vettor'];

    /** inizio modifica FP 09/01/2016
     * modifica piede ddt
     */
    $form['net_weight'] = $tesdoc['net_weight'];
    $form['gross_weight'] = $tesdoc['gross_weight'];
    $form['units'] = $tesdoc['units'];
    $form['volume'] = $tesdoc['volume'];
    $array_destinazioni = array();
    /** fine modifica FP */
    $form['listin'] = $tesdoc['listin'];
    $form['spediz'] = $tesdoc['spediz'];
    $form['portos'] = $tesdoc['portos'];
    $form['imball'] = $tesdoc['imball'];
    $form['destin'] = $tesdoc['destin'];
    $form['id_des'] = $tesdoc['id_des'];
    $form['id_des_same_company'] = $tesdoc['id_des_same_company'];
    $form['search']['id_des'] = substr($id_des['ragso1'], 0, 10);
    $form['traspo'] = $tesdoc['traspo'];
    $form['spevar'] = $tesdoc['spevar'];
    $form['ivaspe'] = 0;
    $form['pervat'] = 0;
    $form['cauven'] = $tesdoc['cauven'];
    $form['caucon'] = $tesdoc['caucon'];
    $form['caumag'] = $tesdoc['caumag'];
    $form['caucon'] = $tesdoc['caucon'];
    $form['id_parent_doc'] = $tesdoc['id_parent_doc'];
    $form['sconto'] = $tesdoc['sconto'];
    $form['lotmag'] = array();
    $i = 0;
    while ($row = gaz_dbi_fetch_array($rs_rig)) {
        $articolo = gaz_dbi_get_row($gTables['artico'], "codice", $row['codart']);
        if ($row['id_body_text'] > 0) { //se ho un rigo testo
            $text = gaz_dbi_get_row($gTables['body_text'], "id_body", $row['id_body_text']);
            $form["row_$i"] = $text['body_text'];
        }
        $form['rows'][$i]['descri'] = $row['descri'];
        $form['rows'][$i]['tiprig'] = $row['tiprig'];
        $form['rows'][$i]['codart'] = $row['codart'];
		$form['rows'][$i]['codice_fornitore'] = $row['codice_fornitore'];//M1 aggiunto a mano
        $form['rows'][$i]['pervat'] = $row['pervat'];
        $form['rows'][$i]['ritenuta'] = $row['ritenuta'];
        $form['rows'][$i]['unimis'] = $row['unimis'];
        $form['rows'][$i]['prelis'] = $row['prelis'];
        $form['rows'][$i]['sconto'] = $row['sconto'];
        $form['rows'][$i]['quanti'] = gaz_format_quantity($row['quanti'], 0, $admin_aziend['decimal_quantity']);
        $form['rows'][$i]['codvat'] = $row['codvat'];
        $form['rows'][$i]['codric'] = $row['codric'];
        $form['rows'][$i]['id_mag'] = $row['id_mag'];
        $form['in_id_orderman'] = $row['id_orderman'];
		$orderman = gaz_dbi_get_row($gTables['orderman'], "id", $row['id_orderman']);
        $form['coseprod'] = $orderman['description'];
        $form['rows'][$i]['id_orderman'] = $row['id_orderman'];
        $form['rows'][$i]['annota'] = $articolo['annota'];
        $mv = $magazz->getStockValue(false, $row['codart'], gaz_format_date($form['datemi'], true), $admin_aziend['stock_eval_method']);
        $magval = array_pop($mv);
        $form['rows'][$i]['scorta'] = $articolo['scorta'];
        $form['rows'][$i]['quamag'] = $magval['q_g'];
        $form['rows'][$i]['pesosp'] = $articolo['peso_specifico'];
        $form['rows'][$i]['gooser'] = $articolo['good_or_service'];
        $form['rows'][$i]['lot_or_serial'] = $articolo['lot_or_serial'];
        $form['rows'][$i]['filename'] = '';
        $form['rows'][$i]['identifier'] = '';
        $form['rows'][$i]['expiry'] = '';
        $form['rows'][$i]['status'] = "UPDATE";
        // recupero eventuale movimento di tracciabilità ma solo se non è stata richiesta una duplicazione (di un ddt c/lavorazione)
		If (file_exists('../../data/files/' . $admin_aziend['company_id'])>0) {
		if (!isset($_GET['Duplicate'])) {
			$lotmag = gaz_dbi_get_row($gTables['lotmag'], 'id_rigdoc', $row['id_rig']);
			// recupero il filename dal filesystem e lo sposto sul tmp 
			$dh = opendir('../../data/files/' . $admin_aziend['company_id']);
			while (false !== ($filename = readdir($dh))) {
				$fd = pathinfo($filename);
				$r = explode('_', $fd['filename']);
				if ($r[0] == 'lotmag' && $r[1] == $lotmag['id']) {
					// riassegno il nome file 
					$form['rows'][$i]['filename'] = $fd['basename'];
				}
			}
			$form['rows'][$i]['identifier'] = $lotmag['identifier'];
			$form['rows'][$i]['expiry'] = gaz_format_date($lotmag['expiry']);
		} else {
			$form['rows'][$i]['status'] = "Insert";
			$form['rows'][$i]['id_mag'] = 0;
		}
		} else {
			$msg['err'][] = "nofold";
		}
        $i++;
    }
    if (isset($_GET['Duplicate'])) {  // duplicate: devo reinizializzare i campi come per la insert
        $form['id_doc_ritorno'] = 0;
        $form['id_tes'] = "";
        $form['datemi'] = date("d/m/Y");
        $form['giotra'] = date("d");
        $form['mestra'] = date("m");
        $form['anntra'] = date("Y");
        $form['oratra'] = date("H");
        $form['mintra'] = date("i");
    }
} elseif (!isset($_POST['Insert'])) { //se e' il primo accesso per INSERT
    $form['tipdoc'] = $_GET['tipdoc'];
    $form['address'] = '';
    $form['hidden_req'] = '';
    $form['id_tes'] = "";
	$form['datreg'] = date("d/m/Y");
	$form['datfat'] = date('d/m/Y', strtotime(' +1 day'));
    $form['datemi'] = date("d/m/Y");;
    if (substr($form['tipdoc'], 0, 1) == 'A') { //un documento d'acquisto ricevuto (non fiscale) imposto l'ultimo giorno del mese in modo da evidenziare un eventuale errore di mancata introduzione manuale del dato
        $utstra = mktime(0, 0, 0, date("m") + 1, date("d"), date("Y"));
    } else {
        $utstra = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
    }
    $form['giotra'] = date("d", $utstra);
    $form['mestra'] = date("m", $utstra);
    $form['anntra'] = date("Y", $utstra);
    $form['oratra'] = date("H");
    $form['mintra'] = date("i");
    $form['rows'] = array();
// tracciabilità
    $form['lotmag'] = array();
// fine tracciabilità
    $i = 0;
// inizio rigo di input
    $form['in_descri'] = "";
    $form['in_tiprig'] = 0;
    /*    $form['in_artsea'] = $admin_aziend['artsea']; */
    $form['in_codart'] = "";
    $form['in_codice_fornitore'] = '';
    $form['in_pervat'] = "";
    $form['in_ritenuta'] = 0;
    $form['in_unimis'] = "";
    $form['in_prelis'] = 0.000;
    $form['in_sconto'] = 0;
    $form['in_quanti'] = 0;
    $form['in_codvat'] = $admin_aziend['preeminent_vat'];
    $form['in_codric'] = $admin_aziend['impacq'];
    if ($form['tipdoc'] == 'AFC') { // nel caso che si tratti di nota di credito
        $form['in_codric'] = $admin_aziend['purchases_return'];
    }
    $form['in_id_mag'] = 0;
    $form['in_id_orderman'] = 0;
    $form['in_annota'] = "";
    $form['in_pesosp'] = 0;
    $form['in_gooser'] = 0;
    $form['in_scorta'] = 0;
    $form['in_quamag'] = 0;
    $form['in_lot_or_serial'] = '';
    $form['in_status'] = "INSERT";
// fine rigo input
    $form['search']['clfoco'] = '';
    $form['cosear'] = "";
    if (isset($_GET['seziva'])) {
        $form['seziva'] = $_GET['seziva'];
    } else {
        $form['seziva'] = 1;
    }
    $form['id_con'] = '';
    $form['protoc'] = "";
    $form['numdoc'] = "";
    $form['numfat'] = "";
    $form['clfoco'] = "";
    $form['pagame'] = "";
    $form['change_pag'] = "";
    $form['banapp'] = "";
    $form['vettor'] = "";

    /** inizio modifica FP 09/01/2016
     * modifica piede ddt
     */
    $form['net_weight'] = 0;
    $form['gross_weight'] = 0;
    $form['units'] = 0;
    $form['volume'] = 0;
    $array_destinazioni = array();
    /** fine modifica FP */
    $form['listin'] = "";
    $form['destin'] = "";
    $form['id_des'] = 0;
    $form['id_des_same_company'] = 0;
    $form['search']['id_des'] = '';
    $form['spediz'] = "";
    $form['portos'] = "";
    $form['imball'] = "";
    $form['traspo'] = 0.00;
    $form['numrat'] = 1;
    $form['speban'] = 0;
    $form['spevar'] = 0;
    if ($admin_aziend['preeminent_vat'] > 0) {
        $form['ivaspe'] = $admin_aziend['preeminent_vat'];
    } else {
        $form['ivaspe'] = 1;
    }
    $result = gaz_dbi_get_row($gTables['aliiva'], "codice", $form['ivaspe']);
    $form['pervat'] = $result['aliquo'];
    $form['cauven'] = 0;
    $form['caucon'] = '';
    if ($form['tipdoc'] == 'DDR') {
        $form['caumag'] = 4; //causale: 4 	SCARICO PER RESO A FORNITORE
    } else if ($form['tipdoc'] == 'DDL') {
        $form['caumag'] = 3; //causale: 3 	SCARICO PER C/LAVORAZIONE
    } else {
        $form['caumag'] = 5; //causale: 5 	CARICO PER ACQUISTO
    }
    $form['id_parent_doc'] = 0;
    $form['sconto'] = 0;
}
require("../../library/include/header.php");
/** Mi pare che jquery in questa pagina venga caricato per la seconda volta
 * non è il caso di caricare differenti versioni di jquery perchè si possono generare conflitti
 * forse è il caso di caricare tutti i js utili per il sistema in un solo posto, nell'header
 * così è più semplice tenere traccia di quello che si carica, il sistema è organico e coerente e manutenibile
 * La versione scaricata dal repository di questa pagina dà due errori javascript, che inibiscono il caricamento della finestra modale
 * commentando i due script di seguito e inibendone il caricamento, rimane ancora un errore attivo, ma il caricamento della modale funziona
 */
$script_transl = HeadMain(0, array(
    'calendarpopup/CalendarPopup',
    'custom/autocomplete',
    'custom/modal_form'
        ));
?>
<script language="JavaScript">
    function pulldown_menu(selectName, destField)
    {
        // Create a variable url to contain the value of the
        // selected option from the the form named broven and variable selectName
        var url = document.docacq[selectName].options[document.docacq[selectName].selectedIndex].value;
        document.docacq[destField].value = url;
    }
    $(function () {
        $(".datepicker").datepicker({dateFormat: 'dd-mm-yy'});
    });
    $(function () {
        $("#datreg").datepicker({showButtonPanel: true, showOtherMonths: true, selectOtherMonths: true});
        $("#datreg").change(function () {
            this.form.submit();
        });
        $("#datfat").datepicker({showButtonPanel: true, showOtherMonths: true, selectOtherMonths: true});
        $("#datfat").change(function () {
            this.form.submit();
        });
        $("#datemi").datepicker({showButtonPanel: true, showOtherMonths: true, selectOtherMonths: true});
        $("#datemi").change(function () {
            this.form.submit();
        });
<?php
if (!(count($msg['err']) > 0 || count($msg['war']) > 0)) { // ho un errore non scrollo
    ?>
    $("html, body").delay(500).animate({scrollTop: $('#search_cosear').offset().top}, 1000);
    <?php
}
?>

	});
</script>
<form class="form-horizontal" role="form" method="post" name="tesdoc" enctype="multipart/form-data" >
    <input type="hidden" name="<?php echo ucfirst($toDo); ?>" value="">
    <input type="hidden" value="<?php echo $form['id_tes']; ?>" name="id_tes">
    <input type="hidden" value="<?php echo $form['tipdoc']; ?>" name="tipdoc">
    <input type="hidden" value="<?php echo $form['id_con']; ?>" name="id_con">
    <input type="hidden" value="<?php echo $form['address']; ?>" name="address">
    <input type="hidden" value="<?php echo $form['ritorno']; ?>" name="ritorno">
    <input type="hidden" value="<?php echo $form['giotra']; ?>" name="giotra">
    <input type="hidden" value="<?php echo $form['mestra']; ?>" name="mestra">
    <input type="hidden" value="<?php echo $form['anntra']; ?>" name="anntra">
    <input type="hidden" value="<?php echo $form['oratra']; ?>" name="oratra">
    <input type="hidden" value="<?php echo $form['mintra']; ?>" name="mintra">
    <input type="hidden" value="<?php echo $form['protoc']; ?>" name="protoc">
    <input type="hidden" value="<?php echo $form['speban']; ?>" name="speban">
    <input type="hidden" value="<?php echo $form['numrat']; ?>" name="numrat">
    <input type="hidden" value="<?php echo $form['change_pag']; ?>" name="change_pag">
    <input type="hidden" value="<?php echo $form['vettor']; ?>" name="vettor">
    <input type="hidden" value="<?php echo $form['listin']; ?>" name="listin">
    <input type="hidden" value="<?php echo $form['spediz']; ?>" name="spediz">
    <input type="hidden" value="<?php echo $form['portos']; ?>" name="portos">
    <input type="hidden" value="<?php echo $form['imball']; ?>" name="imball">
    <input type="hidden" value="<?php echo $form['destin']; ?>" name="destin">
    <input type="hidden" value="<?php echo $form['id_des']; ?>" name="id_des">
    <input type="hidden" value="<?php echo $form['id_des_same_company']; ?>" name="id_des_same_company">
    <input type="hidden" value="<?php echo $form['gross_weight']; ?>" name="gross_weight">
    <input type="hidden" value="<?php echo $form['banapp']; ?>" name="banapp">
    <input type="hidden" value="<?php echo $form['net_weight']; ?>" name="net_weight">
    <input type="hidden" value="<?php echo $form['units']; ?>" name="units">
    <input type="hidden" value="<?php echo $form['volume']; ?>" name="volume">
    <input type="hidden" value="<?php echo $form['id_parent_doc']; ?>" name="id_parent_doc" />
<?php 
/** inizio modifica FP 28/10/2015 */
$strArrayDest = base64_encode(serialize($array_destinazioni));
echo '<input type="hidden" value="' . $strArrayDest . '" name="rs_destinazioni">' . "\n"; // salvo l'array delle destinazioni in un hidden input 
/** fine modifica FP */
?>
    <input type="hidden" value="<?php echo $form['traspo']; ?>" name="traspo">
    <input type="hidden" value="<?php echo $form['spevar']; ?>" name="spevar">
    <input type="hidden" value="<?php echo $form['ivaspe']; ?>" name="ivaspe">
    <input type="hidden" value="<?php echo $form['pervat']; ?>" name="pervat">
    <input type="hidden" value="<?php echo $form['cauven']; ?>" name="cauven">
    <input type="hidden" value="<?php echo $form['caucon']; ?>" name="caucon">
    <input type="hidden" value="<?php echo $form['banapp']; ?>" name="banapp">
    <div class="text-center">
        <p>
            <b>
<?php
if (count($msg['err']) > 0) { // ho un errore
    $gForm->gazHeadMessage($msg['err'], $script_transl['err'], 'err');
}
if (count($msg['war']) > 0) { // ho un alert
    $gForm->gazHeadMessage($msg['war'], $script_transl['war'], 'war');
}

if ($form['id_tes'] > 0 && substr($form['tipdoc'], 0, 2) == 'AF') {
    $title = $script_transl[0][$form['tipdoc']] . ' prot.<input type="text" class="text-right" style="width:5em;" id="protoc" name="protoc" value="'.$form['protoc'].'">';
} else {
    $title = $script_transl[0][$form['tipdoc']];
}
if ($form['id_tes'] > 0) { // è una modifica
	echo $script_transl['upd_this'].$title;
} else {
    echo '<div>'.$script_transl['ins_this'].$title.'</div>';
}
$select_fornitore = new selectPartner('clfoco');
$select_fornitore->selectDocPartner('clfoco', $form['clfoco'], $form['search']['clfoco'], 'clfoco', $script_transl['search_partner'], $admin_aziend['masfor']);
?>
            </b> 
        </p>
    </div>
    <div class="panel panel-default">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="datreg" class="col-sm-4 control-label"><?php echo $script_transl['datreg']; ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="datreg" name="datreg" value="<?php echo $form['datreg']; ?>">
                        </div>
                    </div>
                </div>                    
                <div class="col-sm-6 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="seziva" class="col-sm-4 control-label"><?php echo $script_transl['seziva']; ?></label>
                        <div class="col-sm-8">
                            <?php $gForm->selectNumber('seziva', $form['seziva'], 0, 1, 9, 'col-sm-8'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="address" class="col-sm-4 control-label"><?php echo $script_transl['address']; ?></label>
                        <div class="col-sm-8"><?php echo $form['address']; ?></div>                
                    </div>
                </div>
            </div>
            <div class="row">
<?php	switch($form['tipdoc']){ // sui DDT non ho numero e data fattura  
				case 'DDR': case 'DDL': ?>
                <div class="col-sm-6 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="numdoc" class="col-sm-4 control-label"><?php echo $script_transl['numdoc']; ?></label>
                        <div class="col-sm-8">
							<?php echo ' :'.$form['numdoc']; ?>
                            <input type="hidden" id="numdoc" name="numdoc" value="<?php echo $form['numdoc']; ?>">
                            <input type="hidden" id="numfat" name="numfat" value="<?php echo $form['numfat']; ?>">
                        </div>
                    </div>
                </div>                    
                <div class="col-sm-6 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="datemi" class="col-sm-4 control-label"><?php echo $script_transl['datemi']; ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="datemi" name="datemi" value="<?php echo $form['datemi']; ?>">
                            <input type="hidden" id="datfat" name="datfat" value="<?php echo $form['datfat']; ?>">
                        </div>
                    </div>
                </div>                    
<?php		break;
			case 'ADT': case 'RDL':?>
                <div class="col-sm-6 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="numdoc" class="col-sm-4 control-label"><?php echo $script_transl['numdoc']; ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control"  id="numdoc" name="numdoc" value="<?php echo $form['numdoc']; ?>">
                            <input type="hidden" id="numfat" name="numfat" value="<?php echo $form['numfat']; ?>">
                        </div>
                    </div>
                </div>                    
                <div class="col-sm-6 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="datemi" class="col-sm-4 control-label"><?php echo $script_transl['datemi']; ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="datemi" name="datemi" value="<?php echo $form['datemi']; ?>">
                            <input type="hidden" id="datfat" name="datfat" value="<?php echo $form['datfat']; ?>">
                        </div>
                    </div>
                </div>                    
<?php		break;
			case 'AFA': case 'AFC': case 'AFT': ?>
                <div class="col-sm-6 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="numdoc" class="col-sm-4 control-label"><?php echo $script_transl['numfat']; ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="numfat" name="numfat" value="<?php echo $form['numfat']; ?>">
                            <input type="hidden" id="numdoc" name="numdoc" value="<?php echo $form['numdoc']; ?>">
                        </div>
                    </div>
                </div>                    
                <div class="col-sm-6 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="datfat" class="col-sm-4 control-label"><?php echo $script_transl['datfat']; ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="datfat" name="datfat" value="<?php echo $form['datfat']; ?>">
                            <input type="hidden" id="datemi" name="datemi" value="<?php echo $form['datemi']; ?>">
                        </div>
                    </div>
                </div>                    
<?php	} ?>
                <div class="col-sm-6 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label for="caumag" class="col-sm-4 control-label" ><?php echo $script_transl['caumag']; ?></label>
                        <div>
                            <?php
                            $magazz->selectCaumag($form['caumag'], $docOperat[$form['tipdoc']], false, '', "col-sm-8",1);
                            ?>                
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="pagame" class="col-sm-4 control-label" ><?php echo $script_transl['pagame']; ?></label>
                        <div>
                            <?php 
							$select_pagame = new selectpagame("pagame");
							$select_pagame->addSelected($form["pagame"]);
							$select_pagame->output();
							?>                
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="banapp" class="col-sm-4 control-label" ><?php echo $script_transl['banapp']; ?></label>
                        <div>
                            <?php 
							$select_banapp = new selectbanapp("banapp");
							$select_banapp->addSelected($form["banapp"]);
							$select_banapp->output();
							?>                
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-4 col-lg-4">
                    <div class="form-group">
                        <label for="sconto" class="col-sm-8 control-label"><?php echo $script_transl['sconto']; ?></label>
                        <div class="col-sm-4">
                            <input type="number" step="0.01" max="100" class="form-control" id="sconto" name="sconto" placeholder="<?php echo $script_transl['sconto']; ?>" value="<?php echo $form['sconto']; ?>" onchange="this.form.submit();">
                        </div>
                    </div>
                </div>
            </div> <!-- chiude row  -->
            <div class="row">
            </div> <!-- chiude row  -->
        </div><!-- chiude container  -->
    </div><!-- chiude panel  -->
    <input type="hidden" value="<?php echo $form['in_codice_fornitore']; ?>" name="in_codice_fornitore" />
    <input type="hidden" value="<?php echo $form['in_descri']; ?>" name="in_descri" />
    <input type="hidden" value="<?php echo $form['in_pervat']; ?>" name="in_pervat" />
    <input type="hidden" value="<?php echo $form['in_unimis']; ?>" name="in_unimis" />
    <input type="hidden" value="<?php echo $form['in_prelis']; ?>" name="in_prelis" />
    <input type="hidden" value="<?php echo $form['in_id_mag']; ?>" name="in_id_mag" />
    <input type="hidden" value="<?php echo $form['in_annota']; ?>" name="in_annota" />
    <input type="hidden" value="<?php echo $form['in_pesosp']; ?>" name="in_pesosp" />
    <input type="hidden" value="<?php echo $form['in_quamag']; ?>" name="in_quamag" />
    <input type="hidden" value="<?php echo $form['in_scorta']; ?>" name="in_scorta" />
    <input type="hidden" value="<?php echo $form['in_ritenuta']; ?>" name="in_ritenuta" />
    <input type="hidden" value="<?php echo $form['in_id_orderman']; ?>" name="in_id_orderman" />
    <input type="hidden" value="<?php echo $form['in_gooser']; ?>" name="in_gooser" />
    <input type="hidden" value="<?php echo $form['in_lot_or_serial']; ?>" name="in_lot_or_serial" />
    <input type="hidden" value="<?php echo $form['in_status']; ?>" name="in_status" />
    <input type="hidden" value="<?php echo $form['hidden_req']; ?>" name="hidden_req" />
    <?php
    if (count($form['rows']) > 0) {
        $tot = 0;
		$imprig=0;
		$tot_row=0;
        $form['net_weight'] = 0;
        $form['units'] = 0;
        $form['volume'] = 0;
        foreach ($form['rows'] as $k => $v) {
            // addizione ai totali peso,pezzi,volume
            $artico = gaz_dbi_get_row($gTables['artico'], 'codice', $v['codart']);
            $form['net_weight'] += $v['quanti'] * $artico['peso_specifico'];
            if ($artico['pack_units'] > 0) {
                $form['units'] += intval(round($v['quanti'] / $artico['pack_units']));
            }
            $form['volume'] += $v['quanti'] * $artico['volume_specifico'];
            // fine addizione peso,pezzi,volume
            $btn_class = 'btn-success';
            $btn_title = '';
            $peso = 0;
            if ($v['tiprig'] == 0) {
                if ($artico['good_or_service']>0){ 
					$btn_class = 'btn-info';
					$btn_title = ' Servizio';
				} elseif ($v['quamag'] < 0.00001 && $admin_aziend['conmag']==2) { // se gestisco la contabilità di magazzino controllo presenza articolo
                    $btn_class = 'btn-danger';
					$btn_title = ' ARTICOLO NON DISPONIBILE!!!';
				} elseif ($v['quamag'] <= $v['scorta'] && $admin_aziend['conmag']==2) { // se gestisco la contabilità di magazzino controllo il sottoscorta
                    $btn_class = 'btn-warning';
					$btn_title = ' Articolo sottoscorta: disponibili '.$v['quamag'].'/'.floatval($v['scorta']);
                } else {
                    $btn_class = 'btn-success';
					$btn_title = $v['quamag'].' '.$v['unimis'].' disponibili';
                }
                if ($v['pesosp'] <> 0) {
                    $peso = gaz_format_number($v['quanti'] / $v['pesosp']);
                }
            }

            // calcolo importo totale (iva inclusa) del rigo e creazione castelletto IVA
            if ($v['tiprig'] <= 1) {    //ma solo se del tipo normale o forfait
                if ($v['tiprig'] == 0) { // tipo normale
                    $tot_row = CalcolaImportoRigo($v['quanti'], $v['prelis'], array($v['sconto'], $form['sconto'], -$v['pervat']));
                } else {                 // tipo forfait
                    $tot_row = CalcolaImportoRigo(1, $v['prelis'], -$v['pervat']);
                }
                if (!isset($castel[$v['codvat']])) {
                    $castel[$v['codvat']] = 0.00;
                }
                $castel[$v['codvat']]+=$tot_row;
                // calcolo il totale del rigo stornato dell'iva
                $imprig = round($tot_row / (1 + $v['pervat'] / 100), 2);
                $tot+=$tot_row;
            }
            // fine calcolo importo rigo, totale e castelletto IVA
            // colonne non editabili
            echo "<input type=\"hidden\" value=\"" . $v['status'] . "\" name=\"rows[$k][status]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['codart'] . "\" name=\"rows[$k][codart]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['tiprig'] . "\" name=\"rows[$k][tiprig]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['codvat'] . "\" name=\"rows[$k][codvat]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['pervat'] . "\" name=\"rows[$k][pervat]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['codric'] . "\" name=\"rows[$k][codric]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['id_mag'] . "\" name=\"rows[$k][id_mag]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['annota'] . "\" name=\"rows[$k][annota]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['scorta'] . "\" name=\"rows[$k][scorta]\">\n";
			echo "<input type=\"hidden\" value=\"" . $v['quamag'] . "\" name=\"rows[$k][quamag]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['pesosp'] . "\" name=\"rows[$k][pesosp]\">\n";
            echo '<input type="hidden" value="' . $v['lot_or_serial'] . '" name="rows[' . $k . '][lot_or_serial]" />';
            // colonne editabili
            echo "<input type=\"hidden\" value=\"" . $v['descri'] . "\" name=\"rows[$k][descri]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['unimis'] . "\" name=\"rows[$k][unimis]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['quanti'] . "\" name=\"rows[$k][quanti]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['prelis'] . "\" name=\"rows[$k][prelis]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['sconto'] . "\" name=\"rows[$k][sconto]\">\n";

            echo "<input type=\"hidden\" value=\"" . $v['codice_fornitore'] . "\" name=\"rows[$k][codice_fornitore]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['ritenuta'] . "\" name=\"rows[$k][ritenuta]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['id_orderman'] . "\" name=\"rows[$k][id_orderman]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['gooser'] . "\" name=\"rows[$k][gooser]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['filename'] . "\" name=\"rows[$k][filename]\">\n";

            // creo l'array da passare alla funzione per la creazione della tabella responsive
            $resprow[$k] = array(
                array('head' => $script_transl["nrow"], 'class' => '',
                    'value' => '<button type="image" name="upper_row[' . $k . ']" class="btn btn-default btn-sm" title="' . $script_transl['upper_row'] . '!">
                                ' . ($k + 1) . ' <i class="glyphicon glyphicon-arrow-up"></i></button>'),
                array('head' => $script_transl["codart"], 'class' => '',
                    'value' => ' <button name="upd_row[' . $k . ']" class="btn ' . $btn_class . ' "
					title="' . $script_transl['update'] . $script_transl['thisrow'] . '! ' . $btn_title . '"
					type="submit">
                                <i class="glyphicon glyphicon-refresh"></i>&nbsp;' . $v['codart'] . '
                                </button>',
                    'td_content' => ' title="' . $script_transl['update'] . $script_transl['thisrow'] . '! Sottoscorta =' . $v['scorta'] . '" '
                ),
                array('head' => $script_transl["codice_fornitore"], 'class' => '',
                    'value' => '<input class="gazie-tooltip" data-type="product-thumb" data-id="' . $v["codart"] . '" data-title="' . $v['annota'] . '" type="text" name="rows[' . $k . '][codice_fornitore]" value="' . $v['codice_fornitore'] . '" maxlength="50" />'
                ),
                array('head' => $script_transl["descri"], 'class' => '',
                    'value' => '<input class="gazie-tooltip" data-type="product-thumb" data-id="' . $v["codart"] . '" data-title="' . $v['annota'] . '" type="text" name="rows[' . $k . '][descri]" value="' . $v['descri'] . '" maxlength="100" />'
                ),
                array('head' => $script_transl["unimis"], 'class' => '',
                    'value' => '<input class="gazie-tooltip" data-type="weight" data-id="' . $peso . '" data-title="' . $script_transl['weight'] . '" type="text" name="rows[' . $k . '][unimis]" value="' . $v['unimis'] . '" maxlength="3" size="2" />'
                ),
                array('head' => $script_transl["quanti"], 'class' => 'text-right numeric',
                    'value' => '<input type="number" step="any" class="gazie-tooltip" data-type="weight" data-id="' . $peso . '" data-title="' . $script_transl['weight'] . '" name="rows[' . $k . '][quanti]" value="' . $v['quanti'] . '" style="width:8em;" maxlength="11" size="4" onchange="this.form.submit();" />'
                ),
                array('head' => $script_transl["prezzo"], 'class' => 'text-right numeric',
                    'value' => '<input type="number" step="any" name="rows[' . $k . '][prelis]" value="' . $v['prelis'] . '" style="width:8em;" maxlength="15" size="4" onchange="this.form.submit()" />'
                ),
                array('head' => $script_transl["sconto"], 'class' => 'text-right numeric',
                    'value' => '<input type="number" step="0.01" name="rows[' . $k . '][sconto]" value="' . $v['sconto'] . '" style="width:3.5em;" maxlength="4" size="1" onchange="this.form.submit()" />'),
                array('head' => $script_transl["amount"], 'class' => 'text-right numeric', 'value' => gaz_format_number($imprig), 'type' => ''),
                array('head' => $script_transl["codvat"], 'class' => 'text-center numeric', 'value' => $v['pervat'], 'type' => ''),
                array('head' => $script_transl["total"], 'class' => 'text-right numeric bg-warning', 'value' => gaz_format_number($tot_row), 'type' => ''),
                array('head' => $script_transl["codric"], 'class' => 'text-center', 'value' => $v['codric']),
                array('head' => $script_transl["delete"], 'class' => 'text-center',
                    'value' => '<button type="submit" class="btn btn-default btn-sm btn-elimina" name="del[' . $k . ']" title="' . $script_transl['delete'] . $script_transl['thisrow'] . '"><i class="glyphicon glyphicon-remove"></i></button>')
            );

            switch ($v['tiprig']) {
                case "0":
                    $lm_acc = '';
                    if ($v['lot_or_serial'] > 0 && $v['id_lotmag'] > 0) {
                        $lm->getAvailableLots($v['codart'], $v['id_mag']);
                        $selected_lot = $lm->getLot($v['id_lotmag']);
                        $lm_acc .= '<div><button class="btn btn-xs btn-success" title="clicca per cambiare lotto" type="image"  data-toggle="collapse" href="#lm_dialog' . $k . '">'
                                . 'lot:' . $selected_lot['id']
                                . ' id:' . $selected_lot['identifier']
                                . ' doc:' . $selected_lot['desdoc']
                                . ' - ' . gaz_format_date($selected_lot['datdoc']) . ' <i class="glyphicon glyphicon-tag"></i></button>';
                        if ($v['id_mag'] > 0) {
                            $lm_acc .= ' <a class="btn btn-xs btn-default" href="lotmag_print_cert.php?id_movmag=' . $v['id_mag'] . '" target="_blank"><i class="glyphicon glyphicon-print"></i></a>';
                        }
                        $lm_acc .= '</div>';
                        $lm_acc .= '<div id="lm_dialog' . $k . '" class="collapse" >
                      <div class="form-group">';
                        if (count($lm->available) > 1) {
                            foreach ($lm->available as $v_lm) {
                                if ($v_lm['id'] <> $v['id_lotmag']) {
                                    $lm_acc .= '<div>change to:<button class="btn btn-xs btn-warning" type="image" onclick="this.form.submit();" name="new_lotmag[' . $k . '][' . $v_lm['id_lotmag'] . ']">'
                                            . 'lot:' . $v_lm['id']
                                            . ' id:' . $v_lm['identifier']
                                            . ' doc:' . $v_lm['desdoc']
                                            . ' - ' . gaz_format_date($v_lm['datdoc']) . '</button></div>';
                                }
                            }
                        } else {
                            $lm_acc .= '<div><button class="btn btn-xs btn-danger" type="image" >Non sono disponibili altri lotti</button></div>';
                        }
                        $lm_acc .= '</div>'
                                . '</div>';
                    }
                    $resprow[$k][3]['value'] .= $lm_acc;
                    break;
                case "1":
                    // in caso di rigo forfait non stampo alcune colonne
                    $resprow[$k][4]['value'] = ''; //unimis
                    $resprow[$k][5]['value'] = ''; //quanti
                    // scambio l'input con la colonna dell'importo... 
                    $resprow[$k][6]['value'] = ''; //prelis
                    $resprow[$k][7]['value'] = ''; //sconto
                    $resprow[$k][8]['value'] = $resprow[$k][5]['value'];
                    break;
                case "2":
                    $resprow[$k][4]['value'] = ''; //unimis
                    $resprow[$k][5]['value'] = ''; //quanti
                    $resprow[$k][6]['value'] = ''; //prelis
                    $resprow[$k][7]['value'] = ''; //sconto
                    $resprow[$k][8]['value'] = ''; //quanti
                    $resprow[$k][9]['value'] = ''; //prelis
                    $resprow[$k][10]['value'] = '';
                    $resprow[$k][11]['value'] = '';
                    $resprow[$k][12]['value'] = '';
                    break;
            }
        }
        $gForm->gazResponsiveTable($resprow, 'gaz-responsive-table');
    }
    ?>
    <div class="panel panel-info">
        <div class="container-fluid">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="pill" href="#insrow1"> <?php echo $script_transl['conf_row']; ?> </a></li>
                <li><a data-toggle="pill" href="#insrow2"><i class="glyphicon glyphicon-eye-open"></i> <?php echo $script_transl['other_row']; ?> </a></li>
                <li><a href="#" id="addmodal" href="#myModal" data-toggle="modal" data-target="#edit-modal" class="btn btn-xs btn-default"><i class="glyphicon glyphicon-export"></i><?php echo $script_transl['add_article']; ?></a></li>
            </ul>
        </div><!-- chiude container  -->
        <div class="tab-content form-horizontal">
            <div id="insrow1" class="tab-pane fade in active bg-info">
                <div class="row">
                    <div class="col-sm-6 col-md-1 col-lg-1">
                        <div class="form-group">
                            <label for="tiprig" class="col-sm-4 control-label"><?php echo $script_transl['tiprig']; ?></label>
                            <div class="col-sm-8">
                                <?php $gForm->selTypeRow('in_tiprig', $form['in_tiprig']);
								//$gForm->variousSelect('in_tiprig', $script_transl['tiprig_value'], $form['in_tiprig'], false, true); 
								?>
                            </div>                
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-5 col-lg-5">
                        <div class="form-group">
                            <label for="item" class="col-sm-4 control-label"><?php echo $script_transl['item']; ?></label>
                            <?php
                            $select_artico = new selectartico("in_codart");
                            $select_artico->addSelected($form['in_codart']);
                            $select_artico->output(substr($form['cosear'], 0, 20), 'C', "col-sm-8");
                            ?>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-4 col-lg-4">
                        <div class="form-group">
                            <label for="quanti" class="col-sm-6 control-label"><?php echo $script_transl['quanti']; ?></label>
                            <input class="col-sm-6" type="number" step="any" tabindex=6 value="<?php echo $form['in_quanti']; ?>" name="in_quanti" />
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-2 col-lg-2">
                        <div class="form-group text-center">
                            <button type="submit"  tabindex=7 class="btn btn-default btn-sm col-sm-12" name="in_submit" title="<?php echo $script_transl['submit'] . $script_transl['thisrow']; ?>">
                                <?php echo $script_transl['conf_row']; ?>&nbsp;<i class="glyphicon glyphicon-ok"></i>
                            </button>
                        </div> 
                    </div>
                </div>
            </div><!-- chiude tab-pane  -->
            <div id="insrow2" class="tab-pane fade bg-info">
                <div class="row">
                    <div class="col-sm-6 col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="sconto" class="col-sm-6 control-label"><?php echo $script_transl['sconto']; ?></label>
                            <input class="col-sm-6" type="number" step="0.01" value="<?php echo $form['in_sconto']; ?>" name="in_sconto" />
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="vat_constrain" class="col-sm-6 control-label"><?php echo $script_transl['vat_constrain']; ?></label>
                            <?php $gForm->selectFromDB('aliiva', 'in_codvat', 'codice', $form['in_codvat'], 'codice', true, '-', 'descri', '', 'col-sm-6'); ?>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="codric" class="col-sm-4 control-label"><?php echo $script_transl['codric']; ?></label>
                            <?php
                            $select_codric = new selectconven("in_codric");
                            $select_codric->addSelected($form['in_codric']);
                            $select_codric->output(substr($form['in_codric'], 0, 1), 'col-sm-8');
                            ?>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-3 col-lg-3">
                        <div class="form-group">
                            <label for="provvigione" class="col-sm-6 control-label"><?php echo $script_transl['provvigione']; ?></label>
                            <input class="col-sm-6" type="number" step="any" value="" name="in_provvigione" />
                        </div>
                    </div>
                </div>
            </div><!-- chiude tab-pane  -->
        </div><!-- chiude tab-content  -->
    </div><!-- chiude panel  -->
    <?php
    if (count($form['rows']) > 0) {
        ?>
        <div class="panel panel-success">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr class="small success">              
                            <th>
                                <?php echo $script_transl["taxable"]; ?>
                            </th>
                            <th>
                                <?php echo $script_transl["codvat"]; ?>
                            </th>
                            <th>
                                <?php echo $script_transl["tax"]; ?>
                            </th>
                            <th class="text-center">
                                <?php echo $script_transl["total"]; ?>
                            </th>
                            <th class="text-center">
                                <?php echo $script_transl["net"]; ?>
                            </th>
                            <th>
                                <?php echo $script_transl["units"]; ?>
                            </th>
                            <th>
                                <?php echo $script_transl["volume"]; ?>
                            </th>
                            <th>
                            </th>
                        </tr>      
                    </thead>    
                    <tbody>
                        <?php
                        $last_castle_row = count($castel);
                        foreach ($castel as $k => $v) {
                            $last_castle_row--;
                            $r = gaz_dbi_get_row($gTables['aliiva'], "codice", $k);
                            $impcast = round($v / (1 + $r['aliquo'] / 100), 2);
                            $ivacast = $v - $impcast;
                            if ($last_castle_row == 0) {
                                echo '<tr><td>' . gaz_format_number($impcast) . '</td>'
                                . '<td>' . $r['descri'] . '</td>'
                                . '<td>' . gaz_format_number($ivacast) . '</td>'
                                . '<td class="bg-warning text-center">'
                                . ''
                                . '<div class="col-sm-8"><b>' . $admin_aziend['html_symbol'] . ' ' . gaz_format_number($tot) . '</b></div>'
                                . '<div class="col-sm-2"></div>'
                                . '</td>'
                                . '<td class="text-center">' . gaz_format_number($form['net_weight']) . '</td>'
                                . '<td>' . $form['units'] . '</td>'
                                . '<td>' . gaz_format_number($form['volume']) . '</td>';
                            } else {
                                echo '<tr><td>' . gaz_format_number($impcast) . '</td>'
                                . '<td>' . $r['descri'] . '</td>'
                                . '<td>' . gaz_format_number($ivacast) . '</td>';
                            }
                            echo "</tr>\n";
                        }
                        ?>
                        <tr> 
                            <td colspan="7">
                                <input class="bg-danger center-block" id="preventDuplicate" tabindex=10 onClick="chkSubmit();" type="submit" name="ins" value="<?php 
                                if ($toDo == 'insert'){
                                    echo $script_transl['insert'];
                                } else {
                                    echo $script_transl['update'];
                                } ?>" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
    }
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
</script>
<!-- ENRICO FEDELE - FINE FINESTRA MODALE -->
<?php
require("../../library/include/footer.php");
?>