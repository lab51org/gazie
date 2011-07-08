<?php
/*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2011 - Antonio De Vincentiis Montesilvano (PE)
                                (www.devincentiis.it)
                        <http://gazie.altervista.org>
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

/*
 * Translated by: Antonio De Vincentiis
 * Revised by:
 */

$strScript = array ("select_comiva.php" =>
                   array(  "Comunicazione annuale dati IVA (generazione file IVC)",
                           "DATI GENERALI",
                           "Codice Fiscale",
                           "Ragione Sociale",
                           "Cognome",
                           "Nome",
                           "Anno di Imposta",
                           "CONTRIBUENTE",
                           "Partita IVA",
                           "Contabilit&agrave; separata",
                           "Com. di societ&agrave; aderente ad un gruppo IVA",
                           "Eventi eccezionali",
                           "DICHIARANTE [COMPILARE SOLO SE DIVERSO DAL CONTRIBUENTE]",
                           "Codice fiscale societ&agrave; dichiarante",
                           "Codice fiscale",
                           "Codice carica",
                           "Rappresentante legale, negoziale, di fatto o socio amministratore",
                           "Rappresentante di minore",
                           "Commissario liquidatore",
                           "Commissario giudiziale",
                           "Rappresentante fiscale di soggetto non residente",
                           "Erede del contribuente",
                           "Liquidatore",
                           "Soggetti risultanti da operazioni straordinarie",
                           "DATI RELATIVI ALLE OPERAZIONI EFFETTUATE",
                           "Codice attivit&agrave;",
                           "OPERAZIONI ATTIVE",
                           "Totale operazioni attive [al netto dell'IVA]",
                           "di cui: operazioni non imponibili",
                           "di cui: operazioni esenti",
                           "cessioni intracomunitarie di beni",
                           'attben'=>"di cui cessioni di beni strumentali",
                           "OPERAZIONI PASSIVE",
                           "Totale operazioni passive [al netto dell'IVA]",
                           "di cui: acquisti non imponibili",
                           "di cui: acquisti esenti",
                           "acquisti intracomunitari di beni",
                           'pasben'=>"di cui acquisti di beni strumentali",
                           "IMPORTAZIONI DI ORO INDUSTRIALE E ARGENTO PURO SENZA PAGAMENTO DELL'IVA IN DOGANA",
                           "IMPORTAZIONI DI ROTTAME E MATERIALI DI RECUPERO SENZA PAGAMENTO DELL'IVA IN DOGANA",
                           "Imponibile",
                           "Imposta",
                           "DETERMINAZIONE DELL'IVA DOVUTA O A CREDITO",
                           "IVA ",
                           "esigibile",
                           "detratta",
                           "dovuta",
                           "o a credito"),
                   "select_chiape.php" =>
                   array(  'title'=>'Chiusura e Apertura Conti',
                           'errors'=>array('La data  non &egrave; corretta!',
                                           'La data di chisura non pu&ograve; essere successiva alla data di apertura!',
                                           "E' stata gi&agrave; fatta una chiusura durante il periodo selezionato!",
                                           "Sbilanciamento dare/avere nei movimenti contabili esegui un controllo"
                                          ),
                           'date_closing'=>'Data Registrazione Chiusura',
                           'date_opening'=>'Data Registrazione Apertura',
                           'closing_balance'=>"Bilancio di Chiusura",
                           'economic_result'=>"Risultato Economico",
                           'operating_profit'=>"Utile d'Esercizio",
                           'operating_losses'=>"Perdita d'Esercizio",
                           'opening_balance'=>"Bilancio d'Apertura",
                           'closing'=>" CHIUSURA ",
                           'opening'=>" APERTURA ",
                           'economic'=>"ECONOMICO",
                           'code'=>"CODICE",
                           'descr'=>"DESCRIZIONE",
                           'exit'=>"AVERE",
                           'entry'=>"DARE",
                           'of'=>" DEL ",
                           'sheet'=>"STATO PATRIMONIALE",
                           'assets'=>"ATTIVO",
                           'liabilities'=>"PASSIVO",
                           'costs'=>"COSTI",
                           'revenues'=>"RICAVI",
                           'acc_o'=>'APERTURA CONTI',
                           'acc_c'=>'CHIUSURA CONTI'
                           ),
                   "select_bilanc.php" =>
                   array(  "Bilancio - Libro Inventari",
                           "Data Inizio Periodo",
                           "Data Fine Periodo",
                           "Stampa definitiva",
                           "Numero prima pagina",
                           "Descrizione",
                           " BILANCIO ",
                           " DAL ",
                           " AL ",
                           " SITUAZIONE PATRIMONIALE ",
                           " Utile ",
                           " Perdita ",
                           "ATTIVO",
                           "PASSIVO",
                           "COSTI",
                           "RICAVI",
                           "TOTALE ",
                           " CONTO ECONOMICO ",
                           " successiva alla ",
                           "Il presente bilancio è conforme alle scritture contabili.",
                           "La scelta della stampa in definitiva aggiorna il campo 'ultima pagina del Libro Inventari' nell'archivio azienda",
                           "Numero della prima pagina da stampare (di default quella riportata sull'archivio azienda + 1)",
                           "Conto",
                           "Pagina ",
                           "Saldo",
                           "Firma",
                           " a riporto : ",
                           " riporto :"
                           ),
                    "select_elencf.php" =>
                   array(  "Elenco clienti e fornitori",
                           "Soggetti in elenco",
                           "Clienti",
                           "Fornitori",
                           "Elenco",
                           "Anno",
                           "Non Imponibile",
                           "Codice fiscale uguale a 0",
                           "Codice fiscale sbagliato per una persona fisica",
                           "Non ha il Codice Fiscale",
                           "Codice Fiscale o indicazione persona giuridica (G) errati",
                           "Codice Fiscale o sesso (M) errati",
                           "Codice Fiscale o sesso (F) errati",
                           "Il Codice Fiscale &egrave; formalmente errato",
                           "La Partita IVA &egrave; formalmente errata",
                           "Non ha la Partita IVA",
                           "Sede legale non corretta, il formato giusto dev'essere come questo esempio: Piazza del Quirinale,41 00187 ROMA (RM)",
                           "Aliquota IVA imponibile con imposta uguale a 0",
                           "Aliquota IVA che non prevede una imposta e che invece &egrave; diversa da 0",
                           "Non si pu&ograve; generare il File Internet perch&egrave; sono stati rilevati errori da correggere (vedi in seguito)",
                           "CORREGGI !",
                           "Non sono stati trovati movimenti IVA da riportare in elenco!",
                           "Totali",
                           "Ci sono degli errori nei dati di configurazione dell'azienda!",
                           "Codice Fiscale",
                           "Partita IVA",
                           "Cognome",
                           "Nome",
                           "Sesso",
                           "Data di Nascita",
                           "Comune di Nascita",
                           "Provincia di Nascita",
                           "Denominazione",
                           "Comune",
                           "Provincia"
                           ),
                    "select_comopril.php" =>
                   array(  "Comunicazioni operazioni IVA rilevanti",
                           "Soggetti in elenco",
                           "Clienti",
                           "Fornitori",
                           "Elenco",
                           "Anno",
                           "Non Imponibile",
                           "Codice fiscale uguale a 0",
                           "Codice fiscale sbagliato per una persona fisica",
                           "Non ha il Codice Fiscale",
                           "Codice Fiscale o indicazione persona giuridica (G) errati",
                           "Codice Fiscale o sesso (M) errati",
                           "Codice Fiscale o sesso (F) errati",
                           "Il Codice Fiscale &egrave; formalmente errato",
                           "La Partita IVA &egrave; formalmente errata",
                           "Non ha la Partita IVA",
                           "Sede legale non corretta, il formato giusto dev'essere come questo esempio: Piazza del Quirinale,41 00187 ROMA (RM)",
                           "Aliquota IVA imponibile con imposta uguale a 0",
                           "Aliquota IVA che non prevede una imposta e che invece &egrave; diversa da 0",
                           "Non si pu&ograve; generare il File Internet perch&egrave; sono stati rilevati errori da correggere (vedi in seguito)",
                           "CORREGGI !",
                           "Non sono stati trovati movimenti IVA da riportare in elenco!",
                           "Totali",
                           "Ci sono degli errori nei dati di configurazione dell'azienda!",
                           "Codice Fiscale",
                           "Partita IVA",
                           "Cognome",
                           "Nome",
                           "Sesso",
                           "Data di Nascita",
                           "Comune di Nascita",
                           "Provincia di Nascita",
                           "Denominazione",
                           "Comune",
                           "Provincia"
                           ),
                    "error_protoc.php" =>
                   array(  'title'=>'Controllo numerazione registri IVA',
                           'year'=>' dell\'anno ',
                           'header'=>array('ID'=>'','Data '=>'','Sezione'=>'','Registro'=>'','Protocollo'=>'','Causale'=>'','Descrizione'=>''
                                          ),
                           'pre_dd'=>' del ',
                           'expect'=>' atteso '
                           )
                           );
?>