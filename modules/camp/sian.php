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
 // IL REGISTRO DI CAMPAGNA E' UN MODULO DI ANTONIO GERMANI - MASSIGNANO AP
// >> Selezione date per la generazione del file di upload per il SIAN <<

require("../../library/include/datlib.inc.php");
require ("../../modules/magazz/lib.function.php");
$admin_aziend=checkAdmin();
$msg='';

// controllo che ci sia la cartella sian
$sianfolder = '../../data/files/' . $admin_aziend['codice'] . '/sian/';
if (!file_exists($sianfolder)) {// se non c'è la creo
    mkdir($sianfolder, 0777);
}

// prendo tutti i file della cartella sian
if ($handle = opendir('../../data/files/' . $admin_aziend['codice'] . '/sian/')){
   while (false !== ($file = readdir($handle))){
       $prevfiles[]=$file;
   }
   closedir($handle);
}

// Prendo l'ultimo file salvato nella cartella sian
foreach(new DirectoryIterator('../../data/files/' . $admin_aziend['codice'] . '/sian') as $item) {
    if ($item->isFile() && (empty($file) || $item->getMTime() > $file->getMTime())) {
        $file = clone $item;
    }
}

if (!empty($file)){
	$fileContent=@file_get_contents('../../data/files/' . $admin_aziend['codice'] . '/sian/'.$file->getFilename()); // prendo il contenuto dell'ultimo file
	$fileField=explode (";",$fileContent);
	$uldtfile=$fileField[((((count($fileField)-1)/49)-1)*49)+3];
	$uldtfile=str_replace("-", "", $uldtfile);
} else { // se non ci sono file imposto come data il primo del mese corrente
	$uldtfile="01".date("m").date("Y");
}

function getMovements($date_ini,$date_fin)
    {
        global $gTables,$admin_aziend;
        $m=array();
        $where="datdoc BETWEEN $date_ini AND $date_fin";
        $what=$gTables['movmag'].".*, ".
              $gTables['camp_mov_sian'].".*, ".
			  $gTables['artico'].".SIAN, ".
			  $gTables['anagra'].".ragso1, ".$gTables['anagra'].".id_SIAN, ".
			  $gTables['clfoco'].".id_anagra, ".
			  $gTables['camp_artico'].".or_macro, ".$gTables['camp_artico'].".or_spec, ".$gTables['camp_artico'].".estrazione, ".$gTables['camp_artico'].".biologico, ".$gTables['camp_artico'].".etichetta, ".$gTables['camp_artico'].".categoria ";
        $table=$gTables['movmag']." LEFT JOIN ".$gTables['camp_mov_sian']." ON (".$gTables['movmag'].".id_mov = ".$gTables['camp_mov_sian'].".id_movmag)
               LEFT JOIN ".$gTables['clfoco']." ON (".$gTables['movmag'].".clfoco = ".$gTables['clfoco'].".codice)
			   LEFT JOIN ".$gTables['camp_artico']." ON (".$gTables['movmag'].".artico = ".$gTables['camp_artico'].".codice)
               LEFT JOIN ".$gTables['artico']." ON (".$gTables['movmag'].".artico = ".$gTables['artico'].".codice)
			   LEFT JOIN ".$gTables['anagra']." ON (".$gTables['anagra'].".id = ".$gTables['clfoco'].".id_anagra)";
        $rs=gaz_dbi_dyn_query ($what,$table,$where, 'datreg ASC, tipdoc ASC, clfoco ASC, operat DESC, id_mov ASC');
        while ($r = gaz_dbi_fetch_array($rs)) {
            $m[] = $r;
        }
        return $m;
    }

