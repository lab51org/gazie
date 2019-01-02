<?php
/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2019 - Antonio De Vincentiis Montesilvano (PE)
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
$anno = date("Y");
$msg = "";
$lm = new lotmag;
$upd_mm = new magazzForm;
$docOperat = $upd_mm->getOperators();

/**
 * carica i dati del cliente dentro $form
 */
function caricaCliente(&$form) {
    global $gTables;
    $_POST['num_rigo'] = 0;
    $form['traspo'] = 0;
    $anagrafica = new Anagrafica();
    $cliente = $anagrafica->getPartner($form['clfoco']);
    $form['indspe'] = $cliente['indspe'] . " - " . $cliente['capspe'] . " " . $cliente['citspe'] . " " . $cliente['prospe'];
    $rs_testate = gaz_dbi_dyn_query("*", $gTables['tesbro'], "clfoco = '" . $form['clfoco'] . "' and tipdoc = 'VOR' AND status NOT LIKE 'EV%' ", "datemi ASC");
    while ($testate = gaz_dbi_fetch_array($rs_testate)) {
        $id_des = $anagrafica->getPartner($testate['id_des']);
        $form['traspo'] += $testate['traspo'];
        $form['speban'] = $testate['speban'];
        $form['expense_vat'] = $testate['expense_vat'];
        $form['stamp'] = $testate['stamp'];
        $form['vettor'] = $testate['vettor'];
        $form['imball'] = $testate['imball'];
        $form['portos'] = $testate['portos'];
        $form['spediz'] = $testate['spediz'];
        $form['pagame'] = $testate['pagame'];
        $form['caumag'] = $testate['caumag'];
        $form['destin'] = $testate['destin'];
        $form['id_des'] = $testate['id_des'];
        $form['search']['id_des'] = substr($id_des['ragso1'], 0, 10);
        $form['id_des_same_company'] = $testate['id_des_same_company'];
        $form['id_agente'] = $testate['id_agente'];
        $form['banapp'] = $testate['banapp'];
        $form['sconto'] = $testate['sconto'];
        $form['tipdoc'] = $testate['tipdoc'];
        $ctrl_testate = $testate['id_tes'];
        $rs_righi = gaz_dbi_dyn_query("*", $gTables['rigbro'], "id_tes = " . $testate['id_tes'], "id_rig asc");
        while ($rigo = gaz_dbi_fetch_array($rs_righi)) {
            $articolo = gaz_dbi_get_row($gTables['artico'], "codice", $rigo['codart']);
            $form['righi'][$_POST['num_rigo']]['id_rig'] = $rigo['id_rig'];
            $form['righi'][$_POST['num_rigo']]['tiprig'] = $rigo['tiprig'];
            $form['righi'][$_POST['num_rigo']]['id_tes'] = $rigo['id_tes'];
            $form['righi'][$_POST['num_rigo']]['tipdoc'] = $testate['tipdoc'];
            $form['righi'][$_POST['num_rigo']]['datemi'] = $testate['datemi'];
            $form['righi'][$_POST['num_rigo']]['numdoc'] = $testate['numdoc'];
            $form['righi'][$_POST['num_rigo']]['descri'] = $rigo['descri'];
            $form['righi'][$_POST['num_rigo']]['id_body_text'] = $rigo['id_body_text'];
            $form['righi'][$_POST['num_rigo']]['codart'] = $rigo['codart'];
            $form['righi'][$_POST['num_rigo']]['unimis'] = $rigo['unimis'];
            $form['righi'][$_POST['num_rigo']]['prelis'] = $rigo['prelis'];
            $form['righi'][$_POST['num_rigo']]['provvigione'] = $rigo['provvigione'];
            $form['righi'][$_POST['num_rigo']]['ritenuta'] = $rigo['ritenuta'];
            $form['righi'][$_POST['num_rigo']]['sconto'] = $rigo['sconto'];
            $form['righi'][$_POST['num_rigo']]['quanti'] = $rigo['quanti'];
			$form['righi'][$_POST['num_rigo']]['lot_or_serial'] = $articolo['lot_or_serial'];
			

            if (!isset($form['righi'][$_POST['num_rigo']]['evadibile'])) {
                $totale_evadibile = $rigo['quanti'];
                $rs_evasi = gaz_dbi_dyn_query("*", $gTables['rigdoc'], "id_order=" . $rigo['id_tes'] . " and codart='" . $rigo['codart'] . "'", "id_rig asc");
                while ($rg_evasi = gaz_dbi_fetch_array($rs_evasi)) {
                    $totale_evadibile -= $rg_evasi['quanti'];
                }
                if ($totale_evadibile == 0) {
                    $form['righi'][$_POST['num_rigo']]['checkval'] = false;
                }
                $form['righi'][$_POST['num_rigo']]['evaso_in_precedenza'] = $rigo['quanti'] - $totale_evadibile;
                $form['righi'][$_POST['num_rigo']]['evadibile'] = $totale_evadibile;
            }
            $form['righi'][$_POST['num_rigo']]['id_doc'] = $rigo['id_doc'];
            $form['righi'][$_POST['num_rigo']]['codvat'] = $rigo['codvat'];
            $form['righi'][$_POST['num_rigo']]['pervat'] = $rigo['pervat'];
            $form['righi'][$_POST['num_rigo']]['codric'] = $rigo['codric'];
            $_POST['num_rigo'] ++;
        }
    }
}

/*
 * codice per porre lo "status" a "EVASO" perchè altrimenti vengono inseriti nel ddt tutti gli ordini, anche quelli evasi
 * 
 */

function setOrdineEvaso($righi) {
    global $gTables;
//                controllo se ci sono ancora righi inevasi
    $id_tesArray = array_unique(array_column($righi, 'id_tes'));
    foreach ($id_tesArray as $id_tes) {
        $inevasi = false;
        foreach ($righi as $rigo) {
            $qtEvasa = (float) $rigo["evaso_in_precedenza"];
            if($rigo["checkval"]){  // solo se il rigo è selezionato per essere evaso
                $qtEvasa += (float) $rigo["evadibile"];
            }
            $qtOrdinata = (float) $rigo["quanti"];
            $isQuestoOrdine = ($rigo["id_tes"] == $id_tes);
            $isInevaso = ($qtOrdinata > $qtEvasa);
            if ($isQuestoOrdine && $isInevaso) {
                $inevasi = true;
                break;
            }
        }
        if (!$inevasi) {  //se non ci sono + righi da evadere
//                    modifico lo status della testata dell'ordine solo se completamente evaso
            gaz_dbi_put_row($gTables['tesbro'], "id_tes", $id_tes, "status", "EVASO");
        }
    }
}

