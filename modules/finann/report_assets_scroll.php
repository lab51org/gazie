<?php
/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2016 - Antonio De Vincentiis Montesilvano (PE)
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
// prevent direct access
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if (!$isAjax) {
    $user_error = 'Access denied - not an AJAX request...';
    trigger_error($user_error, E_USER_ERROR);
}
if (isset($_POST['getresult'])) { //	Evitiamo errori se lo script viene chiamato direttamente
    require("../../library/include/datlib.inc.php");
    $admin_aziend = checkAdmin();
    $no = intval($_POST['getresult']);
    $result = gaz_dbi_dyn_query('*', $gTables['assets'], 'id = 1', 'id DESC', $no, PER_PAGE);
    while ($row = gaz_dbi_fetch_array($result)) {
        $tesmov = gaz_dbi_get_row($gTables['tesmov'], "id_tes", $row['id_tes']);
        $anagrafica = new Anagrafica();
        $fornitore = $anagrafica->getPartner($tesmov['clfoco']);
        ?>
        <tr class="gaz-tr">              
            <td>
                <a class="btn btn-xs btn-default" href="../acquis/admin_assets.php?Update&id=<?php echo $row['id']; ?>" ><i class="glyphicon glyphicon-edit"></i>&nbsp;<?php echo $row['id']; ?></a>
            </td>
            <td>
                <?php echo $row["descri"]; ?>
            </td>
            <td>
                <?php echo $fornitore["descri"]; ?>
            </td>
            <td class="text-right">
                <div class="collapse navbar-collapse">
                <?php echo gaz_format_number($row["a_value"] * $row["quantity"]); ?>
                </div>
            </td>
            <td class="text-right">
                <div class="collapse navbar-collapse">
                    <?php echo round($row["valamm"],1); ?>%
                </div>
            </td>
        </tr>  
        <?php
    }
    exit();
}
?>