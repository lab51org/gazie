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
$admin_aziend=checkAdmin();
require("../../library/include/header.php");
$script_transl=HeadMain();

if (isset($_GET['auxil'])) {
	$auxil = $_GET['auxil'];
} else {
	$auxil = "";
}
	
if (isset($_GET['auxil1'])) {
	$auxil1 = $_GET['auxil1'];
} else {
	$auxil1 = "";
}

if (isset($_GET['all'])) {
	$auxil = "&all=yes";
	$passo = 100000;
} else {
	if (isset($_GET['auxil']) and $auxil1=="") {
		$where .= " AND ragso1 LIKE '".addslashes($auxil)."%'";
	} elseif (isset($_GET['auxil1'])) {
		$codicetemp = intval($mascli)+intval($auxil1); 
		$where .= " AND id LIKE '".$codicetemp."%'";
	}
}

if (!isset($_GET['field'])) {
	$orderby = "id DESC";
}

if ( isset($_GET['ricerca_completa'])) {
	$ricerca_testo = $_GET['ricerca_completa'];
	$where .= " and ( ragso1 like '%".$ricerca_testo."%' ";
	$where .= " or ragso2 like '%".$ricerca_testo."%' ";
	$where .= " or pariva like '%".$ricerca_testo."%' ";
	$where .= " or pariva like '%".$ricerca_testo."%' ";
	$where .= " or codfis like '%".$ricerca_testo."%' ";
	$where .= " or citspe like '%".$ricerca_testo."%' )";
}

?>
<div align="center" class="FacetFormHeaderFont"><?php echo $script_transl["title"]; ?></div>
<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
<table class="Tlarge">
<tr>
<td class="FacetFieldCaptionTD">
<input placeholder="Cerca" class="input-xs form-control" type="text" name="auxil1" value="<?php echo $auxil1 ?>" maxlength="6" size="7" tabindex="1" class="FacetInput">
</td>
<td class="FacetFieldCaptionTD">
<input placeholder="Cerca Ragione Sociale" class="input-xs form-control" type="text" name="auxil" value="<?php if ($auxil != "&all=yes") echo $auxil; ?>" maxlength="6" size="7" tabindex="1" class="FacetInput">
</td>
<td class="FacetFieldCaptionTD">
<input type="submit" class="btn btn-xs btn-default" name="search" value="Cerca" tabindex="1" onClick="javascript:document.report.all.value=1;">
</td>
<td class="FacetFieldCaptionTD" colspan="6">
<input type="submit" class="btn btn-xs btn-default" name="all" value="Mostra tutti" onClick="javascript:document.report.all.value=1;">
</td>
</tr>
<?php
$result = gaz_dbi_dyn_query ('*', $gTables['anagra'], $where, $orderby, $limit, $passo);
$headers_ = array  (
            "Codice" => "id",
            "Ragione Sociale" => "ragso1",
            "Ragione Sociale" => "ragso2",
            "Tipo" => "sexper",
            "Citt&agrave;" => "citspe",
            "Telefono" => "telefo",
            "Cellulare" => "cell",
            "Fax" => "fax",
            "EMail" => "e_mail",
            //"P.IVA - C.F." => "",
            "Cancella" => ""
            );
$linkHeaders = new linkHeaders($headers_);
$linkHeaders -> output();
$recordnav = new recordnav($gTables['anagra'], $where, $limit, $passo);
$recordnav -> output();
?>
</tr>
<?php
while ($a_row = gaz_dbi_fetch_array($result)) {
    echo "<tr>";
    echo "<td class=\"FacetDataTD\" align=\"center\"><a class=\"btn btn-xs btn-default\" href=\"admin_anagra.php?id=".$a_row["id"]."&Update\"><i class=\"glyphicon glyphicon-edit\"></i>&nbsp;".$a_row["id"]."</a> &nbsp</td>";
	echo "<td class=\"FacetDataTD\" title=\"".$a_row["ragso2"]."\">".$a_row["ragso1"]." ".$a_row["ragso2"]."</td>";
	echo "<td class=\"FacetDataTD\" align=\"center\">".$a_row["sexper"]."</td>";
	$google_string = str_replace(" ","+",$a_row["indspe"]).",".str_replace(" ","+",$a_row["capspe"]).",".str_replace(" ","+",$a_row["citspe"]).",".str_replace(" ","+",$a_row["prospe"]);
    echo "<td class=\"FacetDataTD\" title=\"".$a_row["capspe"]." ".$a_row["indspe"]."\">";
	echo "<a class=\"btn btn-xs btn-default\" target=\"_blank\" href=\"https://www.google.it/maps/place/".$google_string."\">".$a_row["citspe"]." (".$a_row["prospe"].")&nbsp;<i class=\"glyphicon glyphicon-map-marker\"></i></a>";
	echo "</td>";
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
	echo "<td class=\"FacetDataTD\" title=\"$title\" align=\"center\">".gaz_html_call_tel($a_row["telefo"])." &nbsp;</td>";
    echo "<td class=\"FacetDataTD\" title=\"$title\" align=\"center\">".gaz_html_call_tel($a_row["cell"])." &nbsp;</td>";
    echo "<td class=\"FacetDataTD\" title=\"$title\" align=\"center\">".gaz_html_call_tel($a_row["fax"])." &nbsp;</td>";
    echo "<td class=\"FacetDataTD\" title=\"$title\" align=\"center\"><a href='mailto:".$a_row["e_mail"]."'>".$a_row["e_mail"]."</a> &nbsp;</td>";   
    echo "<td class=\"FacetDataTD\" align=\"center\"><a class=\"btn btn-xs btn-default btn-elimina\" href=\"delete_anagra.php?codice=".$a_row["id"]."\"><i class=\"glyphicon glyphicon-remove\"></i></a></td>";
    echo "</tr>\n";
}
?>
</form>
</table>
</div>
</body>
</html>