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
require("../../library/include/datlib.inc.php");
$admin_aziend = checkAdmin();

function getStartToEndDate($week, $year) {
  $dto = new DateTime();
  $dto->setISODate($year, $week);
  $ret['mon'] = $dto->format('Y-m-d');
  $dto->modify('+1 days');
  $ret['tue'] = $dto->format('Y-m-d');
  $dto->modify('+1 days');
  $ret['wed'] = $dto->format('Y-m-d');
  $dto->modify('+1 days');
  $ret['thu'] = $dto->format('Y-m-d');
  $dto->modify('+1 days');
  $ret['fri'] = $dto->format('Y-m-d');
  $dto->modify('+1 days');
  $ret['sat'] = $dto->format('Y-m-d');
  $dto->modify('+1 days');
  $ret['sun'] = $dto->format('Y-m-d');
  return $ret;
}

function getWorkers($date) {
	global $gTables;
    $orderby = "id_staff ASC";
    $where = "end_date IS NULL OR end_date >= " . $date;
    $field = 'staff.id_staff, CONCAT(ana.ragso1,\' \',ana.ragso2) AS worker_descri ';
    $from = $gTables['staff'] . ' AS staff ' .
            'LEFT JOIN ' . $gTables['clfoco'] . ' AS worker ON staff.id_clfoco=worker.codice ' .
            'LEFT JOIN ' . $gTables['anagra'] . ' AS ana ON worker.id_anagra=ana.id ';
    $result = gaz_dbi_dyn_query($field, $from, $where, $orderby);
	$ret= array();
    while ($row = gaz_dbi_fetch_array($result)) {
		$ret[$row['id_staff']]=$row;
	}
	return $ret;
}

function getStaffTimesheet($worker,$dates) {
	global $gTables;
	foreach ($dates as $k=>$v) {
        $r = gaz_dbi_get_row($gTables['staff_worked_hours'], "id_staff ", $worker."' AND work_day ='".$v);
		if (!empty($r)){
			$ret[$worker][$v] = $r;
		} else {
			$ret[$worker][$v] = array( 'hours_normal'=>'', 'id_work_type_extra'=>0,
			'hours_extra'=>'', 'id_absence_type'=>0, 'hours_absence'=>'',
			'id_other_type'=>0, 'hours_other'=>'', 'note'=>'', 'id_orderman'=>0
			);
		}
	}
	return $ret;
}

if (isset($_POST['week'])) { // accessi successivi
    $form['week'] = filter_input(INPUT_POST, 'week');
    $form['year'] = filter_input(INPUT_POST, 'year');
	if ($_POST['goto']=='next'){
		if ($form['week']<52) { // settimana successiva
			$form['week'] ++;
		} else {
			$form['year'] ++;
			$form['week'] =1;
		}
	}
	if ($_POST['goto']=='prev'){
		if ($form['week']>1) { // settimana precedente
			$form['week'] --;
		} else {
			$form['year'] --;
			$form['week'] =52;
		}
	}
	if (isset($_POST['go_insert'])){
		$week_days = getStartToEndDate($form['week'], $form['year']);
		// inserisco o modifico i dati sul database
		foreach ($_POST['rows'] as $id_worker=>$v_dates){ 
			foreach ($v_dates as $k_date=>$v){
				$exist=gaz_dbi_record_count($gTables['staff_worked_hours'], "work_day = '" . $k_date . "' AND id_staff = ".$id_worker );
				if ($exist>=1){ // se ho già un record del lavoratore per quella data faccio UPDATE
				    $query = 'UPDATE ' . $gTables['staff_worked_hours'] . ' SET `id_staff`='.$id_worker.",`work_day`='".$k_date."',`hours_normal`=".$v['hours_normal'].',`id_work_type_extra`='.$v['id_work_type_extra'].',`hours_extra`='.$v['hours_extra'].',`id_absence_type`='.$v['id_absence_type'].',`hours_absence`='.$v['hours_absence'].',`id_other_type`='.$v['id_other_type'].',`hours_other`='.$v['hours_other'].",`note`='".$v['note']."' WHERE `id_staff`=".$id_worker." AND `work_day`='".$k_date."'";
					gaz_dbi_query($query);
				} else { // faccio l'INSERT
					$v['id_staff']=$id_worker;
					$v['work_day']=$k_date;
					gaz_dbi_table_insert('staff_worked_hours', $v);
				}
				header("Location: docume_humres.php"); 
			}
		}
	}
} else { // al primo accesso
	$dto = new DateTime();
    $form['week'] = $dto->format("W");
    $form['year'] = $dto->format("Y");
}

$week_days = getStartToEndDate($form['week'], $form['year']);

