<?php
/* ------------------------------------------------------------------------
  INTERFACCIA Download ordini da e-commerce a GAzie via FTP
  ------------------------------------------------------------------------
  @Author    Antonio Germani
  @Website   http://www.programmisitiweb.lacasettabio.it
  @Copyright Copyright (C) 2018 - 2021 Antonio Germani All Rights Reserved.
  versione 3.0
  ------------------------------------------------------------------------ */
global $gTables,$admin_aziend;
$resserver = gaz_dbi_get_row($gTables['company_config'], "var", "server");
$ftp_host= $resserver['val'];
$resuser = gaz_dbi_get_row($gTables['company_config'], "var", "user");
$ftp_user = $resuser['val'];
$respass = gaz_dbi_get_row($gTables['company_config'], "var", "pass");
$ftp_pass= $respass['val'];
$accpass = gaz_dbi_get_row($gTables['company_config'], "var", "accpass")['val'];
$path = gaz_dbi_get_row($gTables['company_config'], 'var', 'path');
$urlinterf = $path['val']."ordini-gazie.php";//nome del file interfaccia presente nella root del sito Joomla. Per evitare intrusioni indesiderate Il file dovrà gestire anche una password. Per comodità viene usata la stessa FTP.
// il percorso per raggiungere questo file va impostato in configurazione avanzata azienda alla voce "Website root directory"
$test = gaz_dbi_query("SHOW COLUMNS FROM `" . $gTables['admin'] . "` LIKE 'enterprise_id'");
$exists = (gaz_dbi_num_rows($test)) ? TRUE : FALSE;
if ($exists) {
    $c_e = 'enterprise_id';
} else {
    $c_e = 'company_id';
}
$admin_aziend = gaz_dbi_get_row($gTables['admin'] . ' LEFT JOIN ' . $gTables['aziend'] . ' ON ' . $gTables['admin'] . '.' . $c_e . '= ' . $gTables['aziend'] . '.codice', "user_name", $_SESSION['user_name']);
	
