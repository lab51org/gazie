<?php

class venditForm extends GAzieForm {

    function ticketPayments($name, $val, $class = 'FacetSelect') {
        global $gTables;
        $query = 'SELECT codice,descri,tippag FROM `' . $gTables['pagame'] . "` WHERE tippag = 'D' OR tippag = 'C' OR tippag = 'K' ORDER BY tippag";
        echo "\t <select name=\"$name\" class=\"$class\">\n";
        $result = gaz_dbi_query($query);
        while ($r = gaz_dbi_fetch_array($result)) {
            $selected = '';
            if ($r['codice'] == $val) {
                $selected = "selected";
            }
            echo "\t\t<option value=\"" . $r['codice'] . "\" $selected >" . $r['descri'] . "</option>\n";
        }
        print "\t </select>\n";
    }

    function getECR_userData($login) {
        global $gTables;
        return gaz_dbi_get_row($gTables['cash_register'], 'adminid', $login);
    }

    function getECRdata($id) {
        global $gTables;
        return gaz_dbi_get_row($gTables['cash_register'], 'id_cash', $id);
    }

    function selectCustomer($name, $val, $strSearch = '', $val_hiddenReq = '', $mesg, $class = 'FacetSelect') {
        global $gTables, $admin_aziend;
        $anagrafica = new Anagrafica();
        if ($val > 100000000) { //vengo da una modifica della precedente select case quindi non serve la ricerca
            $partner = $anagrafica->getPartner($val);
            echo "\t<input type=\"hidden\" name=\"$name\" value=\"$val\">\n";
            echo "\t<input type=\"hidden\" name=\"search[$name]\" value=\"" . substr($partner['ragso1'], 0, 8) . "\">\n";
            echo "\t<input type=\"submit\" value=\"" . $partner['ragso1'] . " " . $partner["ragso2"] . " " . $partner["citspe"] . " (" . $partner["codice"] . ")\" name=\"change\" onclick=\"this.form.$name.value='0'; this.form.hidden_req.value='change';\" title=\"$mesg[2]\">\n";
        } else {
            if (strlen($strSearch) >= 2) { //sto ricercando un nuovo partner
                echo "\t<select tabindex=\"1\" name=\"$name\" class=\"FacetSelect\" onchange=\"this.form.hidden_req.value='$name'; this.form.submit();\">\n";
                echo "<option value=\"0\"> ---------- </option>";
                $partner = $anagrafica->queryPartners("*", "codice LIKE '" . $admin_aziend['mascli'] . "%' AND codice >" . intval($admin_aziend['mascli'] . '000000') . "  AND ragso1 LIKE '" . addslashes($strSearch) . "%'", "codice ASC");
                if (count($partner) > 0) {
                    foreach ($partner as $r) {
                        $selected = '';
                        if ($r['codice'] == $val) {
                            $selected = "selected";
                        }
                        echo "\t\t <option value=\"" . $r['codice'] . "\" $selected >" . $r['ragso1'] . " " . $r["ragso2"] . " " . $r["citspe"] . "</option>\n";
                    }
                    echo "\t </select>\n";
                } else {
                    $msg = $mesg[0];
                }
            } else {
                $msg = $mesg[1];
                echo "\t<input type=\"hidden\" name=\"$name\" value=\"$val\">\n";
            }
            echo "\t<input tabindex=\"2\" type=\"text\" id=\"search_$name\" name=\"search[$name]\" value=\"" . $strSearch . "\" maxlength=\"15\" size=\"9\" class=\"FacetInput\">\n";
            if (isset($msg)) {
                echo "<input type=\"text\" style=\"color: red; font-weight: bold;\" size=\"" . strlen($msg) . "\" disabled value=\"$msg\">";
            }
            //echo "\t<input tabindex=\"3\" type=\"image\" align=\"middle\" name=\"search_str\" src=\"../../library/images/cerbut.gif\">\n";
            /** ENRICO FEDELE */
            /* Cambio l'aspetto del pulsante per renderlo bootstrap, con glyphicon */
            echo '<button type="submit" class="btn btn-default btn-sm" name="search_str" tabindex="3"><i class="glyphicon glyphicon-search"></i></button>';
            /** ENRICO FEDELE */
        }
    }

}

class Agenti {

    function getPercent($id_agente, $articolo = '') {
        global $gTables;
        if ($id_agente < 1) {
            return false;
        } else { // devo ricavare la percentuale associata all'articolo(prioritaria) o categoria merceologica
            $value = gaz_dbi_get_row($gTables['artico'], 'codice', $articolo);
            $rs = gaz_dbi_dyn_query($gTables['agenti'] . ".*," . $gTables['provvigioni'] . ".*", $gTables['agenti'] . " LEFT JOIN " . $gTables['provvigioni'] . " ON " . $gTables['agenti'] . ".id_agente = " . $gTables['provvigioni'] . ".id_agente", $gTables['provvigioni'] . ".id_agente = " . $id_agente . " AND ((cod_articolo = '" . $articolo . "' AND cod_articolo != '') OR (cod_catmer = " . intval($value['catmer']) . " AND cod_articolo = ''))", 'cod_articolo DESC', 0, 1);
            $result = gaz_dbi_fetch_array($rs);
            if ($result) {
                return $result['percentuale'];
            } else {
                $result = gaz_dbi_get_row($gTables['agenti'], 'id_agente', $id_agente);
                return $result['base_percent'];
            }
        }
    }

}

