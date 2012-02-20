<?php
/*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2012 - Antonio De Vincentiis Montesilvano (PE)
                                (www.devincentiis.it)
                        <http://gazie.it>
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
$mastroclienti = $admin_aziend['mascli']."000000";
$mastrofornitori = $admin_aziend['masfor']."000000";

$msg = "";

if (!isset($_POST['ritorno'])) {
        $_POST['ritorno'] = $_SERVER['HTTP_REFERER'];
}
$form = array();
if ((isset($_POST['Update'])) or (isset($_GET['Update']))) {
    if (!isset($_GET['id_tes'])) {
        header("Location: ".$_POST['ritorno']);
        exit;
    } else {
        $_POST['id_tes'] = $_GET['id_tes'];
    }
    $toDo = 'update';
} else {
    $toDo = 'insert';
}

// Se viene inviata la richiesta di conferma totale ...
if (isset($_POST['ins'])) {
    $ctrltotdar = 0.00;
    $ctrltotmov = 0.00;
    $ctrltotave = 0.00;
    $ctrlsaldo = 0.00;
    $ctrlmoviva = 0.00;
    //calcolo i totali dare e avere per poter eseguire il controllo
    for ($i = 0; $i < $_POST['rigcon']; $i++) {
        $_POST['importorc'][$i] = preg_replace("/\,/",'.',$_POST['importorc'][$i]);
        $nr = $i + 1;
        if (substr($_POST['conto_rc'.$i],3,6) < 1)  { //controllo che tutti i conti siano stati introdotti...
            $msg .= "0+";
        }
        if ($_POST['importorc'][$i]==0)  { //controllo che non ci siamo valori a 0
            $msg .= "1+";
        }
        if ($_POST['registroiva'] == 4 and (
           substr($_POST['conto_rc'.$i],0,3) == $admin_aziend['masban']
           or substr($_POST['conto_rc'.$i],0,3) == substr($admin_aziend['cassa_'],0,3)))  {
           $ctrlmoviva = number_format($_POST['importorc'][$i] + $ctrlmoviva,2, '.', '');
        } elseif (substr($_POST['conto_rc'.$i],0,3) == $admin_aziend['mascli']
           or substr($_POST['conto_rc'.$i],0,3) == $admin_aziend['masfor']) {
           $ctrlmoviva = number_format($_POST['importorc'][$i],2, '.', '');
        }
        if ($_POST['darave_rc'][$i] == "D") {
            $ctrltotdar += $_POST['importorc'][$i];
        } else {
            $ctrltotave += $_POST['importorc'][$i];
        }
        $ctrlsaldo = number_format($ctrltotdar - $ctrltotave,2, '.', '');
    }
    //calcolo i totali iva per poter eseguire il controllo
    if (!isset($_POST['rigiva'])) {
       $_POST['rigiva'] = 0;
    }
    for ($i = 0; $i < $_POST['rigiva']; $i++) {
        $_POST['imponi_ri'][$i] = preg_replace("/\,/",'.',$_POST['imponi_ri'][$i]);
        $_POST['impost_ri'][$i] = preg_replace("/\,/",'.',$_POST['impost_ri'][$i]);
        $ctrltotmov += $_POST['imponi_ri'][$i] + $_POST['impost_ri'][$i];
    }
    $ctrltotmov = number_format($ctrltotmov,2, '.', '');
    if ($ctrlsaldo != 0) {
        $msg .= "2+";
    }
    if ($ctrltotdar == 0) {
        $msg .= "3+";
    }
    if ($ctrltotave == 0) {
        $msg .= "4+";
    }
    if ($_POST['registroiva'] > 0 and $ctrltotmov == 0) {
        $msg .= "5+";
    }
    if ($_POST['registroiva'] > 0 and $ctrltotmov <> $ctrlmoviva) {
        $msg .= "6+";
    }
    if (empty($_POST['descrizion'])) {
        $msg .= "7+";
    }
    //controllo le date
    if (!checkdate( $_POST['date_reg_M'], $_POST['date_reg_D'], $_POST['date_reg_Y']))
        $msg .= "8+";
    //controllo che siano stati inseriti in maniera giusta i dati del documento
    if ($_POST['inserimdoc'] > 0 ) {
        if (!checkdate( $_POST['date_doc_M'], $_POST['date_doc_D'], $_POST['date_doc_Y'])) {
            $msg .= "9+";
        }
        if ($_POST['protocollo'] <= 0) {
            $msg .= "10+";
        }
        if (empty($_POST['numdocumen'])) {
            $msg .= "11+";
        }
        $ctrldatreg = mktime (0,0,0,$_POST['date_reg_M'], $_POST['date_reg_D'], $_POST['date_reg_Y']);
        $ctrldatdoc = mktime (0,0,0,$_POST['date_doc_M'], $_POST['date_doc_D'], $_POST['date_doc_Y']);
        if ($ctrldatreg < $ctrldatdoc) {
            $msg .= "12+";
        }
    }
    if ($msg == "") { // nessun errore
        //se è un update recupero i vecchi righi per trovare quelli da inserire/modificare/cancellare
        //formatto le date
        $datareg = $_POST['date_reg_Y']."-".$_POST['date_reg_M']."-".$_POST['date_reg_D'];
        $datadoc = $_POST['date_doc_Y']."-".$_POST['date_doc_M']."-".$_POST['date_doc_D'];
        if ($_POST['inserimdoc'] == 0 and $_POST['registroiva'] == 0) { //se non sono richisti i dati documenti e iva
              $_POST['sezioneiva'] = 0;
              $_POST['protocollo'] = 0;
              $_POST['numdocumen'] = "";
              $datadoc = 0;
        }
        if ( $toDo == 'update') {  //se è una modifica
           $vecchi_righcon = gaz_dbi_dyn_query("*", $gTables['rigmoc'], "id_tes = '".intval($_POST['id_testata'])."'","id_rig asc");
           $i=0;
           $count = count($_POST['id_rig_rc'])-1;
           while ($row_con = gaz_dbi_fetch_array($vecchi_righcon)) {
              //se l'id del vecchio rigo e' ancora presente nel nuovo lo modifico
              if ($i <= $count) {
                 gaz_dbi_table_update('rigmoc',array('id_rig',$row_con['id_rig']),array('id_tes'=>intval($_POST['id_testata']),'darave'=>substr($_POST['darave_rc'][$i],0,1),'codcon'=>intval($_POST['conto_rc'.$i]),'import'=>floatval($_POST['importorc'][$i])));
              } else { //altrimenti lo elimino
                 gaz_dbi_del_row($gTables['rigmoc'], "id_rig", $row_con['id_rig']);
              }
              $i++;
           }
           //qualora i nuovi righi fossero di più dei vecchi inserisco l'eccedenza
           for ($i = $i; $i <= $count; $i++) {
                rigmocInsert(array('id_tes'=>intval($_POST['id_testata']),'darave'=>substr($_POST['darave_rc'][$i],0,1),'codcon'=>intval($_POST['conto_rc'.$i]),'import'=>floatval($_POST['importorc'][$i])));
           }
           $vecchi_righiva = gaz_dbi_dyn_query("*", $gTables['rigmoi'], "id_tes = '".intval($_POST['id_testata'])."'","id_rig asc");
           $i=0;
           if ($_POST['registroiva'] > 0) {
              $count = count($_POST['id_rig_ri'])-1;
           } else {
              $count = 0;
              $i = 1;
           }
           while ($row_iva = gaz_dbi_fetch_array($vecchi_righiva)) {
              //se l'id del vecchio rigo e' ancora presente nel nuovo lo modifico
              if ($i <= $count) {
                //recupero i dati dell'aliquota iva
                $vv = gaz_dbi_get_row($gTables['aliiva'],'codice',intval($_POST['codiva_ri'][$i]));
                //aggiungo i valori mancanti all'array
                $vv['codiva']=$vv['codice'];
                $vv['id_tes']=intval($_POST['id_testata']);
                $vv['periva']=$vv['aliquo'];
                $vv['imponi']=floatval($_POST['imponi_ri'][$i]);
                $vv['impost']=floatval($_POST['impost_ri'][$i]);
                gaz_dbi_table_update('rigmoi',array('id_rig',$row_iva['id_rig']),$vv);
              } else { //altrimenti lo elimino
                gaz_dbi_del_row($gTables['rigmoi'], "id_rig", $row_iva['id_rig']);
              }
              $i++;
           }
           //qualora i nuovi righi iva fossero di più dei vecchi inserisco l'eccedenza
           for ($i = $i; $i <= $count; $i++) {
                $vv = gaz_dbi_get_row($gTables['aliiva'],'codice',intval($_POST['codiva_ri'][$i]));
                //aggiungo i valori mancanti all'array
                $vv['codiva']=$vv['codice'];
                $vv['id_tes']=intval($_POST['id_testata']);
                $vv['periva']=$vv['aliquo'];
                $vv['imponi']=floatval($_POST['imponi_ri'][$i]);
                $vv['impost']=floatval($_POST['impost_ri'][$i]);
                rigmoiInsert($vv);
           }
           //modifico la testata
           $codice=array('id_tes',intval($_POST['id_testata']));
           $newValue=array('caucon'=>substr($_POST['codcausale'],0,3),
                           'descri'=>substr($_POST['descrizion'],0,50),
                           'datreg'=>$datareg,
                           'seziva'=>intval($_POST['sezioneiva']),
                           'protoc'=>intval($_POST['protocollo']),
                           'numdoc'=>substr($_POST['numdocumen'],0,20),
                           'datdoc'=>$datadoc,
                           'clfoco'=>intval($_POST['cod_partner']),
                           'regiva'=>substr($_POST['registroiva'],0,1),
                           'operat'=>intval($_POST['operatore'])
                           );
           tesmovUpdate($codice,$newValue);
        } else { //se è un'inserimento
           //inserisco la testata
           $newValue=array('caucon'=>substr($_POST['codcausale'],0,3),
                           'descri'=>substr($_POST['descrizion'],0,50),
                           'datreg'=>$datareg,
                           'seziva'=>intval($_POST['sezioneiva']),
                           'protoc'=>intval($_POST['protocollo']),
                           'numdoc'=>substr($_POST['numdocumen'],0,20),
                           'datdoc'=>$datadoc,
                           'clfoco'=>intval($_POST['cod_partner']),
                           'regiva'=>substr($_POST['registroiva'],0,1),
                           'operat'=>intval($_POST['operatore'])
                           );
           tesmovInsert($newValue);
           //recupero l'id assegnato dall'inserimento
           $ultimo_id = gaz_dbi_last_id();
           //inserisco i righi iva
           for ($i = 0; $i < $_POST['rigiva']; $i++) {
                $vv = gaz_dbi_get_row($gTables['aliiva'],'codice',intval($_POST['codiva_ri'][$i]));
                //aggiungo i valori mancanti all'array
                $vv['codiva']=$vv['codice'];
                $vv['id_tes']=$ultimo_id;
                $vv['periva']=$vv['aliquo'];
                $vv['imponi']=floatval($_POST['imponi_ri'][$i]);
                $vv['impost']=floatval($_POST['impost_ri'][$i]);
                rigmoiInsert($vv);
           }
           //inserisco i righi contabili
           for ($i = 0; $i < $_POST['rigcon']; $i++) {
                rigmocInsert(array('id_tes'=>$ultimo_id,'darave'=>substr($_POST['darave_rc'][$i],0,1),'codcon'=>intval($_POST['conto_rc'.$i]),'import'=>floatval($_POST['importorc'][$i])));
           }
        }
        header("Location:report_movcon.php");
        exit;
    }
}
if ((!isset($_POST['Update'])) and (isset($_GET['Update']))) { //se e' il primo accesso per UPDATE
    $form['hidden_req'] = '';
    //recupero la testata con la causale
    $rs_testata = gaz_dbi_dyn_query("*", $gTables['tesmov'], "id_tes = '".intval($_GET['id_tes'])."'","id_tes asc",0,1);
    $testata = gaz_dbi_fetch_array($rs_testata);
    $form['id_testata'] = $testata['id_tes'];
    $form['codcausale'] = $testata['caucon'];
    $form['descrizion'] = $testata['descri'];
    $form['registroiva'] = $testata['regiva'];
    $form['operatore'] = $testata['operat'];
    $form['date_reg_D'] = substr($testata['datreg'],8,2);
    $form['date_reg_M'] = substr($testata['datreg'],5,2);
    $form['date_reg_Y'] = substr($testata['datreg'],0,4);
    $form['sezioneiva'] = $testata['seziva'];
    $form['protocollo'] = $testata['protoc'];
    $form['numdocumen'] = $testata['numdoc'];
    $form['date_doc_D'] = substr($testata['datdoc'],8,2);
    $form['date_doc_M'] = substr($testata['datdoc'],5,2);
    $form['date_doc_Y'] = substr($testata['datdoc'],0,4);
    $form['cod_partner'] = $testata['clfoco'];
    if ($form['numdocumen'] > 0 or !empty($form['numdocumen'])) {
       $form['inserimdoc'] = '1';
    } else {
       $form['inserimdoc'] = '0';
    }
    $form['registroiva'] = $testata['regiva'];
    $form['operatore'] = $testata['operat'];
    $form['insert_mastro'] = '000000000';
    $form['insert_conto'] = '000000000';
    $form['search']['insert_conto']='';
    $form['paymov']=array();
    $form['insert_darave'] = 'A';
    $form['insert_conto'] = '000000000';
    $form['insert_codiva'] = $admin_aziend['alliva'];
    $form['insert_imponi'] = 0;
    //recupero i righi iva
    $rs_righiva = gaz_dbi_dyn_query("*", $gTables['rigmoi'], "id_tes = '".intval($form['id_testata'])."'","id_rig asc");
    $i=0;
    while ($row = gaz_dbi_fetch_array($rs_righiva)) {
        $msg = "13+";
        $form['insert_codiva'] = $row['codiva'];;
        $form['id_rig_ri'][$i] = $row['id_rig'];
        $form['codiva_ri'][$i] = $row['codiva'];
        $form['imponi_ri'][$i] = $row['imponi'];
        $form['impost_ri'][$i] = $row['impost'];
        $i++;
        $_POST['rigiva'] = $i;
    }
    //recupero i righi contabili
    $rs_righcon = gaz_dbi_dyn_query("*", $gTables['rigmoc'], "id_tes = '".intval($form['id_testata'])."'","id_rig asc");
    $i=0;
    while ($row = gaz_dbi_fetch_array($rs_righcon)) {
        $form['id_rig_rc'][$i] = $row['id_rig'];
        $form['mastro_rc'][$i] = substr($row['codcon'],0,3).'000000';
        $form['conto_rc'.$i] = $row['codcon'];
        $form['search']['conto_rc'.$i]='';
        // creo l'array contenente i partner per la gestione delle partite aperte
        if (($form['mastro_rc'][$i] == $mastroclienti || $form['mastro_rc'][$i] == $mastrofornitori)
            && $form['conto_rc'.$i] > 0 ) {  
            $form['paymov'][$i] = array('codcon'=>$row['codcon']);
            $rs_paymov = gaz_dbi_dyn_query("*", $gTables['paymov'], "id_rigmoc_pay = ".$row['id_rig']." OR id_rigmoc_doc = ".$row['id_rig'],"id");
            while ($r_pm = gaz_dbi_fetch_array($rs_paymov)) {
                $form['paymov'][$i] = array_merge($r_pm,$row);
   
            }

        }
        $form['darave_rc'][$i] = $row['darave'];
        $form['importorc'][$i] = $row['import'];
        $i++;
        $_POST['rigcon'] = $i;
    }

} elseif ((isset($_POST['Insert'])) or (isset($_POST['Update']))) {   //se non e' il primo accesso
    $form['hidden_req']=htmlentities($_POST['hidden_req']);
    //ricarico i registri per il form della testata
    $form['id_testata'] = $_POST['id_testata'];
    $form['codcausale'] = $_POST['codcausale'];
    $form['descrizion'] = $_POST['descrizion'];

    $form['date_reg_D']=intval($_POST['date_reg_D']);
    $form['date_reg_M']=intval($_POST['date_reg_M']);
    $form['date_reg_Y']=intval($_POST['date_reg_Y']);

    $form['inserimdoc'] = $_POST['inserimdoc'];
    $form['registroiva'] = $_POST['registroiva'];
    $form['operatore'] = $_POST['operatore'];
    if ($form['registroiva'] > 0) {
       $form['inserimdoc'] = 1;
    }
    $form['sezioneiva'] = $_POST['sezioneiva'];
    $form['protocollo'] = $_POST['protocollo'];
    $form['numdocumen'] = $_POST['numdocumen'];

    $form['date_doc_D']=intval($_POST['date_doc_D']);
    $form['date_doc_M']=intval($_POST['date_doc_M']);
    $form['date_doc_Y']=intval($_POST['date_doc_Y']);

    $form['cod_partner'] = $_POST['cod_partner'];
    //ricarico i registri per il form del rigo di inserimento contabile
    $form['insert_mastro'] = $_POST['insert_mastro'];
    $form['insert_conto'] = $_POST['insert_conto'];
    foreach($_POST['search'] as $k=>$v){
       $form['search'][$k]=$v;
    }
    $form['insert_darave'] = $_POST['insert_darave'];
    //ricarico i registri per il form del rigo di inserimento iva
    if (!isset($_POST['rigiva'])) {  //se non c'erano righi in precedenza
        $_POST['rigiva'] = 0;
        $form['insert_codiva'] = $admin_aziend['alliva'];
        $form['insert_imponi'] = 0;
    } else {
        $form['insert_codiva'] = $_POST['insert_codiva'];
        $form['insert_imponi'] = $_POST['insert_imponi'];
    }
    $form['paymov']=array();
    //ricarico i registri per il form dei righi contabili già  immessi
    $loadCosRic = 0;
    for( $i = 0; $i < $_POST['rigcon']; $i++ ) {
        $form['id_rig_rc'][$i] = $_POST['id_rig_rc'][$i];
        $form['mastro_rc'][$i] = $_POST['mastro_rc'][$i];
        $form['conto_rc'.$i] = $_POST['conto_rc'.$i];
        $form['darave_rc'][$i] = $_POST['darave_rc'][$i];
        $form['importorc'][$i] = $_POST['importorc'][$i];
        if (($_POST['mastro_rc'][$i] == $mastroclienti || $_POST['mastro_rc'][$i] == $mastrofornitori) && $_POST['conto_rc'.$i] > 0) {
           //se viene inserito un nuovo partner do l'ok alla ricarica della contropartita costi/ricavi in base al conto presente sull'archivio clfoco
           if  ($_POST['cod_partner'] == 0 and $form['conto_rc'.$i] > 0) {
               $anagrafica = new Anagrafica();
               $partner = $anagrafica->getPartner($form['conto_rc'.$i]);
               $loadCosRic = substr($form['conto_rc'.$i],0,1);
           }
           $form['cod_partner'] = $_POST['conto_rc'.$i];
           //ricarico i registri per il form delle partite aperte dei clienti/fornitori
           foreach($_POST['paymov'][$i] as $k=>$v) {
                $form['paymov'][$i][$k] = $v;  // devo fare il parsing
           }
           
        }
        if ($loadCosRic == 1 && substr($form['conto_rc'.$i],0,1) == 4
            && $partner['cosric'] > 0 && $form['registroiva'] > 0 ){  //e' un  cliente agisce sui ricavi
                $form['mastro_rc'][$i] = substr($partner['cosric'],0,3)."000000";
                $form['conto_rc'.$i] = $partner['cosric'];
                $loadCosRic = 0;
        } elseif ($loadCosRic == 2 && substr($form['conto_rc'.$i],0,1) == 3
            && $partner['cosric'] > 0 && $form['registroiva'] > 0 ){ //è un fornitore  agisce sui costi
                $form['mastro_rc'][$i] = substr($partner['cosric'],0,3)."000000";
                $form['conto_rc'.$i] = $partner['cosric'];
                $loadCosRic = 0;
        }
    }
    //ricarico i registri per il form dei righi iva già  immessi
    for( $i = 0; $i < $_POST['rigiva']; $i++ ) {
        $form['id_rig_ri'][$i] = $_POST['id_rig_ri'][$i];
        $form['codiva_ri'][$i] = $_POST['codiva_ri'][$i];
        $form['imponi_ri'][$i] = $_POST['imponi_ri'][$i];
        $form['impost_ri'][$i] = $_POST['impost_ri'][$i];
    }


    // Se viene inviata la richiesta di conferma della causale la carico con le relative contropartite...
    if (isset($_POST['inscau_x'])) {
       // Se la descrizione è vuota e la causale è stata selezionata
       if (!empty($form['codcausale']) and empty($form['descrizion'])) {

            function getLastNumber($type,$year,$sezione,$registro=6)  // questa funzione trova l'ultimo numero di protocollo
            {                                                           // controllando sia l'archivio documenti che sul
                  global $gTables;                                      // registro IVA passato come variabile (default acquisti)
                  $rs_ultimo_tesdoc = gaz_dbi_dyn_query("*", $gTables['tesdoc'],"YEAR(datemi) = $year AND tipdoc LIKE '$type' AND seziva = $sezione","protoc DESC",0,1);
                  $ultimo_tesdoc = gaz_dbi_fetch_array($rs_ultimo_tesdoc);
                  $rs_ultimo_tesmov = gaz_dbi_dyn_query("*", $gTables['tesmov'],"YEAR(datreg) = $year AND regiva = $registro AND seziva = $sezione","protoc DESC",0,1);
                  $ultimo_tesmov = gaz_dbi_fetch_array($rs_ultimo_tesmov);
                  $lastProtocol=0;
                  if ($ultimo_tesdoc) {
                     $lastProtocol = $ultimo_tesdoc['protoc'];
                  }
                  if ($ultimo_tesmov) {
                     if ($ultimo_tesmov['protoc'] > $lastProtocol){
                        $lastProtocol = $ultimo_tesmov['protoc'];
                     }
                  }
                  return $lastProtocol+1;
            }

            $causa = gaz_dbi_get_row($gTables['caucon'],'codice',$form['codcausale']);
            if ($causa['regiva'] > 0) { // trovo l'ultimo numero di protocollo e di documento
               $form['protocollo'] = getLastNumber(substr($form['codcausale'],0,1).'__',$form['date_reg_Y'],$form['sezioneiva'],$causa['regiva']);
               if ($causa['regiva'] <= 5) { // il numero di documento solo se è di vendita
                  $form['numdocumen'] = getLastNumber($form['codcausale'],$form['date_reg_Y'],$form['sezioneiva'],$causa['regiva']);
               }
            }
            if ($causa['regiva'] == 0 and $_POST['registroiva'] > 0) {//se la nuova causale non prevede righi IVA mentre la precedente lo prevedeva, elimino i righi
               for($i=$_POST['rigiva']-1; $i >= 0; $i-- ) { //qui cancello tutti i movimenti IVA
                   array_splice($form['id_rig_ri'],$i,1);
                   array_splice($form['codiva_ri'],$i,1);
                   array_splice($form['imponi_ri'],$i,1);
                   array_splice($form['impost_ri'],$i,1);
               }
            } elseif ($causa['regiva'] > 0 and $_POST['registroiva'] > 0) { //se la nuova causale prevede righi IVA come la precedente li riuso per caricarci le nuove

                     //calcolo il totale dell'imponibile e dell'iva postati
                     $imponi = 0;
                     $impost = 0;
                     for ($i = 0; $i < $_POST['rigiva']; $i++ ) {
                         $imponi += $form['imponi_ri'][$i];
                         $impost += $form['impost_ri'][$i];
                     }
                     $newRow = 0;
                     for( $i = 1; $i <= 6; $i++ ) { //se ce ne sono, carico le contropartite
                          switch ($causa["tipim$i"]) {
                                 case "A": //totale
                                 $nuovo_importo[$newRow] = $imponi+$impost;
                                 break;
                                 case "B": //imponibile
                                 $nuovo_importo[$newRow] = $imponi;
                                 break;
                                 case "C": //iva
                                 $nuovo_importo[$newRow] = $impost;
                                 break;
                          }
                          $newRow++;
                     }
            }
            $form['descrizion'] = $causa['descri'];
            $form['inserimdoc'] = $causa['insdoc'];
            $form['registroiva'] = $causa['regiva'];
            $form['operatore'] = $causa['operat'];
            $newRow = 0;
            for( $i = 1; $i <= 6; $i++ ) { //se ce ne sono, carico le contropartite
                 if ($causa["contr$i"] > 0) {
                    if (!isset($form['id_rig_rc'][$newRow])){ //se e' un rigo inesistente
                       $form['id_rig_rc'][$newRow] = 'NUOVO';
                    }
                    if (substr($causa["contr$i"],3,6) == 0){
                       if  (substr($causa["contr$i"],0,3) == substr($form['cod_partner'],0,3)) {
                           $form['conto_rc'.$newRow] = $form['cod_partner'];;
                       } else {
                           $form['conto_rc'.$newRow] = 0;
                       }
                    } else {
                       $form['conto_rc'.$newRow] = $causa["contr$i"];
                    }
                    $form['mastro_rc'][$newRow] = substr($causa["contr$i"],0,3)."000000";
                    $form['search']['conto_rc'.$newRow]='';
                    $form['darave_rc'][$newRow] = $causa["daav_$i"];
                    if (isset($nuovo_importo[$newRow])) {
                       $form['importorc'][$newRow] = $nuovo_importo[$newRow];
                    } else {
                       $form['importorc'][$newRow] = 0;
                    }
                    $newRow++;
                 }
            }
            //qui cancello tutti gli eventuali successivi movimenti contabili
            for($i=$_POST['rigcon']-1; $i >= $newRow; $i-- ) { //se ce ne sono, carico le contropartite
                 array_splice($form['id_rig_rc'],$i,1);
                 array_splice($form['mastro_rc'],$i,1);
                 unset($form['conto_rc'.$i]);
                 array_splice($form['darave_rc'],$i,1);
                 array_splice($form['importorc'],$i,1);
            }
            $_POST['rigcon'] = $newRow;
      }
   }
   // Se viene inviata la richiesta di aggiunta, aggiunge un rigo contabile
   if (isset($_POST['add_x'])) {
      $rigo = $_POST['rigcon'];
      $form['id_rig_rc'][$rigo] = "";
      $form['mastro_rc'][$rigo] = intval($_POST['insert_mastro']);
      $form['conto_rc'.$rigo] = intval($_POST['insert_conto']);
      $form['search']['conto_rc'.$rigo]='';
      $form['darave_rc'][$rigo] = $_POST['insert_darave'];
      $form['importorc'][$rigo] = preg_replace("/\,/",'.',$_POST['insert_import']);;
      // e accodo anche all'array contenente i paymov per la gestione delle partite aperte
      if (($form['mastro_rc'][$rigo] == $mastroclienti || $form['mastro_rc'][$rigo] == $mastrofornitori)
          && $form['conto_rc'.$rigo] > 0 ) {  
            $form['paymov'][$rigo] = array('codcon'=>$form['conto_rc'.$rigo]);
      }
      $_POST['rigcon']++;
   }

   // Se viene inviata la richiesta di eliminazione, elimina il rigo contabile
   if (isset($_POST['del'])) {
      $delri= key($_POST['del']);
      array_splice($form['id_rig_rc'],$delri,1);
      array_splice($form['mastro_rc'],$delri,1);
      for ($i=$delri; $i<$_POST['rigcon']-1; $i++ ) {
           $form['conto_rc'.$i]=$form['conto_rc'.($i+1)];
      }
      unset($form['conto_rc'.($i+1)]);
      array_splice($form['darave_rc'],$delri,1);
      array_splice($form['importorc'],$delri,1);
      $_POST['rigcon']--;
   }

   // Se viene inviata la richiesta di aggiunta, aggiunge un rigo iva
   if (isset($_POST['adi_x']) && $_POST['insert_imponi'] <> 0) {
      if ($_POST['insert_codiva'] > 0) {
         $causa = gaz_dbi_get_row($gTables['caucon'],"codice",$form['codcausale']);
         $riiv = $_POST['rigiva'];
         $form['id_rig_ri'][$riiv] = "";
         $form['codiva_ri'][$riiv] = $_POST['insert_codiva'];
         $ivarigo = gaz_dbi_get_row($gTables['aliiva'],"codice",$_POST['insert_codiva']);
         if ($form['registroiva'] == 4) { //se è un corrispettivo faccio lo scorporo
            $form['imponi_ri'][$riiv] = number_format(round(preg_replace("/\,/",'.',$_POST['insert_imponi']) /(100 + $ivarigo['aliquo']) * 10000)/100 ,2, '.', '');
            $form['impost_ri'][$riiv] = number_format(preg_replace("/\,/",'.',$_POST['insert_imponi']) - $form['imponi_ri'][$riiv],2, '.', '');
         } else { //altrimenti calcolo solo l'iva
            $form['imponi_ri'][$riiv] = number_format(preg_replace("/\,/",'.',$_POST['insert_imponi']),2, '.', '');
            $form['impost_ri'][$riiv] = number_format(round($form['imponi_ri'][$riiv] * $ivarigo['aliquo']) / 100,2, '.', '');
         }
         //ricalcolo il totale dell'imponibile e dell'iva postati
         $imponi = 0;
         $impost = 0;
         for ($i = 0; $i <= $_POST['rigiva']; $i++ ) {
             $imponi += $form['imponi_ri'][$i];
             $impost += $form['impost_ri'][$i];
         } //fine calcolo
         for ($rc=0; $rc < $_POST['rigcon']; $rc++ ) { //mi ripasso le contropartite inserite e ci indroduco l'eventuale giusto valore
             for( $i = 1; $i <= 6; $i++ ) {
                  if ($causa["contr$i"] == $form['conto_rc'.$rc] or (substr($causa["contr$i"],3,6) == 0 and substr($form['mastro_rc'][$rc],0,3) == substr($causa["contr$i"],0,3))) {
                     switch ($causa["tipim$i"]) {
                           case "A": //totale
                           $form['importorc'][$rc] = $imponi+$impost;
                           break;
                           case "B": //imponibile
                           $form['importorc'][$rc] = $imponi;
                           break;
                           case "C": //iva
                           $form['importorc'][$rc] = $impost;
                           break;
                     }
                  }
             }
         }
         $_POST['rigiva']++;
      }
   }
   // Se viene inviata la richiesta di eliminazione, elimina il rigo iva
   if (isset($_POST['dei'])) {
      $delri= key($_POST['dei']);
      array_splice($form['codiva_ri'],$delri,1);
      array_splice($form['imponi_ri'],$delri,1);
      array_splice($form['impost_ri'],$delri,1);
      $_POST['rigiva']--;
   }

   /* Se viene inviata la richiesta di bilanciamento dei righi contabili
      aggiungo il valore pasato al primo rigo. E' un pò rudimentale,
      si potrebbe fare meglio e molto più intelligente, ma non ho tempo...
   */
   if (isset($_POST['balb'])) {
        $bb=floatval($_POST['diffV']);
        if ($bb > 0 ) { //eccesso in dare
           $key=array_search('A',$form['darave_rc']);
           if ($key || $key === 0) {
              $form['importorc'][$key] += $bb;
           }
        } else {        //eccesso in avere
           $key=array_search('D',$form['darave_rc']);
           if ($key || $key === 0) {
              $form['importorc'][$key] -= $bb;
           }
        }
   }


} elseif (!isset($_POST['Insert'])) { //se e' il primo accesso per INSERT
    $form['hidden_req'] = '';
    //registri per il form della testata
    $form['id_testata'] = "";
    $form['codcausale'] = "";
    $form['descrizion'] = "";
    // ricerco l'ultimo inserimento per ricavarne la data
    $rs_last = gaz_dbi_dyn_query('datreg', $gTables['tesmov'],1,"id_tes DESC",0,1);
    $last = gaz_dbi_fetch_array($rs_last);
    if ($last) {
       $form['date_reg_D'] = substr($last['datreg'],8,2);
       $form['date_reg_M'] = substr($last['datreg'],5,2);
       $form['date_reg_Y'] = substr($last['datreg'],0,4);
    } else {
       $form['date_reg_D'] = date("d");
       $form['date_reg_M'] = date("m");
       $form['date_reg_Y'] = date("Y");
    }
    $form['sezioneiva'] = 1;
    $form['protocollo'] = "";
    $form['numdocumen'] = "";
    $form['date_doc_D'] = date("d");
    $form['date_doc_M'] = date("m");
    $form['date_doc_Y'] = date("Y");
    $form['inserimdoc'] = 0;
    $form['registroiva'] = 0;
    $form['operatore'] = 0;
    //registri per il form del rigo di inserimento contabile
    $form['insert_mastro'] = 0;
    $form['insert_conto'] = 0;
    $form['search']['insert_conto']='';
    $form['paymov']=array();
    $form['insert_darave'] = "A";
    //registri per il form del rigo di inserimento iva
    $form['insert_imponi'] = 0;
    $form['insert_codiva'] = $admin_aziend['alliva'];
    $form['insert_imponi'] = 0;
    //registri per il form dei righi contabili
    $_POST['rigcon'] = 0;
    $form['id_rig_rc'] = array();
    $form['mastro_rc'] = array();
    $form['darave_rc'] = array();
    $form['importorc'] = array();
    $form['cod_partner'] = 0;
    //registri per il form dei righi iva
    $_POST['rigiva'] = 0;
    $form['id_rig_ri'] = array();
    $form['codiva_ri'] = array();
    $form['imponi_ri'] = array();
    $form['impost_ri'] = array();
}
$anagrafica = new Anagrafica();

