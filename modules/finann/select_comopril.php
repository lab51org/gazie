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
require("../../library/include/datlib.inc.php");

$admin_aziend=checkAdmin();
$msg = '';

require("../../library/include/check.inc.php");

if (!isset($_GET['anno'])) { //al primo accesso allo script suppongo che si debba produrre l'elenco per l'anno precedente
    $_GET['anno'] = date("Y")-1;
    $_GET['min_limit'] = 3;
}

function printTransact($transact,$error)
{
          global $script_transl,$admin_aziend;
          echo "<tr>\n
               <td class=\"FacetFormHeaderFont\" align=\"center\" colspan=\"10\">".$script_transl[2]."</td>
               </tr>\n";
          echo "<tr>";
          echo "<td align=\"right\" class=\"FacetDataTD\" >N.</td>";
          echo "<td colspan=\"3\" class=\"FacetDataTD\" >Rag.Sociale</td>";
          echo "<td colspan=\"3\" class=\"FacetDataTD\" >Indirizzo</td>";
          echo "<td colspan=\"2\" class=\"FacetDataTD\" >Comune</td>";
          echo "<td class=\"FacetDataTD\" >Prov.</td>";
          echo "</tr>\n";
          echo "<tr>";
          echo "<td align=\"right\" class=\"FacetDataTD\" ></td>";
          echo "<td align=\"center\" class=\"FacetDataTD\" >Tipo Operazione</td>";
          echo "<td class=\"FacetDataTD\" >Codice Fiscale Partita I.V.A.</td>";
          echo "<td class=\"FacetDataTD\" >N.Doc.</td>";
          echo "<td align=\"right\" class=\"FacetDataTD\" >Imponibile</td>";
          echo "<td align=\"right\" class=\"FacetDataTD\" >Imposta</td>";
          echo "<td align=\"right\" class=\"FacetDataTD\" >Non Imponibile</td>";
          echo "<td align=\"right\" class=\"FacetDataTD\" >Esente</td>";
          echo "<td align=\"right\" class=\"FacetDataTD\" >Totale</td>";
          echo "</tr>\n";
          foreach ($transact as $key=>$value ) {
               if (isset($error[$key])){
                  $class = ' class="FacetDataTDred" ';
               } else {
                  $class = ' ';
               }
               $totale = gaz_format_number($value['operazioni_imponibili'] +
                                           $value['imposte_addebitate']+
                                           $value['operazioni_nonimp']+
                                           $value['operazioni_esente']);
               echo "<tr>";
               echo "<td align=\"right\" $class>".$value['Date_Documenti']."</td>";
               echo "<td colspan=\"3\" $class>".$value['Rag_Sociale']."</td>";
               echo "<td colspan=\"3\" $class></td>";
               echo "<td colspan=\"2\" $class></td>";
               echo "<td $class></td>";
               echo "</tr>\n";
               echo "<tr>";
               echo "<td align=\"right\" $class></td>";
               echo "<td align=\"right\" $class>".$value['Tipologia']." </td>";
               echo "<td $class>".$value['Codice_Fiscale']." ".$value['Partita_IVA']."</td>";
               echo "<td $class>".$value['Num_Documenti']."</td>";
               echo "<td align=\"right\" $class>".gaz_format_number($value['operazioni_imponibili'])."</td>";
               echo "<td align=\"right\" $class>".gaz_format_number($value['imposte_addebitate'])."</td>";
               echo "<td align=\"right\" $class>".gaz_format_number($value['operazioni_nonimp'])."</td>";
               echo "<td align=\"right\" $class>".gaz_format_number($value['operazioni_esente'])."</td>";
               echo "<td align=\"right\" $class>$totale</td>";
               echo "</tr>\n";
               if (isset($error[$key])) {
                  foreach ($error[$key] as $val_err ) {
                          echo "<tr>";
                          echo "<td class=\"FacetDataTDred\" colspan=\"10\">".$val_err;
                          if (substr($value['Cod_Partner'],0,3) == $admin_aziend['mascli']) {
                             echo ", <a href='../vendit/admin_client";
                          } else {
                             echo ", <a href='../acquis/admin_fornit";
                          }
                          echo ".php?codice=".substr($value['Cod_Partner'],3,6)."&Update' target='_NEW'> $script_transl[20]</a><br /></td>
                               </tr>\n";
                  }
               }
          }
}

