<?php

/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2020 - Antonio De Vincentiis Montesilvano (PE)
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
            'Esiste gi&agrave; un fornitore con la stessa Partita IVA',
            'Il codice fiscale &egrave; formalmente errato',
            'Esiste gi&agrave; un fornitore con lo stesso Codice Fiscale',
            'C.F. mancante! In automatico &egrave; stato<br />impostato con lo stesso valore della Partita IVA!',
            'E\' una persona fisica, inserire il codice fiscale',
            'Esiste una anagrafica con la stessa partita IVA',
            'Esiste una anagrafica con lo stesso Codice Fiscale',
            '&Egrave; necessario scegliere la modalit&agrave; di pagamento',
            'Il codice del fornitore &egrave; gi&agrave; esistente, riprova l\'inserimento con quello proposto (aumentato di 1)',
            'La data di nascita &egrave; sbagliata',
            'Indirizzo email formalmente sbagliato',
			'Non è stato descritto il servizio esternalizzato',
			'Il codice SIAN &egrave; gi&agrave; esistente, riprova l\'inserimento con quello proposto (aumentato di 1)'
        ),
        'link_anagra' => ' Clicca sotto per inserire l\'anagrafica esistente sul piano dei conti',
        'codice' => "Codice ",
        'ragso1' => "Ragione Sociale 1",
        'ragso1_placeholder' => "opp. nome cognome legale rappresentante",
        'ragso2' => "Ragione Sociale 2",
        'sedleg' => "Sede legale",
        'external_resp' => 'Responsabile Esterno Trattamento Dati',
        'external_resp_value' => array(1 => 'Si', 0 => 'No'),
        'external_service_descri' => 'Descrizione del servizio esternalizzato',
        'luonas' => 'Luogo di nascita',
        'datnas' => 'Data di Nascita',
        'pronas' => 'Provincia di nascita',
        'counas' => 'Nazione di Nascita',
        'legrap_pf_nome' => "Legale rappr(Nome - Cognome)",
        'legrap_pf_title' => "la ragione sociale lasciata vuota verrà riempita con questi campi",
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
        'pec_email' => 'Posta Elettronica Certificata',
        'e_mail' => 'e mail',
        'cosric' => 'Conto di costo',
        'codpag' => 'Modalit&agrave; di pagamento *',
        'sconto' => '% Sconto da applicare',
        'banapp' => 'Banca d\'appoggio',
        'portos' => 'Porto - Resa',
        'spediz' => 'Spedizione',
        'imball' => 'Imballo',
        'listin' => 'Listino da applicare',
        'id_des' => 'Destinazione ⇒ da anagrafica',
        'destin' => 'Destinazione ⇒ descrizione libera',
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
        'operation_type' => 'Tipo di operazione'
    ),
    "report_broacq.php" =>
    array('title'=>'Preventivi e ordini a fornitori',
        'title_dist' => array('APR'=>'Richieste di preventivo a fornitori','AOR'=>'Ordini a fornitori'),
        'header' => array(
			"Numero" => 'numdoc',
            "Produzione" => "id_orderman",
            "Data" => "datemi",
            "Fornitore" => "",
            "Status" => "",
            "Stampa" => "",
            "Operaz." => "",
            "Mail" => "",
            "Cancella" => ""
        ),
        'tuttitipi' => 'Tutti i tipi',
        'tuttianni' => 'Tutti gli anni',
        'tuttiforni' => 'Tutti i fornitori'
    ),
    "report_debiti.php" =>
    array('title' => 'Lista dei debiti verso i fornitori',
        'start_date' => 'Data inizio',
        'end_date' => 'Data fine',
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
    array('title' => 'Lista dei documenti d\'acquisto',
		"statistica ",
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
        array("DDR" => "D.d.T. di Reso a Fornitore", "RDL" => "D.d.T. ricevuto per ritorno da lavorazione", "DDL" => "D.d.T. c/lavorazione", "AFA" => "Fattura d'Acquisto", "ADT" => "D.d.T. d'Acquisto", "AFC" => "Nota Credito da Fornitore", "AFD" => "Nota Debito da Fornitore", "AOR" => "Ordine a Fornitore", "APR" => "Richiesta di Preventivo a Fornitore"),
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 2 caratteri!',
            'Cambia cliente/fornitore'),
        "add_article" => "Aggiungi un nuovo articolo di magazzino",
        'art_code' => 'Codice articolo',
        'art_barcode' => 'Codice a barre',
        'art_descr' => 'Descrizione',
        'search_for' => 'Cerca per',
        'weight' => 'Peso specifico:',
        'zero_rows' => 'Il documento non contiene righi o prodotti, compila la ricerca articoli nella sezione corpo per aggiungerne, inserisci il valore % per avere una lista completa o per effettuare una ricerca parziale',
        'discount_alert' => 'sconto da anagrafe articoli',
        'last_row' => 'Ultimo rigo inserito',
        'lotmag' => 'certificato',
        'expiry' => 'Data di scadenza',
        'identifier' => 'Lotto o matricola',
        'units' => 'N.Colli',
        'volume' => 'Volume',
        'net_weight' => 'Peso netto',
        'netpay' => 'Netto a pagare',
        'gross_weight' => 'Peso lordo',
        'ins_this' => 'Inserisci ',
        'upd_this' => 'Modifica ',
        'datreg' => 'Data registrazione',
        'datfat' => 'Data della fattura',
		'cod_operaz_value' => array(11=>'',0=>'C0-Acquisto olio da ditta italiana',1=>'C1-Acquisto olio da ditta comunitaria',2=>'C2-Acquisto olio da ditta extracomunitaria',3=>'C3-Carico olio da lavorazione o deposito terzi',5=>'C5-Carico olio da altro stabilimento stessa impresa',8=>'C8-Reso olio da clienti',10=>'C10-Carico olio lampante da recupero'),
        'err' => array(
            'nofold' => 'Non esiste la cartella ID azienda nel percorso data/files!',
            'sampri' => 'Nulla da variare, il prezzo dell\'articolo &egrave lo stesso',
            'noartupd'=>'Articolo non inserito: impossibile modificare!',
            'forceone'=>'L\'articolo di magazzino ha una matricola/numero di serie pertanto la quantit&agrave; &egrave; stata forzata ad 1',
            'norwum' => 'Un rigo &egrave; senza l\'unit&agrave; di misura!',
            'norwde' => 'Un rigo &egrave; senza la descrizione!',
            'nopaga' => 'Non hai selezionato la modalit&agrave; di pagamento!',
            'noforn' => 'Non hai selezionato il fornitore!',
            'samedoc'=> 'Risulta un documento gi&agrave; registrato con questo numero fattura per questo fornitore',
            'docpre' => 'La data di emissione non pu&ograve; essere antecedente a quella dell\'ultimo documento dello stesso tipo emesso!',
			'ddtpre' => 'La data di emissione non pu&ograve; essere antecedente a quella dell\'ultimo DdT emesso!',
            'dtsucc' => 'Stai tentando di modificare il documento con una data successiva a quello dello stesso tipo di documento con numero successivo!',
            'dtante' => 'Stai tentando di modificare il documento con una data antecedente a quello dello stesso tipo di documento con numero precedente!',
            'dtnusc' => 'Stai tentando di modificare il DdT con una data successiva a quella del DdT con numero successivo!',
			'dtnuan' => 'Stai tentando di modificare il DdT con una data antecedente a quella del DdT con numero precedente!',
			'norows' => 'Non ci sono righi per poter emettere il documento!',
			'nocod_operaz' => 'Non è stato selezionato il tipo di operazione SIAN!',
			'nofor_sian' => 'Il fornitore non ha il codice identificativo SIAN!',
			'norecipdestin' => 'Nei movimenti interni è necessario indicare anche il recipiente di destinazione',
			'norecipstocc' => 'Non è stato selezionato il recipiente di stoccaggio',
			'dtintr' => 'La data di inizio trasporto non pu&ograve; essere precedente alla data di emissione!',
			'dttrno' => 'La data di inizio trasporto non &egrave; corretta!',
			'nonudo' => 'Non &egrave; stato inserito il numero del documento!',
			'dregpr' => 'La data di registrazione non pu&ograve; essere antecedente a quella del documento da registrare!',
			'notrack'=> 'Non &egrave; stato possibile caricare il documento per la tracciabilità del prodotto!',
			'soloconf' => 'Operazione effettuabile solo con olio confezionato!'
			),
        'war' => array('serial' => "La quantità è stata forzata ad 1 perché l'articolo prevede il numero di serie",'accounted'=>"Questo documento &egrave; gi&agrave; stato contabilizzato!"),
        'customer' => 'Cliente',
        'search_partner' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 2 caratteri!',
            'Cambia cliente',
            'Scegli un cliente',
            ' C.F.:',
            'Anonimo'
        ),
        'indspe' => 'Indirizzo',
        'seziva' => 'Sezione IVA',
        'numfat' => "Numero fattura",
        'numdoc' => "Numero documento",
        'tipdoc' => 'Tipo di documento',
        'listin' => 'Listino n.',
        'datemi' => 'Data emissione',
        'banapp' => 'Banca d\'appoggio',
        'caumag' => 'Causale magazz.',
        'sconto' => 'Sconto',
        'address' => 'Indirizzo',
        'pagame' => 'Pagamento',
        'orderman' => 'Produzione',
        'codric' => 'C/costo',
        'codvat' => '%IVA',
        'vat_constrain' => '(forza) IVA al ',
        'total' => 'Totale',
        'id_agente' => 'Agente',
        'in_rows_title' => 'inserimento rigo',
        'item' => 'Articolo',
        'search' => ' ricerca per ',
        'in_artsea_value' => array('C' => 'Codice articolo', 'B' => 'Codice a barre', 'D' => 'Descrizione'),
        'tiprig' => 'Tipo rigo',
        'tiprig_value' => array(0 => 'Normale', 1 => 'Forfait', 2 => 'Descrittivo',4=>'Cassa previdenziale',6=>'Testo',51=>'Descrittivo c/allegato'),
        'body_title' => 'corpo',
        'foot_title' => 'totali',
        'nrow' => 'Rigo',
        'upper_row' => 'Tira su!',
        'codart' => 'Codice',
		'codice_fornitore'=>'Cod.Fornitore',
        'descri' => 'Descrizione',
        'unimis' => 'U.M.',
        'quanti' => 'Quantità ',
        'prezzo' => 'Prezzo',
        'ritenuta' => '% Ritenuta',
        'amount' => 'Importo',
        'sconto' => 'Sconto',
        'other_row' => 'Altri dati',
        'conf_row' => ' rigo ',
        'net' => 'Peso netto',
        'units' => 'N.Colli',
        'volume' => 'Volume',
        'taxable' => 'Imponibile',
        'tax' => 'I.V.A.',
        'add_article' => 'Aggiungi nuovo articolo in magazzino',
        'weight' => 'peso',
		'imball'=>"Imballo",
        'spediz'=>"Spedizione",
        'vettor'=>"Vettore",
        'portos'=>"Porto",
        'initra'=>"Inizio trasporto",
        'iniore'=>" ore "
    ),
    "admin_assets.php" =>
    array('title' => 'Acquisto bene ammmortizzabile da fornitore',
        'mesg' => array('La ricerca non ha dato risultati!', 'Inserire almeno 2 caratteri', 'Cambia cliente/fornitore'),
        'info' => array('gg_to_year_end_1' => ' giorni mancano alla fine dell\'anno ',
            'gg_to_year_end_2' => ' quando potranno essere ammortizzati € '),
        'err' => array('regdat' => 'La data di registrazione precede quella della fattura',
            'numfat' => 'Non è stato introdotto il numero della fattura',
            'clfoco' => 'Non è stato scelto il fornitore',
            'pagame' => 'Non è stata scelta la modalità di pagamento',
            'datfat' => 'La data fattura è sbagliata',
            'datreg' => 'La data di registrazione è sbagliata',
            'mas_fixed_assets' => 'Non è stato scelto il mastro immobilizzazioni',
            'mas_found_assets' => 'Non è stato scelto il mastro fondo ammortamenti',
            'mas_cost_assets' => 'Non è stato scelto il mastro costo ammortamenti',
            'deduct_vat' => 'Non è stato scelta l\'aliquota IVA indetraibile',
            'deduct_cost' => 'Non è stato scelto il costo per la quota ammortamenti indeducibile',
            'descri' => 'Non è stata inserita una descrizione del bene acquistato',
            'ss_amm_min' => 'Non è stata inserita la sottospecie della tabella Ammortamenti Ministeriali'),
        'war' => array('update' => 'Sono consentite le modifiche solo ad alcuni campi perché'
            . ' l\'inserimento di questo cespite ha creato sul piano dei conti un sottoconto su ognuno dei'
            . ' tre mastri scelti per l\'immobilizzazione, il fondo ammortamento e per le quote di costi'
            . ' annuali per ammortamenti. Inoltre è stata fatta una registrazione contabile ed IVA '
            . ' pertanto se volete modificare gli altri dati dovrete agire in prima nota sul seguente'
            . ' movimento contabile'),
        'seziva' => "Sez. IVA",
        'indspe' => "Indirizzo",
        'datfat' => "Data fattura",
        'numfat' => "Numero fattura",
        'datreg' => "Data registr.",
        'pagame' => "Pagamento",
        'valamm' => "% ammortamento",
        'mas_fixed_assets' => 'Immobilizzaz.',
        'mas_found_assets' => 'Fondo ammort.',
        'mas_cost_assets' => 'Costo ammort.',
        'des_fixed_assets' => 'Immobilizzazione ',
        'des_found_assets' => 'Fondo ammortamento ',
        'des_cost_assets' => 'Ammortamento ',
        'id_no_deduct_vat' => 'IVA indetraibile',
        'no_deduct_vat_rate' => '% IVA indetraibile',
        'acc_no_deduct_cost' => 'Costo indeducibile',
        'no_deduct_cost_rate' => '% quota indeducibile',
        'descri' => 'Descrizione bene',
        'quantity' => 'Quantità',
        'unimis' => 'Unità Misura',
        'a_value' => 'Prezzo',
        'codvat' => 'I.V.A.',
        'amount' => 'Costo totale del bene € ',
        'ss_amm_min' => 'Ammort.Minister.',
        'super_ammort' => '% Super ammortamento'
    ),
    "accounting_documents.php" =>
    array('title' => 'Genera i movimenti contabili a partire dai documenti d\'acquisto',
        'errors' => array('Data non corretta',
            'Non ci sono documenti da contabilizzare nell\'intervallo selezionato'
        ),
        'vat_section' => ' della sezione IVA n.',
        'date' => 'Fino al :',
        'type' => 'Registro IVA ',
        'type_value' => array('AF' => 'dei documenti di Acquisto'),
        'proini' => 'Protocollo iniziale',
        'profin' => 'Protocollo finale',
        'preview' => 'Anteprima di contabilizzazione',
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
        'errors' => array('La data non &egrave; corretta!',
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
        'descri' => 'Descrizione',
        'amount' => 'Importo'
    ),
    "select_suppliers_status.php" =>
    array('title' => 'Selezione per la visualizzazione e/o la stampa dello scadenzario dei fornitori',
        'print_title' => 'SCADENZARIO FORNITORI ',
        'errors' => array('La data  non &egrave; corretta!',
            'err' => 'Ci sono degli errori che impediscono la stampa'
        ),
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 2 caratteri!',
            'Cambia fornitore'
        ),
		'supplier'=>'Tutti o fornitore',
        'date_ini' => 'Data di riferimento ',
        'header' => array('ID' => '', 'Importo apertura' => '', 'Data Scadenza' => '', 'Importo chiusura' => ''
            , 'Data chiusura' => '', 'Giorni esposizione' => '', 'Stato' => ''
        ),
        'status_value' => array(0 => 'APERTA', 1 => 'CHIUSA', 2 => 'ESPOSTA', 3 => 'SCADUTA', 9 => 'ANTICIPO'),
        'remove' => 'Elimina tutte le partite chiuse di '
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
        'transfer_fees_acc' => 'Conto spese bonifico',
        'transfer_fees' => 'Addebito per bonifico'
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
    array('title' => 'Documenti di trasporto da e verso i fornitori',
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
    "report_acqddt.php" =>
    array('title' => 'Documenti di trasporto ricevuti da fornitori ',
    ),
	"prop_ordine.php" =>
	array('title' => 'Propone un ordine a fornitore calcolato sul venduto',
	      'mesg' => array('La ricerca non ha dato risultati!',
		                  'Inserire almeno 2 caratteri!',
						  'Cambia fornitore',
        ),
		'includi' => 'Includi tutti',
		'fornitore' => 'Fornitore :',
		'giorni_app' => 'Giorni di approvvigionamento',
		'calcola_giorni' => 'Calcola sulle vendite degli ultimi',
		'giorni' => 'giorni',
	),
    "bank_receipts_payment.php" =>
    array('title' => 'Registra disposizione di pagamenti',
        'err' => array(
            'nopay' => 'Non è stata selezionata alcuna scadenza',
            'noacc' => 'Non è stato selezionato il conto corrente di addebito',
            'expif' => 'La data di inizio non può essere successiva a quella di fine scadenza'
        ),
        'entry_date' => 'Data di registrazione',
        'expiry_ini' => 'Data di scadenza iniziale',
        'expiry_fin' => 'Data di scadenza finale',
        'orderby' => 'Ordina per: ',
        'orderby_value' => array(0 => 'Scadenza crescente', 1 => 'Scadenza decrescente',
            2 => 'Importo crescente', 3 => 'Importo decrescente'
        ),
        'target_account' => 'Conto corrente di addebito',
        'transfer_fees_acc' => 'Conto addebito spese bancarie',
        'transfer_fees' => 'Eventuali spese bancarie',
        'description' => 'Descrizione del movimento contabile',
        'description_value' => 'DISPOSTO PAGAMENTO RI.BA.',
        'status_value' => array(0 => 'APERTA', 1 => 'CHIUSA', 2 => 'ESPOSTA', 3 => 'SCADUTA', 9 => 'ANTICIPO'),
        'sel_riba' => 'Seleziona RiBa',
        'sel_other' => 'Seleziona Altri',
        'sel_all' => 'Seleziona TUTTO!',
        'total' => 'TOTALE €',
        'confirm_entry' => 'Conferma la registrazione dei pagamenti selezionati',
        'upd_entry'=> 'Modifica il movimento contabile generato da questo documento'
    ),
    "order_delivered.php" =>
    array('doc_name' => array(
            "AOR" => "Ordine",
            "APR" => "Preventivo"
        ),
        'title' => 'Ricevuto ordine da fornitore',
        'err' => array(
            'norows' => 'Non ci sono articoli da ricevere',
            'noric' => 'Non ci sono righi ricevibili',
            'upres' => 'Rigo con quantità superiore al residuo',
            'numdoc' => 'Non hai indicato il numero di documento'
        ),
        'partner' => 'Fornitore',
        'seziva' => 'Sezione IVA',
        'datemi' => 'Data ',
		'numddt' => 'Numero documento',
        'nrow' => 'n.',
        'sconto' => 'Sconto',
        'preview_title' => 'DA RICEVERE:',
        'codart' => 'Codice',
        'descri' => 'Descrizione',
        'unimis' => 'U.M.',
        'quanti' => 'Quantità ',
        'prezzo' => 'Prezzo',
        'codvat' => 'I.V.A.',
        'amount' => 'Importo',
        'taxable' => 'Imponibile',
        'confirm' => 'Conferma ricevuta merce',
        'checkbox' => 'Questa checkbox dev\'essere selezionata per confermare la quantità ricevuta',
        'total' => 'Totale'
		),
    "acquire_invoice.php" =>
    array('title' => 'Acquisizione file fattura elettronica da fornitore',
		'btn_acquire'=>'ACQUISISCI!',
        'war' => array(
            'ok_suppl' => 'Il fornitore è già in archivio',
            'no_suppl' => 'Ho già questa anagrafica ma è un nuovo fornitore',
            'no_anagr' => "Di questo nuovo fornitore non ho l'anagrafica, utilizzerò questi dati per crearla",
            'no_db' => "Di questo file è stato fatto solo l'upload ma non è stata confermata la registrazione"
        ),
        'err' => array(
            'filmim' => 'Il file deve essere nel formato XML o P7M',
            'invalid_xml' => 'Il contenuto del file non è un XML valido',
            'invalid_fae' => 'Il contenuto del file XML non sembra essere una fattura elettronica',
            'file_exists' => 'Un file con questo nome è stato già stato acquisito',
            'not_mine' => 'La fattura non è stata rilasciata nei confronti di questa azienda',
            'no_upload' => 'File non inviato',
            'no_pagame' => 'Non hai selezionato la modalità di pagamento',
            'no_codric' => 'Non hai selezionato il codice conto di costo',
            'no_codvat' => 'Non hai selezionato l\'aliquota IVA',
            'same_content' => 'Una fattura di questo fornitore, contenente lo stesso numero e data fattura, è già stato acquisito'
        ),
        'head_text1' => "La fatture elettronica: ",
		'head_text2' => " che stai per acquisire è visibile in fondo.<br/> In questo form ti proponiamo di imputare i costi secondo quanto contenuto sugli archivi, e ti invitiamo a controllare ed eventualmente apportare le modifiche opportune. Avrai comunque la possibilità di modificarla sia prima che dopo averla contabilizzata agendo attraverso le apposite interfacce. Dopo l'acquisizione verrai portato sulla lista delle fatture di acquisto già inserite",
        'seziva' => 'Sezione IVA',
        'datreg' => 'Data Registrazione',
		'pagame'=>'Modalità di pagamento',
        'nrow' => 'Rigo',
        'codart' => 'Codice',
        'descri' => 'Descrizione',
        'unimis' => 'U.M.',
        'quanti' => 'Quantità ',
        'prezzo' => 'Prezzo',
        'amount' => 'Importo',
        'sconto' => 'Sconto',
        'taxable' => 'Imponibile',
        'tax' => 'I.V.A.',
        'operation_type' => 'Tipo oper.',
        'conto' => 'Conto',
		'new_acconcile'=>'Cambia conti sui righi con:'
	),
    "admin_broacq.php" =>
    array('title' => 'Inserimento/modifica documenti a fornitori',
        array("DDR" => "D.d.T. di Reso a Fornitore", "RDL" => "D.d.T. ricevuto per ritorno da lavorazione", "DDL" => "D.d.T. c/lavorazione", "AFA" => "Fattura d'Acquisto", "ADT" => "D.d.T. d'Acquisto", "AFC" => "Nota Credito da Fornitore", "AFD" => "Nota Debito da Fornitore", "AOR" => "Ordine a Fornitore", "APR" => "Richiesta di Preventivo a Fornitore"),
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
        "Destinazione",
        "Numero ",
        "La data di registrazione non pu&ograve; essere antecedente a quella del documento da registrare!",
        "La data del documento da registrare non &egrave; corretta!",
        "Non &egrave; stato inserito il numero del documento!",
        "Non &egrave; stato possibile caricare il documento per la tracciabilità del prodotto!",
        "L'articolo di magazzino ha una matricola/numero di serie pertanto la quantit&agrave; &egrave; stata forzata ad 1",
        "Risulta un documento gi&agrave; registrato con questo numero fattura per questo fornitore",
		"Non esiste la cartella ID azienda nel percorso data/files!",
		"Nulla da variare, il prezzo dell'articolo &egrave lo stesso",
		"Modificare il prezzo nella scheda dell'articolo di",
		"Articolo non inserito: impossibile modificare!",
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
        'lotmag' => 'certificato',
        'expiry' => 'Scadenza',
        'identifier' => 'Numero di serie - matricola, se non immesso verrà attribuito automaticamente',
        'units' => 'N.Colli',
        'volume' => 'Volume',
        'net_weight' => 'Peso netto',
        'gross_weight' => 'Peso lordo',
        'datreg' => 'Data registrazione contabile',
        'datfat' => 'Data della fattura',
		'delivery'=>'Consegna'
    ),
    "report_contfor.php" =>
    array('title' => 'Visualizzazione partitario fornitore',
			'datini' => 'Data registrazione inizio  ',
			'datfin' => 'Data registrazione fine ',
			'header' => array('Data' => '', 'ID' => '', 'Descrizione' => '', 'N.Prot.' => '',
            'N.Doc.' => '', 'Data Doc.' => '', 'Dare' => '', 'Avere' => ''),
	        'errors' => array(4=>'Non ci sono movimenti nei limiti selezionati'),

    )
);
?>
