<?php
/*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2012 - Antonio De Vincentiis Montesilvano (PE)
                                (www.devincentiis.it)
                        <http://gazie.it>
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
 $strScript = array ("admin_aziend.php" =>
                   array(  'title'=>'Gestione delle aziende',
                           'ins_this'=>'Inserisci l\'azienda',
                           'upd_this'=>'Modifica l\'azienda ',
                           'errors'=>array('&Egrave; necessario indicare la Ragione Sociale',
                                           '&Egrave; necessario indicare il sesso',
                                           'La data di nascita &egrave; sbagliata',
                                           '&Egrave; necessario indicare l\'inidirizzo',
                                           '&Egrave; necessario indicare la citt&agrave;',
                                           '&Egrave; necessario indicare la provincia',
                                           'Il codice fiscale &egrave; formalmente errato',
                                           'Il codice fiscale non &egrave; di una persona fisica',
                                           'La partita IVA &egrave; formalmente errata',
                                           'Il codice fiscale non &egrave; di una persona giuridica',
                                           '&Egrave; necessario indicare il codice fiscale',
                                           'Il file immagine dev\'essere nel formato PNG',
                                           'il file dell\'immagine ha una dimensione maggiore di 64kb',
                                           'Il colore che hai scelto ha una luminosit&agrave; minore di 408 (hex88+88+88)',
                                           'Devi inserire una immagine per il logo aziendale',
                                           'Il codice di avviamento postale (CAP) &egrave; sbagliato',
                                           'Indirizzo email formalmente sbagliato',
                                           'Indirizzo web formalmente sbagliato'
                                          ),
                           'codice'=>"Codice ",
                           'ragso1'=>"Ragione Sociale 1",
                           'ragso2'=>"Ragione Sociale 2",
                           'image'=>"Logo Aziendale<br /> (jpg,png,gif) circa 400x400px max 64kb",
                           'sedleg'=>"Sede legale",
                           'legrap'=>"Legale rappresentante ",
                           'sexper'=>"Sesso/pers.giuridica ",
                           'sexper_value'=>array(''=>'-','M'=>'Maschio','F'=>'Femmina','G'=>'Giuridica'),
                           'datnas'=>'Data di nascita',
                           'luonas'=>'Luogo di nascita - Provincia',
                           'indspe'=>'Indirizzo',
                           'capspe'=>'Codice Postale',
                           'citspe'=>'Citt&agrave; - Provincia',
                           'country'=>'Nazione',
                           'telefo'=>'Telefono',
                           'fax'=>'Fax',
                           'codfis'=>'Codice Fiscale',
                           'pariva'=>'Partita I.V.A.',
                           'rea'=>'R.E.A.',
                           'e_mail'=>'e mail',
                           'web_url'=>'Web url<br />(es: http://nomeazienda.it)',
                           'cod_ateco'=>'Codice attivit&agrave; (ATECOFIN)',
                           'regime'=>'Regime contabile',
                           'regime_value'=>array('0'=>'Ordinario','1'=>'Semplificato'),
                           'decimal_quantity'=>'N&ordm; decimali sulla quantit&agrave;',
                           'decimal_quantity_value'=>array(0,1,2,3,9=>'Float'),
                           'decimal_price'=>'N&ordm; decimali sui prezzi',
                           'stock_eval_method'=>'Metodo di valorizzazione magazzino',
                           'stock_eval_method_value'=>array(0=>'Standard',1=>'Prezzo medio ponderato',2=>'LIFO',3=>'FIFO'),
                           'mascli'=>'Mastro clienti ',
                           'masfor'=>'Mastro fornitori',
                           'masban'=>'Mastro banche',
                           'cassa_'=>'Conto cassa',
                           'ivaacq'=>'Conto I.V.A. acquisti',
                           'ivaven'=>'Conto I.V.A. vendite',
                           'ivacor'=>'Conto I.V.A. corrispettivi',
                           'ivaera'=>'Conto I.V.A. liquidazione',
                           'impven'=>'Conto imponibile vendite',
                           'imptra'=>'Conto spese di trasporto',
                           'impimb'=>'Conto spese di imballo',
                           'impspe'=>'Conto spese incasso effetti',
                           'impvar'=>'Conto spese varie',
                           'boleff'=>'Conto bolli su effetti',
                           'omaggi'=>'Conto omaggi',
                           'sales_return'=>'Resi su vendite',
                           'impacq'=>'Conto imponibile acquisti',
                           'cost_tra'=>'Conto acquisto trasporti',
                           'cost_imb'=>'Conto acquisto imballi',
                           'cost_var'=>'Conto acquisti vari',
                           'purchases_return'=>'Resi su acquisti',
                           'coriba'=>'Conto portafoglio Ri.Ba ',
                           'cotrat'=>'Conto portafoglio Tratte',
                           'cocamb'=>'Conto portafoglio Cambiali',
                           'c_ritenute'=>'Conto ritenute subite',
                           'ritenuta'=>'% Ritenuta',
                           'upgrie'=>'Ultima pagina riepilogativo IVA',
                           'upggio'=>'Ultima pagina Libro Giornale',
                           'upginv'=>'Ultima pagina Libro Inventari',
                           'upgve'=>'Ultime pagine registri Fatture Vendita',
                           'upgac'=>'Ultime pagine registri Fatture degli Acquisti',
                           'upgco'=>'Ultime pagine registri Corrispettivi',
                           'sezione'=>'Sez. IVA ',
                           'acciva'=>'Percentuale d\'acconto I.V.A. (%)',
                           'ricbol'=>'Importo bollo su ricevute',
                           'perbol'=>'Aliquota imposta di bollo su tratte (%)',
                           'round_bol'=>'Arrotondamento bolli',
                           'round_bol_value'=>array(1=>'centesimo',5=>'centesimi',10=>'centesimi',
                                                    50=>'centesimi',100=>'centesimi (unit&agrave;)'),
                           'sperib'=>'Spese incasso RIBA da addebitare ',
                           'desez'=>'Descrizione della ',
                           'fatimm'=>'Sezione delle Fatture Immediate',
                           'fatimm_value'=>array('R'=>'Sezione del Report','U'=>'Sezione ultima emissione',
                                                 '1'=>'Propone sempre 1','2'=>'Propone sempre 2','3'=>'Propone sempre 3'),
                           'artsea'=>'Ricerca articoli per',
                           'artsea_value'=>array('C'=>'Codice','B'=>'Barcode','D'=>'Descrizione'),
                           'templ_set'=> 'Template set dei documenti',
                           'colore'=>'Colore sfondo documenti',
                           'conmag'=>'Contabilit&agrave; di magazzino',
                           'conmag_value'=>array(0=>'Nessuna',1=>'Manuale (sconsigliata)',2=>'Automatica'),
                           'ivam_t'=>'Periodicit&agrave; liquidazione IVA',
                           'ivam_t_value'=>array('M'=>'Mensile','T'=>'Trimestrale'),
                           'alliva'=>'Aliquota preminente',
                           'interessi'=>'Interessi su IVA Trimestrale'
                         ),
                     "report_aziend.php" =>
                   array(  'title'=>'Lista delle aziende installate',
                           'ins_this'=>'Crea una nuova azienda',
                           'upd_this'=>'Modifica l\'azienda ',
                           'codice'=>'ID',
                           'ragso1'=>'Ragione Sociale',
                           'e_mail'=>'Internet',
                           'telefo'=>'Telefono',
                           'regime'=>'Regime',
                           'regime_value'=>array('0'=>'Ordinario','1'=>'Semplificato'),
                           'ivam_t'=>'Periodicit&agrave; IVA',
                           'ivam_t_value'=>array('M'=>'Mensile','T'=>'Trimestrale')
                         ),
                     "create_new_enterprise.php" =>
                   array(  'title'=>'Crea una nuova azienda',
                           'errors'=>array('Il codice deve essere compreso tra 1 e 999!',
                                           'Codice azienda gi&agrave; in uso!'
                                          ),
                           'codice'=>'Numero ID (codice)',
                           'ref_co'=>'Azienda di riferimento per il popolamento dei dati',
                           'clfoco'=>'Creazione piano dei conti',
                           'users'=>'Abilita gli stessi utenti dell\'azienda di riferimento',
                           'clfoco_value'=>array(0=>'No (sconsigliato)',
                                                 1=>'Si, ma senza clienti,fornitori e banche',
                                                 2=>'Si, compresi clienti,fornitori e banche'),
                           'base_arch'=>'Popolamento degli archivi di base',
                           'base_arch_value'=>array(0=>'No (sconsigliato)',
                                                    1=>'Si, ma senza vettori e imballi',
                                                    2=>'Si, compresi vettori e imballi')
                         ),
                     "admin_pagame.php" =>
                   array(  "Modalit&agrave; di pagamento",
                           "Codice pagamento",
                           "Descrizione",
                           "Tipo di pagamento",
                           "Incasso automatico",
                           "Tipo di decorrenza",
                           "Giorni di decorrenza",
                           "Mese escluso",
                           "Mese successivo",
                           "Giorno successivo",
                           "Numero di rate",
                           "Tipo di rate",
                           "C/C bancario d'accredito",
                           "Annotazioni",
                           array('C' => 'contanti','D' => 'rimessa diretta','B' => 'ricevuta bancaria','R' => 'ricevuta con bollo','T' => 'cambiale-tratta','V' => 'mediante avviso(MAV)'),
                           array('S' => 'Si','N' => 'No'),
                           array('D'=>'data fattura', 'G'=>'giorno fisso','F'=>'fine mese'),
                           array('Q' => 'quindicinali','M' => 'mensili','B' => 'bimestrali','T' => 'trimestrali','U' => 'quadrimestrali','S' => 'semestrali','A' => 'annuali'),
                           "Il codice scelto &egrave; gi&agrave; stato usato!",
                           "La descrizione &egrave; vuota!",
                           "Il codice dev'essere compreso tra 1 e 99",
                           'ins_this'=>'Inserisci una nuova modalit&agrave; di pagamento'
                         ),
                    "report_aliiva.php" =>
                   array(  'title'=>"Aliquote I.V.A.",
                           'ins_this'=>'Inserisci una nuova aliquota IVA',
                           'codice'=>"Codice",
                           'descri'=>"Descizione",
                           'type'=>"Tipo",
                           'aliquo'=>"Percentuale"
                        ),
                     "admin_aliiva.php" =>
                   array(  "Aliquota IVA",
                           "Codice",
                           "Descrizione",
                           "% aliquota",
                           "Annotazioni",
                           "Il codice scelto &egrave; gi&agrave; stato usato!",
                           "Il codice dev'essere compreso tra 1 e 99",
                           "La descrizione &egrave; vuota!",
                           "% Aliquota non valida!",
                           "Tipo IVA"
                         ),
                    "admin_banapp.php" =>
                   array(  'title'=>'Gestione delle banche di appoggio',
                           'ins_this'=>'Inserisci una nuova banca di appoggio',
                           'upd_this'=>'Modifica la banca di appoggio ',
                           'errors'=>array('Il codice non &egrave; valido (min=1 max=99)!',
                                           'Il codice scelto &egrave; gi&agrave; stato usato!',
                                           'Non &egrave; stata inserita la descrizione!',
                                           'Il codice ABI non  &egrave; valido!',
                                           'Il codice CAB non  &egrave; valido!'
                                          ),
                           'codice'=>"Codice ",
                           'descri'=>"Descrizione ",
                           'codabi'=>"Codice ABI",
                           'codcab'=>"Codice CAB",
                           'locali'=>"Localit&agrave;",
                           'codpro'=>"Provincia",
                           'annota'=>"Annotazioni",
                           'report'=>'Lista delle banche di appoggio'
                         ),
                    "admin_imball.php" =>
                   array(  'title'=>'Gestione degli imballi',
                           'ins_this'=>'Inserisci un nuovo tipo di imballo',
                           'upd_this'=>'Modifica l\'imballo ',
                           'errors'=>array('Il codice non &egrave; valido (min=1 max=99)!',
                                           'Il codice scelto &egrave; gi&agrave; stato usato!',
                                           'Non &egrave; stata inserita la descrizione!',
                                           'Il peso non dev\'essere negativo'
                                          ),
                           'codice'=>"Codice ",
                           'descri'=>"Descrizione imballo ",
                           'weight'=>"Peso",
                           'annota'=>"Annotazioni",
                           'report'=>'Lista degli imballi',
                           'del_this'=>'imballo'
                         ),
                    "admin_portos.php" =>
                   array(  'title'=>'Gestione dei porti/rese',
                           'ins_this'=>'Inserisci una nuovo porto/resa',
                           'upd_this'=>'Modifica il porto/resa ',
                           'errors'=>array('Il codice non &egrave; valido (min=1 max=99)!',
                                           'Il codice scelto &egrave; gi&agrave; stato usato!',
                                           'Non &egrave; stata inserita la descrizione!'
                                          ),
                           'codice'=>"Codice ",
                           'descri'=>"Descrizione porto/resa ",
                           'annota'=>"Annotazioni",
                           'report'=>'Lista dei porti/rese',
                           'del_this'=>'porto/resa'
                         ),
                    "admin_spediz.php" =>
                   array(  'title'=>'Gestione delle spedizioni',
                           'ins_this'=>'Inserisci una nuovo tipo di spedizione',
                           'upd_this'=>'Modifica la spedizione ',
                           'errors'=>array('Il codice non &egrave; valido (min=1 max=99)!',
                                           'Il codice scelto &egrave; gi&agrave; stato usato!',
                                           'Non &egrave; stata inserita la descrizione!'
                                          ),
                           'codice'=>"Codice ",
                           'descri'=>"Descrizione spedizione ",
                           'annota'=>"Annotazioni",
                           'report'=>'Lista delle spedizioni',
                           'del_this'=>'spedizione'
                         ),
                    "report_banche.php" =>
                   array(  'title'=>"Conti correnti bancari",
                           'ins_this'=>'Inserisci un nuovo conto corrente bancario',
                           'msg'=>array('CONTO BANCARIO ESISTENTE SOLO SUL PIANO DEI CONTI','Visualizza e stampa il partitario'),
                           'codice'=>"Codice",
                           'ragso1'=>"Nome",
                           'iban'=>"Codice IBAN",
                           'citspe'=>"Citt&agrave;",
                           'prospe'=>"Prov.",
                           'telefo'=>"Telefono"
                        ),
                    "admin_bank_account.php" =>
                   array(  "Conto corrente bancario ",
                           "Codice (dal piano dei conti) ",
                           "Descrizione ",
                           "Banca d'appoggio (selezionare in luogo della descrizione)",
                           "Indirizzo ",
                           "CAP ",
                           "Citt&agrave; - Provincia ",
                           "Nazione ",
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
                           "La nazione &egrave; incompatibile con l'IBAN!"
                        ),
                    "admin_vettore.php" =>
                    array( 'title'=>'Gestione dei vettori',
                           'ins_this'=>'Inserisci un nuovo vettore',
                           'upd_this'=>'Modifica il vettore n.',
                           'errors'=>array('Non &egrave; stata inserita la ragione sociale',
                                           'Non &egrave; stato inserito l\'indirizzo',
                                           'Non &egrave; stata inserita la citt&agrave;',
                                           'Non &egrave; stato inserito il codice d\'avviamento postale',
                                           'Il codice fiscale &egrave; formalmente errato',
                                           'La partita IVA &egrave; formalmente errata',
                                           'Non &egrave; stata inserita la partita IVA'
                                          ),
                           'codice'=>"Codice",
                           'ragione_sociale'=>'Ragione Sociale',
                           'indirizzo'=>'Indirizzo',
                           'cap'=>'CAP',
                           'citta'=>'Citta\'',
                           'provincia'=>'Provincia',
                           'partita_iva'=>'Partita IVA',
                           'codice_fiscale'=>'Codice Fiscale',
                           'n_albo'=>'Numero iscrizione albo autotrasportatori',
                           'telefo'=>'Recapito telefonico',
                           'descri'=>'Altre descrizioni',
                           'annota'=>'Annotazioni',
                           'report'=>'Lista dei vettori',
                           'del_this'=>'vettori'
                           ),
                    "admin_utente.php" =>
                   array(  'title'=>'Gestione degli utenti',
                           'ins_this'=>'Inserire un nuovo utente',
                           'upd_this'=>'Modificare l\'utente',
                           'errors'=>array('Il nickname &egrave; gi&agrave; usato!',
                                           'Inserire il cognome!',
                                           "Inserire il nickname!",
                                           "E' necessario inserire la Password !",
                                           "La password non &egrave; sufficientemente lunga!",
                                           "La password &egrave; diversa da quella di conferma!",
                                           "Non puoi aumentere il tuo Livello di Abilitazione l'operazione &egrave; riservata all'amministratore!",
                                           "Il file dev'essere in formato JPG",
                                           "L'immagine non dev'essere pi&ugrave; grande di 10 kb",
                                           "Non puoi avere un livello inferiore a 9 perch&egrave; sei l'unico amministratore!"
                                          ),
                           'Login'=>"Nickname",
                           'Cognome'=>"Cognome",
                           'Nome'=>"Nome",
                           'image'=>'Immagine dell\'utente<br />(solo in formato JPG, max 10kb)',
                           'Abilit'=>"Livello",
                           'Access'=>"Accesso",
                           'pre_pass'=>'Password (min.',
                           'post_pass'=>'caratteri)',
                           'rep_pass'=>'Ripeti la Password',
                           'lang'=>'Language',
                           'style'=>'Tema / stile',
                           'mod_perm'=>'Permessi dei moduli',
                           'report'=>'Lista degli Utenti',
                           'del_this'=>'Utente',
                           'del_err'=>'Non puoi cancellarti perch&egrave; sei l\'unico ad avere i diritti di amministratore! '
                         )
          );
?>