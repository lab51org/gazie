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

$admin_aziend = checkAdmin();
$message = "";

// campi ammissibili per la ricerca
$search_fields = [
    'sezione'
        => "seziva = %d",
    'proto'
        => "protoc = %d",
    'tipo'
        => "tipdoc like '%s'",
    'numero'
        => "numfat like '%%%s%%'",
    'anno'
        => "YEAR(datfat) = %d",
    'fornitore'
        => "clfoco = '%s'"
];

// creo l'array (header => campi) per l'ordinamento dei record
$sortable_headers = array(
    "Prot." => "protoc",
    "Dat.Reg." => "datreg",
    "Documento" => "tipdoc",
    "Numero" => "numfat",
    "Data" => "datfat",
    "Fornitore" => "ragso1",
    "Status" => "",
    "Stampa" => "",
    "Cancella" => ""
);

// campi ammissibili per l'ordinamento
$order_fields = array_filter(array_values($sortable_headers));

require("../../library/include/header.php");
$script_transl = HeadMain();

$ts = new TableSorter($gTables['tesdoc'], $passo, ['datreg' => 'desc', 'protoc' => 'desc'], ['sezione' => 1, 'tipo' => 'AF_']);

# le select spaziano solo tra i documenti d'acquisto del sezionale corrente
$where_select = "tipdoc LIKE 'AF_' AND seziva = '$sezione'";

?>

<form method="GET" >
    <div align="center" class="FacetFormHeaderFont">
        <?php echo $script_transl['title']; ?>

        <select name="sezione" class="FacetSelect" onchange="this.form.submit()">
<?php
            echo "<option value=''>1</option>\n"; # è l'opzione di default perciò ha valore vuoto
            for ($sez = 2; $sez <= 9; $sez++) {
                $selected = $sezione == $sez ? "selected" : "";
                echo "<option value='$sez' $selected > $sez </option>\n";
            }
?>
        </select>
    </div>
<?php

    $ts->output_navbar();

?>
    <div class="box-primary table-responsive">
    <table class="Tlarge table table-striped table-bordered table-condensed table-responsive">
        <tr>
            <td colspan="1" class="FacetFieldCaptionTD">
                <input type="text" placeholder="Cerca Prot." class="input-sm form-control" name="proto" value="<?php if (isset($proto)) print $proto; ?>" maxlength="6" size="3" tabindex="1" class="FacetInput">
            </td>
            <td colspan="1" class="FacetFieldCaptionTD">
                &nbsp;
            </td>
            <td colspan="1" class="FacetFieldCaptionTD">
                <?php
                    gaz_flt_disp_select("tipo", "tipdoc as tipo", $gTables["tesdoc"], $where_select, "tipo ASC");
                ?>
            </td>
            <td colspan="1" class="FacetFieldCaptionTD">
                <input type="text" placeholder="Cerca Num." class="input-sm form-control" name="numero" value="<?php if (isset($numero)) print $numero; ?>" size="3" tabindex="3" class="FacetInput">			
            </td>
            <td colspan="1" class="FacetFieldCaptionTD">
                <?php 
                    gaz_flt_disp_select("anno", "YEAR(datfat) AS anno", $gTables["tesdoc"],  $where_select, "anno DESC");
                ?>
            </td>
            <td colspan="1" class="FacetFieldCaptionTD">
                <?php 
                    gaz_flt_disp_select("fornitore", "clfoco AS fornitore, ragso1 as nome", 
                        $gTables['tesdoc'] . " LEFT JOIN " . $gTables['clfoco'] . " ON " . $gTables['tesdoc'] . ".clfoco = " . $gTables['clfoco'] . ".codice LEFT JOIN " . $gTables['anagra'] . ' ON ' . $gTables['clfoco'] . '.id_anagra = ' . $gTables['anagra'] . '.id',
                        $where_select, "nome ASC", "nome");
                ?>
            </td>
            <td colspan="1" class="FacetFieldCaptionTD">
                &nbsp;
            </td>
            <td colspan="1" class="FacetFieldCaptionTD">
                <input type="submit" class="btn btn-sm btn-default" name="search" value="Cerca" tabindex="1" onClick="javascript:document.report.all.value = 1;">
            </td>
            <td colspan="1" class="FacetFieldCaptionTD">
                <a class="btn btn-xs btn-default" href="?">Reset</a>
            </td>
        </tr>
        <tr>
<?php
            $ts->output_headers();
?>
        </tr>
<?php

/*
$rs_last_doc = gaz_dbi_dyn_query("MAX(protoc) AS maxpro, YEAR(datfat) AS y", $gTables['tesdoc'], "tipdoc LIKE 'AF_' AND seziva = '$seziva' GROUP BY y ", 'protoc DESC');
while ($last_doc = gaz_dbi_fetch_array($rs_last_doc)) {
    $lt_doc[$last_doc['y']] = $last_doc['maxpro'];
}
*/

