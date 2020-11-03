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
if (isset($styles) && is_array($styles)) {
    foreach ($styles as $v) {
        ?>
        <link href="../../library/theme/g7/<?php echo $v; ?>" rel="stylesheet" type="text/css" />
        <?php
    }
}
if (!isset($_SESSION['menu_alerts_lastcheck'])||((round(time()/60)-$_SESSION['menu_alerts_lastcheck'])> $period )){ // sono passati $period minuti
	// non ho mai controllato se ci sono nuovi ordini oppure è passato troppo tempo dall'ultimo controllo vado a farlo
		echo '<script>menu_check_from_modules();</script>';
} elseif(isset($_SESSION['menu_alerts']) && count($_SESSION['menu_alerts'])>=1) {
        foreach($_SESSION['menu_alerts'] as $k=>$v) {
            // faccio il load per creae un bottone per ogni modulo che lo ha generato (mod,title,button,label,link,style)
            echo "<script>menu_alerts_check('".$k."','".addslashes($v['title'])."','".addslashes($v['button'])."','".addslashes($v['label'])."','".addslashes($v['link'])."','".$v['style']."');</script>";
        }
}

if ( $debug_active==true ) echo "<div>".d($GLOBALS, $_SERVER)."</div>";


?>

<!-- questo è contenuto in library/theme/g7/footer.php -->
<div class="navbar navbar-fixed-bottom" style="border:none;" >
    <div style="background:white;" > GAzie Version: <?php echo GAZIE_VERSION; ?> Software Open Source (lic. GPL)
        <?php echo $script_transl['business'] . " " . $script_transl['proj']; ?>
        <a target="_new" title="<?php echo $script_transl['auth']; ?>" href="http://www.devincentiis.it"> http://www.devincentiis.it</a>
    </div>
</div>
<script src="../../library/bootstrap/js/bootstrap.min.js"></script>
<script src="../../library/theme/g7/smartmenus-master/jquery.smartmenus.js" type="text/javascript"></script>
<script src="../../library/theme/g7/smartmenus-master/bootstrap/jquery.smartmenus.bootstrap.js" type="text/javascript"></script>
<script src="../../js/jquery.ui/jquery-ui.min.js"></script>
<script src="../../js/jquery.ui/datepicker-<?php echo substr($admin_aziend['lang'], 0, 2); ?>.js"></script>
<script src="../../js/custom/jquery.ui.autocomplete.html.js"></script>
<script src="../../js/custom/gz-library.js"></script>
<script src="../../js/tinymce/tinymce.min.js"></script>
<script src="../../js/custom/tinymce.js"></script>
<script>
// setto comunque dei check intervallati dei minuti inseriti in configurazione avanzata azienda 15*60*1000ms perché non è detto che si facciano i refresh, ad es. se il browser rimane fermo sulla stessa pagina per un lungo periodo > $period
setInterval(menu_check_from_modules,<?php echo intval($period*60000);?>);</script>
</div><!-- chiude <div class="container-fluid gaz-body"> presente su header.php -->
</body>
</html>