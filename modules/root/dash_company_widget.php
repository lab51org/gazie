<?php
function selectCompany($name, $val, $strSearch = '', $val_hiddenReq = '', $mesg, $class = 'FacetSelect') {
    global $gTables, $admin_aziend;
    $table = $gTables['aziend'] . ' LEFT JOIN ' . $gTables['admin_module'] . ' ON ' . $gTables['admin_module'] . '.company_id = ' . $gTables['aziend'] . '.codice';
    $where = $gTables['admin_module'] . '.adminid=\'' . $admin_aziend['user_name'] . '\' GROUP BY company_id';
    if ($val > 0 && $val < 1000) { // vengo da una modifica della precedente select case quindi non serve la ricerca
        $co_rs = gaz_dbi_dyn_query("*", $table, 'company_id = ' . $val . ' AND ' . $where, "ragso1 ASC");
        $co = gaz_dbi_fetch_array($co_rs);
        changeEnterprise(intval($val));
        echo "\t<input type=\"hidden\" name=\"$name\" value=\"$val\">\n";
        echo "\t<input type=\"hidden\" name=\"search[$name]\" value=\"%%\">\n";
        echo "\t<input type=\"submit\" value=\"" . $co['ragso1'] . "\" name=\"change\" onclick=\"this.form.$name.value='0'; this.form.hidden_req.value='change';\" title=\"$mesg[2]\">\n";
    } else {
        if (strlen($strSearch) >= 2) { //sto ricercando un nuovo partner
            echo "\t<select name=\"$name\" class=\"FacetSelect\" onchange=\"this.form.hidden_req.value='$name'; this.form.submit();\">\n";
            $co_rs = gaz_dbi_dyn_query("*", $table, "ragso1 LIKE '" . addslashes($strSearch) . "%' AND " . $where, "ragso1 ASC");
            if ($co_rs) {
                echo "<option value=\"0\"> ---------- </option>";
                while ($r = gaz_dbi_fetch_array($co_rs)) {
                    $selected = '';
                    if ($r['company_id'] == $val) {
                        $selected = "selected";
                    }
                    echo "\t\t <option value=\"" . $r['company_id'] . "\" $selected >" . intval($r['company_id']) . "-" . $r["ragso1"] . "</option>\n";
                }
                echo "\t </select>\n";
            } else {
                $msg = $mesg[0];
            }
        } else {
            $msg = $mesg[1];
            echo "\t<input type=\"hidden\" name=\"$name\" value=\"$val\">\n";
        }
        echo "\t<input type=\"text\" name=\"search[$name]\" value=\"" . $strSearch . "\" maxlength=\"15\" size=\"6\" class=\"FacetInput\">\n";
        if (isset($msg)) {
            echo "<input type=\"text\" style=\"color: red; font-weight: bold;\" size=\"" . strlen($msg) . "\" disabled value=\"$msg\">";
        }
        //echo "\t<input type=\"image\" align=\"middle\" name=\"search_str\" src=\"../../library/images/cerbut.gif\">\n";
        /** ENRICO FEDELE */
        /* Cambio l'aspetto del pulsante per renderlo bootstrap, con glyphicon */
        echo '<button type="submit" class="btn btn-default btn-sm" name="search_str"><i class="glyphicon glyphicon-search"></i></button>';
        /** ENRICO FEDELE */
    }
}

?>
<div class="col-sm-6">
    <div class="panel panel-default company-color panel-company" >
        <p>
            <?php echo $script_transl['company'] ?>
        <div class="img-containter">
            <a href="../config/admin_aziend.php"><img class="img-circle dit-picture" src="view.php?table=aziend&value=<?php echo $form['company_id']; ?>" alt="Logo" style="max-width: 100%;" border="0" title="<?php echo $script_transl['upd_company']; ?>" ></a>
        </div>
        </p>
        <p>
            <?php
			if ($company_choice==1 || $admin_aziend['Abilit'] >= 8){
				echo $script_transl['mesg_co'][2] . ' &rArr; ';
				selectCompany('company_id', $form['company_id'], $form['search']['company_id'], $form['hidden_req'], $script_transl['mesg_co']);
            }
			?>
        </p>
        <p>
            <?php echo $script_transl['logout']; ?> &rArr; <input name="logout" type="submit" value=" Logout ">
        </p>
    </div>
</div>