//recupero le testate in base alle scelte impostate
$result = gaz_dbi_dyn_query($gTables['tesdoc'] . ".*," . $gTables['anagra'] . ".ragso1", $gTables['tesdoc'] . " LEFT JOIN " . $gTables['clfoco'] . " ON " . $gTables['tesdoc'] . ".clfoco = " . $gTables['clfoco'] . ".codice LEFT JOIN " . $gTables['anagra'] . ' ON ' . $gTables['clfoco'] . '.id_anagra = ' . $gTables['anagra'] . '.id', $ts->where, $ts->orderby, $ts->getOffset(), $ts->getLimit());
while ($row = gaz_dbi_fetch_array($result)) {
    // faccio il check per vedere se ci sono righi da trasferire in contabilità di magazzino
    $ck = gaz_dbi_dyn_query("*", $gTables['rigdoc'], "id_tes=". $row['id_tes']." AND  LENGTH(TRIM(codart))>=1 AND tiprig=0 AND id_mag=0");
    $check = gaz_dbi_fetch_array($ck);
    // fine check magazzino
    $y = substr($row['datfat'], 0, 4);
    if ($row["tipdoc"] == 'AFA') {
        $tipodoc = "Fattura";
        $modulo = "stampa_docacq.php?id_tes=" . $row['id_tes'];
        $modifi = "admin_docacq.php?Update&id_tes=" . $row['id_tes'];
    } elseif ($row["tipdoc"] == 'AFC') {
        $tipodoc = "Nota Credito";
        $modulo = "stampa_docacq.php?id_tes=" . $row['id_tes'];
        $modifi = "admin_docacq.php?Update&id_tes=" . $row['id_tes'];
    }

    $clfoco = gaz_dbi_get_row($gTables['clfoco'], 'codice', $row['clfoco']);
    $anagra = gaz_dbi_get_row($gTables['anagra'], 'id', $clfoco['id_anagra']);
    echo "<tr class=\"FacetDataTD\">";
    if (!empty($modifi)) {
        echo "<td align=\"center\"><a class=\"btn btn-xs btn-default btn-edit\" href=\"" . $modifi . "\"><i class=\"glyphicon glyphicon-edit\"></i>&nbsp;" . $row["protoc"] . "</td>";
    } else {
        echo "<td><button class=\"btn btn-xs btn-default btn-edit disabled\">" . $row["protoc"] . " &nbsp;</button></td>";
    }
    echo "<td>" . gaz_format_date($row["datreg"]) . " &nbsp;</td>";
    if (empty($row["fattura_elettronica_original_name"])) {
        print '<td>'.$tipodoc."</td>\n";
    } else {
        print '<td><a class="btn btn-xs btn-default btn-xml" target="_blank" href="view_fae.php?id_tes=' . $row["id_tes"] . '">'.$tipodoc.' '.$row["fattura_elettronica_original_name"]."</a></td>";
    }
    echo "<td>" . $row["numfat"] . " &nbsp;</td>";
    echo "<td>" . gaz_format_date($row["datfat"]) . " &nbsp;</td>";
    echo "<td><a title=\"Dettagli fornitore\" href=\"report_fornit.php?auxil=" . htmlspecialchars($anagra["ragso1"]) . "&search=Cerca\">" . $anagra["ragso1"] . ((empty($anagra["ragso2"]))?"":" ".$anagra["ragso2"]) . "</a>&nbsp;</td>";
    echo "<td align=\"center\">";
    if ($row["id_con"] > 0) {
        echo "<a class=\"btn btn-xs btn-default btn-default\" href=\"../contab/admin_movcon.php?id_tes=" . $row["id_con"] . "&Update\">Cont. n." . $row["id_con"] . "</a>";
    } else {
        echo "<a class=\"btn btn-xs btn-default btn-cont\" href=\"accounting_documents.php?type=A&last=" . $row["protoc"] . "\">Contabilizza</a>";					
    }
    if ($check) { // ho qualche rigo da traferire
        echo " <a class=\"btn btn-xs btn-default btn-warning\" href=\"../magazz/genera_movmag.php\">Movimenta magazzino</a> ";
    }
    echo "</td>";
    echo "<td><a class=\"btn btn-xs btn-default\" href=\"" . $modulo . "\" target=\"_blank\"><i class=\"glyphicon glyphicon-print\"></i></a></td>";
//  if ($lt_doc[$y] == $row['protoc']) {
        echo "<td><a class=\"btn btn-xs btn-default btn-elimina\" href=\"delete_docacq.php?id_tes=" . $row["id_tes"] . "\"><i class=\"glyphicon glyphicon-remove\"></i></a></td>";
//  } else {
//      echo "<td><button title=\"Per garantire la sequenza corretta della numerazione, non &egrave; possibile cancellare un documento diverso dall'ultimo\" class=\"btn btn-xs btn-default btn-elimina disabled\"><i class=\"glyphicon glyphicon-remove\"></i></button></td>";
//  }
    echo "</tr>\n";
}
?>


        <tr>
            <td colspan="9" class="FacetFieldCaptionTD"></td>
        </tr>
    </table>
</div>
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