class venditCalc extends Compute {

    function contractCalc($id_contract) {
        //recupero il contratto da calcolare
        global $gTables, $admin_aziend;
        $this->contract_castle = array();
        $contract = gaz_dbi_get_row($gTables['contract'], "id_contract", $id_contract);
        $this->contract_castel[$contract['vat_code']]['impcast'] = $contract['current_fee'];
        $result = gaz_dbi_dyn_query('*', $gTables['contract_row'], $gTables['contract_row'] . '.id_contract =' . $id_contract, $gTables['contract_row'] . '.id_row');
        while ($row = gaz_dbi_fetch_array($result)) {
            $r_val = CalcolaImportoRigo($row['quanti'], $row['price'], array($row['discount']));
            if (!isset($this->contract_castel[$row['vat_code']])) {
                $this->contract_castel[$row['vat_code']]['impcast'] = 0.00;
            }
            $this->contract_castel[$row['vat_code']]['impcast']+=$r_val;
        }
        $this->add_value_to_VAT_castle($this->contract_castel, 444, $admin_aziend['taxstamp_vat']);
    }

    function computeRounTo($rows, $body_discount, $down = false, $decimal = 5) {
        // questa funzione mi servrà per arrotondare ad 1 euro (sia per difetto che per eccesso) i documenti di vendita
        $tot = 0;
        $tqu = 0;
        foreach ($rows as $k => $v) {
            $rows[$k]['sortkey'] = $k; // mi serve per ricordare l'ordine originale
            $rows[$k]['sortquanti'] = 9999999; // mi serve per evitare di ordinare quantità a zero
            if ($v['tiprig'] == 1 || ($v['quanti'] >= 0.001 && $v['tiprig'] == 0)) {
                if ($v['tiprig'] == 0) { // tipo normale
                    $tot_row = CalcolaImportoRigo($v['quanti'], $v['prelis'], array($v['sconto'], $body_discount, -$v['pervat']));
                } else {                 // tipo forfait
                    $tot_row = CalcolaImportoRigo(1, $v['prelis'], -$v['pervat']);
                    $v['quanti'] = 1;
                }
                $rows[$k]['totrow'] = $tot_row;
                $rows[$k]['sortquanti'] = $v['quanti'];
            }
            $tot+=$tot_row;
            $tqu+=$v['quanti'];
            $tot_row = 0;
        }
        $vt = ceil($tot);
        if ($down) {
            $vt = floor($tot);
        }
        // cifra totale da arrontondare  e non superare!!!
        $diff = round(($vt - $tot), 2);
        // cifra da arrotondare per ogni rigo (IVA compresa)
        $rest = $diff / $tqu;
        // riordino l'array per quantità in modo da tentare di imputare le variazioni di prezzo per prima alle quantità maggiori dove è più difficile raggiungere questo obbiettivo
        usort($rows, function($a, $b) {
            return $b['sortquanti'] - $a['sortquanti'];
        });
        // riattraverso l'array e scrivo di quanto dovrebbe essere aumentato il prezzo per ogni rigo
        $acc_diff = 0;
        $acc = $rows;
        foreach ($rows as $k => $v) { // riattraverso l'array e scrivo di quanto dovrebbe essere aumentato il prezzo per ogni rigo
            if ($v['tiprig'] == 1 || ($v['quanti'] >= 0.001 && $v['tiprig'] == 0)) {
                // tolgo l'iva che verrà sommata ma ci aggiungo gli eventuali sconti
                $rest_part = $rest / (1 + $v['pervat'] / 100) / (1 - $body_discount / 100) / (1 - $v['sconto'] / 100);
                $acc[$k]['prelis'] = round(($v['prelis'] + $rest_part), $decimal);
                if ($v['tiprig'] == 0) { // tipo normale
                    $new_tot_row = CalcolaImportoRigo($v['quanti'], $acc[$k]['prelis'], array($v['sconto'], $body_discount, -$v['pervat']));
                } else {                 // tipo forfait
                    $new_tot_row = CalcolaImportoRigo(1, $acc[$k]['prelis'], -$v['pervat']);
                }
                // accumulo la differenza
                $acc_diff -= ($rows[$k]['totrow'] - $new_tot_row);
            }
        }
        // controllo se ho arrotondato tutta la diffarenza iniziale
        $ctrl_diff = round(($diff - $acc_diff), 2);
        // sull'ultimo rigo che è pure quello con la quantità più bassa provo ad arrotondare perchè più facile farlo modificando il solo prezzo 
        end($acc);
        $lastkey = key($acc);
        $decpow = pow(10, $decimal);
        if (($ctrl_diff <= -0.01 || $ctrl_diff >= 0.01) && $acc[$lastkey]['quanti'] > 0.001) { // se sto arrotondando per eccesso no posso diminuire di troppo allora il valore non dovrà eccedere
            $diff_prelis = ceil($ctrl_diff / (1 + $acc[$lastkey]['pervat'] / 100) / (1 - $body_discount / 100) / (1 - $acc[$lastkey]['sconto'] / 100) / $acc[$lastkey]['quanti'] * $decpow) / $decpow;
            $acc[$lastkey]['prelis'] += $diff_prelis;
        }
        // riordino l'array secondo le key originarie
        usort($acc, function($a, $b) {
            return $a['sortkey'] - $b['sortkey'];
        });
        return $acc;
    }

}

