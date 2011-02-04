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
$strScript = array("admin_fornit.php" =>
                   array(  'title'=>'Gestione dei fornitori',
                           'ins_this'=>'Inserisci un fornitore',
                           'upd_this'=>'Modifica  il fornitore ',
                           'mesg'=>array('La ricerca non ha dato risultati!',
                                         'Inserire almeno 2 caratteri!',
                                         'Cambia anagrafica'),
                           'errors'=>array('&Egrave; necessario indicare la Ragione Sociale',
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
                                           '&Egrave; necessario scegliere la modalit&agrave; di pagamnto'
                                          ),
                           'link_anagra'=>' Clicca sotto per inserire l\'anagrafica esistente sul piano dei conti',
                           'codice'=>"Codice ",
                           'ragso1'=>"Ragione Sociale 1",
                           'ragso2'=>"Ragione Sociale 2",
                           'sedleg'=>"Sede legale",
                           'legrap'=>"Legale rappresentante ",
                           'sexper'=>"Sesso/pers.giuridica ",
                           'sexper_value'=>array(''=>'-','M'=>'Maschio','F'=>'Femmina','G'=>'Giuridica'),
                           'indspe'=>'Indirizzo',
                           'capspe'=>'Codice Postale',
                           'citspe'=>'Citt&agrave; - Provincia',
                           'country'=>'Nazione',
                           'telefo'=>'Telefono',
                           'fax'=>'Fax',
                           'cell'=>'Cellulare',
                           'codfis'=>'Codice Fiscale',
                           'pariva'=>'Partita I.V.A.',
                           'e_mail'=>'e mail',
                           'cosric'=>'Conto di costo',
                           'codpag'=>'Modalit&agrave; di pagamento *',
                           'sconto'=>'% Sconto da apllicare',
                           'banapp'=>'Banca d\'appoggio',
                           'portos'=>'Porto - Resa',
                           'spediz'=>'Spedizione',
                           'imball'=>'Imballo',
                           'listin'=>'Listino da applicare',
                           'id_des'=>'Destinazione &rArr; da anagrafica',
                           'destin'=>'Destinazione &rArr; descrizione libera',
                           'iban'=>'IBAN',
                           'maxrat'=>'Massimo importo delle rate',
                           'ragdoc'=>'Raggruppamento documenti',
                           'addbol'=>'Addebito spese bolli',
                           'speban'=>'Addebito spese bancarie',
                           'spefat'=>'Addebito spese di fatturazione',
                           'stapre'=>'Stampa prezzi su D.d.T.',
                           'allegato'=>'Allegato IVA - Elenco fornitori',
                           'yn_value'=>array('S'=>'Si','N'=>'No'),
                           'aliiva'=>'Riduzione I.V.A.',
                           'ritenuta'=>'% Ritenuta',
                           'status'=>'Visibilit&agrave; alla ricerca',
                           'status_value'=>array(''=>'Attiva','HIDDEN'=>'Disabilitata'),
                           'annota'=>'Annotazioni'
                         ),
                   "report_broacq.php" =>
                   array('Nuovo Preventivo','Nuovo Ordine'),
                   "report_debiti.php" =>
                   array(  'title'=>'Lista dei debiti verso i fornitori',
                           'start_date'=>'Anno inizio',
                           'end_date'=>'Anno fine',
                           'codice'=>'Codice',
                           'partner'=>'Fornitore',
                           'telefo'=>'Telefono',
                           'mov'=>'Movimenti',
                           'dare'=>'Dare',
                           'avere'=>'Avere',
                           'saldo'=>'Saldo',
                           'pay'=>'Paga',
                           'statement'=>'Estr.Conto',
                           'pay_title'=>'Paga il debito con ',
                           'statement_title'=>'Stampa l\'estratto conto di '
                           ),
                   "report_docacq.php" =>
                   array(  "statistica ",
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
                           " Valore in $money[0] ",
                           " Fuori "),
                   "admin_docacq.php" =>
                   array(  array("DDR" => "D.d.T. di Reso a Fornitore","DDL" => "D.d.T. c/lavorazione","AFA" => "Fattura d'Acquisto","ADT" => "D.d.T. d'Acquisto","AFC" => "Nota Credito da Fornitore","AOR" => "Ordine a Fornitore","APR" => "Richiesta di Preventivo a Fornitore"),
                           'mesg'=>array('La ricerca non ha dato risultati!',
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
                           " acquisto " ,
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
                           "Stai tentando di modificare il documento con una data antecedente a quello dello stesso tipo di documento con numero precedente!" ,
                           "Stai tentando di modificare il documento con una data successiva a quello dello stesso tipo di documento con numero successivo!" ,
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
                           "Non &egrave; stato inserito il numero del documento!"
                           ),
                    "accounting_documents.php" =>
                    array( 'title'=>'Genera i movimenti contabili a partire dai documenti d\'acquisto',
                           'errors'=>array('Data non corretta',
                                           'Non ci sono documenti da contabilizzare nell\'intervallo selezionato'
                                          ),
                           'vat_section'=>' della sezione IVA n.',
                           'date'=>'Fino al :',
                           'type'=>'Registro IVA ',
                           'type_value'=>array('A'=>'dei documenti di Acquisto'),
                           'proini'=>'Protocollo iniziale',
                           'profin'=>'Protocollo finale',
                           'preview'=>'Anteprima di contablizzazione',
                           'date_reg'=>'Data',
                           'protoc'=>'Protocollo',
                           'doc_type'=>'Tipo',
                           'doc_type_value'=>array('FAD'=>'FATTURA DIFFERITA A CLIENTE',
                                                   'FAI'=>'FATTURA IMMEDIATA A CLIENTE',
                                                   'FAP'=>'PARCELLA',
                                                   'FNC'=>'NOTA CREDITO A CLIENTE',
                                                   'FND'=>'NOTA DEBITO A CLIENTE',
                                                   'VCO'=>'CORRISPETTIVO',
                                                   'VRI'=>'RICEVUTA',
                                                   'AFA'=>'FATTURA D\'ACQUISTO',
                                                   'AFC'=>'NOTA CREDITO DA FORNITORE',
                                                   'AFD'=>'NOTA DEBITO DA FORNITORE'
                                                   ),
                           'customer'=>'Cliente',
                           'taxable'=>'Imponibile',
                           'vat'=>'I.V.A.',
                           'stamp'=>'Bolli su tratte',
                           'tot'=>'Totale'
                           )
                   );
?>