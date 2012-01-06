<?php
/*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2011 - Antonio De Vincentiis Montesilvano (PE)
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

    Traduzione Tedesca, Da Sangregorio Antonino.
 --------------------------------------------------------------------------
*/
$strScript = array ("admin_aziend.php" =>
                   array(  'title'=>'Management Company',
                           'ins_this'=>'Geben Sie die Konfiguration des Unternehmens',
                           'upd_this'=>'Ändern Sie die Konfiguration des Unternehmens ',
                           'errors'=>array('Sie müssen ein Firmenname',
                                           'Sie müssen ein Geschlecht',
                                           'Geburtsdatum falsch',
                                           'Sie müssen in-Adresse eingeben',
                                           'Sie müssen eine Stadt',
                                           'Sie müssen Provinzen geben',
                                           'Tax-Code ist formal korrekt',
                                           'Die Steuer-Code ist nicht eine natürliche',
                                           'Die MwSt-Code ist formal korrekt',
                                           'Die Abgabenordnung ist keine juristische',
                                           'Sie müssen Ihre Abgabenordnung',
                                           'Das Bild muss im PNG werden',
                                           'Das Bild hat eine Größe 64kb Greater Than',
                                           'Die gewählte Farbe hat eine Helligkeit von weniger als 408  (hex88 +88 +88)',
                                           'Sie müssen ein Bild für das Firmen-Logo',
                                           'Ungültige Postleitzahl',
                                           'Email address formally wrong',
                                           'Web address formally wrong'
                                    ),
                           'codice'=>"Code ",
                           'ragso1'=>"Firmenname 1",
                           'ragso2'=>"Firmenname 2",
                           'image'=>"Firmenlogo <br /> (jpg,gif,png) ca. 400x400px max 64kb",
                           'sedleg'=>"Juristische Adresse",
                           'legrap'=>"Gesetzlicher Vertreter",
                           'sexper'=>"Sex oder juristische Person",
                           'sexper_value'=>array(''=>'-','M'=>'Male','F'=>'Female','G'=>'Legal'),
                           'datnas'=>'Geburtsdatum',
                           'luonas'=>'Geburtsort - County',
                           'indspe'=>'Addresse',
                           'capspe'=>'Postleitzahl',
                           'citspe'=>'City - Provinz',
                           'country'=>'Land',
                           'telefo'=>'Telefon',
                           'fax'=>'Fax',
                           'codfis'=>'Tax code',
                           'pariva'=>'MwSt-Nummer',
                           'rea'=>'R.E.A.',
                           'e_mail'=>'e mail',
                           'web_url'=>'Web url<br />(es: http://companyname.com)',
                           'cod_ateco'=>'Activity code (ATECOFIN)',
                           'regime'=>'Accounting regime',
                           'regime_value'=>array('0'=>'Ordinary','1'=>'Semplified'),
                           'decimal_quantity'=>'N&ordm; decimal quantity',
                           'decimal_quantity_value'=>array(0,1,2,3,9=>'Float'),
                           'decimal_price'=>'N&ordm; decimal price',
                           'stock_eval_method'=>'Method of stock enhancement',
                           'stock_eval_method_value'=>array(0=>'Standard',1=>'Weighted average cost',2=>'LIFO',3=>'FIFO'),
                           'mascli'=>'Master Konto Kunden ',
                           'masfor'=>'Master Lieferanten Konto',
                           'masban'=>'Master Banken Konto',
                           'cassa_'=>'Cash-Konto',
                           'ivaacq'=>'Käufe MwSt-Konto',
                           'ivaven'=>'Zahlen MwSt. konto',
                           'ivacor'=>'Tickets MwSt-Konto',
                           'ivaera'=>'Schatzamt MwSt-Konto',
                           'impven'=>'Steuerpflichtige Umsätze Konto',
                           'imptra'=>'Verkehr Einnahmen-Konto',
                           'impimb'=>'Verpackung Einnahmen belaufen sich auf',
                           'impspe'=>'Der Umsatz der Sammlung Rechnung',
                           'impvar'=>'Sonstige Einnahmen-Konto',
                           'boleff'=>'Stamp-Konto',
                           'omaggi'=>'Geschenke-Konto',
                           'sales_return'=>'Sales Konto zurück',
                           'impacq'=>'Steuerpflichtige Käufe',
                           'cost_tra'=>'Die Transportkosten Rechnung',
                           'cost_imb'=>'Verpackung ausmachen',
                           'cost_var'=>'Sonstige Kosten Rechnung',
                           'purchases_return'=>'Käufe zurückkehren Konto',
                           'coriba'=>'Portefeuille Ri.Ba Konto',
                           'cotrat'=>'Portefeuille Entwurf Rechnung',
                           'cocamb'=>'Portefeuille  Rechnungen Konto ',
                           'c_ritenute'=>'Quellensteuer-Konto',
                           'ritenuta'=>'% Quellensteuer',
                           'upgrie'=>'Letzte Seite der Mehrwertsteuer Zusammenfassung reg.',
                           'upggio'=>'Letzte Seite der Zeitschrift',
                           'upginv'=>'Letzte Seite der Buchbestände',
                           'upgve'=>'Letzten Seiten Verkaufsrechnung reg.',
                           'upgac'=>'Letzten Seiten Käufe Rechnung reg.',
                           'upgco'=>'Letzten Seiten des Tickets Rechnung reg.',
                           'sezione'=>'MwSt. Abschnitt ',
                           'acciva'=>'Umsatzsteuervoranmeldung (in%)',
                           'ricbol'=>'Höhe der Stempelsteuer auf Einnahmen',
                           'perbol'=>'Rate von Briefmarken über den Entwurf (%)',
                           'round_bol'=>'Runde von Briefmarken',
                           'round_bol_value'=>array(1=>'cent',5=>'cents',10=>'cents',
                                                    50=>'cents',100=>'cents (unit)'),
                           'sperib'=>'RIBA Inkassokosten verrechnet werden',
                           'desez'=>'Beschreibung des',
                           'fatimm'=>'Abschnitt der unmittelbaren Abrechnung',
                           'fatimm_value'=>array('R'=>'Report section','U'=>'Section of last entry',
                                                 '1'=>'Always 1','2'=>'Always 2','3'=>'Always 3'),
                           'artsea'=>'Artikel suchen',
                           'artsea_value'=>array('C'=>'Code','B'=>'Barcode','D'=>'Description'),
                           'templ_set'=> 'Vorlage der Unterlagen gesetzt',
                           'colore'=>'Hintergrundfarbe von Dokumenten',
                           'conmag'=>'Stock Datensätze',
                           'conmag_value'=>array(0=>'Never',1=>'Manual (not recommended)',2=>'Automatic'),
                           'ivam_t'=>'Frequenz Zahlung der Mehrwertsteuer',
                           'ivam_t_value'=>array('M'=>'Monatlich','T'=>'Vierteljährlich'),
                           'alliva'=>'Gewöhnlich Mehrwertsteuersatz',
                           'interessi'=>'Zinsen auf Vierteljährlich MwSt.'
                         ),
                     "report_aziend.php" =>
                   array(  'title'=>'Liste der installierten Unternehmen',
                           'ins_this'=>'Neues Unternehmen',
                           'upd_this'=>'Update Unternehmen ',
                           'codice'=>'ID',
                           'ragso1'=>'Firmenname',
                           'e_mail'=>'Internet',
                           'telefo'=>'Telephone',
                           'regime'=>'Regime',
                           'regime_value'=>array('0'=>'Ordinary','1'=>'Semplified'),
                           'ivam_t'=>'MwSt. Frequenz',
                           'ivam_t_value'=>array('M'=>'Monatlich','T'=>'Vierteljährlich')
                         ),
                     "create_new_enterprise.php" =>
                   array(  'title'=>'Erstellen Sie ein neues Unternehmen',
                           'errors'=>array('Code muss zwischen 1 und 999 sein!',
                                           'Buchungskreis bereits im Einsatz!'
                                          ),
                           'codice'=>'ID number (code)',
                           'ref_co'=>'Firmen Referenz für Bevölkerungsdaten',
                           'clfoco'=>'Neues Rechnungswesen Flugzeug',
                           'users'=>'Lassen Sie die Benutzer der Firma Referenzen',
                           'clfoco_value'=>array(0=>'Keine (nicht empfohlen)',
                                                 1=>'Ja, aber ohne Kunden, suppliers and banks',
                                                 2=>'Ja, einschließlich Kunden, lieferanten und Banken '),
                           'base_arch'=>'Auffüllen Base-Archiv',
                           'base_arch_value'=>array(0=>'Keine (nicht empfohlen)',
                                                    1=>'Ja, aber ohne Träger und Verpackung',
                                                    2=>'Ja, einschließlich Träger und Verpackung')
                         ),
                    "admin_pagame.php" =>
                   array(  "Zahlungen Methode",
                           "Zahlung Kode",
                           "Beschreibung",
                           "Zahlungsart",
                           "Gleichzeitige Erfassung",
                           "Art der Wirkung",
                           "Effect Tag",
                           "Ausgeschlossen Monat",
                           "Nächster Monat",
                           "Am nächsten Tag",
                           "Anzahl der Zahlungen",
                           "Periodizität",
                           "Bankkonto",
                           "Note",
                           array('C' => 'Simultaneous','D' => 'direkte Überweisung','B' => 'Bank recepit','R' => 'Empfang mit Umsatzabgabe','T' => 'Wechsel','V' => 'RID'),
                           array('S' => 'ja','N' => 'Nein'),
                           array('D'=>'Rechnungsdatum', 'G'=>'Fest tag','F'=>'Ende des Monats'),
                           array('Q' => 'vierzehntägig','M' => 'Monatlich','B' => 'zweimonatlich','T' => 'trimestrali','U' => 'vierteljährlich','S' => 'semestrali','A' => 'jährlich'),
                           "Der Code gewählt wird bereits verwendet!",
                           "Die Beschreibung ist leer!",
                           "Der Code muss zwischen 1 und 99 werden",
                           'ins_this'=>'Legen Sie eine neue Zahlungsmethode'
                           ),
                    "report_aliiva.php" =>
                   array(  'title'=>"MWST. rates",
                           'ins_this'=>'Legen Sie eine neue Mehrwertsteuersatz',
                           'codice'=>"Code",
                           'descri'=>"Beschreibung",
                           'type'=>"Typ",
                           'aliquo'=>"Rate"
                        ),
                    "admin_aliiva.php" =>
                   array(  "MwSt-Satz",
                           "Code",
                           "Beschreibung",
                           "%Satz",
                           "Note",
                           "Die gewählten Code bereits verwendet wurde!",
                           "Der Code muss zwischen 1 und 99 werden",
                           "Die Beschreibung ist leer!",
                           "% Rate ungültig!",
                           "Typ MwSt."
                         ),
                    "admin_banapp.php" =>
                   array(  'title'=>'Bank-Management-Unterstützung',
                           'ins_this'=>'Legen Sie eine neue Bank zu unterstützen',
                           'upd_this'=>'Update Bankenpaket',
                           'errors'=>array('Invalid code (min=1 max=99)!',
                                           'Der Code gewählt wird bereits verwendet!',
                                           'Geben Beschreibung!',
                                           'Ungültige ABI-Code!',
                                           'Ungültige CAB-Code!'
                                          ),
                           'codice'=>"Code ",
                           'descri'=>"Beschreibung ",
                           'codabi'=>"ABI Code",
                           'codcab'=>"CAB Code ",
                           'locali'=>"City",
                           'codpro'=>"State",
                           'annota'=>"Note",
                           'report'=>'Bericht des Banken-Unterstützung',
                           'del_this'=>' Bank-Unterstützung'
                         ),
                    "admin_imball.php" =>
                   array(  'title'=>'Paketverwaltung',
                           'ins_this'=>'Legen Sie eine neue Paket-Typ',
                           'upd_this'=>'Update-Paket',
                           'errors'=>array('Invalid code (min=1 max=99)!',
                                           'Der Code gewählt wird bereits verwendet!',
                                           'Geben Beschreibung!',
                                           'Das Gewicht darf nicht negativ sein!'
                                          ),
                           'codice'=>"Code ",
                           'descri'=>" Beschreibung",
                           'weight'=>"Gewicht",
                           'annota'=>"Note",
                           'report'=>'Liste der Pakete',
                           'del_this'=>'Paket'
                         ),
                    "admin_portos.php" =>
                   array(  'title'=>'Rendered ports management',
                           'ins_this'=>'Legen Sie neue gerendert Hafen',
                           'upd_this'=>'Update gemacht Hafen',
                           'errors'=>array('Invalid code (min=1 max=99)!',
                                           'The code chosen is already been used!',
                                           'Enter description!'
                                          ),
                           'codice'=>"Code ",
                           'descri'=>"Description ",
                           'annota'=>"Note",
                           'report'=>'List of the ports',
                           'del_this'=>'port'
                         ),
                    "admin_spediz.php" =>
                   array(  'title'=>'Delivery management',
                           'ins_this'=>'Insert new delivery',
                           'upd_this'=>'Update delivery',
                           'errors'=>array('Invalid code (min=1 max=99)!',
                                           'Der Code gewählt wird bereits verwendet!',
                                           'Geben Beschreibung!'
                                          ),
                           'codice'=>"Code ",
                           'descri'=>"Beschereibung ",
                           'annota'=>"Note",
                           'report'=>'Liste der Lieferungen',
                           'del_this'=>'Lieferung'
                         ),
                    "report_banche.php" =>
                   array(  'title'=>"Bank-Konten",
                           'ins_this'=>'Legen Sie eine neue Bankverbindung',
                           'msg'=>array('Bestehendes Bankkonto NUR EIN PLAN Volkswirtschaftlicher Gesamtrechnungen "," Profil und / oder Ausdrucken der Geschäftsbücher'),
                           'codice'=>"Code",
                           'ragso1'=>"Name",
                           'iban'=>"IBAN code",
                           'citspe'=>"City",
                           'prospe'=>"Provinz",
                           'telefo'=>"Telephone"
                        ),
                    "admin_bank_account.php" =>
                   array(  " Bankkonto",
                           "Code-Nummer (von acconting Plan) ",
                           "Beschreibung ",
                           "Bank Kredit (anstelle der Beschreibung wählen)",
                           "Addresse ",
                           "Postleitzahl",
                           "City - County code ",
                           "Nation ",
                           "IBAN code ",
                           "Hauptsitz",
                           "Telephone ",
                           "Fax ",
                           "e-mail ",
                           "Note ",
                           "Die Jahresabschlüsse der Plan nicht die Master-Banken!",
                           "In Konfiguration Firma wurde kein Meister ausgewählten Banken!",
                           "IBAN ist falsch!",
                           "Bestehende Code!",
                           "Code weniger als 1!",
                           "Beschreibung leer!",
                           "Die Nation ist unvereinbar mit der IBAN!"),
                    "admin_vettore.php" =>
                    array( 'title'=>'Shipper admin',
                           'ins_this'=>'Legen Sie neue Verlader',
                           'upd_this'=>'Update Verlader n.',
                           'errors'=>array('Firmenname nicht vorhanden!',
                                           'Adresse fehlt!',
                                           'City fehlt!',
                                           'PLZ angeben!',
                                           'MWst-Code ist falsch',
                                           'USt-Id-Nummer ist falsch',
                                           'Umsatzsteuer-Identifikationsnummer fehlt!'
                                          ),
                           'codice'=>"Code",
                           'ragione_sociale'=>'Firmenname',
                           'indirizzo'=>'Addresse',
                           'cap'=>'Postleitzahl',
                           'citta'=>'City',
                           'provincia'=>'Provinz',
                           'partita_iva'=>'Umsatzsteuer-Identifikationsnummer',
                           'codice_fiscale'=>'MWst code',
                           'n_albo'=>'Absender Registriernummer',
                           'telefo'=>'Telephone',
                           'descri'=>'Andere Bezeichnung',
                           'annota'=>'Note',
                           'report'=>'Liste der Verlader',
                           'del_this'=>'Absender'
                           ),
                    "admin_utente.php" =>
                   array(  'title'=>'Verwaltung der Benutzer',
                           'ins_this'=>'Legen Sie neue Benutzer',
                           'upd_this'=>'Update Benutzer',
                           'errors'=>array('Nickname bereits vorhanden!',
                                           'Geben Sie Name!',
                                           "Geben Sie Nickname!",
                                           "Enter Password!",
                                           "Das Passwort ist nicht lang genug!",
                                           "Das Passwort ist verschieden von der Bestätigung!",
                                           "Sie können nicht erhöhen Ihre Befähigung der Operation ist für den Administrator vorbehalten!",
                                           "Die Datei muss im JPG-Format sein",
                                           "Das Bild darf nicht größer als 10 kb sein",
                                           "Man kann nicht mit einem geringeren als 9, weil Sie die letzte Administrator sind!"
                                          ),
                           'Login'=>"Nickname",
                           'Cognome'=>"Familienname",
                           'Nome'=>"Name",
                           'image'=>'Image <br /> (nur JPG-Format, max 10kb)',
                           'Abilit'=>"Ebene",
                           'Access'=>"Access-Nummer",
                           'pre_pass'=>'Passwort (min.',
                           'post_pass'=>'Zeichen)',
                           'rep_pass'=>'Kennwort wiederholen',
                           'lang'=>'Sprache',
                           'style'=>'Theme / style',
                           'mod_perm'=>'Module ermöglichen',
                           'report'=>'Benutzer list',
                           'del_this'=>'Benutzer',
                           'del_err'=>'Sie können nicht löschen, weil Sie \ der einzige mit Administrator-Rechten!'
                         )
                    );
?>