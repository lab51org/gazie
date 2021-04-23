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
  scriva   alla   Free  Software Foundation, 51 Franklin Street,
  Fifth Floor Boston, MA 02110-1335 USA Stati Uniti.
  --------------------------------------------------------------------------
 */
require("../../library/include/datlib.inc.php");
require("../../modules/magazz/lib.function.php");

$admin_aziend = checkAdmin();
$msgtoast = "";
$msg = array('err' => array(), 'war' => array());
$calc = new Compute;
$upd_mm = new magazzForm;
$docOperat = $upd_mm->getOperators();
$lm = new lotmag;
function getFAIseziva($tipdoc) {
    global $admin_aziend, $gTables, $auxil;
    if ($tipdoc == 'FAI'||$tipdoc == 'FAA'||$tipdoc == 'FAF'||$tipdoc == 'FAP') { // se è una fattura immediata
        switch ($admin_aziend['fatimm']) {
            case 1:
            case 2:
            case 3:
                $si = $admin_aziend['fatimm'];
            break;
            case "R":
                $si = (isset($_GET['seziva']) && $_GET['seziva'] >= 1 && $_GET['seziva'] <= 3 )? $_GET['seziva'] : 1;
            break;
            case "U":
                $rs_ultimo = gaz_dbi_dyn_query("seziva", $gTables['tesdoc'], "tipdoc = '" . $tipdoc . "'", "datfat desc", 0, 1);
                $ultimo = gaz_dbi_fetch_array($rs_ultimo);
                $si = ($ultimo)? $ultimo['seziva']:1;
            break;
            default:
                $si = 1;
        }
    } else { // per gli altri documenti mi baso su quello passato da url o eventualmente sull'ultimo
        if ($tipdoc == 'DDT' || $tipdoc == 'DDV' || $tipdoc == 'CMR') {
            $tipdoc .= "' OR tipdoc ='FAD";
        }
        $rs_ultimo = gaz_dbi_dyn_query("seziva", $gTables['tesdoc'], "tipdoc = '" . $tipdoc . "'", "datfat DESC", 0, 1);
        $ultimo = gaz_dbi_fetch_array($rs_ultimo);
        $si = ($ultimo) ? $ultimo['seziva'] : 1;
        $si = (isset($_GET['seziva']))? $_GET['seziva'] : $si;
    }
    return $si;
}

if (!isset($_POST['ritorno']) and ! isset($_GET['ritorno'])) {
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
} else {
    $form['ritorno'] = $_POST['ritorno'];
}

if ((isset($_GET['Update']) and ! isset($_GET['id_tes'])) and ! isset($_GET['tipdoc'])) {
    header("Location: " . $form['ritorno']);
    exit;
}

if (isset($_POST['newdestin'])) {
    $_POST['id_des'] = 0;
    $_POST['destin'] = "";
}

if ((isset($_POST['Update'])) or ( isset($_GET['Update']))) {
    $toDo = 'update';
} else {
    $toDo = 'insert';
}

$show_artico_composit = gaz_dbi_get_row($gTables['company_config'], 'var', 'show_artico_composit');
$tipo_composti = gaz_dbi_get_row($gTables['company_config'], 'var', 'tipo_composti');