if (isset($_POST['conferma'])) { // se confermato
	// ricavo il progressivo in base al tipo di documento
	$where = "numdoc desc";
	$sql_documento = "YEAR(datemi) = " . date("Y") . " and tipdoc = 'VOW'";
	$rs_ultimo_documento = gaz_dbi_dyn_query("*", $gTables['tesbro'], $sql_documento, $where, 0, 1);
	$ultimo_documento = gaz_dbi_fetch_array($rs_ultimo_documento);
	// se e' il primo documento dell'anno, resetto il contatore
	if ($ultimo_documento) {
		$numdoc = $ultimo_documento['numdoc'] + 1;
	} else {
		$numdoc = 1;
	}
    // scrittura ordini su database di GAzie
	for ($ord=0 ; $ord<=$_POST['num_orders']; $ord++){// ciclo gli ordini e scrivo i database
	
		$query = "SHOW TABLE STATUS LIKE '" . $gTables['tesbro'] . "'";
		$result = gaz_dbi_query($query);
		$row = $result->fetch_assoc();
		$id_tesbro = $row['Auto_increment']; // trovo l'ID che avrà TESBRO: testata documento/ordine
	
		$query = "SHOW TABLE STATUS LIKE '" . $gTables['anagra'] . "'";
		$result = gaz_dbi_query($query);
		$row = $result->fetch_assoc();
		$id_anagra = $row['Auto_increment']; // trovo l'ID che avrà ANAGRA: Anagrafica cliente
	
		$anagrafica = new Anagrafica(); 
		$last = $anagrafica->queryPartners('*', "codice BETWEEN " . $admin_aziend['mascli'] . "000000 AND " . $admin_aziend['mascli'] . "999999", "codice DESC", 0, 1);
		$codice = substr($last[0]['codice'], 3) + 1;
		$clfoco = $admin_aziend['mascli'] * 1000000 + $codice;// trovo il codice di CLFOCO da connettere all'anagrafica cliente	se cliente inesistente
	    	
		$listin=intval($_POST['numlist'.$ord]);
		$listinome=$_POST['numlistnome'.$ord];
		$includevat=$_POST['includevat'.$ord];
		
		$stapre="T"; // stampa prezzi con totale
		
		if (isset($_POST['download'.$ord]) ) {
			$esiste=0;
			if (strlen($_POST['codfis'.$ord])>0){ // controllo esistenza cliente per codice fiscale
				$query = "SELECT * FROM " . $gTables['anagra'] . " WHERE codfis ='" . $_POST['codfis'.$ord] . "'";
				$check = gaz_dbi_query($query);
				if ($check->num_rows > 0){
					$esiste=1;
					$row = $check->fetch_assoc();
					$cl = gaz_dbi_get_row($gTables['clfoco'], "id_anagra", $row['id']);
					$clfoco=$cl['codice'];
				}
			}
			if (intval($_POST['pariva'.$ord])>0 AND $esiste==0 ){ // controllo esistenza cliente per partita iva
				$query = "SELECT * FROM " . $gTables['anagra'] . " WHERE pariva ='" . $_POST['pariva'.$ord] . "'";
				$check = gaz_dbi_query($query);
				if ($check->num_rows > 0){
					$esiste=1;
					$row = $check->fetch_assoc();
					$cl = gaz_dbi_get_row($gTables['clfoco'], "id_anagra", $row['id']);
					$clfoco=$cl['codice'];
				}
			}
			 // controllo esistenza cliente per cognome, nome e città
			if ($esiste==0){	
				$query = "SELECT * FROM " . $gTables['anagra'] . " WHERE ragso1 ='" . addslashes($_POST['ragso1'.$ord]) . "' AND ragso2 ='". addslashes($_POST['ragso2'.$ord]) . "' LIMIT 1";
				$check = gaz_dbi_query($query);
				while ($row = $check->fetch_assoc()) {
					if (($check->num_rows > 0) && ($row['citspe']=$_POST['citspe'.$ord]) && ($row['indspe']=$_POST['indspe'.$ord])){						
						$cl = gaz_dbi_get_row($gTables['clfoco'], "id_anagra", $row['id']);
						if ($cl){
						$clfoco=$cl['codice'];
						$esiste=1;
						}
					}
				}
			}
	
			if ($esiste==0) { //registro cliente se non esiste
					if ($_POST['country'.$ord]=="IT"){ // se la nazione è IT
						$lang="1";
					} else {
						$lang="0";
					}
					if (strlen ($_POST['codfis'.$ord])>1 AND intval ($_POST['codfis'.$ord])==0){ // se il codice fiscale non è numerico 
							if (substr($_POST['codfis'.$ord],9,2)>40){ // deduco il sesso 
								$sexper="F";
							} else {
								$sexper="M";
							}
					} else {
						$sexper="G";
					}
					gaz_dbi_query("INSERT INTO " . $gTables['anagra'] . "(ragso1,ragso2,sexper,indspe,capspe,citspe,prospe,country,id_currency,id_language,telefo,codfis,pariva,fe_cod_univoco,e_mail,pec_email) VALUES ('" . addslashes($_POST['ragso1'.$ord]) . "', '" . addslashes($_POST['ragso2'.$ord]) . "', '". $sexper. "', '". addslashes($_POST['indspe'.$ord]) ."', '".$_POST['capspe'.$ord]."', '". addslashes($_POST['citspe'.$ord]) ."', '". $_POST['prospe'.$ord] ."', '" . $_POST['country'.$ord]. "', '1', '".$lang."', '". $_POST['telefo'.$ord] ."', '". $_POST['codfis'.$ord] ."', '" . $_POST['pariva'.$ord] . "', '" . $_POST['fe_cod_univoco'.$ord] . "', '". $_POST['email'.$ord] . "', '". $_POST['pec_email'.$ord] . "')");
					
					gaz_dbi_query("INSERT INTO " . $gTables['clfoco'] . "(ref_ecommerce_id_customer,codice,id_anagra,listin,descri,destin,speban,stapre,codpag) VALUES ('".$_POST['ref_ecommerce_id_customer'.$ord]."', '". $clfoco . "', '" . $id_anagra . "', '". $listin ."' , '" .addslashes($_POST['ragso1'.$ord])." ".addslashes($_POST['ragso2'.$ord]) . "', '". $_POST['destin'.$ord] ."', 'S', '". $stapre ."', '". $_POST['pagame'.$ord] ."')");
			}
			
			if ($_POST['order_discount_price'.$ord]>0){ // se il sito ha mandato uno sconto totale a valore calcolo lo sconto in percentuale da dare ad ogni rigo
				$lordo=$_POST['order_full_price'.$ord]+$_POST['order_discount_price'.$ord]-$_POST['speban'.$ord]-$_POST['traspo'.$ord];
				$netto=$lordo-$_POST['order_discount_price'.$ord];
				$percdisc= 100-(($netto/$lordo)*100);
			} else {
				$percdisc="";
			}
			
			if ($_POST['codvatcost'.$ord] == ""){ // se l'e-commerce non ha mandato il codice delle spese incasso  e trasporto
				$expense_vat = $admin_aziend['preeminent_vat']; // ci metto quelle preminenti aziendali
			} else {
				$expense_vat = $_POST['codvatcost'.$ord]; // altrimenti metto il codice che ha mandato
			}
			if ($includevat=="true"){ // se l'e-commerce include l'iva la scorporo alle spese banca e trasporto
				$vat = gaz_dbi_get_row($gTables['aliiva'], "codice", $expense_vat);
				$div="1.".intval($vat['aliquo']);
				$_POST['speban'.$ord]=floatval($_POST['speban'.$ord]) / $div;
				$_POST['traspo'.$ord]=floatval($_POST['traspo'.$ord]) / $div;
			}		
		
			// registro testata ordine
			gaz_dbi_query("INSERT INTO " . $gTables['tesbro'] . "(ref_ecommerce_id_order,tipdoc,seziva,print_total,datemi,numdoc,datfat,clfoco,pagame,listin,spediz,traspo,speban,caumag,expense_vat,initra,status,adminid) VALUES ('".$_POST['ref_ecommerce_id_order'.$ord]."', 'VOW', '" . $_POST['seziva'.$ord] . "', '1', '" . $_POST['datemi'.$ord] . "', '" . $numdoc . "', '0000-00-00', '". $clfoco . "', '" .$_POST['pagame'.$ord]."', '". $listin . "', '".$_POST['spediz'.$ord]."', '". $_POST['traspo'.$ord] ."', '". $_POST['speban'.$ord] ."', '1', '". $expense_vat ."', '" . $_POST['datemi'.$ord]. "', 'ONLINE-SHOP', '" . $admin_aziend['adminid'] . "')");
		
			// Gestione righi ordine					
			for ($row=0; $row<=$_POST['num_rows'.$ord]; $row++){
								
				// controllo se esiste l'articolo in GAzie 
				$ckart = gaz_dbi_get_row($gTables['artico'], "ref_ecommerce_id_product", $_POST['refid'.$ord.$row]);
				if ($ckart){
					$codart=$ckart['codice']; // se esiste ne prendo il codice come $codart
					$descri=$ckart['descri'].$_POST['adddescri'.$ord.$row];// se esiste, lo metto in $descri e aggiungo l'eventuale adddescription
				}
				 
				if (!$ckart){ // se non esiste creo un nuovo articolo su gazie 
					if ($_POST['stock'.$ord.$row]>0){
						$good_or_service=0;
					} else {
						$good_or_service=1;
					}
					if ($_POST['aliiva'.$ord.$row]==""){ // se il sito non ha mandato l'aliquota IVA dell'articolo ci metto quello che deve mandare come base aziendale riservato alle spese
						$_POST['codvat'.$ord.$row]=$_POST['codvatcost'.$ord];
						$_POST['aliiva'.$ord.$row]=$_POST['aliivacost'.$ord];
					}
					if ($_POST['codvat'.$ord.$row]<1){ // se il sito non ha mandato il codice iva di GAzie cerco di ricavarlo dalla tabella aliiva
						$vat = gaz_dbi_get_row($gTables['aliiva'], "aliquo", $_POST['aliiva'.$ord.$row], " AND tipiva = 'I'");
						$codvat=$vat['codice'];
						$aliiva=$vat['aliquo'];
					} else {
						$codvat=$_POST['codvat'.$ord.$row];
						$aliiva=$_POST['aliiva'.$ord.$row];
					}
					if ($includevat=="true" AND $_POST['prelis_imp'.$ord.$row]==0){ // se l'e-commerce include l'iva e non ha mandato il prezzo imponibile, scorporo l'iva dal prezzo dell'articolo
						$div=0;
						$div="1.".intval($aliiva);
						$prelis=$_POST['prelis_vatinc'.$ord.$row] / $div;					
					} elseif ($includevat=="true" AND $_POST['prelis_imp'.$ord.$row]>0) {
						$prelis=$_POST['prelis_imp'.$ord.$row];
					}
					if ($includevat!=="true"){ // se l'ecommerce non iclude l'iva uso il prezzo imponibile
						$prelis=$_POST['prelis_imp'.$ord.$row];
					}
					
					$id_artico_group="";
					$arrayvar="";
					if ($_POST['product_parent_id'.$ord.$row] > 0 OR $_POST['type'.$ord.$row] == "variant" ){ // se è una variante
					
						// controllo se esiste il suo artico_group/padre in GAzie
						unset($parent);
						$parent = gaz_dbi_get_row($gTables['artico_group'], "ref_ecommerce_id_main_product", $_POST['product_parent_id'.$ord.$row]);// trovo il padre in GAzie
						if ($parent){ // se esiste il padre
							$id_artico_group=$parent['id_artico_group']; // imposto il riferimento al padre
						} else {// se non esiste lo devo creare con i pochi dati che ho
							$parent['descri']=$_POST['descri'.$ord.$row];
							gaz_dbi_query("INSERT INTO " . $gTables['artico_group'] . "(descri,large_descri,image,web_url,ref_ecommerce_id_main_product,web_public,depli_public,adminid) VALUES ('" . addslashes($parent['descri']) . "', '" . htmlspecialchars_decode (addslashes($parent['descri'])). "', '', '', '". $_POST['product_parent_id'.$ord.$row] . "', '1', '1', '". $admin_aziend['adminid'] ."')");
							$id_artico_group=gaz_dbi_last_id(); // imposto il riferimento al padre
						}
						
						if (strlen($_POST['descri'.$ord.$row])<2){ // se non c'è la descrizione della variante 
							$_POST['descri'.$ord.$row]=$parent['descri']."-".$_POST['characteristic'.$ord.$row];// ci metto quella del padre accodandoci la variante
						}
						
						// creo un json array per la variante
						$arrayvar= array("var_id" => floatval($_POST['characteristic_id'.$ord.$row]), "var_name" => $_POST['characteristic'.$ord.$row]);
						$arrayvar = json_encode ($arrayvar);
						
					}
					
					// ricongiungo la categoria dell'e-commerce con quella di GAzie, se esiste	
					$category="";
					if (intval($_POST['catmer'.$ord.$row])>0){
						$cat = gaz_dbi_get_row($gTables['catmer'], "ref_ecommerce_id_category", addslashes (substr($_POST['catmer'.$ord.$row],0,15)));// controllo se esiste in GAzie
						if ($cat){
							$category=$cat['codice'];
						}
					}
					
					// prima di inserire il nuovo articolo controllo se il suo codice è stato già usato				
					unset($usato);
					$usato = gaz_dbi_get_row($gTables['artico'], "codice", $_POST['codice'.$ord.$row]);// controllo se il codice è già stato usato in GAzie	
					if ($usato){ // se il codice è già in uso lo modifico accodandoci l'ID
						$_POST['codice'.$ord.$row]=substr($_POST['codice'.$ord.$row],0,10)."-".substr($_POST['refid'.$ord.$row],0,4);
					}
					
					// inserisco il nuovo articolo
					gaz_dbi_query("INSERT INTO " . $gTables['artico'] . "(peso_specifico,web_mu,web_multiplier,ecomm_option_attribute,id_artico_group,web_public,codice,descri,ref_ecommerce_id_product,good_or_service,unimis,catmer,".$listinome.",aliiva,codcon,adminid) VALUES ('". $_POST['peso_specifico'.$ord.$row] ."', '". $_POST['unimis'.$ord.$row] ."', '1', '". $arrayvar ."', '". $id_artico_group ."', '1', '". addslashes (substr($_POST['codice'.$ord.$row],0,15)) ."', '". addslashes($_POST['descri'.$ord.$row]) ."', '".$_POST['refid'.$ord.$row]."', '".$good_or_service."', '" . $_POST['unimis'.$ord.$row] . "', '". $category ."', '". $prelis ."', '".$codvat."', '420000006', '" . $admin_aziend['adminid'] . "')");
					$codart= substr($_POST['codice'.$ord.$row],0,15);// dopo averlo creato ne prendo il codice come $codart
					$descri= $_POST['descri'.$ord.$row].$_POST['adddescri'.$ord.$row]; //prendo anche la descrizione
					
					
				} else {
					$codvat=gaz_dbi_get_row($gTables['artico'], "codice", $codart)['aliiva'];
					$aliiva=$_POST['aliiva'.$ord.$row];
					if ($includevat=="true" AND $_POST['prelis_imp'.$ord.$row]==0){ // se l'e-commerce include l'iva e non ha mandato il prezzo imponibile, scorporo l'iva dal prezzo dell'articolo
						$div=0;
						$div="1.".intval($aliiva);
						$prelis=$_POST['prelis_vatinc'.$ord.$row] / $div;					
					} elseif ($includevat=="true" AND $_POST['prelis_imp'.$ord.$row]>0) {
						$prelis=$_POST['prelis_imp'.$ord.$row];
					}
					if ($includevat!=="true"){ // se l'ecommerce non iclude l'iva uso il prezzo imponibile
						$prelis=$_POST['prelis_imp'.$ord.$row];
					}
				}								
									
				// salvo rigo su database tabella rigbro 
				gaz_dbi_query("INSERT INTO " . $gTables['rigbro'] . "(id_tes,codart,descri,unimis,quanti,prelis,sconto,codvat,codric,pervat,status) VALUES ('" . intval($id_tesbro) . "','" . $codart . "','" . addslashes($descri) . "','". $_POST['unimis'.$ord.$row] . "','" . $_POST['quanti'.$ord.$row] . "','" . $prelis . "', '".$percdisc."', '". $codvat. "', '420000006', '". $aliiva. "', 'ONLINE-SHOP')");
			}
						
			$id_tesbro++;
			$numdoc++; //incremento il numero d'ordine GAzie
		}		
	}
	header("Location: " . "../../modules/vendit/report_broven.php?auxil=VOW");
    exit;
}

