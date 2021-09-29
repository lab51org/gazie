<?php
/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2021 - Antonio De Vincentiis Montesilvano (PE)
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
$script_transl = HeadMain();

$stato_lavorazione = array(0 => "aperto", 1 => "in attesa", 2 => "in lavorazione", 3 => "materiale ordinato", 4 => "incontrate difficoltà", 5 => "in attesa di spedizione", 6 => "spedito", 7 => "consegnato", 8 => "non chiuso", 9 => "chiuso");

// campi ammissibili per la ricerca
$search_fields = [
    'asset' => "id = %d",
    'descri' => "description LIKE '%%%s%%'",
    'camp' => "campo_impianto = %d",
];

// creo l'array (header => campi) per l'ordinamento dei record
$sortable_headers = array("Codice ID"      => "id",
							"Descrizione" => "description",
							"Tipo lavorazione"  => "order_type",
							"Informazioni aggiuntive" => "add_info",
							"Articolo" => "",
							"Q.tà prodotta" => "",
							"Lotto e scadenza" => "",
							"Ordine" => "",
							"Inizio produzione" => "",
							"Durata" => "",
							"Luogo di produzione" => "campo_impianto",
							"Stato" => "stato_lavorazione",
							"Distinta" => "",
							"Cancella"    => ""
							);

$tablejoin = $gTables['orderman'];

$ts = new TableSorter(
    $tablejoin, 
    $passo, 
    ['id'=>'desc']);
