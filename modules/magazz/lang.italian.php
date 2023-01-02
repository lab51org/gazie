<?php

/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2023 - Antonio De Vincentiis Montesilvano (PE)
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
    "situaz_magazz.php" =>
      array('title' => 'Situazione magazzino',
      ),
    "report_artico.php" =>
      array('title' => 'Lista delle merci e dei servizi',
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
    "report_movmag.php" =>
      array("movimenti di magazzino ",
        "codice",
        "Inserisci ",
        "Lista dei ",
        "Data reg.",
        "Articolo",
        "Quantit&agrave;",
        "Importo",
        "Documento",
        " del ",
        "Genera movimenti da documenti",
        "Lotto"),
    "admin_movmag.php" =>
      array("movimento di magazzino ",
        "Data della registrazione ",
        "Causale ",
        "Cliente",
        "Fornitore",
        "C",
        "F",
        "Articolo",
        "Data documento ",
        "Descrizione doc.",
        "Sconto chiusura ",
        "Unit&agrave; di misura ",
        "Quantit&agrave; ",
        "Prezzo",
        "Sconto su rigo",
        "La data del documento non &egrave; corretta!",
        "La data di registrazione non &egrave; corretta!",
        "La data del documento &egrave; successiva a quella di registazione!",
        "Non &egrave; stato selezionato l'articolo!",
        "La quantit&agrave; non pu&ograve; essere uguale a zero!",
        "Questo movimento può essere modificato esclusivamente dal modulo Registro di campagna!",
        "Articolo con lotto",
        "Gli articoli composti possono essere caricati sono con una produzione!",
        "Si può caricare olio lampante da recupero solo se è sfuso!",
        "Non è stato indicato il recipiente di stoccaggio!",
        "Al consumatore finale si può vendere solo olio confezionato!",
        "Manca il codice operazione SIAN!",
        "La cessione omaggio è solo per olio confezionato!",
        "Lo scarico di morchie può avvenire solo da oli NON confezionati!",
        "Nello storno inventario bisogna indicare se carico o scarico",
        "L'inventario di magazzino deve essere un carico",
        "Non si può fare uno storno ad un inventario che non esiste",
        "Nel recipiente di stoccaggio selezionato non c'è sufficiente quantità",
        "La data di registrazione è precedente all'ultimo movimento inviato al SIAN. Se si conferma, questo movimento non sarà inviato al SIAN",
        "Quantità articolo non sufficiente! Se si conferma si creerà una quantità negativa",
        "La somma dei singoli lotti disponibili non corrisponde alla quantità inserita. Se la giacenza è corretta, allora, prima di inventariare, bisognerà aggiustare manualmente i lotti",
        "Non può uscire un lotto in data precedente alla sua creazione",
         'operat' => 'Operazione',
         'operat_value' => array(-1 => "Scarico", 0 => "Non opera", 1 => "Carico"),
        'cod_operaz_value' => array(11=>'',0=>'S0-Vendita olio a consumatore finale',6=>'S6-Cessione omaggio olio confezionato',7=>'S7-Scarico olio destinato ad altri usi',8=>'S8-Scarico olio autoconsumo',12=>'SP-Perdite o cali di olio',13=>'Q-Separazione morchie'),
        'cop_operazione' => 'Codice operazione SIAN',
        'cod_operaz_value_carico' => array(11=>'',10=>'C10-Carico olio lampante da recupero'),
        'partner' => 'Cliente/Fornitore',
        'del_this' => 'Elimina il movimento di magazzino',
        'amount' => " Valore in ",
        ),
    "report_caumag.php" =>
      array("causali di magazzino ",
        "Lista delle "
      ),
    "admin_catmer.php" =>
      array("categoria merceologica ",
        "Numero ",
        "Descrizione ",
        "Immagine (jpg,png,gif) max 64Kb: ",
        "% di ricarico ",
        "Annotazioni ",
        "codice gi&agrave; esistente!",
        "la descrizione &egrave; vuota!",
        "Il file immagine dev'essere nel formato PNG",
        "L'immagine non dev'essere pi&ugrave; grande di 64Kb",
        "Descrizione estesa",
        "ID riferimento e-commerce",
        'web_url' => 'Web url<br />(es: http://site.com/group.html)',
        'top' => 'Sincronizzazione e-commerce',
        'top_value' => array(0 => 'NON sincronizzato', 1 => 'Attivo e pubblicato in home', 2 => 'Attivo', 3 => 'Disattivato')
      ),
    "admin_ragstat.php" =>
      array("raggruppamento statistico ",
        "Numero ",
        "Descrizione ",
        "Immagine (jpg,png,gif) max 64kb: ",
        "% di ricarico ",
        "Annotazioni ",
        "codice gi&agrave; esistente!",
        "la descrizione &egrave; vuota!",
        "Il file immagine dev'essere nel formato PNG",
        "L'immagine non dev'essere pi&ugrave; grande di 64Kb",
        'web_url' => 'Web url<br />(es: http://site.com/group.html)'
      ),
    "admin_caumag.php" =>
    array("causale di magazzino ",
        "Codice ",
        "Descrizione ",
        "Dati documento ",
        "Operazione",
        "Aggiorna Esistenza",
        "No",
        "Si",
        "Scarico",
        "Non opera",
        "Carico",
        "Cliente/Fornitore",
        "Cliente",
        "Entrambi",
        "Fornitore",
        "codice gi&agrave; esistente!",
        "la descrizione &egrave; vuota!",
        "il codice dev'essere un numero minore di 99"
      ),
    "genera_movmag.php" =>
      array("Genera movimenti di magazzino da documenti",
        "Data inizio ",
        "Data fine",
        "Azienda senza obbligo di magazzino fiscale!",
        " successiva alla ",
        "  righi sono da traferire in magazzino:",
        " Non ci sono righi da trasferire in magazzino!"),
    "select_giomag.php" =>
      array(0 => 'Stampa giornale di magazzino',
        'title' => 'Selezione per la visualizzazione e/o la stampa del giornale di magazzino',
        'errors' => array('La data  non &egrave; corretta!',
            'La data di inizio dei movimenti contabili da stampare non pu&ograve; essere successiva alla data dell\'ultimo !',
            'La data di stampa non pu&ograve; essere precedente a quella dell\'ultimo movimento!'
        ),
        'date' => 'Data di stampa ',
        'date_ini' => 'Data registrazione inizio  ',
        'date_fin' => 'Data registrazione fine ',
        'mode' => 'Tipo di stampa sul pdf',
        'cover' => 'Stampa copertina',
        'subtitle' => 'Titolo aggiuntivo sul pdf (facoltativo)',
        'mode_value' => array(1 => 'Tutti i movimenti', 2 => 'Solo le entrate', 3 => 'Solo le uscite'),
        'price_value' => array(1 => 'con prezzi', 2 => 'senza prezzi con rag.sociale'),
        'header' => array('Data' => '', 'Causale' => '', 'Descrizione documento' => '' ,
          'Articolo' => '', 'Prezzo' => '', 'Importo' => '', 'UM' => '', 'Quantit&agrave;' => ''
          )
      ),
    "recalc_exist_value.php" =>
    array("Rivalutazione esistenza articoli da movimenti di magazzino",
        "Anno di riferimento",
        "Metodo di rivalutazione, scelto in configurazione azienda",
        "Sono stati movimentati i seguenti",
        "articoli durante il ",
        "Movimenti",
        "Codice",
        "Descrizione",
        "Esistenza",
        "UM acq.",
        "Valore precedente",
        "Valore rivalutato",
        "NON RIVALUTATO vedi nota ",
        "(1) perch&egrave; ci sono degli acquisti negli anni successivi al ",
        "(2) perch&egrave; non ci sono movimenti di acquisto nel ",
        "Non ci sono articoli movimentati!"),
    "inventory_stock.php" =>
    array('title' => "Inventario fisico di magazzino",
        'del' => "del",
        'catmer' => "Categoria Merceologica ",
        'select' => "Sel.",
        'code' => "Codice",
        'descri' => "Descrizione articolo",
        'mu' => "U.M.",
        'load' => "Carico",
        'unload' => "Scarico",
        'value' => "Nuovo valore giacenza",
        'v_a' => "Valore attuale",
        'v_r' => "Valore reale",
        'g_a' => "Giacenza attuale",
        'g_r' => "Giacenza reale",
        'g_v' => "Valore giacenza",
        'noitem' => "Non sono stati trovati articoli in questa categoria merceologica",
        'errors' => array(" La giacenza reale non pu&ograve; essere negativa",
        " Il valore reale non pu&ograve; essere negativo o uguale a zero",
        " Si sta tentando di fare l'inventario con giacenza attuale e reale entrambe a zero",
        " Per questo articolo è già stato fatto l'inventario nello stesso giorno"),
        'preview_title' => 'Confermando le scelte fatte si registreranno i seguenti movimenti di magazzino:'
    ),
    "select_schart.php" =>
      array(0 => 'Stampa schedari di magazzino',
        'title' => 'Selezione per la visualizzazione e/o la stampa delle schede di magazzino',
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 1 carattere!',
            'Cambia articolo'
        ),
        'errors' => array('La data  non &egrave; corretta!',
            'La data di inizio dei movimenti contabili da stampare non pu&ograve; essere successiva alla data dell\'ultimo !',
            'La data di stampa non pu&ograve; essere precedente a quella dell\'ultimo movimento!',
            'L\'articolo iniziale non pu&ograve; avere un codice successivo a quello finale!',
            'La categoria merceologica iniziale non pu&ograve; avere un codice successivo a quello finale!'
        ),
        'date' => 'Data di stampa ',
        'cm_ini' => 'Categoria merceologica inizio ',
        'art_ini' => 'Articolo inizio ',
        'date_ini' => 'Data registrazione inizio  ',
        'cm_fin' => 'Categoria merceologica fine ',
        'art_fin' => 'Articolo fine ',
        'date_fin' => 'Data registrazione fine ',
        'header' => array('Data' => '', 'Causale' => '', 'Magazzino' => '', 'Descrizione documento' => '', 'Prezzo' => '', 'UM' => '', 'Quantità' => '', 'Valore carico' => '', 'Valore scarico' => '', 'Quantità giacenza' => '', 'Valore giacenza' => '' ),
        'tot' => 'Consistenza'
      ),
    "stampa_schart.php" =>
      array(0 => 'SCHEDA DI MAGAZZINO dal ', 1 => ' al ',
        'bot' => 'a riportare : ',
        'top' => 'da riporto :  ',
        'item_head' => array('Codice', 'Cat.Merc', 'Descrizione', 'U.M.', 'ScortaMin.'),
        'header' => array('Data', 'Causale', 'Descrizione documento',
            'Prezzo', 'UM', 'Quantita',
            'Val. carico', 'Val. scarico',
            'Q.ta giacenza', 'Val. giacenza'
        ),
        'tot' => 'Consistenza al '
      ),
    "select_deplia.php" =>
      array('title' => 'Selezione per la stampa del catalogo',
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 1 carattere!',
            'Cambia articolo'
        ),
        'errors' => array('La data  non &egrave; corretta!',
            'L\'articolo iniziale non pu&ograve; avere un codice successivo a quello finale!',
            'La categoria merceologica iniziale non pu&ograve; avere un codice successivo a quello finale!'
        ),
        'date' => 'Data di stampa ',
        'cm_ini' => 'Categoria merceologica inizio ',
        'art_ini' => 'Articolo inizio ',
        'cm_fin' => 'Categoria merceologica fine ',
        'art_fin' => 'Articolo fine ',
        'barcode' => 'Stampa',
        'jumpcat' => 'Cambio pagina al cambio categoria',
        'barcode_value' => array(0 => 'Immagini', 1 => 'Codici a Barre'),
        'listino' => 'Listino',
        'listino_value' => array(1 => ' di Vendita 1', 2 => ' di Vendita 2', 3 => ' di Vendita 3', 4 => ' di Vendita 4', 'web' => ' di Vendita Online')
      ),
    "select_listin.php" =>
      array('title' => 'Selezione per la stampa dei listini',
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 1 carattere!',
            'Cambia articolo'
        ),
        'errors' => array('La data  non &egrave; corretta!',
            'L\'articolo iniziale non pu&ograve; avere un codice successivo a quello finale!',
            'La categoria merceologica iniziale non pu&ograve; avere un codice successivo a quello finale!'
        ),
        'date' => 'Data di stampa ',
        'cm_ini' => 'Categoria merceologica inizio ',
        'art_ini' => 'Articolo inizio ',
        'cm_fin' => 'Categoria merceologica fine ',
        'art_fin' => 'Articolo fine ',
        'listino' => 'Listino',
        'listino_value' => array(0 => 'd\'Acquisto', 1 => ' di Vendita 1', 2 => ' di Vendita 2', 3 => ' di Vendita 3', 4 => ' di Vendita 4', 'web' => ' di Vendita Online'),
        'id_anagra' => 'Fornitore (vuoto per tutti)',
        'ordineStampa' => 'Ordine di Stampa',
        'alternativeOrdineStampa' => array('default',
            'codice articolo',
            'descrizione articolo',
            'categoria articolo'
        ),
        'tipoStampa' => 'Tipo di Stampa',
        'alternativeTipoStampa' => array('espansa', 'compatta', 'verticale'),
      ),
    "update_vatrate.php" =>
      array('title' => 'Modifica aliquota IVA degli articoli',
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 1 carattere!',
            'Cambia articolo'
        ),
        'errors' => array('Errore nullo',
            'L\'articolo iniziale non pu&ograve; avere un codice successivo a quello finale!',
            'La categoria merceologica iniziale non pu&ograve; avere un codice successivo a quello finale!'
        ),
        'cm_ini' => 'Categoria merceologica inizio ',
        'art_ini' => 'Articolo inizio ',
        'cm_fin' => 'Categoria merceologica fine ',
        'art_fin' => 'Articolo fine ',
        'rate_obj' => 'Aliquota oggetto della modifica',
        'rate_new' => 'Nuova aliquota',
        'header' => array('Cat.Merceologica' => '', 'Codice' => '', 'Descrizione' => '', 'U.M.' => '',
            'Aliquota vecchia' => '', 'Aliquota nuova' => ''
        )
      ),
    "update_prezzi.php" =>
      array('title' => 'Modifica prezzi di listino',
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 1 carattere!',
            'Cambia articolo'
        ),
        'errors' => array('Valore "0" inaccettabile in questa modalit&agrave; di Modifica',
            'L\'articolo iniziale non pu&ograve; avere un codice successivo a quello finale!',
            'La categoria merceologica iniziale non pu&ograve; avere un codice successivo a quello finale!'
        ),
        'cm_ini' => 'Categoria merceologica inizio ',
        'art_ini' => 'Articolo inizio ',
        'cm_fin' => 'Categoria merceologica fine ',
        'art_fin' => 'Articolo fine ',
        'lis_obj' => 'Listino oggetto della modifica',
        'lis_bas' => 'Listino base di calcolo',
        'listino_value' => array(0 => 'd\'Acquisto', 1 => ' di Vendita 1', 2 => ' di Vendita 2', 3 => ' di Vendita 3', 4 => ' di Vendita 4', 'web' => ' di Vendita Online'),
        'mode' => 'Modalit&agrave; di modifica',
        'mode_value' => array('A' => 'Sostituzione', 'B' => 'Somma in percentuale', 'C' => 'Somma valore',
            'D' => 'Moltiplicazione per valore', 'E' => 'Divisione per valore', 'F' => 'Azzeramento e somma percentuale'),
        'valore' => 'Percentuale/valore',
        'round_mode' => 'Arrotondamento matematico a',
        'round_mode_value' => array('1 ', '10 centesimi', '1 centesimo', '1 millesimo', '0,1 millesimi', '0,01 millesimi'),
        'weight_valadd' => 'Incidenza su peso specifico es. €/kg',
        'header' => array('Cat.Merceologica' => '', 'Codice' => '', 'Descrizione' => '', 'U.M.' => '',
            'Prezzo vecchio' => '', 'Incidenza peso' => '', 'Prezzo nuovo' => ''
        )),
    "admin_artico.php" =>
      array('title' => 'Gestione degli articoli',
        'ins_this' => 'Inserimento articolo',
        'upd_this' => 'Modifica l\'articolo ',
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
        ),
        'war' => array(
            'ok_ins' => 'Articolo inserito con successo'
        ),
        'codice' => "Codice",
        'descri' => "Descrizione",
        'good_or_service' => "Tipologia di articolo",
        'good_or_service_value' => array(0 => 'Merce', 1 => 'Servizio', 2=> 'Composizione'),
        'body_text' => "Testo descrittivo (precede il rigo)",
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
        'catmer' => "Categoria merceologica",
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
        'SIAN' => 'Movimenta il SIAN',
        'SIAN_value' => array(0=>'non movimenta',1=>'come olio',2=>'come olive',6=>'come fitosanitario'),
        'maintenance_period' => 'Periodicità manutenzione (gg)',
        'peso_specifico' => 'Peso specifico (kg/l) o Moltiplicatore',
        'volume_specifico' => 'Volume specifico',
        'pack_units' => 'Pezzi in imballo',
        'codcon' => 'Conto di ricavo su vendite',
        'id_cost' => 'Conto di costo su acquisti',
        'annota' => 'Annotazioni (pubblicate anche sul web)',
        'fornitori-codici' => 'Codici Fornitori',
        'document' => 'Documenti e/o certificazioni',
        'imageweb' => 'immagini e foto',
        'web_mu' => 'Unit&agrave; di misura online',
        'web_price' => 'Prezzo di vendita online',
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
        'modal_ok_insert' => 'Articolo inserito con successo clicca sulla X in alto a destra per uscire oppure...',
        'iterate_invitation' => 'INSERISCI UN ALTRO ARTICOLO DI MAGAZZINO',
        'browse_for_file' => 'Sfoglia',
        'id_anagra' => 'Fornitore di riferimento',
        'last_buys' => 'Ultimi acquisti da fornitori',
        'ordinabile' => 'Articolo ordinabile',
        'durability_mu' => 'Unità di misura durabilità',
        'durability' => 'Valore durabilità',
        'warranty_days' => 'Giorni di garanzia',
        'unita_durability' => array('' => '', '>' => '>', '<' => '<', 'H' => 'H', 'D' => 'D', 'M' => 'M'),
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 1 carattere!',
            'Cambia fornitore'
        )
    ),
	"admin_group.php" =>
    array('title' => 'Gestione dei gruppi delle varianti',
        'ins_this' => 'Inserimento gruppo delle varianti',
        'upd_this' => 'Modifica il gruppo delle varianti',
        'err' => array(
            'codice' => 'Il codice articolo &egrave; gi&agrave; esistente',
            'empty_var' => 'Deve esserci per forza almeno una variante',
            'grcod' => 'Questo articolo appartiene già ad un altro gruppo',
            'descri' => 'Inserire una descrizione',
            'no_ins' => 'Non sono riuscito ad inserire l\'articolo sul database',
            'char' => 'Sul codice articolo ho sostituito i caratteri speciali non consentiti con "_" ',
            'codart_len' => 'Il codice articolo ha una lunghezza diversa da quella stabilita in configurazione avanzata azienda ',
            'filmim' => 'Il file dev\'essere nel formato png, x-png, jpg, gif, x-gif, jpeg',
            'filetoobig' => 'Controllare che il file selezionato non superi le dimensioni impostate nella configurazione php'
        ),
        'war' => array(
            'ok_ins' => 'Articolo inserito con successo'
		),
		'info' => 'Solitamente gli e-commerce usano creare degli articoli con caratteristiche molto simili tra loro ponendoli sotto un articolo principale, il genitore. <br/>Per permettere la sincronizzazione
        dei databases in GAzie un genitore è definito "gruppo" e le varianti, opzioni, attributi sono i singoli articoli che fanno riferimento allo stesso gruppo-genitore.',
		'home' => "ID gruppo",
		'variant' => "Varianti, opzioni, attributi",
		'codice' => "ID gruppo",
        'descri' => "Descrizione",
		'image' => 'immagine',
        'web_public' => 'Sincronizza sul sito web',
        'web_public_value' => array(0 => 'No', 1 => 'Si', 2 => 'Attivo e prestabilito', 3 => 'Attivo e pubblicato in home', 4 => 'Attivo, in home e prestabilito', 5 => 'Disattivato su web'),
		'body_text' => 'Descrizione estesa',
        'depli_public' => 'Pubblica sul catalogo',
        'depli_public_value' => array(0 => 'No', 1 => 'Si'),
        'web_url' => 'Web url<br />(es: http://site.com/item.html)',
        'modal_ok_insert' => 'Articolo inserito con successo clicca sulla X in alto a destra per uscire oppure...',
        'mesg' => array(
        )
    ),
	"mostra_lotti.php" =>
    array('title' => 'Mostra lotti articolo'
    ),
  "admin_artico_compost.php" =>
    array('title' => 'Gestione delle composizioni',
      'err' => array(
        'codart' => 'Non hai selezionato l\'articolo da aggiungere alla composizione',
        'quanti' => 'Non hai selezionato la quantità da aggiungere alla composizione',
        'quarow' => 'Rigo con quantità zero',
        'artexi' => 'Articolo già presente in composizione: eventualmente cambia la quantità',
        'artpar' => 'Non puoi aggiungere un articolo genitore'
        ),
      'codice' => "Codice",
      'descri' => "Descrizione",
      'good_or_service' => "Tipologia di articolo",
      'good_or_service_value' => array(0 => 'Merce', 1 => 'Servizio', 2=> 'Composizione'),
      'body_text' => "Testo descrittivo (precede il rigo)",
      'lot_or_serial' => 'Lotti o numeri seriali',
      'lot_or_serial_value' => array(0 => 'No', 1 => 'Lotti', 2 => 'Seriale/Matricola'),
      'barcode' => "Codice a Barre EAN13",
      'image' => "Immagine (jpg,png,gif) max 64Kb",
      'unimis' => "Unit&agrave; di misura vendite",
      'catmer' => "Categoria merceologica",
      'ragstat' => "Raggruppamento statistico",
      'preacq' => 'Prezzo d\'acquisto',
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
      'peso_specifico' => 'Peso specifico/Moltiplicatore',
      'volume_specifico' => 'Volume specifico',
      'pack_units' => 'Pezzi in imballo',
      'codcon' => 'Conto di ricavo su vendite',
      'id_cost' => 'Conto di costo su acquisti',
      'annota' => 'Annotazioni (pubblicate anche sul web)',
      'document' => 'Documenti e/o certificazioni',
      'web_mu' => 'Unit&agrave; di misura online',
      'web_price' => 'Prezzo di vendita online',
      'web_multiplier' => 'Moltiplicatore prezzo web',
      'web_public' => 'Sincronizza sul sito web',
      'web_public_value' => array(0 => 'No', 1 => 'Si', 2 => 'Attivo e prestabilito', 3 => 'Attivo e pubblicato in home', 4 => 'Attivo, in home e prestabilito', 5 => 'Disattivato su web'),
      'ordinabile_value' => array( '' => '----','S' => 'Ordinare','N'=> 'Non ordinare'),
      'movimentabile' => 'Articolo Movimentabile',
      'movimentabile_value' => array ( '' => '----','S' => 'Si','N'=>'No','E' => 'Esaurito'),
      'depli_public' => 'Pubblica sul catalogo',
      'depli_public_value' => array(0 => 'No', 1 => 'Si'),
      'web_url' => 'Web url<br />(es: http://site.com/item.html)',
      'modal_ok_insert' => 'Articolo inserito con successo clicca sulla X in alto a destra per uscire oppure...',
      'iterate_invitation' => 'INSERISCI UN ALTRO ARTICOLO DI MAGAZZINO',
      'browse_for_file' => 'Sfoglia',
      'id_anagra' => 'Fornitore',
      'codice_fornitore' => 'Codice del fornitore',
      'ordinabile' => 'Articolo ordinabile',
      'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 1 carattere!',
            'Cambia fornitore'
      )
    ),
	"admin_warehouse.php" =>
    array('title' => 'Lista dei magazzini ( oltre la sede )',
        'ins_this' => 'Inserimento nuovo magazzino',
        'upd_this' => 'Modifica il magazzino',
        'err' => array(
            'name' => 'Il nome del magazzino deve essere lungo almeno 4 caratteri',
            'filmim' => 'Il file dev\'essere nel formato PNG, JPG, GIF'
        ),
        'war' => array(
            'ok_ins' => 'Magazzino inserito con successo'
		),
        'name' => 'Nome',
        'web_url' => 'Indirizzo web',
        'image' => 'Immagine',
        'note_other' => 'Descrizione/Indirizzo',
    )
);
?>
