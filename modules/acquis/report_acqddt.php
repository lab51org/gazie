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
?>
<table border="0" cellpadding="3" cellspacing="1" align="center" width="70%">
	<tr>
		<td align="center" class="FacetFormHeaderFont">
        	<a href="admin_docacq.php?tipdoc=ADT&Insert" accesskey="d">Ricevuto D.d.T. d'acquisto da Fornitore</a>
        </td>
    </tr>
</table>
<div align="center" class="FacetFormHeaderFont">Documenti di Trasporto d'Acquisto</div>
<?php
if (!isset($_GET['flag_order'])) {
    $orderby = "datemi DESC";
    }
$where = "tipdoc = 'ADT'";
$recordnav = new recordnav($gTables['tesdoc'], $where, $limit, $passo);
$recordnav -> output();
?>
<table class="Tlarge table table-striped table-bordered table-condensed table-responsive">
	<thead>
		<tr>
<?php
// creo l'array (header => campi) per l'ordinamento dei record
$headers_tesdoc = array  (
            "ID" => "id_tes",
            "Numero" => "numfat",
            "Data" => "datemi",
            "Fornitore (cod.)" => "clfoco",
            "Status" => "",
            "Stampa" => "",
            "Cancella" => ""
            );
$linkHeaders = new linkHeaders($headers_tesdoc);
$linkHeaders -> output();
?>
		</tr>
       </thead>
       <tbody>
<?php
$result = gaz_dbi_dyn_query ('*', $gTables['tesdoc'], $where, $orderby, $limit, $passo);
$anagrafica = new Anagrafica();

while ($a_row = gaz_dbi_fetch_assoc($result)) {
    $cliente = $anagrafica->getPartner($a_row['clfoco']);
    echo '			<tr>
						<td>
							<a class="btn btn-xs btn-default btn-edit" href="admin_docacq.php?id_tes='.$a_row["id_tes"].'&Update">
								<i class="glyphicon glyphicon-edit"></i>'.$a_row["id_tes"].'
							</a>
						</td>
						<td>'.$a_row["numfat"].'</td>
						<td>'.$a_row["datemi"].'</td>
						<td>'.$cliente["ragso1"].'</td>
						<td>'.$a_row["status"].'</td>
						<td>
							<a class="btn btn-xs btn-default" href="stampa_docacq.php?id_tes='.$a_row["id_tes"].'" title="Stampa">
								<i class="glyphicon glyphicon-print"></i>
							</a>
						</td>
						<td>
							<a class="btn btn-xs btn-default" href="delete_docacq.php?id_tes='.$a_row["id_tes"].'" title="Cancella">
								<i class="glyphicon glyphicon-remove"></i>
							</a>
						</td>
					</tr>';
}
?>
			</tbody>
		</table>
        </div>
	</body>
</html>