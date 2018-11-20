﻿<?php
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
require ("../../library/include/datlib.inc.php");
require ("../../modules/magazz/lib.function.php");
$admin_aziend = checkAdmin();
$msg = "";
$lm = new lotmag;
$gForm = new ordermanForm();
if (isset($_GET['popup'])) { //controllo se proviene da una richiesta apertura popup
    $popup = $_GET['popup'];
} else {
    $popup = "";
}
if (isset($_GET['type'])) { // controllo se proviene anche da una richiesta del modulo camp
    $form['order_type'] = $_GET['type'];
}
if ((isset($_POST['Update'])) or (isset($_GET['Update']))) {
    $toDo = 'update';
} else {
    $toDo = 'insert';
}
if (!isset($_POST['ritorno'])) {
    $_POST['ritorno'] = $_SERVER['HTTP_REFERER'];
}
if ((isset($_GET['Update']) and !isset($_GET['codice'])) or isset($_POST['Return'])) {
    header("Location: " . $_POST['ritorno']);
    exit;
}
if ((isset($_POST['Insert'])) or (isset($_POST['Update']))) { // Antonio Germani se non e' il primo accesso
    $form = gaz_dbi_parse_post('orderman');
	
    $form['order_type'] = $_POST['order_type'];
    $form['description'] = $_POST['description'];
    $form['gioinp'] = $_POST['gioinp'];
    $form['mesinp'] = $_POST['mesinp'];
    $form['anninp'] = $_POST['anninp'];
    $form['day_of_validity'] = $_POST['day_of_validity'];
    $form["campo_impianto"] = $_POST["campo_impianto"];    
    $form['quantip'] = $_POST['quantip'];
    $form['cosear'] = $_POST['cosear'];
	if (isset ($_POST['codart'])){
		$form['codart'] = $_POST['codart'];
		$resartico = gaz_dbi_get_row($gTables['artico'], "codice", $form['codart']);
	} else {
		$resartico = gaz_dbi_get_row($gTables['artico'], "codice", $form['cosear']);
		$form['codart'] = $resartico['codice'];
	} 
	$form['lot_or_serial'] = $resartico['lot_or_serial'];
	if ($resartico['good_or_service'] == 2) { // se è un articolo composto
		if ($toDo == "update") { //se UPDATE
			 // prendo i movimenti di magazzino dei componenti 
            $query = "SELECT " . '*' . " FROM " . $gTables['movmag'] . " WHERE operat = '-1' AND id_orderman ='" . $_GET['codice'] . "'";
            $result7 = gaz_dbi_query($query);
		} else { // se INSERT
			// prendo i componenti che formerano l'articolo
            $query = "SELECT * FROM " . $gTables['distinta_base'] . " WHERE codice_composizione = '" . $form['codart'] . "'";
            $rescompo = gaz_dbi_query($query);
		}   
	}    
    $form['coseor'] = $_POST['coseor'];	
    if (intval($form['coseor']) > 0) { // se c'è un numero ordine lo importo tramite l'id
        $res = gaz_dbi_get_row($gTables['tesbro'], "id_tes", $form['coseor']);
		$form['order'] = $res['numdoc'];
		$form['id_tes'] = $res['id_tes'];
        if (isset($res)) { // se esiste veramente l'ordine ne prendo il rigo per l'articolo selezionato
            $res2 = gaz_dbi_get_row($gTables['rigbro'], "id_tes", $res['id_tes'] . "' AND codart = '" . $form['codart']);
            $form['quantipord'] = $res2['quanti'];
            $form['id_tesbro'] = $res['id_tes'];
            $form['id_rigbro'] = $res2['id_rig'];
			// prendo tutte le produzioni/orderman in cui c'è questo rigbro per conteggiare la quantità eventualmente già prodotta
            $query = "SELECT id FROM " . $gTables['orderman'] . " WHERE id_rigbro = '" . $res2['id_rig'] . "'";
            $resor = gaz_dbi_query($query);
            $quantiprod = 0;
            while ($row = $resor->fetch_assoc()) { // scorro tutte le produzioni/orderman trovate
                // per ogni orderman consulto movmag e conteggio le quantità per articolo già prodotte
                $row = gaz_dbi_get_row($gTables['movmag'], "artico ", $form['codart'] . "' AND operat ='1' AND id_orderman ='" . $row['id']);
                $quantiprod = $quantiprod + $row['quanti'];
            }
        } else { // se l'ordine non esiste ed è stato inserito un numero anomalo
            $form['codart'] = "";
            $form['quantip'] = 0;
            $form['id_tesbro'] = 0;
            $form['id_rigbro'] = 0;
            $form['order'] = 0;
            $form['quantipord'] = 0;
        }
		if ($toDo == "update") { // se update importo il nome del cliente dell'ordine
			$res3 = gaz_dbi_get_row($gTables['clfoco'], "codice", $res['clfoco']);
		}
    } else {
		$form['id_tes'] ="";
        $form['quantip'] = $_POST['quantip'];
        $form['id_tesbro'] = 0;
        $form['id_rigbro'] = 0;
        $form['order'] = 0;
        $form['quantipord'] = 0;
    }
    $form['nmov'] = $_POST['nmov'];
    $form['nmovdb'] = $_POST['nmovdb'];
    for ($m = 0;$m <= $form['nmov'];++$m) {
        $form['staff'][$m] = $_POST['staff' . $m];
    }
    if ($toDo == "update") { // se update mantengo il codice staff memorizzato inizialmente nel data base
        for ($m = 0;$m <= $form['nmovdb'];++$m) {
            $form['staffdb'][$m] = $_POST['staffdb' . $m];
        }
    }
    $form['filename'] = $_POST['filename'];
    $form['identifier'] = $_POST['identifier'];
    $form['expiry'] = $_POST['expiry'];
    $form['lot_or_serial'] = $_POST['lot_or_serial'];
    if (strlen($_POST['datreg']) > 0) {
        $form['datreg'] = $_POST['datreg'];
    } else {
        $form['datreg'] = date("Y-m-d");
    }
    $form['id_movmag'] = $_POST['id_movmag'];
    $form['id_lotmag'] = $_POST['id_lotmag'];
    if (isset($_POST['numcomp'])) {
        $form['numcomp'] = $_POST['numcomp'];
        if ($form['numcomp'] > 0) {
            for ($m = 0;$m < $form['numcomp'];++$m) {
                $form['artcomp'][$m] = $_POST['artcomp' . $m];
                $form['quanti_comp'][$m] = $_POST['quanti_comp' . $m];
                $form['q_lot_comp'][$m] = $_POST['q_lot_comp' . $m];
                for ($n = 0;$n < $form['q_lot_comp'][$m];++$n) { // se q lot comp è zero vuol dire che non ci sono lotti
                    $form['id_lot_comp'][$m][$n] = $_POST['id_lotmag' . $m . $n];
                    $form['lot_quanti'][$m][$n] = $_POST['lot_quanti' . $m . $n];
                }
            }
        }
    }
    // Antonio Germani > questo serve per aggiungere o togliere un operaio
    if (isset($_POST['add_staff'])) {
        $form['nmov'] = $_POST['nmov'];
        for ($m = 0;$m <= $form['nmov'];++$m) {
            $form['staff'][$m] = $_POST['staff' . $m];
        }
        $form['nmov'] = $form['nmov'] + 1;
        $form['staff'][$form['nmov']] = "";
    }
    if (isset($_POST['Del_mov'])) {
        $form['staff'][$form['nmov']] = "";
        if ($_POST['nmov'] > 0) {
            $form['nmov'] = $form['nmov'] - 1;
        }
    }
    // Se viene inviata la richiesta di conferma totale ...CONTROLLO ERRORI ***
    $form['datemi'] = $form['anninp'] . "-" . $form['mesinp'] . "-" . $form['gioinp'];
    if (isset($_POST['ins'])) {
        $itemart = gaz_dbi_get_row($gTables['artico'], "codice", $form['codart']);
        if ($form['codart'] <> "" && !isset($itemart)) { // controllo se codice articolo non esiste o se è nullo
            $msg.= "20+";
        }
        if ($itemart['good_or_service'] == 2 && isset($form['numcomp'])) { // se articolo composto controllo se quantità sufficienti dei componenti
            for ($nc = 0;$nc <= $form['numcomp'] - 1;++$nc) {
                if ($form['quanti_comp'][$nc] == "ERRORE") { // se c'è errore segnalo
                    $msg.= "21+";
                }
            }
        }
        if ($form['order'] > 0) { // se c'è un numero ordine controllo che esista veramente l'ordine
            $itemord = gaz_dbi_get_row($gTables['tesbro'], "numdoc", $form['order']);
            if (!isset($itemord)) {
                $msg.= "23+";
                unset($itemord);
            }
            if (isset($_POST['okprod']) && $_POST['okprod'] <> "ok" && $toDo == "insert") {
                $msg.= "24+";
            }
        }
        if (empty($form['description'])) { //descrizione vuota
            $msg.= "4+";
        }
        if (strlen($form['order_type']) < 3) { //tipo produzione vuota
            $msg.= "12+";
        }
        if ($form['order_type'] == "IND") { // in produzione industriale
            if (strlen($form['codart']) == 0) { // articolo vuoto
                $msg.= "16+";
            }
            if ($form['quantip'] == 0) { // quantità vuota
                $msg.= "17+";
            }
            if ($form['staff'][0] > 0 && $form['day_of_validity'] > 13) { // D. Lgs. 66/2003 > massimo ore giornaliere lavorabili = 13
                $msg.= "18+";
            }
            if (intval($form['datreg']) == 0) { // se manca la data di registrazione
                $msg.= "22+";
            }
        }
        if ($msg == "") { // nessun errore
            // Antonio Germani >>>> inizio SCRITTURA dei database    §§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§§
            // i dati dell'articolo che non sono nel form li avrò nell' array $resartico
            
            if ($toDo == "update") { // se è un update cancello eventuali precedenti file temporanei nella cartella tmp
                foreach (glob("../../modules/orderman/tmp/*") as $fn) {
                    unlink($fn);
                }
                $id_orderman = $_GET['codice'];
            } else { // se è insert
                if ($form['order_type'] == "IND" or $form['order_type'] == "ART") { // se produzione industriale
                    $query = "SHOW TABLE STATUS LIKE '" . $gTables['movmag'] . "'";
                    unset($row);
                    $result = gaz_dbi_query($query);
                    $row = $result->fetch_assoc();
                    $id_movmag = $row['Auto_increment']; // trovo l'ID che avrà il movimento di magazzino MOVMAG
                    
                }
                $query = "SHOW TABLE STATUS LIKE '" . $gTables['orderman'] . "'";
                unset($row);
                $result = gaz_dbi_query($query);
                $row = $result->fetch_assoc();
                $id_orderman = $row['Auto_increment']; // trovo l'ID che avrà il movimento di produzione ORDERMAN
                if ($form['lot_or_serial'] == 1) {
                    $query = "SHOW TABLE STATUS LIKE '" . $gTables['lotmag'] . "'";
                    unset($row);
                    $result = gaz_dbi_query($query);
                    $row = $result->fetch_assoc();
                    $id_lotmag = $row['Auto_increment']; // trovo l'ID che avrà il lotto
                    
                }
                $query = "SHOW TABLE STATUS LIKE '" . $gTables['tesbro'] . "'";
                unset($row);
                $result = gaz_dbi_query($query);
                $row = $result->fetch_assoc();
                $id_tesbro = $row['Auto_increment']; // trovo l'ID che avrà TESBRO testata documento
                $query = "SHOW TABLE STATUS LIKE '" . $gTables['rigbro'] . "'";
                unset($row);
                $result = gaz_dbi_query($query);
                $row = $result->fetch_assoc();
                $id_rigbro = $row['Auto_increment']; // trovo l'ID che avrà RIGBRO rigo documento
                if (intval($form['order']) > 0) { // se c'è un ordine cliente esistente devo sovrascrivere gli id tesbro e rigbro
                    $id_tesbro = $form['id_tesbro'];
                    $id_rigbro = $form['id_rigbro'];
                }
            }
            if ($form['order_type'] == "AGR" or $form['order_type'] == "RIC" or $form['order_type'] == "PRF") {
                // escludo AGR RIC e PRF dal creare movimento di magazzino e lotti
                
            } else {
                // scrittura movimento di magazzino MOVMAG
                if ($toDo == "update") { // se è update, aggiorno in ogni caso
                    $query = "UPDATE " . $gTables['movmag'] . " SET quanti = '" . $form['quantip'] . "', datreg = '" . $form['datreg'] . "', datdoc = '" . $form['datemi'] . "', artico = '" . $form['codart'] . "' , campo_coltivazione = '" . $form['campo_impianto'] . "', id_orderman = '" . $_GET['codice'] . "' , id_lotmag = '" . $form['id_lotmag'] . "' WHERE id_mov ='" . $form['id_movmag'] . "'";
                    gaz_dbi_query($query);
                    /* Nell' update è stata bloccata la possibilità di variare l'articolo e la sua composizione, pertanto quanto segue è stato commentato
                    
                    if ($itemart['good_or_service']==2){ // se è un articolo composto
                    $query="DELETE FROM ".$gTables['movmag']." WHERE id_orderman = '".$_GET['codice']."' AND operat = '-1'";
                    gaz_dbi_query($query); //cancello tutti movimenti di magazzino corrispondenti alle uscite dei componenti
                    //Riscrivo nuovamente le uscite per i componenti
                    for ($nc = 0; $nc <= $form['numcomp']-1; ++$nc){ // faccio un ciclo con tutti i componenti
                    if ($form['q_lot_comp'][$nc]>0){ // se il componente ha lotti
                    for ($n = 0; $n < $form['q_lot_comp'][$nc]; ++$n){ //faccio un ciclo con i lotti di ogni singolo componente
                    $query="INSERT INTO " . $gTables['movmag'] . "(type_mov,operat,datreg,tipdoc,desdoc,datdoc,artico,campo_coltivazione,quanti,id_orderman,id_lotmag,adminid) VALUES ('0', '-1', '".$form['datreg']."', 'MAG', 'Scarico per Produzione', '".$form['datemi']."', '".$form['artcomp'][$nc]."', '".$form['campo_impianto']."', '".$form['lot_quanti'][$nc][$n]."', '".$id_orderman."', '".$form['id_lot_comp'][$nc][$n]."', '".$admin_aziend['adminid']."')";
                    gaz_dbi_query ($query); // Scarico il magazzino con l'articolo usato e i suoi lotti
                    }
                    } else { // se non ci sono lotti scarico semplicemente il magazzino
                    $query="INSERT INTO " . $gTables['movmag'] . "(type_mov,operat,datreg,tipdoc,desdoc,datdoc,artico,campo_coltivazione,quanti,id_orderman,id_lotmag,adminid) VALUES ('0', '-1', '".$form['datreg']."', 'MAG', 'Scarico per Produzione', '".$form['datemi']."', '".$form['artcomp'][$nc]."', '".$form['campo_impianto']."', '".$form['quanti_comp'][$nc]."', '".$id_orderman."', '', '".$admin_aziend['adminid']."')";
                    gaz_dbi_query ($query); // Scarico il magazzino con l'articolo usato
                    }
                    }
                    }
                    */
                }
                if ($toDo == "insert") { // se è insert, creo il movimento di magazzino
                    $query = "INSERT INTO " . $gTables['movmag'] . "(type_mov,operat,datreg,tipdoc,desdoc,datdoc,artico,campo_coltivazione,quanti,id_orderman,id_lotmag,adminid) VALUES ('0', '1', '" . $form['datreg'] . "', 'MAG', 'Produzione', '" . $form['datemi'] . "', '" . $form['codart'] . "', '" . $form['campo_impianto'] . "', '" . $form['quantip'] . "', '" . $id_orderman . "', '" . $id_lotmag . "', '" . $admin_aziend['adminid'] . "')";
                    gaz_dbi_query($query); // carico il magazzino con l'articolo prodotto
                    if ($itemart['good_or_service'] == 2) { // se è un articolo composto
                        for ($nc = 0;$nc <= $form['numcomp'] - 1;++$nc) { // faccio un ciclo con tutti i componenti
                            if ($form['q_lot_comp'][$nc] > 0) { // se il componente ha lotti
                                for ($n = 0;$n < $form['q_lot_comp'][$nc];++$n) { //faccio un ciclo con i lotti di ogni singolo componente
                                    $query = "INSERT INTO " . $gTables['movmag'] . "(type_mov,operat,datreg,tipdoc,desdoc,datdoc,artico,campo_coltivazione,quanti,id_orderman,id_lotmag,adminid) VALUES ('0', '-1', '" . $form['datreg'] . "', 'MAG', 'Scarico per Produzione', '" . $form['datemi'] . "', '" . $form['artcomp'][$nc] . "', '" . $form['campo_impianto'] . "', '" . $form['lot_quanti'][$nc][$n] . "', '" . $id_orderman . "', '" . $form['id_lot_comp'][$nc][$n] . "', '" . $admin_aziend['adminid'] . "')";
                                    gaz_dbi_query($query); // Scarico il magazzino con l'articolo usato e i suoi lotti
                                    
                                }
                            } else { // se non ci sono lotti scarico semplicemente il magazzino
                                $query = "INSERT INTO " . $gTables['movmag'] . "(type_mov,operat,datreg,tipdoc,desdoc,datdoc,artico,campo_coltivazione,quanti,id_orderman,id_lotmag,adminid) VALUES ('0', '-1', '" . $form['datreg'] . "', 'MAG', 'Scarico per Produzione', '" . $form['datemi'] . "', '" . $form['artcomp'][$nc] . "', '" . $form['campo_impianto'] . "', '" . $form['quanti_comp'][$nc] . "', '" . $id_orderman . "', '', '" . $admin_aziend['adminid'] . "')";
                                gaz_dbi_query($query); // Scarico il magazzino con l'articolo usato
                                
                            }
                        }
                    }
                }
                //Antonio Germani - > inizio salvo LOTTO, se c'è lotto e se il prodotto li richiede
                if ($form['lot_or_serial'] > 0) { // se l'articolo prevede un lotto
                    // ripulisco il numero lotto inserito da caratteri dannosi
                    $form['identifier'] = (empty($form['identifier'])) ? '' : filter_var($form['identifier'], FILTER_SANITIZE_STRING);
                    if (strlen($form['identifier']) == 0) { // se non c'è il lotto lo inserisco con data e ora in automatico
                        $form['identifier'] = date("Ymd Hms");
                    }
                    if (strlen($form['expiry']) == 0) { // se non c'è la scadenza la inserisco a zero in automatico
                        $form['expiry'] = "0000-00-00 00:00:00";
                    }
                    // è un nuovo INSERT
                    if (strlen($form['identifier']) > 0 && $toDo == "insert") {
                        $form['id_lotmag'] = $id_lotmag; //inserisco il nuovo lotto che deve essere nuovo ad ogni inerimento di orderman
                        gaz_dbi_query("INSERT INTO " . $gTables['lotmag'] . "(codart,id_movmag,identifier,expiry) VALUES ('" . $form['codart'] . "','" . $id_movmag . "','" . $form['identifier'] . "','" . $form['expiry'] . "')");
                        gaz_dbi_query("UPDATE " . $gTables['movmag'] . " SET id_lotmag = '" . $form['id_lotmag'] . "' WHERE id_mov ='" . $form['id_movmag'] . "'"); // aggiorno id_lotmag sul movmag
                        
                    }
                    //  è un UPDATE
                    if (strlen($form['identifier']) > 0 && $toDo == "update") {
                        $resin = gaz_dbi_get_row($gTables['orderman'], "id", $_GET['codice']);
                        $resin2 = gaz_dbi_get_row($gTables['lotmag'], "id", $resin['id_lotmag']);
                        if ($resin2['identifier'] == $form['identifier']) { // se ha lo stesso numero di lotto di quello precedentemente salvato faccio update di lotmag
                            gaz_dbi_query("UPDATE " . $gTables['lotmag'] . " SET codart = '" . $form['codart'] . "' , id_movmag = '" . $form['id_movmag'] . "' , identifier = '" . $form['identifier'] . "' , expiry = '" . $form['expiry'] . "' WHERE id = '" . $form['id_lotmag'] . "'");
                        } else { // se non è lo stesso numero, cancello il lotto iniziale e ne creo uno nuovo
                            gaz_dbi_query("DELETE FROM " . $gTables['lotmag'] . " WHERE id = " . $resin['id_lotmag']);
                            gaz_dbi_query("INSERT INTO " . $gTables['lotmag'] . "(codart,id_movmag,identifier,expiry) VALUES ('" . $form['codart'] . "','" . $form['id_movmag'] . "','" . $form['identifier'] . "','" . $form['expiry'] . "')");
                            $query = "SHOW TABLE STATUS LIKE '" . $gTables['lotmag'] . "'";
                            unset($row);
                            $result = gaz_dbi_query($query);
                            $row = $result->fetch_assoc();
                            $form['id_lotmag'] = $row['auto_increment'] - 1; // vedo dove è stato salvato lotmag
                            gaz_dbi_query("UPDATE " . $gTables['movmag'] . " SET id_lotmag = '" . $form['id_lotmag'] . "' WHERE id_mov ='" . $form['id_movmag'] . "'"); // aggiorno id_lotmag sul movmag
                            
                        }
                    }
                }
                // Antonio Germani - inizio salvo documento/CERTIFICATO lotto
                if ($toDo == "update") { // se è update lascio $form id_lotmag del form
                    $form['id_lotmag']; //
                    
                } else { // se è insert nuovo metto il nuovo id cercat ad inizio salvataggio
                    $form['id_lotmag'] = $id_lotmag;
                }
                if (substr($form['filename'], 0, 7) <> 'lotmag_') { // se è stato cambiato il file, cioè il nome non inizia con lotmag e, quindi, anche se è un nuovo insert
                    if (!empty($form['filename'])) { // e se ha un nome impostato nel form
                        $tmp_file = "../../data/files/tmp/" . $admin_aziend['adminid'] . '_' . $admin_aziend['company_id'] . '_' . $form['filename'];
                        // sposto il file nella cartella definitiva, rinominandolo e cancellandolo dalla temporanea
                        $fd = pathinfo($form['filename']);
                        rename($tmp_file, "../../data/files/" . $admin_aziend['company_id'] . "/lotmag_" . $form['id_lotmag'] . '.' . $fd['extension']);
                    }
                } // altrimenti se il file non è cambiato, anche se è update, non faccio nulla
                // <<< fine salvo lotti
                
            }
            // INIZIO gestione salvataggio database operai
            for ($form['mov'] = 0;$form['mov'] <= $form['nmov'];++$form['mov']) { // per ogni operaio
                if (intval($form['staff'][$form['mov']]) > 0) { // se il codice operaio esiste
                    $id_worker = $form['staff'][$form['mov']]; //identificativo operaio
                    // questa è la data documento iniziale >> $form['datdocin']
                    $work_day = $form['anninp'] . "-" . $form['mesinp'] . "-" . $form['gioinp']; // giorno lavorato
                    if ($form['day_of_validity'] > 8) {
                        $hours_normal = 8; //ore lavorate normali
                        $hours_extra = $form['day_of_validity'] - 8; //ore lavorate extra
                        $id_work_type_extra = 2;
                    } else {
                        $hours_normal = $form['day_of_validity'];
                        $hours_extra = 0;
                        $id_work_type_extra = 0;
                    }
                    $result2 = gaz_dbi_get_row($gTables['tesbro'], "id_orderman", $id_orderman); // prendo le ore della vecchia registrazione della produzione
                    if ($result2['day_of_validity'] > 8) {
                        $hours_normal_pre = 8;
                        $hours_extra_pre = $result2['day_of_validity'] - 8;
                    } else {
                        $hours_normal_pre = $result2['day_of_validity'];
                        $hours_extra_pre = 0;
                    }
                    // controllo se è una variazione movimento e se è stato cambiato l'operaio
                    if ($form['nmov'] <= $form['nmovdb'] && $toDo == "update" && $form['staffdb'][$form['mov']] <> $id_worker) { // se è update ed è stato cambiato l'operaio già memorizzato nel database
                        if (strtotime($work_day) == strtotime($result2['datemi'])) { // se non è stata cambiata la data della produzione
                            // all'operaio che è stato sostituito, devo togliere le ore
                            $rin = gaz_dbi_get_row($gTables['staff_worked_hours'], "id_staff ", $form['staffdb'][$form['mov']] . "' AND work_day ='" . $work_day . "' AND id_orderman ='" . $id_orderman);
                            if (isset($rin)) { // se confermato che esiste giorno e operaio sostituito, tolgo le ore lavorate memorizzate in precedenza
                                $ore_normal = $rin['hours_normal'] - $hours_normal_pre;
                                $ore_extra = $rin['hours_extra'] - $hours_extra_pre;
                                if ($hours_extra == 0) {
                                    $id_work_type_extra = "";
                                } else {
                                    $id_work_type_extra = 2;
                                }
                                // e faccio l'UPDATE
                                $query = "UPDATE " . $gTables['staff_worked_hours'] . " SET hours_normal = '" . $ore_normal . "', id_work_type_extra = '" . $id_work_type_extra . "', id_orderman = '', hours_extra = '" . $ore_extra . "' WHERE id_staff = '" . $form['staffdb'][$form['mov']] . "' AND work_day = '" . $work_day . "' AND id_orderman = '" . $id_orderman . "'";
                                gaz_dbi_query($query);
                            }
                            // al nuovo operaio devo aggiungere le ore lavorate
                            $r = gaz_dbi_get_row($gTables['staff_worked_hours'], "id_staff ", $id_worker . "' AND work_day ='" . $work_day . "' AND id_orderman ='" . $id_orderman);
                            if (isset($r)) { // se esiste il nuovo operaio nel giorno e con id_orderman, vedo se ci sono ore lavorate in precedenza e ci aggiungo quelle della produzione
                                $ore_normal = $r['hours_normal'] + $hours_normal;
                                $ore_extra = $r['hours_extra'] + $hours_extra;
                            }
                            if ($ore_normal > 8) {
                                $ore_extra = $ore_extra + ($ore_normal - 8);
                                $ore_normal = 8;
                            }
                            if ($ore_extra > 0) {
                                $id_work_type_extra = 2;
                            } else {
                                $id_work_type_extra = "";
                            }
                            // salvo ore su nuovo operaio
                            $exist = gaz_dbi_record_count($gTables['staff_worked_hours'], "work_day = '" . $work_day . "' AND id_staff = " . $id_worker . "' AND id_orderman ='" . $id_orderman);
                            if ($exist >= 1) { // se ho già un record del lavoratore per quella data e con quel id_orderman faccio UPDATE
                                $query = 'UPDATE ' . $gTables['staff_worked_hours'] . ' SET id_staff =' . $id_worker . ", id_orderman = '" . $id_orderman . "', work_day = '" . $work_day . "', hours_normal = '" . $ore_normal . "', id_work_type_extra = '" . $id_work_type_extra . "', hours_extra = '" . $ore_extra . "' WHERE id_staff = '" . $id_worker . "' AND work_day = '" . $work_day . "' AND id_orderman = '" . $id_orderman . "'";
                                gaz_dbi_query($query);
                            } else { // altrimenti faccio l'INSERT
                                $v = array();
                                $v['id_staff'] = $id_worker;
                                $v['work_day'] = $work_day;
                                $v['hours_normal'] = $hours_normal;
                                $v['hours_extra'] = $hours_extra;
                                $v['id_orderman'] = $id_orderman;
                                $v['id_work_type_extra'] = $id_work_type_extra;
                                gaz_dbi_table_insert('staff_worked_hours', $v);
                            }
                        } else { // se è stata cambiata la data di produzione
                            // all'operaio che è stato sostituito, devo togliere le ore al giorno in cui gli erano state date
                            $rin = gaz_dbi_get_row($gTables['staff_worked_hours'], "id_staff ", $form['staffdb'][$form['mov']] . "' AND work_day ='" . $result2['datemi'] . "' AND id_orderman ='" . $id_orderman);
                            if (isset($rin)) { // se confermato che esiste giorno e operaio sostituito con quell' id_ordeman, tolgo le ore lavorate memorizzate in precedenza
                                $ore_normal = $rin['hours_normal'] - $hours_normal_pre;
                                $ore_extra = $rin['hours_extra'] - $hours_extra_pre;
                                if ($ore_extra == 0) {
                                    $id_work_type_extra = "";
                                } else {
                                    $id_work_type_extra = 2;
                                }
                                // e faccio l'UPDATE
                                $query = "UPDATE " . $gTables['staff_worked_hours'] . " SET hours_normal = '" . $ore_normal . "', id_work_type_extra = '" . $id_work_type_extra . "', id_orderman = '', hours_extra = '" . $ore_extra . "' WHERE id_staff = '" . $form['staffdb'][$form['mov']] . "' AND work_day = '" . $result2['datemi'] . " AND id_orderman = '" . $id_ordeman . "'";
                                gaz_dbi_query($query);
                            }
                            // al nuovo operaio devo aggiungere le ore lavorate nel nuovo giorno di produzione
                            $r = gaz_dbi_get_row($gTables['staff_worked_hours'], "id_staff ", $id_worker . "' AND work_day ='" . $work_day . "' AND id_orderman ='" . $id_orderman);
                            if (isset($r)) { // se esiste giorno e nuovo operaio con id_orderman, vedo se ci sono ore lavorate in precedenza e ci aggiungo quelle della produzione
                                $ore_normal = $r['hours_normal'] + $hours_normal;
                                $ore_extra = $r['hours_extra'] + $hours_extra;
                            }
                            if ($ore_normal > 8) {
                                $ore_extra = $ore_extra + ($ore_normal - 8);
                                $ore_normal = 8;
                            }
                            if ($ore_extra > 0) {
                                $id_work_type_extra = 2;
                            } else {
                                $id_work_type_extra = "";
                            }
                            // salvo ore su nuovo operaio
                            $exist = gaz_dbi_record_count($gTables['staff_worked_hours'], "work_day = '" . $work_day . "' AND id_staff = " . $id_worker . "' AND id_orderman ='" . $id_orderman);
                            if ($exist >= 1) { // se ho già un record del lavoratore per quella data e con quel id_orderman faccio UPDATE
                                $query = 'UPDATE ' . $gTables['staff_worked_hours'] . ' SET id_staff =' . $id_worker . ", id_orderman = '" . $id_orderman . "', work_day = '" . $work_day . "', hours_normal = '" . $ore_normal . "', id_work_type_extra = '" . $id_work_type_extra . "', hours_extra = '" . $ore_extra . "' WHERE id_staff = '" . $id_worker . "' AND work_day = '" . $work_day . "' AND id_orderman = '" . $id_orderman . "'";
                                gaz_dbi_query($query);
                            } else { // altrimenti faccio l'INSERT
                                $v = array();
                                $v['id_staff'] = $id_worker;
                                $v['work_day'] = $work_day;
                                $v['hours_normal'] = $hours_normal;
                                $v['hours_extra'] = $hours_extra;
                                $v['id_orderman'] = $id_orderman;
                                $v['id_work_type_extra'] = $id_work_type_extra;
                                gaz_dbi_table_insert('staff_worked_hours', $v);
                            }
                        }
                    } else { // se non è stato cambiato operaio ed è sempre update
                        if ($toDo == "update" && $form['staffdb'][$form['mov']] == $id_worker && $form['nmov'] <= $form['nmovdb']) { // se è update e NON è stato cambiato l'operaio del database e non è un nuovo aggiunto
                            if (strtotime($work_day) <> strtotime($result2['datemi'])) { // se è stata cambiata la data
                                // tolgo le ore al giorno iniziale e gli azzero pure il riferimento alla produzione perché non è più fatta in quel giorno, quindi id_orderman=""
                                $rin = gaz_dbi_get_row($gTables['staff_worked_hours'], "id_staff ", $id_worker . "' AND work_day ='" . $result2['datemi'] . "' AND id_orderman ='" . $id_orderman);
                                if (isset($rin)) { // se esiste giorno e operaio gli tolgo le ore memorizzate in precedenza
                                    $ore_normal = $rin['hours_normal'] - $hours_normal_pre;
                                    $ore_extra = $rin['hours_extra'] - $hours_extra_pre;
                                    if ($ore_extra == 0) {
                                        $id_work_type_extra = "";
                                    } else {
                                        $id_work_type_extra = 2;
                                    }
                                    $query = 'UPDATE ' . $gTables['staff_worked_hours'] . " SET hours_normal = '" . $ore_normal . "', id_work_type_extra = '" . $id_work_type_extra . "', id_orderman = '', hours_extra = '" . $ore_extra . "' WHERE id_staff = '" . $id_worker . "' AND work_day = '" . $result2['datemi'] . "' AND id_orderman= '" . $id_orderman . "'";
                                    gaz_dbi_query($query);
                                }
                                $rin = gaz_dbi_get_row($gTables['staff_worked_hours'], "id_staff ", $id_worker . "' AND work_day ='" . $work_day . "' AND id_orderman ='" . $id_orderman);
                                if (isset($rin)) { // se esiste giorno e operaio con id_orderman gli aggiungo le ore
                                    $ore_normal = $rin['hours_normal'] + $hours_normal;
                                    $ore_extra = $rin['hours_extra'] + $hours_extra;
                                    if ($ore_normal > 8) {
                                        $ore_extra = $ore_extra + ($ore_normal - 8);
                                        $ore_normal = 8;
                                    }
                                    if ($ore_extra > 0) {
                                        $id_work_type_extra = 2;
                                    } else {
                                        $id_work_type_extra = "";
                                    }
                                    $query = 'UPDATE ' . $gTables['staff_worked_hours'] . " SET hours_normal = '" . $ore_normal . "', id_work_type_extra = '" . $id_work_type_extra . "', hours_extra = '" . $ore_extra . "' WHERE id_staff = '" . $id_worker . "' AND work_day = '" . $work_day . "' AND id_orderman = '" . $id_orderman . "'";
                                    gaz_dbi_query($query);
                                } else { // altrimenti faccio l'INSERT
                                    $v = array();
                                    $v['id_staff'] = $id_worker;
                                    $v['work_day'] = $work_day;
                                    $v['hours_normal'] = $hours_normal;
                                    $v['id_orderman'] = $id_orderman;
                                    $v['hours_extra'] = $hours_extra;
                                    $v['id_work_type_extra'] = $id_work_type_extra;
                                    gaz_dbi_table_insert('staff_worked_hours', $v);
                                }
                            } else { //se NON è stata cambiata la data aggiorno solo le ore
                                $rin = gaz_dbi_get_row($gTables['staff_worked_hours'], "id_staff ", $id_worker . "' AND work_day ='" . $work_day . "' AND id_orderman ='" . $id_orderman);
                                if (isset($rin)) { // se esiste giorno e operaio con id_orderman gli modifico le ore nello stesso giorno
                                    $ore_normal = $rin['hours_normal'] - $hours_normal_pre + $hours_normal;
                                    $ore_extra = $rin['hours_extra'] - $hours_extra_pre + $hours_extra;
                                    if ($ore_normal > 8) {
                                        $ore_extra = $ore_extra + ($ore_normal - 8);
                                        $ore_normal = 8;
                                    }
                                    if ($ore_extra > 0) {
                                        $id_work_type_extra = 2;
                                    } else {
                                        $id_work_type_extra = "";
                                    }
                                    $query = 'UPDATE ' . $gTables['staff_worked_hours'] . " SET hours_normal = '" . $ore_normal . "', id_work_type_extra = '" . $id_work_type_extra . "', hours_extra = '" . $ore_extra . "' WHERE id_staff = '" . $id_worker . "' AND work_day = '" . $work_day . "' AND id_orderman = '" . $id_orderman . "'";
                                    gaz_dbi_query($query);
                                } else { // altrimenti faccio l'INSERT perché è stato aggiunto operaio in update senza operai
                                    $v = array();
                                    $v['id_staff'] = $id_worker;
                                    $v['work_day'] = $work_day;
                                    $v['hours_normal'] = $hours_normal;
                                    $v['id_orderman'] = $id_orderman;
                                    $v['hours_extra'] = $hours_extra;
                                    $v['id_work_type_extra'] = $id_work_type_extra;
                                    gaz_dbi_table_insert('staff_worked_hours', $v);
                                }
                            }
                        }
                    }
                    if ($toDo == "update" && $form['nmov'] > $form['nmovdb'] && $form['staffdb'][$form['mov']] <> $id_worker) { // se è update ed è stato aggiunto un nuovo operaio a quelli esistenti e si tratta proprio di quello aggiunto
                        $rin = gaz_dbi_get_row($gTables['staff_worked_hours'], "id_staff ", $id_worker . "' AND work_day ='" . $work_day . "' AND id_orderman ='" . $id_orderman);
                        if (isset($rin)) { // se esiste giorno e operaio con id_ordeman gli aggiungo le ore e aggiorno database
                            $ore_normal = $rin['hours_normal'] + $hours_normal;
                            $ore_extra = $rin['hours_extra'] + $hours_extra;
                            if ($ore_normal > 8) {
                                $ore_extra = $ore_extra + ($ore_normal - 8);
                                $ore_normal = 8;
                            }
                            if ($ore_extra > 0) {
                                $id_work_type_extra = 2;
                            } else {
                                $id_work_type_extra = "";
                            }
                            $query = 'UPDATE ' . $gTables['staff_worked_hours'] . " SET hours_normal = '" . $ore_normal . "', id_work_type_extra = '" . $id_work_type_extra . "', hours_extra = '" . $ore_extra . "', id_orderman = '" . $id_orderman . "' WHERE id_staff = '" . $id_worker . "' AND work_day = '" . $work_day . "'";
                            gaz_dbi_query($query);
                        } else { // altrimenti faccio l'INSERT
                            $v = array();
                            $v['id_staff'] = $id_worker;
                            $v['work_day'] = $work_day;
                            $v['hours_normal'] = $hours_normal;
                            $v['id_orderman'] = $id_orderman;
                            $v['hours_extra'] = $hours_extra;
                            $v['id_work_type_extra'] = $id_work_type_extra;
                            gaz_dbi_table_insert('staff_worked_hours', $v);
                        }
                    }
                    if ($toDo <> "update") { // se non è un update è per forza una nuova produzione, quindi devo fare un insert
                        // INSERT nuovo rigo su staff_worked_hours
                        $v = array();
                        $v['id_staff'] = $id_worker;
                        $v['work_day'] = $work_day;
                        $v['hours_normal'] = $hours_normal;
                        $v['id_orderman'] = $id_orderman;
                        $v['hours_extra'] = $hours_extra;
                        $v['id_work_type_extra'] = $id_work_type_extra;
                        gaz_dbi_table_insert('staff_worked_hours', $v);
                    }
                }
            }
            // FINE registrazione database operai
            // Antonio Germani - Inizio Scrittura produzione ORDERMAN e, se non già creati da un ordine, scrittura di TESBRO E RIGBRO
            if ($toDo == 'update') { //  se e' una modifica, aggiorno orderman e tesbro
                $query = "UPDATE " . $gTables['orderman'] . " SET order_type = '" . $form['order_type'] . "', description = '" . $form['description'] . "', campo_impianto = '" . $form["campo_impianto"] . "', id_lotmag = '" . $form['id_lotmag'] . "', add_info = '" . $form['add_info'] . "', duration = '" . $form['day_of_validity'] . "' WHERE id = '" . $form['id'] . "'";
                gaz_dbi_query($query);
                $resin = gaz_dbi_get_row($gTables['tesbro'], "id_orderman", $id_orderman);
                if ($resin['id_tes'] <> $form['id_tesbro']) { // se l'ordine iniziale è diverso da quello del form
                    if ($resin['tipdoc'] == "PRO") { // se era autogenerato, cioè era PRO, lo cancello e basta perché vuol dire che è stato tolto completamente dal form o sostituito con un vero ordine VOR
                        gaz_dbi_query("DELETE FROM " . $gTables['tesbro'] . " WHERE id_orderman = " . $id_orderman);
                        // devo cancellare anche il relativo rigo rigbro ad esso connesso
                        gaz_dbi_query("DELETE FROM " . $gTables['rigbro'] . " WHERE id_tes = " . $resin['id_tes']);
                    } else { // se il numero ordine iniziale non era PRO, cioè era un ordine vero, gli azzero solo id orderman
                        gaz_dbi_query("UPDATE " . $gTables['tesbro'] . " SET id_orderman = '' WHERE id_tes = '" . $resin['id_tes'] . "'");
                    }
                    $query = "UPDATE " . $gTables['orderman'] . " SET " . 'id_tesbro' . " = '', " . 'id_rigbro' . " = '' WHERE id = '" . $form['id'] . "'"; // azzero anche i riferimenti su orderman
                    gaz_dbi_query($query);
                    if ($form['id_tesbro'] > 0) { // poi, se c'è un nuovo ordine VOR nel form, lo collego a id orderman
                        gaz_dbi_query("UPDATE " . $gTables['tesbro'] . " SET id_orderman = '" . $id_orderman . "' WHERE id_tes = '" . $form['id_tesbro'] . "'");
                        $query = "UPDATE " . $gTables['orderman'] . " SET " . 'id_tesbro' . " = '" . $form['id_tesbro'] . "', " . 'id_rigbro' . " = '" . $form['id_rigbro'] . "' WHERE id = '" . $form['id'] . "'";
                        gaz_dbi_query($query); // aggiorno i riferimenti su orderman
                        
                    } else { // se non c'è un nuovo ordine lo creo in automatico in tesbro, rigbro e metto i riferimenti su orderman
                        $query = "SHOW TABLE STATUS LIKE '" . $gTables['tesbro'] . "'";
                        unset($row);
                        $result = gaz_dbi_query($query);
                        $row = $result->fetch_assoc();
                        $id_tesbro = $row['auto_increment']; // trovo l'ID che avrà TESBRO testata documento
                        $query = "SHOW TABLE STATUS LIKE '" . $gTables['rigbro'] . "'";
                        unset($row);
                        $result = gaz_dbi_query($query);
                        $row = $result->fetch_assoc();
                        $id_rigbro = $row['auto_increment']; // trovo l'ID che avrà RIGBRO rigo documento
                        gaz_dbi_query("INSERT INTO " . $gTables['tesbro'] . "(tipdoc,datemi,numdoc,id_orderman,status,adminid) VALUES ('PRO','" . $form['datemi'] . "', '" . time() . "', '" . $id_orderman . "', 'AUTOGENERA', '" . $admin_aziend['adminid'] . "')"); // creo tesbro
                        gaz_dbi_query("INSERT INTO " . $gTables['rigbro'] . "(id_tes,codart,descri,unimis,quanti,status) VALUES ('" . $id_tesbro . "','" . $form['codart'] . "','" . $resartico['descri'] . "','" . $resartico['unimis'] . "', '" . $form['quantip'] . "', 'AUTOGENERA')"); // creo rigbro
                        $query = "UPDATE " . $gTables['orderman'] . " SET " . 'id_tesbro' . " = '" . $id_tesbro . "', " . 'id_rigbro' . " = '" . $id_rigbro . "' WHERE id = '" . $form['id'] . "'";
                        gaz_dbi_query($query); // aggiorno i riferimenti su orderman
                        
                    }
                } else { // se il numero d'ordine NON è stato cambiato posso fare update solo se è PRO, cioè autogenerato
                    if ($resin['tipdoc'] == "PRO") {
                        $res = gaz_dbi_get_row($gTables['rigbro'], "id_tes", $form['id_tesbro']);
                        if (isset($res)) { // se esiste il rigo lo aggiorno tesbro e rigbro
                            $query = "UPDATE " . $gTables['tesbro'] . " SET " . 'datemi' . " = '" . $form['datemi'] . "', id_orderman = '" . $id_orderman . "' WHERE id_tes = '" . $form['id_tesbro'] . "'";
                            $res = gaz_dbi_query($query);
                            $query = "UPDATE " . $gTables['rigbro'] . " SET " . 'codart' . " = '" . $form['codart'] . "', " . 'descri' . " = '" . $resartico['descri'] . "', " . 'unimis' . " = '" . $resartico['unimis'] . "', " . 'quanti' . " = '" . $form['quantip'] . "' WHERE id_tes = '" . $form['id_tesbro'] . "'";
                            $res = gaz_dbi_query($query);
                        }
                    }
                }
            } else { // e' un nuovo inserimento
                // creo e salvo ORDERMAN
                gaz_dbi_query("INSERT INTO " . $gTables['orderman'] . "(order_type,description,add_info,id_tesbro,id_rigbro,campo_impianto,id_lotmag,duration,adminid) VALUES ('" . $form['order_type'] . "','" . $form['description'] . "','" . $form['add_info'] . "','" . $id_tesbro . "', '" . $id_rigbro . "', '" . $form['campo_impianto'] . "', '" . $form['id_lotmag'] . "', '" . $form['day_of_validity'] . "', '" . $admin_aziend['adminid'] . "')");
                if (intval($form['order']) <= 0) { // se non c'è un numero ordine ne creo uno fittizio in TESBRO e RIGBRO
                    gaz_dbi_query("INSERT INTO " . $gTables['tesbro'] . "(tipdoc,datemi,numdoc,id_orderman,status,adminid) VALUES ('PRO','" . $form['datemi'] . "', '" . time() . "', '" . $id_orderman . "', 'AUTOGENERA', '" . $admin_aziend['adminid'] . "')");
                    gaz_dbi_query("INSERT INTO " . $gTables['rigbro'] . "(id_tes,codart,descri,unimis,quanti,status) VALUES ('" . $id_tesbro . "','" . $form['codart'] . "','" . $resartico['descri'] . "','" . $resartico['unimis'] . "', '" . $form['quantip'] . "', 'AUTOGENERA')");
                } else { // se c'è l'ordine lo collego ad orderman
                    $query = "UPDATE " . $gTables['tesbro'] . " SET id_orderman = '" . $id_orderman . "' WHERE id_tes = '" . $form['id_tesbro'] . "'";
                    $res = gaz_dbi_query($query);
                }
            }
            // fine orderman, tesbro e rigbro
            // se sono in un popup lo chiudo dopo aver salvato tutto
            if ($popup == 1) {
                echo "<script> 
				window.opener.location.reload(true);
				window.close();</script>";
            } else {
                header("Location: " . $_POST['ritorno']);
            }
            exit;
        }
    }
    //  fine scrittura database §§§§§§§§§§§§§§§§§§§§§§§§§§§§
    
} elseif ((!isset($_POST['Update'])) and (isset($_GET['Update']))) { //se e' il primo accesso per UPDATE
    $result = gaz_dbi_get_row($gTables['orderman'], "id", $_GET['codice']);
    $form['ritorno'] = $_POST['ritorno'];
    $form['id'] = $_GET['codice'];
    $form['order_type'] = $result['order_type'];
    $form['description'] = $result['description'];
    $form['id_tesbro'] = $result['id_tesbro'];	
    $form['id_rigbro'] = $result['id_rigbro'];
    $form['add_info'] = $result['add_info'];
    $form['day_of_validity'] = $result['duration'];
    $result4 = gaz_dbi_get_row($gTables['movmag'], "id_orderman", $_GET['codice']);
    $form['datreg'] = $result4['datreg'];
    $form['quantip'] = $result4['quanti'];
    $form['id_movmag'] = $result4['id_mov'];
    $result2 = gaz_dbi_get_row($gTables['tesbro'], "id_tes", $result['id_tesbro']);
    $form['gioinp'] = substr($result2['datemi'], 8, 2);
    $form['mesinp'] = substr($result2['datemi'], 5, 2);
    $form['anninp'] = substr($result2['datemi'], 0, 4);
    $form['datemi'] = $result2['datemi'];
    $form['campo_impianto'] = $result['campo_impianto'];
    $form['id_lotmag'] = $result['id_lotmag'];
    $form['order'] = $result2['numdoc'];	
	$res3 = gaz_dbi_get_row($gTables['clfoco'], "codice", $result2['clfoco']);// importo il nome del cliente dell'ordine
	$form['coseor'] = $result2['id_tes'];
	$form['id_tes'] = $result2['id_tes'];
    $result3 = gaz_dbi_get_row($gTables['rigbro'], "id_rig", $result['id_rigbro']);
    $form['codart'] = $result3['codart'];
    $form['quantipord'] = $result3['quanti'];
    $result5 = gaz_dbi_get_row($gTables['lotmag'], "id", $result['id_lotmag']);
    $form['identifier'] = $result5['identifier'];
    $form['expiry'] = $result5['expiry'];
	$resartico = gaz_dbi_get_row($gTables['artico'], "codice", $form['codart']);
	$form['lot_or_serial'] = $resartico['lot_or_serial'];
	if ($resartico['good_or_service'] == 2) { // se è un articolo composto
		// prendo i movimenti di magazzino dei componenti 
        $query = "SELECT " . '*' . " FROM " . $gTables['movmag'] . " WHERE operat = '-1' AND id_orderman ='" . $_GET['codice'] . "'";
        $result7 = gaz_dbi_query($query);		   
	}
    // Antonio Germani - se è presente, recupero il file documento lotto
    $form['filename'] = "";
    if (file_exists('../../data/files/' . $admin_aziend['company_id']) > 0) {
        // recupero il filename dal filesystem
        $dh = opendir('../../data/files/' . $admin_aziend['company_id']);
        while (false !== ($filename = readdir($dh))) {
            $fd = pathinfo($filename);
            $r = explode('_', $fd['filename']);
            if ($r[0] == 'lotmag' && $r[1] == $result['id_lotmag']) {
                // riassegno il nome file
                $form['filename'] = $fd['basename'];
            }
        }
    }
    // se presenti, prendo gli operai
    $query = "SELECT " . '*' . " FROM " . $gTables['staff_worked_hours'] . " WHERE id_orderman ='" . $_GET['codice'] . "'";
    $result6 = gaz_dbi_query($query);
    $form['mov'] = 0;
    $form['nmov'] = 0;
    $form['nmovdb'] = 0;
    $form['staff'][$form['mov']] = "";
    $form['staffdb'][$form['mov']] = "";
    if ($result6->num_rows > 0) {
        while ($row = $result6->fetch_assoc()) {
            $form['staff'][$form['mov']] = $row['id_staff'];
            $form['staffdb'][$form['mov']] = $row['id_staff'];
            $form['mov']++;
        }
        $form['nmov'] = $form['mov'] - 1;
        $form['nmovdb'] = $form['mov'] - 1;
    }
    $form['cosear'] = "";
} else { //se e' il primo accesso per INSERT
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
	$form['codart']="";
	$form['numdoc']="";
    if (isset($_GET['type'])) { // controllo se proviene anche da una richiesta del modulo camp
        $form['order_type'] = $_GET['type'];
    } else {
        $form['order_type'] = "";
    }
    $form['description'] = "";
    $form['id_tesbro'] = "";
    $form['add_info'] = "";
    $form['gioinp'] = date("d");
    $form['mesinp'] = date("m");
    $form['anninp'] = date("Y");
    $form['day_of_validity'] = "";
    $form["campo_impianto"] = "";
    $form['order'] = 0;
    $form['coseor'] = "";	
    $form['codart'] = "";
    $form['cosear'] = "";	
    $form['mov'] = 0;
    $form['nmov'] = 0;
    $form['nmovdb'] = 0;
    $form['staff'][$form['mov']] = "";
    $form['filename'] = "";
    $form['identifier'] = "";
    $form['expiry'] = "";
    $form['lot_or_serial'] = "";
    $form['datreg'] = date("Y-m-d");
    $form['quantip'] = "";
    $form['quantipord'] = "";
    $form['id_movmag'] = "";
    $form['id_lotmag'] = "";
    $form['numcomp'] = 0;
	$resartico['lot_or_serial']="";
	$resartico['good_or_service']="";
	$form['id_tes']="";
}
if (isset($_POST['Cancel'])) { // se è stato premuto ANNULLA
    $form['hidden_req'] = '';
    $form['order_type'] = "";
    $form['description'] = "";
    $form['id_tesbro'] = "";
    $form['add_info'] = "";
    $form['gioinp'] = date("d");
    $form['mesinp'] = date("m");
    $form['anninp'] = date("Y");
    $form['day_of_validity'] = "";
    $form["campo_impianto"] = "";
    $form['order'] = "";
    $form['codart'] = "";
    $form['mov'] = 0;
    $form['nmov'] = 0;
    $form['nmovdb'] = 0;
    $form['staff'][$form['mov']] = "";
    $form['filename'] = "";
    $form['identifier'] = "";
    $form['expiry'] = "";
    $form['quantip'] = "";
    $form['id_movmag'] = "";
    $form['id_lotmag'] = "";
    $form['numcomp'] = 0;
}
if (!empty($_FILES['docfile_']['name'])) { // Antonio Germani - se c'è un nome in $_FILES
    $prefix = $admin_aziend['adminid'] . '_' . $admin_aziend['company_id'];
    foreach (glob("../../data/files/tmp/" . $prefix . "_*.*") as $fn) { // prima cancello eventuali precedenti file temporanei
        unlink($fn);
    }
    $mt = substr($_FILES['docfile_']['name'], -3);
    if (($mt == "png" || $mt == "odt" || $mt == "peg" || $mt == "jpg" || $mt == "pdf") && $_FILES['docfile_']['size'] > 1000) { // se rispetta limiti e parametri lo salvo nella cartella tmp
        move_uploaded_file($_FILES['docfile_']['tmp_name'], '../../data/files/tmp/' . $prefix . '_' . $_FILES['docfile_']['name']);
        $form['filename'] = $_FILES['docfile_']['name'];
    } else {
        $msg.= "14+";
    }
}
require ("../../library/include/header.php");
$script_transl = HeadMain(0,array('custom/autocomplete',));
if ($toDo == 'update') {
    $title = ucwords($script_transl['upd_this']) . " n." . $form['id'];
} else {
    $title = ucwords($script_transl['ins_this']);
}
print "<form method=\"POST\" name=\"myform\" enctype=\"multipart/form-data\">\n";
print "<input type=\"hidden\" name=\"" . ucfirst($toDo) . "\" value=\"\">\n";
print "<input type=\"hidden\" value=\"" . $_POST['ritorno'] . "\" name=\"ritorno\">\n";
print "<input type=\"hidden\" name=\"hidden_req\" value=\"TRUE\">\n"; // per auto submit on change select input
print "<div align=\"center\" class=\"FacetFormHeaderFont\">$title</div>";
print "<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"FacetFormTABLE\" align=\"center\">\n";
if (!empty($msg)) {
    $message = "";
    $rsmsg = array_slice(explode('+', chop($msg)), 0, -1);
    foreach ($rsmsg as $value) {
        $message.= $script_transl['error'] . "! -> ";
        $rsval = explode('-', chop($value));
        foreach ($rsval as $valmsg) {
            $message.= $script_transl[$valmsg] . " ";
        }
        $message.= "<br />";
    }
    echo '<tr><td colspan="5" class="FacetDataTDred">' . $message . "</td></tr>\n";
}
if ($toDo == 'update') {
    print "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[0]</td><td class=\"FacetDataTD\"><input type=\"hidden\" name=\"id\" value=\"" . $form['id'] . "\" />" . $form['id'] . "</td></tr>\n";
}
// Antonio Germani > inserimento tipo di produzione
print "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[1]</td><td class=\"FacetDataTD\">";
?>
<script>
  $(function() {
    $( ".datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
	
});
</script>

<?php if ($toDo == "insert") {
?>
	<select name="order_type" onchange="this.form.submit()" >
	<option <?php if ($form['order_type'] == "") echo 'selected'; ?> value="">--</option>
	<option <?php if ($form['order_type'] == "AGR") echo 'selected'; ?> value="AGR">Agricola</option>
	<option <?php if ($form['order_type'] == "IND") echo 'selected'; ?> value="IND">Industriale</option>
	<option <?php if ($form['order_type'] == "RIC") echo 'selected'; ?> value="RIC">Ricerca e sviluppo</option>
	<option <?php if ($form['order_type'] == "PRF") echo 'selected'; ?> value="PRF">Professionale</option>
	<option <?php if ($form['order_type'] == "ART") echo 'selected'; ?> value="ART">Artigianale</option>
	</select>
<?php
} else {
    echo $form['order_type'], "&nbsp &nbsp";
    echo '<input type="hidden" name="order_type" value="' . $form['order_type'] . '">';
}
// inserimento data di registrazione
if ($form['order_type'] == "IND") {
    echo '<label>' . 'Data registrazione magazzino: ' . ' </label><input class="datepicker" type="text" onchange="this.form.submit();" name="datreg"  value="' . $form['datreg'] . '">';
} else {
    echo '<input type="hidden" name="datreg" value="">';
    if ($form['order_type'] != "") {
        echo "Non registra magazzino!";
    }
}

?>
</td></tr>
<?php
if ($form['order_type'] <> "AGR") { // input esclusi se produzione agricola

    // Antonio Germani > inserimento ordine    
?>
	<tr>
	<td class="FacetFieldCaptionTD"><?php echo $script_transl['8']; ?> </td>	
	<td colspan="2" class="FacetDataTD">
		<?php
    if ($toDo == "update") {
        echo "N: ",$form['order']," - Cliente: ",$res3['descri'];
?>
		<input type="hidden" name="order" Value="<?php echo $form['order']; ?>"/>
		<input type="hidden" name="coseor" Value="<?php echo $form['coseor']; ?>"/>
		<?php
    } else {
		// Inserimento ORDINE
		$select_order = new selectorder("id_tes");
		$select_order->addSelected($form['id_tes']);
		$select_order->output($form['coseor']);
    }
    if (strlen($form['order']) > 0) {
?>
<!--			<span class="glyphicon glyphicon-bell fa-2x" title="L'ordine impone l'articolo e la quantità" style="color:blue"></span>-->
			<?php
    }	
?>
	</td>
</tr>
<?php
    if ($form['order'] > 0 && $toDo != "update") { // se c'è un ordine e non siamo in update seleziono l'articolo fra quelli ordinati 
		echo "<tr><td class=\"FacetFieldCaptionTD\">" . $script_transl[9] . "</td><td class=\"FacetDataTD\">\n";
		// SELECT articolo da rigbro
		$gForm->selectFromDB('rigbro', 'cosear','codart', $form['codart'], 'id_tes', 1, ' - ','descri','TRUE','FacetSelect' , null, '','id_tes = '. $form['id_tes'].' ');		
		} else { //se non c'è l'ordine seleziono l'articolo da artico      
?>
			<!-- Antonio Germani > inserimento articolo	con autocomplete dalla tabella artico-->
			<tr> 
			<td class="FacetFieldCaptionTD"><?php echo $script_transl['9']; ?> </td>
			<td colspan="2" class="FacetDataTD">
			<?php
			if ($toDo == "update") {
				echo $form['codart'];?>
				<input type="hidden" name="codart" Value="<?php echo $form['codart']; ?>"/>
				<input type="hidden" name="cosear" Value="<?php echo $form['cosear']; ?>"/>
				<?php
			} else {
				$select_artico = new selectartico("codart");
				$select_artico->addSelected($form['codart']);			
				$select_artico->output(substr($form['cosear'], 0, 20));
			}
		}	
	echo '<input type="hidden" name="lot_or_serial" value="' . $resartico['lot_or_serial'] . '"/>';
	
    if ($resartico['good_or_service'] == 2) { // se è un articolo composto
?>
		<div class="container-fluid">
		<div class="row" style="margin-left: 0px;">
		<div class="col-sm-12" align="center" style="border:1px solid red">Articolo composto
		</div>
		</div>
		<?php
        if ($toDo == "update") { //se UPDATE
            // se ci sono, visualizzo i componenti; NON sarà, però,  possibile modificarli        
            if (isset($result7)) {
                while ($row = $result7->fetch_assoc()) {
?>
					<div class="row" style="margin-left: 0px;">
						<div class="col-sm-3 "  style="background-color:lightcyan;"><?php echo $row['artico']; ?>
						</div>
						<div class="col-sm-4 "  style="background-color:lightcyan;"><?php echo "Q.tà usata: ", gaz_format_quantity($row['quanti'], 0, $admin_aziend['decimal_quantity']); ?>
						</div>
						<?php
                    if (intval($row['id_lotmag']) > 0) {
?>
							<div class="col-sm-4 "  style="background-color:lightcyan;"><?php echo "id lotto: ", $row['id_lotmag']; ?>
							<?php
                    } else {
?>
							<div class="col-sm-4 "  style="background-color:lightcyan;"><?php echo "articolo senza lotto "; ?>
							<?php
                    }
?>
						</div>
					</div> <!-- chiude row  -->
					<?php
                }
            }
        } else { // se INSERT
            $nc = 0; // numero componente
            $l = 0; // numero lotto componente
            while ($row = $rescompo->fetch_assoc()) { // creo gli input dei componenti visualizzandone anche disponibilità di magazzino
                if ($form['quantip'] > 0) {
                    $row['quantita_artico_base'] = $row['quantita_artico_base'] * $form['quantip'];
                    $mv = $gForm->getStockValue(false, $row['codice_artico_base']);
                    $magval = array_pop($mv); // controllo disponibilità in magazzino
                    if ($toDo == "update") { // se è un update riaggiungo la quantità utilizzata
                        $magval['q_g'] = $magval['q_g'] + $row['quantita_artico_base'];
                    }
?>
					<input type="hidden" name="artcomp<?php echo $nc; ?>" value="<?php echo $row['codice_artico_base']; ?>">
								
					<div class="row" style="margin-left: 0px;">
					<div class="col-sm-3 "  style="background-color:lightcyan;"><?php echo $row['codice_artico_base']; ?>
					</div>
					<div class="col-sm-4 "  style="background-color:lightcyan;"><?php echo "Necessari: ", gaz_format_quantity($row['quantita_artico_base'], 0, $admin_aziend['decimal_quantity']); ?>
					</div>
					<div class="col-sm-4 "  style="background-color:lightcyan;"><?php echo "Disponibili: ", gaz_format_quantity($magval['q_g'], 0, $admin_aziend['decimal_quantity']); ?>
					</div>
					<?php
                    if ($magval['q_g'] - $row['quantita_artico_base'] >= 0) { // giacenza sufficiente
                        ?>
						<input type="hidden" name="quanti_comp<?php echo $nc; ?>" value="<?php echo $row['quantita_artico_base']; ?>"> <!-- quantità utilizzata di ogni componente   -->
						<div class="col-sm-1" style="background-color:lightgreen;"> OK</div>
						<?php
                    } else { // giacenza insufficiente
                        ?>
						<input type="hidden" name="quanti_comp<?php echo $nc; ?>" value="ERRORE"> <!-- quantità insufficiente componente, ERRORE -->
						<div class="col-sm-1" style="background-color:red;"> KO</div>
						<?php
                    }
                    // Antonio Germani - inserimento lotti in uscita
                    $artico = gaz_dbi_get_row($gTables['artico'], "codice", $row['codice_artico_base']);
                    if ($artico['lot_or_serial'] == 1) { // se il componente prevede lotti
                        // PROBLEMA IN UPDATE non esclude il lotti, per questo motivo non sono modificabili
                        $lm->getAvailableLots($row['codice_artico_base']); // Antonio Germani - non è stato inserito il movimento di magazzino da escludere perché questa funzione ne accetta uno solo e, invece, potrebbero essere di più. E' questo il motivo per cui, in update, sono stati bloccati articolo, componenti, lotti e quantità.
                        $ld = $lm->divideLots($row['quantita_artico_base']);
                        $l = 0;
                        $form['lot_quanti'][$nc][$l] = 0;
                        if ($ld > 0) { // segnalo preventivamente l'errore
                            echo "ERRORE ne mancano: ", $ld, "<br>"; // >>>>> quantità insufficiente - metto come valore ERRORE così potrò ritrovarlo facilmente e annullo quanti lotti sono interessati per questo componente
                            ?>
							<input type="hidden" name="quanti<?php echo $nc, $l; ?>" value="ERRORE">
							<input type="hidden" name="q_lot_comp<?php echo $nc; ?>" value="">
							<?php
                        } else {
                            // ripartisco la quantità introdotta tra i vari lotti disponibili per l'articolo
                            foreach ($lm->divided as $k => $v) { // ciclo i lotti scelti da getAvailableLots
                                if ($v['qua'] >= 0.00001) {
                                    $form['id_lotmag'][$l] = $v['id']; // setto il lotto
                                    $form['quanti'][$l] = $v['qua']; // e la quantità in base al riparto
                                    $selected_lot = $lm->getLot($form['id_lotmag'][$l]);
                                    echo '<div><button class="btn btn-xs btn-success"  title="Lotto selezionato automaticamente" data-toggle="collapse" >' . $selected_lot['id'] . ' Lotto n.: ' . $selected_lot['identifier'] . ' Scadenza: ' . gaz_format_date($selected_lot['expiry']);
                                    echo ' - <i class="glyphicon glyphicon-tag"></i></button>';
                                    echo " Se ne utilizzerà: ", $form['quanti'][$l];
?>
									<input type="hidden" name="id_lotmag<?php echo $nc, $l; ?>" value="<?php echo $form['id_lotmag'][$l]; ?>">
									<input type="hidden" name="lot_quanti<?php echo $nc, $l; ?>" value="<?php echo $form['quanti'][$l]; ?>">
									<?php
                                    $l++;
?>
									<input type="hidden" name="q_lot_comp<?php echo $nc; ?>" value="<?php echo $l; ?>">
									<?php // q lot comp ha volutamente una unità in più per distinguerlo da quando è zero cioè nullo                                   
                                }
                            }
                        }
?>		
								</div>	
								<?php
                    } else { // se non prevede lotto azzero id_lotmag e q_lot_mag di $nc
                        echo '<input type="hidden" name="id_lotmag' . $nc . '" value="">';
                        echo '<input type="hidden" name="q_lot_comp' . $nc . '" value="">'; // non ci sono lotti per questo componente
                        echo " Componente senza lotto";
                    }
?>
					</div> <!-- chiude row  -->
				</div>								
				<?php
                    $nc = $nc + 1;
                }
            }
            echo '<input type="hidden" name="numcomp" value="' . $nc . '">'; // Antonio Germani - Nota bene: numcomp ha sempre una unità in più! Non l'ho tolta per distinguere se c'è un solo componente o nessuno.            
        }
		?>
		</div>
		<?php
    }
?>	
	</td>
</tr>
<!--- Antonio Germani - inserimento quantità  -->
<tr>
	<td class="FacetFieldCaptionTD"><?php echo $script_transl['15']; ?> </td>
	<td colspan="2" class="FacetDataTD">
	<?php
    if ($toDo == "update") {
        echo gaz_format_quantity($form['quantip'], 0, $admin_aziend['decimal_quantity']);
?>
			<input type="hidden" name="quantip" Value="<?php echo $form['quantip']; ?>"/>
			<?php
        if ($form['quantipord'] - $form['quantip'] > 0) {
            echo " Sono ancora da produrre: ", gaz_format_quantity($form['quantipord'] - $form['quantip'], 0, $admin_aziend['decimal_quantity']);
        }
        if ($form['quantipord'] - $form['quantip'] <= 0) {
            echo " La produzione per questo ordine è completata";
        }
    } else {
?>
				<input type="text" name="quantip" onchange="this.form.submit()" value="<?php echo $form['quantip']; ?>" />
				<?php
        // Antonio Germani - Visualizzo quantità prodotte e rimanenti
        if (($form['order']) > 0 && strlen($form['codart']) > 0) { // se c'è un ordine e c'è un articolo selezionato, controllo se è già stato prodotto
            
            if ($quantiprod > 0) { // se c'è stata già una produzione per questo articolo e per questo ordine
                echo "già prodotti:", $quantiprod;
                echo " <b> Ne servono ancora: ", gaz_format_quantity($form['quantipord'] - $quantiprod, 0, $admin_aziend['decimal_quantity']), "</b>";
            } else {
                echo "L'ordine ne richiede: ", gaz_format_quantity($form['quantipord'], 0, $admin_aziend['decimal_quantity']);
            }
            if ($form['quantipord'] - $quantiprod > 0) {
                $form['okprod'] = "ok";
            } else {
                $form['okprod'] = "";
            }
?>
					<input type="hidden" name="okprod" value="<?php echo $form['okprod']; ?>">
					<?php
        } else {
?>
				<input type="hidden" name="okprod" value="">
				<?php
        }
    }
?>
		<input type="hidden" name="id_movmag" value="<?php echo $form['id_movmag']; ?>">
	</td>
</tr>
<?php
} else { // se è produzione agricola
    print "<tr><td><input type=\"hidden\" name=\"order\" value=\"\">";
    print "<input type=\"hidden\" name=\"codart\" value=\"\">";
	print "<input type=\"hidden\" name=\"cosear\" value=\"\">";
	print "<input type=\"hidden\" name=\"coseor\" value=\"\">";
    print "<input type=\"hidden\" name=\"id_movmag\" value=\"\">";
    print "<input type=\"hidden\" name=\"quantip\" value=\"\"></td></tr>";
}
?>
<!--- Antonio Germani - inserimento descrizione  -->
<tr>
	<td class="FacetFieldCaptionTD"><?php echo $script_transl['2']; ?> </td>
	<td colspan="2" class="FacetDataTD">
		<textarea type="text" name="description" align="right" maxlength="255" cols="67" rows="3"><?php echo $form['description']; ?></textarea>
	</td>
</tr>
<?php
print "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[3]</td><td class=\"FacetDataTD\"><input type=\"text\" name=\"add_info\" value=\"" . $form['add_info'] . "\" maxlength=\"80\" size=\"80\" /></td></tr>\n";
// DATA inizio produzione
echo "<tr><td class=\"FacetFieldCaptionTD\">" . $script_transl[5] . "</td><td class=\"FacetDataTD\">\n";
echo "\t <select name=\"gioinp\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
for ($counter = 1;$counter <= 31;$counter++) {
    $selected = "";
    if ($counter == $form['gioinp']) $selected = "selected";
    echo "\t <option value=\"$counter\"  $selected >$counter</option>\n";
}
echo "\t </select>\n";
echo "\t <select name=\"mesinp\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
for ($counter = 1;$counter <= 12;$counter++) {
    $selected = "";
    if ($counter == $form['mesinp']) $selected = "selected";
    $nome_mese = ucwords(strftime("%B", mktime(0, 0, 0, $counter, 1, 0)));
    echo "\t <option value=\"$counter\"  $selected >$nome_mese</option>\n";
}
echo "\t </select>\n";
echo "\t <select name=\"anninp\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
for ($counter = date("Y") - 10;$counter <= date("Y") + 10;$counter++) {
    $selected = "";
    if ($counter == $form['anninp']) $selected = "selected";
    echo "\t <option value=\"$counter\"  $selected >$counter</option>\n";
}
echo "\t </select></td>\n";
// end data inizio produzione
// Antonio Germani > DURATA produzione
if ($form['order_type'] == "AGR") {
    print "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[6]</td>";
} else {
    print "<tr><td class=\"FacetFieldCaptionTD\">$script_transl[11]</td>";
}
print "<td class=\"FacetDataTD\"><input type=\"number\" name=\"day_of_validity\" min=\"0\" maxlength=\"3\" step=\"any\" size=\"3\" value=\"" . $form['day_of_validity'] . "\"  /></td></tr>\n";
/*Antonio Germani LUOGO di produzione  */
echo "<tr><td class=\"FacetFieldCaptionTD\">" . $script_transl[7] . "</td><td class=\"FacetDataTD\">\n";
		// SELECT luogo di produzione da campi
		$gForm->selectFromDB('campi', 'campo_impianto','codice', $form['campo_impianto'], 'codice', 1, ' - ','descri','TRUE','FacetSelect' , null, '');
echo "</td></tr>";
if ($form['order_type'] <> "AGR") { // input esclusi se produzione agricola
    // Antonio Germani selezione operai
    if ($toDo == "update") { // mantengo il codice staff memorizzato inizialmente nel data base
        echo '<tr><td>';
        for ($form['mov'] = 0;$form['mov'] <= $form['nmovdb'];++$form['mov']) {
            echo '<input type="hidden" name="staffdb' . $form['mov'] . '" value="' . $form['staffdb'][$form['mov']] . '">';
        }
        echo '</td></tr>';
    }
    for ($form['mov'] = 0;$form['mov'] <= $form['nmov'];++$form['mov']) {
        echo "<tr><td class=\"FacetFieldCaptionTD\">" . $script_transl[10] . "</td><td class=\"FacetDataTD\">\n";
		
		// SELECT Operaio da staff con acquisizione nome da clfoco
		$gForm->selectFrom2DB('staff','clfoco','codice','descri','staff'. $form['mov'],'id_staff', $form['staff'][$form['mov']],'', 1, ' - ','id_clfoco','TRUE','FacetSelect' , null, '');
		
        if ($form['staff'][$form['mov']] > 0) {
            echo "<input type=\"submit\" name=\"add_staff\" value=\"" . $script_transl[19] . "\">\n";
        }
        if ($form['mov'] > 0 && $form['mov'] > $form['nmovdb']) { // se è update non si possono togliere gli operai già memorizzati nel database
            echo "<input type=\"submit\" title=\"Togli ultimo operaio\" name=\"Del_mov\" value=\"X\">\n";
        }
    }
    $form['mov'] = $form['nmov'];
    echo "<input type=\"hidden\" name=\"nmovdb\" value=\"" . $form['nmovdb'] . "\">\n";
    echo "<input type=\"hidden\" name=\"nmov\" value=\"" . $form['nmov'] . "\">\n</td></tr>";
    // Antonio Germani > Inizio LOTTO in entrata o creazione nuovo
    if (intval($form['lot_or_serial']) == 1) { // se l'articolo prevede il lotto apro la gestione lotti nel form       
?>	  
		<tr><td class="FacetFieldCaptionTD"><?php echo $script_transl[13]; ?></td>
		<td class="FacetDataTD" >
		<input type="hidden" name="filename" value="<?php echo $form['filename']; ?>">
		<input type="hidden" name="id_lotmag" value="<?php echo $form['id_lotmag']; ?>">
<?php
        if (strlen($form['filename']) == 0) {
            echo '<div><button class="btn btn-xs btn-danger" type="image" data-toggle="collapse" href="#lm_dialog">' . 'Inserire nuovo certificato' . ' ' . '<i class="glyphicon glyphicon-tag"></i>' . '</button></div>';
        } else {
            echo '<div><button class="btn btn-xs btn-success" type="image" data-toggle="collapse" href="#lm_dialog">' . $form['filename'] . ' ' . '<i class="glyphicon glyphicon-tag"></i>' . '</button>';
            echo '</div>';
        }
        if (strlen($form['identifier']) == 0) {
            echo '<div><button class="btn btn-xs btn-danger" type="image" data-toggle="collapse" href="#lm_dialog_lot">' . 'Inserire nuovo Lotto' . ' ' . '<i class="glyphicon glyphicon-tag"></i></button></div>';
        } else {
            if (intval($form['expiry']) > 0) {
                echo '<div><button class="btn btn-xs btn-success" type="image" data-toggle="collapse" href="#lm_dialog_lot">' . $form['identifier'] . ' ' . gaz_format_date($form['expiry']) . '<i class="glyphicon glyphicon-tag"></i></button></div>';
            } else {
                echo '<div><button class="btn btn-xs btn-success" type="image" data-toggle="collapse" href="#lm_dialog_lot" >' . $form['identifier'] . '<i class="glyphicon glyphicon-tag" ></i></button></div>';
            }
        }
        echo '<div id="lm_dialog" class="collapse" >
                        <div class="form-group">
                          <div>';
?>
               <input type="file" onchange="this.form.submit();" name="docfile_">
			 </div>
		     </div>
             </div>
				<?php
        echo '<div id="lm_dialog_lot" class="collapse" >
                        <div class="form-group">
                          <div>';
        echo '<label>' . "Numero: " . '</label><input type="text" name="identifier" value="' . $form['identifier'] . '" >';
        echo "<br>";
        echo '<label>' . 'Scadenza: ' . ' </label><input class="datepicker" type="text" onchange="this.form.submit();" name="expiry"  value="' . $form['expiry'] . '"></div></div></div>';
    } else {
        echo '<tr><td><input type="hidden" name="filename" value="' . $form['filename'] . '">';
        echo '<input type="hidden" name="identifier" value="' . $form['identifier'] . '">';
        echo '<input type="hidden" name="id_lotmag" value="' . $form['id_lotmag'] . '">';
        echo '<input type="hidden" name="expiry" value="' . $form['expiry'] . '"></td></tr>';
    }
    // fine LOTTI in entrata   
} else { //se è produzione agricola
    print "<tr><td><input type=\"hidden\" name=\"nmov\" value=\"0\">";
    print "<input type=\"hidden\" name=\"nmovdb\" value=\"\">\n";
    print "<input type=\"hidden\" name=\"staff0\" value=\"\">\n";
    print "<input type=\"hidden\" name=\"filename\" value=\"\">\n";
    print "<input type=\"hidden\" name=\"expiry\" value=\"\">\n";
    print "<input type=\"hidden\" name=\"identifier\" value=\"\">\n";
    print "<input type=\"hidden\" name=\"id_lotmag\" value=\"\">\n";
    print "<input type=\"hidden\" name=\"lot_or_serial\" value=\"\"></td></tr>";
}
if ($popup <> 1) {
    //ANNULLA/RESET NON FUNZIONA DA RIVEDERE > print "<tr><td class=\"FacetFieldCaptionTD\"><input type=\"reset\" name=\"Cancel\" value=\"".$script_transl['cancel']."\">\n";
    print "<tr><td class=\"FacetDataTD\" align=\"right\">\n";
    print "<input type=\"submit\" name=\"Return\" value=\"" . $script_transl['return'] . "\">\n";
} else {
    print "<tr><td>";
}
if ($toDo == 'update') {
    print '<input type="submit" accesskey="m" name="ins" id="preventDuplicate" onClick="chkSubmit();" value="' . strtoupper($script_transl['update']) . '!">';
} else {
    print '<input type="submit" accesskey="i" name="ins" id="preventDuplicate" onClick="chkSubmit();" value="' . strtoupper($script_transl['insert']) . '!">';
}
print "</td></tr></table>\n";
?>
</form>
<?php
require ("../../library/include/footer.php");
?>