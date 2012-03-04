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
require("../../library/include/datlib.inc.php");
$admin_aziend=checkAdmin();

$titolo = "Bilancio IV direttiva CEE";
$oggi = date("Y-m-d");
$orainizio = date("Y-m-d H:i:s");
$message = "";
$errore ="";

$nromani = array(0=>"",1=>"I",2=>"II",3=>"III",4=>"IV",5=>"V",6=>"VI",7=>"VII",8=>"VIII",9=>"IX",10=>"X",11=>"XI",12=>"XII",13=>"XIII",14=>"XIV",15=>"XV",16=>"XVI",17=>"XVII",18=>"XVIII",19=>"XIX",20=>"XX");
$attdesc = array('A'=>array("Titolo"=>") CREDITI VERSO SOCI:"),'B'=>array("Titolo"=>") IMMOBILIZZAZIONI:",1=>" - Immobilizzazioni immateriali: ",2=>" - Immobilizzazioni materiali:",3=>" - Immobilizzazioni finanziarie: "),'C'=>array("Titolo"=>") ATTIVO CIRCOLANTE:",1=>" - Rimanenze: ",2=>" - Crediti: ",3=>" - Attivit&agrave; finanziarie: ",4=>" - Disponibilit&agrave; liquide: "),'D'=>array("Titolo"=>") RATEI E RISCONTI:"));
$pasdesc = array('A'=>array("Titolo"=>") PATRIMONIO NETTO:",1=>" - Capitale:",2=>" - Riserva da sovrapprezzo delle azioni:",3=>" - Riserva di rivalutazione:",4=>" - Riserva legale:",5=>" - Riserva per azioni proprie in portafoglio:",6=>" - Riserve statutarie:",7=>" - Altre riserve distintamente indicate:",8=>" - Utili (perdite) portati a nuovo:",9=>" - Utile (perdita) dell'esercizio:"),'B'=>array("Titolo"=>") FONDI RISCHI E ONERI:"),'C'=>array("Titolo"=>") TRATTAMENTO DI FINE RAPPORTO DI LAVORO SUBORDINATO:"),'D'=>array("Titolo"=>") DEBITI:"),'E'=>array("Titolo"=>") RATEI E RISCONTI:"));
$ecodesc = array('A'=>array("Titolo"=>") Valore della produzione:"),'B'=>array("Titolo"=>") Costi della produzione:"),'C'=>array("Titolo"=>") Proventi e oneri finanziari:"),'D'=>array("Titolo"=>") Rettifiche di valore di attivit finanziarie:"),'E'=>array("Titolo"=>") Proventi e oneri straordinari:"),'_'=>array("Titolo"=>") Risultato prima delle imposte:"));

//
// L'array $bin[] serve ad accumulare i valori calcolati, per poi poter generare
// una riclassificazione e gli indici relativi. L'array è associativo e la
// la chiave di accesso viene generata usando la variabile $code.
//
$bil = array();
$code = "";

//
// Carica i dati del bilancio IV direttiva CEE
// Legge le linee del file
//
$data = array();
$descon = array();
$noclass = 'non riclassificato';
$lines=file('IVdirCEE.bil');
foreach($lines as $line) {
        $nuova = explode(';',$line,2);
        $descon[trim($nuova[0])] = $nuova[1];
        $data[] = trim($nuova[0]);
}
$data = array_slice($data,1);
if (!isset($_GET['gioini']))
    $_GET['gioini'] = "1";
if (!isset($_GET['mesini']))
    $_GET['mesini'] = "1";
if (!isset($_GET['annini']))
    $_GET['annini'] =   date("Y")-1;
if (!isset($_GET['giofin']))
    $_GET['giofin'] =  "31";
if (!isset($_GET['mesfin']))
    $_GET['mesfin'] =  "12";
if (!isset($_GET['annfin']))
    $_GET['annfin'] =  date("Y")-1;

//controllo i campi
if (!checkdate( $_GET['mesini'], $_GET['gioini'], $_GET['annini']))
    $message .= "La data ".$_GET['gioini']."-".$_GET['mesini']."-".$_GET['annini']." non &egrave; corretta!<br>";
if (!checkdate( $_GET['mesfin'], $_GET['giofin'], $_GET['annfin']))
    $message .= "La data ".$_GET['giofin']."-".$_GET['mesfin']."-".$_GET['annfin']." non &egrave; corretta!<br>";
    $utsini= mktime(0,0,0,$_GET['mesini'],$_GET['gioini'],$_GET['annini']);
    $utsdop= mktime(0,0,0,$_GET['mesini'],$_GET['gioini']-1,$_GET['annini']+1);
    $utsfin= mktime(0,0,0,$_GET['mesfin'],$_GET['giofin'],$_GET['annfin']);
    $datainizio = date("Ymd",$utsini);
    $datadopo = date("Ymd",$utsdop);
    $datafine = date("Ymd",$utsfin);
if ($utsini >= $utsfin)
    $message .="La data di inizio periodo dev'essere precedente alla data di fine periodo !<br>";
if (isset($_GET['stampa'])) {
    $locazione = "Location: stampa_bilcee.php?&bilini=".$datainizio."&bilfin=".$datafine;
    header($locazione);
    exit;
}
if (isset($_GET['Return'])) {
    header("Location:docume_finean.php");
    exit;
}
require("../../library/include/header.php");
$script_transl=HeadMain();
?>
<form method="GET">
<div align="center" class="FacetFormHeaderFont">Bilancio IV direttiva CEE</div>
<table border="0" cellpadding="3" cellspacing="1" class="FacetFormTABLE" align="center">
<tr>
<td colspan="2" class="FacetDataTD"  style="color: red;">
<?php
if (! $message == "") {
    print "$message";
}
?>
</td>
</tr>
<tr><td class="FacetFieldCaptionTD">Data Inizio Periodo &nbsp;</td>
<td class="FacetDataTD" colspan=3>
<?php
// select del giorno
print "\t <select name=\"gioini\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
for( $counter = 1; $counter <= 31; $counter++ ) {
    $selected = "";
    if($counter ==  $_GET['gioini'])
       $selected = "selected";
    print "\t\t <option value=\"$counter\" $selected >$counter</option>\n";
}
print "\t </select>\n";
// select del mese
echo "\t <select name=\"mesini\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
for( $counter = 1; $counter <= 12; $counter++ ) {
     $selected = "";
     if($counter == $_GET['mesini'])
        $selected = "selected";
        $nome_mese = ucwords(strftime("%B", mktime (0,0,0,$counter,1,0)));
        echo "\t\t <option value=\"$counter\"  $selected >$nome_mese</option>\n";
}
echo "\t </select>\n";
// select del anno
            print "\t <select name=\"annini\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
            for( $counter = 2003; $counter <= 2030; $counter++ )
                {
                $selected = "";
                if($counter == $_GET['annini'])
                        $selected = "selected";
                print "\t\t <option value=\"$counter\"  $selected >$counter</option>\n";
                }
            print "\t </select>\n";
         ?>
    </td>
  </tr>
  <tr>
    <td class="FacetFieldCaptionTD">Data Fine Periodo &nbsp;</td>
    <td class="FacetDataTD"  colspan=3 >
         <?php
            // select del giorno
            print "\t <select name=\"giofin\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
            for( $counter = 1; $counter <= 31; $counter++ )
                {
                $selected = "";
                if($counter ==  $_GET['giofin'])
                        $selected = "selected";
                print "\t\t <option value=\"$counter\" $selected >$counter</option>\n";
                }
            print "\t </select>\n";
            // select del mese
            echo "\t <select name=\"mesfin\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
            for( $counter = 1; $counter <= 12; $counter++ )
                {
                $selected = "";
                if($counter == $_GET['mesfin'])
                        $selected = "selected";
                $nome_mese = ucwords(strftime("%B", mktime (0,0,0,$counter,1,0)));
                echo "\t\t <option value=\"$counter\"  $selected >$nome_mese</option>\n";
                }
            echo "\t </select>\n";
            // select del anno
            print "\t <select name=\"annfin\" class=\"FacetSelect\" onchange=\"this.form.submit()\">\n";
            for( $counter = 2003; $counter <= 2030; $counter++ )
                {
                $selected = "";
                if($counter == $_GET['annfin'])
                        $selected = "selected";
               print "\t\t <option value=\"$counter\"  $selected >$counter</option>\n";
                }
            print "\t </select>\n";
         ?>
    </td>
  </tr>
