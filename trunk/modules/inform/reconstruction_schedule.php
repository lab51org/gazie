<?php
/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2021 - Antonio De Vincentiis Montesilvano (PE)
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
$msg = array('err' => array(), 'war' => array());
$paymov = new Schedule;

if (!isset($_POST['hidden_req'])) { //al primo accesso allo script per update
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
    $form['hidden_req'] = '';
    $form['search_partner'] = '';
    $form['descri_partner'] = '';
    $form['id_partner'] = 0;
    if (isset($_GET['id_partner'])){
        $form['id_partner'] = intval($_GET['id_partner']);
        $partner = gaz_dbi_get_row($gTables['clfoco'], 'codice',  $form['id_partner']);
        $form['search_partner'] = $partner['ragso1'];
        $form['descri_partner'] = $partner['ragso1'];
    }
} else { // accessi successivi
    $form['ritorno'] = $_POST['ritorno'];
    $form['hidden_req'] = $_POST['hidden_req'];
    $form['id_partner'] = intval($_POST['id_partner']);
    $form['search_partner'] = '';
    if ($form['id_partner']>0){
       $partner = gaz_dbi_get_row($gTables['clfoco']." LEFT JOIN ".$gTables['anagra']." ON ".$gTables['clfoco'].".id_anagra = ".$gTables['anagra'].".id", 'codice',  $form['id_partner']);
       $form['search_partner'] = $partner['ragso1'];
    }
    // Se viene inviata la richiesta di cambio produzione
    if ($_POST['hidden_req'] == 'change_partner') {
        $form['id_partner'] = 0;
        $form['search_partner'] = '';
        $form['descri_partner'] = '';
        $form['hidden_req'] = '';
    }

    if (count($msg['err']) <= 0) { // non ci sono errori, posso procedere
    }
    
}

require("../../library/include/header.php");
$script_transl = HeadMain(0, array('custom/autocomplete'));
?>
<script>
$( function() {
    $( "#search_partner" ).autocomplete({
        source: "search.php?opt=partner",
        minLength: 2,
        html: true, // optional (jquery.ui.autocomplete.html.js required)

        // optional (if other layers overlap autocomplete list)
        open: function(event, ui) {
            $(".ui-autocomplete").css("z-index", 1000);
        },
        select: function(event, ui) {
            $("#id_partner").val(ui.item.value);
            $(this).closest("form").submit();
        }
    });
});
</script>
<form method="POST" name="form">
<input type="hidden" name="ritorno" value="<?php echo $form['ritorno']; ?>">
<input type="hidden" name="hidden_req" value="<?php echo $form['hidden_req']; ?>">
<?php
$gForm = new informForm();

if (count($msg['err']) > 0) { // ho un errore
    $gForm->gazHeadMessage($msg['err'], $script_transl['err'], 'err');
}
if (count($msg['war']) > 0) { // ho un alert
    $gForm->gazHeadMessage($msg['war'], $script_transl['war'], 'war');
}
?>
<div class="h3 text-center"><?php echo ucfirst($script_transl['title']); ?></div>
<div class="panel panel-default gaz-table-form div-bordered">
  <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="id_partner" class="col-sm-4 control-label"><?php echo $script_transl['id_partner']; ?></label>
    <?php
    $gForm->selectPartner($form['search_partner'], $form['id_partner'], $admin_aziend['mascli']);    
    ?>
                </div>
            </div>
        </div><!-- chiude row  -->
</div>
</div>
</form>
<?php
$date=date('Y-m-d');
// ottengo il valore del saldo contabile per confrontarlo con quello dello scedenziario
$acc_bal = $paymov->getPartnerAccountingBalance($form['id_partner'], $date);
$paymov->getPartnerStatus($form['id_partner'], $date,'DESC');

