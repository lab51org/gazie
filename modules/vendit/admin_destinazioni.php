<?php
/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2017 - Antonio De Vincentiis Montesilvano (PE)
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
$msg = '';

if (isset($_POST['Update']) || isset($_GET['Update'])) {
   $toDo = 'update';
} else {
   $toDo = 'insert';
}

if (isset($_POST['Insert']) || isset($_POST['Update'])) {   //se non e' il primo accesso
   $form = gaz_dbi_parse_post('destina');
   $form['codice'] = trim($form['codice']);
   $form['ritorno'] = $_POST['ritorno'];
   $form['ref_code'] = substr($_POST['ref_code'], 0, 15);
   $form['id_anagra'] = $_POST['id_anagra'];
   foreach ($_POST['search'] as $k => $v) {
      $form['search'][$k] = $v;
   }
   if (isset($_POST['Submit'])) { // conferma tutto
//      if ($toDo == 'update') {  // controlli in caso di modifica
//         if ($form['codice'] != $form['ref_code']) { // se sto modificando il codice originario
//            // controllo che la destinazione ci sia gia'
//            $rs_articolo = gaz_dbi_dyn_query('codice', $gTables['destina'], "codice = '" . $form['codice'] . "'", "codice DESC", 0, 1);
//            $rs = gaz_dbi_fetch_array($rs_articolo);
//            if ($rs) {
//               $msg .= "0+";
//            }
//            // controllo che il precedente non abbia movimenti di magazzino associati
//            $rs_articolo = gaz_dbi_dyn_query('destina', $gTables['movmag'], "artico = '" . $form['ref_code'] . "'", "artico DESC", 0, 1);
//            $rs = gaz_dbi_fetch_array($rs_articolo);
//            if ($rs) {
//               $msg .= "1+";
//            }
//         }
//      } else {
//         // controllo che la destinazione ci sia gia'
//         $rs_articolo = gaz_dbi_dyn_query('codice', $gTables['destina'], "codice = '" . $form['codice'] . "'", "codice DESC", 0, 1);
//         $rs = gaz_dbi_fetch_array($rs_articolo);
//         if ($rs) {
//            $msg .= "2+";
//         }
//      }
//      $msg .= (empty($form["codice"]) ? "5+" : '');
      $msg .= (empty($form["id_anagra"]) ? "0+" : '');
      $msg .= (empty($form["indspe"]) ? "1+" : '');
      $msg .= (empty($form["capspe"]) ? "2+" : '');
      $msg .= (empty($form["citspe"]) ? "3+" : '');
      if (empty($msg)) { // nessun errore
         // aggiorno il db
         if ($toDo == 'insert') {
            gaz_dbi_table_insert('destina', $form);
         } elseif ($toDo == 'update') {
            gaz_dbi_table_update('destina', $form['codice'], $form);
         }
         header("Location: " . $form['ritorno']);
         exit;
      }
   } elseif (isset($_POST['Return'])) { // torno indietro
      header("Location: " . $form['ritorno']);
      exit;
   }
} elseif (!isset($_POST['Update']) && isset($_GET['Update'])) { //se e' il primo accesso per UPDATE
   $form = gaz_dbi_get_row($gTables['destina'], 'codice', substr($_GET['codice'], 0, 15));
   $form['ref_code'] = $form['codice'];
   $form['ritorno'] = $_SERVER['HTTP_REFERER'];
   $form['search']['id_anagra'] = '';
} else { //se e' il primo accesso per INSERT
   $form = gaz_dbi_fields('destina');
   $form['ritorno'] = $_SERVER['HTTP_REFERER'];
   $form['ref_code'] = "";
   $form['country'] = $admin_aziend['country'];
   $form['search']['id_anagra'] = '';
//   $form['id_anagra'] = '';
}

require("../../library/include/header.php");
$script_transl = HeadMain();
echo "<form method=\"POST\" name=\"form\" enctype=\"multipart/form-data\">\n";
echo "<input type=\"hidden\" name=\"ritorno\" value=\"" . $form['ritorno'] . "\">\n";
echo "<input type=\"hidden\" name=\"ref_code\" value=\"" . $form['ref_code'] . "\">\n";
echo "<input type=\"hidden\" name=\"codice\" value=\"" . $form['codice'] . "\">\n";
echo "<input type=\"hidden\" name=\"" . ucfirst($toDo) . "\" value=\"\">";
$gForm = new GAzieForm();
//$mv = $gForm->getStockValue(false, $form['codice']);
//$magval = array_pop($mv);
if ($toDo == 'insert') {
   echo "<div align=\"center\" class=\"FacetFormHeaderFont\">" . $script_transl['ins_this'] . "</div>\n";
} else {
   echo "<div align=\"center\" class=\"FacetFormHeaderFont\">" . $script_transl['upd_this'] . " '" . $form['codice'] . "'</div>\n";
}
echo "<table class=\"Tmiddle\">\n";
if (!empty($msg)) {
   echo '<tr><td colspan="3" class="FacetDataTDred">' . $gForm->outputErrors($msg, $script_transl['errors']) . "</td></tr>\n";
}

echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">" . $script_transl['id_anagra'] . " </td><td class=\"FacetDataTD\" colspan=\"2\">\n";
$select_id_anagra = new selectPartner("id_anagra");
$select_id_anagra->selectAnagra('id_anagra', $form['id_anagra'], $form['search']['id_anagra'], 'id_anagra', $script_transl['mesg']);
echo "</td>\n";
echo "</tr>\n";


echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">" . $script_transl['unita_locale1'] . "</td>\n";
echo "\t<td colspan=\"2\" class=\"FacetDataTD\">
      <input type=\"text\" name=\"unita_locale1\" value=\"" . $form['unita_locale1'] . "\" align=\"right\" maxlength=\"255\" size=\"70\" /></td>\n";
echo "</tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">" . $script_transl['unita_locale2'] . "</td>\n";
echo "\t<td colspan=\"2\" class=\"FacetDataTD\">
      <input type=\"text\" name=\"unita_locale2\" value=\"" . $form['unita_locale2'] . "\" align=\"right\" maxlength=\"255\" size=\"70\" /></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">" . $script_transl['indspe'] . " * </td>\n";
echo "\t<td colspan=\"2\" class=\"FacetDataTD\">
      <input type=\"text\" name=\"indspe\" value=\"" . $form['indspe'] . "\" align=\"right\" maxlength=\"50\" size=\"50\" /></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">" . $script_transl['capspe'] . " * </td>\n";
echo "\t<td colspan=\"2\" class=\"FacetDataTD\">
      <input type=\"text\" name=\"capspe\" codice=\"search_location-capspe\" value=\"" . $form['capspe'] . "\" align=\"right\" maxlength=\"10\" size=\"5\" /></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">" . $script_transl['citspe'] . " *  </td>\n";
echo "\t<td class=\"FacetDataTD\">
      <input type=\"text\" name=\"citspe\" codice=\"search_location\" value=\"" . $form['citspe'] . "\" align=\"right\" maxlength=\"50\" size=\"50\" /></td>\n";
echo "\t<td class=\"FacetDataTD\">
      <input type=\"text\" name=\"prospe\" codice=\"search_location-prospe\" value=\"" . $form['prospe'] . "\" align=\"right\" maxlength=\"2\" size=\"2\" /></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">" . $script_transl['country'] . "</td><td colspan=\"2\" class=\"FacetDataTD\">\n";
$gForm->selectFromDB('country', 'country', 'iso', $form['country'], 'iso', 0, ' - ', 'name');
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">" . $script_transl['telefo'] . " </td>\n";
echo "\t<td colspan=\"2\" class=\"FacetDataTD\">
      <input type=\"text\" name=\"telefo\" value=\"" . $form['telefo'] . "\" align=\"right\" maxlength=\"50\" size=\"50\" /></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">" . $script_transl['fax'] . " </td>\n";
echo "\t<td colspan=\"2\" class=\"FacetDataTD\">
      <input type=\"text\" name=\"fax\" value=\"" . $form['fax'] . "\" align=\"right\" maxlength=\"50\" size=\"50\" /></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">" . $script_transl['cell'] . " </td>\n";
echo "\t<td colspan=\"2\" class=\"FacetDataTD\">
      <input type=\"text\" name=\"cell\" value=\"" . $form['cell'] . "\" align=\"right\" maxlength=\"50\" size=\"50\" /></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">" . $script_transl['e_mail'] . "</td>\n";
echo "\t<td class=\"FacetDataTD\" colspan=\"2\">
      <input type=\"text\" codice=\"email\" name=\"e_mail\" value=\"" . $form['e_mail'] . "\" align=\"right\" maxlength=\"50\" size=\"50\" /></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">" . $script_transl['annota'] . "</td>\n";
echo "\t<td colspan=\"2\" class=\"FacetDataTD\">
      <input type=\"text\" name=\"annota\" value=\"" . $form['annota'] . "\" align=\"right\" maxlength=\"100\" size=\"50\" /></td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">" . $script_transl['sqn'] . "</td>";
echo "\t </td>\n";
echo "\t<td  class=\"FacetDataTD\">\n";
echo '<input name="none" type="submit" value="" disabled>';
echo '<input name="Return" type="submit" value="' . $script_transl['return'] . '!">';
echo "\t </td>\n";
echo "\t<td  class=\"FacetDataTD\" align=\"right\">\n";
echo '<input name="Submit" type="submit" value="' . strtoupper($script_transl[$toDo]) . '!">';
echo "\t </td>\n";
echo "</tr>\n";
?>
</table>
</form>
<?php
require("../../library/include/footer.php");
?>