if (!isset($_POST['ritorno'])) {
    $_POST['ritorno'] = $_SERVER['HTTP_REFERER'];
}
if (!isset($_POST['id_tes'])) { //al primo accesso  faccio le impostazioni ed il controllo di presenza ordini evadibili
    $_POST['num_rigo'] = 0;
    $form['hidden_req'] = '';
    $form['righi'] = array();
    $form['indspe'] = '';
    $form['search']['clfoco'] = '';
    $form['id_tes'] = "new";
    $form['seziva'] = 1;
    $form['datemi_D'] = date("d");
    $form['datemi_M'] = date("m");
    $form['datemi_Y'] = $anno;
    $form['initra_D'] = date("d");
    $form['initra_M'] = date("m");
    $form['initra_Y'] = $anno;
    $form['initra_I'] = date("i");
    $form['initra_H'] = date("H");
    $form['traspo'] = 0.00;
    $form['speban'] = 0.00;
    $form['expense_vat'] = 0.00;
    $form['stamp'] = 0.00;
    $form['vettor'] = "";
    $form['portos'] = "";
    $form['imball'] = "";
    $form['pagame'] = "";
    $form['destin'] = '';
    $form['id_des'] = 0;
    $form['id_des_same_company'] = 0;
    $form['caumag'] = '';
    $form['id_agente'] = 0;
    $form['banapp'] = "";
    $form['spediz'] = "";
    $form['sconto'] = 0.00;
    $form['ivaspe'] = $admin_aziend['preeminent_vat'];
    $form['listin'] = 1;
    $form['net_weight'] = 0;
    $form['gross_weight'] = 0;
    $form['units'] = 0;
    $form['volume'] = 0;
    if (isset($_GET['id_tes'])) { //se � stato richiesto un ordine specifico lo carico
        $form['id_tes'] = intval($_GET['id_tes']);
        $testate = gaz_dbi_get_row($gTables['tesbro'], "id_tes", $form['id_tes']);
        $form['clfoco'] = $testate['clfoco'];
        $anagrafica = new Anagrafica();
        $cliente = $anagrafica->getPartner($form['clfoco']);
        $id_des = $anagrafica->getPartner($testate['id_des']);
        $form['search']['clfoco'] = substr($cliente['ragso1'], 0, 10);
        $form['seziva'] = $testate['seziva'];
        $form['tipdoc'] = $testate['tipdoc'];
        $form['indspe'] = $cliente['indspe'];
        $form['traspo'] = $testate['traspo'];
        $form['speban'] = $testate['speban'];
        $form['stamp'] = $testate['stamp'];
        $form['expense_vat'] = $testate['expense_vat'];
        $form['vettor'] = $testate['vettor'];
        $form['portos'] = $testate['portos'];
        $form['imball'] = $testate['imball'];
        $form['pagame'] = $testate['pagame'];
        $form['destin'] = $testate['destin'];
        $form['id_des'] = $testate['id_des'];
        $form['search']['id_des'] = substr($id_des['ragso1'], 0, 10);
        $form['id_des_same_company'] = $testate['id_des_same_company'];
        $form['caumag'] = $testate['caumag'];
        $form['id_agente'] = $testate['id_agente'];
        $form['banapp'] = $testate['banapp'];
        $form['spediz'] = $testate['spediz'];
        $form['sconto'] = $testate['sconto'];
        $form['listin'] = $testate['listin'];
        $form['net_weight'] = $testate['net_weight'];
        $form['gross_weight'] = $testate['gross_weight'];
        $form['units'] = $testate['units'];
        $form['volume'] = $testate['volume'];
        $rs_righi = gaz_dbi_dyn_query("*", $gTables['rigbro'], "id_tes = " . $form['id_tes'], "id_rig asc");
        while ($rigo = gaz_dbi_fetch_array($rs_righi)) {
            $articolo = gaz_dbi_get_row($gTables['artico'], "codice", $rigo['codart']);
            $form['righi'][$_POST['num_rigo']]['id_rig'] = $rigo['id_rig'];
            $form['righi'][$_POST['num_rigo']]['tiprig'] = $rigo['tiprig'];
            $form['righi'][$_POST['num_rigo']]['id_tes'] = $rigo['id_tes'];
            $form['righi'][$_POST['num_rigo']]['tipdoc'] = $testate['tipdoc'];
            $form['righi'][$_POST['num_rigo']]['datemi'] = $testate['datemi'];
            $form['righi'][$_POST['num_rigo']]['numdoc'] = $testate['numdoc'];
            $form['righi'][$_POST['num_rigo']]['descri'] = $rigo['descri'];
            $form['righi'][$_POST['num_rigo']]['id_body_text'] = $rigo['id_body_text'];
            $form['righi'][$_POST['num_rigo']]['codart'] = $rigo['codart'];
            $form['righi'][$_POST['num_rigo']]['unimis'] = $rigo['unimis'];
            $form['righi'][$_POST['num_rigo']]['prelis'] = $rigo['prelis'];
            $form['righi'][$_POST['num_rigo']]['provvigione'] = $rigo['provvigione'];
            $form['righi'][$_POST['num_rigo']]['ritenuta'] = $rigo['ritenuta'];
            $form['righi'][$_POST['num_rigo']]['sconto'] = $rigo['sconto'];
            $form['righi'][$_POST['num_rigo']]['quanti'] = $rigo['quanti'];
			$form['righi'][$_POST['num_rigo']]['lot_or_serial'] = $articolo['lot_or_serial'];
			$form['righi'][$_POST['num_rigo']]['id_lotmag'] = "";
			

            // controllo la quantità già evasa sfogliando le tabelle tesdoc e rigdoc
            $totale_evadibile = $rigo['quanti'];
            $rs_evasi = gaz_dbi_dyn_query("*", $gTables['rigdoc'], "id_order = " . $form['id_tes'] . " and codart='" . $rigo['codart'] . "'", "id_rig asc");
            while ($rg_evasi = gaz_dbi_fetch_array($rs_evasi)) {
                $totale_evadibile -= $rg_evasi['quanti'];
            }
            if ($totale_evadibile == 0) {
                $form['righi'][$_POST['num_rigo']]['checkval'] = false;
            }

            $form['righi'][$_POST['num_rigo']]['evaso_in_precedenza'] = $rigo['quanti'] - $totale_evadibile;
            $form['righi'][$_POST['num_rigo']]['evadibile'] = $totale_evadibile;
            $form['righi'][$_POST['num_rigo']]['id_doc'] = $rigo['id_doc'];
            $form['righi'][$_POST['num_rigo']]['codvat'] = $rigo['codvat'];
            $form['righi'][$_POST['num_rigo']]['pervat'] = $rigo['pervat'];
            $form['righi'][$_POST['num_rigo']]['codric'] = $rigo['codric'];
            $_POST['num_rigo'] ++;
        }
    }
    if (isset($_GET['clfoco'])) { //quando viene caricato un cliente
        $form['clfoco'] = $_GET['clfoco'];
        caricaCliente($form);
    }
} else { //negli accessi successivi riporto solo il form
    $form['id_tes'] = $_POST['id_tes'];
    $form['seziva'] = $_POST['seziva'];
    $form['tipdoc'] = substr($_POST['tipdoc'], 0, 3);
    $form['datemi_Y'] = intval($_POST['datemi_Y']);
    $form['datemi_M'] = intval($_POST['datemi_M']);
    $form['datemi_D'] = intval($_POST['datemi_D']);
    $form['initra_D'] = intval($_POST['initra_D']);
    $form['initra_M'] = intval($_POST['initra_M']);
    $form['initra_Y'] = intval($_POST['initra_Y']);
    $form['initra_I'] = intval($_POST['initra_I']);
    $form['initra_H'] = intval($_POST['initra_H']);
    $form['traspo'] = number_format($_POST['traspo'], 2, '.', '');
    $form['indspe'] = $_POST['indspe'];
    $form['speban'] = $_POST['speban'];
    $form['expense_vat'] = $_POST['expense_vat'];
    $form['stamp'] = $_POST['stamp'];
    $form['vettor'] = $_POST['vettor'];
    $form['portos'] = $_POST['portos'];
    $form['imball'] = $_POST['imball'];
    $form['destin'] = $_POST['destin'];
    $form['id_des'] = substr($_POST['id_des'], 3);
    $form['id_des_same_company'] = intval($_POST['id_des_same_company']);
    $form['pagame'] = $_POST['pagame'];
    $form['caumag'] = $_POST['caumag'];
    $form['id_agente'] = $_POST['id_agente'];
    $form['banapp'] = $_POST['banapp'];
    $form['spediz'] = $_POST['spediz'];
    $form['sconto'] = $_POST['sconto'];
    $form['listin'] = $_POST['listin'];
    $form['net_weight'] = $_POST['net_weight'];
    $form['gross_weight'] = $_POST['gross_weight'];
    $form['units'] = $_POST['units'];
    $form['volume'] = $_POST['volume'];
    $form['hidden_req'] = $_POST['hidden_req'];
    foreach ($_POST['search'] as $k => $v) {
        $form['search'][$k] = $v;
    }
    if (isset($_POST['righi'])) {
        $form['righi'] = $_POST['righi'];
    }
    if ($_POST['hidden_req'] == 'clfoco') { //quando viene confermato un cliente
        if (isset($_POST['clfoco'])) {
            $form['clfoco'] = $_POST['clfoco'];
        } else {
            $form['clfoco'] = 0;
        }
        caricaCliente($form);
    }
}
if (isset($_POST['clfoco']) || isset($_GET['clfoco'])) {
    if (isset($_POST['clfoco'])) {
        $form['clfoco'] = $_POST['clfoco'];
    } else {
        $form['clfoco'] = $_GET['clfoco'];
        $_POST['clfoco'] = $_GET['clfoco'];
    }
    $anagrafica = new Anagrafica();
    $cliente = $anagrafica->getPartner($form['clfoco']);
} elseif (!isset($form['clfoco'])) {
    $form['clfoco'] = 0;
}

