<?php

/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2017 - Antonio De Vincentiis Montesilvano (PE)
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

$strScript = array(
    "report_letter.php" =>
    array("Lista delle lettere ",
        "Data ",
        "Numero ",
        "Tipo ",
        "Ragione sociale ",
        "Oggetto ",
        "Scrivi una nuova lettera",
        'mail_alert0' => 'Invio lettera con email',
        'mail_alert1' => 'Hai scelto di inviare una e-mail all\'indirizzo: ',
        'mail_alert2' => 'con allegato la seguente lettera:'),
    "admin_letter.php" =>
    array('title' => " Lettera ",
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 2 caratteri!',
            'Cambia anagrafica'),
        array("LET" => " Normale ", "DIC" => "Dichiarazione", "SOL" => " Sollecito "),
        " del ",
        " a ",
        " numero ",
        "Oggetto ",
        "alla c.a. ",
        "firma utente ",
        "Tipo ",
        "Corpo ",
        "Apponi nome utente",
        "La data non &egrave; corretta!",
        "Devi selezionare un cliente o un fornitore!"
    ),
    "update_control.php" =>
    array('title' => " Controllo aggiornamento software ",
        'new_ver1' => 'E\' disponibile una <b>nuova</b> versione (',
        'new_ver2' => ') di GAzie! <br>Per effettuare l\'aggiornamento puoi scaricare i files da',
        'is_align' => 'Non ci sono nuovi aggiornamenti disponibili. Questa versione di GAzie &egrave; aggiornata all\'ultima disponibile.',
        'no_conn' => 'Ci sono problemi di connessione al server per il controllo della versione!',
        'disabled' => 'Il controllo delle versioni aggiornate &egrave; stato disabilitato. &Egrave; possibile riattivarlo scegliendo uno dei servizi di check messi a disposizione dai seguenti siti',
        'zone' => 'ZONA',
        'city' => 'CITT&Agrave;',
        'sms' => 'SMS',
        'web' => 'Indirizzo WEB',
        'choice' => 'SCEGLI',
        'check_value' => array(0 => 'Abilita!', 1 => 'Abilitato'),
        'check_title_value' => array(0 => 'Abilita il controllo di versione da questo sito!', 1 => 'Disabilita il controllo di versione da questo sito!'),
        'all_disabling' => array(0 => 'Disabilita tutti!', 1 => 'Disabilita tutti i siti per il controllo della versione!')
    ),
    "report_anagra.php" =>
    array('title' => " Anagrafica contatti ",
        'new_ver1' => 'E\' disponibile una <b>nuova</b> versione (',
        'new_ver2' => ') di GAzie! <br>Per effettuare l\'aggiornamento puoi scaricare i files da',
        'is_align' => 'Non ci sono nuovi aggiornamenti disponibili. Questa versione di GAzie &egrave; aggiornata all\'ultima disponibile.',
        'no_conn' => 'Ci sono problemi di connessione al server per il controllo della versione!',
        'disabled' => 'Il controllo delle versioni aggiornate &egrave; stato disabilitato. &Egrave; possibile riattivarlo scegliendo uno dei servizi di check messi a disposizione dai seguenti siti',
        'zone' => 'ZONA',
        'city' => 'CITT&Agrave;',
        'sms' => 'SMS',
        'web' => 'Indirizzo WEB',
        'choice' => 'SCEGLI',
        'check_value' => array(0 => 'Abilita!', 1 => 'Abilitato'),
        'check_title_value' => array(0 => 'Abilita il controllo di versione da questo sito!', 1 => 'Disabilita il controllo di versione da questo sito!'),
        'all_disabling' => array(0 => 'Disabilita tutti!', 1 => 'Disabilita tutti i siti per il controllo della versione!')
    ),
    "gaziecart_update.php" =>
    array('title' => "Aggiornamento del catalogo online, estensione GAzieCart per Joomla!",
        'errors' => array('Il server non &egrave; stato trovato',
            'Impossibile fare il login, credenziali errate',
            'Direttorio inesistente',
            'Uno o pi&ugrave; file non sono stati aggiornati',
            "COMPLETATO!!! L'upload sul server web è andato a buon fine!"
        ),
        'server' => 'Nome del server FTP es: devincentiis.it',
        'user' => 'User - Nome utente per l\'autenticazione',
        'pass' => 'Password per l\'autenticazione',
        'path' => 'Dir. radice di joomla es. joomla/ opp. nulla',
        'listin' => 'Listino ',
        'listin_value' => array(1 => ' di Vendita 1', 2 => ' di Vendita 2', 3 => ' di Vendita 3', 'web' => ' di Vendita Online')
    ),
    "gazie_site_update.php" =>
    array('title' => "Aggiornamento del sito web",
        'errors' => array('Il server non &egrave; stato trovato',
            'Impossibile fare il login, credenziali errate',
            'Direttorio inesistente',
            'Uno o pi&ugrave; file non sono stati aggiornati'
        ),
        'server' => 'Nome del server FTP es: ftp.devincentiis.it',
        'user' => 'User - Nome utente per l\'autenticazione',
        'pass' => 'Password per l\'autenticazione',
        'path' => 'Directory radice del sito es. public_html/ opp. www/',
        'head_title' => 'Descrizione aggiuntiva al titolo',
        'head_subtitle' => 'Descrizione aggiuntiva al sottotitolo',
        'author' => 'Autore del sito (meta author)',
        'keywords' => 'Parole chiave del sito (meta keywords)',
        'listin' => 'Pubblicazione',
        'listin_value' => array(0 => 'Non pubblicare il listino', 1 => 'Listino senza prezzi', 2 => 'Listino con prezzi online'),
        'addpage' => 'Aggiungi una pagina al sito'
    ),
    "backup.php" =>
    array('title' => "Backup dei dati per mettere in sicurezza il lavoro!",
        'errors' => array(),
        'instructions' => 'Aggiungere le istruzioni seguenti',
        'table_selection' => 'Backup di',
        'table_selection_value' => array(0 => ' tutte le tabelle della base di dati ', 1 => ' le sole tabelle con prefisso '),
        'text_encoding' => 'Codifica',
        'sql_submit' => 'Genera il file sql',
    ),
    "report_backup.php" =>
    array('title' => "Lista dei backup interni ",
        'errors' => array(),
        'id' => 'Identificativo',
        'ver' => 'Versione',
        'name' => 'Nome file',
        'size' => 'Dimensione',
        'rec' => 'Ripristina',
        'dow' => 'Scarica',
        'del' => 'Elimina',
        'config' => 'Configurazione',
        'backup_mode' => 'Modalità di backup',
        'backup_mode_value' => array('automatic' => 'Automatico', 'internal' => 'Locale', 'external' => 'Remoto'),
        'sure' => 'Sei sicuro?',
        'recover' => 'Ripristinare'
    ),
    "report_anagra.php" =>
    array('title' => "Anagrafiche comuni"
    ),
    "delete_backup.php" =>
    array('title' => 'Eliminazione backup in corso',
        'sure' => 'Sei sicuro di voler cancellare il file?',
        'warning' => 'Attenzione'
    ),
    "admin_anagra.php" =>
    array('title' => 'Modifica anagrafica comune',
        'err' => array('ragso1' => '&Egrave; necessario indicare la Ragione Sociale',
            'indspe' => '&Egrave; necessario indicare l\'indirizzo',
            'capspe' => 'Il codice di avviamento postale (CAP) &egrave; sbagliato',
            'citspe' => '&Egrave; necessario indicare la citt&agrave;',
            'prospe' => '&Egrave; necessario indicare la provincia',
            'sexper' => '&Egrave; necessario indicare il sesso',
            'pf_no_codfis' => 'Codice fiscale sbagliato per una persona fisica',
            'pariva' => 'La partita IVA &egrave; formalmente errata!',
            'same_pariva' => 'Esiste gi&agrave una anagrafica con la stessa Partita IVA',
            'codfis' => 'Il codice fiscale &egrave; formalmente errato',
            'same_codfis' => 'Esiste gi&agrave; una anagrafica con lo stesso Codice Fiscale',
            'pf_ins_codfis' => 'E\' una persona fisica, inserire il codice fiscale',
            'datnas' => 'La data di nascita &egrave; sbagliata',
            'pec_email' => 'Indirizzo posta elettronica certificata formalmente sbagliato',
            'e_mail' => 'Indirizzo email formalmente sbagliato',
            'cf_pi_set' => 'E\' stata impostato un codice fiscale uguale alla partita IVA, se giusto, conferma nuovamente la modifica',
        ),
        'id' => "Codice ",
        'ragso1' => "Ragione sociale 1",
        'ragso2' => "Ragione sociale 2",
        'sedleg' => 'Sede legale',
        'legrap' => 'Legale rappresentante',
        'luonas' => 'Luogo di nascita',
        'datnas' => 'Data di Nascita',
        'pronas' => 'Provincia di nascita',
        'counas' => 'Nazione di Nascita',
        'id_language' => 'Lingua',
        'id_currency' => 'Valuta',
        'sexper' => "Sesso/pers.giuridica ",
        'sexper_value' => array('' => '-', 'M' => 'Maschio', 'F' => 'Femmina', 'G' => 'Giuridica'),
        'indspe' => 'Indirizzo',
        'capspe' => 'Codice Postale',
        'citspe' => 'Citt&agrave; - Provincia',
        'prospe' => 'Provincia',
        'country' => 'Nazione',
        'latitude' => 'Latitudine',
        'longitude' => 'Longitudine',
        'telefo' => 'Telefono',
        'fax' => 'Fax',
        'cell' => 'Cellulare',
        'codfis' => 'Codice Fiscale',
        'pariva' => 'Partita IVA',
        'fe_cod_univoco' => 'Cod.Univoco Destinatario (fatt.elettronica)',
        'pec_email' => 'Posta Elettronica Certificata',
        'e_mail' => 'e mail',
        'fatt_email' => 'Invio fattura:',
        'fatt_email_value' => array(0 => 'No, solo stampa PDF', 1 => 'In formato PDF su email', 2 => 'In formato XML su PEC', 3 => 'In formato PDF su email + XML su PEC')
    )
);
?>