require("../../library/include/header.php");
$script_transl=HeadMain(0,array('calendarpopup/CalendarPopup',
                                  'jquery/jquery-1.7.1.min',
                                  'jquery/ui/jquery.ui.core',
                                  'jquery/ui/jquery.ui.widget',
                                  'jquery/ui/jquery.ui.mouse',
                                  'jquery/ui/jquery.ui.button',
                                  'jquery/ui/jquery.ui.autocomplete',
                                  'jquery/ui/jquery.ui.dialog',
                                  'jquery/ui/jquery.ui.position',
                                  'jquery/ui/jquery.ui.draggable',
                                  'jquery/ui/jquery.ui.resizable',
                                  'jquery/ui/jquery.effects.core',
                                  'jquery/ui/jquery.effects.scale',
                                  'jquery/modal_form.js'));
echo '<SCRIPT type="text/javascript">
      $(function() {
           $( "#search_insert_conto" ).autocomplete({
           source: "../../modules/root/search.php",
           minLength: 2,
           });';
for ($i=0; $i<$_POST['rigcon']; $i++ ) {
  echo '   $( "#search_conto_rc'.$i.'" ).autocomplete({
           source: "../../modules/root/search.php",
           minLength: 2,
           });
        ';
}
foreach($form['paymov'] as $k=>$v) {
  echo '   $( "#dialog'.$k.'").dialog({
              autoOpen: false
           });
        ';
}
echo '});
      function dialogSchedule(paymov){
        clfoco = paymov.id.substring(6,15);
        nrow = paymov.id.substring(23);
        impo= document.getElementById("impoRC"+nrow).value.toString();
        getResults(clfoco);
        $.fx.speeds._default = 500;
        var expiry = $( "#expiry" ),
            amount = $( "#amount" ),
            remrow = $( "#remrow" ),
            allFields = $( [] ).add( expiry ).add( amount ).add( remrow ),
            tips = $( ".validateTips" );

        function getResults(term_val) {
           $.get("expiry.php",
                 {clfoco:term_val},
                 function(data) {
                   var items=[];       
                   $.each(data, function(i,value){
                         $( "#openitem"+ nrow + " tbody" ).append( "<tr>" +
                              "<td>" + value.expiry + "</td>" +
                               "<td>" + value.amount + "</td>" +
                               '."'<td><button><img src=\"../../library/images/x.gif\" /></button></td>'".' +
                               "</tr>" );
                   });
                 },"json"
                 );
        }

        function updateTips( t ) {
           tips
           .text( t )
           .addClass( "ui-state-highlight" );
           setTimeout(function() {
                tips.removeClass( "ui-state-highlight", 1500 );
           }, 500 );
        }

        function checkLength( o, n, min, max ) {
            if ( o.val().length > max || o.val().length < min ) {
                o.addClass( "ui-state-error" );
                updateTips( "Length of " + n + " must be between " +
                min + " and " + max + "." );
                return false;
           } else {
                return true;
           }
        }

        function checkRegexp( o, regexp, n ) {
           if ( !( regexp.test( o.val() ) ) ) {
                o.addClass( "ui-state-error" );
                updateTips( n );
                return false;
           } else {
                return true;
           }
        }

        $( "#dialog"+nrow ).dialog({
          autoOpen: false,
          show: "scale",
          width: 360,
          modal: true,
          buttons: {
            "Chiudi":function(){ $(this).dialog( "close" );}
          },
          close: function() {
            allFields.val( "" ).removeClass( "ui-state-error" );
          }
        });
        $("#dialog"+nrow ).dialog( "open" );
        $( "#rerun" ).click(function() {
                var bValid = true;
                allFields.removeClass( "ui-state-error" );
                bValid = bValid && checkLength( expiry, "userexpiry", 3, 16 );
                bValid = bValid && checkLength( amount, "amount", 6, 80 );
                bValid = bValid && checkRegexp( expiry, /^[a-z]([0-9a-z_])+$/i, "Userexpiry may consist of a-z, 0-9, underscores, begin with a letter." );
                bValid = bValid && checkRegexp( amount, /^[a-z]([0-9a-z_])+$/i, "Userexpiry may consist of a-z, 0-9, underscores, begin with a letter." );
                if ( bValid ) {
                    $( "#openitem"+ nrow + " tbody" ).append( "<tr>" +
                       "<td>" + expiry.val() + "</td>" +
                       "<td>" + amount.val() + "</td>" +
                       '."'<td><button><img src=\"../../library/images/x.gif\" /></button></td>'".' +
                       "</tr>" );
                       updateTips( "" );
                }
        });
    }