if (isset($_POST['ddt']) || isset($_POST['cmr'])) { //conferma dell'evasione di un ddt
    //controllo i campi
    $dataemiss = $_POST['datemi_Y'] . "-" . $_POST['datemi_M'] . "-" . $_POST['datemi_D'];
    $utsDataemiss = mktime(0, 0, 0, $_POST['datemi_M'], $_POST['datemi_D'], $_POST['datemi_Y']);
    $iniziotrasporto = $_POST['initra_Y'] . "-" . $_POST['initra_M'] . "-" . $_POST['initra_D'];
    $utsIniziotrasporto = mktime(0, 0, 0, $_POST['initra_M'], $_POST['initra_D'], $_POST['initra_Y']);
    if ($form["clfoco"] < $admin_aziend['mascli'] . '000001')
        $msg .= "0+";
    if (!isset($_POST["righi"])) {
        $msg .= "1+";
    } else {
        $inevasi = "";
        foreach ($_POST['righi'] as $k => $v) {
            if (isset($v['checkval']) and $v['id_doc'] == 0 and ( $v['tiprig'] == 0 or $v['tiprig'] == 1))
                $inevasi = "ok";
        }
        if (empty($inevasi)) {
            $msg .= "2+";
        }
    }
    if (empty($_POST["pagame"]))
        $msg .= "3+";
    if (!checkdate($_POST['datemi_M'], $_POST['datemi_D'], $_POST['datemi_Y']))
        $msg .= "4+";
    if (!checkdate($_POST['initra_M'], $_POST['initra_D'], $_POST['initra_Y']))
        $msg .= "5+";
    if ($utsIniziotrasporto < $utsDataemiss) {
        $msg .= "6+";
    }
    // controllo che la data dell'ultimo ddt emesso non sia successiva a questa
    if (isset($_POST['cmr'])) {
        $rs_lastddt = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = " . $form['datemi_Y'] . " AND ( tipdoc LIKE 'CMR' OR tipdoc = 'FAD') AND ddt_type='R' AND seziva = " . $form['seziva'], "numdoc DESC", 0, 1);
    } else {
        $rs_lastddt = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = " . $form['datemi_Y'] . " AND ( tipdoc LIKE 'DD_' OR tipdoc = 'FAD') AND ddt_type!='R' AND seziva = " . $form['seziva'], "numdoc DESC", 0, 1);
    }
    $r = gaz_dbi_fetch_array($rs_lastddt);
    if ($r) {
        $uts_last_data_emiss = gaz_format_date($r['datemi'], false, 2); // mktime
        if ($uts_last_data_emiss > $utsDataemiss) {
            $msg .= "8+";
        }
    }

    if ($msg == "") {//procedo all'inserimento
        $iniziotrasporto .= " " . $_POST['initra_H'] . ":" . $_POST['initra_I'] . ":00";
        require("lang." . $admin_aziend['lang'] . ".php");
        $script_transl = $strScript['select_evaord.php'];
        //ricavo il numero progressivo
        if (isset($_POST['cmr'])) {
            $rs_ultimo_ddt = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "datemi LIKE '" . $_POST['datemi_Y'] . "%' AND (tipdoc like 'CMR' OR tipdoc = 'FAD') AND ddt_type='R' AND seziva = " . $_POST['seziva'], "numdoc DESC", 0, 1);
        } else {
            $rs_ultimo_ddt = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "datemi LIKE '" . $_POST['datemi_Y'] . "%' AND (tipdoc like 'DD_' OR tipdoc = 'FAD') AND ddt_type!='R' AND seziva = " . $_POST['seziva'], "numdoc DESC", 0, 1);
        }
        $ultimo_ddt = gaz_dbi_fetch_array($rs_ultimo_ddt);
        // se e' il primo documento dell'anno, resetto il contatore
        if ($ultimo_ddt) {
            $form['numdoc'] = $ultimo_ddt['numdoc'] + 1;
        } else {
            $form['numdoc'] = 1;
        }
        //inserisco la testata
        if (isset($_POST['cmr'])) {
            $form['ddt_type'] = 'R';
            $form['tipdoc'] = 'CMR';
        } else {
            $form['ddt_type'] = 'T';
//            $form['tipdoc'] = 'DDT';  // tolto perchè lo prende dall'elenco a discesa
        }
        $form['template'] = "FatturaSemplice";
        $form['id_con'] = '';
        $form['status'] = 'GENERATO';
        $form['initra'] = $iniziotrasporto;
        $form['datemi'] = $dataemiss;
        tesdocInsert($form);
        //recupero l'id assegnato dall'inserimento
        $last_id = gaz_dbi_last_id();
        $ctrl_tes = 0;
        foreach ($form['righi'] as $k => $v) {
            if ($v['id_tes'] != $ctrl_tes) {  //se fa parte di un'ordine diverso dal precedente
                //inserisco un rigo descrittivo per il riferimento all'ordine sul DdT
                $row_descri['descri'] = " da " . $script_transl['doc_name'][$v['tipdoc']] . " n." . $v['numdoc'] . " del " . substr($v['datemi'], 8, 2) . "-" . substr($v['datemi'], 5, 2) . "-" . substr($v['datemi'], 0, 4);
                $row_descri['id_tes'] = $last_id;
                $row_descri['id_order'] = $v['id_tes'];
                $row_descri['tiprig'] = 2;
                rigdocInsert($row_descri);
            }
            if (isset($v['checkval'])) {   //se e' un rigo selezionato
                //lo inserisco nel DdT
                $row = $v;
                if ($v['quanti'] == $v['evadibile']) {
                    unset($row['id_rig']);
                }
                $row['id_tes'] = $last_id;
                $row['id_order'] = $v['id_tes'];
                $row['quanti'] = $v['evadibile'];
                //echo "evadibile ........................ ".$v["evadibile"]."<br>";
                rigdocInsert($row);
                $last_rigdoc_id = gaz_dbi_last_id();
                if ($v['id_body_text'] > 0) { //se è un rigo testo copio il contenuto vecchio su uno nuovo
                    $old_body_text = gaz_dbi_get_row($gTables['body_text'], "id_body", $v['id_body_text']);
                    bodytextInsert(array('table_name_ref' => 'rigdoc', 'id_ref' => $last_rigdoc_id, 'body_text' => $old_body_text['body_text']));
                    gaz_dbi_put_row($gTables['rigdoc'], 'id_rig', $last_rigdoc_id, 'id_body_text', gaz_dbi_last_id());
                }
                $articolo = gaz_dbi_get_row($gTables['artico'], "codice", $form['righi'][$k]['codart']);
				// Antonio Germani - vedo in quale id_mov verrà registrato il prossimo movimento di magazzino
					$query = "SHOW TABLE STATUS LIKE '" . $gTables['movmag'] . "'";
                    unset($row);
                    $result = gaz_dbi_query($query);
                    $row = $result->fetch_assoc();
                    $id_movmag = $row['Auto_increment'];
					
                if ($admin_aziend['conmag'] == 2 and $articolo['good_or_service'] == 0 and
                        $form['righi'][$k]['tiprig'] == 0 and ! empty($form['righi'][$k]['codart'])) { //se l'impostazione in azienda prevede l'aggiornamento automatico dei movimenti di magazzino
                    $upd_mm->uploadMag($last_rigdoc_id, $form['tipdoc'], $form['numdoc'], $form['seziva'], $dataemiss, $form['clfoco'], $form['sconto'], $form['caumag'], $v['codart'], $v['evadibile'], $v['prelis'], $v['sconto'], 0, $admin_aziend['stock_eval_method']);
                } else if ($admin_aziend['conmag'] == 2 and
                        $form['righi'][$k]['tiprig'] == 14 and ! empty($form['righi'][$k]['codart'])) {
                    $upd_mm->uploadMag($last_rigdoc_id, $form['tipdoc'], $form['numdoc'], $form['seziva'], $dataemiss, $form['clfoco'], $form['sconto'], $form['caumag'], $v['codart'], $v['evadibile'], $v['prelis'], $v['sconto'], 0, $admin_aziend['stock_eval_method']);
                }
				// Antonio Germani - inserisco id_lotmag nel movimento di magazzino appena registrato
				if (intval($v['id_lotmag']) >0){
					$query = "UPDATE " . $gTables['movmag'] . " SET id_lotmag = '" . $v['id_lotmag'] . "' WHERE id_mov ='" . $id_movmag . "'";
					gaz_dbi_query($query);
				}
				// fine inserisco id_lotmag
				
                //modifico il rigo dell'ordine indicandoci l'id della testata del DdT
                //gaz_dbi_put_row($gTables['tesdoc'], "id_tes", $last_id, "id_order", $form['id_tes'] );
            }
            if ($v['tiprig'] >= 11 && $v['tiprig'] <= 13) {
                $row = $v;
                unset($row['id_rig']);
                $row['id_tes'] = $last_id;
                rigdocInsert($row);
            }
            $ctrl_tes = $v['id_tes'];
        }
        setOrdineEvaso($form['righi']);
        $_SESSION['print_request'] = $last_id;
        header("Location: invsta_docven.php");
        exit;
    }
} elseif (isset($_POST['vco'])) { //conferma dell'evasione di un corrispettivo
    //controllo i campi
    $dataemiss = $form['datemi_Y'] . "-" . $form['datemi_M'] . "-" . $form['datemi_D'];
    $utsDataemiss = mktime(0, 0, 0, $_POST['datemi_M'], $_POST['datemi_D'], $_POST['datemi_Y']);
    $iniziotrasporto = $_POST['initra_Y'] . "-" . $_POST['initra_M'] . "-" . $_POST['initra_D'];
    $utsIniziotrasporto = mktime(0, 0, 0, $_POST['initra_M'], $_POST['initra_D'], $_POST['initra_Y']);
    $gForm = new venditForm();
    $ecr = $gForm->getECR_userData($admin_aziend["user_name"]);
    // ALLERTO SE NON E' STATA ESEGUITA LA CHIUSURA/CONTABILIZZAZIONE DEL GIORNO PRECEDENTE
    $rs_no_accounted = gaz_dbi_dyn_query("datemi", $gTables['tesdoc'], "id_con = 0 AND tipdoc = 'VCO' AND datemi < '$dataemiss' AND tipdoc = 'VCO'", 'id_tes', 0, 1);
    $no_accounted = gaz_dbi_fetch_array($rs_no_accounted);
    if ($no_accounted) {
        $msg .= "7+";
    }
    // FINE ALLERTAMENTO

    if (!isset($_POST["clfoco"]))
        $msg .= "0+";
    if (!isset($_POST["righi"])) {
        $msg .= "1+";
    } else {
        $inevasi = "";
        foreach ($_POST['righi'] as $k => $v) {
            if (isset($v['checkval']) and $v['id_doc'] == 0 and ( $v['tiprig'] == 0 or $v['tiprig'] == 1))
                $inevasi = "ok";
        }
        if (empty($inevasi)) {
            $msg .= "2+";
        }
    }
    if (empty($_POST["pagame"]))
        $msg .= "3+";
    if (!checkdate($_POST['datemi_M'], $_POST['datemi_D'], $_POST['datemi_Y']))
        $msg .= "4+";
    if (!checkdate($_POST['initra_M'], $_POST['initra_D'], $_POST['initra_Y']))
        $msg .= "5+";
    if ($utsIniziotrasporto < $utsDataemiss) {
        $msg .= "6+";
    }
    // controllo che la data dell'ultimo scontrino emesso non sia successiva a questa
    $rs_last = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = " . $form['datemi_Y'] . " AND tipdoc = 'VCO' AND seziva = " . $form['seziva'], "datemi DESC", 0, 1);
    $r = gaz_dbi_fetch_array($rs_last);
    if ($r) {
        $uts_last_data_emiss = gaz_format_date($r['datemi'], false, 2); // mktime
        if ($uts_last_data_emiss > $utsDataemiss) {
            $msg .= "9+";
        }
    }
    if ($msg == "") {//procedo all'inserimento
        require("lang." . $admin_aziend['lang'] . ".php");
        $script_transl = $strScript['select_evaord.php'];
        $ecr_user = gaz_dbi_get_row($gTables['cash_register'], 'adminid', $admin_aziend["user_name"]);
        if (!$ecr_user) {
            header("Location: error_msg.php?ref=admin_scontr");
            exit;
        };
        $iniziotrasporto .= " " . $_POST['initra_H'] . ":" . $_POST['initra_I'] . ":00";
        $form['tipdoc'] = 'VCO';
        $form['template'] = 'FatturaAllegata';
        $form['id_con'] = '';
        $form['id_contract'] = $ecr['id_cash'];
        $form['seziva'] = $ecr['seziva'];
        $form['datemi'] = $dataemiss;
        // ricavo il progressivo della cassa del giorno (in id_contract c'� la cassa alla quale invio lo scontrino)
        $rs_last_n = gaz_dbi_dyn_query("numdoc", $gTables['tesdoc'], "tipdoc = 'VCO' AND id_con = 0 AND id_contract = " . $ecr['id_cash'], 'datemi DESC, numdoc DESC', 0, 1);
        $last_n = gaz_dbi_fetch_array($rs_last_n);
        if ($last_n) {
            $form['numdoc'] = $last_n['numdoc'] + 1;
        } else {
            $form['numdoc'] = 1;
        }
        if ($form['clfoco'] > 100000000) {  // cliente selezionato quindi fattura allegata
            // ricavo l'ultimo numero di fattura dell'anno
            $rs_last_f = gaz_dbi_dyn_query("numfat*1 AS fattura", $gTables['tesdoc'], "YEAR(datfat) = " . $form['datemi_Y'] . " AND tipdoc = 'VCO' AND seziva = " . $ecr['seziva'], 'fattura DESC', 0, 1);
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
        $ctrl_tes = 0;
        foreach ($form['righi'] as $k => $v) {
            if ($v['id_tes'] != $ctrl_tes) {  //se fa parte di un'ordine diverso dal precedente
                //inserisco un rigo descrittivo per il riferimento all'ordine sul corrispettivo
                $row_descri['descri'] = $script_transl['doc_name'][$v['tipdoc']] . " " . substr($v['datemi'], 8, 2) . "-" . substr($v['datemi'], 5, 2) . "-" . substr($v['datemi'], 0, 4);
                $row_descri['id_tes'] = $last_id;
                $row_descri['id_order'] = $v['id_tes'];
                $row_descri['tiprig'] = 2;
                rigdocInsert($row_descri);
                $row_descri['descri'] = "N." . $v['numdoc'];
                $row_descri['id_tes'] = $last_id;
                $row_descri['id_order'] = $v['id_tes'];
                $row_descri['tiprig'] = 2;
                rigdocInsert($row_descri);
            }
            if (isset($v['checkval'])) {   //se e' un rigo selezionato
                //lo inserisco nel VCO
                $row = $v;
                if ($v['quanti'] == $v['evadibile']) {
                    unset($row['id_rig']);
                }
                $row['id_tes'] = $last_id;
                $row['id_order'] = $v['id_tes'];
                $row['quanti'] = $v['evadibile'];
                rigdocInsert($row);
                $last_rigdoc_id = gaz_dbi_last_id();
                $articolo = gaz_dbi_get_row($gTables['artico'], "codice", $form['righi'][$k]['codart']);
				// Antonio Germani - vedo in quale id_mov verrà registrato il prossimo movimento di magazzino
					$query = "SHOW TABLE STATUS LIKE '" . $gTables['movmag'] . "'";
                    unset($row);
                    $result = gaz_dbi_query($query);
                    $row = $result->fetch_assoc();
                    $id_movmag = $row['Auto_increment'];
					
                if ($admin_aziend['conmag'] == 2 and $articolo['good_or_service'] == 0 and
                        $form['righi'][$k]['tiprig'] == 0 and ! empty($form['righi'][$k]['codart'])) { //se l'impostazione in azienda prevede l'aggiornamento automatico dei movimenti di magazzino
                    $upd_mm->uploadMag($last_rigdoc_id, $form['tipdoc'], $form['numdoc'], $form['seziva'], $dataemiss, $form['clfoco'], $form['sconto'], $form['caumag'], $v['codart'], $v['evadibile'], $v['prelis'], $v['sconto'], 0, $admin_aziend['stock_eval_method']
                    );
                } else if ($admin_aziend['conmag'] == 2 and
                        $form['righi'][$k]['tiprig'] == 14 and ! empty($form['righi'][$k]['codart'])) {
                    $upd_mm->uploadMag($last_rigdoc_id, $form['tipdoc'], $form['numdoc'], $form['seziva'], $dataemiss, $form['clfoco'], $form['sconto'], $form['caumag'], $v['codart'], $v['evadibile'], $v['prelis'], $v['sconto'], 0, $admin_aziend['stock_eval_method']);
                }
				// Antonio Germani - inserisco id_lotmag nel movimento di magazzino appena registrato
				if (intval($v['id_lotmag']) >0){
					$query = "UPDATE " . $gTables['movmag'] . " SET id_lotmag = '" . $v['id_lotmag'] . "' WHERE id_mov ='" . $id_movmag . "'";
					gaz_dbi_query($query);
				}
				// fine inserisco id_lotmag
				
                //modifico il rigo dell'ordine indicandoci l'id della testata del VCO
                //gaz_dbi_put_row($gTables['rigbro'], "id_rig", $v['id_rig'], "id_doc", $last_id);
                //gaz_dbi_put_row($gTables['tesdoc'], "id_tes", $last_id, "id_order", $form['id_tes'] );
            }
            /* non serve più
              if ($ctrl_tes != 0 and $ctrl_tes != $v['id_tes']) {  //se non � il primo rigo processato
              //controllo se ci sono ancora righi inevasi
              $rs_righi_inevasi = gaz_dbi_dyn_query("id_tes", $gTables['rigbro'], "id_tes = $ctrl_tes AND id_doc = 0 AND tiprig BETWEEN 0 AND 1 or tiprig=14", "id_rig", 0, 1);
              $inevasi = gaz_dbi_fetch_array($rs_righi_inevasi);
              if (!$inevasi) {  //se non ci sono + righi da evadere
              //modifico lo status della testata dell'ordine solo se completamente evaso
              gaz_dbi_put_row($gTables['tesbro'], "id_tes", $ctrl_tes, "status", "EVASO");
              }
              }
             */
            $ctrl_tes = $v['id_tes'];
        }
        /* non serve più
          //controllo se l'ultimo ordine tra quelli processati ha ancora righi inevasi
          $rs_righi_inevasi = gaz_dbi_dyn_query("id_tes", $gTables['rigbro'], "id_tes = $ctrl_tes AND id_doc = 0 AND tiprig BETWEEN 0 AND 1 or tiprig=14", "id_rig", 0, 1);
          $inevasi = gaz_dbi_fetch_array($rs_righi_inevasi);
          if (!$inevasi) {  //se non ci sono + righi da evadere
          //modifico lo status della testata dell'ordine solo se completamente evaso
          gaz_dbi_put_row($gTables['tesbro'], "id_tes", $ctrl_tes, "status", "EVASO");
          }
         */
        setOrdineEvaso($form['righi']);

        // INIZIO l'invio dello scontrino alla stampante fiscale dell'utente
        require("../../library/cash_register/" . $ecr['driver'] . ".php");
        $ticket_printer = new $ecr['driver'];
        $ticket_printer->set_serial($ecr['serial_port']);
        $ticket_printer->open_ticket();
        $ticket_printer->set_cashier($admin_aziend['Nome']);
        $tot = 0;
        foreach ($form['righi'] as $i => $v) {
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
                $tot += $tot_row;
            } else {                    // se descrittivo
                $desc_arr = str_split(trim($v['descri']), 24);
                foreach ($desc_arr as $d_v) {
                    $ticket_printer->descri_ticket($d_v);
                }
            }
        }
        if (!empty($form['fiscal_code'])) { // � stata impostata la stampa del codice fiscale
            $ticket_printer->descri_ticket('CF= ' . $form['fiscal_code']);
        }
        $ticket_printer->pay_ticket();
        $ticket_printer->close_ticket();
        // FINE invio
        if ($form['clfoco'] > 100000000) {
            // procedo alla stampa della fattura solo se c'� un cliente selezionato
            $_SESSION['print_request'] = $last_id;
            header("Location: invsta_docven.php");
            exit;
        } else {
            header("Location: report_scontr.php");
            exit;
        }
        $_SESSION['print_request'] = $last_id;
        header("Location: invsta_docven.php");
        exit;
    }
} elseif (isset($_POST['fai'])) { //conferma dell'evasione di una fattura immediata
    //cerco l'ultimo template
    $rs_ultimo_template = gaz_dbi_dyn_query("template", $gTables['tesdoc'], "tipdoc = 'FAI' and seziva = " . $form['seziva'], "datfat DESC, protoc DESC", 0, 1);
    $ultimo_template = gaz_dbi_fetch_array($rs_ultimo_template);
    if ($ultimo_template['template'] == 'FatturaImmediata') {
        $form['template'] = "FatturaImmediata";
    } else {
        $form['template'] = "FatturaSemplice";
    }
    //controllo i campi
    $dataemiss = $form['datemi_Y'] . "-" . $form['datemi_M'] . "-" . $form['datemi_D'];
    $utsDataemiss = mktime(0, 0, 0, $form['datemi_M'], $form['datemi_D'], $form['datemi_Y']);
    $iniziotrasporto = $form['initra_Y'] . "-" . $form['initra_M'] . "-" . $form['initra_D'];
    $utsIniziotrasporto = mktime(0, 0, 0, $form['initra_M'], $form['initra_D'], $form['initra_Y']);
    if ($form["clfoco"] < $admin_aziend['mascli'] . '000001')
        $msg .= "0+";
    if (!isset($form["righi"])) {
        $msg .= "1+";
    } else {
        $inevasi = "";
        foreach ($form['righi'] as $k => $v) {
            if (isset($v['checkval']) and $v['id_doc'] == 0 and ( $v['tiprig'] == 0 or $v['tiprig'] == 1))
                $inevasi = "ok";
        }
        if (empty($inevasi)) {
            $msg .= "2+";
        }
    }
    if (empty($form["pagame"]))
        $msg .= "3+";
    if (!checkdate($form['datemi_M'], $form['datemi_D'], $form['datemi_Y']))
        $msg .= "4+";
    if (!checkdate($form['initra_M'], $form['initra_D'], $form['initra_Y']))
        $msg .= "5+";
    if ($utsIniziotrasporto < $utsDataemiss) {
        $msg .= "6+";
    }
    // controllo che la data dell'ultima fattura emessa non sia successiva a questa
    $rs_last = gaz_dbi_dyn_query("*", $gTables['tesdoc'], "YEAR(datemi) = " . $form['datemi_Y'] . " AND tipdoc LIKE 'F__'  AND seziva = " . $form['seziva'], "protoc DESC", 0, 1);
    $r = gaz_dbi_fetch_array($rs_last);
    if ($r) {
        $uts_last_data_emiss = gaz_format_date($r['datfat'], false, 2); // mktime
        if ($uts_last_data_emiss > $utsDataemiss) {
            $msg .= "10+";
        }
    }
    if ($msg == "") {//procedo all'inserimento
        require("lang." . $admin_aziend['lang'] . ".php");
        $script_transl = $strScript['select_evaord.php'];
        $iniziotrasporto .= " " . $form['initra_H'] . ":" . $form['initra_I'] . ":00";
        //ricavo il progressivo del numero fattura
        $rs_ultima_fat = gaz_dbi_dyn_query("numfat*1 AS documento", $gTables['tesdoc'], "YEAR(datemi) = " . $form['datemi_Y'] . " AND tipdoc LIKE 'FA_' AND seziva = " . $form['seziva'], "documento DESC", 0, 1);
        $ultima_fat = gaz_dbi_fetch_array($rs_ultima_fat);
        // se e' il primo documento dell'anno, resetto il contatore
        if ($ultima_fat) {
            $form['numdoc'] = $ultima_fat['documento'] + 1;
            $form['numfat'] = $form['numdoc'];
        } else {
            $form['numdoc'] = 1;
            $form['numfat'] = 1;
        }
        //ricavo il progressivo protocollo
        $rs_ultimo_pro = gaz_dbi_dyn_query("protoc", $gTables['tesdoc'], "YEAR(datemi) = " . $form['datemi_Y'] . " AND tipdoc LIKE 'F__' and seziva = " . $form['seziva'], "protoc DESC", 0, 1);
        $ultimo_pro = gaz_dbi_fetch_array($rs_ultimo_pro);
        // se e' il primo documento dell'anno, resetto il contatore
        if ($ultimo_pro) {
            $form['protoc'] = $ultimo_pro['protoc'] + 1;
        } else {
            $form['protoc'] = 1;
        }
        //inserisco la testata
        $form['tipdoc'] = 'FAI';
        $form['id_con'] = '';
        $form['status'] = 'GENERATO';
        $form['initra'] = $iniziotrasporto;
        $form['datemi'] = $dataemiss;
        $form['datfat'] = $dataemiss;
        tesdocInsert($form);
        //recupero l'id assegnato dall'inserimento
        $last_id = gaz_dbi_last_id();
        $ctrl_tes = 0;
        foreach ($form['righi'] as $k => $v) {
            if ($v['id_tes'] != $ctrl_tes) {  //se fa parte di un'ordine diverso dal precedente
                //inserisco un rigo descrittivo per il riferimento all'ordine sulla fattura immediata
                $row_descri['descri'] = "da " . $script_transl['doc_name'][$v['tipdoc']] . " n." . $v['numdoc'] . " del " . substr($v['datemi'], 8, 2) . "-" . substr($v['datemi'], 5, 2) . "-" . substr($v['datemi'], 0, 4);
                $row_descri['id_tes'] = $last_id;
                $row_descri['id_order'] = $v['id_tes'];
                $row_descri['tiprig'] = 2;
                rigdocInsert($row_descri);
            }
            if (isset($v['checkval'])) {   //se e' un rigo selezionato
                //lo inserisco nella fattura immediata
                $row = $v;
                if ($v['quanti'] == $v['evadibile']) {
                    unset($row['id_rig']);
                }
				
				// Antonio Germani - se c'è un lotto ne accodo numero e scadenza alla descrizione articolo
				if (intval ($form['righi'][$k]['id_lotmag'])>0){
					if (intval ($form['righi'][$k]['expiry'])<=0){
						$form['righi'][$k]['expiry']="";
					}
					$form['righi'][$k]['descri'] = $form['righi'][$k]['descri'] . " - Lot: " . $form['righi'][$k]['identifier'] . " " . $form['righi'][$k]['expiry'];
				}
				// fine accodo lotto
				
                $row['id_tes'] = $last_id;
                $row['id_order'] = $v['id_tes'];
                $row['quanti'] = $v['evadibile'];
                rigdocInsert($row);
                $last_rigdoc_id = gaz_dbi_last_id();
                if ($v['id_body_text'] > 0) { //se è un rigo testo copio il contenuto vecchio su uno nuovo
                    $old_body_text = gaz_dbi_get_row($gTables['body_text'], "id_body", $v['id_body_text']);
                    bodytextInsert(array('table_name_ref' => 'rigdoc', 'id_ref' => $last_rigdoc_id, 'body_text' => $old_body_text['body_text']));
                    gaz_dbi_put_row($gTables['rigdoc'], 'id_rig', $last_rigdoc_id, 'id_body_text', gaz_dbi_last_id());
                }
                $articolo = gaz_dbi_get_row($gTables['artico'], "codice", $form['righi'][$k]['codart']);
				// Antonio Germani - vedo in quale id_mov verrà registrato il prossimo movimento di magazzino
					$query = "SHOW TABLE STATUS LIKE '" . $gTables['movmag'] . "'";
                    unset($row);
                    $result = gaz_dbi_query($query);
                    $row = $result->fetch_assoc();
                    $id_movmag = $row['Auto_increment'];
					
                if ($admin_aziend['conmag'] == 2 and $articolo['good_or_service'] == 0 and
                        $form['righi'][$k]['tiprig'] == 0 and ! empty($form['righi'][$k]['codart'])) { //se l'impostazione in azienda prevede l'aggiornamento automatico dei movimenti di magazzino
                    $upd_mm->uploadMag($last_rigdoc_id, $form['tipdoc'], $form['numdoc'], $form['seziva'], $dataemiss, $form['clfoco'], $form['sconto'], $form['caumag'], $v['codart'], $v['quanti'], $v['prelis'], $v['sconto'], 0, $admin_aziend['stock_eval_method']
                    );
                } else if ($admin_aziend['conmag'] == 2 and
                        $form['righi'][$k]['tiprig'] == 14 and ! empty($form['righi'][$k]['codart'])) {
                    $upd_mm->uploadMag($last_rigdoc_id, $form['tipdoc'], $form['numdoc'], $form['seziva'], $dataemiss, $form['clfoco'], $form['sconto'], $form['caumag'], $v['codart'], $v['quanti'], $v['prelis'], $v['sconto'], 0, $admin_aziend['stock_eval_method']);
                }
				// Antonio Germani - inserisco id_lotmag nel movimento di magazzino appena registrato
				if (intval($v['id_lotmag']) >0){
					$query = "UPDATE " . $gTables['movmag'] . " SET id_lotmag = '" . $v['id_lotmag'] . "' WHERE id_mov ='" . $id_movmag . "'";
					gaz_dbi_query($query);
				}
				// fine inserisco id_lotmag
				
                //modifico il rigo dell'ordine indicandoci l'id della testata della fattura immediata
                //gaz_dbi_put_row($gTables['tesdoc'], "id_tes", $last_id, "id_order", $form['id_tes'] );
            }
            if ($ctrl_tes != 0 and $ctrl_tes != $v['id_tes']) {  //se non è il primo rigo processato
                //controllo se ci sono ancora righi inevasi
                /* $rs_righi_inevasi = gaz_dbi_dyn_query("id_tes", $gTables['rigbro'], "id_tes = $ctrl_tes AND id_doc = 0 AND tiprig BETWEEN 0 AND 1 or tiprig=14", "id_rig", 0, 1);
                  $inevasi = gaz_dbi_fetch_array($rs_righi_inevasi);
                  if (!$inevasi) {  //se non ci sono + righi da evadere
                  //modifico lo status della testata dell'ordine solo se completamente evaso
                  gaz_dbi_put_row($gTables['tesbro'], "id_tes", $ctrl_tes, "status", "EVASO");
                  } */
            }
            if ($v['tiprig'] >= 11 && $v['tiprig'] <= 13) {
                $row = $v;
                unset($row['id_rig']);
                $row['id_tes'] = $last_id;
                rigdocInsert($row);
            }
            $ctrl_tes = $v['id_tes'];
        }
        //controllo se l'ultimo ordine tra quelli processati ha ancora righi inevasi
        /* $rs_righi_inevasi = gaz_dbi_dyn_query("id_tes", $gTables['rigbro'], "id_tes = $ctrl_tes AND id_doc = 0 AND tiprig BETWEEN 0 AND 1 or tiprig=14", "id_rig", 0, 1);
          $inevasi = "";
          $inevasi = gaz_dbi_fetch_array($rs_righi_inevasi);
          if (!$inevasi) {  //se non ci sono + righi da evadere
          //modifico lo status della testata dell'ordine solo se completamente evaso
          gaz_dbi_put_row($gTables['tesbro'], "id_tes", $ctrl_tes, "status", "EVASO");
          } */
        $_SESSION['print_request'] = $last_id;
        header("Location: invsta_docven.php");
        exit;
    }
} elseif (isset($_POST['Return'])) {  //ritorno indietro
    header("Location: " . $_POST['ritorno']);
    exit;
}



