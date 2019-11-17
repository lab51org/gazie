<?php
/* ------------------------------------------------------------------------
  INTERFACCIA Download ordini da Joomla a GAzie
  ------------------------------------------------------------------------
  @Author    Antonio Germani
  @Website   http://www.lacasettabio.it
  @Copyright Copyright (C) 2018 - 2019 Antonio Germani All Rights Reserved.
  versione 1.0
  ------------------------------------------------------------------------ */

$resserver = gaz_dbi_get_row($gTables['company_config'], "var", "server");
$ftp_host= $resserver['val'];
$resuser = gaz_dbi_get_row($gTables['company_config'], "var", "user");
$ftp_user = $resuser['val'];
$respass = gaz_dbi_get_row($gTables['company_config'], "var", "pass");
$ftp_pass= $respass['val'];
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
$admin_aziend = gaz_dbi_get_row($gTables['admin'] . ' LEFT JOIN ' . $gTables['aziend'] . ' ON ' . $gTables['admin'] . '.' . $c_e . '= ' . $gTables['aziend'] . '.codice', "user_name", $_SESSION["user_name"]);
	
if (isset($_POST['conferma'])) { // se confermato
	
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
	    	
		$listin=$_POST['numlist'.$ord];
		$listinome=$_POST['numlistnome'.$ord];
		$includevat=$_POST['includevat'.$ord];
		
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
			if (intval($_POST['pariva'.$ord])>0){ // controllo esistenza cliente per partita iva
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
			$query = "SELECT * FROM " . $gTables['anagra'] . " WHERE ragso1 ='" . addslashes($_POST['ragso1'.$ord]) . "' AND ragso2 ='". addslashes($_POST['ragso2'.$ord]) . "'";
			$check = gaz_dbi_query($query);
			while ($row = $check->fetch_assoc()) {
				if (($check->num_rows > 0) && ($row['citspe']=$_POST['citspe'.$ord]) && ($row['indspe']=$_POST['indspe'.$ord])){
					$esiste=1;
					$cl = gaz_dbi_get_row($gTables['clfoco'], "id_anagra", $row['id']);
					$clfoco=$cl['codice'];
				}
			}
						
			If ($esiste==0) { //registro cliente se non esiste
					if ($_POST['country'.$ord]=="IT"){ // se la nazione è IT
						$lang="1";
					} else {
						$lang="0";
					}
					gaz_dbi_query("INSERT INTO " . $gTables['anagra'] . "(ragso1,ragso2,indspe,capspe,citspe,prospe,country,id_currency,id_language,telefo,codfis,pariva,fe_cod_univoco,e_mail,pec_email) VALUES ('" . addslashes($_POST['ragso1'.$ord]) . "', '" . addslashes($_POST['ragso2'.$ord]) . "', '". addslashes($_POST['indspe'.$ord]) ."', '".$_POST['capspe'.$ord]."', '". addslashes($_POST['citspe'.$ord]) ."', '". $_POST['prospe'.$ord] ."', '" . $_POST['country'.$ord]. "', '1', '".$lang."', '". $_POST['telefo'.$ord] ."', '". $_POST['codfis'.$ord] ."', '" . $_POST['pariva'.$ord] . "', '" . $_POST['fe_cod_univoco'.$ord] . "', '". $_POST['email'.$ord] . "', '". $_POST['pec_email'.$ord] . "')");
					
					gaz_dbi_query("INSERT INTO " . $gTables['clfoco'] . "(codice,id_anagra,descri,speban) VALUES ('". $clfoco . "', '" . $id_anagra . "', '" .addslashes($_POST['ragso1'.$ord])." ".addslashes($_POST['ragso2'.$ord]) . "', 'S')");
			}
			
			if ($_POST['order_discount_price'.$ord]>0){ // se il sito ha mandato uno sconto totale a valore calcolo lo sconto in percentuale da dare ad ogni rigo
				$lordo=$_POST['order_full_price'.$ord]+$_POST['order_discount_price'.$ord]-$_POST['speban'.$ord]-$_POST['traspo'.$ord];
				$netto=$lordo-$_POST['order_discount_price'.$ord];
				$percdisc= 100-(($netto/$lordo)*100);
			} else {
				$percdisc="";
			}
			
			if ($includevat=="true"){ // se il sito include l'iva la scorporo alle spese banca e trasporto
					$_POST['speban'.$ord]=$_POST['speban'.$ord] / 1.22;
					$_POST['traspo'.$ord]=$_POST['traspo'.$ord] / 1.22;
				}
							
		
			// registro testata ordine
			gaz_dbi_query("INSERT INTO " . $gTables['tesbro'] . "(tipdoc,seziva,print_total,datemi,numdoc,datfat,clfoco,pagame,listin,spediz,traspo,speban,caumag,expense_vat,initra,status,adminid) VALUES ('VOR', '1', '1', '" . $_POST['datemi'.$ord] . "', '" .$_POST['numdoc'.$ord] . "', '0000-00-00', '". $clfoco . "', '" .$_POST['pagame'.$ord]."', '". $listin . "', '".$_POST['spediz'.$ord]."', '". $_POST['traspo'.$ord] ."', '". $_POST['speban'.$ord] ."', '1', '". $_POST['codvatcost'.$ord]."', '" . $_POST['datemi'.$ord]. "', 'ONLINE-SHOP', '" . $admin_aziend['adminid'] . "')");
		
			// Gestione righi ordine					
			for ($row=0; $row<=$_POST['num_rows'.$ord]; $row++){
				
				// controllo se esiste l'articolo in GAzie 
				$ckart = gaz_dbi_get_row($gTables['artico'], "codice", substr($_POST['codice'.$ord.$row],0,15));
				$codart=$ckart['codice']; // se esiste ne prendo il codice come $codart
				if (!$ckart){ // se non esiste con il codice, controllo se esiste con il codice a barre
					$ckart = gaz_dbi_get_row($gTables['artico'], "barcode", substr($_POST['codice'.$ord.$row],0,64));
					$codart=$ckart['codice'];// se esiste ne prendo il codice come $codart
				} 
				if (!$ckart){ // se non esiste creo un nuovo articolo su gazie come servizio perché non ha magazzino
					if ($_POST['codvat'.$ord.$row]<1){ // se il sito non ha mandato il codice IVA dell'articolo ci metto quello che deve mandare come base aziendale riservato alle spese
						$_POST['codvat'.$ord.$row]=$_POST['codvatcost'.$ord];
						$_POST['aliiva'.$ord.$row]=$_POST['aliivacost'.$ord];
					}
					gaz_dbi_query("INSERT INTO " . $gTables['artico'] . "(codice,descri,good_or_service,unimis,catmer,".$listinome.",aliiva,codcon,adminid) VALUES ('". substr($_POST['codice'.$ord.$row],0,15) ."', '". addslashes($_POST['descri'.$ord.$row]) ."', '1', '" . $_POST['unimis'.$ord.$row] . "', '" .$_POST['catmer'.$ord.$row] . "', '". $_POST['prelis'.$ord.$row] ."', '".$_POST['codvat'.$ord.$row]."', '420000006', '" . $admin_aziend['adminid'] . "')");
					$codart= substr($_POST['codice'.$ord.$row],0,15);// se non esiste dopo averlo creato ne prendo il codice come $codart
				}
								
					if ($includevat=="true"){ // se è impostato iva compresa, scorporo l'iva al prezzo articoli
						$codvat=gaz_dbi_get_row($gTables['artico'], "codice", $codart);
						$aliiva=gaz_dbi_get_row($gTables['aliiva'], "codice", $codvat['aliiva']);
						$diviva=(($aliiva['aliquo']/100)+1);
						$_POST['prelis'.$ord.$row]=$_POST['prelis'.$ord.$row]/$diviva;
						$codvat=$codvat['aliiva'];
						$aliiva=$aliiva['aliquo'];
						 
					} else { // se è impostato iva esclusa non scorporo niente
						$codvat=gaz_dbi_get_row($gTables['artico'], "codice", substr($_POST['codice'.$ord.$row],0,15));
						$aliiva=gaz_dbi_get_row($gTables['aliiva'], "codice", $codvat['aliiva']);
						$codvat=$codvat['aliiva'];
						$aliiva=$aliiva['aliquo'];
						
					}
				
				// salvo rigo su database tabella rigbro
				gaz_dbi_query("INSERT INTO " . $gTables['rigbro'] . "(id_tes,codart,descri,unimis,quanti,prelis,sconto,codvat,codric,pervat,status) VALUES ('" . intval($id_tesbro) . "','" . $codart . "','" . addslashes($_POST['descri'.$ord.$row]) . "','". $_POST['unimis'.$ord.$row] . "','" . $_POST['quanti'.$ord.$row] . "','" . $_POST['prelis'.$ord.$row] . "', '".$percdisc."', '". $codvat. "', '420000006', '". $aliiva. "', 'ONLINE-SHOP')");
			}
						
			/*
			// Questo codice serviva per inserire le spese di trasporto e di incasso come righi. Adesso vengono inserite direttamente in tesbro!
			$ckart = gaz_dbi_get_row($gTables['artico'], "codice", "Spese");				
				if (!$ckart){ // se non esiste l'articolo 'Spese' in GAzie lo creo come servizio perché non movimenta il magazzino
				gaz_dbi_query("INSERT INTO " . $gTables['artico'] . "(codice,descri,good_or_service,unimis,catmer,web_price,aliiva,adminid) VALUES ('Spese', 'Spese', '1', 'n', '', '', '".$_POST['codvatcost'.$ord]."', '" . $admin_aziend['adminid'] . "')");
				}
			if ($_POST['speban'.$ord]>0) { // se ci sono spese di incasso registro un ulteriore rigo specifico per queste spese
				gaz_dbi_query("INSERT INTO " . $gTables['rigbro'] . "(id_tes,codart,descri,unimis,quanti,prelis,codvat,codric,pervat,status) VALUES ('" . intval($id_tesbro) . "',' Spese ',' Spese incasso ',' n.',' 1 ','" . $_POST['speban'.$ord] . "', '".$_POST['codvatcost'.$ord]."', '420000009', '22', 'ONLINE-SHOP')");				
			}
			if ($_POST['traspo'.$ord]>0) { // se ci sono spese di trasporto registro un ulteriore rigo specifico per queste spese
				gaz_dbi_query("INSERT INTO " . $gTables['rigbro'] . "(id_tes,codart,descri,unimis,quanti,prelis,codvat,codric,pervat,status) VALUES ('" . intval($id_tesbro) . "',' Spese ',' Spese trasporto ',' n.',' 1 ','" . $_POST['traspo'.$ord] . "', '".$_POST['codvatcost'.$ord]."', '420000009', '22', 'ONLINE-SHOP')");				
			}
			*/
			
			$id_tesbro++;
		}  
	}
	header("Location: " . "../../modules/vendit/report_broven.php?auxil=VOR");
    exit;
}
 
