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

/*
 * Translated by: Antonio De Vincentiis
 * Revised by:
 */
$strScript = array("admin_fornit.php" =>
    array('title' => 'Gestione dei fornitori',
        'ins_this' => 'Inserisci un fornitore',
        'upd_this' => 'Modifica  il fornitore ',
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 2 caratteri!',
            'Cambia anagrafica'),
        'errors' => array('&Egrave; necessario indicare la Ragione Sociale',
            '&Egrave; necessario indicare l\'indirizzo',
            'Il codice di avviamento postale (CAP) &egrave; sbagliato',
            '&Egrave; necessario indicare la citt&agrave;',
            '&Egrave; necessario indicare la provincia',
            '&Egrave; necessario indicare il sesso',
            'L\'IBAN non &egrave; corretto',
            'L\'IBAN e la nazione sono diversi',
            'Codice fiscale sbagliato per una persona fisica',
            'La partita IVA &egrave; formalmente errata!',
            'Esiste gi&agrave un fornitore con la stessa Partita IVA',
            'Il codice fiscale &egrave; formalmente errato',
            'Esiste gi&agrave; un fornitore con lo stesso Codice Fiscale',
            'C.F. mancante! In automatico &egrave; stato<br />impostato con lo stesso valore della Partita IVA!',
            'E\' una persona fisica, inserire il codice fiscale',
            'Esiste una anagrafica con la stessa partita IVA',
            'Esiste una anagrafica con lo stesso Codice Fiscale',
            '&Egrave; necessario scegliere la modalit&agrave; di pagamnto',
            'Il codice del fornitore &egrave; gi&agrave; esistente, riprova l\'inserimento con quello proposto (aumentato di 1)',
            'La data di nascita &egrave; sbagliata',
            'Indirizzo email formalmente sbagliato'
        ),
        'link_anagra' => ' Clicca sotto per inserire l\'anagrafica esistente sul piano dei conti',
        'codice' => "Codice ",
        'ragso1' => "Ragione Sociale 1",
        'ragso2' => "Ragione Sociale 2",
        'sedleg' => "Sede legale",
        'luonas' => 'Luogo di nascita',
        'datnas' => 'Data di Nascita',
        'pronas' => 'Provincia di nascita',
        'counas' => 'Nazione di Nascita',
        'legrap' => "Legale rappresentante ",
        'sexper' => "Sesso/pers.giuridica ",
        'sexper_value' => array('' => '-', 'M' => 'Maschio', 'F' => 'Femmina', 'G' => 'Giuridica'),
        'indspe' => 'Indirizzo',
        'capspe' => 'Codice Postale',
        'citspe' => 'Citt&agrave; - Provincia',
        'country' => 'Nazione',
        'id_language' => 'Lingua',
        'id_currency' => 'Valuta',
        'telefo' => 'Telefono',
        'fax' => 'Fax',
        'cell' => 'Cellulare',
        'codfis' => 'Codice Fiscale',
        'pariva' => 'Partita I.V.A.',
        'e_mail' => 'e mail',
        'cosric' => 'Conto di costo',
        'codpag' => 'Modalit&agrave; di pagamento *',
        'sconto' => '% Sconto da applicare',
        'banapp' => 'Banca d\'appoggio',
        'portos' => 'Porto - Resa',
        'spediz' => 'Spedizione',
        'imball' => 'Imballo',
        'listin' => 'Listino da applicare',
        'id_des' => 'Destinazione &rArr; da anagrafica',
        'destin' => 'Destinazione &rArr; descrizione libera',
        'iban' => 'IBAN',
        'maxrat' => 'Massimo importo delle rate',
        'ragdoc' => 'Raggruppamento documenti',
        'addbol' => 'Addebito spese bolli',
        'speban' => 'Addebito spese bancarie',
        'spefat' => 'Addebito spese di fatturazione',
        'stapre' => 'Stampa prezzi su D.d.T.',
        'op_type' => 'Tipologia operazioni',
        'op_type_value' => array(3 => 'Acquisto di beni', 4 => 'Acquisto di servizi'),
        'allegato' => 'Spesometro - Elenco fornitori',
        'allegato_value' => array(1 => 'Si', 0 => 'No', 2 => 'Riepilogativo'),
        'yn_value' => array('S' => 'Si', 'N' => 'No'),
        'aliiva' => 'Riduzione I.V.A.',
        'ritenuta' => '% Ritenuta',
        'status' => 'Visibilit&agrave; alla ricerca',
        'status_value' => array('' => 'Attiva', 'HIDDEN' => 'Disabilitata'),
        'annota' => 'Annotazioni',
        'id_agente' => 'Agente',
    ),
    "report_broacq.php" =>
    array('Nuovo Preventivo', 'Nuovo Ordine',
        'title' => 'Preventivi e ordini',
        'mail_alert0' => 'Invio documento con email',
        'mail_alert1' => 'Hai scelto di inviare una e-mail all\'indirizzo: ',
        'mail_alert2' => 'con allegato il seguente documento:',
		'title2' => 'Preventivi e ordini a fornitori',
		'tuttitipi' => 'Tutti i tipi',
		'tuttianni' => 'Tutti gli anni',
		'tuttiforni' => 'Tutti i fornitori'
    ),
    "report_debiti.php" =>
    array('title' => 'Lista dei debiti verso i fornitori',
        'start_date' => 'Anno inizio',
        'end_date' => 'Anno fine',
        'codice' => 'Codice',
        'partner' => 'Fornitore',
        'telefo' => 'Telefono',
        'mov' => 'Movimenti',
        'dare' => 'Dare',
        'avere' => 'Avere',
        'saldo' => 'Saldo',
        'pay' => 'Paga',
        'statement' => 'Estr.Conto',
        'pay_title' => 'Paga il debito con ',
        'statement_title' => 'Stampa l\'estratto conto di '
    ),
    "report_docacq.php" =>
    array("statistica ",
        "vendite",
        "acquisti",
        "anno",
        "ordinato per ",
        " da: ",
        " a: ",
        "Ultimo acquisto il ",
        "Ultima vendita il ",
        " dell'anno ",
        " Categoria merc. ",
        " Quantit&agrave; ",
        " Valore in ",
        " Fuori ",
		'tuttitipi' => 'Tutti i tipi',
		'tuttianni' => 'Tutti gli anni',
		'tutticlienti' => 'Tutti i fornitori'
		),
    "admin_docacq.php" =>
    array('title' => 'Inserimento/modifica documenti a fornitori',
        array("DDR" => "D.d.T. di Reso a Fornitore", "DDL" => "D.d.T. c/lavorazione", "AFA" => "Fattura d'Acquisto", "ADT" => "D.d.T. d'Acquisto", "AFC" => "Nota Credito da Fornitore", "AOR" => "Ordine a Fornitore", "APR" => "Richiesta di Preventivo a Fornitore"),
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 2 caratteri!',
            'Cambia cliente/fornitore'),
        " corpo ",
        " piede ",
        " Tira su ",
        " Sezione ",
        " Indirizzo ",
        " Data ",
        " Listino ",
        " Pagamento ",
        " Banca ",
        " Destinazione ",
        " Causale ",
        " magazzino ",
        " acquisto ",
        "Vettore",
        "Articolo",
        "Quantit&agrave;",
        "Tipo",
        "Costo",
        "I.V.A.",
        "Codice",
        "Descrizione",
        "U.M.",
        "Prezzo",
        "Sconto",
        "Importo",
        "Imballo",
        "Spedizione",
        "Trasporto",
        "Porto",
        "Inizio trasporto",
        " ore ",
        "Imponibile",
        "Imposta",
        "Merce",
        "Peso",
        "Totale",
        "La data di inizio trasporto non &egrave; corretta!",
        "La data di inizio trasporto non pu&ograve; essere precedente alla data di emissione!",
        "Non ci sono righi per poter emettere il documento!",
        "Stai tentando di modificare il DdT con una data antecedente a quella del DdT con numero precedente!",
        "Stai tentando di modificare il DdT con una data successiva a quella del DdT con numero successivo!",
        "Stai tentando di modificare il documento con una data antecedente a quello dello stesso tipo di documento con numero precedente!",
        "Stai tentando di modificare il documento con una data successiva a quello dello stesso tipo di documento con numero successivo!",
        "La data di emissione non pu&ograve; essere antecedente a quella dell'ultimo DdT emesso!",
        "La data di emissione non pu&ograve; essere antecedente a quella dell'ultimo documento dello stesso tipo emesso!",
        "La data di emissione non &egrave; corretta!",
        "Non hai selezionato il fornitore!",
        "Non hai selezionato la modalit&agrave; di pagamento!",
        "Un rigo &egrave; senza la descrizione!",
        "Un rigo &egrave; senza l'unit&agrave; di misura!",
        "Causale mag.",
        "Numero ",
        "La data di registrazione non pu&ograve; essere antecedente a quella del documento da registrare!",
        "La data del documento da registrare non &egrave; corretta!",
        "Non &egrave; stato inserito il numero del documento!",
        "Non &egrave; stato possibile caricare il documento per la tracciabilità del prodotto!",
        "L'articolo di magazzino ha una matricola/numero di serie pertanto la quantità è stata forzata ad 1",
        /** ENRICO FEDELE */
        "add_article" => "Aggiungi un nuovo articolo",
        'art_code' => 'Codice articolo',
        'art_barcode' => 'Codice a barre',
        'art_descr' => 'Descrizione',
        'search_for' => 'Cerca per',
        'weight' => 'Peso specifico:',
        'zero_rows' => 'Il documento non contiene righi o prodotti, compila la ricerca articoli nella sezione corpo per aggiungerne, inserisci il valore % per avere una lista completa o per effettuare una ricerca parziale',
        'discount_alert' => 'sconto da anagrafe articoli',
        'last_row' => 'Ultimo rigo inserito',
        'lotmag'=>'certificato',
        'expiry'=>'Scadenza',
        'identifier'=>'Numero di serie - matricola, se non immesso verrà attribuito automaticamente'
    ),
    "accounting_documents.php" =>
    array('title' => 'Genera i movimenti contabili a partire dai documenti d\'acquisto',
        'errors' => array('Data non corretta',
            'Non ci sono documenti da contabilizzare nell\'intervallo selezionato'
        ),
        'vat_section' => ' della sezione IVA n.',
        'date' => 'Fino al :',
        'type' => 'Registro IVA ',
        'type_value' => array('A' => 'dei documenti di Acquisto'),
        'proini' => 'Protocollo iniziale',
        'profin' => 'Protocollo finale',
        'preview' => 'Anteprima di contablizzazione',
        'date_reg' => 'Data',
        'protoc' => 'Protocollo',
        'doc_type' => 'Tipo',
        'doc_type_value' => array('FAD' => 'FATTURA DIFFERITA A CLIENTE',
            'FAI' => 'FATTURA IMMEDIATA A CLIENTE',
            'FAP' => 'PARCELLA',
            'FNC' => 'NOTA CREDITO A CLIENTE',
            'FND' => 'NOTA DEBITO A CLIENTE',
            'VCO' => 'CORRISPETTIVO',
            'VRI' => 'RICEVUTA',
            'AFA' => 'FATTURA D\'ACQUISTO',
            'AFC' => 'NOTA CREDITO DA FORNITORE',
            'AFD' => 'NOTA DEBITO DA FORNITORE'
        ),
        'customer' => 'Fornitore',
        'taxable' => 'Imponibile',
        'vat' => 'I.V.A.',
        'stamp' => 'Bolli su tratte',
        'tot' => 'Totale'
    ),
    "report_schedule_acq.php" =>
    array('title' => 'Lista dei movimenti delle partite',
        'header' => array('ID' => 'id',
            'Identificativo partita' => "id_tesdoc_ref",
            'Movimento contabile apertura (documento)' => "id_rigmoc_doc",
            'Movimento contabile chiusura (pagamento)' => "id_rigmoc_pay",
            'Importo' => "amount",
            'Scadenza' => "expiry"),
    ),
    "select_schedule_debt.php" =>
    array('title' => 'Selezione per la visualizzazione e/o la stampa delle partite aperte',
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 2 caratteri!',
            'Cambia fornitore'
        ),
        'errors' => array('La data  non &egrave; corretta!',
            'Non sono stati trovati movimenti!'
        ),
        'account' => 'Fornitore ',
        'orderby' => 'Ordina per: ',
        'orderby_value' => array(0 => 'Scadenza crescente', 1 => 'Scadenza decrescente',
            2 => 'Fornitore crescente', 3 => 'Fornitore decrescente'
        ),
        'header' => array('Fornitore' => '', 'ID Partita' => '', 'Status' => '', 'Mov.Cont.' => '', 'Descrizione' => '',
            'N.Doc.' => '', 'Data Doc.' => '', 'Data Reg.' => '', 'Dare' => '', 'Avere' => '',
            'Scadenza' => '',
            'Opzioni' => ''
        ),
        'status_value' => array(0 => 'APERTA', 1 => 'CHIUSA', 2 => 'ESPOSTA', 3 => 'SCADUTA', 9 => 'ANTICIPO'),
        'total_open' => 'Totale partite aperte'
    ),
    "delete_schedule.php" =>
    array('title' => 'Cancellazione movimenti chiusi dello scadenzario',
        'ragsoc' => 'Fornitore',
        'id_tesdoc_ref' => 'Identificativo partita',
        'descri' => 'Descrizione'
    ),
    "select_suppliers_status.php" =>
    array('title' => 'Selezione per la visualizzazione e/o la stampa dello scadenzario dei fornitori',
        'print_title' => 'SCADENZARIO FORNITORI ',
        'errors' => array('La data  non &egrave; corretta!',
            'err' => 'Ci sono degli errori che impediscono la stampa'
        ),
        'date_ini' => 'Data di riferimento ',
        'header' => array('ID' => '', 'Importo apertura' => '', 'Data Scadenza' => '', 'Importo chiusura' => ''
            , 'Data chiusura' => '', 'Giorni esposizione' => '', 'Stato' => ''
        ),
        'status_value' => array(0 => 'APERTA', 1 => 'CHIUSA', 2 => 'ESPOSTA', 3 => 'SCADUTA', 9 => 'ANTICIPO')
    ),
    "delete_docacq.php" =>
    array('title' => 'Eliminazione Documento d\'Acquisto'),
    "supplier_payment.php" =>
    array('title' => 'Pagamento debito verso fornitore (chiusura partita/e)',
        'errors' => array('La data  non &egrave; corretta!'),
        'partner' => 'Fornitore ',
        'date_ini' => 'Data di registrazione',
        'target_account' => "Conto per il pagamento: ",
        'accbal' => 'Saldo risultante dai movimenti contabili: ',
        'paymovbal' => 'Saldo risultante dalle partite aperte: ',
        'header' => array('ID' => '', 'Importo apertura' => '', 'Data Scadenza' => '', 'Importo chiusura' => ''
            , 'Data chiusura' => '', 'Giorni esposizione' => '', 'Stato' => ''
        ),
        'status_value' => array(0 => 'APERTA', 1 => 'CHIUSA', 2 => 'ESPOSTA', 3 => 'SCADUTA', 9 => 'ANTICIPO'),
        'header' => array('ID' => '', 'Importo apertura' => '', 'Data Scadenza' => '', 'Importo chiusura' => ''
            , 'Data chiusura' => '', 'Giorni esposizione' => '', 'Stato' => '', 'Paga' => ''
        ),
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 2 caratteri!',
            'Cambia fornitore',
            'Il saldo contabile &egrave; diverso da quello dello scadenzario,<br> se vuoi registrare la riscossione di un documento presente solo in contabilità fallo da qui:',
            'Nessun importo &egrave; stato pagato!',
            "Non &egrave; stato selezionato il conto per l'incasso",
            'Stai tentando di inserire il pagamento ad un fornitore senza movimenti'
        ),
        'descr_mov' => 'Descrizione del movimento<br>(vuoto per descrizione standard)',
    ),
    "admin_agenti_forn.php" =>
    array("Agenti di Fornitori ",
        "Agente di Fornitori",
        "Numero",
        "Anagrafe",
        "Telefono",
        "Fax",
        "% Provvigioni",
        "Categoria Merceologica",
        "Articolo",
        "Percentuale",
        "Specifiche per ",
        "gi&agrave; inserito!",
        "Mancano dei dati necessari per aggiungere un rigo!",
        "La percentuale delle provvigioni dev'essere maggiore di 0.01!",
        "Stai tentando di inserire un agente senza collegarlo ad un fornitore!",
        "Non &egrave; stato inserito il numero di agente",
        "Stai tentando di inserire un fornitore che &egrave; gi&agrave; un agente!",
        "Stai tentando di inserire un numero di agente che gi&agrave; esiste!",
        "Stai elimimando un agente!",
        "Lista Provvigioni",
        'title' => 'Gestione degli Agenti di Fornitori',
        'ins_this' => 'Inserisci un nuovo agente di fornitori'
    ),
    "select_provvigioni.php" =>
    array("Selezione per Stampa lista Provvigioni",
        "Agente di vendita",
        "Tutti gli Agenti",
        "Inserire min. 2 caratteri!",
        "Non &egrave; stato trovato nulla!",
        "Data periodo inizio",
        "Data periodo fine"
    ),
	"report_ddtacq.php" =>
    array('title' => 'Lista DDT di acquisto',
        'tuttitipi' => 'Tutti i tipi',
		'tuttianni' => 'Tutti gli anni',
		'tuttiforni' => 'Tutti i fornitori'
    ),
	"report_fornit.php" =>
    array('title' => 'Lista fornitori',
        'tuttitipi' => 'Tutti i tipi',
		'tuttianni' => 'Tutti gli anni',
		'tuttiforni' => 'Tutti i fornitori',
		'tuttecitta' => 'Tutte le città'
    ),
    "admin_docven.php" =>
    array('none',
        'doc_name' => array("DDT" => "D.d.T. di Vendita",
            "FAD" => "D.d.T. di Vendita",
            "FAI" => "Fattura Immediata",
            "FAP" => "Parcella",
            "FNC" => "Nota Credito a Cliente",
            "FND" => "Nota Debito a Cliente",
            "VCO" => "Vendita Corrispettivo",
            "DDV" => "D.d.T. per Cessione in Conto Visione",
            "DDY" => "D.d.T. da non fatturare automaticamente",
            "RDV" => "Ricevuto DdT di Reso da C/Visione",
            "VRI" => "Ricevuta"
        ),
        'title' => 'Documenti di vendita',
        'tipdoc' => ' documento di vendita tipo: ',
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 2 caratteri!',
            'Cambia cliente/fornitore'),
        " corpo ",
        " piede ",
        " Tira su ",
        " Sezione ",
        " Indirizzo ",
        " Data ",
        " Listino ",
        " Pagamento ",
        " Banca ",
        " Destinazione ",
        " Causale ",
        " magazzino ",
        " vendita ",
        "Vettore",
        "Articolo",
        "Quantit&agrave;",
        "Tipo",
        "Ricavo",
        "I.V.A.",
        "Codice",
        "Descrizione",
        "U.M.",
        "Prezzo",
        "Sconto",
        "Importo",
        "Imballo",
        "Spedizione",
        "Trasporto",
        "Porto",
        "Inizio trasporto",
        " ore ",
        "Imponibile",
        "Imposta",
        "Tot.Corpo",
        "Peso",
        "Totale",
        "La data di inizio trasporto non &egrave; corretta!",
        "La data di inizio trasporto non pu&ograve; essere precedente alla data di emissione!",
        "Non ci sono righi per poter emettere il documento!",
        "Stai tentando di modificare il DdT con una data antecedente a quella del DdT con numero precedente!",
        "Stai tentando di modificare il DdT con una data successiva a quella del DdT con numero successivo!",
        "Stai tentando di modificare il documento con una data antecedente a quello dello stesso tipo di documento con numero precedente!",
        "Stai tentando di modificare il documento con una data successiva a quello dello stesso tipo di documento con numero successivo!",
        "La data di emissione non pu&ograve; essere antecedente a quella dell'ultimo DdT emesso!",
        "La data di emissione non pu&ograve; essere antecedente a quella dell'ultimo documento dello stesso tipo emesso!",
        "La data di emissione non &egrave; corretta!",
        "Non hai selezionato il cliente!",
        "Non hai selezionato la modalit&agrave; di pagamento!",
        "Un rigo &egrave; senza la descrizione!",
        "Un rigo &egrave; senza l'unit&agrave; di misura!",
        "Causale mag.",
        "Peso netto",
        "Peso lordo",
        "N.Colli",
        "Volume",
        "Provvigioni",
        "Vuoi modificare un D.d.T. gi&agrave; fatturato!",
        "Questo documento &egrave; gi&agrave; stato contabilizzato!",
        "Per questo documento è obbligatorio selezionare il DdT di cessione in c/visione di riferimento",
        'speban' => "Spese incasso",
        'speban_title' => 'Spese incasso non documentate/rate',
        'traspo_title' => 'Spese trasporto non documentate',
        'stamp' => 'Bolli su tratte',
        'ritenuta' => 'Ritenuta ',
        'netpay' => 'Netto a pagare',
        'id_agente' => "Agente di vendita",
        'in_artsea' => 'Ricerca per ',
        'vat_constrain' => '(forza) IVA al ',
        'taxstamp' => 'Bollo',
        'virtual_taxstamp' => 'Modo',
        'virtual_taxstamp_value' => array(0 => 'No', 1 => 'Materiale', 2 => 'Virtuale', 3 => 'Virtuale senza addebito'),
        'ragbol' => "Raggr.DDT",
        'ragbol_title' => 'Raggruppamento DDT',
        'order_date' => "Data Ordine",
        'ragbol_value' => array(0 => 'A', 1 => 'B', 2 => 'C', 3 => 'D', 4 => 'E', 5 => 'F', 6 => 'G'),
        'zero_rows' => 'Il documento non contiene righi o prodotti, compila la ricerca articoli nella sezione corpo per aggiungerne, inserisci il valore % per avere una lista completa o per effettuare una ricerca parziale',
        'weight' => 'peso',
        'discount_alert' => 'sconto da anagrafe articoli',
        'last_row' => 'Ultimo rigo inserito',
        'id_doc_ritorno_title' => 'Seleziona il DdT per cessione in c/visione di riferimento',
        'id_doc_ritorno_alert' => 'Non è stato selezionato il DdT per cessione in c/visione di riferimento'
    ),
);
?>