if ((isset($_POST['Insert'])) or ( isset($_POST['Update']))) {   //se non e' il primo accesso

    //qui si dovrebbe fare un parsing di quanto arriva dal browser...
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
    $form['id_tes'] = $_POST['id_tes'];
    $anagrafica = new Anagrafica();
    $cliente = $anagrafica->getPartner($_POST['clfoco']);
    $form['hidden_req'] = $_POST['hidden_req'];
    $form['roundup_y'] = $_POST['roundup_y'];
    // ...e della testata
    foreach ($_POST['search'] as $k => $v) {
        $form['search'][$k] = $v;
    }
    $form['cosear'] = $_POST['cosear'];
    $form['seziva'] = $_POST['seziva'];
    $form['tipdoc'] = $_POST['tipdoc'];
    $form['id_doc_ritorno'] = intval($_POST['id_doc_ritorno']);
    $form['template'] = $_POST['template'];
	if (substr($_POST['tipdoc'],0,2) == 'FN' || $_POST['tipdoc']=='FAA' || $_POST['tipdoc']=='FAF') { // forzo i template delle fatture d'acconto, note credito e debito su fattura semplice
        $form['template'] = "FatturaSemplice";
    } elseif($_POST['tipdoc']=='FAP'||$_POST['tipdoc']=='FAQ') { // forzo i template delle parcelle
        $form['template'] = "Parcella";
	}
	$form['datemi'] = substr($_POST['datemi'],0,10);
	$form['initra'] = substr($_POST['initra'],0,10);
    $form['oratra'] = $_POST['oratra'];
    $form['mintra'] = $_POST['mintra'];
    $form['protoc'] = $_POST['protoc'];
    $form['numdoc'] = $_POST['numdoc'];
    $form['numfat'] = $_POST['numfat'];
    $form['datfat'] = $_POST['datfat'];
    $form['clfoco'] = $_POST['clfoco'];
    //tutti i controlli su  tipo di pagamento e rate
    $form['speban'] = floatval($_POST['speban']);
    $form['numrat'] = intval($_POST['numrat']);
    $form['expense_vat'] = intval($_POST['expense_vat']);
    $form['split_payment'] = substr($_POST['split_payment'],0,1);
    $form['virtual_taxstamp'] = intval($_POST['virtual_taxstamp']);
    $form['taxstamp'] = floatval($_POST['taxstamp']);
    $form['stamp'] = floatval($_POST['stamp']);
    $form['round_stamp'] = intval($_POST['round_stamp']);
    $form['pagame'] = $_POST['pagame'];
    $form['change_pag'] = $_POST['change_pag'];
    if ($form['change_pag'] != $form['pagame']) {  //se è stato cambiato il pagamento
        $new_pag = gaz_dbi_get_row($gTables['pagame'], "codice", $form['pagame']);
        if ($toDo == 'update') {  //se è una modifica mi baso sulle vecchie spese
            $old_header = gaz_dbi_get_row($gTables['tesdoc'], "id_tes", $form['id_tes']);
            if ($cliente['speban'] == "S" && ($new_pag['tippag'] == 'T' || $new_pag['tippag'] == 'B' || $new_pag['tippag'] == 'V')) {
                if ($old_header['speban'] > 0) {
                    $form['speban'] = $old_header['speban'];
                } else {
                    $form['speban'] = $admin_aziend['sperib'];
                }
            } else {
                $form['speban'] = 0.00;
            }
        } else { //altrimenti, se previste,  mi avvalgo delle nuove dell'azienda
            if ($cliente['speban'] == "S" && ($new_pag['tippag'] == 'B' || $new_pag['tippag'] == 'T' || $new_pag['tippag'] == 'V')) {
                $form['speban'] = $admin_aziend['sperib'];
            } else {
                $form['speban'] = 0;
            }
        }
        if ($new_pag['tippag'] == 'T' && $form['stamp'] == 0) {  //se il pagamento prevede il bollo
            $form['stamp'] = $admin_aziend['perbol'];
            $form['round_stamp'] = $admin_aziend['round_bol'];
        } elseif ($new_pag['tippag'] == 'R') {
            $form['stamp'] = $admin_aziend['taxstamp'];
            $form['round_stamp'] = 0;
        } elseif ($new_pag['tippag'] != 'T') {
            $form['stamp'] = 0;
            $form['round_stamp'] = 0;
        }
        $form['numrat'] = $new_pag['numrat'];
        $form['pagame'] = $_POST['pagame'];
        $form['change_pag'] = $_POST['pagame'];
    }
    $form['banapp'] = $_POST['banapp'];
    $form['vettor'] = $_POST['vettor'];
    $form['id_agente'] = intval($_POST['id_agente']);
    $form['net_weight'] = floatval($_POST['net_weight']);
    $form['gross_weight'] = floatval($_POST['gross_weight']);
    $form['units'] = intval($_POST['units']);
    $form['volume'] = $_POST['volume'];
    $form['listin'] = $_POST['listin'];
    $form['spediz'] = $_POST['spediz'];
    $form['portos'] = $_POST['portos'];
    $form['imball'] = $_POST['imball'];
    $form['destin'] = $_POST['destin'];
    $form['id_des'] = substr($_POST['id_des'], 3);
    $form['id_des_same_company'] = intval($_POST['id_des_same_company']);
    $form['traspo'] = $_POST['traspo'];
    $form['spevar'] = $_POST['spevar'];
    $form['cauven'] = $_POST['cauven'];
    $form['caucon'] = $_POST['caucon'];
    $form['caumag'] = $_POST['caumag'];
    /** inizio modifica FP 15/10/2015
     * aggiunti campi
     */
    $form['ragbol'] = $_POST['ragbol'];
    $form['data_ordine'] = $_POST['data_ordine'];
    $form['gioord'] = $_POST['gioord'];
    $form['mesord'] = $_POST['mesord'];
    $form['annord'] = $_POST['annord'];
    $form['caucon'] = $_POST['caucon'];
    $form['sconto'] = floatval(preg_replace("/\,/", '.', $_POST['sconto']));
    // inizio rigo di input
    $form['in_descri'] = $_POST['in_descri'];
    $form['in_tiprig'] = $_POST['in_tiprig'];
    /*    $form['in_artsea'] = $_POST['in_artsea']; */
    $form['in_codart'] = $_POST['in_codart'];
    $form['in_pervat'] = $_POST['in_pervat'];
    $form['in_tipiva'] = $_POST['in_tipiva'];
    $form['in_ritenuta'] = $_POST['in_ritenuta'];
    $form['in_unimis'] = $_POST['in_unimis'];
    $form['in_prelis'] = $_POST['in_prelis'];
    $form['in_sconto'] = $_POST['in_sconto'];
    $form['in_quanti'] = gaz_format_quantity($_POST['in_quanti'], 0, $admin_aziend['decimal_quantity']);
    $form['in_codvat'] = $_POST['in_codvat'];
    $form['in_codric'] = $_POST['in_codric'];
    $form['in_provvigione'] = $_POST['in_provvigione']; // in caso tiprig=4 questo campo è utilizzato per indicare l'aliquota della cassa previdenziale
    $form['in_id_mag'] = $_POST['in_id_mag'];
    $form['in_annota'] = $_POST['in_annota'];
    $form['in_scorta'] = $_POST['in_scorta'];
    $form['in_quamag'] = $_POST['in_quamag'];
    $form['in_pesosp'] = $_POST['in_pesosp'];
    $form['in_gooser'] = intval($_POST['in_gooser']);
    $form['in_lot_or_serial'] = intval($_POST['in_lot_or_serial']);
	$form['in_SIAN'] = intval($_POST['in_SIAN']);
    $form['in_id_lotmag'] = intval($_POST['in_id_lotmag']);
	$form['in_identifier'] = $_POST['in_identifier'];
	$form['in_cod_operazione'] = $_POST['in_cod_operazione'];
	$form['in_recip_stocc'] = $_POST['in_recip_stocc'];
	$form['in_recip_stocc_destin'] = $_POST['in_recip_stocc_destin'];
    $form['in_status'] = $_POST['in_status'];
    // fine rigo input
    $form['rows'] = array();
    // creo un array dove andrò a mettere tutti i righi normali e/o forfait ai quali potranno eventualmente essere riferiti gli elementi dal 2.1.2 a 2.1.7
	$form['RiferimentoNumeroLinea'] = array();
    $next_row = 0;
	$fae_id_documento_exist=array();
	$fae_other_el_exist=array();
    if (isset($_POST['rows'])) {
        foreach ($_POST['rows'] as $next_row => $v) {
			switch($v['tiprig']){
				case'0':
				case'1':
				case'90':
				$form['RiferimentoNumeroLinea'][$next_row+1] = substr($v['descri'],0,20);
				break;
				case'13':
				$fae_id_documento_exist[$v['codric']]=true; // servirà per controllare se c'è questo elemento in presenza di almeno un altro, altrimenti darò errore
				if (empty($v['descri'])){
					$msg['err'][] = "49";
				}
				break;
				case'11':
				case'12':
				case'14':
				case'15':
				case'16':
				$fae_other_el_exist[$v['codric']]=true;
				if (empty($v['descri'])){
					$msg['err'][] = "49";
				}
				break;
			}
            if (isset($_POST["row_$next_row"])) { //se ho un rigo testo
                $form["row_$next_row"] = $_POST["row_$next_row"];
            }
            $form['rows'][$next_row]['descri'] = substr($v['descri'], 0, 100);
            $form['rows'][$next_row]['tiprig'] = intval($v['tiprig']); 
            $form['rows'][$next_row]['codart'] = substr($v['codart'], 0, 15);
            if ($_POST['hidden_req']=="fae_tipo_cassa".$next_row && $v['tiprig']==4) { 
				// se provengo da un cambiamento di un rigo cassa previdenziale aggiorno la descrizione 
	            $xml = simplexml_load_file('../../library/include/fae_tipo_cassa.xml');
				foreach ($xml->record as $vx) {
					$selected = '';
					if ($vx->field[0] == $v['codart']) {
						$form['rows'][$next_row]['descri']= 'Contributo '.strtolower($vx->field[1]);
					}
				}
            }
            $form['rows'][$next_row]['pervat'] = preg_replace("/\,/", '.', $v['pervat']);
            $form['rows'][$next_row]['tipiva'] = strtoupper(substr($v['tipiva'], 0, 1));
            $form['rows'][$next_row]['ritenuta'] = preg_replace("/\,/", '.', $v['ritenuta']);
            $form['rows'][$next_row]['unimis'] = substr($v['unimis'], 0, 3);
            $form['rows'][$next_row]['prelis'] = number_format(floatval(preg_replace("/\,/", '.', $v['prelis'])), $admin_aziend['decimal_price'], '.', '');
            $form['rows'][$next_row]['sconto'] = floatval(preg_replace("/\,/", ".", $v['sconto']));
            $form['rows'][$next_row]['quanti'] = gaz_format_quantity($v['quanti'], 0, $admin_aziend['decimal_quantity']);
            $form['rows'][$next_row]['codvat'] = intval($v['codvat']);
            $form['rows'][$next_row]['codric'] = intval($v['codric']);
            if (isset($v['provvigione'])) {// in caso tiprig=4 questo campo è utilizzato per indicare l'aliquota della cassa previdenziale
                $form['rows'][$next_row]['provvigione'] = floatval($v['provvigione']);
            }
            $form['rows'][$next_row]['id_mag'] = intval($v['id_mag']);
            $form['rows'][$next_row]['annota'] = substr($v['annota'], 0, 50);
            $form['rows'][$next_row]['scorta'] = floatval($v['scorta']);
            $form['rows'][$next_row]['quamag'] = floatval($v['quamag']);
            $form['rows'][$next_row]['pesosp'] = floatval($v['pesosp']);
            $form['rows'][$next_row]['gooser'] = intval($v['gooser']);
            $form['rows'][$next_row]['lot_or_serial'] = intval($v['lot_or_serial']);
			$form['rows'][$next_row]['SIAN'] = intval($v['SIAN']);
            $form['rows'][$next_row]['id_lotmag'] = intval($v['id_lotmag']);
			$form['rows'][$next_row]['identifier'] = $v['identifier'];
			$form['rows'][$next_row]['cod_operazione'] = $v['cod_operazione'];
			$form['rows'][$next_row]['recip_stocc'] = $v['recip_stocc'];
			$form['rows'][$next_row]['recip_stocc_destin'] = $v['recip_stocc_destin'];
            if ($v['tiprig'] == 0 && $v['quanti'] < 0.00001 && $v['quanti'] > -0.00001) {
				$msg['err'][] = "64";
			}
            if ($v['lot_or_serial'] == 2 && $v['id_lotmag'] > 0) {
            // se è prevista la gestione per numero seriale/matricola la quantità non può essere diversa da 1
                if ($form['rows'][$next_row]['quanti'] <> 1) {
                    $msg['err'][] = "60";
                }
                $form['rows'][$next_row]['quanti'] = 1;
            }
            $form['rows'][$next_row]['status'] = substr($v['status'], 0, 10);
            if (isset($_POST['upd_row'])) {
                $k_row = key($_POST['upd_row']);
                if ($k_row == $next_row) {
                    // sottrazione ai totali peso,pezzi,volume
                    $artico = gaz_dbi_get_row($gTables['artico'], "codice", $form['rows'][$k_row]['codart']);
                    $form['net_weight'] -= $form['rows'][$k_row]['quanti'] * $artico['peso_specifico'];
                    $form['gross_weight'] -= $form['rows'][$k_row]['quanti'] * $artico['peso_specifico'];
                    if ($artico['pack_units'] > 0) {
                        $form['units'] -= intval(round($form['rows'][$k_row]['quanti'] / $artico['pack_units']));
                    }
                    $form['volume'] -= $form['rows'][$k_row]['quanti'] * $artico['volume_specifico'];
                    // fine sottrazione peso,pezzi,volume
                    $form['in_descri'] = $form['rows'][$k_row]['descri'];
                    $form['in_tiprig'] = $form['rows'][$k_row]['tiprig'];
                    $form['in_codart'] = $form['rows'][$k_row]['codart'];
                    $form['in_pervat'] = $form['rows'][$k_row]['pervat'];
                    $form['in_tipiva'] = $form['rows'][$k_row]['tipiva'];
                    $form['in_ritenuta'] = $form['rows'][$k_row]['ritenuta'];
                    $form['in_unimis'] = $form['rows'][$k_row]['unimis'];
                    $form['in_prelis'] = $form['rows'][$k_row]['prelis'];
                    $form['in_sconto'] = $form['rows'][$k_row]['sconto'];
                    $form['in_quanti'] = $form['rows'][$k_row]['quanti'];
                    $form['in_codvat'] = $form['rows'][$k_row]['codvat'];
                    $form['in_codric'] = $form['rows'][$k_row]['codric'];
                    $form['in_provvigione'] = $form['rows'][$k_row]['provvigione'];// in caso tiprig=4 questo campo è utilizzato per indicare l'aliquota della cassa previdenziale
                    $form['in_id_mag'] = $form['rows'][$k_row]['id_mag'];
                    $form['in_annota'] = $form['rows'][$k_row]['annota'];
                    $form['in_scorta'] = $form['rows'][$k_row]['scorta'];
                    $form['in_quamag'] = $form['rows'][$k_row]['quamag'];
                    $form['in_pesosp'] = $form['rows'][$k_row]['pesosp'];
                    $form['in_gooser'] = $form['rows'][$k_row]['gooser'];
                    $form['in_lot_or_serial'] = $form['rows'][$k_row]['lot_or_serial'];
					$form['in_SIAN'] = $form['rows'][$k_row]['SIAN'];
                    $form['in_id_lotmag'] = $form['rows'][$k_row]['id_lotmag'];
					$form['in_identifier'] = $form['rows'][$k_row]['identifier'];
					$form['in_cod_operazione'] = $form['rows'][$k_row]['cod_operazione'];
					$form['in_recip_stocc'] = $form['rows'][$k_row]['recip_stocc'];
					$form['in_recip_stocc_destin'] = $form['rows'][$k_row]['recip_stocc_destin'];
                    $form['in_status'] = "UPDROW" . $k_row;
                    $form['cosear'] = $form['rows'][$k_row]['codart'];
                    //}
                    array_splice($form['rows'], $k_row, 1);
                    $next_row--;
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
                if ($artico['lot_or_serial'] > 0) {
					$disp = $lm->dispLotID ($form['rows'][$next_row]['codart'], $form['rows'][$next_row]['id_lotmag'], $form['rows'][$next_row]['id_mag']);
                    $lm->getAvailableLots($form['rows'][$next_row]['codart'], $form['rows'][$next_row]['id_mag']);
					$ld = $lm->divideLots($form['rows'][$next_row]['quanti']);					
					if ($form['rows'][$next_row]['quanti'] <= $disp AND $ld > 0){ // suddivido la quantità richiesta solo se c'è sufficiente disponibilità altrimenti segnalo e permetto forzatura
						/* ripartisco la quantità introdotta tra i vari lotti disponibili per l'articolo
						 * e se è il caso creo più righi
						 */
						$i = $next_row;
						foreach ($lm->divided as $k => $v) {
							if ($v['qua'] >= 0.00001) {
								$form['rows'][$i] = $form['rows'][$next_row]; // copio il rigo di origine
								$form['rows'][$i]['id_lotmag'] = $k; // setto il lotto
								$form['rows'][$i]['quanti'] = $v['qua']; // e la quantità in base al riparto
								$getlot = $lm->getLot($form['rows'][$i]['id_lotmag']);
								$form['rows'][$i]['identifier'] = $getlot['identifier'];
								$i++;
							}
						}
					}
                }
                $form['net_weight'] += $form['rows'][$next_row]['quanti'] * $artico['peso_specifico'];
                $form['gross_weight'] += $form['rows'][$next_row]['quanti'] * $artico['peso_specifico'];
                if ($artico['pack_units'] > 0) {
                    $form['units'] += intval(round($form['rows'][$next_row]['quanti'] / $artico['pack_units']));
                }
                $form['volume'] += $form['rows'][$next_row]['quanti'] * $artico['volume_specifico'];
            }
            $next_row++;
        } // fine ciclo dei righi 
		
		/* inizio controlli errori sugli elementi di fattura elettronica dal 2.1.2 al 2.1.6 
		*/
		foreach ($fae_other_el_exist as $k=>$V){
			if (!isset($fae_id_documento_exist[$k])){
				$msg['err'][] = "id_documento";
			}
			
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

    $datemi = gaz_format_date($form['datemi'],true);// adatto al db;
    // Se viene inviata la richiesta di conferma totale ...
    if (isset($_POST['ins'])) {
        $sezione = $form['seziva'];
        $utsemi = gaz_format_date($form['datemi'],2); // mktime
        if ($form['tipdoc'] != 'DDT' && $form['tipdoc'] != 'DDY' && $form['tipdoc'] != 'DDS' && $form['tipdoc'] != 'RDV' && $form['tipdoc'] != 'DDV' && $form['template'] != 'FatturaImmediata') {
            $initra = $datemi;
            $utstra = $utsemi;
        } else {
			$initra = gaz_format_date($form['initra'],true);// adatto al db
            $utstra = gaz_format_date($form['initra'],2); // mktime
        }
		
        if (!gaz_format_date($form['initra'],'chk') AND $form['tipdoc'] != 'VCO') {
            $msg['err'][] = "37";
        }
        if ($utstra < $utsemi AND $form['tipdoc'] != 'VCO') {
            $msg['err'][] = "38";
        }
        if (!isset($_POST['rows'])) {
            $msg['err'][] = "39";
        }
        if ($form['tipdoc'] == 'RDV' && $form['id_doc_ritorno'] <= 0) {  //se è un RDV vs Fattura differita
            $msg['err'][] = "59";
        }
        // --- inizio controllo coerenza date-numerazione
        if ($toDo == 'update') {  // controlli in caso di modifica
            if ($form['tipdoc'] == 'DDT' || $form['tipdoc'] == 'DDV' || $form['tipdoc'] == 'DDY' || $form['tipdoc'] == 'DDS' || $form['tipdoc'] == 'FAD') {  //se è un DDT vs Fattura differita
                $rs_query = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = " . substr($datemi,0,4) . " and datemi < '$datemi' and ( tipdoc like 'DD_' or tipdoc = 'FAD') and seziva = $sezione", "numdoc desc", 0, 1);
                $result = gaz_dbi_fetch_array($rs_query); //giorni precedenti
                if ($result and ( $form['numdoc'] < $result['numdoc'])) {
                    if ( !$modifica_fatture_ddt ) {
                        $msg['err'][] = "40";
                    }
                }
                $rs_query = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = " . substr($datemi,0,4) . " and datemi > '$datemi' and ( tipdoc like 'DD_' or tipdoc = 'FAD') and seziva = $sezione", "numdoc asc", 0, 1);
                $result = gaz_dbi_fetch_array($rs_query); //giorni successivi
                if ($result and ( $form['numdoc'] > $result['numdoc'])) {
                    if ( !$modifica_fatture_ddt ) {
                        $msg['err'][] = "41";
                    }
                }
            } else if ( $form['tipdoc'] == 'CMR' || $form['tipdoc'] == 'FAC' ) {
                $rs_query = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = " . substr($datemi,0,4) . " and datemi < '$datemi' and ( tipdoc like 'CMR' or tipdoc = 'FAC') and seziva = $sezione", "numdoc desc", 0, 1);
                $result = gaz_dbi_fetch_array($rs_query); //giorni precedenti
                if ($result and ( $form['numdoc'] < $result['numdoc'])) {
                    if ( !$modifica_fatture_ddt ) {
                        $msg['err'][] = "40";
                    }
                }
                $rs_query = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = " . substr($datemi,0,4) . " and datemi > '$datemi' and ( tipdoc like 'CMR' or tipdoc = 'FAC') and seziva = $sezione", "numdoc asc", 0, 1);
                $result = gaz_dbi_fetch_array($rs_query); //giorni successivi
                if ($result and ( $form['numdoc'] > $result['numdoc'])) {
                    if ( !$modifica_fatture_ddt ) {
                        $msg['err'][] = "41";
                    }
                }
            } else { //se sono altri documenti
                $rs_query = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = " . substr($datemi,0,4) . " and datfat < '$datemi' and tipdoc like '" . $form['tipdoc'] . "' and seziva = $sezione", "protoc desc", 0, 1);
                $result = gaz_dbi_fetch_array($rs_query); //giorni precedenti
                if ($result and ( $form['numfat'] < $result['numfat'])) {
                    if ( !$modifica_fatture_ddt ) {
                        $msg['err'][] = "42";
                    }
                }
                $rs_query = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = " . substr($datemi,0,4) . " and datfat > '$datemi' and tipdoc like '" . $form['tipdoc'] . "' and seziva = $sezione", "protoc asc", 0, 1);
                $result = gaz_dbi_fetch_array($rs_query); //giorni successivi
                if ($result and ( $form['numfat'] > $result['numfat'])) {
                    if ( !$modifica_fatture_ddt ) {
                        $msg['err'][] = "43";
                    }
                }
            }
        } else {    //controlli in caso di inserimento
            if ($form['tipdoc'] == 'DDT' || $form['tipdoc'] == 'DDV' || $form['tipdoc'] == 'DDS' || $form['tipdoc'] == 'DDY') {  //se è un DDT
                $rs_ultimo_ddt = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = " . substr($datemi,0,4) . " AND (tipdoc LIKE 'DD_' OR tipdoc = 'FAD') AND ddt_type!='R' AND seziva = " . $sezione, "datemi DESC ,numdoc DESC ", 0, 1);
                $ultimo_ddt = gaz_dbi_fetch_array($rs_ultimo_ddt);
                $utsUltimoDdT = mktime(0, 0, 0, substr($ultimo_ddt['datemi'], 5, 2), substr($ultimo_ddt['datemi'], 8, 2), substr($ultimo_ddt['datemi'], 0, 4));
                if ($ultimo_ddt and ( $utsUltimoDdT > $utsemi)) {
                    $msg['err'][] = "44";
                }
            } else if ($form['tipdoc'] == 'VRI') {
                $rs_last_n = gaz_dbi_dyn_query("numdoc", $gTables['tesdoc'], "tipdoc = 'VRI' AND id_con = 0", 'datemi DESC, numdoc DESC', 0, 1);
                $last_n = gaz_dbi_fetch_array($rs_last_n);
                if ($last_n) {
                    $form['numdoc'] = $last_n['numdoc'] + 1;
                } else {
                    $form['numdoc'] = 1;
                }
            } else if ($form['tipdoc'] == 'CMR' ) {
                $rs_last_n = gaz_dbi_dyn_query("numdoc", $gTables['tesdoc'], "tipdoc = 'CMR' AND ddt_type='R' AND id_con = 0", 'datemi DESC, numdoc DESC', 0, 1);
                $last_n = gaz_dbi_fetch_array($rs_last_n);
                if ($last_n) {
                    $form['numdoc'] = $last_n['numdoc'] + 1;
                } else {
                    $form['numdoc'] = 1;
                }
            } else { //se sono altri documenti
                $rs_ultimo_tipo = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = " . substr($datemi,0,4) . " and tipdoc like '" . substr($form['tipdoc'], 0, 1) . "%' and seziva = $sezione", "protoc desc, datfat desc, datemi desc", 0, 1);
                $ultimo_tipo = gaz_dbi_fetch_array($rs_ultimo_tipo);
				if ($ultimo_tipo){
					$utsUltimoProtocollo = mktime(0, 0, 0, substr($ultimo_tipo['datfat'], 5, 2), substr($ultimo_tipo['datfat'], 8, 2), substr($ultimo_tipo['datfat'], 0, 4));
					if ($ultimo_tipo and ( $utsUltimoProtocollo > $utsemi)) {
						$msg['err'][] = "45";
					}
				}
			}
        }
        // --- fine controllo coerenza date-numeri
        if (!gaz_format_date($form['datemi'],'chk'))
            $msg['err'][] = "46";
        if (empty($form["clfoco"]))
            $msg['err'][] = "47";
        if (empty($form["pagame"]))
            $msg['err'][] = "48";
        //controllo che i rows non abbiano descrizioni  e unita' di misura vuote in presenza di quantita diverse da 0
        $rit_ctrl=false;

        // controllo se ci sono diversi tipi di iva nel documento o se è presente iva split e non
        $iva_split_payment = false;
        $iva_altri_tipi = false;

        foreach ($form['rows'] as $i => $v) {
            // controllo se presente iva split e iva normale
            if ( $v['tipiva']=="T" ) {
                $iva_split_payment = true;
            } else if ( $v['tipiva']!="" && ($v['tiprig']==0 || $v['tiprig']==1)) {
                $iva_altri_tipi = true;
            }
            if ($v['descri'] == '' && ($v['quanti'] > 0 || $v['quanti'] < 0)) {
                $msgrigo = $i + 1;
                $msg['err'][] = "49";
            }
            if ($v['tiprig']== 0 && $v['unimis'] == '' && ($v['quanti'] > 0 || $v['quanti'] < 0)) {
                $msgrigo = $i + 1;
                $msg['err'][] = "50";
            }
            if ($v['tiprig'] == 90) {
                if (empty($v['descri'])) {
                    $msgrigo = $i + 1;
                    $msg['err'][] = "49";
                }
                if ($v['codric'] < 100000000) {
                    $msgrigo = $i + 1;
                    $msg['err'][] = "61";
                }
            }
			if ($v['ritenuta']>=0.01){
				$rit_ctrl=true;
			}
			// Antonio Germani - controllo input su rigo SIAN
			if ($v['SIAN']>0){
				if ($v['cod_operazione'] < 0 or $v['cod_operazione']==11){ // controllo se è stato inserito il codice operazione SIAN
					$msgrigo = $i + 1;
					$msg['err'][] = "nocod_operaz";
				}
				$clfoco = gaz_dbi_get_row($gTables['clfoco'], "codice", $form["clfoco"]);
				$anagra = gaz_dbi_get_row($gTables['anagra'], "id", $clfoco["id_anagra"]);
				if ($anagra['id_SIAN']<=0 AND ($v['cod_operazione']==1 OR $v['cod_operazione']==2 OR $v['cod_operazione']==3 OR $v['cod_operazione']==5 OR $v['cod_operazione']==10)){
					$msgrigo = $i + 1;
					$msg['err'][] = "nofor_sian";
				}
				$art = gaz_dbi_get_row($gTables['camp_artico'], "codice", $v['codart']);
				/*if ($v['cod_operazione'] == 4 AND strlen($v['recip_stocc_destin'])==0 AND $art['confezione']==0){
					$msgrigo = $i + 1;
					$msg['err'][] = "norecipdestin"; // manca il recipiente di destinazione
				}*/
				if (strlen($v['recip_stocc'])==0 AND $art['confezione']==0){
					$msgrigo = $i + 1;
					$msg['err'][] = "norecipstocc"; // manca il recipiente di stoccaggio
				}
				if ($v['cod_operazione'] == 0 AND $art['confezione']==0){
					$msgrigo = $i + 1;
					$msg['err'][] = "soloconf"; // operazione con solo olio confezionato
				}
				if ($v['cod_operazione'] == 0 AND strlen($v['identifier'])==0){
					$msgrigo = $i + 1;
					$msg['err'][] = "sololotto"; // operazione con solo olio confezionato deve avere il lotto
				}
				if ($v['cod_operazione'] == 6 AND $art['confezione']==0){
					$msgrigo = $i + 1;
					$msg['err'][] = "soloconf"; // Cessione omaggio solo confezionato
				}
				if ($v['cod_operazione'] == 9 AND $art['confezione']==0){
					$msgrigo = $i + 1;
					$msg['err'][] = "soloconf"; // Cessione omaggio solo confezionato
				}
			}
			
			// Antonio Germani - controllo input su lotti rigo
			if ($v['lot_or_serial']>0){ 
				// controllo se per questo ID lotto la quantità richiesta è sufficiente
				$idmag="";
				if ($toDo == 'update') { // se è update faccio togliere dal conteggio l'eventuale suo stesso movimento
					$idmag=$v['id_mag'];
				}
				/* commentato per permettere di forzare in quanto l'errore viene comunque segnalato con un warning
				$disp= $lm -> dispLotID ($v['codart'], $v['id_lotmag'], $idmag);		
				if ($v['quanti']>$disp AND $form['tipdoc']<>"FNC"){
					$msg['err'][] = "lotinsuf";
				}
				*/
			}
        }
        // faccio visualizzare l'errore in caso di iva diversa
        if ( $iva_altri_tipi && $iva_split_payment ) {
            $msg['err'][] = "66";
        }

		// dal 2019 non sarà più possibile emettere fatture a clienti che non ci hanno comunicato la PEC o il codice SdI
		if (substr($datemi,0,4)>=2019 && strlen($cliente['pec_email'])<5 && strlen(trim($cliente['fe_cod_univoco']))<6 && $form['tipdoc']!='VRI' ){
				//$msg['err'][] = "62";
		}
		if ($rit_ctrl && $admin_aziend['causale_pagam_770']==''){
				$msg['err'][] = "63";
        }
        if (count($msg['err']) < 1) {// *** nessun errore ***
            $initra .= " " . $form['oratra'] . ":" . $form['mintra'] . ":00";
            if (preg_match("/^id_([0-9]+)$/", $form['clfoco'], $match)) {
                $new_clfoco = $anagrafica->getPartnerData($match[1], 1);
                $form['clfoco'] = $anagrafica->anagra_to_clfoco($new_clfoco, $admin_aziend['mascli'], $form['pagame']);
            }
            if ($toDo == 'update') { // e' una modifica
                $old_rows = gaz_dbi_dyn_query("*", $gTables['rigdoc'], "id_tes = " . $form['id_tes'], "id_rig asc");
                $i = 0;
                $count = count($form['rows']) - 1;
                while ($val_old_row = gaz_dbi_fetch_array($old_rows)) {
					// per evitare problemi qualora siano stati modificati i righi o comunque cambiati di ordine elimino sempre il vecchio movimento di magazzino e sotto ne inserisco un altro attenendomi a questo
                    if (intval($val_old_row['id_mag']) > 0) {  //se c'è stato un movimento di magazzino lo azzero
                        $upd_mm->uploadMag('DEL', $form['tipdoc'], '', '', '', '', '', '', '', '', '', '', $val_old_row['id_mag'], $admin_aziend['stock_eval_method']);
						// se c'è stato, cancello pure il movimento sian 
						gaz_dbi_del_row($gTables['camp_mov_sian'], "id_movmag", $val_old_row['id_mag']);
                    }
                    if ($i <= $count) { //se il vecchio rigo e' ancora presente nel nuovo lo modifico
                        $form['rows'][$i]['id_tes'] = $form['id_tes'];
                        $codice = array('id_rig', $val_old_row['id_rig']);
                        rigdocUpdate($codice, $form['rows'][$i]);
                        if (isset($form["row_$i"]) && $val_old_row['id_body_text'] > 0) { //se è un rigo testo giè presente lo modifico
                            bodytextUpdate(array('id_body', $val_old_row['id_body_text']), array('table_name_ref' => 'rigdoc', 'id_ref' => $val_old_row['id_rig'], 'body_text' => $form["row_$i"], 'lang_id' => $admin_aziend['id_language']));
                            gaz_dbi_put_row($gTables['rigdoc'], 'id_rig', $val_old_row['id_rig'], 'id_body_text', $val_old_row['id_body_text']);
                        } elseif (isset($form["row_$i"]) && $val_old_row['id_body_text'] == 0) { //prima era un rigo diverso da testo
                            bodytextInsert(array('table_name_ref' => 'rigdoc', 'id_ref' => $val_old_row['id_rig'], 'body_text' => $form["row_$i"], 'lang_id' => $admin_aziend['id_language']));
                            gaz_dbi_put_row($gTables['rigdoc'], 'id_rig', $val_old_row['id_rig'], 'id_body_text', gaz_dbi_last_id());
                        } elseif (!isset($form["row_$i"]) && $val_old_row['id_body_text'] > 0) { //un rigo che prima era testo adesso non lo è piè
                            gaz_dbi_del_row($gTables['body_text'], "table_name_ref = 'rigdoc' AND id_ref", $val_old_row['id_rig']);
                        }
						// riscrivo mov mag	
						if ( ($tipo_composti['val']=="STD" || $form['rows'][$i+1]['tiprig']!=210) && intval($val_old_row['id_mag'])>0) {
							$id_mag=$upd_mm->uploadMag($val_old_row['id_rig'], $form['tipdoc'], $form['numdoc'], $form['seziva'], $datemi, $form['clfoco'], $form['sconto'], $form['caumag'], $form['rows'][$i]['codart'], $form['rows'][$i]['quanti'], $form['rows'][$i]['prelis'], $form['rows'][$i]['sconto'], 0, $admin_aziend['stock_eval_method'], false, $form['protoc'], $form['rows'][$i]['id_lotmag']);
							gaz_dbi_put_row($gTables['rigdoc'], 'id_rig', $val_old_row['id_rig'], 'id_mag', $id_mag); // inserisco il riferimento movmag nel rigo doc
							if ($form['rows'][$i]['SIAN'] > 0) { // se l'articolo deve movimentare il SIAN creo anche il movimento
								$value_sian['cod_operazione']= $form['rows'][$i]['cod_operazione'];
								$value_sian['recip_stocc']= $form['rows'][$i]['recip_stocc'];
								$value_sian['recip_stocc_destin']= $form['rows'][$i]['recip_stocc_destin'];
								$value_sian['id_movmag']=$id_mag;
								gaz_dbi_table_insert('camp_mov_sian', $value_sian);
							}
						}
                    } else { //altrimenti lo elimino

                        if (intval($val_old_row['id_body_text']) > 0) {  //se c'è un testo allegato al rigo elimino anch'esso
                            gaz_dbi_del_row($gTables['body_text'], "table_name_ref = 'rigdoc' AND id_ref", $val_old_row['id_rig']);
                        }
                        gaz_dbi_del_row($gTables['rigdoc'], 'id_rig', $val_old_row['id_rig']);
                    }
                    $i++;
                }
                //qualora i nuovi righi fossero di più dei vecchi inserisco l'eccedenza
                for ($i = $i; $i <= $count; $i++) {
					$form['rows'][$i]['id_tes'] = $form['id_tes'];
                    $last_rigdoc_id=rigdocInsert($form['rows'][$i]);// inserisco il rig doc
                    if ($admin_aziend['conmag'] == 2 &&
                            $form['rows'][$i]['tiprig'] == 0 &&
                            $form['rows'][$i]['gooser'] != 1 &&
                            !empty($form['rows'][$i]['codart'])) { //se l'impostazione in azienda prevede l'aggiornamento automatico dei movimenti di magazzino
                        if ( $tipo_composti['val']=="STD" || $form['rows'][$i+1]['tiprig']!=210 ) {                                                    
                            $id_mag=$upd_mm->uploadMag(gaz_dbi_last_id(), $form['tipdoc'], $form['numdoc'], $form['seziva'], $datemi, $form['clfoco'], $form['sconto'], $form['caumag'], $form['rows'][$i]['codart'], $form['rows'][$i]['quanti'], $form['rows'][$i]['prelis'], $form['rows'][$i]['sconto'], 0, $admin_aziend['stock_eval_method'], false, $form['protoc'], $form['rows'][$i]['id_lotmag']);
							gaz_dbi_put_row($gTables['rigdoc'], 'id_rig', $last_rigdoc_id, 'id_mag', $id_mag); // inserisco il riferimento mov mag nel rigo doc
							if ($form['rows'][$i]['SIAN'] > 0) { // se l'articolo deve movimentare il SIAN creo anche il movimento
								$value_sian['cod_operazione']= $form['rows'][$i]['cod_operazione'];
								$value_sian['recip_stocc']= $form['rows'][$i]['recip_stocc'];
								$value_sian['recip_stocc_destin']= $form['rows'][$i]['recip_stocc_destin'];
								$value_sian['id_movmag']=$id_mag;
								gaz_dbi_table_insert('camp_mov_sian', $value_sian);
							}
						}
                    }
                    if ($admin_aziend['conmag'] == 2 &&
                            $form['rows'][$i]['tiprig'] == 210 &&
                            !empty($form['rows'][$i]['codart'])) { //se l'impostazione in azienda prevede l'aggiornamento automatico dei movimenti di magazzino
                        $upd_mm->uploadMag(gaz_dbi_last_id(), $form['tipdoc'], $form['numdoc'], $form['seziva'], $datemi, $form['clfoco'], $form['sconto'], $form['caumag'], $form['rows'][$i]['codart'], $form['rows'][$i]['quanti'], $form['rows'][$i]['prelis'], $form['rows'][$i]['sconto'], 0, $admin_aziend['stock_eval_method'], false, $form['protoc'], $form['rows'][$i]['id_lotmag']);
						if ($form['rows'][$i]['SIAN'] > 0) { // se l'articolo deve movimentare il SIAN creo anche il movimento
							$value_sian['cod_operazione']= $form['rows'][$i]['cod_operazione'];
							$value_sian['recip_stocc']= $form['rows'][$i]['recip_stocc'];
							$value_sian['recip_stocc_destin']= $form['rows'][$i]['recip_stocc_destin'];
							$value_sian['id_movmag']=$id_mag;
							gaz_dbi_table_insert('camp_mov_sian', $value_sian);
						}
                    }
                    $last_rigdoc_id = gaz_dbi_last_id();
                    if (isset($form["row_$i"])) { //se è un rigo testo lo inserisco il contenuto in body_text
                        bodytextInsert(array('table_name_ref' => 'rigdoc', 'id_ref' => $last_rigdoc_id, 'body_text' => $form["row_$i"], 'lang_id' => $admin_aziend['id_language']));
                        gaz_dbi_put_row($gTables['rigdoc'], 'id_rig', $last_rigdoc_id, 'id_body_text', gaz_dbi_last_id());
                    }
                }
                //modifico la testata con i nuovi dati...
                $old_head = gaz_dbi_get_row($gTables['tesdoc'], 'id_tes', $form['id_tes']);
                if (substr($form['tipdoc'], 0, 2) == 'DD') { //se è un DDT non fatturato
                    $form['datfat'] = '';
                    $form['numfat'] = 0;
                } elseif ($form['tipdoc'] == 'FAD') {  // se è fatturato
                    $form['datfat'] = $old_head['datfat'];
                    $form['numfat'] = $old_head['numfat'];
                } else {
                    $form['datfat'] = $datemi;
                    $form['numfat'] = $old_head['numfat'];
                }
				if ($form['tipdoc']=="VCO"){
					$form['initra'] = "0000-00-00";
				} else {
					$form['initra'] = $initra;
				}
                $form['ddt_type'] = $old_head['ddt_type'];
                $form['geneff'] = $old_head['geneff'];
                $form['id_contract'] = $old_head['id_contract'];
                $form['id_con'] = $old_head['id_con'];
                $form['status'] = $old_head['status'];
                
                $form['datemi'] = $datemi;
                $codice = array('id_tes', $form['id_tes']);
                /** inizio modifica FP 19/10/2015 */
                $form['data_ordine'] = $datemi = $form['annord'] . "-" . $form['mesord'] . "-" . $form['gioord'];
                /** fine modifica FP */
                tesdocUpdate($codice, $form);
                header("Location: " . $form['ritorno']);
                exit;
            } else { // e' un'inserimento
                // ricavo i progressivi in base al tipo di documento
                $where = " fattura DESC";
                switch ($form['tipdoc']) {
                    case "DDT":
                    case "DDV": // conto visione
                    case "DDY": // triangolazione
                    case "DDS": // notula di servizio
                        $sql_documento = "YEAR(datemi) = " . substr($datemi,0,4) . " AND ( tipdoc like 'DD_' or (tipdoc = 'FAD' && ddt_type != 'R')) and seziva = $sezione";
                        $where = "numdoc DESC";
                        $sql_protocollo = " 0";
                        break;
                    case "CMR":
                        $sql_documento = "YEAR(datemi) = " . substr($datemi,0,4) . " AND ( tipdoc like 'CMR' or (tipdoc = 'FAD' && ddt_type = 'R')) and seziva = $sezione";
                        $where = "numdoc DESC";
                        $sql_protocollo = " 0";
                        break;
                    case "FAI":
                    case "FAP":
                    case "FAA":
                    case "FAF":
                    case "FAQ":
                        $sql_documento = "YEAR(datfat) = " . substr($datemi,0,4) . " AND tipdoc LIKE 'FA_' AND seziva = $sezione ";
                        $sql_protocollo = "YEAR(datfat) = " . substr($datemi,0,4) . " AND tipdoc LIKE 'F__' AND seziva = $sezione ";
                        break;
                    case "FNC":
                        $sql_documento = "YEAR(datfat) = " . substr($datemi,0,4) . " and tipdoc = 'FNC' and seziva = $sezione";
                        $sql_protocollo = "YEAR(datfat) = " . substr($datemi,0,4) . " and tipdoc like 'F__' and seziva = $sezione";
                        break;
                    case "FND":
                        $sql_documento = "YEAR(datfat) = " . substr($datemi,0,4) . " and tipdoc = 'FND' and seziva = $sezione";
                        $sql_protocollo = "YEAR(datfat) = " . substr($datemi,0,4) . " and tipdoc like 'F__' and seziva = $sezione";
                        break;
                    case "RDV": // reso da visione
                        $sql_documento = "YEAR(datemi) = " . substr($datemi,0,4) . " AND  tipdoc = 'RDV' and seziva = $sezione";
                        $where = "numdoc DESC";
                        $sql_protocollo = " 0";
			break;
                    case "VRI": // Vendita con ricevuta
                        $sql_documento = "YEAR(datemi) = " . substr($datemi,0,4) . " AND  tipdoc = 'VRI' and seziva = $sezione";
                        $where = "numdoc DESC";
                        $sql_protocollo = "YEAR(datfat) = " . substr($datemi,0,4) . " AND tipdoc LIKE 'VRI' AND seziva = $sezione ";
			break;
                }
                $rs_ultimo_documento = gaz_dbi_dyn_query("numdoc, numfat*1 AS fattura", $gTables['tesdoc'], $sql_documento, $where, 0, 1);
                $ultimo_documento = gaz_dbi_fetch_array($rs_ultimo_documento);
                // se e' il primo documento dell'anno, resetto il contatore
                if ($ultimo_documento) {
                    $form['numfat'] = $ultimo_documento['fattura'] + 1;
                    $form['numdoc'] = $ultimo_documento['numdoc'] + 1;
                } else {
                    $form['numfat'] = 1;
                    $form['numdoc'] = 1;
                }
                $rs_ultimo_protocollo = gaz_dbi_dyn_query("protoc", $gTables['tesdoc'], $sql_protocollo, "protoc desc", 0, 1);
                $ultimo_protocollo = gaz_dbi_fetch_array($rs_ultimo_protocollo);
                // se e' il primo protocollo dell'anno, resetto il contatore
                if ($ultimo_protocollo) {
                    $form['protoc'] = $ultimo_protocollo['protoc'] + 1;
                } else {
                    $form['protoc'] = 1;
                }
                if (substr($form['tipdoc'], 0, 2) == 'DD' || $form['tipdoc']=='CMR') {  //ma se e' un ddt il protocollo è 0 cosè come il numero e data fattura
                    $form['protoc'] = 0;
                    $form['numfat'] = 0;
                    $form['datfat'] = 0;
                    $form['status'] = 'FATTURARE';
                    $form['ddt_type'] = substr($form['tipdoc'], -1);
                } else { //in tutti gli altri casi coincide con il numero documento.
                    $form['numdoc'] = $form['numfat'];
                    $form['datfat'] = $datemi;
                    $form['status'] = 'DA CONTAB';
                }
                //inserisco la testata
                $form['initra'] = $initra;
                $form['datemi'] = $datemi;
                /** inizio modifica FP 19/10/2015 */
                $form['data_ordine'] = $datemi = $form['annord'] . "-" . $form['mesord'] . "-" . $form['gioord'];
                /** fine modifica FP */
                tesdocInsert($form);
                //recupero l'id assegnato dall'inserimento
                $ultimo_id = gaz_dbi_last_id();
                //inserisco i righi
                foreach ($form['rows'] as $i => $v) {
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
                        if ( $tipo_composti['val']=="STD" || $form['rows'][$i+1]['tiprig']!=210 ) {
							$id_mag=$upd_mm->uploadMag($last_rigdoc_id, $form['tipdoc'], $form['numdoc'], $form['seziva'], $datemi, $form['clfoco'], $form['sconto'], $form['caumag'], $form['rows'][$i]['codart'], $form['rows'][$i]['quanti'], $form['rows'][$i]['prelis'], $form['rows'][$i]['sconto'], 0, $admin_aziend['stock_eval_method'], false, $form['protoc'], $form['rows'][$i]['id_lotmag']);
							gaz_dbi_put_row($gTables['rigdoc'], 'id_rig', $last_rigdoc_id, 'id_mag', $id_mag); // inserisco il riferimento mov mag nel rigo doc
							if ($form['rows'][$i]['SIAN']>0){// se l'articolo movimenta il SIAN creo il movimento SIAN
								$value_sian['cod_operazione']= $form['rows'][$i]['cod_operazione'];
								$value_sian['recip_stocc']= $form['rows'][$i]['recip_stocc'];
								$value_sian['recip_stocc_destin']= $form['rows'][$i]['recip_stocc_destin'];
								$value_sian['id_movmag']=$id_mag;
								gaz_dbi_table_insert('camp_mov_sian', $value_sian);
							}
                        }
                    }
                    //se è un'articolo composto scarico gli articoli presenti nelle righe di tipo 210
                    if ($admin_aziend['conmag'] == 2 &&
                        $form['rows'][$i]['tiprig'] == 210 &&
                        !empty($form['rows'][$i]['codart'])) {
                            $upd_mm->uploadMag($last_rigdoc_id, $form['tipdoc'], $form['numdoc'], $form['seziva'], $datemi, $form['clfoco'], $form['sconto'], $form['caumag'], $form['rows'][$i]['codart'], $form['rows'][$i]['quanti'], $form['rows'][$i]['prelis'], $form['rows'][$i]['sconto'], 0, $admin_aziend['stock_eval_method'], false, $form['protoc'], $form['rows'][$i]['id_lotmag']);
                    }
                }
                if ($form['id_doc_ritorno'] > 0) { // è un RDV pertanto non lo stampo e inserisco il riferimento sulla testata relativa
                    gaz_dbi_put_row($gTables['tesdoc'], 'id_tes', $form['id_doc_ritorno'], 'id_doc_ritorno', $ultimo_id);
                    header("Location: report_doctra.php");
                    exit;
                } else {
                    $_SESSION['print_request'] = $ultimo_id;
                    header("Location: invsta_docven.php");
                    exit;
                }
            }
        }
    }

    // Se viene cambiata la tipologia di documento e la nuova è una fattura immediata ricontrollo la modalitè di assegnazione della sezione IVA
    if ($_POST['hidden_req'] == 'tipdoc') {
        $form['seziva'] = getFAIseziva($form['tipdoc']);
        $form['hidden_req'] = '';
    }
    // Se viene inviata la richiesta di conferma cliente
    if ($_POST['hidden_req'] == 'clfoco') {
        $anagrafica = new Anagrafica();
        if (preg_match("/^id_([0-9]+)$/", $form['clfoco'], $match)) {
            $cliente = $anagrafica->getPartnerData($match[1], 1);
        } else {
            $cliente = $anagrafica->getPartner($form['clfoco']);
        }
        $result = gaz_dbi_get_row($gTables['imball'], "codice", $cliente['imball']);
        $form['imball'] = $result['descri'];
        if (($form['net_weight'] - $form['gross_weight']) >= 0) {
            $form['gross_weight'] += $result['weight'];
        }
        $result = gaz_dbi_get_row($gTables['portos'], "codice", $cliente['portos']);
        $form['portos'] = $result['descri'];
        $result = gaz_dbi_get_row($gTables['spediz'], "codice", $cliente['spediz']);
        $form['spediz'] = $result['descri'];
        $form['destin'] = $cliente['destin'];
        $form['id_agente'] = $cliente['id_agente'];
        if ($form['id_agente'] > 0) { // carico la provvigione standard
            $provvigione = new Agenti;
            $form['in_provvigione'] = $provvigione->getPercent($form['id_agente']);
            if (isset($_POST['rows'])) {  // aggiorno le provvigioni sui righi
                foreach ($_POST['rows'] as $k => $val) {
                    $form['rows'][$k]['provvigione'] = $provvigione->getPercent($form['id_agente'], $val['codart']);
                }
            }
        }

        $form['id_des'] = $cliente['id_des'];
        $id_des = $anagrafica->getPartner($form['id_des']);
        $form['search']['id_des'] = (empty($id_des)) ?: substr($id_des['ragso1'], 0, 10);
//        $des_same = gaz_dbi_get_row($gTables['destina'], "id_anagra", $cliente['id_anagra']);
//        $form['id_des_same_company'] = $des_same['codice'];
        $form['in_codvat'] = $cliente['aliiva'];
        if ($cliente['cosric'] >= 100000000) {
            $form['in_codric'] = $cliente['cosric'];
        }
		if ($cliente['sconto_rigo']>=0.01){
			$form['in_sconto'] = $cliente['sconto_rigo'];
		}
        $form['expense_vat'] = $admin_aziend['preeminent_vat'];
        $form['split_payment'] = gaz_dbi_get_row($gTables['aliiva'], "codice", $form['expense_vat'])['tipiva'];
        if ($cliente['aliiva'] > 0) {
            $form['expense_vat'] = $cliente['aliiva'];
			$form['split_payment'] = gaz_dbi_get_row($gTables['aliiva'], "codice", $cliente['aliiva'])['tipiva'];
       }
        if ($cliente['ritenuta'] > 0) { // carico la ritenuta se previsto
            $form['in_ritenuta'] = $cliente['ritenuta'];
        }
        if ($cliente['addbol'] != 'S' && $form['virtual_taxstamp'] > 1) { // in caso di cliente senza addebito di bollo virtuale
            $form['virtual_taxstamp'] = 3;  // forzo al nuovo modo 3 (bollo a carico dell'emittente)
        }
        $form['sconto'] = $cliente['sconto'];
        $form['pagame'] = $cliente['codpag'];
        $form['change_pag'] = $cliente['codpag'];
        $form['banapp'] = $cliente['banapp'];
        $form['listin'] = $cliente['listin'];
        $pagame = gaz_dbi_get_row($gTables['pagame'], "codice", $form['pagame']);
        if (($pagame['tippag'] == 'B' or $pagame['tippag'] == 'T' or $pagame['tippag'] == 'V') && $cliente['speban'] == 'S') {
            $form['speban'] = $admin_aziend['sperib'];
            $form['numrat'] = $pagame['numrat'];
            $form['stamp'] = 0;
            $form['round_stamp'] = $admin_aziend['round_bol'];
        } elseif ($pagame['tippag'] == 'R') {
            $form['speban'] = 0.00;
            $form['numrat'] = 1;
            $form['stamp'] = $admin_aziend['taxstamp'];
            $form['round_stamp'] = 0;
        } else {
            $form['speban'] = 0.00;
            $form['numrat'] = 1;
            $form['stamp'] = 0;
            $form['round_stamp'] = 0;
        }
        if ($pagame['tippag'] == 'T' && $cliente['addbol'] == 'S') {
            $form['stamp'] = $admin_aziend['perbol'];
        }
        $form['hidden_req'] = '';
    }

    // Se viene modificato l'agente
    if ($_POST['hidden_req'] == 'AGENTE') {
        if ($form['id_agente'] > 0) { // carico la provvigione standard
            $provvigione = new Agenti;
            $form['in_provvigione'] = $provvigione->getPercent($form['id_agente']);
            if (isset($_POST['rows'])) {  // aggiorno le provvigioni sui righi
                foreach ($_POST['rows'] as $k => $val) {
                    $form['rows'][$k]['provvigione'] = $form['in_provvigione'];
                    $form['rows'][$k]['provvigione'] = $provvigione->getPercent($form['id_agente'], $val['codart']);
                }
            }
        }
        $form['hidden_req'] = '';
    }

    // Se viene inviata la richiesta di conferma rigo

    /** ENRICO FEDELE */
    /* Con button non funziona _x */
    //if (isset($_POST['in_submit_x'])) {
    /** ENRICO FEDELE */
	if (isset($_POST['in_submit_desc'])) { //rigo Descrittivo rapido
        $form['rows'][$next_row]['codart'] = '';
        $form['rows'][$next_row]['annota'] = '';
        $form['rows'][$next_row]['pesosp'] = '';
        $form['rows'][$next_row]['gooser'] = 0;
        $form['rows'][$next_row]['unimis'] = '';
        $form['rows'][$next_row]['quanti'] = 0;
        $form['rows'][$next_row]['prelis'] = 0;
        $form['rows'][$next_row]['codric'] = 0;
        $form['rows'][$next_row]['sconto'] = 0;
        $form['rows'][$next_row]['pervat'] = 0;
        $form['rows'][$next_row]['tipiva'] = 0;
        $form['rows'][$next_row]['ritenuta'] = 0;
        $form['rows'][$next_row]['codvat'] = 0;
        $form['rows'][$next_row]['lot_or_serial'] = 0;
		$form['rows'][$next_row]['SIAN'] = 0;
        $form['rows'][$next_row]['id_lotmag'] = 0;
		$form['rows'][$next_row]['identifier'] = '';
		$form['rows'][$next_row]['cod_operazione'] = 11;
		$form['rows'][$next_row]['recip_stocc'] = '';
		$form['rows'][$next_row]['recip_stocc_destin'] = '';
        $form['rows'][$next_row]['descri'] = '';
        $form['rows'][$next_row]['id_mag'] = 0;
        $form['rows'][$next_row]['status'] = 'INSERT';
        $form['rows'][$next_row]['scorta'] = 0;
        $form['rows'][$next_row]['quamag'] = 0;
        $form['rows'][$next_row]['tiprig'] = 2;
        $next_row++;
    } else if (isset($_POST['in_submit_text'])) { //rigo Testo rapido
        $form["row_$next_row"] = '';
        $form['rows'][$next_row]['codart'] = '';
        $form['rows'][$next_row]['annota'] = '';
        $form['rows'][$next_row]['pesosp'] = '';
        $form['rows'][$next_row]['gooser'] = 0;
        $form['rows'][$next_row]['unimis'] = '';
        $form['rows'][$next_row]['quanti'] = 0;
        $form['rows'][$next_row]['prelis'] = 0;
        $form['rows'][$next_row]['codric'] = 0;
        $form['rows'][$next_row]['sconto'] = 0;
        $form['rows'][$next_row]['pervat'] = 0;
        $form['rows'][$next_row]['tipiva'] = 0;
        $form['rows'][$next_row]['ritenuta'] = 0;
        $form['rows'][$next_row]['codvat'] = 0;
        $form['rows'][$next_row]['lot_or_serial'] = 0;
		$form['rows'][$next_row]['SIAN'] = 0;
        $form['rows'][$next_row]['id_lotmag'] = 0;
		$form['rows'][$next_row]['identifier'] = '';
		$form['rows'][$next_row]['cod_operazione'] = 11;
		$form['rows'][$next_row]['recip_stocc'] = '';
		$form['rows'][$next_row]['recip_stocc_destin'] = '';
        $form['rows'][$next_row]['descri'] = '';
        $form['rows'][$next_row]['id_mag'] = 0;
        $form['rows'][$next_row]['status'] = 'INSERT';
        $form['rows'][$next_row]['scorta'] = 0;
        $form['rows'][$next_row]['quamag'] = 0;
        $form['rows'][$next_row]['tiprig'] = 6;
        $next_row++;
	} else if (isset($_POST['in_submit_cig'])) { //rigo CIG rapido
        $form['rows'][$next_row]['codart'] = '';
        $form['rows'][$next_row]['annota'] = '';
        $form['rows'][$next_row]['pesosp'] = '';
        $form['rows'][$next_row]['gooser'] = 0;
        $form['rows'][$next_row]['unimis'] = '';
        $form['rows'][$next_row]['quanti'] = 0;
        $form['rows'][$next_row]['prelis'] = 0;
        $form['rows'][$next_row]['codric'] = 0;
        $form['rows'][$next_row]['sconto'] = 0;
        $form['rows'][$next_row]['pervat'] = 0;
        $form['rows'][$next_row]['tipiva'] = 0;
        $form['rows'][$next_row]['ritenuta'] = 0;
        $form['rows'][$next_row]['codvat'] = 0;
        $form['rows'][$next_row]['lot_or_serial'] = 0;
		$form['rows'][$next_row]['SIAN'] = 0;
        $form['rows'][$next_row]['id_lotmag'] = 0;
		$form['rows'][$next_row]['identifier'] = '';
		$form['rows'][$next_row]['cod_operazione'] = 11;
		$form['rows'][$next_row]['recip_stocc'] = '';
		$form['rows'][$next_row]['recip_stocc_destin'] = '';
        $form['rows'][$next_row]['descri'] = '';
        $form['rows'][$next_row]['id_mag'] = 0;
        $form['rows'][$next_row]['status'] = 'INSERT';
        $form['rows'][$next_row]['scorta'] = 0;
        $form['rows'][$next_row]['quamag'] = 0;
        $form['rows'][$next_row]['tiprig'] = 11;
        $next_row++;
    } else if (isset($_POST['in_submit'])) {

        $artico = gaz_dbi_get_row($gTables['artico'], "codice", $form['in_codart']);
        if (!$artico) $artico=array('codart'=>'','sconto'=>'','annota'=>'','peso_specifico'=>0,'SIAN'=>0,'volume_specifico'=>0,'preve1'=>0,'pack_units'=>0,'retention_tax'=>0,'good_or_service'=>'','lot_or_serial'=>'','descri'=>'','unimis'=>'','codcon'=>'','aliiva'=>0,'scorta'=>0,'payroll_tax'=>0);
        // addizione ai totali peso,pezzi,volume, ma se l'unità di misura è uguale a KG forzo il peso specifico ad 1, ed in futuro qui dovrei utilizzare il nuovo metodo di calcolo utilizzato anche in acquis/admin_broven.php
		if (isset($artico) && strtoupper(substr($artico['unimis'],0,2))=='KG'){
			$artico['peso_specifico']=1;	
		}
        $form['net_weight'] += $form['in_quanti'] * $artico['peso_specifico'];
        $form['gross_weight'] += $form['in_quanti'] * $artico['peso_specifico'];
        if ($artico['pack_units'] > 0) {
            $form['units'] += intval(round($form['in_quanti'] / $artico['pack_units']));
        }
        $form['volume'] += (isset($artico)) ? $form['in_quanti'] * $artico['volume_specifico'] : 0;
        // fine addizione peso,pezzi,volume
        if (substr($form['in_status'], 0, 6) == "UPDROW") { //se è un rigo da modificare
            $old_key = intval(substr($form['in_status'], 6));
            $form['rows'][$old_key]['tiprig'] = $form['in_tiprig'];
			if ($form['in_tiprig']<=1 || $form['in_tiprig']==90){
				$form['RiferimentoNumeroLinea'][$old_key+1] = substr($form['in_descri'],0,20);
			}
            $form['rows'][$old_key]['descri'] = $form['in_descri'];
            $form['rows'][$old_key]['lot_or_serial'] = $form['in_lot_or_serial'];
			$form['rows'][$old_key]['SIAN'] = $form['in_SIAN'];
            $form['rows'][$old_key]['id_lotmag'] = $form['in_id_lotmag'];
			$form['rows'][$old_key]['identifier'] = $form['in_identifier'];
			$form['rows'][$old_key]['cod_operazione'] = $form['in_cod_operazione'];
			$form['rows'][$old_key]['recip_stocc'] = $form['in_recip_stocc'];
			$form['rows'][$old_key]['recip_stocc_destin'] = $form['in_recip_stocc_destin'];
            $form['rows'][$old_key]['id_mag'] = $form['in_id_mag'];
            $form['rows'][$old_key]['status'] = "UPDATE";
            $form['rows'][$old_key]['unimis'] = $form['in_unimis'];
            $form['rows'][$old_key]['quanti'] = $form['in_quanti'];
            $form['rows'][$old_key]['codart'] = $form['in_codart'];
            $form['rows'][$old_key]['codric'] = $form['in_codric'];
            $form['rows'][$old_key]['ritenuta'] = $form['in_ritenuta'];
            $form['rows'][$old_key]['provvigione'] = $form['in_provvigione']; // in caso tiprig=4 questo campo è utilizzato per indicare l'aliquota della cassa previdenziale
            $form['rows'][$old_key]['prelis'] = number_format($form['in_prelis'], $admin_aziend['decimal_price'], '.', '');
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
              $iva_row = gaz_dbi_get_row($gTables['aliiva'],"codice",$form['in_codvat']);
              $form['rows'][$old_key]['pervat'] = $iva_row['aliquo'];
              $form['rows'][$old_key]['tipiva'] = $iva_row['tipiva']; */
            $form['rows'][$old_key]['scorta'] = 0;
            $form['rows'][$old_key]['quamag'] = 0;
            $form['rows'][$old_key]['annota'] = '';
            $form['rows'][$old_key]['pesosp'] = '';
            $form['rows'][$old_key]['gooser'] = 0;
            if ($form['in_tiprig'] == 0 and ! empty($form['in_codart'])) {  //rigo normale
                $form['rows'][$old_key]['annota'] = $artico['annota'];
                $form['rows'][$old_key]['pesosp'] = $artico['peso_specifico'];
                $form['rows'][$old_key]['gooser'] = $artico['good_or_service'];
                $form['rows'][$old_key]['lot_or_serial'] = $artico['lot_or_serial'];
				$form['rows'][$old_key]['SIAN'] = $artico['SIAN'];
                /* devo ricaricare un nuovo id lotmag
                 */
                if ($artico['lot_or_serial'] > 0) {
                    $lm->getAvailableLots($form['in_codart'], $form['in_id_mag']);
                    $ld = $lm->divideLots($form['in_quanti']);
                    foreach ($lm->divided as $k => $v) {
                        if ($v['qua'] >= 0.00001) {                            
                            $form['rows'][$old_key]['id_lotmag'] = $k; // setto il lotto
                           	$getlot = $lm->getLot($form['rows'][$old_key]['id_lotmag']);
							$form['rows'][$old_key]['identifier'] = $getlot['identifier'];
                        }
                    }
				}
				
                $form['rows'][$old_key]['unimis'] = $artico['unimis'];
                $form['rows'][$old_key]['descri'] = $artico['descri'];
                if ($form['listin'] == 2) {
                    $form['rows'][$old_key]['prelis'] = number_format($artico['preve2'], $admin_aziend['decimal_price'], '.', '');
                } elseif ($form['listin'] == 3) {
                    $form['rows'][$old_key]['prelis'] = number_format($artico['preve3'], $admin_aziend['decimal_price'], '.', '');
                } elseif ($form['listin'] == 4) {
                    $form['rows'][$old_key]['prelis'] = number_format($artico['preve4'], $admin_aziend['decimal_price'], '.', '');
                } else {
                    $form['rows'][$old_key]['prelis'] = number_format($artico['preve1'], $admin_aziend['decimal_price'], '.', '');
                }
                $mv = $upd_mm->getStockValue(false, $form['in_codart'], $datemi, $admin_aziend['stock_eval_method']);
                $magval = array_pop($mv);
                $magval=(is_numeric($magval))?['q_g'=>0,'v_g'=>0]:$magval;
                $form['rows'][$old_key]['scorta'] = $artico['scorta'];
                $form['rows'][$old_key]['quamag'] = $magval['q_g'];
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
                $form['rows'][$old_key]['tipiva'] = 0;
                $form['rows'][$old_key]['ritenuta'] = 0;
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
            } elseif ($form['in_tiprig'] == 4) { //rigo cassa previdenziale
                $form['rows'][$old_key]['unimis'] = "";
                $form['rows'][$old_key]['quanti'] = 0;
                $form['rows'][$old_key]['sconto'] = 0;
            } elseif ($form['in_tiprig'] == 11 || $form['in_tiprig'] == 12 
					|| $form['in_tiprig'] == 13 || $form['in_tiprig'] == 14 
					|| $form['in_tiprig'] == 15 || $form['in_tiprig'] == 16 
					|| $form['in_tiprig'] == 21 || $form['in_tiprig'] == 25 
					|| $form['in_tiprig'] == 31) { //per  fattura elettronica riferibili ad altri righi o a tutto il documento
                $form['rows'][$old_key]['codart'] = "";
                $form['rows'][$old_key]['annota'] = "";
                $form['rows'][$old_key]['pesosp'] = "";
                $form['rows'][$old_key]['gooser'] = 0;
                $form['rows'][$old_key]['unimis'] = "";
                $form['rows'][$old_key]['quanti'] = 0;
                $form['rows'][$old_key]['prelis'] = 0;
                $form['rows'][$old_key]['sconto'] = 0;
                $form['rows'][$old_key]['pervat'] = 0;
                $form['rows'][$old_key]['tipiva'] = 0;
                $form['rows'][$old_key]['ritenuta'] = 0;
                $form['rows'][$old_key]['codvat'] = 0;
            } elseif ($form['in_tiprig'] == 90) {   // vendita cespite ammortizzabile
                $form['rows'][$old_key]['codart'] = "";
                $form['rows'][$old_key]['quanti'] = 0;
                $form['rows'][$old_key]['unimis'] = "";
                $form['rows'][$old_key]['sconto'] = 0;
            }
            ksort($form['rows']);
        } else { //se è un rigo da inserire
            if ($form['in_tiprig'] == 0) {   // è un rigo normale controllo se l'articolo prevede un rigo testuale che lo precede
				$article_text = gaz_dbi_get_row($gTables['company_config'], 'var', 'article_text');
				if ($article_text['val'] < 2){
					$bodytext = gaz_dbi_get_row($gTables['body_text'], "table_name_ref", 'artico_' . $form['in_codart']);
				} else {
					$bodytext = '';
				}
                if (!empty($bodytext) && !empty($bodytext['body_text'])) { // il testo aggiuntivo c'è (e non è vuoto)
                    $form["row_$next_row"] = $bodytext['body_text'];
                    $form['rows'][$next_row]['tiprig'] = 6;
                    $form['rows'][$next_row]['descri'] = '';
                    $form['rows'][$next_row]['id_mag'] = 0;
                    $form['rows'][$next_row]['id_lotmag'] = 0;
					$form['rows'][$next_row]['identifier'] = '';
					$form['rows'][$next_row]['cod_operazione'] = 11;
					$form['rows'][$next_row]['recip_stocc'] = '';
					$form['rows'][$next_row]['recip_stocc_destin'] = '';
                    $form['rows'][$next_row]['lot_or_serial'] = 0;
					$form['rows'][$next_row]['SIAN'] = 0;
                    $form['rows'][$next_row]['status'] = '';
                    $form['rows'][$next_row]['scorta'] = 0;
                    $form['rows'][$next_row]['quamag'] = 0;
                    $form['rows'][$next_row]['codart'] = '';
                    $form['rows'][$next_row]['annota'] = '';
                    $form['rows'][$next_row]['pesosp'] = '';
                    $form['rows'][$next_row]['gooser'] = 0;
                    $form['rows'][$next_row]['unimis'] = '';
                    $form['rows'][$next_row]['quanti'] = 0;
                    $form['rows'][$next_row]['prelis'] = 0;
                    $form['rows'][$next_row]['codric'] = 0;
                    $form['rows'][$next_row]['sconto'] = 0;
                    $form['rows'][$next_row]['pervat'] = 0;
                    $form['rows'][$next_row]['tipiva'] = 0;
                    $form['rows'][$next_row]['ritenuta'] = 0;
                    $form['rows'][$next_row]['codvat'] = 0;
                    $next_row++;
                }
            }
            $form['rows'][$next_row]['lot_or_serial'] = 0;
			$form['rows'][$next_row]['SIAN'] = 0;
            $form['rows'][$next_row]['id_lotmag'] = 0;
			$form['rows'][$next_row]['identifier'] = "";
			$form['rows'][$next_row]['cod_operazione'] = 11;
			$form['rows'][$next_row]['recip_stocc'] = "";
			$form['rows'][$next_row]['recip_stocc_destin'] = "";
            $form['rows'][$next_row]['tiprig'] = $form['in_tiprig'];
			if ($form['in_tiprig']<=1 || $form['in_tiprig']==90){
				$form['RiferimentoNumeroLinea'][$next_row+1] = substr($form['in_descri'],0,20);
			}
            $form['rows'][$next_row]['descri'] = $form['in_descri'];
            $form['rows'][$next_row]['id_mag'] = $form['in_id_mag'];
            $form['rows'][$next_row]['status'] = "INSERT";
            $form['rows'][$next_row]['scorta'] = 0;
            $form['rows'][$next_row]['quamag'] = 0;
            $form['rows'][$next_row]['ritenuta'] = $form['in_ritenuta'];
            if ($form['in_tiprig'] == 0) {  //rigo normale
                $form['rows'][$next_row]['codart'] = $form['in_codart'];
                $form['rows'][$next_row]['annota'] = $artico['annota'];
                $form['rows'][$next_row]['pesosp'] = $artico['peso_specifico'];
                $form['rows'][$next_row]['gooser'] = $artico['good_or_service'];
                $form['rows'][$next_row]['lot_or_serial'] = $artico['lot_or_serial'];
				$form['rows'][$next_row]['SIAN'] = $artico['SIAN'];
                $form['rows'][$next_row]['descri'] = $artico['descri'];
                $form['rows'][$next_row]['unimis'] = $artico['unimis'];
                $form['rows'][$next_row]['prelis'] = number_format($form['in_prelis'], $admin_aziend['decimal_price'], '.', '');
                $form['rows'][$next_row]['codric'] = $form['in_codric'];
                $form['rows'][$next_row]['quanti'] = $form['in_quanti'];
                $form['rows'][$next_row]['sconto'] = $form['in_sconto'];

                /** inizio modifica FP 09/10/2015
                 * se non ho inserito uno sconto nella maschera prendo quello standard registrato nell'articolo
                 */
                //rimossa            $form['rows'][$next_row]['sconto'] = $form['in_sconto'];
                $in_sconto = $form['in_sconto'];
                if ($in_sconto != "#") {
                    $form['rows'][$next_row]['sconto'] = $in_sconto;
                } else {
					if ($form["sconto"] > 0) { // gestione sconto cliente sul totale merce o sul rigo
                        $form['rows'][$next_row]['sconto'] = 0;
					} else {
						$comp = new venditCalc();
						$tmpPrezzoNetto_Sconto = $comp->trovaPrezzoNetto_Sconto(((isset($cliente))?$cliente['codice']:''), $form['rows'][$next_row]['codart'], $artico['sconto']);
						if ($tmpPrezzoNetto_Sconto < 0) { // è un prezzo netto
							$form['rows'][$next_row]['prelis'] = -$tmpPrezzoNetto_Sconto;
							$form['rows'][$next_row]['sconto'] = 0;
						} else {
							$form['rows'][$next_row]['sconto'] = $tmpPrezzoNetto_Sconto;
						}
                    }
					/*                     * ** TODO da modificare */
//               $form['rows'][$next_row]['sconto'] = $artico['sconto'];
//               if ($artico['sconto'] != 0) {
//                  $msgtoast = $form['rows'][$next_row]['codart'] . ": sconto da anagrafe articoli";
//               }
                }
                /* fine modifica FP */
                if ($artico['retention_tax'] > 0) { // se richiesto dall'articolo impongo la ritenuta
                    $form['rows'][$next_row]['ritenuta'] = $admin_aziend['ritenuta'];
                }
                $provvigione = new Agenti;
                $form['rows'][$next_row]['provvigione'] = $provvigione->getPercent($form['id_agente'], $form['in_codart']);
                if (!isset($tmpPrezzoNetto_Sconto) or ( $tmpPrezzoNetto_Sconto >= 0)) { // non ho trovato un prezzo netto per il cliente/articolo
                    if ($form['listin'] == 2) {
                        $form['rows'][$next_row]['prelis'] = number_format($artico['preve2'], $admin_aziend['decimal_price'], '.', '');
                    } elseif ($form['listin'] == 3) {
                        $form['rows'][$next_row]['prelis'] = number_format($artico['preve3'], $admin_aziend['decimal_price'], '.', '');
                    } elseif ($form['listin'] == 4) {
                        $form['rows'][$next_row]['prelis'] = number_format($artico['preve4'], $admin_aziend['decimal_price'], '.', '');
                    } else {
                        $form['rows'][$next_row]['prelis'] = number_format($artico['preve1'], $admin_aziend['decimal_price'], '.', '');
                    }
                }
                $form['rows'][$next_row]['codvat'] = $admin_aziend['preeminent_vat'];
                $iva_azi = gaz_dbi_get_row($gTables['aliiva'], "codice", $admin_aziend['preeminent_vat']);
                $form['rows'][$next_row]['pervat'] = $iva_azi['aliquo'];
                $form['rows'][$next_row]['tipiva'] = $iva_azi['tipiva'];
                if ($artico['aliiva'] > 0) {
                    $form['rows'][$next_row]['codvat'] = $artico['aliiva'];
                    $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $artico['aliiva']);
                    $form['rows'][$next_row]['pervat'] = $iva_row['aliquo'];
                    $form['rows'][$next_row]['tipiva'] = $iva_row['tipiva'];
                }
                if ($form['in_codvat'] > 0) {
                    $form['rows'][$next_row]['codvat'] = $form['in_codvat'];
                    $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $form['in_codvat']);
                    $form['rows'][$next_row]['pervat'] = $iva_row['aliquo'];
                    $form['rows'][$next_row]['tipiva'] = $iva_row['tipiva'];
                }
				if ($form['split_payment']=='T' &&  $artico['aliiva'] > 0) { // ho in testata lo split payment ed un articolo con aliquota IVA impostata  allora eseguo i controlli ed il push per forzare, se la trovo, anche il nuovo rigo ad una aliquota con la stessa percentuale IVA ma in split payment (tipiva=T)
					$pervat_art=gaz_dbi_get_row($gTables['aliiva'], 'codice', $artico['aliiva']);
					$iva_isp=gaz_dbi_get_row($gTables['aliiva'], 'tipiva', 'T', "AND aliquo = '". $pervat_art['aliquo']."'");
					if ($iva_isp) { // ho una 
						$form['rows'][$next_row]['codvat'] = $iva_isp['codice'];
						$form['rows'][$next_row]['pervat'] = $iva_isp['aliquo'];
						$form['rows'][$next_row]['tipiva'] = 'T';
					} else {
						// allerto che non ho trovato una aliquota split_payment con quella percentuale
						$msg['war'][] = "aliiva_nosplit";
					}
				}
                if ($artico['codcon'] > 0) {
                    $form['rows'][$next_row]['codric'] = $artico['codcon'];
                    $form['in_codric'] = $artico['codcon'];
                } elseif (!empty($artico['codice'])) {
                    $form['rows'][$next_row]['codric'] = $admin_aziend['impven'];
                    $form['in_codric'] = $admin_aziend['impven'];
                }
                if ($form['tipdoc'] == 'FNC') { // nel caso che si tratti di nota di credito
                    $form['rows'][$next_row]['codric'] = $admin_aziend['sales_return'];
                    $form['in_codric'] = $admin_aziend['sales_return'];
                }
                $mv = $upd_mm->getStockValue(false, $form['in_codart'], $datemi, $admin_aziend['stock_eval_method']);
                $magval = array_pop($mv);
                $magval=(is_numeric($magval))?['q_g'=>0,'v_g'=>0]:$magval;
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
							$getlot = $lm->getLot($form['rows'][$i]['id_lotmag']);
							$form['rows'][$i]['identifier'] = $getlot['identifier'];
                            $i++;
                        }
                    }
                }
                if ($artico['payroll_tax'] > 0) {
                    /* se l'articolo impone anche un ulteriore rigo per la cassa previdenziale
                     * procedo con l'aggiunta di un ulteriore rigo di tipo 4 in base
                     * alla configurazione aziendale
                     */
                    $ptd = gaz_dbi_get_row($gTables['company_config'], 'var', 'payroll_tax_descri');
                    $nr = $next_row + 1;
                    $form['rows'][$nr]['tiprig'] = 4;
					$xml = simplexml_load_file('../../library/include/fae_tipo_cassa.xml');
					foreach ($xml->record as $vx) {
						if ($vx->field[0] == $admin_aziend['fae_tipo_cassa']) {
							$form['rows'][$nr]['descri']= 'Contributo '.strtolower($vx->field[1]);
						}
					}
                    $form['rows'][$nr]['codart'] =$admin_aziend['fae_tipo_cassa'];
					// su prelis ho l'imponibile cassa come se fosse un rigo forfait
					$form['rows'][$nr]['prelis'] = CalcolaImportoRigo($form['rows'][$next_row]['quanti'], $form['rows'][$next_row]['prelis'], $form['rows'][$next_row]['sconto']);
                    $form['rows'][$nr]['id_mag'] = 0;
                    $form['rows'][$nr]['id_lotmag'] = 0;
					$form['rows'][$nr]['identifier'] = "";
					$form['rows'][$nr]['cod_operazione'] = 11;
					$form['rows'][$nr]['recip_stocc'] = "";
					$form['rows'][$nr]['recip_stocc_destin'] = "";
                    $form['rows'][$nr]['lot_or_serial'] = 0;
					$form['rows'][$nr]['SIAN'] = 0;
                    $form['rows'][$nr]['status'] = "INSERT";
                    $form['rows'][$nr]['scorta'] = 0;
                    $form['rows'][$nr]['quamag'] = 0;
                    $form['rows'][$nr]['annota'] = "";
                    $form['rows'][$nr]['pesosp'] = 0;
                    $form['rows'][$nr]['gooser'] = 0;
                    $form['rows'][$nr]['unimis'] = "";
                    $form['rows'][$nr]['quanti'] = 0;
					// sul rigo provvigione ho l'aliquota della cassa pertanto il valore del contributo alla cassa la calcolerò in base a questa 
					$form['rows'][$nr]['provvigione'] = $admin_aziend['payroll_tax'];
                    $form['rows'][$nr]['codric'] = $admin_aziend['c_payroll_tax'];
                    $form['rows'][$nr]['sconto'] = 0;
                    $form['rows'][$nr]['codvat'] = $admin_aziend['preeminent_vat'];
                    $iva_azi = gaz_dbi_get_row($gTables['aliiva'], "codice", $admin_aziend['preeminent_vat']);
                    $form['rows'][$nr]['pervat'] = $iva_azi['aliquo'];
                    $form['rows'][$nr]['tipiva'] = $iva_azi['tipiva'];
					$form['rows'][$nr]['ritenuta'] = 0;
					if ($admin_aziend['ra_cassa']==1) {
						$form['rows'][$nr]['ritenuta'] = $admin_aziend['ritenuta'];
					}
                }
                if ($artico['good_or_service']==2 && $tipo_composti['val']=="KIT" ) {
                    $whe_dis = "codice_composizione = '".$form['in_codart']."'";
                    $res_dis = gaz_dbi_dyn_query('*', $gTables['distinta_base'], $whe_dis, 'id', 0, PER_PAGE);
                    while ($row = gaz_dbi_fetch_array($res_dis)) {
                        $next_row++;
                        $result2 = gaz_dbi_dyn_query('*', $gTables['artico'], " codice = '".$row['codice_artico_base']."'", 'codice', 0, PER_PAGE);
                        $row2 = gaz_dbi_fetch_array($result2);
                        $form['rows'][$next_row]['lot_or_serial'] = 0;
						$form['rows'][$next_row]['SIAN'] = 0;
                        $form['rows'][$next_row]['id_lotmag'] = 0;
						$form['rows'][$next_row]['identifier'] = "";
						$form['rows'][$next_row]['cod_operazione'] = 11;
						$form['rows'][$next_row]['recip_stocc'] = "";
						$form['rows'][$next_row]['recip_stocc_destin'] = "";
                        $form['rows'][$next_row]['tiprig'] = 210;
                        $form['rows'][$next_row]['descri'] = "";
                        $form['rows'][$next_row]['id_mag'] = "";
                        $form['rows'][$next_row]['status'] = "INSERT";
                        $form['rows'][$next_row]['scorta'] = 0;
                        $form['rows'][$next_row]['quamag'] = 0;
                        $form['rows'][$next_row]['ritenuta'] = "";
                        $form['rows'][$next_row]['codart'] = $row2['codice'];
                        $form['rows'][$next_row]['descri'] = $row2['descri'];
                        $form['rows'][$next_row]['unimis'] = $row2['unimis'];
                        $form['rows'][$next_row]['codric'] = "";
                        $form['rows'][$next_row]['quanti'] = $row['quantita_artico_base'];
                        $form['rows'][$next_row]['sconto'] = "";
                        $form['rows'][$next_row]['codvat'] = "";
                        $form['rows'][$next_row]['pervat'] = "";
                        $form['rows'][$next_row]['tipiva'] = "";
                        $form['rows'][$next_row]['annota'] = "";
                        $form['rows'][$next_row]['pesosp'] = "";
                        $form['rows'][$next_row]['gooser'] = 0;
                    }
                }
            } elseif ($form['in_tiprig'] == 1) { //forfait
                $form['rows'][$next_row]['codart'] = "";
                $form['rows'][$next_row]['annota'] = "";
                $form['rows'][$next_row]['pesosp'] = "";
                $form['rows'][$next_row]['gooser'] = 0;
                $form['rows'][$next_row]['unimis'] = "";
                $form['rows'][$next_row]['quanti'] = 0;
                $form['rows'][$next_row]['prelis'] = 0;
                $form['rows'][$next_row]['codric'] = $form['in_codric'];
                $form['rows'][$next_row]['sconto'] = 0;
                $form['rows'][$next_row]['codvat'] = $admin_aziend['preeminent_vat'];
                $iva_azi = gaz_dbi_get_row($gTables['aliiva'], "codice", $admin_aziend['preeminent_vat']);
                $form['rows'][$next_row]['pervat'] = $iva_azi['aliquo'];
                $form['rows'][$next_row]['tipiva'] = $iva_azi['tipiva'];
                if ($form['in_codvat'] > 0) {
                    $form['rows'][$next_row]['codvat'] = $form['in_codvat'];
                    $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $form['in_codvat']);
                    $form['rows'][$next_row]['pervat'] = $iva_row['aliquo'];
                    $form['rows'][$next_row]['tipiva'] = $iva_row['tipiva'];
                }
                $form['rows'][$next_row]['ritenuta'] = $form['in_ritenuta'];
            } elseif ($form['in_tiprig'] == 2) { //descittivo
                $form['rows'][$next_row]['codart'] = "";
                $form['rows'][$next_row]['annota'] = "";
                $form['rows'][$next_row]['pesosp'] = "";
                $form['rows'][$next_row]['gooser'] = 0;
                $form['rows'][$next_row]['unimis'] = "";
                $form['rows'][$next_row]['quanti'] = 0;
                $form['rows'][$next_row]['prelis'] = 0;
                $form['rows'][$next_row]['codric'] = 0;
                $form['rows'][$next_row]['sconto'] = 0;
                $form['rows'][$next_row]['pervat'] = 0;
                $form['rows'][$next_row]['tipiva'] = 0;
                $form['rows'][$next_row]['ritenuta'] = 0;
                $form['rows'][$next_row]['codvat'] = 0;
            } elseif ($form['in_tiprig'] == 3) {   //var tot
                $form['rows'][$next_row]['codart'] = "";
                $form['rows'][$next_row]['annota'] = "";
                $form['rows'][$next_row]['pesosp'] = "";
                $form['rows'][$next_row]['gooser'] = 0;
                $form['rows'][$next_row]['unimis'] = "";
                $form['rows'][$next_row]['quanti'] = 0;
                $form['rows'][$next_row]['prelis'] = number_format($form['in_prelis'], $admin_aziend['decimal_price'], '.', '');
                $form['rows'][$next_row]['codric'] = $form['in_codric'];
                $form['rows'][$next_row]['sconto'] = 0;
                $form['rows'][$next_row]['codvat'] = $form['in_codvat'];
                $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $form['in_codvat']);
                $form['rows'][$next_row]['pervat'] = $iva_row['aliquo'];
                $form['rows'][$next_row]['tipiva'] = $iva_row['tipiva'];
                $form['rows'][$next_row]['ritenuta'] = 0;
            } elseif ($form['in_tiprig'] == 4) { // cassa previdenziale
                $form['rows'][$next_row]['codart'] = $admin_aziend['fae_tipo_cassa'];// propongo quella aziendale uso il codice articolo
                $form['rows'][$next_row]['annota'] = "";
                $form['rows'][$next_row]['pesosp'] = "";
                $form['rows'][$next_row]['gooser'] = 0;
                $form['rows'][$next_row]['unimis'] = "";
                $form['rows'][$next_row]['quanti'] = 0;
                $form['rows'][$next_row]['prelis'] = 0;
				$form['rows'][$next_row]['provvigione'] = $form['in_provvigione'];
                $form['rows'][$next_row]['codric'] = $admin_aziend['c_payroll_tax'];
                $form['rows'][$next_row]['sconto'] = 0;
                $form['rows'][$next_row]['codvat'] = $admin_aziend['preeminent_vat'];
                $iva_azi = gaz_dbi_get_row($gTables['aliiva'], "codice", $admin_aziend['preeminent_vat']);
                $form['rows'][$next_row]['pervat'] = $iva_azi['aliquo'];
                $form['rows'][$next_row]['tipiva'] = $iva_azi['tipiva'];
                if ($form['in_codvat'] > 0) {
                    $form['rows'][$next_row]['codvat'] = $form['in_codvat'];
                    $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $form['in_codvat']);
                    $form['rows'][$next_row]['pervat'] = $iva_row['aliquo'];
                    $form['rows'][$next_row]['tipiva'] = $iva_row['tipiva'];
                }
				if ($form['in_ritenuta']<0.01 && $admin_aziend['ra_cassa']>=1){ // in azienda ho configurato di avere la ritenuta anche sulla cassa previdenziale
					$form['rows'][$next_row]['ritenuta'] = $admin_aziend['ritenuta'];
				} else {
					$form['rows'][$next_row]['ritenuta'] = $form['in_ritenuta'];
				}
				// carico anche la descrizione corrispondente dal file xml
	            $xml = simplexml_load_file('../../library/include/fae_tipo_cassa.xml');
				foreach ($xml->record as $v) {
					$selected = '';
					if ($v->field[0] == $form['rows'][$next_row]['codart']) {
						$form['rows'][$next_row]['descri']= 'Contributo '.strtolower($v->field[1]);
					}
				}

            } elseif ($form['in_tiprig'] > 5 && $form['in_tiprig'] < 9) { //testo
                $form["row_$next_row"] = "";
                $form['rows'][$next_row]['codart'] = "";
                $form['rows'][$next_row]['annota'] = "";
                $form['rows'][$next_row]['pesosp'] = "";
                $form['rows'][$next_row]['gooser'] = 0;
                $form['rows'][$next_row]['unimis'] = "";
                $form['rows'][$next_row]['quanti'] = 0;
                $form['rows'][$next_row]['prelis'] = 0;
                $form['rows'][$next_row]['codric'] = 0;
                $form['rows'][$next_row]['sconto'] = 0;
                $form['rows'][$next_row]['pervat'] = 0;
                $form['rows'][$next_row]['tipiva'] = 0;
                $form['rows'][$next_row]['ritenuta'] = 0;
                $form['rows'][$next_row]['codvat'] = 0;
            } elseif ($form['in_tiprig'] == 11 || $form['in_tiprig'] == 12 
					|| $form['in_tiprig'] == 13 || $form['in_tiprig'] == 14 
					|| $form['in_tiprig'] == 15 || $form['in_tiprig'] == 16 
					|| $form['in_tiprig'] == 21 || $form['in_tiprig'] == 25 
					|| $form['in_tiprig'] == 31) { //per  fattura elettronica riferibili ad altri righi o a tutto il documento
                $form['rows'][$next_row]['codart'] = "";
                $form['rows'][$next_row]['annota'] = "";
                $form['rows'][$next_row]['pesosp'] = "";
                $form['rows'][$next_row]['gooser'] = 0;
                $form['rows'][$next_row]['unimis'] = "";
                $form['rows'][$next_row]['quanti'] = 0;
                $form['rows'][$next_row]['prelis'] = 0;
                $form['rows'][$next_row]['codric'] = 0;
                $form['rows'][$next_row]['sconto'] = 0;
                $form['rows'][$next_row]['pervat'] = 0;
                $form['rows'][$next_row]['tipiva'] = 0;
                $form['rows'][$next_row]['ritenuta'] = 0;
                $form['rows'][$next_row]['codvat'] = 0;
            } elseif ($form['in_tiprig'] == 90) { // rigo vendita cespite ammortizzabile
                $form['rows'][$next_row]['codart'] = "";
                $form['rows'][$next_row]['annota'] = "";
                $form['rows'][$next_row]['pesosp'] = "";
                $form['rows'][$next_row]['gooser'] = 0;
                $form['rows'][$next_row]['unimis'] = "";
                $form['rows'][$next_row]['quanti'] = 0;
                $form['rows'][$next_row]['prelis'] = 0;
                $form['rows'][$next_row]['codric'] = $form['in_codric'];
                $form['rows'][$next_row]['sconto'] = 0;
                $form['rows'][$next_row]['codvat'] = $admin_aziend['preeminent_vat'];
                $iva_azi = gaz_dbi_get_row($gTables['aliiva'], "codice", $admin_aziend['preeminent_vat']);
                $form['rows'][$next_row]['pervat'] = $iva_azi['aliquo'];
                $form['rows'][$next_row]['tipiva'] = $iva_azi['tipiva'];
                if ($form['in_codvat'] > 0) {
                    $form['rows'][$next_row]['codvat'] = $form['in_codvat'];
                    $iva_row = gaz_dbi_get_row($gTables['aliiva'], "codice", $form['in_codvat']);
                    $form['rows'][$next_row]['pervat'] = $iva_row['aliquo'];
                    $form['rows'][$next_row]['tipiva'] = $iva_row['tipiva'];
                }
                $form['rows'][$next_row]['ritenuta'] = $form['in_ritenuta'];
            } elseif ($form['in_tiprig'] == 210) { // rigo vendita cespite ammortizzabile

            }
        }
        // reinizializzo rigo di input tranne che tipo rigo, aliquota iva, ritenuta e conto ricavo
        $form['in_descri'] = "";
        $form['in_codart'] = "";
        $form['in_unimis'] = "";
        $form['in_prelis'] = 0;
        $form['in_quanti'] = 0;
        $form['in_id_mag'] = 0;
        $form['in_annota'] = "";
        $form['in_scorta'] = 0;
        $form['in_quamag'] = 0;
        $form['in_pesosp'] = 0;
        $form['in_gooser'] = 0;
		$form['in_SIAN'] = 0;
        $form['in_status'] = "INSERT";
        // fine reinizializzo rigo input
        $form['cosear'] = "";
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
        if (isset($form["row_$upp_key"])) { //se sto spostando un rigo testo
            $form["row_$new_key"] = $form["row_$upp_key"];
            unset($form["row_$upp_key"]);
        } elseif(isset($form["row_$new_key"]))  { //se lo sto spostando dove prima c'era un rigo testo
            $form["row_$upp_key"] = $form["row_$new_key"];
            unset($form["row_$new_key"]);
        }
        $pull_row = $form['rows'][$new_key];
        $form['rows'][$new_key] = $form['rows'][$upp_key];
        $form['rows'][$upp_key] = $pull_row;
        ksort($form['rows']);
        unset($pull_row);
    }
    // Se viene inviata la richiesta elimina il rigo corrispondente
    if (isset($_POST['del'])) {
        $delri = key($_POST['del']);
        unset($form["RiferimentoNumeroLinea"][$delri+1]);
        // sottrazione ai totali peso,pezzi,volume
        $artico = gaz_dbi_get_row($gTables['artico'], "codice", $form['rows'][$delri]['codart']);
        $form['net_weight'] -= (isset($artico)) ? $form['rows'][$delri]['quanti'] * $artico['peso_specifico'] : 0;
        $form['gross_weight'] -= (isset($artico)) ? $form['rows'][$delri]['quanti'] * $artico['peso_specifico'] : 0;
        if (isset($artico) && $artico['pack_units'] > 0) {
            $form['units'] -= intval(round($form['rows'][$delri]['quanti'] / $artico['pack_units']));
        }
        $form['volume'] -= (isset($artico)) ? $form['rows'][$delri]['quanti'] * $artico['volume_specifico'] : 0;
        // fine sottrazione peso,pezzi,volume
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
        $next_row--;
    }
    if (isset($_POST['new_lotmag'])) {
        // assegno il rigo ad un nuovo lotto
        $row_lm = key($_POST['new_lotmag']);
        $form['rows'][$row_lm]['id_lotmag'] = key($_POST['new_lotmag'][$row_lm]);
		$getlot = $lm->getLot($form['rows'][$row_lm]['id_lotmag']);
		$form['rows'][$row_lm]['identifier'] = $getlot['identifier'];
    }		
		
	$countric=array();
	foreach ($form['rows'] as $i => $v) { // raggruppo e conteggio q.tà richieste per i lotti
		if ($v['lot_or_serial'] > 0 && $v['id_lotmag'] > 0 AND $form['tipdoc']<>"FNC"){
								
			$key=$v['identifier']; // chiave per il conteggio dei totali raggruppati per lotto 
			if( !array_key_exists($key, $countric) ){ // se la chiave ancora non c'è nell'array
				// Aggiungo la chiave con il rispettivo valore iniziale
				$countric[$key] = $v['quanti'];
			} else {
				// Altrimenti, aggiorno il valore della chiave
				$countric[$key] += $v['quanti'];
			}
		}
	} 
	foreach ($form['rows'] as $i => $v) { // Antonio Germani - controllo delle giacenze per l'articolo con lotti e data di registrazione per SIAN
		if ($v['SIAN']>0){ 
			$uldtfile=getLastSianDay();
			if (strtotime($datemi) < strtotime($uldtfile)){
				$msg['war'][] = "siandate";
			}
		}
		if ($v['lot_or_serial'] > 0 && $v['id_lotmag'] > 0){
			$n=0;// controllo se un ID lotto è presente in più righi
			foreach ($form['rows'] as $ii => $vv){
				if ($v['id_lotmag']==$vv['id_lotmag']){
					$n++;
					if ($n>1){
						$msg['war'][] = "2";
					}
				}
			}
			$lm->getAvailableLots($v['codart']);					
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
			if ($countric[$v['identifier']] > $count[$v['identifier']] AND $form['tipdoc']<>"FNC"){ // confronto con la quantità richiesta
				$msgrigo = $i + 1;
				$msg['war'][] = "1";
			}
			$disp= $lm -> dispLotID ($v['codart'], $v['id_lotmag'], $idmag); // controllo disponibilità per ID lotto		
			if ($v['quanti']>$disp AND $form['tipdoc']<>"FNC"){
				$msg['war'][] = "lotinsuf";
			}
		}
	}
		

} elseif (((!isset($_POST['Update'])) and ( isset($_GET['Update']))) or ( isset($_GET['Duplicate']))) { //se e' il primo accesso per UPDATE
	$form['in_barcode']="";
	$form['ok_barcode']="";
    $form['id_tes'] = intval($_GET['id_tes']);
    $tesdoc = gaz_dbi_get_row($gTables['tesdoc'], "id_tes", $form['id_tes']);
    $anagrafica = new Anagrafica();
    $cliente = $anagrafica->getPartner($tesdoc['clfoco']);
    $id_des = $anagrafica->getPartner($tesdoc['id_des']);
    $rs_rig = gaz_dbi_dyn_query("*", $gTables['rigdoc'], "id_tes = " . $form['id_tes'], "id_rig asc");
    $form['hidden_req'] = '';
    $form['roundup_y'] = '';
    // inizio rigo di input
    $form['in_descri'] = "";
    $form['in_tiprig'] = 0;
    /*    $form['in_artsea'] = $admin_aziend['artsea']; */
    $form['in_codart'] = "";
    $form['in_pervat'] = 0;
    $form['in_tipiva'] = 0;
	if ($cliente['ritenuta'] > 0) {
		$form['in_ritenuta'] = $cliente['ritenuta'];
	} else {
		$form['in_ritenuta'] = 0;
	}
    $form['in_unimis'] = "";
    $form['in_prelis'] = 0;
    /** inizio modifica FP 09/10/2015
     * inizializzo il campo con '#' per indicare che voglio lo sconto standard dell'articolo
     */
//rimossa    $form['in_sconto'] = 0;
    $form['in_sconto'] = '#';
    $form['in_quanti'] = 0;
    $form['in_codvat'] = 0;
    $form['in_codric'] = $admin_aziend['impven'];
    $form['in_id_mag'] = 0;
    $form['in_annota'] = "";
    $form['in_scorta'] = 0;
    $form['in_quamag'] = 0;
    $form['in_pesosp'] = 0;
    $form['in_gooser'] = 0;
    $form['in_lot_or_serial'] = 0;
	$form['in_SIAN'] = 0;
    $form['in_id_lotmag'] = 0;
	$form['in_identifier'] = "";
	$form['in_cod_operazione'] = 11;
	$form['in_recip_stocc'] = "";
	$form['in_recip_stocc_destin'] = "";
    $form['in_status'] = "INSERT";
    // fine rigo input
    $form['rows'] = array();
    // ...e della testata
    $form['search']['clfoco'] = substr($cliente['ragso1'], 0, 10);
    $form['cosear'] = "";
    $form['seziva'] = $tesdoc['seziva'];
    $form['tipdoc'] = $tesdoc['tipdoc'];
    $form['id_doc_ritorno'] = $tesdoc['id_doc_ritorno'];
    if ($tesdoc['tipdoc'] == 'FAD') {
        // se non è attiva la possibilità di modifica della fattura differita visualizzo il messaggio
        if ( !$modifica_fatture_ddt ) {
            $msg['err'][] = "57";
        }
    }
    if ($tesdoc['id_con'] > 0) {
        $msg['err'][] = "58";
    }
    if ($form['tipdoc'] == 'FNC') { // nel caso che si tratti di nota di credito
        $form['in_codric'] = $admin_aziend['sales_return'];
    }
    $form['template'] = $tesdoc['template'];
    $form['datemi'] = gaz_format_date($tesdoc['datemi']);
    $form['initra'] = gaz_format_date($tesdoc['initra']);
    $form['oratra'] = substr($tesdoc['initra'], 11, 2);
    $form['mintra'] = substr($tesdoc['initra'], 14, 2);
    $form['protoc'] = $tesdoc['protoc'];
    $form['numdoc'] = $tesdoc['numdoc'];
    $form['numfat'] = $tesdoc['numfat'];
    $form['datfat'] = $tesdoc['datfat'];
    $form['clfoco'] = $tesdoc['clfoco'];
    $form['pagame'] = $tesdoc['pagame'];
    $form['change_pag'] = $tesdoc['pagame'];
    $form['speban'] = $tesdoc['speban'];
    $pagame = gaz_dbi_get_row($gTables['pagame'], "codice", $form['pagame']);
    if ($pagame['tippag'] == 'B' || $pagame['tippag'] == 'T' || $pagame['tippag'] == 'V') {
        $form['numrat'] = $pagame['numrat'];
    } else {
        $form['numrat'] = 1;
    }
    $form['banapp'] = $tesdoc['banapp'];
    $form['vettor'] = $tesdoc['vettor'];
    $form['id_agente'] = $tesdoc['id_agente'];
    $provvigione = new Agenti;
    $form['in_provvigione'] = $provvigione->getPercent($form['id_agente']);
    $form['net_weight'] = $tesdoc['net_weight'];
    $form['gross_weight'] = $tesdoc['gross_weight'];
    $form['units'] = $tesdoc['units'];
    $form['volume'] = $tesdoc['volume'];
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
    $form['expense_vat'] = $tesdoc['expense_vat'];
	$form['split_payment'] = gaz_dbi_get_row($gTables['aliiva'], "codice", $form['expense_vat'])['tipiva'];
    $form['virtual_taxstamp'] = $tesdoc['virtual_taxstamp'];
    $form['taxstamp'] = $tesdoc['taxstamp'];
    $form['stamp'] = $tesdoc['stamp'];
    $form['round_stamp'] = $tesdoc['round_stamp'];
    $form['cauven'] = $tesdoc['cauven'];
    $form['caucon'] = $tesdoc['caucon'];
    $form['caumag'] = $tesdoc['caumag'];
    $form['caucon'] = $tesdoc['caucon'];
    $form['sconto'] = $tesdoc['sconto'];
    /** inizio modifica FP 15/10/2015
     * aggiunto ragbol
     */
    $form['ragbol'] = $tesdoc['ragbol'];
    $form['data_ordine'] = $tesdoc['data_ordine'];
    $form['gioord'] = substr($tesdoc['data_ordine'], 8, 2);
    $form['mesord'] = substr($tesdoc['data_ordine'], 5, 2);
    $form['annord'] = substr($tesdoc['data_ordine'], 0, 4);
    /** fine modifica FP */
    $next_row = 0;
	$form['RiferimentoNumeroLinea'] = array();
    while ($rigo = gaz_dbi_fetch_array($rs_rig)) {
		if ($rigo['tiprig']<=1 || $rigo['tiprig']==90){
			$form['RiferimentoNumeroLinea'][$next_row+1] = substr($rigo['descri'],0,20);
		}
        $articolo = gaz_dbi_get_row($gTables['artico'], "codice", $rigo['codart']);
        if ($rigo['id_body_text'] > 0) { //se ho un rigo testo
            $text = gaz_dbi_get_row($gTables['body_text'], "id_body", $rigo['id_body_text']);
            $form["row_$next_row"] = $text['body_text'];
        }
        $form['rows'][$next_row]['descri'] = $rigo['descri'];
        $form['rows'][$next_row]['tiprig'] = $rigo['tiprig'];
        $form['rows'][$next_row]['codart'] = $rigo['codart'];
        $form['rows'][$next_row]['pervat'] = $rigo['pervat'];
        $iva_row = gaz_dbi_get_row($gTables['aliiva'], 'codice', $rigo['codvat']);
        $form['rows'][$next_row]['tipiva'] = $iva_row['tipiva'];
        $form['rows'][$next_row]['ritenuta'] = $rigo['ritenuta'];
        $form['rows'][$next_row]['unimis'] = $rigo['unimis'];
        $form['rows'][$next_row]['prelis'] = number_format($rigo['prelis'], $admin_aziend['decimal_price'], '.', '');
        $form['rows'][$next_row]['sconto'] = $rigo['sconto'];
        $form['rows'][$next_row]['quanti'] = gaz_format_quantity($rigo['quanti'], 0, $admin_aziend['decimal_quantity']);
        $form['rows'][$next_row]['codvat'] = $rigo['codvat'];
        $form['rows'][$next_row]['codric'] = $rigo['codric'];
        $form['rows'][$next_row]['provvigione'] = $rigo['provvigione'];// in caso tiprig=4 questo campo è utilizzato per indicare l'aliquota della cassa previdenziale
        $form['rows'][$next_row]['id_mag'] = (isset($_GET['Duplicate']) ? 0 : $rigo['id_mag']);
        $form['rows'][$next_row]['annota'] = $articolo['annota'];
        $mv = $upd_mm->getStockValue(false, $rigo['codart'], $datemi, $admin_aziend['stock_eval_method']);
        $magval = array_pop($mv);
        $magval=(is_numeric($magval))?['q_g'=>0,'v_g'=>0]:$magval;
        $form['rows'][$next_row]['scorta'] = $articolo['scorta'];
        $form['rows'][$next_row]['quamag'] = $magval['q_g'];
        $form['rows'][$next_row]['pesosp'] = $articolo['peso_specifico'];
        $form['rows'][$next_row]['gooser'] = $articolo['good_or_service'];
        $form['rows'][$next_row]['lot_or_serial'] = $articolo['lot_or_serial'];
		$form['rows'][$next_row]['SIAN'] = $articolo['SIAN'];
        $movmag = gaz_dbi_get_row($gTables['movmag'], "id_mov", $rigo['id_mag']);
        $form['rows'][$next_row]['id_lotmag'] = $movmag['id_lotmag'];
		$getlot = $lm->getLot($form['rows'][$next_row]['id_lotmag']);
		$form['rows'][$next_row]['identifier'] = $getlot['identifier'];
		$movsian = gaz_dbi_get_row($gTables['camp_mov_sian'], "id_movmag", $rigo['id_mag']);
		$form['rows'][$next_row]['cod_operazione'] = $movsian['cod_operazione'];
		$form['rows'][$next_row]['recip_stocc'] = $movsian['recip_stocc'];
		$form['rows'][$next_row]['recip_stocc_destin'] = $movsian['recip_stocc_destin'];
        $form['rows'][$next_row]['status'] = (isset($_GET['Duplicate']) ? "Insert" : "UPDATE");
        $next_row++;
    }
    if (isset($_GET['Duplicate'])) {  // duplicate: devo reinizializzare i campi come per la insert
        $form['id_doc_ritorno'] = 0;
        $form['id_tes'] = "";
        $form['datemi'] = date("d/m/Y");
        $form['initra'] = date("d/m/Y");
        $form['oratra'] = date("H");
        $form['mintra'] = date("i");
    }
} elseif (!isset($_POST['Insert'])) { //se e' il primo accesso per INSERT
	$form['in_barcode']="";
	$form['ok_barcode']="";
    $form['tipdoc'] = '';
    $form['id_doc_ritorno'] = 0;
    if (isset($_GET['tipdoc'])) {
        $form['tipdoc'] = preg_replace("/[^A-Z?! ]/","",$_GET['tipdoc']);
    }
    $form['id_tes'] = "";
    $form['datemi'] = date("d/m/Y");
    $form['initra'] = date("d/m/Y");
    $form['oratra'] = date("H");
    $form['mintra'] = date("i");
    $form['rows'] = array();
    $next_row = 0;
    $form['hidden_req'] = '';
    $form['roundup_y'] = '';
    // inizio rigo di input
    $form['in_descri'] = "";
    $form['in_tiprig'] = 0;
    /*    $form['in_artsea'] = $admin_aziend['artsea']; */
    $form['in_codart'] = "";
    $form['in_pervat'] = "";
    $form['in_tipiva'] = "";
    $form['in_ritenuta'] = 0;
    $form['in_unimis'] = "";
    $form['in_prelis'] = 0;
    /** inizio modifica FP 09/10/2015
     * inizializzo il campo con '#' per indicare che voglio lo sconto standard dell'articolo
     */
//rimossa    $form['in_sconto'] = 0;
    $form['in_sconto'] = '#';
    $form['gioord'] = date("d");
    $form['mesord'] = date("m");
    $form['annord'] = date("Y");
    /* fine modifica FP */
    $form['in_quanti'] = 0;
    $form['in_codvat'] = 0;
    $form['in_codric'] = $admin_aziend['impven'];
    if ($form['tipdoc'] == 'FNC') { // nel caso che si tratti di nota di credito
        $form['in_codric'] = $admin_aziend['sales_return'];
        if ($form['in_codric'] < 300000000) {
            $form['in_codric'] = '4';
        }
    }
    $form['in_provvigione'] = 0;// in caso tiprig=4 questo campo è utilizzato per indicare l'aliquota della cassa previdenziale
    $form['in_id_mag'] = 0;
    $form['in_annota'] = "";
    $form['in_scorta'] = 0;
    $form['in_quamag'] = 0;
    $form['in_pesosp'] = 0;
    $form['in_gooser'] = 0;
    $form['in_lot_or_serial'] = 0;
	$form['in_SIAN'] = 0;
    $form['in_id_lotmag'] = 0;
	$form['in_identifier'] = "";
	$form['in_cod_operazione'] = 11;
	$form['in_recip_stocc'] = "";
	$form['in_recip_stocc_destin'] = "";
    $form['in_status'] = "INSERT";
    // fine rigo input
    $form['search']['clfoco'] = '';
    $form['cosear'] = "";
    $form['seziva'] = getFAIseziva($form['tipdoc']);
    //cerco l'ultimo template
    $rs_ultimo_template = gaz_dbi_dyn_query($gTables['tesdoc'] . ".template", $gTables['tesdoc'], "tipdoc = '" . $form['tipdoc'] . "' and ddt_type!='R' and seziva = " .$form['seziva'], 'datfat desc, protoc desc', 0, 1);
    $ultimo_template = gaz_dbi_fetch_array($rs_ultimo_template);
    if (isset($ultimo_template['template']) && $ultimo_template['template'] == 'FatturaImmediata') {
        $form['template'] = "FatturaImmediata";
    } elseif (!empty($ultimo_template['template'])) {
        $form['template'] = $ultimo_template['template'];
    } elseif ($form['tipdoc'] == 'FAA') {
        $form['template'] = "FatturaSemplice";
    } elseif ($form['tipdoc'] == 'FAF') {
        $form['template'] = "FatturaSemplice";
    } elseif ($form['tipdoc'] == 'FAP' || $form['tipdoc'] == 'FAQ') {  //se e' una parcella
        $form['template'] = 'Parcella';
    } elseif ($form['tipdoc'] == 'VRI') {  //se e' una ricevuta
        $form['template'] = 'Received';
    } else {
        $form['template'] = "FatturaSemplice";
    }
    $form['protoc'] = "";
    $form['numdoc'] = "";
    $form['numfat'] = "";
    $form['datfat'] = "";
    $form['clfoco'] = 0;
    $form['pagame'] = "";
    $form['change_pag'] = "";
    $form['banapp'] = "";
    $form['vettor'] = "";
    $form['id_agente'] = 0;
    $form['net_weight'] = 0;
    $form['gross_weight'] = 0;
    $form['units'] = 0;
    $form['volume'] = 0;
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
    $form['stamp'] = 0;
    $form['expense_vat'] = $admin_aziend['preeminent_vat'];
    $form['split_payment'] = '';
    $form['virtual_taxstamp'] = $admin_aziend['virtual_taxstamp'];
    $form['taxstamp'] = 0;
    $form['round_stamp'] = $admin_aziend['round_bol'];
    $form['cauven'] = 0;
    $form['caucon'] = '';
    $form['caumag'] = 0;
    /** inizio modifica FP 15/10/2015
     * aggiunti campi
     */
    $form['ragbol'] = 0;
    $form['data_ordine'] = "";
    /** fine modifica FP */
    $form['sconto'] = 0;
    $cliente['indspe'] = "";
    $cliente['fe_cod_univoco'] = "";
    $cliente['codfis'] = "";
    $cliente['pariva'] = "";
}

