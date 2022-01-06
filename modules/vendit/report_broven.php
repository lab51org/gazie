<?php
/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2022 - Antonio De Vincentiis Montesilvano (PE)
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

function getDayNameFromDayNumber($day_number) {
    return ucfirst(utf8_encode(strftime('%A', mktime(0, 0, 0, 3, 19+$day_number, 2017))));
}

// funzione di utilità generale, adatta a mysqli.inc.php
function cols_from($table_name, ...$col_names) {
    $full_names = array_map(function ($col_name) use ($table_name) { return "$table_name.$col_name"; }, $col_names);
    return implode(", ", $full_names);
}

// visualizza i bottoni dei documenti di evasione associati all'ordine
function mostra_documenti_associati($ordine) {
    global $gTables;
    // seleziono i documenti evasi che contengono gli articoli di questo ordine
    $rigdoc_result = gaz_dbi_dyn_query('DISTINCT id_tes', $gTables['rigdoc'], "id_order = " . $ordine, 'id_tes ASC');
    while ( $rigdoc = gaz_dbi_fetch_array($rigdoc_result) ) {
        // per ogni documento vado a leggere il numero documento
        $tesdoc_result = gaz_dbi_dyn_query('*', $gTables['tesdoc'], "id_tes = " . $rigdoc['id_tes'], 'id_tes DESC');
        $tesdoc_r = gaz_dbi_fetch_array($tesdoc_result);
        // a seconda del tipo di documento visualizzo il bottone corrispondente
        if ($tesdoc_r["tipdoc"] == "FAI") {
            // fattura immediata
            echo "<a class=\"btn btn-xs btn-default\" title=\"visualizza la fattura immediata\" href=\"stampa_docven.php?id_tes=" . $tesdoc_r['id_tes'] . "\">";
            echo "fatt. " . $tesdoc_r["numfat"];
            echo "</a> ";
        } elseif ($tesdoc_r["tipdoc"] == "DDT" || ($tesdoc_r["tipdoc"] == "FAD" && $tesdoc_r["ddt_type"]!='R')) {
            // documento di trasporto
            echo "<a class=\"btn btn-xs btn-default\" title=\"visualizza il documento di trasporto\" href=\"stampa_docven.php?id_tes=" . $tesdoc_r['id_tes'] . "&template=DDT\">";
            echo "ddt " . $tesdoc_r["numdoc"];
            echo "</a> ";
        } elseif ($tesdoc_r["tipdoc"] == "CMR" || ($tesdoc_r["tipdoc"] == "FAD" && $tesdoc_r["ddt_type"]='R')) {
            // documento cmr
            echo "<a class=\"btn btn-xs btn-default\" title=\"visualizza il cmr\" href=\"stampa_docven.php?id_tes=" . $tesdoc_r['id_tes'] . "&template=CMR\">";
            echo "cmr " . $tesdoc_r["numdoc"];
            echo "</a> ";
        } elseif ($tesdoc_r["tipdoc"] == "VCO") {
            // scontrino
            echo "<a class=\"btn btn-xs btn-default\" title=\"visualizza lo scontrino come fattura\" href=\"stampa_docven.php?id_tes=" . $tesdoc_r['id_tes'] . "&template=".$tesdoc_r["template"]."\">";
            echo "scontr. " . $tesdoc_r["numdoc"] . "<br /> " . gaz_format_date($tesdoc_r["datemi"]);
            echo "</a> ";
        } elseif ($tesdoc_r["tipdoc"] == "VRI") {
            // ricevuta
            echo "<a class=\"btn btn-xs btn-default\" title=\"visualizza la ricevuta\" href=\"stampa_docven.php?id_tes=" . $tesdoc_r['id_tes'] . "&template=Received\">";
            echo "ricevuta " . $tesdoc_r["numdoc"] . "<br /> " . gaz_format_date($tesdoc_r["datemi"]);
            echo "</a> ";
        } else {
            echo $tesdoc_r["tipdoc"];
        }
    }
}
if (isset ($_GET['inevasi'])){
	$form['swStatus']=$_GET['inevasi'];
} elseif (isset ($_GET['tutti'])){
	$form['swStatus']=$_GET['tutti'];
} else {
	$form['swStatus']=(isset($_GET['swStatus']))?$_GET['swStatus']:'';
}

