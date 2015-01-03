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
$admin_aziend=checkAdmin();
require("../../library/include/document.php");
$testata= array('id_tes'=> 0,'seziva'=>0,'pagame'=>0,'banapp'=>0,'vettor'=>0,
                'listin'=>0,'spediz'=>'','portos'=>0,'imball'=>0,'traspo'=>0,
                'speban'=>0,'spevar'=>0,'ivaspe'=>0,'sconto'=>0,'id_agente'=>0,
                'initra'=>0,'geneff'=>0,'id_contract'=>0,'id_con'=>0,'status'=>'',
                'template'=>'Lettera');

$lettera = gaz_dbi_get_row($gTables['letter'], "id_let", $_GET['id_let']);
$testata['numdoc'] = $lettera['numero'];
$testata['numfat'] = $lettera['numero'];
$testata['protoc'] = $lettera['numero'];
$testata['datfat'] = $lettera['write_date'];
$testata['datemi'] = $lettera['write_date'];
$testata['tipdoc'] = $lettera['tipo'];
$testata['clfoco'] = $lettera['clfoco'];
if (empty($lettera['oggetto'])){
    $testata['destin'] = '';
} else {
    $testata['destin'] = array('Oggetto:',$lettera['oggetto']);
}

$testata['c_a'] = $lettera['c_a'];
$testata['corpo'] = $lettera['corpo'];
if ($lettera['signature'] > 0){
    $testata['signature'] = $admin_aziend['Nome'].' '.$admin_aziend['Cognome'];
} else {
    $testata['signature'] = '';
}
if (isset($_GET['dest'])&& $_GET['dest']=='E' ){ // se l'utente vuole inviare una mail
    createDocument($testata, 'Lettera', $gTables,'rigdoc','E');
} else {
	createDocument($testata,'Lettera',$gTables);
}
?>