require("../../library/include/header.php");
$script_transl = HeadMain(0, array(/* 'tiny_mce/tiny_mce', */
    /* 'boxover/boxover', */
    'calendarpopup/CalendarPopup',
    'custom/autocomplete',
    'custom/miojs',
        ));
?>
<script>
    $(function () {
        $("#initra").datepicker({showButtonPanel: true, showOtherMonths: true, selectOtherMonths: true});
        $("#datemi").datepicker({showButtonPanel: true, showOtherMonths: true, selectOtherMonths: true});
<?php
if ( count($msg['err'])<=0 && count($msg['war'])<=0 && $form['clfoco']>=100000000 ) { // scrollo solo e se ho selezionato il cliente e non ci sono errori
    ?>
            $("html, body").delay(100).animate({scrollTop: $('#search_cosear').offset().top-100}, 1000);
    <?php
}
?>
    });
    function pulldown_menu(selectName, destField)
    {
        // Create a variable url to contain the value of the
        // selected option from the the form named docven and variable selectName
        var url = document.docven[selectName].options[document.docven[selectName].selectedIndex].value;
        document.docven[destField].value = url;
    }

</script>
<script type="text/javascript" language="JavaScript" ID="datapopup">
    var cal = new CalendarPopup();
    cal.setReturnFunction("setMultipleValues");
    function setMultipleValues(y, m, d) {
        document.docven.anntra.value = y;
        document.docven.mestra.value = LZ(m);
        document.docven.giotra.value = LZ(d);
    }
