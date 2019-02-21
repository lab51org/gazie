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

$strScript = array("select_liqiva.php" =>
    array('title' => 'Selezione per la visualizzazione e/o la stampa della liquidazione IVA periodica',
        'errors' => array('La data  non &egrave; corretta!',
            'La data di inizio non pu&ograve; essere successiva alla data di fine !'
        ),
        'page_ini' => 'N. pagina iniziale',
        'sta_def' => 'Stampa definitiva',
        'sta_def_title' => 'Se selezionato modifica il valore dell\'ultima pagina stampata di questo registro in configurazione azienda',
        'descri' => 'Descrizione',
        'descri_value' => array('M' => 'del mese di ', 'T' => 'del trimestre '
        ),
        'date_ini' => 'Data registrazione inizio  ',
        'cover' => 'Stampa la copertina',
        'date_fin' => 'Data registrazione fine ',
        'header' => array('Sezione' => '', 'Registro' => '', 'Descrizione aliquota' => '', 'Imponibile' => '',
            'Aliquota' => '', 'Imposta' => '', 'Indetraibile' => '', 'Totale' => ''
        ),
        'regiva_value' => array(0 => 'Nessuno', 2 => 'Fatture di Vendita', 4 => 'Corrispettivi', 6 => 'Fatture d\'Acquisto',9=>'Versamenti IVA'),
        'of' => ' del ',
        'tot' => ' TOTALE ',
        't_pos' => ' I.V.A A DEBITO',
        't_neg' => ' I.V.A A CREDITO',
        'carry' => 'Credito da periodo precedente',
        'pro_rata' => 'Detrazione sugli acquisti (PRO RATA)'
    ),
    "stampa_liqiva.php" =>
    array('title' => 'Liquidazione IVA periodica',
        'cover_descri' => 'Registro riepilogativo dell\'I.V.A. dell\'anno ',
        'page' => 'Pagina',
        'sez' => 'Sezione',
        'regiva_value' => array(0 => 'Nessuno', 2 => 'Registro delle Fatture di Vendita', 4 => 'Regitro dei Corrispettivi', 6 => 'Registro delle Fatture d\'Acquisto',9=>'Versamenti IVA'),
        'code' => 'Codice',
        'descri' => 'Descrizione aliquota I.V.A.',
        'imp' => 'Imponibile',
        'iva' => 'Imposta',
        'rate' => '%',
        'ind' => 'Indetraibile',
        'isp' => 'Inesigibile',
        'tot' => 'Totale',
        't_reg' => 'Totale I.V.A. del registro ',
        't_pos' => ' I.V.A A DEBITO',
        't_neg' => ' I.V.A A CREDITO',
        'inter' => 'Maggiorazione a titolo di interessi ',
        'pay' => ' a pagare',
        'carry' => 'Credito da periodo precedente',
        'pro_rata' => 'PRO RATA',
        'pay_date' => 'Pagata in data ',
        'co' => 'presso ',
        'abi' => ' A.B.I. ',
        'cab' => ' C.A.B. '
    ),
    "select_partit.php" =>
    array('title' => 'Selezione per la visualizzazione e/o la stampa dei partitari',
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 2 caratteri!',
            'Cambia cliente/fornitore'
        ),
        'errors' => array('La data  non &egrave; corretta!',
            'La data di inizio dei movimenti contabili da stampare non pu&ograve; essere successiva alla data dell\'ultimo !',
            'La data di stampa non pu&ograve; essere precedente a quella dell\'ultimo movimento!',
            'Il conto iniziale non pu&ograve; essere successivo a quello finale!',
            'Non ci sono movimenti nei limiti selezionati'
        ),
        'date' => 'Data di stampa ',
        'master_ini' => 'Mastro inizio ',
        'account_ini' => 'Conto inizio ',
        'date_ini' => 'Data registrazione inizio  ',
        'master_fin' => 'Mastro fine ',
        'account_fin' => 'Conto fine ',
        'date_fin' => 'Data registrazione fine ',
        'selfin' => 'Copia conto iniziale',
        'header1' => array('Conto' => '', 'Num.Mov.' => '', 'Descrizione' => '',
            'Dare' => '', 'Avere' => '', 'Saldo<br />progressivo' => ''
        ),
        'header2' => array('Data' => '', 'ID' => '', 'Descrizione' => '', 'N.Prot.' => '',
            'N.Doc.' => '', 'Data Doc.' => '', 'Dare' => '', 'Avere' => '',
            'Saldo<br />progressivo' => ''
        )
    ),
    "admin_caucon.php" =>
    array('title' => 'Gestione delle causali contabili',
        'ins_this' => 'Inserisci una nuova causale contabile ',
        'upd_this' => 'Modifica della causale contabile',
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 2 caratteri!',
            'Cambia cliente/fornitore'
        ),
        'err' => array('codice_emp' => 'Inserire un codice valido!',
            'descri' => '&Egrave; necessario inserire una descrizione!',
            'codice_exi' => 'Codice causale esistente!',
            'clfoco_ref' => "C'&egrave; almeno un rigo con conto non definito!",
            'CHI' => 'Codice riservato alla CHIUSURA AUTOMATICA CONTI!',
            'APE' => 'Codice riservato alla APERTURA AUTOMATICA CONTI!',
            'AMM' => 'Codice riservato al RILEVAMENTO AMMORTAMENTI DI FINE ANNO!'
        ),
        'head' => 'Conti movimentati ',
        'codice' => 'Codice causale *',
        'descri' => 'Descrizione *',
        'insdoc' => 'Inserimento dati documento di riferimento',
        'insdoc_value' => array(0 => 'No', 1 => 'Si'),
        'regiva' => 'Registro I.V.A.',
        'regiva_value' => array(0 => 'Nessuno', 2 => 'Fatture di Vendita', 4 => 'Corrispettivi', 6 => 'Fatture d\'Acquisto',9=>'Versamenti IVA'),
        'operat' => 'Operatore',
        'operat_value' => array(0 => 'Non opera', 1 => 'Somma', 2 => 'Sottrae'),
        'pay_schedule' => 'Partite aperte (scadenzario)',
        'pay_schedule_value' => array(0 => 'Non opera', 1 => 'Documento vendita/acquisto (apre)', 2 => 'Pagamento (chiude)'),
        'clfoco_mastro' => 'Mastro',
        'clfoco_sub' => 'Sottoconto',
        'tipim' => 'Tipo importo',
        'tipim_value' => array('' => '', 'A' => 'Totale', 'B' => 'Imponibile', 'C' => 'Imposta'),
        'daav' => 'DARE/AVERE',
        'daav_value' => array('D' => 'DARE', 'A' => 'AVERE'),
        'report' => 'Lista delle causali contabili',
        'del_this' => 'Causale contabile ',
        'add_row' => 'Aggiungi un rigo'
    ),
    "admin_piacon.php" =>
    array('title' => 'Gestione del piano dei conti',
        'ins_this' => 'Inserisci un nuovo conto',
        'upd_this' => 'Modifica il conto ',
        'errors' => array('Il codice non &egrave; valido!',
            'Il codice scelto &egrave; gi&agrave; stato usato!',
            'Non &egrave; stata inserita la descrizione!'
        ),
        'codice' => "Codice ",
        'mas' => "Mastro",
        'sub' => "Sottoconto",
        'descri' => "Descrizione",
        'ceedar' => "Riclassificazione Bilancio CEE / DARE",
        'ceeave' => "Riclassificazione Bilancio CEE / AVERE",
		'paymov' => "Apre scadenzario",
		'paymov_value' => array(''=>'No','D'=>'Apre in DARE (portafoglio attivo)','A'=>'Apre in AVERE (portafoglio passivo)'),
        'annota' => "Note"
    ),
    "admin_movcon.php" =>
    array('title' => 'Gestione dei movimenti contabili',
        'ins_this' => 'Inserisci un nuovo movimento contabile ',
        'upd_this' => 'Modifica il movimento contabile',
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 2 caratteri!',
            'Cambia cliente/fornitore'
        ),
        'errors' => array('Almeno un rigo non ha conti!',
            'Almeno un rigo ha valore zero!',
            'Il movimento &egrave; sbilanciato!',
            'Il totale dei righi in dare non dev\'essere 0 !',
            'Il totale dei righi in avere non dev\'essere 0 !',
            'Il movimento IVA &egrave; nullo !',
            'I movimenti IVA hanno una somma diversa da quella del movimento contabile!',
            'E\' necessario inserire una descrizione del movimento!',
            'La data di registrazione del movimento  non &egrave; corretta!',
            'La data del documento  non &egrave; corretta!',
            'Hai dimenticato di inserire il numero di protocollo!',
            'Hai dimenticato di inserire il numero del documento!',
            'La data del documento non dev\'essere successiva a quella del movimento!',
            'ATTENZIONE stai modificando un movimento che interessa un registro IVA!',
            'Stai tentando di registrare un documento gi&agrave; registrato',
            'Il totale dei movimenti dello scadenziario non coincidono con l\'importo del rigo ad esso relativo',
            'Se stai registrando una fattura con reverse charge non è possibile avere più righi IVA'
        ),
        'id_testata' => 'Numero di movimento',
        'date_reg' => 'Data di registrazione',
        'datliq' => 'Data liquidazione',
        'descri' => 'Descrizione',
        'caucon' => 'Causale contabile',
        'v_caucon' => 'Conferma la Causale!',
        'insdoc' => 'Riferimenti al documento',
        'insdoc_value' => array(0 => 'No', 1 => 'Si'),
        'regiva' => 'Registro I.V.A.',
        'regiva_value' => array(0 => 'Nessuno', 2 => 'Fatture di Vendita', 4 => 'Corrispettivi', 6 => 'Fatture d\'Acquisto',9=>'Versamenti IVA'),
        'operat' => 'Operatore',
        'operat_value' => array(0 => 'Non opera', 1 => 'Somma', 2 => 'Sottrae'),
        'date_doc' => 'Data del documento',
        'seziva' => 'Sezione IVA',
        'protoc' => 'Protocollo',
        'numdoc' => 'Numero',
        'partner' => 'Cliente / Fornitore',
        'insiva' => 'Movimenti I.V.A.',
        'vat' => 'Aliquota I.V.A.',
        'taxable' => 'Imponibile',
        'tax' => 'Imposta',
        'mas' => "Mastro",
        'sub' => "Conto",
        'amount' => 'Importo',
        'daav' => 'Dare/Avere',
        'daav_value' => array('D' => 'DARE', 'A' => 'AVERE'),
        'bal_title' => "Bilancia rispetto a questo valore!",
        'bal' => "Bilanciato",
        'addval' => "Incrementa il valore di ",
        'subval' => "Diminuisci il valore di ",
        'zero' => "Movimenti a zero!",
        'diff' => "Differenza",
        'tot_d' => 'Totale DARE',
        'tot_a' => 'Totale AVERE',
        'visacc' => 'Visualizza il partitario',
        'report' => 'Lista dei Movimenti Contabili',
        'del_this' => 'Movimenti Contabili',
        'sourcedoc' => 'Documento che ha originato il movimento',
        'source' => 'Origine',
        'customer_receipt' => 'Stampa la ricevuta',
        'ins_other' => 'Altri dati',
        'reverse_charge' => 'Reverse charge',
        'operation_type' => 'Tipo di operazione',
    ),
    "report_piacon.php" =>
    array('title' => 'Piano dei conti',
        'ins_this' => 'Inserisci un nuovo conto',
        'view_this' => 'Visualzza e/o stampa i partitari',
        'print_this' => 'Stampa il piano dei conti',
        'header' => array('Mastro' => '', 'Conto' => '', 'Descrizione' => '', 'Dare' => '',
            'Avere' => '', 'Saldo' => '', 'Visualizza<br />e/o stampa' => '',
            'Cancella' => ''),
        'msg1' => 'Ricorda che devi introdurre i mastri per le attivit&agrave; compresi tra 100 e 199, le passivit&agrave; tra 200 e 299, i costi tra 300 e 399, i ricavi tra 400 e 499 e i conti d\'ordine o transitori tra 500 e 599. Clicca sulle righe dei mastri (in rosso) per espandere e visualizzare i sotto conti.',
        'msg2' => 'Saldi relativi all\'anno',
        /** ENRICO FEDELE */
        'edit_master' => 'Modifica il mastro',
        'edit_account' => 'Modifica il conto',
        'print_ledger' => 'Visualizza e stampa il paritario'
    /** ENRICO FEDELE */
    ),
    "select_regiva.php" =>
    array('title' => 'Selezione per la visualizzazione e/o la stampa dei registri IVA',
        'errors' => array('La data  non &egrave; corretta!',
            'La data di inizio non pu&ograve; essere successiva alla data di fine !',
            'P' => 'La sequenza dei numeri di protocollo non &egrave; corretta',
            'N' => 'La sequenza dei numeri dei documenti non &egrave; corretta',
            'T' => 'C\'&egrave; un movimento IVA senza aliquota',
            'err' => 'Ci sono degli errori che non giustificano la stampa del registro'
        ),
        'vat_reg' => 'Registro IVA da stampare:',
        'vat_reg_value' => array(2 => 'Fatture di Vendita', 4 => 'Corrispettivi', 6 => 'Fatture d\'Acquisto',9=>'Lista dei versamenti'),
        'vat_section' => 'Sezione IVA ',
        'page_ini' => 'N. pagina iniziale',
        'jump' => 'Riepilogo ad ogni salto periodo',
        'jump_title' => 'Se selezionato stampa sul PDF tutti i riepiloghi periodici',
        'sta_def' => 'Stampa definitiva',
        'sta_def_title' => 'Se selezionato modifica il valore dell\'ultima pagina stampata di questo registro in configurazione azienda',
        'descri' => 'Descrizione',
        'descri_value' => array('M' => 'del mese di ', 'T' => 'del trimestre '
        ),
        'date_ini' => 'Data inizio periodo ',
        'sem_ord' => ' Dettaglio ',
        'sem_ord_value' => array(0 => ' Senza conti costi/ricavi ', 1 => ' Con conti costi/ricavi '
        ),
        'cover' => 'Stampa la copertina',
        'date_fin' => 'Data fine periodo ',
        'header' => array('Protocollo'=>'','ID-Data Reg.'=>'','Descrizione documento'=>'','Cliente/Fornitore'=>'','Imponibile'=>'','Aliquota'=>'','Imposta'=> '','Liquidazione'=>''),
        'of' => ' del ',
        'tot' => ' TOTALE ',
        'reg' => 'REGISTRO',
        'liq' => 'LIQUIDABILE',
        't_gen' => 'TOTALE REGISTRO IVA DEL PERIODO',
		't_liq'=>'TOTALE LIQUIDABILE NEL PERIODO'
    ),
    "stampa_regiva.php" =>
    array('title' => array(2 => 'Registro delle fatture di vendita ',
            4 => 'Registro dei corrispettivi ',
            6 => 'Registro degli acquisti '),
        'cover_descri' => array(2 => 'Registro delle fatture di vendita dell\'anno',
            4 => 'Registro dei corrispettivi dell\'anno ',
            6 => 'Registro degli acquisti dell\'anno '),
        'partner_descri' => array(2 => 'Ragione Sociale Cliente',
            4 => 'Descrizione',
            6 => 'Ragione Sociale Fornitore'),
        'vat_section' => ' sez. IVA n.',
        'page' => 'pag.',
        'top_carry' => 'da riporto : ',
        'bot_carry' => 'a riporto : ',
        'top' => array('prot' => 'N.Prot.',
            'dreg' => 'Data Reg.',
            'desc' => 'N.Documento/Descr.',
            'ddoc' => 'DataDoc/Tipo Op.',
            'txbl' => 'Imponibile',
            'perc' => 'Perc.',
            'tax' => 'Imposta',
            'tot' => 'Totale',
			'liq' => 'Liquidabile'),
        'of' => ' del ',
        'vat_castle_title' => ' RIEPILOGO TOTALI PER ALIQUOTE ',
        'descri' => 'descrizione',
        'taxable' => 'imponibile',
        'tax' => 'imposta',
        'tot' => 'totale',
        'tot_descri' => 'TOTALE VISUALIZZATO SU REGISTRO',
        'tot_liqui' => 'TOTALE LIQUIDABILE NEL PERIODO',
        'acc_castle_title' => ' RIEPILOGO TOTALI CONTI ',
        'amount' => 'importo',
        'operation_type_title' => 'Legenda dei tipi di operazioni',
        'operation_type_code' => 'Codice',
        'operation_type_name' => 'Descrizione',
        'operation_type_other' => 'OPERAZIONI NORMALI'
    ),
    "select_libgio.php" =>
    array('title' => 'Selezione per la visualizzazione e/o la stampa dei Libro giornale',
        'errors' => array('La data di inizio non &egrave; corretta!',
            'La data di fine non &egrave; corretta!',
            'La data di inizio non pu&ograve; essere successiva alla data di fine !'
        ),
        'pagini' => 'N. pagina iniziale',
        'stadef' => 'Stampa definitiva',
        'stadef_title' => 'Se selezionato modifica il valore dell\'ultima pagina stampata in configurazione azienda',
        'date_ini' => 'Data registrazione inizio  ',
        'cover' => ' stampa la copertina -> ',
        'date_fin' => 'Data registrazione fine ',
        'valdar' => 'Dare (inizio)',
        'valave' => 'Avere (inizio)',
        'nrow' => 'Numero righe:',
        'tot_a' => ' Totale Avere ',
        'tot_d' => ' Totale Dare '
    ),
    "select_situazione_contabile.php" =>
    array('title' => 'Analisi situazione contabile',
        'date' => 'Data Stampa',
        'id_anagra' => 'Anagrafica (vuoto per tutti)',
        'clfr' => 'Stampare situazione di',
        'aperte_tutte' => 'Cosa Stampare',
        'id_agente' => 'Agente',
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 2 caratteri!',
            'Cambia anagrafica'),
        'errors' => array('La data  non &egrave; corretta!',
            'Non sono stati trovati movimenti!'
        ),
        'header' => array('Cliente' => '', 'ID Partita' => '', 'Status' => '', 'Mov.Cont.' => '', 'Descrizione' => '',
            'N.Doc.' => '', 'Data Doc.' => '', 'Data Reg.' => '', 'Dare' => '', 'Avere' => '',
            'Scadenza' => '', 'Opzioni' => ''
        ),
        /** ENRICO FEDELE */
        'status_value' => array(0 => 'Chiusa', 1 => 'Aperta'),
    ),
    "select_debiti_crediti.php" =>
    array('title' => 'Analisi debiti/crediti',
        'date' => 'Data Stampa',
        'id_anagra' => 'Anagrafica (vuoto per tutti)',
        'clfr' => 'Stampare situazione di',
        'aperte_tutte' => 'Cosa Stampare',
        'id_agente' => 'Agente',
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 2 caratteri!',
            'Cambia anagrafica'),
        'errors' => array('La data  non &egrave; corretta!',
            'Non sono stati trovati movimenti!'
        ),
        'header' => array('Cliente' => '', 'Dare' => '', 'Avere' => '', 'Saldo' => ''),
        /** ENRICO FEDELE */
        'status_value' => array(0 => 'Chiusa', 1 => 'Aperta'),
    ),
    "comunicazione_liquidazioni_periodiche.php" =>
    array('title' => "Comunicazione liquidazioni periodiche IVA ",
        'war' => array('download' => 'Il file xml è pronto per essere utilizzato:'),
        'err' => array('nodata' => 'Non ci sono dati per liquidare questo trimestre',
            'eseguita' => 'Il file XML per comunicare la liquidazione è già stato generato, puoi modificarlo andando sulla Lista'),
        'codice_fiscale' => 'Codice Fiscale del contribuente',
        'partita_iva' => 'Pertita IVA del contribuente',
        'anno_imposta' => 'Anno di imposta',
        'periodo' => 'Periodo di riferimento',
        'periodo_val' => array(1 => 'PRIMO', 2 => 'SECONDO', 3 => 'TERZO', 4 => 'QUARTO'),
        'ivam_t_val' => array('M' => 'MESE', 'T' => 'TRIMESTRE'),
        'sub' => 'Subforniture',
        'eventi' => 'Eventi eccezionali',
        'vp2' => 'Totale operazioni attive (al netto dell’IVA)',
        'vp3' => 'Totale operazioni passive (al netto dell’IVA)',
        'vp4' => 'IVA esigibile',
        'vp5' => 'IVA detratta',
        'vp6' => 'IVA dovuta',
        'vp6c' => 'o a credito',
        'vp7' => 'Debito periodo precedente non superiore 25,82 euro',
        'vp8' => 'Credito periodo precedente',
        'vp9' => 'Credito anno precedente',
        'vp10' => 'Versamenti auto UE',
        'vp11' => 'Crediti d’imposta',
        'vp12' => 'Interessi dovuti per liquidazioni trimestrali',
        'vp13' => 'Acconto dovuto',
        'vp13m' => 'Metodo',
        'vp13m_val' => array(0 => '', 1 => 'Storico', 2 => 'Previsionale', 3 => 'Analitico',4 => 'Settore Utenze'),
        'vp14' => 'IVA da versare',
        'vp14c' => 'o a credito',
    ),
    "report_comunicazioni_liquidazioni_periodiche.php" =>
    array('title' => "Lista delle comunicazioni delle liquidazioni periodiche dell'IVA",
        'anno' => 'Anno',
        'periodo' => 'Periodo',
        'periodo_val' => array(1 => 'Primo', 2 => 'Secondo', 3 => 'Terzo', 4 => 'Quarto'),
        'periodicita_val' => array('M' => 'Mese', 'T' => 'trimestre'),
        'vp4' => 'IVA esigibile',
        'vp5' => 'IVA detratta',
        'vp7-13' => 'Altro',
        'vp14' => 'Saldo'
    ),
    "comunicazione_dati_fatture.php" =>
    array('title' => "Comunicazione dati fatture ",
        'war' => array('download' => 'Il file ZIP contenente i due file XML è pronto per essere utilizzato:'),
        'err' => array('nodata' => 'Non ci sono dati per comunicare dati di questo periodo',
            'eseguita' => 'I file XML per comunicare i dati delle fatture sono già stati generati, puoi modificarli andando sulla Lista'),
        'codice_fiscale' => 'Codice Fiscale',
        'partita_iva' => 'Partita IVA',
        'anno_imposta' => 'Anno di imposta',
        'periodicita' => 'Periodicità',
        'periodicita_value' => array('T' => 'Trimestrale', 'S' => 'Semestrale'),
        'trimestre_semestre' => 'Periodo di riferimento',
        'trimestre_semestre_value' => array('T'=>array(1 => 'Primo (gennaio-marzo)', 2 => 'Secondo (aprile-giugno)', 3 => 'Terzo (luglio-settembre)', 4 => 'Quarto (ottobre-dicembre)'),'S'=>array(1 => 'Primo (gennaio-giugno)', 2 => 'Secondo (luglio-dicembre)')),
        'CessionarioCommittente' => 'Cessionario Committente',
        'CedentePrestatore' => 'Cedente Prestatore',
        'TipoDocumento' => 'Tipo Documento',
        'Data' => 'Data',
        'Numero' => 'Numero',
        'DataRegistrazione' => 'Data Registrazione',
        'DatiGenerali' => 'Dati Generali',
        'DatiRiepilogo' => 'Dati Riepilogo',
        'ImponibileImporto' => 'Imponibile o importo',
        'NonImponibile' => 'Non Impon.',
        'Imposta' => 'Imposta',
        'Aliquota' => 'Aliquota',
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
            "Non si pu&ograve; generare il File perch&egrave; sono stati rilevati errori da correggere (vedi in seguito)",
            "Non sono stati trovati movimenti IVA da riportare in elenco!",
            "Non ho trovato movimenti relativi a clienti e/o fornitori per il periodo selezionato!",
            'legrap_pf_nome'=>"Sul cliente e/o fornitore persona fisica manca il nome e/o cognome del legale rappresentante!"),
        'ok' => 'Genera il file ZIP contenente i due file XML per la comunicazione dati fatture (quadri DTE e DTR)',
    ),
    "report_comunicazioni_dati_fatture.php" =>
    array('title' => "Lista delle comunicazioni periodiche dei dati fatture",
        'anno' => 'Anno',
        'periodicita' => 'Periodicità',
        'periodicita_value' => array('T' => 'Trimestrale', 'S' => 'Semestrale'),
        'trimestre_semestre' => 'Periodo',
        'trimestre_semestre_value' => array('T'=>array(1 => 'Primo (gennaio-marzo)', 2 => 'Secondo (aprile-giugno)', 3 => 'Terzo (luglio-settembre)', 4 => 'Quarto (ottobre-dicembre)'),'S'=>array(1 => 'Primo (gennaio-giugno)', 2 => 'Secondo (luglio-dicembre)'))
    ),
    "comunicazioni_doc.php" =>
    array('title' => "Comunicazioni dati",
        'text'=>"In questo sottomenù troverete tutte le procedure per l'invio dati in formato XML all'Agenzia delle Entrate"
    )
);
?>