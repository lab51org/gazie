<?php

/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2015 - Antonio De Vincentiis Montesilvano (PE)
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
require("../../library/include/datlib.inc.php");

$admin_aziend = checkAdmin();
if (!ini_get('safe_mode')) { //se me lo posso permettere...
   ini_set('memory_limit', '128M');
   gaz_set_time_limit(0);
}
if (!isset($_GET['orderby'])) {
   header("Location: " . $_SERVER['HTTP_REFERER']);
   exit;
}
require("../../config/templates/report_template.php");
$form['id_anagra'] = $_GET['id_anagra'];
$form['clfr'] = $_GET['clfr'];
$form['id_agente'] = $_GET['id_agente'];
$form['orderby'] = $_GET['orderby'];
$form['aperte_tutte'] = $_GET['aperte_tutte'];
/* ENRICO FEDELE */
/* strftime effettivamente formatta sulla base della lingua del server, ma se l'italiano non è installato, comunque la data sarà in inglese
  stessa cosa dicasi per il fuso orario (sul mio NAS non so perchè se stampo l'ora, mi rendo conto che il fuso orario è quello cinese!!!
  mi chiedo perchè è stato usato mktime invete di lasciare che sia il sistema a prendere data/ora correnti con time(), forse per tentare di
  bypassare il problema del fuso orario?
  Per avere sicuramente data e ora nella lingua impostata dall'utente, occorrerebbe predisporre degli apposity array di localizzazione
  $date = array("month" => array(1=> "Gennaio", 2 => "Febbraio", ...., 12 => "Dicembre"),
  "day"	 => array(1=> "Lunedì",  2 => "Martedì", ...., 7 => "Domenica"));
  da richiamare poi con $date["month"][date("n")]
  $date["day"][date("N")]
 */
$luogo_data = $admin_aziend['citspe'] . ", lì " . ucwords(strftime("%d %B %Y", mktime(0, 0, 0, date("m"), date("d"), date("Y"))));
/* ENRICO FEDELE */
$item_head = array('top' => array(array('lun' => 80, 'nam' => 'Descrizione'),
        array('lun' => 25, 'nam' => 'Numero Conto')
    )
);
$select_id_anagra = new selectPartner("id_anagra");
/* ENRICO FEDELE */
/* Modifico larghezza e intestazione delle colonne */
$title = array('luogo_data' => $luogo_data,
    'title' => ($form['aperte_tutte'] == 0 ? "PARTITE APERTE" : "ESTRATTO CONTO")
    . ($form['id_agente'] == 0 ? "" : " - AGENTE: " . $select_id_anagra->queryNomeAgente($form['id_agente'])),
    'hile' => array(/* array('lun' => 45, 'nam' => 'Cliente'), */
        array('lun' => 20, 'nam' => 'ID Partita'),
        array('lun' => 65, 'nam' => 'Descrizione'),
        array('lun' => 32, 'nam' => 'N.Doc.'),
        array('lun' => 13, 'nam' => 'D. Doc.'),
        array('lun' => 13, 'nam' => 'D. Reg.'),
        array('lun' => 15, 'nam' => 'Dare'),
        array('lun' => 15, 'nam' => 'Avere'),
        array('lun' => 13, 'nam' => 'Scad.')
    )
);
/* ENRICO FEDELE */
$aRiportare = array('top' => array(array('lun' => 166, 'nam' => 'da riporto : '),
        array('lun' => 20, 'nam' => '')
    ),
    'bot' => array(array('lun' => 166, 'nam' => 'a riportare : '),
        array('lun' => 20, 'nam' => '')
    )
);
$pdf = new Report_template();
$pdf->setVars($admin_aziend, $title);
$pdf->setFooterMargin(22);
$pdf->setTopMargin(43);
$pdf->SetFillColor(160, 255, 220);
$pdf->setRiporti('');
$pdf->AddPage();
$config = new Config;
$scdl = new Schedule;
if (!empty($form['id_anagra'])) {
//   $cosaStampare = $select_id_anagra->queryClfoco($form['id_anagra'], ($form['clfr'] == 0 ? $admin_aziend['mascli'] : $admin_aziend['masfor'])); // anagrafe selezionata
   $cosaStampare = $form['id_anagra'];
} else {// voglio tutti
   ini_set('memory_limit', '128M'); // mi occorre tanta memoria
   gaz_set_time_limit(0);  // e tanto tempo
   if ($form['clfr'] == 0) {
      $cosaStampare = $admin_aziend['mascli']; // clienti
   } else {
      $cosaStampare = $admin_aziend['masfor']; // fornitori
   }
}

