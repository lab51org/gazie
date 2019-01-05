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
 
/*
--------=======oooooooooooo!!!!!  ATTENZIONE !!!!!ooooooooooo========-------------
QUESTO FILE DI CONFIGURAZIONE IN ORIGINE DI NOME "gconfig.myconfig.default.php" 
LO SI DEVE RINOMINARE in "gconfig.myconfig.php" SULLE INSTALLAZIONE PRODUTTIVE E 
SUCCESSIVAMENTE MODIFICARE IN ACCORO CON LE IMPOSTAZIONI DEL VOSTRO SERVER MARIADB , 
DEL SERVER WEB APACHE, E COMUNQUE IN BASE ALLE SCELTE CHE VEDETE SOTTO.
COSI' EVITERETE DI USARE I SETTAGGI CONTENUTI IN "gconfig.php" ADATTI AD UN AMBIENTE
DI SVILUPPO (root senza password) E CONTEMPORANEAMENTE NON VERRA' SOVRASCRITTO 
AD OGNI AGGIORNAMETO DI GAZIE CHE CONSISTE, APPUNTO, NELLA SOVRASCRITTURA DI TUTTI I
FILES, COMPRESO IL CITATO "gconfig.php"
*/
 
if (isset($_SERVER['SCRIPT_FILENAME']) && (str_replace('\\', '/', __FILE__) == $_SERVER['SCRIPT_FILENAME'])) {
    exit('Accesso diretto non consentito');
}
//nome DBMS usato per la libreria specifica (MySQL=mysql.lib, SQLite=sqlite.lib, ecc)
//per il momento disponibile solo la libreria mysql.lib
define('NomeDB', 'mysqli');

////////////////////////////////////////////////////////////////////////
//
// Parametri di accesso: server, db, utente, passwd
//
////////////////////////////////////////////////////////////////////////
//
// Server MySQL. Si può specificare anche la porta per connettersi a
// MySQL, per esempio:
//
// $Host = "mysql.2freehosting.com:3306";
//
define('Host', 'localhost');

//
// Nome della base di dati a cui ci si connette.
//
define('Database', 'gazie');

//
// Utente della base di dati che ha il permesso di accedervi con tutti
// i privilegi necessari.
//
define('User', 'root');

//
// Parola d'ordine necessaria per accedere alla base di dati
// in qualità di utente $User.
//
define('Password', '');

//
// Porta sulla quale è in ascolto il database (normalmente 3306 per mysql, 3307 per mariadb)
define('Port', '3306');

//
// Prefisso delle tabelle di Gazie.
//
// ATTENZIONE: il prefisso delle tabelle predefinito è "gaz". Eventualmente, si
// possono usare altri prefissi, ma composti sempre dai primi tre caratteri
// "gaz" e seguiti da un massimo di nove caratteri, costituiti da lettere
// minuscole e cifre numeriche. Per esempio, "gaz123" è valido, mentre "gaga1"
// o "gaz_123" non sono validi.
//
define('table_prefix', 'gaz');

//
// Utente proposto inizialmente per l'accesso a Gazie. Se non si vuole
// suggerire alcunché, è sufficiente assegnare la stringa vuota.
//
define('default_user', '');

//
// Fuso orario, per la rappresentazione corretta delle date, indipendentemente
// dalla collocazione del server HTTP+PHP. MA NON FUNZIONA, perché MySQL aggiorna
// in modo indipendente le date di accesso alle tabelle.
//
define('Timezone', 'Europe/Rome');

//
// Testo da aggiungere eventualmente ai messaggi di posta elettronica, sistematicamente,
// per qualche motivo.
//
define('MY_EMAIL_FOOTER', 'E-mail generata da GAzie ver.');

//
// GAzie utilizza la funzione PHP set_time_limit() per consentire il completamento
// di elaborazioni che richiedono più tempo del normale.
// In condizioni normali, la variabile $disable_set_time_limit deve corrispondere
// a FALSE. La modifica del valore a TRUE serve solo in situazioni eccezionali,
// per esempio quando si vuole installare GAzie presso un servizio che vieta
// l'uso della funzione set_time_limit(), sapendo però che ciò pregiudica il funzionamento
// corretto di GAzie.
//
define('disable_set_time_limit', FALSE);

//
// Se il servente HTTP-PHP non ha una configurazione locale corretta,
// questa può essere impostata qui, espressamente.
//
define('gazie_locale', '');

//
// Numero di righe per pagina sui report, determina anche quante ne saranno caricate dallo scroll-onload
//
define('MY_PER_PAGE', 30);

//
// Le seguenti definizioni assegnano il percorso delle directory che devono essere scrivibili
// dal web server.
//
// Directory usata da modules/root/retrieve.php
//
define('MY_DATA_DIR', '../../data/');

//
// Directory usata dal modulo tcpdf
//
define('MY_K_PATH_CACHE', '../../library/tcpdf/cache/');

////////////////////////////////////////////////////////////////////////
// definisce il nome della sessione ma solo in caso di uso dei domini di livello superiore al secondo, in
// caso di installazione su domini di secondo livello viene attribuito automaticamente
// il nome del direttorio di installazione che normalmente e', appunto:  gazie
define('MY_SESSION_NAME', 'gazie');

//url di default per l'aggiornamento di GAzie
define('update_URI_files', 'https://sourceforge.net/projects/gazie');

// url per comunicare (ping) il mio nuovo IP DINAMICO  all'hosting di appoggio
define('MY_SET_DYNAMIC_IP',''); 

// abilita il debug delle variabili nel footer della pagina (impostare true/false)
define('debug_active', FALSE);
?>
