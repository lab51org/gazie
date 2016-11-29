<?php
    if (!strstr($_SERVER["REQUEST_URI"], "login_admin") == "login_admin.php") {
        $_SESSION['lastpage'] = $_SERVER["REQUEST_URI"];
    }
    $menuclass = ' class="FacetMainMenu" ';
    $style = 'default.css';
    $skin = 'default.css'; 
    if (isset($_POST['logout'])) {
        header("Location: logout.php");
        exit;
    }   
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $admin_aziend['ragso1'];?></title>
    <link rel="shortcut icon" href="../../library/images/favicon.ico">			
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="../../library/theme/lte/adminlte/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../library/theme/lte/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="../../library/theme/lte/ionicons/css/ionicons.min.css">
    <link rel="stylesheet" href="../../library/theme/lte/adminlte/dist/css/AdminLTE.css">
    <link rel="stylesheet" href="../../library/theme/lte/adminlte/dist/css/skins/_all-skins.min.css">
    <script src="../../library/theme/lte/adminlte/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    
    <?php
        if (!empty($admin_aziend['style']) && file_exists("../../library/theme/lte/skeletons/" . $admin_aziend['style'])) {
		$style = $admin_aziend['style'];
	}
	if (!empty($admin_aziend['skin']) && file_exists("../../library/theme/lte/skins/" . $admin_aziend['skin'])) {
		$skin = $admin_aziend['skin'];
	}
    ?>
    <!--<link href="../../library/theme/lte/scheletons/<?php echo $style; ?>" rel="stylesheet" type="text/css" />
    <link href="../../library/theme/lte/skins/<?php echo $skin; ?>" rel="stylesheet" type="text/css" />-->
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body class="sidebar-collapse hold-transition skin-blue sidebar-mini">
    <form method="POST" name="head_form" action="../../modules/root/admin.php">
    <div class="wrapper">

      <header class="main-header">
        <!-- Logo -->
        <a href="../../modules/root/admin.php" class="logo">
          <!-- mini logo for sidebar mini 50x50 pixels -->
          <span class="logo-mini"><b>G<?php echo $versSw; ?></b></span>
          <!-- logo for regular state and mobile devices -->
          <span class="logo-lg"><b>GA</b>zie <?php echo $versSw; ?></span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
          <!-- Sidebar toggle button-->
          <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
          </a>
      
          <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
            
          <!-- Messages: style can be found in dropdown.less-->
          <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-star"></i>
              <!--<span class="label label-success">4</span>-->
            </a>
            <ul class="dropdown-menu">
              <!--<li class="header">You have 4 messages</li>-->
              <li>
                <!-- inner menu: contains the actual data -->
                <ul class="menu">
<?php
    $result   = gaz_dbi_dyn_query("*", $gTables['menu_usage'], ' company_id="' . $admin_aziend['company_id'] . '" AND adminid="' . $admin_aziend['Login'] . '" ', ' click DESC, last_use DESC', 0, 8);
    $res_last = gaz_dbi_dyn_query("*", $gTables['menu_usage'], ' company_id="' . $admin_aziend['company_id'] . '" AND adminid="' . $admin_aziend['Login'] . '" ', ' last_use DESC, click DESC', 0, 8);

    if (gaz_dbi_num_rows($result) > 0) {
                while ($r = gaz_dbi_fetch_array($result)) {
                    $rref = explode('-', $r['transl_ref']);
                    $rl = gaz_dbi_fetch_array($res_last);
                    $rlref = explode('-', $rl['transl_ref']);
                    switch ($rref[1]) {
                        case 'm1':
                            require '../' . $rref[0] . '/menu.' . $admin_aziend['lang'] . '.php';
                            $rref_name = $transl[$rref[0]]['title'];
                            break;
                        case 'm2':
                            require '../' . $rref[0] . '/menu.' . $admin_aziend['lang'] . '.php';
                            $rref_name = $transl[$rref[0]]['m2'][$rref[2]][0];
                            break;
                        case 'm3':
                            require '../' . $rref[0] . '/menu.' . $admin_aziend['lang'] . '.php';
                            $rref_name = $transl[$rref[0]]['m3'][$rref[2]][0];
                            break;
                        case 'sc':
                            require '../' . $rref[0] . '/lang.' . $admin_aziend['lang'] . '.php';
                            $rref_name = $strScript[$rref[2]][$rref[3]];
                            break;
                        default:
                            $rref_name = 'Nome script non trovato';
                            break;
                    }
                    switch ($rlref[1]) {
                        case 'm1':
                            require '../' . $rlref[0] . '/menu.' . $admin_aziend['lang'] . '.php';
                            $rlref_name = $transl[$rlref[0]]['title'];
                            break;
                        case 'm2':
                            require '../' . $rlref[0] . '/menu.' . $admin_aziend['lang'] . '.php';
                            $rlref_name = $transl[$rlref[0]]['m2'][$rlref[2]][0];
                            break;
                        case 'm3':
                            require '../' . $rlref[0] . '/menu.' . $admin_aziend['lang'] . '.php';
                            $rlref_name = $transl[$rlref[0]]['m3'][$rlref[2]][0];
                            break;
                        case 'sc':
                            require '../' . $rlref[0] . '/lang.' . $admin_aziend['lang'] . '.php';
                            $rlref_name = $strScript[$rlref[2]][$rlref[3]];
                            break;
                        default:
                            $rlref_name = 'Nome script non trovato';
                            break;
                    }?>
                  <li><!-- start message -->
                    <a href="<?php
                            if ($r["link"] != "")
                                echo '../../modules' . $r["link"];
                            else
                                echo "&nbsp;";
                            ?>">
                      <div class="pull-left">
                          <i class="fa fa-archive" style="color:#<?php echo $r["color"]; ?>"></i>
                      </div>
                      <h4>
                        <?php echo substr( $rref_name, 0, 25); ?>
                        <small><i class="fa fa-thumbs-o-up"></i> <?php echo $r["click"] . ' click'; ?></small>
                      </h4>
                      <p><?php echo $r["link"]; ?></p>
                    </a>
                  </li>  
                  <?php
                }
    }
