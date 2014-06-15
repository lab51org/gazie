<?php
/*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2014 - Antonio De Vincentiis Montesilvano (PE)
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
    scriva   alla   Free  Software Foundation,  Inc.,   59
    Temple Place, Suite 330, Boston, MA 02111-1307 USA Stati Uniti.
 --------------------------------------------------------------------------
*/
$strScript = array ("admin_aziend.php" =>
                   array(  'title'=>'Gestione delle aziende',
                           'ins_this'=>'Enter the configuration of the company',
                           'upd_this'=>'Change the configuration of the company ',
                           'errors'=>array('You must enter a Company Name',
                                           'You must enter a sex',
                                           'Birthdate incorrect',
                                           'You must enter a address',
                                           'You must enter a city',
                                           'You must enter a province',
                                           'Tax code is formally incorrect',
                                           'The tax code is not a natural',
                                           'The VAT code is formally incorrect',
                                           'The tax code is not a legal',
                                           'You must provide your tax code',
                                           'The picture must be in PNG',
                                           'The picture has a size greater than 64kb',
                                           'The color you choose has a brightness of less than 408 (hex88 +88 +88)',
                                           'You must enter a picture for the company logo',
                                           'Invalid postal code',
                                           'Email address formally wrong',
                                           'Web address formally wrong',
                                           'ATECO 2007 code invalid'
                                    ),
                           'codice'=>"Code ",
                           'ragso1'=>"Company name 1",
                           'ragso2'=>"Company name 2",
                           'image'=>"Company logo<br />(jpg,png,gif) around 400x400px max 64kb",
                           'intermediary'=>"Agenzia delle Entrate intermediary",
                           'sedleg'=>"Legal address",
                           'legrap'=>"Legal representative ",
                           'sexper'=>"Sex or legal person ",
                           'sexper_value'=>array(''=>'-','M'=>'Male','F'=>'Female','G'=>'Legal'),
                           'datnas'=>'Birthdate',
                           'luonas'=>'Place of Birth - County',
                           'indspe'=>'Address',
                           'capspe'=>'Postal code',
                           'citspe'=>'City - Province',
                           'country'=>'Country',
                           'id_language'=>'Language',
                           'id_currency'=>'Currency',
                           'telefo'=>'Telephone',
                           'fax'=>'Fax',
                           'codfis'=>'Tax code',
                           'pariva'=>'VAT code',
                           'rea'=>'R.E.A.',
                           'e_mail'=>'e mail',
                           'web_url'=>'Web url<br />(ex: http://companyname.com)',
                           'cod_ateco'=>'Activity code (ATECOFIN)',
                           'regime'=>'Accounting regime',
                           'regime_value'=>array('0'=>'Ordinary','1'=>'Semplified'),
                           'fiscal_reg'=>'Regime fiscale',
                           'fiscal_reg_value'=>array('RF01'=>'Ordinario','RF02'=>'Contribuenti minimi','RF03'=>'Nuove iniziative produttive','RF04'=>'Agricoltura e attività connesse e pesca',
                                                     'RF05'=>'Vendita sali e tabacchi','RF06'=>'Commercio dei fiammiferi','RF07'=>'Editoria','RF08'=>'Gestione di servizi di telefonia pubblica'),
                           'decimal_quantity'=>'N&ordm; decimal quantity',
                           'decimal_quantity_value'=>array(0,1,2,3,9=>'Float'),
                           'decimal_price'=>'N&ordm; decimal price',
                           'stock_eval_method'=>'Method of stock enhancement',
                           'stock_eval_method_value'=>array(0=>'Standard',1=>'Weighted average cost',2=>'LIFO',3=>'FIFO'),
                           'mascli'=>'Master customers account ',
                           'masfor'=>'Master suppliers account',
                           'masban'=>'Master banks account',
                           'mas_staff'=>'Master employees account',
                           'cassa_'=>'Cash account',
                           'ivaacq'=>'Purchases VAT account',
                           'ivaven'=>'Sales VAT account',
                           'ivacor'=>'Tickets VAT account',
                           'ivaera'=>'Treasury VAT account',
                           'impven'=>'Taxable sales account',
                           'imptra'=>'Transport revenues account',
                           'impimb'=>'Packaging revenues account',
                           'impspe'=>'Revenues of collection account',
                           'impvar'=>'Miscellaneous revenues account',
                           'boleff'=>'Stamp account',
                           'omaggi'=>'Gifts account',
                           'sales_return'=>'Sales return account',
                           'impacq'=>'Taxable purchases account',
                           'cost_tra'=>'Transport costs account',
                           'cost_imb'=>'Packaging costs account',
                           'cost_var'=>'Miscellaneous costs account',
                           'purchases_return'=>'Purchases return account',
                           'coriba'=>'Portfolio Ri.Ba account',
                           'cotrat'=>'Portfolio draft account',
                           'cocamb'=>'Portfolio bills account',
                           'c_ritenute'=>'Withholding account',
                           'ritenuta'=>'% Withholding',
                           'upgrie'=>'Last page of VAT summary reg.',
                           'upggio'=>'Last page of the journal',
                           'upginv'=>'Last Page of Book Inventories',
                           'upgve'=>'Last pages of Sales invoice reg.',
                           'upgac'=>'Last pages of Purchases invoice reg.',
                           'upgco'=>'Last pages of Tickets invoice reg.',
                           'sezione'=>'VAT section ',
                           'acciva'=>'VAT advance rate (%)',
                           'ricbol'=>'Amount of stamp duty on receipts',
                           'perbol'=>'Rate of stamps on draft (%)',
                           'round_bol'=>'Round of stamps',
                           'round_bol_value'=>array(1=>'cent',5=>'cents',10=>'cents',
                                                    50=>'cents',100=>'cents (unit)'),
                           'virtual_stamp_auth_prot'=>'Virtual stamp authorizzation number ',
                           'virtual_stamp_auth_date'=>' date ',
                           'sperib'=>'RIBA collection costs to be charged ',
                           'desez'=>'Description of ',
                           'fatimm'=>'Sezione delle Fatture Immediate',
                           'fatimm_value'=>array('R'=>'Report section','U'=>'Section of last entry',
                                                 '1'=>'Always 1','2'=>'Always 2','3'=>'Always 3'),
                           'artsea'=>'Ricerca articoli per',
                           'artsea_value'=>array('C'=>'Code','B'=>'Barcode','D'=>'Description'),
                           'templ_set'=> 'Template set of the documents',
                           'colore'=>'Background color of documents',
                           'conmag'=>'Stock records',
                           'conmag_value'=>array(0=>'Never',1=>'Manual (not recommended)',2=>'Automatic'),
                           'ivam_t'=>'Frequency VAT payment',
                           'ivam_t_value'=>array('M'=>'Monthly','T'=>'Quarterly'),
                           'alliva'=>'Usually VAT rate',
                           'interessi'=>'Interest on Quarterly VAT'
                         ),
                     "report_aziend.php" =>
                   array(  'title'=>'List of installed enterprise',
                           'ins_this'=>'Create new company',
                           'upd_this'=>'Update company ',
                           'codice'=>'ID',
                           'ragso1'=>'Company name',
                           'e_mail'=>'Internet',
                           'telefo'=>'Telephone',
                           'regime'=>'Regime',
                           'regime_value'=>array('0'=>'Ordinary','1'=>'Semplified'),
                           'ivam_t'=>'VAT Frequency',
                           'ivam_t_value'=>array('M'=>'Monthly','T'=>'Quarterly')
                         ),
                     "create_new_enterprise.php" =>
                   array(  'title'=>'Create a new enterprise',
                           'errors'=>array('Code must be between 1 and 999!',
                                           'Company code already in use!'
                                          ),
                           'codice'=>'ID number (code)',
                           'ref_co'=>'Azienda di riferimento per il popolamento dei dati',
                           'clfoco'=>'Create accounting plane',
                           'users'=>'Allow the users of the company reference',
                           'clfoco_value'=>array(0=>'No (not recommended)',
                                                 1=>'Yes, but without customers, suppliers and banks',
                                                 2=>'Yes, including customers, suppliers and banks'),
                           'base_arch'=>'Populating base archive',
                           'base_arch_value'=>array(0=>'No (not recommended)',
                                                    1=>'Yes, but without carriers and packaging',
                                                    2=>'Yes, including carriers and packaging'),
                           'artico_catmer'=>'Duplicazione articoli di magazzino',
                           'artico_catmer_value'=>array(0=>'No (default)',
                                                        1=>'Sì (normalmente sulle installazione didattiche)')
                         ),
                    "admin_pagame.php" =>
                   array(  "Payments method",
                           "Payment code",
                           "Description",
                           "Payment type",
                           "Simultaneous collection",
                           "Type of effect",
                           "Effect days",
                           "Excluded month",
                           "Next month",
                           "Next day",
                           "Number of payments",
                           "Periodicity",
                           "Bank account",
                           "Note",
                           array('C' => 'Simultaneous','D' => 'rimessa diretta','B' => 'Bank recepit','R' => 'Receipt with stamp tax','T' => 'Bill of exchange','V' => 'RID'),
                           array('S' => 'Yes','N' => 'No'),
                           array('D'=>'invoice date', 'G'=>'fix day','F'=>'end of month'),
                           array('Q' => 'quindicinali','M' => 'mensili','B' => 'bimestrali','T' => 'trimestrali','U' => 'quadrimestrali','S' => 'semestrali','A' => 'annuali'),
                           "Il codice scelto &egrave; gi&agrave; stato usato!",
                           "La descrizione &egrave; vuota!",
                           "Il codice dev'essere compreso tra 1 e 99",
                           'ins_this'=>'Insert new payments method',
                           'fae_mode'=>"PA electronic invoice mode"
                           ),
                    "report_aliiva.php" =>
                   array(  'title'=>"V.A.T. rates",
                           'ins_this'=>'Insert a new VAT rate',
                           'codice'=>"Code",
                           'descri'=>"Description",
                           'type'=>"Type",
                           'aliquo'=>"Rate",
                           'fae_natura'=>"Nature - PA electronic invoice"
                        ),
                    "admin_aliiva.php" =>
                   array(  "VAT rate",
                           "Code",
                           "Description",
                           "% rate",
                           "Note",
                           "The chosen code has already been used!",
                           "The code must be between 1 and 99",
                           "The description is empty!",
                           "% Rate invalid!",
                           "Type VAT",
                           "Select the nature of the exemption / exclusion!",
                           'fae_natura'=>"Nature - PA electronic invoice"
                         ),
                    "admin_banapp.php" =>
                   array(  'title'=>'Bank support management',
                           'ins_this'=>'Insert new bank support',
                           'upd_this'=>'Update bank support',
                           'errors'=>array('Invalid code (min=1 max=99)!',
                                           'The code chosen is already been used!',
                                           'Enter description!',
                                           'Invalid ABI code!',
                                           'Invalid CAB code!'
                                          ),
                           'codice'=>"Code ",
                           'descri'=>"Description ",
                           'codabi'=>"ABI Code",
                           'codcab'=>"CAB Code ",
                           'locali'=>"City",
                           'codpro'=>"State",
                           'annota'=>"Note",
                           'report'=>'Report of Banks support',
                           'del_this'=>' Bank support'
                         ),
                    "admin_imball.php" =>
                   array(  'title'=>'Package management',
                           'ins_this'=>'Insert new package type',
                           'upd_this'=>'Update package',
                           'errors'=>array('Invalid code (min=1 max=99)!',
                                           'The code chosen is already been used!',
                                           'Enter description!',
                                           'The weight can not be negative!'
                                          ),
                           'codice'=>"Code ",
                           'descri'=>"Description ",
                           'weight'=>"Weight",
                           'annota'=>"Note",
                           'report'=>'List of the packages',
                           'del_this'=>'package'
                         ),
                    "admin_portos.php" =>
                   array(  'title'=>'Rendered ports management',
                           'ins_this'=>'Insert new rendered port',
                           'upd_this'=>'Update rendered port',
                           'errors'=>array('Invalid code (min=1 max=99)!',
                                           'The code chosen is already been used!',
                                           'Enter description!'
                                          ),
                           'codice'=>"Code ",
                           'descri'=>"Description ",
                           'annota'=>"Note",
                           'incoterms'=>'Incoterms-standard ICC',
                           'report'=>'List of the ports',
                           'del_this'=>'port'
                         ),
                    "admin_spediz.php" =>
                   array(  'title'=>'Delivery management',
                           'ins_this'=>'Insert new delivery',
                           'upd_this'=>'Update delivery',
                           'errors'=>array('Invalid code (min=1 max=99)!',
                                           'The code chosen is already been used!',
                                           'Enter description!'
                                          ),
                           'codice'=>"Code ",
                           'descri'=>"Description ",
                           'annota'=>"Note",
                           'report'=>'List of deliveries',
                           'del_this'=>'delivery'
                         ),
                    "report_banche.php" =>
                   array(  'title'=>"Bank accounts",
                           'ins_this'=>'Insert new bank account',
                           'msg'=>array('EXISTING BANK ACCOUNT ONLY ON PLAN OF ACCOUNTS','View and/or print the ledgers'),
                           'codice'=>"Code",
                           'ragso1'=>"Name",
                           'iban'=>"IBAN code",
                           'citspe'=>"City",
                           'prospe'=>"Province",
                           'telefo'=>"Telephone"
                        ),
                    "admin_bank_account.php" =>
                   array(  "Bank account ",
                           "Code number (from acconting plan) ",
                           "Description ",
                           "Bank credit (choose in place of description)",
                           "Address ",
                           "Postal code ",
                           "City - County code ",
                           "Nation ",
                           "IBAN code ",
                           'sia_code'=>'SIA Code',
                           'eof'=>'File RiBA record with end of line characters',
                           'eof_value'=>array('S'=>'Yes','N'=>'No'),
                           "Head office ",
                           "Telephone ",
                           "Fax ",
                           "e-mail ",
                           "Note ",
                           "The accounts of the plan does not have the master banks!",
                           "In configuration company was not selected a master banks!",
                           "IBAN is incorrect!",
                           "Existing code!",
                           "Code less than 1!",
                           "Description empty!",
                           "The nation is incompatible with the IBAN!"),
                    "admin_vettore.php" =>
                    array( 'title'=>'Shipper admin',
                           'ins_this'=>'Insert new shipper',
                           'upd_this'=>'Update shipper n.',
                           'errors'=>array('Company name missing!',
                                           'Address missing!',
                                           'City missing!',
                                           'Postal code missing!',
                                           'Tax code is incorrect',
                                           'VAT registration number is incorrect',
                                           'VAT registration number missing!'
                                          ),
                           'codice'=>"Code",
                           'ragione_sociale'=>'Company name',
                           'indirizzo'=>'Address',
                           'cap'=>'Postal Code',
                           'citta'=>'City',
                           'provincia'=>'Province',
                           'partita_iva'=>'VAT registration number',
                           'codice_fiscale'=>'Tax code',
                           'n_albo'=>'Shipper registration number',
                           'telefo'=>'Telephone',
                           'descri'=>'Other description',
                           'annota'=>'Note',
                           'report'=>'List of shippers',
                           'del_this'=>'shipper'
                           ),
                    "admin_utente.php" =>
                   array(  'title'=>'Management of the Users',
                           'ins_this'=>'Insert new User',
                           'upd_this'=>'Update User',
                           'errors'=>array('Nickname already exist!',
                                           'Enter Surname!',
                                           "Enter Nickname!",
                                           "Enter Password!",
                                           "The password is not long enough!",
                                           "The password is different from that of confirmation!",
                                           "You can't increase your level of competency the operation is reserved for the administrator!",
                                           "The file must be in JPG format",
                                           "The image must not be larger than 10 kb",
                                           "You can not have a level lower than 9 because you are the last administrator!"
                                          ),
                           'Login'=>"Nickname",
                           'Cognome'=>"Surname",
                           'Nome'=>"Name",
                           'image'=>'Image<br />(only JPG format, max 10kb)',
                           'Abilit'=>"Level",
                           'Access'=>"Access number",
                           'pre_pass'=>'Password (min.',
                           'post_pass'=>'characters)',
                           'rep_pass'=>'Repeat password',
                           'lang'=>'Language',
                           'style'=>'Theme / style',
                           'mod_perm'=>'Modules permit',
                           'report'=>'Users list',
                           'del_this'=>'User',
                           'del_err'=>'You can not delete because you\'re the only one with administrator rights!'
                         )
                    );
?>