</SCRIPT>';
echo "<SCRIPT type=\"text/javascript\">\n";




echo "var cal = new CalendarPopup();
      var calName = '';
      function setMultipleValues(y,m,d) {
         document.getElementById(calName+'_Y').value=y;
         document.getElementById(calName+'_M').selectedIndex=m*1-1;
         document.getElementById(calName+'_D').selectedIndex=d*1-1;
      }
      function setDate(name) {
         calName = name.toString();
         var year = document.getElementById(calName+'_Y').value.toString();
         var month = document.getElementById(calName+'_M').value.toString();
         var day = document.getElementById(calName+'_D').value.toString();
         var mdy = month+'/'+day+'/'+year;
         cal.setReturnFunction('setMultipleValues');
         cal.showCalendar('anchor', mdy);
      }\n";

echo "function toggleContent(currentContent) {
        var thisContent = document.getElementById(currentContent);
        if ( thisContent.style.display == 'none') {
           thisContent.style.display = '';
           return;
        }
        thisContent.style.display = 'none';
      }

      function selectValue(currentValue,currentContent) {
         if (currentValue < 0) {
            currentValue = -currentValue;
            document.getElementById(currentContent+'_darave').options[0].selected=true;
         } else {
            document.getElementById(currentContent+'_darave').options[1].selected=true;
         }
         document.getElementById(currentContent+'_import').value=currentValue;
         toggleContent(currentContent);
      }\n";

