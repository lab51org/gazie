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
$admin_aziend=checkAdmin();
require("../../library/include/header.php");
$script_transl=HeadMain();
echo '<div align="center" class="FacetFormHeaderFont">'.$script_transl['title'].'</div>';
if(!isset($_GET['field'])){
	$field='codice';
	$orderby='codice';
	$flagorder='DESC';
	$flagorpost='DESC';
}
$recordnav = new recordnav($gTables['aliiva'], $where, $limit, $passo);
$recordnav -> output();
echo '<div class="table-responsive"><table class="Tlarge table table-striped table-bordered table-condensed">';
$headers = array  (
            $script_transl['codice']=>'codice',
            $script_transl['descri']=>'descri',
            $script_transl['type']=>'tipiva',
            $script_transl['operation_type']=>'operation_type',
            $script_transl['aliquo']=>'aliquo',
            $script_transl['taxstamp']=>'taxstamp',
            $script_transl['fae_natura']=>'fae_natura',
            $script_transl['delete']=>''
            );
$linkHeaders = new linkHeaders($headers);
$linkHeaders -> output();
$result = gaz_dbi_dyn_query ('*', $gTables['aliiva'], $where, $orderby, $limit, $passo);
while ($a_row = gaz_dbi_fetch_array($result))
    {
    echo "<tr class=\"FacetDataTD\">";
    echo "<td><a class=\"btn btn-xs btn-default\" href=\"admin_aliiva.php?Update&codice=".$a_row["codice"]."\"><i class=\"glyphicon glyphicon-edit\"></i>&nbsp;".$a_row["codice"]."</a> &nbsp</td>";
    echo "<td>".$a_row["descri"]." &nbsp;</td>";
    echo "<td align=\"center\">".$script_transl['tipiva'][$a_row["tipiva"]]."</td>";
    echo "<td align=\"center\">".$a_row["operation_type"]." &nbsp;</td>";
    echo "<td align=\"center\">".$a_row["aliquo"]." &nbsp;</td>";
    echo "<td align=\"center\">".$script_transl['yn_value'][$a_row["taxstamp"]]." &nbsp;</td>";
    echo "<td align=\"center\">".$a_row["fae_natura"]." &nbsp;</td>";
    echo "<td align=\"center\"><a class=\"btn btn-xs btn-default btn-elimina\" href=\"delete_aliiva.php?codice=".$a_row["codice"]."\"><i class=\"glyphicon glyphicon-remove\"></i></a></td>";
    echo "</tr>";
    }
?>
</table></div>
<?php
require("../../library/include/footer.php");
?>