function getHeaderData()
{
      global $admin_aziend;
      // preparo il nome dell'azienda e faccio i controlli di errore
      $Testa['anno'] = $_GET['anno'];
      $Testa['pariva'] = $admin_aziend['pariva'];
      $Testa['codfis'] = $admin_aziend['codfis'];
      if ($admin_aziend['sexper'] == 'G') {
         // persona giuridica
         if (strlen($Testa['codfis']) <> 11) {
            $Testa['fatal_error'] = '';
         }
         if (empty($admin_aziend['ragso1']) and empty($admin_aziend['ragso2'])) {
            $Testa['fatal_error'] = '';
         } else {
            $Testa['ragsoc'] = strtoupper($admin_aziend['ragso1']." ".$admin_aziend['ragso2']);
         }
         if (empty($admin_aziend['citspe'])) {
            $Testa['fatal_error'] = '';
         } else {
            $Testa['sedleg'] = strtoupper($admin_aziend['citspe']);
         }
         if (strlen(trim($admin_aziend['prospe'])) < 2) {
            $Testa['fatal_error'] = '';
         } else {
            $Testa['proleg'] = strtoupper($admin_aziend['prospe']);
         }
      } elseif ($admin_aziend['sexper'] == 'F' or $admin_aziend['sexper'] == 'M') {
        // persona fisica
        $gn=substr($Testa['codfis'],9,2);
        if (($admin_aziend['sexper'] == 'M' and ($gn < 1 or $gn > 31))
            or
           ($admin_aziend['sexper'] == 'F' and ($gn < 41 or $gn > 71))) {
            $Testa['fatal_error'] = '';
        }
        $Testa['sesso'] = strtoupper($admin_aziend['sexper']);
        if (!empty($admin_aziend['legrap'])) {
            // persona fisica con cognome e nome non separati nel campo legale rappresentante
            $Testa['cognome'] = '';
            $Testa['nome'] = '';
            $line = strtoupper($admin_aziend['legrap']);
            $nuova = explode(' ',chop($line));
            $lenght = count($nuova);
            $middle = intval(($lenght+1)/2);
            for( $i = 0; $i < $lenght; $i++ ) {
                 if ($i < $middle) {
                    $Testa['cognome'] .= $nuova[$i]." ";
                 } else {
                    $Testa['nome'] .= $nuova[$i]." ";
                 }
            }
        } elseif(!empty($admin_aziend['ragso1']) and !empty($admin_aziend['ragso2'])) {
            // persona fisica con cognome e nome separati tra ragso1 e ragso2
            $Testa['cognome'] = strtoupper($admin_aziend['ragso1']);
            $Testa['nome'] = strtoupper($admin_aziend['ragso2']);
        } else {
            $Testa['fatal_error'] = '';
        }
        if (empty($admin_aziend['luonas'])) {
                $Testa['fatal_error'] = '';
        } else {
            $Testa['luonas'] = strtoupper($admin_aziend['luonas']);
        }
        if (strlen(trim($admin_aziend['pronas'])) < 2) {
                $Testa['fatal_error'] = '';
        } else {
            $Testa['pronas'] = strtoupper($admin_aziend['pronas']);
        }
        $d=substr($admin_aziend['datnas'],8,2);
        $m=substr($admin_aziend['datnas'],5,2);
        $Y=substr($admin_aziend['datnas'],0,4);
        if (checkdate($m, $d, $Y)) {
            $Testa['datnas'] = $d.$m.$Y;
        } else {
            $Testa['fatal_error'] = '';
        }
      } else {
        $Testa['fatal_error'] = '';
      }
      return $Testa;
}

