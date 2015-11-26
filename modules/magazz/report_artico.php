<?php
/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2015 - Antonio De Vincentiis Montesilvano (PE)
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
  scriva   alla   Free  Software Foundation,  Inc.,   59
  Temple Place, Suite 330, Boston, MA 02111-1307 USA Stati Uniti.
  --------------------------------------------------------------------------
 */
require("../../library/include/datlib.inc.php");
$admin_aziend = checkAdmin();

function getLastDoc($item_code) {
    global $gTables;
    $rs = false;
    $rs_last_doc = gaz_dbi_dyn_query("*", $gTables['files'], " item_ref ='" . $item_code . "'", 'id_doc DESC', 0, 1);
    $last_doc = gaz_dbi_fetch_array($rs_last_doc);
    // se e' il primo documento dell'anno, resetto il contatore
    if ($last_doc) {
        $rs = $last_doc;
    }
    return $rs;
}

$search_field_Array = array('C' => array('codice', 'Codice'), 'D' => array('descri', 'Descrizione'), 'B' => array('barcode', 'Codice a barre'));
//
require("../../library/include/header.php");

$script_transl = HeadMain();

if (isset($_GET['auxil'])) {
    $auxil = $_GET['auxil'];
}
if (isset($_GET['all'])) {
    $auxil = "&all=yes";
    $where = $search_field_Array[$admin_aziend['artsea']][0] . " LIKE '%'";
    $passo = 100000;
} else {
    if (isset($_GET['auxil'])) {
        $where = $search_field_Array[$admin_aziend['artsea']][0] . " LIKE '" . addslashes($_GET['auxil']) . "%'";
    }
}

if (!isset($_GET['auxil'])) {
    $auxil = "";
    $where = $search_field_Array[$admin_aziend['artsea']][0] . " LIKE '$auxil%'";
}
?>
<div align="center" class="FacetFormHeaderFont">Articoli</div>
<?php
$recordnav = new recordnav($gTables['artico'], $where, $limit, $passo);
$recordnav->output();
/** ENRICO FEDELE */
/* pulizia del codice, eliminato boxover, aggiunte classi bootstrap alla tabella, convertite immagini in glyphicons */
?>
<form method="GET">
    <table class="Tlarge table table-striped table-bordered table-condensed table-responsive">
        <thead>
            <tr>
                <th class="FacetFieldCaptionTD" colspan="2"><?php echo $search_field_Array[$admin_aziend['artsea']][1]; ?>:
                    <input type="text" name="auxil" value="<?php if ($auxil != "&all=yes") echo $auxil; ?>" maxlength="15" size="15" tabindex="1" class="FacetInput" />
                    <input type="submit" name="search" value="Cerca" tabindex="1" onClick="javascript:document.report.all.value = 1;" />
                </th>
                <th></th>
                <th>
                    <input type="submit" name="all" value="Mostra tutti" onClick="javascript:document.report.all.value = 1;" />
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = gaz_dbi_dyn_query('*', $gTables['artico'], $where, $orderby, $limit, $passo);
// creo l'array (header => campi) per l'ordinamento dei record
            $headers_artico = array("Codice" => "codice",
                "Descrizione" => "descri",
                "Doc." => "",
                "Categoria merceologica" => "catmer",
                "U.M." => "unimis",
                "Prezzo 1" => "preve1",
                "Prezzo acquisto" => "preacq",
                "Giacenza" => "");
            if ($admin_aziend['conmag'] > 0) {
                $headers_artico = array_merge($headers_artico, array("Visualizza e/o stampa" => '',
                    "Barcode" => "barcode",
                    "Duplica" => "",
                    "Cancella" => ""));
            } else {
                $headers_artico = array_merge($headers_artico, array("Barcode" => "barcode",
                    "Duplica" => "",
                    "Cancella" => ""));
            }

            $linkHeaders = new linkHeaders($headers_artico);
            $gForm = new magazzForm();

            echo '<tr>';
            $linkHeaders->output();
            echo '</tr>';
            while ($r = gaz_dbi_fetch_array($result)) {

                gaz_set_time_limit(30);
                $lastdoc = getLastDoc($r["codice"]);
                $mv = $gForm->getStockValue(false, $r['codice']);
                $magval = array_pop($mv);
                $iva = gaz_dbi_get_row($gTables['aliiva'], "codice", $r["aliiva"]);
                echo '<tr>
	   			<td class="FacetDataTD">
					<a class="btn btn-xs btn-success btn-block" href="admin_artico.php?codice=' . $r["codice"] . '&amp;Update">
						<i class="glyphicon glyphicon-edit"></i>&nbsp;' . $r["codice"] . '
					</a>
				</td>';
                echo '	<td class="FacetDataTD">
	   				<span class="gazie-tooltip" data-type="product-thumb" data-id="' . $r["codice"] . '" data-title="' . $r["annota"] . '">' . $r["descri"] . '</span>
				</td>
	   			<td class="FacetDataTD" align="center" title="">';
                if ($lastdoc) {
                    echo '		<a href="../root/retrieve.php?id_doc=' . $lastdoc["id_doc"] . '">
		 				<i class="glyphicon glyphicon-file" title="Ultimo certificato e/o documentazione disponibile"></i>
					</a>';
                }
                echo '	</td>
	   			<td class="FacetDataTD" align="center">' . $r["catmer"] . ' </td>
				<td class="FacetDataTD" align="center">' . $r["unimis"] . ' </td>
				<td class="FacetDataTD" align="right">' . number_format($r["preve1"], $admin_aziend['decimal_price'], ',', '.') . ' </td>
				<td class="FacetDataTD" align="right">' . number_format($r["preacq"], $admin_aziend['decimal_price'], ',', '.') . ' </td>
				<td class="FacetDataTD" align="right" title="' . $admin_aziend['symbol'] . ' ' . $magval['v_g'] . '">
					' . number_format($magval['q_g'], $admin_aziend['decimal_quantity'], ',', '.') . '
				</td>';
                if ($admin_aziend['conmag'] > 0) {
                    echo '	<td class="FacetDataTD" align="center" title="Visualizza e/o stampa la scheda di magazzino">
		  				<a class="btn btn-xs btn-default" href="../magazz/select_schart.php?di=0101' . date('Y') . '&df=' . date('dmY') . '&id=' . $r['codice'] . '">
							<i class="glyphicon glyphicon-check"></i><i class="glyphicon glyphicon-print"></i>
						</a>
					</td>';
                }
                echo '	<td class="FacetDataTD" align="center" title="Stampa Codici a Barre">
	   				<a class="btn btn-xs btn-default" href="stampa_barcode.php?code=' . $r["codice"] . '">
						<i class="glyphicon glyphicon-barcode"></i>
					</a>
				</td>
				<td class="FacetDataTD" align="center" title="Duplica articolo in (' . $r["codice"] . '_2)">
					<a class="btn btn-xs btn-default" href="clone_artico.php?codice=' . $r["codice"] . '">
						<i class="glyphicon glyphicon-export"></i>
					</a>
				</td>
				<td class="FacetDataTD" align="center">
					<a class="btn btn-xs btn-default btn-elimina" href="delete_artico.php?codice=' . $r["codice"] . '">
						<i class="glyphicon glyphicon-remove"></i>
					</a>
				</td>
			</tr>';
            }
            ?>
        </tbody>
    </table>
</form>
</body>
</html>