echo "function balance(row)
      {
      var rw = Number([row]);
      var sumD = 0;
      var sumA = 0;
      for (i=0; i<".$_POST['rigcon']."; i++) {
        if (i == rw) {
           var rva = document.getElementById('impoRC'+i).value*1;
           var rda = document.getElementById('daavRC'+i).value;
        }
        var elva = document.getElementById('impoRC'+i).value*1;
        var elda = document.getElementById('daavRC'+i).value;
        if (elda == 'D') {
           sumD += elva;
        } else {
           sumA += elva;
        }
        document.getElementById('balbRC'+i).value='\u21D4';
        document.getElementById('balbRC'+i).disabled=true;
      }
      var difSUM = sumD - sumA;
      if (((rda == 'D' && difSUM > 0) || (rda == 'A' && difSUM < 0 )) && Math.abs(difSUM) < rva ) {
          var nv = Math.abs(Math.abs(difSUM) - rva);
          var ntot = Math.min(sumD,sumA);
      } else if ((rda == 'D' && difSUM < 0) || (rda == 'A' && difSUM > 0)) {
          var nv = Math.abs(difSUM) + rva;
          var ntot = Math.max(sumD,sumA);
      } else {
          var nv = rva;
          var ntot = sumA;
      }
      document.getElementById('impoRC'+rw).value = (Math.round(nv*100)/100).toFixed(2);
      document.getElementById('impoRC'+rw).style.backgroundColor='transparent';
      document.myform.tot_A.value=(Math.round(ntot*100)/100).toFixed(2);
      document.myform.tot_D.value=(Math.round(ntot*100)/100).toFixed(2);
      document.myform.tot_A.disabled=true;
      document.myform.tot_D.disabled=true;
      document.myform.tot_A.style.backgroundColor='#BBBBBB';
      document.myform.tot_D.style.backgroundColor='#BBBBBB';
      document.myform.ins.disabled=false;
      document.myform.diffV.value='".$script_transl['bal']."';
      }\n";

