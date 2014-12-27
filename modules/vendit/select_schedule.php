<?php
/*
--------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2014 - Antonio De Vincentiis Montesilvano (PE)
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
$msg = "";

if (!isset($_POST['hidden_req'])) { //al primo accesso allo script
    $form['hidden_req'] = '';
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
    $form['this_date_D']=date("d");
    $form['search']['account']='';
    if (isset($_GET['id'])) {
       $form['account']=intval($_GET['id']);
    } else {
       $form['account']=0;
    }
    $form['orderby']=0;
} else { // accessi successivi
    $form['hidden_req']=htmlentities($_POST['hidden_req']);
    $form['ritorno']=$_POST['ritorno'];
    $form['account']=intval($_POST['account']);
    foreach($_POST['search'] as $k=>$v){
       $form['search'][$k]=$v;
    }
    if (isset($_POST['return'])) {
        header("Location: ".$form['ritorno']);
        exit;
    }
    $form['orderby']=intval($_POST['orderby']);
}
// fine controlli

if (isset($_POST['print']) && $msg=='') {
    $_SESSION['print_request']=array('script_name'=>'print_schedule',
                                     'account'=>$form['account'],
                                     'orderby'=>$form['orderby']
                                     );
    header("Location: sent_print.php");
    exit;
}

require("../../library/include/header.php");
$script_transl=HeadMain(0,array('jquery/jquery-1.7.1.min','calendarpopup/CalendarPopup',
                                  'jquery/ui/jquery.ui.core',
                                  'jquery/ui/jquery.ui.widget',
                                  'jquery/ui/jquery.ui.position',
                                  'jquery/ui/jquery.ui.autocomplete',
                                  'jquery/autocomplete_location'));
echo "<form method=\"POST\" name=\"select\">\n";
echo "<input type=\"hidden\" value=\"".$form['hidden_req']."\" name=\"hidden_req\" />\n";
echo "<input type=\"hidden\" value=\"".$form['ritorno']."\" name=\"ritorno\" />\n";
$gForm = new venditForm();
echo "<br /><div align=\"center\" class=\"FacetFormHeaderFont\">".$script_transl['title'];
echo "</div>\n";
echo "<table class=\"Tmiddle\">\n";
if (!empty($msg)) {
    echo '<tr><td colspan="2" class="FacetDataTDred">'.$gForm->outputErrors($msg,$script_transl['errors'])."</td></tr>\n";
}
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['orderby']."</td><td  class=\"FacetDataTD\">\n";
$gForm->variousSelect('orderby',$script_transl['orderby_value'],$form['orderby'],'FacetSelect',0,'orderby');
echo "\t </td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['account']." </td><td class=\"FacetDataTD\" colspan=\"2\">\n";
$select_cliente = new selectPartner('account');
$select_cliente->selectDocPartner('account',$form['account'],$form['search']['account'],'account',$script_transl['mesg'],$admin_aziend['mascli']);
echo "</td>\n";
echo "</tr>\n";
echo "\t<tr class=\"FacetFieldCaptionTD\">\n";
echo "<td align=\"left\"><input type=\"submit\" name=\"return\" value=\"".$script_transl['return']."\">\n";
echo '<td align="right" colspan="2"> <input type="submit" accesskey="i" name="preview" value="';
echo $script_transl['view'];
echo '" tabindex="100" >';
echo "\t </td>\n";
echo "\t </tr>\n";
echo "</table>\n";

if (isset($_POST['preview'])) {
  $scdl = new Schedule;
  $m = $scdl->getScheduleEntries($form['orderby']);
  echo "<table class=\"Tlarge\">";
  if (sizeof($scdl->Entries) > 0) {
        $ctrl_partner=0;
        $ctrl_id_tes=0;
        $ctrl_paymov=0;
        echo "<tr>";
        $linkHeaders = new linkHeaders($script_transl['header']);
        $linkHeaders -> output();
        echo "</tr>";
        while (list($key, $mv) = each($scdl->Entries)) {
            $class_partner='';
            $class_paymov='';
            $class_id_tes='';
            $partner='';
            $id_tes='';
            $paymov='';
            if ($mv["clfoco"]<>$ctrl_partner){
                $class_partner='FacetDataTDred';
                $partner=$mv["ragsoc"];
            }
            if ($mv["id_tes"]<>$ctrl_id_tes){
                $class_id_tes='FacetFieldCaptionTD';
                $id_tes=$mv["id_tes"];
                $mv["datdoc"]=gaz_format_date($mv["datdoc"]);
            } else {
                $mv['descri']='';
                $mv['numdoc']='';
                $mv['datdoc']='';
            }
            if ($mv["id_tesdoc_ref"]<>$ctrl_paymov){
                $paymov=$mv["id_tesdoc_ref"];
                $scdl->getStatus($paymov);
                if($scdl->Status['diff_paydoc']<>0){
                    $class_paymov='FacetDataTDevidenziaOK';
                    $status_descr=$script_transl['status_value'][1];
                } else {
                    $class_paymov='FacetDataTDevidenziaKO';
                    $status_descr=$script_transl['status_value'][0];
                }
            }
            echo "<tr>";
            echo "<td class=\"$class_partner\">".$partner." &nbsp;</td>";
            echo "<td align=\"center\" class=\"$class_paymov\">".$paymov." &nbsp;</td>";
            echo "<td align=\"center\" class=\"$class_paymov\">".$status_descr." &nbsp;</td>";
            echo "<td align=\"center\" class=\"$class_id_tes\"><a href=\"../contab/admin_movcon.php?id_tes=".$mv["id_tes"]."&Update\">".$id_tes."</a> &nbsp</td>";
            echo "<td class=\"$class_id_tes\"><a href=\"../contab/admin_movcon.php?id_tes=".$mv["id_tes"]."&Update\">".$mv['descri']."</a> &nbsp;</td>";
            echo "<td align=\"center\" class=\"FacetDataTD\">".$mv["numdoc"]." &nbsp;</td>";
            echo "<td align=\"center\" class=\"FacetDataTD\">".$mv["datdoc"]." &nbsp;</td>";
            echo "<td align=\"center\" class=\"FacetDataTD\">".gaz_format_date($mv["datreg"])." &nbsp;</td>";
            if ($mv['id_rigmoc_pay']==0){
                echo "<td align=\"center\" class=\"FacetDataTD\">".$mv["amount"]." &nbsp;</td>";
                echo "<td class=\"FacetDataTD\"></td>";
            } else {
                echo "<td class=\"FacetDataTD\"></td>";
                echo "<td align=\"center\" class=\"FacetDataTD\">".$mv["amount"]." &nbsp;</td>";
            }
            echo "<td align=\"center\" class=\"FacetDataTD\">".gaz_format_date($mv["expiry"])." &nbsp;</td>";
            $ctrl_partner=$mv["clfoco"];
            $ctrl_id_tes=$mv["id_tes"];
            $ctrl_paymov=$mv["id_tesdoc_ref"];

        }
     echo "\t<tr class=\"FacetFieldCaptionTD\">\n";
     echo '<td colspan="9" align="right"><input type="submit" name="print" value="';
     echo $script_transl['print'];
     echo '">';
     echo "\t </td>\n";
     echo "\t </tr>\n";
  } else {
     echo "<tr><td class=\"FacetDataTDred\" align=\"center\">".$script_transl['errors'][1]."</TD></TR>\n";
  }
  echo "</table></form>";
}
?>
</body>
</html>