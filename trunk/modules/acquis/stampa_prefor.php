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
$admin_aziend=checkAdmin();
require("../../library/include/document.php");
$testat = intval($_GET['id_tes']);
$tesbro = gaz_dbi_get_row($gTables['tesbro'],"id_tes", $testat);
//se non e' il tipo di documento stampabile da questo modulo ... va a casa
if ($tesbro['tipdoc'] <> 'APR') {
    header("Location: report_broacq.php?flt_tipo='APR'");
    exit;
}
if (isset($_GET['dest'])){
  if ($_GET['dest']=='E'){ //  invio  mail all'indirizzo in testata o in alternativa se sta sul fornitore
  } else { // in dest ho l'indirizzo email quindi lo setto in testata e poi procedo all'invio
	$tesbro['email']=filter_var($_GET['dest'], FILTER_VALIDATE_EMAIL);
  	$r=gaz_dbi_put_row($gTables['tesbro'], 'id_tes', $testat, 'email',$tesbro['email']);
  }	
  createDocument($tesbro, 'PreventivoFornitore',$gTables,'rigbro','E');
} else {
  createDocument($tesbro, 'PreventivoFornitore',$gTables,'rigbro');
}
?>