echo "function tot_bal(da)
      {
      var d_a = [da];
      var ovD = document.getElementById('tot_D').value*1;
      var ovA = document.getElementById('tot_A').value*1;
      var ref = document.getElementById('tot_'+d_a).value*1
      var difSUM = ovD - ovA;
      if ((d_a == 'D' && difSUM > 0) || (d_a == 'A' && difSUM < 0)){
         var oper = 1;
      } else {
         var oper = -1;
      }
      var accu = Math.abs(difSUM);
      for (i=0; i<".$_POST['rigcon']."; i++) {
        var elva = document.getElementById('impoRC'+i).value*1;
        var elda = document.getElementById('daavRC'+i).value;
        if (elda != d_a && accu > 0) {
           if (oper == 1) {
               document.getElementById('impoRC'+i).value=(Math.round((elva + accu)*100)/100).toFixed(2);;
               accu = 0;
           } else if (accu < elva && oper == -1) {
               document.getElementById('impoRC'+i).value=(Math.round((elva - accu)*100)/100).toFixed(2);
               accu = 0;
           } else if (accu > elva && oper == -1) {
               accu -= elva;
               document.getElementById('impoRC'+i).value=0;
               document.getElementById('impoRC'+i).style.backgroundColor='#FFAAAA';
           }
        }
        document.getElementById('balbRC'+i).value='\u21D4';
        document.getElementById('balbRC'+i).disabled=true;
      }
      document.myform.tot_A.value=(Math.round(ref*100)/100).toFixed(2);
      document.myform.tot_D.value=(Math.round(ref*100)/100).toFixed(2);
      document.myform.tot_A.disabled=true;
      document.myform.tot_D.disabled=true;
      document.myform.tot_A.style.backgroundColor='#BBBBBB';
      document.myform.tot_D.style.backgroundColor='#BBBBBB';
      document.myform.ins.disabled=false;
      document.myform.diffV.value='".$script_transl['bal']."';
      }\n";

