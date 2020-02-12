<?php
/*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2020 - Antonio De Vincentiis Montesilvano (PE)
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
$message = "Sei sicuro di voler rimuovere ?";
$titolo="Cancella l'uso di questo fitofarmaco";
if (isset($_POST['Delete']))
    {
        $result = gaz_dbi_del_row($gTables['camp_uso_fitofarmaci'], "id", $_POST['id']);
        header("Location: report_fitofarmaci.php");
        exit;
    }

if (isset($_POST['Return']))
        {
        header("Location: report_fitofarmaci.php");
        exit;
        }

if (!isset($_POST['Delete']))
    {
    $id= $_GET['id'];
    $form = gaz_dbi_get_row($gTables['camp_uso_fitofarmaci'], "id", $id);
    }

require("../../library/include/header.php"); HeadMain();
?>
<form method="POST">
<input type="hidden" name="id" value="<?php print $id?>">
<div><font class="text-center text-danger">Attenzione!!! Eliminazione uso  ID: <?php print $id; ?> </font></div>
<table class="GazFormDeleteTable">
<tr>
<td colspan="2" class="FacetDataTDred">
<?php
if (! $message == "")
    {
    print "$message";
    }
?>
</td>
</tr>
<tr>
<tr>
<td class="FacetFieldCaptionTD">ID &nbsp;</td>
<td class="FacetDataTD"> <?php print $form["id"]; ?>&nbsp;</td>
</tr>
<tr>
<td class="FacetFieldCaptionTD">Nome fitofarmaco &nbsp;</td>
<td class="FacetDataTD"><?php print $form["cod_art"] ?>&nbsp;</td>
</tr>

<td colspan="2" align="right">Se sei sicuro conferma l'eliminazione &nbsp;
<input type="submit" name="Delete" class="btn btn-danger" value="Elimina">&nbsp;
</td>
</tr>
</table>
</form>
<?php
require("../../library/include/footer.php");
?>