function createRowsAndErrors($min_limit){
    global $gTables,$admin_aziend,$script_transl;
    $sqlquery= "SELECT ".$gTables['rigmoi'].".*, CONCAT(ragso1,' ',ragso2) AS ragsoc, sedleg,sexper,indspe,
               citspe,prospe,codfis,pariva,clfoco, numdoc, datreg, op_type,
               operat, SUM(impost - impost*2*(caucon LIKE '_NC')) AS imposta,".$gTables['rigmoi'].".id_tes AS idtes,
               SUM(imponi - imponi*2*(caucon LIKE '_NC')) AS imponibile FROM ".$gTables['rigmoi']."
               LEFT JOIN ".$gTables['tesmov']." ON ".$gTables['rigmoi'].".id_tes = ".$gTables['tesmov'].".id_tes
               LEFT JOIN ".$gTables['aliiva']." ON ".$gTables['rigmoi'].".codiva = ".$gTables['aliiva'].".codice
               LEFT JOIN ".$gTables['clfoco']." ON ".$gTables['tesmov'].".clfoco = ".$gTables['clfoco'].".codice
               LEFT JOIN ".$gTables['anagra']." ON ".$gTables['anagra'].".id = ".$gTables['clfoco'].".id_anagra
               WHERE YEAR(datdoc) = ".intval($_GET['anno'])." AND ( clfoco LIKE '".$admin_aziend['masfor']."%' OR clfoco LIKE '".$admin_aziend['mascli']."%')
               GROUP BY id_tes, tipiva
               ORDER BY regiva, datreg";
    $result = gaz_dbi_query($sqlquery);
    $castel_transact= array();
    $error_transact= array();
    if (gaz_dbi_num_rows($result) > 0 ) {
       // inizio creazione array righi ed errori
       $progressivo = 0;
       $ctrl_id = 0;
       $value_imponi = 0.00;
       $value_impost = 0.00;
       while ($row = gaz_dbi_fetch_array($result)) {
         if ($row['operat'] == 1) {
                $value_imponi = $row['imponibile'];
                $value_impost = $row['imposta'];
         } elseif ($row['operat'] == 2) {
                $value_imponi = -$row['imponibile'];
                $value_impost = -$row['imposta'];
         } else {
                $value_imponi = 0;
                $value_impost = 0;
         }
         if ($ctrl_id <> $row['idtes']) {
            // se il precedente movimento non ha raggiunto l'importo lo elimino
            if (isset($castel_transact[$ctrl_id]) && $castel_transact[$ctrl_id]['operazioni_imponibili'] < $min_limit ) {
               unset ($castel_transact[$ctrl_id]);
               unset ($error_transact[$ctrl_id]);
            }
                 // inizio controlli su CF e PI
                 $nuw = new check_VATno_TAXcode();
                 $resultpi = $nuw->check_VAT_reg_no($row['pariva']);
                 if( strlen(trim($row['codfis'])) == 11) {
                     $resultcf = $nuw->check_VAT_reg_no($row['codfis']);
                     if (intval($row['codfis']) == 0) {
                        $error_transact[$row['idtes']][] = $script_transl[7];
                     } elseif ($row['sexper'] != 'G') {
                        $error_transact[$row['idtes']][] = $script_transl[8];
                     }
                 } else {
                     $resultcf = $nuw->check_TAXcode($row['codfis']);
                     if (empty($row['codfis'])) {
                         $error_transact[$row['idtes']][] = $script_transl[9];
                     } elseif ($row['sexper'] == 'G' and
                         empty($resultcf)) {
                        $error_transact[$row['idtes']][] = $script_transl[10];
                     } elseif ($row['sexper'] == 'M' and
                         empty($resultcf) and
                         (intval(substr($row['codfis'],9,2)) > 31 or
                         intval(substr($row['codfis'],9,2)) < 1) ) {
                         $error_transact[$row['idtes']][] = $script_transl[11];
                     } elseif ($row['sexper'] == 'F' and
                         empty($resultcf) and
                         (intval(substr($row['codfis'],9,2)) > 71 or
                         intval(substr($row['codfis'],9,2)) < 41) ) {
                         $error_transact[$row['idtes']][] = $script_transl[12];
                     } elseif (! empty ($resultcf)) {
                         $error_transact[$row['idtes']][] = $script_transl[13];
                     }
                 }
                 if (! empty ($resultpi)) {
                    $error_transact[$row['idtes']][] = $script_transl[14];
                    $error_transact['fatal_error'] = '';
                 } elseif (empty($row['pariva'])) {
                    $error_transact[$row['idtes']][] = $script_transl[15];
                    $error_transact['fatal_error'] = '';
                 }
                 // fine controlli su CF e PI
                 $castel_transact[$row['idtes']] = array(
                      'Cod_Partner'=> $row['clfoco'],
                      'Progressivo'=> $row['idtes'],
                      'Num_Documenti'=> $row['numdoc'],
                      'Date_Documenti'=> gaz_format_date($row['datreg']),
                      'Rag_Sociale'=> $row['ragsoc'],
                      'Partita_IVA'=> $row['pariva'],
                      'Tipologia'=> $row['op_type'],
                      'Codice_Fiscale'=> $row['codfis']
                 );
                 if ($row['sexper'] == 'G'){
                        $castel_transact[$row['idtes']]['persona_fisica'] = '';
                 } else {
                        $castel_transact[$row['idtes']]['persona_fisica'] = 'X';
                 }
                 if ($row['op_type'] == 0 && substr($row['clfoco'],0,3) == $admin_aziend['masfor'] ){
                     $castel_transact[$row['idtes']]['Tipologia'] = 3;
                 } elseif ($row['op_type'] == 0 && substr($row['clfoco'],0,3) == $admin_aziend['mascli'] ) {
                     $castel_transact[$row['idtes']]['Tipologia'] = 1;
                 }
                 if (!empty($row['sedleg'])){
                     if ( preg_match("/([\w\,\.\s]+)([0-9]{5})[\s]+([\w\s\']+)\(([\w]{2})\)/",$row['sedleg'],$regs)) {
                        $castel_transact[$row['idtes']]['Indirizzo'] = $regs[1];
                        $castel_transact[$row['idtes']]['Comune'] = $regs[3];
                        $castel_transact[$row['idtes']]['Provincia'] = $regs[4];
                     } else {
                       $error_transact[$row['idtes']][] = $script_transl[16];
                     }
                 }
                 // inizio valorizzazione imponibile,imposta,senza_iva,art8
                 $castel_transact[$row['idtes']]['operazioni_imponibili'] = 0;
                 $castel_transact[$row['idtes']]['imposte_addebitate'] = 0;
                 $castel_transact[$row['idtes']]['operazioni_esente'] = 0;
                 $castel_transact[$row['idtes']]['operazioni_nonimp'] = 0;
                 switch ($row['tipiva']) {
                        case 'I':
                        case 'D':
                             $castel_transact[$row['idtes']]['operazioni_imponibili'] = $value_imponi;
                             $castel_transact[$row['idtes']]['imposte_addebitate'] = $value_impost;
                             if ($value_impost == 0){  //se non c'� imposta il movimento � sbagliato
                                $error_transact[$row['idtes']][] = $script_transl[17];
                             }
                        break;
                        case 'E':
                             $castel_transact[$row['idtes']]['operazioni_esente'] = $value_imponi;
                             if ($value_impost != 0){  //se c'� imposta il movimento � sbagliato
                                $error_transact[$row['idtes']][] = $script_transl[18];
                             }
                        break;
                        case 'N':
                        //case 'C':
                             $castel_transact[$row['idtes']]['operazioni_nonimp'] = $value_imponi;
                             if ($value_impost != 0){  //se c'� imposta il movimento � sbagliato
                                $error_transact[$row['idtes']][] = $script_transl[18];
                             }
                        break;
                 }
              } else { //movimenti successivi al primo ma dello stesso id
                 // inizio addiziona valori imponibile,imposta,esente,non imponibile
                 switch ($row['tipiva']) {
                        case 'I':
                        case 'D':
                             $castel_transact[$row['idtes']]['operazioni_imponibili'] += $value_imponi;
                             $castel_transact[$row['idtes']]['imposte_addebitate'] += $value_impost;
                             if ($value_impost == 0){  //se non c'� imposta il movimento � sbagliato
                                $error_transact[$row['idtes']][] = $script_transl[17];
                             }
                        break;
                        case 'E':
                             $castel_transact[$row['idtes']]['operazioni_esente'] = $value_imponi;
                             if ($value_impost != 0){  //se c'� imposta il movimento � sbagliato
                                $error_transact[$row['idtes']][] = $script_transl[18];
                             }
                        break;
                        case 'N':
                        //case 'C':
                             $castel_transact[$row['idtes']]['operazioni_nonimp'] = $value_imponi;
                             if ($value_impost != 0){  //se c'� imposta il movimento � sbagliato
                                $error_transact[$row['idtes']][] = $script_transl[18];
                             }
                        break;
                 }
                 // fine addiziona valori imponibile,imposta,esente,non imponibile
              }
              // fine valorizzazione imponibile,imposta,esente,non imponibile
              $ctrl_id = $row['idtes'];

       }
    } else {
              $error_transact[0] = $script_transl[21];
    }
    // fine creazione array righi ed errori
    return array($castel_transact,$error_transact);
}