echo "function updateTot(row,newva)
      {
      var nv = [newva.value].toString().replace(/\,/g,'.').split(/\./);
      if (!nv[1]){
           nv[1] = '0';
      }
      nv = (Math.round(Number(nv[0]+'.'+nv[1])*100)/100).toFixed(2);
      if (isNaN(nv)){
           nv = 0;
      }
      var rw = Number([row]);
      var sumD = 0;
      var sumA = 0;
      for (i=0; i<".$_POST['rigcon']."; i++) {
        if (i == rw) {
           document.getElementById('impoRC'+i).value=nv;
           if (nv < 0.01) {
               document.getElementById('impoRC'+i).style.backgroundColor='#FFAAAA';
           } else {
               document.getElementById('impoRC'+i).style.backgroundColor='transparent';
           }
        }
        var elva = document.getElementById('impoRC'+i).value*1;
        var elda = document.getElementById('daavRC'+i).value;
        if (elda == 'D') {
           sumD = sumD + elva;
        } else {
           sumA = sumA + elva;
        }
      }
      var difSUM = (Math.round((sumD - sumA)*100)/100).toFixed(2);
      var dtit = ' ".$script_transl['subval']." ';
      for (i=0; i<".$_POST['rigcon']."; i++) {
          var elda = document.getElementById('daavRC'+i).value;
          var elva = document.getElementById('impoRC'+i).value*1;
          if ((elda == 'D' && difSUM > 0) || (elda == 'A' && difSUM < 0)) {
             if (Math.abs(difSUM) < elva ) {
                document.getElementById('balbRC'+i).value='\u21D3';
                document.getElementById('balbRC'+i).disabled=false;
                document.getElementById('balbRC'+i).title=dtit + Math.abs(difSUM) + ' ".$admin_aziend['symbol']."';
             } else {
                document.getElementById('balbRC'+i).value='\u21D3';
                document.getElementById('balbRC'+i).disabled=true;
             }
          } else if ((elda == 'D' && difSUM < 0) || (elda == 'A' && difSUM > 0)) {
             document.getElementById('balbRC'+i).value='\u21D1';
             document.getElementById('balbRC'+i).disabled=false;
             document.getElementById('balbRC'+i).title='".$script_transl['addval']." ' + Math.abs(difSUM) + ' ".$admin_aziend['symbol']."';
          } else {
             document.getElementById('balbRC'+i).value='\u21D4';
             document.getElementById('balbRC'+i).disabled=true;
          }
      }
      if (difSUM != 0) {
           document.myform.tot_A.style.backgroundColor='#FFAAAA';
           document.myform.tot_D.style.backgroundColor='#FFAAAA';
           if (sumA == 0 ) {
              document.myform.tot_A.disabled=true;
              document.myform.tot_D.disabled=false;
              document.myform.tot_D.title='".$script_transl['bal_title']."';
           } else if (sumD == 0 ){
              document.myform.tot_A.disabled=false;
              document.myform.tot_D.disabled=true;
              document.myform.tot_A.title='".$script_transl['bal_title']."';
           } else {
              document.myform.tot_A.disabled=false;
              document.myform.tot_D.disabled=false;
              document.myform.tot_A.title='".$script_transl['bal_title']."';
              document.myform.tot_D.title='".$script_transl['bal_title']."';
           }
           document.myform.ins.disabled=true;
           document.myform.diffV.value='".$script_transl['diff']." ' + difSUM + ' ".$admin_aziend['symbol']."';
      } else if (sumA == 0 ) {
           document.myform.tot_A.style.backgroundColor='#FFAAAA';
           document.myform.tot_D.style.backgroundColor='#FFAAAA';
           document.myform.ins.disabled=true;
           document.myform.diffV.value='".$script_transl['zero']."';
      } else {
           document.myform.tot_A.disabled=true;
           document.myform.tot_D.disabled=true;
           document.myform.tot_A.style.backgroundColor='#BBBBBB';
           document.myform.tot_D.style.backgroundColor='#BBBBBB';
           document.myform.ins.disabled=false;
           document.myform.diffV.value='".$script_transl['bal']."';
      }
      document.myform.tot_A.value = (Math.round(sumA*100)/100).toFixed(2);
      document.myform.tot_D.value = (Math.round(sumD*100)/100).toFixed(2);
      }\n";
echo "</script>\n";
?>
<form method="POST" name="myform">
<?php
$gForm = new contabForm();
echo "<input type=\"hidden\" value=\"".$form['hidden_req']."\" name=\"hidden_req\" />\n";
echo "<input type=\"hidden\" name=\"".ucfirst($toDo)."\" value=\"\">\n";
if ($toDo == 'insert') {
   echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".$script_transl['ins_this']."</div>\n";
} else {
   echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".$script_transl['upd_this']." n.".$form['id_testata']."</div>\n";
}


// creo i dialog form delle partite aperte dei clienti/fornitori

//print_r($form['paymov']);
foreach($form['paymov'] as $k=>$v) {
    foreach($form['paymov'][$k] as $k_r=>$v_r) {
        echo "<input type=\"hidden\" name=\"paymov[$k][$k_r]\" value=\"".$v_r."\">\n";
    }    
    echo '<div id="dialog'.$k.'" title="Partite Aperte del conto n.'.$v['codcon'].'">
   <p class="validateTips"></p>
   <div id="openitem-contain" class="ui-widget">
    <table id="openitem'.$k.'" class="ui-widget ui-widget-content">
     <tbody>
    <tr>
    <td><label for="expiry">Scadenza</label></td>
    <td><label for="name">Importo</label></td>
    <td><label for="remrow"></label></td>
    </tr>
    <tr>
    <td><input type="text" name="expiry" id="expiry" class="text ui-widget-content ui-corner-all" /></td>
    <td><input type="text" name="amount" id="amount" class="text ui-widget-content ui-corner-all" /></td>
    <td><button id="rerun"><img src="../../library/images/v.gif" />  </button></td>
    <td></td>
    </tr>
    <tr><td colspan="3">
    <DIV name="resultsContainer" id="resultsContainer" class="text ui-widget-content ui-corner-all">__RISULTATO_</DIV>
    </td></tr>
     </tbody>
    </table>
   </div>
  </div>';

}

?>


<table border="0" cellpadding="3" cellspacing="1" class="FacetFormTABLE" align="center">