$partner_select = !gaz_dbi_get_row($gTables['company_config'], 'var', 'partner_select_mode')['val'];
$tesbro_e_partners = "{$gTables['tesbro']} LEFT JOIN {$gTables['clfoco']} ON {$gTables['tesbro']}.clfoco = {$gTables['clfoco']}.codice LEFT JOIN {$gTables['anagra']} ON {$gTables['clfoco']}.id_anagra = {$gTables['anagra']}.id";
$tesbro_e_destina = $tesbro_e_partners . " LEFT JOIN {$gTables['destina']} ON {$gTables['tesbro']}.id_des_same_company = {$gTables['destina']}.codice";

// campi ammissibili per la ricerca
$search_fields = [
    'id_doc'
    => "id_tes = %d",
    'numero'
    => "numdoc = %d",
    'auxil'  // leggi: 'tipo' (per compatibilità con link menù esistenti)
    => "tipdoc LIKE '%s'",
    'destinaz'
    => "unita_locale1 LIKE '%%%s%%'",
    'anno'
    => "YEAR(datemi) = %d",
    'cliente'
    => $partner_select ? "clfoco = '%s'" : "ragso1 LIKE '%s%%'",
    'giorno'
    => "weekday_repeat = %d"
];

require("../../library/include/header.php");
$script_transl = HeadMain(0, array('custom/modal_form'));

// creo l'array (header => campi) per l'ordinamento dei record
$terzo = (isset($_GET['auxil']) && $_GET['auxil'] == 'VOG') ? ['weekday_repeat' => 'weekday_repeat'] : ['date' => 'datemi'];
$sortable_headers = array(
    "ID" => "id_tes",
    $script_transl['number'] => "numdoc",
    $script_transl[key($terzo)] => current($terzo),
    "Cliente" => "clfoco",
    "Destinazione" => "unita_locale1",
    $script_transl['status'] => "status",
    $script_transl['print'] => "",
    "Mail" => "",
    $script_transl['duplicate'] => "",
    $script_transl['delete'] => ""
);
unset($terzo);
if (isset($form['swStatus']) AND $form['swStatus']=="Inevasi"){
	$passo=1000;
}
if (count($_GET)<=1){
	// ultimo documento
	$rs_last = gaz_dbi_dyn_query('seziva, YEAR(datemi) AS yearde', $gTables['tesbro'], "tipdoc LIKE '".substr($auxil,0,3)."'", 'datemi DESC, id_tes DESC', 0, 1);
	$last = gaz_dbi_fetch_array($rs_last);
	if ($last) {
		$default_where=['sezione' => $last['seziva'], 'tipo' => 'F%', 'anno'=>$last['yearde']];
        $_GET['anno']=$last['yearde'];
	} else {
		$default_where= ['auxil' => 'VOR', 'anno'=>date("Y")];	
        $_GET['anno']=date("Y");
	}
	
} else {
   $default_where= ['auxil' => 'VOR'];	
}
$ts = new TableSorter(
    isset($_GET["destinaz"]) ? $tesbro_e_destina :
	(!$partner_select && isset($_GET["cliente"]) ? $tesbro_e_partners : $gTables['tesbro']),
    $passo,
    ['datemi' => 'desc', 'numdoc' => 'desc'],
    $default_where
);
$tipo = $auxil;

