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

if (isset($_POST['Recover'])) {
$mysqlDatabaseName =$Database;
$mysqlUserName =$User;
$mysqlPassword =$Password;
$mysqlHostName =$Host;

$zip = new ZipArchive;
$res = $zip->open('../../data/files/backups/'.$_GET["id"]);
if ($res === TRUE) {
  $zip->extractTo('../../data/files/backups/');
  $zip->close();
  $mysqlImportFilename =rtrim($_GET["id"],".gaz");
} else {
  echo 'Errore!';
  exit;
}

echo 'mysql -h' .$mysqlHostName .' -u' .$mysqlUserName .' -p' .$mysqlPassword .' ' .$mysqlDatabaseName .' < ' .$mysqlImportFilename;

//DONT EDIT BELOW THIS LINE
//Export the database and output the status to the page
gaz_dbi_query("DROP DATABASE ".$mysqlDatabaseName);

$command='mysql -h' .$mysqlHostName .' -u' .$mysqlUserName .' -p' .$mysqlPassword .' ' .$mysqlDatabaseName .' < ' .$mysqlImportFilename;
$output=array();
exec($command,$output,$worked);
switch($worked){
    case 0:
        echo 'Import file <b>' .$mysqlImportFilename .'</b> successfully imported to database <b>' .$mysqlDatabaseName .'</b>';
        unlink ("../../data/files/backups/".$mysqlImportFilename);
        break;
    case 1:
        echo 'There was an error during import.';
        break;
}
    
    header("Location: report_backup.php");
    exit;
} 

if (isset($_POST['Return'])){
    header("Location: report_backup.php");
    exit;
}

require("../../library/include/header.php");
$script_transl=HeadMain('','','report_backup');
print "<form method=\"POST\">\n";
print "<div align=\"center\" class=\"FacetFormHeaderFont\">".$script_transl['warning'].'!!! '.$script_transl['title']."</div>\n";
print "<table border=\"0\" cellpadding=\"3\" cellspacing=\"1\" class=\"FacetFormTABLE\" align=\"center\">\n";
print "<tr><td class=\"FacetFieldCaptionTD\">".$script_transl['sure']."</td><td class=\"FacetDataTD\">".$_GET["id"]."</td></tr>";

print "<td align=\"right\"><input type=\"submit\" name=\"Return\" value=\"".$script_transl['return']."\"></td><td align=\"right\"><input type=\"submit\" name=\"Recover\" value=\"".strtoupper($script_transl['recover'])."!\"></td></tr>";
?>
</table>
</form>
<?php
require("../../library/include/footer.php");
?>