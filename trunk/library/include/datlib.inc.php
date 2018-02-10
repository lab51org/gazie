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

require("../../config/config/gconfig.php");
require('../../library/include/' . $NomeDB . '.lib.php');
require("../../library/include/function.inc.php");

if (isset($_SESSION['table_prefix'])) {
    $table_prefix = substr($_SESSION['table_prefix'], 0, 12);
} elseif (isset($_GET['tp'])) {
    $table_prefix = filter_var(substr($_GET['tp'], 0, 12), FILTER_SANITIZE_MAGIC_QUOTES);
} else {
    $table_prefix = filter_var(substr($table_prefix, 0, 12), FILTER_SANITIZE_MAGIC_QUOTES);
}


// tabelle comuni alle aziende della stessa gestione
$tn = array('admin', 'admin_config', 'admin_module', 'anagra', 'aziend', 'classroom', 'config',
    'country', 'currencies', 'currency_history', 'destina', 'languages',
    'menu_module', 'menu_script', 'menu_usage', 'module', 'municipalities',
    'provinces', 'regions', 'students');
foreach ($tn as $v) {
    $gTables[$v] = $table_prefix . "_" . $v;
}

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $local = gaz_dbi_get_row($gTables['config'], 'variable', 'win_locale');
} else {
    $local = gaz_dbi_get_row($gTables['config'], 'variable', 'lin_locale');
}

if ($gazie_locale != "") {
    setlocale(LC_TIME, $gazie_locale);
} else {
    setlocale(LC_TIME, $local['cvalue']);
}

$id = 1;
if (isset($_SESSION['company_id'])) {
    $id = sprintf('%03d', $_SESSION['company_id']);
}

/* controllo anche se includere il file dei nomi di tabelle specifico del modulo
  residente nella directory del module stesso, con queste caratteristiche:
  modules/nome_modulo/lib.data.php
 */
if (@file_exists('./lib.data.php')) {
    require('./lib.data.php');
}

//tabelle aziendali
$tn = array('aliiva', 'agenti', 'artico', 'assets', 'banapp', 'body_text', 'cash_register',
    'catmer', 'caucon', 'caucon_rows', 'caumag', 'clfoco', 'company_config', 'company_data',
	'comunicazioni_dati_fatture', 'contract', 'effett', 'extcon', 'files', 'imball', 
	'letter', 'liquidazioni_iva', 'lotmag', 'movmag', 'pagame', 'paymov', 'portos', 
	'provvigioni', 'rigbro', 'rigdoc', 'rigmoc', 'rigmoi', 'spediz', 'staff', 'staff_skills',
	'tesbro', 'tesdoc', 'tesmov', 'vettor', 'fae_flux', 'assist', 'ragstat', 'agenti_forn',
	'movimenti', 'sconti_articoli', 'sconti_raggruppamenti', 'instal', 'orderman');
foreach ($tn as $v) {
    $gTables[$v] = $table_prefix . "_" . $id . $v;
}
?>