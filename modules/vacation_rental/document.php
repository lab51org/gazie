<?php

/*
--------------------------------------------------------------------------
  GAzie - MODULO 'VACATION RENTAL'
  Copyright (C) 2022-20223 - Antonio Germani, Massignano (AP)
  (http://www.programmisitiweb.lacasettabio.it)
  Ogni diritto è riservato.
  E' possibile usare questo modulo solo dietro autorizzazione dell'autore
  --------------------------------------------------------------------------
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2022 - Antonio De Vincentiis Montesilvano (PE)
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

class DocContabVars {

    function setData($gTables, $tesdoc, $testat, $tableName, $ecr = false, $genTables="", $azTables="") {

        $link=$GLOBALS['link'];
        global $gazie_locale;
        $this->gazTimeFormatter = new IntlDateFormatter($gazie_locale,IntlDateFormatter::FULL,IntlDateFormatter::FULL);

        $IDaz=preg_replace("/[^1-9]/", "", $azTables );

        $sql = "SELECT * FROM ".$genTables."aziend"." WHERE codice = '".$IDaz."' LIMIT 1";
        if ($result = mysqli_query($link, $sql)) {
          $admin_aziend = mysqli_fetch_assoc($result);

        } else {
          echo "Error: " . $sql . "<br>" . mysqli_error($link);
        }

        $this->ecr = $ecr;
        $this->gTables = $gTables;


        $sql = "SELECT * FROM ".$azTables."company_config"." WHERE var = 'layout_pos_logo_on_doc' LIMIT 1";
        if ($result = mysqli_query($link, $sql)) {
          $company = mysqli_fetch_assoc($result);
        }else{
          echo "Error: " . $sql . "<br>" . mysqli_error($link);
        }
        $this->layout_pos_logo_on_doc = $company['val'];

        $sql = "SELECT * FROM ".$azTables."company_config"." WHERE var = 'descriptive_last_row' LIMIT 1";
        if ($result = mysqli_query($link, $sql)) {
          $company = mysqli_fetch_assoc($result);
        }else{
          echo "Error: " . $sql . "<br>" . mysqli_error($link);
        }
        $this->descriptive_last_row = trim($company['val']);

         $sql = "SELECT * FROM ".$azTables."company_config"." WHERE var = 'descriptive_last_ddt' LIMIT 1";
        if ($result = mysqli_query($link, $sql)) {
          $company = mysqli_fetch_assoc($result);
        }else{
          echo "Error: " . $sql . "<br>" . mysqli_error($link);
        }
        $this->descriptive_last_ddt = $company['val'];
        $this->show_artico_composit = 0;
        if (isset($_SESSION["user_name"])){// se sono dentro GAzie cioè ho session valorizzato
          $this->user = gaz_dbi_get_row($gTables['admin'], "user_name", $_SESSION["user_name"]);
        }else{// se sono nel frontend prendo il primo amministratore
			$sql = "SELECT * FROM ".$genTables."admin"." WHERE company_id = ".$IDaz." LIMIT 1";
			if ($result = mysqli_query($link, $sql)) {
				$this->user = mysqli_fetch_assoc($result);
			}else{
				echo "Error: " . $sql . "<br>" . mysqli_error($link);
			}
		}
        $sql = "SELECT * FROM ".$azTables."pagame"." WHERE codice = '".$tesdoc['pagame']."' LIMIT 1";
        if ($result = mysqli_query($link, $sql)) {
          $rescau = mysqli_fetch_assoc($result);
        }else{
          echo "Error: " . $sql . "<br>" . mysqli_error($link);
        }
        $this->pagame = $rescau;

        if (isset($tesdoc['caumag']) && (!is_null($tesdoc['caumag']))) {

            //$this->caumag = gaz_dbi_get_row($gTables['caumag'], "codice", $tesdoc['caumag']);

        }
         $sql = "SELECT * FROM ".$azTables."banapp"." WHERE codice = '".$tesdoc['banapp']."' LIMIT 1";
        if ($result = mysqli_query($link, $sql)) {
          $resban = mysqli_fetch_assoc($result);
        }else{
          echo "Error: " . $sql . "<br>" . mysqli_error($link);
        }

        $banapp = $resban;
        $this->banapp =($banapp)?$banapp:array('descri'=>'');
       // $anagrafica = new Anagrafica();

		//commentato perché nel frontend mi da errore in quanto la classe Anagrafica sta in function.inc e getPartner usa l'sql di gazie

        //$this->banacc =($this->pagame)?$anagrafica->getPartner($this->pagame['id_bank']):'';
		if ($this->pagame){
			$sql = "SELECT * FROM ". $azTables."clfoco" . " LEFT JOIN " . $genTables."anagra" . " ON " . $azTables."clfoco" . ".id_anagra = " . $genTables."anagra" . ".id WHERE codice = '".$this->pagame['id_bank']."' LIMIT 1";
			if ($result = mysqli_query($link, $sql)) {
			  $this->banacc = mysqli_fetch_array($result);
			}else{
			  echo "Error: " . $sql . "<br>" . mysqli_error($link);
			}
		}else{
			$this->banacc="";
		}

        $this->vettor ="";
        $this->tableName = $tableName;
        $this->intesta1 = $admin_aziend['ragso1'];
        $this->intesta1bis = $admin_aziend['ragso2'];
        $this->intesta2 = $admin_aziend['indspe'] . ' ' . sprintf("%05d", $admin_aziend['capspe']) . ' ' . $admin_aziend['citspe'] . ' (' . $admin_aziend['prospe'] . ')';
        $this->intesta3 = 'Tel.' . $admin_aziend['telefo'] . ' ';
        $this->aziendTel = $admin_aziend['telefo'];
        $this->aziendFax = $admin_aziend['fax'];
        $this->codici = '';
        if ($admin_aziend['codfis'] != '') {
            $this->codici .= 'C.F. ' . $admin_aziend['codfis'] . ' ';
        }
        if ($admin_aziend['pariva']) {
            $this->codici .= 'P.I. ' . $admin_aziend['pariva'] . ' ';
        }
        if (strlen($admin_aziend['REA_ufficio'])>1 && strlen($admin_aziend['REA_numero'])>3 ) {
            $this->codici .= 'R.E.A. ' . $admin_aziend['REA_ufficio'].' '.$admin_aziend['REA_numero'];
        }

        $this->intesta4 = $admin_aziend['e_mail'];
        $this->intesta5 = $admin_aziend['sexper'];
        $this->colore = $admin_aziend['colore'];
        $this->decimal_quantity = $admin_aziend['decimal_quantity'];
        $this->decimal_price = $admin_aziend['decimal_price'];
        $this->logo = $admin_aziend['image'];
        $this->link = $admin_aziend['web_url'];
        // leggo la sede legale dell'azienda
        $this->sedelegale = $admin_aziend['sedleg'];
        $this->perbollo = 0;

        if (isset ($tesdoc['taxstamp_vat']) && intval($tesdoc['taxstamp_vat'])>0){
         $sql = "SELECT * FROM ".$azTables."aliiva"." WHERE codice = '".$tesdoc['taxstamp_vat']."' LIMIT 1";
        if ($result = mysqli_query($link, $sql)) {
          $resbol = mysqli_fetch_assoc($result);
        }else{
          echo "Error: " . $sql . "<br>" . mysqli_error($link);
        }
        $this->iva_bollo = $resbol;
        }else{
           $this->iva_bollo = 0;
        }

        //$this->client = $anagrafica->getPartner($tesdoc['clfoco']);
		$sql = "SELECT * FROM ". $azTables."clfoco" . " LEFT JOIN " . $genTables."anagra" . " ON " . $azTables."clfoco" . ".id_anagra = " . $genTables."anagra" . ".id WHERE codice = '".$tesdoc['clfoco']."' LIMIT 1";
        if ($result = mysqli_query($link, $sql)) {
          $this->client = mysqli_fetch_array($result);
        }else{
          echo "Error: " . $sql . "<br>" . mysqli_error($link);
        }

		if(!$this->client){
			$this->client=['ragso1'=>'Anonimo','ragso2'=>'','pec_email'=>'','fe_cod_univoco'=>'','fe_cod_univoco'=>'','indspe'=>'','citspe'=>'','country'=>'IT','capspe'=>'','prospe'=>'','pariva'=>'','pariva'=>'','codfis'=>'','sedleg'=>'','fiscal_rapresentative_id'=>''];
		}
        if ( $this->client['country']!=="IT" ) {
            $this->descri_partner = 'Customer';
        } else {
            $this->descri_partner = 'Cliente';
        }

        if (substr($tesdoc['clfoco'], 0, 3) == $admin_aziend['masfor']) {
            $this->descri_partner = 'Fornitore';
        }
        $this->codice_partner = intval(substr($tesdoc['clfoco'], 3, 6));
        $this->cod_univoco = $this->client['fe_cod_univoco'];
        $this->pec_cliente = $this->client['pec_email'];
        $this->cliente1 = $this->client['ragso1'];
        $this->cliente2 = $this->client['ragso2'];
        $this->cliente3 = $this->client['indspe'];
        if (!empty($this->client['citspe'])) {
			if ($this->client['country'] == 'IT') {
				$this->client['capspe'] = sprintf("%05d",$this->client['capspe']);
				$this->cliente4 = (($this->client['capspe']=='00000') ? '' : $this->client['capspe'].' ') . strtoupper($this->client['citspe']) . ' ' . strtoupper($this->client['prospe']);
			} else {
				$this->cliente4 = (empty($this->client['capspe']) ? '' : $this->client['capspe'].' ') . strtoupper($this->client['citspe']) . ' ' . strtoupper($this->client['prospe']);
			}
        } else {
            $this->cliente4 = '';
        }

          $sql = "SELECT * FROM ".$genTables."country"." WHERE iso = '".$this->client['country']."' LIMIT 1";
        if ($result = mysqli_query($link, $sql)) {
          $rescou = mysqli_fetch_assoc($result);
        }else{
          echo "Error: " . $sql . "<br>" . mysqli_error($link);
        }
        $country = $rescou;

        if ($this->client['country'] != 'IT') {
            $this->cliente4b = strtoupper($country['istat_name']);
        } else {
            $this->cliente4b = 'Italy';
        }
        if (!empty($this->client['pariva'])) {
            $this->cliente5 = 'P.I. ' . $this->client['pariva'] . ' ';
        } else {
            $this->cliente5 = '';
        }
        if ( $this->client['country']!="IT" && $this->client['country']!="" ) {
            $this->cliente5 = 'vat num. ' . $this->client['country'] .$this->client['codfis'];
        } else if (!empty($this->client['pariva'])) { //se c'e' la partita iva
            if (!empty($this->client['codfis']) and $this->client['codfis'] == $this->client['pariva']) {
                $this->cliente5 = 'C.F. e P.I. ' . $this->client['country'] . $this->client['codfis'];
            } elseif (!empty($this->client['codfis']) and $this->client['codfis'] != $this->client['pariva']) {
                $this->cliente5 = 'C.F. ' . $this->client['codfis'] . ' P.I. ' . $this->client['country'] . $this->client['pariva'];
            } else { //per es. se non c'e' il codice fiscale
                $this->cliente5 = ' P.I. ' . $this->client['country'] . $this->client['pariva'];
            }
        } else { //se  NON c'e' la partita iva
            $this->cliente5 = '';
            if (!empty($this->client['codfis'])) {
                $this->cliente5 = 'C.F. ' . $this->client['codfis'];
            }
        }
        // variabile e' sempre un array
        $sql = "SELECT * FROM ".$azTables."agenti"." WHERE id_agente = '".$tesdoc['id_agente']."' LIMIT 1";
        if ($result = mysqli_query($link, $sql)) {
          $resag = mysqli_fetch_assoc($result);
        }else{
          echo "Error: " . $sql . "<br>" . mysqli_error($link);
        }
        $this->id_agente = $resag;

        //$this->rs_agente = ($this->id_agente)?$anagrafica->getPartner($this->id_agente['id_fornitore']):'';
		if ($this->id_agente){
			$sql = "SELECT * FROM ". $azTables."clfoco" . " LEFT JOIN " . $genTables."anagra" . " ON " . $azTables."clfoco" . ".id_anagra = " . $genTables."anagra" . ".id WHERE codice = '".$this->id_agente['id_fornitore']."' LIMIT 1";
			if ($result = mysqli_query($link, $sql)) {
			  $this->rs_agente = mysqli_fetch_array($result);
			}else{
			  echo "Error: " . $sql . "<br>" . mysqli_error($link);
			}
		}else{
			$this->rs_agente='';
		}

        $this->name_agente = ($this->id_agente)?substr($this->rs_agente['ragso1'] . " " . $this->rs_agente['ragso2'], 0, 47):'';

        if (isset($tesdoc['destin']) and is_array($tesdoc['destin'])) {
            $this->destinazione = $tesdoc['destin'];
        } elseif (isset($tesdoc['destin']) and is_string($tesdoc['destin'])) {
            $destino = preg_split("/[\r\n]+/i", $tesdoc['destin'], 3);
            $this->destinazione = substr($destino[0], 0, 45);
            foreach ($destino as $key => $value) {
                if ($key == 1) {
                    $this->destinazione .= "\n" . substr($value, 0, 45) . "\n";
                } elseif ($key > 1) {
                    $this->destinazione .= substr(preg_replace("/[\r\n]+/i", ' ', $value), 0, 45);
                }
            }
        } else {
            $this->destinazione = '';
        }

        $this->clientSedeLegale = ((trim($this->client['sedleg']) != '') ? preg_split("/\n/", trim($this->client['sedleg'])) : array());
        $this->fiscal_rapresentative = false;
        if ($this->client['fiscal_rapresentative_id'] > 0) {
           $this->fiscal_rapresentative = gaz_dbi_get_row($gTables['anagra'], "id", $this->client['fiscal_rapresentative_id']);
        }
        if (isset($tesdoc['c_a'])) {
            $this->c_Attenzione = $tesdoc['c_a'];
        } else {
            $this->c_Attenzione = '';
        }
        //$this->client = $anagrafica->getPartner($tesdoc['clfoco']);
		$sql = "SELECT * FROM ". $azTables."clfoco" . " LEFT JOIN " . $genTables."anagra" . " ON " . $azTables."clfoco" . ".id_anagra = " . $genTables."anagra" . ".id WHERE codice = '".$tesdoc['clfoco']."' LIMIT 1";
        if ($result = mysqli_query($link, $sql)) {
          $this->client = mysqli_fetch_array($result);
        }else{
          echo "Error: " . $sql . "<br>" . mysqli_error($link);
        }

        $this->tesdoc = $tesdoc;
        $this->min = substr($tesdoc['initra'], 14, 2);
        $this->ora = substr($tesdoc['initra'], 11, 2);
        $this->day = substr($tesdoc['initra'], 8, 2);
        $this->month = substr($tesdoc['initra'], 5, 2);
        $this->year = substr($tesdoc['initra'], 0, 4);
        $this->trasporto = $tesdoc['traspo'];
        $this->testat = $testat;

        $this->docRelNum = $this->tesdoc["numdoc"];    // Numero del documento relativo
        $this->docRelDate = $this->tesdoc["datemi"];    // Data del documento relativo
		$this->fae_reinvii = '';
        if (isset($tesdoc['fattura_elettronica_reinvii'])) {
			$this->fae_reinvii = $this->tesdoc["fattura_elettronica_reinvii"];
		}
		$this->efattura='';

        switch ($tesdoc["tipdoc"]) {
            case "FAD":
            case "FAI":
            case "FAA":
            case "FAF":
            case "FAP":
            case "FAQ":
            case "FNC":
            case "FND":
                $this->docRelNum = $this->tesdoc["numfat"];
                $this->docRelDate = $this->tesdoc["datfat"];
				// in caso di fattura elettronica ricavo il nome del file
				if (substr($tesdoc['datfat'], 0, 4)>=2019 ) { // dal 2019 valorizzo il nome della e-fattura
					// faccio l'encode in base 36 per ricavare il progressivo unico di invio
					$data = array('azienda' => $admin_aziend['codice'],
								  'anno' => $this->docRelDate,
        						  'sezione' => $this->tesdoc["seziva"],
								  'fae_reinvii'=> $this->fae_reinvii,
								  'protocollo' => $this->tesdoc["protoc"]);
					$this->efattura = encodeSendingNumber($data, 36);
				}
                break;
            case "VCO":
                $this->docRelNum = $this->tesdoc["numfat"];
                $this->docRelDate = $this->tesdoc["datfat"];
				// in caso di fattura elettronica ricavo il nome del file
				if (substr($tesdoc['datfat'], 0, 4)>=2019 ) { // dal 2019 valorizzo il nome della e-fattura
					// faccio l'encode in base 36 per ricavare il progressivo unico di invio
					$data = array('azienda' => $admin_aziend['codice'],
								  'anno' => $this->docRelDate,
        						  'sezione' => $this->tesdoc["seziva"],
								  'fae_reinvii'=> $this->fae_reinvii+4, // sulle fatture allegate allo scontrino per non far coincidere il progressivo unico invio
								  'protocollo' => $this->tesdoc["numfat"]);
					$this->efattura = "IT" . $admin_aziend['codfis'] . "_".encodeSendingNumber($data, 36).'.xml';
				}
                $ecr = gaz_dbi_get_row($gTables['cash_register'], "id_cash", $tesdoc['id_contract']);
				$this->destinazione = '-';

                $this->ecr=($ecr)?$ecr['descri']:'';
                break;
            case "DDT":
            case "DDL":
            case "DDR":
            case "DDV":
            case "DDY":
            case "DDS":
            default:
                $this->docRelNum = $this->tesdoc["numdoc"];    // Numero del documento relativo
                $this->docRelDate = $this->tesdoc["datemi"];    // Data del documento relativo
        }
        $this->withoutPageGroup = false;
        if ( $this->client['country']!=="IT") {
            $this->pers_title = 'Dear';
        } else {
            $this->pers_title = 'Spett.le';
        }
		$admin_aziend['other_email']='';
		// se ho la mail in testata documento la inserisco sui dati aziendali per poterla passare alla funzione sendMail
		if (isset($tesdoc["email"])&&strlen($tesdoc["email"])>10){
			$admin_aziend['other_email']=$tesdoc["email"];
		}
        $this->azienda = $admin_aziend;
		if ($tesdoc['tipdoc'] == 'AFA' || $tesdoc['tipdoc'] == 'AFT') {
			$clfoco = gaz_dbi_get_row($gTables['clfoco'], "codice", $tesdoc['clfoco']);
			$this->iban = $clfoco['iban'];
		}
        $this->artico_doc = array(); // accumulatore referenze ai documenti degli articoli eventualemente da allegare
		// ATTRIBUISCO UN EVENTUALE REGIME FISCALE DIVERSO DALLA CONFIGURAZIONE AZIENDA SE LA SEZIONE IVA E' LEGATO AD ESSO TRAMITE IL RIGO var='sezione_regime_fiscale' IN gaz_XXXcompany_config
		$this->regime_fiscale=$this->azienda['fiscal_reg'];

		//if ($fr=getRegimeFiscale($this->tesdoc["seziva"])) $this->regime_fiscale=$fr;
		$res=false;
		$sql = "SELECT * FROM ".$azTables."company_config"." WHERE var = 'sezione_regime_fiscale' LIMIT 1";
        if ($result = mysqli_query($link, $sql)) {
          $conf_rf = mysqli_fetch_assoc($result);
        }else{
          echo "Error: " . $sql . "<br>" . mysqli_error($link);
        }
		$rrff=($conf_rf)?trim($conf_rf['val']):0;
		$rf=explode(';',$rrff);
		if (isset($rf[0])&&!empty($rf[0])){// ho almeno un altro regime
			foreach($rf as $v){
				$exrf=explode('=',$v);
				if (preg_match("/^([1-8]{1})$/", $exrf[0], $rgsez)&&preg_match("/^(RF[0-9]{2})$/", $exrf[1], $rgrf)){
					if ($rgsez[1]==$this->tesdoc["seziva"]) $res=$rgrf[1];
				}
			}
		}
		$fr=$res;
		if ($fr){
			$this->regime_fiscale=$fr;
		}

    }

    function initializeTotals() {
        // definisco le variabili dei totali
        $this->totimp_body = 0;
        $this->body_castle = array();
        $this->taxstamp = 0;
        $this->virtual_taxstamp = 0;
        $this->tottraspo = 0;
    }



    function getRigo() {
       // $from = $this->gTables[$this->tableName] . ' AS rs LEFT JOIN ' . $this->gTables['aliiva'] . ' AS vat ON rs.codvat=vat.codice';
       // $rs_rig = gaz_dbi_dyn_query('rs.*,vat.tipiva AS tipiva', $from, "rs.id_tes = " . $this->testat, "id_tes DESC, id_rig");
      global $azTables;
      $azTables=$GLOBALS['azTables'];
      global $link;
      $link=$GLOBALS['link'];

        $sql = "SELECT ".$azTables."rigbro".".*, ".$azTables.'aliiva'.".tipiva, ".$azTables.'artico'.".custom_field, ".$azTables.'artico'.".descri AS desart, ".$azTables.'artico'.".annota, ".$azTables.'artico'.".web_url, ".$azTables."rental_events".".* FROM ".$azTables."rigbro"."
        LEFT JOIN " . $azTables.'aliiva' . " ON codvat=codice
        LEFT JOIN " . $azTables.'artico' . " ON " . $azTables.'artico'.".codice=" . $azTables.'rigbro' . ".codart
        LEFT JOIN " . $azTables.'rental_events' . " ON " . $azTables.'rental_events'.".id_rigbro=" . $azTables.'rigbro' . ".id_rig
        WHERE id_tes = " . $this->testat ." ORDER BY id_tes DESC, id_rig";
        if ($rs_rig = mysqli_query($link, $sql)){
        }else{
          echo "Error: " . $sql . "<br>" . mysqli_error($link);
        }
        $this->tottraspo += $this->trasporto;
        if ($this->taxstamp < 0.01 && $this->tesdoc['taxstamp'] >= 0.01) {
            $this->taxstamp = $this->tesdoc['taxstamp'];
        }
        $this->roundcastle = [];
        $this->riporto = 0.00;
        $this->ritenuta = 0.00;
		$this->totiva = 0.00;
        $results = array();
        while ($rigo = gaz_dbi_fetch_array($rs_rig)) {
			//echo "<br><pre>",print_r($rigo);
          $rigo['barcode']="";
          if ($rigo['tiprig'] <= 1 || $rigo['tiprig'] == 4 || $rigo['tiprig'] == 50 || $rigo['tiprig'] == 90) {
              $tipodoc = substr($this->tesdoc["tipdoc"], 0, 1);
              $rigo['importo'] = CalcolaImportoRigo($rigo['quanti'], $rigo['prelis'], $rigo['sconto']);
              $v_for_castle = CalcolaImportoRigo($rigo['quanti'], $rigo['prelis'], array($rigo['sconto'], $this->tesdoc['sconto']));
              if ($rigo['tiprig'] == 1) {
                  $rigo['importo'] = CalcolaImportoRigo(1, $rigo['prelis'], 0);
                  $v_for_castle = CalcolaImportoRigo(1, $rigo['prelis'], $this->tesdoc['sconto']);
              }
              if ($rigo['tiprig'] == 4) {
                  $rigo['importo'] = round($rigo['provvigione']*$rigo['prelis']/100,2);
                  $v_for_castle = $rigo['importo'] ;
              }

              if (!isset($this->castel[$rigo['codvat']])) {
                  $this->castel[$rigo['codvat']] = 0;
              }
              if (!isset($this->body_castle[$rigo['codvat']])) {
                  $this->body_castle[$rigo['codvat']]['impcast'] = 0;
              }
              $this->body_castle[$rigo['codvat']]['impcast'] += $v_for_castle;
              $this->castel[$rigo['codvat']] += $v_for_castle;
              $this->totimp_body += $rigo['importo'];
              $this->ritenuta += round($rigo['importo'] * $rigo['ritenuta'] / 100, 2);
			  $this->totiva += ($rigo['importo']*$rigo['pervat'])/100;
          } elseif ($rigo['tiprig'] == 6 || $rigo['tiprig'] == 7 || $rigo['tiprig'] == 8) {
            //  $body_text = gaz_dbi_get_row($this->gTables['body_text'], "id_body", $rigo['id_body_text']);
			$sql = "SELECT * FROM ".$azTables."body_text"." WHERE id_body = ".$rigo['id_body_text']." LIMIT 1";
			if ($result = mysqli_query($link, $sql)) {
			  $body_text = mysqli_fetch_assoc($result);
			}else{
			  echo "Error: " . $sql . "<br>" . mysqli_error($link);
			}

              $rigo['descri'] = $body_text['body_text'];
          } elseif ($rigo['tiprig'] == 3) {
              $this->riporto += $rigo['prelis'];
          } elseif ($rigo['tiprig'] == 91) {
              $this->roundcastle[$rigo['codvat']] = $rigo['prelis'];
          }
          if ($this->tesdoc['tipdoc']=='AFA' && $rigo['tiprig'] <= 2 && strlen($rigo['descri'])>70  ){
            // 	se la descrizione no la si riesce a contenere in un rigo (es.fattura elettronica d'acquisto)	aggiungo righi descrittivi

            $descrizione_nuova='';
            $nuovi_righi=array();
            $n_r=explode(' ',$rigo['descri']);
            foreach($n_r as $v){
              if (strlen($descrizione_nuova)<=60){ // se  la descrizione è ancora abbastanza corta la aggiungo
                $descrizione_nuova .= ' '.$v;
              } else {
                // i righi iniziali sono aggiunti e definiti descrittivi
                $nuovi_righi[]=array('tiprig'=>2,'codart'=>'','descri'=>$descrizione_nuova,'quanti'=>0, 'unimis'=>'','prelis'=>0,'sconto'=>0,'prelis'=>0,'pervat'=>0,'codric'=>0,'provvigione'=>0,'ritenuta'=>0,'id_order'=>0,'id_mag'=>0,'id_orderman'=>0);
                // riparto con un nuovo valore di descrizione
                $descrizione_nuova = $v;
              }
            }
            // quando esco dal ciclo sull'ultimo rigo rimane dello stesso tipo originale
            $rigo['descri']=$descrizione_nuova;
            $nuovi_righi[]=$rigo;
            foreach($nuovi_righi as $v_nr) { // riattraverso l'array dei nuovi righi e sull'ultimo
              $results[] = $v_nr;
            }
          } else {
            $results[] = $rigo;
          }
            //creo il castelletto IVA ma solo se del tipo normale o forfait
        }
        return $results;
    }

    function setTotal() {
		global $azTables;
		$azTables=$GLOBALS['azTables'];
		global $link;
		$link=$GLOBALS['link'];

        $this->totivafat = 0.00;
        $this->totimpfat = 0.00;
        $this->totimpmer = 0.00;
        $this->tot_ritenute = $this->ritenuta;
        $this->virtual_taxstamp = $this->tesdoc['virtual_taxstamp'];
        $this->impbol = 0.00;
        $this->totroundcastle = $this->roundcastle;
        $this->totriport = $this->riporto;
        $this->speseincasso = ((isset($this->tesdoc['speban']))?$this->tesdoc['speban']:0) * ((isset($this->pagame['numrat']))?$this->pagame['numrat']:1);
        $this->cast =[];
        if (!isset($this->castel)) {
            $this->castel = [];
        }
        if (!isset($this->totimp_body)) {
            $this->totimp_body = 0;
        }
        $this->totimpmer = $this->totimp_body;
        $this->totimp_body = 0;
        $somma_spese = $this->tottraspo + $this->speseincasso + $this->tesdoc['spevar'];

		if (isset($_SESSION["user_name"])){
			$calc = new Compute();
			$calc->add_value_to_VAT_castle($this->body_castle, $somma_spese, $this->tesdoc['expense_vat']);
			if ($this->tesdoc['stamp'] > 0) {
				$calc->payment_taxstamp($calc->total_imp + $this->totriport + $calc->total_vat - $calc->total_isp - $this->tot_ritenute + $this->taxstamp, $this->tesdoc['stamp'], $this->tesdoc['round_stamp'] * $this->pagame['numrat']);
				$this->impbol = $calc->pay_taxstamp;
			}
			$this->totimpfat = $calc->total_imp;
			$this->totivafat = $calc->total_vat;
			$this->totivasplitpay = $calc->total_isp;
			// aggiungo gli eventuali bolli al castelletto
			if ($this->virtual_taxstamp == 0 || $this->virtual_taxstamp == 3) { //  se è a carico dell'emittente non lo aggiungo al castelletto IVA
				$this->taxstamp = 0.00;
			}
			if ($this->impbol >= 0.01 || $this->taxstamp >= 0.01) {
				$calc->add_value_to_VAT_castle($calc->castle, $this->taxstamp + $this->impbol, $this->azienda['taxstamp_vat']);
			}
			if (count($this->roundcastle)>=1){ // ci sono stati dei tiprig = 91 per arrotondamenti IVA su castelletto
				$calc->round_VAT_castle($calc->castle,$this->roundcastle);
			}
			$this->cast = $calc->castle;
			$this->riporto = 0;
			$this->totroundcastle = $calc->totroundcastle;
		}else{// se sono nel front-end mi calcolo per conto mio iva e totali da riportare senza castelletto perché non mi serve
			$sql = "SELECT aliquo FROM ".$azTables."aliiva"." WHERE codice = '".$this->tesdoc['expense_vat']."' LIMIT 1";
			if ($result = mysqli_query($link, $sql)) {
			  $resali = mysqli_fetch_assoc($result);
			}else{
			  echo "Error: " . $sql . "<br>" . mysqli_error($link);
			}
			$this->totivafat=$this->totiva+(($somma_spese*$resali['aliquo'])/100);
			$this->totimpfat=$this->totimpmer;
		}

        $this->ritenute = 0;
        $this->roundcastle = [];
        $this->castel = [];
    }

    function getExtDoc() {
        /* con questa funzione faccio il push sull'accumulatore dei righi contenenti "documenti esterni" da allegare al pdf
		  riprendo il nome del file relativo al documento e lo aggiungo alla matrice solo se il file esiste, prima di chiamare
		  questo metodo dovrò settare $this->id_rig
        */
        if (!isset($this->ExternalDoc)) {
            $this->ExternalDoc = array();
        }
		$r=false;
		$r['file']= $this->azienda['codice'].'/';
        $r['ext'] = '';
        $dh = opendir( DATA_DIR . 'files/' . $this->azienda['codice'] );
        while (false !== ($filename = readdir($dh))) {
            $fd = pathinfo($filename);
            if ($fd['filename'] == 'rigbrodoc_' . $this->id_rig) {
                $r['file'] .= $filename;
                $r['ext'] = $fd['extension'];
				$this->ExternalDoc[] = $r;
            }
        }
        return $r; // in ExternalDocs troverò gli eventuali documenti da allegare
    }


}

