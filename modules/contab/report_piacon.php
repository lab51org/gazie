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

if (!isset($_POST['annrip'])) {
    $_POST['annrip'] = date("Y");
}
// INIZIO determinazione limiti di date
$final_date = intval($_POST['annrip']).'1231';
$rs_last_opening = gaz_dbi_dyn_query("*", $gTables['tesmov'], "caucon = 'APE' AND datreg <= ".$final_date,"datreg DESC",0,1);
$last_opening = gaz_dbi_fetch_array($rs_last_opening);
if ($last_opening) {
   $date_ini = substr($last_opening['datreg'],0,4).substr($last_opening['datreg'],5,2).substr($last_opening['datreg'],8,2);
} else {
   $date_ini = '20040101';
}
// FINE determinazione limiti di date

require("../../library/include/header.php");
$strTransl=HeadMain();

/**
Tentativo di breadcrumb, ma credo sia inutile, il menu è sempre bello in alto... si allungherebbe solo la pagina, costringendo a scrollare
<div class="breadcrumb">
	<li><a href="../../modules/root/admin.php">Home</a></li>
	<li><a href="../../modules/contab/docume_contab.php">Contabilit&agrave;</a></li>
	<li class="active"><?php echo $strTransl['title']; ?></li>
</div>
*/

?>
<div class="FacetFormHeaderFont text-center"><?php echo $strTransl['title']; ?></div>
<div class="alert alert-danger text-center" role="alert"><?php echo $strTransl['msg1']; ?></div>

<form method="POST">
    <table class="Tlarge table table-striped table-bordered table-condensed table-responsive">
        <thead>
            <tr>
<?php
foreach ($strTransl['header'] as $k=>$v) {
	echo '				<th class="FacetFieldCaptionTD">'.$k.'</th>';
}
?>
			</tr>
		</thead>
	    <tbody>
<?php
echo '			<tr>
					<td colspan="8" class="FacetDataTD text-right">'.$strTransl['msg2'].' : <select name="annrip" class="FacetSelect" onchange="this.form.submit();">';
for( $counter = date("Y")-3; $counter <= date("Y"); $counter++ ) {
     $selected = "";
     if($counter == $_POST['annrip']) {
        $selected = ' selected=""';
     }
     echo '						<option value="'.$counter.'"'.$selected.'>'.$counter.'</option>';
}
echo '					</select>
					</td>
				</tr>';
$where = "    (codice < ".$admin_aziend['mascli']."000001 OR codice > ".$admin_aziend['mascli']."999999)
          AND (codice < ".$admin_aziend['masfor']."000001 OR codice > ".$admin_aziend['masfor']."999999)";

$select = " SUM(import*(darave='D')) AS dare, 
			SUM(import*(darave='A')) AS avere";

$table  = $gTables['rigmoc']." LEFT JOIN ".$gTables['tesmov']." ON ".$gTables['rigmoc'].".id_tes = ".$gTables['tesmov'].".id_tes ";

$where2 = " AND datreg BETWEEN ".$date_ini." AND ".$final_date." GROUP BY codcon";

$rs     = gaz_dbi_dyn_query ('codice,descri', $gTables['clfoco'], $where, 'codice');

$collapse = 0;

while ($r = gaz_dbi_fetch_array($rs)) {
       $r2=array('dare'=>0,'avere'=>0);
       $rs2=gaz_dbi_dyn_query ($select, $table, 'codcon='.$r['codice'].$where2, 'codcon');
       if ($rs2) {
          $r2=gaz_dbi_fetch_array($rs2);
       }
       if (substr($r["codice"],3) == '000000') {
		   $collapse = $r["codice"];
           echo '			<tr data-toggle="collapse" data-target=".'.$collapse.'">	
								<td class="FacetData">
									<a class="btn btn-xs btn-default btn-edit" href="admin_piacon.php?Update&amp;codice='.$r["codice"].'" title="'.$strTransl['edit_master'].'" >
										<i class="glyphicon glyphicon-edit"></i>&nbsp;'.substr($r["codice"],0,3).'
									</a>
								</td>
								<td class="FacetData">'.substr($r["codice"],3).'</td>
								<td class="FacetData text-danger" colspan="5"><strong>'.$r["descri"].'</strong></td>
								<td class="FacetData text-center">
									<a class="btn btn-xs btn-default btn-elimina" href="delete_piacon.php?codice='.$r["codice"].'">
										<i class="glyphicon glyphicon-remove"></i>
									</a>
								</td>
							</tr>';
       } else {
           echo '			<tr class="'.$collapse.' collapse" aria-expanded="false">
								<td class="FacetDataTD">'.substr($r["codice"],0,3).' </td>
								<td class="FacetDataTD">
									<a class="btn btn-xs btn-default btn-edit" href="admin_piacon.php?Update&amp;codice='.$r["codice"].'" title="'.$strTransl['edit_account'].'">
										<i class="glyphicon glyphicon-edit"></i>&nbsp;'.substr($r["codice"],3).'
									</a>
								</td>
								<td class="FacetDataTD">'.$r["descri"].' </td>
								<td class="FacetDataTD text-right">'.gaz_format_number($r2["dare"]).' </td>
								<td class="FacetDataTD text-right">'.gaz_format_number($r2["avere"]).' </td>
								<td class="FacetDataTD text-right">'.gaz_format_number($r2["dare"]-$r2["avere"]).' </td>
								<td class="FacetDataTD text-center" title="Visualizza e stampa il paritario">
									<a class="btn btn-xs btn-default" href="select_partit.php?id='.$r["codice"].'">
										<i class="glyphicon glyphicon-check"></i>&nbsp;<i class="glyphicon glyphicon-print"></i>
									</a>
								</td>
								<td class="FacetDataTD text-center">
									<a class="btn btn-xs btn-default btn-elimina" href="delete_piacon.php?codice='.$r["codice"].'">
										<i class="glyphicon glyphicon-remove"></i>
									</a>
								</td>
							</tr>';
       }
}
?>
		</tbody>
	</table>
</form>
</div><!-- chiude div container role main --></body>
</html>