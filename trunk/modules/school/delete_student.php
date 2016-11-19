<?php

/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2016 - Antonio De Vincentiis Montesilvano (PE)
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
$admin_aziend = checkAdmin(9);
$tp = $table_prefix . str_pad(intval($_GET['id']), 4, '0', STR_PAD_LEFT) . "\_";
$ve = gaz_dbi_query("SELECT CONCAT(  'DROP VIEW `', TABLE_NAME,  '`;' ) AS query, TABLE_NAME as tn
FROM INFORMATION_SCHEMA.VIEWS
WHERE TABLE_NAME LIKE  '" . $tp . "%'");
while ($r = gaz_dbi_fetch_array($ve)) {
    print 'cancellata vista:' . $r['tn'] . "<br>\n";
    gaz_dbi_query($r['query']);
}
$te = gaz_dbi_query("SELECT CONCAT(  'DROP TABLE `', TABLE_NAME,  '`;' ) AS query, TABLE_NAME as tn
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_NAME LIKE  '" . $tp . "%'");
while ($r = gaz_dbi_fetch_array($te)) {
    print 'cancellata tabella:' . $r['tn'] . "<br>\n";
    gaz_dbi_query($r['query']);
}
// cancello il rigo dalla tabella students dell'installazione principale
gaz_dbi_del_row($gTables['students'], 'student_id', intval($_GET['id']));
    print '<b>CANCELLATO LO STUDENTE</B>';

?>