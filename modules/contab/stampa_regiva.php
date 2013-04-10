<?php
/*
--------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2013 - Antonio De Vincentiis Montesilvano (PE)
         (www.facebook.com/antonio.devincentiis.9)
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
$admin_aziend=checkAdmin();
if (!ini_get('safe_mode')){ //se me lo posso permettere...
    ini_set('memory_limit','128M');
    gaz_set_time_limit (300);
}
if (!isset($_GET['vr']) ||
    !isset($_GET['vs']) ||
    !isset($_GET['pi']) ||
    !isset($_GET['sd']) ||
    !isset($_GET['jp']) ||
    !isset($_GET['so']) ||
    !isset($_GET['cv']) ||
    !isset($_GET['ri']) ||
    !isset($_GET['rf'])) {
    header("Location: ".$_SERVER['HTTP_REFERER']);
    exit;
}

require("../../config/templates/standard_template.php");

class vatBook extends Standard_template
{
    function setData($data,$gTables,$admin_aziend) {
        $this->azienda = $admin_aziend;
        require("lang.".$admin_aziend['lang'].".php");
        $this->script_transl=$strScript['stampa_regiva.php'];
        $this->endyear=substr($data['f'],4,4);
        $this->vatsect=intval($data['vs']);
        $this->typbook=intval($data['vr']);
        $this->semplificata=intval($data['so']);
        $this->inidate=date("Ymd",mktime(0,0,0,substr($data['i'],2,2),substr($data['i'],0,2),substr($data['i'],4,4)));
        $this->enddate=date("Ymd",mktime(0,0,0,substr($data['f'],2,2),substr($data['f'],0,2),substr($data['f'],4,4)));
    }
    
    function getRows($gTables) { // recupera i righi dell'intervallo settato 
        //recupero i movimenti IVA del conto insieme alle relative testate
        $what = $gTables['tesmov'].".*, ".
                $gTables['rigmoi'].".*,
                CONCAT(".$gTables['anagra'].".ragso1, ' ',".$gTables['anagra'].".ragso2) AS ragsoc, ".
                $gTables['aliiva'].".descri AS desiva ";
        $table= $gTables['rigmoi']." LEFT JOIN ".$gTables['tesmov']." ON (".$gTables['rigmoi'].".id_tes = ".$gTables['tesmov'].".id_tes)
                LEFT JOIN ".$gTables['clfoco']." ON (".$gTables['tesmov'].".clfoco = ".$gTables['clfoco'].".codice)
                LEFT JOIN ".$gTables['anagra']." ON (".$gTables['anagra'].".id = ".$gTables['clfoco'].".id_anagra)
                LEFT JOIN ".$gTables['aliiva']." ON (".$gTables['rigmoi'].".codiva = ".$gTables['aliiva'].".codice)";
        $orderby="datreg ASC , protoc ASC, id_rig ASC";
        $where="datreg BETWEEN ".$this->inidate." AND ".$this->enddate." AND seziva = ". $this->vatsect." AND regiva = ".$this->typbook;
        $result = gaz_dbi_dyn_query($what, $table, $where, $orderby);        
        $this->rows = array();
        $this->vat_castle = array();
        $this->acc_castle = array();
        $this->taxable =0.00;
        $this->tax =0.00;
        $ctrl_idtes=0;
        while ($mov = gaz_dbi_fetch_array($result)) {
            $codiva = $mov['codiva'];
            $id_tes = $mov['id_tes'];
            switch ($mov['operat']) {
                case "1":
                    $taxable = $mov['imponi'];
                    $tax = $mov['impost'];
                break;
                case "2":
                    $taxable = -$mov['imponi'];
                    $tax = -$mov['impost'];
                break;
                default:
                    $taxable = 0;
                    $tax = 0;
                break;
            }
            // aggiungo ai totali generali
            $this->taxable += $taxable;
            if ($mov['tipiva'] != "D") {
               $this->tax += $tax;
            }
            
            // creo il castelletto IVA
            if (!isset($this->vat_castle[$codiva]['taxable'])) {
               $this->vat_castle[$codiva]['taxable']= 0;
            }
            $this->vat_castle[$codiva]['taxable'] += $taxable;
            
            if (!isset($this->vat_castle[$codiva]['tax'])) {
               $this->vat_castle[$codiva]['tax']= 0;
            }
            $this->vat_castle[$codiva]['tax'] += $tax;
            //se e' una semplificata recupero anche i righi contabili
            $this->acc_rows = array();
            if ($this->semplificata==1 && $ctrl_idtes<>$id_tes) {
                $rs_accounting_rows = gaz_dbi_dyn_query("*",
                    $gTables['rigmoc']." LEFT JOIN ".$gTables['clfoco']." ON (".$gTables['rigmoc'].".codcon = ".$gTables['clfoco'].".codice)",
                           "id_tes = '".$mov['id_tes']."'
                           AND codcon NOT LIKE '".$this->azienda['mascli']."%'
                           AND codcon NOT LIKE '".$this->azienda['masfor']."%'
                           AND codcon NOT LIKE '".substr($this->azienda['cassa_'],0,3)."%'
                           AND codcon NOT LIKE '".$this->azienda['masban']."%'
                           AND codcon <> ".$this->azienda['ivaacq']."
                           AND codcon <> ".$this->azienda['ivaven']."
                           AND codcon <> ".$this->azienda['ivacor'],
                          "id_rig asc");
                while ($acc_rows = gaz_dbi_fetch_array($rs_accounting_rows)) {
                    $codcon = $acc_rows['codcon'];
                    if (!isset($this->acc_castle[$codcon])) {
                        $this->acc_castle[$codcon] = array('value'=>0,'descri'=>'');
                        $this->acc_castle[$codcon]['descri'] = $acc_rows['descri'];
                    }
                    if (($acc_rows['darave'] == 'A' && $mov['regiva'] > 5) || ($acc_rows['darave'] == 'D' && $mov['regiva'] <= 5) ) {
                        $this->acc_castle[$codcon]['value'] -= $acc_rows['import'];
                    } else {
                        $this->acc_castle[$codcon]['value'] += $acc_rows['import'];
                    }
                    $this->acc_rows[$codcon] = array('value'=>$acc_rows['import'],'descri'=>$acc_rows['descri']);
                }
                $this->rows[]=$mov+array('acc_rows'=>$this->acc_rows);
            } else {
                $this->rows[]=$mov;
            }
            $ctrl_idtes=$id_tes;
        }
    }
}

function calcPeriod($dateIni,$dateFin,$period){
    if ($period=='M'){ // mensile
        $period_num = 1+ substr($dateFin,2,2) - substr($dateIni,2,2) + (substr($dateFin,4,4) - substr($dateIni,4,4))*12;
        for ($i=1; $i<=$period_num; $i++){
            $rs[$i]['m']='M';
            if ($period_num==1) { // il solo
                $rs[$i]['i']=date("dmY",mktime(0,0,0,substr($dateIni,2,2),substr($dateIni,0,2),substr($dateIni,4,4)));
                $rs[$i]['f']=date("dmY",mktime(0,0,0,substr($dateFin,2,2),substr($dateFin,0,2),substr($dateFin,4,4)));
            } elseif ($i==1) { // il primo
                $rs[$i]['i']=date("dmY",mktime(0,0,0,substr($dateIni,2,2),substr($dateIni,0,2),substr($dateIni,4,4)));
                $rs[$i]['f']=date("dmY",mktime(0,0,0,substr($dateIni,2,2)+1,0,substr($dateIni,4,4)));
            } elseif ($i==$period_num) { // l'ultimo
                $rs[$i]['i']=date("dmY",mktime(0,0,0,substr($dateIni,2,2)+$i-1,1,substr($dateIni,4,4)));
                $rs[$i]['f']=date("dmY",mktime(0,0,0,substr($dateFin,2,2),substr($dateFin,0,2),substr($dateFin,4,4)));
            } else { // gli intermedi
                $rs[$i]['i']=date("dmY",mktime(0,0,0,substr($dateIni,2,2)+$i-1,1,substr($dateIni,4,4)));
                $rs[$i]['f']=date("dmY",mktime(0,0,0,substr($dateIni,2,2)+$i,0,substr($dateIni,4,4)));
            }
        }
    } elseif  ($period=='no'){ // tutto
        $period_num = 1;
        $rs[1]['m']='N';
        $rs[1]['i']=$dateIni;
        $rs[1]['f']=$dateFin;
    } else { // trimestrale
        if (substr($dateIni,2,2) >= 1 and substr($dateIni,2,2) < 4) {
            $tri_ini = 1;
        } elseif (substr($dateIni,2,2) >= 4 and substr($dateIni,2,2) < 6) {
            $tri_ini = 2;
        } elseif (substr($dateIni,2,2) >= 6 and substr($dateIni,2,2) < 10) {
            $tri_ini = 3;
        } else {
            $tri_ini = 4;
        }
        if (substr($dateFin,2,2) >= 1 and substr($dateFin,2,2) < 4) {
            $tri_fin = 1;
        } elseif (substr($dateFin,2,2) >= 4 and substr($dateFin,2,2) < 6) {
            $tri_fin = 2;
        } elseif (substr($dateFin,2,2) >= 6 and substr($dateFin,2,2) < 10) {
            $tri_fin = 3;
        } else {
            $tri_fin = 4;
        }
        $period_num = 1 + $tri_fin - $tri_ini +(substr($dateFin,4,4) - substr($dateIni,4,4))*4;
        for ($i=1; $i<=$period_num; $i++){
            $rs[$i]['m']='T';
            if ($period_num==1) { // il solo
                $rs[$i]['i']=date("dmY",mktime(0,0,0,substr($dateIni,2,2),substr($dateIni,0,2),substr($dateIni,4,4)));
                $rs[$i]['f']=date("dmY",mktime(0,0,0,substr($dateFin,2,2),substr($dateFin,0,2),substr($dateFin,4,4)));
            } elseif ($i==1) { // il primo
                $rs[$i]['i']=date("dmY",mktime(0,0,0,substr($dateIni,2,2),substr($dateIni,0,2),substr($dateIni,4,4)));
                $rs[$i]['f']=date("dmY",mktime(0,0,0,$tri_ini*3+1,0,substr($dateIni,4,4)));
            } elseif ($i==$period_num) { // l'ultimo
                $rs[$i]['i']=date("dmY",mktime(0,0,0,$tri_ini*3+($i-2)*3+1,1,substr($dateIni,4,4)));
                $rs[$i]['f']=date("dmY",mktime(0,0,0,substr($dateFin,2,2),substr($dateFin,0,2),substr($dateFin,4,4)));
            } else { // gli intermedi
                $rs[$i]['i']=date("dmY",mktime(0,0,0,$tri_ini*3+($i-2)*3+1,1,substr($dateIni,4,4)));
                $rs[$i]['f']=date("dmY",mktime(0,0,0,$tri_ini*3+($i-2)*3+4,0,substr($dateIni,4,4)));
            }
        }
    }
    return $rs;
}

// -------------  INIZIO STAMPA  -------------------------------

$pdf = new vatBook();
$ini_page = intval($_GET['pi']);
if ($_GET['cv']=='cover') {
   $ini_page--;
}

$url_get=$_GET;
$period=$admin_aziend['ivam_t'];
if ($url_get['jp']!='jump'){
    $period='no';
}
$period_chopped=calcPeriod($url_get['ri'],$url_get['rf'],$period);
$p_max=count($period_chopped);
//print_r($period_chopped);
for( $i = 1; $i <= $p_max; $i++ ) {
    $pdf->setData($period_chopped[$i]+$url_get,$gTables,$admin_aziend);
    if ($i==1) {
        $n_page=array('ini_page'=>$ini_page,'year'=>ucwords($pdf->script_transl['vat_section']).$pdf->vatsect.' '.$pdf->script_transl['page'].' '.substr($url_get['ri'],4,4));
    } else {
        $n_page=false;
    }
    $descri_period=$pdf->script_transl['title'][$pdf->typbook].ucwords(strftime("%B %Y", mktime (0,0,0,substr($period_chopped[$i]['i'],2,2),1,substr($period_chopped[$i]['i'],4,4))));
    if (substr($period_chopped[$i]['f'],2,6)!= substr($period_chopped[$i]['i'],2,6)) {
        $descri_period .= ' - '.ucwords(strftime("%B %Y", mktime (0,0,0,substr($period_chopped[$i]['f'],2,2),1,substr($period_chopped[$i]['f'],4,4))));
    }
    $pdf->setVars($admin_aziend,$descri_period,0,$n_page);
    $pdf->getRows($gTables);
    if ($_GET['cv']=='cover') {
       $pdf->setCover($pdf->script_transl['cover_descri'][$pdf->typbook]."\n".substr($url_get['ri'],4,4)."\n".$pdf->script_transl['vat_section'].$pdf->vatsect);
       $pdf->AddPage();
       $_GET['cv']='';
    }
    // creo la matrice dei valori per la stampa della barra delle descrizioni delle colonne
    $topCarry = array(array('lenght' => 118,'name'=>$pdf->script_transl['top_carry'],'frame' => 'B','fill'=>0,'font'=>8),
                      array('lenght' => 20,'name'=>'','frame' => 1,'fill'=>0),
                      array('lenght' => 32,'name'=>'','frame' => 1,'fill'=>0),
                      array('lenght' => 20,'name'=>'','frame' => 1,'fill'=>0));
    $botCarry = array(array('lenght' => 118,'name'=>$pdf->script_transl['bot_carry'],'frame' => 'T','fill'=>0,'font'=>8),
                      array('lenght' => 20,'name'=>'','frame' => 1,'fill'=>1),
                      array('lenght' => 32,'name'=>'','frame' => 1,'fill'=>1),
                      array('lenght' => 20,'name'=>'','frame' => 1,'fill'=>1));
    $top = array(array('lenght' => 10,'name'=>$pdf->script_transl['top']['prot'],'frame' => 1,'fill'=>1,'font'=>7),
                 array('lenght' => 18,'name'=>$pdf->script_transl['top']['dreg'],'frame' => 1,'fill'=>1),
                 array('lenght' => 32,'name'=>$pdf->script_transl['top']['desc'],'frame' => 1,'fill'=>1),
                 array('lenght' => 18,'name'=>$pdf->script_transl['top']['ddoc'],'frame' => 1,'fill'=>1),
                 array('lenght' => 40,'name'=>$pdf->script_transl['partner_descri'][$pdf->typbook],'frame' => 1,'fill'=>1),
                 array('lenght' => 20,'name'=>$pdf->script_transl['top']['txbl'],'frame' => 1,'fill'=>1),
                 array('lenght' => 14,'name'=>$pdf->script_transl['top']['perc'],'frame' => 1,'fill'=>1),
                 array('lenght' => 18,'name'=>$pdf->script_transl['top']['tax'],'frame' => 1,'fill'=>1),
                 array('lenght' => 20,'name'=>$pdf->script_transl['top']['tot'],'frame' => 1,'fill'=>1));
    $pdf->setTopBar($top);
    $pdf->AddPage();
    $pdf->setFooterMargin(21);
    $pdf->setTopMargin(44);
    $pdf->SetFont('helvetica','',8);
    $maxY = $pdf->GetY();
    $ctrl=0;
    $totimponi =0.00;
    $totimpost =0.00;
    foreach($pdf->rows as $k=>$v) {
        switch ($v['operat']) {
               case "1":
               $imponi = $v['imponi'];
               $impost = $v['impost'];
               break;
               case "2":
               $imponi = number_format(-$v['imponi'],2, '.', '');
               $impost = number_format(-$v['impost'],2, '.', '');
               break;
               default:
               $imponi = 0;
               $impost = 0;
               break;
        }
        $totimponi += $imponi;
        if ($v['tipiva'] != "D") {
            $totimpost += $impost;
        }
        if ($ctrl != $v['id_tes']) { // primo rigo iva del movimento contabile
            if ($maxY>265){
                $pdf->AddPage();
                $maxY = $pdf->GetY();
            }
            $pdf->SetY($maxY);
            $pdf->Cell(10,4,$v['protoc'],'LTB',0,'C');
            $pdf->Cell(18,4,gaz_format_date($v['datreg']),'LTB',0,'C');
            $pdf->Cell(32,4,$v['numdoc'],'LTB',0,'C');
            $pdf->Cell(18,4,gaz_format_date($v['datdoc']),'LTB',0,'R');
            $pdf->Cell(112,4,$v['ragsoc'],'LTR',1,'L');
            $topCarry[1]['name']= gaz_format_number($totimponi).' ';
            $botCarry[1]['name']= gaz_format_number($totimponi).' ';
            $topCarry[2]['name']= gaz_format_number($totimpost).' ';
            $botCarry[2]['name']= gaz_format_number($totimpost).' ';
            $topCarry[3]['name']= gaz_format_number($totimponi+$totimpost).' ';
            $botCarry[3]['name']= gaz_format_number($totimponi+$totimpost).' ';
            $pdf->setTopCarryBar($topCarry);
            $pdf->setBotCarryBar($botCarry);
            $pdf->Cell(66,4,$v['descri'],'LTB',0,'R');
            $pdf->Cell(12,4,'cod. '.$v['codiva'],1,0,'C');
            $pdf->Cell(40,4,$v['desiva'],1,0,'L');
            $pdf->Cell(20,4,gaz_format_number($v['imponi']),1,0,'R');
            $pdf->Cell(14,4,gaz_format_number($v['periva']).'%',1,0,'C');
            $pdf->Cell(18,4,gaz_format_number($v['impost']),1,0,'R');
            $pdf->Cell(20,4,gaz_format_number($v['impost'] + $v['imponi']),1,1,'R');
            $topY = $pdf->GetY();
            if (isset($v['acc_rows'])) {
                foreach ($v['acc_rows']as $k1=>$v1){
                    $pdf->SetFont('helvetica','',7);
                    $pdf->Cell(50,4,$k1."-".substr($v1['descri'],0,23),'L');
                    $pdf->Cell(1,4,$admin_aziend['symbol']);
                    $pdf->Cell(15,4,gaz_format_number($v1['value']),'R',1,'R');
                    $pdf->SetFont('helvetica','',8);
                }
            }
            $maxY = $pdf->GetY();                        
        } else { // righi iva successivi al primo
            $pdf->SetY($topY);
            $pdf->Cell(66,4,'','L');
            $pdf->Cell(12,4,'cod. '.$v['codiva'],1,0,'C');
            $pdf->Cell(40,4,$v['desiva'],1,0,'L');
            $pdf->Cell(20,4,gaz_format_number($v['imponi']),1,0,'R');
            $pdf->Cell(14,4,gaz_format_number($v['periva']).'%',1,0,'C');
            $pdf->Cell(18,4,gaz_format_number($v['impost']),1,0,'R');
            $pdf->Cell(20,4,gaz_format_number($v['impost'] + $v['imponi']),1,1,'R');
            $topCarry[1]['name']= gaz_format_number($totimponi).' ';
            $botCarry[1]['name']= gaz_format_number($totimponi).' ';
            $topCarry[2]['name']= gaz_format_number($totimpost).' ';
            $botCarry[2]['name']= gaz_format_number($totimpost).' ';
            $topCarry[3]['name']= gaz_format_number($totimponi+$totimpost).' ';
            $botCarry[3]['name']= gaz_format_number($totimponi+$totimpost).' ';
            $pdf->setTopCarryBar($topCarry);
            $pdf->setBotCarryBar($botCarry);
            if ( $maxY < $pdf->GetY()){
                 $maxY = $pdf->GetY();                        
            }
            $topY = $pdf->GetY();                        
        }
        $ctrl = $v['id_tes'];
    }
    
    $pdf->setTopCarryBar('');
    $pdf->setBotCarryBar('');
    $pdf->Cell(190,1,'','T');
    $pdf->SetFont('helvetica','B',10);
    $pdf->Ln(6);
    $pdf->Cell(190,6,$pdf->script_transl['vat_castle_title'],1,1,'C',1);
    $pdf->Cell(20,5,'cod.',1,0,'C');
    $pdf->Cell(60,5,$pdf->script_transl['descri'],1,0,'C');
    $pdf->Cell(30,5,$pdf->script_transl['taxable'],1,0,'R');
    $pdf->Cell(20,5,'%',1,0,'C');
    $pdf->Cell(30,5,$pdf->script_transl['tax'],1,0,'R');
    $pdf->Cell(30,5,$pdf->script_transl['tot'],1,1,'R');
    foreach ($pdf->vat_castle as $k=>$v) {
         $iva = gaz_dbi_get_row($gTables['aliiva'],"codice",$k);
         $pdf->Cell(20,5,$k,1,0,'C');
         $pdf->Cell(60,5,$iva['descri'],1,0,'C');
         $pdf->Cell(30,5,gaz_format_number($v['taxable']),1,0,'R');
         $pdf->Cell(20,5,$iva['aliquo'].'%',1,0,'C');
         $pdf->Cell(30,5,gaz_format_number($v['tax']),1,0,'R');
         $pdf->Cell(30,5,gaz_format_number($v['taxable'] + $v['tax']),1,1,'R');
    }
    $pdf->SetFont('helvetica','B',10);
    $pdf->Cell(80,5,$pdf->script_transl['tot_descri'],1,0,'C',1);
    $pdf->Cell(30,5,gaz_format_number($pdf->taxable),1,0,'R',1);
    $pdf->Cell(20,5);
    $pdf->Cell(30,5,gaz_format_number($pdf->tax),1,0,'R',1);
    $pdf->Cell(30,5,gaz_format_number($pdf->taxable+$pdf->tax),1,1,'R',1);
    if (count($pdf->acc_castle)>0) {
        $pdf->Ln(6);
        $pdf->SetFont('helvetica','B',10);
        $pdf->Cell(35);
        $pdf->Cell(120,6,$pdf->script_transl['acc_castle_title'],1,2,'C',1);
        $pdf->Cell(20,5,'cod.',1,0,'C');
        $pdf->Cell(75,5,$pdf->script_transl['descri'],1,0,'C');
        $pdf->Cell(25,5,$pdf->script_transl['amount'],1,1,'R');
        $pdf->SetFont('helvetica','',8);
        foreach($pdf->acc_castle as $k=>$v) {
            $pdf->Cell(35);
            $pdf->Cell(20,5,$k,1,0,'C');
            $pdf->Cell(75,5,$v['descri'],1,0,'L');
            $pdf->Cell(25,5,gaz_format_number($v['value']),1,1,'R');
        }
    }
}
if ($_GET['sd']=='sta_def') {
    switch($pdf->typbook) { 	 
        case 2: 	 
            $azireg='upgve'.intval($_GET['vs']); 	 
        break; 	 
        case 4: 	 
            $azireg='upgco'.intval($_GET['vs']); 	 
        break; 	 
        case 6: 	 
            $azireg='upgac'.intval($_GET['vs']); 	 
        break; 	 
    }    
    gaz_dbi_put_row($gTables['aziend'],'codice',$admin_aziend['codice'],$azireg, $pdf->getGroupPageNo()+$n_page-1);
}
$pdf->Output();
?>