<?php

require("../../library/include/datlib.inc.php");
$admin_aziend=checkAdmin();

$msg = '';

if (isset($_POST['Update']) || isset($_GET['Update'])) {    
	$toDo = 'update';
} else {    
	$toDo = 'insert';
}

if (isset($_POST['Insert']) || isset($_POST['Update'])) {   //se non e' il primo accesso	
	$form=gaz_dbi_parse_post('assist');
	$anagrafica = new Anagrafica();
   $cliente = $anagrafica->getPartner($_POST['clfoco']);
	if ( isset($_POST['hidden_req']) ) $form['hidden_req'] = $_POST['hidden_req'];
   foreach($_POST['search'] as $k=>$v){
      $form['search'][$k]=$v;
   }
   //$form['cosear'] = $_POST['cosear'];
   
	$form['codice'] = trim($form['codice']);
	
	$form['descrizione'] = $_POST['descrizione'];
   $form['seriale'] = $_POST['seriale'];
   $form['datainst'] = $_POST['datainst'];
	$form['clfoco'] = $_POST['clfoco'];
   $form['note'] = $_POST['note'];
 	$form['ritorno'] = $_POST['ritorno'];
	$form['ref_code'] = $_POST['ref_code'];
	
	$form['utente'] = $_SESSION["Login"];
    
	//$form['rows'] = array();	
   if (isset($_POST['Submit'])) {
		// conferma tutto       
		if ($toDo == 'update') {
			// controlli in caso di modifica         
			if ($form['codice'] != $form['ref_code']) { 
				// se sto modificando il codice originario          
				// controllo che l'articolo ci sia gia'          
				$rs_assist = gaz_dbi_dyn_query('codice', $gTables['instal'], "codice = ".$form['codice'],"codice DESC",0,1);
				$rs = gaz_dbi_fetch_array($rs_assist);
				if ($rs) { 
					$msg .= "0+";
				}         
			}       
		} else {          
			// controllo che l'articolo ci sia gia'          
			$rs_articolo = gaz_dbi_dyn_query('codice', $gTables['instal'], "codice = ".$form['codice'],"codice DESC",0,1);
			$rs = gaz_dbi_fetch_array($rs_articolo);
			if ($rs) {             
				$msg .= "2+";
			}
		}    
		$msg .= (empty($form["codice"]) ? "5+" : '');
		//$msg .= (empty($form["descrizione"]) ? "6+" : '');
		if (empty($msg)) { 
		   if (preg_match("/^id_([0-9]+)$/",$form['clfoco'],$match)) {
            $new_clfoco = $anagrafica->getPartnerData($match[1],1);
            $form['clfoco']=$anagrafica->anagra_to_clfoco($new_clfoco,$admin_aziend['mascli']);
         }
			// aggiorno il db          
			if ($toDo == 'insert') {
                if ( $form['clfoco']==0 ) $form['clfoco']=103000001;
				gaz_dbi_table_insert('instal',$form);
			} elseif ($toDo == 'update') {             
                if ( $form['clfoco']==0 ) $form['clfoco']=103000001;
				gaz_dbi_table_update('instal',$form['ref_code'],$form);
			}          
			header("Location: ".$form['ritorno']);
			exit;
		}    
	} elseif (isset($_POST['Return'])) { // torno indietro          
		header("Location: report_install.php");
        exit;
	}
} 
elseif (!isset($_POST['Update']) && isset($_GET['Update'])) { 
	$assist = gaz_dbi_get_row($gTables['instal'],"codice",$_GET['codice']);
	//se e' il primo accesso per UPDATE    
	$anagrafica = new Anagrafica();
   $cliente = $anagrafica->getPartner($assist['clfoco']);
	$form = gaz_dbi_get_row($gTables['instal'], 'codice', $_GET['codice']);
	$form['search']['clfoco']=substr($cliente['ragso1'],0,10);
   
   //$form['codart'] = $assist['codart'];
   //$form['cosear'] = $assist['codart'];
   $form['ritorno']="../../modules/suppor/report_install.php";
   $form['ref_code']=$form['codice'];
    
} 
else { 
	//se e' il primo accesso per INSERT   
	$form=gaz_dbi_fields('assist');
	$rs_ultima_ass = gaz_dbi_dyn_query("codice", $gTables['instal'],$where,"codice desc");
	$ultimo_documento = gaz_dbi_fetch_array($rs_ultima_ass);
	// se e' il primo documento dell'anno, resetto il contatore   
	if ($ultimo_documento) {      
		$form['codice'] = $ultimo_documento['codice'] + 1;
	} else {      
		$form['codice'] = 1;
	}  
	//$form['tipo'] = 'ASS';
   $form['utente'] = $_SESSION["Login"];
	$form['datainst'] = date("Y-m-d");
   $form['seriale'] = '';
   //$form['cosear'] = '';
   //$form['codart'] = '';
   
   /*$rs_ultimo_tec = gaz_dbi_dyn_query("codice,tecnico", $gTables['assist'],"tecnico<>''","codice desc");
	$ultimo_tecnico = gaz_dbi_fetch_array($rs_ultimo_tec);
   $form['tecnico'] = $ultimo_tecnico['tecnico'];*/
	
	$form['search']['clfoco']='';
	$form['ritorno']=$_SERVER['HTTP_REFERER'];
	$form['ref_code']='';
}