if (isset($_GET['pdf'])) {
    header("Location: stampa_elencf.php?anno=".$_GET['anno']."&partner=".$_GET['partner']);
    exit;
}

if (isset($_GET['file_agenzia'])) {
      require("../../library/include/agenzia_entrate.inc.php");
      function prepareAgenziaEntrateData($data,$tipo)
      {
               $pr = 1;
               $el = 2;
               $tot['imponibile'] = 0;
               $tot['imposta'] = 0;
               $tot['nonimp'] = 0;
               $tot['esente'] = 0;
               foreach ($data as $value){
                       $acc[$pr]['tipo'] = $tipo;
                       $acc[$pr]['progressivo'] = $pr;
                       if (!empty($value['Codice_Fiscale'])) {
                          $acc[$pr]['codfis'] = strtoupper($value['Codice_Fiscale']);
                          $el ++;
                       }
                       $acc[$pr]['pariva'] = $value['Partita_IVA'];
                       if ($value['operazioni_imponibili'] <> 0) {
                          $acc[$pr]['imponibile'] = round($value['operazioni_imponibili']);
                          $tot['imponibile'] += $acc[$pr]['imponibile'];
                          $el ++;
                       }
                       if ($value['imposte_addebitate'] <> 0) {
                          $acc[$pr]['imposta'] = round($value['imposte_addebitate']);
                          $tot['imposta'] += $acc[$pr]['imposta'];
                          $el ++;
                       }
                       if ($value['operazioni_nonimp'] <> 0) {
                          $acc[$pr]['nonimp'] = round($value['operazioni_nonimp']);
                          $tot['nonimp'] += $acc[$pr]['nonimp'];
                          $el ++;
                       }
                       if ($value['operazioni_esente'] <> 0) {
                          $acc[$pr]['esente'] = round($value['operazioni_esente']);
                          $tot['esente'] += $acc[$pr]['esente'];
                          $el ++;
                       }
                       $acc[$pr]['elementi'] = $el;
                       $el = 2;
                       $tot['numero'] = $pr;
                       $pr ++;
               }
               // --- fine preparazione
               return array($acc,$tipo => $tot);
      }
      $annofornitura = date("y");
      // Impostazione degli header per l'opozione "save as" dello standard input che verr� generato
      header('Content-Type: text/x-ecf');
      header("Content-Disposition: attachment; filename=".$admin_aziend['codfis'].'_'.$_GET['anno'].".ecf");
      header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');// per poter ripetere l'operazione di back-up pi� volte.
      if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
         header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
         header('Pragma: public');
      } else {
         header('Pragma: no-cache');
      }
      // --- preparo gli array da passare alla classe AgenziaEntrate a secondo della scelta effettuata
      $Testa = getHeaderData();
      $agenzia = new AgenziaEntrate;
      $content = $agenzia->creaFileART21($Testa,$Dati);
      print $content;
      exit;
}

