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
$admin_aziend=checkAdmin();
$tesdoc_ref =  filter_var(substr($_GET['id_tesdoc_ref'],0,15),FILTER_SANITIZE_MAGIC_QUOTES);
$id_exc =  filter_var(intval($_GET['id_exc']),FILTER_SANITIZE_MAGIC_QUOTES);
$return_arr = array();
$sqlquery= "SELECT * FROM ".$gTables['paymov']."
            LEFT JOIN ".$gTables['rigmoc']." ON ( ".$gTables['rigmoc'].".id_rig = ".$gTables['paymov'].".id_rigmoc_doc OR ".$gTables['rigmoc'].".id_rig = ".$gTables['paymov'].".id_rigmoc_pay ) 
            LEFT JOIN ".$gTables['tesmov']." ON ".$gTables['tesmov'].".id_tes = ".$gTables['rigmoc'].".id_tes
            WHERE id_tesdoc_ref='".$tesdoc_ref."' AND ".$gTables['rigmoc'].".id_rig <> $id_exc ORDER BY ".$gTables['tesmov'].".datreg DESC, id_tesdoc_ref DESC, id_rig";
$result = gaz_dbi_query($sqlquery);

while($row = gaz_dbi_fetch_array($result)) {
            array_push($return_arr,$row);
}
echo json_encode($return_arr);
?>

