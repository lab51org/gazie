<?php

/*
  --------------------------------------------------------------------------
  GAzie - MODULO 'VACATION RENTAL'
  Copyright (C) 2022-2023 - Antonio Germani, Massignano (AP) - telefono +39 340 50 11 912
  (http://www.programmisitiweb.lacasettabio.it)

  --------------------------------------------------------------------------
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2022 - Antonio De Vincentiis Montesilvano (PE)
  (http://www.devincentiis.it)
  <http://gazie.devincentiis.it>
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

//*+- Nuovo stringa per colonna Fornitore in array 'Lista delle merci e dei servizi' - DC - 02 feb 2018

$strScript = array(
    "browse_document.php" =>
      array('title' => "Lista dei Documenti/Certificati",
        'errors' => array('Il formato del file non è stato accettato!',
        'Il file è troppo grande!',
        'Il file è vuoto!',
        'Nessun file selezionato'),
        'ins_this' => "Inserisci un Documento e/o Certificato",
        'upd_this' => "Modifica Documento e/o Certificato",
        'ins_this_img' => "Inserisci un'immagine",
        'upd_this_img' => "Modifica immagine",
        'item' => "Articolo di riferimento",
        'table_name_ref' => "Tabella di riferimento",
        'note' => "Didascalia/Appunti/Note",
        'ext' => "Estensione",
        'select' => "Sel.",
        'code' => "Codice"),
    "report_facility.php" =>
      array('title' => 'Lista deglle strutture ricettive',
        'codice' => "Codice",
        'descri' => "Descrizione",
        'ricerca' => "Ricerca",
        'good_or_service' => "Merce-Servizio",
        'good_or_service_value' => array(0=>'Merce',1=>'Servizio',2=>'Composizione'),
        'unimis' => "U.M.",
        'catmer' => "Cat. merc.",
        'preacq' => 'Prezzo acquisto',
        'preve1' => 'Prezzo vend.1',
        'stock' => 'Giacenza',
        'aliiva' => 'IVA',
        'retention_tax' => 'Ritenuta',
        'payroll_tax' => 'Cassa Prev.',
        'barcode' => 'Cod.Barre',
        'clone' => 'Duplica',
        'cosear'=>'aggiungi "%" e invia per ricerca, oppure seleziona',
        'clfoco'=>'Fornitore',
        'lot'=>'Lotto',
      ),
	 "report_extra.php" =>
      array('title' => 'Lista degli extra',
        'codice' => "Codice",
        'descri' => "Descrizione",
        'ricerca' => "Ricerca",
        'good_or_service' => "Merce-Servizio",
        'good_or_service_value' => array(0=>'Merce',1=>'Servizio',2=>'Composizione'),
        'unimis' => "U.M.",
        'catmer' => "Cat. merc.",
        'preacq' => 'Prezzo acquisto',
        'preve1' => 'Prezzo vend.1',
        'stock' => 'Giacenza',
        'aliiva' => 'IVA',
        'retention_tax' => 'Ritenuta',
        'payroll_tax' => 'Cassa Prev.',
        'barcode' => 'Cod.Barre',
        'clone' => 'Duplica',
        'cosear'=>'aggiungi "%" e invia per ricerca, oppure seleziona',
        'clfoco'=>'Fornitore',
        'lot'=>'Lotto',
      ),
    "report_accommodation.php" =>
      array('title' => 'Lista degli alloggi',
        'codice' => "Codice",
        'descri' => "Descrizione",
        'ricerca' => "Ricerca",
        'good_or_service' => "Merce-Servizio",
        'good_or_service_value' => array(0=>'Merce',1=>'Servizio',2=>'Composizione'),
        'unimis' => "U.M.",
        'catmer' => "Cat. merc.",
        'preacq' => 'Prezzo acquisto',
        'preve1' => 'Prezzo vend.1',
        'stock' => 'Giacenza',
        'aliiva' => 'IVA',
        'retention_tax' => 'Ritenuta',
        'payroll_tax' => 'Cassa Prev.',
        'barcode' => 'Cod.Barre',
        'clone' => 'Duplica',
        'cosear'=>'aggiungi "%" e invia per ricerca, oppure seleziona',
        'clfoco'=>'Fornitore',
        'lot'=>'Lotto',
      ),
    "report_booking.php" =>
      array("Elenco delle prenotazioni ",
        "codice",
		'number' => 'Nr. prenotazione',
		'weekday_repeat' => 'settimanale',
		'status' => 'Stato',
		'print' => 'settimanale',
		'duplicate' => 'Duplica',
		'delete' => 'Cancella',
		'cancel' => 'Elimina',
		'submit' => 'Inserisci',
		'date' => 'data emissione',
		'title_value' => array('VPR' => 'Preventivi a clienti',
            'VOR' => 'Elenco prenotazioni',
            'VOW' => 'Elenco prenotazioni dal web',
            'VOG' => 'Prenotazioni settimanali del giorno'),
		'type' => 'Tipo',
        'type_value' => array('VPR' => 'Preventivo',
            'VOR' => 'Prenotazione',
            'VOW' => 'Prenotazione web',
            'VOG' => 'Prenotazione settimanale del giorno'),
		'mail_alert0' => 'Invio documento con email',
        'mail_alert1' => 'Hai scelto di inviare una e-mail all\'indirizzo: ',
        'mail_alert2' => 'con allegato il seguente documento di vendita:',
		'search' => 'Ricerca'
       ),

    "report_discount.php" =>
      array('title' => 'Lista degli sconti',
        'codice' => "Codice",
        'descri' => "Descrizione",

      ),


	 "settings.php" =>
      array("impostazioni ",
	  'title' => 'Impostazioni del modulo Vacation Rental di Antonio Germani'

      ),
    "admin_booking.php" =>
    array(array("VPR" => "Nuova prenotazione", "VOR" => "Prenotazione diretta", "VOW" => "Prenotazione dal Web", "VOG" => "Ordine settimanale del giorno"),
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 2 caratteri!',
            'Cambia cliente'),
        'title' => 'Prenotazioni',
        " Prenotazione ",
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
        " vendita ",
        "Vettore",
        "Alloggio",
        "Quantit&agrave;",
        "Tipo",
        "Ricavo",
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
        "Tot.Corpo",
        "Peso",
        "Totale",
        "Le date del check-in e/o check-out non sono corrette!",
        "La data del check-out non può essere precedente al check-in",
        "Non ci sono righi per poter emettere il documento!",
        "Stai tentando di modificare il DdT con una data antecedente a quella del DdT con numero precedente!",
        "Stai tentando di modificare il DdT con una data successiva a quella del DdT con numero successivo!",
        "Stai tentando di modificare il documento con una data antecedente a quello dello stesso tipo di documento con numero precedente!",
        "Stai tentando di modificare il documento con una data successiva a quello dello stesso tipo di documento con numero successivo!",
        "La data di emissione non pu&ograve; essere antecedente a quella dell'ultimo DdT emesso!",
        "La data di emissione non pu&ograve; essere antecedente a quella dell'ultimo documento dello stesso tipo emesso!",
        "La data di emissione non &egrave; corretta!",
        "Non hai selezionato il cliente!",
        "Non hai selezionato la modalit&agrave; di pagamento!",
        "Un rigo &egrave; senza la descrizione!",
        "Un rigo &egrave; senza l'unit&agrave; di misura!",
        "Causale mag.",
        "Peso netto",
        "Peso lordo",
        "N.Colli",
        "Volume",
        "Provvigioni",
        "Vuoi modificare un D.d.T. gi&agrave; fatturato!",
        "Questo documento &egrave; gi&agrave; stato contabilizzato!",
        "E stata superata la capienza massima di persone ammesse per l'alloggio",
        "Il numero di adulti è superiore a quello ammesso per l'alloggio",
        "Il numero di minori è superiore a quello ammesso per l'alloggio",
        "Prima di inserire un alloggio è necessario inserire le date di check-in e check-out",
        "ATTENZIONE OVERBOOKING! Il periodo inserito risulta occupato",
        "ATTENZIONE OVERBOOKING! Non c'è sufficiente disponibilità di questo extra",
        "ATTENZIONE Il numero di notti sono inferiori al soggiorno minimo delle impostazioni generali",
        "ATTENZIONE il numero di notti supera il massimo consentito",
        'speban' => "Spese incasso",
        'speban_title' => 'Spese incasso non documentate/rate',
        'traspo_title' => 'Spese trasporto non documentate',
        'stamp' => 'Bolli',
        'ritenuta' => 'Ritenuta ',
        'netpay' => 'Netto a pagare',
        'id_agente' => "Agente di vendita",
        'print_total' => "Stampa totale",
        'print_total_value' => array(0 => 'No', 1 => 'Si'),
        'delivery_time' => "Tempi di consegna",
        'day_of_validity' => "Giorni di validità ",
        'vat_constrain' => '(forza) IVA al ',
        'taxstamp' => 'Bollo',
        'virtual_taxstamp' => 'Modo',
        'virtual_taxstamp_value' => array(0 => 'No', 1 => 'Materiale', 2 => 'Virtuale'),
        'typerow_booking' => array(0 => 'Alloggio', 2 => 'Rigo descrittivo'),
        'weight' => 'peso',
        'zero_rows' => 'Il documento non contiene righi di alloggi, compila la ricerca alloggio nella sezione inserimento alloggio, inserisci il valore % per avere una lista completa o per effettuare una ricerca parziale',
        'discount_alert' => 'sconto da anagrafe articoli',
        'last_row' => 'Ultimo rigo inserito',
        'Deposito cauzionale da versare in contanti al check-in: EURO ',
        'In assenza di danni il deposito sarà restituito al check-out',
        'Proprietario',
        'access1' => '<p>Le inviamo, in allegato, una copia della sua prenotazione</p><p>Per accedere alla prenotazione online e controllarne stato e pagamento usi questo link',
        'access2' => 'e i seguenti codici di accesso',
        'booking_number' => 'Numero prenotazione',
        'best_regards' => 'Grazie, cordiali saluti',
		'on_behalf' => 'Locazione effettuata per conto del '
    ),
    "admin_house.php" =>
      array('title' => 'Gestione degli alloggi',
        'ins_this' => 'Inserimento alloggio',
        'upd_this' => 'Modifica l\'alloggio',
        'err' => array(
            'codice' => 'Il codice alloggio &egrave; gi&agrave; esistente',
            'movmag' => 'Si st&agrave; tentando di modificare il codice alloggio con dei movimenti di magazzino associati',
            'filmim' => 'Il file dev\'essere nel formato PNG, JPG, GIF',
            'filsiz' => 'L\'immagine non dev\'essere pi&ugrave; grande di 64Kb',
            'valcod' => 'Inserire un codice valido',
            'descri' => 'Inserire una descrizione',
            'unimis' => 'Inserire l\'unit&agrave; di misura delle vendite',
            'aliiva' => 'Inserire l\'aliquota I.V.A.',
            'lotmag' => 'Per avere la tracciabilità per lotti è necessario attivare la contabilità di magazzino in configurazione azienda',
            'no_ins' => 'Non sono riuscito ad inserire l\'alloggio sul database',
            'char' => 'Sul codice alloggio ho sostituito i caratteri speciali non consentiti con "_" ',
            'codart_len' => 'Il codice alloggio ha una lunghezza diversa da quella stabilita in configurazione avanzata azienda ',
            'no_web' => 'Per attivare l\'alloggio nell\'e-commerce è necessario che sia inserito il riferimento ID e-commerce nella scheda magazzino'
        ),
        'war' => array(
            'ok_ins' => 'Alloggio inserito con successo'
        ),
        'codice' => "Codice",
        'descri' => "Descrizione",
        'accommodation_type' => "Tipologia di alloggio",
        'accommodation_type_value' => array(3 => 'Appartamento', 4 => 'Casa vacanze', 5=> 'Bed & breakfast', 6=> 'Camera'),
        'body_text' => "Testo descrittivo",
        'lot_or_serial' => 'Lotti o numeri seriali',
        'lot_or_serial_value' => array(0 => 'No', 1 => 'Lotti', 2 => 'Seriale/Matricola'),
        'barcode' => "Codice a Barre EAN13",
        'image' => "Immagine (jpg,png,gif) max 64Kb",
        'unimis' => "Unit&agrave; di misura vendite",
        'quality' => "Qualità",
        'larghezza' => "Larghezza (mm)",
        'lunghezza' => "Lunghezza (mm)",
        'spessore' => "Spessore (mm)",
        'bending_moment'=>"Resistenza es.Wx cm³",
        'catmer' => "Categoria",
        'ragstat' => "Raggruppamento statistico",
        'preacq' => array( 0=>'Costo di produzione', 1=>'Prezzo d\'acquisto', 2=>'Costo di produzione' ),
        'preve1' => 'Prezzo di vendita listino 1',
        'preve2' => 'Prezzo di vendita listino 2',
        'preve3' => 'Prezzo di vendita listino 3',
        'preve4' => 'Prezzo di vendita listino 4',
        'preve1_sc' => 'Prezzo scontato 1',
        'preve2_sc' => 'Prezzo scontato 2',
        'preve3_sc' => 'Prezzo scontato 3',
        'preve4_sc' => 'Prezzo scontato 4',
        'sconto' => 'Sconto',
        'aliiva' => 'Aliquota IVA',
        'retention_tax' => 'Applica la ritenuta d\'acconto',
        'retention_tax_value' => array(0 => 'No', 1 => 'Si'),
        'payroll_tax' => 'Genera rigo Cassa Previdenziale',
        'payroll_tax_value' => array(0 => 'No', 1 => 'Si'),
        'esiste' => 'Esistenza attuale',
        'valore' => 'Valore dell\'esistente',
        'last_cost' => 'Costo dell\'ultimo acquisto',
        'scorta' => 'Scorta minima',
        'riordino' => 'Lotto acquisto',
        'uniacq' => 'Unit&agrave; di misura acquisti',
        'classif_amb' => 'Classificazione ambientale',
        'classif_amb_value' => array(0=>'non classificato',1=>'irritante',2=>'nocivo',3=>'tossico',4=>'molto tossico'),
        'maintenance_period' => 'Periodicità manutenzione (gg)',
        'peso_specifico' => 'Peso specifico (kg/l) o Moltiplicatore',
        'volume_specifico' => 'Superficie m2',
        'pack_units' => 'Pezzi in imballo',
        'codcon' => 'Conto di ricavo su vendite',
        'id_cost' => 'Conto di costo su acquisti',
        'annota' => 'Indirizzo e/o dati catastali (da riportare nel contratto di locazione)',
        'fornitori-codici' => 'Codici Fornitori',
        'document' => 'Documenti e/o certificazioni',
        'imageweb' => 'immagini e foto',
        'web_mu' => 'Unit&agrave; di misura online',
        'web_price' => 'Prezzo base a notte',
        'web_multiplier' => 'Moltiplicatore prezzo web',
        'web_public' => 'Sincronizza sul sito web',
        'web_public_value' => array(0 => 'No', 1 => 'Si', 2 => 'Attivo e prestabilito', 3 => 'Attivo e pubblicato in home', 4 => 'Attivo, in home e prestabilito', 5 => 'Disattivato su web'),
        'ordinabile_value' => array( '' => '----','S' => 'Ordinare','N'=> 'Non ordinare'),
        'movimentabile' => 'Articolo Movimentabile',
        'movimentabile_value' => array ( '' => '----','S' => 'Si','N'=>'No','E' => 'Esaurito'),
        'codice_fornitore' => 'Codice del produttore',
        'utilizzato' => 'Utilizzato',
        'depli_public' => 'Pubblica sul catalogo',
        'depli_public_value' => array(0 => 'No', 1 => 'Si'),
        'web_url' => 'Web url<br />(es: http://site.com/item.html)',
        'modal_ok_insert' => 'Casa inserita con successo clicca sulla X in alto a destra per uscire oppure...',
        'iterate_invitation' => 'INSERISCI UN\'ALTRA CASA',
        'browse_for_file' => 'Sfoglia',
        'id_anagra' => 'Fornitore di riferimento',
        'last_buys' => 'Ultimi acquisti da fornitori',
        'ordinabile' => 'Articolo ordinabile',
        'durability_mu' => 'Unità di misura durabilità',
        'durability' => 'Valore durabilità',
        'warranty_days' => 'Giorni di garanzia',
        'id_agent' => 'Proprietario/responsabile',
        'unita_durability' => array('' => '', '>' => '>', '<' => '<', 'H' => 'H', 'D' => 'D', 'M' => 'M'),
		'tur_tax' => 'Tassa turistica',
		'tur_tax_mode' => 'Modalità Tassa turistica',
		'deposit_type_value' => array(0 => 'a valore', '1' => 'a percentuale sul totale'),
		'tur_tax_value' => array(0 => 'a persona', '1' => 'a persona escluso i minori', '2' => 'a notte e a persona', '3' => 'a notte escluso i minori', '4' => 'a soggiorno'),
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 1 carattere!',
            'Cambia fornitore'
        )
    ),
	"admin_facility.php" =>
    array('title' => 'Gestione delle strutture turistiche',
        'ins_this' => 'Inserimento struttura turistica',
        'upd_this' => 'Modifica la struttura turistica',
        'err' => array(
            'codice' => 'Il codice alloggio &egrave; gi&agrave; esistente',
            'empty_var' => 'Deve esserci per forza almeno un\'alloggio inserito nella struttura',
            'grcod' => 'Questo alloggio appartiene già ad un\'altra struttura',
            'descri' => 'Inserire una descrizione',
            'no_ins' => 'Non sono riuscito ad inserire l\'aalloggio sul database',
            'char' => 'Sul codice alloggio ho sostituito i caratteri speciali non consentiti con "_" ',
            'codart_len' => 'Il codice alloggio ha una lunghezza diversa da quella stabilita in configurazione avanzata azienda per gli articoli ',
            'filmim' => 'Il file dev\'essere nel formato png, x-png, jpg, gif, x-gif, jpeg',
            'filetoobig' => 'Controllare che il file selezionato non superi le dimensioni impostate nella configurazione php',
            'issue_lat' => 'Errore nella latitudine',
            'issue_long' => 'Errore nella longitudine'
        ),
        'war' => array(
            'ok_ins' => 'Alloggio inserito con successo'
		),
		'info' => 'La struttura turistica è qui intesa come un insieme di alloggi presenti nello stesso stabile. <br/>Un esempio classico è un hotel che ha le camere nello stesso edificio; in questo caso l\'hotel è la struttura e le camere sono gli alloggi.',
		'home' => "ID struttura",
		'variant' => "Alloggi",
		'codice' => "ID struttura",
        'descri' => "Descrizione",
		'image' => 'immagine',
        'web_public' => 'Sincronizza sul sito web',
        'web_public_value' => array(0 => 'No', 1 => 'Si', 2 => 'Attivo e prestabilito', 3 => 'Attivo e pubblicato in home', 4 => 'Attivo, in home e prestabilito', 5 => 'Disattivato su web'),
		'body_text' => 'Descrizione estesa',
        'depli_public' => 'Pubblica sul catalogo',
        'depli_public_value' => array(0 => 'No', 1 => 'Si'),
        'web_url' => 'Web url<br />(es: http://site.com/item.html)',
        'modal_ok_insert' => 'Alloggio inserito con successo clicca sulla X in alto a destra per uscire oppure...',
        'mesg' => array(
        )
    ),
	"banner_search.php" =>
    array('title' => 'Iframe ricerca generale',
        'err' => array(
			'date_uguali'=> 'Le date di check-in e check-out sono uguali',
			'start>end'=> 'il check-in è posteriore al check-out'
        ),
        'war' => array(
            'ok' => 'ok riempitura array'
		),
		'guest'=>'Ospiti',
		'look'=>'Guarda',
		'price'=>'Prezzo',
		'search'=>"Cerca",
		'open_calendar'=>"Apri il calendario generale",
		'imposta'=>"Per disponibilità, prezzo o prenotazione impostare le date",
		'risultato'=>"Disponibilità per ",
		'book'=>"Prenota",
		'nessun_risultato'=>"Per il periodo selezionato non ci sono disponibilità. Provare a modificare le date o a visionare il calendario generale.",
		'for_night' => 'a notte',
		'nights' => 'notti',
		'over_guest' => 'Attenzione: questo alloggio è disponibile ma ha una capienza massima di',
		'persons' => 'persone',
		'avviso' => 'Il soggiorno minimo è di',
		'avviso2' => 'Ci sono sconti crescenti per 12, 21 e 28 notti.',
    'over_max_booking' => 'Alcuni alloggi hanno il limite massimo di ',
    'over_max_booking2' => 'Prova a ridurre le notti nella richiesta.',
    'no_search_house' => 'L\'alloggio richiesto non è disponibile, guarda la disponibilità nel suo calendario. Oppure ci sono queste alternative:'
    ),
	"booking_form.php" =>
    array('title' => 'Modulo di prenotazione',
        'err' => array(
			'date_uguali'=> 'Le date di check-in e check-out sono uguali',
			'start>end'=> 'il check-in è posteriore al check-out',
			'no_payment'=> 'Non è stato selezionato il tipo di pagamento',
			'no_ccinfo'=> 'I dati della carta di credito sono errati o mancanti',
			'email-error' => 'I due indirizzi e-mail non coincidono',
			'email-validate' => 'L\'indirizzo email non è scritto correttamente',
      'inexistent_booking' => 'La prenotazione NON esiste',
      'wrong_data' => 'I dati inseriti non sono corretti',
      'too_many_error' => 'Accesso BLOCCATO per troppi errori. Riprovare più tardi',
      'text_missing' => 'Il testo della recensione è mancante o troppo corto',
      'err_codfis' => 'Errore nel codice fiscale, correggere e riprovare',
      'coupon_error' => 'Codice sconto non applicato perché non valido',
        ),
        'war' => array(
            'ok' => 'ok riempitura array'
		),
		'guest'=>'Ospiti',
		'rules_label'=>'Leggere il contratto di locazione',
		'privacy_label'=>'Leggere il regolamento sulla privacy',
		'rules_button'=>'Contratto di locazione',
		'privacy_button'=>'Regolamento sulla privacy',
		'title'=>'Modulo di richiesta di prenotazione',
    'booking' => 'Prenotazione',
		'submit'=>'Vai avanti al riepilogo',
		'return'=>'Indietro',
		'tur_tax' => 'Tassa turistica',
		'submitpay'=>'Sottoscrivi la locazione e paga',
		'price_for' => 'Prezzo per ',
		'discount_to' => 'Sconto da applicare ',
		'select_guest' => 'Seleziona gli ospiti ',
		'max_capability' => 'Capacità massima ',
		'persons' => ' persone',
		'adults' => 'adulti',
		'children' => 'bambini fino ad anni ',
		'price' => 'prezzo',
		'each' => 'cadauno',
		'per_person' => 'a persona',
		'per_booking' => 'a prenotazione',
		'per_night' => 'a notte',
		'per_person_night' => 'per ogni notte a persona',
		'out_stock' => 'Esaurito per questo periodo',
		'quantity' => 'quantità',
		'per_stay' => 'a soggiorno',
		'per_night_adult' => 'a notte escluso i minori',
		'per_person_adult' => 'a persona escluso i minori',
		'required' => 'obbligatorio',
		'read_rules' => 'Ho letto e accetto il contratto di locazione',
		'read_privacy' => 'Accetto il trattamento dati sensibili',
		'price_sum' => 'Riepilogo prezzi',
		'booking_tot' => 'Totale della locazione',
		'which_deposit' => 'Importo da pagare per confermare la prenotazione (caparra confirmatoria)',
		'select_pay_deposit' => 'Seleziona il tipo di pagamento per la caparra',
    'select_pay_balance' => 'Seleziona il tipo di pagamento per il saldo',
		'bank_transfer' => 'Bonifico bancario',
		'credit_card_offline' => 'Carta di credito offline',
		'payment' => 'Pagamento',
		'transfer_instruction' => 'Effettuare il bonifico bancario alle seguenti coordinate entro 24 ore',
		'bank_beneficiary' => 'Beneficiario',
		'amount' => 'Importo',
		'bank_reason' => 'Causale: ',
		'unsecure_protocol' => '> ATTENZIONE la tua connessione non è sicura, sei in http <',
		'secure_protocol' => 'Sei in connessione sicura https',
		'amount_pay' => 'Importo da pagare',
		'nights' => 'notti',
		'name' => 'Nome',
		'surname' => 'Cognome',
		'address' => 'Indirizzo con numero civico',
		'city' => 'Città',
		'postalcode' => 'CAP',
		'provincia' => 'Provincia',
		'nation' => 'Nazione',
		'vatcode' => 'Codice fiscale',
		'phone' => 'Telefono',
		'email' => 'E-mail',
		'email2' => 'Per evitare errori, riscrivere l\'e-mail',
		'apartment' => 'Appartamento',
		'house' => 'Casa vacanze',
		'bandb' => 'Bed & breakfast',
		'for' => 'per',
		'and' => 'e',
		'card_holder' => 'Titolare della carta (nome e cognome)',
		'card_number' => 'Numero della carta',
		'cvv' => 'Numero CVV (Card Validation Value)',
		'expiry' => 'Scadenza',
		'email_confirm_body' => 'Salve,<br> Nel ringraziarla per aver scelto di trascorrere una vacanza nella nostra struttura, le inviamo, in allegato, una copia della sua richiesta di prenotazione. Il gestore è stato già avvisato e la prenotazione verrà confermata al più presto secondo quanto stabilito dalle norme della struttura. ',
		'email_confirm_subject' => 'Richiesta di prenotazione n.',
		'email_confirm_addbody' => '<p>Entro 24 ore, dovrà effettuare il pagamento della caparra confirmatoria così come segue:</p>',
    'email_confirm_addbody_warning' => '<p>Nota bene: il mancato pagamento o il mancato invio della ricevuta comporterà automaticamente la cancellazione della prenotazione.</p>',
    'email_confirm_addbody_receipt' => '<p>Effettuato il pagamento ne dovrà inviare la ricevuta rispondendo a questa e-mail.</p>',
		'regards' => 'Cordiali saluti',
		'of' => 'del',
    'discount_code' => 'Se hai un codice sconto, inseriscilo qui; lo vedrai applicato nel passaggio successivo.',
    'coupon_apply' => 'Buono sconto da applicare',
    'discount_apply' => 'Sconto da applicare',
    'coupon_error' => 'Codice sconto non applicato perché non valido',
    'paypal_payment' => 'Pagamento con PayPal',
    'paypal_cancelled' => 'La transazione di PayPal e la richiesta di prenotazione sono state annullate',
    'booking_cancell' => 'Cancellazione prenotazione',
    'confirm_cancell' => 'Conferma di avvenuta cancellazione della prenotazione.',
    'click_to_paypal' => 'Per procedere al pagamento su PayPal cliccare il pulsante',
    'user_access' => 'Accesso utente',
    'deposit' => 'Caparra confirmatoria locazione ',
    'balance' => 'Saldo rimanente',
    'total_amount' => 'Importo totale',
    'paypal_cancelled2' => 'Transazione con PayPal annullata',
    'stripe_payment' => 'Pagamento con carte di credito',
    'accommodation' => 'Alloggio',
    'pay_now' => 'Paga adesso',
    'payment_success' => 'Pagamento effettuato correttamente',
    'payment_failed' => 'Pagamento NON riuscito, controllare la carta di credito',
    'payment_info' => 'Dati della transazione',
    'back_to_site' => 'Chiudi e torna al sito web',
    'security_deposit_des2' => 'Oltre a quanto sopra, al check-in si dovrà versare in contanti un deposito cauzionale di',
    'security_deposit_des' => 'Al check-in si dovrà versare in contanti un deposito cauzionale di',
    'GENERATO' => 'GENERATO da approvare',
    'PENDING' => 'IN ATTESA',
    'CONFIRMED' => 'CONFERMATO e APPROVATO',
    'FROZEN' => 'CONGELATO',
    'ISSUE' => 'PROBLEMI da risolvere',
    'CANCELLED' => 'ANNULLATO',
    'no_extra' => 'Nessun extra acquistato',
    'change_status' => 'Lo stato della prenotazione è cambiato in',
    'booking_number' => 'Numero prenotazione',
    'deposit_return' => 'In assenza di danni, il deposito cauzionale sarà restituito al check-out',
    'ask_feedback' => 'Grazie per essere stato nostro ospite, speriamo che il soggiorno sia stato di suo gradimento.<br>La soddisfazione totale degli ospiti è il nostro obbiettivo. Una sua recensione sarebbe molto apprezzata, ci dedichi solo pochi minuti e lasci una recensione cliccando sul link sottostante, per favore.',
    'ask_feedback2' => 'Il suo giudizio è molto importante per noi. <br>Sperando di rivederla presto <br> Cordiali saluti',
    'use_access' => 'Per accedere usi i seguenti codici di accesso',
    'more_info' => 'Maggiori info',
    'booking_status' => 'Stato della prenotazione',
    'total_paid' => 'Totale pagato'
    ),

  "admin_extra.php" =>
    array('title' => 'Gestione degli extra',
		'ins_this' => 'Inserimento nuovo extra',
		'upd_this' => 'Aggiorna questo extra',
		'codice' => 'Codice extra',
		'descri' =>"Descrizione",
		'catmer' => 'Categoria merceologica',
		'body_text' => 'Descrizione estesa',
		'barcode' => 'Codice a barre',
		'image' => 'immagine base',
		'aliiva' => 'aliquota IVA',
		'web_url' => 'Url pagina web corrispondente',
		'web_public' => 'Pubblicazione sito web',
		'document' => 'Documenti e/o certificazioni',
		'imageweb' => 'immagini e foto HQ',
		'web_public_value' => array(0 => 'No', 1 => 'Si', 2 => 'Attivo e prestabilito', 3 => 'Attivo e pubblicato in home', 4 => 'Attivo, in home e prestabilito', 5 => 'Disattivato su web'),
		'web_price' => 'Prezzo anche su web',
		'mod_prezzo' => 'Modalità applicazione prezzo',
		'web_multiplier' => 'Moltiplicatore prezzo web',
		'web_mu' => 'Unità di misura',
		'retention_tax' => 'Applica la ritenuta d\'acconto',
        'retention_tax_value' => array(0 => 'No', 1 => 'Si'),
		'payroll_tax' => 'Genera rigo Cassa Previdenziale',
        'payroll_tax_value' => array(0 => 'No', 1 => 'Si'),
		'codcon' => 'Conto di ricavo su vendite',
        'id_cost' => 'Conto di costo su acquisti',
		'annota' => 'Annotazione (da riportare nel contratto di locazione)',
		'err' => array(
        'valcod' => 'Non hai inserito il codice dell\'extra',
        'descri' => 'Non hai scritto la descrizione',
        'char' => 'Il codice è stato modificato perché conteneva caratteri speciali non ammessi'
		),
		" Extra ",
		"Descrizione",
		"Prezzo",
		"Modalità prezzo",
		"Codice alloggio",
		"5 booo",
		"codice extra",
		"codice alloggio",
		'typerow_price' => array(0 => 'a prenotazione', 1 => 'a persona', 2 => 'a notte', 3 => 'a persona e a notte', 4 => 'cadauno'),
		'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 1 carattere!',
            'Cambia fornitore'
      ),
    'err' => array(
          'codice' => 'Il codice articolo &egrave; gi&agrave; esistente',
          'movmag' => 'Si st&agrave; tentando di modificare il codice ad un articolo con dei movimenti di magazzino associati',
          'filmim' => 'Il file dev\'essere nel formato PNG, JPG, GIF',
          'filsiz' => 'L\'immagine non dev\'essere pi&ugrave; grande di 64Kb',
          'valcod' => 'Inserire un codice valido',
          'descri' => 'Inserire una descrizione',
          'unimis' => 'Inserire l\'unit&agrave; di misura delle vendite',
          'aliiva' => 'Inserire l\'aliquota I.V.A.',
          'lotmag' => 'Per avere la tracciabilità per lotti è necessario attivare la contabilità di magazzino in configurazione azienda',
          'no_ins' => 'Non sono riuscito ad inserire l\'articolo sul database',
          'char' => 'Sul codice articolo ho sostituito i caratteri speciali non consentiti con "_" ',
          'codart_len' => 'Il codice articolo ha una lunghezza diversa da quella stabilita in configurazione avanzata azienda ',
          'no_web' => 'Per attivare l\'articolo nell\'e-commerce è necessario che sia inserito il riferimento ID e-commerce nella scheda magazzino'
      )
    ),
	"admin_discount.php" =>
    array('title' => 'Gestione degli sconti',
        'ins_this' => 'Inserimento nuovo sconto',
        'upd_this' => 'Modifica sconto',
        'sconto' => 'sconto',
        'title_dis' => 'Titolo',
        'descri' => 'Descrizione',
        'accommodation_code' => "Codice alloggio",
        'facility_id' => "ID struttura",
        'valid_from' => "Valido dal",
        'valid_to' => "Valido fino al",
        'value' => "Valore sconto",
        'discount_voucher_code' => 'Codice buono sconto',
        'is_percent' => 'E\' in percentuale',
        'is_percent_value' => array(0=>'NO',1=>'Sì'),
        'manca_descri' => "Inserire la descrizione",
        'valore_vuoto' => "Il valore non può essere vuoto",
        'buono_esiste' => "Esiste già un buono con lo stesso codice",
        'min_stay' => "Notti minime per avere lo sconto",
        'priority'=> "Priorità su altri sconti (0 nessuna; più alto è il numero maggiore è la priorità)",
        'stop_further_processing'=> "Questo sconto blocca tutti gli altri con minore priorità",
        'id_anagra'=> "ID utente a cui è riservato lo sconto",
        'reusable' => "Il buono sconto è utilizzabile più volte (0=infinite; numero=volte)"
    ),
    "lease.php" =>
    array('locatore' => 'Il locatore',
    'conduttore' => 'il conduttore',
    'parti' => 'Le parti',
    'e' => 'e ',
    'contratto_n' => 'Contratto di locazione turistica n.',
    'body1' => 'stipulano quanto segue: ',
    'oggetto' => 'Oggetto',
    'body2' => 'Per il solo ed esclusivo uso turistico abitativo, il locatore concede in locazione al conduttore che accetta: ',
    'body3' => 'Così come descritto nel sito internet alla pagina ',
    'body4' => 'Ogni altra cosa o servizio non contemplati nella suddetta pagina internet o nel presente contratto sono da considerarsi non compresi nel canone di locazione e quindi non forniti.',
    'divieti' => 'Divieti ed obblighi',
    'durata' => 'Durata e orari',
    'canone' => 'Canone e spese',
    'rinvio' => 'Rinvio',
    'rinvio1' =>'Per quanto non espressamente previsto dal presente contratto, le parti dichiarano applicabili le disposizioni del codice civile e le norme del codice del turismo, nonché gli usi locali.',
    'apartment' => 'Appartamento',
		'house' => 'Casa vacanze',
		'bandb' => 'Bed & breakfast',
    'body5' => 'La locazione è concessa per un numero massimo di ',
    'body6' => ' persone, di cui ',
    'body7' => ' adulti e ',
    'body8' => ' minori di anni ',
    'divieto1' => 'E\' vietato Il pernotto, anche occasionale, di un numero di persone superiore a quanto concordato.',
    'divieto2' => 'Il conduttore si impegna a concordare con il locatore gli orari previsti di arrivo e partenza con almeno 12 ore di anticipo. Il conduttore si impegna a comunicare al locatore, durante il viaggio, l\'eventuale aggiornamento dell\'orario di arrivo previsto.',
    'divieto3' => 'Con il ritiro delle chiavi, il conduttore accetta e si obbliga di diventare il custode dell\'alloggio per tutta la durata della locazione. Pertanto si assume l\'obbligo di custodirlo con la dovuta diligenza e di non arrecare danni all\'alloggio, al mobilio e agli elettrodomestici, di non apportare alcuna modifica alla disposizione dei mobili e degli oggetti e di non effettuare alcuna riparazione se non preventivamente autorizzata dal locatore.',
    'divieto7' => 'Eventuali guasti o rotture dovranno immediatamente essere comunicati al locatore. Qualora guasti o rotture siano derivati da un corretto e coscenzioso uso del bene essi saranno a carico del locatore altrimenti verranno addebitati al conduttore. I tempi tecnici per la riparazione e/o ripristino saranno determinati di volta in volta da un tecnico competente che verrà incaricato dal locatore. In attesa della riparazione, per il mancato uso del bene guasto il conduttore dichiara sin da ora di rinunciare ad ogni tipo di risarcimento.',
    'divieto4' => 'E\' severamente vietato alloggiare animali di qualsiasi specie e taglia.',
    'divieto5' => 'Il locatore consegnerà l\'alloggio in buono stato di pulizia e manutenzione. Lo stesso dovrà essere restituito nel medesimo stato. A tal fine il conduttore accetta che le pulizie durante il soggiorno, così come quelle finali alla partenza, saranno a sua cura e carico.',
    'tour_tax' => 'dell\'imposta di soggiorno turistica,',
    'durata1' => 'Il presente contratto è stipulato a tempo determinato, per la durata massima di notti ',
    'durata2' => 'L\'arrivo (check-in, consegna) è stabilito e accettato dal conduttore nel giorno',
    'durata2bis' => 'e nella fascia oraria',
    'durata3' => 'La partenza (check-out, restituzione) è stabilita e accettata dal conduttore nel giorno',
    'durata4' => ' In tale momento la presente locazione cesserà senza bisogno di disdetta alcuna, che si intende data ora per allora, con obbligo del conduttore di restituire l\'alloggio al locatore.',
    'durata5' => 'Non sono ammessi arrivi o partenze fuori dalla fascia oraria.',
    'canone1' => 'Il canone della presente locazione, convenuto ed accettato da entrambe le parti, è di',
    'canone2bis' => ' comprensivo ',
    'canone2' => ' delle spese per i consumi di luce, acqua e gas. Il conduttore accetta e si obbliga a corrispondere il saldo della locazione contestualmente al ritiro delle chiavi il giorno di arrivo.',
    'canone3' => 'A titolo di garanzia, al check-in il conduttore accetta di dover versare in contanti un deposito cauzionale infruttifero di',
    'canone4' => 'Accertata l\'assenza di danni e l\'avvenuta pulizia finale, tale deposito sarà restituito al check-out. In caso contrario la somma sarà trattenuta dal locatore a titolo di rimborso fermo il diritto di richiedere il maggior danno anche per un eventuale mancato utilizzo dell\'alloggio.',
    'recesso' => 'Recesso',
    'recesso1' => 'Il conduttore può recedere dal presente contratto almeno con 60 giorni di anticipo dalla data di arrivo; in tale caso il locatore rimborserà la caparra confirmatoria trattenendo € 50,00.',
    'recesso2' => 'Il conduttore può recedere dal presente contratto fra 59 e 31 giorni di anticipo dalla data di arrivo; in tale caso il locatore rimborserà solo la metà della caparra confirmatoria trattenendo la restante metà.',
    'recesso3' => 'Il conduttore può recedere dal presente contratto nei 30 giorni che precedono la data di arrivo; in tale caso il locatore tratterrà tutta la caparra confirmatoria.',
    'recesso4' => 'Il locatore può recedere dal presente contratto con almeno 30 giorni di anticipo dalla data di arrivo; in tale caso restituirà al locatore la caparra confirmatoria.',
    'recesso5' => 'Il locatore può recedere dal presente contratto nei 29 giorni che precedono la data di arrivo. In tale caso restituirà la caparra confirmatoria ricevuta oltre ad un ulteriore importo di pari importo a titolo di indennizzo e risarcimento.',
    'recesso6' => 'Effettuato il check-in, il conduttore potrà terminare anticipatamente la locazione ma non avrà diritto ad alcun rimborso per le notti non utilizzate.',
    'recesso7' => 'Qualora a causa di eventi straordinari (a solo titolo di esempio: incendio, terremoto, allagamento etc) il conduttore sarà costretto a terminare anticipatamente la locazione, il locatore restituirà l\'importo delle notti non godute. Nessun altro danno potrà essere richiesto al locatore.',
    'recesso8' => 'Il mancato rispetto, da parte del conduttore, anche di un solo punto del presente contratto così come la mancata presentazione del conduttore, in mancanza di disdetta, alla data di arrivo e nella fascia oraria stabilita comporterà la risoluzione espressa ipso iure del presente contratto ai sensi dell\'art. 1456 del C.C. e l\'obbligo del pagamento del saldo della locazione.',
    'accettazione' => 'Accettazione',
    'accettazione1' => 'Il presente contratto è stato letto accettato e sottoscritto on-line con la spunta sull\'apposita casella. Sarà pertanto valido a tutti gli effetti di legge anche in mancanza della firma fisica.',
    'accettazione2' => 'Per ogni controversia in ordine all\'esecuzione del presente contratto viene tra le parti convenuta ed accettata la competenza esclusiva del foro di Fermo.',
    'sign-online' => 'Firmato digitalmente on-line',
    'sign' => 'Firmato'
    ),
    "review_viewer.php" =>
    array('title' => 'Visualizzatore di recensioni',
        'err' => array(
			'date_uguali'=> 'Le date di check-in e check-out sono uguali'
        ),
        'war' => array(
          'ok' => 'ok riempitura array'
		),
		'guest'=>'Ospiti',
    'opinion' => 'Valutazioni alloggio',
    'average' => 'Valutazione media generale',
    'guest_reviews' => 'Recensioni degli ospiti',
    'no_review' => 'Non ci sono ancora recensioni'
    )
);
?>