// **************************************************************************************************************************
//
//  Visualizzazione maschera di evasione ordine di vendita
//
// **************************************************************************************************************************



require("../../library/include/header.php");
$script_transl = HeadMain(0, array('calendarpopup/CalendarPopup', 'custom/autocomplete'));
?>
<script type="text/javascript">
    function pulldown_menu(selectName, destField)
    {
        // Create a variable url to contain the value of the
        // selected option from the the form named broven and variable selectName
        var url = document.myform[selectName].options[document.myform[selectName].selectedIndex].value;
        document.myform[destField].value = url;
    }

    function calcheck(checkin)
    {
        with (checkin.form) {
            if (checkin.checked == false) {
                hiddentot.value = eval(hiddentot.value) - eval(checkin.value);
            } else {
                hiddentot.value = eval(hiddentot.value) + eval(checkin.value);
            }
            var totalecheck = eval(hiddentot.value) - eval(hiddentot.value) * eval(sconto.value) / 100 + eval(traspo.value);
            return((Math.round(totalecheck * 100) / 100).toFixed(2));
        }
    }

    function summa(sumtraspo)
    {
        if (isNaN(parseFloat(eval(sumtraspo.value)))) {
            sumtraspo.value = 0.00;
        }
        var totalecheck = eval(document.myform.hiddentot.value) - eval(document.myform.hiddentot.value) * eval(document.myform.sconto.value) / 100 + eval(sumtraspo.value);
        return((Math.round(totalecheck * 100) / 100).toFixed(2));
    }

    function sconta(percsconto)
    {
        if (isNaN(parseFloat(eval(percsconto.value)))) {
            percsconto.value = 0.00;
        }
        var totalecheck = eval(document.myform.hiddentot.value) - eval(document.myform.hiddentot.value) * eval(percsconto.value) / 100 + eval(document.myform.traspo.value);
        return((Math.round(totalecheck * 100) / 100).toFixed(2));
    }