require("../../library/include/header.php");
$script_transl = HeadMain();
echo "<form method=\"GET\">\n";
echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".ucfirst($script_transl[0])."</div>\n";
echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"FacetFormTABLE\" align=\"center\">\n";
if (!empty($msg)) {
    $message = "";
    $rsmsg = array_slice( explode('+',chop($msg)),0,-1);
    foreach ($rsmsg as $value){
            $message .= $script_transl['error']."! -> ";
            $rsval = explode('-',chop($value));
            foreach ($rsval as $valmsg){
                    $message .= $script_transl[$valmsg]." ";
            }
            $message .= "<br />";
    }
    echo '<tr><td colspan="5" class="FacetDataTDred">'.$message."</td></tr>\n";
}
echo "<tr><td class=\"FacetFieldCaptionTD\">".$script_transl[1]."</td>
     <td class=\"FacetDataTD\">\n";
echo "<select name=\"partner\" class=\"FacetSelect\">\n";
for( $counter =  1; $counter <=  3; $counter++ ){
      $selected = '';
      if($_GET['partner'] == $counter){
         $selected = "selected";
      }
      if ($counter == 1 ){
         echo "\t\t <option value=\"".$counter."\" $selected >".$script_transl[2]."</option>\n";
      } elseif($counter == 2) {
         echo "\t\t <option value=\"".$counter."\" $selected >".$script_transl[3]."</option>\n";
      } else {
         echo "\t\t <option value=\"".$counter."\" $selected >".$script_transl[2]." &amp; ".$script_transl[3]."</option>\n";
      }
}
echo "</select></td></tr>\n";
echo "<tr><td class=\"FacetFieldCaptionTD\">".$script_transl[5]."</td><td class=\"FacetDataTD\" colspan=\"3\">";
echo "\t <select name=\"anno\" class=\"FacetSelect\" >\n";
for( $counter =  date("Y")-10; $counter <=  date("Y")+10; $counter++ ){
    $selected = "";
    if($counter == $_GET['anno'])
            $selected = "selected";
    echo "\t\t <option value=\"$counter\"  $selected >$counter</option>\n";
}
echo "\t </select></td></tr>\n";
echo "<tr>\n
     <td class=\"FacetFieldCaptionTD\"><input type=\"submit\" name=\"Return\" value=\"".ucfirst($script_transl['return'])."\"></td>\n
     <td align=\"right\" class=\"FacetFooterTD\"><input type=\"submit\" name=\"view\" value=\"".ucfirst($script_transl['view'])."\"></td>\n
     </tr>\n";