if (!isset($_POST['hidden_req'])) { //al primo accesso allo script
    $form['hidden_req'] = '';
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
    $form['this_date_Y']=date("Y");
    $form['this_date_M']=date("m");
    $form['this_date_D']=date("d");
    $form['date_ini_D']=substr($uldtfile,0,2); // imposto la data di inizio partendo da quella dell'ultimo file
    $form['date_ini_M']=substr($uldtfile,2,2);
    $form['date_ini_Y']=substr($uldtfile,4,4);
    $form['date_fin_D']=date('d', strtotime('-1 day', strtotime(date("Y-m-d"))));
    $form['date_fin_M']=date('m', strtotime('-1 day', strtotime(date("Y-m-d"))));
    $form['date_fin_Y']=date('Y', strtotime('-1 day', strtotime(date("Y-m-d"))));
} else { // accessi successivi
    $form['hidden_req']=htmlentities($_POST['hidden_req']);
    $form['ritorno']=$_POST['ritorno'];
    $form['date_ini_D']=substr($uldtfile,0,2); // impongo la data di inizio partendo da quella dell'ultimo file
    $form['date_ini_M']=substr($uldtfile,2,2);
    $form['date_ini_Y']=substr($uldtfile,4,4);
    $form['date_fin_D']=intval($_POST['date_fin_D']);
    $form['date_fin_M']=intval($_POST['date_fin_M']);
    $form['date_fin_Y']=intval($_POST['date_fin_Y']);
    $form['this_date_Y']=intval($_POST['this_date_Y']);
    $form['this_date_M']=intval($_POST['this_date_M']);
    $form['this_date_D']=intval($_POST['this_date_D']);
    if (isset($_POST['return'])) {
        header("Location: ".$form['ritorno']);
        exit;
    }
}
$date_ini =  sprintf("%04d%02d%02d",$form['date_ini_Y'],$form['date_ini_M'],$form['date_ini_D']);
$date_fin =  sprintf("%04d%02d%02d",$form['date_fin_Y'],$form['date_fin_M'],$form['date_fin_D']);

//controllo le date
if (!checkdate( $form['this_date_M'],$form['this_date_D'],$form['this_date_Y']) ||
    !checkdate( $form['date_ini_M'], $form['date_ini_D'], $form['date_ini_Y']) ||
    !checkdate( $form['date_fin_M'], $form['date_fin_D'], $form['date_fin_Y'])) {
    $msg .='0+';
}
$utsexe= mktime(0,0,0,$form['this_date_M'],$form['this_date_D'],$form['this_date_Y']);
$utsini= mktime(0,0,0,$form['date_ini_M'],$form['date_ini_D'],$form['date_ini_Y']);
$utsfin= mktime(0,0,0,$form['date_fin_M'],$form['date_fin_D'],$form['date_fin_Y']);

if ($utsini > $utsfin) {
    $msg .='1+';
}
if ($utsexe < $utsfin) {
    $msg .='2+';
}
if ($utsfin>strtotime('-1 day', strtotime(date("Y-m-d")))) {
    $msg .='4+';
}

// fine controlli

if (isset($_POST['create']) && $msg=='') {
    
    $utsini=date("dmY",$utsini);
    $utsfin=date("dmY",$utsfin);
    $utsexe=date("dmY",$utsexe);
	$uldtfile=$form['date_ini_Y'].$form['date_ini_M'].$form['date_ini_D'];                                  
    header("Location: create_sian.php?ri=$utsini&rf=$utsfin&ds=$utsexe&ud=$uldtfile");
    exit;
}


