<?php
/*
 --------------------------------------------------------------------------
  GAzie - MODULO 'VACATION RENTAL'
  Copyright (C) 2022-2023 - Antonio Germani, Massignano (AP)
  (http://www.programmisitiweb.lacasettabio.it)

  --------------------------------------------------------------------------
  --------------------------------------------------------------------------
  GAzie - Gestione Azienda
  Copyright (C) 2004-2023 - Antonio De Vincentiis Montesilvano (PE)
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
// prevent direct access
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if (!$isAjax) {
    $user_error = 'Access denied - not an AJAX request...';
    trigger_error($user_error, E_USER_ERROR);
}
use Ddeboer\Imap\Server;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
require("../../library/include/datlib.inc.php");
require("../../modules/magazz/lib.function.php");
$admin_aziend=checkAdmin();
$libFunc = new magazzForm();
if (isset($_GET['term'])) {
    if (isset($_GET['opt'])) {
        $opt = $_GET['opt'];
    } else {
        $opt = 'orders';
    }
    switch ($opt) {
      case 'point':

        $points_expiry = gaz_dbi_get_row($gTables['company_config'], 'var', 'points_expiry')['val'];
        if (is_numeric($_GET['points']) && intval($_GET['points'])<>0 && strlen($_GET['motive'])>2){
          $result = gaz_dbi_get_row($gTables['anagra'], "id", intval($_GET['ref']));
          if (isset($result['custom_field']) && $data = json_decode($result['custom_field'],true)){// se c'è un json in anagra lo acquisisco in $data
            if (isset($data['vacation_rental']['points'])){

              if (intval($points_expiry)>0){// se i punti hanno una scadenza
                $date=(isset($data['vacation_rental']['points_date']))?date_create($data['vacation_rental']['points_date']):date_create("2023-09-01");
                date_add($date,date_interval_create_from_date_string(intval($points_expiry)." days"));// aggiungo la durata dei punti
                if (strtotime(date_format($date,"Y-m-d")) < strtotime(date("Y-m-d"))){// se i punti sono scaduti
                  echo "I vecchi punti scaduti sono stati cancellati. ";
                  $data['vacation_rental']['points'] = intval($_GET['points']);// cancello i vecchi e inserisco i nuovi
                }else{// i punti accumulati sono validi
                  $data['vacation_rental']['points'] = intval($data['vacation_rental']['points'])+intval($_GET['points']);// aggiungo i nuovi ai vecchi
                }
              }else{// i punti non hano scadenza
                $data['vacation_rental']['points'] = intval($data['vacation_rental']['points'])+intval($_GET['points']);// aggiungo i nuovi ai vecchi
              }

            }else{// se non ci sono mai stati punti
              $data['vacation_rental']['points']=intval($_GET['points']);
            }
            $data['vacation_rental']['points_date']=date("Y-m-d");
            $data['vacation_rental']['points']=(intval($data['vacation_rental']['points'])<0)?0:$data['vacation_rental']['points'];// evito di mandare i punti in negativo
            $custom_field = json_encode($data);
            gaz_dbi_update_anagra(array('id', intval($_GET['ref'])), array('custom_field'=>$custom_field,));
            echo "Punti attribuiti correttamente. Totale attuale: ",$data['vacation_rental']['points'];
            if ($_GET['email']=="true" && (filter_var($result['e_mail'], FILTER_VALIDATE_EMAIL) || filter_var($result['e_mail2'], FILTER_VALIDATE_EMAIL))){
              $language=gaz_dbi_get_row($gTables['languages'], "lang_id", $result['id_language']); // carico la lingua del cliente
              $langarr = explode(" ",$language['title_native']);
              $lang = strtolower($langarr[0]);
              if (file_exists("lang.".$lang.".php")){// se esiste
                include "lang.".$lang.".php";// carico il file traduzione lingua
              }else{// altrimenti carico di default la lingua inglese
                include "lang.english.php";
              }
              $script_transl=$strScript['report_booking.php'];
              $tesbro = gaz_dbi_get_row($gTables['tesbro'], "id_tes", intval($_GET['idtes']));
              // imposto PHP Mailer per invio email di cambio stato
              $host = gaz_dbi_get_row($gTables['company_config'], 'var', 'smtp_server')['val'];
              $usr = gaz_dbi_get_row($gTables['company_config'], 'var', 'smtp_user')['val'];
              //$psw = gaz_dbi_get_row($gTables['company_config'], 'var', 'smtp_password')['val'];
              $rsdec=gaz_dbi_query("SELECT AES_DECRYPT(FROM_BASE64(val),'".$_SESSION['aes_key']."') FROM ".$gTables['company_config']." WHERE var = 'smtp_password'");
              $rdec=gaz_dbi_fetch_row($rsdec);
              $psw=$rdec?$rdec[0]:'';
              $port = gaz_dbi_get_row($gTables['company_config'], 'var', 'smtp_port')['val'];
              $mail = new PHPMailer(true);
              $mail->CharSet = 'UTF-8';
              //Server settings
              $mail->SMTPDebug  = 0;                           //Enable verbose debug output default: SMTP::DEBUG_SERVER;
              $mail->isSMTP();                                 //Send using SMTP
              $mail->Host       = $host;                       //Set the SMTP server to send through
              $mail->SMTPAuth   = true;                        //Enable SMTP authentication
              $mail->Username   = $usr;                        //SMTP username
              $mail->Password   = $psw;                        //SMTP password
              $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; //Enable implicit TLS encryption
              $mail->Port       = $port;                       //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
              // creo e invio email di conferma
              //Recipients
              $mail->setFrom($admin_aziend['e_mail']); // sender (e-mail dell'account che sta inviando)
              $mail->addReplyTo($admin_aziend['e_mail']); // reply to sender (e-mail dell'account che sta inviando)
              if (filter_var($result['e_mail'], FILTER_VALIDATE_EMAIL)){
                $mail->addAddress($result['e_mail']);                  // se c'è invio all'email destinatario principale
              } else{
                $mail->addAddress($result['e_mail2']);                  // altrimenti alla secondaria
              }
              $mail->addCC($admin_aziend['e_mail']);             //invio copia a mittente
              $mail->isHTML(true);
              $mail->Subject = $script_transl['booking']." ".$tesbro['numdoc'].' '.$script_transl['of'].' '.gaz_format_date($tesbro['datemi']);
              $mail->Body    = "<p>".$script_transl['email_give_point']." ".$_GET['points']." ".$script_transl['email_give_point2']." ".$_GET['motive']."</p><p>".$script_transl['email_give_point3']." ".$data['vacation_rental']['points']." ".$script_transl['points']."</p><p><b>".$admin_aziend['ragso1']." ".$admin_aziend['ragso2']."</b></p>";
              if($mail->send()) {
                echo ". E-mail inviata";
              }else {
                echo "Errore imprevisto nello spedire la mail di attribuzione punti: " . $mail->ErrorInfo;
              }
            }else{
              echo ". Impossibile inviare e-mail: indirizzo mancante o non corretto";
            }
          }
        }else{
          echo "No data passed!"," points:",$_GET['points']," - motive:",$_GET['motive'];
        }
      break;
      case 'orders':
        $codice= substr($_GET['term'],0,15);
        $orders= $libFunc->getorders($codice);
        echo json_encode($orders);
      break;
      case 'lastbuys':
        $codice= substr($_GET['term'],0,15);
        $lastbuys= $libFunc->getLastBuys($codice,false);
        echo json_encode($lastbuys);
      break;
      case 'group':
        $codice= intval($_GET['term']);
        $query = "SELECT descri, id_artico_group FROM " . $gTables['artico_group'] . " WHERE id_artico_group ='". $codice ."' LIMIT 1";
        $result = gaz_dbi_query($query);
        $n=0;
        while ($res = $result->fetch_assoc()){
          $return[$n]=$res;
          $n++;
        }
        $query = "SELECT codice, descri FROM " . $gTables['artico'] . " WHERE id_artico_group ='". $codice ."'";
        $result = gaz_dbi_query($query);
        while ($res = $result->fetch_assoc()){
          $return[$n]=$res;
          $n++;
        }
        echo json_encode($return);
      break;
      case'load_votes':
        $return=array();
        $codice= intval($_GET['term']);
        $query = "SELECT score, element FROM ". $gTables['rental_feedback_scores'] ." LEFT JOIN ". $gTables['rental_feedback_elements'] ." ON ". $gTables['rental_feedback_elements'] .".id =  ". $gTables['rental_feedback_scores'] .".element_id WHERE feedback_id ='". $codice ."' ORDER BY ". $gTables['rental_feedback_scores'] .".id ASC";
        $result = gaz_dbi_query($query);
        $n=0;
        while ($res = $result->fetch_assoc()){
          $return[$n]=$res;
          $n++;
        }
        echo json_encode($return);
      break;
      case'change_feed_status':
        $codice= intval($_GET['term']);
        $query = "UPDATE ".$gTables['rental_feedbacks']." SET status = ". intval($_GET['status']) ." WHERE id = ".$codice;
        gaz_dbi_query($query);
      break;
      default:
      return false;
    }
}
?>