echo "</table>\n";
if (isset($_GET['view'])) {
   $queryData = createRowsAndErrors(3000);
   $Testa = getHeaderData();
   if (!isset ($error_transact[0])) {
       echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".$script_transl['view']."</div>";
       echo "<table class=\"Tlarge\">";
       echo "<tr>";
       echo "<td colspan=\"2\"></td>";
       echo "<td colspan=\"1\" class=\"FacetFieldCaptionTD\">$script_transl[24]</td>";
       echo "<td colspan=\"4\" class=\"FacetDataTD\">".$Testa['codfis']."</td>";
       echo "<td colspan=\"3\"></td>";
       echo "</tr>\n";
       echo "<tr>";
       echo "<td colspan=\"2\"></td>";
       echo "<td colspan=\"1\" class=\"FacetFieldCaptionTD\">$script_transl[25]</td>";
       echo "<td colspan=\"4\" class=\"FacetDataTD\">".$Testa['pariva']."</td>";
       echo "<td colspan=\"3\"></td>";
       echo "</tr>\n";
       if (!isset($Testa['sesso'])){ // � una persona giuridica
          echo "<tr>";
          echo "<td colspan=\"2\"></td>";
          echo "<td colspan=\"1\" class=\"FacetFieldCaptionTD\">$script_transl[32]</td>";
          echo "<td colspan=\"4\" class=\"FacetDataTD\">".$Testa['ragsoc']."</td>";
          echo "<td colspan=\"3\"></td>";
          echo "</tr>\n";
          echo "<tr>";
          echo "<td colspan=\"2\"></td>";
          echo "<td colspan=\"1\" class=\"FacetFieldCaptionTD\">$script_transl[33]</td>";
          echo "<td colspan=\"4\" class=\"FacetDataTD\">".$Testa['sedleg']."</td>";
          echo "<td colspan=\"3\"></td>";
          echo "</tr>\n";
          echo "<tr>";
          echo "<td colspan=\"2\"></td>";
          echo "<td colspan=\"1\" class=\"FacetFieldCaptionTD\">$script_transl[34]</td>";
          echo "<td colspan=\"4\" class=\"FacetDataTD\">".$Testa['proleg']."</td>";
          echo "<td colspan=\"3\"></td>";
          echo "</tr>\n";
       } else {     // persona fisica
          echo "<tr>";
          echo "<td colspan=\"2\"></td>";
          echo "<td colspan=\"1\" class=\"FacetFieldCaptionTD\">$script_transl[26]</td>";
          echo "<td colspan=\"4\" class=\"FacetDataTD\">".$Testa['cognome']."</td>";
          echo "<td colspan=\"3\"></td>";
          echo "</tr>\n";
          echo "<tr>";
          echo "<td colspan=\"2\"></td>";
          echo "<td colspan=\"1\" class=\"FacetFieldCaptionTD\">$script_transl[27]</td>";
          echo "<td colspan=\"4\" class=\"FacetDataTD\">".$Testa['nome']."</td>";
          echo "<td colspan=\"3\"></td>";
          echo "</tr>\n";
          echo "<tr>";
          echo "<td colspan=\"2\"></td>";
          echo "<td colspan=\"1\" class=\"FacetFieldCaptionTD\">$script_transl[28]</td>";
          echo "<td colspan=\"4\" class=\"FacetDataTD\">".$Testa['sesso']."</td>";
          echo "<td colspan=\"3\"></td>";
          echo "</tr>\n";
          echo "<tr>";
          echo "<td colspan=\"2\"></td>";
          echo "<td colspan=\"1\" class=\"FacetFieldCaptionTD\">$script_transl[29]</td>";
          echo "<td colspan=\"4\" class=\"FacetDataTD\">".$Testa['datnas']."</td>";
          echo "<td colspan=\"3\"></td>";
          echo "</tr>\n";
          echo "<tr>";
          echo "<td colspan=\"2\"></td>";
          echo "<td colspan=\"1\" class=\"FacetFieldCaptionTD\">$script_transl[30]</td>";
          echo "<td colspan=\"4\" class=\"FacetDataTD\">".$Testa['luonas']."</td>";
          echo "<td colspan=\"3\"></td>";
          echo "</tr>\n";
          echo "<tr>";
          echo "<td colspan=\"2\"></td>";
          echo "<td colspan=\"1\" class=\"FacetFieldCaptionTD\">$script_transl[31]</td>";
          echo "<td colspan=\"4\" class=\"FacetDataTD\">".$Testa['pronas']."</td>";
          echo "<td colspan=\"3\"></td>";
          echo "</tr>\n";
       }
       if (!empty($error_transact) and $_GET['anno'] > 2007){
               echo "<tr>\n
                    <td class=\"FacetDataTDred\" colspan=\"10\">$script_transl[19]:</td>
                    </tr>\n";
       } elseif (isset($Testa['fatal_error'])) {
              echo "<tr>\n
                   <td align=\"center\" class=\"FacetFieldCaptionTD\" colspan=\"4\"><input type=\"submit\" name=\"pdf\" value=\"PDF\"></td>\n
                   <td align=\"center\" class=\"FacetDataTDred\" colspan=\"6\">$script_transl[23]</td>\n
                   </tr>\n";
       } else {
              echo "<tr>\n
                   <td align=\"center\" class=\"FacetFieldCaptionTD\" colspan=\"4\"><input type=\"submit\" name=\"pdf\" value=\"PDF\"></td>\n
                   <td a7lign=\"center\" class=\"FacetFieldCaptionTD\" colspan=\"6\"><input type=\"submit\" name=\"file_agenzia\" value=\"File Internet (ECF)\"></td>\n
                   </tr>\n";
       }
       printTransact($queryData[0],$queryData[1]);
       echo "</table>";
   } else {
       echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".$error_transact[0]."</div>";
   }
}
?>
</form>
</body>
</html>