// disegno maschera di inserimento modifica
require("../../library/include/header.php");
$script_transl = HeadMain();

if ($toDo == 'insert') echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".$script_transl['ins_this']."</div>";
else echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".$script_transl['upd_this']." '".$form['codice']."'</div>";
if (!empty($msg)) echo $script_transl['errors'][substr($msg, 0, 1)];
$select_cliente = new selectPartner('clfoco');
?>
<form method="POST" name="form" enctype="multipart/form-data">
<input type="hidden" name="ritorno" value="<?php echo $form['ritorno']; ?>">
<input type="hidden" name="ref_code" value="<?php echo $form['ref_code']; ?>">
<input type="hidden" name="codice" value="<?php echo $form['codice']; ?>">
<input type="hidden" name="<?php echo ucfirst($toDo); ?>" value="">
<div class="table-responsive">
<table class="Tlarge table table-striped table-bordered table-condensed">
<tr>
	<td class="FacetFieldCaptionTD"><?php echo $script_transl['codice']; ?> </td>
	<td colspan="2" class="FacetDataTD">
		<button ><?php echo $form['codice']; ?></button>
	</td>
</tr>
<tr>
	<td class="FacetFieldCaptionTD">Data Installazione</td>
	<td colspan="2" class="FacetDataTD">
		<input type="text" name="datainst" value="<?php echo $form['datainst']; ?>" align="right" maxlength="255" size="70"/>
	</td>
</tr>
<tr>
	<td class="FacetFieldCaptionTD"><?php echo $script_transl['cliente']; ?> </td>
	<td colspan="2" class="FacetDataTD">
	<?php 
		$select_cliente->selectDocPartner('clfoco',$form['clfoco'],$form['search']['clfoco'],'clfoco',$script_transl['mesg'],$admin_aziend['mascli']);
	?>
</td>
</tr>
<tr>
	<td class="FacetFieldCaptionTD">Oggetto</td>
	<td colspan="2" class="FacetDataTD">
		<input type="text" name="oggetto" value="<?php echo $form['oggetto']; ?>" align="right" maxlength="255" size="70"/>
	</td>
</tr>
<tr>
	<td class="FacetFieldCaptionTD"><?php echo $script_transl['descrizione']; ?> </td>
	<td colspan="2" class="FacetDataTD">
		<input type="text" name="descrizione" value="<?php echo $form['descrizione']; ?>" maxlength="255" size="70" />
	</td>
</tr>
<tr>
	<td class="FacetFieldCaptionTD">Note </td>
	<td colspan="2" class="FacetDataTD">
		<textarea type="text" name="note" align="right" cols="67" rows="3" class="mceClass"><?php echo $form['note']; ?></textarea>
	</td>
</tr>
<tr>
	<td class="FacetFieldCaptionTD">Seriale</td>
	<td colspan="2" class="FacetDataTD">
		<input type="text" name="seriale" value="<?php echo $form['seriale']; ?>" align="right" maxlength="255" size="70"/>
	</td>
</tr>
<tr>
	<td class="FacetFieldCaptionTD"><?php echo $script_transl['sqn']; ?></td>
	<td  class="FacetDataTD">
		<input name="Return" type="submit" value="<?php echo $script_transl['return']; ?>!">
	</td>
	<td  class="FacetDataTD" align="right">
		<input name="Submit" type="submit" value="<?php echo strtoupper($script_transl[$toDo]); ?>!">
	</td>
