<?php
/*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2018 - Antonio De Vincentiis Montesilvano (PE)
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

$message = "";
$anno = date("Y");

$where = "tipdoc like 'A_R'";
$orderby = "id_tes DESC";
$all = $where;

if (isset($_GET['flt_tipo']) && $_GET['flt_tipo']!= "All") {
    if ($_GET['flt_tipo'] != "") {
        $tipdoc = $_GET['flt_tipo'];
        $auxil .= "&flt_tipo=" . $tipdoc;
        $where = " tipdoc like '%$tipdoc%'";
    }
} else {
    $tipdoc = '';
}
if (isset($_GET['protoc'])) {
    if ($_GET['protoc'] != "") {
        $protocollo = $_GET['protoc'];
        $where .= " and id_tes = $protocollo";
    }
} else {
    $tipdoc = '';
}
if (isset($_GET['numdoc'])) {
    if ($_GET['numdoc'] != "") {
        $numdoc = $_GET['numdoc'];
        $where .= " AND numdoc like '%$numdoc%'";
    }
} else {
    $numfat = '';
}
if (isset($_GET['flt_year'])) {
	if ($_GET['flt_year'] != "" && $_GET['flt_year']!= "All") {
        $year = $_GET['flt_year'];
		$where .= " and datemi >= \"".$year."/01/01\" and datemi <= \"".$year."/12/31\"";
    } else {
		$year = 'All';
	}
} else {
	$year = 'All';    
}
if (isset($_GET['flt_ragso1'])) {
    if ($_GET['flt_ragso1'] != "") {
        $ragso1 = $_GET['flt_ragso1'];
		if ($ragso1!="All") {
			$where .= " and ".$gTables["tesbro"].".clfoco = ".$ragso1;
		}
    }
} else {
    $ragso1 = '';
}
if (isset($_GET['all'])) {
    $year = "";
    $numdoc = "";
    $tipdoc = "";
    $ragso1 = "";
    $protocollo = "";
	$passo = 100000;
	$where = "tipdoc like 'A_R'";
    $auxil = "&all=yes";
}


// visualizza i bottoni dei documenti di evasione associati all'ordine
function mostra_documenti_associati($ordine) {
    global $gTables;
    $rigdoc_result = gaz_dbi_dyn_query('DISTINCT id_tes', $gTables['rigdoc'], "id_order = " . $ordine, 'id_tes ASC');
    while ( $rigdoc = gaz_dbi_fetch_array($rigdoc_result) ) {
        $tesdoc_result = gaz_dbi_dyn_query('*', $gTables['tesdoc'], "id_tes = " . $rigdoc["id_tes"], 'id_tes DESC');
        $tesdoc_r = gaz_dbi_fetch_array($tesdoc_result);
        if ($tesdoc_r["tipdoc"] == "AFA") {
            echo "<a class=\"btn btn-xs btn-default\" title=\"visualizza la fattura immediata\" href=\"stampa_docacq.php?id_tes=" . $tesdoc_r["id_tes"] . "\">";
            echo "fatt. " . $tesdoc_r["numfat"];
            echo "</a> ";
        } else {
            echo $tesdoc_r["tipdoc"] . $rigbro_r["id_doc"] . " ";
        }
    }
}
require("../../library/include/header.php");
$script_transl=HeadMain(0,array('custom/modal_form'));

?>
<script>
$(function() {
   $( "#dialog" ).dialog({
      autoOpen: false
   });
});

function confirmemail(cod_partner,id_tes) {
	$.get("search_email_address.php",
		  {clfoco: cod_partner},
		  function (data) {
			var j=0;
			$.each(data, function (i, value) {
				if (j==0){
					$("#mailbutt").append("<div>Indirizzi archiviati:</div>");
				}
				$("#mailbutt").append("<div align='center'><button id='fillmail_" + j+"'>" + value.email + "</button></div>");
                $("#fillmail_" + j).click(function () {
					$("#mailaddress").val(value.email);
				});
				j++;
			});
		  }, "json"
         );
		 
	$( function() {
    var dialog
	,	 
    emailRegex = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/,
	dialog = $("#confirm_email").dialog({
		modal: true,
		show: "blind",
		hide: "explode",
		buttons: {
			Invia: function() {
				if ( !( emailRegex.test( $("#mailaddress").val() ) ) ) {
					alert('Mail formalmente errata');
				} else {
					$("#mailbutt div").remove();
					var dest=$("#mailaddress").val();
                    window.location.href = 'stampa_prefor.php?id_tes='+id_tes+'&dest='+dest;
				}

				}
		},
		close: function(){
				$("#mailbutt div").remove();
		}

	});
	});
}

function choicePartner(row)
{
	$( "#search_partner"+row ).autocomplete({
		source: "../../modules/root/search.php?opt=supplier",
		minLength: 2,
        html: true, // optional (jquery.ui.autocomplete.html.js required)
 
      	// optional (if other layers overlap autocomplete list)
        open: function(event, ui) {
            $(".ui-autocomplete").css("z-index", 1000);
        },
		select: function(event, ui) {
			$(".supplier_name").replaceWith(ui.item.value);
			$("#confirm_duplicate").dialog({
				modal: true,
				show: "blind",
				hide: "explode",
				buttons: {
					Duplica: function() {
						window.location.href = 'duplicate_broacq.php?id_tes='+row+'&duplicate='+ui.item.codice;
						}
					},
				close: function(){}
				});
			}		
	});
}

</script>

<div align="center" class="FacetFormHeaderFont"><?php echo $script_transl["title2"]; ?></div>
<?php
$recordnav = new recordnav($gTables['tesbro'], $where, $limit, $passo);
$recordnav -> output();
?>
<form method="GET" >
<div style="display:none" id="dialog" title="<?php echo $script_transl['mail_alert0']; ?>">
      <p id="mail_alert1"><?php echo $script_transl['mail_alert1']; ?></p>
      <p class="ui-state-highlight" id="mail_adrs"></p>
      <p id="mail_alert2"><?php echo $script_transl['mail_alert2']; ?></p>
      <p class="ui-state-highlight" id="mail_attc"></p>
</div>
    <div class="box-primary table-responsive">
    <table class="Tlarge table table-striped table-bordered table-condensed">
<!-- inizio il filtro ricerca -->
<tr>
    <td colspan="1" class="FacetFieldCaptionTD">
        <input type="text" placeholder="Cerca Prot." class="input-sm form-control" name="protoc" value="<?php if (isset($protocollo)) print $protocollo; ?>" maxlength="6" size="3" tabindex="1" class="FacetInput">
    </td>
	<td colspan="1" class="FacetFieldCaptionTD">
		<select class="form-control input-sm" name="flt_tipo" onchange="this.form.submit()">
			<option value="All"><?php echo $script_transl['tuttitipi']; ?></option>
			<?php 
				$res = gaz_dbi_dyn_query("distinct tipdoc", $gTables["tesbro"], $all, $orderby, 0, 999);
				while ( $val = gaz_dbi_fetch_array($res) ) {
					if ( $tipdoc == $val["tipdoc"] ) $selected = "selected";
					else $selected = "";
					echo "<option value=\"".$val["tipdoc"]."\" ".$selected.">".$val["tipdoc"]."</option>";
				}
			?>
		</select>
	</td>
    <td colspan="1" class="FacetFieldCaptionTD">
		<input type="text" placeholder="Cerca Num." class="input-sm form-control" name="numdoc" value="<?php if (isset($numdoc)) print $numdoc; ?>" size="3" tabindex="3" class="FacetInput">			
	</td>
	<td colspan="1" class="FacetFieldCaptionTD">
		<select class="form-control input-sm" name="flt_year" onchange="this.form.submit()">
		<option value="All"><?php echo $script_transl['tuttianni']; ?></option>
		<?php $res = gaz_dbi_dyn_query("distinct YEAR(datemi) as year", $gTables["tesbro"], $all, $orderby, 0, 999);
			while ( $val = gaz_dbi_fetch_array($res) ) {
				if ( $year == $val["year"] ) $selected = "selected";
				else $selected = "";
				echo "<option value=\"".$val["year"]."\" ".$selected.">".$val["year"]."</option>";
			} ?>
		</select>
	</td>
	<td colspan="1" class="FacetFieldCaptionTD">
		<select class="form-control input-sm" name="flt_ragso1" onchange="this.form.submit()">
		<option value="All"><?php echo $script_transl['tuttiforni']; ?></option>
		<?php $res = gaz_dbi_dyn_query("distinct ".$gTables['anagra'].".ragso1,".$gTables["tesbro"].".clfoco", $gTables['tesbro'] . " LEFT JOIN " . $gTables['clfoco'] . " ON " . $gTables['tesbro'] . ".clfoco = " . $gTables['clfoco'] . ".codice LEFT JOIN " . $gTables['anagra'] . ' ON ' . $gTables['clfoco'] . '.id_anagra = ' . $gTables['anagra'] . '.id', $all, $orderby, 0, 999);
			while ( $val = gaz_dbi_fetch_array($res) ) {
				if ( $ragso1 == $val["clfoco"] ) $selected = "selected";
				else $selected = "";
				echo "<option value=\"".$val["clfoco"]."\" ".$selected.">".$val["ragso1"]."</option>";
			} ?>
		</select>
	</td>
	<td colspan="1" class="FacetFieldCaptionTD">
		&nbsp;
		<?php //gaz_filtro( "status", $_GET["status"], $gTables["tesbro"], $all, $orderby, "select" ); ?>
	</td>
	<td colspan="1" class="FacetFieldCaptionTD">
		&nbsp;
	</td>
	<td colspan="1" class="FacetFieldCaptionTD">
        <input type="submit" class="btn btn-sm btn-default" name="search" value="Cerca" tabindex="1" onClick="javascript:document.report.all.value = 1;">
    </td>
    <td colspan="1" class="FacetFieldCaptionTD">
        <input type="submit" class="btn btn-sm btn-default" name="all" value="Mostra tutti" onClick="javascript:document.report.all.value = 1;">
    </td>
</tr>

<tr>
<?php
$headers_tesdoc = array  (
              "ID" => "id_tes",
              "Produzione" => "id_orderman",
              "Numero" => "numdoc",
              "Data" => "datemi",
              "Fornitore" => "clfoco",
              "Status" => "",
              "Stampa" => "",
              "Duplica" => "",
              "Mail" => "",
              "Cancella" => ""
              );
$linkHeaders = new linkHeaders($headers_tesdoc);
$linkHeaders -> output();
?>
</tr>
<?php
if (!isset($_GET['flag_order']))  $orderby = "id_tes desc";
$result = gaz_dbi_dyn_query ($gTables['tesbro'].".*, ".$gTables['clfoco'].".codice", $gTables['tesbro']." LEFT JOIN ".$gTables['clfoco']." ON ".$gTables['tesbro'].".clfoco = ".$gTables['clfoco'].".codice ", $where, $orderby, $limit, $passo);
$ctrlprotoc = "";
$anagrafica = new Anagrafica();
while ($r = gaz_dbi_fetch_array($result)) {
    if ($r["tipdoc"] == 'APR') {
        $tipodoc="Preventivo";
        $modulo="stampa_prefor.php?id_tes=".$r['id_tes'];
        $modifi="admin_broacq.php?id_tes=".$r['id_tes']."&Update";
    }
    if ($r["tipdoc"] == 'AOR') {
        $tipodoc="Ordine";
        $modulo="stampa_ordfor.php?id_tes=".$r['id_tes'];
        $modifi="admin_broacq.php?id_tes=".$r['id_tes']."&Update";
    }
    $fornitore = $anagrafica->getPartner($r['clfoco']);
    echo "<tr class=\"FacetDataTD\">";
    if (! empty ($modifi)) {
       echo "<td>
	   			<a class=\"btn btn-xs btn-default\" href=\"".$modifi."\">
					<i class=\"glyphicon glyphicon-edit\">".$tipodoc."</i>&nbsp;".$r["id_tes"]."
				</a>
			 </td>";
    } else {
       echo "<td>
	   			<button class=\"btn btn-xs btn-default disabled\">".$r["id_tes"]." ".$tipodoc." &nbsp;</button>
			</td>";
    }
            // per colonna stato ordine e produzione
			$orderman_descr='';
            $remains_atleastone = false; // Almeno un rigo e' rimasto da evadere.
            $processed_atleastone = false; // Almeno un rigo e' gia' stato evaso.  
            $rigbro_result = gaz_dbi_dyn_query('*', $gTables['rigbro']." LEFT JOIN ".$gTables['orderman']." ON ".$gTables['rigbro'].".id_orderman = ".$gTables['orderman'].".id", "id_tes = " . $r["id_tes"] . " AND tiprig <=1 ", 'id_tes DESC');
            while ( $rigbro_r = gaz_dbi_fetch_array($rigbro_result) ) {
				if ($rigbro_r['id_orderman']>0){
					$orderman_descr=$rigbro_r['id_orderman'].'-'.$rigbro_r['description'];
				}
				$totale_da_evadere = $rigbro_r['quanti'];
                $totale_evaso = 0;
                $rigdoc_result = gaz_dbi_dyn_query('*', $gTables['rigdoc'], "id_order=" . $r['id_tes'] . " AND codart='".$rigbro_r['codart']."' AND tiprig <=1 ", 'id_tes DESC');
                while ($rigdoc_r = gaz_dbi_fetch_array($rigdoc_result)) {
                    $totale_evaso += $rigdoc_r['quanti'];
                    $processed_atleastone = true;
                }               
                if ( $totale_evaso != $totale_da_evadere ) {
                    $remains_atleastone = true;
                }
            }
    echo '			<td>'.$orderman_descr." &nbsp;</td>
			<td>".$r["numdoc"]." &nbsp;</td>
			<td>".gaz_format_date($r["datemi"])." &nbsp;</td>
			<td>".$fornitore["ragso1"]."&nbsp;</td>";
			//<td>".$r["status"]." &nbsp;</td>
            
            //
            // Se l'ordine e' da evadere completamente, verifica lo status ed
            // eventualmente lo aggiorna.
            //
            if ($remains_atleastone && !$processed_atleastone) {
                //
                // L'ordine e' completamente da evadere.
                //
                if ($r["status"] != "GENERATO") {
                    gaz_dbi_put_row($gTables['tesbro'], "id_tes", $r["id_tes"], "status", "RIGENERATO");
                }
                echo "<td><a class=\"btn btn-xs btn-warning\" href=\"select_evaord.php?id_tes=" . $r['id_tes'] . "\">evadi</a></td>";

            } elseif ($remains_atleastone) {
                echo "<td> ";

                $ultimo_documento = 0;
                //
                // Interroga la tabella gaz_XXXrigbro per le righe corrispondenti
                // a questa testata.
                //
                mostra_documenti_associati ( $r["id_tes"]);
                echo "<a class=\"btn btn-xs btn-warning\" href=\"select_evaord.php?id_tes=" . $r['id_tes'] . "\">evadi il rimanente</a></td>";
            } else {
                echo "<td>";
                //
                $ultimo_documento = 0;
                //
                // Interroga la tabella gaz_XXXrigbro per le righe corrispondenti
                // a questa testata.
                //
                mostra_documenti_associati ( $r["id_tes"]);
                echo "</td>";
            }

            echo "<td align=\"center\">
				<a class=\"btn btn-xs btn-default\" href=\"".$modulo."\" target=\"_blank\">
					<i class=\"glyphicon glyphicon-print\"></i>
				</a>
			</td>";
			echo '<td align="center" title="Stesso preventivo per altro fornitore"><button class="btn btn-default btn-sm" type="button" data-toggle="collapse" data-target="#duplicate_'.$r['id_tes'].'" aria-expanded="false" aria-controls="duplicate_'.$r['id_tes'].'"><i class="glyphicon glyphicon-tags"></i></button>';
            echo '<div class="collapse" id="duplicate_'.$r['id_tes'].'">Fornitore: <input id="search_partner'.$r['id_tes'].'" onClick="choicePartner(\''.$r['id_tes'].'\');"  value="" rigo="'. $r['id_tes'] .'" type="text" /></div></td><td align="center">';
    if (!empty($fornitore["e_mail"])) {
        echo ' <a class="btn btn-xs btn-default btn-email" onclick="confirmemail(\''.$r["clfoco"].'\',\''.$r['id_tes'].'\');" id="doc'.$r["id_tes"].'"><i class="glyphicon glyphicon-envelope"></i></a>';
    } else {
		echo '<a title="Non hai memorizzato l\'email per questo fornitore, inseriscila ora" target="_blank" href="admin_fornit.php?codice='.substr($r["codice"],3).'&Update"><i class="glyphicon glyphicon-edit"></i></a>';
	 }		  
    echo "	</td>
			<td align=\"center\">
				<a class=\"btn btn-xs btn-default btn-elimina\" href=\"delete_broacq.php?id_tes=".$r['id_tes']."\">
					<i class=\"glyphicon glyphicon-remove\"></i>
				</a>
			</td>
		  </tr>";
}
?>
 <tr><th class="FacetFieldCaptionTD" colspan="10"></th></tr>
</table>
    </div>
</form>
<div class="modal" id="confirm_email" title="Invia mail...">
    <fieldset>
        <div>
            <label for="mailaddress">all'indirizzo:</label>
            <input type="text"  placeholder="seleziona sotto oppure digita" value="" id="mailaddress" name="mailaddress" maxlength="50" />
        </div>
        <div id="mailbutt">
		</div>
    </fieldset>
</div>
<div class="modal" id="confirm_duplicate" title="Duplica preventivo">
    <fieldset>
        <div>
            <label for="duplicate">a:</label>
            <div class="supplier_name"><div/>
        </div>
    </fieldset>
</div>
<?php
require("../../library/include/footer.php");
?>