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

$strScript = array("admin_fornit.php" =>
    array('title' => 'Suppliers management',
        'ins_this' => 'Insert new supplier',
        'upd_this' => 'Update supplier',
        'mesg' => array('The search yielded no results!',
            'Insert at least 2 characters!',
            'Changing supplier'
        ),
        'errors' => array('Must indicate the company name',
            'Must indicate the address',
            'Invalid postal code',
            'Must indicate the city',
            'Must indicate the province',
            'Must indicate the sex',
            'L\'IBAN is  incorrect',
            'The IBAN and the nation are different',
            'Tax Code incorrect for an individual',
            'VAT registration number is incorrect',
            'There is already a supplier with the same VAT registration number',
            'Tax code is incorrect',
            'There is already a supplier with the same Tax Code',
            'Tax code missing! Is automatically set with <br />the same value of the VAT registration number',
            'Is an individual, enter the Tax Code',
            'Is there a registry with the same VAT registration number',
            'Is there a registry with the same Tax code',
            'You must choose your payment method',
            'The supplier code is already there, try the entry with the one proposed (plus 1)',
            'The date of birth is wrong',
            'Email address formally wrong',
			'Non è stato descritto il servizio esternalizzato'
        ),
        'link_anagra' => ' Click below to enter the existing registry on the your chart of accounts',
        'codice' => "Code ",
        'ragso1' => "Company Name 1",
        'ragso1_placeholder' => "opp. nome cognome legale rappresentante",
        'ragso2' => "Company Name 2",
        'sedleg' => "Registered Office",
        'external_resp' => 'Responsabile Esterno Trattamento Dati',
        'external_resp_value' => array(1 => 'Si', 0 => 'No'),
        'external_service_descri' => 'Descrizione del servizio esternalizzato',
        'luonas' => 'Birthplace',
        'datnas' => 'Date of birth',
        'pronas' => 'Province of birth',
        'counas' => 'Country of birth',
        'legrap_pf_nome' => "Legal representative ",
        'legrap_pf_title' => "la ragione sociale lasciata vuota verrà riempita con questi campi",
        'sexper' => "Sex/legal person",
        'sexper_value' => array('' => '-', 'M' => 'Male', 'F' => 'Female', 'G' => 'Legal'),
        'indspe' => 'Address',
        'capspe' => 'Postal Code',
        'citspe' => 'City - Province',
        'country' => 'Nation',
        'id_language' => 'Language',
        'id_currency' => 'Currency',
        'telefo' => 'Telephone',
        'fax' => 'Fax',
        'cell' => 'Cellphone',
        'codfis' => 'Tax code',
        'pariva' => 'VAT registration number',
        'pec_email' => 'Posta Elettronica Certificata',
        'e_mail' => 'e mail',
        'cosric' => 'Cost Account',
        'codpag' => 'Payment method*',
        'sconto' => '% Discount applied',
        'banapp' => 'Bank support',
        'portos' => 'Rendered port',
        'spediz' => 'Delivery',
        'imball' => 'Package',
        'listin' => 'Pricelist applied',
        'id_des' => 'Destination ⇒ from registry',
        'destin' => 'Destination ⇒ free description',
        'iban' => 'IBAN',
        'maxrat' => 'Maximum amount of bills',
        'ragdoc' => 'Grouping documents',
        'addbol' => 'Charge the stamp expenses',
        'speban' => 'Charge the bank expenses',
        'spefat' => 'Charge the cost of billing',
        'stapre' => 'Print prices on shipping documents',
        'allegato' => 'Attached VAT - Customers report',
        'yn_value' => array('S' => 'Yes', 'N' => 'No'),
        'aliiva' => 'VAT reduction',
        'ritenuta' => '% Withholding',
        'status' => 'Visibility at the research',
        'status_value' => array('' => 'Yes', 'HIDDEN' => 'Hidden'),
        'annota' => 'Note',
        'id_agente' => 'Agente',
        'operation_type' => 'Operation type'
    ),
    "report_broacq.php" =>
    array('New Preveter', 'New Order',
        'title' => 'Preventivi e ordini',
        'mail_alert0' => 'Invio documento con email',
        'mail_alert1' => 'Hai scelto di inviare una e-mail all\'indirizzo: ',
        'mail_alert2' => 'con allegato il seguente documento:',
    ),
    "report_debiti.php" =>
    array('title' => 'List of debts to suppliers',
        'start_date' => 'Year-start',
        'end_date' => 'Year-end',
        'codice' => 'Code',
        'partner' => 'Supplier',
        'telefo' => 'Telephone',
        'mov' => 'N.Entries',
        'dare' => 'Debit',
        'avere' => 'Credit',
        'saldo' => 'Balance',
        'pay' => 'Pay',
        'statement' => 'Statement',
        'pay_title' => 'Pays the debt with ',
        'statement_title' => 'Print the statement of '
    ),
    "admin_docacq.php" =>
    array('title' => 'Inserimento/modifica documenti a fornitori',
        array("DDR" => "D.d.T. di Reso a Fornitore", "DDL" => "D.d.T. c/lavorazione", "AFA" => "Fattura d'Acquisto", "ADT" => "D.d.T. d'Acquisto", "AFC" => "Nota Credito da Fornitore", "AOR" => "Ordine a Fornitore", "APR" => "Richiesta di Preventivo a Fornitore"),
        'mesg' => array('The search yielded no results!',
            'Insert at least 2 characters!',
            'Changing customer / supplier'
        ),
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
        "It was not possible to load the document for product traceability!",
        "goods item have a serial number, therefore the quantity has been forced to 1",
        /** ENRICO FEDELE */
        "add_article" => "Add a new article",
        'art_code' => 'Article code',
        'art_barcode' => 'Bar code',
        'art_descr' => 'Description',
        'search_for' => 'Search by',
        'weight' => 'Weight:',
        'zero_rows' => 'The document no contains rows or products. For add  them to the body enter the "%" value for a complete list or to make a partial search',
        'discount_alert' => 'discount taken from article\'s informations',
        'last_row' => 'Last row',
        'lotmag'=>'certificate',
        'expiry'=>'Expiry',
        'identifier'=>'Serial number - if you don\'t entered it will be assigned automatically'
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
        'datreg' => "Data registr.",
        'pagame' => "Pagamento",
        'valamm' => "% ammortamento",
        'numfat' => "Numero fatt.",
        'mas_fixed_assets' => 'Immobilizzaz.',
        'mas_found_assets' => 'Fondo ammort.',
        'mas_cost_assets' => 'Costo ammort.',
        'des_fixed_assets' => 'Immobilizzazione ',
        'des_found_assets' => 'Fondo ammortamento ',
        'des_cost_assets' => 'Quota ammortamento ',
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
        'super_ammort'=>'% Super ammortamento'
    ),
    "accounting_documents.php" =>
    array('title' => 'Create movements accounting from taxable documents',
        'errors' => array('Incorrect date',
            'There are no documents to be written in the selected'
        ),
        'vat_section' => ' of VAT section n.',
        'date' => 'Until :',
        'type' => 'VAT register ',
        'type_value' => array('A' => 'of Purchase Invoices'),
        'proini' => 'Initial protocol',
        'profin' => 'Final protocol',
        'preview' => 'Accounting preview',
        'date_reg' => 'Date',
        'protoc' => 'Protocol',
        'doc_type' => 'Type',
        'doc_type_value' => array('FAD' => 'DEFERRED INVOICE TO THE customER',
            'FAI' => 'IMMEDIATE INVOICE TO THE customER',
            'FNC' => 'CREDIT NOTE TO THE customER',
            'FND' => 'DEBT NOTE TO THE customER',
            'VCO' => 'FEES',
            'VRI' => 'RECEIVED',
            'AFA' => 'PURCHASE INVOICE',
            'AFC' => 'CREDIT NOTE FROM PURCHASE',
            'AFD' => 'DEBT NOTE FROM PURCHASE'
        ),
        'customer' => 'Supplier',
        'taxable' => 'Taxable',
        'vat' => 'VAT',
        'stamp' => 'Stamps on bills',
        'tot' => 'Total'
    ),
    "report_schedule_acq.php" =>
    array('title' => 'Lista dei movimenti delle partite',
        'header' => array('ID' => 'id',
            'Identificativo partita' => "id_tesdoc_ref",
            'Movimento contabile apertura (documento)' => "id_rigmoc_doc",
            'Movimento contabile chiusura (pagamento)' => "id_rigmoc_pay",
            'Importo' => "amount",
            'Scadenza' => "expiry"
        ),
    ),
    "delete_docacq.php" =>
    array('title' => 'Eliminazione Documento d\'Acquisto' ),
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
            'Options' => ''
        ),
        'status_value' => array(0 => 'OPEN', 1 => 'CLOSED', 2 => 'RISK', 3 => 'EXPIRED', 9 => 'ANTICIPO'),
        'total_open' => 'Total amount for open items'
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
        'date_ini' => 'Data di riferimento ',
        'header' => array('ID' => '', 'Importo apertura' => '', 'Data Scadenza' => '', 'Importo chiusura' => ''
            , 'Data chiusura' => '', 'Giorni esposizione' => '', 'Stato' => ''
        ),
        'status_value' => array(0 => 'APERTA', 1 => 'CHIUSA', 2 => 'ESPOSTA', 3 => 'SCADUTA', 9 => 'ANTICIPO'),
        'remove' => 'Elimina tutte le partite chiuse di '
    ),
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
            'Il saldo contabile è diverso da quello dello scadenzario,<br> se vuoi registrare la riscossione di un documento presente solo in contabilità fallo da qui:',
            'Nessun importo è stato pagato!',
            "Non è stato selezionato il conto per l'incasso",
            'Stai tentando di inserire il pagamento ad un fornitore senza movimenti'
        ),
        'descr_mov' => 'Descrizione del movimento<br>(vuoto per descrizione standard)',
		'transfer_fees_acc'=>'Conto spese bonifico',
		'transfer_fees'=>'Addebito per bonifico'
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
    )
);
?>
