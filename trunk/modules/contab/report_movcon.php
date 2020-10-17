<?php
/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2020 - Antonio De Vincentiis Montesilvano (PE)
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
$admin_aziend = checkAdmin();
require("../../library/include/header.php");

$partner_select = !gaz_dbi_get_row($gTables['company_config'], 'var', 'partner_select_mode')['val'];
$tesmov_e_partners = $gTables['tesmov'] . " LEFT JOIN " . $gTables['clfoco'] . " ON " . $gTables['tesmov'] . ".clfoco = " . $gTables['clfoco'] . ".codice LEFT JOIN " . $gTables['anagra'] . ' ON ' . $gTables['clfoco'] . '.id_anagra = ' . $gTables['anagra'] . '.id';

$script_transl = HeadMain('', '', 'admin_movcon');

// campi ammissibili per la ricerca
$search_fields = [
    'movimento'
        => "{$gTables['tesmov']}.id_tes = %d",
    'anno'
        => "YEAR(datreg) = %d",
    'causale'
        => "caucon LIKE '%s%%'",
    'descri'
        => $partner_select ? "clfoco = '%s'" : "{$gTables['anagra']}.ragso1 LIKE '%%%s%%'"
];

// creo l'array (header => campi) per l'ordinamento dei record
$sortable_headers = array(
    "N." => "id_tes",
    $script_transl['date_reg'] => "datreg",
    $script_transl['caucon'] => "caucon",
    $script_transl['descri'] => "descri",
    $script_transl['protoc'] => "",
    $script_transl['numdoc'] => "",
    $script_transl['amount'] => "",
    $script_transl['source'] => "",
    $script_transl['delete'] => ""
);

