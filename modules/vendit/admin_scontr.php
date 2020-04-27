<?php

/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2020 - Antonio De Vincentiis Montesilvano (PE)
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
$gForm = new venditForm();
$magazz = new magazzForm();

$ecr_user = gaz_dbi_get_row($gTables['cash_register'], 'adminid', $admin_aziend["user_name"]);
$ecr = $gForm->getECR_userData($admin_aziend["user_name"]);

$operat = $magazz->getOperators();
$lm = new lotmag;

if (!isset($_POST['ritorno'])) {
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
} else {
    $form['ritorno'] = $_POST['ritorno'];
}

if ((isset($_GET['Update']) and ! isset($_GET['id_tes']))) {
    header("Location: " . $form['ritorno']);
    exit;
}

if ((isset($_POST['Update'])) or ( isset($_GET['Update']))) {
    $toDo = 'update';
} else {
    $toDo = 'insert';
}

if ((isset($_POST['Insert'])) or ( isset($_POST['Update']))) {   //se non e' il primo accesso
    //qui si deve fare un parsing di quanto arriva dal browser...
	if (isset($_POST['button_ok_barcode']) || $_POST['ok_barcode']=="ok"){
		$form['ok_barcode']="ok";
	} else {
		$form['ok_barcode']="";
	}
	if (isset ($_POST['no_barcode'])){
		$form['ok_barcode']="no";
		unset ($_POST['in_barcode']);
		$form['ok_barcode']="";
	}
	if (isset ($_POST['in_barcode']) && strlen($_POST['in_barcode'])>0){
		$form['in_barcode']=$_POST['in_barcode'];
		$serbar = gaz_dbi_get_row($gTables['artico'], "barcode", $form['in_barcode']);
		if (!isset($serbar)){
			$form['in_barcode']="NOT FOUND";
		} else {
			$_POST['cosear']=$serbar['codice'];
			$form['in_codart']=$serbar['codice'];
			$_POST['in_codart']=$serbar['codice'];
			$_POST['in_submit']="submit";
			$form['in_barcode']="";
			$form['in_quanti']="1";
			$_POST['in_quanti']="1";
		}
	} else {
		$form['in_barcode']="";
	}
	
	
    $form['id_tes'] = intval($_POST['id_tes']);
    $form['hidden_req'] = $_POST['hidden_req'];
    $form['roundup_y'] = $_POST['roundup_y'];
    $form['clfoco'] = substr($_POST['clfoco'], 0, 13);
    $form['fiscal_code'] = strtoupper(substr(trim($_POST['fiscal_code']), 0, 16));
    foreach ($_POST['search'] as $k => $v) {
        $form['search'][$k] = $v;
    }
    $form['tipdoc'] = strtoupper(substr($_POST['tipdoc'], 0, 3));
    $form['numdoc'] = intval($_POST['numdoc']);
    $form['numfat'] = intval($_POST['numfat']);
    $form['id_cash'] = intval($_POST['id_cash']);
    $form['id_con'] = intval($_POST['id_con']);
    $form['seziva'] = intval($_POST['seziva']);
    $form['listin'] = intval($_POST['listin']);
    $form['datemi'] = substr($_POST['datemi'], 0, 10);
    $form['caumag'] = intval($_POST['caumag']);
    $form['sconto'] = floatval(substr(preg_replace("/\,/", '.', $_POST['sconto']), 0, 5));
    if ($form['sconto'] > 100) {
        $form['sconto'] = 100;
    } elseif ($form['sconto'] < -100) {
        $form['sconto'] = -100;
    }
    $form['address'] = $_POST['address'];
    $form['id_agente'] = intval($_POST['id_agente']);
    $form['pagame'] = intval($_POST['pagame']);

    // se non ho il cliente (nemmeno l'anonimo) azzero i dati
    if ($_POST['clfoco'] < $admin_aziend['mascli']) {
        $form['address'] = '';
        $form['id_agente'] = 0;
        $form['pagame'] = 1;
    };

    // inizio rigo di input
    $form['in_descri'] = $_POST['in_descri'];
    $form['in_tiprig'] = $_POST['in_tiprig'];
    $form['in_codart'] = $_POST['in_codart'];
    $form['in_pervat'] = $_POST['in_pervat'];
    $form['in_unimis'] = $_POST['in_unimis'];
    $form['in_prezzo'] = $_POST['in_prezzo'];
    $form['in_sconto'] = $_POST['in_sconto'];
    $form['in_quanti'] = gaz_format_quantity($_POST['in_quanti'], 0, $admin_aziend['decimal_quantity']);
    $form['in_codvat'] = $_POST['in_codvat'];
    $form['in_codric'] = $_POST['in_codric'];
    $form['in_provvigione'] = $_POST['in_provvigione'];
    $form['in_id_mag'] = $_POST['in_id_mag'];
    $form['in_annota'] = $_POST['in_annota'];
    $form['in_scorta'] = $_POST['in_scorta'];
    $form['in_quamag'] = $_POST['in_quamag'];
    $form['in_pesosp'] = $_POST['in_pesosp'];
    $form['in_lot_or_serial'] = intval($_POST['in_lot_or_serial']);
    $form['in_id_lotmag'] = intval($_POST['in_id_lotmag']);
    $form['in_status'] = $_POST['in_status'];
    $form['cosear'] = $_POST['cosear'];
    // fine rigo input

    $form['rows'] = array();
    $next_row = 0;
    if (isset($_POST['rows'])) {

        foreach ($_POST['rows'] as $next_row => $v) {
            $form['rows'][$next_row]['tiprig'] = intval($v['tiprig']);
            $form['rows'][$next_row]['codart'] = substr($v['codart'], 0, 15);
            $form['rows'][$next_row]['status'] = substr($v['status'], 0, 30);
            $form['rows'][$next_row]['descri'] = substr($v['descri'], 0, 100);
            $form['rows'][$next_row]['unimis'] = substr($v['unimis'], 0, 3);
            if ($v['tiprig'] <= 1) {
                $form['rows'][$next_row]['prelis'] = number_format(floatval(preg_replace("/\,/", '.', $v['prelis'])), $admin_aziend['decimal_price'], '.', '');
            } else {
                $form['rows'][$next_row]['prelis'] = 0;
            }
            $form['rows'][$next_row]['sconto'] = floatval(preg_replace("/\,/", '.', $v['sconto']));
            $form['rows'][$next_row]['quanti'] = gaz_format_quantity($v['quanti'], 0, $admin_aziend['decimal_quantity']);
            $form['rows'][$next_row]['provvigione'] = intval($v['provvigione']);
            $form['rows'][$next_row]['codvat'] = intval($v['codvat']);
            $form['rows'][$next_row]['pervat'] = preg_replace("/\,/", '.', $v['pervat']);
            $form['rows'][$next_row]['codric'] = intval($v['codric']);
            $form['rows'][$next_row]['id_mag'] = intval($v['id_mag']);
            $form['rows'][$next_row]['lot_or_serial'] = intval($v['lot_or_serial']);
            $form['rows'][$next_row]['id_lotmag'] = intval($v['id_lotmag']);
            if ($v['lot_or_serial'] == 2 && $v['id_lotmag'] > 0) {
// se è prevista la gestione per numero seriale/matricola la quantità non può essere diversa da 1 
                if ($form['rows'][$next_row]['quanti'] <> 1) {
                    $msg['war'][] = "serial";
                }
                $form['rows'][$next_row]['quanti'] = 1;
            }
            $form['rows'][$next_row]['annota'] = substr($v['annota'], 0, 50);
            $form['rows'][$next_row]['scorta'] = floatval($v['scorta']);
            $form['rows'][$next_row]['quamag'] = floatval($v['quamag']);
            $form['rows'][$next_row]['pesosp'] = floatval($v['pesosp']);
            if (isset($_POST['upd_row'])) {
                $key_row = key($_POST['upd_row']);
                if ($key_row == $next_row) {
                    $form['in_descri'] = $form['rows'][$key_row]['descri'];
                    $form['in_tiprig'] = $form['rows'][$key_row]['tiprig'];
                    $form['in_codart'] = $form['rows'][$key_row]['codart'];
                    $form['in_pervat'] = $form['rows'][$key_row]['pervat'];
                    $form['in_unimis'] = $form['rows'][$key_row]['unimis'];
                    $form['in_prezzo'] = $form['rows'][$key_row]['prelis'];
                    $form['in_sconto'] = $form['rows'][$key_row]['sconto'];
                    $form['in_quanti'] = $form['rows'][$key_row]['quanti'];
                    //$form['in_codvat'] = $form['rows'][$key_row]['codvat'];
                    $form['in_codric'] = $form['rows'][$key_row]['codric'];
                    $form['in_provvigione'] = $form['rows'][$key_row]['provvigione'];
                    $form['in_id_mag'] = $form['rows'][$key_row]['id_mag'];
                    $form['in_annota'] = $form['rows'][$key_row]['annota'];
                    $form['in_scorta'] = $form['rows'][$key_row]['scorta'];
                    $form['in_quamag'] = $form['rows'][$key_row]['quamag'];
                    $form['in_pesosp'] = $form['rows'][$key_row]['pesosp'];
                    $form['in_lot_or_serial'] = $form['rows'][$key_row]['lot_or_serial'];
                    $form['in_id_lotmag'] = $form['rows'][$key_row]['id_lotmag'];
                    $form['in_status'] = "UPDROW" . $key_row;
                    $form['cosear'] = $form['rows'][$key_row]['codart'];
                    array_splice($form['rows'], $key_row, 1);
                    $next_row--;
                }
            }
            $next_row++;
        }
        $comp = new venditCalc();
        if (isset($_POST['roundup'])) { // richiesta di arrotondamento verso l'alto
            $form['rows'] = $comp->computeRounTo($form['rows'], $form['sconto'], false, $admin_aziend['decimal_price']);
            $form['roundup_y'] = 'disable';
        }
        if (isset($_POST['rounddown'])) { // richiesta di arrotondamento verso il basso
            $form['rows'] = $comp->computeRounTo($form['rows'], $form['sconto'], true, $admin_aziend['decimal_price']);
        }
        // se è stato settato uno sconto chiusura dalla procedura di arrotondamento lo passo
        if (isset($form['rows'][0]['new_body_discount'])) {
            $form['sconto'] = $form['rows'][0]['new_body_discount'];
        }
    }

    // Se viene inviata la richiesta di conferma totale ...
    if (isset($_POST['ins'])) {
        if (!gaz_format_date($form["datemi"], 'chk')) {
            $msg['err'][] = "datemi";
        }
        if ($form["clfoco"] < $admin_aziend['mascli']) { // non c'e' un cliente
            $msg['err'][] = "clfoco";
        } elseif ($form["clfoco"] == $admin_aziend['mascli']) { //  e' un cliente anonimo
            // il pagamento dev'essere contestuale, non si fa credito agli anonimi!
            $payment = gaz_dbi_get_row($gTables['pagame'], 'codice', $form["pagame"]);
            if ($payment['incaut'] <= 100000000) {
                $msg['err'][] = "incaut";
            }
        }
        if (empty($form["pagame"])) {
            $msg['err'][] = "pagame";
        }
        //controllo dei righi e del totale
        $tot = 0;
        $tim = 0;
        foreach ($form['rows'] as $i => $v) {
            if (empty($v['descri']) && $v['quanti'] > 0) {
                $msg['err'][] = "rowdes";
            }
            if (empty($v['unimis']) && $v['quanti'] > 0) {
                $msg['err'][] = "unimis";
            }
            if ($v['tiprig'] <= 1) {    // se del tipo normale o forfait
                if ($v['tiprig'] == 0) { // tipo normale
                    $tim_row = CalcolaImportoRigo($v['quanti'], $v['prelis'], array($v['sconto'], $form['sconto']));
                    $tot_row = CalcolaImportoRigo($v['quanti'], $v['prelis'], array($v['sconto'], $form['sconto'], -$v['pervat']));
                } else {                 // tipo forfait
                    $tim_row = CalcolaImportoRigo($v['quanti'], $v['prelis'], 0);
                    $tot_row = CalcolaImportoRigo(1, $v['prelis'], -$v['pervat']);
                }
                $tot+=$tot_row;
                $tim+=$tim_row;
            }
        }
        if ($tot == 0) {  //il totale e' zero
            $msg['err'][] = "totzer";
        } elseif ($tim >= 3000) { // se il totale supera i 3600 euro
            if ($form["clfoco"] == $admin_aziend['mascli']) {
                $msg['err'][] = "cashlimit";
            }
        }
        if (!empty($form['fiscal_code'])) {  // controllo codice fiscale
            require("../../library/include/check.inc.php");
            $ctrl_cf = new check_VATno_TAXcode();
            $rs_cf = $ctrl_cf->check_TAXcode($form['fiscal_code']);
            if (!empty($rs_cf)) {
                $msg['err'][] = "codfis";
            }
        }
        if (count($msg['err']) < 1) { // ***   nessun errore   ***
            $form['datemi'] = gaz_format_date($form['datemi'], true);
            if (preg_match("/^id_([0-9]+)$/", $form['clfoco'], $match)) {
                $new_clfoco = $anagrafica->getPartnerData($match[1], 1);
                $form['clfoco'] = $anagrafica->anagra_to_clfoco($new_clfoco, $admin_aziend['mascli'],$form['pagame']);
            }
            if ($toDo == 'update') { // e' una modifica
                $old_rows = gaz_dbi_dyn_query("*", $gTables['rigdoc'], "id_tes = " . $form['id_tes'], "id_tes, id_rig");
				
                $i = 0;
                $count = count($form['rows']) - 1;
                while ($val_old_row = gaz_dbi_fetch_array($old_rows)) { 
                    if ($i <= $count) { //se il vecchio rigo e' ancora presente nel nuovo lo modifico
                        $form['rows'][$i]['id_tes'] = $form['id_tes'];
                        rigdocUpdate(array('id_rig', $val_old_row['id_rig']), $form['rows'][$i]);
                        if ($form['rows'][$i]['id_mag'] > 0) { //se il rigo ha un movimento di magazzino associato
                            $magazz->uploadMag($val_old_row['id_rig'], $form['tipdoc'], $form['numdoc'], '', $form['datemi'], $form['clfoco'], $form['sconto'], $form['caumag'], $form['rows'][$i]['codart'], $form['rows'][$i]['quanti'], $form['rows'][$i]['prelis'], $form['rows'][$i]['sconto'], $form['rows'][$i]['id_mag'], $admin_aziend['stock_eval_method'], false, 0, $form['rows'][$i]['id_lotmag']);
                        }
                    } else { //altrimenti lo elimino
                        if (intval($val_old_row['id_mag']) > 0) {  //se c'è stato un movimento di magazzino lo azzero
                            $magazz->uploadMag('DEL', $form['tipdoc'], '', '', '', '', '', '', '', '', '', '', $val_old_row['id_mag'], $admin_aziend['stock_eval_method']);
                        }
                        gaz_dbi_del_row($gTables['rigdoc'], 'id_rig', $val_old_row['id_rig']);
                    }
                    $i++;
                } 
                if($count > $i){//qualora i nuovi righi fossero di più dei vecchi inserisco l'eccedenza
					for ($i = $i; $i <= $count; $i++) {
						$form['rows'][$i]['id_tes'] = $form['id_tes'];
						rigdocInsert($form['rows'][$i]);
						if ($admin_aziend['conmag'] == 2 &&
								$form['rows'][$i]['tiprig'] == 0 &&
								!empty($form['rows'][$i]['codart'])) { //se l'impostazione in azienda prevede l'aggiornamento automatico dei movimenti di magazzino
							$magazz->uploadMag(gaz_dbi_last_id(), $form['tipdoc'], $form['numdoc'], '', $form['datemi'], $form['clfoco'], $form['sconto'], $form['caumag'], $form['rows'][$i]['codart'], $form['rows'][$i]['quanti'], $form['rows'][$i]['prelis'], $form['rows'][$i]['sconto'], 0, $admin_aziend['stock_eval_method'], false, 0, $form['rows'][$i]['id_lotmag']);
						}
					}
				}
                $form['datfat'] = $form['datemi'];
                $form['id_contract'] = $form['id_cash'];
                tesdocUpdate(array('id_tes', $form['id_tes']), $form);
                header("Location: " . $form['ritorno']);
                exit;
            } else { // e' un'inserimento
                $form['template'] = 'FatturaAllegata';
                $form['id_contract'] = $form['id_cash'];
                $form['seziva'] = $form['seziva'];
                $form['spediz'] = $form['fiscal_code'];
                // ricavo il progressivo della cassa del giorno (in id_contract c'è la cassa alla quale invio lo scontrino)
                $rs_last_n = gaz_dbi_dyn_query("numdoc", $gTables['tesdoc'], "tipdoc = 'VCO' AND id_con = 0 AND id_contract = " . $form['id_cash'], 'datemi DESC, numdoc DESC', 0, 1);
                $last_n = gaz_dbi_fetch_array($rs_last_n);
                if ($last_n) {
                    $form['numdoc'] = $last_n['numdoc'] + 1;
                } else {
                    $form['numdoc'] = 1;
                }
                if ($form['clfoco'] > 100000000) {  // cliente selezionato quindi fattura allegata
                    // ricavo l'ultimo numero di fattura dell'anno
                    $rs_last_f = gaz_dbi_dyn_query("numfat*1 AS fattura", $gTables['tesdoc'], "YEAR(datfat) = " . substr($form['datemi'], 0, 4) . " AND tipdoc = 'VCO' AND seziva = " . $form['seziva'], 'fattura DESC', 0, 1);
                    $last_f = gaz_dbi_fetch_array($rs_last_f);
                    if ($last_f) {
                        $form['numfat'] = $last_f['fattura'] + 1;
                    } else {
                        $form['numfat'] = 1;
                    }
                    $form['datfat'] = $form['datemi'];
                }
                tesdocInsert($form);
                $last_id = gaz_dbi_last_id();
                //inserisco i righi
                foreach ($form['rows'] as $v) {
                    $v['id_tes'] = $last_id;
                    $last_rigdoc_id=rigdocInsert($v);
                    if ($admin_aziend['conmag'] == 2 and
                            $v['tiprig'] == 0 and ! empty($v['codart'])) { //se l'impostazione in azienda prevede l'aggiornamento automatico dei movimenti di magazzino
                        $id_mag=$magazz->uploadMag(gaz_dbi_last_id(), $form['tipdoc'], $form['numdoc'], '', $form['datemi'], $form['clfoco'], $form['sconto'], $form['caumag'], $v['codart'], $v['quanti'], $v['prelis'], $v['sconto'], 0, $admin_aziend['stock_eval_method'], false, 0, $v['id_lotmag']
                        );
						gaz_dbi_put_row($gTables['rigdoc'], 'id_rig', $last_rigdoc_id, 'id_mag', $id_mag); // inserisco il riferimento mov mag nel rigo doc
	                }
                }
				if ($ecr_user){ // se è un utente abilitato all'invio all'ecr procedo in tal senso , altrimenti genererò un file XML dopo aver contabilizzato
                    // INIZIO l'invio dello scontrino alla stampante fiscale dell'utente
                    require("../../library/cash_register/" . $ecr['driver'] . ".php");
                    $ticket_printer = new $ecr['driver'];
                    $ticket_printer->set_serial($ecr['serial_port']);
                    $ticket_printer->open_ticket();
                    $ticket_printer->set_cashier($admin_aziend['Nome']);
                    $tot = 0;
                    foreach ($form['rows'] as $i => $v) {
                        if ($v['tiprig'] <= 1) {    // se del tipo normale o forfait
                            if ($v['tiprig'] == 0) { // tipo normale
                                $tot_row = CalcolaImportoRigo($v['quanti'], $v['prelis'], array($v['sconto'], $form['sconto'], -$v['pervat']));
                            } else {                 // tipo forfait
                                $tot_row = CalcolaImportoRigo(1, $v['prelis'], -$v['pervat']);
                                $v['quanti'] = 1;
                                $v['codart'] = $v['descri'];
                            }
                            $price = $v['quanti'] . 'x' . round($tot_row / $v['quanti'], $admin_aziend['decimal_price']);
                            $ticket_printer->row_ticket($tot_row, $price, $v['codvat'], $v['codart']);
                            $tot+=$tot_row;
                        } else {                    // se descrittivo
                            $desc_arr = str_split(trim($v['descri']), 24);
                            foreach ($desc_arr as $d_v) {
                                $ticket_printer->descri_ticket($d_v);
                            }
                        }
                    }
                    if (!empty($form['fiscal_code'])) { // è stata impostata la stampa del codice fiscale
                        $ticket_printer->descri_ticket('CF= ' . $form['fiscal_code']);
                    }
                    $ticket_printer->pay_ticket();
                    $ticket_printer->close_ticket();
					// FINE invio
				}
                if ($form['clfoco'] > 100000000) {
                    // procedo alla stampa della fattura solo se c'è un cliente selezionato
                    $_SESSION['print_request'] = $last_id;
                    header("Location: invsta_docven.php");
                    exit;
                } else {
                    header("Location: report_scontr.php");
                    exit;
                }
            }
        }
    }

    // Se viene inviata la richiesta di conferma cliente
    if ($_POST['hidden_req'] == 'clfoco') {
        if (preg_match("/^id_([0-9]+)$/", $form['clfoco'], $match)) {
            $cliente = $anagrafica->getPartnerData($match[1], 1);
        } else {
            $cliente = $anagrafica->getPartner($form['clfoco']);
        }
        $form['pagame'] = $cliente['codpag'];
        $form['fiscal_code'] = $cliente['codfis'];
        $form['address'] = $cliente['indspe'] . ' ' . $cliente['citspe'];
        $form['id_agente'] = $cliente['id_agente'];
        $form['in_codvat'] = $cliente['aliiva'];
        $form['hidden_req'] = '';
    }

    // Se viene inviata la richiesta di conferma rigo
    /** ENRICO FEDELE */
    /* Con button non funziona _x */
    //if (isset($_POST['in_submit_x'])) {
    /** ENRICO FEDELE */
    if (isset($_POST['in_submit'])) {
        $artico = gaz_dbi_get_row($gTables['artico'], "codice", $form['in_codart']);
        if (substr($form['in_status'], 0, 6) == "UPDROW") { //se è un rigo da modificare
            $old_key = intval(substr($form['in_status'], 6));
            $form['rows'][$old_key]['tiprig'] = $form['in_tiprig'];
            $form['rows'][$old_key]['descri'] = $form['in_descri'];
            $form['rows'][$old_key]['lot_or_serial'] = $form['in_lot_or_serial'];
            $form['rows'][$old_key]['id_lotmag'] = $form['in_id_lotmag'];
            $form['rows'][$old_key]['id_mag'] = $form['in_id_mag'];
            $form['rows'][$old_key]['status'] = "UPDATE";
            $form['rows'][$old_key]['unimis'] = $form['in_unimis'];
            $form['rows'][$old_key]['quanti'] = $form['in_quanti'];
            $form['rows'][$old_key]['codart'] = $form['in_codart'];
            $form['rows'][$old_key]['codric'] = $form['in_codric'];
            $form['rows'][$old_key]['provvigione'] = $form['in_provvigione'];
            $form['rows'][$old_key]['prelis'] = number_format($form['in_prezzo'], $admin_aziend['decimal_price'], '.', '');
            $form['rows'][$old_key]['sconto'] = $form['in_sconto'];
            if ($artico['aliiva'] > 0) {
                $form['rows'][$old_key]['codvat'] = $artico['aliiva'];
                $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $artico['aliiva']);
                $form['rows'][$old_key]['pervat'] = $iva_row['aliquo'];
                $form['rows'][$old_key]['tipiva'] = $iva_row['tipiva'];
            }
            if ($form['in_codvat'] > 0) {
                $form['rows'][$old_key]['codvat'] = $form['in_codvat'];
                $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $form['in_codvat']);
                $form['rows'][$old_key]['pervat'] = $iva_row['aliquo'];
                $form['rows'][$old_key]['tipiva'] = $iva_row['tipiva'];
            }
            /* $form['rows'][$old_key]['codvat'] = $form['in_codvat'];
              $pervat=gaz_dbi_get_row($gTables['aliiva'],"codice",$form['in_codvat']);
              $form['rows'][$old_key]['pervat'] = $pervat['aliquo']; */
            $form['rows'][$old_key]['annota'] = '';
            $mv = $magazz->getStockValue(false, $form['in_codart'], gaz_format_date($form['datemi'], true), $admin_aziend['stock_eval_method']);
            $magval = array_pop($mv);
            $form['rows'][$old_key]['scorta'] = $artico['scorta'];
            $form['rows'][$old_key]['quamag'] = $magval['q_g'];
            $form['rows'][$old_key]['pesosp'] = '';
            if ($form['in_tiprig'] == 0 and ! empty($form['in_codart'])) {  //rigo normale
                $form['rows'][$old_key]['annota'] = $artico['annota'];
                $form['rows'][$old_key]['pesosp'] = $artico['peso_specifico'];
                $form['rows'][$old_key]['unimis'] = $artico['unimis'];
                $form['rows'][$old_key]['descri'] = $artico['descri'];
                if ($form['listin'] == 2) {
                    $form['rows'][$old_key]['prelis'] = number_format($artico['preve2'], $admin_aziend['decimal_price'], '.', '');
                } elseif ($form['listin'] == 3) {
                    $form['rows'][$old_key]['prelis'] = number_format($artico['preve3'], $admin_aziend['decimal_price'], '.', '');
                } elseif ($form['listin'] == 4) {
                    $form['rows'][$old_key]['prelis'] = number_format($artico['preve4'], $admin_aziend['decimal_price'], '.', '');
                } elseif ($form['listin'] == 5) {
                    $form['rows'][$old_key]['prelis'] = number_format($artico['web_price'], $admin_aziend['decimal_price'], '.', '');
                } else {
                    $form['rows'][$old_key]['prelis'] = number_format($artico['preve1'], $admin_aziend['decimal_price'], '.', '');
                }
                $mv = $magazz->getStockValue(false, $form['in_codart'], gaz_format_date($form['datemi'], true), $admin_aziend['stock_eval_method']);
                $magval = array_pop($mv);
                $form['rows'][$old_key]['scorta'] = $artico['scorta'];
                $form['rows'][$old_key]['quamag'] = $magval['q_g'];
            } elseif ($form['in_tiprig'] == 1) { //rigo forfait
                $form['rows'][$old_key]['codart'] = "";
                $form['rows'][$old_key]['unimis'] = "";
                $form['rows'][$old_key]['quanti'] = 0;
                $form['rows'][$old_key]['sconto'] = 0;
            } else { // rigo descrittivo
                $form['rows'][$old_key]['codart'] = "";
                $form['rows'][$old_key]['annota'] = "";
                $form['rows'][$old_key]['pesosp'] = "";
                $form['rows'][$old_key]['unimis'] = "";
                $form['rows'][$old_key]['quanti'] = 0;
                $form['rows'][$old_key]['prelis'] = 0;
                $form['rows'][$old_key]['codric'] = 0;
                $form['rows'][$old_key]['sconto'] = 0;
                $form['rows'][$old_key]['pervat'] = 0;
                $form['rows'][$old_key]['codvat'] = 0;
            }
            ksort($form['rows']);
        } else { //se è un rigo da inserire
            $form['rows'][$next_row]['tiprig'] = $form['in_tiprig'];
            $form['rows'][$next_row]['descri'] = $form['in_descri'];
            $form['rows'][$next_row]['lot_or_serial'] = 0;
            $form['rows'][$next_row]['id_lotmag'] = 0;
            $form['rows'][$next_row]['id_mag'] = $form['in_id_mag'];
            $form['rows'][$next_row]['status'] = "INSERT";
            $form['rows'][$next_row]['scorta'] = 0;
            $form['rows'][$next_row]['quamag'] = 0;
            if ($form['in_tiprig'] == 0) {  //rigo normale
                $form['rows'][$next_row]['codart'] = $form['in_codart'];
                $form['rows'][$next_row]['annota'] = $artico['annota'];
                $form['rows'][$next_row]['pesosp'] = $artico['peso_specifico'];
                $form['rows'][$next_row]['lot_or_serial'] = $artico['lot_or_serial'];
                $form['rows'][$next_row]['descri'] = $artico['descri'];
                $form['rows'][$next_row]['unimis'] = $artico['unimis'];
                $form['rows'][$next_row]['prelis'] = number_format($form['in_prezzo'], $admin_aziend['decimal_price'], '.', '');
                $form['rows'][$next_row]['codric'] = $form['in_codric'];
                $form['rows'][$next_row]['quanti'] = $form['in_quanti'];
                $form['rows'][$next_row]['sconto'] = $form['in_sconto'];
                $provvigione = new Agenti;
                $form['rows'][$next_row]['provvigione'] = $provvigione->getPercent($form['id_agente'], $form['in_codart']);
                $form['rows'][$next_row]['codvat'] = $admin_aziend['preeminent_vat'];
                $iva_azi = gaz_dbi_get_row($gTables['aliiva'], "codice", $admin_aziend['preeminent_vat']);
                $form['rows'][$next_row]['pervat'] = $iva_azi['aliquo'];
                if ($artico['aliiva'] > 0) {
                    $form['rows'][$next_row]['codvat'] = $artico['aliiva'];
                    $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $artico['aliiva']);
                    $form['rows'][$next_row]['pervat'] = $iva_row['aliquo'];
                }
                if ($form['in_codvat'] > 0) {
                    $form['rows'][$next_row]['codvat'] = $form['in_codvat'];
                    $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $form['in_codvat']);
                    $form['rows'][$next_row]['pervat'] = $iva_row['aliquo'];
                }
                if ($form['listin'] == 2) {
                    $price = $artico['preve2'];
                } elseif ($form['listin'] == 3) {
                    $price = $artico['preve3'];
                } elseif ($form['listin'] == 4) {
                    $price = $artico['preve4'];
                } elseif ($form['listin'] == 5) {
                    $price = $artico['web_price'];
                } else {
                    $price = $artico['preve1'];
                }
                $form['rows'][$next_row]['prelis'] = number_format($price, $admin_aziend['decimal_price'], '.', '');
                if ($artico['codcon'] > 0) {
                    $form['rows'][$next_row]['codric'] = $artico['codcon'];
                    $form['in_codric'] = $artico['codcon'];
                } elseif (!empty($artico['codice'])) {
                    $form['rows'][$next_row]['codric'] = $admin_aziend['impven'];
                    $form['in_codric'] = $admin_aziend['impven'];
                }
                $mv = $magazz->getStockValue(false, $form['in_codart'], gaz_format_date($form['datemi'], true), $admin_aziend['stock_eval_method']);
                $magval = array_pop($mv);
                $form['rows'][$next_row]['scorta'] = $artico['scorta'];
                $form['rows'][$next_row]['quamag'] = $magval['q_g'];
                if ($artico['lot_or_serial'] > 0) {
                    $lm->getAvailableLots($form['in_codart'], $form['in_id_mag']);
                    $ld = $lm->divideLots($form['in_quanti']);
                    /* ripartisco la quantità introdotta tra i vari lotti disponibili per l'articolo
                     * e se è il caso creo più righi  
                     */
                    $i = $next_row;
                    foreach ($lm->divided as $k => $v) {
                        if ($v['qua'] >= 0.00001) {
                            $form['rows'][$i] = $form['rows'][$next_row]; // copio il rigo di origine
                            $form['rows'][$i]['id_lotmag'] = $k; // setto il lotto 
                            $form['rows'][$i]['quanti'] = $v['qua']; // e la quantità in base al riparto
                            $i++;
                        }
                    }
                }
            } elseif ($form['in_tiprig'] == 1) { //forfait
                $form['rows'][$next_row]['codart'] = "";
                $form['rows'][$next_row]['annota'] = "";
                $form['rows'][$next_row]['pesosp'] = "";
                $form['rows'][$next_row]['unimis'] = "";
                $form['rows'][$next_row]['quanti'] = 0;
                $form['rows'][$next_row]['prelis'] = 0;
                $form['rows'][$next_row]['codric'] = $form['in_codric'];
                $form['rows'][$next_row]['sconto'] = 0;
                $form['rows'][$next_row]['codvat'] = $admin_aziend['preeminent_vat'];
                $iva_azi = gaz_dbi_get_row($gTables['aliiva'], "codice", $admin_aziend['preeminent_vat']);
                $form['rows'][$next_row]['pervat'] = $iva_azi['aliquo'];
                if ($form['in_codvat'] > 0) {
                    $form['rows'][$next_row]['codvat'] = $form['in_codvat'];
                    $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $form['in_codvat']);
                    $form['rows'][$next_row]['pervat'] = $iva_row['aliquo'];
                }
                $provvigione = new Agenti;
                $form['rows'][$next_row]['provvigione'] = $provvigione->getPercent($form['id_agente']);
            } elseif ($form['in_tiprig'] == 2) { //descrittivo
                $form['rows'][$next_row]['codart'] = "";
                $form['rows'][$next_row]['annota'] = "";
                $form['rows'][$next_row]['pesosp'] = "";
                $form['rows'][$next_row]['unimis'] = "";
                $form['rows'][$next_row]['quanti'] = 0;
                $form['rows'][$next_row]['prelis'] = 0;
                $form['rows'][$next_row]['codric'] = 0;
                $form['rows'][$next_row]['sconto'] = 0;
                $form['rows'][$next_row]['pervat'] = 0;
                $form['rows'][$next_row]['codvat'] = 0;
                $form['rows'][$next_row]['provvigione'] = 0;
            }
        }
        // reinizializzo rigo di input tranne che tipo rigo, aliquota iva e conto ricavo
        $form['in_descri'] = "";
        $form['in_codart'] = "";
        $form['in_unimis'] = "";
        $form['in_prezzo'] = 0;
        $form['in_sconto'] = 0;
        $form['in_quanti'] = 0;
        $form['in_id_mag'] = 0;
        $form['in_annota'] = "";
        $form['in_scorta'] = 0;
        $form['in_quamag'] = 0;
        $form['in_pesosp'] = 0;
        $form['in_status'] = "INSERT";
        $form['cosear'] = "";
        // fine reinizializzo rigo input
        $next_row++;
    }

    // Se viene inviata la richiesta di spostamento verso l'alto del rigo
    if (isset($_POST['upper_row'])) {
        $upp_key = key($_POST['upper_row']);
        $k_next = $upp_key - 1;
        if ($upp_key > 0) {
            $new_key = $upp_key - 1;
        } else {
            $new_key = $next_row - 1;
        }
        $pull_row = $form['rows'][$new_key];
        $form['rows'][$new_key] = $form['rows'][$upp_key];
        $form['rows'][$upp_key] = $pull_row;
        ksort($form['rows']);
        unset($pull_row);
    }
    if (isset($_POST['new_lotmag'])) {
        // assegno il rigo ad un nuovo lotto
        $row_lm = key($_POST['new_lotmag']);
        $form['rows'][$row_lm]['id_lotmag'] = key($_POST['new_lotmag'][$row_lm]);
    }
    // Se viene inviata la richiesta elimina il rigo corrispondente
    if (isset($_POST['del'])) {
        $delri = key($_POST['del']);
        array_splice($form['rows'], $delri, 1);
        $next_row--;
    }
} elseif ((!isset($_POST['Update'])) and ( isset($_GET['Update']))) { //se e' il primo accesso per UPDATE
	$form['in_barcode']="";
	$form['ok_barcode']="";
    $tesdoc = gaz_dbi_get_row($gTables['tesdoc'], "id_tes", intval($_GET['id_tes']));
    $cliente = $anagrafica->getPartner($tesdoc['clfoco']);
    $form['hidden_req'] = '';
    $form['roundup_y'] = '';
    $form['id_tes'] = $tesdoc['id_tes'];
    $form['tipdoc'] = $tesdoc['tipdoc'];
    $form['numdoc'] = $tesdoc['numdoc'];
    $form['id_cash'] = $tesdoc['id_contract'];
    $form['seziva'] = $tesdoc['seziva'];
    $form['id_con'] = $tesdoc['id_con'];
    $form['numfat'] = $tesdoc['numfat'];
    $form['clfoco'] = $tesdoc['clfoco'];
    // uso impropriamente la colonna spediz per mettere il codice fiscale inserito manualmente
    $form['fiscal_code'] = $tesdoc['spediz'];
    $form['search']['clfoco'] = substr($cliente['ragso1'], 0, 6);
    $form['id_agente'] = $tesdoc['id_agente'];
    $provvigione = new Agenti;
    $form['in_provvigione'] = $provvigione->getPercent($form['id_agente']);
    $form['listin'] = $tesdoc['listin'];
    $form['datemi'] = gaz_format_date($tesdoc['datemi'], false, false);
    $form['sconto'] = $tesdoc['sconto'];
    $form['address'] = $cliente['indspe'] . ' ' . $cliente['citspe'];
    $form['pagame'] = $tesdoc['pagame'];
    $form['caumag'] = $tesdoc['caumag'];

    // inizio rigo di input
    $form['in_descri'] = "";
    $form['in_tiprig'] = 0;
    $form['in_codart'] = "";
    $form['in_pervat'] = 0;
    $form['in_unimis'] = "";
    $form['in_prezzo'] = 0;
    $form['in_sconto'] = 0;
    $form['in_quanti'] = 0;
    $form['in_codvat'] = 0;
    $form['in_codric'] = $admin_aziend['impven'];
    $form['in_id_mag'] = 0;
    $form['in_annota'] = "";
    $form['in_scorta'] = 0;
    $form['in_quamag'] = 0;
    $form['in_pesosp'] = 0;
    $form['in_lot_or_serial'] = 0;
    $form['in_id_lotmag'] = 0;
    $form['in_status'] = "INSERT";
    $form['cosear'] = "";
    // fine rigo input
    // recupero i righi
    $rs_rows = gaz_dbi_dyn_query("*", $gTables['rigdoc'], "id_tes = " . intval($_GET['id_tes']), "id_rig");
    $next_row = 0;
    while ($r = gaz_dbi_fetch_array($rs_rows)) {
        $articolo = gaz_dbi_get_row($gTables['artico'], "codice", $r['codart']);
        $form['rows'][$next_row]['descri'] = $r['descri'];
        $form['rows'][$next_row]['tiprig'] = $r['tiprig'];
        $form['rows'][$next_row]['codart'] = $r['codart'];
        $form['rows'][$next_row]['pervat'] = $r['pervat'];
        $form['rows'][$next_row]['unimis'] = $r['unimis'];
        $form['rows'][$next_row]['prelis'] = number_format($r['prelis'], $admin_aziend['decimal_price'], '.', '');
        $form['rows'][$next_row]['sconto'] = $r['sconto'];
        $form['rows'][$next_row]['quanti'] = gaz_format_quantity($r['quanti'], 0, $admin_aziend['decimal_quantity']);
        $form['rows'][$next_row]['codvat'] = $r['codvat'];
        $form['rows'][$next_row]['codric'] = $r['codric'];
        $form['rows'][$next_row]['provvigione'] = $r['provvigione'];
        $form['rows'][$next_row]['id_mag'] = $r['id_mag'];
        $form['rows'][$next_row]['annota'] = $articolo['annota'];
        $mv = $magazz->getStockValue(false, $r['codart'], gaz_format_date($form['datemi'], true), $admin_aziend['stock_eval_method']);
        $magval = array_pop($mv);
        $form['rows'][$next_row]['scorta'] = $articolo['scorta'];
        $form['rows'][$next_row]['quamag'] = $magval['q_g'];
        $form['rows'][$next_row]['pesosp'] = $articolo['peso_specifico'];
        $form['rows'][$next_row]['lot_or_serial'] = $articolo['lot_or_serial'];
        $movmag = gaz_dbi_get_row($gTables['movmag'], "id_mov", $r['id_mag']);
        $form['rows'][$next_row]['id_lotmag'] = $movmag['id_lotmag'];
        $form['rows'][$next_row]['status'] = "UPDATE";
        $next_row++;
    }
} elseif (!isset($_POST['Insert'])) { //se e' il primo accesso per INSERT
	$form['in_barcode']="";
	$form['ok_barcode']="";
 	if (!$ecr_user) { // creerò un XML con id_cash '0' oppure invierò all'ecr (RT)
		$form['id_cash'] = 0;
		$form['seziva'] = 1;
	}else {
		$form['id_cash'] = $ecr['id_cash'];
		$form['seziva'] = $ecr['seziva'];
	}
    $form['ritorno'] = 0;
    $form['id_tes'] = 0;
    $form['tipdoc'] = 'VCO';
    $form['numdoc'] = 0;
    $form['numfat'] = 0;
    $form['id_con'] = 0;
    $form['listin'] = 1;
    $form['datemi'] = date("d/m/Y");
    $form['clfoco'] = $admin_aziend['mascli'];
    $form['fiscal_code'] = '';
    $form['search']['clfoco'] = '';
    $form['caumag'] = 0;
    $form['sconto'] = 0.00;
    $form['pagame'] = 0;
    $form['address'] = '';
    $form['caumag'] = 0;
    $form['id_agente'] = 0;
    $form['rows'] = array();
    $next_row = 0;
    $form['hidden_req'] = '';
    $form['roundup_y'] = '';
    // inizio rigo di input
    $form['in_descri'] = "";
    $form['in_tiprig'] = 0;
    $form['in_codart'] = "";
    $form['in_pervat'] = 0;
    $form['in_unimis'] = "";
    $form['in_prezzo'] = 0;
    $form['in_sconto'] = 0;
    $form['in_provvigione'] = 0;
    $form['in_quanti'] = 0;
    $form['in_codvat'] = 0;
    $form['in_codric'] = $admin_aziend['impven'];
    $form['in_id_mag'] = 0;
    $form['in_annota'] = "";
    $form['in_scorta'] = 0;
    $form['in_quamag'] = 0;
    $form['in_pesosp'] = 0;
    $form['in_lot_or_serial'] = 0;
    $form['in_id_lotmag'] = 0;
    $form['in_status'] = "INSERT";
    $form['cosear'] = "";
    // fine rigo input
    // ALLERTO SE NON SONO PASSATI OLTRE 10 GIORNI DALLA  LA CHIUSURA/CONTABILIZZAZIONE IN CASO DI XML OPPURE QUELLA DEL GIORNO PRECEDENTE IN PRESENZA DI REGISTRATORE DI CASSA
 	if(!$ecr_user){ // creerò un XML con id_cash '0' oppure invierò all'ecr (RT)
		$rs_no_accounted = gaz_dbi_dyn_query("datemi", $gTables['tesdoc'], "id_con = 0 AND tipdoc = 'VCO' AND datemi < DATE_SUB('" . date("Y-m-d") . "',INTERVAL 10 DAY) AND tipdoc = 'VCO'", 'id_tes', 0, 1);
		$no_accounted = gaz_dbi_fetch_array($rs_no_accounted);
		if ($no_accounted) {
			$msg['err'][] = "ecrc10";
		}
	}else{
		$rs_no_accounted = gaz_dbi_dyn_query("datemi", $gTables['tesdoc'], "id_con = 0 AND tipdoc = 'VCO' AND datemi < " . date("Ymd") . " AND tipdoc = 'VCO'", 'id_tes', 0, 1);
		$no_accounted = gaz_dbi_fetch_array($rs_no_accounted);
		if ($no_accounted) {
			$msg['err'][] = "ecrclo";
		}
	}
    // FINE ALLERTAMENTO
}

