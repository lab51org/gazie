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
 * Translated by: Antonio De Vincentiis
 * Revised by:
 */

$strScript = array("select_comiva.php" =>
    array("Comunicazione annuale dati IVA (generazione file IVC)",
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
        'attben' => "di cui cessioni di beni strumentali",
        "OPERAZIONI PASSIVE",
        "Totale operazioni passive [al netto dell'IVA]",
        "di cui: acquisti non imponibili",
        "di cui: acquisti esenti",
        "acquisti intracomunitari di beni",
        'pasben' => "di cui acquisti di beni strumentali",
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
    array('title' => 'Chiusura e Apertura Conti',
        'errors' => array('La data  non &egrave; corretta!',
            'La data di chisura non pu&ograve; essere successiva alla data di apertura!',
            "E' stata gi&agrave; fatta una chiusura durante il periodo selezionato!",
            "Sbilanciamento dare/avere nei movimenti contabili esegui un controllo"
        ),
        'date_closing' => 'Data Registrazione Chiusura',
        'date_opening' => 'Data Registrazione Apertura',
        'closing_balance' => "Bilancio di Chiusura",
        'economic_result' => "Risultato Economico",
        'operating_profit' => "Utile d'Esercizio",
        'operating_losses' => "Perdita d'Esercizio",
        'opening_balance' => "Bilancio d'Apertura",
        'closing' => " CHIUSURA ",
        'opening' => " APERTURA ",
        'economic' => "ECONOMICO",
        'code' => "CODICE",
        'descr' => "DESCRIZIONE",
        'exit' => "AVERE",
        'entry' => "DARE",
        'of' => " DEL ",
        'sheet' => "STATO PATRIMONIALE",
        'assets' => "ATTIVO",
        'liabilities' => "PASSIVO",
        'costs' => "COSTI",
        'revenues' => "RICAVI",
        'acc_o' => 'APERTURA CONTI',
        'acc_c' => 'CHIUSURA CONTI'
    ),
    "select_bilanc.php" =>
    array("Bilancio - Libro Inventari",
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
        " riporto :",
        "Clienti/Fornitori",
        "cf_value" => array(1 => "Completi", 2 => "Solo totali", 3 => "Dettaglio in calce"),
    ),
    "select_elencf.php" =>
    array("Elenco clienti e fornitori",
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
    array('title' => "Comunicazioni operazioni IVA rilevanti (ART.21)",
        'limit' => "Limite min. operazioni s/fattura",
        'year' => "Anno di riferimento",
        'op_date' => "Data Operazione",
        'ragso1' => "Cognome / Ragione Sociale 1",
        'ragso2' => "Nome / Ragione Sociale 2",
        'soggetto' => "Cognome Nome/ Ragione Sociale ",
        'sourcedoc' => "Doc.origine",
        'sex' => "Sesso / Persona Giuridica",
        'sedleg' => "Sede Legale",
        'proleg' => "Provincia",
        'datnas' => 'Data di nascita',
        'luonas' => 'Luogo di nascita',
        'pronas' => 'Provincia di nascita',
        'soggetto_type' => "Tipo soggetto",
        'soggetto_type_value' => array(1 => 'Soggetto senza Partita IVA', 2 => 'Titolare di Partita IVA', 3 => 'Non residente', 4 => 'Nota variazione-Residenti', 5 => 'NotaVariazione-Non Residenti'),
        'imptype' => "Tipologia imponibile",
        'imptype_value' => array(1 => 'Imponibile', 2 => 'NON Imponibile', 3 => 'Esente', 4 => 'Imponibile con IVA non esposta'),
        'amount' => "Imponibile",
        'tax' => "Imposta",
        'riepil' => 'Riepilogativo',
        'quadro' => 'Quadro',
        'errors' => array("CORREGGI !",
            "Codice fiscale uguale a 0 e non indicato come riepilogativo",
            "Codice fiscale sbagliato per una persona fisica",
            "Non ha il Codice Fiscale ",
            "Codice Fiscale o indicazione persona giuridica (G) errati",
            "Codice Fiscale o sesso (M) errati",
            "Codice Fiscale o sesso (F) errati",
            "Il Codice Fiscale &egrave; formalmente errato",
            "Non ha la Partita IVA o essa &egrave; formalmente errata",
            "Persona Fisica straniera senza dati di nascita ",
            "Sede legale non corretta, il formato giusto dev'essere come questo esempio: Piazza del Quirinale,41 00187 ROMA (RM)",
            "Aliquota IVA imponibile con imposta uguale a 0",
            "Aliquota IVA che non prevede una imposta e che invece &egrave; diversa da 0",
            "Non si pu&ograve; generare il File Internet perch&egrave; sono stati rilevati errori da correggere (vedi in seguito)",
            "Non sono stati trovati movimenti IVA da riportare in elenco!",
            "Ci sono degli errori nei dati di configurazione dell'azienda!"),
        'total' => "Totali",
        'codfis' => "Codice Fiscale",
        'pariva' => "Partita IVA",
    ),
    "error_protoc.php" =>
    array('title' => 'Controllo numerazione registri IVA',
        'year' => ' dell\'anno ',
        'header' => array('ID' => '', 'Data ' => '', 'Sezione' => '', 'Registro' => '', 'Protocollo' => '', 'Causale' => '', 'Descrizione' => ''
        ),
        'pre_dd' => ' del ',
        'expect' => ' atteso '
    ),
    "report_assets.php" =>
    array('title' => 'Lista dei beni ammortizzabili',
        'datreg' => 'Data di acquisto',
        'descri' => 'Descizione del bene',
        'clfoco' => 'Fornitore',
        'amount' => 'Valore',
        'valamm' => 'Ammort.'
    ),
    "depreciation_assets.php" =>
    array('title' => 'Registrazione quote di ammortamento cespiti',
        'datreg' => ' al: ',
        'book' => ' Stampa il Libro cespiti a questa data ',
        'ammmin_ssd' => 'Sottospecie',
        'asset_des' => 'Descrizione bene',
        'movdes' => ' con Fat.',
        'clfoco' => ' da ',
        'fixed_val' => 'Immobilizzazione',
        'found_val' => 'Fondo',
        'cost_val' => 'Quota deducibile',
        'noded_val' => 'Quota non deduc.',
        'rest_val' => 'Residuo',
        'lost_cost' => 'Amm.<50%',
        'suggest_amm' => 'Proposta di ammortamento',
        'no_suggest_amm' => 'Ammortamento completato',
        'sold_suggest_amm' => 'Bene alienato',
        'err' => array('datreg' => 'A questa data non risultano ammortamenti da rilevare!',
                'ammsuc' => 'Per questo bene sono già stati eseguiti ammortamenti pari o successivi a questa data!'),
        'war' => array('noamm' => 'Non risulta che siano mai stati eseguiti ammortamenti a questa data!')
    )
);
?>