<?php
if ($message == "")
  {
    echo "<tr><td class=\"FacetFieldCaptionTD\"> </td><td align=\"right\" nowrap class=\"FacetFooterTD\"><input type=\"submit\" name=\"Return\" value=\"Indietro\"> <input type=\"submit\" name=\"visualizza\" value=\"VISUALIZZA L'ANTEPRIMA !\"> </td></tr>";
  }
echo "</table>";
//
if (isset($_GET['visualizza']) and $message == "")
  {
    $where = "datreg between '$datainizio' and '$datafine' and caucon <> 'CHI' and caucon <> 'APE' or (caucon = 'APE' and datreg between '$datainizio' and '$datadopo') group by codcon ";
    $orderby = " codcon ";
    $rs_castel = gaz_dbi_dyn_query("codcon, ragso1,".$gTables['clfoco'].".descri AS descri, SUM(import*(darave='D')-import*(darave='A')) AS saldo, ceedar, ceeave", $gTables['rigmoc']."
                                   LEFT JOIN ".$gTables['tesmov']." ON ".$gTables['rigmoc'].".id_tes = ".$gTables['tesmov'].".id_tes
                                   LEFT JOIN ".$gTables['clfoco']." ON ".$gTables['rigmoc'].".codcon = ".$gTables['clfoco'].".codice
                                   LEFT JOIN ".$gTables['anagra']." ON ".$gTables['anagra'].".id = ".$gTables['clfoco'].".id_anagra", $where, $orderby);
    $ctrlnum = gaz_dbi_num_rows($rs_castel);
    if ($ctrlnum > 0)
      {
        //procedura per la creazione dell'array dei conti per la riclassificazione...
        while($castel = gaz_dbi_fetch_array($rs_castel))
          {
            if ($castel["saldo"] > 0) // se l'eccedenza è in dare
              {
                if (! in_array(trim($castel['ceedar']),$data)) // se non e' riclassificato
                  {
                    // vedo se c'è la riclassificazione sul mastro
                    $mastro = gaz_dbi_get_row($gTables['clfoco'],"codice",substr($castel['codcon'],0,3)."000000");
                    $castel['ceedar']=trim($mastro['ceedar']);
                    if (! in_array($castel['ceedar'],$data)) // se non e' riclassificato neanche il mastro
                      {
                        $errore .= 'Il conto '.$castel["codcon"]." ".$castel["descri"].' non &egrave; stato riclassificato per l\'eccedenza in dare! <br>' ;
                        $castel['ceedar']=trim($castel['codcon']);
                      }
                  }
                $conti[$castel['codcon']] = array($castel["saldo"],$castel["descri"],$castel["ceedar"]);
              }
            if ($castel["saldo"] < 0) //se l'eccedenza è in avere
              {
                if(! in_array(trim($castel['ceeave']),$data))
                  {
                    // vedo se c'è la riclassificazione sul mastro
                    $mastro = gaz_dbi_get_row($gTables['clfoco'],"codice",substr($castel['codcon'],0,3)."000000");
                    $castel['ceeave']=trim($mastro['ceeave']);
                    if (! in_array(trim($castel['ceeave']),$data)) //se non e' riclassificato neanche il mastro
                      {
                        $errore .= 'Il conto '.$castel["codcon"]." ".$castel["descri"].' non &egrave; stato riclassificato per l\'eccedenza in avere! <br>' ;
                        $castel['ceeave']=trim($castel['codcon']);
                      }
                  }
                $conti[$castel['codcon']] = array($castel["saldo"],$castel["descri"],$castel["ceeave"]);
              }
          }
        $contiassoc = array();
        foreach ($conti as $value)
          {
            if (! array_key_exists($value[2],$contiassoc))
               $contiassoc[$value[2]] = $value[0];
            else
               $contiassoc[$value[2]] += $value[0];
          }
        ksort($contiassoc);
        //array conti creato chiave con codice e valore con saldo totale!
        // calcolo l'utile o la perdita (conto economico) e ricreo gli array attivita,passivita,economico.
        $economico = array();
        $attivo = array();
        $passivo = array();
        $risulta = array();
        foreach ($contiassoc as $key => $value)
          {
            $ctrlett = substr($key,1,1);
            $ctrlrom = substr($key,2,2);
            $ctrltipcon = substr($key,0,1);
            switch($ctrltipcon)
              {
                case 'E':
                case 4:
                case 3:
                if (trim($ctrlett) == '')
                  {
                    $ctrlett='_';
                  }
                $economico = $economico + array($key=>$value);
                $risulta[$ctrlett][$ctrlrom][$key] = -$value;
                break;
                case 'A':
                case 1:
                $attivo[$ctrlett][$ctrlrom][$key] = $value;
                break;
                case 'P':
                case 2:
                $passivo[$ctrlett][$ctrlrom][$key] = -$value;
                break;
              }
          }
        //
        // aggiungo l'utile(perdita) sul relativo conto e riclassifico
        //
        $passivo['A']['09']['PA09000'] = -array_sum($economico);
        ksort($passivo);
        ksort($risulta);

        $totrom =0.00;
        $totlet =0.00;
        $totale =0.00;
        echo "<div><center><b>ANTEPRIMA BILANCIO IV direttiva CEE AL ".$_GET['giofin']."-".$_GET['mesfin']."-".$_GET['annfin']."</b></CENTER></div>\n";
        echo "<table class=\"Tlarge\">";
        echo "<tr><td colspan=\"4\">Questo bilancio, riclassificato secondo la IV direttiva CEE, &egrave; stato generato leggendo i movimenti compresi nel periodo selezionato escludendo tutti quelli di apertura e chiusura, unica eccezione &egrave; l'apertura effettuata entro l'anno successivo alla data impostata come inizio periodo.</td></tr>\n";

        if ($errore != "" )
          {
            echo "<tr><td colspan=\"4\" style=\"color: red;\">Sono stati riscontrati i seguenti errori che non ne giustificano la stampa ma la sola visualizzazione: <br></td></tr>\n";
            echo "<tr><td colspan=\"4\" style=\"color: red;\">".$errore."</td></tr>\n";
          }
        else
          {
            echo "<tr><td colspan=\"4\" align=\"center\"><input type=\"submit\" name=\"stampa\" value=\"STAMPA IL BILANCIO CEE !\"></TD></TR>\n";
          }
        echo "<tr><td colspan=\"4\"><hr></TD></TR>\n";
        echo "<tr><TD><hr></TD><TD align=\"center\" class=\"FacetFormHeaderFont\">SITUAZIONE PATRIMONIALE AL ".$_GET['giofin']."-".$_GET['mesfin']."-".$_GET['annfin']."</TD><td colspan=\"2\"><hr></TD></TR>\n";
        echo "<tr><TD align=\"left\"  class=\"FacetFormHeaderFont\" style=\"color: blue;\">ATTIVO</TD><td colspan=\"3\"></TD></TR>\n";
        foreach ($attivo as $keylet => $vallet)
          {
            if (! key_exists($keylet,$attdesc))
              {
                $keylet = strtoupper($noclass);
                $attdesc[$keylet]['Titolo']= '';
                $attdesc[$keylet][0]= ucfirst($noclass);
              }
            echo "<tr><TD align=\"left\" class=\"FacetFormHeaderFont\">".$keylet.$attdesc[$keylet]['Titolo']." </TD><td colspan=\"3\"></TD></TR>\n";
            foreach ($vallet as $keyrom => $valrom)
              {
                if (! key_exists($keyrom,$attdesc))
                  {
                    $attdesc[$keylet][intval($keyrom)]= '';
                  }
                echo "<tr><TD align=\"left\">".$nromani[intval($keyrom)].$attdesc[$keylet][intval($keyrom)]." </TD><td colspan=\"3\"></TD></TR>\n";
                foreach ($valrom as $key => $value)
                  {
                    $conto = substr($key,4,3);
                    if ($conto == 0)
                      {
                        $conto = "";
                      }
                    else
                      {
                        $conto=intval($conto);
                      }
                    $totrom +=$value;
                    $totlet +=$value;
                    $totale +=$value;
                    if($key < 100000000)  //controllo per i conti non classificati
                      {
                        if($value > 0)
                          {
                            $stampaval = number_format($value,2,'.','');
                          }
                        else
                          {
                            $stampaval = "(".number_format(-$value,2,'.','').")";
                          }
                        echo "<tr><td align=\"right\">".$conto.substr($key,7,1).") </td><td>".$descon[$key]."</td><td>Euro</td><td align=\"right\">".$stampaval."</td></tr>\n";
                        //
                        // Salva dentro $bil[];
                        //
                        $code = "a".$keylet.$keyrom.$conto.substr($key,7,1);
                        $bil[$code] = round ($value);
                      }
                    else
                      {
                        if($value > 0)
                          {
                            $stampaval = number_format($value,2,'.','');
                          }
                        else
                          {
                            $stampaval = "(".number_format(-$value,2,'.','').")";
                          }
                        $descricon=gaz_dbi_get_row($gTables['clfoco'],"codice",$key);
                        echo "<tr><td></td><td style=\"color: red;\">\"".$key." - ".$descricon["descri"]."\" non riclassificato</td><td>Euro</td><td align=\"right\">".$stampaval."</td></tr>\n";
                      }
                  }
                if($totrom > 0)
                  {
                    $stampaval = number_format($totrom,2,'.','');
                  }
                else
                  {
                    $stampaval = "(".number_format(-$totrom,2,'.','').")";
                  }
                echo "<tr><td></td><td align=\"right\"> Totale ".$nromani[intval($keyrom)]."</td><td>Euro</td><td align=\"right\"> ".$stampaval."</td></tr>\n";
                //
                // Salva dentro $bil[];
                //
                $code = "a".$keylet.$keyrom;
                $bil[$code] = round ($totrom);
                //
                $totrom=0.00;
              }
            if($totlet > 0)
              {
                $stampaval = number_format($totlet,2,'.','');
              }
            else
              {
                $stampaval = "(".number_format(-$totlet,2,'.','').")";
              }
            echo "<tr><td></td><td align=\"right\" class=\"FacetFormHeaderFont\"> Totale ".$keylet." </td><td>Euro</td><td align=\"right\" class=\"FacetFormHeaderFont\"> ".$stampaval."</td></tr>\n";
            //
            // Salva dentro $bil[];
            //
            $code = "a".$keylet;
            $bil[$code] = round ($totlet);
            //
            $totlet=0.00;
          }
        echo "<tr><td align=\"center\" colspan=\"2\"></td><td colspan=\"2\"><hr></td></tr>";
        echo "<tr><td align=\"right\" colspan=\"2\" class=\"FacetFormHeaderFont\" style=\"color: blue;\"> TOTALE DELL'ATTIVO </td><td>Euro</td><td align=\"right\" class=\"FacetFormHeaderFont\" style=\"color: blue;\">".number_format($totale,2,'.','')."</td></tr>";
        $totale=0.00;
        echo "<tr><td colspan=\"4\"><hr></TD></TR>\n";
        echo "<tr><TD align=\"left\"  class=\"FacetFormHeaderFont\" style=\"color: brown;\">PASSIVO</TD><td colspan=\"3\"></TD></TR>";
        foreach ($passivo as $keylet => $vallet)
          {
            if (! key_exists($keylet,$pasdesc))
              {
                $keylet = strtoupper($noclass);
                $pasdesc[$keylet]['Titolo']= '';
                $pasdesc[$keylet][0]= ucfirst($noclass);
              }
            echo "<tr><TD align=\"left\" class=\"FacetFormHeaderFont\">".$keylet.$pasdesc[$keylet]['Titolo']." </TD><td colspan=\"3\"></TD></TR>\n";
            foreach ($vallet as $keyrom => $valrom)
              {
                if (! key_exists($keyrom,$pasdesc))
                  {
                    $pasdesc[$keylet][intval($keyrom)]= '';
                  }
                if ($keyrom != 0)
                  {
                    echo "<tr><TD align=\"left\">".$nromani[intval($keyrom)].$pasdesc[$keylet][intval($keyrom)]." </TD><td colspan=\"3\"></TD></TR>\n";
                  }
                foreach ($valrom as $key => $value)
                  {
                    $conto = substr($key,4,3);
                    if ($conto == 0)
                      {
                        $conto = "";
                      }
                    else
                      {
                        $conto=intval($conto);
                      }
                    $totrom +=$value;
                    $totlet +=$value;
                    $totale +=$value;
                    if($value > 0)
                      {
                        $stampaval = number_format($value,2,'.','');
                      }
                    else
                      {
                        $stampaval = "(".number_format(-$value,2,'.','').")";
                      }
                    if($key < 100000000)  //controllo per i conti non classificati
                      {
                        echo "<tr><td align=\"right\">".$conto.substr($key,7,1).") </td><td>".$descon[$key]."</td><td>Euro</td><td align=\"right\">".$stampaval."</td></tr>\n";
                        //
                        // Salva dentro $bil[];
                        //
                        $code = "p".$keylet.$keyrom.$conto.substr($key,7,1);
                        $bil[$code] = round ($value);
                      }
                    else
                      {
                        $descricon=gaz_dbi_get_row($gTables['clfoco'],"codice",$key);
                        echo "<tr><td></td><td style=\"color: red;\">\"".$key." - ".$descricon["descri"]."\" non riclassificato</td><td>Euro</td><td align=\"right\">".$stampaval."</td></tr>\n";
                      }
                  }
                if($totrom > 0)
                  {
                    $stampaval = number_format($totrom,2,'.','');
                  }
                else
                  {
                    $stampaval = "(".number_format(-$totrom,2,'.','').")";
                  }
                echo "<tr><td></td><td align=\"right\"> Totale ".$nromani[intval($keyrom)]."</td><td>Euro</td><td align=\"right\"> ".$stampaval."</td></tr>\n";
                //
                // Salva dentro $bil[];
                //
                $code = "p".$keylet.$keyrom;
                $bil[$code] = round ($totrom);
                $totrom=0.00;
              }
            if($totlet > 0)
              {
                $stampaval = number_format($totlet,2,'.','');
              }
            else
              {
                $stampaval = "(".number_format(-$totlet,2,'.','').")";
              }
            echo "<tr><td></td><td align=\"right\" class=\"FacetFormHeaderFont\"> Totale ".$keylet." </td><td>Euro</td><td align=\"right\" class=\"FacetFormHeaderFont\"> ".$stampaval."</td></tr>\n";
            //
            // Salva dentro $bil[];
            //
            $code = "p".$keylet;
            $bil[$code] = round ($totlet);
            $totlet=0.00;
          }
        echo "<tr><td align=\"center\" colspan=\"2\"></td><td colspan=\"2\"><hr></td></tr>";
        echo "<tr><td align=\"right\" colspan=\"2\" class=\"FacetFormHeaderFont\" style=\"color: brown;\"> TOTALE DEL PASSIVO </td><td>Euro</td><td align=\"right\" class=\"FacetFormHeaderFont\" style=\"color: brown;\">".number_format($totale,2,'.','')."</td></tr>\n";
        $totale=0.00;
        echo "<tr><td colspan=\"4\"><hr></TD></TR>\n";
        echo "<tr><TD><hr></TD><TD align=\"center\" class=\"FacetFormHeaderFont\">CONTO ECONOMICO DAL ".$_GET['gioini']."-".$_GET['mesini']."-".$_GET['annini']." AL ".$_GET['giofin']."-".$_GET['mesfin']."-".$_GET['annfin']."</TD><td colspan=\"2\"><hr></TD></TR>";
        echo "<tr><TD align=\"left\"  class=\"FacetFormHeaderFont\" style=\"color: orange;\">CONTO ECONOMICO</TD><td colspan=\"3\"></TD></TR>";
        foreach ($risulta as $keylet => $vallet)
          {
            if (! key_exists($keylet,$ecodesc))
              {
                $keylet = strtoupper($noclass);
                $ecodesc[$keylet]['Titolo']= '';
                $ecodesc[$keylet][0]= ucfirst($noclass);
              }
            echo "<tr><TD align=\"left\" class=\"FacetFormHeaderFont\">".$keylet.$ecodesc[$keylet]['Titolo']." </TD><td colspan=\"3\"></TD></TR>\n";
            foreach ($vallet as $keyrom => $valrom)
              {
                foreach ($valrom as $key => $value)
                  {
                    $conto = substr($key,4,3);
                    if ($conto == 0)
                      {
                        $conto = "";
                      }
                    else
                      {
                        $conto=intval($conto);
                      }
                    $totrom +=$value;
                    $totlet +=$value;
                    $totale +=$value;
                    if($value > 0)
                      {
                        $stampaval = number_format($value,2,'.','');
                      }
                    else
                      {
                        $stampaval = "(".number_format(-$value,2,'.','').")";
                      }
                    if($key < 100000000) //controllo per i conti non classificati
                      {
                        echo "<tr><td align=\"right\">".$conto.substr($key,7,1).") </td><td>".$descon[$key]."</td><td>Euro</td><td align=\"right\">".$stampaval."</td></tr>\n";
                        //
                        // Salva dentro $bil[];
                        //
                        $code = "e".$keylet.$keyrom.$conto.substr($key,7,1);
                        $bil[$code] = round ($value);
                      }
                    else
                      {
                        $descricon=gaz_dbi_get_row($gTables['clfoco'],"codice",$key);
                        echo "<tr><td></td><td style=\"color: red;\">\"".$key." - ".$descricon["descri"]."\" non riclassificato</td><td>Euro</td><td align=\"right\">".$stampaval."</td></tr>\n";
                      }
                  }
                if($totrom > 0)
                    $stampaval = number_format($totrom,2,'.','');
                else
                    $stampaval = "(".number_format(-$totrom,2,'.','').")";
                echo "<tr><td></td><td align=\"right\"> Totale ".$nromani[intval($keyrom)]."</td><td>Euro</td><td align=\"right\"> ".$stampaval."</td></tr>\n";
                //
                // Salva dentro $bil[];
                //
                $code = "e".$keylet.$keyrom;
                $bil[$code] = round ($totrom);
                $totrom=0.00;
              }
            if($totlet > 0)
                $stampaval = number_format($totlet,2,'.','');
            else
                $stampaval = "(".number_format(-$totlet,2,'.','').")";
            echo "<tr><td></td><td align=\"right\" class=\"FacetFormHeaderFont\"> Totale ".$keylet." </td><td>Euro</td><td align=\"right\" class=\"FacetFormHeaderFont\"> ".$stampaval."</td></tr>\n";
            //
            // Salva dentro $bil[];
            //
            $code = "e".$keylet;
            $bil[$code] = round ($totlet);
            $totlet=0.00;
          }
        if($totale > 0)
            $stampaval = number_format($totale,2,'.','');
        else
            $stampaval = "(".number_format(-$totale,2,'.','').")";
        echo "<tr><td align=\"center\" colspan=\"2\"></td><td colspan=\"2\"><hr></td></tr>";
        echo "<tr><td align=\"right\" colspan=\"2\" class=\"FacetFormHeaderFont\" style=\"color: orange;\"> UTILE(PERDITA) DI ESERCIZIO </td><td>Euro</td><td align=\"right\" class=\"FacetFormHeaderFont\" style=\"color: orange;\">".$stampaval."</td></tr>\n";
        $totale=0.00;
      }
    echo "</table>\n";
    //
    // Determina alcune voci di bilancio di cui mancano i totali.
    //
    $bil["aB031"]  = $bil["aB031a"]  + $bil["aB031b"]  + $bil["aB031c"]  + $bil["aB031d"];
    $bil["aB032"]  = $bil["aB032a"]  + $bil["aB032b"]  + $bil["aB032c"]  + $bil["aB032d"];
    $bil["eB009"]  = $bil["eB009a"]  + $bil["eB009b"]  + $bil["eB009c"]  + $bil["eB009d"] + $bil["eB009e"];
    $bil["eB0010"] = $bil["eB0010a"] + $bil["eB0010b"] + $bil["eB0010c"] + $bil["eB0010d"];
    $bil["eC0016"] = $bil["eC0016a"] + $bil["eC0016b"] + $bil["eC0016c"] + $bil["eC0016d"];
    $bil["eD0018"] = $bil["eD0018a"] + $bil["eD0018b"] + $bil["eD0018c"];
    $bil["eD0019"] = $bil["eD0019a"] + $bil["eD0019b"] + $bil["eD0019c"];
    //
    // Riclassificazione al valore aggiunto.
    //
    if ($errore == "" )
      {
        echo "<div><center><b>RICLASSIFICAZIONE AL VALORE AGGIUNTO AL ".$_GET['giofin']."-".$_GET['mesfin']."-".$_GET['annfin']."</b></CENTER></div>\n";
        echo "<table class=\"Tlarge\">";
        //
        $bil["Rv"] = $bil["eA001"];
        echo "<tr><td align=\"center\">A1</td><td align=\"center\"> </td>";
        echo "<td align=\"left\"><strong>Ricavi netti di vendita</strong></td>";
        echo "<td align=\"right\">".$bil["Rv"]."</td><td align=\"center\">Rv</td></tr>\n";
        //
        echo "<tr><td align=\"center\">A4</td><td align=\"center\">+</td>";
        echo "<td align=\"left\">costi patrimonializzati per lavori interni</td>";
        echo "<td align=\"right\">".$bil["eA004"]."</td><td align=\"center\">  </td></tr>\n";
        //
        echo "<tr><td align=\"center\">A2+A3</td><td align=\"center\">+</td>";
        echo "<td align=\"left\">variazione delle rimanenze di prodotti finiti, semilavorati, prodotti in lavorazione, lavorazioni in corso su ordinazioni</td>";
        echo "<td align=\"right\">".($bil["eA002"]+$bil["eA003"])."</td><td align=\"center\">  </td></tr>\n";
        //
        echo "<tr><td align=\"center\">A5</td><td align=\"center\">+</td>";
        echo "<td align=\"left\">altri ricavi e proventi di gestione</td>";
        echo "<td align=\"right\">".$bil["eA005"]."</td><td align=\"center\">  </td></tr>\n";
        //
        $bil["Vp"] = $bil["eA"];
        echo "<tr><td align=\"center\">A</td><td align=\"center\">=</td>";
        echo "<td align=\"left\"><strong>Valore della produzione</strong></td>";
        echo "<td align=\"right\">".$bil["Vp"]."</td><td align=\"center\">Vp</td></tr>\n";
        //
        echo "<tr><td align=\"center\">B6</td><td align=\"center\">-</td>";
        echo "<td align=\"left\">costi netti per l'acquisto di materie prime, sussidiarie e merci</td>";
        echo "<td align=\"right\">".-$bil["eB006"]."</td><td align=\"center\">  </td></tr>\n";
        //
        echo "<tr><td align=\"center\">B11</td><td align=\"center\">-</td>";
        echo "<td align=\"left\">variazione delle rimanenze di materie prime, sussidiarie, di consumo e merci</td>";
        echo "<td align=\"right\">".-$bil["eB0011"]."</td><td align=\"center\">  </td></tr>\n";
        //
        echo "<tr><td align=\"center\">B7+B8</td><td align=\"center\">-</td>";
        echo "<td align=\"left\">costi per servizi e per godimento beni di terzi</td>";
        echo "<td align=\"right\">".-($bil["eB007"]+$bil["eB008"])."</td><td align=\"center\">  </td></tr>\n";
        //
        echo "<tr><td align=\"center\">B7+B8</td><td align=\"center\">-</td>";
        echo "<td align=\"left\">altri costi diversi di gestione</td>";
        echo "<td align=\"right\">".-$bil["eB0014"]."</td><td align=\"center\">  </td></tr>\n";
        //
        $bil["Va"] = $bil["Vp"]-(-($bil["eB006"]+$bil["eB0011"]+$bil["eB007"]+$bil["eB008"]+$bil["eB0014"]));
        echo "<tr><td align=\"center\"> </td><td align=\"center\">=</td>";
        echo "<td align=\"left\"><strong>Valore aggiunto</strong></td>";
        echo "<td align=\"right\">".$bil["Va"]."</td><td align=\"center\">Va</td></tr>\n";
        //
        echo "<tr><td align=\"center\">B9</td><td align=\"center\">-</td>";
        echo "<td align=\"left\">costi del personale</td>";
        echo "<td align=\"right\">".-$bil["eB009"]."</td><td align=\"center\">  </td></tr>\n";
        //
        $bil["Mol"] = $bil["Va"]-(-$bil["eB009"]);
        echo "<tr><td align=\"center\"> </td><td align=\"center\">=</td>";
        echo "<td align=\"left\"><strong>Margine operativo lordo (EBITDA)</strong></td>";
        echo "<td align=\"right\">".$bil["Mol"]."</td><td align=\"center\">Mol</td></tr>\n";
        //
        echo "<tr><td align=\"center\">B10a+B10b+B10c</td><td align=\"center\">-</td>";
        echo "<td align=\"left\">ammortamenti</td>";
        echo "<td align=\"right\">".-($bil["eB0010a"]+$bil["eB0010b"]+$bil["eB0010c"])."</td><td align=\"center\">  </td></tr>\n";
        //
        echo "<tr><td align=\"center\">B10d</td><td align=\"center\">-</td>";
        echo "<td align=\"left\">svalutazione crediti</td>";
        echo "<td align=\"right\">".-$bil["eB0010d"]."</td><td align=\"center\">  </td></tr>\n";
        //
        echo "<tr><td align=\"center\">B12+B13</td><td align=\"center\">-</td>";
        echo "<td align=\"left\">accantonamenti a fondo rischi e oneri</td>";
        echo "<td align=\"right\">".-($bil["eB0012"]+$bil["eB0013"])."</td><td align=\"center\">  </td></tr>\n";
        //
        $bil["Ro"] = $bil["Mol"]-(-($bil["eB0010a"]+$bil["eB0010b"]+$bil["eB0010c"]+$bil["eB0010d"]+$bil["eB0012"]+$bil["eB0013"]));
        echo "<tr><td align=\"center\"> </td><td align=\"center\">=</td>";
        echo "<td align=\"left\"><strong>Reddito operativo (EBIT)</strong></td>";
        echo "<td align=\"right\">".$bil["Ro"]."</td><td align=\"center\">Ro</td></tr>\n";
        //
        echo "<tr><td align=\"center\">C</td><td align=\"center\">+</td>";
        echo "<td align=\"left\">risultato della gestione finanziaria</td>";
        echo "<td align=\"right\">".$bil["eC"]."</td><td align=\"center\">  </td></tr>\n";
        //
        echo "<tr><td align=\"center\">D</td><td align=\"center\">+</td>";
        echo "<td align=\"left\">risultato della gestione accessoria</td>";
        echo "<td align=\"right\">".$bil["eD"]."</td><td align=\"center\">  </td></tr>\n";
        //
        $bil["Rego"] = $bil["Ro"]+($bil["eC"]+$bil["eD"]);
        echo "<tr><td align=\"center\"> </td><td align=\"center\">=</td>";
        echo "<td align=\"left\"><strong>Risultato economico della gestione ordinaria</strong></td>";
        echo "<td align=\"right\">".$bil["Rego"]."</td><td align=\"center\"> </td></tr>\n";
        //
        echo "<tr><td align=\"center\">E19</td><td align=\"center\">+</td>";
        echo "<td align=\"left\">risultato della gestione straordinaria</td>";
        echo "<td align=\"right\">".$bil["eE0019"]."</td><td align=\"center\">  </td></tr>\n";
        //
        $bil["Rl"] = $bil["Rego"]+$bil["eE0019"];
        echo "<tr><td align=\"center\"> </td><td align=\"center\">=</td>";
        echo "<td align=\"left\"><strong>Risultato economico al lordo delle imposte</strong></td>";
        echo "<td align=\"right\">".$bil["Rl"]."</td><td align=\"center\">Rl</td></tr>\n";
        //
        $bil["Tx"] = $bil["e_0022"];
        echo "<tr><td align=\"center\">22</td><td align=\"center\">-</td>";
        echo "<td align=\"left\">imposte d'esercizio</td>";
        echo "<td align=\"right\">".$bil["e_0022"]."</td><td align=\"center\">Tx</td></tr>\n";
        //
        $bil["Re"] = $bil["Rl"]-(-$bil["e_0022"]);
        echo "<tr><td align=\"center\"> </td><td align=\"center\">=</td>";
        echo "<td align=\"left\"><strong>Utile o perdita d'esercizio</strong></td>";
        echo "<td align=\"right\">".$bil["Re"]."</td><td align=\"center\">Re</td></tr>\n";
        //
        echo "</table>\n";
      }
    //
    //// Costo del venduto.
    ////
    //if ($errore == "" )
    //  {
    //    echo "<div><center><b>COSTO DEL VENTUTO AL ".$_GET['giofin']."-".$_GET['mesfin']."-".$_GET['annfin']."</b></CENTER></div>\n";
    //    echo "<table class=\"Tlarge\">";
    //    //
    //    echo "<tr><td align=\"center\">c.e. B6</td><td align=\"center\">+</td>";
    //    echo "<td align=\"left\">acquisti di materie prime, sussidiarie, di consumo e merci</td>";
    //    echo "<td align=\"right\">".-$bil["eB006"]."</td><td align=\"center\">  </td></tr>\n";
    //    //
    //    echo "<tr><td align=\"center\">c.e. B7+B9+B10-B10d</td><td align=\"center\">+</td>";
    //    echo "<td align=\"left\">costi industriali (costi per servizi, costi del personale, ammortamenti e accantonamenti riferiti alla produzione)<br>esistenze iniziali di materie prime, sussidiarie, di consumo, di merci, di prodotti in lavorazione, di semilavorati e di prodotti finiti</td>";
    //    echo "<td align=\"right\">".-($bil["eB007"]+$bil["eB009"]+$bil["eB0010"]-$bil["eB0010d"])."</td><td align=\"center\">  </td></tr>\n";
    //    //
    //    echo "<tr><td align=\"center\">attivo CI</td><td align=\"center\">-</td>";
    //    echo "<td align=\"left\">rimanenze finali di materie prime, sussidiarie, di consumo, di merci, di prodotti in lavorazione, di semilavorati e di prodotti finiti</td>";
    //    echo "<td align=\"right\">".$bil["aC01"]."</td><td align=\"center\">  </td></tr>\n";
    //    //
    //    echo "<tr><td align=\"center\">c.e. A4</td><td align=\"center\">-</td>";
    //    echo "<td align=\"left\">costi patrimonializzati per lavori interni</td>";
    //    echo "<td align=\"right\">".-($bil["eA004"])."</td><td align=\"center\">  </td></tr>\n";
    //    //
    //    $bil["Cv"] = (-$bil["eB006"]+$bil["eB007"]+$bil["eB009"]+$bil["eB0010"]-$bil["eB0010d"])-$bil["aC01"]-(-$bil["eA004"]);
    //    echo "<tr><td align=\"center\"> </td><td align=\"center\">=</td>";
    //    echo "<td align=\"left\"><strong>Costo del venduto</strong></td>";
    //    echo "<td align=\"right\">".$bil["Cv"]."</td><td align=\"center\">Cv</td></tr>\n";
    //    //
    //    echo "</table>\n";
    //  }
    //
    // Dati per indici.
    //
    if ($errore == "" )
      {
        echo "<div><center><b>DATI PER GLI INDICI AL ".$_GET['giofin']."-".$_GET['mesfin']."-".$_GET['annfin']."</b></CENTER></div>\n";
        echo "<table class=\"Tlarge\">";
        //
        $bil["Rm"] = $bil["aC01"];
        echo "<tr><td align=\"center\">attivo CI</td>";
        echo "<td align=\"left\">Rimanenze</td>";
        echo "<td align=\"right\">".$bil["Rm"]."</td><td align=\"center\">Rm</td></tr>\n";
        //
        $bil["Df"] = $bil["aC02"];
        echo "<tr><td align=\"center\">attivo CII</td>";
        echo "<td align=\"left\">Disponibilità finanziarie</td>";
        echo "<td align=\"right\">".$bil["Df"]."</td><td align=\"center\">Df</td></tr>\n";
        //
        $bil["Dl"] = $bil["aC04"];
        echo "<tr><td align=\"center\">attivo CIV</td>";
        echo "<td align=\"left\">Disponibilità liquide</td>";
        echo "<td align=\"right\">".$bil["Dl"]."</td><td align=\"center\">Df</td></tr>\n";
        //
        $bil["Ac"] = $bil["aC"];
        echo "<tr><td align=\"center\">attivo C</td>";
        echo "<td align=\"left\">Attivo circolante</td>";
        echo "<td align=\"right\">".$bil["Ac"]."</td><td align=\"center\">Ac</td></tr>\n";
        //
        $bil["Im"] = $bil["aB"];
        echo "<tr><td align=\"center\">attivo B</td>";
        echo "<td align=\"left\">Immobilizzazioni</td>";
        echo "<td align=\"right\">".$bil["Im"]."</td><td align=\"center\">Im</td></tr>\n";
        //
        $bil["Ti"] = $bil["aA"]+$bil["aB"]+$bil["aC"]+$bil["aD"];
        echo "<tr><td align=\"center\">attivo A+B+C+D</td>";
        echo "<td align=\"left\">Totale impieghi</td>";
        echo "<td align=\"right\">".$bil["Ti"]."</td><td align=\"center\">Ti</td></tr>\n";
        //
        $bil["Rv"] = $bil["eA001"];
        echo "<tr><td align=\"center\">c.e. A1</td>";
        echo "<td align=\"left\">Ricavi di vendita</td>";
        echo "<td align=\"right\">".$bil["Rv"]."</td><td align=\"center\">Rv</td></tr>\n";
        //
        $bil["Cl"] = -$bil["eB009"];
        echo "<tr><td align=\"center\">c.e. B9</td>";
        echo "<td align=\"left\">Costi del lavoro</td>";
        echo "<td align=\"right\">".$bil["Cl"]."</td><td align=\"center\">Cl</td></tr>\n";
        //
        $bil["Am"] = -($bil["eB0010a"]+$bil["eB0010b"]+$bil["eB0010c"]);
        echo "<tr><td align=\"center\">c.e. B10a+B10b+B10c</td>";
        echo "<td align=\"left\">Ammortamenti</td>";
        echo "<td align=\"right\">".$bil["Am"]."</td><td align=\"center\">Am</td></tr>\n";
        //
        $bil["Cd"] = -$bil["pD"];
        echo "<tr><td align=\"center\">passivo D</td>";
        echo "<td align=\"left\">Capitale di debito (totale dei debiti a breve, a media e a lunga scadenza)</td>";
        echo "<td align=\"right\">".$bil["Cd"]."</td><td align=\"center\">Cd</td></tr>\n";
        //
        $bil["Cp"] = -($bil["pA"]-$bil["pA08"]-$bil["pA09"]);
        echo "<tr><td align=\"center\">passivo A-AVIII-AIX</td>";
        echo "<td align=\"left\">Capitale proprio</td>";
        echo "<td align=\"right\">".$bil["Cp"]."</td><td align=\"center\">Cp</td></tr>\n";
        //
        echo "<tr><td align=\"center\">c.e. 26</td>";
        echo "<td align=\"left\">Risultato economico d'esercizio</td>";
        echo "<td align=\"right\">".$bil["Re"]."</td><td align=\"center\">Re</td></tr>\n";
        //
        $bil["Tf"] = -($bil["pA"]+$bil["pB"]+$bil["pC"]+$bil["pD"]);
        echo "<tr><td align=\"center\">passivo A+B+C+D</td>";
        echo "<td align=\"left\">Totale fonti</td>";
        echo "<td align=\"right\">".$bil["Tf"]."</td><td align=\"center\">Tf</td></tr>\n";
        //
        $bil["Of"] = -($bil["eC0017"]);
        echo "<tr><td align=\"center\">c.e. C17</td>";
        echo "<td align=\"left\">Oneri finanziari</td>";
        echo "<td align=\"right\">".$bil["Of"]."</td><td align=\"center\">Of</td></tr>\n";
        ////
        //echo "<tr><td align=\"center\"> </td>";
        //echo "<td align=\"left\">Costo del venduto</td>";
        //echo "<td align=\"right\">".$bil["Cv"]."</td><td align=\"center\">Cv</td></tr>\n";
        //
        echo "<tr><td align=\"center\"> </td>";
        echo "<td align=\"left\">Valore aggiunto</td>";
        echo "<td align=\"right\">".$bil["Va"]."</td><td align=\"center\">Va</td></tr>\n";
        //
        echo "<tr><td align=\"center\"> </td>";
        echo "<td align=\"left\">Reddito operativo (EBIT)</td>";
        echo "<td align=\"right\">".$bil["Ro"]."</td><td align=\"center\">Ro</td></tr>\n";
        //
        echo "</table>\n";
      }
    //
    // Analisi per redditività.
    //
    if ($errore == "" )
      {
        echo "<div><center><b>ANALISI PER REDDITIVITÀ AL ".$_GET['giofin']."-".$_GET['mesfin']."-".$_GET['annfin']."</b></CENTER></div>\n";
        echo "<table class=\"Tlarge\">";
        //
        $bil["ROE"] = ($bil["Cp"] == 0 ? 0 : $bil["Re"]/$bil["Cp"]);
        echo "<tr><td align=\"center\">ROE (return on equity)</td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>utile netto d'esercizio<p><hr><p>capitale netto</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>Re<p><hr><p>Cp</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>".$bil["Re"]."<p><hr><p>".$bil["Cp"]."</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"right\">".$bil["ROE"]."</td>\n";
        //
        $bil["ROI"] = ($bil["Ti"] == 0 ? 0 : $bil["Ro"]/$bil["Ti"]);
        echo "<tr><td align=\"center\">ROI (return on investments)</td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>reddito operativo<p><hr><p>totale impieghi</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>Ro<p><hr><p>Ti</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>".$bil["Ro"]."<p><hr><p>".$bil["Ti"]."</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"right\">".$bil["ROI"]."</td>\n";
        //
        $bil["ROD"] = ($bil["Cd"] == 0 ? 0 : $bil["Of"]/$bil["Cd"]);
        echo "<tr><td align=\"center\">ROD (return on debts)</td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>oneri finanziari totali<p><hr><p>capitale di debito</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>Of<p><hr><p>Cd</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>".$bil["Of"]."<p><hr><p>".$bil["Cd"]."</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"right\">".$bil["ROD"]."</td>\n";
        //
        $bil["ROS"] = ($bil["Rv"] == 0 ? 0 : $bil["Ro"]/$bil["Rv"]);
        echo "<tr><td align=\"center\">ROS (return on sales)</td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>reddito operativo<p><hr><p>ricavi di vendita</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>Ro<p><hr><p>Rv</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>".$bil["Ro"]."<p><hr><p>".$bil["Rv"]."</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"right\">".$bil["ROS"]."</td>\n";
        //
        $bil["RotImp"] = ($bil["Ti"] == 0 ? 0 : $bil["Rv"]/$bil["Ti"]);
        echo "<tr><td align=\"center\">rotazione degli impieghi</td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>ricavi di vendita<p><hr><p>totale impieghi</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>Rv<p><hr><p>Ti</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>".$bil["Rv"]."<p><hr><p>".$bil["Ti"]."</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"right\">".$bil["RotImp"]."</td>\n";
        //
        $bil["Leverage"] = ($bil["Cp"] == 0 ? 0 : $bil["Ti"]/$bil["Cp"]);
        echo "<tr><td align=\"center\">Leverage</td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>totale impieghi<p><hr><p>capitale proprio</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>Ti<p><hr><p>Cp</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>".$bil["Ti"]."<p><hr><p>".$bil["Cp"]."</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"right\">".$bil["Leverage"]."</td>\n";
        //
        $bil["InGeNoCa"] = ($bil["Ro"] == 0 ? 0 : $bil["Re"]/$bil["Ro"]);
        echo "<tr><td align=\"center\">indice della gestione non caratteristica</td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>utile netto d'esercizio<p><hr><p>reddito operativo</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>Re<p><hr><p>Ro</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>".$bil["Re"]."<p><hr><p>".$bil["Ro"]."</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"right\">".$bil["InGeNoCa"]."</td>\n";
        //
        echo "</table>\n";
      }
    //
    // Analisi per redditività.
    //
    if ($errore == "" )
      {
        echo "<div><center><b>ANALISI PER PRODUTTIVITÀ AL ".$_GET['giofin']."-".$_GET['mesfin']."-".$_GET['annfin']."</b></CENTER></div>\n";
        echo "<table class=\"Tlarge\">";
        //
        $bil["Va/Ti"] = ($bil["Ti"] == 0 ? 0 : $bil["Va"]/$bil["Ti"]);
        echo "<tr><td align=\"center\">indice di produttività del capitale investito</td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>valore aggiunto<p><hr><p>totale impieghi</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>Va<p><hr><p>Ti</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>".$bil["Va"]."<p><hr><p>".$bil["Ti"]."</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"right\">".$bil["Va/Ti"]."</td>\n";
        //
        $bil["Cl/Rv"] = ($bil["Rv"] == 0 ? 0 : $bil["Cl"]/$bil["Rv"]);
        echo "<tr><td align=\"center\">incidenza del fattore lavoro</td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>costo del personale<p><hr><p>ricavi netti di vendita</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>Cl<p><hr><p>Rv</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>".$bil["Cl"]."<p><hr><p>".$bil["Rv"]."</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"right\">".$bil["Cl/Rv"]."</td>\n";
        //
        echo "</table>\n";
      }
    //
    // Analisi patrimoniale.
    //
    if ($errore == "" )
      {
        echo "<div><center><b>ANALISI PATRIMONIALE AL ".$_GET['giofin']."-".$_GET['mesfin']."-".$_GET['annfin']."</b></CENTER></div>\n";
        echo "<table class=\"Tlarge\">";
        //
        $bil["Im/Ti"] = ($bil["Ti"] == 0 ? 0 : $bil["Im"]/$bil["Ti"]);
        echo "<tr><td align=\"center\">rigidità degli impieghi</td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>immobilizzazioni<p><hr><p>totale impieghi</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>Im<p><hr><p>Ti</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>".$bil["Im"]."<p><hr><p>".$bil["Ti"]."</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"right\">".$bil["Im/Ti"]."</td>\n";
        //
        $bil["Ac/Ti"] = ($bil["Ti"] == 0 ? 0 : $bil["Ac"]/$bil["Ti"]);
        echo "<tr><td align=\"center\">elasticità degli impieghi</td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>attivo corrente<p><hr><p>totale impieghi</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>Ac<p><hr><p>Ti</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>".$bil["Ac"]."<p><hr><p>".$bil["Ti"]."</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"right\">".$bil["Ac/Ti"]."</td>\n";
        //
        $bil["Ac/Im"] = ($bil["Im"] == 0 ? 0 : $bil["Ac"]/$bil["Im"]);
        echo "<tr><td align=\"center\">indice di elasticità</td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>attivo corrente<p><hr><p>immobilizzazioni</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>Ac<p><hr><p>Im</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>".$bil["Ac"]."<p><hr><p>".$bil["Im"]."</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"right\">".$bil["Ac/Im"]."</td>\n";
        //
        $bil["Cp/Ti"] = ($bil["Ti"] == 0 ? 0 : $bil["Cp"]/$bil["Ti"]);
        echo "<tr><td align=\"center\">incidenza del capitale proprio (autonomia finanziaria)</td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>capitale proprio<p><hr><p>totale impieghi</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>Cp<p><hr><p>Ti</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>".$bil["Cp"]."<p><hr><p>".$bil["Ti"]."</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"right\">".$bil["Cp/Ti"]."</td>\n";
        //
        $bil["Cp/Cd"] = ($bil["Cd"] == 0 ? 0 : $bil["Cp"]/$bil["Cd"]);
        echo "<tr><td align=\"center\">grado di capitalizzazione</td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>capitale proprio<p><hr><p>capitale di debito complessivo</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>Cp<p><hr><p>Cd</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>".$bil["Cp"]."<p><hr><p>".$bil["Cd"]."</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"right\">".$bil["Cp/Cd"]."</td>\n";
        //
        echo "</table>\n";
      }
    //
    // Analisi finanziaria.
    //
    if ($errore == "" )
      {
        echo "<div><center><b>ANALISI FINANZIARIA AL ".$_GET['giofin']."-".$_GET['mesfin']."-".$_GET['annfin']."</b></CENTER></div>\n";
        echo "<table class=\"Tlarge\">";
        //
        $bil["Cp/Im"] = ($bil["Im"] == 0 ? 0 : $bil["Cp"]/$bil["Im"]);
        echo "<tr><td align=\"center\">indice di autocopertura delle immobilizzazioni</td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>capitale proprio<p><hr><p>immobilizzazioni</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>Cp<p><hr><p>Im</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>".$bil["Cp"]."<p><hr><p>".$bil["Im"]."</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"right\">".$bil["Cp/Im"]."</td>\n";
        //
        $bil["Rv/Ac"] = ($bil["Ac"] == 0 ? 0 : $bil["Rv"]/$bil["Ac"]);
        echo "<tr><td align=\"center\">indice di rotazione dell'attivo circolante</td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>ricavi di vendita<p><hr><p>attivo circolante</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>Rv<p><hr><p>Ac</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>".$bil["Rv"]."<p><hr><p>".$bil["Ac"]."</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"right\">".$bil["Rv/Ac"]."</td>\n";
        ////
        //$bil["Cv/Rm"] = ($bil["Rm"] == 0 ? 0 : $bil["Cv"]/$bil["Rm"]);
        //echo "<tr><td align=\"center\">indice di rotazione delle rimanenze</td>";
        //echo "<td align=\"center\">=</td>";
        //echo "<td align=\"center\"><p>costo del venduto<p><hr><p>rimanenze</p></td>";
        //echo "<td align=\"center\">=</td>";
        //echo "<td align=\"center\"><p>Cv<p><hr><p>Rm</p></td>";
        //echo "<td align=\"center\">=</td>";
        //echo "<td align=\"center\"><p>".$bil["Cv"]."<p><hr><p>".$bil["Rm"]."</p></td>";
        //echo "<td align=\"center\">=</td>";
        //echo "<td align=\"right\">".$bil["Cv/Rm"]."</td>\n";
        //
        $bil["Rv/Rm"] = ($bil["Rm"] == 0 ? 0 : $bil["Rv"]/$bil["Rm"]);
        echo "<tr><td align=\"center\">indice di rotazione delle scorte al valore di vendita</td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>ricavi di vendita<p><hr><p>rimanenze</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>Rv<p><hr><p>Rm</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"center\"><p>".$bil["Rv"]."<p><hr><p>".$bil["Rm"]."</p></td>";
        echo "<td align=\"center\">=</td>";
        echo "<td align=\"right\">".$bil["Rv/Rm"]."</td>\n";
        //
        echo "</table>\n";
      }
    ////
    //// Diag.
    ////
    //foreach ($bil as $x => $valor)
    //  {
    //    echo "<p>".$x." ".$valor."</p>";
    //  }
  }
?>
</form>
</body>
</html>