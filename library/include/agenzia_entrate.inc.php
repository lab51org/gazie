<?php
/*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2011 Antonio De Vincentiis Montesilvano (PE)
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


*****************************************************************************************
Questa classe genera il file da importare con l'apposito software messo a disposizione della
Agenzie delle Entrate (Uniconline - File Internet) per effettuare la trasmissione per via
telematica.
Affinch� il tutto avvenga correttamente si devono passare alle funzioni specifiche sotto
elencate  denominate "creaFileXXX" gli array di seguito specificati per singola  funzione.
******************************************************************************************/

class AgenziaEntrate
      {

/****** creaFileIVC - COMUNICAZIONE ANNUALE DATI IVA
      Una AVVERTENZA IMPORTANTE � quella di considerare che siccome questa funzione � predisposta
      solo per chi si invia "IN PROPRIO" le dichiarazioni viene impostato il relativo flag
      (campo 4 del record di testa "A") sempre a "01" e quindi il codice fiscale del fornitore
      deve coincidere con quello del contribuente, lo stesso vale per il campo 7 del Record "B"
      dove viene richiesto il codice fiscale del produttore del software.
      $A = array monodimensionale con i seguenti index:
              [0] = Codice fornitura 3 alfanumerico (IVC)
              [1] = Anno fornitura 2 numerico
              [2] = Codice Fiscale fornitore 16 alfanumerico
      $B = array monodimensionale con i seguenti index:
              [0] = Codice Fiscale del contribuente 16 alfanumerico
              [1] = Codice Fiscale o Partita IVA del produttore del software 16 alfanumerico
              [2] = Ragione Sociale del Contribuente 60 alfanumerico
              [3] = Cognome del Contribuente se persona fisica 24 alfanumerico
              [4] = Cognome del Contribuente se persona fisica 20 alfanumerico
              [5] = Anno d'Imposta 4 numerico
              [6] = Partita IVA del Contribuente 11 numerico
              [7] = Codice Attivit� 5 alfanumerico
              [8] = Flag Contabilit� Separata 1 boleano
              [9] = Flag Societa appartenente ad un Gruppo IVA 1 boleano
              [10]= Flag Eventi Eccezionali 1 boleano
              [11]= Codice Fiscale Dichiarante (Responsabile della dichiarazione) 16 alfanumerico
              [12]= Codice carica del dichiarante se presente(0,1,2,5,6,7,8 o 9) 2 nuemrico
              [13]= Codice Fiscale societ� Dichiarante 11 numerico
              [14]= Totale operazioni attive al netto dell'IVA 11 numerico
              [15]= Operazioni non imponibili 11 numerico
              [16]= Operazioni esenti 11 numerico
              [17]= Cessioni intracomunitarie di beni 11 numerico
              [18]= Cessioni beni strumentali
              [19]= Totale operazioni passive al netto dell'IVA 11 numerico
              [20]= Acquisti non imponibili 11 numerico
              [21]= Acquisti esenti 11 numerico
              [22]= Acquisti intracomunitari si beni 11 numerico
              [23]= Acquisti beni strumentali
              [24]= Importazione oro e argento senza pagamento IVA Imponibile 11 numerico
              [25]= Importazione oro e argento senza pagamento IVA Imposta 11 numerico
              [26]= Importazione rottami Acquisti senza pagamento IVA Imponibile 11 numerico
              [27]= Importazione rottami Acquisti senza pagamento IVA Imposta 11 numerico
              [28]= IVA esigibile CD4 11 numerico
              [29]= IVA detratta  CD5 11 numerico
              [30]= IVA dovuta    CD6 11 numerico
              [31]= IVA a credito CD6 11 numerico
      $Z = numero di record "B", di default 1
*/
      function RecordA($A) //record testa della fornitura comunicazione annuale dati IVA
               {
               $this->CodiceFornitura = substr(str_pad($A[0],3,' '),0,3);
               $this->AnnoFornitura = substr(str_pad($A[1],2,'0',STR_PAD_LEFT),0,2);
               $this->CFFornitore = substr(str_pad($A[2],16,' '),0,16);
               return "A".str_repeat(" ",14).$this->CodiceFornitura.$this->AnnoFornitura."01"
                      .$this->CFFornitore.str_repeat(" ",483).str_repeat("0",8).str_repeat(" ",1368)
                      ."A\r\n";
               }
      function RecordB($B) //record dati della comunicazione annuale IVA
               {
               $this->CFContribuente = substr(str_pad($B[0],16,' '),0,16);
               $this->CFProduttore = substr(str_pad($B[1],16,' '),0,16);
               $this->Ragionesociale = substr(str_pad($B[2],60,' '),0,60);
               $this->Cognome = substr(str_pad($B[3],24,' '),0,24);
               $this->Nome = substr(str_pad($B[4],20,' '),0,20);
               $this->AnnoImposta = substr(str_pad($B[5],4,'0',STR_PAD_LEFT),0,4);
               $this->PIContribuente = substr(str_pad($B[6],11,'0',STR_PAD_LEFT),0,11);
               $this->CodiceAttivita = substr(str_pad($B[7],6,' '),0,6);
               if ($B[8]>0){
                  $this->ContabSeparata = 1;
               } else {
                  $this->ContabSeparata = 0;
               }
               if ($B[9]>0){
                  $this->SocietaGruppo = 1;
               } else {
                  $this->SocietaGruppo = 0;
               }
               if ($B[10]>0){
                  $this->EventiEccezio = 1;
               } else {
                  $this->EventiEccezio = 0;
               }
               $this->CFDichiarante = substr(str_pad($B[11],16,' '),0,16);
               $this->CodiceCarica = substr(str_pad($B[12],2,'0',STR_PAD_LEFT),0,2);
               $this->CNSocDichiar = substr(str_pad($B[13],11,'0',STR_PAD_LEFT),0,11);
               //   Dall'index 14 all'index 29 sono tutti da portare ad un valore numerico
               //   intero di lunghezza 11 caratteri (valori in unita' di euro)
               $this->Valori = "";
               for ($i = 14; $i < 32; $i++) {
                   $this->Valori .= str_pad(intval($B[$i]),11,' ',STR_PAD_LEFT);
               }
               return "B".$this->CFContribuente.str_repeat(" ",56).$this->CFProduttore."0"
                      .$this->Ragionesociale.$this->Cognome.$this->Nome.$this->AnnoImposta
                      .$this->PIContribuente.$this->CodiceAttivita.$this->ContabSeparata
                      .$this->SocietaGruppo.$this->EventiEccezio.$this->CFDichiarante
                      .$this->CodiceCarica.$this->CNSocDichiar.$this->Valori."0".str_repeat(" ",16).str_repeat("0",16).str_repeat(" ",1419)."A\r\n";
               }
      function RecordZ($Z=1) //record coda della fornitura, in Z c'e' il numero di record "B"
               {
               return "Z".str_repeat(" ",14).str_pad(intval($Z),9,'0',STR_PAD_LEFT).str_repeat(" ",1873)."A\r\n";
               }
      function creaFileIVC($A,$B)
               {
               $accumulatore = $this->RecordA($A).$this->RecordB($B).$this->RecordZ();
               return $accumulatore;
               }
// --- FINE FUNZIONI COMUNICAZIONE ANNUALE DATI IVA (IVC)

/****** creaFileECF - ELENCO CLIENTI E FORNITORI
      $testa = array monodimensionale con i seguenti index:
              [codfis] = Codice Fiscale del contribuente 16 alfanumerico o 11 numerico
              [pariva] = Partita IVA del contribuente 11 numerico
              [nome] = Nome del Contribuente 26 alfanumerico
              [cognome] = Cognome del Contribuente 25 alfanumerico
              [sesso] = Sesso se Persona Fisica (d'ora in poi PF) 1 alfanumerico (MF)
              [datnas] = Data di nascita contribuente se PF formato GGMMAAAA
              [luonas] = Comune o stato estero di nascita contribuente se PF 40 alfanumerico
              [pronas] = Provincia o stato estero (EE) di nascita contribuente se PF 2 alfanumerico
              [ragsoc] = Ragione sociale contribuente se Persona Giuridica (d'ora in poi PG) 70 alfanumerico
              [segleg] = Comune della sede legale se PG 40 alfanumerico
              [proleg] = Provincia della sede legale se PG 2 alfanumerico
              [anno] = Anno fornitura 4 numerico
      $dati = array bidimensionale con i seguenti index posti anche in questo ordine:
              [tipo] = Tipo di Record 1 numerico (1=clienti 2=fornitori)
              [progressivo] = Numero Progressivo del cliente/fornitore 16 numerico
              [codfis] = Codice Fiscale del cliente/fornitore 16 alfanumerico o 11 numerico
              [pariva] = Partita IVA del cliente/fornitore 11 numerico
              [imponibile] = Imponibile
              [imposta] = Imposta applicata
              [nonimp] = Non imponibile
              [esente] = Esente
              [elementi] = Numero di elementi valorizzati nel record compreso il progressivo ed escluso esso stesso
     $totali = array bidimensionale (prima dimensione:index [1] =clienti, index[2] = fornitori)  la seconda dimensione deve avere i seguenti index:
              [numero] = Numero di clienti/fornitori 8 numerico
              [imponibile] = Imponibile
              [imposta] = Imposta applicata
              [esente] = Esente
              [nonimp] = Non imponibile
*/
      function Record09($T) //record testa degli elenchi clienti e fornitori
               {
               $this->CFContribuente = substr(str_pad($T['codfis'],16,' '),0,16);
               $this->PIContribuente = substr(str_pad($T['pariva'],11,'0',STR_PAD_LEFT),0,11);
               if (isset($T['sesso'])){
                  $this->AltriDati = substr(str_pad($T['cognome'],26,' '),0,26).
                                  substr(str_pad($T['nome'],25,' '),0,25).
                                  substr($T['sesso'],0,1).
                                  substr(str_pad($T['datnas'],8,' '),0,8).
                                  substr(str_pad($T['luonas'],40,' '),0,40).
                                  substr(str_pad($T['pronas'],2,' '),0,2).str_repeat(' ',112);
               } else {
                  $this->AltriDati = str_repeat(' ',102).
                                   substr(str_pad($T['ragsoc'],70,' '),0,70).
                                   substr(str_pad($T['sedleg'],40,' '),0,40).
                                   substr(str_pad($T['proleg'],2,' '),0,2);
               }
               $this->Anno = substr(str_pad($T['anno'],4,'0'),0,4);
               return "ECF0038".$this->CFContribuente.$this->PIContribuente.$this->AltriDati.
                      str_repeat(' ',16).$this->Anno.str_repeat('0',8).str_repeat(' ',16).
                      str_repeat('0',14).str_repeat(' ',1490)."A\r\n";

               }

      function Record12($D) // corpo e riepilogativo con i dati dei record dei singoli clienti/fornitori
               {
               function CreaElemento($k,$v,$t){
                      $acc = '';
                      if ($t == 1) { // clienti
                           $tipo = 'CL';
                           switch ($k) {
                                  case 'progressivo':
                                  $acc = $tipo.'001001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'codfis':
                                  $acc = $tipo.'002001'.substr(str_pad($v,16,' '),0,16);
                                  break;
                                  case 'pariva':
                                  $acc = $tipo.'003001'.substr(str_pad($v,16,' '),0,16);
                                  break;
                                  case 'imponibile':
                                  $acc = $tipo.'004001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'imposta':
                                  $acc = $tipo.'004002'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'nonimp':
                                  $acc = $tipo.'005001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'esente':
                                  $acc = $tipo.'006001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                           }
                      } else { //fornitori
                           $tipo = 'FR';
                           switch ($k) {
                                  case 'progressivo':
                                  $acc = $tipo.'001001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'codfis':
                                  $acc = $tipo.'002001'.substr(str_pad($v,16,' '),0,16);
                                  break;                                                                   case 'pariva':
                                  $acc = $tipo.'003001'.substr(str_pad($v,16,' '),0,16);
                                  break;
                                  case 'imponibile':
                                  $acc = $tipo.'004001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'imposta':
                                  $acc = $tipo.'004002'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'nonimp':
                                  $acc = $tipo.'006001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'esente':
                                  $acc = $tipo.'007001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                           }
                  }
                  return $acc;
               }
               $n_elements = 0;
               $ctrl_tipo = 0;
               foreach ($D as $ElementsData){
                       if ($ctrl_tipo < $ElementsData['tipo'] and $ctrl_tipo != 0) { // non � lo stesso partner precedente e non � il primo
                          $diff_to_end = (70-$n_elements)*24 + 116;
                          $acc .= str_repeat(' ',$diff_to_end)."A\r\n".$ElementsData['tipo'];
                          $n_elements = 0;
                       } elseif ($ctrl_tipo == 0) {
                          $acc = $ElementsData['tipo'];
                       }
                       foreach ($ElementsData as $key=>$value){
                           $rs_elemento = CreaElemento($key,$value,$ElementsData['tipo']);
                           if (!empty($rs_elemento)){ // se � un elemento valido
                              $acc .= $rs_elemento;
                              $n_elements++;
                           }
                           if ($n_elements == 70 ){
                              // salta sulla riga successiva
                              $acc .= str_repeat(' ',116)."A\r\n".$ElementsData['tipo'];
                              $n_elements = 0;
                           }
                       }
                       $ctrl_tipo = $ElementsData['tipo'];
               }
               $diff_to_end = (70-$n_elements)*24 + 116;
               $acc .= str_repeat(' ',$diff_to_end)."A\r\n";
               return $acc;
               }

      function Record3($T) // record di coda degli elenchi clienti e fornitori
               {
               $acc = '3';
               if (isset($T[1]) and !isset($T[2])){ // solo clienti
                  $acc .= substr(str_pad($T[1]['numero'],8,'0',STR_PAD_LEFT),0,8).'00000000';
                  // clienti
                  $acc .= substr(str_pad($T[1]['imponibile'],20,' ',STR_PAD_LEFT),0,20);
                  $acc .= substr(str_pad($T[1]['imposta'],20,' ',STR_PAD_LEFT),0,20);
                  $acc .= substr(str_pad($T[1]['nonimp'],20,' ',STR_PAD_LEFT),0,20);
                  $acc .= substr(str_pad($T[1]['esente'],20,' ',STR_PAD_LEFT),0,20);
                  for ($i=0;$i < 18;$i++){
                      $acc .= str_repeat(' ',19).'0';
                  }
               } elseif (!isset($T[1]) and isset($T[2])){ // solo fornitori
                  $acc .= '00000000'.substr(str_pad($T[2]['numero'],8,'0',STR_PAD_LEFT),0,8);
                  // fornitori
                  for ($i=0;$i < 10;$i++){
                      $acc .= str_repeat(' ',19).'0';
                  }
                  $acc .= substr(str_pad($T[2]['imponibile'],20,' ',STR_PAD_LEFT),0,20);
                  $acc .= substr(str_pad($T[2]['imposta'],20,' ',STR_PAD_LEFT),0,20);
                  $acc .= str_repeat(' ',19).'0';
                  $acc .= substr(str_pad($T[2]['nonimp'],20,' ',STR_PAD_LEFT),0,20);
                  $acc .= substr(str_pad($T[2]['esente'],20,' ',STR_PAD_LEFT),0,20);
                  for ($i=0;$i < 7;$i++){
                      $acc .= str_repeat(' ',19).'0';
                  }
               } elseif (isset($T[1]) and isset($T[2])) { // clienti e fornitori
                  $acc .=  substr(str_pad($T[1]['numero'],8,'0',STR_PAD_LEFT),0,8).substr(str_pad($T[2]['numero'],8,'0',STR_PAD_LEFT),0,8);
                  // clienti
                  $acc .= substr(str_pad($T[1]['imponibile'],20,' ',STR_PAD_LEFT),0,20);
                  $acc .= substr(str_pad($T[1]['imposta'],20,' ',STR_PAD_LEFT),0,20);
                  $acc .= substr(str_pad($T[1]['nonimp'],20,' ',STR_PAD_LEFT),0,20);
                  $acc .= substr(str_pad($T[1]['esente'],20,' ',STR_PAD_LEFT),0,20);
                  // fornitori
                  for ($i=0;$i < 6;$i++){
                      $acc .= str_repeat(' ',19).'0';
                  }
                  $acc .= substr(str_pad($T[2]['imponibile'],20,' ',STR_PAD_LEFT),0,20);
                  $acc .= substr(str_pad($T[2]['imposta'],20,' ',STR_PAD_LEFT),0,20);
                  $acc .= str_repeat(' ',19).'0';
                  $acc .= substr(str_pad($T[2]['nonimp'],20,' ',STR_PAD_LEFT),0,20);
                  $acc .= substr(str_pad($T[2]['esente'],20,' ',STR_PAD_LEFT),0,20);
                  for ($i=0;$i < 7;$i++){
                      $acc .= str_repeat(' ',19).'0';
                  }
               }
               $acc .= str_repeat(' ',1340);
               return $acc."A\r\n";
               }
      function creaFileECF($testa,$dati,$totali)
               {
               $accumulatore = '0'.$this->Record09($testa).$this->Record12($dati).
                               $this->Record3($totali).'9'.$this->Record09($testa);
               return $accumulatore;
               }
// --- FINE FUNZIONI ELENCO CLIENTI E FORNITORI (ECF)





/****** creaFileART21 - COMUNICAZIONE OPERAZIONI RILEVANTI AI FINI IVA
      $testa = array monodimensionale con i seguenti index:
              [codfis] = Codice Fiscale del contribuente 16 alfanumerico o 11 numerico
              [pariva] = Partita IVA del contribuente 11 numerico
              [nome] = Nome del Contribuente 26 alfanumerico
              [cognome] = Cognome del Contribuente 25 alfanumerico
              [sesso] = Sesso se Persona Fisica (d'ora in poi PF) 1 alfanumerico (MF)
              [datnas] = Data di nascita contribuente se PF formato GGMMAAAA
              [luonas] = Comune o stato estero di nascita contribuente se PF 40 alfanumerico
              [pronas] = Provincia o stato estero (EE) di nascita contribuente se PF 2 alfanumerico
              [ragsoc] = Ragione sociale contribuente se Persona Giuridica (d'ora in poi PG) 70 alfanumerico
              [segleg] = Comune della sede legale se PG 40 alfanumerico
              [proleg] = Provincia della sede legale se PG 2 alfanumerico
              [anno] = Anno fornitura 4 numerico
      $dati = array bidimensionale con i seguenti index posti anche in questo ordine:
              [tipo] = Tipo di Record 1 numerico (1=SOGGETTI NON TITOLARI DI PARTITA IVA,2=SOGGETTI TITOLARI DI PARTITA IVA,3=SOGGETTI NON RESIDENTI)
              [codfis] = Codice Fiscale del cliente 16 alfanumerico
              [pariva] = Partita IVA del cliente/fornitore 11 numerico
              [imponibile] = Imponibile o corrispettivo in caso di [tipo] = 1
              [imposta] = Imposta applicata
              [tipoimponibile] = Tipologia Imponibile (1=Imponibile,2 = Non imponibile,3 = Esente,4 = Imponibile con IVA non esposta in fattura )
              [tipooperazione] = Tipologia dell'operazione (1 = Cessione di beni,2 = Prestazione di servizi,3 = Acquisto di beni,4 = Acquisto di servizi )
              [cognome] = Cognome in caso di [tipo] = 3
              [nome] = Nome in caso di [tipo] = 3
              [datnas] = Data di Nascita in caso di [tipo] = 3   (se non valorizzato discrimina viene considerata una persona non fisica)
              [luonas] = Comune o Stato estero di nascita in caso di [tipo] = 3
              [pronas] = Provincia di nascita in caso di [tipo] = 3 (in caso di Stato estero, indicare "EE")
              [stato] = Stato estero del domicilio fiscale in caso di [tipo] = 3 (Indicare uno dei codici, corrispondente allo Stato di residenza della controparte, di
                                                                                 cui all'Elenco dei Paesi e Territori esteri contenuto nelle istruzioni per la compilazione
                                                                                 del modello UNICO di dichiarazione dei redditi.)
              [indirizzo] = Indirizzo estero del domicilio fiscale in caso di [tipo] = 3  (se non valorizzato discrimina viene considerata una persona fisica)
     $totali = array bidimensionale (prima dimensione:index [1] =clienti, index[2] = fornitori)  la seconda dimensione deve avere i seguenti index:
              [numero] = Numero di clienti/fornitori 8 numerico
              [imponibile] = Imponibile
              [imposta] = Imposta applicata
              [esente] = Esente
              [nonimp] = Non imponibile
*/
      function Record_0($T) // TRACCIATO RECORD DI TESTA
               {
               $this->CFContribuente = substr(str_pad($T['codfis'],16,' '),0,16);
               $this->PIContribuente = substr(str_pad($T['pariva'],11,'0',STR_PAD_LEFT),0,11);
               if (isset($T['sesso'])){
                  $this->AltriDati = substr(str_pad($T['cognome'],26,' '),0,26).
                                  substr(str_pad($T['nome'],25,' '),0,25).
                                  substr($T['sesso'],0,1).
                                  substr(str_pad($T['datnas'],8,' '),0,8).
                                  substr(str_pad($T['luonas'],40,' '),0,40).
                                  substr(str_pad($T['pronas'],2,' '),0,2).str_repeat(' ',112);
               } else {
                  $this->AltriDati = str_repeat(' ',102).
                                   substr(str_pad($T['ragsoc'],70,' '),0,70).
                                   substr(str_pad($T['sedleg'],40,' '),0,40).
                                   substr(str_pad($T['proleg'],2,' '),0,2);
               }
               $this->Anno = substr(str_pad($T['anno'],4,'0'),0,4);
               return "0ART2147".$this->CFContribuente.$this->PIContribuente.$this->AltriDati.
                      str_repeat(' ',16).$this->Anno.str_repeat('0',8).str_repeat(' ',16).
                      str_repeat('0',14).str_repeat(' ',1490)."A\r\n";

               }

      function Record_1($D) // TRACCIATO RECORD DI DETTAGLIO OPERAZIONI CON SOGGETTI NON TITOLARI DI PARTITA IVA
               {
               function CreaElemento($k,$v,$t){
                      $acc = '';
                      if ($t == 1) { // clienti
                           $tipo = 'CL';
                           switch ($k) {
                                  case 'progressivo':
                                  $acc = $tipo.'001001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'codfis':
                                  $acc = $tipo.'002001'.substr(str_pad($v,16,' '),0,16);
                                  break;
                                  case 'pariva':
                                  $acc = $tipo.'003001'.substr(str_pad($v,16,' '),0,16);
                                  break;
                                  case 'imponibile':
                                  $acc = $tipo.'004001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'imposta':
                                  $acc = $tipo.'004002'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'nonimp':
                                  $acc = $tipo.'005001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'esente':
                                  $acc = $tipo.'006001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                           }
                      } else { //fornitori
                           $tipo = 'FR';
                           switch ($k) {
                                  case 'progressivo':
                                  $acc = $tipo.'001001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'codfis':
                                  $acc = $tipo.'002001'.substr(str_pad($v,16,' '),0,16);
                                  break;                                                                   case 'pariva':
                                  $acc = $tipo.'003001'.substr(str_pad($v,16,' '),0,16);
                                  break;
                                  case 'imponibile':
                                  $acc = $tipo.'004001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'imposta':
                                  $acc = $tipo.'004002'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'nonimp':
                                  $acc = $tipo.'006001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'esente':
                                  $acc = $tipo.'007001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                           }
                  }
                  return $acc;
               }
               $n_elements = 0;
               $ctrl_tipo = 0;
               foreach ($D as $ElementsData){
                       if ($ctrl_tipo < $ElementsData['tipo'] and $ctrl_tipo != 0) { // non � lo stesso partner precedente e non � il primo
                          $diff_to_end = (70-$n_elements)*24 + 116;
                          $acc .= str_repeat(' ',$diff_to_end)."A\r\n".$ElementsData['tipo'];
                          $n_elements = 0;
                       } elseif ($ctrl_tipo == 0) {
                          $acc = $ElementsData['tipo'];
                       }
                       foreach ($ElementsData as $key=>$value){
                           $rs_elemento = CreaElemento($key,$value,$ElementsData['tipo']);
                           if (!empty($rs_elemento)){ // se � un elemento valido
                              $acc .= $rs_elemento;
                              $n_elements++;
                           }
                           if ($n_elements == 70 ){
                              // salta sulla riga successiva
                              $acc .= str_repeat(' ',116)."A\r\n".$ElementsData['tipo'];
                              $n_elements = 0;
                           }
                       }
                       $ctrl_tipo = $ElementsData['tipo'];
               }
               $diff_to_end = (70-$n_elements)*24 + 116;
               $acc .= str_repeat(' ',$diff_to_end)."A\r\n";
               return $acc;
               }

      function Record_2($D) // TRACCIATO RECORD DI DETTAGLIO OPERAZIONI CON SOGGETTI TITOLARI DI PARTITA IVA
               {
               function CreaElemento($k,$v,$t){
                      $acc = '';
                      if ($t == 1) { // clienti
                           $tipo = 'CL';
                           switch ($k) {
                                  case 'progressivo':
                                  $acc = $tipo.'001001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'codfis':
                                  $acc = $tipo.'002001'.substr(str_pad($v,16,' '),0,16);
                                  break;
                                  case 'pariva':
                                  $acc = $tipo.'003001'.substr(str_pad($v,16,' '),0,16);
                                  break;
                                  case 'imponibile':
                                  $acc = $tipo.'004001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'imposta':
                                  $acc = $tipo.'004002'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'nonimp':
                                  $acc = $tipo.'005001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'esente':
                                  $acc = $tipo.'006001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                           }
                      } else { //fornitori
                           $tipo = 'FR';
                           switch ($k) {
                                  case 'progressivo':
                                  $acc = $tipo.'001001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'codfis':
                                  $acc = $tipo.'002001'.substr(str_pad($v,16,' '),0,16);
                                  break;                                                                   case 'pariva':
                                  $acc = $tipo.'003001'.substr(str_pad($v,16,' '),0,16);
                                  break;
                                  case 'imponibile':
                                  $acc = $tipo.'004001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'imposta':
                                  $acc = $tipo.'004002'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'nonimp':
                                  $acc = $tipo.'006001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'esente':
                                  $acc = $tipo.'007001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                           }
                  }
                  return $acc;
               }
               $n_elements = 0;
               $ctrl_tipo = 0;
               foreach ($D as $ElementsData){
                       if ($ctrl_tipo < $ElementsData['tipo'] and $ctrl_tipo != 0) { // non � lo stesso partner precedente e non � il primo
                          $diff_to_end = (70-$n_elements)*24 + 116;
                          $acc .= str_repeat(' ',$diff_to_end)."A\r\n".$ElementsData['tipo'];
                          $n_elements = 0;
                       } elseif ($ctrl_tipo == 0) {
                          $acc = $ElementsData['tipo'];
                       }
                       foreach ($ElementsData as $key=>$value){
                           $rs_elemento = CreaElemento($key,$value,$ElementsData['tipo']);
                           if (!empty($rs_elemento)){ // se � un elemento valido
                              $acc .= $rs_elemento;
                              $n_elements++;
                           }
                           if ($n_elements == 70 ){
                              // salta sulla riga successiva
                              $acc .= str_repeat(' ',116)."A\r\n".$ElementsData['tipo'];
                              $n_elements = 0;
                           }
                       }
                       $ctrl_tipo = $ElementsData['tipo'];
               }
               $diff_to_end = (70-$n_elements)*24 + 116;
               $acc .= str_repeat(' ',$diff_to_end)."A\r\n";
               return $acc;
               }

      function Record_3($D) // TRACCIATO RECORD DI DETTAGLIO OPERAZIONI CON SOGGETTI NON RESIDENTI PRIVI DI CODICE FISCALE
               {
               function CreaElemento($k,$v,$t){
                      $acc = '';
                      if ($t == 1) { // clienti
                           $tipo = 'CL';
                           switch ($k) {
                                  case 'progressivo':
                                  $acc = $tipo.'001001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'codfis':
                                  $acc = $tipo.'002001'.substr(str_pad($v,16,' '),0,16);
                                  break;
                                  case 'pariva':
                                  $acc = $tipo.'003001'.substr(str_pad($v,16,' '),0,16);
                                  break;
                                  case 'imponibile':
                                  $acc = $tipo.'004001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'imposta':
                                  $acc = $tipo.'004002'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'nonimp':
                                  $acc = $tipo.'005001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'esente':
                                  $acc = $tipo.'006001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                           }
                      } else { //fornitori
                           $tipo = 'FR';
                           switch ($k) {
                                  case 'progressivo':
                                  $acc = $tipo.'001001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'codfis':
                                  $acc = $tipo.'002001'.substr(str_pad($v,16,' '),0,16);
                                  break;                                                                   case 'pariva':
                                  $acc = $tipo.'003001'.substr(str_pad($v,16,' '),0,16);
                                  break;
                                  case 'imponibile':
                                  $acc = $tipo.'004001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'imposta':
                                  $acc = $tipo.'004002'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'nonimp':
                                  $acc = $tipo.'006001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                                  case 'esente':
                                  $acc = $tipo.'007001'.substr(str_pad($v,16,' ',STR_PAD_LEFT),0,16);
                                  break;
                           }
                  }
                  return $acc;
               }
               $n_elements = 0;
               $ctrl_tipo = 0;
               foreach ($D as $ElementsData){
                       if ($ctrl_tipo < $ElementsData['tipo'] and $ctrl_tipo != 0) { // non � lo stesso partner precedente e non � il primo
                          $diff_to_end = (70-$n_elements)*24 + 116;
                          $acc .= str_repeat(' ',$diff_to_end)."A\r\n".$ElementsData['tipo'];
                          $n_elements = 0;
                       } elseif ($ctrl_tipo == 0) {
                          $acc = $ElementsData['tipo'];
                       }
                       foreach ($ElementsData as $key=>$value){
                           $rs_elemento = CreaElemento($key,$value,$ElementsData['tipo']);
                           if (!empty($rs_elemento)){ // se � un elemento valido
                              $acc .= $rs_elemento;
                              $n_elements++;
                           }
                           if ($n_elements == 70 ){
                              // salta sulla riga successiva
                              $acc .= str_repeat(' ',116)."A\r\n".$ElementsData['tipo'];
                              $n_elements = 0;
                           }
                       }
                       $ctrl_tipo = $ElementsData['tipo'];
               }
               $diff_to_end = (70-$n_elements)*24 + 116;
               $acc .= str_repeat(' ',$diff_to_end)."A\r\n";
               return $acc;
               }

      function Record_9($T) //TRACCIATO RECORD DI CODA
               {
               $this->CFContribuente = substr(str_pad($T['codfis'],16,' '),0,16);
               $this->PIContribuente = substr(str_pad($T['pariva'],11,'0',STR_PAD_LEFT),0,11);
               if (isset($T['sesso'])){
                  $this->AltriDati = substr(str_pad($T['cognome'],26,' '),0,26).
                                  substr(str_pad($T['nome'],25,' '),0,25).
                                  substr($T['sesso'],0,1).
                                  substr(str_pad($T['datnas'],8,' '),0,8).
                                  substr(str_pad($T['luonas'],40,' '),0,40).
                                  substr(str_pad($T['pronas'],2,' '),0,2).str_repeat(' ',112);
               } else {
                  $this->AltriDati = str_repeat(' ',102).
                                   substr(str_pad($T['ragsoc'],70,' '),0,70).
                                   substr(str_pad($T['sedleg'],40,' '),0,40).
                                   substr(str_pad($T['proleg'],2,' '),0,2);
               }
               $this->Anno = substr(str_pad($T['anno'],4,'0'),0,4);
               return "0ART2147".$this->CFContribuente.$this->PIContribuente.$this->AltriDati.
                      str_repeat(' ',16).$this->Anno.str_repeat('0',8).str_repeat(' ',16).
                      str_repeat('0',14).str_repeat(' ',1490)."A\r\n";

               }

      function creaFileART21($testa,$dati,$totali)
               {
               $accumulatore = '0'.$this->Record09($testa).$this->Record12($dati).
                               $this->Record3($totali).'9'.$this->Record09($testa);
               return $accumulatore;
               }
// --- FINE FUNZIONI COMUNICAZIONE OPERAZIONI RILEVANTI AI FINI IVA (ART21)
      }
?>