function createDocument($testata, $templateName, $gTables, $rows = 'rigdoc', $dest = false, $lang_template=false,$genTables='',$azTables='',$IDaz='',$link='',$id_ag=0) {

    $templates = array('Received' => 'received',
        'CartaIntestata' => 'carta_intestata',
        'Lettera' => 'lettera',
        'FatturaAcquisto' => 'fattura_acquisto',
        'FatturaImmediata' => 'fattura_immediata',
        'Parcella' => 'parcella',
        'PreventivoCliente' => 'preventivo_cliente',
        'OrdineCliente' => 'ordine_cliente',
        'OrdineWeb' => 'ordine_web',
        'FatturaSemplice' => 'fattura_semplice',
        'FatturaAllegata' => 'fattura_allegata',
        'Scontrino' => 'scontrino',
        'OrdineFornitore' => 'ordine_fornitore',
        'OrdineAcquistoProduzioni' => 'ordine_acquisto_produzioni',
        'PreventivoFornitore' => 'preventivo_fornitore',
        'InformativaPrivacy' => 'informativa_privacy',
        'RichiestaPecSdi' => 'richiesta_pecsdi',
        'NominaResponsabile'=>'nomina_responsabile',
        'NominaResponsabileEsterno'=>'nomina_responsabile_esterno',
        'NominaIncaricatoInterno'=>'nomina_incaricato_interno',
        'RegolamentoPrivacy'=>'privacy_regol',
        'DDT' => 'ddt',
        'Etichette' => 'etichette',
        'CMR' => 'cmr',
        'Ticket'=>'ticket',
        'Maintenance'=>'maintenance',
        'BookingSummary' => 'booking_summary',
        'Lease' => 'lease'
    );

    //$config = new Config;
    $configTemplate = new configTemplate;
    if ($lang_template) {
      $ts=$configTemplate->template;
      $configTemplate->setTemplateLang($lang_template);
      if (empty($ts)){
        $configTemplate->template=substr($configTemplate->template, 1);
      }
    }
	$lh=(($dest && $dest == 'H')?'_lh':''); // eventuale scelta di stampare su carta intestata, aggiungo il suffisso "lh";

	require_once ("templates" . ($configTemplate->template ? '.' . $configTemplate->template : '') . '/' . $templates[$templateName] .$lh. '.php');
	$pdf = new $templateName();
    $docVars = new DocContabVars();

    $docVars->setData($gTables, $testata, $testata['id_tes'], $rows, false, $genTables, $azTables);	
    $docVars->initializeTotals();
	
	 // se il template è lease e c'è un proprietario devo intestare il contratto al proprietario	
	if ($templateName=='Lease' && intval($id_ag)>0){// modifico i dati intestazione con quelli del proprietario	
		$ag_anagra=gaz_dbi_get_row($gTables['anagra'], 'id', intval($id_ag));
		$docVars->intesta1=$ag_anagra['ragso1']." ".$ag_anagra['ragso2'];
		$docVars->intesta2=$ag_anagra['indspe']." ".$ag_anagra['capspe']." ".$ag_anagra['citspe']." ".$ag_anagra['prospe'];
		$docVars->intesta3= "tel.: ".$ag_anagra['telefo']." ";
		$docVars->intesta4= "e-mail: ".$ag_anagra['e_mail'];
	}
	
    $pdf->setVars($docVars, $templateName);
    $pdf->setTesDoc();
    $pdf->setCreator('GAzie - ' . $docVars->intesta1);
    $pdf->setAuthor($docVars->user['user_lastname'] . ' ' . $docVars->user['user_firstname']);
    $pdf->setTitle($templateName);
    if ($templates[$templateName]=="lease"){// il contratto non ha intestazione, quindi il margine superiore deve essere minore
      $pdf->setTopMargin(25);
    }else{
      $pdf->setTopMargin(79);
    }
    $pdf->setHeaderMargin(5);
    $pdf->Open();
    $pdf->pageHeader();
    $pdf->compose();
    $pdf->pageFooter();
    $doc_name = preg_replace("/[^a-zA-Z0-9]+/", "_", $docVars->intesta1 . '_' . $pdf->tipdoc) . '.pdf';
	// aggiungo all'array con indice 'azienda' altri dati
	$docVars->azienda['cliente1']=$docVars->cliente1;
	$docVars->azienda['doc_name']=$pdf->tipdoc.'.pdf';
    if ($dest && $dest == 'E') { // è stata richiesta una e-mail
        $dest = 'S';     // Genero l'output pdf come stringa binaria
        // Costruisco oggetto con tutti i dati del file pdf da allegare
        $content = new StdClass;
		$content->urlfile=false;
        $content->name = $doc_name;
        $content->string = $pdf->Output($doc_name, $dest);
        $content->encoding = "base64";
        $content->mimeType = "application/pdf";
        $gMail = new GAzieMail();
        $gMail->sendMail($docVars->azienda, $docVars->user, $content, $docVars->client);
    } elseif ($dest && $dest == 'X') { // è stata richiesta una stringa da allegare
        $dest = 'S';     // Genero l'output pdf come stringa binaria
        $content=$pdf->Output($doc_name, $dest);
        return ($content);
    } else { // va all'interno del browser
      if ($testata['tipdoc']=='AOR'){
        /* in caso di ordine a fornitore che non viene inviato via mail al fornitore ma solo al browser
        cambio la descrizione del file per ricordare a chi è stato fatto*/
        $doc_name = preg_replace("/[^a-zA-Z0-9]+/", "_", $docVars->cliente1 . '_' . $pdf->tipdoc) . '.pdf';
      }
        $pdf->Output($doc_name);
    }
}
?>
