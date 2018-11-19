<?php
/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2018 - Antonio De Vincentiis Montesilvano (PE)
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
$admin_aziend = checkAdmin(7);
$msg = array('err' => array(), 'war' => array());
$rs_azienda = gaz_dbi_dyn_query('*', $gTables['aziend'], intval($_SESSION['company_id']), 'codice DESC', 0, 1);
$exist_true = gaz_dbi_fetch_array($rs_azienda);

if ($exist_true) {
    $toDo = 'update';
} else {
    $toDo = 'insert';
}

if (isset($_POST['Insert']) || isset($_POST['Update'])) {   //se non e' il primo accesso
    $form = gaz_dbi_parse_post('aziend');
    $form['ritorno'] = $_POST['ritorno'];
    $form['pec'] = trim($form['pec']);
    $form['e_mail'] = trim($form['e_mail']);
    $form['web_url'] = trim($form['web_url']);
    $form['mascli'] = intval(substr($_POST['mascli'], 0, 3));
    $form['masfor'] = intval(substr($_POST['masfor'], 0, 3));
    $form['mas_staff'] = intval(substr($_POST['mas_staff'], 0, 3));
    $form['masban'] = intval(substr($_POST['masban'], 0, 3));
    $form['mas_fixed_assets'] = intval(substr($_POST['mas_fixed_assets'], 0, 3));
    $form['mas_found_assets'] = intval(substr($_POST['mas_found_assets'], 0, 3));
    $form['mas_cost_assets'] = intval(substr($_POST['mas_cost_assets'], 0, 3));
    $form['datnas'] = substr($_POST['datnas'], 0, 10);
    $form['virtual_stamp_auth_date'] = substr($_POST['virtual_stamp_auth_date'], 0, 10);
    $form['intermediary_code'] = intval($_POST['intermediary_code']);
    $form['intermediary_descr'] = substr($_POST['intermediary_descr'], 0, 50);
    $form['amm_min'] = filter_input(INPUT_POST, 'amm_min');
    $form['fae_tipo_cassa'] = filter_input(INPUT_POST, 'fae_tipo_cassa');
    if (isset($_POST['Submit'])) { // conferma tutto
        require("../../library/include/check.inc.php");
        $chk = new check_VATno_TAXcode();
        $cf = trim($form['codfis']);
        if (!empty($_FILES['userfile']['name'])) {
            if (!( $_FILES['userfile']['type'] == "image/png" ||
                    $_FILES['userfile']['type'] == "image/x-png" ||
                    $_FILES['userfile']['type'] == "image/jpeg" ||
                    $_FILES['userfile']['type'] == "image/jpg" ||
                    $_FILES['userfile']['type'] == "image/gif" ||
                    $_FILES['userfile']['type'] == "image/x-gif"))
                $msg['err'][] = 'image';
            if ($_FILES['userfile']['size'] > 63999)
                $msg['err'][] = 'imasize';
        }
        if ($toDo == 'insert' && $_FILES['userfile']['size'] < 1) {
            $msg['err'][] = 'sexper';
        }
        if (strlen($cf) == 11) {
            $rs_cf = $chk->check_VAT_reg_no($cf, $form['country']);
            if ($form['sexper'] != 'G') {
                $msg['err'][] = 'cf_sex';
            }
        } elseif (empty($cf)) {
            $msg['err'][] = 'cf_emp';
        } else {
            $rs_cf = $chk->check_TAXcode($cf, $form['country']);
            if ($form['sexper'] == 'G') {
                $msg['err'][] = 'cf_pg';
            }
        }
        if (!empty($rs_cf)) {
            $msg['err'][] = 'codfis';
        }
        if (!empty($form['pariva'])) {
            $rs_pi = $chk->check_VAT_reg_no($form['pariva'], $form['country']);
            if (!empty($rs_pi)) {
                $msg['err'][] = 'pariva';
            }
        }
        /** ENRICO FEDELE */
        /* Compatibilità con il nuovo simple pick color */
        $form["colore"] = substr($form["colore"], 1);
        /** ENRICO FEDELE */
        $lumix = hexdec(substr($form["colore"], 0, 2)) + hexdec(substr($form["colore"], 0, 2)) + hexdec(substr($form["colore"], 0, 2));
        if ($lumix < 408) {
            $msg['err'][] = 'sexper';
        }
        if (empty($form['ragso1'])) {
            $msg['err'][] = 'ragso1';
        }
        if (empty($form['sexper'])) {
            $msg['err'][] = 'sexper';
        }
        if (!gaz_format_date($form["datnas"], 'chk'))
            $msg['err'][] = 'datnas';
        if (!gaz_format_date($form["virtual_stamp_auth_date"], 'chk'))
            $msg['err'][] = 'virtual_stamp_auth_date';

        if (empty($form['indspe'])) {
            $msg['err'][] = 'indspe';
        }
        if (empty($form['citspe'])) {
            $msg['err'][] = 'citspe';
        }
        if (empty($form['prospe'])) {
            $msg['err'][] = 'prospe';
        }
        $cap = new postal_code;
        if ($cap->check_postal_code($form["capspe"], $form["country"])) {
            $msg['err'][] = 'capspe';
        }
        if (!filter_var($form['pec'], FILTER_VALIDATE_EMAIL) && !empty($form['pec'])) {
            $msg['err'][] = 'pec';
        }
        if (!filter_var($form['e_mail'], FILTER_VALIDATE_EMAIL) && !empty($form['e_mail'])) {
            $msg['err'][] = 'e_mail';
        }
        if (!filter_var($form['web_url'], FILTER_VALIDATE_URL) && !empty($form['e_mail']) && $form['web_url'] != "") {
            $msg['err'][] = 'web_url';
        }
        if ($form['cod_ateco'] < 10000) {
            $msg['err'][] = 'cod_ateco';
        }
        if (count($msg['err']) == 0) { // nessun errore
            $form['datnas'] = gaz_format_date($form['datnas'], true);
            $form['virtual_stamp_auth_date'] = gaz_format_date($form['virtual_stamp_auth_date'], true);
            if ($_FILES['userfile']['size'] > 0) { //se c'e' una nuova immagine nel buffer
                $form['image'] = file_get_contents($_FILES['userfile']['tmp_name']);
            }
            // aggiorno il db
            if ($toDo == 'insert') {
                gaz_dbi_table_insert('aziend', $form);
            } elseif ($toDo == 'update') {
                gaz_dbi_table_update('aziend', $form['codice'], $form);
            }
            // in ogni caso se è stata scelta come azienda intermediatrice verso l'AdE aggiorno la configurazione
            if (( $form['codice'] == $form['intermediary_code'] || $form['intermediary_code'] == 0 ) && isset($_POST['intermediary_check'])) {
                if ($_POST['intermediary_check'] == 'y') {
                    gaz_dbi_put_row($gTables['config'], 'variable', 'intermediary', 'cvalue', $form['codice']);
                } else { // no intermediario
                    gaz_dbi_put_row($gTables['config'], 'variable', 'intermediary', 'cvalue', 0);
                }
            }
            header("Location: docume_config.php");
            exit;
        }
    } elseif (isset($_POST['Return'])) { // torno indietro
        header("Location: " . $form['ritorno']);
        exit;
    }
} elseif ($exist_true) { //se e' il primo accesso per UPDATE
    $form = gaz_dbi_get_row($gTables['aziend'], 'codice', intval($_SESSION['company_id']));
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
    $form['datnas'] = gaz_format_date($form['datnas'], false, false);
    $form['virtual_stamp_auth_date'] = gaz_format_date($form['virtual_stamp_auth_date'], false, false);
    // rilevo l'eventuale intermediario
    $intermediary = gaz_dbi_get_row($gTables['config'], 'variable', 'intermediary');
    $form['intermediary_code'] = $intermediary['cvalue'];
    if ($intermediary['cvalue'] > 0) {
        $intermediary_descr = gaz_dbi_get_row($gTables['aziend'], 'codice', $intermediary['cvalue']);
        $form['intermediary_descr'] = $intermediary_descr['ragso1'] . ' ' . $intermediary_descr['ragso2'];
    } else {
        $form['intermediary_descr'] = '';
    }
} elseif (!isset($_POST['Insert'])) { //se e' il primo accesso per INSERT
    $form = gaz_dbi_fields('aziend');
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
    $form['datnas'] = date("d/m/Y");
    $form['virtual_stamp_auth_date'] = '1/1/2000';
    $form['country'] = 'IT';
    $form['id_language'] = 1;
    $form['id_currency'] = 1;
    $form['decimal_price'] = 3;
    $form['ivaera'] = 5;
    $form['image'] = file_get_contents( "../../library/images/comp_logo.gif");
    $form['web_url'] = 'http://';
    // rilevo l'eventuale intermediario
    $intermediary = gaz_dbi_get_row($gTables['config'], 'variable', 'intermediary');
    $form['intermediary_code'] = $intermediary['cvalue'];
    if ($intermediary['cvalue'] > 0) {
        $intermediary_descr = gaz_dbi_get_row($gTables['aziend'], 'codice', $intermediary['cvalue']);
        $form['intermediary_descr'] = $intermediary_descr['ragso1'] . ' ' . $intermediary_descr['ragso2'];
    } else {
        $form['intermediary_descr'] = '';
    }
}

