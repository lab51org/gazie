<?php
/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2019 - Antonio De Vincentiis Montesilvano (PE)
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
$modal_ok_insert = false;
/** ENRICO FEDELE */
/* Inizializzo per aprire in finestra modale */
$modal = false;
if (isset($_POST['mode']) || isset($_GET['mode'])) {
    $modal = true;
    if (isset($_GET['ok_insert'])) {
        $modal_ok_insert = true;
    }
}
/** ENRICO FEDELE */
if (isset($_POST['Update']) || isset($_GET['Update'])) {
    $toDo = 'update';
} else {
    $toDo = 'insert';
}

if (isset($_POST['Insert']) || isset($_POST['Update'])) {   //se non e' il primo accesso
    $form = gaz_dbi_parse_post('municipalities');
    $form['ritorno'] = $_POST['ritorno'];
    if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL) && !empty($form['email'])) {
        $msg['err'][] = 'email';
    }
    if (!filter_var($form['web_url'], FILTER_VALIDATE_URL) && !empty($form['web_url'])) {
        $msg['err'][] = 'web_url';
    }
    if (isset($_POST['Submit'])) { // conferma tutto
        if (strlen($form["name"])<4) {
            $msg['err'][] = 'name';
        }
        if (strlen($form["postal_code"])<4) {
            $msg['err'][] = 'postal_code';
        }
        if (count($msg['err']) == 0) { // nessun errore
            // aggiorno il db
            if ($toDo == 'insert') {
				unset($form['id']);
                gaz_dbi_table_insert('municipalities', $form);
            } elseif ($toDo == 'update') {
                gaz_dbi_table_update('municipalities', array('id',$form['id']), $form);
            }
            header("Location: report_municipalities.php");
            exit;
        }
    }
} elseif (!isset($_POST['Update']) && isset($_GET['Update'])) { //se e' il primo accesso per UPDATE
    $form = gaz_dbi_get_row($gTables['municipalities'], 'id', intval($_GET['id']));
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
} else { //se e' il primo accesso per INSERT
    $form = gaz_dbi_fields('municipalities');
    /** ENRICO FEDELE */
    if ($modal === false) {
        $form['ritorno'] = $_SERVER['HTTP_REFERER'];
    } else {
        $form['ritorno'] = 'admin_municipalities.php';
    }
}

require("../../library/include/header.php");
    $script_transl = HeadMain();
?>
<script type="text/javascript">
</script>
<form method="POST" name="form" enctype="multipart/form-data" id="add-product">
<?php
if ($toDo == 'insert') {
    echo '<div class="text-center"><b>' . $script_transl['ins_this'] . '</b></div>';
} else {
    echo '<div class="text-center"><b>' . $script_transl['upd_this'] . ' ' . $form['id'] . '</b></div>';
}
if (!empty($form['descri'])) $form['descri'] = htmlentities($form['descri'], ENT_QUOTES);
echo '<input type="hidden" name="ritorno" value="' . $form['ritorno'] . '" />';
echo '<input type="hidden" name="id" value="' . $form['id'] . '" />';
$gForm = new informForm();
echo '<input type="hidden" name="' . ucfirst($toDo) . '" value="" />';
if (count($msg['err']) > 0) { // ho un errore
    $gForm->gazHeadMessage($msg['err'], $script_transl['err'], 'err');
}

?>
    <div class="panel panel-default gaz-table-form">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="name" class="col-sm-4 control-label"><?php echo $script_transl['name']; ?></label>
                        <input class="col-sm-8" type="text" value="<?php echo $form['name']; ?>" name="name" maxlength="255" />
                    </div>
                </div>
            </div><!-- chiude row  -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="id_province" class="col-sm-4 control-label"><?php echo $script_transl['id_province']; ?></label>
    <?php
    $gForm->selectFromDB('provinces', 'id_province', 'id', $form['id_province'], false, false, ' - ', 'name', '', 'col-sm-8', null, 'style="max-width: 250px;"');
    ?>
                        </div>
                    </div>
                </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="postal_code" class="col-sm-4 control-label"><?php echo $script_transl['postal_code']; ?></label>
                        <input class="col-sm-4" type="text" value="<?php echo $form['postal_code']; ?>" name="postal_code" maxlength="10" />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="dialing_code" class="col-sm-4 control-label"><?php echo $script_transl['dialing_code']; ?></label>
                        <input class="col-sm-4" type="text" value="<?php echo $form['dialing_code']; ?>" name="dialing_code" maxlength="10" />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="stat_code" class="col-sm-4 control-label"><?php echo $script_transl['stat_code']; ?></label>
                        <input class="col-sm-4" type="text" value="<?php echo $form['stat_code']; ?>" name="stat_code" maxlength="10" />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="code_register" class="col-sm-4 control-label"><?php echo $script_transl['code_register']; ?></label>
                        <input class="col-sm-4" type="text" value="<?php echo $form['code_register']; ?>" name="code_register" maxlength="10" />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="web_url" class="col-sm-4 control-label"><?php echo $script_transl['web_url']; ?></label>
                        <input class="col-sm-4" type="text" value="<?php echo $form['web_url']; ?>" name="web_url" maxlength="100" />
                    </div>
                </div>
            </div><!-- chiude row  -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="email" class="col-sm-4 control-label"><?php echo $script_transl['email']; ?></label>
                        <input class="col-sm-4" type="text" value="<?php echo $form['email']; ?>" name="email" maxlength="100" />
                    </div>
                </div>
            </div><!-- chiude row  -->
        </div> <!-- chiude container -->
    </div><!-- chiude panel -->
    <div class="row">
		<div class="col-xs-1 col-md-4 col-lg-5">
        </div>
		<div class="col-xs-6 col-md-4 col-lg-2">
        <input class="btn btn-warning" tabindex=10 onClick="chkSubmit();" type="submit" name="Submit" value="<?php echo strtoupper($script_transl[$toDo]);?>!"> 
        </div>
	</div><!-- chiude row  -->

</form>
<script type="text/javascript">
    // Basato su: http://www.abeautifulsite.net/whipping-file-inputs-into-shape-with-bootstrap-3/
    $(document).on('change', '.btn-file :file', function () {
        var input = $(this),
                numFiles = input.get(0).files ? input.get(0).files.length : 1,
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [numFiles, label]);
    });
    $(document).ready(function () {
        $('.btn-file :file').on('fileselect', function (event, numFiles, label) {

            var input = $(this).parents('.input-group').find(':text'),
                    log = numFiles > 1 ? numFiles + ' files selected' : label;
            if (input.length) {
                input.val(log);
            } else {
                if (log)
                    alert(log);
            }

        });
    });</script>


<?php
require("../../library/include/footer.php");
?>
