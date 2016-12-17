<?php
 /*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2016 - Antonio De Vincentiis Montesilvano (PE)
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
require_once("../../library/include/datlib.inc.php");
$admin_aziend=checkAdmin();

// se non viene richiamata dalla pagina interventi mostra la barra
if ( !isset($_GET['include']) ) {
   require_once("../../library/include/header.php");
   $script_transl=HeadMain();
}
$orderby = "data asc";
$where 	= "tipo = 'ASP' ";
$all 	= $where;

// cambia lo stato della periodica in base a valore precedente
if ( isset($_GET["q"]) && $_GET["q"]==0 ) gaz_dbi_table_update("assist", $_GET["codice"], array("stato" => "1"));
if ( isset($_GET["q"]) && $_GET["q"]==1 ) gaz_dbi_table_update("assist", $_GET["codice"], array("stato" => "2"));
if ( isset($_GET["q"]) && $_GET["q"]==2 ) {
   $result = gaz_dbi_dyn_query( $gTables['assist'].".*", $gTables['assist'], " codice='".$_GET['codice']."'" );
   $value = gaz_dbi_fetch_array($result); 
   gaz_dbi_table_update("assist", $_GET["codice"], array("stato" => "3"));
}
if ( isset($_GET["q"]) && $_GET["q"]==3 ) {
   $result = gaz_dbi_dyn_query( $gTables['assist'].".*", $gTables['assist'], " codice='".$_GET['codice']."'" );
   $value = gaz_dbi_fetch_array($result);
   $rs_ultima_ass = gaz_dbi_dyn_query("codice", $gTables['assist'],"1","codice desc");
	$ultimo_documento = gaz_dbi_fetch_array($rs_ultima_ass);
	if ($ultimo_documento) {      
		$value['codice'] = $ultimo_documento['codice'] + 1;
	}
   $value["stato"]="0";
 
   if ( $value["ogni"]=="Anni" ) $ogni = "years";
   if ( $value["ogni"]=="Mesi" ) $ogni = "months";
   if ( $value["ogni"]=="Giorni" ) $ogni = "days";
   
   $value["data"]=date( "Y-m-d", strtotime( "+".$value['ripetizione']." ".$ogni, strtotime($value["data"]) ));
   gaz_dbi_table_insert("assist", $value);
   gaz_dbi_table_update("assist", $_GET["codice"], array("stato" => "4"));
}

/*if ( isset($_GET["q"]) && $_GET["q"]=="avv" ) gaz_dbi_table_update("assist", $_GET["codice"], array("stato" => "avvisato"));
if ( isset($_GET["q"]) && $_GET["q"]=="eff" ) gaz_dbi_table_update("assist", $_GET["codice"], array("stato" => "effettuato"));
if ( isset($_GET["q"]) && $_GET["q"]=="fat" ) {
   $result = gaz_dbi_dyn_query( $gTables['assist'].".*", $gTables['assist'], " codice='".$_GET['codice']."'" );
   $value = gaz_dbi_fetch_array($result); 
   gaz_dbi_table_update("assist", $_GET["codice"], array("stato" => "fatturato"));
}
if ( isset($_GET["q"]) && $_GET["q"]=="chi" ) {
   $result = gaz_dbi_dyn_query( $gTables['assist'].".*", $gTables['assist'], " codice='".$_GET['codice']."'" );
   $value = gaz_dbi_fetch_array($result);
   $rs_ultima_ass = gaz_dbi_dyn_query("codice", $gTables['assist'],"1","codice desc");
	$ultimo_documento = gaz_dbi_fetch_array($rs_ultima_ass);
	if ($ultimo_documento) {      
		$value['codice'] = $ultimo_documento['codice'] + 1;
	}
   $value["stato"]="aperto";
 
   if ( $value["ogni"]=="Anni" ) $ogni = "years";
   if ( $value["ogni"]=="Mesi" ) $ogni = "months";
   if ( $value["ogni"]=="Giorni" ) $ogni = "days";
   
   $value["data"]=date( "Y-m-d", strtotime( "+".$value['ripetizione']." ".$ogni, strtotime($value["data"]) ));
   gaz_dbi_table_insert("assist", $value);
   gaz_dbi_table_update("assist", $_GET["codice"], array("stato" => "chiuso"));
}*/

// se le periodiche vengono richiamate dalla pagina installazioni
if ( isset( $_GET['idinstallazione']) ) {
   $where .= " and idinstallazione=".$_GET['idinstallazione'];
   if ( isset( $_GET['flt_cliente'] ) ) {
      $where .= " and clfoco=".$_GET['flt_cliente'];
   }
}

// imposto le variabili per i filtri
gaz_flt_var_assign('id', 'i');
gaz_flt_var_assign('data', 'd');
gaz_flt_var_assign('clfoco', 'v');
gaz_flt_var_assign('telefo', 'v');
gaz_flt_var_assign('oggetto', 'v');
gaz_flt_var_assign('descrizione', "v");
gaz_flt_var_assign('tecnico', "v");
gaz_flt_var_assign('stato', "v");