require("../../library/include/header.php");
?>
<script type="text/javascript">
    $(function () {
		$('.dropdownmenustyle').selectmenu();        
		$("#prev").click(function () {
			$("#goto").val('prev');
            this.form.submit();
        });
        $("#next").click(function () {
			$("#goto").val('next');
            this.form.submit();
        });
	});
	
</script>
<?php
$script_transl = HeadMain();
$gForm = new humresForm();
?>
<form method="POST" id="form">
    <div class="text-center"><b><?php echo $script_transl['title']; ?></b></div>
	<div class="panel panel-info" style="max-width:650px;">
		<div class="container-fluid">
			<div class="row">
				<div class="col-lg-12 text-center"><button type="button" class="btn btn-xs btn-default" id="prev"><i class="glyphicon glyphicon-chevron-left"></i><?php echo ucfirst($script_transl['prev']); ?></button> <b><?php echo intval($form["week"]).'^'; ?> SETTIMANA <?php echo ' -> dal '.gaz_format_date($week_days['mon']).' al '.gaz_format_date($week_days['sun']); ?> </b> <button type="button"  class="btn btn-xs btn-default" id="next"><i class="glyphicon glyphicon-chevron-right"></i><?php echo ucfirst($script_transl['next']); ?></button></div>
			</div>
	<?php
	$workers=getWorkers($week_days['sun']);
    foreach ($workers as $k => $v) {
		$form['rows'] = getStaffTimesheet($k,$week_days);
	?>
		<div class="row center-block">
			<div class="panel panel-default">
			<table>
                <thead>
                    <tr class="bg-success">              
                        <th class="col-xs-4">
							<?php echo $v['worker_descri']; ?>                       
						</th>
                        <th class="col-xs-1">
                            <?php echo utf8_encode(substr(strftime("%A", strtotime("01/01/2018")),0,3)). ' ' . intval(substr($week_days['mon'],8,2));  ?>
                        </th>
                        <th class="col-xs-1">
                            <?php echo utf8_encode(substr(strftime("%A", strtotime("01/02/2018")),0,3)). ' ' . intval(substr($week_days['tue'],8,2)); ?>
                        </th>
                        <th class="col-xs-1">
                            <?php echo utf8_encode(substr(strftime("%A", strtotime("01/03/2018")),0,3)). ' ' . intval(substr($week_days['wed'],8,2)); ?>
                        </th>
                        <th class="col-xs-1">
                            <?php echo utf8_encode(substr(strftime("%A", strtotime("01/04/2018")),0,3)). ' ' . intval(substr($week_days['thu'],8,2)); ?>
                        </th>
                        <th class="col-xs-1">
                            <?php echo utf8_encode(substr(strftime("%A", strtotime("01/05/2018")),0,3)). ' ' . intval(substr($week_days['fri'],8,2)); ?>
                        </th>
                        <th class="col-xs-1">
                            <?php echo utf8_encode(substr(strftime("%A", strtotime("01/06/2018")),0,3)). ' ' . intval(substr($week_days['sat'],8,2)); ?>
                        </th>
                        <th class="col-xs-1 bg-warning">
                            <?php echo utf8_encode(substr(strftime("%A", strtotime("01/07/2018")),0,3)). ' ' . intval(substr($week_days['sun'],8,2)); ?>
                        </th>
                    </tr>      
                </thead>    
                <tbody>
                    <tr>              
                        <td>
							<?php echo $script_transl['work_hou']; ?>  
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['mon']."][hours_normal]"; ?>"  maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['mon']]['hours_normal']; ?>">
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['tue']."][hours_normal]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['tue']]['hours_normal']; ?>">
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['wed']."][hours_normal]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['wed']]['hours_normal']; ?>">
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['thu']."][hours_normal]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['thu']]['hours_normal']; ?>">
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['fri']."][hours_normal]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['fri']]['hours_normal']; ?>">
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['sat']."][hours_normal]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['sat']]['hours_normal']; ?>">
                        </td>
                        <td class="bg-warning"><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['sun']."][hours_normal]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['sun']]['hours_normal']; ?>">
                        </td>
                    </tr>      
                    <tr>              
                        <td>
							<?php echo $script_transl['cau_hextra']; ?> 
                        </td>
                        <td>
						<?php
							$gForm->selectHextraType("rows[".$k."][".$week_days['mon']."][id_work_type_extra]",$form['rows'][$k][$week_days['mon']]['id_work_type_extra']);
						?>
						</td>
                        <td>						
						<?php
							$gForm->selectHextraType("rows[".$k."][".$week_days['tue']."][id_work_type_extra]",$form['rows'][$k][$week_days['tue']]['id_work_type_extra']);
						?>
                        </td>
                        <td>						
						<?php
							$gForm->selectHextraType("rows[".$k."][".$week_days['wed']."][id_work_type_extra]",$form['rows'][$k][$week_days['wed']]['id_work_type_extra']);
						?>
                        </td>
                        <td>						
						<?php
							$gForm->selectHextraType("rows[".$k."][".$week_days['thu']."][id_work_type_extra]",$form['rows'][$k][$week_days['thu']]['id_work_type_extra']);
						?>
                        </td>
                        <td>						
						<?php
							$gForm->selectHextraType("rows[".$k."][".$week_days['fri']."][id_work_type_extra]",$form['rows'][$k][$week_days['fri']]['id_work_type_extra']);
						?>
                        </td>
                        <td>						
						<?php
							$gForm->selectHextraType("rows[".$k."][".$week_days['sat']."][id_work_type_extra]",$form['rows'][$k][$week_days['sat']]['id_work_type_extra']);
						?>
                        </td>
                        <td class="bg-warning">						
						<?php
							$gForm->selectHextraType("rows[".$k."][".$week_days['sun']."][id_work_type_extra]",$form['rows'][$k][$week_days['sun']]['id_work_type_extra']);
						?>
                        </td>
                    </tr>      
                    <tr>              
                        <td>
							<?php echo $script_transl['work_hextra']; ?>  
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['mon']."][hours_extra]"; ?>"  maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['mon']]['hours_extra']; ?>">
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['tue']."][hours_extra]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['tue']]['hours_extra']; ?>">
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['wed']."][hours_extra]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['wed']]['hours_extra']; ?>">
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['thu']."][hours_extra]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['thu']]['hours_extra']; ?>">
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['fri']."][hours_extra]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['fri']]['hours_extra']; ?>">
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['sat']."][hours_extra]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['sat']]['hours_extra']; ?>">
                        </td>
                        <td class="bg-warning"><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['sun']."][hours_normal]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['sun']]['hours_extra']; ?>">
                        </td>
                    </tr>      
                    <tr>              
                        <td>
							<?php echo $script_transl['absence_cau']; ?> 
                        </td>
                        <td>
						<?php
							$gForm->selectAbsenceCau("rows[".$k."][".$week_days['mon']."][id_absence_type]",$form['rows'][$k][$week_days['mon']]['id_absence_type']);
						?>
						</td>
                        <td>						
						<?php
							$gForm->selectAbsenceCau("rows[".$k."][".$week_days['tue']."][id_absence_type]",$form['rows'][$k][$week_days['tue']]['id_absence_type']);
						?>
                        </td>
                        <td>						
						<?php
							$gForm->selectAbsenceCau("rows[".$k."][".$week_days['wed']."][id_absence_type]",$form['rows'][$k][$week_days['wed']]['id_absence_type']);
						?>
                        </td>
                        <td>						
						<?php
							$gForm->selectAbsenceCau("rows[".$k."][".$week_days['thu']."][id_absence_type]",$form['rows'][$k][$week_days['thu']]['id_absence_type']);
						?>
                        </td>
                        <td>						
						<?php
							$gForm->selectAbsenceCau("rows[".$k."][".$week_days['fri']."][id_absence_type]",$form['rows'][$k][$week_days['fri']]['id_absence_type']);
						?>
                        </td>
                        <td>						
						<?php
							$gForm->selectAbsenceCau("rows[".$k."][".$week_days['sat']."][id_absence_type]",$form['rows'][$k][$week_days['sat']]['id_absence_type']);
						?>
                        </td>
                        <td class="bg-warning">						
						<?php
							$gForm->selectAbsenceCau("rows[".$k."][".$week_days['sun']."][id_absence_type]",$form['rows'][$k][$week_days['sun']]['id_absence_type']);
						?>
                        </td>
                    </tr>      
                    <tr>              
                        <td>
							<?php echo $script_transl['absence_hou']; ?> 
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['mon']."][hours_absence]"; ?>"  maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['mon']]['hours_absence']; ?>">
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['tue']."][hours_absence]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['tue']]['hours_absence']; ?>">
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['wed']."][hours_absence]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['wed']]['hours_absence']; ?>">
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['thu']."][hours_absence]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['thu']]['hours_absence']; ?>">
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['fri']."][hours_absence]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['fri']]['hours_absence']; ?>">
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['sat']."][hours_absence]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['sat']]['hours_absence']; ?>">
                        </td>
                        <td class="bg-warning"><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['sun']."][hours_absence]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['sun']]['hours_absence']; ?>">
                        </td>
                    </tr>      
                    <tr>              
                        <td>
							<?php echo $script_transl['other_cau']; ?> 
                        </td>
                        <td>
						<?php
							$gForm->selectOtherType("rows[".$k."][".$week_days['mon']."][id_other_type]",$form['rows'][$k][$week_days['mon']]['id_other_type']);
						?>
						</td>
                        <td>						
						<?php
							$gForm->selectOtherType("rows[".$k."][".$week_days['tue']."][id_other_type]",$form['rows'][$k][$week_days['tue']]['id_other_type']);
						?>
                        </td>
                        <td>						
						<?php
							$gForm->selectOtherType("rows[".$k."][".$week_days['wed']."][id_other_type]",$form['rows'][$k][$week_days['wed']]['id_other_type']);
						?>
                        </td>
                        <td>						
						<?php
							$gForm->selectOtherType("rows[".$k."][".$week_days['thu']."][id_other_type]",$form['rows'][$k][$week_days['thu']]['id_other_type']);
						?>
                        </td>
                        <td>						
						<?php
							$gForm->selectOtherType("rows[".$k."][".$week_days['fri']."][id_other_type]",$form['rows'][$k][$week_days['fri']]['id_other_type']);
						?>
                        </td>
                        <td>						
						<?php
							$gForm->selectOtherType("rows[".$k."][".$week_days['sat']."][id_other_type]",$form['rows'][$k][$week_days['sat']]['id_other_type']);
						?>
                        </td>
                        <td class="bg-warning">						
						<?php
							$gForm->selectOtherType("rows[".$k."][".$week_days['sun']."][id_other_type]",$form['rows'][$k][$week_days['sun']]['id_other_type']);
						?>
                        </td>
                    </tr>      
                    <tr>              
                        <td>
							<?php echo $script_transl['other_qua']; ?> 
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['mon']."][hours_other]"; ?>"  maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['mon']]['hours_other']; ?>">
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['tue']."][hours_other]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['tue']]['hours_other']; ?>">
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['wed']."][hours_other]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['wed']]['hours_other']; ?>">
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['thu']."][hours_other]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['thu']]['hours_other']; ?>">
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['fri']."][hours_other]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['fri']]['hours_other']; ?>">
                        </td>
                        <td><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['sat']."][hours_other]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['sat']]['hours_other']; ?>">
                        </td>
                        <td class="bg-warning"><input class="form-control" name="<?php echo "rows[".$k."][".$week_days['sun']."][hours_other]"; ?>" maxlength="4" type="text" value="<?php echo $form['rows'][$k][$week_days['sun']]['hours_other']; ?>">
                        </td>
                    </tr>      
                    <tr>              
                        <td>
							<?php echo $script_transl['note']; ?> 
                        </td>
                        <td><textarea class="form-control" name="<?php echo "rows[".$k."][".$week_days['mon']."][note]"; ?>"><?php echo $form['rows'][$k][$week_days['mon']]['note']; ?></textarea>
                        </td>
                        <td><textarea class="form-control" name="<?php echo "rows[".$k."][".$week_days['tue']."][note]"; ?>"><?php echo $form['rows'][$k][$week_days['tue']]['note']; ?></textarea>
                        </td>
                        <td><textarea class="form-control" name="<?php echo "rows[".$k."][".$week_days['wed']."][note]"; ?>"><?php echo $form['rows'][$k][$week_days['wed']]['note']; ?></textarea>
                        </td>
                        <td><textarea class="form-control" name="<?php echo "rows[".$k."][".$week_days['thu']."][note]"; ?>"><?php echo $form['rows'][$k][$week_days['thu']]['note']; ?></textarea>
                        </td>
                        <td><textarea class="form-control" name="<?php echo "rows[".$k."][".$week_days['fri']."][note]"; ?>"><?php echo $form['rows'][$k][$week_days['fri']]['note']; ?></textarea>
                        </td>
                        <td><textarea class="form-control" name="<?php echo "rows[".$k."][".$week_days['sat']."][note]"; ?>"><?php echo $form['rows'][$k][$week_days['sat']]['note']; ?></textarea>
                        </td>
                        <td class="bg-warning"><textarea class="form-control" name="<?php echo "rows[".$k."][".$week_days['sun']."][note]"; ?>"><?php echo $form['rows'][$k][$week_days['sun']]['note']; ?></textarea>
                        </td>
                    </tr>      
				</tbody>     
            </table>
			</div><!-- chiude panel  -->
		</div>
	<?php
    }
	?>
			<div class="row">
				<div class="col-sm-8">
                <button name="go_insert" class="btn btn-sm btn-edit">
                    <i class="glyphicon glyphicon-edit">				
					<?php
					echo $script_transl['submit'];
					?>
					</i>
                </button>
				</div>
			</div>
        </div><!-- chiude container  -->
    </div><!-- chiude panel  -->
    <input type="hidden" name="year" id="year" value="<?php echo $form["year"]; ?>">
    <input type="hidden" name="week" id="week" value="<?php echo $form["week"]; ?>">
    <input type="hidden" name="goto" id="goto">
</form>
<div id="loader-icon"><img src="../../library/images/ui-anim_basic_16x16.gif" />
</div>  
<?php
require("../../library/include/footer.php");
?>