class lotmag {

    function __construct() {
        $this->available = array();
    }

    function getLot($id) {
        // restituisce i dati relativi ad uno specifico lotto
        global $gTables;
        $sqlquery = "SELECT * FROM " . $gTables['lotmag'] . "
            LEFT JOIN " . $gTables['movmag'] . " ON " . $gTables['lotmag'] . ".id_movmag =" . $gTables['movmag'] . ".id_mov  
            WHERE " . $gTables['lotmag'] . ".id = '" . $id . "'";
        $result = gaz_dbi_query($sqlquery);
        $this->lot = gaz_dbi_fetch_array($result);
        return $this->lot;
    }

    function getAvailableLots($codart, $excluded_movmag = 0) {
        // restituisce tutti i lotti non completamente venduti ordinandoli in base alla configurazione aziendale (FIFO o LIFO)
        // e propone una ripartizione, se viene passato un movimento di magazzino questo verrà escluso perché si suppone sia lo stesso
        // che si sta modificando
        global $gTables, $admin_aziend;
        $ob = ' ASC'; // FIFO-PWM-STANDARD
        if ($admin_aziend['stock_eval_method'] == 2) {
            $ob = ' DESC'; // LIFO
        }
        $sqlquery = "SELECT *, SUM(quanti*operat) AS rest FROM " . $gTables['movmag'] . "
            LEFT JOIN " . $gTables['lotmag'] . " ON " . $gTables['movmag'] . ".id_mov =" . $gTables['lotmag'] . ".id_movmag  
            WHERE " . $gTables['movmag'] . ".artico = '" . $codart . "' AND id_mov <> " . $excluded_movmag . " GROUP BY " . $gTables['movmag'] . ".id_lotmag ORDER BY " . $gTables['movmag'] . ".datreg" . $ob;
        $result = gaz_dbi_query($sqlquery);
        $acc = array();
        $rs = false;
        while ($row = gaz_dbi_fetch_array($result)) {
            if ($row['rest'] >= 0.00001) { // l'articolo ha almeno un lotto caricato 
                $rs = true;
                $acc[] = $row;
            }
        }
        $this->available = $acc;
        return $rs;
    }

    function thereisLot($id_tesdoc) {
        // restituisce true se nel documento di vendita c'è almeno un rigo al quale è assegnato un lotto 
        $r = false;
        global $gTables;
        $sqlquery = "SELECT * FROM " . $gTables['rigdoc'] . " AS rd
            LEFT JOIN " . $gTables['movmag'] . " AS mm ON rd.id_mag = mm.id_mov  
            WHERE rd.id_tes = " . $id_tesdoc . " AND mm.id_lotmag > 0 LIMIT 1";
        $result = gaz_dbi_query($sqlquery);
        $row = gaz_dbi_fetch_array($result);
        if (count($row) > 1) { // il documento ha almeno un lotto caricato 
            $r = true;
        }
        return $r;
    }

    function divideLots($quantity) {
        // riparto la quantità tra i vari lotti presenti se questi non sono sufficienti
        // ritorno il resto non assegnato 
        $acc = array();
        $rest = $quantity;
        foreach ($this->available as $v) {
            if ($v['rest'] >= $rest) { // c'è capienza
                $acc[$v['id_lotmag']] = $v + array('qua' => $rest);
            } elseif ($v['rest'] < $rest) { // non c'è capienza
                $acc[$v['id_lotmag']] = $v + array('qua' => $v['rest']);
            }
            $rest -= $v['rest'];
        }
        $this->divided = $acc;
        if ($rest >= 0.00001) {
            // ritorno il resto, quindi non ho abbastanza lotti per contenere la quantità venduta 
            return $rest;
        } else {
            return NULL;
        }
    }

}

?>