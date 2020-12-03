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

  Traduzione Tedesca, Da Sangregorio Antonino.
  --------------------------------------------------------------------------
 */
$strScript = array("admin_aziend.php" =>
    array('title' => 'Management Company',
        'ins_this' => 'Geben Sie die Konfiguration des Unternehmens',
        'upd_this' => 'Ändern Sie die Konfiguration des Unternehmens ',
        'errors' => array('Sie müssen ein Firmenname',
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
            'Web address formally wrong',
            'ATECO 2007 code invalid'
        ),
        'codice' => "Code ",
        'ragso1' => "Firmenname 1",
        'ragso2' => "Firmenname 2",
        'image' => "Firmenlogo <br /> (jpg,gif,png) ca. 400x400px max 64kb",
        'intermediary' => "Vermittler Agenzia delle Entrate",
        'sedleg' => "Juristische Adresse",
        'legrap_pf_nome' => "Gesetzlicher Vertreter",
        'sexper' => "Sex oder juristische Person",
        'sexper_value' => array('' => '-', 'M' => 'Male', 'F' => 'Female', 'G' => 'Legal'),
        'datnas' => 'Geburtsdatum',
        'luonas' => 'Geburtsort - County',
        'indspe' => 'Addresse',
        'capspe' => 'Postleitzahl',
        'citspe' => 'City - Provinz',
        'country' => 'Land',
        'id_language' => 'Sprache',
        'id_currency' => 'Währung',
        'telefo' => 'Telefon',
        'fax' => 'Fax',
        'codfis' => 'Tax code',
        'pariva' => 'MwSt-Nummer',
        'rea' => 'R.E.A.',
        'e_mail' => 'e mail',
        'web_url' => 'Web url<br />(es: http://companyname.com)',
        'cod_ateco' => 'Activity code (ATECOFIN)',
        'regime' => 'Accounting regime',
        'regime_value' => array('0' => 'Ordinary', '1' => 'Semplified'),
        'fiscal_reg' => 'Regime fiscale',
        'fiscal_reg_value' => array('RF01' => 'Ordinario', 'RF02' => 'Contribuenti minimi', 'RF03' => 'Nuove iniziative produttive', 'RF04' => 'Agricoltura e attività connesse e pesca',
            'RF05' => 'Vendita sali e tabacchi', 'RF06' => 'Commercio dei fiammiferi', 'RF07' => 'Editoria', 'RF08' => 'Gestione di servizi di telefonia pubblica'),
        'decimal_quantity' => 'N&ordm; decimal quantity',
        'decimal_quantity_value' => array(0, 1, 2, 3, 9 => 'Float'),
        'decimal_price' => 'N&ordm; decimal price',
        'stock_eval_method' => 'Method of stock enhancement',
        'stock_eval_method_value' => array(0 => 'Standard', 1 => 'Weighted average cost', 2 => 'LIFO', 3 => 'FIFO'),
        'mascli' => 'Master Konto Kunden ',
        'masfor' => 'Master Lieferanten Konto',
        'masban' => 'Master Banken Konto',
        'mas_staff' => 'Master Arbeitnehmer Konto',
        'cassa_' => 'Cash-Konto',
        'ivaacq' => 'Käufe MwSt-Konto',
        'ivaven' => 'Zahlen MwSt. konto',
        'ivacor' => 'Tickets MwSt-Konto',
        'ivaera' => 'Schatzamt MwSt-Konto',
        'impven' => 'Steuerpflichtige Umsätze Konto',
        'imptra' => 'Verkehr Einnahmen-Konto',
        'impimb' => 'Verpackung Einnahmen belaufen sich auf',
        'impspe' => 'Der Umsatz der Sammlung Rechnung',
        'impvar' => 'Sonstige Einnahmen-Konto',
        'boleff' => 'Stamp-Konto',
        'omaggi' => 'Geschenke-Konto',
        'sales_return' => 'Sales Konto zurück',
        'impacq' => 'Steuerpflichtige Käufe',
        'cost_tra' => 'Die Transportkosten Rechnung',
        'cost_imb' => 'Verpackung ausmachen',
        'cost_var' => 'Sonstige Kosten Rechnung',
        'purchases_return' => 'Käufe zurückkehren Konto',
        'coriba' => 'Portefeuille Ri.Ba Konto',
        'cotrat' => 'Portefeuille Entwurf Rechnung',
        'cocamb' => 'Portefeuille  Rechnungen Konto ',
        'c_ritenute' => 'Quellensteuer-Konto',
        'ritenuta' => '% Quellensteuer',
        'upgrie' => 'Letzte Seite der Mehrwertsteuer Zusammenfassung reg.',
        'upggio' => 'Letzte Seite der Zeitschrift',
        'upginv' => 'Letzte Seite der Buchbestände',
        'upgve' => 'Letzten Seiten Verkaufsrechnung reg.',
        'upgac' => 'Letzten Seiten Käufe Rechnung reg.',
        'upgco' => 'Letzten Seiten des Tickets Rechnung reg.',
        'sezione' => 'MwSt. Abschnitt ',
        'acciva' => 'Umsatzsteuervoranmeldung (in%)',
        'taxstamp' => 'Höhe der Stempelsteuer auf Einnahmen',
        'perbol' => 'Rate von Briefmarken über den Entwurf (%)',
        'round_bol' => 'Runde von Briefmarken',
        'round_bol_value' => array(1 => 'cent', 5 => 'cents', 10 => 'cents',
            50 => 'cents', 100 => 'cents (unit)'),
        'virtual_stamp_auth_prot' => 'Virtual stamp authorizzation number ',
        'virtual_stamp_auth_date' => ' date ',
        'causale_pagam_770' => 'Causale del pagamento ritenuta(mod.770)',
        'causale_pagam_770_value' => array('' => '-------------------',
            'A' => 'Prestazioni di lavoro autonomo rientranti nell’esercizio di arte o professione abituale',
            'B' => 'Utilizzazione economica, da parte dell’autore o dell’inventore, di opere dell’ingegno, di brevetti industriali e di processi, formule o informazioni relativi a esperienze acquisite in campo industriale, commerciale o scientifico',
            'C' => 'Utili derivanti da contratti di associazione in partecipazione e da contratti di cointeressenza, quando l’apporto è costituito esclusivamente dalla prestazione di lavoro',
            'D' => 'Utili spettanti ai soci promotori e ai soci fondatori delle società di capitali',
            'E' => 'Levata di protesti cambiari da parte dei segretari comunali',
            'F' => 'Prestazioni rese dagli sportivi con contratto di lavoro autonomo',
            'G' => 'Indennità corrisposte per la cessazione di attività sportiva professionale',
            'H' => 'Indennità corrisposte per la cessazione dei rapporti di agenzia delle persone fisiche e delle società di persone, con esclusione delle somme maturate entro il 31.12.2003, già imputate per competenza e tassate come reddito d’impresa',
            'I' => 'Indennità corrisposte per la cessazione da funzioni notarili',
            'L' => 'Utilizzazione economica, da parte di soggetto diverso dall’autore o dall’inventore, di opere dell’ingegno, di brevetti industriali e di processi, formule e informazioni relative a esperienze acquisite in campo industriale, commerciale o scientifico',
            'M' => 'Prestazioni di lavoro autonomo non esercitate abitualmente, obblighi di fare, di non fare o permettere',
            'N' => 'Indennità di trasferta, rimborso forfetario di spese, premi e compensi erogati: .. nell’esercizio diretto di attività sportive dilettantistiche; .. in relazione a rapporti di collaborazione coordinata e continuativa di carattere amministrativo-gestionale, di natura non professionale, resi a favore di società e associazioni sportive dilettantistiche e di cori, bande e filodrammatiche da parte del direttore e dei collaboratori tecnici',
            'O' => 'Prestazioni di lavoro autonomo non esercitate abitualmente, obblighi di fare, di non fare o permettere, per le quali non sussiste l’obbligo di iscrizione alla gestione separata (Circ. Inps 104/2001)',
            'P' => 'Compensi corrisposti a soggetti non residenti privi di stabile organizzazione per l’uso o la concessione in uso di attrezzature industriali, commerciali o scientifiche che si trovano nel territorio dello Stato, ecc',
            'Q' => 'Provvigioni corrisposte ad agente o rappresentante di commercio monomandatario',
            'R' => 'Provvigioni corrisposte ad agente o rappresentante di commercio plurimandatario',
            'S' => 'Provvigioni corrisposte a commissionario',
            'T' => 'Provvigioni corrisposte a mediatore',
            'U' => 'Provvigioni corrisposte a procacciatore di affari',
            'V' => 'Provvigioni corrisposte a incaricato per le vendite a domicilio e provvigioni corrisposte a incaricato per la vendita porta a porta e per la vendita ambulante di giornali quotidiani e periodici (L. 25.02.1987, n. 67)',
            'W' => 'Corrispettivi erogati nel 2012 per prestazioni relative a contratti d’appalto cui si sono resi applicabili le disposizioni contenute nell’art. 25-ter D.P.R. 600/1973',
            'X' => 'Canoni corrisposti nel 2004 da società o enti residenti, ovvero da stabili organizzazioni di società estere di cui all’art. 26-quater, c. 1, lett. a) e b) D.P.R. 600/1973, a società o stabili organizzazioni di società, situate in altro Stato membro dell’Unione Europea in presenza dei relativi requisiti richiesti, per i quali è stato effettuato il rimborso della ritenuta ai sensi dell’art. 4 D. Lgs. 143/2005 nell’anno 2006',
            'Y' => 'Canoni corrisposti dall’1.01.2005 al 26.07.2005 da soggetti di cui al punto precedente',
            'Z' => 'Titolo diverso dai precedenti'
        ),
        'sperib' => 'RIBA Inkassokosten verrechnet werden',
        'desez' => 'Beschreibung des',
        'fatimm' => 'Abschnitt der unmittelbaren Abrechnung',
        'fatimm_value' => array('R' => 'Report section', 'U' => 'Section of last entry',
            '1' => 'Always 1', '2' => 'Always 2', '3' => 'Always 3'),
        'artsea' => 'Artikel suchen',
        'artsea_value' => array('C' => 'Code', 'B' => 'Barcode', 'D' => 'Description', 'T' => 'Alle'),
        'templ_set' => 'Vorlage der Unterlagen gesetzt',
        'colore' => 'Hintergrundfarbe von Dokumenten',
        'conmag' => 'Stock Datensätze',
        'conmag_value' => array(0 => 'Never', 1 => 'Manual (not recommended)', 2 => 'Automatic'),
        'ivam_t' => 'Frequenz Zahlung der Mehrwertsteuer',
        'ivam_t_value' => array('M' => 'Monatlich', 'T' => 'Vierteljährlich'),
        'preeminent_vat' => 'Gewöhnlich Mehrwertsteuersatz',
        'interessi' => 'Zinsen auf Vierteljährlich MwSt.'
    ),
    "report_aziend.php" =>
    array('title' => 'Liste der installierten Unternehmen',
        'ins_this' => 'Neues Unternehmen',
        'upd_this' => 'Update Unternehmen ',
        'codice' => 'ID',
        'ragso1' => 'Firmenname',
        'e_mail' => 'Internet',
        'telefo' => 'Telephone',
        'regime' => 'Regime',
        'regime_value' => array('0' => 'Ordinary', '1' => 'Semplified'),
        'ivam_t' => 'MwSt. Frequenz',
        'ivam_t_value' => array('M' => 'Monatlich', 'T' => 'Vierteljährlich')
    ),
    "create_new_company.php" =>
    array('title' => 'Erstellen Sie ein neues Unternehmen',
        'errors' => array('Code muss zwischen 1 und 999 sein!',
            'Buchungskreis bereits im Einsatz!'
        ),
        'codice' => 'ID number (code)',
        'ref_co' => 'Firmen Referenz für Bevölkerungsdaten',
        'clfoco' => 'Neues Rechnungswesen Flugzeug',
        'users' => 'Lassen Sie die Benutzer der Firma Referenzen',
        'clfoco_value' => array(0 => 'Keine (nicht empfohlen)',
            1 => 'Ja, aber ohne Kunden, suppliers and banks',
            2 => 'Ja, einschließlich Kunden, lieferanten und Banken '),
        'base_arch' => 'Auffüllen Base-Archiv',
        'base_arch_value' => array(0 => 'Keine (nicht empfohlen)',
            1 => 'Ja, aber ohne Träger und Verpackung',
            2 => 'Ja, einschließlich Träger und Verpackung'),
        'artico_catmer' => 'Duplicazione articoli di magazzino',
        'artico_catmer_value' => array(0 => 'No (default)',
            1 => 'Sì (normalmente sulle installazione didattiche)')
    ),
    "admin_pagame.php" =>
    array("Zahlungen Methode",
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
        array('C' => 'Simultaneous', 'D' => 'direkte Überweisung', 'B' => 'Bank recepit', 'T' => 'Wechsel', 'V' => 'RID'),
        array('S' => 'ja', 'N' => 'Nein'),
        array('D' => 'Rechnungsdatum', 'G' => 'Fest tag', 'F' => 'Ende des Monats'),
        array('Q' => 'vierzehntägig', 'M' => 'Monatlich', 'B' => 'zweimonatlich', 'T' => 'trimestrali', 'U' => 'vierteljährlich', 'S' => 'semestrali', 'A' => 'jährlich'),
        "Der Code gewählt wird bereits verwendet!",
        "Die Beschreibung ist leer!",
        "Der Code muss zwischen 1 und 99 werden",
        'ins_this' => 'Legen Sie eine neue Zahlungsmethode',
        'fae_mode' => "PA electronic invoice mode"
    ),
    "report_aliiva.php" =>
    array('title' => "MWST. rates",
        'ins_this' => 'Legen Sie eine neue Mehrwertsteuersatz',
        'codice' => "Code",
        'descri' => "Beschreibung",
        'type' => "Typ",
        'aliquo' => "Rate",
        'fae_natura' => "Nature - PA electronic invoice",
        'taxstamp' => 'Subject to stamp duty',
        'yn_value' => array(1 => 'Yes', 0 => 'No')
    ),
    "admin_aliiva.php" =>
    array("MwSt-Satz",
        "Code",
        "Beschreibung",
        "%Satz",
        "Note",
        "Die gewählten Code bereits verwendet wurde!",
        "Der Code muss zwischen 1 und 99 werden",
        "Die Beschreibung ist leer!",
        "% Rate ungültig!",
        "Typ MwSt.",
        "Select the nature of the exemption / exclusion!",
        'taxstamp' => 'Subject to stamp duty',
        'yn_value' => array(1 => 'Yes', 0 => 'No'),
        'fae_natura' => "Nature - PA electronic invoice"
    ),
    "admin_banapp.php" =>
    array('title' => 'Bank-Management-Unterstützung',
        'ins_this' => 'Legen Sie eine neue Bank zu unterstützen',
        'upd_this' => 'Update Bankenpaket',
        'errors' => array('Invalid code (min=1 max=99)!',
            'Der Code gewählt wird bereits verwendet!',
            'Geben Beschreibung!',
            'Ungültige ABI-Code!',
            'Ungültige CAB-Code!'
        ),
        'codice' => "Code ",
        'descri' => "Beschreibung ",
        'codabi' => "ABI Code",
        'codcab' => "CAB Code ",
        'locali' => "City",
        'codpro' => "State",
        'annota' => "Note",
        'report' => 'Bericht des Banken-Unterstützung',
        'del_this' => ' Bank-Unterstützung'
    ),
    "admin_imball.php" =>
    array('title' => 'Paketverwaltung',
        'ins_this' => 'Legen Sie eine neue Paket-Typ',
        'upd_this' => 'Update-Paket',
        'errors' => array('Invalid code (min=1 max=99)!',
            'Der Code gewählt wird bereits verwendet!',
            'Geben Beschreibung!',
            'Das Gewicht darf nicht negativ sein!'
        ),
        'codice' => "Code ",
        'descri' => " Beschreibung",
        'weight' => "Gewicht",
        'annota' => "Note",
        'report' => 'Liste der Pakete',
        'del_this' => 'Paket'
    ),
    "admin_portos.php" =>
    array('title' => 'Rendered ports management',
        'ins_this' => 'Legen Sie neue gerendert Hafen',
        'upd_this' => 'Update gemacht Hafen',
        'errors' => array('Invalid code (min=1 max=99)!',
            'The code chosen is already been used!',
            'Enter description!'
        ),
        'codice' => "Code ",
        'descri' => "Description ",
        'annota' => "Note",
        'incoterms' => 'Incoterms-standard ICC',
        'report' => 'List of the ports',
        'del_this' => 'port'
    ),
    "admin_spediz.php" =>
    array('title' => 'Delivery management',
        'ins_this' => 'Insert new delivery',
        'upd_this' => 'Update delivery',
        'errors' => array('Invalid code (min=1 max=99)!',
            'Der Code gewählt wird bereits verwendet!',
            'Geben Beschreibung!'
        ),
        'codice' => "Code ",
        'descri' => "Beschereibung ",
        'annota' => "Note",
        'report' => 'Liste der Lieferungen',
        'del_this' => 'Lieferung'
    ),
    "report_banche.php" =>
    array('title' => "Bank-Konten",
        'ins_this' => 'Legen Sie eine neue Bankverbindung',
        'msg' => array('Bestehendes Bankkonto NUR EIN PLAN Volkswirtschaftlicher Gesamtrechnungen "," Profil und / oder Ausdrucken der Geschäftsbücher'),
        'codice' => "Code",
        'ragso1' => "Name",
        'iban' => "IBAN code",
        'citspe' => "City",
        'prospe' => "Provinz",
        'telefo' => "Telephone"
    ),
    "admin_bank_account.php" =>
    array(" Bankkonto",
        "Code-Nummer (von acconting Plan) ",
        "Beschreibung ",
        "Bank Kredit (anstelle der Beschreibung wählen)",
        "Addresse ",
        "Postleitzahl",
        "City - County code ",
        "Nation ",
        "IBAN code ",
        'sia_code' => 'SIA Code',
        'eof' => 'File RiBA record with end of line characters',
        'eof_value' => array('S' => 'Yes', 'N' => 'No'),
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
    array('title' => 'Shipper admin',
        'ins_this' => 'Legen Sie neue Verlader',
        'upd_this' => 'Update Verlader n.',
        'errors' => array('Firmenname nicht vorhanden!',
            'Adresse fehlt!',
            'City fehlt!',
            'PLZ angeben!',
            'MWst-Code ist falsch',
            'USt-Id-Nummer ist falsch',
            'Umsatzsteuer-Identifikationsnummer fehlt!'
        ),
        'codice' => "Code",
        'ragione_sociale' => 'Firmenname',
        'indirizzo' => 'Addresse',
        'cap' => 'Postleitzahl',
        'citta' => 'City',
        'provincia' => 'Provinz',
        'partita_iva' => 'Umsatzsteuer-Identifikationsnummer',
        'codice_fiscale' => 'MWst code',
        'n_albo' => 'Absender Registriernummer',
        'telefo' => 'Telephone',
        'descri' => 'Andere Bezeichnung',
        'annota' => 'Note',
        'report' => 'Liste der Verlader',
        'del_this' => 'Absender'
    ),
    "admin_utente.php" =>
    array('title' => 'Verwaltung der Benutzer',
        'ins_this' => 'Legen Sie neue Benutzer',
        'upd_this' => 'Update Benutzer',
        'err' => array(
            'exlogin' => 'Nickname bereits vorhanden!',
            'user_lastname' => 'Geben Sie Name!',
            "user_name" => "Geben Sie Nickname!",
            'Password' => "Enter Password!",
            'passlen' => "Das Passwort ist nicht lang genug!",
            'confpass' => "Das Passwort ist verschieden von der Bestätigung!",
            'upabilit' => "Sie können nicht erhöhen Ihre Befähigung der Operation ist für den Administrator vorbehalten!",
            'filmim' => "Die Datei muss im JPG-Format sein",
            'filsiz' => "Das Bild darf nicht größer als 64Kb sein",
            'Abilit' => "Man kann nicht mit einem geringeren als 9, weil Sie die letzte Administrator sind!"
        ),
        "user_name" => "Nickname",
        'user_lastname' => "Familienname",
        'user_firstname' => "Name",
        'image' => 'Image <br /> (nur JPG-Format, max 64kb)',
        'Abilit' => "Ebene",
        'Access' => "Access-Nummer",
        'pre_pass' => 'Passwort (min.',
        'post_pass' => 'Zeichen)',
        'rep_pass' => 'Kennwort wiederholen',
        'lang' => 'Sprache',
        'theme'=>'Interface engine.<br>It will be active from next login',
        'style' => 'Theme / style',
        'mod_perm' => 'Module ermöglichen',
        'report' => 'Benutzer list',
        'del_this' => 'Benutzer',
        'del_err' => 'Sie können nicht löschen, weil Sie \ der einzige mit Administrator-Rechten!',
        'body_text'=> 'Testo contenuto nelle email che invierai'
    )
);
?>