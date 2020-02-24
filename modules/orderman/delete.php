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
// prevent direct access
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if (!$isAjax) {
    $user_error = 'Access denied - not an AJAX request...';
    trigger_error($user_error, E_USER_ERROR);
}
if (isset($_POST['type'])&&isset($_POST['ref'])) { 
	require("../../library/include/datlib.inc.php");
	$admin_aziend = checkAdmin();
	switch ($_POST['type']) {
        case "orderman":
			$i=intval($_POST['ref']);
			$id_tesbro=intval($_POST['ref2']);
			$res = gaz_dbi_get_row($gTables['tesbro'],"id_tes",$id_tesbro); // prendo il rigo di tesbro interessato
			$query="DELETE FROM ".$gTables['staff_worked_hours']." WHERE id_orderman = '".$i."' AND work_day = '".$res['datemi']."'"; 
			gaz_dbi_query($query); // cancello tutti i righi operai con quel giorno e quella produzione
			
			// prendo tutti i movimenti di magazzino a cui fa riferimento la produzione
			$what=$gTables['movmag'].".id_mov ";
			$table=$gTables['movmag'];$idord=$i;
			$where="id_orderman = $idord";
			$resmov=gaz_dbi_dyn_query ($what,$table,$where);
			while ($r = gaz_dbi_fetch_array($resmov)) {// cancello i relativi movimenti SIAN
				gaz_dbi_del_row($gTables['camp_mov_sian'], "id_movmag", $r['id_mov']);
			} 
			
			$query="DELETE FROM ".$gTables['movmag']." WHERE id_orderman = '".$i."'"; 
			gaz_dbi_query($query); //cancello i movimenti di magazzino corrispondenti
			
			if ($res['clfoco']<=0) { // se NON è un ordine cliente esistente e quindi fu generato automaticamente da orderman
				$result = gaz_dbi_del_row($gTables['tesbro'], "id_tes", $id_tesbro); // cancello tesbro
				$result = gaz_dbi_del_row($gTables['orderman'], "id", $i); // cancello orderman/produzione
				$result = gaz_dbi_del_row($gTables['rigbro'], "id_tes", $id_tesbro); // cancello rigbro
			} else { // se invece è un ordine cliente devo lasciarlo e solo sganciarlo da orderman
				gaz_dbi_query ("UPDATE " . $gTables['tesbro'] . " SET id_orderman = '' WHERE id_tes ='".$id_tesbro."'") ; // sgancio tesbro da orderman
				$result = gaz_dbi_del_row($gTables['orderman'], "id", $i); // cancello orderman/produzione
			}
			// in ogni caso riporto l'auto_increment all'ultimo valore disponibile
			$query="SELECT max(id)+1 AS li FROM ".$gTables['orderman']; 
			$last_autincr=gaz_dbi_query($query);
			$li=gaz_dbi_fetch_array($last_autincr);
			$li=(isset($li['id']))?($li['id']+1):1;
			$query="ALTER TABLE ".$gTables['orderman']." AUTO_INCREMENT=".$li; 
			gaz_dbi_query($query); // riporto l'auto_increment al primo disponibile per non avere vuoti di numerazione
		break;
		case "luoghi":
			$i=intval($_POST['ref']);
			// controllo se ci sono movimenti di magazzino con questo luogo
			$ctrl = gaz_dbi_get_row($gTables['movmag'], "campo_coltivazione", $i);
			if (!isset($ctrl)) { // se non ci sono movimenti posso cancellare
				gaz_dbi_del_row($gTables['campi'], "codice", $i);
			} 			
		break;
	}
}
?>