</tr>
</table>
</div>

<?php
if ( !isset($_GET["Insert"]) ) {
   $_GET['auxil']="";
   $_GET['flt_cliente']=$form['clfoco'];
   $_GET['flt_tecnico']="tutti";
   $_GET['flt_stato']="tutti";
   $_GET['flt_passo']="20";
   $_GET['include']="included";
   $_GET['idinstallazione']=$form['id'];
   $_GET["all"]="all";
   $num = gaz_dbi_record_count ( $gTables['assist'], "idinstallazione=0 and clfoco=".$form["clfoco"] );
   
   if ( $num > 0  ) {
      echo "<br><center>Ci sono assistenze non assegnate per ".$cliente['ragso1']."</center><br>";
      $result = gaz_dbi_dyn_query($gTables['assist'].".*", $gTables['assist'],"idinstallazione=0 and clfoco=".$form["clfoco"], $orderby, $limit, $passo);
      echo "<table class='Tlarge table table-striped table-bordered table-condensed table-responsive'>";
      while ( $row = gaz_dbi_fetch_array($result) ) {
         if ( $row['tipo']=="ASS" ) {
            $tipo = "Intervento di assistenza";
            $color = "#5bc0de";
         }
         else 
         {
            $tipo = "Assistenza periodica";
            $color = "#428bca";
         }
         
         echo "<tr><td bgcolor='$color'>".$row['codice']."</td>";
         echo "<td bgcolor='$color'>".$tipo."</td>";
         echo "<td bgcolor='$color'>".$row['data']."</td>";
         echo "<td bgcolor='$color'>".$row['oggetto']."</td>";
         echo "<td bgcolor='$color'><a class='btn btn-xs btn-danger' href='associa_install.php?id=".$row['id']."&ass=".$form['id']."'><i class='glyphicon glyphicon-retweet'></i> Associa</a></td></tr>";
      }
      echo "</table>";         
   }   
   
   include "report_assist.php";

   $_GET['auxil']="";
   $_GET['flt_cliente']=$form['clfoco'];
   $_GET['flt_tecnico']="tutti";
   $_GET['flt_stato']="tutti";
   $_GET['flt_passo']="20";
   $_GET['include']="included";
   $_GET['idinstallazione']=$form['id'];
   $_GET["all"]="all";
   //echo $form['id']." ".$form['clfoco'];
   //$num = gaz_dbi_record_count ( $gTables['assist'], "idinstallazione=".$form["id"]." and tipo='ASP' and clfoco=".$form["clfoco"] );
   //if ( $num > 0  ) {
   include "report_period.php";
   echo "</div>";
} 
?>
<script src="../../js/custom/autocomplete.js"></script>
<script type="text/javascript">
    tinyMCE.init({
        menubar: false,
        statusbar: false
    });
</script>
<!--</div>-->
</form>

<?php
require("../../library/include/footer.php");
?>


<!--<script src="../../js/custom/autocomplete.js"></script>
<script type="text/javascript">
function updateInputStato(ish){
    document.getElementById("stato").value = ish;
}
function updateInputTecnico(ish){
    document.getElementById("tecnico").value = ish;
}
function calculateTime() {
        var minend = parseInt($("select[name='ora_fine']").val().split(':')[1],10);
		var minstart = parseInt($("select[name='ora_inizio']").val().split(':')[1],10);
		var hstart = parseInt($("select[name='ora_inizio']").val().split(':')[0],10);
		var hend   = parseInt($("select[name='ora_fine']").val().split(':')[0],10);
		
		var min = minend - minstart;
		if ( min<=-1 ) {
			min = "30";
			hend -= 1;
		}
		
		
		if ( hstart <= hend ) {
			var hour = hend - hstart;
		} else {
			var hour = (hend+24)-hstart;
		}
		if ( min == "30" ) min = "50";
		document.getElementById('ore').value = hour+"."+min;
    }
</script>
<script>
$( document.getElementById("toggleTec") ).click(function() {
  $( "#tecnico" ).fadeIn('fast');//toggle( "fold" );
});
$( document.getElementById("toggleSta") ).click(function() {
  $( "#stato" ).fadeIn('fast');
});
$(function() {
 $("#tecnico").fadeOut('fast');//toggle('fold');
 $("#stato").toggle('fold');
})
</script>-->
<?php
   //echo "<pre>";
   //print_r(get_defined_vars());
   //echo "</pre>";
?>