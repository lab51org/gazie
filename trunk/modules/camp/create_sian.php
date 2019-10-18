<?php
/*
 --------------------------------------------------------------------------
                            GAzie - Gestione Azienda
    Copyright (C) 2004-2017 - Antonio De Vincentiis Montesilvano (PE)
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
 // IL REGISTRO DI CAMPAGNA E' UN MODULO DI ANTONIO GERMANI - MASSIGNANO AP
// >> Creazione del file .txt di upload per il SIAN <<
require("../../library/include/datlib.inc.php");
require ("../../modules/magazz/lib.function.php");
if (!ini_get('safe_mode')){ //se me lo posso permettere...
    ini_set('memory_limit','128M');
    gaz_set_time_limit (0);
}

if ($handle = opendir('../../data/files/1/sian/')){
   while (false !== ($file = readdir($handle))){
       $prevfiles[]=$file;
   }
   closedir($handle);
}

$admin_aziend=checkAdmin();
$id_sian = gaz_dbi_get_row($gTables['company_config'], 'var', 'id_sian');

if (!isset ($id_sian) or intval($id_sian['val']==0)){ 
echo "errore manca id sian";
die;}
function getMovements($date_ini,$date_fin)
    {
        global $gTables,$admin_aziend;
        $m=array();
        $where="datdoc BETWEEN $date_ini AND $date_fin";
        $what=$gTables['movmag'].".*, ".
              $gTables['camp_mov_sian'].".*, ".
			  $gTables['artico'].".SIAN, ".
			  $gTables['anagra'].".ragso1, ".$gTables['anagra'].".id_SIAN, ".
			  $gTables['clfoco'].".id_anagra, ".
			  $gTables['rigdoc'].".id_tes, ".
			  $gTables['tesdoc'].".numdoc, ".
			  $gTables['lotmag'].".identifier, ".
			  $gTables['camp_artico'].".* ";
        $table=$gTables['movmag']." LEFT JOIN ".$gTables['camp_mov_sian']." ON (".$gTables['movmag'].".id_mov = ".$gTables['camp_mov_sian'].".id_movmag)
               LEFT JOIN ".$gTables['clfoco']." ON (".$gTables['movmag'].".clfoco = ".$gTables['clfoco'].".codice)
			   LEFT JOIN ".$gTables['camp_artico']." ON (".$gTables['movmag'].".artico = ".$gTables['camp_artico'].".codice)
               LEFT JOIN ".$gTables['artico']." ON (".$gTables['movmag'].".artico = ".$gTables['artico'].".codice)
			   LEFT JOIN ".$gTables['rigdoc']." ON (".$gTables['movmag'].".id_rif = ".$gTables['rigdoc'].".id_rig)
			   LEFT JOIN ".$gTables['tesdoc']." ON (".$gTables['rigdoc'].".id_tes = ".$gTables['tesdoc'].".id_tes)
			   LEFT JOIN ".$gTables['lotmag']." ON (".$gTables['movmag'].".id_mov = ".$gTables['lotmag'].".id_movmag)
			   LEFT JOIN ".$gTables['anagra']." ON (".$gTables['anagra'].".id = ".$gTables['clfoco'].".id_anagra)";
        $rs=gaz_dbi_dyn_query ($what,$table,$where, 'datreg ASC, tipdoc ASC, clfoco ASC, operat DESC, id_mov ASC');
        while ($r = gaz_dbi_fetch_array($rs)) {
            $m[] = $r;
        }
        return $m;
    }

$type_array=array(); // creo l'array formattato SIAN vuoto *** NON TOCCARE MAI!!! ***
$type="                ;0000000000;0000000000;        ;          ;        ;          ;0000000000;0000000000;0000000000000;0000000000000;          ;          ;0000000000;00;00;00;                                                                                ;00;                                                                                ;0000000000000;0000000000000;0000000000000;0000000000000;0000000000000;0000000000000;0000000000000;                    ;                                                                                                                                                                                                                                                                                                            ; ; ; ; ; ; ; ; ; ; ; ;                 ;                 ;0000;          ;          ;             ;        ;          ; ;";

$type_array= explode (";", $type);

$giori = substr($_GET['ri'],0,2);
$mesri = substr($_GET['ri'],2,2);
$annri = substr($_GET['ri'],4,4);
$utsri= mktime(0,0,0,$mesri,$giori,$annri);
$giorf = substr($_GET['rf'],0,2);
$mesrf = substr($_GET['rf'],2,2);
$annrf = substr($_GET['rf'],4,4);
$utsrf= mktime(0,0,0,$mesrf,$giorf,$annrf);

$giosta = substr($_GET['ds'],0,2);
$messta = substr($_GET['ds'],2,2);
$annsta = substr($_GET['ds'],4,4);

$progr=0;$datsta=$annsta.$messta.$giosta;
foreach ($prevfiles as $files){
	$f=explode("_",$files);
	if (isset($f[1])){ 
		if ($f[1]==$datsta){
			if($f[1]>$progr){
				$progr=$f[2];
			}
		}
	}
}
$progr++;
	
// Antonio Germani - creo il nome del file
$namefile=$admin_aziend['codfis']."_".$datsta."_".$progr."_OPERREGI.txt";

$result=getMovements(strftime("%Y%m%d",$utsri),strftime("%Y%m%d",$utsrf));

if (sizeof($result) > 0) { // se ci sono movimenti creo il file
	$myfile = fopen("../../data/files/1/sian/".$namefile, "w") or die("Unable to open file!");
	$nprog=0;
	while (list($key, $row) = each($result)) {
		if ($row['SIAN']>0) {
			if ( $_GET['ud']==str_replace("-", "", $row['datdoc']) AND $row['id_mov']<=$_GET['umv']) {
					// escludo i movimenti già inseriti null'ultimo file con stessa data
			} else {
					$nprog++;
					$row['quanti'] = sprintf ("%013d", str_replace(".", "", $row['quanti'])); // togli il separatore decimali perch? il SIAN non lo vuole. le ultime tre cifre sono sempre decimali. Aggiunge zeri iniziali.
					if (intval($row['numdoc'])==0){ // se il numero documento ? zero tolgo lo zero e annullo l'eventuale data
						$row['numdoc']="";$dd="";
					} else if (isset($row['datdoc'])) { //se c'è il numero e la data documento, imposto la data come GGMMAAA
						$gio = substr($row['datdoc'],8,2);
						$mes = substr($row['datdoc'],5,2);
						$ann = substr($row['datdoc'],0,4);
						$dd=$gio.$mes.$ann;
					}
					if ($row['operat']==1){ //se è un carico
						if ($row['SIAN']==1){ // se è olio
							if ($row['confezione']==0) { // se è sfuso
								$type_array[22]=$row['quanti'];
							} else { // se è confezionato
								$type_array[24]=$row['quanti'];
							}
						} else { //se sono olive
							$type_array[9]=$row['quanti'];
						}
						if ($row['cod_operazione']==3 OR $row['cod_operazione']==8 ){
							$type_array[7]=sprintf ("%010d",$row['id_SIAN']); // identificatore fornitore/cliente/terzista
						} 
						if ($row['cod_operazione']==0 OR $row['cod_operazione']==1 OR $row['cod_operazione']==2) {
							$type_array[8]=sprintf ("%010d",$row['id_SIAN']); // identificatore fornitore/cliente/terzista/committente
						}
						if ($row['cod_operazione']==5) {
							$type_array[13]=sprintf ("%010d",$row['id_SIAN']); // identificativo stabilimento di provenienza/destinazione olio
						}
					} else { // se è uno scarico
						if ($row['SIAN']==1){ // se è olio
							if ($row['confezione']==0) { // se è sfuso
								$type_array[23]=$row['quanti'];
							} else { // se è confezionato
								$type_array[25]=$row['quanti'];
							}
						} else { //se sono olive
							$type_array[10]=$row['quanti'];
						}
						if ($row['cod_operazione']==1 OR $row['cod_operazione']==2 OR $row['cod_operazione']==3 OR $row['cod_operazione']==10){
							$type_array[8]=sprintf ("%010d",$row['id_SIAN']); // identificatore fornitore/cliente/terzista/committente
						}
						if ($row['cod_operazione']==5 OR $row['cod_operazione']==6) {
							$type_array[7]=sprintf ("%010d",$row['id_SIAN']); // identificatore fornitore/cliente/terzista
						}
						if ($row['cod_operazione']==4) {
							$type_array[13]=sprintf ("%010d",$row['id_SIAN']); // identificativo stabilimento di provenienza/destinazione olio
						}
					}
		
					$type_array[0]=str_pad($admin_aziend['codfis'], 16); // aggiunge spazi finali
					$type_array[1]=sprintf ("%010d",$id_sian); // identificativo stabilimento/deposito
					$type_array[2]=sprintf ("%010d",$nprog); // num. progressivo
					$type_array[3]=str_pad($_GET['ds'], 8);//data dell'operazione
					$type_array[4]=str_pad($row['numdoc'], 10);// numero documento giustificativo
					$type_array[5]=str_pad($dd, 8);//data del documento giustificativo
					$type_array[6]=str_pad($row['cod_operazione'], 10); // codice operazione			
					$type_array[11]=str_pad($row['recip_stocc'], 10); // identificativo recipiente o silos di stoccaggio
					$type_array[12]=str_pad($row['recip_stocc_destin'], 10); // identificativo recipiente o silos di stoccaggio destinazione
					$type_array[14]=sprintf ("%02d",$row['categoria']); // Categoria olio
					$type_array[16]=sprintf ("%02d",$row['or_macro']); // Origine olio per macro area
					$type_array[16]=sprintf ("%02d",$row['or_spec']); // Origine olio specifica
					$type_array[27]=str_pad($row['identifier'], 20); // Lotto di appartenenza
					if ($row['estrazione']=1){
						$type_array[30]="X"; // Flag prima spremitura a freddo
					}
					if ($row['estrazione']=2){
						$type_array[32]="X"; // Flag estratto a freddo
					}
					if ($row['biologico']=1){
						$type_array[34]="X"; // Flag biologico
					}
					if ($row['biologico']=2){
						$type_array[36]="X"; // Flag in conversione
					}
					if ($row['etichetta']=0){
						$type_array[38]="X"; // Flag NON etichettato
					}
					if ($row['confezione']>0){
						$type_array[45]=sprintf ("%013d", str_replace(".", "", $row['confezione'])); // capacità confezione
					}
					$type_array[48]="I";
					$type= implode(";",$type_array);
					fwrite($myfile, $type);
					$ulmvsian=$row['id_mov'];
			}
		}
	}
	fclose($myfile);
}
// aggiorno l'ultimo movmag inviato al SIAN
gaz_dbi_put_row($gTables['company_data'], 'var', 'ulmvsian', 'data', $ulmvsian);
echo "file generato e salvato";die;
?>