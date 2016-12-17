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
require("../../library/include/datlib.inc.php");
$admin_aziend = checkAdmin(9);
require("../root/lib.function.php");
$checkUpd = new CheckDbAlign;
//
// Verifica i parametri della chiamata.
//
require("../../library/include/header.php");
$script_transl = HeadMain();

if (isset($_POST['hidden_req'])) { // accessi successivi allo script
    $form['hidden_req'] = $_POST["hidden_req"];
    $form['ritorno'] = $_POST['ritorno'];
    $form['do_backup'] = $_POST["do_backup"];
    if (isset($_POST['save_config'])) { // ho chiesto la modifica della configurazione
        $nv = filter_input(INPUT_POST, 'backup_mode');
        $kb = filter_input(INPUT_POST, 'keep_backup');
        $fs = filter_input(INPUT_POST, 'freespace_backup');
        $fi = filter_input(INPUT_POST, 'filebackup');
        $checkUpd->backupMode($nv); // passando un valore alla stessa funzione faccio l'update
        gaz_dbi_put_row($gTables['config'], 'variable', 'keep_backup', 'cvalue', $kb);
        gaz_dbi_put_row($gTables['config'], 'variable', 'freespace_backup', 'cvalue', $fs);
        gaz_dbi_put_row($gTables['config'], 'variable', 'file_backup', 'cvalue', $fi);
    }
} else {
    $form['hidden_req'] = '';
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
    $form['do_backup'] = 0;
}
$bm = $checkUpd->backupMode();
$keep = gaz_dbi_get_row($gTables['config'], 'variable', 'keep_backup');
$freespace = gaz_dbi_get_row($gTables['config'], 'variable', 'freespace_backup');
$filebackup = gaz_dbi_get_row($gTables['config'], 'variable', 'file_backup');
?>
<div align="center" class="FacetFormHeaderFont">
    <?php echo $script_transl['title']; ?>
</div>
<form method="POST">
    <div class="container">
        <ul class="nav nav-tabs">
            <li class="active"><a data-toggle="pill" href="#lista"><?php echo $script_transl['title']; ?></a></li>
            <li><a data-toggle="pill" href="#config"><?php echo $script_transl['config']; ?></a></li>
        </ul>
    </div>

    <input type="hidden" name="do_backup" value="1">
    <input type="hidden" value="<?php echo $form['hidden_req']; ?>" name="hidden_req" />
    <input type="hidden" value="<?php echo $form['ritorno']; ?>" name="ritorno" />

    <div class="tab-content">
        <div id="lista" class="tab-pane fade in active">
            <div class="table-responsive">

            <table class="Tlarge table table-striped table-bordered table-condensed">
                <tr>
                    <th class="FacetFieldCaptionTD"><?php echo $script_transl['id']; ?></th>
                    <th class="FacetFieldCaptionTD"><?php echo $script_transl['ver']; ?></th>
                    <th class="FacetFieldCaptionTD"><?php echo $script_transl['name']; ?></th>
                    <th class="FacetFieldCaptionTD"><?php echo $script_transl['size']; ?></th>            
                    <th class="FacetFieldCaptionTD"><?php echo $script_transl['rec']; ?></th>
                    <th class="FacetFieldCaptionTD"><?php echo $script_transl['dow']; ?></th>
                    <th class="FacetFieldCaptionTD" align="center"><?php echo $script_transl['delete']; ?></th>
                </tr>
                <?php
                $interval = 0;
                $files = array();
                if ($handle = opendir('../../data/files/backups/')) {
                    while (false !== ($file = readdir($handle))) {
                        if ($file != "." && $file != ".." && strpos($file, ".gaz")) {
                            $files[filemtime('../../data/files/backups/' . $file)] = $file;
                        }
                    }
                    closedir($handle);
                    krsort($files);
                    $reallyLastModified = end($files);
                    $index = 0;
                    $id = array ();
                    foreach ($files as $file) {
                        preg_match('/-(.*?)-/',$file, $id);
                        
                        if ($index < 30) {
                            ?>
                            <tr class="FacetDataTD"><td><a class="btn btn-xs btn-default" href="">
                                        <?php echo (count($id)>0) ? $id[1] : "nd"; ?>
                                    </a></td>
                                <td>
                                    <?php 
                                        if ( preg_match('/-v(.*?).sql/',$file, $versione)>0 )                                      
                                            echo $versione[1];
                                    ?>
                                </td>
                                <td>
                                    <?php echo $file; ?>
                                </td>
                                <td>
                                    <?php echo formatSizeUnits(filesize('../../data/files/backups/' . $file)); ?>
                                </td>
                                <td align="center">
                                    <a class="btn btn-xs btn-default" href=""><i class="glyphicon glyphicon-repeat"></i></a>
                                </td>
                                <td align="center">
                                    <a class="btn btn-xs btn-default" href="downlo_backup.php?id=<?php echo $file; ?>"><i class="glyphicon glyphicon-download"></i></a>
                                </td>
                                <td align="center">
                                    <a class="btn btn-xs btn-default" href="delete_backup.php?id=<?php echo $file ?>"><i class="glyphicon glyphicon-remove"></i></a>
                                </td>
                            </tr>
                            <?php
                            $index++;
                        } else {
                            unlink("../../data/files/backups/" . $file);
                        }
                    }
                }
                ?>
            </table>
            </div>
        </div>
        <div id="config" class="tab-pane fade">
            <div class="Tlarge FacetDataTD div-config div-table">
                <div class="div-table-row">
                    <div class="div-table-col">
                        <h4><?php echo $script_transl['backup_mode']; ?></h4>
                        <ul class="licheck">

                        <?php
                        // . ': <br/>';

                        foreach ($script_transl['backup_mode_value'] as $k => $v) {
                            echo '<li><input type="radio" name="backup_mode" value="' . $k . '"';
                            if ($bm == $k) {
                                echo ' checked ';
                            }
                            echo '/>&nbsp;' . $v . '</li>';
                        }
                        ?>
                        </ul>
                    </div>
                </div>
                <div class="div-table-row">
                    <div class="div-table-col">
                        <h4>Automatico</h4>
                        <ul class="licheck">
                            <li>Esegui backup dei files (la cartella di gazie verrà salvata) : <input <?php echo ($filebackup['cvalue']==1) ? 'checked="checked"' : ''; ?> type="checkbox" name="filebackup" value="1" /> </li>
                            <li>Numero di backup da conservare : <input type="text" name="keep_backup" value="<?php echo $keep['cvalue']; ?>" /> inserire 0 per tutti</li>
                            <li>Spazio da lasciare libero (%) : <input type="text" name="freespace_backup" value="<?php echo $freespace['cvalue']; ?>" /> raggiunto il limite i backup vecchi verranno cancellati</li>
                        </ul>
                    </div>
                </div>
                <div class="div-table-row">
                    <div class="div-table-col" >
                        <input type="submit" name="save_config" value="<?php echo $script_transl['update']; ?>" />
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>
<?php
require("../../library/include/footer.php");
?>