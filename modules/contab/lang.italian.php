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

$strScript = array ("select_liqiva.php" =>
                    array( 'title'=>'Selezione per la visualizzazzione e/o la stampa della liquidazione IVA periodica',
                           'errors'=>array('La data  non &egrave; corretta!',
                                           'La data di inizio non pu&ograve; essere successiva alla data di fine !'
                                          ),
                           'page_ini'=>'N. pagina iniziale',
                           'sta_def'=>'Stampa definitiva',
                           'sta_def_title'=>'Se selezonato modifica il valore dell\'ultima pagina stampata di questo registro in configurazione azienda',
                           'descri'=>'Descrizione',
                           'descri_value'=>array('M'=>'del mese di ','T'=>'del trimestre '
                                           ),
                           'date_ini'=>'Data registrazione inizio  ',
                           'sem_ord'=>' Regime ',
                           'sem_ord_value'=>array(0=>' Contabilit&agrave; ordinaria ',1=>' Contabilit&agrave; semplificata '
                                           ),
                           'cover'=>'Stampa la copertina',
                           'date_fin'=>'Data registrazione fine ',
                           'header'=>array('Sezione'=>'','Registro'=>'','Descrizione aliquota'=>'','Imponibile'=>'',
                                           'Aliquota'=>'','Imposta'=>'','Indetraibile'=>'','Totale'=>''
                                           ),
                           'regiva_value'=>array(0=>'Nessuno',2 =>'Fatture di Vendita',4=>'Corrispettivi',6=>'Fatture d\'Acquisto'),
                           'of'=>' del ',
                           'tot'=>' TOTALE ',
                           't_pos'=>' I.V.A A DEBITO',
                           't_neg'=>' I.V.A A CREDITO',
                           'carry'=>'Credito da periodo precedente'
                           ),
                    "stampa_liqiva.php" =>
                    array( 'title'=>'Liquidazione IVA periodica',
                           'cover_descri'=>'Registro riepilogativo dell\'I.V.A. dell\'anno ',
                           'page'=>'Pagina',
                           'sez'=>'Sezione',
                           'regiva_value'=>array(0=>'Nessuno',2 =>'Registro delle Fatture di Vendita',4=>'Regitro dei Corrispettivi',6=>'Registro delle Fatture d\'Acquisto'),
                           'code'=>'Codice',
                           'descri'=>'Descrizione aliquota I.V.A.',
                           'imp'=>'Imponibile',
                           'iva'=>'Imposta',
                           'rate'=>'%',
                           'ind'=>'Indetraibile',
                           'tot'=>'Totale',
                           't_reg'=>'Totale I.V.A. del registro ',
                           't_pos'=>' I.V.A A DEBITO',
                           't_neg'=>' I.V.A A CREDITO',
                           'inter'=>'Maggiorazione a titolo di interessi ',
                           'pay'=>' a pagare',
                           'carry'=>'Credito da periodo precedente',
                           'pay_date'=>'Pagata in data ',
                           'co'=>'presso ',
                           'abi'=>' A.B.I. ',
                           'cab'=>' C.A.B. '
                           ),
                    "select_partit.php" =>
                    array( 'title'=>'Selezione per la visualizzazzione e/o la stampa dei partitari',
                           'mesg'=>array('La ricerca non ha dato risultati!',
                                         'Inserire almeno 2 caratteri!',
                                         'Cambia cliente/fornitore'
                                          ),
                           'errors'=>array('La data  non &egrave; corretta!',
                                           'La data di inizio dei movimenti contabili da stampare non pu&ograve; essere successiva alla data dell\'ultimo !',
                                           'La data di stampa non pu&ograve; essere precedente a quella dell\'ultimo movimento!',
                                           'Il conto iniziale non pu&ograve; essere successivo a quello finale!',
                                           'Non ci sono movimenti nei limiti selezionati'
                                          ),
                           'date'=>'Data di stampa ',
                           'master_ini'=>'Mastro inizio ',
                           'account_ini'=>'Conto inizio ',
                           'date_ini'=>'Data registrazione inizio  ',
                           'master_fin'=>'Mastro fine ',
                           'account_fin'=>'Conto fine ',
                           'date_fin'=>'Data registrazione fine ',
                           'selfin'=>'Copia conto iniziale',
                           'header1'=>array('Conto'=>'','Num.Mov.'=>'','Descrizione'=>'',
                                            'Dare'=>'','Avere'=>'','Saldo<br />progressivo'=>''
                                           ),
                           'header2'=>array('Data'=>'','ID'=>'','Descrizione'=>'','N.Doc.'=>'',
                                            'Data Doc.' =>'','Dare'=>'','Avere'=>'',
                                            'Saldo<br />progressivo'=>''
                                           )
                           ),
                    "admin_caucon.php" =>
                    array( 'title'=>'Gestione delle causali contabili',
                           'ins_this'=>'Inserisci una nuova causale contabile ',
                           'upd_this'=>'Modifica della causale contabile',
                           'mesg'=>array('La ricerca non ha dato risultati!',
                                         'Inserire almeno 2 caratteri!',
                                         'Cambia cliente/fornitore'
                                          ),
                           'errors'=>array('Inserire un codice valido!',
                                           '&Egrave; necessario inserire una descrizione!',
                                           'Codice esistente usare l\'apposita procedura se lo si vuole modificare!',
                                           'Si deve definire almeno un conto!',
                                           'Codice riservato alla CHIUSURA AUTOMATICA CONTI!',
                                           'Codice riservato alla APERTURA AUTOMATICA CONTI!'
                                          ),
                           'head'=>'Conti da movimentare ',
                           'codice'=>'Codice causale *',
                           'descri'=>'Descrizione *',
                           'insdoc'=>'Inserimento dati documento di riferimento',
                           'insdoc_value'=>array(0=>'No',1=>'Si'),
                           'regiva'=>'Registro I.V.A.',
                           'regiva_value'=>array(0=>'Nessuno',2 =>'Fatture di Vendita',4=>'Corrispettivi',6=>'Fatture d\'Acquisto'),
                           'operat'=>'Operatore',
                           'operat_value'=>array(0=>'Non opera',1=>'Somma',2=>'Sottrae'),
                           'contr'=>'Conto (minimo 1) *',
                           'tipim'=>'Tipo di importo',
                           'tipim_value'=>array(''=>'','A'=>'Totale','B'=>'Imponibile','C'=>'Imposta'),
                           'daav'=>'DARE/AVERE',
                           'daav_value'=>array('D'=>'DARE','A'=>'AVERE'),
                           'report'=>'Lista delle causali contabili',
                           'del_this'=>'Causale contabile '
                           ),
                    "admin_piacon.php" =>
                   array(  'title'=>'Gestione del piano dei conti',
                           'ins_this'=>'Inserisci un nuovo conto',
                           'upd_this'=>'Modifica il conto ',
                           'errors'=>array('Il codice non &egrave; valido!',
                                           'Il codice scelto &egrave; gi&agrave; stato usato!',
                                           'Non &egrave; stata inserita la descrizione!'
                                          ),
                           'codice'=>"Codice ",
                           'mas'=>"Mastro",
                           'sub'=>"Sottoconto",
                           'descri'=>"Descrizione",
                           'ceedar'=>"Riclassificazione Bilancio CEE / DARE",
                           'ceeave'=>"Riclassificazione Bilancio CEE / AVERE",
                           'annota'=>"Note"
                         ),
                    "admin_movcon.php" =>
                    array( 'title'=>'Gestione dei movimenti contabili',
                           'ins_this'=>'Inserisci un nuovo movimento contabile ',
                           'upd_this'=>'Modifica il movimento contabile',
                           'mesg'=>array('La ricerca non ha dato risultati!',
                                         'Inserire almeno 2 caratteri!',
                                         'Cambia cliente/fornitore'
                                          ),
                           'errors'=>array('Almeno un rigo non ha conti!',
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
                                           'ATTENZIONE stai modificando un movimento che interessa un registro IVA!'
                                          ),
                           'id_testata'=>'Numero di movimento',
                           'date_reg'=>'Data di registrazione',
                           'descri'=>'Descrizione',
                           'caucon'=>'Causale contabile',
                           'v_caucon'=>'Conferma la Causale!',
                           'insdoc'=>'Dati del documento di riferimento',
                           'insdoc_value'=>array(0=>'No',1=>'Si'),
                           'regiva'=>'Registro I.V.A.',
                           'regiva_value'=>array(0=>'Nessuno',2 =>'Fatture di Vendita',4=>'Corrispettivi',6=>'Fatture d\'Acquisto'),
                           'operat'=>'Operatore',
                           'operat_value'=>array(0=>'Non opera',1=>'Somma',2=>'Sottrae'),
                           'date_doc'=>'Data del documento',
                           'seziva'=>'Sezione IVA',
                           'protoc'=>'Protocollo',
                           'numdoc'=>'Numero',
                           'partner'=>'Cliente / Fornitore',
                           'insiva'=>'Movimenti I.V.A.',
                           'vat'=>'Aliquota I.V.A.',
                           'taxable'=>'Imponibile',
                           'tax'=>'Imposta',
                           'mas'=>"Mastro",
                           'sub'=>"Conto",
                           'amount'=>'Importo',
                           'daav'=>'Dare/Avere',
                           'daav_value'=>array('D'=>'DARE','A'=>'AVERE'),
                           'bal_title'=>"Bilancia rispetto a questo valore!",
                           'bal'=>"Bilanciato",
                           'addval'=>"Incrementa il valore di ",
                           'subval'=>"Diminuisci il valore di ",
                           'zero'=>"Movimenti a zero!",
                           'diff'=>"Differenza",
                           'tot_d'=>'Totale DARE',
                           'tot_a'=>'Totale AVERE',
                           'visacc'=>'Visualizza il partitario',
                           'report'=>'Lista dei Movimenti Contabili',
                           'del_this'=>'Movimenti Contabili',
                           'sourcedoc'=>'Documento che ha originato il movimento',
                           'source'=>'Origine'
                           ),
                    "report_piacon.php" =>
                   array(  'title'=>'Piano dei conti',
                           'ins_this'=>'Inserisci un nuovo conto',
                           'view_this'=>'Visualzza e/o stampa i partitari',
                           'print_this'=>'Stampa il piano dei conti',
                           'header'=>array('Mastro'=>'','Conto'=>'','Descrizione'=>'','Dare'=>'',
                                            'Avere'=>'','Saldo'=>'','Visualizza<br />e/o stampa'=>'',
                                            'Cancella'=>''),
                           'msg1'=>'Ricorda che devi introdurre i mastri per le attivit&agrave; compresi tra 100 e 199, le passivit&agrave; tra 200 e 299, i costi tra 300 e 399, i ricavi tra 400 e 499 e i conti d\'ordine o transitori tra 500 e 599',
                           'msg2'=>'Saldi relativi all\'anno'
                         ),
                    "select_regiva.php" =>
                    array( 'title'=>'Selezione per la visualizzazzione e/o la stampa dei registri IVA',
                           'errors'=>array('La data  non &egrave; corretta!',
                                           'La data di inizio non pu&ograve; essere successiva alla data di fine !',
                                           'P'=>'La sequenza dei numeri di protocollo non &egrave; corretta',
                                           'N'=>'La sequenza dei numeri dei documenti non &egrave; corretta',
                                           'T'=>'C\'&egrave; un movimento IVA senza aliquota',
                                           'err'=>'Ci sono degli errori che non giustificano la stampa del registro'
                                          ),
                           'vat_reg'=>'Registro IVA da stampare:',
                           'vat_reg_value'=>array(2=>'Fatture di Vendita',4=>'Corrispettivi',6=>'Fatture d\'Acquisto'),
                           'vat_section'=>'Sezione IVA ',
                           'page_ini'=>'N. pagina iniziale',
                           'sta_def'=>'Stampa definitiva',
                           'sta_def_title'=>'Se selezonato modifica il valore dell\'ultima pagina stampata di questo registro in configurazione azienda',
                           'descri'=>'Descrizione',
                           'descri_value'=>array('M'=>'del mese di ','T'=>'del trimestre '
                                           ),
                           'date_ini'=>'Data registrazione inizio  ',
                           'sem_ord'=>' Regime ',
                           'sem_ord_value'=>array(0=>' Contabilit&agrave; ordinaria ',1=>' Contabilit&agrave; semplificata '
                                           ),
                           'cover'=>'Stampa la copertina',
                           'date_fin'=>'Data registrazione fine ',
                           'header'=>array('Protocollo'=>'','Data - ID movimento'=>'','Descrizione documento'=>'','Cliente o Fornitore'=>'',
                                            'Imponibile' =>'','Aliquota'=>'','Imposta'=>''
                                           ),
                           'of'=>' del ',
                           'tot'=>' TOTALE ',
                           't_gen'=>' GENERALE'
                           ),
                    "select_libgio.php" =>
                    array( 'title'=>'Selezione per la visualizzazzione e/o la stampa dei Libro giornale',
                           'errors'=>array('La data di inizio non &egrave; corretta!',
                                           'La data di fine non &egrave; corretta!',
                                           'La data di inizio non pu&ograve; essere successiva alla data di fine !'
                                          ),
                           'pagini'=>'N. pagina iniziale',
                           'stadef'=>'Stampa definitiva',
                           'stadef_title'=>'Se selezonato modifica il valore dell\'ultima pagina stampata in configurazione azienda',
                           'date_ini'=>'Data registrazione inizio  ',
                           'cover'=>' stampa la copertina -> ',
                           'date_fin'=>'Data registrazione fine ',
                           'valdar'=>'Dare (inizio)',
                           'valave'=>'Avere (inizio)',
                           'nrow'=>'Numero righe:',
                           'tot_a'=>' Totale Avere ',
                           'tot_d'=>' Totale Dare '
                           )
                    );
?>