function getPaymov($id_tes, $clfoco) { // restituisce l'id_rig se c'è un movimento di scadenzario
    global $gTables;
    $rig_res = gaz_dbi_dyn_query('*', $gTables['rigmoc'], "id_tes = " . $id_tes . " AND codcon=" . $clfoco, 'id_rig ASC', 0, 1);
    $rig_r = gaz_dbi_fetch_array($rig_res);
    if ($rig_r) {
        $pay_res = gaz_dbi_dyn_query('*', $gTables['paymov'], "id_rigmoc_pay = " . $rig_r['id_rig'], 'expiry ASC', 0, 1);
        $pay_r = gaz_dbi_fetch_array($pay_res);
        if ($pay_r) {
            return $rig_r['id_rig'];
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function getDocRef($data) {
    global $gTables;
    $r = '';
    switch ($data['caucon']) {
        case "FAI":
        case "FND":
        case "FNC":
            $tesdoc_result = gaz_dbi_dyn_query('*', $gTables['tesdoc'], "id_con = " . $data["id_tes"], 'id_tes DESC', 0, 1);
            $tesdoc_r = gaz_dbi_fetch_array($tesdoc_result);
            if ($tesdoc_r) {
                $r = "../vendit/stampa_docven.php?id_tes=" . $tesdoc_r["id_tes"];
            }
            break;
        case "FAD":
            $tesdoc_result = gaz_dbi_dyn_query('*', $gTables['tesdoc'], "tipdoc = \"" . $data["caucon"] . "\" AND seziva = " . $data["seziva"] . " AND protoc = " . $data["protoc"] . " AND numfat = '" . $data["numdoc"] . "' AND datfat = \"" . $data["datdoc"] . "\"", 'id_tes DESC');
            $tesdoc_r = gaz_dbi_fetch_array($tesdoc_result);
            if ($tesdoc_r) {
                $r = "../vendit/stampa_docven.php?td=2&si=" . $tesdoc_r["seziva"] . "&pi=" . $tesdoc_r['protoc'] . "&pf=" . $tesdoc_r['protoc'] . "&di=" . $tesdoc_r["datfat"] . "&df=" . $tesdoc_r["datfat"];
            }
            break;
        case "RIB":
        case "TRA":
            $effett_result = gaz_dbi_dyn_query('*', $gTables['effett'], "id_con = " . $data["id_tes"], 'id_tes', 0, 1);
            $effett_r = gaz_dbi_fetch_array($effett_result);
            if ($effett_r) {
                $r = "../vendit/stampa_effett.php?id_tes=" . $effett_r["id_tes"];
            }
            break;
    }
    return $r;
}
?>
<div align="center" class="FacetFormHeaderFont"><?php echo $script_transl['report']; ?></div>

<?php 
$t = new TableSorter(
    !$partner_select && isset($_GET["descri"]) ? $tesmov_e_partners : $gTables['tesmov'],
    $passo, ['id_tes' => 'desc']);
$t -> output_navbar();
?>
<script>
$(function() {
	$("#dialog_delete").dialog({ autoOpen: false });
	$('.dialog_delete').click(function() {
		$("p#idcodice").html($(this).attr("ref"));
		$("p#iddescri").html($(this).attr("descri"));
		var id = $(this).attr('ref');		
		$( "#dialog_delete" ).dialog({
			minHeight: 1,
			width: "auto",
			modal: "true",
			show: "blind",
			hide: "explode",
			buttons: {
				delete:{ 
					text:'Elimina', 
					'class':'btn btn-danger delete-button',
					click:function (event, ui) {
					$.ajax({
						data: {'type':'movcon',id_tes:id},
						type: 'POST',
						url: '../contab/delete.php',
						success: function(output){
		                    //alert(output);
							window.location.replace("./report_movcon.php");
						}
					});
				}},
				"Non eliminare": function() {
					$(this).dialog("close");
				}
			}
		});
		$("#dialog_delete" ).dialog( "open" );  
	});
});
</script>
<form method="GET">
	<div style="display:none" id="dialog_delete" title="Conferma eliminazione">
	<p><b>movimento contabile:</b></p>
	<p>ID:</p>
	<p class="ui-state-highlight" id="idcodice"></p>
	<p>Descrizione:</p>
	<p class="ui-state-highlight" id="iddescri"></p>
</div>
	<div class="table-responsive">
    <table class="Tlarge table table-striped table-bordered table-condensed">
        <tr>
            <td class="FacetFieldCaptionTD">
                <input type="text" placeholder="Movimento" class="input-xs form-control FacetInput" name="movimento"
                       value="<?php if (isset($movimento)) echo $movimento; ?>" maxlength ="6" tabindex="1">
            </td>
            <td class="FacetFieldCaptionTD">
                <?php // uso "anno" per selezionare datreg
                gaz_flt_disp_select("anno", "YEAR(datreg) AS anno", $gTables["tesmov"], "", "anno DESC"); ?>
            </td>
            <td align="right" class="FacetFieldCaptionTD">
                <?php // uso "causale" per selezionare caucon
                gaz_flt_disp_select("causale", "caucon AS causale", $gTables["tesmov"], "caucon > ''", "causale ASC"); ?>
            </td>
            <td align="right" class="FacetFieldCaptionTD">
                <?php if ($partner_select) {
                        gaz_flt_disp_select("descri", "clfoco AS descri, ragso1 as nome", 
					    $tesmov_e_partners,
                                            "", "nome ASC", "nome");
                    } else {
                        gaz_flt_disp_int("descri", "Cliente");
                    }
		 ?>
            </td>
            <td class="FacetFieldCaptionTD"></td>
            <td class="FacetFieldCaptionTD"></td>
            <td class="FacetFieldCaptionTD"></td>
            <td class="FacetFieldCaptionTD">
                <input type="submit" name="search" class="btn btn-xs btn-default" value="<?php echo $script_transl['search']; ?>" tabindex="1" onClick="">
                <?php $t->output_order_form(); ?>
            </td>
            <td class="FacetFieldCaptionTD">
                <a class="btn btn-xs btn-default" href="?">Reset</a>
            </td>
        </tr>
        <tr>
<?php
            $result = gaz_dbi_dyn_query("id_tes, datreg, clfoco, caucon, ".$gTables['tesmov'].".descri, protoc, numdoc, seziva, datdoc", $tesmov_e_partners, $t->where, $t->orderby, $t->getOffset(), $t->getLimit());
            $t -> output_headers(); 
?>
        </tr>
<?php
$anagrafica = new Anagrafica();
while ($a_row = gaz_dbi_fetch_array($result)) {
    
    $paymov = false;
    if (substr($a_row["clfoco"], 0, 3) == $admin_aziend['mascli'] or substr($a_row["clfoco"], 0, 3) == $admin_aziend['masfor']) {
        if (substr($a_row["clfoco"], 0, 3) == $admin_aziend['mascli']) {
            $paymov = getPaymov($a_row["id_tes"], $a_row["clfoco"]);
        }
        $account = $anagrafica->getPartner($a_row["clfoco"], true);
        if ((!empty($account['descri']) || !empty($a_row['numdoc'])) && $a_row['caucon'] != 'APE' && $a_row['caucon'] != 'CHI'){
            $a_row['descri'].=' ('.$account['descri'].')';
        }
    }
    // INIZIO crezione tabella per la visualizzazione sul tootip di tutto il movimento e facccio la somma del totale movimento 
    $res_rig = gaz_dbi_dyn_query("*", $gTables['rigmoc'], 'id_tes=' . $a_row["id_tes"], 'id_rig');
    $tt = '<table><th colspan=3 >' . $a_row['descri'] . '</th>';
    $tot = 0.00;
    while ($rr = gaz_dbi_fetch_array($res_rig)) {
        $account = $anagrafica->getPartner($rr["codcon"], true);
        $tt .= '<tr><td>' . htmlspecialchars( $account['descri'] ) . '</td><td align=right>' . $rr['import'] . '</td><td align=right>' . $rr['darave'] . '</td></tr>';
        if ($rr['darave'] == 'D') {
            $tot += $rr['import'];
        }
    }
    $tt .= '</table>';
    // FINE creazione tabella per il tooltip
    echo "<tr class=\"FacetDataTD\">";
    echo "<td align=\"right\"><a class=\"btn btn-xs btn-default btn-edit\" href=\"admin_movcon.php?id_tes=" . $a_row["id_tes"] . "&Update\" title=\"Modifica\"><i class=\"glyphicon glyphicon-edit\"></i>&nbsp;" . $a_row["id_tes"] . "</a> &nbsp</td>";
    echo "<td align=\"center\">" . gaz_format_date($a_row["datreg"]) . " &nbsp;</td>";
    echo '<td align="center"><div class="gazie-tooltip" data-type="movcon-thumb" data-id="' . $a_row["id_tes"] . '" data-title="' . str_replace("\"", "'", $tt) . '" >' . $a_row["caucon"] . "</div></td>";
    echo '<td><div class="gazie-tooltip" data-type="movcon-thumb" data-id="' . $a_row["id_tes"] . '" data-title="' . str_replace("\"", "'", $tt) . '" >' . $a_row["descri"] . '</div></td>';
    if ($a_row["protoc"] > 0) {
        echo "<td align=\"center\">" . $a_row["protoc"] . "/" . $a_row["seziva"] . "";
        echo "</td>";
    } else {
        echo "<td></td>";
    }
    echo "<td align=\"center\">" . $a_row["numdoc"] . "</td>";
    echo '<td align="right">' . gaz_format_number($tot) . '</td>';
    echo "<td align=\"center\">";
    $docref = getDocRef($a_row);
    if (!empty($docref)) {
        echo "<a class=\"btn btn-xs btn-default btn-default\" title=\"" . $script_transl['sourcedoc'] . "\" href=\"$docref\" target=\"_blank\"><i class=\"glyphicon glyphicon-print\"></i></a>";
    } elseif ($paymov) {
        echo "<a class=\"btn btn-xs btn-default btn-default\" title=\"" . $script_transl['customer_receipt'] . "\" href=\"../vendit/print_customer_payment_receipt.php?id_rig=" . $paymov . "\" target=\"_blank\"><i class=\"glyphicon glyphicon-check\"></i>&nbsp;<i class=\"glyphicon glyphicon-euro\"></i>&nbsp;<i class=\"glyphicon glyphicon-print\"></i></a>";
    }
    echo "</td>";
    echo "<td align=\"center\">";
	?>
	<a class="btn btn-xs btn-default btn-elimina dialog_delete" title="Cancella il movimento" ref="<?php echo $a_row['id_tes'];?>" descri="<?php echo $a_row['descri'];?>">
		<i class="glyphicon glyphicon-remove"></i>
	</a>
	<?php
    echo "</td></tr>\n";
}
?>
    </table></div>
</form>

<script>
$(document).ready(function(){
    var selects = $("select");
    // la funzione gaz_flt_dsp_select usa "All", qui usiamo invece valori vuoti
    // (in questo modo i campi non usati possono essere esclusi)        
    $("option", selects).filter(function(){ return this.value == "All"; }).val("");

    // la stessa funzione imposta onchange="this.form.submit()" sulle select: 
    // l'azione non lancia un evento "submit" e non può essere intercettata.
    // per non andare a modificare la funzione rimpiazziamo l'attributo onchange:
    selects.attr('onchange', null).change(function() { $(this.form).submit(); });
    
    // così ora possiamo intercettare tutti i submit
    $("form").submit(function() {
        $(this).find(":input").filter(function(){ return !this.value; }).attr("disabled", "disabled");
        return true; // ensure form still submits
    });

    // Un-disable form fields when page loads, in case they click back after submission
    $( "form" ).find( ":input" ).prop( "disabled", false );
});
</script>

<?php
require("../../library/include/footer.php");
?>