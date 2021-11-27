<?php
/*
	  --------------------------------------------------------------------------
	  GAzie - Gestione Azienda
	  Copyright (C) 2004-2021 - Antonio De Vincentiis Montesilvano (PE)
	  (http://www.devincentiis.it)
	  <http://gazie.sourceforge.net>
	  --------------------------------------------------------------------------
	  REGISTRO DI CAMPAGNA è un modulo creato per GAzie da Antonio Germani, Massignano AP 
	  Copyright (C) 2018-2021 - Antonio Germani, Massignano (AP)
	  https://www.lacasettabio.it 
	  https://www.programmisitiweb.lacasettabio.it
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
	  # free to use, Author name and references must be left untouched  #
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

if(isset($_POST['codart2'])){
	$results = array('error' => false, 'data' => '');
	$codice = $_POST['codart2'];
	if(empty($codice)){
		$results['error'] = true;
	}else{				
		$query="SELECT codice, descri FROM ". $gTables['artico'] ." WHERE mostra_qdc=1 AND (codice LIKE '%$codice%' OR descri LIKE '%$codice%') LIMIT 30";
		$result = gaz_dbi_query($query);
		if($result->num_rows > 0){
			while($ldata = $result->fetch_assoc()){
				$results['data'] .= "
					<li class='dropdown-item3' data-fullname='".$ldata['codice']."'> <a href='#'>".$ldata['codice']."-".$ldata['descri']."</a></li>
				";
			}
		} else {
			$results['data'] = "
				<li class='dropdown-item3' style='background-color: #F6358A;'>Non esiste questo articolo per il Registro di campagna</li>
			";
		}
	}
	echo json_encode($results);
}
?>