?>
                </ul>
              </li>
              <li class="footer"><a href="#">See All Messages</a></li>
            </ul>
          </li>
          
          <!-- Sezione link più usati -->
          <li class="dropdown messages-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-clock-o"></i>
              <!--<span class="label label-success">4</span>-->
            </a>
            <ul class="dropdown-menu">
              <!--<li class="header">You have 4 messages</li>-->
              <li>
                <!-- inner menu: contains the actual data -->
                <ul class="menu">
                  <li><!-- start message -->
                    <a href="#">
                      <div class="pull-left">
                        <img src="../../dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                      </div>
                      <h4>
                        Support Team
                        <small><i class="fa fa-clock-o"></i> 5 mins</small>
                      </h4>
                      <p>Why not buy a new awesome theme?</p>
                    </a>
                  </li>
                  <!-- end message -->
                  <li>
                    <a href="#">
                      <div class="pull-left">
                        <img src="../../dist/img/user3-128x128.jpg" class="img-circle" alt="User Image">
                      </div>
                      <h4>
                        AdminLTE Design Team
                        <small><i class="fa fa-clock-o"></i> 2 hours</small>
                      </h4>
                      <p>Why not buy a new awesome theme?</p>
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <div class="pull-left">
                        <img src="../../dist/img/user4-128x128.jpg" class="img-circle" alt="User Image">
                      </div>
                      <h4>
                        Developers
                        <small><i class="fa fa-clock-o"></i> Today</small>
                      </h4>
                      <p>Why not buy a new awesome theme?</p>
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <div class="pull-left">
                        <img src="../../dist/img/user3-128x128.jpg" class="img-circle" alt="User Image">
                      </div>
                      <h4>
                        Sales Department
                        <small><i class="fa fa-clock-o"></i> Yesterday</small>
                      </h4>
                      <p>Why not buy a new awesome theme?</p>
                    </a>
                  </li>
                  <li>
                    <a href="#">
                      <div class="pull-left">
                        <img src="../../dist/img/user4-128x128.jpg" class="img-circle" alt="User Image">
                      </div>
                      <h4>
                        Reviewers
                        <small><i class="fa fa-clock-o"></i> 2 days</small>
                      </h4>
                      <p>Why not buy a new awesome theme?</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="footer"><a href="#">See All Messages</a></li>
            </ul>
          </li>
                
              <!-- User Account: style can be found in dropdown.less -->
              <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <img src="<?php echo '../root/view.php?table=admin&field=Login&value=' . $admin_aziend['Login']; ?>" class="user-image" alt="User Image">
                  <span class="hidden-xs"><?php echo $admin_aziend['Nome'].' '.$admin_aziend['Cognome']; ?></span>
                </a>
                <ul class="dropdown-menu">
                  <!-- User image -->
                  <li class="user-header">
                    <img src="<?php echo '../root/view.php?table=admin&field=Login&value=' . $admin_aziend['Login']; ?>" class="img-circle" alt="User Image">
                    <p>
                      <?php echo $admin_aziend['Nome'].' '.$admin_aziend['Cognome']; ?>
                      <small>
                      Questo Ã¨ il tuo <b><?php echo $admin_aziend['Access']; ?>Â°</b> accesso<br/>
                      La tua password risale al <b><?php echo gaz_format_date($admin_aziend['datpas']);?></b><br>
                      </small>
                    </p>
                  </li>
                  <!-- Menu Body -->
                  <li class="user-body">
                    <div class="col-xs-4 text-center">
                      <a href="../config/admin_aziend.php">
                        <img class="img-circle usr-picture" src="../../modules/root/view.php?table=aziend&value=<?php echo $admin_aziend['company_id']; ?>" width="70" alt="Logo" border="0" title="<?php echo $script_transl['upd_company']; ?>" >
                     </a>
                    </div>
                    <div class="col-xs-8 text-center" align="center">
                      <a href="../../modules/root/cambia_azien.php"><?php echo $admin_aziend['ragso1']."<br>".$admin_aziend['ragso2']; ?></a> 
                      <?php //selectCompany('company_id', $form['company_id'], $form['search']['company_id'], $form['hidden_req'], $script_transl['mesg_co']); ?>
                    </div>
                    <!--<div class="col-xs-4 text-center">
                      <a href="#">Friends</a>
                    </div>-->
                  </li>
                  <!-- Menu Footer-->
                  <li class="user-footer">
                    <div class="pull-left">
                      <a href="../../modules/config/admin_utente.php?Login=<?php echo $admin_aziend['Login']; ?>&Update" class="btn btn-default btn-flat">Profilo</a>
                    </div>
                    <div class="pull-right">
                      <input name="logout" type="submit" value=" Logout " class="btn btn-default btn-flat">
                    </div>
                  </li>
                </ul>
              </li>
              <!-- Control Sidebar Toggle Button -->
              <li>
                <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
          <!-- Sidebar user panel -->
          <!--<div class="user-panel">
            <div class="pull-left image">
              <img src="<?php //echo '../root/view.php?table=admin&field=Login&value=' . $admin_aziend['Login']; ?>" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
              <p><?php //echo $admin_aziend['Nome'].' '.$admin_aziend['Cognome']; ?></p>
              <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
            </div>
          </div>
          <!-- search form 
          <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
              <input type="text" name="q" class="form-control" placeholder="Search...">
              <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
              </span>
            </div>
          </form>-->
          <ul class="sidebar-menu">
            <!--<li class="header">MENU' PRINCIPALE</li>-->