$access=base64_encode($accpass);

// avvio il file di interfaccia presente nel sito web remoto
$headers = @get_headers($urlinterf.'?access='.$access);
if ( intval(substr($headers[0], 9, 3))==200){ // controllo se il file esiste o mi dà accesso

	$xml=simplexml_load_file($urlinterf.'?access='.$access.'&rnd='.mktime()) ;
	if (!$xml){
		?>
		<script>
		alert("<?php echo "Errore nella creazione del file xml"; ?>");
		location.replace("<?php echo $_POST['ritorno']; ?>");
		</script>
		<?php
	}
	require('../../library/include/header.php');
	$script_transl = HeadMain();
	?>
	<form method="POST" name="download" enctype="multipart/form-data">
	<input type="hidden" name="ritorno" value="<?php echo $_POST['ritorno'];?>" >
	<input type="hidden" name="download" value="download" >
			
			<table class="table table-striped" style="margin: 0 auto; max-width: 80%; margin-top:10px;">
				<thead>
					<tr>
					<th></th>
					<th>Codice</th>
					<th>Nome</th>
					<th>Cognome</th>
					<th>Città</th>
					<th>Totale</th>
					<th>Scarica</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$n=0;
					foreach($xml->Documents->children() as $order) { // carico le testate degli ordini
						$nr=0;
						?>
						<tr>
						<?php
						echo '<td>';
						echo $n;
						echo '</td><td>';
						echo $order->Number;
						echo '<input type="hidden" name="numdoc'. $n .'" value="'. $order->Number . '">';
						echo '</td><td>';
						echo $order->CustomerName;
						echo '<input type="hidden" name="ragso1'. $n .'" value="'. $order->CustomerSurname ." ". $order->CustomerName . '">';
						echo '</td><td>';
						echo $order->CustomerSurname;
						echo '<input type="hidden" name="ragso2'. $n .'" value="'. $order->BusinessName . '">';
						echo '</td><td>';
						echo $order->CustomerCity ;
						echo '<input type="hidden" name="citspe'. $n .'" value="'. $order->CustomerCity .'">';
						echo '</td><td>';
						echo gaz_format_number($order->Total);
						echo '<input type="hidden" name="order_full_price'. $n .'" value="'. $order->Total .'">';
						echo '<input type="hidden" name="order_discount_price'. $n .'" value="'. $order->TotalDiscount .'">';
						echo '</td><td>';
						echo '<input type="hidden" name="ref_ecommerce_id_order'. $n .'" value="'. $order->Numbering .'">';
						echo '<input type="hidden" name="ref_ecommerce_id_customer'. $n .'" value="'. $order->CustomerCode .'">';
						echo '<input type="hidden" name="prospe'. $n .'" value="'. $order->CustomerProvince .'">';
						echo '<input type="hidden" name="capspe'. $n .'" value="'. $order->CustomerPostCode .'">';
						echo '<input type="hidden" name="indspe'. $n .'" value="'. $order->CustomerAddress .'">';
						echo '<input type="hidden" name="country'. $n .'" value="'. $order->CustomerCountry .'">';
						echo '<input type="hidden" name="codfis'. $n .'" value="'. $order->CustomerFiscalCode .'">';
						echo '<input type="hidden" name="pariva'. $n .'" value="'. $order->CustomerVatCode .'">';				
						echo '<input type="hidden" name="telefo'. $n .'" value="'. $order->CustomerTel .'">';
						echo '<input type="hidden" name="datemi'. $n .'" value="'. $order->DateOrder .'">';
						echo '<input type="hidden" name="pagame'. $n .'" value="'. $order->PaymentName .'">';
						echo '<input type="hidden" name="numlistnome'. $n .'" value="'. $order->PriceList .'">';
						echo '<input type="hidden" name="numlist'. $n .'" value="'. $order->PriceListNum .'">';
						echo '<input type="hidden" name="includevat'. $n .'" value="'. $order->PricesIncludeVat .'">';
						echo '<input type="hidden" name="speban'. $n .'" value="'. $order->CostPaymentAmount .'">';
						echo '<input type="hidden" name="traspo'. $n .'" value="'. $order->CostShippingAmount .'">';
						echo '<input type="hidden" name="destin'. $n .'" value="'. $order->CustomerShippingDestin .'">';
						echo '<input type="hidden" name="spediz'. $n .'" value="'. $order->Carrier .'">';
						echo '<input type="hidden" name="codvatcost'. $n .'" value="'. $order->CostVatCode .'">';
						echo '<input type="hidden" name="aliivacost'. $n .'" value="'. $order->CostVatAli .'">';
						echo '<input type="hidden" name="seziva'. $n .'" value="'. $order->SezIva .'">';
						echo '<input type="hidden" name="email'. $n .'" value="'. $order->CustomerEmail .'">';
						echo '<input type="hidden" name="pec_email'. $n .'" value="'. $order->CustomerPecEmail .'">';
						echo '<input type="hidden" name="fe_cod_univoco'. $n .'" value="'. $order->CustomerCodeFattEl .'">';
						foreach($xml->Documents->Document[$n]->Rows->children() as $orderrow) { // carico le righe degli articoli ordinati
							echo '<input type="hidden" name="codice'. $n . $nr.'" value="'. $orderrow->Code . '">';
							echo '<input type="hidden" name="type'. $n . $nr.'" value="'. $orderrow->Type . '">';
							echo '<input type="hidden" name="descri'. $n . $nr.'" value="'. $orderrow->Description . '">';
							echo '<input type="hidden" name="adddescri'. $n . $nr.'" value="'. $orderrow->AddDescription . '">';
							echo '<input type="hidden" name="stock'. $n . $nr.'" value="'. $orderrow->Stock . '">';
							echo '<input type="hidden" name="catmer'. $n . $nr.'" value="'. $orderrow->Category . '">';
							echo '<input type="hidden" name="quanti'. $n . $nr.'" value="'. $orderrow->Qty . '">';
							echo '<input type="hidden" name="prelis_imp'. $n . $nr.'" value="'. $orderrow->Price . '">';
							echo '<input type="hidden" name="prelis_vatinc'. $n . $nr.'" value="'. $orderrow->PriceVATincl . '">';
							echo '<input type="hidden" name="codvat'. $n . $nr.'" value="'. $orderrow->VatCode . '">';
							echo '<input type="hidden" name="aliiva'. $n . $nr.'" value="'. $orderrow->VatAli . '">';
							echo '<input type="hidden" name="refid'. $n . $nr.'" value="'. $orderrow->Id . '">';
							echo '<input type="hidden" name="unimis'. $n . $nr.'" value="'. $orderrow->MeasureUnit . '">';
							echo '<input type="hidden" name="peso_specifico'. $n . $nr.'" value="'. $orderrow->ProductWeight . '">';
							echo '<input type="hidden" name="num_rows'. $n .'" value="'. $nr . '">';
							echo '<input type="hidden" name="product_parent_id'. $n . $nr .'" value="'. $orderrow->ParentId .'">';// se ci sono varianti questo è l'id del padre
							echo '<input type="hidden" name="characteristic_id'. $n . $nr .'" value="'. $orderrow->CharacteristicId .'">';
							echo '<input type="hidden" name="characteristic'. $n . $nr .'" value="'. $orderrow->Characteristic .'">';
							$nr++;
						}
						
						if(gaz_dbi_get_row($gTables['tesbro'], "numdoc", $order->Number, " OR  	ref_ecommerce_id_order  = '".$order->Numbering."'")){
						?>
						<span class="glyphicon glyphicon-ban-circle text-danger" title="Già scaricato"></span>
						<?php
						} else {
							?>
							<input type="checkbox" name="download<?php echo $n; ?>" value="download">
							<?php
						}
						?>
						<input type="hidden" name="num_orders" value="<?php echo $n; ?>">
						</td></tr>
						<?php
						
						
						
						$n++;
					} 
					
					?>
					
					<tr>
					<td style="text-align: right;">
					<input type="submit" name="Return"  value="Indietro">
					</td>
					<td></td><td></td>
					<td style="background-color:lightgreen;">
					<?php
					echo "Connesso a " . $ftp_host;
					?>
					</td>
					<td></td><td></td>
					<td>
					<input type="submit" name="conferma"  onClick="chkSubmit();" value="Scarica">
					</td>
					</tr>
				</tbody>	
			</table>	
	</form>
	<?php
	require("../../library/include/footer.php");
} else { // IL FILE INTERFACCIA NON ESISTE > ESCO
	?>
	<script>
	alert("<?php echo "Errore di connessione al file di interfaccia web = ",intval(substr($headers[0], 9, 3)); ?>");
	location.replace("<?php echo $_POST['ritorno']; ?>");
    </script>
	<?php
} 
?>