<?php
/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2022 - Antonio De Vincentiis Montesilvano (PE)
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

if (isset($_GET['id_tes'])) { //	Evitiamo errori se lo script viene chiamato direttamente
   require("../../library/include/datlib.inc.php");
   $admin_aziend = checkAdmin();
   //duplico la testata
   $id_testata = intval($_GET['id_tes']);
   $testata = gaz_dbi_get_row($gTables['tesbro'], 'id_tes', $id_testata); // recupero i dati della testata di origine
   $fornitore = intval($_GET['duplicate']);
   $tipdoc='APR';
   $email="''";
   if (isset($_GET['dest'])){ // devo trasformare un preventivo in ordine
	 $fornitore = "`clfoco`";
	 $tipdoc='AOR';
	 $email="`email`";
   } else { // devo fare una duplicazione  
	// prevent direct access
	$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
	if (!$isAjax && !isset($_GET['duplicate'])) {
		$user_error = 'Access denied - not an AJAX request...';
		trigger_error($user_error, E_USER_ERROR);
	}
    if ($testata['tipdoc']=='AOR'){ // provengo da un ordine e lo devo trasformare in preventivo
	 $tipdoc='APR';
    } else { // provvengo da un preventivo, lo duplico soltanto senza cambiare il tipo documento
    }	
   }
   $numdoc = trovaNuovoNumero($gTables,$tipdoc);  // numero nuovo documento
   $today = gaz_today();
   $sql = "INSERT INTO ".$gTables['tesbro']." (`id_tes`, `seziva`, `tipdoc`, `template`, `email`, `print_total`, `delivery_time`, `day_of_validity`, `datemi`, `protoc`, `numdoc`, `numfat`, `datfat`, `clfoco`, `pagame`, `banapp`, `vettor`, `listin`, `destin`, `id_des`, `id_des_same_company`, `spediz`, `portos`, `imball`, `traspo`, `speban`, `spevar`, `round_stamp`, `cauven`, `caucon`, `caumag`, `id_agente`, `id_parent_doc`, `sconto`, `expense_vat`, `stamp`, `taxstamp`, `virtual_taxstamp`, `net_weight`, `gross_weight`, `units`, `volume`, `initra`, `geneff`, `id_contract`, `id_con`, `id_orderman`, `status`, `adminid`, `last_modified`) "
           . "SELECT null, `seziva`, '".$tipdoc."', `template`, ".$email.", `print_total`, `delivery_time`, `day_of_validity`, '$today', `protoc`, $numdoc, '', '', ".$fornitore.", `pagame`, `banapp`, `vettor`, `listin`, `destin`, `id_des`, `id_des_same_company`, `spediz`, `portos`, `imball`, `traspo`, `speban`, `spevar`, `round_stamp`, `cauven`, `caucon`, `caumag`, `id_agente`, '".$id_testata."', `sconto`, `expense_vat`, `stamp`, `taxstamp`, `virtual_taxstamp`, `net_weight`, `gross_weight`, `units`, `volume`,  '$today', `geneff`, `id_contract`, `id_con`, `id_orderman`, `status`, `adminid`, CURRENT_TIMESTAMP FROM ".$gTables['tesbro']." WHERE id_tes =". $id_testata.";";
   gaz_dbi_query($sql);
   $nuovaChiave = gaz_dbi_last_id();
   // duplico i righi
   $sql = "INSERT INTO ".$gTables['rigbro']." (`id_rig`, `id_tes`, `tiprig`, `codart`, `descri`, `quality`, `id_body_text`, `unimis`, `larghezza`, `lunghezza`, `spessore`, `peso_specifico`, `pezzi`, `quanti`, `prelis`, `sconto`, `codvat`, `pervat`, `codric`, `provvigione`, `ritenuta`, `delivery_date`, `id_doc`, `id_mag`, `id_orderman`, `status`) "
           . "SELECT null, $nuovaChiave, `tiprig`, `codart`, `descri`, `quality`, `id_body_text`, `unimis`, `larghezza`, `lunghezza`, `spessore`, `peso_specifico`, `pezzi`, `quanti`, `prelis`, `sconto`, `codvat`, `pervat`, `codric`, `provvigione`, `ritenuta`, `delivery_date`, 0, 0, `id_orderman`, 'INSERT' FROM ".$gTables['rigbro']." WHERE id_tes =". $id_testata.";";
   gaz_dbi_query($sql);
   $head="Location: ";
   if ($tipdoc=='APR'){ // ho duplicato un preventivo
	header($head.="report_broacq.php?flt_tipo=APR");
   } else {
	header($head.="admin_broacq.php?id_tes=".$nuovaChiave."&Update");
   }
   exit;
}

function trovaNuovoNumero($gTables,$tipdoc='APR') {
	$orderBy = "datemi desc, numdoc desc";
	$rs_ultimo_documento = gaz_dbi_dyn_query("numdoc", $gTables['tesbro'], $gTables['tesbro'].".tipdoc='".$tipdoc."'", $orderBy, 0, 1);
	$ultimo_documento = gaz_dbi_fetch_array($rs_ultimo_documento);
	// se e' il primo documento dell'anno, resetto il contatore
	if ($ultimo_documento) {
      $numdoc = $ultimo_documento['numdoc'] + 1;
   } else {
      $numdoc = 1;
   }
   return $numdoc;
}
?>