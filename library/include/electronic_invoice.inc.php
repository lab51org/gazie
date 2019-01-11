<?php

/* $
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2019 - Antonio De Vincentiis Montesilvano (PE)
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

require("../../library/include/expiry_calc.php");

class invoiceXMLvars {

    function setXMLvars($gTables, $tesdoc, $testat, $tableName, $ecr = false) {
        $this->gTables = $gTables;
        $admin_aziend = gaz_dbi_get_row($gTables['aziend'], 'codice', $_SESSION['company_id']);
        $this->descriptive_last_row = trim(gaz_dbi_get_row($gTables['company_config'], 'var', 'descriptive_last_row')['val']);
        $this->azienda = $admin_aziend;
        $this->pagame = gaz_dbi_get_row($gTables['pagame'], "codice", $tesdoc['pagame']);
        $this->banapp = gaz_dbi_get_row($gTables['banapp'], "codice", $tesdoc['banapp']);
        $anagrafica = new Anagrafica();
        $this->banacc = $anagrafica->getPartner($this->pagame['id_bank']);
        $this->vettor = gaz_dbi_get_row($gTables['vettor'], "codice", $tesdoc['vettor']);
        $this->tableName = $tableName;
        $this->intesta1 = $admin_aziend['ragso1'];
        $this->intesta1bis = $admin_aziend['ragso2'];
        $this->intesta2 = $admin_aziend['indspe'] . ' ' . sprintf("%05d", $admin_aziend['capspe']) . ' ' . $admin_aziend['citspe'] . ' (' . $admin_aziend['prospe'] . ')';
        $this->intesta3 = 'Tel.' . $admin_aziend['telefo'] . ' ';
        $this->aziendTel = $admin_aziend['telefo'];
        $this->aziendFax = $admin_aziend['fax'];
		// REA
        $this->REA_ufficio = $admin_aziend['REA_ufficio'];
        $this->REA_numero = $admin_aziend['REA_numero'];
        $this->REA_capitale = $admin_aziend['REA_capitale'];
        $this->REA_socio = $admin_aziend['REA_socio'];
        $this->REA_stato = $admin_aziend['REA_stato'];
        $this->codici = '';
        if ($admin_aziend['codfis'] != '') {
            $this->codici .= 'C.F. ' . $admin_aziend['codfis'] . ' ';
        }
        if ($admin_aziend['pariva']) {
            $this->codici .= 'P.I. ' . $admin_aziend['pariva'] . ' ';
        }
        if ($tesdoc['template'] == 'FatturaImmediata') {
            $this->sempl_accom = true;
        } else {
            $this->sempl_accom = false;
        }
        $this->intesta4 = $admin_aziend['e_mail'];
        $this->intesta5 = $admin_aziend['sexper'];
        if ($admin_aziend['sexper'] == 'G') {
            $this->TipoRitenuta = 'RT02';
        } else {
            $this->TipoRitenuta = 'RT01';
        }

        $this->colore = $admin_aziend['colore'];
        $this->decimal_quantity = $admin_aziend['decimal_quantity'];
        $this->decimal_price = $admin_aziend['decimal_price'];
        $this->logo = $admin_aziend['image'];
        $this->link = $admin_aziend['web_url'];
        $this->perbollo = 0;
        $this->iva_bollo = gaz_dbi_get_row($gTables['aliiva'], "codice", $admin_aziend['taxstamp_vat']);
        $this->client = $anagrafica->getPartner($tesdoc['clfoco']);
        $this->cliente1 = $this->client['ragso1'];
        $this->cliente2 = $this->client['ragso2'];
        $this->cliente3 = $this->client['indspe'];
		$this->pec_email = $this->client['pec_email'];
        if (!empty($this->client['citspe'])) {
            $this->cliente4 = sprintf("%05d", $this->client['capspe']) . ' ' . strtoupper($this->client['citspe']) . ' ' . strtoupper($this->client['prospe']);
        } else {
            $this->cliente4 = '';
        }

        if (!empty($this->client['pariva'])) {
            $this->cliente5 = 'P.I. ' . $this->client['pariva'] . ' ';
        } else {
            $this->cliente5 = '';
        }
        if (!empty($this->client['pariva'])) { //se c'e' la partita iva
            if (!empty($this->client['codfis']) and $this->client['codfis'] == $this->client['pariva']) {
                $this->cliente5 = 'C.F. e P.I. ' . $this->client['codfis'];
            } elseif (!empty($this->client['codfis']) and $this->client['codfis'] != $this->client['pariva']) {
                $this->cliente5 = 'C.F. ' . $this->client['codfis'] . ' P.I. ' . $this->client['pariva'];
            } else { //per es. se non c'e' il codice fiscale
                $this->cliente5 = ' P.I. ' . $this->client['pariva'];
            }
        } else { //se  NON c'e' la partita iva
            $this->cliente5 = '';
            if (!empty($this->client['codfis'])) {
                $this->cliente5 = 'C.F. ' . $this->client['codfis'];
            }
        }
        // variabile e' sempre un array
        $this->id_agente = gaz_dbi_get_row($gTables['agenti'], 'id_agente', $tesdoc['id_agente']);
        $this->rs_agente = $anagrafica->getPartner($this->id_agente['id_fornitore']);
        $this->name_agente = substr($this->rs_agente['ragso1'] . " " . $this->rs_agente['ragso2'], 0, 47);
        if ((isset($tesdoc['id_des_same_company'])) and ( $tesdoc['id_des_same_company'] > 0)) {
            $this->partner_dest = gaz_dbi_get_row($gTables['destina'], 'codice', $tesdoc['id_des_same_company']);
            $this->destinazione = substr($this->partner_dest['unita_locale1'] . " " . $this->partner_dest['unita_locale2'], 0, 45);
            $this->destinazione .= "\n" . substr($this->partner_dest['indspe'], 0, 45);
            $this->destinazione .= "\n" . substr($this->partner_dest['capspe'] . " " . $this->partner_dest['citspe'] . " (" . $this->partner_dest['prospe'] . ")", 0, 45);
        } elseif ((isset($tesdoc['id_des'])) and ( $tesdoc['id_des'] > 0)) {
            $this->partner_dest = $anagrafica->getPartnerData($tesdoc['id_des']);
            $this->destinazione = substr($this->partner_dest['ragso1'] . " " . $this->partner_dest['ragso2'], 0, 45);
            $this->destinazione .= "\n" . substr($this->partner_dest['indspe'], 0, 45);
            $this->destinazione .= "\n" . substr($this->partner_dest['capspe'] . " " . $this->partner_dest['citspe'] . " (" . $this->partner_dest['prospe'] . ")", 0, 45);
        } else {
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
        }
        $this->vettore = false;
		if ($tesdoc['vettor']>0){
			$this->vettore = gaz_dbi_get_row($gTables['vettor'], "codice", $tesdoc['vettor']);
		}
        $this->clientSedeLegale = ((trim($this->client['sedleg']) != '') ? preg_split("/\n/", trim($this->client['sedleg'])) : array());
        $this->client = $anagrafica->getPartner($tesdoc['clfoco']);
        $this->tesdoc = $tesdoc;
        $this->expense_pervat = gaz_dbi_get_row($gTables['aliiva'], "codice", $this->tesdoc['expense_vat']);
        $this->min = substr($tesdoc['initra'], 14, 2);
        $this->ora = substr($tesdoc['initra'], 11, 2);
        $this->day = substr($tesdoc['initra'], 8, 2);
        $this->month = substr($tesdoc['initra'], 5, 2);
        $this->year = substr($tesdoc['initra'], 0, 4);
        $this->trasporto = $tesdoc['traspo'];
        $this->testat = $testat;
        $this->ddt_data = false;

        $this->TipoDocumento = 'TD01';    // <TipoDocumento> 2.1.1.1
        $this->docRelNum = $this->tesdoc["numdoc"].'/'.$this->tesdoc["seziva"];    // Numero del documento relativo
        $this->docRelDate = $this->tesdoc["datemi"];    // Data del documento relativo
		$this->protoc = $this->tesdoc["protoc"];
        $this->fae_reinvii = $this->tesdoc["fattura_elettronica_reinvii"];
        switch ($tesdoc["tipdoc"]) {
            case "FAD":
                $this->ddt_data = true;
                $this->docRelNum = $this->tesdoc["numfat"].'/'.$this->tesdoc["seziva"];
                $this->docRelDate = $this->tesdoc["datfat"];
                break;
            case "FAI":
                $this->docRelNum = $this->tesdoc["numfat"].'/'.$this->tesdoc["seziva"];
                $this->docRelDate = $this->tesdoc["datfat"];
                break;
            case "FAA": // Fattura d'acconto
                $this->TipoDocumento = 'TD02';    // <TipoDocumento> 2.1.1.1
                $this->docRelNum = $this->tesdoc["numfat"].'/'.$this->tesdoc["seziva"];
                $this->docRelDate = $this->tesdoc["datfat"];
                break;
            case "FNC":
                $this->TipoDocumento = 'TD04';    // <TipoDocumento> 2.1.1.1
                $this->docRelNum = $this->tesdoc["numfat"].'/'.$this->tesdoc["seziva"];
                $this->docRelDate = $this->tesdoc["datfat"];
                break;
            case "FND":
                $this->TipoDocumento = 'TD05';    // <TipoDocumento> 2.1.1.1
                $this->docRelNum = $this->tesdoc["numfat"].'/'.$this->tesdoc["seziva"];
                $this->docRelDate = $this->tesdoc["datfat"];
                break;
            case "FAP":
                $this->TipoDocumento = 'TD06';    // <TipoDocumento> 2.1.1.1
                $this->docRelNum = $this->tesdoc["numfat"].'/'.$this->tesdoc["seziva"];
                $this->docRelDate = $this->tesdoc["datfat"];
                break;
            case "FAQ": // Parcella d'acconto
                $this->TipoDocumento = 'TD03';    // <TipoDocumento> 2.1.1.1
                $this->docRelNum = $this->tesdoc["numfat"].'/'.$this->tesdoc["seziva"];
                $this->docRelDate = $this->tesdoc["datfat"];
                break;
            case "VCO":
                $this->protoc = $this->tesdoc["numfat"]; //forzo il protocollo al numero fattura in caso di registro corrispettivi
				$this->fae_reinvii = $this->fae_reinvii+4; // e aggiungo 4 per non far collidere con un eventuale fattura normale della stessa sezione
                $this->docRelNum = $this->tesdoc["numfat"].'/'.$this->tesdoc["seziva"].'/SCONTR';
                $this->docRelDate = $this->tesdoc["datfat"];
                break;
            case "DDT":
            case "DDL":
            case "DDR":
            default:
                $this->ddt_data = true;
                $this->docRelNum = $this->tesdoc["numdoc"].'/'.$this->tesdoc["seziva"];    // Numero del documento relativo
                $this->docRelDate = $this->tesdoc["datemi"];    // Data del documento relativo
        }
        $this->seziva = $this->tesdoc["seziva"];
        $this->docYear = substr($this->tesdoc["datemi"], 0, 4);    // Anno del documento
        $this->IdCodice = $admin_aziend['codfis'];
        $this->totimp_body = 0;
        $this->totimp_decalc = 0;
        $this->totimp_doc = 0;
		// ATTRIBUISCO UN EVENTUALE REGIME FISCALE DIVERSO DALLA CONFIGURAZIONE AZIENDA SE LA SEZIONE IVA E' LEGATO AD ESSO TRAMITE IL RIGO var='sezione_regime_fiscale' IN gaz_XXXcompany_config
		$this->regime_fiscale=$this->azienda['fiscal_reg'];
		if ($fr=getRegimeFiscale($this->seziva)) $this->regime_fiscale=$fr;
    }

    function getXMLrows() {
        $this->tot_trasporto += $this->trasporto;
        if ($this->taxstamp < 0.01 && $this->tesdoc['taxstamp'] >= 0.01) {
            $this->taxstamp = $this->tesdoc['taxstamp'];
        }
        $from = $this->gTables[$this->tableName] . ' AS rs
                 LEFT JOIN ' . $this->gTables['aliiva'] . ' AS vat ON rs.codvat=vat.codice
                 LEFT JOIN ' . $this->gTables['movmag'] . ' AS mom ON rs.id_mag=mom.id_mov
                 LEFT JOIN ' . $this->gTables['lotmag'] . ' AS ltm ON mom.id_lotmag=ltm.id
				 ';
        $rs_rig = gaz_dbi_dyn_query('rs.*,vat.tipiva AS tipiva, vat.fae_natura AS natura, ltm.identifier AS idlotto, ltm.expiry AS scadenzalotto', $from, "rs.id_tes = " . $this->testat, "id_tes DESC, id_rig");
        $this->riporto = 0.00;
        $this->ritenuta = 0.00;
        $this->cassa_prev = array();
        $righiDescrittivi = array();
        $last_normal_row = 0;
        $nr = 1;
        $results = array();
        $dom = new DOMDocument;
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		// questi mi servono per associare i numeri righi ad id_rig  per riferire i valori sull'accumulatore per 2.1.X
		$id_rig_ref=array();
		$ctrl_idtes=0;
		$nr_idtes=1; //
        while ($rigo = gaz_dbi_fetch_array($rs_rig)) {
			if ($ctrl_idtes<>$rigo['id_tes']){ // è cambiata la testata riparto da NumeroLinea 1 e azzero l'array ref
				$nr_idtes=1;
				$id_rig_ref=array();
			}
            $rigo['sconto_su_imponibile'] = array();
			$rigo['codice_tipo']='';
            if ($rigo['tiprig'] <= 1) {
				if (!empty($rigo['codart'])){ // ho un codice articolo lo riprendo per settare il codice tipo ( ci metterò se bene o servizio e categoria merceologica)
					$artico = gaz_dbi_get_row($this->gTables['artico'], "codice", $rigo['codart']);
					$rigo['codice_tipo']=($artico['good_or_service'] == 0) ? 'BENE_CAT_'.$artico['catmer'] : 'SERVIZIO';
				}
				$id_rig_ref[$nr_idtes]=$rigo['id_rig']; // associo l'id_rig al numero rigo mi servirà per valorizzare l'accumulatore per 2.1.X
				$nr_idtes++; // è un tipo rigo a cui possono essere riferiti i dati degli elementi 2.1.X, lo aumento
                $last_normal_row = $nr; // mi potrebbe servire se alla fine dei righi mi ritrovo con dei descrittivi non ancora indicizzati perché seguono l'ultimo rigo normale
                // se ho avuto dei righi descrittivi che hanno preceduto  questo allora li inputo a questo rigo
                if (isset($righiDescrittivi[0])) {
                    foreach ($righiDescrittivi[0] as $v) {
                        $righiDescrittivi[$nr][] = $v; // faccio il push su un array indicizzato con $nr (numero rigo)
                    }
                }
                unset($righiDescrittivi[0]); // svuoto l'array per prepararlo ad eventuali nuovi righi descrittivi
                $rigo['importo'] = CalcolaImportoRigo($rigo['quanti'], $rigo['prelis'],0);
                $v_for_castle = CalcolaImportoRigo($rigo['quanti'], $rigo['prelis'], array($rigo['sconto'], $this->tesdoc['sconto']));
                if ($rigo['tiprig'] == 1) {
                    $rigo['importo'] = CalcolaImportoRigo(1, $rigo['prelis'], 0);
                    $v_for_castle = CalcolaImportoRigo(1, $rigo['prelis'], $this->tesdoc['sconto']);
					$rigo['quanti']=1;
                }
				$sconto_su_imponibile = round($v_for_castle - $rigo['importo'], 2); // qui metto l'eventuale totale imponibile scontato
				if (abs($sconto_su_imponibile)>=0.01){
					if ($sconto_su_imponibile < 0) {
                       $t = 'SC';
					} else {
                       $t = 'MG'; // è una maggiorazione
					}
					$perc_sconto=100*(1-(1-$rigo['sconto']/100)*(1-$this->tesdoc['sconto']/100));
					$rigo['sconto_su_imponibile'][$rigo['id_rig']]=array('tipo'=>$t,'importo_sconto'=>$sconto_su_imponibile,'scorig'=>floatval($rigo['sconto']),'scotes'=>floatval($this->tesdoc['sconto']),'perc_sconto'=>$perc_sconto,'rigo'=>$rigo);
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
                $this->totimp_doc += $v_for_castle;
                // aggiungo all'accumulatore l'eventuale iva non esigibile (split payment PA)
                if ($rigo['tipiva'] == 'T') {
                    $this->ivasplitpay += round(($v_for_castle * $rigo['pervat']) / 100, 2);
                }
            } elseif ($rigo['tiprig'] == 2) { // descrittivo
                // faccio prima il parsing XML e poi il push su un array ancora da indicizzare (0)
                $righiDescrittivi[0][] = htmlspecialchars($rigo['descri'], ENT_XML1 | ENT_QUOTES, 'UTF-8', true);
            } elseif ($rigo['tiprig'] == 4) { // cassa previdenziale
                if (!isset($this->castel[$rigo['codvat']])) {
                    $this->castel[$rigo['codvat']] = 0;
                }
                if (!isset($this->body_castle[$rigo['codvat']])) {
                    $this->body_castle[$rigo['codvat']]['impcast'] = 0;
                }
                $rigo['importo'] = round($rigo['provvigione']*$rigo['prelis']/100,2);
                $v_for_castle = $rigo['importo'] ;
                $this->body_castle[$rigo['codvat']]['impcast'] += $v_for_castle;
                $this->castel[$rigo['codvat']] += $v_for_castle;
                $this->totimp_body += $rigo['importo'];
                $this->ritenuta += round($rigo['importo'] * $rigo['ritenuta'] / 100, 2);
                $this->totimp_doc += $v_for_castle;
                // aggiungo all'accumulatore l'eventuale iva non esigibile (split payment PA)
                if ($rigo['tipiva'] == 'T') {
                    $this->ivasplitpay += round(($v_for_castle * $rigo['pervat']) / 100, 2);
                }
				/* con codart valorizzo l'elemento <TipoCassa> e creo l'array che mi servirà per generare gli elementi <DatiCassaPrevidenziale> */
                if (!isset($this->cassa_prev[$rigo['codart']])) { // se il tipo cassa non ce l'ho
                    $this->cassa_prev[$rigo['codart']] = array('AlCassa'=>$rigo['provvigione'],'ImportoContributoCassa'=>$rigo['importo'],'ImponibileCassa'=>$rigo['prelis'],'AliquotaIVA'=>$rigo['pervat'],'Ritenuta'=>$rigo['ritenuta'],'Natura'=>$rigo['natura']);
                } else { // ho già l'elemento <TipoCassa>
					$this->cassa_prev[$rigo['codart']]['ImponibileCassa'] +=$rigo['prelis'];
					$this->cassa_prev[$rigo['codart']]['ImportoContributoCassa'] +=$rigo['importo'];
				}
            } elseif ($rigo['tiprig'] == 6 || $rigo['tiprig'] == 8) {
                $body_text = gaz_dbi_get_row($this->gTables['body_text'], "id_body", $rigo['id_body_text']);
                $dom->loadHTML($body_text['body_text']);
                $rigo['descri'] = strip_tags($dom->saveXML());
                $res = explode("\n", wordwrap($rigo['descri'], 60, "\n"));
                // faccio il push ricorsivo su un array ancora da indicizzare (0)
                foreach ($res as $v) {
                    $ctrl_v = trim($v);
                    if (!empty($ctrl_v)) {
                        $righiDescrittivi[0][] = $v;
                    }
                }
            } elseif ($rigo['tiprig'] == 3) {  // var.totale fattura
                $this->riporto += $rigo['prelis'];
            } elseif ($rigo['tiprig']>10 && $rigo['tiprig']<17) {
				if ($rigo['codric']>0){
					$this->IdRig_NumeroLinea[$id_rig_ref[$rigo['codric']]]=$rigo['id_rig']; // qui riferirò l'id_rig del rigo da riportare sull'accumulatore per 2.1.X con l'id_rig del normale
				} else {
					$id_rig_ref[$rigo['codric']]=0;
				}
				$weight_tiprig=array(11=>7,12=>6,13=>2,14=>3,15=>4,16=>5);
				// qui valorizzo l'accumulatore 2.1.X e dipende dal tipo che ho scritto su codvat
				switch ($rigo['codvat']) {
					case "6":
						$this->DatiVari[5][$id_rig_ref[$rigo['codric']]][$weight_tiprig[$rigo['tiprig']]]=$rigo['descri'];
					break;
					case "5":
						$this->DatiVari[4][$id_rig_ref[$rigo['codric']]][$weight_tiprig[$rigo['tiprig']]]=$rigo['descri'];
					break;
					case "4":
						$this->DatiVari[3][$id_rig_ref[$rigo['codric']]][$weight_tiprig[$rigo['tiprig']]]=$rigo['descri'];
					break;
					case "3":
						$this->DatiVari[2][$id_rig_ref[$rigo['codric']]][$weight_tiprig[$rigo['tiprig']]]=$rigo['descri'];
					break;
					case "2":
                    default:
						$this->DatiVari[1][$id_rig_ref[$rigo['codric']]][$weight_tiprig[$rigo['tiprig']]]=$rigo['descri'];
					break;
				}
            } elseif ($rigo['tiprig'] == 21) {  // Causale
               	$this->Causale=$rigo['descri'];
            } elseif ($rigo['tiprig'] == 25) {  // DatiSAL
               	$this->DatiSAL[]=$rigo['descri']; //faccio il push sull'array
            } elseif ($rigo['tiprig'] == 31) {  // DatiVeicoli 2.3
               	$this->DatiVeicoli=array('Data'=>$rigo['descri'],'TotalePercorso'=>intval($rigo['quanti']));
            } elseif ($rigo['tiprig'] == 90) {
				$this->id_rig_ref[$nr_idtes]=$rigo['id_rig'];
				$nr_idtes++; // è un tipo rigo a cui possono essere riferiti i DatiOrdiniAcquisto,ecc lo aumento
			}
			$ctrl_idtes=$rigo['id_tes'];
            $results[$nr] = $rigo;
            $nr++;
            //creo il castelletto IVA ma solo se del tipo normale o forfait
        }
        /* se finiti i righi ho incontrato dei descrittivi che non sono stati
          imputati a dei righi normali perché successivi a questi allora
          li imputo all'ultimo normale incontrato
         */
        if (isset($righiDescrittivi[0])) {
            foreach ($righiDescrittivi[0] as $v) {
                $righiDescrittivi[$last_normal_row][] = $v; // faccio il push su un array indicizzato con $nr (numero rigo)
            }
        }
        unset($righiDescrittivi[0]);
        // se ho dei trasporti lo aggiungo ai righi del relativo DdT
        if ($this->trasporto >= 0.1) {
            $rigo_T = array('id_rig'=>0,'tiprig'=>'T','descri'=>'TRASPORTO','importo'=>$this->trasporto,'pervat'=>$this->expense_pervat['aliquo'],'ritenuta'=>0,'natura'=>$this->expense_pervat['fae_natura']);
            $results[$nr] = $rigo_T;
            $nr++;
        }

        foreach ($results as $k => $v) { // associo l'array dei righi descrittivi con quello del righo corrispondente
            $r[$k] = $v;
            if (isset($righiDescrittivi[$k])) {
                $r[$k]['descrittivi'] = $righiDescrittivi[$k];
            }
        }
        return $r;
    }

    function setXMLtot() {
        $calc = new Compute();
        $this->totivafat = 0.00;
        $this->totimpfat = 0.00;
        $this->totimpmer = 0.00;
        $this->tot_ritenute = $this->ritenuta;
        $this->virtual_taxstamp = $this->tesdoc['virtual_taxstamp'];
        $this->impbol = 0.00;
        $this->BolloVirtuale = false; // ovviamente il bollo potrà essere solo virtuale ma comunque lo setto per evidenziare l'errore
        if ($this->tesdoc['virtual_taxstamp'] == 2 || $this->tesdoc['virtual_taxstamp'] == 3) { // bollo virtualmente assolto
            $this->BolloVirtuale = 'SI';
        }
        $this->totriport = $this->riporto;
        $this->speseincasso = $this->tesdoc['speban'] * $this->pagame['numrat'];
        if (!isset($this->castel)) {
            $this->castel = array();
        }
        if (!isset($this->totimp_body)) {
            $this->totimp_body = 0;
        }
        $this->totimpmer = $this->totimp_body;
        $this->totimp_body = 0;
        if (!isset($this->totimp_doc)) {
            $this->totimp_doc = 0;
        }
        $this->totimpfat = $this->totimp_doc;
        $this->totimp_doc = 0;
        $somma_spese = $this->tot_trasporto + $this->speseincasso + $this->tesdoc['spevar'];
        $calc->add_value_to_VAT_castle($this->body_castle, $somma_spese, $this->tesdoc['expense_vat']);
        if ($this->tesdoc['stamp'] > 0) {
            $calc->payment_taxstamp($calc->total_imp + $this->totriport + $calc->total_vat - $this->tot_ritenute + $this->taxstamp - $this->ivasplitpay, $this->tesdoc['stamp'], $this->tesdoc['round_stamp'] * $this->pagame['numrat']);
            $this->impbol = $calc->pay_taxstamp;
        }
        $this->totimpfat = $calc->total_imp;
        $this->totivafat = $calc->total_vat;
        // aggiungo gli eventuali bolli al castelletto
        $chk_taxstamp = true;
        if ($this->virtual_taxstamp == 0 || $this->virtual_taxstamp == 3) { //  se è a carico dell'emittente non lo aggiungo al castelletto IVA
            $chk_taxstamp = false;
        }
        if ($this->impbol >= 0.01 || ($this->taxstamp >= 0.01 && $chk_taxstamp)) {
            $this->impbol += $this->taxstamp;
            $calc->add_value_to_VAT_castle($calc->castle, $this->impbol, $this->azienda['taxstamp_vat']);
        } elseif (!$chk_taxstamp) { // bollo da non addebitare ma esistente
            $this->impbol = $this->taxstamp;
        }
        $this->cast = $calc->castle;
        $this->riporto = 0;
        $this->ritenute = 0;
    }

}