<?php
if (!empty($msg)) {
    echo '<tr><td colspan="6" class="FacetDataTDred">'.$gForm->outputErrors($msg,$script_transl['errors'])."</td></tr>\n";
}
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['date_reg']."</td><td colspan=\"5\" class=\"FacetDataTD\">\n";
$gForm->CalendarPopup('date_reg',$form['date_reg_D'],$form['date_reg_M'],$form['date_reg_Y'],'FacetSelect',1);
echo "</td>\n";
echo "</tr>\n";
?>
<tr>
<td class="FacetFieldCaptionTD"><?php echo $script_transl['caucon'];?></td>
<td  class="FacetDataTD" colspan="5">
<?php
echo '<select name="codcausale" class="FacetSelect" ';
if (empty($form["codcausale"])) {
  echo ' tabindex="14"';
  $tabsmt=' tabindex="15"';
} else {
  $tabsmt='';
}
echo '><option value="">Libera</option>';
$result = gaz_dbi_dyn_query("*", $gTables['caucon'],1,"regiva DESC, operat DESC, descri ASC");
while ($row = gaz_dbi_fetch_array($result)) {
    $selected="";
    if($form["codcausale"] == $row['codice']) {
       $selected = " selected ";
    }
    echo "<option value=\"".$row['codice']."\"".$selected.">".$row['codice']." - ".$row['descri']."</option>\n";
}
echo "</select> &nbsp;<input type=\"image\" name=\"inscau\" src=\"../../library/images/vbut.gif\" title=\"".$script_transl['v_caucon']."!\" $tabsmt ></td></tr>\n";
echo "<tr>\n";
echo "\t<td class=\"FacetFieldCaptionTD\">".$script_transl['descri']."</td>\n";
echo "\t<td colspan=\"5\" class=\"FacetDataTD\"><input type=\"text\" name=\"descrizion\" value=\"".$form['descrizion']."\" maxlength=\"50\" size=\"50\" /></td>\n";
echo "</td>\n";
echo "</tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['insdoc']."</td><td class=\"FacetDataTD\" >\n";
$gForm->variousSelect('inserimdoc',$script_transl['insdoc_value'],$form['inserimdoc'],'FacetSelect',false,'inserimdoc');
echo "\t </td>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['regiva']."</td><td class=\"FacetDataTD\">\n";
$gForm->variousSelect('registroiva',$script_transl['regiva_value'],$form['registroiva'],'FacetSelect',false,'registroiva');
echo "\t </td>\n";
echo "<td class=\"FacetFieldCaptionTD\">".$script_transl['operat']."</td><td class=\"FacetDataTD\">\n";
$gForm->variousSelect('operatore',$script_transl['operat_value'],$form['operatore'],'FacetSelect',false,'operatore');
echo "\t </td>\n";
echo "</tr>\n";
?>
</table>
<?php
//inserimento dati documenti
if($form["inserimdoc"] == 1) {
    echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".$script_transl['insdoc']."</div>";
    echo "<table class=\"Tlarge\">";
    echo "<tr><td class=\"FacetDataTD\" >".$script_transl['seziva']."</td><td class=\"FacetDataTD\" >".$script_transl['numdoc']."</td><td class=\"FacetDataTD\" >".$script_transl['protoc']."</td><td class=\"FacetDataTD\" >".$script_transl['date_doc']."</td><td class=\"FacetDataTD\" >".$script_transl['partner']."</td></tr>";
    echo "<tr><td class=\"FacetDataTD\" >";
    echo "<select name=\"sezioneiva\" class=\"FacetSelect\" onchange=\"this.form.submit()\">";
    for ($sez = 1; $sez <= 3; $sez++) {
        $selected="";
        if($form["sezioneiva"] == $sez) {
           $selected = " selected ";
        }
        echo "<option value=\"".$sez."\"".$selected.">".$sez."</option>\n";
    }
    echo "</select></td>";
    if (empty($form['numdocumen'])){
      $tabnum = ' tabindex="10" ';
    } else {
      $tabnum = '';
    }
    echo "<td class=\"FacetDataTD\" ><input type=\"text\" value=\"".$form['numdocumen']."\" maxlength=\"20\" size=\"20\" name=\"numdocumen\" $tabnum></td>";
    echo "<td class=\"FacetDataTD\" ><input type=\"text\" value=\"".$form['protocollo']."\" maxlength=\"7\" size=\"7\" name=\"protocollo\"></td>";
    echo "<td class=\"FacetDataTD\">\n";
    $gForm->CalendarPopup('date_doc',$form['date_doc_D'],$form['date_doc_M'],$form['date_doc_Y'],'FacetSelect',1);
    echo "</td>\n";
    $partnersel = $anagrafica->getPartner($form['cod_partner']);
    echo "<td class=\"FacetDataTD\" >".$partnersel['ragso1']." ".$partnersel['citspe']."</td></tr></table>";
} else {
    echo "<input type=\"hidden\" name=\"sezioneiva\" value=\"".$form['sezioneiva']."\">\n";
    echo "<input type=\"hidden\" name=\"numdocumen\" value=\"".$form['numdocumen']."\">\n";
    echo "<input type=\"hidden\" name=\"protocollo\" value=\"".$form['protocollo']."\">\n";
    echo "<input type=\"hidden\" name=\"date_doc_D\" value=\"".$form['date_doc_D']."\">\n";
    echo "<input type=\"hidden\" name=\"date_doc_M\" value=\"".$form['date_doc_M']."\">\n";
    echo "<input type=\"hidden\" name=\"date_doc_Y\" value=\"".$form['date_doc_Y']."\">\n";
}
echo "<input type=\"hidden\" name=\"cod_partner\" value=\"".$form['cod_partner']."\">\n";

//inserimento movimento iva
if($form["registroiva"] > 0) {
    echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".$script_transl['insiva']."</div>\n";
    echo "<table class=\"Tlarge\">\n";
    echo "<tr><td class=\"FacetColumnTD\">".$script_transl['taxable']."</td><td class=\"FacetColumnTD\">".$script_transl['vat']."</td><td class=\"FacetColumnTD\">".$script_transl['tax']."</td><td class=\"FacetColumnTD\" align=\"right\">".$script_transl['addrow']."!</td></tr>\n";
    if ($_POST['rigiva']==0) { //se non ci sono righi tabulo
      $tabimp = ' tabindex="20" ';
      $tabsmt = ' tabindex="21" ';
    } else {
      $tabimp = '';
      $tabsmt = '';
    }
    echo "<tr><td class=\"FacetColumnTD\"><input type=\"text\" value=\"\" $tabimp maxlength=\"13\" size=\"13\" name=\"insert_imponi\">";
    echo "<td class=\"FacetColumnTD\">";
    $select_aliiva = new selectaliiva("insert_codiva");
    $select_aliiva -> addSelected($form["insert_codiva"]);
    $select_aliiva -> output();
    echo "</td>";
    echo "<td class=\"FacetColumnTD\"></td><td class=\"FacetColumnTD\" align=\"right\"><input type=\"image\" name=\"adi\" src=\"../../library/images/vbut.gif\" title=\"Aggiungi il rigo\" $tabsmt >";
    echo "<input type=\"hidden\" value=\"".$_POST['rigiva']."\" name=\"rigiva\"><input type=\"hidden\" name=\"ritorno\" value=\"{$_POST['ritorno']}\"></td></tr>\n";
    echo "<TR><td class=\"FacetColumnTD\" colspan=\"4\"><hr></td></tr>";
    echo "<tr>";
    for ($i = 0; $i < $_POST['rigiva']; $i++) {
        if (!isset($form['imponi_ri'][$i])) {
            $form['imponi_ri'][$i] = "";
        }
        if (!isset($form['impost_ri'][$i])) {
            $form['impost_ri'][$i] = "";
        }
        if (!isset($form['codiva_ri'][$i])) {
           $form['codiva_ri'][$i] = "";
        }
        $rigoi = gaz_dbi_get_row($gTables['aliiva'],"codice",$form['codiva_ri'][$i]);
        echo "<td class=\"FacetDataTD\"><input type=\"text\" align=\"right\" name=\"imponi_ri[$i]\" value=\"".sprintf("%01.2f",preg_replace("/\,/",'.',$form['imponi_ri'][$i]))."\" maxlength=\"13\" size=\"13\"></td>\n";
        echo "<td class=\"FacetDataTD\"><input type=\"hidden\" name=\"id_rig_ri[$i]\" value=\"{$form['id_rig_ri'][$i]}\"><input type=\"hidden\" name=\"codiva_ri[$i]\" value=\"{$form['codiva_ri'][$i]}\">".$rigoi['descri']."</td>\n";
        echo "<td class=\"FacetDataTDred\"><input type=\"text\" align=\"right\" name=\"impost_ri[$i]\" value=\"".sprintf("%01.2f",preg_replace("/\,/",'.',$form['impost_ri'][$i]))."\" maxlength=\"13\" size=\"13\"></td>\n";
        echo "<TD  class=\"FacetDataTD\" align=\"right\"><input type=\"image\" name=\"dei[$i]\"  src=\"../../library/images/xbut.gif\" title=\"".$script_transl['delrow']."!\" ><br></td></tr>\n";
        echo "</tr>";
    }
echo "</table>";
}
//inserimento movimento contabile
echo "<div align=\"center\" class=\"FacetFormHeaderFont\">".$script_transl['del_this']."</div>\n";
echo "<table class=\"Tlarge\">\n";
echo "<tr><td class=\"FacetColumnTD\">".$script_transl['mas']."</td><td class=\"FacetColumnTD\">".$script_transl['sub']."</td><td class=\"FacetColumnTD\">".$script_transl['amount']."</td><td class=\"FacetColumnTD\">".$script_transl['daav']."</td><td class=\"FacetColumnTD\">".$script_transl['addrow']."!</td></tr>\n";
echo "<tr>\n";
echo "<td class=\"FacetColumnTD\">\n";
$gForm->selMasterAcc('insert_mastro',$form['insert_mastro'],'insert_mastro');
echo "</td>\n";
echo "<td class=\"FacetColumnTD\">\n";
$gForm->lockSubtoMaster($form['insert_mastro'],'insert_conto');
$gForm->selSubAccount('insert_conto',$form['insert_conto'],$form['search']['insert_conto'],$form['hidden_req'],$script_transl['mesg']);
echo "</td>\n";
echo "<td class=\"FacetColumnTD\"><div onmousedown=\"toggleContent('insert')\" class=\"clickarea\" style=\"cursor:pointer;\">";
echo "<input style=\"text-align:right;\" type=\"text\" value=\"\" maxlength=\"13\" size=\"13\" id=\"insert_import\" name=\"insert_import\"> &crarr;</div>\n";
$gForm->settleAccount('insert',$form['insert_conto'],sprintf("%04d%02d%02d",$form['date_reg_Y'],$form['date_reg_M'],$form['date_reg_D']));
echo "</td>";
echo "\t<td  class=\"FacetDataTD\">\n";
$gForm->variousSelect('insert_darave',$script_transl['daav_value'],$form['insert_darave'],'FacetSelect',false);
echo "\t </td>\n";
echo "<td class=\"FacetColumnTD\" align=\"right\"><input type=\"image\" name=\"add\" src=\"../../library/images/vbut.gif\" title=\"".$script_transl['addrow']."\"></td></tr>\n";
echo "<TR><td class=\"FacetColumnTD\" colspan=\"5\"><hr></td></tr>";
//fine rigo inserimento

