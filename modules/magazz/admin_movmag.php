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
  scriva   alla   Free  Software Foundation,  Inc.,   59
  Temple Place, Suite 330, Boston, MA 02111-1307 USA Stati Uniti.
  --------------------------------------------------------------------------
 */
require("../../library/include/datlib.inc.php");
require ("../../modules/vendit/lib.function.php");
$admin_aziend = checkAdmin();
$msg = "";
$lm = new lotmag;
$gForm = new magazzForm;

if (!isset($_POST['ritorno'])) {
    $_POST['ritorno'] = $_SERVER['HTTP_REFERER'];
}

$form = array();

function getItemPrice($item, $partner = 0) {
    global $admin_aziend, $gTables;
    $artico = gaz_dbi_get_row($gTables['artico'], 'codice', $item);
    if ($partner > 0) {
        $partner = gaz_dbi_get_row($gTables['clfoco'], 'codice', $partner);
        $list = $partner['listin'];
        if (substr($partner['codice'], 0, 3) == $admin_aziend['mascli'] && $list > 0 && $list <= 3) {
            $price = $artico["preve$list"];
        } else {
            $price = $artico["preacq"];
        }
        $sconto = $partner['sconto'];
    } else { // prezzo articolo
        $sconto = 0;
        $price = $artico["preve1"];
    }
    return CalcolaImportoRigo(1, $price, $sconto, $admin_aziend['decimal_price']);
}

