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
$mas_staff = $admin_aziend['mas_staff'] . "000000";
$worker = $admin_aziend['mas_staff'];
$where = "codice BETWEEN " . $worker . "000000 AND " . $worker . "999999 and codice > $mas_staff";

if (isset($_GET['auxil'])) {
    $auxil = filter_input(INPUT_GET, 'auxil');
} else {
    $auxil = "";
}

if (isset($_GET['auxil1'])) {
    $auxil1 = filter_input(INPUT_GET, 'auxil1');
} else {
    $auxil1 = "";
}

if (isset($_GET['all'])) {
    $auxil = "&all=yes";
    $passo = 100000;
} else {
    if (isset($_GET['auxil']) and $auxil1 == "") {
        $where .= " AND ragso1 LIKE '" . addslashes($auxil) . "%'";
    } elseif (isset($_GET['auxil1'])) {
        $codicetemp = intval($mas_staff) + intval($auxil1);
        $where .= " AND codice LIKE '" . $codicetemp . "%'";
    }
}

if (!isset($_GET['field'])) {
    $orderby = "codice DESC";
}

if (isset($_GET['ricerca_completa'])) {
    $ricerca_testo = filter_input(INPUT_GET, 'ricerca_completa');
    $where .= " and ( ragso1 like '%" . $ricerca_testo . "%' ";
    $where .= " or ragso2 like '%" . $ricerca_testo . "%' ";
    $where .= " or pariva like '%" . $ricerca_testo . "%' ";
    $where .= " or pariva like '%" . $ricerca_testo . "%' ";
    $where .= " or codfis like '%" . $ricerca_testo . "%' ";
    $where .= " or citspe like '%" . $ricerca_testo . "%' )";
}

require("../../library/include/header.php");
$script_transl = HeadMain();
?>
<div align="center" class="FacetFormHeaderFont"> <?php echo $script_transl['title']; ?>
    <form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <table class="Tlarge">
            <tr>
                <td class="FacetFieldCaptionTD">
                    <input placeholder="Cerca" class="input-xs form-control" type="text" name="auxil1" value="<?php echo $auxil1 ?>" maxlength="6" size="7" tabindex="1" class="FacetInput">
                </td>
                <td class="FacetFieldCaptionTD">
                    <input placeholder="Cerca Cognome" class="input-xs form-control" type="text" name="auxil" value="<?php if ($auxil != "&all=yes") echo $auxil; ?>" maxlength="6" size="7" tabindex="1" class="FacetInput">
                </td>
                <td>
                    <input type="submit" class="btn btn-xs btn-default" name="search" value="Cerca" tabindex="1" onClick="javascript:document.report.all.value = 1;">
                </td>
                <td colspan="3">
                    <input type="submit" class="btn btn-xs btn-default" name="all" value="Mostra tutti" onClick="javascript:document.report.all.value = 1;">
                </td>
            </tr>
            <tr>        
                <?php
                $result = gaz_dbi_dyn_query('*', $gTables['clfoco'] . ' LEFT JOIN ' . $gTables['anagra'] . ' ON ' . $gTables['clfoco'] . '.id_anagra = ' . $gTables['anagra'] . '.id'
                        . ' LEFT JOIN ' . $gTables['staff'] . ' ON ' . $gTables['staff'] . '.id_clfoco = ' . $gTables['clfoco'] . '.codice', $where, $orderby, $limit, $passo);
                $recordnav = new recordnav($gTables['clfoco'] . ' LEFT JOIN ' . $gTables['anagra'] . ' ON ' . $gTables['clfoco'] . '.id_anagra = ' . $gTables['anagra'] . '.id', $where, $limit, $passo);
                $recordnav->output();
                ?>
            </tr>
            <tr>
                <?php
                $linkHeaders = new linkHeaders($script_transl['header']);
                $linkHeaders->setAlign(array('left', 'left', 'left', 'center', 'left', 'center', 'center', 'center', 'center', 'center'));
                $linkHeaders->output();
                ?>
            </tr>
            <?php
            while ($a_row = gaz_dbi_fetch_array($result)) {
                echo "<tr>";
                // Colonna codice staffe
                echo "<td class=\"FacetDataTD\" align=\"center\"><a class=\"btn btn-xs btn-default\" href=\"admin_staff.php?codice=" . substr($a_row["codice"], 3) . "&Update\"><i class=\"glyphicon glyphicon-edit\"></i>&nbsp;" . substr($a_row["codice"], 3) . "</a> &nbsp</td>";
                // Colonna cognome
                echo "<td class=\"FacetDataTD\" >" . $a_row["ragso1"] . " &nbsp;</td>";
                // Colonna nome
                echo "<td class=\"FacetDataTD\" >" . $a_row["ragso2"] . " &nbsp;</td>";
                // colonna sesso
                echo "<td class=\"FacetDataTD\" align=\"center\">" . $a_row["sexper"] . "</td>";
                // Colonna mansione
                echo "<td class=\"FacetDataTD\" >" . $a_row["job_title"] . " &nbsp;</td>";
                // colonna indirizzo
                $google_string = str_replace(" ", "+", $a_row["indspe"]) . "," . str_replace(" ", "+", $a_row["capspe"]) . "," . str_replace(" ", "+", $a_row["citspe"]) . "," . str_replace(" ", "+", $a_row["prospe"]);
                echo "<td class=\"FacetDataTD\" title=\"" . $a_row["capspe"] . " " . $a_row["indspe"] . "\">";
                echo "<a class=\"btn btn-xs btn-default\" target=\"_blank\" href=\"https://www.google.it/maps/place/" . $google_string . "\">" . $a_row["citspe"] . " (" . $a_row["prospe"] . ")&nbsp;<i class=\"glyphicon glyphicon-map-marker\"></i></a>";
                echo "</td>";
                // composizione telefono
                $title = "";
                $telefono = "";
                if (!empty($a_row["telefo"])) {
                    $telefono = $a_row["telefo"];
                    if (!empty($a_row["cell"])) {
                        $title .= "cell:" . $a_row["cell"];
                    }
                    if (!empty($a_row["fax"])) {
                        $title .= " fax:" . $a_row["fax"];
                    }
                } elseif (!empty($a_row["cell"])) {
                    $telefono = $a_row["cell"];
                    if (!empty($a_row["fax"])) {
                        $title .= " fax:" . $a_row["fax"];
                    }
                } else {
                    $telefono = "_";
                    $title = " nessun contatto telefonico memorizzato ";
                }
                // colonna telefono
                echo "<td class=\"FacetDataTD\" title=\"$title\" align=\"center\">" . gaz_html_call_tel($telefono) . " &nbsp;</td>";
                // colonna fiscali
                echo "<td class=\"FacetDataTD\" align=\"center\">" . $a_row['codfis'] . "</td>";
                // colonna stampa privacy
                echo "<td title=\"stampa informativa sulla privacy\" class=\"FacetDataTD\" align=\"center\"><a class=\"btn btn-xs btn-default\" href=\"stampa_privacy.php?codice=" . $a_row["codice"] . "\" target=\"_blank\"><i class=\"glyphicon glyphicon-print\"></i></a></td>";
                echo "<td class=\"FacetDataTD\" align=\"center\"><a class=\"btn btn-xs btn-default btn-elimina\" href=\"delete_staff.php?codice=" . substr($a_row["codice"], 3) . "\"><i class=\"glyphicon glyphicon-remove\"></i></a></td>";
                echo "</tr>\n";
            }
            ?>
    </form>
</table>
</div><!-- chiude div container role main --></body>
</html>