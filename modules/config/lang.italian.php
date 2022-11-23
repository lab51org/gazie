<?php

/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2022 - Antonio De Vincentiis Montesilvano (PE)
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
    "admin_aziend.php" =>
    array('title' => 'Gestione delle aziende',
        'ins_this' => 'Inserisci l\'azienda',
        'upd_this' => 'Modifica l\'azienda ',
        'err' => array(
            'ragso1' => '&Egrave; necessario indicare la Ragione Sociale',
            'sexper' => '&Egrave; necessario indicare il sesso',
            'datnas' => 'La data di nascita &egrave; sbagliata',
            'indspe' => '&Egrave; necessario indicare l\'indirizzo',
            'citspe' => '&Egrave; necessario indicare la citt&agrave;',
            'prospe' => '&Egrave; necessario indicare la provincia',
            'codfis' => 'Il codice fiscale &egrave; formalmente errato',
            'cf_sex' => 'Il codice fiscale non &egrave; di una persona fisica',
            'pariva' => 'La partita IVA &egrave; formalmente errata',
            'cf_pg' => 'Il codice fiscale non &egrave; di una persona giuridica',
            'cf_emp' => '&Egrave; necessario indicare il codice fiscale',
            'regdat' => 'Il file immagine dev\'essere nel formato PNG',
            'imasize' => 'il file dell\'immagine ha una dimensione maggiore di 64kb',
            'colore' => 'Il colore che hai scelto ha una luminosit&agrave; minore di 408 (hex88+88+88)',
            'image' => 'Devi inserire una immagine per il logo aziendale',
            'capspe' => 'Il codice di avviamento postale (CAP) &egrave; sbagliato',
            'pec' => 'Indirizzo posta elettronica certificata formalmente sbagliato',
            'e_mail' => 'Indirizzo email formalmente sbagliato',
            'web_url' => 'Indirizzo web formalmente sbagliato',
            'cod_ateco' => 'Codice ATECO 2007 non valido',
            'sez_rc' => 'Il sezionale delle fatture immediate non può essere lo stesso di quello usato per il reverse charge'
        ),
        'codice' => "Codice ",
        'ragso1' => "Ragione Sociale 1",
        'ragso2' => "Ragione Sociale 2",
        'image' => "Logo Aziendale<br /> (jpg,png,gif) circa 400x400px max 64kb",
        'intermediary' => "Intermediario presso l'Agenzia delle Entrate",
        'sedleg' => "Sede legale",
        'legrap_pf_nome' => "Legale rappresentante (Nome - Cognome)",
        'sexper' => "Sesso/pers.giuridica ",
        'sexper_value' => array('' => '-', 'M' => 'Maschio', 'F' => 'Femmina', 'G' => 'Giuridica'),
        'datnas' => 'Data di nascita',
		'order_type_label'=>'Tipo di produzione',
        'luonas' => 'Luogo di nascita - Provincia',
        'indspe' => 'Indirizzo',
        'latitude' => 'Latitudine',
        'longitude' => 'Longitude',
        'capspe' => 'Codice Postale',
        'citspe' => 'Citt&agrave; - Provincia',
        'country' => 'Nazione',
        'id_language' => 'Lingua',
        'id_currency' => 'Valuta',
        'telefo' => 'Telefono',
        'fax' => 'Fax',
        'codfis' => 'Codice Fiscale',
        'pariva' => 'Partita I.V.A.',
        'REA_ufficio' => 'Provincia ufficio REA (elem.1.2.4.1 FAE)',
        'REA_numero' => 'Numero REA (elem.1.2.4.2 FAE)',
        'REA_capitale' => 'Capitale Sociale (elem.1.2.4.3 FAE)',
        'REA_socio' => 'Socio (elem.1.2.4.4 FAE)',
        'REA_socio_value' => array(''=>'-----------', 'SU' => 'Socio Unico', 'SM' => 'più Soci'),
        'REA_stato' => 'Stato (se in liquidazione elem.1.2.4.5 FAE)',
        'REA_stato_value' => array('LN' => 'non in liquidazione', 'LS' => 'in liquidazione'),
        'pec' => 'Posta Elettronica Certificata',
        'e_mail' => 'e mail',
        'web_url' => 'Web url<br />(es: https://nomeazienda.it)',
        'gazSynchro' => 'Moduli sincronizzazioni ecommerce e/o devices',
        'cod_ateco' => 'Codice attivit&agrave; (ATECOFIN)',
        'regime' => 'Regime contabile',
        'regime_value' => array('0' => 'Ordinario', '1' => 'Semplificato'),
        'fiscal_reg' => 'Regime fiscale',
        'decimal_quantity' => 'N&ordm; decimali sulla quantit&agrave;',
        'decimal_quantity_value' => array(0, 1, 2, 3, 9 => 'Float'),
        'decimal_price' => 'N&ordm; decimali sui prezzi',
        'stock_eval_method' => 'Metodo di valorizzazione magazzino',
        'stock_eval_method_value' => array(0 => 'Standard', 1 => 'Prezzo medio ponderato', 2 => 'LIFO', 3 => 'FIFO'),
        'mascli' => 'Mastro clienti ',
        'masfor' => 'Mastro fornitori',
        'masban' => 'Mastro banche',
        'mas_staff' => 'Mastro collaboratori',
        'mas_fixed_assets' => 'Mastro immobilizzazioni',
        'mas_found_assets' => 'Mastro fondo ammortamenti',
        'mas_cost_assets' => 'Mastro costi ammortamento',
        'lost_cost_assets' => 'Conto quote perse ammortamento',
        'min_rate_deprec' => 'Ammortamento minimo (%)',
        'super_amm_account' => 'Super ammortamento conto',
        'super_amm_rate' => 'Super ammortamento (eccedente 100%)',
        'capital_loss_account' => 'Conto minusvalenze',
        'capital_gains_account' => 'Conto plusvalenze',
        'cassa_' => 'Conto cassa',
        'ivaacq' => 'Conto I.V.A. acquisti',
        'ivaven' => 'Conto I.V.A. vendite',
        'ivacor' => 'Conto I.V.A. corrispettivi',
        'ivaera' => 'Conto I.V.A. liquidazione',
        'split_payment' => 'Conto IVA Split Payment PA',
        'impven' => 'Conto imponibile vendite',
        'imptra' => 'Conto spese di trasporto',
        'impimb' => 'Conto spese di imballo',
        'impspe' => 'Conto spese incasso effetti',
        'impvar' => 'Conto spese varie',
        'boleff' => 'Conto bolli su effetti',
        'omaggi' => 'Conto omaggi',
        'sales_return' => 'Resi su vendite',
        'impacq' => 'Conto imponibile acquisti',
        'cost_tra' => 'Conto acquisto trasporti',
        'cost_imb' => 'Conto acquisto imballi',
        'cost_var' => 'Conto acquisti vari',
        'purchases_return' => 'Resi su acquisti',
        'coriba' => 'Conto portafoglio Ri.Ba ',
        'cotrat' => 'Conto portafoglio Tratte',
        'cocamb' => 'Conto portafoglio Cambiali',
        'c_ritenute' => 'Conto ritenute subite',
        'c_ritenute_autonomi' => 'Ritenute autonomi da versare',
        'payroll_tax' => 'Percentuale Cassa Previdenziale',
        'c_payroll_tax' => 'Conto rivalsa Cassa Previdenziale',
        'ritenuta' => '% Ritenuta',
        'upgrie' => 'Ultima pagina riepilogativo IVA',
        'upggio' => 'Ultima pagina Libro Giornale',
        'upginv' => 'Ultima pagina Libro Inventari',
        'upgve' => 'Ultime pagine registri Fatture Vendita',
        'upgac' => 'Ultime pagine registri Fatture degli Acquisti',
        'upgco' => 'Ultime pagine registri Corrispettivi',
        'sezione' => 'Sez. IVA ',
        'acciva' => 'Percentuale d\'acconto I.V.A. (%)',
        'taxstamp_limit' => 'Limite esenzione imposta di bollo',
        'taxstamp' => 'Importo bollo operazioni senza IVA',
        'taxstamp_vat' => 'Aliquota IVA dei bolli',
        'taxstamp_account' => 'Conto costo bolli',
        'perbol' => 'Aliquota imposta di bollo su tratte (%)',
        'round_bol' => 'Arrotondamento bolli',
        'round_bol_value' => array(1 => 'centesimo', 5 => 'centesimi', 10 => 'centesimi',
            50 => 'centesimi', 100 => 'centesimi (unit&agrave;)'),
        'virtual_taxstamp' => 'Modalità assoluzione bolli',
        'virtual_taxstamp_value' => array(0 => 'No', 1 => 'Fisica', 2 => 'Virtuale'),
        'virtual_stamp_auth_prot' => 'Protocollo autorizzazione bollo virtuale ',
        'virtual_stamp_auth_date' => ' rilasciata il ',
        'causale_pagam_770' => 'Causale del pagamento ritenuta(mod.770)',
        'causale_pagam_770_value' => array('' => '-------------------',
            'A' => 'Prestazioni di lavoro autonomo rientranti nell’esercizio di arte o professione abituale',
            'B' => 'Utilizzazione economica, da parte dell’autore o dell’inventore, di opere dell’ingegno, di brevetti industriali e di processi, formule o informazioni relativi a esperienze acquisite in campo industriale, commerciale o scientifico',
            'C' => 'Utili derivanti da contratti di associazione in partecipazione e da contratti di cointeressenza, quando l’apporto è costituito esclusivamente dalla prestazione di lavoro',
            'D' => 'Utili spettanti ai soci promotori e ai soci fondatori delle società di capitali',
            'E' => 'Levata di protesti cambiari da parte dei segretari comunali',
            'G' => 'Indennità corrisposte per la cessazione di attività sportiva professionale',
            'H' => 'Indennità corrisposte per la cessazione dei rapporti di agenzia delle persone fisiche e delle società di persone, con esclusione delle somme maturate entro il 31.12.2003, già imputate per competenza e tassate come reddito d’impresa',
            'I' => 'Indennità corrisposte per la cessazione da funzioni notarili',
            'L' => 'Utilizzazione economica, da parte di soggetto diverso dall’autore o dall’inventore, di opere dell’ingegno, di brevetti industriali e di processi, formule e informazioni relative a esperienze acquisite in campo industriale, commerciale o scientifico',
            'L1' => 'Redditi derivanti dall’utilizzazione economica di opere dell’ingegno, di brevetti industriali e di processi, formule e informazioni relativi a esperienze acquisite in campo industriale, commerciale o scientifico, che sono percepiti da soggetti che abbiano acquistato a titolo oneroso i diritti alla loro utilizzazione',
            'M' => 'Prestazioni di lavoro autonomo non esercitate abitualmente, obblighi di fare, di non fare o permettere',
            'M1' => 'redditi derivanti dall’assunzione di obblighi di fare, di non fare o permettere',
            'N' => 'Indennità di trasferta, rimborso forfetario di spese, premi e compensi erogati: .. nell’esercizio diretto di attività sportive dilettantistiche; .. in relazione a rapporti di collaborazione coordinata e continuativa di carattere amministrativo-gestionale, di natura non professionale, resi a favore di società e associazioni sportive dilettantistiche e di cori, bande e filodrammatiche da parte del direttore e dei collaboratori tecnici',
            'O' => 'Prestazioni di lavoro autonomo non esercitate abitualmente, obblighi di fare, di non fare o permettere, per le quali non sussiste l’obbligo di iscrizione alla gestione separata (Circ. Inps 104/2001)',
            'O1' => 'Redditi derivanti dall’assunzione di obblighi di fare, di non fare o permettere, per le quali non sussiste l’obbligo di iscrizione alla gestione separata (Circ. INPS n. 104/2001);',
            'P' => 'Compensi corrisposti a soggetti non residenti privi di stabile organizzazione per l’uso o la concessione in uso di attrezzature industriali, commerciali o scientifiche che si trovano nel territorio dello Stato, ecc',
            'Q' => 'Provvigioni corrisposte ad agente o rappresentante di commercio monomandatario',
            'R' => 'Provvigioni corrisposte ad agente o rappresentante di commercio plurimandatario',
            'S' => 'Provvigioni corrisposte a commissionario',
            'T' => 'Provvigioni corrisposte a mediatore',
            'U' => 'Provvigioni corrisposte a procacciatore di affari',
            'V' => 'Provvigioni corrisposte a incaricato per le vendite a domicilio e provvigioni corrisposte a incaricato per la vendita porta a porta e per la vendita ambulante di giornali quotidiani e periodici (L. 25.02.1987, n. 67)',
            'V1' => 'Redditi derivanti da attività commerciali non esercitate abitualmente (ad esempio, provvigioni corrisposte per prestazioni occasionali ad agente o rappresentante di commercio, mediatore, procacciatore d’affari o incaricato per le vendite a domicilio);',
            'W' => 'Corrispettivi erogati nel 2012 per prestazioni relative a contratti d’appalto cui si sono resi applicabili le disposizioni contenute nell’art. 25-ter D.P.R. 600/1973',
            'X' => 'Canoni corrisposti nel 2004 da società o enti residenti, ovvero da stabili organizzazioni di società estere di cui all’art. 26-quater, c. 1, lett. a) e b) D.P.R. 600/1973, a società o stabili organizzazioni di società, situate in altro Stato membro dell’Unione Europea in presenza dei relativi requisiti richiesti, per i quali è stato effettuato il rimborso della ritenuta ai sensi dell’art. 4 D. Lgs. 143/2005 nell’anno 2006',
            'Y' => 'Canoni corrisposti dall’1.01.2005 al 26.07.2005 da soggetti di cui al punto precedente',
            'Z' => 'Titolo diverso dai precedenti'
        ),
        'sperib' => 'Spese incasso RIBA da addebitare ',
        'desez' => 'Descrizione della ',
        'reverse_charge_sez' => 'Sezione utilizzata per il reverse charge',
        'fatimm' => 'Sezione delle Fatture Immediate',
        'fatimm_value' => array('R' => 'Sezione del Report', 'U' => 'Sezione ultima emissione',
            '1' => 'Propone sempre 1', '2' => 'Propone sempre 2', '3' => 'Propone sempre 3'),
        'artsea' => 'Ricerca articoli per',
        'artsea_value' => array('C' => 'Codice', 'B' => 'Barcode', 'D' => 'Descrizione', 'T' => 'Tutti'),
        'templ_set' => 'Template set dei documenti',
        'colore' => 'Colore sfondo documenti',
        'conmag' => 'Contabilit&agrave; di magazzino',
        'conmag_value' => array(0 => 'Nessuna', 1 => 'Manuale (sconsigliata)', 2 => 'Automatica'),
        'ivam_t' => 'Periodicit&agrave; liquidazione IVA',
        'ivam_t_value' => array('M' => 'Mensile', 'T' => 'Trimestrale'),
        'preeminent_vat' => 'Aliquota IVA spese (preminente)',
        'interessi' => 'Interessi su IVA Trimestrale',
        'amm_min' => 'Tabella Ammortamenti Ministeriali',
        'fae_tipo_cassa' => 'Tipo Cassa (fatture elettroniche)',
        'ra_cassa' => 'Ritenuta su cassa previdenziale',
        'vat_susp' => 'Tipo di liquidazione IVA',
        'vat_susp_value' => array(0=>'Normale',1=> 'Iva per cassa ex art. 32 bis del D.p.r. 83/2012'),
    ),
    "report_aziend.php" =>
    array('title' => 'Lista delle aziende installate',
        'ins_this' => 'Crea una nuova azienda',
        'upd_this' => 'Modifica l\'azienda ',
        'codice' => 'ID',
        'ragso1' => 'Ragione Sociale',
        'e_mail' => 'Internet',
        'telefo' => 'Telefono',
        'regime' => 'Regime',
        'regime_value' => array('0' => 'Ordinario', '1' => 'Semplificato'),
        'ivam_t' => 'Periodicit&agrave; IVA',
        'ivam_t_value' => array('M' => 'Mensile', 'T' => 'Trimestrale')
    ),
    "create_new_company.php" =>
    array('title' => 'Crea una nuova azienda',
        'errors' => array('Il codice deve essere compreso tra 1 e 999!',
            'Codice azienda gi&agrave; in uso!'
        ),
        'codice' => 'Numero ID (codice)',
        'ref_co' => 'Azienda di riferimento per il popolamento dei dati',
        'clfoco' => 'Creazione piano dei conti',
        'users' => 'Abilita gli stessi utenti dell\'azienda di riferimento',
        'clfoco_value' => array(0 => 'No (sconsigliato)',
            1 => 'Si, ma senza clienti,fornitori e banche',
            2 => 'Si, compresi clienti,fornitori e banche'),
        'base_arch' => 'Popolamento degli archivi di base',
        'base_arch_value' => array(0 => 'No (sconsigliato)',
            1 => 'Si, ma senza vettori e imballi',
            2 => 'Si, compresi vettori e imballi'),
        'artico_catmer' => 'Duplicazione articoli di magazzino',
        'artico_catmer_value' => array(0 => 'No (default)',
            1 => 'Sì (normalmente sulle installazione didattiche)')
    ),
    "admin_pagame.php" =>
    array("Modalit&agrave; di pagamento",
        "Codice pagamento",
        "Descrizione",
        "Tipo di pagamento",
        "Incasso automatico",
        'pagaut' => "Pagamento automatico",
        "Tipo di decorrenza",
        "Giorni di decorrenza",
        "Mese escluso",
        "Mese successivo",
        "Giorno successivo",
        "Numero di rate",
        "Tipo di rate",
        "C/C bancario d'accredito",
        "Annotazioni",
        array('C' => 'contanti','O' => 'bonifico', 'K' => 'carte di pagamento', 'D' => 'rimessa diretta', 'I' => 'rapporto interbancario diretto (RID)', 'B' => 'Ricevuta Bancaria', 'T' => 'Cambiale-Tratta', 'V' => 'mediante avviso(MAV)'),
        array('S' => 'Si', 'N' => 'No'),
        array('D' => 'data fattura', 'G' => 'giorno fisso', 'F' => 'fine mese'),
        array('Q' => 'quindicinali', 'M' => 'mensili', 'B' => 'bimestrali', 'T' => 'trimestrali', 'U' => 'quadrimestrali', 'S' => 'semestrali', 'A' => 'annuali'),
        "Il codice scelto &egrave; gi&agrave; stato usato!",
        "La descrizione &egrave; vuota!",
        "Il codice dev'essere compreso tra 1 e 99",
		'Non hai scelto la tipologia di pagamento per la fattura elettronica',
        'ins_this' => 'Inserisci una nuova modalit&agrave; di pagamento',
        'fae_mode' => "Modalità fatt.elettronica",
				'web_payment_ref'=>'Riferimento  al codice pagamento ecommerce'
    ),
    "report_aliiva.php" =>
    array('title' => "Aliquote I.V.A.",
        'ins_this' => 'Inserisci una nuova aliquota IVA',
        'codice' => "Codice",
        'descri' => "Descizione",
        'type' => "Tipo",
        'operation_type' => "Tipo di operazione",
        'aliquo' => "Percentuale",
        'taxstamp' => 'Soggetto a bollo',
        'fae_natura' => "Natura fatt.elettronica PA",
        'yn_value' => array(1 => 'Si', 0 => 'No')
    ),
    "admin_aliiva.php" =>
    array("Aliquota IVA",
        "Codice",
        "Descrizione",
        "% aliquota",
        "Annotazioni",
        "Il codice scelto &egrave; gi&agrave; stato usato!",
        "Il codice dev'essere compreso tra 1 e 99",
        "La descrizione &egrave; vuota!",
        "% Aliquota non valida!",
        "Tipo IVA",
        "Indicare la natura dell'esenzione/esclusione!",
        'fae_natura' => "Natura fatt.elettronica PA",
        'taxstamp' => 'Soggetto a bollo',
        'operation_type' => "Tipo di operazione",
        'yn_value' => array(1 => 'Si', 0 => 'No'),
        'annota' => 'Descrizione estesa'
    ),
    "admin_banapp.php" =>
    array('title' => 'Gestione delle banche di appoggio',
        'ins_this' => 'Inserisci una nuova banca di appoggio',
        'upd_this' => 'Modifica la banca di appoggio ',
        'del_this' => ' la banca di appoggio ',
        'errors' => array('Il codice non &egrave; valido (min=1 max=99)!',
            'Il codice scelto &egrave; gi&agrave; stato usato!',
            'Non &egrave; stata inserita la descrizione!',
            'Il codice ABI non  &egrave; valido!',
            'Il codice CAB non  &egrave; valido!'
        ),
        'codice' => "Codice ",
        'descri' => "Descrizione ",
        'codabi' => "Codice ABI",
        'codcab' => "Codice CAB",
        'locali' => "Localit&agrave;",
        'codpro' => "Provincia",
        'annota' => "Annotazioni",
        'report' => 'Lista delle banche di appoggio'
    ),
    "admin_imball.php" =>
    array('title' => 'Gestione degli imballi',
        'ins_this' => 'Inserisci un nuovo tipo di imballo',
        'upd_this' => 'Modifica l\'imballo ',
        'errors' => array('Il codice non &egrave; valido (min=1 max=99)!',
            'Il codice scelto &egrave; gi&agrave; stato usato!',
            'Non &egrave; stata inserita la descrizione!',
            'Il peso non dev\'essere negativo'
        ),
        'codice' => "Codice ",
        'descri' => "Descrizione imballo ",
        'weight' => "Peso",
        'annota' => "Annotazioni",
        'report' => 'Lista degli imballi',
        'del_this' => 'imballo'
    ),
    "admin_portos.php" =>
    array('title' => 'Gestione dei porti/rese',
        'ins_this' => 'Inserisci una nuovo porto/resa',
        'upd_this' => 'Modifica il porto/resa ',
        'errors' => array('Il codice non &egrave; valido (min=1 max=99)!',
            'Il codice scelto &egrave; gi&agrave; stato usato!',
            'Non &egrave; stata inserita la descrizione!'
        ),
        'codice' => "Codice ",
        'descri' => "Descrizione porto/resa ",
        'incoterms' => 'Incoterms-standard ICC',
        'annota' => "Annotazioni",
        'report' => 'Lista dei porti/rese',
        'del_this' => 'porto/resa'
    ),
    "admin_spediz.php" =>
    array('title' => 'Gestione delle spedizioni',
        'ins_this' => 'Inserisci una nuovo tipo di spedizione',
        'upd_this' => 'Modifica la spedizione ',
        'errors' => array('Il codice non &egrave; valido (min=1 max=99)!',
            'Il codice scelto &egrave; gi&agrave; stato usato!',
            'Non &egrave; stata inserita la descrizione!'
        ),
        'codice' => "Codice ",
        'descri' => "Descrizione spedizione ",
        'annota' => "Annotazioni",
        'report' => 'Lista delle spedizioni',
        'del_this' => 'spedizione'
    ),
    "report_banche.php" =>
    array('title' => "Conti correnti bancari",
        'ins_this' => 'Inserisci un nuovo conto corrente bancario',
        'msg' => array('CONTO BANCARIO ESISTENTE SOLO SUL PIANO DEI CONTI', 'Visualizza e stampa il partitario'),
        'codice' => "Codice",
        'ragso1' => "Nome",
        'iban' => "Codice IBAN",
        'citspe' => "Citt&agrave;",
        'prospe' => "Prov.",
        'telefo' => "Telefono"
    ),
    "admin_bank_account.php" =>
    array("Conto corrente bancario ",
        "Codice (dal piano dei conti) ",
        "Descrizione ",
        "Banca d'appoggio (selezionare in luogo della descrizione)",
        "Indirizzo ",
        "CAP ",
        "Citt&agrave; - Provincia ",
        "Nazione ",
        'sia_code' => 'Codice SIA',
        'eof' => 'Tracciato file RiBA con caratteri di fine rigo',
        'eof_value' => array('S' => 'Si', 'N' => 'No'),
        "Codice IBAN ",
        "Sede legale ",
        "Telefono ",
        "Fax ",
        "e-mail ",
        "Annotazioni ",
        "Il sul piano dei conti non ha il mastro banche!",
        "In configurazione azienda non &egrave; stato selezionato un mastro banche!",
        "L'IBAN non &egrave; corretto!",
        "Codice esistente!",
        "Codice minore di 1!",
        "Descrizione vuota!",
        "La nazione &egrave; incompatibile con l'IBAN!",
        'transfer_fees_acc' => 'Conto spese bonifici',
        'transfer_fees' => 'Addebito per bonifici',
    ),
    "admin_vettore.php" =>
    array('title' => 'inserisci un vettore ',
		  'title_upd' => 'Modifica il vettore',
          'err' => array('ragso1'=>'Non &egrave; stata inserita la ragione sociale',
            'indiri'=>'Non &egrave; stato inserito l\'indirizzo',
            'citspe'=>'Non &egrave; stata inserita la citt&agrave;',
            'capspe'=>'Non &egrave; stato inserito il codice d\'avviamento postale',
            'country'=>'Indicare la nazionalità',
            'sameidfis'=>'Verranno utilizzati i dati di una anagrafica esistente (stessa partita IVA / codice fiscale)',
            'sexper'=>'Non &egrave; stato inserito il sesso/persona giuridica' ,
            'pariva'=>'La partita IVA non è formalmente corretta',
            'pariva_used'=>'La partita IVA è già utilizzata in altra anagrafica' ),
        'codice' => "Codice",
        'ragso1' => 'Ragione Sociale 1',
        'ragso2' => 'Ragione Sociale 2',
        'sexper' => "Sesso/pers.giuridica ",
        'sexper_value' => array('' => '-', 'M' => 'Maschio', 'F' => 'Femmina', 'G' => 'Giuridica'),
        'indspe' => 'Indirizzo',
        'capspe' => 'CAP',
        'citspe' => 'Citta\'',
        'prospe' => 'Provincia',
        'pariva' => 'Partita IVA',
        'codfis' => 'Codice Fiscale',
        'country' => 'Nazione',
        'id_language' => 'Lingua',
        'id_currency' => 'Valuta',
        'pec_email' => 'Indirizzo PEC',
        'e_mail' => 'Indirizzo e-mail',
        'n_albo' => 'Numero iscrizione albo autotrasportatori',
        'conducente' => 'Conducente',
        'telefo' => 'Recapito telefonico',
        'targa' => 'Targa',
        'annota' => 'Annotazioni',
        'report' => 'Lista dei vettori',
        'del_this' => 'vettori',
        'confirm_entry'=> 'Inserisci nuovo vettore',
        'confirm_entry_upd'=> 'Modifica il vettore',
        'link_anagra' => 'Esiste già una anagrafica con la stessa partita IVA, puoi collegarla clicca qui',

    ),
    "admin_utente.php" =>
    array('title' => 'Gestione degli utenti',
        'ins_this' => 'Inserire un nuovo utente',
        'upd_this' => 'Modificare l\'utente',
        'err' => array(
            'exlogin' => 'Il nickname &egrave; gi&agrave; usato!',
            'user_lastname' => 'Inserire il cognome!',
            "user_name" => "Inserire il nickname!",
            'Password' => "E' necessario inserire la Password !",
            'passold' => "La vecchia password &egrave; sbagliata!",
            'passlen' => "La password non &egrave; sufficientemente lunga!",
            'confpass' => "La password &egrave; diversa da quella di conferma!",
            'upabilit' => "Non puoi aumentare il tuo Livello di Abilitazione l'operazione &egrave; riservata all'amministratore!",
            'filmim' => "Il file dev'essere in formato JPG",
            'filsiz' => "L'immagine non dev'essere pi&ugrave; grande di 64Kb",
            'Abilit' => "Non puoi avere un livello inferiore a 9 perch&egrave; sei l'unico amministratore!",
            'Abilit_stud' => "Non puoi avere un livello inferiore a 7 perch&egrave; sei l'unico utente!",
            'charpass' => "La password non può contenere caratteri alcuni speciali \" / > <",
			'email'=>'Indirizzo e-mail formalmente errato'
        ),
        "user_name" => "Nickname",
        'user_lastname' => "Cognome",
        'user_firstname' => "Nome",
        'user_email' => "Mail (anche per recupero password)",
        'az_email' => "Mail aziendale dell'utente",
        'image' => 'Immagine dell\'utente<br />(solo in formato JPG, max 64kb)',
        'Abilit' => "Livello",
        'Abilit_value' => array('9' => 'Amministratore', '8' => 'Avanzato/Studente','5'=>'Utente','0'=>'Nessuno'),
		'company'=>'Azienda',
        'mesg_co' => array('Non &egrave; stato trovato nulla!', 'Minimo 2 caratteri', 'Azienda di lavoro'),
        'Access' => "Accesso",
        'user_password_old' => 'Vecchia Password',
        'user_password_new' => 'Nuova Password',
        'user_password_ver' => 'Verifica Password',
		'user_active' => 'Abilitazione utente',
		'user_active_value' => array('1'=>'Attivo','0'=>'Disattivo'),
        'lang' => 'Language',
        'theme' => 'Tipo di menù<br>(sarà attivo dal prossimo login)',
        'style' => 'Struttura dello stile',
        'skin' => 'Aspetto dello stile',
        'mod_perm' => 'Permessi dei moduli',
        'report' => 'Lista degli Utenti',
        'del_this' => 'Utente',
        'del_err' => 'Non puoi cancellarti perch&egrave; sei l\'unico ad avere i diritti di amministratore! ',
        'body_text' => 'Testo contenuto nelle email che invierai'
    ),
    "config_aziend.php" =>
    array('title' => 'Configurazione avanzata azienda'),
	"edit_privacy_regol.php" =>
    array('title' => 'Regolamento per l’utilizzo e la gestione delle risorse informatiche',
        'ins_this' => 'Inserisci il Regolamento per l’utilizzo e la gestione delle risorse informatiche',
        'upd_this' => 'Modifica il Regolamento per l’utilizzo e la gestione delle risorse informatiche',
        'err' => array(
            'body_text' => 'Testo del regolamento troppo breve'
        ),
        'body_text' => "Testo "
 	)
);
?>