// imposto la connessione al server
$conn_id = ftp_connect($ftp_host);

// effettuo login con user e pass
$mylogin = ftp_login($conn_id, $ftp_user, $ftp_pass);

// controllo se la connessione è OK...
if ((!$conn_id) or (!$mylogin))
{ 
	?>
	<script>
	alert("<?php echo "Errore: connessione FTP a " . $ftp_host . " non riuscita!"; ?>");
	location.replace("<?php echo $_POST['ritorno']; ?>");
    </script>
	<?php
}

$access=base64_encode($ftp_pass);

// avvio il file di interfaccia presente nel sito web remoto
$headers = @get_headers($urlinterf.'?access='.$access);
if ( intval(substr($headers[0], 9, 3))==200){ // controllo se il file esiste o mi dà accesso

	$xml=simplexml_load_file($urlinterf.'?access='.$access) ;
	if (!$xml){
		?>
		<script>
		alert("<?php echo "Errore nella creazione del file xml"; ?>");
		location.replace("<?php echo $_POST['ritorno']; ?>");
		</script>
		<?php
	}
	?>
	<!DOCTYPE html>
	<link href="../../library/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
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
						echo '<input type="hidden" name="ragso1'. $n .'" value="'. $order->CustomerName . '">';
						echo '</td><td>';
						echo $order->CustomerSurname;
						echo '<input type="hidden" name="ragso2'. $n .'" value="'. $order->CustomerSurname . '">';
						echo '</td><td>';
						echo $order->CustomerCity ;
						echo '<input type="hidden" name="citspe'. $n .'" value="'. $order->CustomerCity .'">';
						echo '</td><td>';
						echo gaz_format_number($order->Total);
						echo '<input type="hidden" name="order_full_price'. $n .'" value="'. $order->Total .'">';
						echo '<input type="hidden" name="order_discount_price'. $n .'" value="'. $order->TotalDiscount .'">';
						echo '</td><td>';
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
						echo '<input type="hidden" name="spediz'. $n .'" value="'. $order->Carrier .'">';
						echo '<input type="hidden" name="codvatcost'. $n .'" value="'. $order->CostVatCode .'">';
						echo '<input type="hidden" name="aliivacost'. $n .'" value="'. $order->CostVatAli .'">';
						echo '<input type="hidden" name="email'. $n .'" value="'. $order->CustomerEmail .'">';
						echo '<input type="hidden" name="pec_email'. $n .'" value="'. $order->CustomerPecEmail .'">';
						echo '<input type="hidden" name="fe_cod_univoco'. $n .'" value="'. $order->CustomerCodeFattEl .'">';
						foreach($xml->Documents->Document[$n]->Rows->children() as $orderrow) { // carico le righe degli ordini
							echo '<input type="hidden" name="codice'. $n . $nr.'" value="'. $orderrow->Code . '">';
							echo '<input type="hidden" name="descri'. $n . $nr.'" value="'. $orderrow->Description . '">';
							echo '<input type="hidden" name="catmer'. $n . $nr.'" value="'. $orderrow->Category . '">';
							echo '<input type="hidden" name="quanti'. $n . $nr.'" value="'. $orderrow->Qty . '">';
							echo '<input type="hidden" name="prelis'. $n . $nr.'" value="'. $orderrow->Price . '">';
							echo '<input type="hidden" name="codvat'. $n . $nr.'" value="'. $orderrow->VatCode . '">';
							echo '<input type="hidden" name="aliiva'. $n . $nr.'" value="'. $orderrow->VatAli . '">';
							
							echo '<input type="hidden" name="unimis'. $n . $nr.'" value="'. $orderrow->MeasureUnit . '">';
							echo '<input type="hidden" name="num_rows'. $n .'" value="'. $nr . '">';
							$nr++;
						}
						
						if(gaz_dbi_get_row($gTables['tesbro'], "numdoc", $order->Number)){
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
} else { // IL FILE INTERFACCIA NON ESISTE > ESCO
	ftp_quit($conn_id);
	?>
	<script>
	alert("<?php echo "Errore di connessione al file di interfaccia web = ",intval(substr($headers[0], 9, 3)); ?>");
	location.replace("<?php echo $_POST['ritorno']; ?>");
    </script>
	<?php
	exit;
}
// chiudo la connessione FTP 
ftp_quit($conn_id); 
?>
                            