function create_XML_invoice($testata, $gTables, $rows = 'rigdoc', $dest = false, $name_ziparchive = false) {
    $XMLvars = new invoiceXMLvars();
    $domDoc = new DOMDocument;
	$domDoc->preserveWhiteSpace = false;
	$domDoc->formatOutput = true;
    $ctrl_doc = 0;
    $ctrl_fat = 0;
    $n_linea = 1;
    // definisco le variabili dei totali
    $XMLvars->totimp_body = 0;
    $XMLvars->taxstamp = 0;
    $XMLvars->virtual_taxstamp = 0;
    $XMLvars->tot_trasporto = 0;
    $XMLvars->body_castle = array();
    $XMLvars->ivasplitpay = 0.00;
	/* inizializzo l'accumulatore per 2.1.X DatiVari*/
	$XMLvars->DatiVari=array();
	$XMLvars->IdRig_NumeroLinea=array();
	// inizializzo l'accumulatore per DatiSAL 2.1.7
	$XMLvars->DatiSAL=array();
	// inizializzo la variabile per Causale 2.1.1.11
	$XMLvars->Causale=false;
	// inizializzo la variabile per DatiVeicoli 2.3
	$XMLvars->DatiVeicoli=false;
	// inizializzo l'accumulatore per DatiDDT
	$XMLvars->DatiDDT=array();

    while ($tesdoc = gaz_dbi_fetch_array($testata)) {
        $XMLvars->setXMLvars($gTables, $tesdoc, $tesdoc['id_tes'], $rows, false);
        $cod_destinatario=trim($XMLvars->client['fe_cod_univoco']); // elemento 1.1.4
		if ($ctrl_fat <>$XMLvars->tesdoc['numfat']) {
			// stabilisco quale template dovrò usare ad ogni cambio di fattura
			if ($XMLvars->docYear <= 2016) { // FAttura Elettronica PA fino al 2016
				$domDoc->load("../../library/include/template_fae.xml");
				$XMLvars->FormatoTrasmissione='FPA';
			} elseif (strlen($cod_destinatario)<=0 || strlen($cod_destinatario)>=7) { // FAttura Elettronica Privati
				$domDoc->load("../../library/include/template_fae_FPR12.xml");
				$XMLvars->FormatoTrasmissione='FPR';
			} else { // FAttura Elettronica PA a partire dal 2017
				$domDoc->load("../../library/include/template_fae_FPA12.xml");
				$XMLvars->FormatoTrasmissione='FPA';
			}
			$xpath = new DOMXPath($domDoc);
		}
        // controllo se ho un ufficio diverso da quello di base
        if (isset($tesdoc['id_des_same_company']) && $tesdoc['id_des_same_company'] > 0) {
            $dest = gaz_dbi_get_row($gTables['destina'], 'codice', $tesdoc['id_des_same_company']);
            $cod_destinatario=trim($dest['fe_cod_ufficio']); // elemento 1.1.4
            $XMLvars->client['fe_cod_univoco']=$cod_destinatario;
        }

		// se c'è un ddt di origine ogni testata creerà un riferimento in <DatiDDT>
        if ($XMLvars->ddt_data) {// se c'è un ddt di origine ogni testata faccio il push sull'accumulatore per creare il blocco <DatiDDT>
			$XMLvars->DatiDDT[$XMLvars->tesdoc['numdoc']]=array("DataDDT"=>$XMLvars->tesdoc['datemi'],"RiferimentoNumeroLinea"=>array());
        }

        if ($ctrl_doc == 0) {
            $id_progressivo = substr($XMLvars->docRelDate, 2, 2) . $XMLvars->seziva .$XMLvars->fae_reinvii . str_pad($XMLvars->protoc, 6, '0', STR_PAD_LEFT);
            //per il momento sono singole chiamate xpath a regime e' possibile usare un array associativo da passare ad una funzione
            $results = $xpath->query("//FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdPaese")->item(0);
            $attrVal = $domDoc->createTextNode('IT');
            $results->appendChild($attrVal);


            $results = $xpath->query("//FatturaElettronicaHeader/DatiTrasmissione/ProgressivoInvio")->item(0);
            $attrVal = $domDoc->createTextNode($id_progressivo);
            $results->appendChild($attrVal);

		   //Il formato della trasmissione è encodato nei file
           // $results = $xpath->query("//FatturaElettronicaHeader/DatiTrasmissione/FormatoTrasmissione")->item(0);
           // $attrVal = $domDoc->createTextNode("SDI11");
           // $results->appendChild($attrVal);

            $codice_trasmittente = $XMLvars->IdCodice;
            $results = $xpath->query("//FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdCodice")->item(0);
            $attrVal = $domDoc->createTextNode($codice_trasmittente);
            $results->appendChild($attrVal);

            $results = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdPaese")->item(0);
            $attrVal = $domDoc->createTextNode("IT");
            $results->appendChild($attrVal);

            //il IdCodice iva e' la partita iva?
            $results = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdCodice")->item(0);
            $attrVal = $domDoc->createTextNode(trim($XMLvars->azienda['pariva']));
            $results->appendChild($attrVal);

            //nodo 1.2.1.2 Codice Fiscale richiesto da alcune amministrazioni come obbligatorio
            $results = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/CodiceFiscale")->item(0);
            $attrVal = $domDoc->createTextNode(trim($XMLvars->azienda['codfis']));
            $results->appendChild($attrVal);

			if ($XMLvars->FormatoTrasmissione == "FPA") {
				//nodo 1.1.4
				$results = $xpath->query("//FatturaElettronicaHeader/DatiTrasmissione/CodiceDestinatario")->item(0);
				$attrVal = $domDoc->createTextNode(trim($XMLvars->client['fe_cod_univoco']));
				$results->appendChild($attrVal);
			} else {
				if (strlen($cod_destinatario) < 6 ) {
					$results = $xpath->query("//FatturaElettronicaHeader/DatiTrasmissione/CodiceDestinatario")->item(0);
					if ($XMLvars->client['country']=='IT'){
						$attrVal = $domDoc->createTextNode("0000000");
					} else {
						$attrVal = $domDoc->createTextNode("XXXXXXX");
					}
					$results->appendChild($attrVal);
					if (strlen(trim($XMLvars->client['pec_email'])) > 6 ) { // l'elemento per la pec la creo solo se c'è
						//nodo 1.1.6
						$el = $domDoc->createElement("PECDestinatario", trim($XMLvars->client['pec_email']));
						$results1 = $xpath->query("//FatturaElettronicaHeader/DatiTrasmissione")->item(0);
						$results1->appendChild($el);
					}
				} else {
					$results = $xpath->query("//FatturaElettronicaHeader/DatiTrasmissione/CodiceDestinatario")->item(0);
					$attrVal = $domDoc->createTextNode(trim($XMLvars->client['fe_cod_univoco']));
					$results->appendChild($attrVal);
                }
			}


            $el = $domDoc->createElement("CodiceFiscale", trim($XMLvars->client['codfis']));
            $results = $xpath->query("//CessionarioCommittente/DatiAnagrafici")->item(0);
            $results1 = $xpath->query("//CessionarioCommittente/DatiAnagrafici/Anagrafica")->item(0);
			if ($XMLvars->client['country']=='IT'){
				$results->insertBefore($el, $results1);
			}

            // nodo 1.4.1.1 partita IVA del committente, se disponibile
            if (!empty($XMLvars->client['pariva'])) {
                $el = $domDoc->createElement("IdFiscaleIVA", '');
                $results = $el->appendChild($domDoc->createElement('IdPaese', $XMLvars->client['country']));
                $results = $el->appendChild($domDoc->createElement('IdCodice', $XMLvars->client['pariva']));
                $results = $xpath->query("//CessionarioCommittente/DatiAnagrafici")->item(0);
				if ($XMLvars->client['country']=='IT'){
					$results1 = $xpath->query("//CessionarioCommittente/DatiAnagrafici/CodiceFiscale")->item(0);
				} else {
					$results1 = $xpath->query("//CessionarioCommittente/DatiAnagrafici/Anagrafica")->item(0);
				}
				$results->insertBefore($el, $results1);
			}


            $results = $xpath->query("//CessionarioCommittente/DatiAnagrafici/Anagrafica/Denominazione")->item(0);
            $attrVal = $domDoc->createTextNode(substr(trim($XMLvars->client['ragso1']) . " " . trim($XMLvars->client['ragso2']), 0, 80));
            $results->appendChild($attrVal);

            $results = $xpath->query("//CessionarioCommittente/Sede/Indirizzo")->item(0);
            $attrVal = $domDoc->createTextNode(trim($XMLvars->client['indspe']));
            $results->appendChild($attrVal);


            $el = $domDoc->createElement("Provincia", strtoupper(trim($XMLvars->client['prospe'])));
            $results = $xpath->query("//CessionarioCommittente/Sede")->item(0);
            $results1 = $xpath->query("//CessionarioCommittente/Sede/Nazione")->item(0);
			if ($XMLvars->client['country']=='IT'){
				$results->insertBefore($el, $results1);
			}


            $results = $xpath->query("//CessionarioCommittente/Sede/Comune")->item(0);
            $attrVal = $domDoc->createTextNode(trim($XMLvars->client['citspe']));
            $results->appendChild($attrVal);

			if ($XMLvars->client['country']!='IT'){
				$XMLvars->client['capspe']='99999';
			}
            $results = $xpath->query("//CessionarioCommittente/Sede/CAP")->item(0);
            $attrVal = $domDoc->createTextNode(trim($XMLvars->client['capspe']));
            $results->appendChild($attrVal);

            $results = $xpath->query("//CessionarioCommittente/Sede/Nazione")->item(0);
            $attrVal = $domDoc->createTextNode(trim($XMLvars->client['country']));
            $results->appendChild($attrVal);

            $results = $xpath->query("//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/TipoDocumento")->item(0);
            $attrVal = $domDoc->createTextNode($XMLvars->TipoDocumento);
            $results->appendChild($attrVal);

            //sempre in euro?
            $results = $xpath->query("//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Divisa")->item(0);
            $attrVal = $domDoc->createTextNode("EUR");
            $results->appendChild($attrVal);

            $results = $xpath->query("//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Data")->item(0);
            $attrVal = $domDoc->createTextNode(trim($XMLvars->docRelDate));
            $results->appendChild($attrVal);

            $results = $xpath->query("//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Numero")->item(0);
            $attrVal = $domDoc->createTextNode(trim($XMLvars->docRelNum));
            $results->appendChild($attrVal);


            $results = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/Anagrafica/Denominazione")->item(0);
            $attrVal = $domDoc->createTextNode(trim($XMLvars->intesta1 . " " . $XMLvars->intesta1bis));
            $results->appendChild($attrVal);

            //regime fiscale RF01 valido per il regime fiscale ordinario
            $results = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/RegimeFiscale")->item(0);
            $attrVal = $domDoc->createTextNode(trim($XMLvars->regime_fiscale));
            $results->appendChild($attrVal);


            $results = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Sede/Indirizzo")->item(0);
            $attrVal = $domDoc->createTextNode(trim($XMLvars->azienda['indspe']));
            $results->appendChild($attrVal);

            $results = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Sede/CAP")->item(0);
            $attrVal = $domDoc->createTextNode(trim($XMLvars->azienda['capspe']));
            $results->appendChild($attrVal);

            $results = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Sede/Comune")->item(0);
            $attrVal = $domDoc->createTextNode(trim($XMLvars->azienda['citspe']));
            $results->appendChild($attrVal);

            $results = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Sede/Provincia")->item(0);
            $attrVal = $domDoc->createTextNode(trim($XMLvars->azienda['prospe']));
            $results->appendChild($attrVal);

            $results = $xpath->query("//FatturaElettronicaHeader/CedentePrestatore/Sede/Nazione")->item(0);
            $attrVal = $domDoc->createTextNode(trim($XMLvars->azienda['country']));
            $results->appendChild($attrVal);
			//IscrizioneREA
            if ($XMLvars->REA_ufficio != "" && $XMLvars->REA_numero != "") { // ho i dati minimi indispensabili per valorizzare il REA
                $results = $xpath->query("//CedentePrestatore")->item(0);
                $el = $domDoc->createElement("IscrizioneREA","");
                $el1 = $domDoc->createElement("Ufficio", $XMLvars->REA_ufficio);
                $el->appendChild($el1);
                $el1 = $domDoc->createElement("NumeroREA", $XMLvars->REA_numero);
                $el->appendChild($el1);
                if (floatval($XMLvars->REA_capitale) > 1) {
                    $el1 = $domDoc->createElement("CapitaleSociale", $XMLvars->REA_capitale);
                    $el->appendChild($el1);
                }
                if (strlen($XMLvars->REA_socio) >= 2) {
                    $el1 = $domDoc->createElement("SocioUnico", $XMLvars->REA_socio);
                    $el->appendChild($el1);
                }
                $el1 = $domDoc->createElement("StatoLiquidazione", $XMLvars->REA_stato);
                $el->appendChild($el1);
                $results->appendChild($el);
            }
        } elseif ($ctrl_doc <> $XMLvars->docRelNum) { // quando cambia il DdT
            /*
              in caso di necessità qui potrò aggiungere linee di codice
             */
        }
        //elenco beni in fattura
        $lines = $XMLvars->getXMLrows();
		$idrig_n_linea[0]=0;
		foreach ($lines AS $key => $rigo) {
			// creo un array per associare l'id_rig al NumeroLinea mi servirà per riferire sui DatiVari
			$idrig_n_linea[$rigo['id_rig']]=$n_linea;
            $nl = false;
			$sc_su_imp['importo_sconto']=0.00;
            switch ($rigo['tiprig']) {
                case "0":       // normale
					$benserv = $xpath->query("//FatturaElettronicaBody/DatiBeniServizi")->item(0);
                    $el = $domDoc->createElement("DettaglioLinee", "");
                    $el1 = $domDoc->createElement("NumeroLinea", $n_linea);
                    $el->appendChild($el1);
					$codart=preg_replace("/[^A-Za-z0-9]i/",'',$rigo['codart']);
                    if (!empty($codart)) { // ho un codice articolo creo l'elemento
						$el1 = $domDoc->createElement("CodiceArticolo", '');
						$el2 = $domDoc->createElement("CodiceTipo",$rigo['codice_tipo']); // il codice tipo è obbligatorio è stato formattato in precedenza per indicarci se è un bene o un servizio e la categoria merceologica
						$el1->appendChild($el2);
						$el2 = $domDoc->createElement("CodiceValore",$codart); // qui metto il valore del codice vero e proprio e che avevo parsato in precedenza
						$el1->appendChild($el2);
						//$el2 = $domDoc->createElement("Quantita", number_format($rigo['quanti'], 2, '.', ''));
						//$el1->appendChild($el2);
						$el->appendChild($el1);
					}
                    if (isset($rigo['descrittivi'])) {
                        // se ho dei righi descrittivi associati li posso aggiungere fino a che la lunghezza non superi 1000 caratteri quindi ne posso aggiungere al massimo 15*60
                        foreach ($rigo['descrittivi'] as $k => $v) {
                            if ($k < 16) {
                                $rigo['descri'] .= ' '.$v; // ogni $v è lungo al massimo 60 caratteri
                                unset($rigo['descrittivi'][$k]); // lo tolgo in modo da mettere un eventuale accesso sotto
                            }
                        }
                    }
                    if ($rigo['idlotto']!='') {
                        // se ho un lotto di magazzino lo accodo alla ddescrizione
                        $rigo['descri'] .= ' LOTTO: '.$rigo['idlotto'].' SCAD.'.$rigo['scadenzalotto']; // ogni $v è lungo al massimo 60 caratteri
                    }
                    $el1 = $domDoc->createElement("Descrizione", htmlspecialchars(str_replace(chr(0xE2).chr(0x82).chr(0xAC),"",substr($rigo['descri'], -1000)), ENT_XML1 | ENT_QUOTES, 'UTF-8', true)) ;
                    $el->appendChild($el1);
                    $el1 = $domDoc->createElement("Quantita", number_format($rigo['quanti'], 2, '.', ''));
                    $el->appendChild($el1);
                    $el1 = $domDoc->createElement("UnitaMisura", $rigo['unimis']);
                    $el->appendChild($el1);
                    $el1 = $domDoc->createElement("PrezzoUnitario", number_format($rigo['prelis'], $XMLvars->decimal_price, '.', ''));
                    $el->appendChild($el1);
					// qualora questo rigo preveda uno sconto
                    if (isset($rigo['sconto_su_imponibile'][$rigo['id_rig']])) {
						$sc_su_imp=$rigo['sconto_su_imponibile'][$rigo['id_rig']];
						/* AGGIUNGO GLI EVENTUALI SCONTI, LO SCONTO CHIUSURA VERRA' MESSO IN CASCATA SUI SINGOLI RIGHI */
						if ($sc_su_imp['scorig']>=0.01 || $sc_su_imp['scorig']<=-0.01){
                            $el1 = $domDoc->createElement("ScontoMaggiorazione", "");
                            $sc1 = $domDoc->createElement("Tipo", $sc_su_imp['tipo']);
                            $el1->appendChild($sc1);
                            $sc1 = $domDoc->createElement("Percentuale", number_format(round($sc_su_imp['scorig'],2), 2, '.', ''));
                            $el1->appendChild($sc1);
							$el->appendChild($el1);
						}
						if ($sc_su_imp['scotes']>=0.01 || $sc_su_imp['scotes']<=-0.01){
                            $el1 = $domDoc->createElement("ScontoMaggiorazione", "");
                            $sc1 = $domDoc->createElement("Tipo", $sc_su_imp['tipo']);
                            $el1->appendChild($sc1);
                            $sc1 = $domDoc->createElement("Percentuale", number_format(round($sc_su_imp['scotes'],2), 2, '.', ''));
                            $el1->appendChild($sc1);
							$el->appendChild($el1);
						}
					}
                    $el1 = $domDoc->createElement("PrezzoTotale", number_format(round($rigo['importo']+$sc_su_imp['importo_sconto'],2), 2, '.', ''));
                    $el->appendChild($el1);
                    $el1 = $domDoc->createElement("AliquotaIVA", number_format($rigo['pervat'], 2, '.', ''));
                    $el->appendChild($el1);
                    if ($rigo['ritenuta'] > 0) {
                        $el1 = $domDoc->createElement("Ritenuta", 'SI');
                        $el->appendChild($el1);
                    }
                    if ($rigo['pervat'] <= 0) {
                        $el1 = $domDoc->createElement("Natura", $rigo['natura']);
                        $el->appendChild($el1);
                    }
                    if (isset($rigo['descrittivi']) && count($rigo['descrittivi']) > 0) {
                        foreach ($rigo['descrittivi'] as $k => $v) {
                            $el1 = $domDoc->createElement("AltriDatiGestionali", '');
                            $el->appendChild($el1);
                            $el2 = $domDoc->createElement("TipoDato", 'txt' . $k);
                            $el1->appendChild($el2);
                            $el2 = $domDoc->createElement("RiferimentoTesto", $v);
                            $el1->appendChild($el2);
                        }
                    }
					// se è una fattura allegata allo scontrino fiscale
                    if ($XMLvars->tesdoc['tipdoc']=='VCO') {
       					$el1 = $domDoc->createElement("AltriDatiGestionali", '');
                        $el->appendChild($el1);
                        $el2 = $domDoc->createElement("TipoDato", 'SCONTRINO FISCALE');
                        $el1->appendChild($el2);
                        $el2 = $domDoc->createElement("RiferimentoTesto", 'NUMERO');
                        $el1->appendChild($el2);
                        $el2 = $domDoc->createElement("RiferimentoNumero", $XMLvars->tesdoc['numdoc']);
                        $el1->appendChild($el2);
                        $el2 = $domDoc->createElement("RiferimentoData",  $XMLvars->tesdoc['datemi']);
                        $el1->appendChild($el2);
                    }
                    $benserv->appendChild($el);
                    $nl = true;
                    break;

                case "1":
                case "90": // forfait, vendita cespite
					$benserv = $xpath->query("//FatturaElettronicaBody/DatiBeniServizi")->item(0);
                    $el = $domDoc->createElement("DettaglioLinee", "");
                    $el1 = $domDoc->createElement("NumeroLinea", $n_linea);
                    $el->appendChild($el1);
                    if (isset($rigo['descrittivi'])) {
                        // se ho dei righi descrittivi associati li posso aggiungere fino a che la lunghezza non superi 1000 caratteri quindi ne posso aggiungere al massimo 15*60
                        foreach ($rigo['descrittivi'] as $k => $v) {
                            if ($k < 16) {
                                $rigo['descri'] .= $v; // ogni $v è lungo al massimo 60 caratteri
                                unset($rigo['descrittivi'][$k]); // lo tolgo in modo da mettere un eventuale accesso sotto
                            }
                        }
                    }
                    $el1 = $domDoc->createElement("Descrizione", substr($rigo['descri'], -1000));
                    $el->appendChild($el1);
                    $el1 = $domDoc->createElement("PrezzoUnitario", number_format($rigo['importo'], 2, '.', ''));
                    $el->appendChild($el1);
					// qualora questo rigo preveda uno sconto
                    if (isset($rigo['sconto_su_imponibile'][$rigo['id_rig']])) {
						$sc_su_imp=$rigo['sconto_su_imponibile'][$rigo['id_rig']];
						/* AGGIUNGO GLI EVENTUALI SCONTI, LO SCONTO CHIUSURA VERRA' MESSO IN CASCATA SUI SINGOLI RIGHI */
						if ($sc_su_imp['scorig']>=0.01 || $sc_su_imp['scorig']<=-0.01){
                            $el1 = $domDoc->createElement("ScontoMaggiorazione", "");
                            $sc1 = $domDoc->createElement("Tipo", $sc_su_imp['tipo']);
                            $el1->appendChild($sc1);
                            $sc1 = $domDoc->createElement("Percentuale", number_format(round($sc_su_imp['scorig'],2), 2, '.', ''));
                            $el1->appendChild($sc1);
							$el->appendChild($el1);
						}
						if ($sc_su_imp['scotes']>=0.01 || $sc_su_imp['scotes']<=-0.01){
                            $el1 = $domDoc->createElement("ScontoMaggiorazione", "");
                            $sc1 = $domDoc->createElement("Tipo", $sc_su_imp['tipo']);
                            $el1->appendChild($sc1);
                            $sc1 = $domDoc->createElement("Percentuale", number_format(round($sc_su_imp['scotes'],2), 2, '.', ''));
                            $el1->appendChild($sc1);
							$el->appendChild($el1);
						}
					}
                    $el1 = $domDoc->createElement("PrezzoTotale", number_format(round($rigo['importo']+$sc_su_imp['importo_sconto'],2), 2, '.', ''));
                    $el->appendChild($el1);
                    $el1 = $domDoc->createElement("AliquotaIVA", number_format($rigo['pervat'], 2, '.', ''));
                    $el->appendChild($el1);
                    if ($rigo['ritenuta'] > 0) {
                        $el1 = $domDoc->createElement("Ritenuta", 'SI');
                        $el->appendChild($el1);
                    }
                    if ($rigo['pervat'] <= 0) {
                        $el1 = $domDoc->createElement("Natura", $rigo['natura']);
                        $el->appendChild($el1);
                    }
                    if (isset($rigo['descrittivi']) && count($rigo['descrittivi']) > 0) {
                        foreach ($rigo['descrittivi'] as $k => $v) {
                            $el1 = $domDoc->createElement("AltriDatiGestionali", '');
                            $el->appendChild($el1);
                            $el2 = $domDoc->createElement("TipoDato", 'txt' . $k);
                            $el1->appendChild($el2);
                            $el2 = $domDoc->createElement("RiferimentoTesto", $v);
                            $el1->appendChild($el2);
                        }
                    }
                    $benserv->appendChild($el);
                    $nl = true;
                    break;
                case "T":       // trasporto
					$benserv = $xpath->query("//FatturaElettronicaBody/DatiBeniServizi")->item(0);
                    $el = $domDoc->createElement("DettaglioLinee", "");
                    $el1 = $domDoc->createElement("NumeroLinea", $n_linea);
                    $el->appendChild($el1);
                    if (isset($rigo['descrittivi'])) {
                        // se ho dei righi descrittivi associati li posso aggiungere fino a che la lunghezza non superi 1000 caratteri quindi ne posso aggiungere al massimo 15*60
                        foreach ($rigo['descrittivi'] as $k => $v) {
                            if ($k < 16) {
                                $rigo['descri'] .= $v; // ogni $v è lungo al massimo 60 caratteri
                                unset($rigo['descrittivi'][$k]); // lo tolgo in modo da mettere un eventuale accesso sotto
                            }
                        }
                    }
                    $el1 = $domDoc->createElement("Descrizione", substr($rigo['descri'], -1000));
                    $el->appendChild($el1);
                    $el1 = $domDoc->createElement("PrezzoUnitario", number_format($rigo['importo'], 2, '.', ''));
                    $el->appendChild($el1);
                    $el1 = $domDoc->createElement("PrezzoTotale", number_format(round($rigo['importo']+$sc_su_imp['importo_sconto'],2), 2, '.', ''));
                    $el->appendChild($el1);
                    $el1 = $domDoc->createElement("AliquotaIVA", number_format($rigo['pervat'], 2, '.', ''));
                    $el->appendChild($el1);
                    if ($rigo['ritenuta'] > 0) {
                        $el1 = $domDoc->createElement("Ritenuta", 'SI');
                        $el->appendChild($el1);
                    }
                    if ($rigo['pervat'] <= 0) {
                        $el1 = $domDoc->createElement("Natura", $rigo['natura']);
                        $el->appendChild($el1);
                    }
                    if (isset($rigo['descrittivi']) && count($rigo['descrittivi']) > 0) {
                        foreach ($rigo['descrittivi'] as $k => $v) {
                            $el1 = $domDoc->createElement("AltriDatiGestionali", '');
                            $el->appendChild($el1);
                            $el2 = $domDoc->createElement("TipoDato", 'txt' . $k);
                            $el1->appendChild($el2);
                            $el2 = $domDoc->createElement("RiferimentoTesto", $v);
                            $el1->appendChild($el2);
                        }
                    }
                    $benserv->appendChild($el);
                    $nl = true;
                    break;
            } // fine switch

			if ($XMLvars->ddt_data && $nl){
				/* è un rigo di ddt devo aggiungere il riferimento alla linea nell'apposito array che ho creato in precedenza */
				$XMLvars->DatiDDT[$XMLvars->tesdoc['numdoc']]['RiferimentoNumeroLinea'][]=$n_linea;
			}
            if ($nl) {
                $n_linea++;
            }
        }
        $ctrl_doc = $XMLvars->tesdoc['numdoc'];
        $ctrl_fat = $XMLvars->tesdoc['numfat'];
    }


// ----- CALCOLO TOTALI E RATE DEL PAGAMENTO
    $XMLvars->setXMLtot();
    $totpar = $XMLvars->totimpfat + $XMLvars->totriport + $XMLvars->totivafat; //totale della fattura al lordo della RDA e dell'IVA
    $totpag = $totpar - $XMLvars->tot_ritenute - $XMLvars->ivasplitpay; // totale a pagare
    if ($XMLvars->virtual_taxstamp == 1 || $XMLvars->virtual_taxstamp == 2) { // se si è scelto di assolvere il bollo sia in modo fisico che virtuale
        $totpag = $totpag + $XMLvars->impbol;
        $totpar = $totpar + $XMLvars->impbol;
    }
    $ex = new Expiry;
    $ratpag = $ex->CalcExpiry($totpag, $XMLvars->tesdoc["datfat"], $XMLvars->pagame['tipdec'], $XMLvars->pagame['giodec'], $XMLvars->pagame['numrat'], $XMLvars->pagame['tiprat'], $XMLvars->pagame['mesesc'], $XMLvars->pagame['giosuc']);
    if ($XMLvars->pagame['numrat'] > 1) {
        $cond_pag = 'TP01';
    } else {
        $cond_pag = 'TP02';
    }
// --- FINE CALCOLO TOTALI

    //Modifica per il sicoge che richiede obbligatoriamente popolato il punto 2.1.1.9
    $results = $xpath->query("//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento")->item(0);
    $el = $domDoc->createElement("ImportoTotaleDocumento", number_format($totpar, 2, '.', ''));  // totale fatura al lordo di RDA
    $results->appendChild($el);

    // alla fine del ciclo sui righi faccio diverse aggiunte es. causale, bolli, descrizione aggiuntive, e spese di incasso, queste essendo cumulative per diversi eventuali DdT non hanno un riferimento


    if ($XMLvars->Causale) {
        $results = $xpath->query("//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento")->item(0);
        $el = $domDoc->createElement("Causale", $XMLvars->Causale);
        $results->appendChild($el);
    }

    if ($XMLvars->DatiVeicoli) {
        $results = $xpath->query("//FatturaElettronicaBody")->item(0);
        $el = $domDoc->createElement("DatiVeicoli", '');
		$el1 = $domDoc->createElement("Data", $XMLvars->DatiVeicoli['Data']);
		$el->appendChild($el1);
		$el1 = $domDoc->createElement("TotalePercorso", $XMLvars->DatiVeicoli['TotalePercorso']);
		$el->appendChild($el1);
        $results->appendChild($el);
    }

    if ($XMLvars->tesdoc['speban'] >= 0.01) {
		$results = $xpath->query("//FatturaElettronicaBody/DatiBeniServizi")->item(0);
        $el = $domDoc->createElement("DettaglioLinee", "");
        $el1 = $domDoc->createElement("NumeroLinea", $n_linea);
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("Descrizione", 'SPESE INCASSO N.' . $XMLvars->pagame['numrat']);
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("PrezzoUnitario", number_format($XMLvars->tesdoc['speban'], 2, '.', ''));
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("PrezzoTotale", number_format(($XMLvars->tesdoc['speban'] * $XMLvars->pagame['numrat']), 2, '.', ''));
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("AliquotaIVA", number_format($XMLvars->expense_pervat['aliquo'], 2, '.', ''));
        $el->appendChild($el1);
        $results->appendChild($el);
        $n_linea++;
    }
    // eventualemente aggiungo i rimborsi per i bolli
    if ($XMLvars->impbol >= 0.01) {
		$results = $xpath->query("//FatturaElettronicaBody/DatiBeniServizi")->item(0);
        $el = $domDoc->createElement("DettaglioLinee", "");
        $el1 = $domDoc->createElement("NumeroLinea", $n_linea);
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("Descrizione", 'RIMBORSO SPESE PER BOLLI ');
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("PrezzoUnitario", number_format($XMLvars->impbol, 2, '.', ''));
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("PrezzoTotale", number_format($XMLvars->impbol, 2, '.', ''));
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("AliquotaIVA", number_format($XMLvars->iva_bollo['aliquo'], 2, '.', ''));
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("Natura", $XMLvars->iva_bollo['fae_natura']);
        $el->appendChild($el1);
        $results->appendChild($el);
        $n_linea++;
    }

    if (!empty($XMLvars->descriptive_last_row) ) { // ... ed se voluto anche il rigo descrittivo derivante dalla configurazione avanzata azienda
		$results = $xpath->query("//FatturaElettronicaBody/DatiBeniServizi")->item(0);
        $el = $domDoc->createElement("DettaglioLinee", "");
        $el1 = $domDoc->createElement("NumeroLinea", $n_linea);
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("Descrizione", $XMLvars->descriptive_last_row);
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("PrezzoUnitario", '0.00');
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("PrezzoTotale", '0.00');
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("AliquotaIVA", number_format($XMLvars->expense_pervat['aliquo'], 2, '.', ''));
        $el->appendChild($el1);
        $results->appendChild($el);
        $n_linea++;
	}




    //DatiVari
    $results = $xpath->query("//FatturaElettronicaBody/DatiGenerali")->item(0);
	$dati_vari_nomi=array('','DatiOrdineAcquisto','DatiContratto','DatiConvenzione','DatiRicezione','DatiFattureCollegate');
	ksort($XMLvars->DatiVari); // l'ordine degli elementi è importante altrimenti non passa il controllo
	foreach($XMLvars->DatiVari as $k0 => $v0){
		$el0 = $domDoc->createElement($dati_vari_nomi[$k0], "");
		foreach($v0 as $k1 => $v1){
			if ($k1>0){
				$el1 = $domDoc->createElement('RiferimentoNumeroLinea', $idrig_n_linea[$k1]);
				$el0->appendChild($el1);
			}
			ksort($v1); // l'ordine degli elementi è importante altrimenti non passa il controllo
			foreach($v1 as $k2 => $v2){
				switch ($k2) {
					case "7":       // CodiceCIG
					$el1 = $domDoc->createElement('CodiceCIG', $v2);
					break;
					case "6":       // CodiceCUP
					$el1 = $domDoc->createElement('CodiceCUP', $v2);
					break;
					case "2":       // IdDocumento
					$el1 = $domDoc->createElement('IdDocumento', $v2);
					break;
					case "3":       // Data
					$el1 = $domDoc->createElement('Data', $v2);
					break;
					case "4":       // NumItem
					$el1 = $domDoc->createElement('NumItem', $v2);
					break;
					case "5":       // CodiceCommessaConvenzione
					$el1 = $domDoc->createElement('CodiceCommessaConvenzione', $v2);
					break;
				}
				$el0->appendChild($el1);
			}
			$results->appendChild($el0);
		}
	}

	// DatiSAL
    if (count($XMLvars->DatiSAL)>0) {
		$results = $xpath->query("//FatturaElettronicaBody/DatiGenerali")->item(0);
		foreach ($XMLvars->DatiSAL as $k=>$v) {
			$el = $domDoc->createElement("DatiSAL",'');
			$el1 = $domDoc->createElement("RiferimentoFase", intval($v));
			$el->appendChild($el1);
			$results->appendChild($el);
		}
    }

    if ($XMLvars->ddt_data) {
		$results = $xpath->query("//FatturaElettronicaBody/DatiGenerali")->item(0);
		foreach ($XMLvars->DatiDDT as $k0=>$v0) {
			$el_ddt = $domDoc->createElement("DatiDDT", "");
            $el1 = $domDoc->createElement("NumeroDDT", $k0);
            $el_ddt->appendChild($el1);
            $el1 = $domDoc->createElement("DataDDT", $v0['DataDDT']);
            $el_ddt->appendChild($el1);
			foreach ($v0['RiferimentoNumeroLinea'] as $k1=>$v1) {
				$el1 = $domDoc->createElement("RiferimentoNumeroLinea", $v1);
				$el_ddt->appendChild($el1);
			}
			$results->appendChild($el_ddt);
		}
    }

    if ($XMLvars->tot_ritenute > 0) {
        $results = $xpath->query("//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento")->item(0);
        $el = $domDoc->createElement("DatiRitenuta", "");
        $el1 = $domDoc->createElement("TipoRitenuta", $XMLvars->TipoRitenuta);
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("ImportoRitenuta", number_format($XMLvars->tot_ritenute, 2, '.', ''));
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("AliquotaRitenuta", number_format($XMLvars->azienda['ritenuta'], 2, '.', ''));
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("CausalePagamento", $XMLvars->azienda['causale_pagam_770']);
        $el->appendChild($el1);
        $results->appendChild($el);
    }

    if (count($XMLvars->cassa_prev) >= 1) {
        $results = $xpath->query("//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento")->item(0);
	    foreach ($XMLvars->cassa_prev as $key => $value) {
			$el = $domDoc->createElement("DatiCassaPrevidenziale", "");
			$el1 = $domDoc->createElement("TipoCassa", $key);
			$el->appendChild($el1);
			$el1 = $domDoc->createElement("AlCassa", number_format($value['AlCassa'], 2, '.', ''));
			$el->appendChild($el1);
			$el1 = $domDoc->createElement("ImportoContributoCassa", number_format($value['ImportoContributoCassa'], 2, '.', ''));
			$el->appendChild($el1);
			$el1 = $domDoc->createElement("ImponibileCassa", number_format($value['ImponibileCassa'], 2, '.', ''));
			$el->appendChild($el1);
			$el1 = $domDoc->createElement("AliquotaIVA", number_format($value['AliquotaIVA'], 2, '.', ''));
			$el->appendChild($el1);
			if ($value['Ritenuta']>=0.01){
				$el1 = $domDoc->createElement("Ritenuta", 'SI');
				$el->appendChild($el1);
			}
			if (substr($value['Natura'],0,1)=='N'){
				$el1 = $domDoc->createElement("Natura", $value['Natura']);
				$el->appendChild($el1);
			}
			$results->appendChild($el);
        }
    }

    if ($XMLvars->BolloVirtuale) {
        $results = $xpath->query("//FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento")->item(0);
        $el = $domDoc->createElement("DatiBollo", "");
        $el1 = $domDoc->createElement("BolloVirtuale", $XMLvars->BolloVirtuale);
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("ImportoBollo", number_format($XMLvars->impbol, 2, '.', ''));
        $el->appendChild($el1);
        $results->appendChild($el);
    }

    $results = $xpath->query("//FatturaElettronicaBody/DatiBeniServizi")->item(0);
    foreach ($XMLvars->cast as $key => $value) {
        $el = $domDoc->createElement("DatiRiepilogo", "");
        $el1 = $domDoc->createElement("AliquotaIVA", number_format($value['periva'], 2, '.', ''));
        $el->appendChild($el1);
        if ($value['periva'] < 0.01) {
            $el1 = $domDoc->createElement("Natura", $value['fae_natura']);
            $el->appendChild($el1);
        }
        // necessario per l'elemento 2.2.2.7
        $value['esigibilita'] = 'I'; // I=esigibiltà immediata
        if ($XMLvars->azienda['fiscal_reg'] == 'RF16' || $XMLvars->azienda['fiscal_reg'] == 'RF17') {
          $value['esigibilita'] = 'D';
        }
        if ($value['tipiva'] == 'T') { // è un'IVA non esigibile per split payment PA
            $value['esigibilita'] = 'S'; // S=scissione dei pagamenti
        }

        $el1 = $domDoc->createElement("ImponibileImporto", number_format($value['impcast'], 2, '.', ''));
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("Imposta", number_format($value['ivacast'], 2, '.', ''));


        if ($value['fae_natura'] == 'N1' || $value['fae_natura'] == 'N2' || $value['fae_natura'] == 'N3' || $value['fae_natura'] == 'N4' || $value['fae_natura'] == 'N5' || $value['fae_natura'] == 'N6') {
            //non viene inserito il nodo EsigibilitaIVA
        } else {
            $el->appendChild($el1);
            $el1 = $domDoc->createElement("EsigibilitaIVA", $value['esigibilita']);
        }

        $el->appendChild($el1);
        $el1 = $domDoc->createElement("RiferimentoNormativo", $value['descriz']);
        $el->appendChild($el1);
        $results->appendChild($el);
    }

    if ($XMLvars->sempl_accom) {
        // se è una fattura accompagnatoria qui inserisco anche i dati relativi al trasporto
        $results = $xpath->query("//FatturaElettronicaBody/DatiGenerali")->item(0);
        $el = $domDoc->createElement("DatiTrasporto", "");
		if ($XMLvars->vettore) { // ho un vettore
			$el1 = $domDoc->createElement("DatiAnagraficiVettore", '');
				$el2 = $domDoc->createElement("IdFiscaleIVA", '');
					$el3 = $domDoc->createElement("IdPaese", 'IT');
					$el2->appendChild($el3);
					$el3 = $domDoc->createElement("IdCodice", $XMLvars->vettore['partita_iva']);
					$el2->appendChild($el3);
				$el1->appendChild($el2);
				$el2 = $domDoc->createElement("Anagrafica", '');
					$el3 = $domDoc->createElement("Denominazione",$XMLvars->vettore['ragione_sociale']);
					$el2->appendChild($el3);
				$el1->appendChild($el2);
			$el->appendChild($el1);
		}
		$el1 = $domDoc->createElement("MezzoTrasporto", $XMLvars->tesdoc['spediz']);
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("CausaleTrasporto", 'VENDITA');
        $el->appendChild($el1);
		if ($XMLvars->tesdoc['units']>=1){
			$el1 = $domDoc->createElement("NumeroColli", $XMLvars->tesdoc['units']);
			$el->appendChild($el1);
		}
		if (strlen(trim($XMLvars->tesdoc['imball']))>=4){
			$el1 = $domDoc->createElement("Descrizione", $XMLvars->tesdoc['imball']);
			$el->appendChild($el1);
		}
		if (($XMLvars->tesdoc['net_weight']+$XMLvars->tesdoc['gross_weight'])>=0.001){
			$el1 = $domDoc->createElement("UnitaMisuraPeso", 'kg');
			$el->appendChild($el1);
			if ($XMLvars->tesdoc['gross_weight']>=0.001){
				$el1 = $domDoc->createElement("PesoLordo", $XMLvars->tesdoc['gross_weight']);
				$el->appendChild($el1);
			}
			if ($XMLvars->tesdoc['net_weight']>=0.001){
				$el1 = $domDoc->createElement("PesoNetto", $XMLvars->tesdoc['net_weight']);
				$el->appendChild($el1);
			}
        }
        $el1 = $domDoc->createElement("DataInizioTrasporto", substr($XMLvars->tesdoc['initra'], 0, 10));
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("DataOraConsegna", substr($XMLvars->tesdoc['initra'], 0, 10) . 'T' . substr($XMLvars->tesdoc['initra'], 11, 8));
        $el->appendChild($el1);
        $results->appendChild($el);
    }

    // elementi dei <DatiPagamento> (2.4)
    $results = $xpath->query("//FatturaElettronicaBody")->item(0);
    $el = $domDoc->createElement("DatiPagamento", "");
    $el1 = $domDoc->createElement("CondizioniPagamento", $cond_pag); // 2.4.1
    $el->appendChild($el1);
    $results->appendChild($el);
    foreach ($ratpag as $k => $v) {
        $results = $xpath->query("//FatturaElettronicaBody/DatiPagamento")->item(0);
        $el = $domDoc->createElement("DettaglioPagamento", ''); // 2.4.2
        $el1 = $domDoc->createElement("Beneficiario", htmlspecialchars(trim($XMLvars->intesta1 . " " . $XMLvars->intesta1bis), ENT_XML1 | ENT_QUOTES, 'UTF-8', true)); // 2.4.2.1
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("ModalitaPagamento", $XMLvars->pagame['fae_mode']); // 2.4.2.2
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("DataScadenzaPagamento", $v['date']); // 2.4.2.5
        $el->appendChild($el1);
        $el1 = $domDoc->createElement("ImportoPagamento", $v['amount']); // 2.4.2.6
        $el->appendChild($el1);
        if ($XMLvars->pagame['tippag'] == 'B') { // se il pagamento è una RiBa indico CAB e ABI
            $el1 = $domDoc->createElement("ABI", str_pad($XMLvars->banapp['codabi'], 5, '0', STR_PAD_LEFT)); // 2.4.2.14
            $el->appendChild($el1);
            $el1 = $domDoc->createElement("CAB", str_pad($XMLvars->banapp['codcab'], 5, '0', STR_PAD_LEFT)); // 2.4.2.15
            $el->appendChild($el1);
        } elseif (!empty($XMLvars->banacc['iban'])) { // se il pagamento ha un IBAN associato
            $el1 = $domDoc->createElement("IBAN", $XMLvars->banacc['iban']); // 2.4.2.13
            $el->appendChild($el1);
        }
        $results->appendChild($el);
    }


    // faccio l'encode in base 36 per ricavare il progressivo unico di invio
    $data = array('azienda' => $XMLvars->azienda['codice'],
        'anno' => $XMLvars->docRelDate,
        'sezione' => $XMLvars->seziva,
		'fae_reinvii'=> $XMLvars->fae_reinvii,
        'protocollo' => $XMLvars->protoc);
    $progressivo_unico_invio = encodeSendingNumber($data, 36);

    $nome_file = "IT" . $codice_trasmittente . "_" . $progressivo_unico_invio;

    $id_tes = $XMLvars->tesdoc['id_tes'];
    $data_ora_ricezione = $XMLvars->docRelDate;

	if ($name_ziparchive){
		if ($name_ziparchive != 'from_string.xml') {
			$verifica = gaz_dbi_get_row($gTables['fae_flux'], 'filename_ori', $nome_file . ".xml");
			if ($verifica == false) {
				$valori = array('filename_ori' => $nome_file . ".xml",
					'filename_zip_package'=>$name_ziparchive,
					'id_tes_ref' => $id_tes,
					'exec_date' => $data_ora_ricezione,
					'received_date' => $data_ora_ricezione,
					'delivery_date' => $data_ora_ricezione,
					'filename_son' => '',
					'id_SDI' => 0,
					'filename_ret' => '',
					'mail_id' => 0,
					'data' => '',
					'flux_status' => '#',
					'progr_ret' => '000',
					'flux_descri' => '');
				fae_fluxInsert($valori);
			}
		}
		return $domDoc->saveXML();
	} else {
		$verifica = gaz_dbi_get_row($gTables['fae_flux'], 'filename_ori', $nome_file . ".xml");
		if ($verifica == false) {
			$valori = array('filename_ori' => $nome_file . ".xml",
			'filename_zip_package'=>'',
            'id_tes_ref' => $id_tes,
            'exec_date' => $data_ora_ricezione,
            'received_date' => $data_ora_ricezione,
            'delivery_date' => $data_ora_ricezione,
            'filename_son' => '',
            'id_SDI' => 0,
            'filename_ret' => '',
            'mail_id' => 0,
            'data' => '',
            'flux_status' => '#',
            'progr_ret' => '000',
            'flux_descri' => '');
			fae_fluxInsert($valori);
		}
		header("Content-type: text/plain");
		header("Content-Disposition: attachment; filename=" . $nome_file . ".xml");
		print $domDoc->saveXML();
	}
}

?>
