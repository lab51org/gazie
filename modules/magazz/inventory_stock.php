<?php
/*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2011 - Antonio De Vincentiis Montesilvano (PE)
                                (www.devincentiis.it)
                        <http://gazie.altervista.org>
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
$gForm = new magazzForm;
$msg='';

if (!isset($_POST['ritorno'])) { //al primo accesso allo script
    $_POST['ritorno'] = $_SERVER['HTTP_REFERER'];
    $form['date_Y'] = date("Y");
    $form['date_M'] = date("m");
    $form['date_D'] = date("d");
    $rs_first = gaz_dbi_dyn_query("codice", $gTables['catmer'],1,"codice ASC",0,1);
    $cm_first = gaz_dbi_fetch_array($rs_first);
    $form['catmer'] = $cm_first['codice'];
    $utsdate= mktime(0,0,0,$form['date_M'],$form['date_D'],$form['date_Y']);
    $date = date("Y-m-d",$utsdate);
    $result = gaz_dbi_dyn_query($gTables['artico'].'.*, '.$gTables['catmer'].'.descri AS descat,'.$gTables['catmer'].'.annota AS anncat', $gTables['artico'].' LEFT JOIN '.$gTables['catmer'].' ON catmer = '.$gTables['catmer'].'.codice', "catmer = ".$form["catmer"],'catmer ASC, '.$gTables['artico'].'.codice ASC');
    if ($result) {
        while ($r = gaz_dbi_fetch_array($result)) {
              $magval=array_pop($gForm->getStockValue(false,$r['codice'],$date));
              $form['a'][$r['codice']]['i_d'] = $r['descri'];
              $form['a'][$r['codice']]['i_u'] = $r['unimis'];
              $form['a'][$r['codice']]['v_a'] = $magval['v'];
              $form['a'][$r['codice']]['i_a'] = $r['annota'];
              $form['a'][$r['codice']]['i_g'] = $r['catmer'];
              $form['a'][$r['codice']]['g_d'] = $r['descat'];
              $form['a'][$r['codice']]['g_a'] = $r['anncat'];
              $form['a'][$r['codice']]['p_e'] = $magval['q_g'];
              $form['a'][$r['codice']]['l_e'] = $magval['v_g'];
              $form['a'][$r['codice']]['t_e'] = $magval['q_g'];
              $form['check'.$r['codice']] = '';
              if ($magval['q_g'] < 0 ){
                 $form['check_'.$r['codice']] = ' checked ';
                 $form['a'][$r['codice']]['col'] = 'red';
                 $form['a'][$r['codice']]['g_a'] = $magval['q_g'];
              } elseif ($magval['q_g']>0) {
                 $form['check_'.$r['codice']] = ' checked ';
                 $form['a'][$r['codice']]['col'] = '';
                 $form['a'][$r['codice']]['g_a'] = $magval['q_g'];
              } else {
                 $form['a'][$r['codice']]['col'] = '';
                 $form['a'][$r['codice']]['g_a'] = $magval['q_g'];
              }
        }
    } else {
    }
} else { //nelle  successive entrate
    if (isset($_POST['Return'])) {
        header("Location: ".$_POST['ritorno']);
        exit;
    }
    $form['date_Y'] = intval($_POST['date_Y']);
    $form['date_M'] = intval($_POST['date_M']);
    $form['date_D'] = intval($_POST['date_D']);
    $form['catmer'] = intval($_POST['catmer']);
    $utsdate= mktime(0,0,0,$form['date_M'],$form['date_D'],$form['date_Y']);
    $date = date("Y-m-d",$utsdate);
    $where="catmer = ".$form["catmer"];
    if ($form['catmer']==100){
      $where=1;
    }
    $ctrl_cm=0;
    $result = gaz_dbi_dyn_query($gTables['artico'].'.*, '.$gTables['catmer'].'.descri AS descat,'.$gTables['catmer'].'.annota AS anncat', $gTables['artico'].' LEFT JOIN '.$gTables['catmer'].' ON catmer = '.$gTables['catmer'].'.codice', $where,'catmer ASC, '.$gTables['artico'].'.codice ASC');
    if ($result) {
       while ($r = gaz_dbi_fetch_array($result)) {
           if ($r['catmer']<>$ctrl_cm ){
             set_time_limit (30);
             $ctrl_cm=$r['catmer'];
           }
           $magval=array_pop($gForm->getStockValue(false,$r['codice'],$date));
           $form['a'][$r['codice']]['i_d'] = $r['descri'];
           $form['a'][$r['codice']]['i_u'] = $r['unimis'];
           $form['a'][$r['codice']]['v_a'] = $magval['v'];
           $form['a'][$r['codice']]['i_a'] = $r['annota'];
           $form['a'][$r['codice']]['i_g'] = $r['catmer'];
           $form['a'][$r['codice']]['g_d'] = $r['descat'];
           $form['a'][$r['codice']]['g_a'] = $r['anncat'];
           $form['a'][$r['codice']]['p_e'] = $magval['q_g'];
           $form['a'][$r['codice']]['l_e'] = $magval['v_g'];
           $form['a'][$r['codice']]['t_e'] = $magval['q_g'];
           $form['check'.$r['codice']] = '';
           if ($magval['q_g'] < 0 ){
              $form['check_'.$r['codice']] = ' checked ';
              $form['a'][$r['codice']]['col'] = 'red';
              $form['a'][$r['codice']]['g_a'] = $magval['q_g'];
           } else {
              $form['a'][$r['codice']]['col'] = '';
              $form['a'][$r['codice']]['g_a'] = $magval['q_g'];
           }
       }
    }
    if (isset($_POST['insert'])) {  //in caso di conferma
        $val=0;
        foreach ($_POST as $k=>$v) { //controllo sui dati inseriti e flaggati
           if (substr($k,0,6) == 'check_') {
           } else continue;
        }
           if (empty($msg)) {  //se non ci sono errori
              header("Location: report_movmag.php");
              exit;
           }
    }
}
require("../../library/include/header.php");
$script_transl=HeadMain(0);
?>
<SCRIPT LANGUAGE="JavaScript" ID="datapopup">
function toggle(boxID, toggleID) {
  var box = document.getElementById(boxID);
  var toggle = document.getElementById(toggleID);
  updateToggle = box.checked ? toggle.disabled=false : toggle.disabled=true;
}
</SCRIPT>
<?php
echo "<form method=\"POST\" name=\"maschera\">\n";
echo "<input type=\"hidden\" name=\"hidden_req\" value=\"\" />";
echo "<input type=\"hidden\" name=\"ritorno\" value=\"".$_POST['ritorno']."\" />";
echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".ucfirst($script_transl['title'])." ". $script_transl['del'];
$gForm->Calendar('date',$form['date_D'],$form['date_M'],$form['date_Y'],'FacetSelect','date');
echo $script_transl['catmer'];
$gForm->selectFromDB('catmer','catmer','codice',$form['catmer'],false,1,'-','descri','catmer','FacetSelect',array('value'=>100,'descri'=>'*** '.$script_transl['all'].' ***'));
echo "</div>\n";
echo "<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"FacetFormTABLE\" align=\"center\">\n";
if (!empty($msg)) {
    echo '<tr><td colspan="6" class="FacetDataTDred">'.$gForm->outputErrors($msg,$script_transl['errors'])."</td></tr>\n";
}
echo "<tr><td class=\"FacetFieldCaptionTD\">".$script_transl['select']."</td>
         <td class=\"FacetFieldCaptionTD\">".$script_transl['code']."</td>
         <td class=\"FacetFieldCaptionTD\">".$script_transl['descri']."</td>
         <td class=\"FacetFieldCaptionTD\">".$script_transl['mu']."</td>
         <td class=\"FacetFieldCaptionTD\">".$script_transl['v_a']."</td>
         <td align=\"right\" class=\"FacetFieldCaptionTD\">".$script_transl['v_r']."</td>
         <td class=\"FacetFieldCaptionTD\">".$script_transl['g_a']."</td>
         <td align=\"right\" class=\"FacetFieldCaptionTD\">".$script_transl['g_r']."</td>
         <td class=\"FacetFieldCaptionTD\">".$script_transl['g_v']."</td>
         </tr>\n";
$ctrl_cm=0;
if (isset($form['a'])) {
   foreach($form['a'] as $k=>$v) {
        //ini default value
        $title = "title=\"cssbody=[FacetInput] cssheader=[FacetButton] header=[".$v['i_a']."] body=[<center><img src='../root/view.php?table=artico&value=".$k."'>] fade=[on] fadespeed=[0.03] \"";
        $class= ' class="FacetDataTD'.$v['col'].'" ';
        // end default value
        if ($ctrl_cm <> $v['i_g']) {
            $cm_title = "title=\"cssbody=[FacetInput] cssheader=[FacetButton] header=[".$v['g_a']."] body=[<center><img src='../root/view.php?table=catmer&value=".$v['i_g']."'>] fade=[on] fadespeed=[0.03] \"";
            echo "<tr>\n";
            echo "<td></td>";
            echo "<td $cm_title class=\"FacetFieldCaptionTD\" colspan=\"8\" align=\"left\">".$v['i_g'].' - '.$v['g_d']."</td>\n";
            echo "</tr>\n";
        }
        echo "<tr>\n";
        if (!isset($form['check_'.$k])){
           $form['check_'.$k]='';
        }
        echo "<td class=\"FacetFieldCaptionTD\" align=\"center\">\n<input type=\"checkbox\" name=\"check_$k\" ".$form['check_'.$k]." ></td>\n";
        echo "<td $title $class align=\"left\">".$k."</td>\n";
        echo "<td $title $class align=\"left\">".$v['i_d']."</td>\n";
        echo "<td $class align=\"center\">".$v['i_u']."</td>\n";
        echo "<td $class align=\"center\" align=\"right\">".gaz_format_quantity($v['v_a'],0,$admin_aziend['decimal_quantity'])."</td>\n";
        echo "<td $class align=\"right\">
              <input id=\"v_achk_$k\" name=\"v_achk_$k\" onClick=\"toggle('v_achk_$k', 'a[$k][v_r]')\" type=\"checkbox\" />
              <input type=\"text\" size=\"10\" style=\"text-align:right\" onchange=\"document.maschera.check_$k.checked=true\" id=\"a[$k][v_r]\" name=\"a[$k][v_r]\" value=\"".$v['v_a']."\" disabled ></td>\n";
        echo "<td $class align=\"center\" align=\"right\">".gaz_format_quantity($v['p_e'],0,$admin_aziend['decimal_quantity'])."</td>\n";
        echo "<td $class align=\"right\"><input type=\"text\" style=\"text-align:right\" onchange=\"document.maschera.check_$k.checked=true\" name=\"a[$k][g_a]\" value=\"".$v['g_a']."\"></td>\n";
        echo "<td $class align=\"center\" align=\"right\">".gaz_format_quantity($v['l_e'],0,$admin_aziend['decimal_quantity'])."</td>\n";
        echo "</tr>\n";
        $ctrl_cm = $v['i_g'];
   }
   echo "<tr>
      <td  colspan=\"2\" class=\"FacetFieldCaptionTD\"><input type=\"submit\" name=\"Return\" value=\"".$script_transl['return']."\">&nbsp;</td>
      <td align=\"right\" colspan=\"7\" class=\"FacetFooterTD\"><input type=\"submit\" name=\"preview\" value=\"".$script_transl['view']."!\">&nbsp;</td>
      </tr>\n";
} else {
   echo "<tr>
      <td colspan=\"8\" class=\"FacetFormHeaderFont\">".$script_transl[17]."</td>
      </tr>\n";

}
echo "</table>\n";
?>
</form>
</body>
</html>