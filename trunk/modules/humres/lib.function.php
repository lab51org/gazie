<?php

/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2018 - Antonio De Vincentiis Montesilvano (PE)
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
class humresForm extends GAzieForm {

    function selectHextraType($name,$val) {
        global $gTables;
        $query = 'SELECT * FROM `' . $gTables['staff_work_type'] . '` ';
        $query .= 'WHERE id_work_type = 1 ORDER BY `id_work_type`';
        $ret0 = '<div';
        $ret1 =  '<select name="'.$name.'" class="col-sm-12 dropdownmenustyle">';
        $ret1 .= '<option value="0"></option>';
        $result = gaz_dbi_query($query);
        while ($r = gaz_dbi_fetch_array($result)) {
            $selected = '';
            if ($r['id_work'] == $val) {
				$ret0 .= ' title="'.$r['descri'].'"';
                $selected = " selected";
				$r['descri']=substr($r['descri'],0,5);
             }
            $ret1 .= '<option value="' . $r['id_work'] . '"'. $selected.' >'.$r['id_work'].'-'.$r['descri'].' '.$r['increase']. "</option>\n";
        }
		$ret0 .= '>';
        echo $ret0.$ret1."\t </select>\n</div>\n";
    }

    function selectAbsenceCau($name,$val) {
        global $gTables;
        $query = 'SELECT * FROM `' . $gTables['staff_absence_type'] . '` ';
        $query .= 'WHERE 1';
        $ret0 = '<div';
        $ret1 =  '<select name="'.$name.'" class="col-sm-12 dropdownmenustyle">';
        $ret1 .= '<option value="0"></option>';
        $result = gaz_dbi_query($query);
        while ($r = gaz_dbi_fetch_array($result)) {
            $selected = '';
            if ($r['id_absence'] == $val) {
				$ret0 .= ' title="'.$r['descri'].'"';
                $selected = " selected";
				$r['descri']=substr($r['descri'],0,5);
             }
            $ret1 .= '<option value="' . $r['id_absence'] . '"'. $selected.' >'.$r['causal'].'-'.$r['descri']. "</option>\n";
        }
		$ret0 .= '>';
        echo $ret0.$ret1."\t </select>\n</div>\n";
    }

    function selectOtherType($name,$val) {
        global $gTables;
        $query = 'SELECT * FROM `' . $gTables['staff_work_type'] . '` ';
        $query .= 'WHERE id_work_type > 1  ORDER BY id_work_type, descri';
        $ret0 = '<div';
        $ret1 =  '<select name="'.$name.'" class="col-sm-12 dropdownmenustyle">';
        $ret1 .= '<option value="0"></option>';
        $result = gaz_dbi_query($query);
        while ($r = gaz_dbi_fetch_array($result)) {
            $selected = '';
            if ($r['id_work'] == $val) {
				$ret0 .= ' title="'.$r['descri'].'"';
                $selected = " selected";
				$r['descri']=substr($r['descri'],0,5);
			}
            $ret1 .= '<option value="' . $r['id_work'] . '"'. $selected.' >'.$r['id_work_type'].' '.$r['descri'].' '.$r['increase']. "</option>\n";
        }
		$ret0 .= '>';
        echo $ret0.$ret1."\t </select>\n</div>\n";
    }
}
?>