require("../../library/include/header.php");
$script_transl = HeadMain(0, array('calendarpopup/CalendarPopup', 'custom/autocomplete', 'custom/jquery.simple-color'));
?>
<script>
    $(function () {
        $('#amm_min').selectmenu();
        $('#fae_tipo_cassa').selectmenu();
        $('#causale_pagam_770').selectmenu();
        $('#fiscal_reg').selectmenu();
        $("#datnas, #virtual_stamp_auth_date").datepicker({showButtonPanel: true, showOtherMonths: true, selectOtherMonths: true});
    });
    $(document).ready(function () {
        $('.simple_color_custom').simpleColor({
            boxWidth: '115px',
            columns: 37,
            border: '1px solid #333333',
            buttonClass: 'button',
            displayColorCode: true,
            livePreview: true,
            colors: ['888888', '8888AD', '8888C1', '8888D6', '8888EA', '8888FF', 'AD8888', 'AD88AD', 'AD88C1', 'AD88D6', 'AD88EA', 'AD88FF', 'C18888', 'C188AD', 'C188C1', 'C188D6', 'C188EA', 'C188FF', 'D68888', 'D688AD', 'D688C1', 'D688D6', 'D688EA', 'D688FF', 'EA8888', 'EA88AD', 'EA88C1', 'EA88D6', 'EA88EA', 'EA88FF', 'FF8888', 'FF88AD', 'FF88C1', 'FF88D6', 'FF88EA', 'FF88FF',
                '88AD88', '88ADAD', '88ADC1', '88ADD6', '88ADEA', '88ADFF', 'ADAD88', 'ADADAD', 'ADADC1', 'ADADD6', 'ADADEA', 'ADADFF', 'C1AD88', 'C1ADAD', 'C1ADC1', 'C1ADD6', 'C1ADEA', 'C1ADFF', 'D6AD88', 'D6ADAD', 'D6ADC1', 'D6ADD6', 'D6ADEA', 'D6ADFF', 'EAAD88', 'EAADAD', 'EAADC1', 'EAADD6', 'EAADEA', 'EAADFF', 'FFAD88', 'FFADAD', 'FFADC1', 'FFADD6', 'FFADEA', 'FFADFF',
                '88C188', '88C1AD', '88C1C1', '88C1D6', '88C1EA', '88C1FF', 'ADC188', 'ADC1AD', 'ADC1C1', 'ADC1D6', 'ADC1EA', 'ADC1FF', 'C1C188', 'C1C1AD', 'C1C1C1', 'C1C1D6', 'C1C1EA', 'C1C1FF', 'D6C188', 'D6C1AD', 'D6C1C1', 'D6C1D6', 'D6C1EA', 'D6C1FF', 'EAC188', 'EAC1AD', 'EAC1C1', 'EAC1D6', 'EAC1EA', 'EAC1FF', 'FFC188', 'FFC1AD', 'FFC1C1', 'FFC1D6', 'FFC1EA', 'FFC1FF',
                '88D688', '88D6AD', '88D6C1', '88D6D6', '88D6EA', '88D6FF', 'ADD688', 'ADD6AD', 'ADD6C1', 'ADD6D6', 'ADD6EA', 'ADD6FF', 'C1D688', 'C1D6AD', 'C1D6C1', 'C1D6D6', 'C1D6EA', 'C1D6FF', 'D6D688', 'D6D6AD', 'D6D6C1', 'D6D6D6', 'D6D6EA', 'D6D6FF', 'EAD688', 'EAD6AD', 'EAD6C1', 'EAD6D6', 'EAD6EA', 'EAD6FF', 'FFD688', 'FFD6AD', 'FFD6C1', 'FFD6D6', 'FFD6EA', 'FFD6FF',
                '88EA88', '88EAAD', '88EAC1', '88EAD6', '88EAEA', '88EAFF', 'ADEA88', 'ADEAAD', 'ADEAC1', 'ADEAD6', 'ADEAEA', 'ADEAFF', 'C1EA88', 'C1EAAD', 'C1EAC1', 'C1EAD6', 'C1EAEA', 'C1EAFF', 'D6EA88', 'D6EAAD', 'D6EAC1', 'D6EAD6', 'D6EAEA', 'D6EAFF', 'EAEA88', 'EAEAAD', 'EAEAC1', 'EAEAD6', 'EAEAEA', 'EAEAFF', 'FFEA88', 'FFEAAD', 'FFEAC1', 'FFEAD6', 'FFEAEA', 'FFEAFF',
                '88FF88', '88FFAD', '88FFC1', '88FFD6', '88FFEA', '88FFFF', 'ADFF88', 'ADFFAD', 'ADFFC1', 'ADFFD6', 'ADFFEA', 'ADFFFF', 'C1FF88', 'C1FFAD', 'C1FFC1', 'C1FFD6', 'C1FFEA', 'C1FFFF', 'D6FF88', 'D6FFAD', 'D6FFC1', 'D6FFD6', 'D6FFEA', 'D6FFFF', 'EAFF88', 'EAFFAD', 'EAFFC1', 'EAFFD6', 'EAFFEA', 'EAFFFF', 'FFFF88', 'FFFFAD', 'FFFFC1', 'FFFFD6', 'FFFFEA', 'FFFFFF'],
            colorCodeColor: '#000'
        });


    });
    $('#check').button();
