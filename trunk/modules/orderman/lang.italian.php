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

$strScript = array("admin_orderman.php" =>
		array('title' => 'Amministrazione delle produzioni',
        'ins_this' => 'Inserisci una nuova produzione',
        'upd_this' => 'Aggiorna la produzione',
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 2 caratteri!'),
        'errors' => array(),
		"ID ",
		"Tipo di produzione",
		"Descrizione produzione",
		"Informazioni supplementari",
		"Descrizione vuota",
		"Data inizio produzione",
		"Durata produzione in giorni",
		"Luogo di produzione",
		"Ordine",
		"Articolo prodotto",
		"Operaio",
		"Durata produzione in ore",
		"Tipo di produzione vuoto",
		"Lotto di produzione",
		"Si possono caricare solo file nel formato png, jpg, pdf, odt di dimensioni inferiori a 1M",
		"Quantità produzione",
		"Manca l'articolo prodotto",
		"Manca la quantità prodotta",
		"Gli operai non possono lavorare più di 13 ore al giorno: D. Lgs. 66/2003",
		"Aggiungi operaio",
		"Articolo non presente o sconosciuto! Selezionare l'articolo fra quelli mostrati nell'elenco a tendina.",
		"Non c'è sufficiente disponibilità di un componente",
		"Manca la data di registrazione",
		"Il numero d'ordine inserito è inesistente",
		"L'articolo è già stato prodotto per questo ordine",
		"La quantità inserita di un lotto, di un componente, è errata"
		),
	"orderman_report.php" =>
		array('title' => 'Lista delle produzioni',
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 2 caratteri!')
			),
	"admin_luoghi.php" =>	
	array('title' => 'Gestione luoghi di produzione',
        'mesg' => array('La ricerca non ha dato risultati!',
            'Inserire almeno 2 caratteri!'),
		"Id",
		"Descrizione",
		"Immagine (jpg,png,gif) max 300kb: ",
		"Note",
		"Codice già usato",
		"Descrizione vuota",
		"L'immagine non dev'essere pi&ugrave; grande di 300 kb",
		"L'immagine inserita ha un formato non ammesso",
		"Luogo di produzione",
		'web_url' => 'Mappa di Google<br />(es: https://goo.gl/maps/YajAcRexvDp)'
			)	
);
?>