if ($form['id_partner'] > 100000000) { // partner selezionato
    $acc_bal = (substr($form['id_partner'],0,3)==$admin_aziend['mascli'])?$acc_bal:-$acc_bal;
    ?>
<div class="col-xs-6">
<h3 class="sub-header">Movimenti partite da scadenzario</h3>
    <div class="table-responsive">
        <table class="table">
             <thead>
                <tr>
                  <th class="col-xs-3 text-center">Documento</th>
                  <th class="col-xs-3 text-right">Importo</th>
                  <th class="col-xs-3 text-center">Scadenza</th>
                  <th class="col-xs-3 text-center">Progressivo</th>
                </tr>
              </thead>
              <tbody>
<?php        
    echo "<tr><td colspan='8'>" . $acc_bal . "</td></tr>";
    $paymov_bal = 0.00;
    foreach ($paymov->PartnerStatus as $k => $v) {
        $tmpNumDoc = $paymov->docData[$k]['numdoc'];
        $tmpDatDoc = $paymov->docData[$k]['datdoc'];
        $tmpData = $paymov->docData[$k];
        /** fine modifica FP */
        $amount = 0.00;
        echo "<tr>";
        echo '<td class="FacetDataTD" colspan=4><a class="btn btn-xs btn-default" href="../contab/admin_movcon.php?Update&id_tes='. $paymov->docData[$k]['id_tes'] . '"><i class="glyphicon glyphicon-edit"></i>' .$paymov->docData[$k]['descri'] . ' n.' . $paymov->docData[$k]['numdoc'] . ' del ' . gaz_format_date($paymov->docData[$k]['datdoc']) . "</a> ID partita $k</td></tr>\n";
        foreach ($v as $ki => $vi) {
            $class_paymov = 'FacetDataTDevidenziaCL';
            $v_op = '';
            $cl_exp = '';
            if ($vi['op_val'] >= 0.01) {
                $v_op = gaz_format_number($vi['op_val']);
                $paymov_bal += $vi['op_val'];
            }
            $v_cl = '';
            if ($vi['cl_val'] >= 0.01) {
                $v_cl = gaz_format_number($vi['cl_val']);
                $cl_exp = gaz_format_date($vi['cl_exp']);
                $paymov_bal -= $vi['cl_val'];
            }
            $expo = '';
            if ($vi['expo_day'] >= 1) {
                $expo = $vi['expo_day'];
                if ($vi['cl_val'] == $vi['op_val']) {
                    $vi['status'] = 2; // la partita è chiusa ma è esposta a rischio insolvenza
                    $class_paymov = 'FacetDataTDevidenziaOK';
                }
            } else {
                if ($vi['cl_val'] == $vi['op_val']) { // chiusa e non esposta
                    $cl_exp = '';
                    $class_paymov = 'FacetDataTD';
                } elseif ($vi['status'] == 3) { // SCADUTA
                    $cl_exp = '';
                    $class_paymov = 'FacetDataTDevidenziaKO';
                } elseif ($vi['status'] == 9) { // PAGAMENTO ANTICIPATO
                    $class_paymov = 'FacetDataTDevidenziaBL';
                    $vi['expiry'] = $vi['cl_exp'];
                }
            }
            echo '<tr class="' . $class_paymov . '">';
            echo '<td colspan=2 class="text-right">' . $v_op . "</td>";
            echo '<td class="text-center">' . gaz_format_date($vi['expiry']) . "</td>";
            echo '<td class="text-center">';
            foreach ($vi['cl_rig_data'] as $vj) {
                echo "<a class=\"btn btn-xs btn-default btn-edit\"  href=\"../contab/admin_movcon.php?id_tes=" . $vj['id_tes'] . "&Update\" title=\"" . $script_transl['update'] . ': ' . $vj['descri'] . " € " . gaz_format_number($vj['import']) . "\"><i class=\"glyphicon glyphicon-edit\"></i>" . $vj['id_tes'] . "</a>\n ";
            }
            echo $v_cl . "</td>";
            if ($vi['status'] <> 1 || $vi['status'] < 9) { // accumulo solo se non è chiusa
                $amount += round($vi['op_val'] - $vi['cl_val'], 2);
            }
            echo "</tr>\n";
        }
        if (!isset($_POST['paymov'])) {
            $form['paymov'][$k][$ki]['amount'] = $amount;
            $form['paymov'][$k][$ki]['id_tesdoc_ref'] = $k;
        }
        $open = 'cl';
        if ($amount >= 0.01) {
            // attributo opcl per js come aperto
            $open = 'op';
        }
        echo '<input type="hidden" id="post_' . $k . '_' . $ki . '_id_tesdoc_ref" name="paymov[' . $k . '][' . $ki . '][id_tesdoc_ref]" value="' . $k . "\" />";
        echo '<tr><td colspan=2 class="text-right"><b>Totale partita: € ' . gaz_format_number($form['paymov'][$k][$ki]['amount']) . '</b></td><td colspan=2 class="text-right"><b>'.gaz_format_number($paymov_bal)."</b></td></tr>\n";
    }
?>
            </tbody>
        </table>
    </div>
</div>
<div class="col-xs-6">
<h3 class="sub-header">Partitario da movimenti contabili</h3>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                  <th class="col-xs-3">#</th>
                  <th class="col-xs-3">#</th>
                  <th class="col-xs-3">#</th>
                  <th class="col-xs-3">#</th>
                </tr>
            </thead>
            <tbody>
            <tbody>
        </table>
    </div>
</div>
<?php
}
//$gForm->delete_all_partner_paymov($form['id_partner']);


/* 

PAYMOV DELETE

$gForm->delete_all_partner_paymov($form['id_partner']);


PAYMOV INSERT

$paymov_value = array('id_tesdoc_ref' => $year . '6' . $form['seziva'] . str_pad($form['protoc'], 9, 0, STR_PAD_LEFT),
    'id_rigmoc_doc' => $last_id_rig,
    'amount' => $v['amount'],
    'expiry' => $v['date']);
paymovInsert($paymov_value);

*/
require("../../library/include/footer.php");
?>