</script>
<script type="text/javascript" id="datapopup">
    var cal = new CalendarPopup();
    cal.setReturnFunction("setMultipleValues");
    function setMultipleValues(y, m, d) {
        document.getElementById(calName + '_Y').value = y;
        document.getElementById(calName + '_M').selectedIndex = m * 1 - 1;
        document.getElementById(calName + '_D').selectedIndex = d * 1 - 1;
    }
    function setDate(name) {
        calName = name.toString();
        var year = document.getElementById(calName + '_Y').value.toString();
        var month = document.getElementById(calName + '_M').value.toString();
        var day = document.getElementById(calName + '_D').value.toString();
        var mdy = month + '/' + day + '/' + year;
        cal.setReturnFunction('setMultipleValues');
        cal.showCalendar('anchor', mdy);
    }
</script>
<form method="POST" name="myform">
    <?php
    $gForm = new venditForm();
    $alert_sezione = '';
    switch ($admin_aziend['fatimm']) {
        case 1:
        case 2:
        case 3:
            if ($admin_aziend['fatimm'] != $form['seziva'])
                $alert_sezione = $script_transl['alert1'];
            break;
        case "U":
            $alert_sezione = $script_transl['alert1'];
            break;
    }
    echo "<input type=\"hidden\" value=\"" . $form['hidden_req'] . "\" name=\"hidden_req\" />\n";
    ?>
    <input type="hidden" name="ritorno" value="<?php echo $_POST['ritorno']; ?>">
    <input type="hidden" name="id_tes" value="<?php echo $form['id_tes']; ?>">
    <input type="hidden" name="tipdoc" value="<?php echo $form['tipdoc']; ?>">
    <input type="hidden" name="speban" value="<?php echo $form['speban']; ?>">
    <input type="hidden" name="expense_vat" value="<?php echo $form['expense_vat']; ?>">
    <input type="hidden" name="stamp" value="<?php echo $form['stamp']; ?>">
    <input type="hidden" name="listin" value="<?php echo $form['listin']; ?>">
    <!--   adesso sono modificabili
        <input type="hidden" name="net_weight" value="<?php echo $form['net_weight']; ?>">
        <input type="hidden" name="gross_weight" value="<?php echo $form['gross_weight']; ?>">
        <input type="hidden" name="units" value="<?php echo $form['units']; ?>">
    -->
    <input type="hidden" name="volume" value="<?php echo $form['volume']; ?>">
    <input type="hidden" name="id_agente" value="<?php echo $form['id_agente']; ?>">
    <input type="hidden" name="caumag" value="<?php echo $form['caumag']; ?>">
    <input type="hidden" name="indspe" value="<?php echo $form['indspe']; ?>'">

    <div align="center" class="FacetFormHeaderFont"><?php echo $script_transl['title']; ?>
        <?php
        $select_cliente = new selectPartner('clfoco');
        $select_cliente->selectDocPartner('clfoco', $form['clfoco'], $form['search']['clfoco'], 'clfoco', $script_transl['search_customer'], $admin_aziend['mascli'], $admin_aziend['mascli']);
        ?>
    </div>
    <table class="Tlarge table table-striped table-bordered table-condensed table-responsive">
        <?php
        echo "<tr>\n";
        echo "<td class=\"FacetFieldCaptionTD\">" . $script_transl['seziva'] . "</td><td class=\"FacetDataTD\" >\n";
        $gForm->selectNumber('seziva', $form['seziva'], 0, 1, 9, 'FacetDataTD', true);
        echo "\t </td>\n";
        if (!empty($msg)) {
            echo '<td colspan="2" class="FacetDataTDred">' . $gForm->outputErrors($msg, $script_transl['errors']) . "</td>\n";
        } else {
            echo "<td class=\"FacetFieldCaptionTD\">" . $script_transl['indspe'] . "</td>";
            echo "\t<td class=\"FacetDataTD\">" . $form['indspe'] . "</td>\n";
        }
        echo "\t<td class=\"FacetFieldCaptionTD\">" . $script_transl['datemi'] . "</td>\n";
        echo "\t<td class=\"FacetDataTD\">\n";
        $gForm->CalendarPopup('datemi', $form['datemi_D'], $form['datemi_M'], $form['datemi_Y']);
        echo "\t </td></tr> <tr>\n";
        echo '<td class="FacetFieldCaptionTD">' . $script_transl['banapp'] . "</td>\n";
        echo '<td colspan="3" class="FacetDataTD">';
        $select_banapp = new selectbanapp("banapp");
        $select_banapp->addSelected($form["banapp"]);
        $select_banapp->output();
        echo "</td>\n";
        echo "\t<td class=\"FacetFieldCaptionTD\" colspan=\"2\">" . $script_transl['initra'] . "\n";
        $gForm->CalendarPopup('initra', $form['initra_D'], $form['initra_M'], $form['initra_Y']);
