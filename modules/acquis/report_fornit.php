<?php
/*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2019 - Antonio De Vincentiis Montesilvano (PE)
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
$titolo="Lista dei Fornitori";
$message = "";

$fornit = $admin_aziend['masfor'];
$masfor = $fornit . "000000";

// campi ammissibili per la ricerca
$search_fields = [
    'codice'
    => "codice LIKE '$fornit%%%d%%'",
    'nome'
    => "CONCAT(ragso1, ragso2) LIKE '%%%s%%'",
    'tipo'
    => "sexper = '%s'",
    'citta'
    => "citspe = '%s'",
    'telefono'
    => "telefo LIKE '%%%s%%'",
    'fiscali'
    => "(codfis LIKE '%%%1\$s%%' OR pariva LIKE '%%%1\$s%%')",
    'codmin'
    => "codice >= $masfor + GREATEST(%d, 1)",
    'codmax'
    => "codice <= $masfor + LEAST(%d, 999999)"
];

// creo l'array (header => campi) per l'ordinamento dei record
$sortable_headers = array(
    "Codice" => "codice",
    "Ragione sociale" => "ragso1",
    "Tipo" => "sexper",
    "Citt&agrave;" => "citspe",
    "Telefono" => "",
    "P.IVA - C.F." => "",
    "Privacy" => "" ,
    "Paga" => "" ,
    "Visualizza<br> e/o stampa" => "",
    "Cancella" => ""
);

require("../../library/include/header.php");
$script_transl=HeadMain();

$partners = "{$gTables['clfoco']} LEFT JOIN {$gTables['anagra']} ON {$gTables['clfoco']}.id_anagra = {$gTables['anagra']}.id";
$ts = new TableSorter(
    $partners,
    $passo,
    ['codice' => 'desc'],
    ['codmin' => 1, 'codmax' => 999999]
);

?>
<div align="center" class="FacetFormHeaderFont">Fornitori</div>
<div align="center"><?php $ts->output_navbar(); ?></div>
<form method="GET">
<table class="Tlarge table table-striped table-bordered table-condensed table-responsive">
	<tr>
	    <td class="FacetFieldCaptionTD">
                <?php gaz_flt_disp_int("codice", "Cerca cod."); ?>
	    </td>
	    <td class="FacetFieldCaptionTD">
                <?php gaz_flt_disp_int("nome", "Ragione Sociale"); ?>
	    </td>
	    <td class="FacetFieldCaptionTD">
		<select class="form-control input-sm" name="tipo" onchange="this.form.submit()">
                    <?php
                    if (!isset($tipo)) $tipo = "";
                    foreach(['' => $script_transl['tuttitipi'],
                             'G' => 'Giuridica',
                             'M' => 'Maschio',
                             'F' => 'Femmina'] as $t => $desc) {
                        echo "<option value='$t' " . (($t == $tipo) ? "selected" : "") . ">$desc</option>";
                    };
                    ?>
		</select>
	    </td>
		<td class="FacetFieldCaptionTD">
                    <?php
                        gaz_flt_disp_select("citta", "citspe as citta", 
					    $partners,
					    $ts->where, "citspe ASC");
                    ?>
		</td>
		<td class="FacetFieldCaptionTD">
                    <?php gaz_flt_disp_int("telefono", "Cerca tel."); ?>
		</td>
		<td class="FacetFieldCaptionTD">
	            <?php gaz_flt_disp_int("fiscali", "Cerca fisc."); ?>
		</td>
		<td class="FacetFieldCaptionTD"></td>
		<td class="FacetFieldCaptionTD"></td>
		<td class="FacetFieldCaptionTD">
                    <input type="submit" class="btn btn-sm btn-default" name="search" value="Cerca" tabindex="1" />
                    <?php $ts->output_order_form(); ?>    
		</td>
		<td class="FacetFieldCaptionTD" colspan="1">
		    <a class="btn btn-sm btn-default" href="?">Reset</a>
		</td>
	</tr>
<tr>
<?php
$result = gaz_dbi_dyn_query ('*', $partners, $ts->where, $ts->orderby, $ts->getOffset(), $ts->getLimit());
$ts->output_headers();
?>
</tr>
<?php
while ($a_row = gaz_dbi_fetch_array($result)) {
	// NOMINA A RESPONSABILE ESTERNO AL TRATTAMENTO DEI DATI?
	$regol_lnk='';
	if (isset ($a_row["external_resp"]) && $a_row["external_resp"]>0) {
		$regol_lnk='<a title="Stampa la Nomina a RESPONSABILE ESTERNO al trattamento dati personali" class="btn btn-xs btn-default btn-warning" href="stampa_nomina.php?id=' . $a_row["codice"] . '" target="_blank"><i class="glyphicon glyphicon-eye-close"></i></a> ';
	} else {
		$regol_lnk="<a class=\"btn btn-xs btn-default\" href=\"stampa_privacy.php?codice=".$a_row["codice"]."\" target=\"_blank\"><i class=\"glyphicon glyphicon-eye-close\"></i></a>";
	}
	
    echo "<tr class=\"FacetDataTD\">";
	 //colonna codice
    echo "<td><a class=\"btn btn-xs btn-default\" href=\"admin_fornit.php?codice=".substr($a_row["codice"],3)."&Update\"><i class=\"glyphicon glyphicon-edit\"></i>&nbsp;".substr($a_row["codice"],3)."</a></td>";
    echo "<td title=\"".$a_row["ragso2"]."\">".$a_row["ragso1"]." &nbsp;</td>";
    echo "<td align=\"center\">".$a_row["sexper"]."</td>";
	 $google_string = str_replace(" ","+",$a_row["indspe"]).",".str_replace(" ","+",$a_row["capspe"]).",".str_replace(" ","+",$a_row["citspe"]).",".str_replace(" ","+",$a_row["prospe"]);
		echo "<td title=\"".$a_row["capspe"]." ".$a_row["indspe"]."\">";	
		echo "<a class=\"btn btn-xs btn-default\" target=\"_blank\" href=\"https://www.google.it/maps/place/".$google_string."\">".$a_row["citspe"]." (".$a_row["prospe"].")&nbsp;<i class=\"glyphicon glyphicon-map-marker\"></i></a>";
		echo "</td>";
    //echo "<td class=\"FacetDataTD\" title=\"".$a_row["capspe"]." ".$a_row["indspe"]."\">".$a_row["citspe"]." (".$a_row["prospe"].")</td>";

    $title = "";
    $telefono = "";
    if (!empty($a_row["telefo"])){
       $telefono = $a_row["telefo"];
       if (!empty($a_row["cell"])){
             $title .= "cell:".$a_row["cell"];
       }
       if (!empty($a_row["fax"])){
             $title .= " fax:".$a_row["fax"];
       }
    } elseif (!empty($a_row["cell"])) {
       $telefono = $a_row["cell"];
       if (!empty($a_row["fax"])){
             $title .= " fax:".$a_row["fax"];
       }
    } elseif (!empty($a_row["fax"])) {
       $telefono = "fax:".$a_row["fax"];
    } else {
       $telefono = "_";
       $title = " nessun contatto telefonico memorizzato ";
    }
    echo "<td title=\"$title\" align=\"center\">".gaz_html_call_tel($telefono)." &nbsp;</td>";
    if ($a_row['pariva'] > 0 and empty($a_row['codfis'])){
        echo "<td align=\"center\">".$a_row['pariva']."</td>";
    } elseif($a_row['pariva'] == 0 and !empty($a_row['codfis'])) {
        echo "<td align=\"center\">".$a_row['codfis']."</td>";
    } elseif($a_row['pariva'] > 0 and !empty($a_row['codfis'])) {
		if ( $a_row['pariva'] == $a_row['codfis'] ) {
			echo "<td align=\"center\">".$a_row['pariva']."</td>";		
		} else {
			echo "<td align=\"center\">".$a_row['pariva']."<br>".$a_row['codfis']."</td>";
		}
    } else {
        echo "<td class=\"FacetDataTDred\" align=\"center\"> * NO * </td>";
    }
    echo "<td title=\"stampa informativa sulla privacy\" align=\"center\">".
	$regol_lnk."</td>";
    echo "<td title=\"Effettua un pagamento a ".$a_row["ragso1"]."\" align=\"center\"><a class=\"btn btn-xs btn-default btn-pagamento\" href=\"supplier_payment.php?partner=".$a_row["codice"]."\"><i class=\"glyphicon glyphicon-euro\"></i></a></td>";
    echo "<td title=\"Visualizza e stampa il partitario\" align=\"center\">  <a class=\"btn btn-xs btn-default\" href=\"report_contfor.php?id=".$a_row["codice"]."\"  target=\"_blank\"><i class=\"glyphicon glyphicon-list-alt\"></i></a> <a class=\"btn btn-xs btn-default\" href=\"../contab/select_partit.php?id=".$a_row["codice"]."\" target=\"_blank\"><i class=\"glyphicon glyphicon-check\"></i>&nbsp;<i class=\"glyphicon glyphicon-print\"></a></td>";
    echo "<td title=\"Cancella\" align=\"center\"><a class=\"btn btn-xs btn-default btn-elimina\" href=\"delete_fornit.php?codice=".substr($a_row["codice"],3)."\"><i class=\"glyphicon glyphicon-remove\"></i></a></td>";
    echo "</tr>\n";
}
?>
</table>
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
?>