// recupero l'ultimo passo impostato
if ( isset($_GET["flt_passo"]) ) $passo = $_GET['flt_passo'];

// se è stato premuto il tasto mostra tutti
if ( !isset($_GET["all"]) ) {
   $where .= " and stato != 'chiuso'";
   $where .= "and data>'".date("Y-m-d", strtotime("-1 month"))."' and data<'".date("Y-m-d", strtotime("+2 month"))."'";
}

if ( isset( $_GET['idinstallazione']) ) {
   $title = "Assistenze Periodiche";
} else {
   $title = $script_transl['title'];
   if (!isset($_GET["all"])) $title .= " dal ".date("Y-m-d", strtotime("-1 month"))." al ".date("Y-m-d", strtotime("+2 month"));
}

?>
<div class="row">
<div class="FacetFormHeaderFont col-xs-12">
	<form method="GET">
	<!-- riga filtro -->

        <!--<div class="box">
            <div class="box-header">
                <h3 class="box-title"><?php echo $title; ?></h3>
            </div>
            <div class="box-body">-->
        
        <div class="box-body table-responsive">
	<table class="Tlarge table table-bordered table-striped">
		<tr>
		<td class="FacetFieldCaptionTD">
                        <?php gaz_flt_disp_int("id", "Numero"); ?>
		</td>
		<td class="FacetFieldCaptionTD">
			<?php gaz_flt_disp_select("data", "YEAR(data) as data", $gTables["assist"], "9999", $orderby); ?>
		</td>
		<td class="FacetFieldCaptionTD">
			<?php gaz_flt_disp_select("clfoco", $gTables['anagra'] . ".ragso1," . $gTables["assist"] . ".clfoco", $gTables['assist'] . " LEFT JOIN " . $gTables['clfoco'] . " ON " . $gTables['assist'] . ".clfoco = " . $gTables['clfoco'] . ".codice LEFT JOIN " . $gTables['anagra'] . " ON " . $gTables['clfoco'] . ".id_anagra = " . $gTables['anagra'] . ".id", $all, "ragso1", "ragso1"); ?>
		</td>
		<td class="FacetFieldCaptionTD">
			<?php gaz_flt_disp_int("telefo", "Telefono"); ?>
		</td>
		<td class="FacetFieldCaptionTD">
			<?php gaz_flt_disp_int("oggetto", "Oggetto"); ?>
		</td>
		<td class="FacetFieldCaptionTD">
			<?php gaz_flt_disp_int("descrizione", "Descrizione"); ?>
		</td>
        <td class="FacetFieldCaptionTD">
			<?php gaz_flt_disp_select("tecnico", "tecnico", $gTables["assist"], "9999", "tecnico"); ?>
		</td>
		<td class="FacetFieldCaptionTD">
			<?php gaz_flt_disp_select("stato", "stato", $gTables["assist"], "9999", "stato", $per_stato); ?>
		</td>
		<td class="FacetFieldCaptionTD" colspan="2">
         <input type="submit" class="btn btn-sm btn-default" name="search" value="Cerca" tabindex="1" onClick="javascript:document.report.all.value = 1;">
			<input type="submit" class="btn btn-sm btn-default" name="all" value="Mostra tutti" onClick="javascript:document.report.all.value=1;">
		</td>
		</tr>

		<?php 
		if ( isset( $_GET['idinstallazione']) ) {
         $headers_assist = array  (
            "ID" 	=> "codice",
            "Data" 		=> "data",
            "Cliente" 	=> "cliente",
            "Telefono" 	=> "telefono",
            "Oggetto" 	=> "oggetto",
            "Descrizione" => "descrizione",
            "Tecnico"   => "tecnico",
            "Stato" 		=> "stato",	
            "Stampa" 	=> ""           
         );
      } else {
         //riga ordinamento colonne
         $headers_assist = array  (
            "ID" 	=> "codice",
            "Data" 		=> "data",
            "Cliente" 	=> "cliente",
            "Telefono" 	=> "telefono",
            "Oggetto" 	=> "oggetto",
            "Descrizione" => "descrizione",
            "Tecnico"   => "tecnico",
            "Stato" 		=> "stato",	
            "Stampa" 	=> "",
            "Elimina" 	=> ""
         );
      }
		$linkHeaders = new linkHeaders($headers_assist);
		$linkHeaders -> output();

		$recordnav = new recordnav($gTables['assist'].
				" LEFT JOIN ".$gTables['clfoco']." ON ".$gTables['assist'].".clfoco = ".$gTables['clfoco'].".codice". 
				" LEFT JOIN ".$gTables['anagra'].' ON '.$gTables['clfoco'].".id_anagra = ".$gTables['anagra'].".id",
			$where, $limit, $passo);
		$recordnav -> output();

