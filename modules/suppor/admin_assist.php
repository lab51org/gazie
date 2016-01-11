<?php
/*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2014 - Antonio De Vincentiis Montesilvano (PE)
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
   // ...e della testata
   foreach($_POST['search'] as $k=>$v){
      $form['search'][$k]=$v;
   }
	$form['codice'] = trim($form['codice']);
	$form['descrizione'] = $_POST['descrizione'];
	$form['clfoco'] = $_POST['clfoco'];
	$form['ritorno'] = $_POST['ritorno'];
	$form['ref_code']= $_POST['ref_code'];
	$form['ore']=	$_POST['ore'];
	$form['utente'] = $_SESSION["Login"];
    
	$form['rows'] = array();	
   if (isset($_POST['Submit'])) {
		// conferma tutto       
		if ($toDo == 'update') {
			// controlli in caso di modifica         
			if ($form['codice'] != $form['ref_code']) { 
				// se sto modificando il codice originario          
				// controllo che l'articolo ci sia gia'          
				$rs_assist = gaz_dbi_dyn_query('codice', $gTables['assist'], "codice = ".$form['codice'],"codice DESC",0,1);
				$rs = gaz_dbi_fetch_array($rs_assist);
				if ($rs) { 
					$msg .= "0+";
				}         
			}       
		} else {          
			// controllo che l'articolo ci sia gia'          
			$rs_articolo = gaz_dbi_dyn_query('codice', $gTables['assist'], "codice = ".$form['codice'],"codice DESC",0,1);
			$rs = gaz_dbi_fetch_array($rs_articolo);
			if ($rs) {             
				$msg .= "2+";
			}
		}    
		$msg .= (empty($form["codice"]) ? "5+" : '');
		$msg .= (empty($form["descrizione"]) ? "6+" : '');
		if (empty($msg)) { 
		   if (preg_match("/^id_([0-9]+)$/",$form['clfoco'],$match)) {
            $new_clfoco = $anagrafica->getPartnerData($match[1],1);
            $form['clfoco']=$anagrafica->anagra_to_clfoco($new_clfoco,$admin_aziend['mascli']);
         }
			// aggiorno il db          
			if ($toDo == 'insert') {
                if ( $form['clfoco']==0 ) $form['clfoco']=103000001;
				gaz_dbi_table_insert('assist',$form);
			} elseif ($toDo == 'update') {             
                if ( $form['clfoco']==0 ) $form['clfoco']=103000001;
				gaz_dbi_table_update('assist',$form['ref_code'],$form);
			}          
			header("Location: ".$form['ritorno']);
			exit;
		}    
	} elseif (isset($_POST['Return'])) { // torno indietro          
		header("Location: ".$form['ritorno']);
        exit;
	}
} elseif (!isset($_POST['Update']) && isset($_GET['Update'])) { 
	$assist = gaz_dbi_get_row($gTables['assist'],"codice",$_GET['codice']);
	//se e' il primo accesso per UPDATE    
	$anagrafica = new Anagrafica();
    $cliente = $anagrafica->getPartner($assist['clfoco']);
	$form = gaz_dbi_get_row($gTables['assist'], 'codice', $_GET['codice']);
	$form['search']['clfoco']=substr($cliente['ragso1'],0,10);
    $form['ritorno']=$_SERVER['HTTP_REFERER'];
    $form['ref_code']=$form['codice'];
} else { 
	//se e' il primo accesso per INSERT   
	$form=gaz_dbi_fields('assist');
	$rs_ultima_ass = gaz_dbi_dyn_query("codice", $gTables['assist'],$where,"codice desc");
	$ultimo_documento = gaz_dbi_fetch_array($rs_ultima_ass);
	// se e' il primo documento dell'anno, resetto il contatore   
	if ($ultimo_documento) {      
		$form['codice'] = $ultimo_documento['codice'] + 1;
	} else {      
		$form['codice'] = 1;
	}   
    $form['utente'] = $_SESSION["Login"];
	$form['data'] = date("Y-m-d");
	$form['ore'] = "0.00";
	$form['stato'] = 'aperto';
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
<table class="Tmiddle">
<tr>
	<td class="FacetFieldCaptionTD"><?php echo $script_transl['codice']; ?> </td>
	<td colspan="2" class="FacetDataTD">
		<button ><?php echo $form['codice']; ?></button>
	</td>
</tr>
<tr>
	<td class="FacetFieldCaptionTD">Data</td>
	<td colspan="2" class="FacetDataTD">
		<input type="text" name="data" value="<?php echo $form['data']; ?>" align="right" maxlength="255" size="70"/>
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
	<td class="FacetFieldCaptionTD"><?php echo $script_transl['tecnico']; ?> </td>
	<td colspan="2" class="FacetDataTD">
            <select name="ctecnico" onchange="updateInputTecnico(this.value)">
			<?php
			$result = gaz_dbi_dyn_query(" DISTINCT ".$gTables['assist'].".tecnico", $gTables['assist'],"", "tecnico", "0", "9999");
			while ($tecnici = gaz_dbi_fetch_array($result)) {				
					if ( $form['tecnico'] == $tecnici["tecnico"] ) $selected = "selected"; 
					else $selected = "";
					echo "<option value=\"".$tecnici["tecnico"]."\" ".$selected.">".$tecnici["tecnico"]."</option>";
			}
			?>
            </select> 
        <input type="text" name="tecnico" id="tecnico" value="<?php echo $form['tecnico']; ?>" align="right" maxlength="255" size="40"/>       
        <button id="toggleTec" type="button">Altro</button>
	</td>
</tr>
<tr>
	<td class="FacetFieldCaptionTD"><?php echo $script_transl['oggetto']; ?> </td>
	<td colspan="2" class="FacetDataTD">
		<input type="text" name="oggetto" value="<?php echo $form['oggetto']; ?>" align="right" maxlength="255" size="70"/>
	</td>
</tr>
<tr>
	<td class="FacetFieldCaptionTD"><?php echo $script_transl['descrizione']; ?> </td>
	<td colspan="2" class="FacetDataTD">
		<textarea type="text" name="descrizione" align="right" maxlength="255" cols="67" rows="4"><?php echo $form['descrizione']; ?></textarea>
	</td>
</tr>
<tr>
	<td class="FacetFieldCaptionTD"><?php echo $script_transl['info_agg']; ?> </td>
	<td colspan="2" class="FacetDataTD">
		<input type="text" name="info_agg" value="<?php echo $form['info_agg']; ?>" align="right" maxlength="255" size="70"/>
	</td>
</tr>
<tr>
	<td class="FacetFieldCaptionTD">Ore</td>
	<td colspan="2" class="FacetDataTD">
		<input type="text" name="ore" value="<?php echo $form['ore']; ?>" align="right" maxlength="255" size="70"/>
	</td>
</tr>
<tr>
	<td class="FacetFieldCaptionTD"><?php echo $script_transl['stato']; ?> </td>
	<td colspan="2" class="FacetDataTD">
		<select name="cstato" onchange="updateInputStato(this.value)">
			<?php
			$result = gaz_dbi_dyn_query(" DISTINCT ".$gTables['assist'].".stato", $gTables['assist'],"", "stato", "0", "9999");
			while ($stati = gaz_dbi_fetch_array($result)) {				
					if ( $form['stato'] == $stati["stato"] ) $selected = "selected"; 
					else $selected = "";
					echo "<option value=\"".$stati["stato"]."\" ".$selected.">".$stati["stato"]."</option>";
			}
			?>
            <option value="chiuso" <?php if ( $form['stato']=='chiuso') echo '"selected"'; ?>>chiuso</option>";
		</select> 
		<input type="text" name="stato" id="stato" value="<?php echo $form['stato']; ?>" align="right" maxlength="255" size="40"/>
        <button id="toggleSta" type="button">Altro</button>
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

</form>
</div><!-- chiude div container role main --></body>
</html>
<script type="text/javascript">
function updateInputStato(ish){
    document.getElementById("stato").value = ish;
}
function updateInputTecnico(ish){
    document.getElementById("tecnico").value = ish;
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
</script>