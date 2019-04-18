<?php 
if (isset($_SESSION['table_prefix'])) {
    $table_prefix = substr($_SESSION['table_prefix'], 0, 12);
} elseif (isset($_GET['tp'])) {
    $table_prefix = filter_var(substr($_GET['tp'], 0, 12), FILTER_SANITIZE_MAGIC_QUOTES);
} else {
    $table_prefix = filter_var(substr($table_prefix, 0, 12), FILTER_SANITIZE_MAGIC_QUOTES);
}
$result = gaz_dbi_dyn_query("*", $table_prefix.'_aziend', 1);

while ($row = gaz_dbi_fetch_array($result)) {
	$aziend_codice = sprintf("%03s", $row["codice"]);
	// inizio controlli presenza di indici altrimenti li creo perché senza di essi le query ricorsive sarebbero troppo lente in caso di tabelle con molti righi
	$idx=array(	0=>array('datreg'=>'tesmov','datliq'=>'tesmov')); // indicizzo la colonna data di registrazione dei movimenti contabili per poter avere libro giornale e partitari velocemente
	foreach($idx as $vi){
		foreach($vi as $k=>$v){
			$rk=gaz_dbi_query("SHOW KEYS FROM ". $table_prefix . "_" . $aziend_codice.$v." WHERE 1");
			$ex=false;	
			while ($vk = gaz_dbi_fetch_array($rk)) {
				if ($vk['Column_name'] == $k){
					$ex=true;
				}
			}
			if (!$ex){
				gaz_dbi_query("ALTER TABLE ". $table_prefix . "_" . $aziend_codice.$v." ADD INDEX `".$k."` (`".$k."`)");		
				echo "<p>Ho creato l'index <b>".$k."</b> su ". $table_prefix . "_" . $aziend_codice.$v." perché non esisteva</p>";
			}
		}
	}
	// fine controlli - creazioni indici mancanti
}
?>