$result = gaz_dbi_dyn_query( $gTables['assist'].".*,
				".$gTables['anagra'].".ragso1, 
				".$gTables['anagra'].".telefo ",
				$gTables['assist'].
					" LEFT JOIN ".$gTables['clfoco']." ON ".$gTables['assist'].".clfoco = ".$gTables['clfoco'].".codice". 
					" LEFT JOIN ".$gTables['anagra'].' ON '.$gTables['clfoco'].'.id_anagra = '.$gTables['anagra'].'.id',
				$where, $orderby, $limit, $passo);

$month = array(1=>"Gennaio", 2=>"Febbraio", 3=>"Marzo", 4=>"Aprile", 5=>"Maggio", 6=>">Giugno", 7=>"Luglio", 8=>"Agosto", 9=>"Settembre", 10=>"Ottobre", 11=>"Novembre", 12=>"Dicembre");

while ($a_row = gaz_dbi_fetch_array($result)) {
?>
   <tr class="FacetDataTD">
		<td>
			<a class="btn btn-xs btn-edit" href="admin_period.php?codice=<?php echo $a_row["codice"]; ?>&Update">
			<?php echo $a_row["codice"]; ?></a>
		</td>
		<td><?php echo date("d",strtotime($a_row["data"]))." ".$month[date("n",strtotime($a_row["data"]))]." ".date("Y",strtotime($a_row["data"])); ?></td>
		<td><a href="../vendit/report_client.php?auxil=<?php echo $a_row["ragso1"]; ?>&search=Cerca">
		<?php 
			if ( strlen($a_row["ragso1"]) > 20 ) {
				echo substr($a_row["ragso1"],0,20)."..."; 
			} else {
				echo $a_row["ragso1"]; 
			}
		?></a>
		</td>
		<td><?php echo $a_row["telefo"]; ?></td>
		<td><?php echo $a_row["oggetto"]; ?></td>
		<td><?php 
            $length = strlen($a_row["descrizione"]);
            $descri = substr(strip_tags($a_row["descrizione"]), 0, 60);
            $descri = str_replace( "<p>", "", $descri );
            $descri = str_replace( "</p>", "", $descri );
            echo $descri."..."; ?></td>
      <td><?php echo $a_row["tecnico"]; ?></td>
		<td>
			<?php 
				if ( $a_row["stato"]=="0" ) echo '<a class="btn btn-xs btn-edit" href="report_period.php?codice='.$a_row["codice"].'&q=0">'.$per_stato[0].'</a>';
				if ( $a_row["stato"]=="1" ) echo '<a class="btn btn-xs btn-fatt" href="report_period.php?codice='.$a_row["codice"].'&q=1">'.$per_stato[1].'</a>';
                if ( $a_row["stato"]=="2" ) echo '<a class="btn btn-xs btn-riba" href="report_period.php?codice='.$a_row["codice"].'&q=2">'.$per_stato[2].'</a>';
                if ( $a_row["stato"]=="3" ) echo '<a class="btn btn-xs btn-riba" href="report_period.php?codice='.$a_row["codice"].'&q=3">'.$per_stato[3].'</a>';
                if ( $a_row["stato"]=="4" ) echo '<btn class="btn btn-xs btn-cont">'.$per_stato[4].'</btn>';
			?>
		</td>
		<td>
			<a class="btn btn-xs btn-default" href="stampa_period.php?id=<?php echo $a_row["id"]; ?>&cod=<?php echo $a_row["codice"]; ?>&stato=<?php echo $a_row["stato"]; ?>" target="_blank"><i class="glyphicon glyphicon-print"></i></a>
		</td>
		<?php
      if ( !isset( $_GET['idinstallazione']) ) {
         echo '<td>
            <a class="btn btn-xs btn-default btn-elimina" href="delete_assist.php?id='.$a_row["id"].'&cod='.$a_row["codice"].'">
            <i class="glyphicon glyphicon-remove"></i></a>
         </td>';
      }
      ?>
   </tr>
<?php 
} 

$passi = array(20, 50, 100, 10000 );
?>
<!-- riga riepilogo tabella -->
<!--<tr>
	<td class="FacetFieldCaptionTD" colspan="8" align="right">Totale Ore : 
		<?php //echo floatval($totale_ore); ?>
	</td>
	<td class="FacetFieldCaptionTD" colspan="3" align="right">Totale Euro :
		<?php //echo floatval($totale_ore * 42); ?>
	</td>
</tr>-->
<tr>
	<td class="FacetFieldCaptionTD" align="center" colspan="11">Numero elementi : 
		<select name="flt_passo" onchange="this.form.submit()">		
		<?php
		foreach ( $passi as $val ) {
			if ( $val == $passo ) $selected = " selected";
			else $selected = "";
			echo "<option value='".$val."'".$selected.">".$val."</option>";
		}
		?>
		</select>
	</td>
        </tr>
        </table>
        </div>
      </div>
</div>
<!--</div>
</div>-->

</form>
<?php
require("../../library/include/footer.php");
?>