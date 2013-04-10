<?php
/*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2013 - Antonio De Vincentiis Montesilvano (PE)
         (www.facebook.com/antonio.devincentiis.9)
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
if (isset($_POST['Delete']))
  {
    //
    // La cancellazione dell'effetto è stata confermata!
    //
    // Rilegge i dati dell'effetto.
    //
    $effetto = gaz_dbi_get_row($gTables['effett'], "id_tes", intval($_POST['id_tes']));
    //
    // Verifica se l'effetto da cancellare è già stato contabilizzato.
    // In tal caso, ne elimina subito la registrazione.
    //
    if ($effetto['id_con'] > 0)
      {
        $movimento = gaz_dbi_get_row($gTables['tesmov'], "id_tes",
                                     $effetto['id_con']);
        if ($movimento['id_tes'] == $effetto['id_con']
            and ($movimento['caucon'] == "RIB"
                 or $movimento['caucon'] == "TRA"))
          {
            gaz_dbi_del_row($gTables['tesmov'], 'id_tes', $effetto['id_con']);
            gaz_dbi_del_row($gTables['rigmoc'], 'id_tes', $effetto['id_con']);
            gaz_dbi_del_row($gTables['rigmoi'], 'id_tes', $effetto['id_con']);
          }
      }
    //
    // Alla fine elimina definitivamente l'effetto e conclude.
    //
    $result = gaz_dbi_del_row($gTables['effett'], "id_tes", intval($_POST['id_tes']));
    header("Location: report_effett.php");
    exit;
  }
else
  {
    //
    // Legge i dati dell'effetto di cui è stata richiesta
    // la cancellazione, assieme a tutto quello che cui
    // l'effetto da cancellare è collegato.
    //
    $form = gaz_dbi_get_row($gTables['effett'], "id_tes", intval($_GET['id_tes']));
    $cliente = gaz_dbi_get_row($gTables['clfoco'],"codice",$form['clfoco']);
    $pagame = gaz_dbi_get_row($gTables['pagame'],"codice",$form['pagame']);
    $banapp = gaz_dbi_get_row($gTables['banapp'],"codice",$form['banapp']);
    //
    // Ripesca anche il riferimento alla scrittura, se l'effetto
    // risulta già contabilizzato.
    //
    if ($form['id_con'] > 0)
      {
        $movimento = gaz_dbi_get_row($gTables['tesmov'], "id_tes",
                                     $form['id_con']);
      }
  }
//
//
//
if (isset($_POST['Return'])){
        header("Location: report_effett.php");
        exit;
}
//
// Se siamo giunti a questo punto, è stata richiesta la cancellazione
// dell'effetto, ma ciò deve ancora essere confermato. Pertanto
// si procede con la costruzione di una tabella riepilogativa dei
// dati dell'effetto.
//
require("../../library/include/header.php");
$script_transl=HeadMain('','','select_effett');
?>
<form method="POST">
<input type="hidden" name="id_tes" value="<?php print intval($_GET['id_tes'])?>">
<div align="center" class="FacetFormHeaderFont"><?php echo $script_transl['warning'].'!!! '.$script_transl['del_this'].' ID= '.intval($_GET['id_tes']); ?> </div>
<table border="0" cellpadding="3" cellspacing="1" class="FacetFormTABLE" align="center">
  <tr>
    <td class="FacetFieldCaptionTD"><?php echo $script_transl['progre']; ?></td>
    <td class="FacetDataTD"><?php print $form["progre"]; ?>&nbsp;</td>
  </tr>
  <tr>
    <td class="FacetFieldCaptionTD"><?php echo $script_transl['clfoco']; ?></td>
    <td class="FacetDataTD"><?php print $cliente["descri"]; ?>&nbsp;</td>
  </tr>
 <tr>
    <td class="FacetFieldCaptionTD"><?php echo $script_transl['pagame']; ?></td>
    <td class="FacetDataTD"><?php print $pagame["descri"]; ?>&nbsp;</td>
  </tr>
 <tr>
    <td class="FacetFieldCaptionTD"><?php echo $script_transl['banapp']; ?></td>
    <td class="FacetDataTD"><?php print $banapp["descri"]; ?>&nbsp;</td>
  </tr>
 <tr>
    <td class="FacetFieldCaptionTD"><?php echo $script_transl['impeff']; ?></td>
    <td class="FacetDataTD"><?php print gaz_format_number($form["impeff"]); ?>&nbsp;</td>
  </tr>
 <tr>
    <td class="FacetFieldCaptionTD"><?php echo $script_transl['date_exp']; ?></td>
    <td class="FacetDataTD"><?php print gaz_format_date($form["scaden"]); ?>&nbsp;</td>
  </tr>
 <tr>
    <td class="FacetFieldCaptionTD"><?php echo $script_transl['account_id']; ?></td>
    <td class="FacetDataTD"><?php print ($form['id_con']); ?>&nbsp;</td>
  </tr>
<tr>
    <td align="right">
<?php
echo '<input type="submit" accesskey="r" name="Return" value="'.$script_transl['return'].'"></td><td>
     '.ucfirst($script_transl['safe']);
echo ' <input type="submit" accesskey="d" name="Delete" value="'.$script_transl['delete'].'">';
?>
</td>
</tr>
</table>
</form>
</body>