<?php

function HeadMain($idScript = '', $jsArray = '', $alternative_transl = false, $cssArray = '') {
    global $module, $admin_aziend, $radix, $scriptname;
    if (is_array($jsArray)) {
        foreach ($jsArray as $v) {
            echo '			<script type="text/javascript" src="../../js/' . $v . '.js"></script>';
        }
    }
    if (is_array($cssArray)) {
        foreach ($cssArray as $v) {
            echo '			<link rel="stylesheet" type="text/css" href="../../modules/' . $v . '">';
        }
    }
    $result = getAccessRights($_SESSION['Login'], $_SESSION['company_id']);
    if (gaz_dbi_num_rows($result) > 0) {
        // creo l'array associativo per la generazione del menu con JSCookMenu
        $ctrl_m1 = 0;
        $ctrl_m2 = 0;
        $ctrl_m3 = 0;
        $menuArray = array();
        $transl = array();
        while ($row = gaz_dbi_fetch_array($result)) {
            if ($row['access'] == 3) {
                if ($ctrl_m1 != $row['m1_id']) {
                    require("../../modules/" . $row['name'] . "/menu.".$admin_aziend['lang'].".php");
                }
                if ($row['name'] == $module) {
                    $row['weight'] = 0;

                    if ($row['m3_link'] == $scriptname) {
                        $title_from_menu = $transl[$row['name']]['m3'][$row['m3_trkey']][0];
                    }

                    if ($ctrl_m2 != $row['m2_id'] and $ctrl_m1 != $row['m1_id']) {
                        require("../../modules/" . $row['name'] . "/lang.".$admin_aziend['lang'].".php");
                        if (isset($strScript[$scriptname])) { // se Ã¨ stato tradotto lo script lo ritorno al chiamante
                            $translated_script = $strScript[$scriptname];
                            if (isset($translated_script['title'])) {
                                $title_from_menu = $translated_script['title'];
                            }
                        }
                    }
                }
                if (isset($row['m3_id']) and $row['m3_id'] > 0) { // Ã¨ un menu3
                    if ($ctrl_m2 != $row['m2_id'] and $ctrl_m1 != $row['m1_id']) { // Ã¨ pure il primo di menu2 e menu1
                        $menuArray[$row['weight']] = array('link' => '../' . $row['name'] . '/' . $row['link'],
                            'icon' => $row['icon'],
                            'name' => $transl[$row['name']]['name'],
                            'title' => $transl[$row['name']]['title'],
                            'class' => $row['class']);
                        $menuArray[$row['weight']][$row['m2_weight']] = array('link' => '../' . $row['name'] . '/' . $row['m2_link'],
                            'icon' => '../' . $row['name'] . '/' . $row['m2_icon'],
                            'name' => $transl[$row['name']]['m2'][$row['m2_trkey']][1],
                            'title' => $transl[$row['name']]['m2'][$row['m2_trkey']][0],
                            'class' => $row['m2_class']);
                    } elseif ($ctrl_m2 != $row['m2_id']) { // Ã¨ solo il primo di menu2
                        $menuArray[$row['weight']][$row['m2_weight']] = array('link' => '../' . $row['name'] . '/' . $row['m2_link'],
                            'icon' => '../' . $row['name'] . '/' . $row['m2_icon'],
                            'name' => $transl[$row['name']]['m2'][$row['m2_trkey']][1],
                            'title' => $transl[$row['name']]['m2'][$row['m2_trkey']][0],
                            'class' => $row['m2_class']);
                    }
                    $menuArray[$row['weight']][$row['m2_weight']][$row['m3_weight']] = array('link' => '../' . $row['name'] . '/' . $row['m3_link'],
                        'icon' => '../' . $row['name'] . '/' . $row['m3_icon'],
                        'name' => $transl[$row['name']]['m3'][$row['m3_trkey']][1],
                        'title' => $transl[$row['name']]['m3'][$row['m3_trkey']][0],
                        'class' => $row['m3_class']);
                } elseif ($ctrl_m1 != $row['m1_id']) { // Ã¨ il primo di menu2
                    $menuArray[$row['weight']] = array('link' => '../' . $row['name'] . '/' . $row['link'],
                        'icon' => $row['icon'],
                        'name' => $transl[$row['name']]['name'],
                        'title' => $transl[$row['name']]['title'],
                        'class' => $row['class']);
                    $menuArray[$row['weight']][$row['m2_weight']] = array('link' => '../' . $row['name'] . '/' . $row['m2_link'],
                        'icon' => '../' . $row['name'] . '/' . $row['m2_icon'],
                        'name' => $transl[$row['name']]['m2'][$row['m2_trkey']][1],
                        'title' => $transl[$row['name']]['m2'][$row['m2_trkey']][0],
                        'class' => $row['m2_class']);
                } else { // non Ã¨ il primo di menu2
                    $menuArray[$row['weight']][$row['m2_weight']] = array('link' => '../' . $row['name'] . '/' . $row['m2_link'],
                        'icon' => '../' . $row['name'] . '/' . $row['m2_icon'],
                        'name' => $transl[$row['name']]['m2'][$row['m2_trkey']][1],
                        'title' => $transl[$row['name']]['m2'][$row['m2_trkey']][0],
                        'class' => $row['m2_class']);
                }
            }
            $ctrl_m1 = $row['m1_id'];
            $ctrl_m2 = $row['m2_id'];
            $ctrl_m3 = $row['m3_id'];
        }
        //ksort($menuArray);

        if (!empty($idScript)) {
            if (is_array($idScript)) { // $idScript dev'essere un array con index [0] per il numero di menu e index[1] per l'id dello script
                if ($idScript[0] == 2) {
                    echo '			&raquo;' . $transl[$module]['m2'][$idScript[1]][0];
                } elseif ($idScript[0] == 3) {
                    echo '			&raquo;' . $transl[$module]['m3'][$idScript[1]][0];
                }
            } elseif ($idScript > 0) {
                echo '			&raquo;' . $transl[$module]['m3'][$idScript][0];
            }
        } elseif (isset($title_from_menu)) {
            //echo '			&raquo;' . $title_from_menu;
        }

    
    $i = 0;
    $colors = array ( "#00CD66", "#DC143C", "#20B2AA", "#FAFAD2", "#CD8500", "#EEEE00", "#B7B7B7", "#20B2AA", "#00FF7F", "#FFDAB9", "#006400" );   
    foreach ($menuArray as $link) {
        if ( $i==0 ) {
            echo "<li class=\"treeview\">";
            echo "  <a href=\"".$link['link']."\">";
            //echo "    <i class=\"".$link['icon']."\"></i>";
            echo "    <i style=\"color:".$colors[$i]."\" class=\"fa fa-circle-o\"></i>";
            echo "      <span>".$link['name']."</span>";
            echo "        <i class=\"fa fa-angle-left pull-right\"></i>";
            echo "  </a>";
        } else {
            echo "<li class=\"treeview\">\n";
            echo "  <a href=\"". $link['link'] ."\">\n";
            echo "    <i style=\"color:".$colors[$i]."\" class=\"fa fa-circle-o\"></i>\n";
            echo "      <span>". $link['name'] ."</span>\n";
            echo "    <i class=\"fa fa-angle-left pull-right\"></i>\n";
            echo "  </a>\n";
        }
        submenu($link, $i);
        echo "          </li>\n";
        $i++;
    }
?>
    </ul>
    </section>
    </aside>
</form>
    <div class="content-wrapper">
      <section class="content-header">
         <h1>
         <!--TITOLO<?php echo $script_transl['title']; ?>-->
         </h1>
         <ol class="breadcrumb">
         <?php
            global $gTables;
            $posizione = explode( '/',$_SERVER['REQUEST_URI'] );
            $posizione = array_pop( $posizione );
            if ( $posizione == "report_received.php" ) $posizione = "report_scontr.php";
            $result    = gaz_dbi_dyn_query("*", $gTables['menu_module'] , ' link="'.$posizione.'" ',' id',0,1);
            if ( !gaz_dbi_num_rows($result)>0 ) {
                $posizione = explode ("?",$posizione );
                $result    = gaz_dbi_dyn_query("*", $gTables['menu_module'] , ' link="'.$posizione[0].'" ',' id',0,1);	
            }
            $riga = gaz_dbi_fetch_array($result);
            if ( $riga["id"]!="" ) {
                $result2 = gaz_dbi_dyn_query("*", $gTables['menu_script'] , ' id_menu='.$riga["id"].' ','id',0);
                while ($r = gaz_dbi_fetch_array($result2)) {
                    echo '<li><a href="'.$r["link"].'">'.stripslashes ($transl[$module]["m3"][$r["translate_key"]]["1"]).'</a></li>';
                }
            }
         ?>
         </ol>
      </section>

<?php
    }
    if (!isset($translated_script)) {
        if ($alternative_transl) { // se e' stato passato il nome dello script sul quale mi devo basare per la traduzione
            $translated_script = $strScript[$alternative_transl . '.php'];
        } else {
            $translated_script = array($module);
        }
    }
    require("../../language/".$admin_aziend['lang']."/menu.inc.php");
    echo '<script type="text/javascript">
		 countclick = 0;
		 function chkSubmit() {
			if(countclick > 0) {
				alert("' . $strCommon['wait_al'] . '");
				document.getElementById(\'preventDuplicate\').disabled=true;
				return false;
			} else {
				var alPre = document.getElementById(\'confirmSubmit\').value.toString();
				if (alPre) {
					var conf = confirm (alPre);
					if (!conf) {
						document.getElementById(\'preventDuplicate\').disabled=true;
						return true;
					}
				}
				countclick++;
				document.getElementById(\'preventDuplicate\').hidden=true;
				return true;
			}
		 }
		 </script>
		 <!--<div class="container" role="main">-->';
         return ($strCommon + $translated_script);
}

function submenu($array, $index, $sub="") {
    if(!is_array($array)) { return ;}
    $numsub = 0;
    foreach($array as $i => $mnu) {
        if(!is_array($mnu)) {continue;}      
	$submnu = '';
	if ($numsub === 0) {
            echo "<ul class=\"treeview-menu\">";
        }       
	if (count($mnu)>5) {
            echo "<li>";
            $sub = '<a href="'. $mnu["link"] .'">Lista '.$submnu.stripslashes($mnu["name"]);
            echo "  <a href=\"#\" hint=\"".$submnu.stripslashes($mnu["name"])."\">". substr($submnu.stripslashes($mnu["name"]),0,20);
            echo "      <i class=\"fa fa-angle-left pull-right\"></i>";
            echo "  </a>";                    
            submenu($mnu, 1, $sub);
            $sub="";
            echo "</li>";
        } else { 
            if ( $sub!="" ) {
                echo "<li>$sub</a></li>";
                $sub="";
            }
            echo "<li >";
            echo "  <a href=\"". $mnu['link'] ."\">". substr($submnu.stripslashes($mnu['name']),0,20) ."</a>";
            echo "</li>";
        }
	$numsub++;
    }
    if ($numsub > 0) {
        echo "    </ul>";
    }
}
?>