require("../../library/include/header.php");
$script_transl=HeadMain(0,array('calendarpopup/CalendarPopup'));
echo "<script type=\"text/javascript\">
var cal = new CalendarPopup();
var calName = '';
function setMultipleValues(y,m,d) {
     document.getElementById(calName+'_Y').value=y;
     document.getElementById(calName+'_M').selectedIndex=m*1-1;
     document.getElementById(calName+'_D').selectedIndex=d*1-1;
}
function setDate(name) {
  calName = name.toString();
  var year = document.getElementById(calName+'_Y').value.toString();
  var month = document.getElementById(calName+'_M').value.toString();
  var day = document.getElementById(calName+'_D').value.toString();
  var mdy = month+'/'+day+'/'+year;
  cal.setReturnFunction('setMultipleValues');
  cal.showCalendar('anchor', mdy);
}
</script>
";
?>
<div class="panel panel-default gaz-table-form">
    <div class="container-fluid">
		<div align="center"><b>Disposizione per la tenuta del registro SIAN</b>
		<p align="justify">
		Le annotazioni nei registri e quindi l'invio del file di upload, si effettuano entro e non oltre il sesto giorno successivo a quello dell’operazione, giorni festivi compresi.
		Gli olivicoltori che detengono e commercializzano esclusivamente olio, allo stato sfuso e/o confezionato, ottenuto da olive provenienti dalla propria azienda, molite
		presso il frantoio proprio o di terzi, possono effettuare entro il 10 di ogni mese le annotazioni dei dati relativi alle operazioni del mese precedente, a condizione
		che l’olio ottenuto dalla molitura non sia superiore ai 700 chilogrammi per campagna di commercializzazione (dal 1 luglio al 30 giugno dell'anno successivo).
		</p></div>
	</div>
</div>	
<?php
echo "<form method=\"POST\" name=\"select\">\n";
echo "<input type=\"hidden\" value=\"".$form['hidden_req']."\" name=\"hidden_req\" />\n";
echo "<input type=\"hidden\" value=\"".$form['ritorno']."\" name=\"ritorno\" />\n";
$gForm = new magazzForm();
echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".$script_transl['title'];
echo "</div>\n";
echo "<table class=\"Tsmall\">\n";
if (!empty($msg)) {
    echo '<tr><td colspan="2" class="FacetDataTDred">'.$gForm->outputErrors($msg,$script_transl['errors'])."</td></tr>\n";
}
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['date']."</td><td  class=\"FacetDataTD\">\n";
$gForm->CalendarPopup('this_date',$form['this_date_D'],$form['this_date_M'],$form['this_date_Y'],'FacetSelect',1);
echo "</tr>\n";
echo "<tr><td class=\"FacetFieldCaptionTD\">".$script_transl['date_ini']."</td><td  class=\"FacetDataTD\">\n";
$gForm->CalendarPopup('date_ini',$form['date_ini_D'],$form['date_ini_M'],$form['date_ini_Y'],'FacetSelect',1);
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['date_fin']."</td><td  class=\"FacetDataTD\">\n";
$gForm->CalendarPopup('date_fin',$form['date_fin_D'],$form['date_fin_M'],$form['date_fin_Y'],'FacetSelect',1);
echo "</tr>\n";
echo "\t<tr class=\"FacetFieldCaptionTD\">\n";
echo "<td align=\"left\"><input type=\"submit\" name=\"return\" value=\"".$script_transl['return']."\">\n";
echo '<td align="right"> <input type="submit" accesskey="i" name="preview" value="';
echo $script_transl['view'];
echo '" tabindex="100" >';
echo "\t </td>\n";
echo "\t </tr>\n";
echo "</table>\n";


if (isset($_POST['preview']) and $msg=='') {
	$m=getMovements($date_ini,$date_fin);
	echo "<table class=\"Tlarge table table-striped table-bordered table-condensed table-responsive\">";
	if (sizeof($m) > 0) {
        $ctr_mv='';
        echo "<tr>";
        $linkHeaders=new linkHeaders($script_transl['header']);
        $linkHeaders->output();
        echo "</tr>";		
		$genera="";
        while (list($key, $mv) = each($m)) {
			if ($mv['id_movmag']>0){ // se è un movimento del SIAN connesso al movimento di magazzino
				if ($form['date_ini_Y'].$form['date_ini_M'].$form['date_ini_D']==str_replace("-", "", $mv['datdoc'])) {
				// escludo i movimenti già inseriti null'ultimo file con stessa data
				} else if ($mv['id_orderman']>0 AND $mv['operat']==-1 AND $mv['cod_operazione']<>"S7"){
					// escludo i movimenti di produzione in uscita
				} else {		
					$genera="ok";
					$datedoc = substr($mv['datdoc'],8,2).'-'.substr($mv['datdoc'],5,2).'-'.substr($mv['datdoc'],0,4);
           
					$movQuanti = $mv['quanti']*$mv['operat'];
					
					echo "<tr><td class=\"FacetDataTD\">".$datedoc." &nbsp;</td>";
					echo "<td class=\"FacetDataTD\" align=\"center\">".$mv['artico']." &nbsp;</td>\n";
			
					echo "<td class=\"FacetDataTD\" align=\"center\">".gaz_format_quantity($movQuanti,1,3)."</td>\n";
					echo "<td class=\"FacetDataTD\" align=\"center\">".$mv['id_SIAN']." - ".$mv['ragso1']." &nbsp;</td>\n";
			
					echo "<td class=\"FacetDataTD\" align=\"center\">".$mv['recip_stocc']." &nbsp;</td>\n";
					echo "<td class=\"FacetDataTD\" align=\"center\">".$mv['cod_operazione']." &nbsp;</td>\n";
					echo "</tr>\n";
					$ctr_mv = $mv['artico'];
				}
			}
         }
         echo "\t<tr class=\"FacetFieldCaptionTD\">\n";
		 if ($genera=="ok"){
			echo '<td colspan="7" align="right"><input type="submit" name="create" value="';
			echo "Genera file SIAN";
			echo '">';
			echo "\t </td>\n";
		 }
         echo "\t </tr>\n";
	}
  echo "</table></form>";
}
?>
<?php
require("../../library/include/footer.php");
?>