?>
<script>
$(function() {
	$("#dialog_delete").dialog({ autoOpen: false });
	$('.dialog_delete').click(function() {
		$("p#idcodice").html($(this).attr("ref"));
		$("p#iddescri").html($(this).attr("orddes"));
		var id = $(this).attr('ref');
		var id2 = $(this).attr('ref2');
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
						data: {'type':'orderman',ref:id,ref2:id2},
						type: 'POST',
						url: '../orderman/delete.php',
						success: function(output){
		                    //alert(output);
							window.location.replace("./orderman_report.php");
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

	$("#dialog_stato_lavorazione").dialog({ autoOpen: false });
	$('.dialog_stato_lavorazione').click(function() {
		$("p#id_status").html($(this).attr("refsta"));
		$("p#de_status").html($(this).attr("prodes"));
		var refsta = $(this).attr('refsta');
        var new_stato_lavorazione = $(this).attr("prosta");
        $("#sel_stato_lavorazione").val(new_stato_lavorazione);
        $('#sel_stato_lavorazione').on('change', function () {
            //ways to retrieve selected option and text outside handler
            new_stato_lavorazione = this.value;
        });        
		$( "#dialog_stato_lavorazione" ).dialog({
			minHeight: 1,
			width: "auto",
			modal: "true",
			show: "blind",
			hide: "explode",
			buttons: {
				delete:{ 
					text:'Modifica', 
					'class':'btn btn-danger delete-button',
					click:function (event, ui) {
					$.ajax({
						data: {'type':'set_new_stato_lavorazione','ref':refsta,'new_status':new_stato_lavorazione},
						type: 'POST',
						url: '../orderman/delete.php',
						success: function(output) {
		                    //alert('id:'+refsta+' new:'+new_stato_lavorazione);
		                    //alert(output);
							window.location.replace("./orderman_report.php");
						}
					});
				}},
				"Non cambiare": function() {
					$(this).dialog("close");
				}
			}
		});
		$("#dialog_stato_lavorazione" ).dialog( "open" );  
	});
    
});
</script>
<div align="center" class="FacetFormHeaderFont">Elenco produzioni</div>
<?php
$ts->output_navbar();

?>
<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<div style="display:none" id="dialog_delete" title="Conferma eliminazione">
        <p><b>produzione:</b></p>
        <p>codice:</p>
        <p class="ui-state-highlight" id="idcodice"></p>
        <p>Descrizione</p>
        <p class="ui-state-highlight" id="iddescri"></p>
	</div>
	<div style="display:none" id="dialog_stato_lavorazione" title="Cambia lo stato">
        <p><b>produzione:</b></p>
        <p class="ui-state-highlight" id="id_status"></p>
        <p class="ui-state-highlight" id="de_status"></p>
        <select name="sel_stato_lavorazione" id="sel_stato_lavorazione">
            <option value="0">Aperta</option>
            <option value="1">In attesa</option>
            <option value="2">In lavorazione</option>
            <option value="3">Materiale ordinato</option>
            <option value="4">Incontrate difficoltà</option>
            <option value="5">In attesa di spedizione</option>
            <option value="6">Spedito</option>
            <option value="7">Consegnato</option>
            <option value="8">Non chiuso</option>
            <option value="9">Chiuso</option>
        </select>
	</div>
	<div class="table-responsive">
    <table class="Tlarge table table-striped table-bordered table-condensed ">
	<tr>
	<tr>
		<td class="FacetFieldCaptionTD">
			<input type="text" name="asset" placeholder="id" class="input-sm form-control" value="<?php echo (isset($asset))? $asset : ""; ?>" maxlength="15">
		</td>
		<td class="FacetFieldCaptionTD">
			<input type="text" name="descri" placeholder="descrizione" class="input-sm form-control" value="<?php echo (isset($descri))? $descri : ""; ?>" maxlength="15">
        </td>
		<td class="FacetFieldCaptionTD" colspan="12">
			<input type="submit" class="btn btn-sm btn-default" name="search" value="<?php echo $script_transl['search'];?>" onClick="javascript:document.report.all.value=1;">
			<a class="btn btn-sm btn-default" href="?">Reset</a>
			<?php  $ts->output_order_form(); ?>
		</td>
	</tr>
<?php
$gForm = new ordermanForm();
$result = gaz_dbi_dyn_query ( '*',$tablejoin, $ts->where, $ts->orderby, $ts->getOffset(), $ts->getLimit());

echo '<tr>';
$ts->output_headers();
echo '</tr>';
while ($r = gaz_dbi_fetch_array($result)) {
        $stato_btn = 'btn-default';
        if ($r['stato_lavorazione']=='0'){
            $stato_btn = 'btn-success';
        }elseif ($r['stato_lavorazione']=='2'){
            $stato_btn = 'btn-warning';
        }elseif ($r['stato_lavorazione']=='7'){
            $stato_btn = 'btn-info';
        }elseif ($r['stato_lavorazione']=='9'){
            $stato_btn = 'btn-danger';
        }
?>		<tr class="FacetDataTD">
			<td>
				<a class="btn btn-xs btn-default btn-block" href="admin_orderman.php?Update&codice=<?php echo $r['id']; ?>">
					<i class="glyphicon glyphicon-edit"></i>&nbsp;<?php echo $r['id'];?>
				</a>
			</td>
			<td>
				<span class="gazie-tooltip" data-type="catmer-thumb" data-id="<?php echo $r['id']; ?>" data-title="<?php echo $r['add_info']; ?>"><?php echo $r['description']; ?></span>
			</td>
			<td align="center"><?php echo $script_transl['order_type'][$r['order_type']];?></td>
			<td align="center"><?php echo $r['add_info'];?></td>
			<?php $d_row = gaz_dbi_get_row($gTables['rigbro'], "id_rig", $r['id_rigbro']);?>
			<td align="center"><?php echo ($d_row)?$d_row['codart']:'';?></td>
			
			<!-- Colonna quantità prodotta -->
			<?php 
			$e_row = gaz_dbi_get_row($gTables['movmag'], "id_orderman", $r['id'], "AND operat = 1");
			if ($e_row && $d_row ){
				$f_row = gaz_dbi_get_row($gTables['lotmag'], "id_movmag", $e_row['id_mov']);
				?>
				<td align="center"><?php echo gaz_format_quantity($e_row['quanti'] ) ." su ". gaz_format_quantity($d_row['quanti'], true, $admin_aziend['decimal_quantity']);?></td>
				<?php
			} else {
				?><td></td><?php
			}

		
			if (isset($f_row) && strlen($f_row['identifier'])>0) {
				echo '<td align="center">'.$f_row['identifier'].' - '.gaz_format_date($f_row['expiry']).'</td>';
			} else {
				echo '<td></td>';
			}
			?>
			<!-- Antonio Germani Vado a leggere la tabella tesbro connessa alla produzione -->
			<?php $b_row = gaz_dbi_get_row($gTables['tesbro'], "id_tes", $r['id_tesbro']);?>
			<td align="center"><?php echo ($b_row)?$b_row['numdoc']:'';?></td>
			<td align="center"><?php echo gaz_format_date(($b_row)?$b_row['datemi']:'');?></td>
			<td align="center"><?php echo $r['duration'];?></td>
			<!-- Antonio Germani Vado a leggere la descrizione del campo connesso alla produzione -->
			<?php $c_row = gaz_dbi_get_row($gTables['campi'], "codice", $r['campo_impianto']);?>
			<td align="center"><?php echo $r['campo_impianto'], " ",(($c_row)?$c_row['descri']:'');?></td>
			
			<!-- Colonna stato lavorazione -->
			<td>
				<a class="btn btn-xs <?php echo $stato_btn; ?> dialog_stato_lavorazione" refsta="<?php echo $r['id']; ?>" prodes="<?php echo $r['description']; ?>" prosta="<?php echo $r['stato_lavorazione']; ?>">
				<i class="glyphicon glyphicon-compressed"></i><?php echo $stato_lavorazione[$r['stato_lavorazione']]; ?>
				</a>
			</td>
			
			<!-- Colonna stampa distinta -->
			<?php
			if ($r['order_type']=="IND" or $r['order_type']=="ART"){
			?>
			<td align="center">
				<a class="btn btn-xs btn-info" href="stampa_produzione.php?id_orderman=<?php echo $r['id']; ?>">
					<i class="glyphicon glyphicon-list-alt"></i>
				</a>
			</td>
			<?php
			} else  {
				echo '<td></td>';
			}
			?>
			<td align="center">
				<a class="btn btn-xs btn-default btn-elimina dialog_delete" ref="<?php echo $r['id'];?>" ref2="<?php echo $r['id_tesbro'];?>" orddes="<?php echo $r['description']; ?>">
					<i class="glyphicon glyphicon-remove"></i>
				</a>
			</td>
		</tr>
<?php
}
?>
        </table></div>
		</form>

<?php

require("../../library/include/footer.php");
?>