</script>
<?php
$gForm = new configForm();
if (count($msg['err']) > 0) { // ho un errore
    $gForm->gazHeadMessage($msg['err'], $script_transl['err'], 'err');
}
?>
<form method="POST" name="form" enctype="multipart/form-data">
    <input type="hidden" name="ritorno" value="<?php echo $form['ritorno'] ?>">
    <input type="hidden" name="<?php echo ucfirst($toDo) ?>" value="">
    <?php
    if ($toDo == 'insert') {
        echo '<div class="text-center"><b>' . $script_transl['ins_this'] . "</b></div>\n";
    } else {
        echo '<div class="text-center"><b>' . $script_transl['upd_this'] . " '" . $form['codice'] . "'</b></div>\n";
        echo '<input type="hidden" value="' . $form['codice'] . '" name="codice" />';
    }
    ?>
    <div class="panel panel-default gaz-table-form">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="ragso1" class="col-sm-4 control-label"><?php echo $script_transl['ragso1']; ?></label>
                        <input class="col-sm-6" type="text" value="<?php echo $form['ragso1']; ?>" name="ragso1" /><a class="btn btn-xs btn-default col-sm-2" href="config_aziend.php"><i class="glyphicon glyphicon-lock"></i>&nbsp;Configurazione avanzata</a>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="ragso2" class="col-sm-4 control-label"><?php echo $script_transl['ragso2']; ?></label>
                        <input class="col-sm-8" type="text" value="<?php echo $form['ragso2']; ?>" name="ragso2" />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="image" class="col-sm-4 control-label"><img src="../root/view.php?table=aziend&value=<?php echo $form['codice']; ?>" width="100" >*</label>
                        <div class="col-sm-8"><?php echo $script_transl['image']; ?><input type="file" name="userfile" /></div>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="legrap_pf_nome" class="col-sm-4 control-label"><?php echo $script_transl['legrap_pf_nome']; ?></label>
                        <input class="col-sm-4" type="text" value="<?php echo $form['legrap_pf_nome']; ?>" name="legrap_pf_nome" />
                        <input class="col-sm-4" type="text" value="<?php echo $form['legrap_pf_cognome']; ?>" name="legrap_pf_cognome" />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="sexper" class="col-sm-4 control-label"><?php echo $script_transl['sexper']; ?>*</label>
                        <?php
                        $gForm->variousSelect('sexper', $script_transl['sexper_value'], $form['sexper'], "col-sm-8", true, '', false, 'style="max-width: 200px;"');
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="datnas" class="col-sm-4 control-label"><?php echo $script_transl['datnas']; ?>*</label>
                        <input type="text" class="col-sm-2" id="datnas" name="datnas" tabindex=7 value="<?php echo $form['datnas']; ?>">
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="luonas" class="col-sm-4 control-label"><?php echo $script_transl['luonas']; ?>*</label>
                        <div class="col-sm-8">
                            <input type="text" id="search_luonas" name="luonas" value="<?php echo $form['luonas']; ?>" maxlength="50" />
                            <input type="text" id="search_pronas" name="pronas" value="<?php echo $form['pronas']; ?>" maxlength="2" size="1" />
                        </div>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="country" class="col-sm-4 control-label"><?php echo $script_transl['country']; ?>*</label>
                        <?php
                        $gForm->selectFromDB('country', 'country', 'iso', $form['country'], 'iso', 0, ' - ', 'name', '', 'col-sm-8', null, 'style="max-width: 250px;"');
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="citspe" class="col-sm-4 control-label"><?php echo $script_transl['citspe']; ?>*</label>
                        <div class="col-sm-8">
                            <input type="text" id="search_location" name="citspe" value="<?php echo $form['citspe']; ?>" maxlength="50" />
                            <input type="text" id="search_location-prospe" name="prospe" value="<?php echo $form['prospe']; ?>" maxlength="2" size="1" />
                        </div>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="indspe" class="col-sm-4 control-label"><?php echo $script_transl['indspe']; ?></label>
                        <input class="col-sm-8" type="text" value="<?php echo $form['indspe']; ?>" name="indspe" />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="capspe" class="col-sm-4 control-label"><?php echo $script_transl['capspe']; ?></label>
                        <input class="col-sm-8" type="text" value="<?php echo $form['capspe']; ?>" name="capspe" maxlength="5"  />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="latitude" class="col-sm-4 control-label"><?php echo $script_transl['latitude'] . " - " . $script_transl['longitude']; ?></label>
                        <div class="col-sm-8">
                            <input class="col-sm-3" type="text" name="latitude" value="<?php echo $form['latitude'] ?>" maxlength="10" /><input class="col-sm-3" type="text" name="longitude" value="<?php echo $form['longitude']; ?>" maxlength="10" /><a class="btn btn-xs btn-default btn-default col-sm-2" href="http://maps.google.com/maps?q=<?php echo $form['latitude'] . "," . $form['longitude']; ?>"> maps -> <i class="glyphicon glyphicon-map-marker"></i></a>
                        </div>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="id_language" class="col-sm-4 control-label"><?php echo $script_transl['id_language']; ?></label>
                        <?php
                        $gForm->selectFromDB('languages', 'id_language', 'lang_id', $form['id_language'], 'lang_id', 1, ' - ', 'title_native', '', 'col-sm-8', null, 'style="max-width: 200px;"');
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="id_currency" class="col-sm-4 control-label"><?php echo $script_transl['id_currency']; ?></label>
                        <?php
                        $gForm->selectFromDB('currencies', 'id_currency', 'id', $form['id_currency'], 'id', 1, ' - ', 'curr_name', '', 'col-sm-8', null, 'style="max-width: 200px;"');
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="sedleg" class="col-sm-4 control-label"><?php echo $script_transl['sedleg']; ?></label>
                        <div class="col-sm-8">
                            <textarea name="sedleg" rows="2" cols="40" maxlength="100" size="30"><?php echo $form['sedleg']; ?></textarea>
                        </div>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="telefo" class="col-sm-4 control-label"><?php echo $script_transl['telefo']; ?></label>
                        <input class="col-sm-8" type="text" value="<?php echo $form['telefo']; ?>" name="telefo" maxlength="50" />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="fax" class="col-sm-4 control-label"><?php echo $script_transl['fax']; ?></label>
                        <input class="col-sm-8" type="text" value="<?php echo $form['fax']; ?>" name="fax" maxlength="50" />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="pariva" class="col-sm-4 control-label"><?php echo $script_transl['pariva']; ?></label>
                        <input class="col-sm-8" type="text" value="<?php echo $form['pariva']; ?>" name="pariva" maxlength="11" />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="codfis" class="col-sm-4 control-label"><?php echo $script_transl['codfis']; ?></label>
                        <input class="col-sm-8" type="text" value="<?php echo $form['codfis']; ?>" name="codfis" maxlength="16" />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="rea" class="col-sm-4 control-label"><?php echo $script_transl['rea']; ?></label>
                        <input class="col-sm-8" type="text" value="<?php echo $form['rea']; ?>" name="rea" maxlength="32" />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="pec" class="col-sm-4 control-label"><?php echo $script_transl['pec']; ?></label>
                        <input class="col-sm-8" type="pec" value="<?php echo $form['pec']; ?>" name="pec" maxlength="50" />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="e_mail" class="col-sm-4 control-label"><?php echo $script_transl['e_mail']; ?></label>
                        <input class="col-sm-8" type="text" value="<?php echo $form['e_mail']; ?>" name="e_mail" maxlength="50" />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="web_url" class="col-sm-4 control-label"><?php echo $script_transl['web_url']; ?></label>
                        <input class="col-sm-8" type="text" value="<?php echo $form['web_url']; ?>" name="web_url" maxlength="50" />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="web_url" class="col-sm-4 control-label"><?php echo $script_transl['intermediary']; ?></label>
                        <input type="hidden" name="intermediary_code" value="<?php echo $form['intermediary_code']; ?>" />
                        <input type="hidden" name="intermediary_descr" value="<?php echo $form['intermediary_descr']; ?>" />
                        <div class="col-sm-8">
                            <?php
                            if ($form['intermediary_code'] == $form['codice']) {
                                ?>
                                <input type="radio" value="y" name="intermediary_check" checked >Si - No<input type="radio" value="n" name="intermediary_check">";
                                <?php
                            } elseif ($form['intermediary_code'] == 0) {
                                echo '<input type="radio" value="y" name="intermediary_check">' . $script_transl['yes'] . ' - ' . $script_transl['no'] . '<input type="radio" checked value="n" name="intermediary_check">';
                            } else {
                                echo $form['intermediary_descr'];
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="cod_ateco" class="col-sm-4 control-label"><?php echo $script_transl['cod_ateco']; ?></label>
                        <input class="col-sm-8" type="text" value="<?php echo $form['cod_ateco']; ?>" name="cod_ateco" maxlength="6" />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="regime" class="col-sm-4 control-label"><?php echo $script_transl['regime']; ?></label>
                        <?php
                        $gForm->variousSelect('regime', $script_transl['regime_value'], $form['regime'], "col-sm-8", false, '', 50, 'style="max-width: 200px;"');
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="preeminent_vat" class="col-sm-4 control-label"><?php echo $script_transl['preeminent_vat']; ?></label>
                        <?php
                        $gForm->selectFromDB('aliiva', 'preeminent_vat', 'codice', $form['preeminent_vat'], 'codice', 0, ' - ', 'descri', '', 'col-sm-8', null, 'style="max-width: 350px;"');
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="fiscal_reg" class="col-sm-4 control-label"><?php echo $script_transl['fiscal_reg']; ?></label>
                        <?php
                        $gForm->variousSelect('fiscal_reg', $script_transl['fiscal_reg_value'], $form['fiscal_reg'], "col-sm-8", true, '', false, 'style="max-width: 350px; font-height:0.4em;"');
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="amm_min" class="col-sm-4 control-label"><?php echo $script_transl['amm_min']; ?></label>
                        <?php
                        $gForm->selSpecieAmmortamentoMin('ammortamenti_ministeriali.xml', 'amm_min', $form["amm_min"]);
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="decimal_quantity" class="col-sm-4 control-label"><?php echo $script_transl['decimal_quantity']; ?></label>
                        <?php
                        $gForm->variousSelect('decimal_quantity', $script_transl['decimal_quantity_value'], $form['decimal_quantity'], "col-sm-8", false, '', 20, 'style="max-width: 100px;"');
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="decimal_price" class="col-sm-4 control-label"><?php echo $script_transl['decimal_price']; ?></label>
                        <?php
                        $gForm->selectNumber('decimal_price', $form['decimal_price'], 0, 0, 5, "col-sm-8", '', 'style="max-width: 100px;"');
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="stock_eval_method" class="col-sm-4 control-label"><?php echo $script_transl['stock_eval_method']; ?></label>
                        <?php
                        $gForm->variousSelect('stock_eval_method', $script_transl['stock_eval_method_value'], $form['stock_eval_method'], "col-sm-8", true, '', false, 'style="max-width: 300px;"');
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="mascli" class="col-sm-4 control-label"><?php echo $script_transl['mascli']; ?></label>
                        <?php
                        $gForm->selectAccount('mascli', $form['mascli'], array(1), '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="masfor" class="col-sm-4 control-label"><?php echo $script_transl['masfor']; ?></label>
                        <?php
                        $gForm->selectAccount('masfor', $form['masfor'], array(2), '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="masban" class="col-sm-4 control-label"><?php echo $script_transl['masban']; ?></label>
                        <?php
                        $gForm->selectAccount('masban', $form['masban'] . '000000', array(1, 5, 9), '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="mas_fixed_assets" class="col-sm-4 control-label"><?php echo $script_transl['mas_fixed_assets']; ?></label>
                        <?php
                        $gForm->selectAccount('mas_fixed_assets', $form['mas_fixed_assets'] . '000000', array(1, 9), '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="mas_found_assets" class="col-sm-4 control-label"><?php echo $script_transl['mas_found_assets']; ?></label>
                        <?php
                        $gForm->selectAccount('mas_found_assets', $form['mas_found_assets'] . '000000', array(2, 9), '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="mas_cost_assets" class="col-sm-4 control-label"><?php echo $script_transl['mas_cost_assets']; ?></label>
                        <?php
                        $gForm->selectAccount('mas_cost_assets', $form['mas_cost_assets'] . '000000', array(3, 9), '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="lost_cost_assets" class="col-sm-4 control-label"><?php echo $script_transl['lost_cost_assets']; ?></label>
                        <?php
                        $gForm->selectAccount('lost_cost_assets', $form['lost_cost_assets'], array('sub', 3, 5), '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="min_rate_deprec" class="col-sm-4 control-label"><?php echo $script_transl['min_rate_deprec']; ?></label>
                        <input class="col-sm-2" step="0.1" type="number" min="0" max="100" value="<?php echo $form['min_rate_deprec']; ?>" name="min_rate_deprec" size="4"  />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="super_amm_account" class="col-sm-4 control-label"><?php echo $script_transl['super_amm_account']; ?></label>
                        <?php
                        $gForm->selectAccount('super_amm_account', $form['super_amm_account'], array('sub', 3, 5), '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="super_amm_rate" class="col-sm-4 control-label"><?php echo $script_transl['super_amm_rate']; ?></label>
                        <input class="col-sm-2" step="0.1" type="number" min="0" max="500" value="<?php echo $form['super_amm_rate']; ?>" name="super_amm_rate" size="4"  />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="capital_gains_account" class="col-sm-4 control-label"><?php echo $script_transl['capital_gains_account']; ?></label>
                        <?php
                        if (!isset($form['capital_gains_account'])) $form['capital_gains_account'] = null;
                        $gForm->selectAccount('capital_gains_account', $form['capital_gains_account'], array('sub', 4), '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="capital_loss_account" class="col-sm-4 control-label"><?php echo $script_transl['capital_loss_account']; ?></label>
                        <?php
                        if (!isset($form['capital_loss_account'])) $form['capital_loss_account'] = null;
                        $gForm->selectAccount('capital_loss_account', $form['capital_loss_account'], array('sub', 3), '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="mas_staff" class="col-sm-4 control-label"><?php echo $script_transl['mas_staff']; ?></label>
                        <?php
                        $gForm->selectAccount('mas_staff', $form['mas_staff'] . '000000', array(2, 9), '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="cassa_" class="col-sm-4 control-label"><?php echo $script_transl['cassa_']; ?></label>
                        <?php
                        $gForm->selectAccount('cassa_', $form['cassa_'], 1, '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="ivaacq" class="col-sm-4 control-label"><?php echo $script_transl['ivaacq']; ?></label>
                        <?php
                        $gForm->selectAccount('ivaacq', $form['ivaacq'], 1, '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="ivaven" class="col-sm-4 control-label"><?php echo $script_transl['ivaven']; ?></label>
                        <?php
                        $gForm->selectAccount('ivaven', $form['ivaven'], 2, '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="ivacor" class="col-sm-4 control-label"><?php echo $script_transl['ivacor']; ?></label>
                        <?php
                        $gForm->selectAccount('ivacor', $form['ivacor'], 2, '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="ivaera" class="col-sm-4 control-label"><?php echo $script_transl['ivaera']; ?></label>
                        <?php
                        $gForm->selectAccount('ivaera', $form['ivaera'],array('sub',1,2,5,9) , '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="split_payment" class="col-sm-4 control-label"><?php echo $script_transl['split_payment']; ?></label>
                        <?php
                        $gForm->selectAccount('split_payment', $form['split_payment'], substr($form['split_payment'], 0, 1), '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="impven" class="col-sm-4 control-label"><?php echo $script_transl['impven']; ?></label>
                        <?php
                        $gForm->selectAccount('impven', $form['impven'], 4, '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="imptra" class="col-sm-4 control-label"><?php echo $script_transl['imptra']; ?></label>
                        <?php
                        $gForm->selectAccount('imptra', $form['imptra'], 4, '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="impimb" class="col-sm-4 control-label"><?php echo $script_transl['impimb']; ?></label>
                        <?php
                        $gForm->selectAccount('impimb', $form['impimb'], 4, '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="impspe" class="col-sm-4 control-label"><?php echo $script_transl['impspe']; ?></label>
                        <?php
                        $gForm->selectAccount('impspe', $form['impspe'], 4, '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="impvar" class="col-sm-4 control-label"><?php echo $script_transl['impvar']; ?></label>
                        <?php
                        $gForm->selectAccount('impvar', $form['impvar'], 4, '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="boleff" class="col-sm-4 control-label"><?php echo $script_transl['boleff']; ?></label>
                        <?php
                        $gForm->selectAccount('boleff', $form['boleff'], 4, '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="omaggi" class="col-sm-4 control-label"><?php echo $script_transl['omaggi']; ?></label>
                        <?php
                        $gForm->selectAccount('omaggi', $form['omaggi'], 3, '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="sales_return" class="col-sm-4 control-label"><?php echo $script_transl['sales_return']; ?></label>
                        <?php
                        $gForm->selectAccount('sales_return', $form['sales_return'], array('sub', 3, 4), '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="impacq" class="col-sm-4 control-label"><?php echo $script_transl['impacq']; ?></label>
                        <?php
                        $gForm->selectAccount('impacq', $form['impacq'], 3, '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="cost_tra" class="col-sm-4 control-label"><?php echo $script_transl['cost_tra']; ?></label>
                        <?php
                        $gForm->selectAccount('cost_tra', $form['cost_tra'], 3, '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="cost_imb" class="col-sm-4 control-label"><?php echo $script_transl['cost_imb']; ?></label>
                        <?php
                        $gForm->selectAccount('cost_imb', $form['cost_imb'], 3, '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="cost_var" class="col-sm-4 control-label"><?php echo $script_transl['cost_var']; ?></label>
                        <?php
                        $gForm->selectAccount('cost_var', $form['cost_var'], 3, '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="purchases_return" class="col-sm-4 control-label"><?php echo $script_transl['purchases_return']; ?></label>
                        <?php
                        $gForm->selectAccount('purchases_return', $form['purchases_return'], array('sub', 3, 4), '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="coriba" class="col-sm-4 control-label"><?php echo $script_transl['coriba']; ?></label>
                        <?php
                        $gForm->selectAccount('coriba', $form['coriba'], array('sub', 1, 2, 5), '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="cotrat" class="col-sm-4 control-label"><?php echo $script_transl['cotrat']; ?></label>
                        <?php
                        $gForm->selectAccount('cotrat', $form['cotrat'], array('sub', 1, 2, 5), '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="cocamb" class="col-sm-4 control-label"><?php echo $script_transl['cocamb']; ?></label>
                        <?php
                        $gForm->selectAccount('cocamb', $form['cocamb'], array('sub', 1, 2, 5), '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="c_ritenute" class="col-sm-4 control-label"><?php echo $script_transl['c_ritenute']; ?></label>
                        <?php
                        $gForm->selectAccount('c_ritenute', $form['c_ritenute'], 1, '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="ritenuta" class="col-sm-4 control-label"><?php echo $script_transl['ritenuta']; ?></label>
                        <input class="col-sm-2" step="0.01" type="number" value="<?php echo $form['ritenuta']; ?>" name="ritenuta"  size="4" />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="c_payroll_tax" class="col-sm-4 control-label"><?php echo $script_transl['c_payroll_tax']; ?></label>
                        <?php
                        $gForm->selectAccount('c_payroll_tax', $form['c_payroll_tax'], array('sub', 2, 4), '', false, "col-sm-8");
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="payroll_tax" class="col-sm-4 control-label"><?php echo $script_transl['payroll_tax']; ?></label>
                        <input class="col-sm-2" step="0.01" type="number" value="<?php echo $form['payroll_tax']; ?>" name="payroll_tax" size="4"  />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="amm_min" class="col-sm-4 control-label"><?php echo $script_transl['fae_tipo_cassa']; ?></label>
                        <?php
						$gForm->selectFromXML('../../library/include/fae_tipo_cassa.xml', 'fae_tipo_cassa', 'fae_tipo_cassa', $form["fae_tipo_cassa"], true, '', 'col-sm-6');
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="amm_min" class="col-sm-4 control-label"><?php echo $script_transl['ra_cassa']; ?></label>
                        <?php
						$gForm->selectNumber('ra_cassa', $form["ra_cassa"],true, 0, 1, "col-sm-8", '', 'style="max-width: 100px;"');
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="causale_pagam_770" class="col-sm-4 control-label"><?php echo $script_transl['causale_pagam_770']; ?></label>
                        <?php
                        $gForm->variousSelect('causale_pagam_770', $script_transl['causale_pagam_770_value'], $form['causale_pagam_770'], "col-sm-8", true, '', false, 'style="max-width: 350px; font-height:0.4em;"');
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="acciva" class="col-sm-4 control-label"><?php echo $script_transl['acciva']; ?></label>
                        <input class="col-sm-2" type="number" value="<?php echo $form['acciva']; ?>" name="acciva" size="4"  />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="taxstamp_limit" class="col-sm-4 control-label"><?php echo $script_transl['taxstamp_limit']; ?></label>
                        <input class="col-sm-2" step="0.01" type="number" value="<?php echo $form['taxstamp_limit']; ?>" name="taxstamp_limit" size="4"  />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="taxstamp" class="col-sm-4 control-label"><?php echo $script_transl['taxstamp']; ?></label>
                        <input class="col-sm-2" step="0.01" type="number" value="<?php echo $form['taxstamp']; ?>" name="taxstamp" size="4"  />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="taxstamp_vat" class="col-sm-4 control-label"><?php echo $script_transl['taxstamp_vat']; ?></label>
                        <?php
                        $gForm->selectFromDB('aliiva', 'taxstamp_vat', 'codice', $form['taxstamp_vat'], 'codice', 0, ' - ', 'descri', '', 'col-sm-8', null, 'style="max-width: 350px;"');
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="perbol" class="col-sm-4 control-label"><?php echo $script_transl['perbol']; ?></label>
                        <input class="col-sm-2" step="0.01" type="number" value="<?php echo $form['perbol']; ?>" name="perbol" size="4"  />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="round_bol" class="col-sm-4 control-label"><?php echo $script_transl['round_bol']; ?></label>
                        <?php
                        $gForm->variousSelect('round_bol', $script_transl['round_bol_value'], $form['round_bol'], "col-sm-8", true, '', false, 'style="max-width: 300px;"');
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="virtual_taxstamp" class="col-sm-4 control-label"><?php echo $script_transl['virtual_taxstamp']; ?></label>
                        <?php
                        $gForm->variousSelect('virtual_taxstamp', $script_transl['virtual_taxstamp_value'], $form['virtual_taxstamp'], "col-sm-8", true, '', false, 'style="max-width: 300px;"');
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="virtual_stamp_auth_prot" class="col-sm-4 control-label"><?php echo $script_transl['virtual_stamp_auth_prot']; ?></label>
                        <input class="col-sm-8" type="text" value="<?php echo $form['virtual_stamp_auth_prot']; ?>" name="virtual_stamp_auth_prot" size="10" maxlength="14"  />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="virtual_stamp_auth_date" class="col-sm-4 control-label"><?php echo $script_transl['virtual_stamp_auth_date']; ?></label>
                        <input type="text" class="col-sm-2" id="virtual_stamp_auth_date" name="virtual_stamp_auth_date" value="<?php echo $form['virtual_stamp_auth_date']; ?>">
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="sperib" class="col-sm-4 control-label"><?php echo $script_transl['sperib']; ?></label>
                        <input class="col-sm-2" step="0.01" type="number" value="<?php echo $form['sperib']; ?>" name="sperib" size="4" />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <?php for ($i = 1; $i <= 9; $i++) { ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="desez" class="col-sm-4 control-label"><?php echo $script_transl['desez'] . $script_transl['sezione'] . $i; ?></label>
                            <input class="col-sm-8" type="text" value="<?php echo $form['desez' . $i]; ?>" name="desez<?php echo $i; ?>"  />
                        </div>
                    </div>
                </div><!-- chiude row  -->
            <?php } ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="reverse_charge_sez" class="col-sm-4 control-label"><?php echo $script_transl['reverse_charge_sez']; ?></label>
                        <?php
                        $gForm->selectNumber('reverse_charge_sez', $form['reverse_charge_sez'], 0, 1, 9, "col-sm-8", '', 'style="max-width: 100px;"');
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="fatimm" class="col-sm-4 control-label"><?php echo $script_transl['fatimm']; ?></label>
                        <?php
                        $gForm->variousSelect('fatimm', $script_transl['fatimm_value'], $form['fatimm'], "col-sm-8", true, '', false, 'style="max-width: 300px;"');
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="fatimm" class="col-sm-4 control-label"><?php echo $script_transl['templ_set']; ?></label>
                        <?php
                        echo '<select name="template">';
                        $relativePath = '../../config';
                        if ($handle = opendir($relativePath)) {
                            while ($file = readdir($handle)) {
                                if (substr($file, 0, 9) != "templates")
                                    continue;
                                $selected = "";
                                if ($form["template"] == substr($file, 10)) {
                                    $selected = " selected ";
                                }
                                echo "<option value=\"" . substr($file, 10) . "\"" . $selected . ">" . ucfirst($file) . "</option>";
                            }
                            closedir($handle);
                        }
                        echo "</select>\n";
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="colore" class="col-sm-4 control-label"><?php echo $script_transl['colore']; ?></label>
                        <div class="col-md-8 company-color">
                            <input class="simple_color_custom" type="text" value="#<?php echo $form['colore']; ?>" name="colore"  />
                        </div>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="conmag" class="col-sm-4 control-label"><?php echo $script_transl['conmag']; ?></label>
                        <?php
                        $gForm->variousSelect('conmag', $script_transl['conmag_value'], $form['conmag'], "col-sm-8", true, '', false, 'style="max-width: 300px;"');
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="ivam_t" class="col-sm-4 control-label"><?php echo $script_transl['ivam_t']; ?></label>
                        <?php
                        $gForm->variousSelect('ivam_t', $script_transl['ivam_t_value'], $form['ivam_t'], "col-sm-8", true, '', false, 'style="max-width: 300px;"');
                        ?>
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="interessi" class="col-sm-4 control-label"><?php echo $script_transl['interessi']; ?></label>
                        <input class="col-sm-2" step="0.01" type="number" value="<?php echo $form['interessi']; ?>" name="interessi"  />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group text-center">
                        <input class="btn btn-warning" name="Submit" type="submit" value="<?php echo strtoupper($script_transl[$toDo]); ?>!">
                    </div>
                </div>
            </div><!-- chiude row  -->
        </div><!-- chiude container  -->
    </div><!-- chiude panel  -->

</form>
<?php
require("../../library/include/footer.php");
?>