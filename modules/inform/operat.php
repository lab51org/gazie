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
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if (!$isAjax) {
    $user_error = 'Access denied - not an AJAX request...';
   // trigger_error($user_error, E_USER_ERROR);
}
if (isset($_POST['type'])&&isset($_POST['ref'])) { 
	require("../../library/include/datlib.inc.php");
	$admin_aziend = checkAdmin();
	switch ($_POST['type']) {
        case "del_bank":
			$i=intval($_POST['ref']);
			gaz_dbi_del_row($gTables['bank'], "id", $i);
		break;
        case "add_banapp":
			$i=intval($_POST['ref']);
            $d=gaz_dbi_get_row($gTables['bank'], 'id',$i);
            $m=gaz_dbi_get_row($gTables['municipalities'], 'id',$d['id_municipalities']);
            $p=gaz_dbi_get_row($gTables['provinces'], 'id',$m['id_province']);
            $sql="INSERT INTO ".$gTables['banapp']." SELECT MAX(codice)+1,".$i." ,'".addslashes(substr($d['descriabi'],0,20).' '.substr($d['descricab'],0,30))."',". $d['codabi'].",". $d['codcab'].",'".addslashes(substr($d['indiri'],0,20).' '.substr($m['name'],0,30))."', '".$p['abbreviation']."', '".$d['cap']."','".$admin_aziend['user_name']."', '".date("Y-m-d H:i:s")."'  FROM ".$gTables['banapp'];
            gaz_dbi_query($sql);
		break;
	}
}
?>