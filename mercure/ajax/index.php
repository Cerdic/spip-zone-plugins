<?php
/*
 MERCURE 
 TCHAT POUR LES REDACTEURS DANS L'ESPACE PRIVE DE SPIP
 v. 0.10 - 07/2009 - SPIP 1.9.2
 Patrick Kuchard - www.encyclopedie-incomplete.com
*/

session_start();

// Against bad configurations
if (get_magic_quotes_gpc()){
    foreach ($_POST as $k=>$v){
        $_POST[$k] = stripslashes($v);
    }
}
// On récupère ce qu'il y a à récupérer
foreach ($_POST as $k=>$v){
 $_SESSION[$k] = $v;
}
foreach ($_GET as $k=>$v){
 $_SESSION[$k] = $v;
}

include_once '../inc/func_bdd.php';

$arrow = new PK_BDD($_SESSION['type']);

switch($_SESSION['action']){
  case 'send_message' :
            $arrow->bdd_ecrire_message( $_SESSION['time'], $_SESSION['nom'], utf8_decode($_SESSION['message']) );
            switch(rand(0,3)){
              case 1 :
                      $arrow->bdd_effacer_vieux_messages($_SESSION['time_limit']);
                      break;
              case 2 :
                      $arrow->bdd_limiter_messages($_SESSION['item_limit']);
                      break;
            }
            echo 'OK';
            break;
  case 'get_board' :
            $messages = $arrow->bdd_lire_messages( $_SESSION['nb_a_lire'] );
            if( $messages != $_SESSION['old_panel']){
              $_SESSION['old_panel'] = $messages;
              if($_SESSION['mercure_notify_sound'] == 'on'){                
                $messages .= '<object type="audio/x-wav" width="0" height="0">
                                <param name="autoplay" value="true">
                                <param name="autostart" value="1">
                                <param name="loop" VALUE="false"> 
                                <param name="volume" value="'.$_SESSION['sound_volume'].'">
                                <param name="src" value="'.$_SESSION['sound2play'].'">
                                <param name="filename" value="'.$_SESSION['sound2play'].'">                                 
                              </object>';
              }
              // $_SESSION['debug'] = $messages;
              $_SESSION['debug'] = '';
            }
            echo $messages;
            break;
  case 'get_all' :
            echo $arrow->bdd_lire_messages(0);
            break;
  case 'time_limit' :
            $arrow->bdd_effacer_vieux_messages($_SESSION['time_limit']);
            echo 'OK';
            break;
  case 'item_limit' :
            $arrow->bdd_limiter_messages($_SESSION['item_limit']);
            echo 'OK';
            break;
  case 'optimiser' :
            echo $arrow->bdd_optimiser();
            echo 'OK';
            break;
  case 'sound_on' :
            $_SESSION['mercure_notify_sound'] = 'on';
            echo 'OK';
            break;
  case 'sound_off' :
            $_SESSION['mercure_notify_sound'] = 'off';
            echo 'OK';
            break;
}

// $stats = unserialize( stripslashes( htmlspecialchars_decode( $_GET['values'] ) ) );
?>
