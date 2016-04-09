<?php
/*
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2016 - Antonio De Vincentiis Montesilvano (PE)
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
global $gTables;

function submenu($menu_data) {
    if (!is_array($menu_data)) {
        return;
    }
    $numsub = 0;
    $submenu = '';
    foreach ($menu_data as $i => $mnu) {
        if (!is_array($mnu)) {
            continue;
        }
        $submnu = '';
        if ($numsub === 0) {
            echo "\n\t\t\t\t\t\t\t" . '<ul class="dropdown-menu">' . "\n";
        }
        if (preg_match("/^[A-Za-z0-9!@#$%&()*;:_.'\/\\\\ ]+\.png$/", $mnu['icon'])) {
            $submnu = '<img src="' . $mnu['icon'] . '" /> ';
        }
        $submnu = '<a href="' . $mnu['link'] . '">' . $submnu . stripslashes($mnu['name']) . "</a>";
        if (count($mnu) > 5) { //	Esiste un sotto menu
            echo "\t\t\t\t\t\t\t" . '<li class="dropdown-submenu">' . $submnu;
            submenu($mnu);
            echo "\t\t\t\t\t\t\t</li>\n";
        } else {
            echo "\t\t\t\t\t\t\t<li>" . $submnu . "</li>\n";
        }
        $numsub++;
        if ($numsub == 0) {
            echo "\t\t\t\t\t\t\t</ul>\n";
        }
    }
    if ($numsub > 0) {
        echo "\t\t\t\t\t\t\t</ul>\n";
    }
}

//preparo la query per la seconda barra 
$posizione = explode('/', $_SERVER['REQUEST_URI']);
$posizione = array_pop($posizione);
//cambio la posizione manualmente per far apparire la seconda barra in questi moduli i report sono invertiti
if ($posizione == "report_received.php")
    $posizione = "report_scontr.php";
if ($posizione == "report_aziend.php")
    $posizione = "admin_aziend.php";
$result = gaz_dbi_dyn_query("*", $gTables['menu_module'], ' link="' . $posizione . '" ', ' id', 0, 1);

if (!gaz_dbi_num_rows($result) > 0) {
    $posizione = explode("?", $posizione);
    $result = gaz_dbi_dyn_query("*", $gTables['menu_module'], ' link="' . $posizione[0] . '" ', ' id', 0, 1);
}

//aggiungo classe per spaziare in caso di assenza seconda barra
$classe_barra1 = "";
$riga = gaz_dbi_fetch_array($result);

if ($riga["id"] != "") {
    $result2 = gaz_dbi_dyn_query("*", $gTables['menu_script'], ' id_menu=' . $riga["id"] . ' ', 'id', 0);
    if (gaz_dbi_num_rows($result2) <= 0) {
        $classe_barra1 = " nav-mb";
    }
} else {
    $classe_barra1 = " nav-mb";
}
?>


<nav class="navbar navbar-default nav-boot" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#gazNavbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
                <a  href="../../modules/root/admin.php"> <?php echo strtoupper($admin_aziend["ragso1"]); ?>
                    <img src="../../modules/root/view.php?table=aziend&amp;value=<?php echo $admin_aziend["company_id"]; ?>" height="35" alt="Logo" border="0" title="<?php echo $admin_aziend["ragso1"]; ?>" />  
                </a>
        </div>
        <div class="collapse navbar-collapse" id="gazNavbar">
            <ul class="nav navbar-nav">
                <?php
                // stampo la prima voce della barra del menù con il dropdown dei moduli 
                $i = 0;
                foreach ($menuArray as $menu_modules_val) {
                    if ($i == 0) { // sul modulo attivo non permetto i submenu in quanto verrano messi sulla barra orizzontale 
                        echo "\t\t\t\t<li class=\"dropdown\">" . '<a class="dropdown-toggle" data-toggle="dropdown"><img src="' . $menu_modules_val["icon"] . '"/>&nbsp;' . $menu_modules_val['name'] . '<span class="caret"></span></a>';
                        echo "\n\t\t\t\t\t" . '<ul class="dropdown-menu">' . "\n";
                    } else {
                        echo "\t\t\t\t\t";
                        echo '<li class="dropdown-submenu"><a href="' . $menu_modules_val['link'] . '"><img src="' . $menu_modules_val["icon"] . '"/>&nbsp;' . $menu_modules_val['name'] . "</a>\n";
                        submenu($menu_modules_val);
                        echo "\t\t\t\t\t</li>\n";
                    }
                    $i++;
                }
                // fine stampa prima voce menu
                ?>
            </ul>
            </li>
            <?php
            $i = 0;
            foreach ($menuArray[0] as $menu) {
                // stampo nella barra del menù il dropdown del modulo 
                $icon_lnk = '';
                if (isset($menu['icon']) && preg_match("/^[A-Za-z0-9!@#$%&()*;:_.'\/\\\\ ]+\.png$/", $menu['icon'])) {
                    $icon_lnk = '<img src="' . $menu['icon'] . '" />';
                }
                if ($i > 4) { // perché ci sono 5 indici prima dei dati veri e propri
                    if (count($menu) > 5) {
                        echo "\t\t\t" . '<li class="dropdown">'
                        . '<a href="' . $menu['link'] . '">' . $icon_lnk . ' ' . $menu['name'] . '<span class="caret"></span></a>';
                    } else {
                        echo "\t\t\t" . '<li><a class="row-menu" href="'.$menu['link'].'">'.$icon_lnk.''.$menu['name'].'</a>';
                    }
                    submenu($menu);
                    echo "\t\t\t\t\t</li>\n";
                    $livello3 = $menu;
                }
                $i++;
            }
            ?>
            <li>
            </li>
            </ul>
        </div>
    </div><!-- chiude div container-fluid -->
</nav><!-- chiude navbar -->
<?php
if ($riga["id"] != "") {
    $result2 = gaz_dbi_dyn_query("*", $gTables['menu_script'], ' id_menu=' . $riga["id"] . ' ', 'id', 0);
    if (gaz_dbi_num_rows($result2) > 0) {
        if (is_array($posizione))
            $posizione = $posizione[0];
        if (isset($_GET['auxil']))
            $auxil = $_GET['auxil'];
        else
            $auxil = "";
        ?>
        <nav class="navbar navbar-default navbar-lower nav-mb" role="navigation">
            <div class="navbar-form navbar-left" role="search">
                <div class="btn-toolbar" role="toolbar">
                
        <?php
        while ($r = gaz_dbi_fetch_array($result2)) {
            echo '<div class="btn-group btn-group-xs"><a href="' . $r["link"] . '"  role="button" class="btn btn-default">' . stripslashes($transl[$module]["m3"][$r["translate_key"]]["1"]) . '</a></div>';
        }
        if (file_exists("function_menu.php")) {
            include "function_menu.php";
        }
        ?>
                </div>
            </div>
        </nav>
        <?php
    }
}
?>