# le <select> spaziano tra i documenti di un solo tipo (VPR, VOR o VOG)
$where_select = sprintf("tipdoc LIKE '%s'", gaz_dbi_real_escape_string($tipo));
?>
<script>
<?php
echo '
$(function() {
   $( "#dialog" ).dialog({
      autoOpen: false
   });
});
function confirMail(link){
   tes_id = link.id.replace("doc", "");
   $.fx.speeds._default = 500;
   targetUrl = $("#doc"+tes_id).attr("url");
   //alert (targetUrl);
   $("p#mail_adrs").html($("#doc"+tes_id).attr("mail"));
   $("p#mail_attc").html($("#doc"+tes_id).attr("namedoc"));
   $( "#dialog" ).dialog({
         modal: "true",
      show: "blind",
      hide: "explode",
         buttons: {
                      " ' . $script_transl['submit'] . ' ": function() {
                         window.location.href = targetUrl;
                      },
                      " ' . $script_transl['cancel'] . ' ": function() {
                        $(this).dialog("close");
                      }
                  }
         });
   $("#dialog" ).dialog( "open" );
}';
?>

function choice_template(modulo) {
	$( function() {
    var dialog
	,
	dialog = $("#confirm_print").dialog({
		modal: true,
		show: "blind",
		hide: "explode",
		width: "400",
		buttons:[{
			text: "Su carta bianca ",
			"class": 'btn',
			click: function () {
				window.location.href = modulo;
			},
		},
		{
			text: "Su carta intestata ",
			"class": 'btn',
			click: function () {
				window.location.href = modulo+'&lh';
			},
		}],
		close: function(){
				$(this).dialog('destroy');
		}
	});
	});
}
</script>
<script>
$(function() {
	$("#dialog_delete").dialog({ autoOpen: false });
	$('.dialog_delete').click(function() {
		$("p#idcodice").html($(this).attr("ref"));
		$("p#iddescri").html($(this).attr("nome"));
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
						data: {'type':'broven',id_tes:id},
						type: 'POST',
						url: '../vendit/delete.php',
						success: function(output){
		                    //alert(output);
							window.location.replace("./report_broven.php?auxil=<?php echo $tipo;?>");
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
function printPdf(urlPrintDoc){
	$(function(){			
		$('#framePdf').attr('src',urlPrintDoc);
		$('#framePdf').css({'height': '100%'});
		$('.framePdf').css({'display': 'block','width': '90%', 'height': '80%', 'z-index':'2000'});
		$('#closePdf').on( "click", function() {
			$('.framePdf').css({'display': 'none'});
		});	
	});	
};
</script>
<div align="center" class="FacetFormHeaderFont"><?php echo $script_transl['title_value'][substr($tipo,0,2).'R']; ?></div>
<?php
$ts->output_navbar();
?>
<form method="GET" >
	<div class="framePdf panel panel-success" style="display: none; position: fixed; left: 5%; top: 10px">
		<div class="col-lg-12">
			<div class="col-xs-11"><h4><?php echo $script_transl['print'];; ?></h4></div>
			<div class="col-xs-1"><h4><button type="button" id="closePdf"><i class="glyphicon glyphicon-remove"></i></button></h4></div>
		</div>
		<iframe id="framePdf"  style="height: 100%; width: 100%" src=""></iframe>
	</div>
  <input type="hidden" name="info" value="none" />
	<div style="display:none" id="dialog_delete" title="Conferma eliminazione">
        <p><b>preventivo:</b></p>
        <p>Numero ID:</p>
        <p class="ui-state-highlight" id="idcodice"></p>
        <p>Cliente:</p>
        <p class="ui-state-highlight" id="iddescri"></p>
	</div>
    <input type="hidden" name="auxil" value="<?php echo $tipo; ?>">
    <div style="display:none" id="dialog" title="<?php echo $script_transl['mail_alert0']; ?>">
        <p id="mail_alert1"><?php echo $script_transl['mail_alert1']; ?></p>
        <p class="ui-state-highlight" id="mail_adrs"></p>
        <p id="mail_alert2"><?php echo $script_transl['mail_alert2']; ?></p>
        <p class="ui-state-highlight" id="mail_attc"></p>
    </div>
    <div class="box-primary table-responsive">
    <table class="Tlarge table table-striped table-bordered table-condensed">
        <tr>
            <td class="FacetFieldCaptionTD">
                <?php gaz_flt_disp_int("id_doc", "Numero Prot."); ?>
            </td>
            <td class="FacetFieldCaptionTD">
                <?php gaz_flt_disp_int("numero", "Numero Doc."); ?>
            </td>
            <td class="FacetFieldCaptionTD">
                <?php
                    if ( $tipo=="VOG" ) {
                        ?>
                            <select class="form-control input-sm" onchange="this.form.submit()" name="giorno">
			                <?php
			                   $gg = isset($giorno) ? $giorno : 'All';
			                ?>
			                <option value="" <?php if ($gg=='All') echo "selected"; ?>>Tutti</option>
			                <option value="0" <?php if ($gg=='0') echo "selected"; ?>>Domenica</option>
			                <option value="1" <?php if ($gg=='1') echo "selected"; ?>>Lunedi</option>
			                <option value="2" <?php if ($gg=='2') echo "selected"; ?>>Martedi</option>
			                <option value="3" <?php if ($gg=='3') echo "selected"; ?>>Mercoledi</option>
			                <option value="4" <?php if ($gg=='4') echo "selected"; ?>>Giovedi</option>
			                <option value="5" <?php if ($gg=='5') echo "selected"; ?>>Venerdi</option>
			                <option value="6" <?php if ($gg=='6') echo "selected"; ?>>Sabato</option>
			                </select>
                        <?php
                    } else {
                        gaz_flt_disp_select("anno", "YEAR(datemi) as anno", $gTables["tesbro"], $where_select, "anno DESC");
                    }
                ?>
            </td>
            <td class="FacetFieldCaptionTD">

                <?php
		if ($partner_select) {
		    gaz_flt_disp_select("cliente", "clfoco AS cliente, ragso1 AS nome",
					$tesbro_e_partners,
					$where_select,
				        "nome ASC",
					"nome");
		} else {
                    gaz_flt_disp_int("cliente", "Cliente");
                }?>
            </td>
            <td class=FacetFieldCaptionTD>
                <?php
								gaz_flt_disp_select("destinaz","unita_locale1 AS destinaz",$tesbro_e_destina, $where_select . " AND unita_locale1 IS NOT NULL", "destinaz DESC",  "destinaz");
								?>
            </td>
            <td class=FacetFieldCaptionTD style="text-align: center;">
				<?php
				if ($form['swStatus']=="" OR $form['swStatus']=="Tutti"){
					?>
					<input type="submit" class="btn btn-sm btn-default" name="inevasi" onClick="chkSubmit();" value="Inevasi">
					<?php
				} else {
					?>
					<input type="submit" class="btn btn-sm btn-default" name="tutti" onClick="chkSubmit();" value="Tutti" style="text-align: center;">
					<?php
				}
				?>
				<input type="hidden" name="swStatus" id="preventDuplicate" value="<?php echo $form['swStatus']; ?>">
            </td>
            <td class=FacetFieldCaptionTD>
                &nbsp;
            </td>
            <td class="FacetFieldCaptionTD">
                <input type="submit" class="btn btn-sm btn-default" name="search" value="<?php echo $script_transl['search']; ?>" tabindex="1">
                <?php $ts->output_order_form(); ?>
            </td>
            <td class="FacetFieldCaptionTD">
                <a class="btn btn-sm btn-default" href="?auxil=<?php echo $tipo; ?>">Reset</a>
            </td>
            <td class="FacetFieldCaptionTD">
                &nbsp;
            </td>
        </tr>
        <tr>
            <?php $ts->output_headers(); ?>
        </tr>
        <?php
		$res1hp=gaz_dbi_get_row($gTables['company_config'], 'var', 'enable_lh_print_dialog');
        $enable_lh_print_dialog=(isset($res1hp))?$res1hp['val']:0;
        //recupero le testate in base alle scelte impostate
        $result = gaz_dbi_dyn_query(cols_from($gTables['tesbro'], "*") . ", " .
				    cols_from($gTables['anagra'],
					      "ragso1",
					      "e_mail AS base_mail") . ", " .
				    cols_from($gTables["destina"], "unita_locale1"),
				    $tesbro_e_destina,
				    $ts->where, $ts->orderby,
				    $ts->getOffset(), $ts->getLimit());
        $ctrlprotoc = "";
        while ($r = gaz_dbi_fetch_array($result)) {
			$remains_atleastone = false; // Almeno un rigo e' rimasto da evadere.
            $processed_atleastone = false; // Almeno un rigo e' gia' stato evaso.
            $rigbro_result = gaz_dbi_dyn_query('*', $gTables['rigbro'], "id_tes = " . $r['id_tes'] . " AND tiprig <=1 ", 'id_tes DESC');
            while ( $rigbro_r = gaz_dbi_fetch_array($rigbro_result) ) {
                if ( $rigbro_r['tiprig']==1 ) $totale_da_evadere = 1;
                else $totale_da_evadere = $rigbro_r['quanti'];
                $totale_evaso = 0;
                $rigdoc_result = gaz_dbi_dyn_query('*', $gTables['rigdoc'], "id_order=" . $r['id_tes'] . " AND codart='".$rigbro_r['codart']."' AND tiprig <=1 ", 'id_tes DESC');
                while ($rigdoc_r = gaz_dbi_fetch_array($rigdoc_result)) {
                    $totale_evaso += $rigdoc_r['quanti'];
                    $processed_atleastone = true;
                }

                if ( $totale_evaso < $totale_da_evadere ) {
                    $remains_atleastone = true;
                }
            }
			if ( ($form['swStatus']=="Tutti" OR $form['swStatus']=="") OR ($form['swStatus']=="Inevasi" AND  $remains_atleastone == true) ){


            if ($r['tipdoc'] == 'VPR') {
                $modulo = "stampa_precli.php?id_tes=" . $r['id_tes'];
                $modifi = "admin_broven.php?Update&id_tes=" . $r['id_tes'];
            }
            if (substr($r['tipdoc'], 1, 1) == 'O') {
                $modulo = "stampa_ordcli.php?id_tes=" . $r['id_tes'];
                $modifi = "admin_broven.php?Update&id_tes=" . $r['id_tes'];
            }
            echo "<tr class=\"FacetDataTD\">";

			if ($r['tipdoc']=="VOW"){
				echo "<td><button title=\"Per modificare un ordine web lo si deve prima cancellare da GAzie, modificarlo nell'e-commerce e poi reimportarlo in GAzie\" class=\"btn btn-xs btn-default disabled\">&nbsp;" . substr($r['tipdoc'], 1, 2) . "&nbsp;" . $r['id_tes'] . " </button></td>";
			}elseif (!empty($modifi)) {
                echo "<td><a class=\"btn btn-xs btn-default btn-edit\" title=\"" . $script_transl['type_value'][$r['tipdoc']] . "\" href=\"" . $modifi . "\"><i class=\"glyphicon glyphicon-edit\"></i>&nbsp;" . substr($r['tipdoc'], 1, 2) . "&nbsp;" . $r['id_tes'] . "</td>";
            } else {
                echo "<td><button class=\"btn btn-xs btn-default disabled\">&nbsp;" . substr($r['tipdoc'], 1, 2) . "&nbsp;" . $r['id_tes'] . " </button></td>";
            }
            echo "<td>" . $r['numdoc'] . " &nbsp;</td>";
            if ( $tipo=="VOG" ) {
                echo "<td>". getDayNameFromDayNumber($r['weekday_repeat']). " &nbsp;</td>";
            } else {
                echo "<td>" . gaz_format_date($r['datemi']) . " &nbsp;</td>";
            }
            echo "<td><a title=\"Dettagli cliente\" href=\"report_client.php?nome=" . $r['ragso1'] . "\">" . $r['ragso1'] . "</a> &nbsp;</td>";
            echo "<td><a href=\"admin_destinazioni.php?codice=".$r['clfoco']."&Update\">".$r['unita_locale1']."</a>";
						if(strlen($r['destin'])>5){ echo '<small>'.nl2br($r['destin']).'</small>';}
						echo "</td>";

            // colonna stato ordine

            //
            // Se l'ordine e' da evadere completamente, verifica lo status ed
            // eventualmente lo aggiorna.
            //
            echo "<td style='text-align: center;'>";
            if ($remains_atleastone && !$processed_atleastone) {
                // L'ordine e' completamente da evadere.
                if ($r['status'] != "GENERATO") {
                    gaz_dbi_put_row($gTables['tesbro'], "id_tes", $r['id_tes'], "status", "RIGENERATO");
                }
                if ( $tipo == "VOG" ) {
                    echo "<a class=\"btn btn-xs btn-warning\" href=\"select_evaord_gio.php?weekday=".$r['weekday_repeat']."\">evadi</a>";
                } else {
                    echo "<a class=\"btn btn-xs btn-warning\" href=\"select_evaord.php?id_tes=" . $r['id_tes'] . "\">evadi</a>&nbsp;";
                    echo "<a class=\"btn btn-xs btn-warning\" href=\"select_evaord.php?clfoco=" . $r['clfoco'] . "\">evadi cliente</a>";
                }
            } elseif ($remains_atleastone) {
                // l'ordine è parzialmente evaso, mostro lista documenti e tasto per evadere rimanenze
                $ultimo_documento = 0;
                mostra_documenti_associati( $r['id_tes'] );
                if ( $tipo == "VOG" ) {
                    echo "<a class=\"btn btn-xs btn-default\" href=\"select_evaord_gio.php\">evadi il rimanente</a>";
                } else {
                    echo "<a class=\"btn btn-xs btn-warning\" href=\"select_evaord.php?id_tes=" . $r['id_tes'] . "\">evadi il rimanente</a>&nbsp;";
                    echo "<a class=\"btn btn-xs btn-warning\" href=\"select_evaord.php?clfoco=" . $r['clfoco'] . "\">evadi cliente</a>";
                }
            } else {
                // l'ordine è completamente evaso, mostro i riferimenti ai documenti che lo hanno evaso
                $ultimo_documento = 0;
                mostra_documenti_associati( $r['id_tes'] );
            }
            echo "</td>";

            // stampa
			//onclick="confirmemail(\''.$r["clfoco"].'\',\''.$r['id_tes'].'\',true);" title="Invia mail di conferma"
            echo "<td align=\"center\"><a class=\"btn btn-xs btn-default btn-stampa\"";
			// vedo se è presente un file di template adatto alla stampa su carta già intestata
			if($enable_lh_print_dialog>0 && withoutLetterHeadTemplate($r['tipdoc'])){
				echo ' onclick="choice_template(\''.$modulo.'\');" title="Scegli modulo per stampa"';
			}else{				
				echo " style=\"cursor:pointer;\" onclick=\"printPdf('".$modulo."')\"";
			}
			echo "><i class=\"glyphicon glyphicon-print\" title=\"Stampa documento PDF\"></i></a>";
            echo "</td>";
			/*
            echo "<td align=\"center\"><a class=\"btn btn-xs btn-default btn-stampa\" href=\"" . $modulo . "\" target=\"_blank\"><i class=\"glyphicon glyphicon-print\"></i></a>";
            echo "</td>";*/
            // Colonna "Mail"
            echo "<td align=\"center\">";
            if (!empty($r['e_mail'])){ // ho una mail sulla destinazione
                echo '<a class="btn btn-xs btn-default btn-email" onclick="confirMail(this);return false;" id="doc' . $r['id_tes'] . '" url="' . $modulo . '&dest=E" href="#" title="mailto: ' . $r['e_mail'] . '"
        mail="' . $r['e_mail'] . '" namedoc="' . $script_transl['type_value'][$r['tipdoc']] . ' n.' . $r['numdoc'] . ' del ' . gaz_format_date($r['datemi']) . '"><i class="glyphicon glyphicon-envelope"></i></a>';
            } elseif (!empty($r['base_mail'])) { // ho una mail sul cliente
                echo '<a class="btn btn-xs btn-default btn-email" onclick="confirMail(this);return false;" id="doc' . $r['id_tes'] . '" url="' . $modulo . '&dest=E" href="#" title="mailto: ' . $r['base_mail'] . '"
        mail="' . $r['base_mail'] . '" namedoc="' . $script_transl['type_value'][$r['tipdoc']] . ' n.' . $r['numdoc'] . ' del ' . gaz_format_date($r['datemi']) . '"><i class="glyphicon glyphicon-envelope"></i></a>';
            } else { // non ho mail
                echo '<a title="Non hai memorizzato l\'email per questo cliente, inseriscila ora" href="admin_client.php?codice=' . substr($r['clfoco'], 3) . '&Update"><i class="glyphicon glyphicon-edit"></i></a>';
            }
            echo "</td>";

            echo "<td align=\"center\"><a class=\"btn btn-xs btn-default btn-duplica\" href=\"duplicate_broven.php?id_tes=" . $r['id_tes'] . "\"><i class=\"glyphicon glyphicon-duplicate\"></i></a>";
            echo "</td>";

            echo "<td align=\"center\">";
            if (!$remains_atleastone || !$processed_atleastone) {
                //possono essere cancellati solo gli ordini inevasi o completamente evasi
				?>
				<a class="btn btn-xs btn-default btn-elimina dialog_delete" title="Cancella il documento" ref="<?php echo $r['id_tes'];?>" nome="<?php echo $r['ragso1']; ?>">
					<i class="glyphicon glyphicon-remove"></i>
				</a>
				<?php
			}
            echo "</td>";
            echo "</tr>\n";
        }
		}
        ?>
        <tr><th class="FacetFieldCaptionTD" colspan="10"></th></tr>
    </table>
    </div>
	<div class="modal" id="confirm_print" title="Scegli la carta dove stampare"></div>
</form>

<script>
$(document).ready(function(){
<?php
if (isset($_SESSION['print_queue']['idDoc']) && !empty($_SESSION['print_queue']['idDoc'])) {
	$printIdDoc =  (int) $_SESSION['print_queue']['idDoc'];
	if (isset($_SESSION['print_queue']['tpDoc'])) {
		$target = "stampa_precli.php?id_tes=$printIdDoc";
		if ($_SESSION['print_queue']['tpDoc'] == 'VOR') {
			$target = "stampa_ordcli.php?id_tes=$printIdDoc";
		}

		echo "fileLoad('$target', false);\n";
	}

	unset($_SESSION['print_queue']);
}
?>
     var selects = $("select");
     // la funzione gaz_flt_dsp_select usa "All", qui usiamo invece valori vuoti
     // (in questo modo i campi non usati possono essere esclusi)
     $("option", selects).filter(function(){ return this.value == "All"; }).val("");

     // la stessa funzione imposta onchange="this.form.submit()" sulle select:
     // l'azione non lancia un evento "submit" e non può essere intercettata.
     // per non andare a modificare la funzione rimpiazziamo l'attributo onchange:
     selects.attr('onchange', null).change(function() { $(this.form).submit(); });

     // così ora possiamo intercettare tutti i submit e pulire la GET dal superfluo
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

function withoutLetterHeadTemplate($tipdoc='VPR')
{
	$withoutLetterHeadTemplate=false;
	$nf="preventivo_cliente";
	if ($tipdoc=='VOR') $nf="ordine_cliente";
	$configTemplate = new configTemplate;
	$handle = opendir("../../config/templates".($configTemplate->template ? '.' . $configTemplate->template : ''));
	while ($file = readdir($handle)) {
		if(($file == ".")||($file == "..")) continue;
		if(!preg_match("/^".$nf."_lh.php$/",$file)) continue; // se è presente un template adatto per stampa su carta intestata (suffisso "_lh" )
		$withoutLetterHeadTemplate = true; //
	}
	return $withoutLetterHeadTemplate;
}

?>