</script>
<?php
$gForm = new venditForm();
if (count($msg['err']) > 0) { // ho un errore
    echo '<div class="text-center"><div><b>';
    $gForm->gazHeadMessage($msg['err'], $script_transl['err'], 'err');
    echo "</b></div></div>\n";
}
if (count($msg['war']) > 0) { // ho un alert-danger
	echo '<div class="text-center"><div><b>';
    $gForm->gazHeadMessage($msg['war'], $script_transl['war'], 'war');
	echo "</b></div></div>\n";
}
echo '<form method="POST" name="docven">';
echo '	<input type="hidden" value="" name="' . ucfirst($toDo) . '" />
	<input type="hidden" value="' . $form['id_tes'] . '" name="id_tes" />
	<input type="hidden" value="' . $form['seziva'] . '" name="seziva" />
	<input type="hidden" value="' . $form['ritorno'] . '" name="ritorno" />
	<input type="hidden" value="' . $form['roundup_y'] . '" name="roundup_y">
	<input type="hidden" value="' . $form['change_pag'] . '" name="change_pag" />
	<input type="hidden" value="' . $form['protoc'] . '" name="protoc" />
	<input type="hidden" value="' . $form['numdoc'] . '" name="numdoc" />
	<input type="hidden" value="' . $form['numfat'] . '" name="numfat" />
	<input type="hidden" value="' . $form['datfat'] . '" name="datfat" />
	<input type="hidden" value="' . (isset($_POST['last_focus']) ? $_POST['last_focus'] : "") . '" name="last_focus" />
	<input type="hidden" value="' . $form['split_payment'] . '" name="split_payment" />
	<input type="hidden" value="' . $form['data_ordine'] . '" name="data_ordine" />';