if ((isset($_POST['Update'])) or ( isset($_GET['Update']))) {
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

if (!isset($_POST['Update']) and isset($_GET['Update'])) { //se e' il primo accesso per UPDATE
    $form['hidden_req'] = '';
    //recupero il movimento
    $result = gaz_dbi_get_row($gTables['movmag'], "id_mov", $_GET['id_mov']);
    $form['id_mov'] = $result['id_mov'];
	$form['type_mov'] = $result['type_mov'];
    $form['id_rif'] = $result['id_rif'];
    $form['caumag'] = $result['caumag'];
    $form['operat'] = $result['operat'];
    $form['gioreg'] = substr($result['datreg'], 8, 2);
    $form['mesreg'] = substr($result['datreg'], 5, 2);
    $form['annreg'] = substr($result['datreg'], 0, 4);
    $form['clfoco'] = $result['clfoco'];
    if (!empty($form['caumag'])) { //controllo quale partner prevede la causale
        $rs_causal = gaz_dbi_get_row($gTables['caumag'], "codice", $form['caumag']);
        $form['clorfo'] = $rs_causal['clifor']; //cliente, fornitore o entrambi
    } else {
        $form['clorfo'] = 0; // entrambi
    }
    $form['tipdoc'] = $result['tipdoc'];
    $form['desdoc'] = $result['desdoc'];
    $form['scochi'] = $result['scochi'];
    $form['giodoc'] = substr($result['datdoc'], 8, 2);
    $form['mesdoc'] = substr($result['datdoc'], 5, 2);
    $form['anndoc'] = substr($result['datdoc'], 0, 4);
	$form['id_lotmag'] = $result['id_lotmag'];
    $form['artico'] = $result['artico'];
	$item_artico = gaz_dbi_get_row($gTables['artico'], "codice", $form['artico']);
	if ($item_artico['lot_or_serial']==1){ 
		$result_lotmag = gaz_dbi_get_row($gTables['lotmag'], "id", $result['id_lotmag']);
		$form['identifier'] = $result_lotmag['identifier'];
		$form['expiry'] = $result_lotmag['expiry'];
		
	}
	$form['lot_or_serial']=$item_artico['lot_or_serial'];
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
    $form['quanti'] = gaz_format_quantity($result['quanti'], 0, $admin_aziend['decimal_quantity']);
    $form['prezzo'] = number_format($result['prezzo'], $admin_aziend['decimal_price'], '.', '');
    $form['scorig'] = $result['scorig'];
    $form['status'] = $result['status'];
    $form['search_partner'] = "";
    $form['search_item'] = "";
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
	
	
} elseif (isset($_POST['Insert']) or isset($_POST['Update'])) {   //      se non e' il primo accesso
    $form['hidden_req'] = htmlentities($_POST['hidden_req']);
    //ricarico i registri per il form facendo gli eventuali parsing
    $form['id_mov'] = intval($_POST['id_mov']);
	//$form['type_mov'] = intval ($_POST['type_mov']);
    $form['id_rif'] = intval($_POST['id_rif']);
    $form['caumag'] = intval($_POST['caumag']);
    $form['operat'] = intval($_POST['operat']);
    $form['gioreg'] = intval($_POST['gioreg']);
    $form['mesreg'] = intval($_POST['mesreg']);
    $form['annreg'] = intval($_POST['annreg']);
    $form['clfoco'] = intval($_POST['clfoco']);
    $form['clorfo'] = intval($_POST['clorfo']); //cliente, fornitore o entrambi
    $form['tipdoc'] = intval($_POST['tipdoc']);
    $form['desdoc'] = substr($_POST['desdoc'], 0, 50);
    $form['giodoc'] = intval($_POST['giodoc']);
    $form['mesdoc'] = intval($_POST['mesdoc']);
    $form['anndoc'] = intval($_POST['anndoc']);
    $form['scochi'] = floatval(preg_replace("/\,/", '.', $_POST['scochi']));
	$form['id_lotmag'] = $_POST['id_lotmag'];
    $form['artico'] = $_POST['artico'];
	$form['lot_or_serial']=$_POST['lot_or_serial'];
	if (strlen($form['artico'])>0) {
		$item_artico = gaz_dbi_get_row($gTables['artico'], "codice", $form['artico']);
		$print_unimis =  $item_artico['unimis'];
		if (isset($_POST['expiry'])){
			$form['filename'] = $_POST['filename'];
			$form['identifier'] = $_POST['identifier'];
			$form['expiry'] = $_POST['expiry'];
		}
	}
    $form['quanti'] = gaz_format_quantity($_POST['quanti'], 0, $admin_aziend['decimal_quantity']);
    $form['prezzo'] = number_format(preg_replace("/\,/", '.', $_POST['prezzo']), $admin_aziend['decimal_price'], '.', '');
    $form['scorig'] = floatval(preg_replace("/\,/", '.', $_POST['scorig']));
    $form['status'] = substr($_POST['status'], 0, 10);
    $form['search_partner'] = $_POST['search_partner'];
    $form['search_item'] = $_POST['search_item'];
    // Se viene inviata la richiesta di conferma della causale la carico con le relative contropartite...
    /** ENRICO FEDELE */
    /* Con button non funziona _x */
    //if (isset($_POST['inscau_x'])) {
    /** ENRICO FEDELE */
    if (isset($_POST['inscau'])) {
        $causa = gaz_dbi_get_row($gTables['caumag'], "codice", $form['caumag']);
        $form['operat'] = $causa['operat'];
        $form['clorfo'] = $causa['clifor']; //cliente, fornitore o entrambi
        if (($causa['clifor'] < 0 and substr($form['clfoco'], 0, 3) == $admin_aziend['masfor']) or ( $causa['clifor'] > 0 and substr($form['clfoco'], 0, 3) == $admin_aziend['mascli'])) {
            $form['clfoco'] = 0;
            $form['search_partner'] = "";
        }
        if ($causa['insdoc'] == 0) {//se la nuova causale non prevede i dati del documento
            $form['tipdoc'] = "";
            $form['desdoc'] = "";
            $form['giodoc'] = date("d");
            $form['mesdoc'] = date("m");
            $form['anndoc'] = date("Y");
            $form['scochi'] = "";
            $form['id_rif'] = 0;
        }
    }
    if (isset($_POST['newpartner'])) {
        $anagrafica = new Anagrafica();
        $partner = $anagrafica->getPartner($_POST['clfoco']);
        $form['search_partner'] = substr($partner['ragso1'], 0, 4);
        $form['clfoco'] = 0;
    }
    if (isset($_POST['newitem'])) {
        $result_newart = gaz_dbi_get_row($gTables['artico'], "codice", $_POST['artico']);
        $form['search_item'] = substr($result_newart['codice'], 0, 4);
        $form['artico'] = "";
    }
    if (isset($_POST['Return'])) {
        header("Location: " . $_POST['ritorno']);
        exit;
    }
    if ($_POST['hidden_req'] == 'new_price') {
        $form['prezzo'] = getItemPrice($form['artico'], $form['clfoco']);
        $form['hidden_req'] = '';
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
	
	// controllo e WARNING su quantità e lotti
	if (strlen($form['artico'])>0 && $form['quanti']>0 && $form['operat']==-1){
		$mv = $gForm->getStockValue(false, $form['artico']);
		$magval = array_pop($mv); // controllo disponibilità in magazzino
		if ($toDo == "update") { // se è un update riaggiungo la quantità utilizzata
			$check_qta = gaz_dbi_get_row($gTables['movmag'], "id_mov", $_GET['id_mov']);
			$magval['q_g'] = $magval['q_g'] + $check_qta['quanti']; 
		}
		if ($magval['q_g']<$form['quanti']){
			?>
			<div class="alert alert-warning alert-dismissible">
			<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
			<strong>Warning!</strong> quantità articolo non sufficiente! Se si conferma si creerà una quantità negativa!
			</div>
			<?php
		}
		
	}
	
    if (!empty($_POST['Insert'])) {        //          Se viene inviata la richiesta di conferma totale ...
        $utsreg = mktime(0, 0, 0, $form['mesreg'], $form['gioreg'], $form['annreg']);
        $utsdoc = mktime(0, 0, 0, $form['mesdoc'], $form['giodoc'], $form['anndoc']);
        if (!checkdate($form['mesreg'], $form['gioreg'], $form['annreg']))
            $msg .= "16+";
        if (!checkdate($form['mesdoc'], $form['giodoc'], $form['anndoc']))
            $msg .= "15+";
        if ($utsdoc > $utsreg) {
            $msg .= "17+";
        }
        if (empty($form['artico'])) {  //manca l'articolo
            $msg .= "18+";
        }
        if ($form['quanti'] == 0) {  //la quantit� � zero
            $msg .= "19+";
        }
		if (isset($_GET['id_mov']) && intval($_GET['id_mov'])>0){
		$result = gaz_dbi_get_row($gTables['movmag'], "id_mov", $_GET['id_mov']);
		if ($result['type_mov']<>0){ //Antonio Germani è un movimento che va gestito esclusivamente con il modulo Camp
			$msg .="20+";
		}
		}
	
        if (empty($msg)) {    //        nessun errore        SALVATAGGIO database
			// Antonio Germani - inizio salvataggio lotto
			if ($form['lot_or_serial']==1){ //se è previsto un lotto 
			
				if (strlen($form['identifier']) == 0) { // se non è stato digitato un lotto lo inserisco d'ufficio come data e ora
                    $form['identifier'] = date("Ymd Hms");
                }
                if (strlen($form['expiry']) == 0) { // se non c'è la scadenza la inserisco a zero d'ufficio
                    $form['expiry'] = "0000-00-00 00:00:00";
                }
				$form['identifier'] = (empty($form['identifier'])) ? '' : filter_var($form['identifier'], FILTER_SANITIZE_STRING); // ne ripulisco il numero da caratteri dannosi
				// prendo l'ID del rigo di movmag
				if ($toDo=="update") {
					$id_movmag=$_GET['id_mov'];
				} else {
					$query = "SHOW TABLE STATUS LIKE '" . $gTables['movmag'] . "'";
                    unset($row);
                    $result = gaz_dbi_query($query);
                    $row = $result->fetch_assoc();
                    $id_movmag = $row['Auto_increment']; 
				}
				
				$check_lot= gaz_dbi_query("SELECT id FROM " . $gTables['lotmag'] . " WHERE identifier = '" . $form['identifier'] . "' AND codart = '".$form['artico']."'");// vedo se il lotto inserito nel form è nuovo o esiste già
					
				if ($check_lot->num_rows > 0){ // se esiste già
					$row = $check_lot->fetch_assoc();
					$form['id_lotmag']=$row['id']; // ne prendo l'id che andrò a memorizzare nel movimento di magazzino
				} else { // se non esiste 
					if ($toDo=="insert") { // se è insert creo il rigo lotto memorizzandolo nella tabella lotmag
						$query = "SHOW TABLE STATUS LIKE '" . $gTables['lotmag'] . "'";
						unset($check_lot);
						$check_lot = gaz_dbi_query($query);
						$row = $check_lot->fetch_assoc();
						$form['id_lotmag'] = $row['Auto_increment']; // trovo l'ID che avrà il lotto e  salvo il lotto
						gaz_dbi_query("INSERT INTO " . $gTables['lotmag'] . "(codart,id_movmag,identifier,expiry) VALUES ('" . $form['artico'] . "','" . $id_movmag. "','" . $form['identifier'] . "','" . $form['expiry'] . "')");
					} else { // se è update modifico il rigo lotto
						gaz_dbi_query("UPDATE " . $gTables['lotmag'] . " SET codart = '" . $form['codart'] . "' , identifier = '" . $form['identifier'] . "' , expiry = '" . $form['expiry'] . "' WHERE id = '" . $form['id_lotmag'] . "'");
						
					}
				}				 
				 
				// Antonio Germani - salvo documento/CERTIFICATO del lotto
				if (substr($form['filename'], 0, 7) <> 'lotmag_') { // se è stato cambiato il file, cioè il nome non inizia con lotmag e, quindi, anche se è un nuovo insert
                    if (!empty($form['filename'])) { // e se ha un nome impostato nel form
                        $tmp_file = "../../data/files/tmp/" . $admin_aziend['adminid'] . '_' . $admin_aziend['company_id'] . '_' . $form['filename'];
                        // sposto il file nella cartella definitiva, rinominandolo e cancellandolo dalla temporanea
                        $fd = pathinfo($form['filename']);
                        rename($tmp_file, "../../data/files/" . $admin_aziend['company_id'] . "/lotmag_" . $form['id_lotmag'] . '.' . $fd['extension']);
                    }
                } // altrimenti se il file non è cambiato, anche se è update, non faccio nulla
			} else {
				$form['id_lotmag']=0;
			}		
	
            $upd_mm = new magazzForm;
            //formatto le date
            $form['datreg'] = $form['annreg'] . "-" . $form['mesreg'] . "-" . $form['gioreg'];
            $form['datdoc'] = $form['anndoc'] . "-" . $form['mesdoc'] . "-" . $form['giodoc'];
            $new_caumag = gaz_dbi_get_row($gTables['caumag'], "codice", $form['caumag']);
            if (!empty($form['artico'])) {
                $upd_mm->uploadMag($form['id_rif'], $form['tipdoc'], 0, // numdoc � in desdoc
                        0, // seziva � in desdoc
                        $form['datdoc'], $form['clfoco'], $form['scochi'], $form['caumag'], $form['artico'], $form['quanti'], $form['prezzo'], $form['scorig'], $form['id_mov'], $admin_aziend['stock_eval_method'], array('datreg' => $form['datreg'], 'operat' => $form['operat'], 'desdoc' => $form['desdoc'])
                );
				// aggiorno id_lotmag nel rigo di movmag
				// e correggo il tipdoc che deve essere MAG
				$query = "UPDATE " . $gTables['movmag'] . " SET id_lotmag = '" . $form['id_lotmag'] . "' , tipdoc = 'MAG' WHERE id_mov ='" . $id_movmag . "'";
                gaz_dbi_query($query);
            }
            header("Location:report_movmag.php");
            exit;
        }
    }
} elseif (!isset($_POST['Insert'])) { //se e' il primo accesso per INSERT
    $form['hidden_req'] = '';
    //registri per il form della testata
    $form['id_mov'] = 0;
    $form['gioreg'] = date("d");
    $form['mesreg'] = date("m");
    $form['annreg'] = date("Y");
    $form['caumag'] = "";
    $form['operat'] = 0;
    $form['clfoco'] = 0;
    $form['clorfo'] = 0;
    $form['tipdoc'] = "";
    $form['desdoc'] = "";
    $form['giodoc'] = date("d");
    $form['mesdoc'] = date("m");
    $form['anndoc'] = date("Y");
    $form['scochi'] = 0;
    $form['artico'] = "";
	$form['lot_or_serial']="";
	$form['filename'] ="";
	$form['id_lotmag'] ="";
	$form['identifier'] ="";
	$form['expiry'] ="";
    $form['quanti'] = 0;
    $form['prezzo'] = 0;
    $form['scorig'] = 0;
    $form['status'] = "";
    $form['search_partner'] = "";
    $form['search_item'] = "";
    $form['id_rif'] = 0;
}

require("../../library/include/header.php");
$script_transl = HeadMain();
require("./lang." . $admin_aziend['lang'] . ".php");
if ($form['id_mov'] > 0) {
    $title = ucfirst($script_transl[$toDo] . $script_transl[0]) . " n." . $form['id_mov'];
} else {
    $title = ucfirst($script_transl[$toDo] . $script_transl[0]);
}
?>
<SCRIPT LANGUAGE="JavaScript">
    function CalcolaImportoRigo()
    {
        var p = document.myform.prezzo.value.toString().replace(/\,/g, '.') * 1;
        if (isNaN(p)) {
            p = 0;
        }
        var q = document.myform.quanti.value.toString().replace(/\,/g, '.') * 1;
        if (isNaN(q)) {
            q = 0;
        }
        var s = document.myform.scorig.value.toString().replace(/\,/g, '.') * 1;
        if (isNaN(s)) {
            s = 0;
        }
        var c = document.myform.scochi.value.toString().replace(/\,/g, '.') * 1;
        if (isNaN(c)) {
            c = 0;
        }
        var sommarigo = p * q - p * q * s / 100;
        var sommatotale = sommarigo - sommarigo * c / 100;
        return((Math.round(sommatotale * 100) / 100).toFixed(2));
    }
	
  $(function() {
    $( ".datepicker" ).datepicker({ dateFormat: 'yy-mm-dd' });
	
});

</SCRIPT>

<?php
echo "<form method=\"POST\" name=\"myform\" enctype=\"multipart/form-data\" >";
echo "<input type=\"hidden\" name=\"" . ucfirst($toDo) . "\" value=\"\">\n";
echo "<input type=\"hidden\" value=\"" . $form['hidden_req'] . "\" name=\"hidden_req\" />\n";
echo "<input type=\"hidden\" name=\"ritorno\" value=\"" . $_POST['ritorno'] . "\">\n";
echo "<input type=\"hidden\" name=\"id_mov\" value=\"" . $form['id_mov'] . "\">\n";
echo "<input type=\"hidden\" name=\"id_rif\" value=\"" . $form['id_rif'] . "\">\n";
echo "<input type=\"hidden\" name=\"tipdoc\" value=\"" . $form['tipdoc'] . "\">\n";
echo "<input type=\"hidden\" name=\"status\" value=\"" . $form['status'] . "\">\n";
echo "<div align=\"center\" class=\"FacetFormHeaderFont\">$title</div>\n";
$importo_rigo = CalcolaImportoRigo($form['quanti'], $form['prezzo'], $form['scorig']);
$importo_totale = CalcolaImportoRigo(1, $importo_rigo, $form['scochi']);
echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"FacetFormTABLE\" align=\"center\">\n";

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
    echo '<tr><td colspan="5" class="FacetDataTDred">' . $message . "</td></tr>\n";
}
echo "<tr><td class=\"FacetFieldCaptionTD\">" . $script_transl[1] . "</td><td class=\"FacetDataTD\">\n";
echo "\t <select name=\"gioreg\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
for ($counter = 1; $counter <= 31; $counter++) {
    $selected = "";
    if ($counter == $form['gioreg'])
        $selected = "selected";
    echo "\t <option value=\"$counter\"  $selected >$counter</option>\n";
}
echo "\t </select>\n";
echo "\t <select name=\"mesreg\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
for ($counter = 1; $counter <= 12; $counter++) {
    $selected = "";
    if ($counter == $form['mesreg'])
        $selected = "selected";
    $nome_mese = ucwords(strftime("%B", mktime(0, 0, 0, $counter, 1, 0)));
    echo "\t <option value=\"$counter\"  $selected >$nome_mese</option>\n";
}
echo "\t </select>\n";
echo "\t <select name=\"annreg\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
for ($counter = date("Y") - 10; $counter <= date("Y") + 10; $counter++) {
    $selected = "";
    if ($counter == $form['annreg'])
        $selected = "selected";
    echo "\t <option value=\"$counter\"  $selected >$counter</option>\n";
}
echo "\t </select></td>\n";
echo "<td class=\"FacetFieldCaptionTD\">" . $script_transl[2] . "</td><td class=\"FacetDataTD\">\n";
echo "<select name=\"caumag\" class=\"FacetSelect\">\n";
echo "<option value=\"\">-------------</option>\n";
$result = gaz_dbi_dyn_query("*", $gTables['caumag'], " 1 ", "codice desc, descri asc");
while ($row = gaz_dbi_fetch_array($result)) {
    $selected = "";
    if ($form["caumag"] == $row['codice']) {
        $selected = " selected ";
    }
    echo "<option value=\"" . $row['codice'] . "\"" . $selected . ">" . $row['codice'] . " - " . $row['descri'] . "</option>\n";
}
/* echo "</select> &nbsp;<input type=\"image\" name=\"inscau\" src=\"../../library/images/vbut.gif\" title=\"".$script_transl['submit']." !\"></td></tr>\n"; */

/** ENRICO FEDELE */
/* glyph-icon */
echo '  </select>&nbsp;<button type="submit" class="btn btn-default btn-sm" name="inscau" title="' . $script_transl['submit'] . '!"><i class="glyphicon glyphicon-ok"></i></button>
		</td>
	   </tr>';
/** ENRICO FEDELE */
echo "<tr><td class=\"FacetFieldCaptionTD\">" . $script_transl[3] . "&hArr;" . $script_transl[4] . "</td><td class=\"FacetDataTD\">\n";
$messaggio = "";
$ric_mastro = substr($form['clfoco'], 0, 3);
echo "\t<input type=\"hidden\" name=\"clfoco\" value=\"" . $form['clfoco'] . "\">\n";
echo "\t<input type=\"hidden\" name=\"clorfo\" value=\"" . $form['clorfo'] . "\">\n";
$rs_partner = "(codice between " . $admin_aziend['mascli'] . "000001 and " . $admin_aziend['mascli'] . "999999 or codice between " . $admin_aziend['masfor'] . "000001 and " . $admin_aziend['masfor'] . "999999 )";
if ($form['clorfo'] < 0) { // cliente
    $rs_partner = "(codice between " . $admin_aziend['mascli'] . "000001 and " . $admin_aziend['mascli'] . "999999 )";
} elseif ($form['clorfo'] > 0) {// fornitore
    $rs_partner = "(codice between " . $admin_aziend['masfor'] . "000001 and " . $admin_aziend['masfor'] . "999999 )";
}
if ($form['clfoco'] == 0) {
    if (strlen($form['search_partner']) >= 2) {

        $anagrafica = new Anagrafica();
        $partner = $anagrafica->queryPartners("*", $rs_partner . " and ragso1 like '" . addslashes($form['search_partner']) . "%'", "codice asc, ragso1 asc");
        if (sizeof($partner) > 0) {
            $clifor = $script_transl[5];
            echo "\t<select name=\"clfoco\" class=\"FacetSelect\" onchange=\"this.form.hidden_req.value='new_price'; this.form.submit();\">\n";
            echo "<option value=\"000000000\"> ---------- </option>";
			foreach ($partner AS $key => $row) {
                $selected = "";
                if ($row["codice"] == $form['clfoco']) {
                    $selected = "selected";
                }
                if (substr($row["codice"], 0, 3) == $admin_aziend['masfor']) {
                    $clifor = $script_transl[6];
                }
                echo "\t\t <option value=\"" . $row["codice"] . "\" $selected >" . $row["ragso1"] . " " . $row["citspe"] . "&nbsp;($clifor)</option>\n";
            }
            echo "\t </select>\n";
        } else {
            $messaggio = ucfirst($script_transl['notfound']) . " !";
        }
    } else {
        $messaggio = ucfirst($script_transl['minins']) . " 2 " . $script_transl['charat'] . "!";
    }
    echo "\t<input type=\"text\" name=\"search_partner\" accesskey=\"e\" value=\"" . $form['search_partner'] . "\" maxlength=\"15\" size=\"9\" class=\"FacetInput\">\n";
    echo $messaggio;
    //echo "\t <input type=\"image\" align=\"middle\" accesskey=\"c\" name=\"search\" src=\"../../library/images/cerbut.gif\">\n";
    /** ENRICO FEDELE */
    /* Cambio l'aspetto del pulsante per renderlo bootstrap, con glyphicon */
    echo '&nbsp;<button type="submit" class="btn btn-default btn-sm" name="search" accesskey="c"><i class="glyphicon glyphicon-search"></i></button>';
    /** ENRICO FEDELE */
} else {
    $anagrafica = new Anagrafica();
    $partner = $anagrafica->getPartner($form['clfoco']);
    echo "<input type=\"submit\" value=\"" . substr($partner['ragso1'], 0, 30) . "\" name=\"newpartner\" title=\"" . ucfirst($script_transl['update']) . "!\">\n";
    echo "\t<input type=\"hidden\" name=\"clfoco\" value=\"" . $form['clfoco'] . "\">\n";
    echo "\t<input type=\"hidden\" name=\"search_partner\" value=\"" . $form['search_partner'] . "\">\n";
}
if (substr($form["clfoco"], 0, 3) == $admin_aziend['masfor']) {
    $unimis = "uniacq";
} else {
    $unimis = "unimis";
}
echo "</td><td class=\"FacetFieldCaptionTD\">" . $script_transl[8] . "</td><td class=\"FacetDataTD\">\n";
echo "\t <select name=\"giodoc\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
for ($counter = 1; $counter <= 31; $counter++) {
    $selected = "";
    if ($counter == $form['giodoc'])
        $selected = "selected";
    echo "\t <option value=\"$counter\"  $selected >$counter</option>\n";
}
echo "\t </select>\n";

echo "\t <select name=\"mesdoc\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
for ($counter = 1; $counter <= 12; $counter++) {
    $selected = "";
    if ($counter == $form['mesdoc'])
        $selected = "selected";
    $nome_mese = ucwords(strftime("%B", mktime(0, 0, 0, $counter, 1, 0)));
    echo "\t <option value=\"$counter\"  $selected >$nome_mese</option>\n";
}
echo "\t </select>\n";
echo "\t <select name=\"anndoc\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
for ($counter = date("Y") - 10; $counter <= date("Y") + 10; $counter++) {
    $selected = "";
    if ($counter == $form['anndoc'])
        $selected = "selected";
    echo "\t <option value=\"$counter\"  $selected >$counter</option>\n";
}
echo "\t </select></td></tr>\n";
echo "<tr><td class=\"FacetFieldCaptionTD\">" . $script_transl[9] . "</td><td class=\"FacetDataTD\" ><input type=\"text\" value=\"" . $form['desdoc'] . "\" maxlength=\"50\" size=\"35\" name=\"desdoc\"></td>";
echo "<td class=\"FacetFieldCaptionTD\">" . $script_transl[10] . "</td><td class=\"FacetDataTD\" ><input type=\"text\" value=\"" . $form['scochi'] . "\" maxlength=\"5\" size=\"5\" name=\"scochi\" onChange=\"this.form.total.value=CalcolaImportoRigo();\"> %</td></tr>";
echo "<tr><td class=\"FacetFieldCaptionTD\">" . $script_transl[7] . "</td><td class=\"FacetDataTD\">\n";
$messaggio = "";
$print_unimis = "";
$ric_mastro = substr($form['artico'], 0, 3);
echo "\t<input type=\"hidden\" name=\"artico\" value=\"" . $form['artico'] . "\">\n";
if ($form['artico'] == '') {
    if (strlen($form['search_item']) >= 1) {
        $result = gaz_dbi_dyn_query("*", $gTables['artico'], "codice like '" . $form['search_item'] . "%' ", "descri asc");
        if (gaz_dbi_num_rows($result) > 0) {
            echo "\t<select name=\"artico\" class=\"FacetSelect\" onchange=\"this.form.hidden_req.value='new_price'; this.form.submit();\">\n";
            echo "<option value=\"\"> -------- </option>";
            while ($row = gaz_dbi_fetch_array($result)) {
                $selected = "";
                if ($row["codice"] == $form['artico']) {
                    $selected = "selected";
                }
                echo "\t\t <option value=\"" . $row["codice"] . "\" $selected >" . $row["descri"] . "&nbsp;</option>\n";
            }
            echo "\t </select>\n";
        } else {
            $messaggio = ucfirst($script_transl['notfound']) . " !";
        }
    } else {
        $messaggio = ucfirst($script_transl['minins']) . " 1 " . $script_transl['charat'] . "!";
    }
    echo "\t<input type=\"text\" name=\"search_item\" accesskey=\"e\" value=\"" . $form['search_item'] . "\" maxlength=\"15\" size=\"9\" class=\"FacetInput\">\n";
    echo $messaggio;
    // echo "\t <input type=\"image\" align=\"middle\" accesskey=\"c\" name=\"search\" src=\"../../library/images/cerbut.gif\"></td>\n";
    /** ENRICO FEDELE */
    /* Cambio l'aspetto del pulsante per renderlo bootstrap, con glyphicon */
    echo '&nbsp;<button type="submit" class="btn btn-default btn-sm" name="search" accesskey="c"><i class="glyphicon glyphicon-search"></i></button>';
    /** ENRICO FEDELE */
} else {
    
    echo "<input type=\"submit\" value=\"" . substr($item_artico['descri'], 0, 30) . "\" name=\"newitem\" title=\"" . ucfirst($script_transl['update']) . "!\">\n ";
    echo "\t<input type=\"hidden\" name=\"artico\" value=\"" . $form['artico'] . "\">\n";
    echo "\t<input type=\"hidden\" name=\"search_item\" value=\"" . $form['search_item'] . "\">\n";
}

    // Antonio Germani > Inizio LOTTO in uscita o in entrata o creazione nuovo
if ($form['artico'] != "" && intval( $item_artico['lot_or_serial']) == 1) { // se l'articolo prevede il lotto apro la gestione lotti nel form 
	$form['lot_or_serial']=$item_artico['lot_or_serial'];
	?>	  
		<div class="FacetFieldCaptionTD"><?php echo $script_transl[21]; ?></div>
		<div class="FacetDataTD" >
		<input type="hidden" name="filename" value="<?php echo $form['filename']; ?>">
		<input type="hidden" name="id_lotmag" value="<?php echo $form['id_lotmag']; ?>">
		
		</div>
<?php
	if ($form['operat']==1 && $form['quanti']>0){ // se è carico di magazzino ed è impostata la quantità
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
		echo '<div id="lm_dialog" class="collapse" ><div class="form-group"><div>';
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
		if ($form['operat']==-1 && $form['quanti']>0){  // se è scarico e è stata impostata la quantità
		 	$lm->getAvailableLots($form['artico']); // Antonio Germani - 
			$ld = $lm->divideLots($form['quanti']);
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
			if (isset($form['id_lotmag']) && $form['id_lotmag'] > 0) { // Selezione manuale del lotto dopo quella iniziale
				echo "Lotto selezionato";
                $selected_lot = $lm->getLot($form['id_lotmag']);
                echo '<div><button class="btn btn-xs btn-success" title="Lotto selezionato. Cliccare per cambiare lotto" type="image"  data-toggle="collapse" href="#lm_dialog">' . $selected_lot['id'] . ' lotto n.:' . $selected_lot['identifier'];
                if (intval($form['expiry']) > 0) {
                    echo ' scadenza:' . gaz_format_date($selected_lot['expiry']);
                }
                echo ' - disponibili: ' . gaz_format_quantity($count[$selected_lot['identifier']]) . ' <i class="glyphicon glyphicon-tag"></i></button>';
				?>
				<input type="hidden" name="id_lotmag" value="<?php echo $selected_lot['id_lotmag']; ?>">
				<input type="hidden" name="identifier" value="<?php echo $selected_lot['identifier']; ?>">
				<input type="hidden" name="expiry" value="<?php echo $selected_lot['expiry']; ?>">
				<?php
				if ($form['quanti']>$count[$selected_lot['identifier']]) { // Se il lotto scelto non ha disponibilità sufficienti segnalo errore
					?>
					<div class="alert alert-warning alert-dismissible">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					<strong>Warning!</strong> <b>Quantità lotto non sufficiente!</b> </br>Se si conferma si creeranno incongruenze fra quantità e lotti! </br> Si consiglia di selezionare un lotto con sufficiente disponibilità</br> oppure di diminuire la quantità in uscita.
					</div>
					<?php
				}				
            } else { // selezione automatica INIZIALE  del lotto disponibile 								
				if (!isset($form['id_lotmag']) or (intval($form['id_lotmag'])==0)) { 
				
					foreach ($lm->divided as $k => $v) { // ciclo i lotti scelti da getAvailableLots
						
						if ($v['qua'] >= 0.00001) {
							if ($v['quanti'] >= $form['quanti']){ 
								$form['id_lotmag']= $v['id']; // al primo ciclo, cioè id lotto è zero, setto il lotto
								$selected_lot = $lm->getLot($form['id_lotmag']);
								
								echo '<div><button class="btn btn-xs btn-success"  title="Lotto selezionato automaticamente. Cliccare per cambiare lotto" data-toggle="collapse" href="#lm_dialog">' . $selected_lot['id'] . ' Lotto n.: ' . $selected_lot['identifier'] . ' Scadenza: ' . gaz_format_date($selected_lot['expiry']). ' disponibili:' . gaz_format_quantity($count[$selected_lot['identifier']]);
								echo '  <i class="glyphicon glyphicon-tag"></i></button>';
								?>
								<input type="hidden" name="id_lotmag" value="<?php echo $selected_lot['id_lotmag']; ?>">
								<input type="hidden" name="identifier" value="<?php echo $selected_lot['identifier']; ?>">
								<input type="hidden" name="expiry" value="<?php echo $selected_lot['expiry']; ?>">
								<?php 
								$l++; 
							}
						}	
					} 
				}
			}
			?>
			<!-- Antonio Germani - Cambio lotto  -->
			<div id="lm_dialog" class="collapse" >
			<?php				
			if ((count($lm->available) >= 1)) { 
				foreach ($lm->available as $v_lm) {
					if ($v_lm['id'] <> $form['id_lotmag']) { 
						echo '<div>Cambia con:<button class="btn btn-xs btn-warning" type="text" onclick="this.form.submit();" name="id_lotmag" value="'.$v_lm['id'].'">'
						. $v_lm['id']
						. ' lotto n.:' . $v_lm['identifier']
						. ' scadenza:' . gaz_format_date($v_lm['expiry'])
						. ' disponibili:' . gaz_format_quantity($count[$v_lm['identifier']])
						. '</button></div>';
					}
				}
			} else {
				echo '<div><button class="btn btn-xs btn-danger" type="image" >Non ci sono disponibili altri lotti.</button></div>';
			}
			?>
			</div>
			<?php		
		} else { 
			echo '<input type="hidden" name="filename" value="">';
			echo '<input type="hidden" name="identifier" value="">';
			echo '<input type="hidden" name="id_lotmag" value="">';
			echo '<input type="hidden" name="expiry" value="">';
		}
	}
	
} else {
        echo '<input type="hidden" name="filename" value="">';
        echo '<input type="hidden" name="identifier" value="">';
        echo '<input type="hidden" name="id_lotmag" value="">';
        echo '<input type="hidden" name="expiry" value="">';
    }
?>
<input type="hidden" name="lot_or_serial" value="<?php echo $form['lot_or_serial']; ?>">
</td>
<?php	
	// fine LOTTO
	
echo "<td class=\"FacetFieldCaptionTD\">" . $script_transl[12] . "</td><td class=\"FacetDataTD\" ><input type=\"text\" value=\"" . $form['quanti'] . "\" maxlength=\"10\" size=\"10\" name=\"quanti\" onChange=\"this.form.total.value=CalcolaImportoRigo();this.form.submit();\"> $print_unimis</td></tr>\n";
echo "<tr><td class=\"FacetFieldCaptionTD\">" . $script_transl[13] . "</td><td class=\"FacetDataTD\" ><input type=\"text\" value=\"" . $form['prezzo'] . "\" maxlength=\"12\" size=\"12\" name=\"prezzo\" onChange=\"this.form.total.value=CalcolaImportoRigo();\"> " . $admin_aziend['symbol'] . "</td>\n";
echo "<td class=\"FacetFieldCaptionTD\">" . $script_transl[14] . "</td><td class=\"FacetDataTD\" ><input type=\"text\" value=\"" . $form['scorig'] . "\" maxlength=\"4\" size=\"4\" name=\"scorig\" onChange=\"this.form.total.value=CalcolaImportoRigo();\"> %</td></tr>\n";
echo "<tr><td class=\"FacetFieldCaptionTD\">" . $strScript["report_movmag.php"][7] . "</td><td class=\"FacetDataTD\" ><input type=\"text\" value=\"" . $importo_totale . "\" name=\"total\" size=\"20\" readonly />\n";
echo "<td class=\"FacetFieldCaptionTD\">" . $strScript["admin_caumag.php"][4] . "</td><td class=\"FacetDataTD\">\n";
echo "<select name=\"operat\" class=\"FacetSelect\">\n";
for ($counter = -1; $counter <= 1; $counter++) {
    $selected = "";
    if ($form["operat"] == $counter) {
        $selected = " selected ";
    }
    echo "<option value=\"$counter\" $selected > " . $strScript["admin_caumag.php"][$counter + 9] . "</option>\n";
}
echo "</select></td></tr><tr><td colspan=\"3\"><input type=\"reset\" name=\"Cancel\" value=\"" . $script_transl['cancel'] . "\">\n";
echo "<input type=\"submit\" name=\"Return\" value=\"" . $script_transl['return'] . "\">\n";
echo "</td><td align=\"right\">\n";
if ($toDo == 'update') {
    echo '<input type="submit" accesskey="m" name="Insert" value="' . strtoupper($script_transl['update']) . '!"></td></tr><tr></tr>';
} else {
    echo '<input type="submit" accesskey="i" name="Insert" value="' . strtoupper($script_transl['insert']) . '!"></td></tr><tr></tr>';
}
echo "</td></tr></table>\n";
?>
</form>
<?php
require("../../library/include/footer.php");
?>