//   $cosaStampare = "103000974";
$rs = $scdl->getPartite($form['orderby'], $cosaStampare, $form['id_agente']);
if ($rs->num_rows > 0) {
   $ctrl_partner = 0;
   $ctrl_id_tes = 0;
   $ctrl_paymov = 0;
   $nuova_anagrafe = true;

   /* ENRICO FEDELE */
   /* Inizializzo le variabili per il totale */
   $tot_dare = 0;
   $tot_avere = 0;
   /* ENRICO FEDELE */

   $tot_diff_anagrafe = 0;

   $mv = gaz_dbi_fetch_array($rs);
   while ($mv) {
      $partner = '';
      $id_tes = '';
      $paymov = '';
      $status_del = false;
      $dati_partite = array();
      $ctrl_partner = $mv["clfoco"];
      $ctrl_id_tesdoc_ref = $mv["id_tesdoc_ref"];
      $tot_diff_tmp = 0;
      $tot_avere_tmp = 0;
      do {
         $dati_partite[] = $mv;
         if ($mv['id_rigmoc_pay'] == 0) {
            /* Incremento il totale del dare */
            $tot_diff_tmp += $mv['amount'];
         } else {
            $tot_diff_tmp -= $mv['amount'];
         }
         $mv = gaz_dbi_fetch_array($rs);
      } while ($mv && ($mv["clfoco"] == $ctrl_partner) && ($mv["id_tesdoc_ref"] == $ctrl_id_tesdoc_ref));
      if ($tot_diff_tmp == 0 && $form['aperte_tutte'] == 0) {// la partita è chiusa ed io voglio solo le partite aperte
         continue;
      }
      $tot_diff_anagrafe+=$tot_diff_tmp;

      $primo = true;
      foreach ($dati_partite as $mv_tmp) {
         if ($primo) {
            $partner = $mv_tmp["ragsoc"];
            $id_tes = $mv_tmp["id_tes"];
            $mv_tmp["datdoc"] = gaz_format_date($mv_tmp["datdoc"]);
            $paymov = $mv_tmp["id_tesdoc_ref"];
            if ($tot_diff_tmp != 0) {
               $status_cl = false;
            } else {
               $status_cl = true;
            }
            if ($nuova_anagrafe) {
               $pdf->SetFont('helvetica', '', 12);
               $pdf->SetFillColor(255, 214, 255);
               $pdf->Cell(186, 4, $partner, 'LTBR', 1, 'C', true, '', 1);
               $nuova_anagrafe = false;
            }
            $primo = false;
         } else {
            $mv_tmp['descri'] = '';
            $mv_tmp['numdoc'] = '';
            $mv_tmp['datdoc'] = '';
            $id_tes = '';
            $partner = '';
            $status_descr = '';
            $status_del = true;
         }
         $pdf->SetFont('helvetica', '', 6);
         $pdf->Cell(20, 4, $paymov, 1, 0, 'R', false, '', 2);
         $pdf->Cell(65, 4, $mv_tmp['descri'], 1, 0, 'C', false, '', 1);
         $pdf->Cell(32, 4, $mv_tmp["numdoc"], 1, 0, 'R', false);
         /* ENRICO FEDELE */
         $pdf->Cell(13, 4, $mv_tmp["datdoc"], 1, 0, 'C', false);
         $pdf->Cell(13, 4, gaz_format_date($mv_tmp["datreg"]), 1, 0, 'C', false);
         if ($mv_tmp['id_rigmoc_pay'] == 0) {
            /* Incremento il totale del dare */
            $tot_dare += $mv_tmp['amount'];
            $pdf->Cell(15, 4, gaz_format_number($mv_tmp['amount']), 1, 0, 'R', false);
            $pdf->Cell(15, 4, '', 1, 0, 'R', false);
         } else {
            /* Incremento il totale dell'avere, e decremento quello del dare */
            $tot_avere += $mv_tmp['amount'];
//               $tot_dare -= $mv_tmp['amount'];
            /* Modifico la larghezza delle celle */
            $pdf->Cell(15, 4, '', 1, 0, 'R', false);
            $pdf->Cell(15, 4, gaz_format_number($mv_tmp['amount']), 1, 0, 'R', false);
         }
         /* ENRICO FEDELE */
         /* Modifico la larghezza della cella */
         $pdf->Cell(13, 4, gaz_format_date($mv_tmp["expiry"]), 1, 1, 'C', false);
      }
      $ctrl_id_tes = $mv["id_tes"];
      $ctrl_paymov = $mv["id_tesdoc_ref"];

      /* ENRICO FEDELE */
      /* Stampo una riga vuota sottile per separare leggermente il totale e metterlo in evidenza */
      $pdf->SetFillColor(235, 235, 235);
      $pdf->SetFont('helvetica', '', 1);
      $pdf->Cell(186, 1, '', 1, 1, 'C', true);

      /* Stampo la riga del totale, in grassetto italico "BI" */
      if ($tot_diff_tmp != 0) {  // partita chiusa
         $pdf->SetFillColor(255, 255, 60);
      } else {// partita aperta
         $pdf->SetFillColor(0, 255, 60);
      }
      $pdf->SetFont('helvetica', 'BI', 6);
      $pdf->Cell(173, 4, 'SALDO PARTITA', 1, 0, 'R', false);
      $pdf->Cell(13, 4, gaz_format_number(-$tot_diff_tmp), 1, 1, 'C', true);
      /* ENRICO FEDELE */
      if (!$mv || $mv["clfoco"] != $ctrl_partner) { // si cambia anagrafe alla prossima iterazione
         /* Stampo la riga del totale, in grassetto italico "BI" */
         $pdf->SetFillColor(255, 214, 255);
         $pdf->SetFont('helvetica', 'BI', 6);
         $pdf->Cell(173, 4, 'SALDO ANAGRAFE', 1, 0, 'R', false);
         $pdf->Cell(13, 4, gaz_format_number(-$tot_diff_anagrafe), 1, 1, 'C', true);
         $pdf->SetFillColor(235, 235, 235);
         $pdf->SetFont('helvetica', '', 1);
         $pdf->Cell(186, 1, '', 1, 1, 'C', true);
         $tot_diff_anagrafe = 0;
         $nuova_anagrafe = true;
      }
   }
   /* Stampo la riga del totale generale, in grassetto italico "BI" */
   $pdf->SetFillColor(255, 214, 255);
   $pdf->SetFont('helvetica', 'BI', 6);
   $pdf->Cell(143, 4, 'SALDO TOTALE', 1, 0, 'R', TRUE);
   $pdf->Cell(15, 4, gaz_format_number($tot_dare), 1, 0, 'R', true);
   $pdf->Cell(15, 4, gaz_format_number($tot_avere), 1, 0, 'R', true);
   $pdf->Cell(13, 4, gaz_format_number(-$tot_dare + $tot_avere), 1, 1, 'C', true);
}


$pdf->setRiporti('');
$pdf->Output();
?>