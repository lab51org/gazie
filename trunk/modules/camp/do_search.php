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
// prevent direct access
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if (!$isAjax) {
    $user_error = 'Access denied - not an AJAX request...';
    trigger_error($user_error, E_USER_ERROR);
}

require("../../library/include/datlib.inc.php");
$admin_aziend = checkAdmin();

if(isset($_POST['codice'])){
	$results = array('error' => false, 'data' => '');
	$codice = $_POST['codice'];
	if(empty($codice)){
		$results['error'] = true;
	}else{				
		$query="SELECT PRODOTTO FROM ". $gTables['camp_fitofarmaci'] ." WHERE PRODOTTO LIKE '%$codice%' LIMIT 30";
		$result = gaz_dbi_query($query);
		if($result->num_rows > 0){
			while($ldata = $result->fetch_assoc()){
				$results['data'] .= "
					<li class='dropdown-item' data-fullname='".$ldata['PRODOTTO']."'> <a href='#'>".$ldata['PRODOTTO']."</a></li>
				";
			}
		} else {
			$results['data'] = "
				<li class='dropdown-item' style='display: none;'>No found data matches Records</li>
			";
		}
	}
	echo json_encode($results);
}
?>