if ($form['id_tes'] > 0) { // è una modifica
    $title = ucfirst($script_transl[$toDo] . $script_transl['doc_name'][$form['tipdoc']]) . " n." . $form['numdoc'];
    echo "<input type=\"hidden\" value=\"" . $form['tipdoc'] . "\" name=\"tipdoc\">\n";
    echo "<div align=\"center\" class=\"FacetFormHeaderFont\">$title ";
} else { // è un inserimento
    $tidoc_selectable = array_intersect_key($script_transl['doc_name'], array('DDT'=>'','FAI'=>'','FAP'=>'','FAQ'=>'','FAA'=>'','FAF'=>'','FNC'=>'','FND'=>'','DDV'=>'','RDV'=>'','DDY'=>'','DDS'=>'','VRI'=>'','CMR'=>''));
    echo "<div align=\"center\" class=\"FacetFormHeaderFont\">" . ucfirst($script_transl[$toDo]) . $script_transl['tipdoc'];
    $gForm->variousSelect('tipdoc', $tidoc_selectable, $form['tipdoc'], 'FacetFormHeaderFont', true, 'tipdoc');
}

if ($form['tipdoc'] == 'FAI') {
    echo "<select name=\"template\" class=\"FacetSelect\">\n";
    echo "<option value=\"FatturaImmediata\" ";
    if ($form['template'] == "FatturaImmediata") {
        echo " selected";
    }
    echo ">Accompagn.</option>\n";
    echo "<option value=\"FatturaSemplice\" ";
    if ($form['template'] == "FatturaSemplice") {
        echo " selected";
    }
    echo ">Normale</option></select>\n";
} else {
    echo "<input type=\"hidden\" value=\"" . $form['template'] . "\" name=\"template\">\n";
}
echo " :";

$select_cliente = new selectPartner('clfoco');
$select_cliente->selectDocPartner('clfoco', $form['clfoco'], $form['search']['clfoco'], 'clfoco', $script_transl['mesg'], $admin_aziend['mascli']);
//tabula solo se non e' stato settato il cliente
$tabula = " tabindex=\"3\" ";
if ($form['clfoco'] > 0)
    $tabula = "";
echo "</div>\n";
echo "<div class=\"box-primary table-responsive\">";
echo "<table class=\"Tlarge table table-bordered table-condensed\">\n";
echo "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[4]</td><td class=\"FacetDataTD\">\n";
echo "<select name=\"seziva\" class=\"FacetSelect\">\n";
for ($counter = 1; $counter <= 5; $counter++) {
    $selected = "";
    if ($form["seziva"] == $counter) {
        $selected = " selected ";
    }
    echo "<option value=\"" . $counter . "\"" . $selected . ">" . $counter . "</option>\n";
}
echo "</select></td>\n";
    echo "<td class=\"FacetFieldCaptionTD\">$script_transl[5]</td><td class=\"FacetDataTD\" colspan=\"1\">" . @$cliente['indspe'] . "<br />";
    echo "</td>\n";

    if (@$cliente['pariva'] > 0) {
        echo "<td class=\"FacetFieldCaptionTD\">P.IVA</td><td class=\"FacetDataTD\" colspan=\"1\">" . @$cliente['pariva'] . "<br />";
        echo "</td>\n";
    } else {
        echo "<td class=\"FacetFieldCaptionTD\">C.F.</td><td class=\"FacetDataTD\" colspan=\"1\">" . @$cliente['codfis'] . "<br />";
        echo "</td>\n";
    }
?>
<td class="FacetFieldCaptionTD"><?php echo $script_transl[6];?></td>
<td class="FacetDataTD"><input type="text" value="<?php echo $form['datemi']; ?>" id="datemi" name="datemi" /></td>
<?php
echo "</tr>\n";
echo "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[7]</td><td class=\"FacetDataTD\">\n";
echo "<select name=\"listin\" class=\"FacetSelect\">\n";
for ($lis = 1; $lis <= 4; $lis++) {
    $selected = "";
    if ($form["listin"] == $lis) {
        $selected = " selected ";
    }
    echo "<option value=\"" . $lis . "\"" . $selected . ">" . $lis . "</option>\n";
}
echo "</select></td>\n";
echo "<td class=\"FacetFieldCaptionTD\">$script_transl[8]</td><td colspan=\"1\" class=\"FacetDataTD\">\n";
$select_pagame = new selectpagame("pagame");
$select_pagame->addSelected($form["pagame"]);
$select_pagame->output();
echo "</td>";