require("../../library/include/header.php");
$script_transl = HeadMain(0, array('custom/autocomplete'));
?>
<script type="text/javascript">
    $(function () {
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
<form class="form-horizontal" role="form" method="post" name="docven" enctype="multipart/form-data" >
    <input type="hidden" name="<?php echo ucfirst($toDo); ?>" value="">
    <input type="hidden" value="<?php echo $form['id_tes']; ?>" name="id_tes">
    <input type="hidden" value="<?php echo $form['tipdoc']; ?>" name="tipdoc">
    <input type="hidden" value="<?php echo $form['numfat']; ?>" name="numfat">
    <input type="hidden" value="<?php echo $form['id_cash']; ?>" name="id_cash">
    <input type="hidden" value="<?php echo $form['seziva']; ?>" name="seziva">
    <input type="hidden" value="<?php echo $form['id_con']; ?>" name="id_con">
    <input type="hidden" value="<?php echo $form['fiscal_code']; ?>" name="fiscal_code">
    <input type="hidden" value="<?php echo $form['address']; ?>" name="address">
    <input type="hidden" value="<?php echo $form['ritorno']; ?>" name="ritorno">
    <input type="hidden" value="<?php echo $form['roundup_y']; ?>" name="roundup_y">
	<input type="hidden" value="<?php echo (isset($_POST['last_focus']) ? $_POST['last_focus'] : ""); ?>" name="last_focus" />
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
                if ($form['id_tes'] > 0) { // è una modifica
                    ?>
                    <?php echo $script_transl['upd_this']; ?>
                    <input type="text" name="numdoc" value="<?php echo $form['numdoc']; ?>" style="text-align:right" maxlength="9"  onchange="this.form.submit()" />
                    <?php
                } else {
                    ?>
                    <input type="hidden" value="" name="numdoc">
                    <input type="hidden" value="<?php echo $script_transl['confirm']; ?>" id="confirmSubmit">
                    <?php
                    echo $script_transl['ins_this'];
                }
                $select_cliente = new selectPartner('clfoco');
                $select_cliente->selectDocPartner('clfoco', $form['clfoco'], $form['search']['clfoco'], 'clfoco', $script_transl['search_customer'], $admin_aziend['mascli'], $admin_aziend['mascli']);
                ?>
            </b> 
        </p>
    </div>
    <div class="panel panel-default">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label for="address" class="col-sm-4 control-label"><?php echo $script_transl['address']; ?></label>
                        <div class="col-sm-8"><?php echo $form['address']; ?></div>                
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label for="datemi" class="col-sm-4 control-label"><?php echo $script_transl['datemi']; ?></label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="datemi" name="datemi" value="<?php echo $form['datemi']; ?>">
                        </div>
                    </div>
                </div>                    
                <div class="col-sm-6 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label for="id_agente" class="col-sm-4 control-label" ><?php echo $script_transl['id_agente']; ?></label>
                        <div>
                            <?php
                            $select_agente = new selectAgente("id_agente");
                            $select_agente->addSelected($form["id_agente"]);
                            $select_agente->output("col-sm-8");
                            ?>                
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label for="pagame" class="col-sm-4 control-label" ><?php echo $script_transl['pagame']; ?></label>
                        <div>
                            <?php $gForm->ticketPayments('pagame', $form['pagame'], "col-sm-8"); ?>                
                        </div>
                    </div>
                </div>
            </div> <!-- chiude row  -->
            <div class="row">
                <div class="col-sm-6 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label for="caumag" class="col-sm-4 control-label" ><?php echo $script_transl['caumag']; ?></label>
                        <div>
                            <?php
                            $magazz->selectCaumag($form['caumag'], $operat[$form['tipdoc']], false, '', "col-sm-8");
                            ?>                
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label for="listin" class="col-sm-4 control-label" ><?php echo $script_transl['listin']; ?></label>
                        <div>
                            <?php
                            $gForm->selectNumber('listin', $form['listin'], 0, 1, 5, 'col-sm-8');
                            ?>                
                        </div>
                    </div>
                </div>                    
                <div class="col-sm-6 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label for="sconto" class="col-sm-8 control-label"><?php echo $script_transl['sconto']; ?></label>
                        <div class="col-sm-4">
                            <input type="number" step="0.01" max="100" class="form-control" id="sconto" name="sconto" placeholder="<?php echo $script_transl['sconto']; ?>" value="<?php echo $form['sconto']; ?>" onchange="this.form.submit();">
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3 col-lg-3">
                    <div class="form-group">
                        <label for="seziva" class="col-sm-4 control-label"><?php echo $script_transl['seziva']; ?></label>
                        <div class="col-sm-8">
                            <?php $gForm->selectNumber('seziva', $form['seziva'], 0, 1, 5, 'col-sm-8'); ?>
                        </div>
                    </div>
                </div>
            </div> <!-- chiude row  -->
        </div><!-- chiude container  -->
    </div><!-- chiude panel  -->
    <?php
    echo "<input type=\"hidden\" value=\"" . $form['in_descri'] . "\" name=\"in_descri\" />
        <input type=\"hidden\" value=\"" . $form['in_pervat'] . "\" name=\"in_pervat\" />
        <input type=\"hidden\" value=\"" . $form['in_unimis'] . "\" name=\"in_unimis\" />
        <input type=\"hidden\" value=\"" . $form['in_prezzo'] . "\" name=\"in_prezzo\" />
        <input type=\"hidden\" value=\"" . $form['in_id_mag'] . "\" name=\"in_id_mag\" />
        <input type=\"hidden\" value=\"" . $form['in_annota'] . "\" name=\"in_annota\" />
        <input type=\"hidden\" value=\"" . $form['in_scorta'] . "\" name=\"in_scorta\" />
        <input type=\"hidden\" value=\"" . $form['in_quamag'] . "\" name=\"in_quamag\" />
        <input type=\"hidden\" value=\"" . $form['in_pesosp'] . "\" name=\"in_pesosp\" />
        <input type=\"hidden\" value=\"" . $form['in_lot_or_serial'] . "\" name=\"in_lot_or_serial\" />
        <input type=\"hidden\" value=\"" . $form['in_id_lotmag'] . "\" name=\"in_id_lotmag\" />
        <input type=\"hidden\" value=\"" . $form['in_status'] . "\" name=\"in_status\" />
        <input type=\"hidden\" value=\"" . $form['hidden_req'] . "\" name=\"hidden_req\" />
		<input type=\"hidden\" value=\"" . $form['ok_barcode'] . "\" name=\"ok_barcode\" />";
    if ($next_row > 0) {
        $tot = 0;
        $form['net_weight'] = 0;
        $form['units'] = 0;
        $form['volume'] = 0;
		$vp = gaz_dbi_get_row($gTables['company_config'], 'var', 'vat_price')['val'];
        foreach ($form['rows'] as $k => $v) {
			// se voglio inserire manualmente il prezzo IVA compresa (configurazione avanzata azienda) attivo il form modale
			$ivacomp=($vp>0)?' onclick="vatPrice(\''.$k.'\',\''.$v['pervat'].'\');" ':'';
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
					$btn_title = ' ARTICOLO NON DISPONIBILE';
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
            echo "<input type=\"hidden\" value=\"" . $v['provvigione'] . "\" name=\"rows[$k][provvigione]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['pesosp'] . "\" name=\"rows[$k][pesosp]\">\n";
            echo '<input type="hidden" value="' . $v['lot_or_serial'] . '" name="rows[' . $k . '][lot_or_serial]" />';
            echo '<input type="hidden" value="' . $v['id_lotmag'] . '" name="rows[' . $k . '][id_lotmag]" />';
            // colonne editabili
            echo "<input type=\"hidden\" value=\"" . $v['descri'] . "\" name=\"rows[$k][descri]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['unimis'] . "\" name=\"rows[$k][unimis]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['quanti'] . "\" name=\"rows[$k][quanti]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['prelis'] . "\" name=\"rows[$k][prelis]\">\n";
            echo "<input type=\"hidden\" value=\"" . $v['sconto'] . "\" name=\"rows[$k][sconto]\">\n";

            // creo l'array da passare alla funzione per la creazione della tabella responsive
            $resprow[$k] = array(
                array('head' => $script_transl["nrow"], 'class' => '',
                    'value' => '<button type="image" name="upper_row[' . $k . ']" class="btn btn-default btn-xs" title="' . $script_transl['upper_row'] . '!">
                                ' . ($k + 1) . ' <i class="glyphicon glyphicon-arrow-up"></i></button>'),
                array('head' => $script_transl["codart"], 'class' => '',
                    'value' => ' <button name="upd_row[' . $k . ']" class="btn ' . $btn_class . ' btn-xs"
					title="' . $script_transl['update'] . $script_transl['thisrow'] . '! ' . $btn_title . '"
					type="submit">
                                <i class="glyphicon glyphicon-refresh"></i>&nbsp;' . $v['codart'] . '
                                </button>',
                    'td_content' => ' title="' . $script_transl['update'] . $script_transl['thisrow'] . '! Sottoscorta =' . $v['scorta'] . '" '
                ),
                array('head' => $script_transl["descri"], 'class' => '',
                    'value' => '<input class="gazie-tooltip" data-type="product-thumb" data-id="' . $v["codart"] . '" data-title="' . $v['annota'] . '" type="text" name="rows[' . $k . '][descri]" value="' . $v['descri'] . '" maxlength="100" />'
                ),
                array('head' => $script_transl["unimis"], 'class' => '',
                    'value' => '<input class="gazie-tooltip" data-type="weight" data-id="' . $peso . '" data-title="' . $script_transl['weight'] . '" type="text" name="rows[' . $k . '][unimis]" value="' . $v['unimis'] . '" maxlength="3" />'
                ),
                array('head' => $script_transl["quanti"], 'class' => 'text-right numeric',
                    'value' => '<input type="number" step="any" class="gazie-tooltip" data-type="weight" data-id="' . $peso . '" data-title="' . $script_transl['weight'] . '" name="rows[' . $k . '][quanti]" value="' . $v['quanti'] . '" maxlength="11" onchange="this.form.submit();" />'
                ),
                array('head' => $script_transl["prezzo"], 'class' => 'text-right numeric',
                    'value' => '<input type="number" step="any" name="rows[' . $k . '][prelis]" value="' . $v['prelis'] . '" maxlength="15"'.$ivacomp.' id="righi_' . $k . '_prelis" onchange="document.docven.last_focus.value=this.id; this.form.submit()" />'
                ),
                array('head' => $script_transl["sconto"], 'class' => 'text-right numeric',
                    'value' => '<input type="number" step="0.01" name="rows[' . $k . '][sconto]" value="' . $v['sconto'] . '" maxlength="4" onchange="this.form.submit()" />'),
                array('head' => $script_transl["amount"], 'class' => 'text-right numeric', 'value' => gaz_format_number($imprig), 'type' => ''),
                array('head' => $script_transl["codvat"], 'class' => 'text-center numeric', 'value' => $v['pervat'], 'type' => ''),
                array('head' => $script_transl["total"], 'class' => 'text-right numeric bg-warning', 'value' => gaz_format_number($tot_row), 'type' => ''),
                array('head' => $script_transl["provvigione"], 'class' => 'text-center numeric', 'value' => $v['provvigione']),
                array('head' => $script_transl["codric"], 'class' => 'text-center',
                    'value' => $v['codric']),
                array('head' => $script_transl["delete"], 'class' => 'text-center',
                    'value' => '<button type="submit" class="btn btn-default btn-xs btn-elimina" name="del[' . $k . ']" title="' . $script_transl['delete'] . $script_transl['thisrow'] . '"><i class="glyphicon glyphicon-remove"></i></button>')
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
                    $resprow[$k][2]['value'] .= $lm_acc;
                    break;
                case "1":
                    // in caso di rigo forfait non stampo alcune colonne
                    $resprow[$k][3]['value'] = ''; //unimis
                    $resprow[$k][4]['value'] = ''; //quanti
                    // scambio l'input con la colonna dell'importo... 
                    $resprow[$k][7]['value'] = $resprow[$k][5]['value'];
                    // ... e poi non la visualizzo più
                    $resprow[$k][5]['value'] = ''; //prelis
                    $resprow[$k][6]['value'] = ''; //sconto
                    break;
                case "2":
                    $resprow[$k][3]['value'] = ''; //unimis
                    $resprow[$k][4]['value'] = ''; //quanti
                    $resprow[$k][5]['value'] = ''; //prelis
                    $resprow[$k][6]['value'] = ''; //sconto
                    $resprow[$k][7]['value'] = ''; //quanti
                    $resprow[$k][8]['value'] = ''; //prelis
                    $resprow[$k][9]['value'] = '';
                    $resprow[$k][10]['value'] = '';
                    $resprow[$k][11]['value'] = '';
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
                    <div class="col-sm-2">
                        <div class="form-group">
                            <label for="tiprig" class="col-sm-4 control-label"><?php echo $script_transl['tiprig']; ?></label>
                            <div class="col-sm-8">
                                <?php $gForm->variousSelect('in_tiprig', $script_transl['tiprig_value'], $form['in_tiprig'], false, true); ?>
                            </div>                
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label for="item" class="col-sm-1 control-label"><?php echo $script_transl['item']; ?></label>
                            <?php
                            $select_artico = new selectartico("in_codart");
                            $select_artico->addSelected($form['in_codart']);
                            $select_artico->output(substr($form['cosear'], 0, 20), 'C', "col-sm-4");
                            ?>
                        
						<?php
						// Antonio Germani - input con pistola lettore codice a barre 
						
							//$class_btn_confirm='btn-success';
							if ($form['ok_barcode']!="ok"){
								?>	
										<button type="submit" name="button_ok_barcode" class="btn btn-edit btn-default btn-xs col-sm-2" title="inserisci con pistola Barcode"> 
										<span class="glyphicon glyphicon-barcode"> Barcode</span>
										</button>
								<?php
							} else {
								if ($form['in_barcode']==""){
								?>						
										
										<input  type="text" value="<?php echo $form['in_barcode']; ?>" name="in_barcode" class="col-sm-4" onchange="this.form.submit()" />
										<button type="submit"  name="no_barcode" title="Togli con pistola Barcode" class="btn btn-edit btn-xs col-sm-2"> 
										<span class="glyphicon glyphicon-remove"> Barcode</span>
										</button>								
								<?php
								} elseif ($form['in_barcode']=="NOT FOUND") {
									$form['in_barcode']="";
									?>						
										
										<input style="border: 3px solid red;"  type="text" value="<?php echo $form['in_barcode']; ?>" class="col-sm-4" name="in_barcode" onchange="this.form.submit()" />
										<button type="submit"  name="no_barcode" title="Togli con pistola Barcode" class="btn btn-edit btn-xs col-sm-2"> 
										<span class="glyphicon glyphicon-remove"> Barcode</span>							
									<?php
								}
							}
						
						// Antonio Germani - fine input con pistola lettore codice a barre -->
						?>
						</div>
					</div>
                    <div class="col-sm-2">
					<?php if ($form['ok_barcode']!="ok"){?>
                        <div class="form-group">
                            <label for="quanti" class="col-sm-6 control-label"><?php echo $script_transl['quanti']; ?></label>
                            <input class="col-sm-6" type="number" step="any" tabindex=6 value="<?php echo $form['in_quanti']; ?>" name="in_quanti" />
                        </div>
					<?php } ?>
                    </div>
					<?php if ($form['ok_barcode']!="ok"){?>
                    <div class="col-sm-2">
                        <div class="form-group text-center">
                            <button type="submit"  tabindex=7 class="btn btn-default btn-xs col-sm-12" name="in_submit" title="<?php echo $script_transl['submit'] . $script_transl['thisrow']; ?>">
                                <?php echo $script_transl['conf_row']; ?>&nbsp;<i class="glyphicon glyphicon-ok"></i>
                            </button>
                        </div> 
                    </div>
					<?php } ?>
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
                            <input class="col-sm-6" type="number" step="any" value="<?php echo $form['in_provvigione']; ?>" name="in_provvigione" />
                        </div>
                    </div>
                </div>
            </div><!-- chiude tab-pane  -->
        </div><!-- chiude tab-content  -->
    </div><!-- chiude panel  -->
    <?php
    if ($next_row > 0) {
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
                                . '<div class="col-sm-2"><button type="submit" class="btn btn-default btn-xs" name="roundup"';
                                if (!empty($form['roundup_y'])) {
                                    echo ' disabled  title="Hai già arrotondato una volta!" ';
                                }
                                echo '><i class="glyphicon glyphicon-arrow-up"></i></button></div>'
                                . '<div class="col-sm-8"><b>' . $admin_aziend['html_symbol'] . ' ' . gaz_format_number($tot) . '</b></div>'
                                . '<div class="col-sm-2"><button type="submit" class="btn btn-default btn-xs" name="rounddown" ><i class="glyphicon glyphicon-arrow-down"></i></button></div>'
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
                                <input class="btn btn-danger center-block" id="preventDuplicate" tabindex=10 onClick="chkSubmit();" type="submit" name="ins" value="<?php 
                                if ($toDo == 'insert'){
                                    echo $script_transl['send_ecr'] . ' ' . $ecr['descri'];
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
<div class="modal" id="vat-price" title="IMPORTO IVA COMPRESA">
	<input type="text" id="cat_prevat" style="text-align: right;" maxlength="11" onkeyup="vatPriceCalc();" />
	<br /><br />
	<!--select id="codvat" name="cat_codvat" class="FacetSelect"></select-->
	<input type="text" id="cat_pervat" style="text-align: center;" maxlength="5" disabled="disabled" />
	<br /><br />
	<input type="text" id="cat_prelis" style="text-align: right;" maxlength="11" disabled="disabled" />
</div>
<script type="text/javascript">
	function vatPrice(row,pervat) {
		var prelis = $("[name='rows["+row+"][prelis]']").val();
		var prevat = Math.round(parseFloat(prelis)*(1+parseFloat(pervat)/100),4);
		$("#cat_prevat").val(prevat);
		$("#cat_pervat").val(pervat);
		$("#cat_prelis").val(prelis);
		$("#vat-price").dialog({
			modal: true,
			buttons: {
				Ok: function() {
					$("[name='rows["+row+"][prelis]']").val($("#cat_prelis").val());
					document.docven.last_focus.value="righi_" + row + "_sconto";
					$("[name='rows["+row+"][prelis]']").parents("form:first").submit();
					$(this).dialog("close");
				}
			}
		});
	};
	function vatPriceCalc() {
		var prevat = $("#cat_prevat").val();
		var pervat = $("#cat_pervat").val();
		if (prevat!="" && pervat!="") {
			var prelis = parseFloat(prevat)/(1+parseFloat(pervat)/100)
			$("#cat_prelis").val(prelis.toFixed(2));
		} else {
			$("#cat_prelis").val("0");
		}
	}
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
	var last_focus_value;
	var last_focus;
	last_focus_value = document.docven.last_focus.value;
	if (last_focus_value != "") {
		last_focus = document.getElementById(last_focus_value);
		if (last_focus != undefined) {
			last_focus.focus();
		}
	}
	last_focus_value = "";
</script>
<!-- ENRICO FEDELE - FINE FINESTRA MODALE -->
<?php
if ($form['ok_barcode']=="ok"){
	?>
	<script type="text/javascript">
	if (this.document.docven.in_barcode.value == '') this.document.docven.in_barcode.focus();
	</script>
	<?php
}

require("../../library/include/footer.php");
?>