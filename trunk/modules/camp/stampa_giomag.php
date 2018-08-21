<?php
/*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2017 - Antonio De Vincentiis Montesilvano (PE)
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

require("../../library/include/datlib.inc.php");

$admin_aziend=checkAdmin();

if (!ini_get('safe_mode')){ //se me lo posso permettere...
    ini_set('memory_limit','128M');
    gaz_set_time_limit (0);
}



function getMovements($date_ini,$date_fin)
    {
        global $gTables,$admin_aziend;
        $m=array();
        $where="datreg BETWEEN $date_ini AND $date_fin";
        $what=$gTables['movmag'].".*, ".
              $gTables['caumag'].".codice, ".$gTables['caumag'].".descri, ".
			  $gTables['clfoco'].".codice, ".$gTables['clfoco'].".descri AS ragsoc, ".
              $gTables['artico'].".codice, ".$gTables['artico'].".descri AS desart, ".$gTables['artico'].".unimis, ".$gTables['artico'].".scorta, ".$gTables['artico'].".catmer, ".$gTables['artico'].".mostra_qdc, ".$gTables['artico'].".classif_amb ";
        $table=$gTables['movmag']." LEFT JOIN ".$gTables['caumag']." ON (".$gTables['movmag'].".caumag = ".$gTables['caumag'].".codice)
				LEFT JOIN ".$gTables['clfoco']." ON (".$gTables['movmag'].".campo_coltivazione = ".$gTables['clfoco'].".codice)
               LEFT JOIN ".$gTables['artico']." ON (".$gTables['movmag'].".artico = ".$gTables['artico'].".codice)";
        $rs=gaz_dbi_dyn_query ($what,$table,$where, 'datreg ASC, tipdoc ASC, campo_coltivazione ASC, operat DESC, id_mov ASC');
        while ($r = gaz_dbi_fetch_array($rs)) {
            $m[] = $r;
        }
        return $m;
    }


$luogo_data=$admin_aziend['citspe'].", lì ";

if (isset($_GET['ds'])) {
   $giosta = substr($_GET['ds'],0,2);
   $messta = substr($_GET['ds'],2,2);
   $annsta = substr($_GET['ds'],4,4);
   $utssta= mktime(0,0,0,$messta,$giosta,$annsta);
   $luogo_data .= ucwords(strftime("%d %B %Y",$utssta));
} else {
   $luogo_data .=ucwords(strftime("%d %B %Y", mktime (0,0,0,date("m"),date("d"),date("Y"))));
}

$giori = substr($_GET['ri'],0,2);
$mesri = substr($_GET['ri'],2,2);
$annri = substr($_GET['ri'],4,4);
$utsri= mktime(0,0,0,$mesri,$giori,$annri);
$giorf = substr($_GET['rf'],0,2);
$mesrf = substr($_GET['rf'],2,2);
$annrf = substr($_GET['rf'],4,4);
$utsrf= mktime(0,0,0,$mesrf,$giorf,$annrf);

$result=getMovements(strftime("%Y%m%d",$utsri),strftime("%Y%m%d",$utsrf));

require("../../config/templates/report_template_qc.php");
$title = array('luogo_data'=>$luogo_data,
               'title'=>"QUADERNO DI CAMPAGNA dal ".strftime("%d %B %Y",$utsri)." al ".strftime("%d %B %Y",$utsrf),
               'hile'=>array(array('lun' => 17,'nam'=>'Data att.'),
                             array('lun' => 35,'nam'=>'Causale'),
                             array('lun' => 30,'nam'=>'Annotazioni'),
                             array('lun' => 12,'nam'=>'Campo'),
                             array('lun' => 10,'nam'=>'ha'),
                             array('lun' => 38,'nam'=>'Coltura'),
							 array('lun' => 69,'nam'=>'Prodotto'),
							 array('lun' => 6,'nam'=>'Cl.'),
                             array('lun' => 8,'nam'=>'U.M.'),
                             array('lun' => 12,'nam'=>'Q.tà'),
							 array('lun' => 30,'nam'=>'Avversità'),
							 array('lun' => 18,'nam'=>'Operat.')
                            )
              );

$pdf = new Report_template('L','mm','A4',true,'UTF-8',false,true);
$pdf->setVars($admin_aziend,$title);
$pdf->SetTopMargin(42);
$pdf->SetleftMargin(6);
$pdf->SetFooterMargin(20);
$config = new Config;
$pdf->AddPage('L',$config->getValue('page_format'));
$pdf->SetFont('helvetica','',9);
if (sizeof($result) > 0) {
  while (list($key, $row) = each($result)) {
	  if ($row['type_mov']==1 or $row['mostra_qdc']==1){ //escludi se non riguarda il Q.d.c. o se non è un articolo agricolo
      $datadoc = substr($row['datdoc'],8,2).'-'.substr($row['datdoc'],5,2).'-'.substr($row['datdoc'],0,4);
      $datareg = substr($row['datreg'],8,2).'-'.substr($row['datreg'],5,2).'-'.substr($row['datreg'],0,4);
      $movQuanti = $row['quanti']*$row['operat'];
      $pdf->Cell(17,6,$datadoc,1,0,'C');
      $pdf->Cell(35,6,$row['descri'],1, 0, 'l', 0, '', 1);
	  $pdf->Cell(30,6,$row['desdoc'],1, 0, 'l', 0, '', 1);
	  $pdf->Cell(12,6,substr($row['campo_coltivazione'],0,3),1);
// Antonio Germani carico la tabella campi
$res = gaz_dbi_dyn_query ('*', $gTables['campi']);
// fine carico tabella campi			
	// Antonio Germani Inserisco superficie e coltura		
            
			$colonna="0";
	while($b_row = $res->fetch_assoc()) { 
	if ($row['campo_coltivazione']==$b_row["codice"]) { 
	$pdf->Cell(10,6,str_replace('.', ',',$b_row["ricarico"]),1);
	 $pdf->Cell(38,6,substr($b_row["annota"],0,40),1);
	 $colonna="1";
		} 
	}
	 if ($colonna<1) {
		$pdf->Cell(10,6,"",1);
		$pdf->Cell(38,6,"",1);
	 }
// fine inserisco superficie, coltura	  
	  
	  
	  
	  
      $pdf->Cell(69,6,$row['artico'].' - '.$row['desart'], 1, 0, 'l', 0, '', 1);
	  If ($row['classif_amb']==0){$pdf->Cell(6,6,"Nc",1);}
	  If ($row['classif_amb']==1){$pdf->Cell(6,6,"Xi",1);}
	  If ($row['classif_amb']==2){$pdf->Cell(6,6,"Xn",1);}
	  If ($row['classif_amb']==3){$pdf->Cell(6,6,"T",1);}
	  If ($row['classif_amb']==4){$pdf->Cell(6,6,"T+",1);}
	  If ($row['classif_amb']==5){$pdf->Cell(6,6,"Pa",1);}
	  $pdf->Cell(8,6,$row['unimis'],1,0,'C');
      $pdf->Cell(12,6,gaz_format_quantity($row["quanti"],1,$admin_aziend['decimal_quantity']),1);
      
      $pdf->Cell(30,6,$row['avversita'],1, 0, 'l', 0, '', 1);
/* Antonio Germani - trasformo nome utente login in cognome e nome e lo stampo */	  
$res2 = gaz_dbi_dyn_query ('*', $gTables['admin']);	
 $colonna="0";
	while($c_row = $res2->fetch_assoc()) { 
	if ($row['adminid']==$c_row["user_name"]) { 
	$pdf->Cell(18,6,$c_row['user_lastname']." ".$c_row['user_firstname'],1, 1, 'l', 0, '', 1);
	 $colonna="1";
		} 
	}
	 if ($colonna<1) {
		$pdf->Cell(18,6,"",1, 1, 'l', 0, '', 1);
	} 
/* Antonio Germani FINE trasformo nome utente login in cognome e nome */	  
      
	  } 
  }
}
$pdf->Output();
?>