echo "<td class=\"FacetFieldCaptionTD\">Cod.Univoco</td><td class=\"FacetDataTD\" colspan=\"1\">" . @$cliente['fe_cod_univoco'] . "<br />";
echo "</td>\n";
echo "<td class=\"FacetFieldCaptionTD\">$script_transl[9]</td><td  class=\"FacetDataTD\">\n";
$select_banapp = new selectbanapp("banapp");
$select_banapp->addSelected($form["banapp"]);
$select_banapp->output();
echo "</td></tr>\n";
echo "<tr>\n";
echo "<td align=\"left\" class=\"FacetFieldCaptionTD\" title=\"" . $script_transl['traspo_title'] . "\">$script_transl[28]" . ' ' . $admin_aziend['html_symbol'] . "</td>\n";
echo "<td class=\"FacetDataTD\" title=\"" . $script_transl['traspo_title'] . "\"><input type=\"text\" value=\"" . $form['traspo'] . "\" name=\"traspo\" maxlength=\"6\" onchange=\"this.form.submit()\" /></td>\n";
echo "<td class=\"FacetFieldCaptionTD\" title=\"" . $script_transl['speban_title'] . "\">" . $script_transl['speban'] . "</td>
      <td class=\"FacetDataTD\" title=\"" . $script_transl['speban_title'] . "\"><input type=\"text\" value=\"" . $form['speban'] . "\" name=\"speban\" maxlength=\"6\"  onchange=\"this.form.submit()\" /> x " . $form['numrat'] . " ";
$sel_expensevat = new selectaliiva("expense_vat");
$sel_expensevat->addSelected($form["expense_vat"]);
$sel_expensevat->output();
echo "</td>\n";
echo "<td align=\"left\" class=\"FacetFieldCaptionTD\">" . $script_transl[51] . "</td><td class=\"FacetDataTD\">\n";
echo "<select name=\"caumag\" class=\"FacetSelect\" width=\"20\">\n";
$result = gaz_dbi_dyn_query("*", $gTables['caumag'], " clifor = -1 AND operat = " . $docOperat[$form['tipdoc']], "codice asc, descri asc");
while ($row = gaz_dbi_fetch_array($result)) {
    $selected = "";
    if ($form["caumag"] == $row['codice']) {
        $selected = " selected ";
    }
    echo "<option value=\"" . $row['codice'] . "\"" . $selected . ">" . $row['codice'] . "-" . substr($row['descri'], 0, 20) . "</option>\n";
}
echo "</select></td>\n";
echo "<td class=\"FacetFieldCaptionTD\">" . $script_transl['id_agente'] . "</td>";
echo "<td class=\"FacetDataTD\">\n";
$select_agente = new selectAgente("id_agente");
$select_agente->addSelected($form["id_agente"]);
$select_agente->output();
/** inizio modifica FP 15/10/2015
 * aggiungo i campi ragbol e data ordine
 */
if ($form['tipdoc'] == "DDT") {
    echo "</td></tr>";
    echo "<tr>\n";
// raggruppamento bolle
    echo "<td align=\"left\" class=\"FacetFieldCaptionTD\" title=\"" . $script_transl['ragbol_title'] . "\">" . $script_transl['ragbol'] . "</td>\n";
    echo "<td class=\"FacetDataTD\">";
    $gForm->variousSelect('ragbol', $script_transl['ragbol_value'], $form['ragbol']);
    echo "</td>";
// data ordine
    echo "<td class=\"FacetFieldCaptionTD\">" . $script_transl['order_date'] . "</td><td class=\"FacetDataTD\">\n";
// select del giorno
    echo "\t <select name=\"gioord\" class=\"FacetSelect\" >\n";
    for ($counter = 1; $counter <= 31; $counter++) {
        $selected = "";
        if ($counter == $form['gioord'])
            $selected = "selected";
        echo "\t\t <option value=\"$counter\" $selected >$counter</option>\n";
    }
    echo "\t </select>\n";
// select del mese
    echo "\t <select name=\"mesord\" class=\"FacetSelect\" >\n";
    for ($counter = 1; $counter <= 12; $counter++) {
        $selected = "";
        if ($counter == $form['mesord'])
            $selected = "selected";
        $nome_mese = ucwords(strftime("%B", mktime(0, 0, 0, $counter, 1, 0)));
        echo "\t\t <option value=\"$counter\"  $selected >$nome_mese</option>\n";
    }
    echo "\t </select>\n";
// select del anno
    echo "\t <select name=\"annord\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
    for ($counter = $form['annord'] - 10; $counter <= $form['annord'] + 10; $counter++) {
        $selected = "";
        if ($counter == $form['annord'])
            $selected = "selected";
        echo "\t\t <option value=\"$counter\"  $selected >$counter</option>\n";
    }
    echo "\t </select></td>";
    echo "</tr></table></div>\n";
    echo '<input type="hidden" value="' . $form['id_doc_ritorno'] . '" name="id_doc_ritorno" />';
} elseif ($form['tipdoc'] == "RDV") {
    echo "</td></tr>";
    echo "<tr><td align=\"left\"  colspan=\"4\" class=\"FacetFieldCaptionTD\" >" . $script_transl['id_doc_ritorno_title'] . "</td>\n";
    echo "<td class=\"FacetDataTD\" colspan=\"4\">\n";
    echo "<select name=\"id_doc_ritorno\" class=\"FacetSelect\" width=\"20\">\n";
    $result = gaz_dbi_dyn_query("*", $gTables['tesdoc'], " clfoco = " . $form['clfoco'] . " AND tipdoc = 'DDV' AND id_doc_ritorno <= 0 ", " datemi desc");
    echo "\t\t <option value=\"\"></option>\n";
    while ($row = gaz_dbi_fetch_array($result)) {
        $selected = "";
        if ($form["id_doc_ritorno"] == $row['id_tes']) {
            $selected = " selected ";
        }
        echo "<option value=\"" . $row['id_tes'] . "\"" . $selected . ">" . $script_transl['doc_name'][$row['tipdoc']] . " n." . $row['numdoc'] . " del " . gaz_format_date($row['datemi']) . "</option>\n";
    }
    echo "</select></td></tr></table></div>\n";
    echo "<input type=\"hidden\" value=\"" . $form['gioord'] . "\" name=\"gioord\">\n";
    echo "<input type=\"hidden\" value=\"" . $form['mesord'] . "\" name=\"mesord\">\n";
    echo "<input type=\"hidden\" value=\"" . $form['annord'] . "\" name=\"annord\">\n";
    echo "<input type=\"hidden\" value=\"" . $form['ragbol'] . "\" name=\"ragbol\">\n";
} else {
    echo "</td></tr></table></div>\n";
    echo "<input type=\"hidden\" value=\"" . $form['gioord'] . "\" name=\"gioord\">\n";
    echo "<input type=\"hidden\" value=\"" . $form['mesord'] . "\" name=\"mesord\">\n";
    echo "<input type=\"hidden\" value=\"" . $form['annord'] . "\" name=\"annord\">\n";
    echo "<input type=\"hidden\" value=\"" . $form['ragbol'] . "\" name=\"ragbol\">\n";
    echo '<input type="hidden" value="' . $form['id_doc_ritorno'] . '" name="id_doc_ritorno" />';
}
// fine modifica FP 

echo '<div class="box-primary table-responsive"><table id="products-list" class="Tlarge table table-bordered table-condensed">
		  <thead>
			<tr>
				<th></th>
				<th>' . $script_transl[20] . '</th>
				<th>' . $script_transl[21] . '</th>
				<th>' . $script_transl[22] . '</th>
                <th>' . $script_transl[16] . '</th>
                <th>' . $script_transl[23] . '</th>            
				<th>%' . substr($script_transl[24], 0, 2) . '.</th>
				<th>%' . substr($script_transl[56], 0, 5) . '.</th>
				<th align="right">' . $script_transl[25] . '</th>
				<th>' . $script_transl[19] . '</th>
				<th>' . $script_transl[18] . '</th>
				<th></th>
			</tr>
		  </thead>
		  <tbody>';
