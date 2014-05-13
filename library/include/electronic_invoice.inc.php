<?php
 /* $
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2014 - Antonio De Vincentiis Montesilvano (PE)
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

require("../../library/include/calsca.inc.php");

class DocContabVars
{
    function setVars($gTables, $tesdoc, $testat, $tableName,$ecr=false)
    {
        $this->ecr=$ecr;
        $this->gTables = $gTables;
        $admin_aziend = gaz_dbi_get_row($gTables['aziend'], 'codice', $_SESSION['enterprise_id']);
        $this->azienda = $admin_aziend;
        $this->user = gaz_dbi_get_row($gTables['admin'], 'Login', $_SESSION['Login']);
        $this->pagame = gaz_dbi_get_row($gTables['pagame'], "codice", $tesdoc['pagame']);
        $this->banapp = gaz_dbi_get_row($gTables['banapp'],"codice",$tesdoc['banapp']);
        $anagrafica = new Anagrafica();
        $this->banacc = $anagrafica->getPartner($this->pagame['id_bank']);
        $this->vettor = gaz_dbi_get_row($gTables['vettor'], "codice", $tesdoc['vettor']);
        $this->tableName = $tableName;
        $this->intesta1 = $admin_aziend['ragso1'];
        $this->intesta1bis = $admin_aziend['ragso2'];
        $this->intesta2 = $admin_aziend['indspe'].' '.sprintf("%05d",$admin_aziend['capspe']).' '.$admin_aziend['citspe'].' ('.$admin_aziend['prospe'].')';
        $this->intesta3 = 'Tel.'.$admin_aziend['telefo'].' ';
        $this->aziendTel = $admin_aziend['telefo'];
        $this->aziendFax = $admin_aziend['fax'];
        $this->codici = '';
        if ($admin_aziend['codfis'] != '') {
            $this->codici .= 'C.F. '.$admin_aziend['codfis'].' ';
        }
        if ($admin_aziend['pariva']) {
            $this->codici .= 'P.I. '.$admin_aziend['pariva'].' ';
        }
        if ($admin_aziend['rea']) {
            $this->codici .= 'R.E.A. '.$admin_aziend['rea'];
        }
        $this->intesta4 = $admin_aziend['e_mail'];
        $this->intesta5 = $admin_aziend['sexper'];
        $this->colore = $admin_aziend['colore'];
        $this->decimal_quantity = $admin_aziend['decimal_quantity'];
        $this->decimal_price = $admin_aziend['decimal_price'];
        $this->logo = $admin_aziend['image'];
        $this->link = $admin_aziend['web_url'];
        $this->perbollo = 0;
        $this->iva_bollo = gaz_dbi_get_row($gTables['aliiva'], "codice", $admin_aziend['ivabol']);
        $this->client = $anagrafica->getPartner($tesdoc['clfoco']);
        $this->cliente1 = $this->client['ragso1'];
        $this->cliente2 = $this->client['ragso2'];
        $this->cliente3 = $this->client['indspe'];
        if (!empty($this->client['citspe'])) {
           $this->cliente4 = sprintf("%05d",$this->client['capspe']).' '.strtoupper($this->client['citspe']).' '.strtoupper($this->client['prospe']);
        } else {
           $this->cliente4 = '';
        }
        $country = gaz_dbi_get_row($gTables['country'], "iso", $this->client['country']);
        if ($this->client['country'] != 'IT') {
            $this->cliente4b = strtoupper($country['istat_name']);
        } else {
			$this->cliente4b = ''; 
		}
        if (!empty($this->client['pariva'])){
           $this->cliente5 = 'P.I. '.$this->client['pariva'].' ';
        } else {
           $this->cliente5 = '';
        }
        if (!empty($this->client['pariva'])){ //se c'e' la partita iva
            if (!empty($this->client['codfis']) and $this->client['codfis'] == $this->client['pariva']) {
                $this->cliente5 = 'C.F. e P.I. '.$this->client['codfis'];
            } elseif(!empty($this->client['codfis']) and $this->client['codfis'] != $this->client['pariva']) {
                $this->cliente5 = 'C.F. '.$this->client['codfis'].' P.I. '.$this->client['pariva'];
            } else { //per es. se non c'e' il codice fiscale
                $this->cliente5 = ' P.I. '.$this->client['pariva'];
            }
        } else { //se  NON c'e' la partita iva
            $this->cliente5 = '';
            if (!empty($this->client['codfis'])) {
                $this->cliente5 = 'C.F. '.$this->client['codfis'];
            }
        }
        // variabile e' sempre un array
        $this->id_agente = gaz_dbi_get_row($gTables['agenti'],'id_agente',$tesdoc['id_agente']);
        $this->rs_agente = $anagrafica->getPartner($this->id_agente['id_fornitore']);
        $this->name_agente = substr($this->rs_agente['ragso1']." ".$this->rs_agente['ragso2'],0,47);
        if ((isset($tesdoc['id_des'])) and ($tesdoc['id_des'] > 0)) {
            $this->partner_dest = $anagrafica->getPartnerData($tesdoc['id_des']);
            $this->destinazione = substr($this->partner_dest['ragso1']." ".$this->partner_dest['ragso2'],0,45);
            $this->destinazione .= "\n".substr($this->partner_dest['indspe'],0,45);
            $this->destinazione .= "\n".substr($this->partner_dest['capspe']." ".$this->partner_dest['citspe']." (".$this->partner_dest['prospe'].")",0,45);
        } else {
            if (isset($tesdoc['destin']) and is_array($tesdoc['destin'])) {
                $this->destinazione = $tesdoc['destin'];
            } elseif (isset($tesdoc['destin']) and is_string($tesdoc['destin'])) {
                $destino = preg_split("/[\r\n]+/i",$tesdoc['destin'],3);
                $this->destinazione = substr($destino[0],0,45);
                foreach ($destino as $key => $value) {
                    if ($key == 1){
                        $this->destinazione .= "\n".substr($value,0,45)."\n";
                    } elseif($key > 1) {
                        $this->destinazione .= substr(preg_replace("/[\r\n]+/i",' ',$value),0,45);
                    }
                }
            } else {
                $this->destinazione = '';
            }
        }

        $this->clientSedeLegale = ((trim($this->client['sedleg']) != '') ? preg_split("/\n/", trim($this->client['sedleg'])) : array());

        if (isset($tesdoc['c_a'])) {
           $this->c_Attenzione = $tesdoc['c_a'];
        } else {
           $this->c_Attenzione = '';
        }
        $this->client = $anagrafica->getPartner($tesdoc['clfoco']);
        $this->tesdoc = $tesdoc;
        $this->min = substr($tesdoc['initra'],14,2);
        $this->ora = substr($tesdoc['initra'],11,2);
        $this->day = substr($tesdoc['initra'],8,2);
        $this->month = substr($tesdoc['initra'],5,2);
        $this->year = substr($tesdoc['initra'],0,4);
        $this->trasporto=$tesdoc['traspo'];
        $this->testat = $testat;

        $this->docRelNum  = $this->tesdoc["numdoc"];    // Numero del documento relativo
        $this->docRelDate = $this->tesdoc["datemi"];    // Data del documento relativo
        
        switch ( $tesdoc["tipdoc"] ) {
            case "FAD":
            case "FAI":
                $this->docRelNum  = $this->tesdoc["numfat"];
                $this->docRelDate = $this->tesdoc["datfat"];
                break;
            case "DDT":
            case "DDL":
            case "DDR":
            default:
                $this->docRelNum  = $this->tesdoc["numdoc"];    // Numero del documento relativo
                $this->docRelDate = $this->tesdoc["datemi"];    // Data del documento relativo
        }
        
        
    }


    function getRigo()
    {
        $from =  $this->gTables[$this->tableName].' AS rows
                 LEFT JOIN '.$this->gTables['aliiva'].' AS vat
                 ON rows.codvat=vat.codice';
        $rs_rig = gaz_dbi_dyn_query('rows.*,vat.tipiva AS tipiva',$from, "rows.id_tes = ".$this->testat,"id_tes DESC, id_rig");
        $this->riporto =0.00;
        $this->ritenuta=0.00;
        $results = array();
        while ($rigo = gaz_dbi_fetch_array($rs_rig)) {
            if ($rigo['tiprig'] <= 1) {
                $rigo['importo'] = CalcolaImportoRigo($rigo['quanti'], $rigo['prelis'], $rigo['sconto']);
                $v_for_castle = CalcolaImportoRigo($rigo['quanti'], $rigo['prelis'], array($rigo['sconto'],$this->tesdoc['sconto']));
                if ($rigo['tiprig'] == 1) {
                    $rigo['importo'] = CalcolaImportoRigo(1,$rigo['prelis'],0);
                    $v_for_castle = CalcolaImportoRigo(1, $rigo['prelis'],$this->tesdoc['sconto']);
                }
                if (!isset($this->castel[$rigo['codvat']])) {
                    $this->castel[$rigo['codvat']] = 0;
                    if ($rigo['tipiva']!='C' && $rigo['tipiva']!='S' ) {
                       $this->decalc_castle[$rigo['codvat']] = 0.00;
                    }
                }
                $this->castel[$rigo['codvat']] += $v_for_castle;
                if ($rigo['tipiva']!='C' && $rigo['tipiva']!='S' ) {
                   $this->decalc_castle[$rigo['codvat']] += $v_for_castle;
                   $this->totimp_decalc += $v_for_castle;
                }
                $this->totimp_body += $rigo['importo'];
                $this->ritenuta += round($rigo['importo']*$rigo['ritenuta']/100,2);
                $this->totimp_doc += $v_for_castle;
            } elseif ($rigo['tiprig']>5 && $rigo['tiprig']<9) {
               $body_text = gaz_dbi_get_row($this->gTables['body_text'], "id_body",$rigo['id_body_text']);
               $rigo['descri'] = $body_text['body_text'];
            } elseif ($rigo['tiprig'] == 3) {
               $this->riporto += $rigo['prelis'];
            }
            $results[] = $rigo;
            //creo il castelletto IVA ma solo se del tipo normale o forfait
        }
        return $results;
    }

    function setTotal($totTrasporto=0)
    {
        $bolli = new Compute();
        $this->totivafat = 0.00;
        $this->totimpfat = 0.00;
        $this->totimpmer = 0.00;
        $this->tot_ritenute = $this->ritenuta;
        $this->impbol = 0.00;
        $this->totriport = $this->riporto;
        $this->speseincasso = $this->tesdoc['speban'] * $this->pagame['numrat'];
        $this->cast = array();
        if (!isset($this->decalc_castle)){
            $this->decalc_castle= array();
        }
        if (!isset($this->castel)){
            $this->castel= array();
        }
        if (!isset($this->totimp_body)){
            $this->totimp_body=0;
        }
        $this->totimpmer = $this->totimp_body;
        $this->totimp_body=0;
        if (!isset($this->totimp_doc)){
            $this->totimp_doc=0;
        }
        $this->totimpfat = $this->totimp_doc;
        $this->totimp_doc = 0;
        $somma_spese = $totTrasporto + $this->speseincasso + $this->tesdoc['spevar'];
        $last=count($this->castel);
        $acc_val=$somma_spese;
        foreach ($this->castel as $k=>$v) {
            $vat = gaz_dbi_get_row($this->gTables['aliiva'],"codice",$k);
            if (isset($this->decalc_castle[$k])) {
               if ($last == 1) {
                  $v += $acc_val;
                  $this->totimpfat += $acc_val;
               } else {
                  $decalc=round($somma_spese*$v/$this->totimpmer,2);
                  $v += $decalc;
                  $this->totimpfat += $decalc;
                  $acc_val-=$decalc;
               }
               $last--;
            }
            $ivacast = round($v*$vat['aliquo'])/ 100;
            $this->totivafat += $ivacast;
            $this->cast[$k]['impcast'] = $v;
            $this->cast[$k]['ivacast'] = $ivacast;
            $this->cast[$k]['descriz'] = $vat['descri'];
        }
        //se il pagamento e' del tipo TRATTA calcolo i bolli da addebitare per l'emissione dell'effetto
        if ($this->pagame['tippag'] == 'T' or $this->pagame['tippag'] == 'R') {
           if ($this->pagame['tippag'] == 'T') {
              $this->impbol = $bolli->stampTax($this->totimpfat+$this->totriport+$this->totivafat-$this->tot_ritenute, $this->tesdoc['stamp'],$this->tesdoc['round_stamp']*$this->pagame['numrat']);
           } elseif($this->pagame['tippag'] == 'R') {
              $this->impbol = $this->tesdoc['stamp'];
           }
        }
        $this->riporto=0;
        $this->ritenute=0;
        $this->castel = array();
        $this->decalc_castle= array();
    }
}


function create_XML_invoice($testata, $gTables, $rows='rigdoc', $dest=false)
{
    $docVars = new DocContabVars();
    $docVars->setVars($gTables, $testata, $testata['id_tes'], $rows, false);

    
    $domDoc = new DOMDocument;
    $domDoc->load("../../library/include/template_fae.xml");
	   
     //per il momento sono singole chiamate xpath a regime e' possibile usare un array associativo da passare ad una funzione
	   $xpath     = new DOMXPath($domDoc);
     $results = $xpath->query("//FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdPaese")->item(0);		
	   $attrVal = $domDoc->createTextNode('IT');	   
	   $results->appendChild($attrVal);
	
  
     $results = $xpath->query("//FatturaElettronicaHeader/DatiTrasmissione/ProgressivoInvio")->item(0);		
	   $attrVal = $domDoc->createTextNode( trim( $docVars->docRelNum ));	   
	   $results->appendChild($attrVal);
     
     $results = $xpath->query("//FatturaElettronicaHeader/DatiTrasmissione/FormatoTrasmissione")->item(0);		
	   $attrVal = $domDoc->createTextNode( "SDI10" );	   
	   $results->appendChild($attrVal);
  
     $id_test='12345678910';
     $results = $xpath->query("//FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdCodice")->item(0);		
	   $attrVal = $domDoc->createTextNode($id_test);	   
	   $results->appendChild($attrVal);	

     $results = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdPaese")->item(0);		
	   $attrVal = $domDoc->createTextNode("IT");	   
	   $results->appendChild($attrVal);

     //il IdCodice iva e' la partita iva?
     $results = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdCodice")->item(0);		
	   $attrVal = $domDoc->createTextNode(trim($docVars->azienda['pariva']));	   
	   $results->appendChild($attrVal);


     $results = $xpath->query("//FatturaElettronicaHeader/DatiTrasmissione/CodiceDestinatario")->item(0);		
	   $attrVal = $domDoc->createTextNode( trim( $docVars->client['fe_cod_univoco'] ));	   
	   $results->appendChild($attrVal);     
     
     
     $el = $domDoc->createElement("CodiceFiscale",trim( $docVars->client['codfis'] ));					 
     $results = $xpath->query("//CessionarioCommittente/DatiAnagrafici")->item(0);
     $results->appendChild($el);
        
     
     $results = $xpath->query("//CessionarioCommittente/DatiAnagrafici/Anagrafica/Denominazione")->item(0);		
	   $attrVal = $domDoc->createTextNode( trim( $docVars->client['ragso1'] ." " . $docVars->client['ragso2'] ));	   
	   $results->appendChild($attrVal);	

	   $results = $xpath->query("//CessionarioCommittente/Sede/Indirizzo")->item(0);		
	   $attrVal = $domDoc->createTextNode( trim( $docVars->client['indspe'] ));	   
	   $results->appendChild($attrVal);	
     
     
     $el = $domDoc->createElement("Provincia",trim( $docVars->client['prospe'] ));					 
     $results = $xpath->query("//CessionarioCommittente/Sede")->item(0);
     $results->appendChild($el);
     
     
     $results = $xpath->query("//CessionarioCommittente/Sede/Comune")->item(0);		
	   $attrVal = $domDoc->createTextNode( trim( $docVars->client['citspe'] ));	   
	   $results->appendChild($attrVal);
     
	   $results = $xpath->query("//CessionarioCommittente/Sede/CAP")->item(0);		
	   $attrVal = $domDoc->createTextNode( trim( $docVars->client['capspe'] ));	   
	   $results->appendChild($attrVal);
     
	   $results = $xpath->query("//CessionarioCommittente/Sede/Nazione")->item(0);		
	   $attrVal = $domDoc->createTextNode( trim( $docVars->client['country'] ));	   
	   $results->appendChild($attrVal);
     
     //sono sempre tutte fatture?
     $results = $xpath->query("//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/TipoDocumento")->item(0);		
	   $attrVal = $domDoc->createTextNode( "TD01" );	   
	   $results->appendChild($attrVal);
     
     //sempre in euro?
     $results = $xpath->query("//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Divisa")->item(0);		
	   $attrVal = $domDoc->createTextNode( "EUR" );	   
	   $results->appendChild($attrVal);
     
     $results = $xpath->query("//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Data")->item(0);		
	   $attrVal = $domDoc->createTextNode( trim( $docVars->docRelDate ));	   
	   $results->appendChild($attrVal);
          
     $results = $xpath->query("//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Numero")->item(0);		
	   $attrVal = $domDoc->createTextNode( trim( $docVars->docRelNum ));	   
	   $results->appendChild($attrVal);          
          

     //regime fiscale RF01 valido per il regime fiscale ordinario
     $results = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/RegimeFiscale")->item(0);		
     $attrVal = $domDoc->createTextNode( trim($docVars->azienda['fiscal_reg']));     	   
	   $results->appendChild($attrVal);


     $results = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Sede/Indirizzo")->item(0);		
     $attrVal = $domDoc->createTextNode( trim($docVars->azienda['indspe']));     	   
	   $results->appendChild($attrVal);

     $results = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Sede/CAP")->item(0);		
     $attrVal = $domDoc->createTextNode( trim($docVars->azienda['capspe']));     	   
	   $results->appendChild($attrVal);
     
     $results = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Sede/Comune")->item(0);		
     $attrVal = $domDoc->createTextNode( trim($docVars->azienda['citspe']));     	   
	   $results->appendChild($attrVal);

     $results = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Sede/Nazione")->item(0);		
     $attrVal = $domDoc->createTextNode( trim($docVars->azienda['country']));     	   
	   $results->appendChild($attrVal);
          

     $results = $xpath->query("//FatturaElettronicaBody/DatiBeniServizi")->item(0);		
	    //$attrVal = $domDoc->createTextNode('IT');	   
	    //$results->appendChild($attrVal);

	   
		 //elenco beni in fattura  
		 $lines = $docVars->getRigo();
     


/////////////////////

       $n_linea = 1;
       while (list($key, $rigo) = each($lines)) {

                switch($rigo['tiprig']) {
                case "0":
                    $el = $domDoc->createElement("DettaglioLinee","");					 
					
					$el1= $domDoc->createElement("NumeroLinea", $n_linea);
					$el->appendChild($el1);
					
					$el1= $domDoc->createElement("Descrizione", $rigo['descri']);
					$el->appendChild($el1);

					$el1= $domDoc->createElement("UnitaMisura", $rigo['unimis']);
					$el->appendChild($el1); 
					
					$el1= $domDoc->createElement("Quantita", $rigo['quanti']);
					$el->appendChild($el1); 
					 
					$el1= $domDoc->createElement("PrezzoTotale", gaz_format_number($rigo['importo']));
					$el->appendChild($el1);
					 
					$el1= $domDoc->createElement("AliquotaIVA", $rigo['pervat']);
					$el->appendChild($el1);
					 
					$results->appendChild($el);
					$n_linea = $n_linea+1;
				/*
					$this->Cell(25, 5, $rigo['codart'],1,0,'L');
                    $this->Cell(80, 5, $rigo['descri'],1,0,'L');
                    $this->Cell(7, 5, $rigo['unimis'],1,0,'C');
                    $this->Cell(16, 5, gaz_format_quantity($rigo['quanti'],1,$this->decimal_quantity),1,0,'R');
                    $this->Cell(18, 5, number_format($rigo['prelis'],$this->decimal_price,',',''),1,0,'R');
                    if ($rigo['sconto']>0) {
                       $this->Cell(8, 5,  number_format($rigo['sconto'],1,',',''),1,0,'C');
                    } else {
                       $this->Cell(8, 5, '',1,0,'C');
                    }
                    $this->Cell(20, 5, gaz_format_number($rigo['importo']),1,0,'R');
                    $this->Cell(12, 5, gaz_format_number($rigo['pervat']),1,1,'R');
					*/

					
					
                    break;
                case "1":
                    /*
					$this->Cell(25, 5, $rigo['codart'],1,0,'L');
                    $this->Cell(80, 5, $rigo['descri'],1,0,'L');
                    $this->Cell(49, 5, '',1);
                    $this->Cell(20, 5, gaz_format_number($rigo['importo']),1,0,'R');
                    $this->Cell(12, 5, gaz_format_number($rigo['pervat']),1,1,'R');
					*/
                    break;
                case "2":
                    /*
					$this->Cell(25,5,'','L');
                    $this->Cell(80,5,$rigo['descri'],'LR',0,'L');
                    $this->Cell(81,5,'','R',1);
					*/
                    break;
                case "3":
                    /*
					$this->Cell(25,5,'',1,0,'L');
                    $this->Cell(80,5,$rigo['descri'],'B',0,'L');
                    $this->Cell(49,5,'','B',0,'L');
                    $this->Cell(20,5,gaz_format_number($rigo['prelis']),1,0,'R');
                    $this->Cell(12,5,'',1,1,'R');
					*/
                    break;
                case "6":
                case "8":
                    /*
					$this->writeHtmlCell(186,6,10,$this->GetY(),$rigo['descri'],1,1);
					*/
                    break;
                }
                if ($rigo['ritenuta']>0) {
                    /*
					$this->Cell(154, 5,'Ritenuta d\'acconto al '.gaz_format_number($rigo['ritenuta']).'%','LB',0,'R');
                    $this->Cell(20, 5,gaz_format_number(round($rigo['importo']*$rigo['ritenuta']/100,2)),'RB',0,'R');
                    $this->Cell(12, 5,'',1,1,'R');
					*/
                }
        }



////////////////////		     
     
       //occorre effettuare alcune validazioni sul numero e sull'id
		   $nome_file = "IT" .  $id_test . "_" . trim( $docVars->docRelNum );
       //rendere dinamico il nome del file    
       header("Content-type: text/plain");
       header("Content-Disposition: attachment; filename=". $nome_file .".xml");
       print $domDoc->saveXML();

	     //echo $domDoc->saveXML();     
    
}


?>
