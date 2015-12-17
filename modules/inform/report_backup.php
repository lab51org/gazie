<?php
/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2015 - Antonio De Vincentiis Montesilvano (PE)
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
$admin_aziend = checkAdmin(9);

if (!ini_get('safe_mode')) { //se me lo posso permettere...
    ini_set('memory_limit', '128M');
    gaz_set_time_limit(0);
}
//
// Verifica i parametri della chiamata.
//
require("../../library/include/header.php");
$script_transl = HeadMain();

if (isset($_POST['hidden_req'])) { // accessi successivi allo script
    $form['hidden_req'] = $_POST["hidden_req"];
    $form['ritorno'] = $_POST['ritorno'];
    /*$form['create_database'] = $_POST["create_database"];
    $form['use_database'] = $_POST["use_database"];
    $form['text_encoding'] = $_POST["text_encoding"];*/
    $form['do_backup'] = $_POST["do_backup"];
} else {
    $form['hidden_req'] = '';
    $form['ritorno'] = $_SERVER['HTTP_REFERER'];
    $form['create_database'] = '';
    $form['use_database'] = '';
    $form['text_encoding'] = '';
    $form['do_backup'] = 0;
}
?>
<div align="center" class="FacetFormHeaderFont">
    <?php echo $script_transl['title']; ?>
</div>
<form method="POST">
    <input type="hidden" name="do_backup" value="1">
    <input type="hidden" value="<?php echo $form['hidden_req']; ?>" name="hidden_req" />
    <input type="hidden" value="<?php echo $form['ritorno']; ?>" name="ritorno" />
    
    <table class="Tlarge">
        <tr>
            <th><?php echo $script_transl['id']; ?></th>
            <th><?php echo $script_transl['name']; ?></th>
            <th><?php echo $script_transl['size']; ?></th>            
            <th><?php echo $script_transl['rec']; ?></th>
            <th><?php echo $script_transl['dow']; ?></th>
            <th><input class="btn btn-xs btn-default" type="submit" name="elimina" value="Elimina vecchi backup"/></th>
        </tr>
<?php

//if ( $backup == "internal" ) {
    $interval = 0;
    $files = array();
    if ($handle = opendir('../../data/files/backups/')) {
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != ".." && strpos($file, ".gaz" )) {
                $files[filemtime('../../data/files/backups/'.$file)] = $file;
            }
        }
        closedir($handle);
        ksort($files);
        $reallyLastModified = end($files);
        foreach($files as $file) {
            $id = substr($file,5,12);?>
            <tr><td><a class="btn btn-xs btn-default" href="<?php echo '../../data/files/backups/'.$file; ?>">
                <?php echo $id; ?>
            </a></td>
            <td>
                <?php echo $file; ?>
            </td>
            <td>
                <?php echo formatSizeUnits(filesize('../../data/files/backups/'.$file)); ?>
            </td>
            <td align="center">
                <a class="btn btn-xs btn-default" href="print_ticket_list.php"><i class="glyphicon glyphicon-repeat"></i></a>
            </td>
            <td align="center">
                <a class="btn btn-xs btn-default" href="print_ticket_list.php"><i class="glyphicon glyphicon-download"></i></a>
            </td>
            <td align="center">
                <a class="btn btn-xs btn-default" href="delete_backup.php?id=<?php echo $id ?>"><i class="glyphicon glyphicon-remove"></i></a>
            </td>
            </tr>
        <?php
        }
    }
/*} else {
    
}*/
?>
</table>
</form>
</div><!-- chiude div container role main --></body>
</html>