// select dell'ora
        echo "\t <select name=\"initra_H\" class=\"FacetText\" >\n";
        for ($counter = 0; $counter <= 23; $counter++) {
            $selected = "";
            if ($counter == $form['initra_H'])
                $selected = "selected";
            echo "\t\t <option value=\"" . sprintf('%02d', $counter) . "\" $selected >" . sprintf('%02d', $counter) . "</option>\n";
        }
        echo "\t </select>\n ";
// select dell'ora
        echo "\t <select name=\"initra_I\" class=\"FacetText\" >\n";
        for ($counter = 0; $counter <= 59; $counter++) {
            $selected = "";
            if ($counter == $form['initra_I'])
                $selected = "selected";
            echo "\t\t <option value=\"" . sprintf('%02d', $counter) . "\" $selected >" . sprintf('%02d', $counter) . "</option>\n";
        }
        echo "\t </select>\n";
        echo "</td></tr><tr>\n";
        echo "\t<td class=\"FacetFieldCaptionTD\">" . $script_transl['traspo'] . ' ' . $admin_aziend['html_symbol'] . "</td>\n";
        echo "\t<td class=\"FacetDataTD\"><input type=\"text\" name=\"traspo\" value=\"" . $form['traspo'] . "\" align=\"right\" maxlength=\"6\" size=\"3\" onChange=\"this.form.total.value=summa(this);\" />\n";
        echo "\t </td>\n";
        echo "\t<td class=\"FacetFieldCaptionTD\">" . $script_transl['pagame'] . "</td><td  class=\"FacetDataTD\">\n";
        $gForm->selectFromDB('pagame', 'pagame', 'codice', $form['pagame'], 'codice', 1, ' ', 'descri');
        echo "\t </td>\n";
        echo '<td class="FacetFieldCaptionTD">' . $script_transl['destin'] . "</td>\n";
        if ($form['id_des_same_company'] > 0) { //  è una destinazione legata all'anagrafica
            echo "<td class=\"FacetDataTD\">\n";
            $gForm->selectFromDB('destina', 'id_des_same_company', 'codice', $form['id_des_same_company'], 'codice', true, '-', 'unita_locale1', '', 'FacetSelect', null, '', "id_anagra = '" . $cliente['id_anagra'] . "'");
            echo "	<input type=\"hidden\" name=\"id_des\" value=\"" . $form['id_des'] . "\">
                <input type=\"hidden\" name=\"destin\" value=\"" . $form['destin'] . "\" /></td>\n";
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
        echo "</tr><tr>\n";
        echo "<td class=\"FacetFieldCaptionTD\">" . $script_transl['id_agente'] . "</td>";
        echo "<td class=\"FacetDataTD\">\n";
        $select_agente = new selectAgente("id_agente");
        $select_agente->addSelected($form["id_agente"]);
        $select_agente->output();
        echo "</td>\n";
        echo "<td class=\"FacetFieldCaptionTD\">" . $script_transl['spediz'] . "</td>\n";
        echo "<td class=\"FacetDataTD\"><input type=\"text\" name=\"spediz\" value=\"" . $form["spediz"] . "\" maxlength=\"50\" size=\"25\" class=\"FacetInput\">\n";
        $select_spediz = new SelectValue("spedizione");
        $select_spediz->output('spediz', 'spediz');
        echo "</td>\n";
        echo "<td class=\"FacetFieldCaptionTD\">" . $script_transl['portos'] . "</td>\n";
        echo "<td class=\"FacetDataTD\"><input type=\"text\" name=\"portos\" value=\"" . $form["portos"] . "\" maxlength=\"50\" size=\"25\" class=\"FacetInput\">\n";
        $select_spediz = new SelectValue("portoresa");
        $select_spediz->output('portos', 'portos');
        echo "</td>\n";
        echo "</td></tr>\n";
        echo '<tr><td class="FacetFieldCaptionTD">';
        echo "%" . $script_transl['sconto'] . ":</td><td class=\"FacetDataTD\"><input type=\"text\" value=\"" . $form['sconto'] . "\" maxlength=\"4\" size=\"1\" name=\"sconto\" onChange=\"this.form.total.value=sconta(this);\">";
        echo "<td class=\"FacetFieldCaptionTD\">" . $script_transl['imball'] . "</td>\n";
        echo "<td class=\"FacetDataTD\"><input type=\"text\" name=\"imball\" value=\"" . $form["imball"] . "\" maxlength=\"50\" size=\"25\" class=\"FacetInput\">\n";
        $select_spediz = new SelectValue("imballo");
        $select_spediz->output('imball', 'imball');
        echo "</td>\n";
        echo "<td class=\"FacetFieldCaptionTD\">" . $script_transl['vettor'] . "</td>\n";
        echo "<td class=\"FacetDataTD\">\n";
        $select_vettor = new selectvettor("vettor");
        $select_vettor->addSelected($form["vettor"]);
        $select_vettor->output();
        echo "</td>\n";
        echo "</tr><tr>
			<td class=\"FacetFieldCaptionTD\">$script_transl[0]</td>
			<td class=\"FacetDataTD\">
				<input type=\"text\" value=\"" . $form['net_weight'] . "\" name=\"net_weight\" maxlength=\"9\" size=\"5\" />
			</td>
			<td class=\"FacetFieldCaptionTD\">$script_transl[1]</td>
			<td class=\"FacetDataTD\">
				<input type=\"text\" value=\"" . $form['gross_weight'] . "\" name=\"gross_weight\" maxlength=\"9\" size=\"5\" />
			</td>
			<td class=\"FacetFieldCaptionTD\">$script_transl[2]</td>
			<td class=\"FacetDataTD\">
				<input type=\"text\" value=\"" . $form['units'] . "\" name=\"units\" maxlength=\"6\" size=\"4\" />
			</td></tr>\n";
        $tidoc_selectable = array("DDT" => "D.d.T. di Vendita", "DDY" => "D.d.T. da non fatturare automaticamente");
        echo "<tr><td class=\"FacetFieldCaptionTD\">" . "Tipo documento" . "</td><td class=\"FacetDataTD\">";
        $gForm->variousSelect('tipdoc', $tidoc_selectable, $form['tipdoc'], 'FacetFormHeaderFont', true, 'tipdoc');
        echo"</td></tr></table>";
        if (!empty($form['righi'])) {
            echo '<div align="center"><b>' . $script_transl['preview_title'] . '</b></div>';
            echo "<table class=\"Tlarge table table-striped table-bordered table-condensed table-responsive\">";
            echo "<tr class=\"FacetFieldCaptionTD\"><th> " . $script_transl['codart'] . "</th>
   <th> " . $script_transl['descri'] . "</th>
   <th align=\"center\"> " . $script_transl['unimis'] . "</th>
   <th align=\"right\"> " . $script_transl['quanti'] . " richiesta</th>
   <th align=\"right\"> " . $script_transl['quanti'] . " evadibile</th>
   <th align=\"right\"> " . $script_transl['prezzo'] . "</th>
   <th align=\"right\"> " . $script_transl['sconto'] . "</th>
   <th align=\"right\"> " . $script_transl['provvigione'] . "</th>
   <th align=\"right\"> " . $script_transl['amount'] . "</th>
   <th></th>
   </tr>";
            $ctrl_tes = 0;
            $total_order = 0;
            $hRowFlds = '';
            foreach ($form['righi'] as $k => $v) {
                //echo $form['righi'][$k]['tiprig']."<br>";
                $checkin = ' disabled ';
                $imprig = 0;
                $v['descri'] = htmlentities($v['descri']);
                //calcolo importo rigo
                switch ($v['tiprig']) {
                    case "0":
                        $imprig = CalcolaImportoRigo($form['righi'][$k]['evadibile'], $form['righi'][$k]['prelis'], $form['righi'][$k]['sconto']);
                        if ($v['id_doc'] == 0) {
//                            $checkin = ' checked';
                            $checkin = ($form['righi'][$k]['evadibile']) > 0 ? ' checked' : '';    // seleziono solo se la qt evadibile è positiva
                            $total_order += $imprig;
                        }
                        break;
                    case "1":
                        $imprig = CalcolaImportoRigo(1, $form['righi'][$k]['prelis'], 0);
                        if ($v['id_doc'] == 0) {
//                            $checkin = ' checked';
                            $checkin = ($form['righi'][$k]['evadibile']) > 0 ? ' checked' : '';    // seleziono solo se la qt evadibile è positiva
                            $total_order += $imprig;
                        }
                        break;
                    case "2":
                        $checkin = '';
                        break;
                    case "3":
                        $checkin = '';
                        break;
                    case "6":
                        $body_text = gaz_dbi_get_row($gTables['body_text'], 'id_body', $v['id_body_text']);
                        $v['descri'] = htmlentities(substr(strip_tags($body_text['body_text']), 0, 80)) . ' ...';
                        $checkin = '';
                        break;
                    case "11":
                    case "12":
                    case "13":
                        $checkin = ' ';
                        break;
                    case "14":
                        $checkin = ' checked';
                        break;
                }
                if ($ctrl_tes != $v['id_tes']) {
                    echo "<tr><td class=\"FacetDataTD\" colspan=\"9\"> " . $script_transl['from'] . " <a href=\"admin_broven.php?Update&id_tes=" . $v["id_tes"] . "\" title=\"" . $script_transl['upd_ord'] . "\"> " . $script_transl['doc_name'][$v['tipdoc']] . " n." . $v['numdoc'] . "</a> " . $script_transl['del'] . ' ' . gaz_format_date($v['datemi']) . " </td></tr>";
                }

                if (empty($checkin) || $checkin == ' disabled ') {
                    echo '<tr style="color: LightGray">';
                } else {
                    echo "<tr>";
                }
                // form hidden fields holding actual row values
                $fields = array('id_tes', 'datemi', 'tipdoc', 'numdoc',
                    'id_rig', 'tiprig', 'id_doc', 'id_body_text',
                    'codvat', 'pervat', 'ritenuta', 'codric',
                    'codart', 'descri'
                );

                echo "<td>" . $v['codart'] . "</td>\n";
                echo "<td>" . $v['descri'];
				
				// Antonio Germani - inizio gestione lotti
				echo "<input type=\"hidden\" value=\"" . $v['lot_or_serial'] . "\" name=\"righi[$k][lot_or_serial]\">\n";
							
				if ($v['lot_or_serial'] > 0) { // se l'articolo prevede lotti apro gestione lotti
					
					$lm->getAvailableLots($v['codart']);
					$ld = $lm->divideLots($v['evadibile']); // divido i lotti in base alla q.tà evadibile
					$l = 0;
					// calcolo delle giacenze per ogni singolo lotto
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
					if ($ld > 0 and $toDo!="update") { // segnalo preventivamente l'errore
						?>
						<div class="alert alert-warning alert-dismissible">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>Warning!</strong> <b>Quantità lotto non sufficiente!</b> </br>Se si conferma si creeranno incongruenze fra quantità e lotti! </br> Si consiglia di selezionare un lotto con sufficiente disponibilità</br> oppure di diminuire la quantità in uscita.
						</div>
						<?php				
					}
					if (isset($v['id_lotmag']) && $v['id_lotmag'] > 0) { // Selezione manuale del lotto dopo quella iniziale
						
						$selected_lot = $lm->getLot($v['id_lotmag']);
						echo '<div><button class="btn btn-xs btn-success" title="Lotto selezionato. Cliccare per cambiare lotto" type="image"  data-toggle="collapse" href="#lm_dialog' . $k . '">' . $selected_lot['id'] . ' lotto n.:' . $selected_lot['identifier'];
						if (intval($v['expiry']) > 0) {
							echo ' scadenza:' . gaz_format_date($selected_lot['expiry']);
						}
						echo ' - disponibili: ' . gaz_format_quantity($count[$selected_lot['identifier']]) . ' <i class="glyphicon glyphicon-tag"></i></button>';						
						
						echo "<input type=\"hidden\" value=\"" . $selected_lot['id_lotmag'] . "\" name=\"righi[$k][id_lotmag]\">\n";
						echo "<input type=\"hidden\" value=\"" . $selected_lot['identifier'] . "\" name=\"righi[$k][identifier]\">\n";
						echo "<input type=\"hidden\" value=\"" . $selected_lot['expiry'] . "\" name=\"righi[$k][expiry]\">\n";
						
						if ($v['evadibile']>$count[$selected_lot['identifier']]) { // Se il lotto scelto non ha disponibilità sufficienti segnalo errore
							?>
							<div class="alert alert-warning alert-dismissible">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<strong>Warning!</strong> <b>Quantità lotto non sufficiente!</b> </br>Se si conferma si creeranno incongruenze fra quantità e lotti! </br> Si consiglia di selezionare un lotto con sufficiente disponibilità</br> oppure di diminuire la quantità in uscita.
							</div>
							<?php
						}				
					} else {  // selezione automatica INIZIALE  del lotto disponibile 								
						if (!isset($v['id_lotmag']) or (intval($v['id_lotmag'])==0)) { 
				
							foreach ($lm->divided as $x => $vc) { // ciclo i lotti scelti da getAvailableLots
						
								if ($vc['qua'] >= 0.00001) {
									if ($vc['quanti'] >= $v['evadibile']){ 
										$v['id_lotmag']= $vc['id']; // al primo ciclo, cioè id lotto è zero, setto il lotto
										$selected_lot = $lm->getLot($v['id_lotmag']);
								
										echo '<div><button class="btn btn-xs btn-success"  title="Lotto selezionato automaticamente. Cliccare per cambiare lotto" data-toggle="collapse" href="#lm_dialog' . $k . '">' . $selected_lot['id'] . ' Lotto n.: ' . $selected_lot['identifier'];
										if (intval($selected_lot['expiry']) > 0) {
											echo ' scadenza:' . gaz_format_date($selected_lot['expiry']);
										}
										echo ' disponibili:' . gaz_format_quantity($count[$selected_lot['identifier']]);
										echo '  <i class="glyphicon glyphicon-tag"></i></button>';
										 echo "<input type=\"hidden\" value=\"" . $selected_lot['id_lotmag'] . "\" name=\"righi[$k][id_lotmag]\">\n";
										echo "<input type=\"hidden\" value=\"" . $selected_lot['identifier'] . "\" name=\"righi[$k][identifier]\">\n";
										echo "<input type=\"hidden\" value=\"" . $selected_lot['expiry'] . "\" name=\"righi[$k][expiry]\">\n";
										$l++; 
									}
								}	
							} 
						}
					}
					
					// Antonio Germani - Cambio lotto  -->
					echo '<div id="lm_dialog' . $k . '" class="collapse" >';
									
					if ((count($lm->available) >= 1)) { 
						foreach ($lm->available as $v_lm) {
							if ($v_lm['id'] <> $v['id_lotmag']) { 
								echo '<div>Cambia con:<button class="btn btn-xs btn-warning" type="text" 	onclick="this.form.submit();" name="righi['.$k.'][id_lotmag]" value="'.$v_lm['id'].'">'
								. $v_lm['id']
								. ' lotto n.:' . $v_lm['identifier'];
								if (intval($v_lm['expiry']) > 0) {
											echo ' scadenza:' . gaz_format_date($v_lm['expiry']);
										}
								echo ' disponibili:' . gaz_format_quantity($count[$v_lm['identifier']])
								. '</button></div>';
							}
						}
					} else {
						echo '<div><button class="btn btn-xs btn-danger" type="image" >Non ci sono disponibili altri lotti.</button></div>';
					}
					?>
					</div>
					<?php					
				}
				// fine gestione lotti
				
				echo "</td>\n";
                if ($v['tiprig'] <= 10 || $v['tiprig'] >= 14) {
                    $fields = array_merge($fields, array('unimis', 'quanti',
                        'prelis', 'provvigione', 'sconto'
                            )
                    );
                    echo "<td align=\"center\">" . $v['unimis'] . "</td>\n";
                    echo "<td align=\"right\">" . $v['quanti'] . "</td>\n";
                    echo "<input type=\"hidden\" value=\"" . $v['evaso_in_precedenza'] . "\" name=\"righi[$k][evaso_in_precedenza]\">\n";
                    echo "<td align=\"right\" width=\"10%\"><input type=\"text\" value=\"" . $v['evadibile'] . "\" name=\"righi[$k][evadibile]\"></td>\n";
                    echo "<td align=\"right\">" . $v['prelis'] . "</td>\n";
                    echo "<td align=\"right\">" . $v['sconto'] . "</td>\n";
                    echo "<td align=\"right\">" . $v['provvigione'] . "</td>\n";
                    echo "<td align=\"right\">$imprig</td>\n";
                    echo "<td align=\"center\"><input type=\"checkbox\" name=\"righi[$k][checkval]\"  title=\"" . $script_transl['checkbox'] . "\" $checkin value=\"$imprig\" onclick=\"this.form.total.value=calcheck(this);\"></td>\n";
                } else {
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td></td>";
                }
                echo "</tr>";

                $ctrl_tes = $v['id_tes'];
                /* probabilmente potevo fare un loop sulle chiavi di $v ma non sono sicuro dell'impatto
                  quindi ho utilizzato un array ad-hoc attenendomi ai soli nomi preesistenti
                 */
                foreach ($fields as $current) {
                    $hRowFlds .= "<input type=\"hidden\" name=\"righi[$k][$current]\" value=\"{$v[$current]}\">\n";
                }
            }
            echo "<tr><td class=\"FacetFieldCaptionTD\">\n";
            echo $hRowFlds;
            unset($fields, $hRowFlds);

            echo "<input type=\"hidden\" name=\"hiddentot\" value=\"$total_order\">\n";
            echo "<input type=\"submit\" name=\"Return\" value=\"" . $script_transl['return'] . "\">&nbsp;</td>\n";
            echo "<td align=\"right\" colspan=\"6\" class=\"FacetFieldCaptionTD\">\n";
            echo "<input type=\"submit\" name=\"cmr\" value=\"" . $script_transl['issue_cmr'] . "\" accesskey=\"m\" />\n";
            echo "<input type=\"submit\" name=\"ddt\" value=\"" . $script_transl['issue_ddt'] . "\" accesskey=\"d\" />\n";
            echo "<input type=\"submit\" name=\"fai\" value=\"" . $script_transl['issue_fat'] . "\" accesskey=\"f\" />\n";
            if (!empty($alert_sezione))
                echo " &sup1;";
            echo "<input type=\"submit\" name=\"vco\" value=\"" . $script_transl['issue_cor'] . "\" accesskey=\"c\" />\n";
            echo "</td>";
            echo "<td colspan=\"2\" class=\"FacetFieldCaptionTD\" align=\"right\">" . $script_transl['taxable'] . " " . $admin_aziend['html_symbol'] . " &nbsp;\n";
            echo "<input type=\"text\"  style=\"text-align:right;\" value=\"" . number_format(($total_order - $total_order * $form['sconto'] / 100 + $form['traspo']), 2, '.', '') . "\" name=\"total\" size=\"8\" readonly />\n";
            echo "</td></tr>";
            if (!empty($alert_sezione))
                echo "<tr><td colspan=\"3\"></td><td colspan=\"2\" class=\"FacetDataTDred\">$alert_sezione </td></tr>";
        }
        ?>
    </table>
</form>
<?php
require("../../library/include/footer.php");
?>