$totimp_body = 0.00;
$totivafat = 0.00;
$totimpfat = 0.00;
$castle = array();
$rit = 0;
$carry = 0;
$last_row = array();
$vp = gaz_dbi_get_row($gTables['company_config'], 'var', 'vat_price')['val'];
foreach ($form['rows'] as $k => $v) {
    //creo il castelletto IVA
    $imprig = 0;
    if ($v['tiprig'] <= 1 || $v['tiprig'] == 4) { // calcolo per tipi righi normale, forfait e cassa previdenziale
        $imprig = CalcolaImportoRigo($v['quanti'], $v['prelis'], $v['sconto']);
        $v_for_castle = CalcolaImportoRigo($v['quanti'], $v['prelis'], array($v['sconto'], $form['sconto']));      
        if ($v['tiprig'] == 1) {// se del tipo forfait 
            $imprig = CalcolaImportoRigo(1, $v['prelis'], 0);
            $v_for_castle = CalcolaImportoRigo(1, $v['prelis'], $form['sconto']);
        }
        if ($v['tiprig'] == 4) {// e se del tipo cassa previdenziale
            $imprig = round($v['provvigione']* $v['prelis']/100,2);
            $v_for_castle =  $imprig;
        }
        if (!isset($castle[$v['codvat']])) {
            $castle[$v['codvat']]['impcast'] = 0.00;
        }
        $totimp_body += $imprig;
        $castle[$v['codvat']]['impcast'] += $v_for_castle;
        $rit += round($imprig * floatval($v['ritenuta']) / 100, 2);
    } elseif ($v['tiprig'] == 3) {
        $carry += $v['prelis'];
    } elseif ($v['tiprig'] == 90) { // rigo vendita cespite ammortizzabile
        $imprig = CalcolaImportoRigo(1, $v['prelis'], 0);
        $v_for_castle = CalcolaImportoRigo(1, $v['prelis'], $form['sconto']);
        if (!isset($castle[$v['codvat']])) {
            $castle[$v['codvat']]['impcast'] = 0.00;
        }
        $totimp_body += $imprig;
        $castle[$v['codvat']]['impcast'] += $v_for_castle;
    }
    $descrizione = htmlentities($v['descri'], ENT_QUOTES);
    echo "<input type=\"hidden\" value=\"" . $v['codart'] . "\" name=\"rows[$k][codart]\">\n";
    echo "<input type=\"hidden\" value=\"" . $v['status'] . "\" name=\"rows[$k][status]\">\n";
    echo "<input type=\"hidden\" value=\"" . $v['tiprig'] . "\" name=\"rows[$k][tiprig]\">\n";
    echo "<input type=\"hidden\" value=\"" . $v['codvat'] . "\" name=\"rows[$k][codvat]\">\n";
    echo "<input type=\"hidden\" value=\"" . $v['pervat'] . "\" name=\"rows[$k][pervat]\">\n";
    echo "<input type=\"hidden\" value=\"" . $v['tipiva'] . "\" name=\"rows[$k][tipiva]\">\n";
    echo "<input type=\"hidden\" value=\"" . $v['ritenuta'] . "\" name=\"rows[$k][ritenuta]\">\n";
    echo "<input type=\"hidden\" value=\"" . $v['codric'] . "\" name=\"rows[$k][codric]\">\n";
    echo "<input type=\"hidden\" value=\"" . $v['id_mag'] . "\" name=\"rows[$k][id_mag]\">\n";
    echo "<input type=\"hidden\" value=\"" . $v['annota'] . "\" name=\"rows[$k][annota]\">\n";
    echo "<input type=\"hidden\" value=\"" . $v['scorta'] . "\" name=\"rows[$k][scorta]\">\n";
    echo "<input type=\"hidden\" value=\"" . $v['quamag'] . "\" name=\"rows[$k][quamag]\">\n";
    echo "<input type=\"hidden\" value=\"" . $v['pesosp'] . "\" name=\"rows[$k][pesosp]\">\n";
    echo "<input type=\"hidden\" value=\"" . $v['gooser'] . "\" name=\"rows[$k][gooser]\">" .
    '<input type="hidden" value="' . $v['lot_or_serial'] . '" name="rows[' . $k . '][lot_or_serial]" />' .
	'<input type="hidden" value="' . $v['SIAN'] . '" name="rows[' . $k . '][SIAN]" />' .
    '<input type="hidden" value="' . $v['id_lotmag'] . '" name="rows[' . $k . '][id_lotmag]" />'.
	'<input type="hidden" value="' . $v['identifier'] . '" name="rows[' . $k . '][identifier]" />';
	'<input type="hidden" value="' . $v['cod_operazione'] . '" name="rows[' . $k . '][cod_operazione]" />';
	'<input type="hidden" value="' . $v['recip_stocc'] . '" name="rows[' . $k . '][recip_stocc]" />';
	'<input type="hidden" value="' . $v['recip_stocc_destin'] . '" name="rows[' . $k . '][recip_stocc_destin]" />';
    echo '<tr>';
    switch ($v['tiprig']) {
        case "0":
            if ($v['gooser']==1){ 
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
            if ($imprig < 0.00001) {
                $imprig_class = 'danger';
            } else {
                $imprig_class = 'default';
            }
            /* Peso */
            $peso = 0;
            if ($v['pesosp'] <> 0) {
                $peso = gaz_format_number($v['quanti'] / $v['pesosp']);
                $peso2 = gaz_format_number($v['pesosp']);
            } else {
                $peso2 = 0;
            }

            echo '	<td>
						<button type="image" name="upper_row[' . $k . ']" class="btn btn-default btn-xs" title="' . $script_transl['3'] . '!">
							<i class="glyphicon glyphicon-arrow-up">' . ($k+1) . '</i>
						</button>
					</td>
					<td title="' . $script_transl['update'] . $script_transl['thisrow'] . '! ' . $btn_title . '">
						<button name="upd_row[' . $k . ']" class="btn btn-xs ' . $btn_class . ' btn-block" type="submit">
							<i class="glyphicon glyphicon-refresh"></i>&nbsp;' . $v['codart'] . '
						</button>
			 		</td>
					<td>
						<input class="gazie-tooltip" data-type="product-thumb" data-id="' . $v["codart"] . '" data-title="' . $v['annota'] . '" type="text" name="rows[' . $k . '][descri]" value="' . $descrizione . '" maxlength="100" />
					';
            if ($v['lot_or_serial'] > 0 && $v['id_lotmag'] > 0) { // se l'articolo prevede lotti
				
                $lm->getAvailableLots($v['codart'], $v['id_mag']);
				// Antonio Germani - calcolo delle giacenze per ogni singolo lotto
				$count=array();
				foreach ($lm->available as $v_lm) { // calcolo la disponbilità per ogni lotto raggruppato
					$key=$v_lm['identifier']; // chiave per il conteggio dei totali raggruppati per lotto 
					if( !array_key_exists($key, $count) ){ // se la chiave ancora non c'è nell'array
						// Aggiungo la chiave con il rispettivo valore iniziale
						$count[$key] = $v_lm['rest'];
					} else {
						// Altrimenti, aggiorno il valore della chiave
						$count[$key] += $v_lm['rest'];
					}
				}
                $selected_lot = $lm->getLot($v['id_lotmag']);
				$disp= $lm -> dispLotID ($v['codart'], $v['id_lotmag'], $v['id_mag']);
				if (!isset($count[$selected_lot['identifier']])){
					$count[$selected_lot['identifier']]="";
				}
				if ($count[$selected_lot['identifier']]>=$v['quanti']){
					echo '<div><button class="btn btn-xs btn-success" title="Clicca per cambiare lotto" ';
				} else {
					echo '<div><button class="btn btn-xs btn-danger" title="Disponibilità non sufficiente" ';
				}
				echo 'type="image" data-toggle="collapse" href="#lm_dialog' . $k . '">'. 'ID:'.$selected_lot['id']
                . '- lotto: ' . $selected_lot['identifier'];
				if (intval ($selected_lot['expiry'])>0) {
					echo ' scad:' . gaz_format_date($selected_lot['expiry']);
				}
				echo ' - disp.ID: '. gaz_format_quantity($disp)
				. ' <i class="glyphicon glyphicon-tag"></i>'
				. ' rif:' . $selected_lot['desdoc']
                . ' - ' . gaz_format_date($selected_lot['datdoc']) .
				'</button>';
                if ($v['id_mag'] > 0) {
                    echo ' <a class="btn btn-xs btn-default" href="lotmag_print_cert.php?id_movmag=' . $v['id_mag'] . '" target="_blank"><i class="glyphicon glyphicon-print"></i></a>';
                }
                echo "</div>\n";
                echo '<div id="lm_dialog' . $k . '" class="collapse" >
                        <div class="form-group">';
                if (count($lm->available) > 1) {
                    foreach ($lm->available as $v_lm) {
                        if ($v_lm['id'] <> $v['id_lotmag']) {
						if ($count[$v_lm['identifier']]>=$v['quanti']){
								echo '<div>change to:<button class="btn btn-xs btn-warning" type="image" ';
							} else {
								echo '<div>change to:<button class="btn btn-xs btn-danger" title="Q.tà non sufficiente" type="image" ';
							}
							echo 'onclick="this.form.submit();" name="new_lotmag[' . $k . '][' . $v_lm['id_lotmag'] . ']">'
                            . $v_lm['id']
                            . '- lotto: ' . $v_lm['identifier'];
							if (intval ($v_lm['expiry'])>0) {
								echo ' scad:' . gaz_format_date($v_lm['expiry']);
							}
                            echo ' disponibili:' . gaz_format_quantity($v_lm['rest']).'<i class="glyphicon glyphicon-tag"></i> rif:' . $v_lm['desdoc']
                            . ' - ' . gaz_format_date($v_lm['datdoc'])
							. '</button></div>';
                        }
                    }
                } else {
                    echo '<div><button class="btn btn-xs btn-danger" type="image" >Non sono disponibili altri lotti</button></div>';
                }
                echo '</div>'
                . "</div>\n";
            }

			// Antonio Germani - Se l'articolo movimenta il SIAN apro il div SIAN
			if ($form['rows'][$k]['SIAN']>0) {
				$art = gaz_dbi_get_row($gTables['camp_artico'], "codice", $v['codart']);
				?>	
				<div class="container-fluid">					
					<div class="row">
						<label for="cod_operazione" class="col-sm-6 control-label"><?php echo "Tipo operazione SIAN"; ?></label>
						<?php
						$gForm->variousSelect('rows[' . $k . '][cod_operazione]', $script_transl['cod_operaz_value'], $form['rows'][$k]['cod_operazione'], "col-sm-6", false, '', false)
						?>
					</div>
					<?php if ($art['confezione']==0){ ?>
					<div class="row">
						<label for="recip_stocc" class="col-sm-6"><?php echo "Recipiente stoccaggio"; ?></label>
						<?php
						$gForm->selectFromDB('camp_recip_stocc', 'rows[' . $k . '][recip_stocc]' ,'cod_silos', $form['rows'][$k]['recip_stocc'], 'cod_silos', 1, ' - kg ','cod_silos','TRUE','col-sm-6' , null, '');
						?>
					</div>
					<?php
					} else {
						echo '<input type="hidden" value="" name="rows[' . $k . '][recip_stocc]" />';
					}
					
					echo '<input type="hidden" value="" name="rows[' . $k . '][recip_stocc_destin]" />';
					
				echo '</div>';
			} else {
				echo '<input type="hidden" value="" name="rows[' . $k . '][cod_operazione]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc_destin]" />
					';
			}
			// fine apro SIAN

            echo '</td>
					<td>
						<input class="gazie-tooltip" data-type="weight" data-id="' . $peso . '" data-title="' . $script_transl['weight'] . '" type="text" name="rows[' . $k . '][unimis]" value="' . $v['unimis'] . '" maxlength="3" />
					</td>
					<td>
						<input class="gazie-tooltip" data-type="weight" data-id="' . $peso . '" data-title="' . $script_transl['weight'] . '" type="text" name="rows[' . $k . '][quanti]" value="' . $v['quanti'] . '" align="right" maxlength="11" id="righi_' . $k . '_quanti" onchange="document.docven.last_focus.value=\'righi_' . $k . '_prelis\'; this.form.hidden_req.value=\'ROW\'; this.form.submit();" />
                    </td>
                    <td>
						<input type="text" name="rows[' . $k . '][prelis]" value="' . $v['prelis'] . '" align="right" maxlength="11" ';
						if ($vp>0) { // solo se scelto in configurazione avanzata azienda si vedrà il dialog per mettere il prezzo iva compresa
							echo 'onclick="vatPrice(\''.$k.'\',\''.$v['pervat'].'\');" ';
						}
						echo ' id="righi_' . $k . '_prelis" onchange="document.docven.last_focus.value=\'righi_' . $k . '_sconto\'; this.form.submit()" />
					</td>
					<td><input type="text" name="rows[' . $k . '][sconto]" value="' . $v['sconto'] . '" maxlength="6" id="righi_' . $k . '_sconto" onchange="document.docven.last_focus.value=this.id; this.form.submit();" /></td>
					<td><input type="text" name="rows[' . $k . '][provvigione]" value="' . $v['provvigione'] . '" maxlength="6" /></td>
					<td class="text-right '.$imprig_class.'">
						<span class="gazie-tooltip text-right text-'.$imprig_class.'" data-type="ritenuta" data-id="' . $v['ritenuta'] . '% = ' . gaz_format_number(round($imprig * floatval($v['ritenuta']) / 100, 2)) . '" data-title="' . $script_transl['ritenuta'] . '">
							' . gaz_format_number($imprig) . '
						</span>
					</td>
					<td class="text-right">
						<span class="gazie-tooltip text-right" data-type="ritenuta" data-id="' . $v['ritenuta'] . '% = ' . gaz_format_number(round($imprig * floatval($v['ritenuta']) / 100, 2)) . '" data-title="' . $script_transl['ritenuta'] . '">
							' . $v['pervat'] . '%
						</span>
					</td>
					<td class="text-right codricTooltip" title="Contropartita">
						' . $v['codric'] . '
					</td>';
            
            $last_row[] = array_unshift($last_row, '' . $v['codart'] . ', ' . $v['descri'] . ', ' . $v['quanti'] . $v['unimis'] . ', <strong>' . $script_transl[23] . '</strong>: ' . gaz_format_number($v['prelis']) . ', %<strong>' . substr($script_transl[24], 0, 2) . '</strong>: ' . gaz_format_number($v['sconto']) . ', <strong>' . $script_transl[25] . '</strong>: ' . gaz_format_number($imprig) . ', <strong>' . $script_transl[19] . '</strong>: ' . $v['pervat'] . '%, <strong>' . $script_transl[18] . '</strong>: ' . $v['codric']);
            break;
        case "1": //forfait
            /*
             */
            echo '	<td>
						<button type="image" name="upper_row[' . $k . ']" class="btn btn-default btn-xs" title="' . $script_transl['3'] . '!">
							<i class="glyphicon glyphicon-arrow-up">' . ($k+1) . '</i>
						</button>
					</td>
					<td title="' . $script_transl['update'] . $script_transl['thisrow'] . '!\">
						<input class="btn btn-xs btn-success btn-block" type="submit" name="upd_row[' . $k . ']" value="' . $script_transl['typerow'][$v['tiprig']] . '" />
					</td>
					  <td>
						<input type="text"   name="rows[' . $k . '][descri]" value="' . $descrizione . '" maxlength="100" />
					</td>
                    <td><input type="hidden" name="rows[' . $k . '][unimis]" value="" /></td>
                    <td><input type="hidden" name="rows[' . $k . '][quanti]" value="" /></td>
					<td><input type="hidden" name="rows[' . $k . '][sconto]" value="" /></td>
					<td><input type="hidden" name="rows[' . $k . '][provvigione]" value="" /></td>
					<td></td>
					<td class="text-right">
						<input class="gazie-tooltip text-right" data-type="ritenuta" data-id="' . $v['ritenuta'] . '% = ' . gaz_format_number(round($imprig * (($v['ritenuta']) ?: 0) / 100, 2)) . '" data-title="' . $script_transl['ritenuta'] . '" type="text" name="rows[' . $k . '][prelis]" value="' . number_format($v['prelis'], 2, '.', '') . '" maxlength="11"';
						if ($vp>0) { // solo se scelto in configurazione avanzata azienda si vedrà il dialog per mettere il prezzo iva compresa
							echo 'onclick="vatPrice(\''.$k.'\',\''.$v['pervat'].'\');"';
						}
						echo ' id="righi_' . $k . '_prelis" onchange="document.docven.last_focus.value=this.id; this.form.submit()" />
					</td>
					<td class="text-right">
						<span class="gazie-tooltip text-right" data-type="ritenuta" data-id="' . $v['ritenuta'] . '% = ' . gaz_format_number(round($imprig * (($v['ritenuta']) ?: 0) / 100, 2)) . '" data-title="' . $script_transl['ritenuta'] . '">' . $v['pervat'] . '%
						</span>
					</td>
					<td class="text-right codricTooltip" title="Contropartita">
						' . $v['codric'] . '
					</td>';
					echo '<input type="hidden" value="" name="rows[' . $k . '][cod_operazione]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc_destin]" />
					';
            $last_row[] = array_unshift($last_row, $script_transl['typerow'][$v['tiprig']]);
            break;
        case "2": //descrittivo
            echo "	<td>
						<button type=\"image\" name=\"upper_row[" . $k . "]\" class=\"btn btn-default btn-xs\" title=\"" . $script_transl['3'] . "!\">
							<i class=\"glyphicon glyphicon-arrow-up\">" . ($k+1) . "</i>
						</button>
					</td>
					<td title=\"" . $script_transl['update'] . $script_transl['thisrow'] . "!\">
						<input class=\"btn btn-xs btn-success btn-block\" type=\"submit\" name=\"upd_row[$k]\" value=\"" . $script_transl['typerow'][$v['tiprig']] . "\" />
					</td>
					<td>
						<input type=\"text\"   name=\"rows[$k][descri]\" value=\"$descrizione\" maxlength=\"100\" />
					</td>
                    <td><input type=\"hidden\" name=\"rows[$k][unimis]\" value=\"\" /></td>
                    <td><input type=\"hidden\" name=\"rows[$k][quanti]\" value=\"\" /></td>
					<td><input type=\"hidden\" name=\"rows[$k][prelis]\" value=\"\" /></td>
					<td><input type=\"hidden\" name=\"rows[$k][sconto]\" value=\"\" /></td>
					<td><input type=\"hidden\" name=\"rows[$k][provvigione]\" value=\"\" /></td>
					<td></td>
					<td></td>
					<td></td>\n";
					echo '<input type="hidden" value="" name="rows[' . $k . '][cod_operazione]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc_destin]" />
					';
            $last_row[] = array_unshift($last_row, $script_transl['typerow'][$v['tiprig']]);
            break;
        case "3": // variazione totale fattura
            echo "	<td>
						<button type=\"image\" name=\"upper_row[" . $k . "]\" class=\"btn btn-default btn-xs\" title=\"" . $script_transl['3'] . "!\">
							<i class=\"glyphicon glyphicon-arrow-up\">" . ($k+1) . "</i>
						</button>
					</td>
					<td title=\"" . $script_transl['update'] . $script_transl['thisrow'] . "!\">
						<input class=\"btn btn-xs btn-success btn-block\" type=\"submit\" name=\"upd_row[$k]\" value=\"" . $script_transl['typerow'][$v['tiprig']] . "\" />
					</td>
					<td>
						<input type=\"text\" name=\"rows[$k][descri]\" value=\"$descrizione\" maxlength=\"100\" >
					</td>
					<td><input type=\"hidden\" name=\"rows[$k][unimis]\" value=\"\" /></td>
                    <td><input type=\"hidden\" name=\"rows[$k][quanti]\" value=\"\" /></td>
					<td><input type=\"hidden\" name=\"rows[$k][sconto]\" value=\"\" /></td>
					<td><input type=\"hidden\" name=\"rows[$k][provvigione]\" value=\"\" /></td>
					<td></td>
					<td align=\"right\">
						<input style=\"text-align:right\" type=\"text\" name=\"rows[$k][prelis]\" value=\"" . number_format($v['prelis'], 2, '.', '') . "\" align=\"right\" maxlength=\"11\" onchange=\"this.form.submit()\" />
					</td>
					<td></td>
					<td></td>\n";
			echo '<input type="hidden" value="" name="rows[' . $k . '][cod_operazione]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc_destin]" />
					';
            $last_row[] = array_unshift($last_row, $script_transl['typerow'][$v['tiprig']]);
            break;
       case "4": // rigo cassa previdenziale
            echo '	<td>
						<button type="image" name="upper_row[' . $k . ']" class="btn btn-default btn-xs" title="' . $script_transl['3'] . '!">
							<i class="glyphicon glyphicon-arrow-up">' . ($k+1) . '</i>
						</button>
					</td>
					<td title="' . $script_transl['update'] . $script_transl['thisrow'] . '! ">';
                     $gForm->selectFromXML('../../library/include/fae_tipo_cassa.xml', 'rows[' . $k . '][codart]', 'rows[' . $k . '][codart]', $v["codart"], true, 'fae_tipo_cassa'.$k, 'col-sm-12');

			echo '					  <td>
						<input type="text" name="rows[' . $k . '][descri]" value="' . $descrizione . '" maxlength="100" />
					</td>
                    <td colspan="2" class="text-right">Imponibile:<input type="hidden" name="rows[' . $k . '][unimis]" value="" /><input type="hidden" name="rows[' . $k . '][quanti]" value="" /><input type="hidden" name="rows[' . $k . '][sconto]" value="" /></td>
                    <td><input type="text" name="rows[' . $k . '][prelis]" value="' . number_format($v['prelis'], 2, '.', '') . '" maxlength="11" ';
						if ($vp>0) { // solo se scelto in configurazione avanzata azienda si vedrà il dialog per mettere il prezzo iva compresa
							echo 'onclick="vatPrice(\''.$k.'\',\''.$v['pervat'].'\');"';
						}
						echo ' id="righi_' . $k . '_prelis" onchange="document.docven.last_focus.value=this.id; this.form.submit()" /></td>
					<td>==></td>';
					// in caso tiprig=4 questo campo è utilizzato per indicare l'aliquota della cassa previdenziale
			echo '	<td><input type="text" name="rows[' . $k . '][provvigione]" value="' . $v['provvigione'] . '" maxlength="6" />%</td>	<td class="text-right">	<span class="gazie-tooltip text-right" data-type="ritenuta" data-id="' . $v['ritenuta'] . '% = ' . gaz_format_number(round($imprig * $v['ritenuta']/100, 2)) . '" data-title="' . $script_transl['ritenuta'] . '">'.gaz_format_number($imprig).'</span>			
					</td>
					<td class="text-right">
						' . $v['pervat'] . '%
					</td>
					<td class="text-right codricTooltip" title="Contropartita">
						' . $v['codric'] . '
					</td>';
			echo '<input type="hidden" value="" name="rows[' . $k . '][cod_operazione]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc_destin]" />
					';		
            $last_row[] = array_unshift($last_row, $script_transl['typerow'][$v['tiprig']]);
            break;
        case "6":
        case "7":
        case "8": // testuali
            //<textarea id="row_'.$k.'" name="row_'.$k.'" class="mceClass'.$k.'" style="width:100%;height:100px;">'.$form["row_$k"].'</textarea>
            echo '	<td>
						<button type="image" name="upper_row[' . $k . ']" class="btn btn-default btn-xs" title="' . $script_transl['3'] . '!\">
							<i class="glyphicon glyphicon-arrow-up"></i>
						</button>
					</td>
					<td title="' . $script_transl['update'] . $script_transl['thisrow'] . '!">
						<input class="btn btn-xs btn-success btn-block" type="submit" name="upd_row[' . $k . ']" value="' . $script_transl['typerow'][$v['tiprig']] . '" />
					</td>
					<td colspan="9">
						<textarea id="row_' . $k . '" name="row_' . $k . '" class="mceClass">' . $form["row_$k"] . '</textarea>
					</td>
					<input type="hidden" value="" name="rows[' . $k . '][descri]" />
					<input type="hidden" value="" name="rows[' . $k . '][unimis]" />
                    <input type="hidden" value="" name="rows[' . $k . '][quanti]" />
					<input type="hidden" value="" name="rows[' . $k . '][prelis]" />
					<input type="hidden" value="" name="rows[' . $k . '][sconto]" />
					<input type="hidden" value="" name="rows[' . $k . '][cod_operazione]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc_destin]" />
					<input type="hidden" value="" name="rows[' . $k . '][provvigione]" />';
            $last_row[] = array_unshift($last_row, $script_transl['typerow'][$v['tiprig']]);
            break;
        case "11": // CIG fattura elettronica
        case "12": // CUP fattura elettronica
            echo "	<td>
						<button type=\"image\" name=\"upper_row[" . $k . "]\" class=\"btn btn-default btn-xs\" title=\"" . $script_transl['3'] . "!\">
							<i class=\"glyphicon glyphicon-arrow-up\">" . ($k+1) . "</i>
						</button>
					</td>
					<td title=\"" . $script_transl['update'] . $script_transl['thisrow'] . "!\">
						<input class=\"btn btn-xs btn-secondary btn-block\" type=\"submit\" name=\"upd_row[$k]\" value=\"" . $script_transl['typerow'][$v['tiprig']] . "\" /></td>
					<td colspan=\"8\" title=\"".$script_transl['fae_dati']."\">";
						$gForm->variousSelect('rows['.$k.'][codvat]', $script_transl['fae_dati_value'], $v['codvat'],'');// uso la colonna codvat del database per memorizzare il tipo di dato della fattura elettronica
						
					echo "
						<input type=\"text\" name=\"rows[$k][descri]\" value=\"$descrizione\" maxlength=\"15\" /> riferito a ";
			$gForm->selRifDettaglioLinea('rows['.$k.'][codric]', $v['codric'], $form['RiferimentoNumeroLinea']); // uso la colonna codric del database per memorizzare il rigo di riferimento al dettaglio linea 
			echo "</td>
			<td><input type=\"hidden\" name=\"rows[$k][unimis]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][quanti]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][prelis]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][sconto]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][provvigione]\" value=\"\" />
			</td>\n";
			echo '<input type="hidden" value="" name="rows[' . $k . '][cod_operazione]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc_destin]" />
					';
            $last_row[] = array_unshift($last_row, $script_transl['typerow'][$v['tiprig']]);
            break;
        case "13": // ID documento fattura elettronica
        case "15": // NumItem fattura elettronica
            echo "	<td>
                            <button type=\"image\" name=\"upper_row[" . $k . "]\" class=\"btn btn-default btn-xs\" title=\"" . $script_transl['3'] . "!\">
                                <i class=\"glyphicon glyphicon-arrow-up\">" . ($k+1) . "</i>
                            </button>
			</td>
                        <td title=\"" . $script_transl['update'] . $script_transl['thisrow'] . "!\" >
                            <input class=\"btn btn-xs btn-secondary btn-block\" type=\"submit\" name=\"upd_row[$k]\" value=\"" . $script_transl['typerow'][$v['tiprig']] . "\" />
                        </td>
			<td colspan=\"8\" title=\"".$script_transl['fae_dati']."\">";
						$gForm->variousSelect('rows['.$k.'][codvat]', $script_transl['fae_dati_value'], $v['codvat'],'');// uso la colonna codvat del database per memorizzare il tipo di dato della fattura elettronica
					echo "<input type=\"text\" name=\"rows[$k][descri]\" value=\"$descrizione\" maxlength=\"20\" /> riferito a ";
			$gForm->selRifDettaglioLinea('rows['.$k.'][codric]', $v['codric'], $form['RiferimentoNumeroLinea']); // uso la colonna codric del database per memorizzare il rigo di riferimento al dettaglio linea 
			echo "</td>
			<td><input type=\"hidden\" name=\"rows[$k][unimis]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][quanti]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][prelis]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][sconto]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][provvigione]\" value=\"\" />
			</td>\n";
			echo '<input type="hidden" value="" name="rows[' . $k . '][cod_operazione]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc_destin]" />
					';
            $last_row[] = array_unshift($last_row, $script_transl['typerow'][$v['tiprig']]);
            break;
        case "14": // Data ordine d'acquisto fattura elettronica      
            echo "	<td>
						<button type=\"image\" name=\"upper_row[" . $k . "]\" class=\"btn btn-default btn-xs\" title=\"" . $script_transl['3'] . "!\">
							<i class=\"glyphicon glyphicon-arrow-up\">" . ($k+1) . "</i>
						</button>
					</td>
					<td title=\"" . $script_transl['update'] . $script_transl['thisrow'] . "!\">
						<input class=\"btn btn-xs btn-secondary btn-block\" type=\"submit\" name=\"upd_row[$k]\" value=\"" . $script_transl['typerow'][$v['tiprig']] . "\" />
					</td>
					<td colspan=\"8\" title=\"".$script_transl['fae_dati']."\">";
						$gForm->variousSelect('rows['.$k.'][codvat]', $script_transl['fae_dati_value'], $v['codvat'],'');// uso la colonna codvat del database per memorizzare il tipo di dato della fattura elettronica
					echo "<input type=\"date\" name=\"rows[$k][descri]\" value=\"".$v['descri']."\" maxlength=\"15\" /> riferito a ";
			$gForm->selRifDettaglioLinea('rows['.$k.'][codric]', $v['codric'], $form['RiferimentoNumeroLinea']); // uso la colonna codric del database per memorizzare il rigo di riferimento al dettaglio linea 
			echo "</td>
			<td><input type=\"hidden\" name=\"rows[$k][unimis]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][quanti]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][prelis]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][sconto]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][provvigione]\" value=\"\" />
			</td>\n";
			echo '<input type="hidden" value="" name="rows[' . $k . '][cod_operazione]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc_destin]" />
					';
            $last_row[] = array_unshift($last_row, $script_transl['typerow'][$v['tiprig']]);
            break;
        case "16": // CodiceCommessaConvenzione fattura elettronica
            echo "	<td>
                            <button type=\"image\" name=\"upper_row[" . $k . "]\" class=\"btn btn-default btn-xs\" title=\"" . $script_transl['3'] . "!\">
                                <i class=\"glyphicon glyphicon-arrow-up\">" . ($k+1) . "</i>
                            </button>
			</td>
                        <td title=\"" . $script_transl['update'] . $script_transl['thisrow'] . "!\" >
                            <input class=\"btn btn-xs btn-secondary btn-block\" type=\"submit\" name=\"upd_row[$k]\" value=\"" . $script_transl['typerow'][$v['tiprig']] . "\" />
                        </td>
			<td colspan=\"8\" title=\"".$script_transl['fae_dati']."\">";
						$gForm->variousSelect('rows['.$k.'][codvat]', $script_transl['fae_dati_value'], $v['codvat'],'');// uso la colonna codvat del database per memorizzare il tipo di dato della fattura elettronica
					echo "<input type=\"text\" name=\"rows[$k][descri]\" value=\"$descrizione\" maxlength=\"100\" /> riferito a ";
			$gForm->selRifDettaglioLinea('rows['.$k.'][codric]', $v['codric'], $form['RiferimentoNumeroLinea']); // uso la colonna codric del database per memorizzare il rigo di riferimento al dettaglio linea 
			echo "</td>
			<td><input type=\"hidden\" name=\"rows[$k][unimis]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][quanti]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][prelis]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][sconto]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][provvigione]\" value=\"\" />
			</td>\n";
			echo '<input type="hidden" value="" name="rows[' . $k . '][cod_operazione]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc_destin]" />
					';
            $last_row[] = array_unshift($last_row, $script_transl['typerow'][$v['tiprig']]);
            break;
        case "21": // Causale 2.1.1.11 fattura elettronica
            echo "	<td>
                            <button type=\"image\" name=\"upper_row[" . $k . "]\" class=\"btn btn-default btn-xs\" title=\"" . $script_transl['3'] . "!\">
                                <i class=\"glyphicon glyphicon-arrow-up\">" . ($k+1) . "</i>
                            </button>
			</td>
                        <td title=\"" . $script_transl['update'] . $script_transl['thisrow'] . "!\" >
                            <input class=\"btn btn-xs btn-secondary btn-block\" type=\"submit\" name=\"upd_row[$k]\" value=\"" . $script_transl['typerow'][$v['tiprig']] . "\" />
                        </td>
			<td colspan=\"8\">
                            <input type=\"text\" name=\"rows[$k][descri]\" value=\"$descrizione\" maxlength=\"100\" /> riferita a tutto il documento</td>
			<td><input type=\"hidden\" name=\"rows[$k][unimis]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][quanti]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][prelis]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][sconto]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][provvigione]\" value=\"\" />
			</td>\n";
			echo '<input type="hidden" value="" name="rows[' . $k . '][cod_operazione]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc_destin]" />
					';
            $last_row[] = array_unshift($last_row, $script_transl['typerow'][$v['tiprig']]);
            break;
        case "25": // SAL Riferimento Fase 2.1.7 fattura elettronica
            echo "	<td>
                            <button type=\"image\" name=\"upper_row[" . $k . "]\" class=\"btn btn-default btn-xs\" title=\"" . $script_transl['3'] . "!\">
                                <i class=\"glyphicon glyphicon-arrow-up\">" . ($k+1) . "</i>
                            </button>
			</td>
                        <td title=\"" . $script_transl['update'] . $script_transl['thisrow'] . "!\" >
                            <input class=\"btn btn-xs btn-secondary\" type=\"submit\" name=\"upd_row[$k]\" value=\"" . $script_transl['typerow'][$v['tiprig']] . "\" />
                        </td>
			<td colspan=\"8\">Fase dello stato di avanzamento
                            <input type=\"number\" step=1 min=1 max=999 name=\"rows[$k][descri]\" value=\"$descrizione\" maxlength=\"100\" /> </td>
			<td><input type=\"hidden\" name=\"rows[$k][unimis]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][quanti]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][prelis]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][sconto]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][provvigione]\" value=\"\" />
			</td>\n";
			echo '<input type="hidden" value="" name="rows[' . $k . '][cod_operazione]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc_destin]" />
					';
            $last_row[] = array_unshift($last_row, $script_transl['typerow'][$v['tiprig']]);
            break;
        case "31": // Dati veicolo 2.3 fattura elettronica
            echo "	<td>
                            <button type=\"image\" name=\"upper_row[" . $k . "]\" class=\"btn btn-default btn-xs\" title=\"" . $script_transl['3'] . "!\">
                                <i class=\"glyphicon glyphicon-arrow-up\">" . ($k+1) . "</i>
                            </button>
			</td>
                        <td title=\"" . $script_transl['update'] . $script_transl['thisrow'] . "!\" >
                            <input class=\"btn btn-xs btn-secondary btn-block\" type=\"submit\" name=\"upd_row[$k]\" value=\"" . $script_transl['typerow'][$v['tiprig']] . "\" />
                        </td>
			<td colspan=\"8\">Data prima immatricolazione
                            <input type=\"date\" name=\"rows[$k][descri]\" value=\"$descrizione\" maxlength=\"100\"  /> KM percorsi:<input type=\"number\" step=1 min=0 max=1000000  name=\"rows[$k][quanti]\" value=\"".$v['quanti']."\" /></td>
			<td><input type=\"hidden\" name=\"rows[$k][unimis]\" value=\"\" />
			
			<input type=\"hidden\" name=\"rows[$k][prelis]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][sconto]\" value=\"\" />
			<input type=\"hidden\" name=\"rows[$k][provvigione]\" value=\"\" />
			</td>\n";
			echo '<input type="hidden" value="" name="rows[' . $k . '][cod_operazione]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc_destin]" />
					';
            $last_row[] = array_unshift($last_row, $script_transl['typerow'][$v['tiprig']]);
            break;
        case "210":  // nel caso di articoli composti li visualizzo nel documento per poter inserire la seconda quantità contattare andrea
            if ( $show_artico_composit['val']=="1" && $tipo_composti['val']=="KIT") {    
                echo "	<td>&nbsp;</td>
                <td title=\"".$script_transl['update'] . $script_transl['thisrow'] . '! ' .  $btn_title . "\">
                    <button name=\"upd_row[' . $k . ']\" class=\"btn btn-xs btn-default btn-block\" type=\"submit\">
                        <i class=\"glyphicon glyphicon-refresh\"></i>&nbsp;" . $v['codart'] . "
                    </button>
                </td>
                    <td>
                        <input type=\"text\" name=\"rows[$k][descri]\" value=\"$descrizione\" maxlength=\"20\" />
                    </td>
                    <td><input class=\"gazie-tooltip\" data-type=\"weight\" data-id=\"" . $peso . "\" data-title=\"" . $script_transl["weight"] . "\" type=\"text\" name=\"rows[" . $k . "][unimis]\" value=\"" . $v["unimis"] . "\" maxlength=\"3\" />
                </td>
                <td>
                    <input class=\"gazie-tooltip\" data-type=\"weight\" data-id=\"" . $peso . "\" data-title=\"" . $script_transl['weight'] . "\" type=\"text\" name=\"rows[" . $k . "][quanti]\" value=\"" . $v["quanti"] . "\" align=\"right\" maxlength=\"11\" id=\"righi_" . $k . "_quanti\" onchange=\"document.docven.last_focus.value=\"righi_" . $k . "_prelis\"; this.form.hidden_req.value=\"ROW\"; this.form.submit();\" />
                </td>
                <td><input type=\"hidden\" name=\"rows[$k][prelis]\" value=\"\" /></td>
                <td><input type=\"hidden\" name=\"rows[$k][sconto]\" value=\"\" /></td>
                <td><input type=\"hidden\" name=\"rows[$k][provvigione]\" value=\"\" /></td>
                <td></td>
                <td></td>
                <td></td>\n";
				echo '<input type="hidden" value="" name="rows[' . $k . '][cod_operazione]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc_destin]" />
					';
            } else {
                echo "<input type=\"hidden\" name=\"rows[$k][descri]\" value=\"$descrizione\" maxlength=\"20\"  />
                    <input type=\"hidden\" class=\"gazie-tooltip\" data-type=\"weight\" data-id=\"" . $peso . "\" data-title=\"" . $script_transl["weight"] . "\" type=\"text\" name=\"rows[" . $k . "][unimis]\" value=\"" . $v["unimis"] . "\" maxlength=\"3\" />
                    <input type=\"hidden\" class=\"gazie-tooltip\" data-type=\"weight\" data-id=\"" . $peso . "\" data-title=\"" . $script_transl['weight'] . "\" type=\"text\" name=\"rows[" . $k . "][quanti]\" value=\"" . $v["quanti"] . "\" align=\"right\" maxlength=\"11\"  id=\"righi_" . $k . "_quanti\" onchange=\"document.docven.last_focus.value=\"righi_" . $k . "_prelis\"; this.form.hidden_req.value=\"ROW\"; this.form.submit();\" />
                    <input type=\"hidden\" name=\"rows[$k][prelis]\" value=\"\" />
                    <input type=\"hidden\" name=\"rows[$k][sconto]\" value=\"\" />
                    <input type=\"hidden\" name=\"rows[$k][provvigione]\" value=\"\" />";
            }
            //$last_row[] = array_unshift($last_row, $script_transl['typerow'][$v['tiprig']]);
            break;           

		case "90": //ventita cespite - alienazione bene ammortizzabile
            /*
             */
            echo '	<td>
						<button type="image" name="upper_row[' . $k . ']" class="btn btn-default btn-xs" title="' . $script_transl['3'] . '!">
							<i class="glyphicon glyphicon-arrow-up">' . ($k+1) . '</i>
						</button>
					</td>
					<td title="' . $script_transl['update'] . $script_transl['thisrow'] . '!\">
						<input class="btn btn-xs btn-success btn-block" type="submit" name="upd_row[' . $k . ']" value="' . $script_transl['typerow'][$v['tiprig']] . '" />
					</td>
					  <td  colspan="6" >';
            $gForm->selectAsset('rows[' . $k . '][codric]', $v['codric']);

            echo '<input type="text" name="rows[' . $k . '][descri]" value="' . $descrizione . '" maxlength="100" />
					</td>
					<td class="text-right">';

            echo '<input type="hidden" name="rows[' . $k . '][unimis]" value="" />
                    <input type="hidden" name="rows[' . $k . '][quanti]" value="" />
					<input type="hidden" name="rows[' . $k . '][sconto]" value="" />
					<input type="hidden" name="rows[' . $k . '][provvigione]" value="" />
                    <input class="gazie-tooltip text-right" data-type="ritenuta" data-id="' . $v['ritenuta'] . '% = ' . gaz_format_number(round($imprig * $v['ritenuta'] / 100, 2)) . '" data-title="' . $script_transl['ritenuta'] . '" type="text" name="rows[' . $k . '][prelis]" value="' . number_format($v['prelis'], 2, '.', '') . '" maxlength="11" onchange="this.form.submit()" />
					</td>
					<td class="text-right">
					</td>
					<td class="text-right">
					</td>';
			echo '<input type="hidden" value="" name="rows[' . $k . '][cod_operazione]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc]" />
					<input type="hidden" value="" name="rows[' . $k . '][recip_stocc_destin]" />
					';		
            $last_row[] = array_unshift($last_row, $script_transl['typerow'][$v['tiprig']]);
            break;
    }
    //echo "<td align=\"right\"><input type=\"image\" name=\"del[$k]\" src=\"../../library/images/xbut.gif\" title=\"" . $script_transl['delete'] . $script_transl['thisrow'] . "!\" /></td></tr>\n";

    /** ENRICO FEDELE */
    /* glyph icon */
    if ( $v['tiprig']!="210" || $show_artico_composit['val']=="1" && $tipo_composti['val']=="KIT" ) { 
        echo '<td align="right">
		     <button type="submit" class="btn btn-default btn-xs" name="del[' . $k . ']" title="' . $script_transl['delete'] . $script_transl['thisrow'] . '"><i class="glyphicon glyphicon-remove"></i></button>
		   </td>';
    }
    echo '</tr>';
    /** ENRICO FEDELE */
}
/** ENRICO FEDELE */
/* Nuovo alert per scontistica, da visualizzare rigorosamente dopo l'ultima riga inserita */
if (count($form['rows']) > 0) {
    $msgtoast = $upd_mm->toast($msgtoast);  //lo mostriamo

} else {
    echo '<tr id="alert-zerorows">
			<td colspan="12" class="alert alert-danger">' . $script_transl['zero_rows'] . '</td>
		  </tr>';
}
echo '		</tbody>
		</table></div>';
        
        
// *** INIZIO RIGO INPUT
echo '	<input type="hidden" value="' . $form['in_descri'] . '" name="in_descri" />
		<input type="hidden" value="' . $form['in_pervat'] . '" name="in_pervat" />
		<input type="hidden" value="' . $form['in_tipiva'] . '" name="in_tipiva" />
		<input type="hidden" value="' . $form['in_ritenuta'] . '" name="in_ritenuta" />
        <input type="hidden" value="' . $form['in_unimis'] . '" name="in_unimis" />
		<input type="hidden" value="' . $form['in_prelis'] . '" name="in_prelis" />
		<input type="hidden" value="' . $form['in_id_mag'] . '" name="in_id_mag" />
		<input type="hidden" value="' . $form['in_annota'] . '" name="in_annota" />
		<input type="hidden" value="' . $form['in_scorta'] . '" name="in_scorta" />
		<input type="hidden" value="' . $form['in_quamag'] . '" name="in_quamag" />
		<input type="hidden" value="' . $form['in_pesosp'] . '" name="in_pesosp" />
		<input type="hidden" value="' . $form['in_gooser'] . '" name="in_gooser" />
		<input type="hidden" value="' . $form['in_lot_or_serial'] . '" name="in_lot_or_serial" />
		<input type="hidden" value="' . $form['in_SIAN'] . '" name="in_SIAN" />
		<input type="hidden" value="' . $form['in_id_lotmag'] . '" name="in_id_lotmag" />
		<input type="hidden" value="' . $form['in_identifier'] . '" name="in_identifier" />
		<input type="hidden" value="' . $form['in_cod_operazione'] . '" name="in_cod_operazione" />
		<input type="hidden" value="' . $form['in_recip_stocc'] . '" name="in_recip_stocc" />
		<input type="hidden" value="' . $form['in_recip_stocc_destin'] . '" name="in_recip_stocc_destin" />
		<input type="hidden" value="' . $form['in_status'] . '" name="in_status" />
		<input type="hidden" value="' . $form['hidden_req'] . '" name="hidden_req" />
		<input type="hidden" value="' . $form['ok_barcode'] . '" name="ok_barcode" />
		';

?>
<!-- DISEGNO LA FORM DI INSERIMENTO DATI -->
<div class="text-center"><?php echo $script_transl[1]; ?></div>
<div class="panel panel-info div-bordered">
  <div class="panel-body"> 
    <div class="container-fluid">  
        <div class="row first_row">
            <div class="form-group col-xs-12 col-sm-6 col-md-3"> 
                <label for="in_tiprig" ><?php echo $script_transl[17].":"; ?></label> 
<?php
$gForm->selTypeRow('in_tiprig', $form['in_tiprig']);
?>
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-3"> 
                <label for="in_codart" ><?php echo $script_transl[15] . ':'; ?></label> 
<?php
$select_artico = new selectartico("in_codart");
$select_artico->addSelected($form['in_codart']);
$select_artico->output(substr($form['cosear'], 0, 20));
// Antonio Germani - input ricerca con pistola lettore codice a barre 
if ($toDo == "insert"){
	$class_btn_confirm='btn-success';
	if ($form['ok_barcode']!="ok"){
		?>
				
		<?php
	} else {
		if ($form['in_barcode']==""){
		?>						
				<label for="no_barcode" ><?php echo "Barcode"; ?></label>
				<input  type="text" value="<?php echo $form['in_barcode']; ?>" name="in_barcode" class="col-xs-4" onchange="this.form.submit()" />
				<button type="submit"  name="no_barcode" title="Togli con pistola Barcode"> 
                <span class="glyphicon glyphicon-remove"></span>
				</button>								
		<?php
		} elseif ($form['in_barcode']=="NOT FOUND") {
			$form['in_barcode']="";
			?>						
				<label for="no_barcode" ><?php echo "Barcode"; ?></label>
				<input style="border: 1px solid red;"  type="text" value="<?php echo $form['in_barcode']; ?>" class="col-xs-4" name="in_barcode" onchange="this.form.submit()" />
				<button type="submit"  name="no_barcode" title="Togli con pistola Barcode"> 
				<span class="glyphicon glyphicon-remove"></span>							
			<?php
		}
	}
}else{
	$class_btn_confirm='btn-warning';
}
// Antonio Germani - fine ricerca con pistola lettore codice a barre -->
?>
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-3"> 
                <label for="in_quanti" ><?php echo  $script_transl[16].':'; ?></label>
                <input type="text" id="in_quanti" value="<?php echo $form['in_quanti']; ?>" maxlength="11" name="in_quanti" tabindex="5" accesskey="q">
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-3"> 
                <label for="vat_constrain" ><?php echo $script_transl['vat_constrain']; ?></label>
                <?php
                $select_in_codvat = new selectaliiva("in_codvat");
                $select_in_codvat->addSelected($form["in_codvat"]);
                $select_in_codvat->output();
                ?>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-xs-12 col-sm-6 col-md-3"> 
                <label for="in_sconto" ><?php echo ' %'.$script_transl[24].':'; ?></label>
                <input type="text" value="<?php echo $form['in_sconto']; ?>" maxlength="6" name="in_sconto" title="# = sconto standard dell'articolo">
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-3"> 
                <label for="in_provvigione" ><?php echo  $script_transl[56].':'; ?></label>
                <input type="text" value="<?php echo $form['in_provvigione']; ?>" maxlength="6" name="in_provvigione">
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-3"> 
                <label for="in_ritenuta" ><?php echo ' %' . $script_transl['ritenuta']; ?></label>
                <input type="text" value="<?php $form['in_ritenuta']; ?>" maxlength="6" name="in_ritenuta">
            </div>
            <div class="form-group col-xs-12 col-sm-6 col-md-3"> 
                <label for="in_codric" class="col-xs-3"><?php echo  $script_transl[18]; ?></label>
                <?php
                $ric = array('sub',intval(substr($form['in_codric'], 0, 1)));
                if ($form['tipdoc'] == 'FAP' || $form['tipdoc'] == 'FAQ') {
                    $ric = array('sub', 1, 2, 4, 5);
                } else if (substr($form['tipdoc'],0,2) == 'FA' || $form['tipdoc']== 'DDT'){
                    $ric = array('sub', 1, 4);
                }
                if (!in_array(substr($form['in_codric'],0,1),$ric)){
                    $ric[]=substr($form['in_codric'],0,1);
                }
                $gForm->selectAccount('in_codric', $form['in_codric'], $ric,'',false,'col-xs-9');
                ?>
            </div>




        </div>
        <div class="row">
            
            <?php
            if (substr($form['in_status'], 0, 6) != "UPDROW") { //se non è un rigo da modificare
            ?>
            <div class="col-xs-6 col-sm-6 col-md-9 text-right"> 
            <!--<div class="form-group col-xs-12 col-sm-6 col-md-3">-->
                <a id="addmodal" href="#myModal" data-toggle="modal" data-target="#edit-modal" class="btn btn-sm btn-default"><i class="glyphicon glyphicon-export"></i><?php //echo $script_transl['add_article']; ?></a>
            <!--</div>-->

                <button type="submit" class="btn btn-default btn-sm" name="button_ok_barcode" title="inserisci con pistola Barcode"><i class="glyphicon glyphicon-barcode"></i></button>
                <button type="submit" class="btn btn-default btn-sm" name="in_submit_desc" title="Aggiungi rigo Descrittivo"><i class="glyphicon glyphicon-pencil"></i></button>
                <button type="submit" class="btn btn-default btn-sm" name="in_submit_text" title="Aggiungi rigo Testo"><i class="glyphicon glyphicon-list"></i></button>
                <button type="submit" class="btn btn-default btn-sm" name="in_submit_cig" title="Aggiungi rigo CIG">CIG</button>
            </div>
<?php
}else{
?>
            <div class="col-xs-6 col-sm-6 col-md-3"></div>
<?php    
}
?>
            <div class="form-group col-xs-6 col-sm-6 col-md-3 text-right"> 
                <button type="submit" class="btn btn-success btn-sm" name="in_submit" title="<?php echo $script_transl['submit'] . $script_transl['thisrow'] ?>" tabindex="6"><i class="glyphicon glyphicon-ok"> Conferma </i></button>
            </div>
		</div>
	</div><!-- chiude container-fuid -->
  </div>
</div><!-- chiude panel -->

<div class="FacetSeparatorTD text-center"><?php echo $script_transl[2]; ?></div>
	<input type="hidden" value="<?php echo $form['numrat']; ?>" name="numrat">
	<input type="hidden" value="<?php echo $form['stamp']; ?>" name="stamp">
	<input type="hidden" value="<?php echo $form['round_stamp']; ?>" name="round_stamp">
	<input type="hidden" value="<?php echo $form['spevar']; ?>" name="spevar">
	<input type="hidden" value="<?php echo $form['cauven']; ?>" name="cauven">
	<input type="hidden" value="<?php echo $form['caucon']; ?>" name="caucon">
	<div class="box-primary table-responsive"><table class="Tlarge table table-bordered table-condensed">
<?php
$somma_spese = $form['traspo'] + $form['speban'] * $form['numrat'] + $form['spevar'];
$calc->add_value_to_VAT_castle($castle, $somma_spese, $form['expense_vat']);
if ($calc->total_exc_with_duty >= $admin_aziend['taxstamp_limit'] && $form['virtual_taxstamp'] > 0 && $form['taxstamp'] < 0.01) {
    $form['taxstamp'] = $admin_aziend['taxstamp'];
} elseif ($calc->total_exc_with_duty < $admin_aziend['taxstamp_limit']) { // se l'importo è inferiore (ad es. eliminado righi) azzero i bolli
    $form['taxstamp'] = 0;
}


if ($form['tipdoc'] == 'DDT' || $form['tipdoc'] == 'DDV' || $form['tipdoc'] == 'DDY' || $form['tipdoc'] == 'DDS' || 
    $form['template'] == 'FatturaImmediata' || $form['tipdoc'] == 'FAD' || $form['tipdoc']=='CMR' ||
    $form['tipdoc']=='FAC') {
    echo "		<tr>
					<td class=\"FacetFieldCaptionTD text-right\">$script_transl[26]</td>
					<td class=\"FacetDataTD\">
						<input type=\"text\" name=\"imball\" value=\"" . $form["imball"] . "\" maxlength=\"50\" class=\"FacetInput\" />\n";
    $select_spediz = new SelectValue("imballo");
    $select_spediz->output('imball', 'imball');
    echo "			</td>
					<td class=\"FacetFieldCaptionTD text-right\">$script_transl[27]</td>
					<td class=\"FacetDataTD\">
						<input type=\"text\" name=\"spediz\" value=\"" . $form["spediz"] . "\" maxlength=\"50\" class=\"FacetInput\" />\n";
    $select_spediz = new SelectValue("spedizione");
    $select_spediz->output('spediz', 'spediz');
    /** ENRICO FEDELE */
    /* td chiuso male */
    echo "			</td>
					<td class=\"FacetFieldCaptionTD\">$script_transl[14]</td>
					<td class=\"FacetDataTD\">\n";
    $select_vettor = new selectvettor("vettor");
    $select_vettor->addSelected($form["vettor"]);
    $select_vettor->output();
    echo "			</td>
					<td class=\"FacetFieldCaptionTD text-right\">$script_transl[29]</td>
					<td class=\"FacetDataTD\">
						<input type=\"text\" name=\"portos\" value=\"" . $form["portos"] . "\" maxlength=\"50\" class=\"FacetInput\" />\n";
    $select_spediz = new SelectValue("portoresa");
    $select_spediz->output('portos', 'portos');
    echo "
					</td>
				</tr>";
?>
<!-- SECONDA RIGA - 8 colonne -->
				<tr>
					<td class="FacetFieldCaptionTD text-right\"><?php echo $script_transl[30];?></td>
					<td class="FacetDataTD"><div class="col-xs-12">
                        <input class="col-xs-6" type="text" id="initra" name="initra" value="<?php echo $form['initra']; ?>">
						<div class="col-xs-2"><?php echo $script_transl[31];
    // select dell'ora
    echo "</div>\t <select name=\"oratra\" class=\"col-xs-2\" >\n";
    for ($counter = 0; $counter <= 23; $counter++) {
        $selected = "";
        if ($counter == $form['oratra'])
            $selected = ' selected=""';
        echo "\t\t <option value=\"" . sprintf('%02d', $counter) . "\" $selected >" . sprintf('%02d', $counter) . "</option>\n";
    }
    echo "\t </select>\n ";
    // select dell'ora
    echo "\t <select name=\"mintra\" class=\"col-xs-2\" >\n";
    for ($counter = 0; $counter <= 59; $counter++) {
        $selected = "";
        if ($counter == $form['mintra'])
            $selected = ' selected=""';
        echo "\t\t <option value=\"" . sprintf('%02d', $counter) . "\" $selected >" . sprintf('%02d', $counter) . "</option>\n";
    }
    echo "				\t</select>
						</div></td>
						<td class=\"FacetFieldCaptionTD\">$script_transl[10]</td>\n";
//    if ($form['id_des_same_company'] > 0) { //  è una destinazione legata all'anagrafica
    $tmpIdAnagra=(isset($cliente['id_anagra']) ? $cliente['id_anagra'] : "");
    if (!empty($tmpIdAnagra) && gaz_dbi_record_count($gTables['destina'], "id_anagra=$tmpIdAnagra") > 0) { //  è una destinazione legata all'anagrafica
       
        echo "<td class=\"FacetDataTD\">\n";
        $gForm->selectFromDB('destina', 'id_des_same_company', 'codice', $form['id_des_same_company'], 'codice', true, '-', 'unita_locale1', '', 'FacetSelect', null, '', "id_anagra = '" . $cliente['id_anagra'] . "'");
//        echo selectDestinazione($cliente['id_anagra']);
        echo "	<br/><textarea rows=\"1\" cols=\"30\" name=\"destin\" class=\"FacetInput\">" . $form["destin"] . "</textarea>
						</td>
						<input type=\"hidden\" name=\"id_des\" value=\"" . $form['id_des'] . "\">
						<input type=\"hidden\" name=\"search[id_des]\" value=\"" . $form['search']['id_des'] . "\">\n";
    } elseif ($form['id_des'] > 0) { // la destinazione è un'altra anagrafica
        echo "<td class=\"FacetDataTD\">\n";
        $select_id_des = new selectPartner('id_des');
        $select_id_des->selectDocPartner('id_des', 'id_' . $form['id_des'], $form['search']['id_des'], 'id_des', $script_transl['mesg'], $admin_aziend['mascli']);
        echo "			<input type=\"hidden\" name=\"id_des_same_company\" value=\"" . $form['id_des_same_company'] . "\">
                                <input type=\"hidden\" name=\"destin\" value=\"" . $form['destin'] . "\" />
						</td>\n";
    } else {
        echo "			<td class=\"FacetDataTD\">";
        echo "				<textarea rows=\"1\" cols=\"30\" name=\"destin\" class=\"FacetInput\">" . $form["destin"] . "</textarea>
						</td>
						<input type=\"hidden\" name=\"id_des_same_company\" value=\"" . $form['id_des_same_company'] . "\">
						<input type=\"hidden\" name=\"id_des\" value=\"" . $form['id_des'] . "\">
						<input type=\"hidden\" name=\"search[id_des]\" value=\"" . $form['search']['id_des'] . "\">\n";
    }
    echo "			<td align=\"right\" class=\"FacetFieldCaptionTD\">$script_transl[54]</td>
					<td class=\"FacetDataTD\"><input type=\"text\" value=\"" . $form['units'] . "\" name=\"units\" maxlength=\"6\" ></td>
					<td align=\"right\" class=\"FacetFieldCaptionTD\">$script_transl[55]</td>
					<td class=\"FacetDataTD\"><input type=\"text\" value=\"" . $form['volume'] . "\" name=\"volume\" maxlength=\"20\"  ></td>
				</tr>
				<tr>
					<td align=\"right\" class=\"FacetFieldCaptionTD\">$script_transl[52]</td>
					<td class=\"FacetDataTD\"><input type=\"text\" value=\"" . $form['net_weight'] . "\" name=\"net_weight\" maxlength=\"9\" ></td>
					<td align=\"right\" class=\"FacetFieldCaptionTD\">$script_transl[53]</td>
					<td class=\"FacetDataTD\"><input type=\"text\" value=\"" . $form['gross_weight'] . "\" name=\"gross_weight\" maxlength=\"9\" ></td>
					<td class=\"FacetFieldCaptionTD\" colspan=\"8\"><div class=\"col-xs-2\">
						" . $script_transl['taxstamp'] . "<input type=\"text\" value=\"" . $form['taxstamp'] . "\" name=\"taxstamp\" maxlength=\"6\" onchange=\"this.form.submit();\" ></div><div class=\"col-xs-2\">" . $script_transl['virtual_taxstamp'] ;
    $gForm->variousSelect('virtual_taxstamp', $script_transl['virtual_taxstamp_value'], $form['virtual_taxstamp'],'FacetSelect',true,'virtual_taxstamp');
    echo "		</div>	</td>
				</tr>";
} else {
    echo '	<tr><td class="FacetFieldCaptionTD"><input type="hidden" value="' . $form['imball'] . '" name="imball" />
			<input type="hidden" value="' . $form['spediz'] . '" name="spediz" />
			<input type="hidden" value="' . $form['vettor'] . '" name="vettor" />
			<input type="hidden" value="' . $form['portos'] . '" name="portos" />
			<input type="hidden" value="' . $form['initra'] . '" name="initra" />
			<input type="hidden" value="' . $form['oratra'] . '" name="oratra" />
			<input type="hidden" value="' . $form['mintra'] . '" name="mintra" />
			<input type="hidden" value="' . $form['id_des'] . '" name="id_des" />
			<input type="hidden" value="' . $form['id_des_same_company'] . '" name="id_des_same_company" />
			<input type="hidden" value="' . $form['search']['id_des'] . '" name="search[id_des]" />
			<input type="hidden" value="' . $form['destin'] . '" name="destin" />
			<input type="hidden" value="' . $form['net_weight'] . '" name="net_weight" />
			<input type="hidden" value="' . $form['gross_weight'] . '" name="gross_weight" />
			<input type="hidden" value="' . $form['units'] . '" name="units" />
			<input type="hidden" value="' . $form['volume'] . '" name="volume" />
			</td>
				<td class="FacetFieldCaptionTD" colspan="8">'."<div class=\"col-xs-2\">
						" . $script_transl['taxstamp'] . "<input type=\"text\" value=\"" . $form['taxstamp'] . "\" name=\"taxstamp\" maxlength=\"6\" onchange=\"this.form.submit();\" ></div><div class=\"col-xs-2\">" . $script_transl['virtual_taxstamp'];
    $gForm->variousSelect('virtual_taxstamp', $script_transl['virtual_taxstamp_value'], $form['virtual_taxstamp'],'FacetSelect',true,'virtual_taxstamp');
    echo "		</div>".'</td>
		  	</tr>';
}

echo '	<tr>
			<td class="FacetFieldCaptionTD text-right">' . $script_transl[32] . '</td>
			<td class="FacetFieldCaptionTD text-right">' . $script_transl[33] . '</td>
			<td class="FacetFieldCaptionTD text-right">' . $script_transl[34] . '</td>
			<td class="FacetFieldCaptionTD text-right">
				% ' . $script_transl[24] . ' <input type="text" name="sconto" value="' . $form["sconto"] . '" maxlength="6"  onchange="this.form.submit()" />
			</td>
			<td class="FacetFieldCaptionTD text-right">' . $script_transl[32] . '</td>
			<td class="FacetFieldCaptionTD text-right">' . $script_transl[19] . '</td>
			<td class="FacetFieldCaptionTD text-right">' . $script_transl['stamp'] . '</td>
			<td class="FacetFieldCaptionTD text-right">' . $script_transl[36] . ' ' . $admin_aziend['html_symbol'] . '</td>
		</tr>';
$i = 0;
foreach ($calc->castle as $k => $v) {
    echo '	<tr>
				<td class="text-right">' . gaz_format_number($v['impcast']) . '</td>
				<td class="text-right">' . $v['descriz'] . ' ' . gaz_format_number($v['ivacast']) . '</td>
				<td colspan="6"></td>
			</tr>';
}

if ($next_row > 0) {
    echo '<tr>
			<td colspan="2"></td>';
    if ($form['stamp'] > 0) {
        $calc->payment_taxstamp($calc->total_imp + $calc->total_vat + $carry - $rit + $form['taxstamp'], $form['stamp'], $form['round_stamp'] * $form['numrat']);
        $stamp = $calc->pay_taxstamp;
    } else {
        $stamp = 0;
    }
    if ($form['virtual_taxstamp'] == 3) { // se senza addebito di bollo virtuale azzero il valore taxstamp
        $form['taxstamp'] = 0;  // forzo al nuovo modo 3 (bollo a carico dell'emittente)
    }
    echo '		<td class="text-right">' . gaz_format_number($totimp_body) . '</td>
				<td class="text-right">' . gaz_format_number(($totimp_body - $calc->total_imp + $somma_spese), 2, '.', '') . '</td>
				<td class="text-right">' . gaz_format_number($calc->total_imp) . '</td>
				<td class="text-right">' . gaz_format_number($calc->total_vat) . '</td>
				<td class="text-right">' . gaz_format_number($stamp) . '</td>
				<td class="text-center"><div class="col-sm-2"><button type="submit" class="btn btn-default btn-xs" name="roundup"';
    if (!empty($form['roundup_y']) || $rit >= 0.01) {
        echo ' disabled  title="Arrotondamento disabilitato!" ';
    }
    echo '><i class="glyphicon glyphicon-arrow-up"></i></button></div><div class="col-sm-8"><b>' . gaz_format_number($calc->total_imp + $calc->total_vat + $stamp + $form['taxstamp']) . '</b></div><div class="col-sm-2"><button type="submit" class="btn btn-default btn-xs" name="rounddown"';
    if ($rit >= 0.01) {
        echo ' disabled  title="Arrotondamento disabilitato!" ';
    }
    echo '><i class="glyphicon glyphicon-arrow-down"></i></button></div></td>
			</tr>';
    if ($rit > 0) {
        echo '	<tr>
					<td class="text-right" colspan="7">' . $script_transl['ritenuta'] . '</td>
					<td class="text-right">' . gaz_format_number($rit) . '</td>
				</tr>
				<tr>
					<td class="text-right" colspan="7">' . $script_transl['netpay'] . '</td>
					<td class="text-right">' . gaz_format_number($calc->total_imp + $calc->total_vat + $stamp - $rit + $form['taxstamp']) . '</td>
				</tr>';
    }
	if (!empty($msg['war'][0])){
		$class_btn_confirm = "btn-danger";
		$addvalue=" Nonostante l'errore";
	}
    echo '		<tr>
					<td colspan="8" class="text-center FacetFieldCaptionTD">
						<input name="ins" class="btn '.$class_btn_confirm.'" id="preventDuplicate" onClick="chkSubmit();" type="submit" value="' . ucfirst($script_transl[$toDo]). @$addvalue . '">
					</td>
				</tr>';
}
echo '</table>';
?>
</div>
</form>
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
		var prevat = parseFloat(prelis)*(1+parseFloat(pervat)/100);
		$("#cat_prevat").val(prevat.toFixed(<?php echo $admin_aziend['decimal_price'] ?>));
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
			var prelis = parseFloat(prevat)/(1+parseFloat(pervat)/100);
			$("#cat_prelis").val(prelis.toFixed(<?php echo $admin_aziend['decimal_price'] ?>));
		} else {
			$("#cat_prelis").val("0");
		}
	}
</script>
<?php
if ($form['ok_barcode']=="ok"){
	?>
	<script type="text/javascript">
	if (this.document.docven.in_barcode.value == '') this.document.docven.in_barcode.focus();
	</script>
	<?php
}
?>
<script language="JavaScript">
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

$( document ).ready(function() {
	$(".codricTooltip").each(function(index){$(this).attr('title', $("#in_codric option[value='"+$( this ).text().trim()+"']").text());});
});

</script>
<?php
require("../../library/include/footer.php");
?>