// inizio righi già inseriti
// faccio un primo ciclo del form per sommare e analizzare gli sbilanciamenti
$form['tot_D']=0.00;
$form['tot_A']=0.00;
for ($i = 0; $i < $_POST['rigcon']; $i++) {
    $val=sprintf("%01.2f",preg_replace("/\,/",'.',$form['importorc'][$i]));
    if ($form["darave_rc"][$i] == 'D'){
        $form['tot_D'] += $val;
    } else {
        $form['tot_A'] += $val;
    }
}
$diffDA=number_format($form['tot_D']-$form['tot_A'],2,'.','');
if ($diffDA <> 0){
    if ($form['tot_D'] == 0) {
        $d_but=' style="text-align:right; background-color:#FFAAAA;" disabled ';
        $a_but=' style="text-align:right; background-color:#FFAAAA;" title="'.$script_transl['bal_title'].'" ';
    } elseif ($form['tot_A'] == 0) {
        $d_but=' style="text-align:right; background-color:#FFAAAA;" title="'.$script_transl['bal_title'].'" ';
        $a_but=' style="text-align:right; background-color:#FFAAAA;" disabled ';
    } else {
        $d_but=' style="text-align:right; background-color:#FFAAAA;" title="'.$script_transl['bal_title'].'" ';
        $a_but=' style="text-align:right; background-color:#FFAAAA;" title="'.$script_transl['bal_title'].'" ';
    }
    $i_but=' disabled ';
    $diffV = ' <input style="text-align:center;" value="'.$diffDA.'" type="text" name="diffV" disabled />';
} elseif ($form['tot_A'] == 0){
    $d_but=' style="text-align:right; background-color:#FFAAAA;" title="'.$script_transl['bal_title'].'" ';
    $a_but=' style="text-align:right; background-color:#FFAAAA;" title="'.$script_transl['bal_title'].'" ';
    $i_but=' disabled ';
    $diffV = ' <input style="text-align:center;" value="Movimenti a zero" type="text" name="diffV" disabled />';
} else {
    $d_but=' style="text-align:right; background-color:#BBBBBB;" disabled ';
    $a_but=' style="text-align:right; background-color:#BBBBBB;" disabled ';
    $i_but='';
    $diffV = ' <input style="text-align:center;" value="'.$script_transl['bal'].'" type="text" name="diffV" disabled />';
}
//fine analisi sbilanciamento

for ($i = 0; $i < $_POST['rigcon']; $i++) {
    if ($form['registroiva'] > 0 and
        (substr($form['conto_rc'.$i],0,3) == $admin_aziend['mascli'] or
         substr($form['conto_rc'.$i],0,3) == $admin_aziend['masfor'])) {
        $form['insert_partner'] = $form['conto_rc'.$i];
    }
    echo "<tr>";
    echo "<td class=\"FacetDataTD\">".($i+1);
    $gForm->selMasterAcc("mastro_rc[$i]",$form["mastro_rc"][$i],"mastro_rc[$i]");
    echo "</td>\n";
    echo "<td class=\"FacetDataTD\">";
    $gForm->lockSubtoMaster($form["mastro_rc"][$i],'conto_rc'.$i);
    $gForm->selSubAccount('conto_rc'.$i,$form['conto_rc'.$i],$form['search']['conto_rc'.$i],$form['hidden_req'],$script_transl['mesg']);
    echo "<a href=\"select_partit.php?id=".$form['conto_rc'.$i]."\" target=\"_new\"> <img src=\"../../library/images/vis.gif\" title=\"".$script_transl['visacc']."\" border=\"0\"> </a>\n";
    echo "</td>\n";
    $val=sprintf("%01.2f",preg_replace("/\,/",'.',$form['importorc'][$i]));
    $valsty=' style="text-align:right;" ';
    if ($val<0.01) {
       $valsty =' style="text-align:right; background-color:#FFAAAA;" ';
    }
    echo "<td class=\"FacetDataTD\">
          <input type=\"text\" name=\"importorc[$i]\" ID=\"impoRC$i\" value=\"$val\" $valsty onchange=\"updateTot($i,this);\" maxlength=\"13\" size=\"13\"  tabindex=\"".(30+$i*2)."\" >\n";
    echo "<input type=\"hidden\" name=\"id_rig_rc[$i]\" value=\"".$form['id_rig_rc'][$i]."\">\n";
    // inizio input degli sbilanci
    if ($form['darave_rc'][$i] == 'D' && $form['tot_D'] > $form['tot_A'] ||
        $form['darave_rc'][$i] == 'A' && $form['tot_A'] > $form['tot_D'] ) {
        $r_but=' value="&dArr;" title="'.$script_transl['subval'].' ';
        if (abs($diffDA) < $form['importorc'][$i] ) {
           $r_but=' value="&dArr;" title="'.$script_transl['subval'].' '.abs($diffDA)." ".$admin_aziend['symbol']."\" ";
        } else {
           $r_but=' value="&dArr;" disabled ';
        }
    } elseif ($form['darave_rc'][$i] == 'D' && $form['tot_D'] < $form['tot_A'] ||
        $form['darave_rc'][$i] == 'A' && $form['tot_A'] < $form['tot_D'] ) {
        $r_but=' value="&uArr;" title="'.$script_transl['addval'].' '.abs($diffDA)." ".$admin_aziend['symbol']."\" ";
    } else {                                     //bilanciato
        $r_but=' value="&hArr;" disabled';
    }
    echo "<input type=\"button\" ID=\"balbRC$i\" name=\"balb[$i]\" $r_but  onclick=\"balance($i);\"/>\n";
    echo "</td>";
    //fine inpunt degli sbilanci
    echo "<td class=\"FacetDataTD\"><select class=\"FacetSelect\" ID=\"daavRC$i\" name=\"darave_rc[$i]\" onchange=\"this.form.submit()\" tabindex=\"".(31+$i*2)."\">";
    foreach ($script_transl['daav_value'] as $key => $value) {
        $selected="";
        if($form["darave_rc"][$i] == $key) {
            $selected = " selected ";
        }
        echo "<option value=\"".$key."\"".$selected.">".$value."</option>\n";
    }
    echo "</select></td>\n";
    echo "<td class=\"FacetDataTD\" align=\"right\"><input type=\"image\" name=\"del[$i]\"  src=\"../../library/images/xbut.gif\" title=\"".$script_transl['delrow']."!\" ></td></tr>\n";
}

//faccio il post del numero di righi
echo "<input type=\"hidden\" value=\"".$_POST['rigcon']."\" name=\"rigcon\">";
echo "<input type=\"hidden\" value=\"".$form['id_testata']."\" name=\"id_testata\">";
echo '<tr><td>';
echo '<input name="Back" type="button" value="'.$script_transl['return'].'!" onclick="location.href=\''.$_POST['ritorno'].'\'">';
echo '<td colspan="2">'.$script_transl['tot_d'].' :';
echo "<input type=\"button\" $d_but value=\"".number_format($form['tot_D'],2,'.','')."\" ID=\"tot_D\" name=\"tot_D\" onclick=\"tot_bal('D');\" />\n";
echo $diffV.' '.$script_transl['tot_a'].' :';
echo "<input type=\"button\" $a_but value=\"".number_format($form['tot_A'],2,'.','')."\" ID=\"tot_A\" name=\"tot_A\" onclick=\"tot_bal('A');\" />\n";
echo "</td>\n";
echo '<td align="right">';
echo '<input name="ins" id="preventDuplicate" onClick="chkSubmit();" type="submit" '.$i_but.' tabindex="99" value="'.strtoupper($script_transl[$toDo]).'!">';
echo "\n</td></tr></table>";
?>
</form>
</body>
</html>