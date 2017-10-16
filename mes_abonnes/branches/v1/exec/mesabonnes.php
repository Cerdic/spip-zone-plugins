<?php
// ---------------------------------------------------------
//  Mes abonnés
// ---------------------------------------------------------

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_mesabonnes(){ 
  global $connect_statut;
	global $connect_toutes_rubriques;

	include_spip('inc/presentation');
  // main ------------------------------------------------------  
	$commencer_page = charger_fonction('commencer_page', 'inc');
  echo $commencer_page(_T('mesabonnes:mes_abonnes'),_T('mesabonnes:mes_abonnes'),_T('mesabonnes:mes_abonnes'));	  
 
	echo debut_gauche('',  true);
  echo debut_droite('', true);
	if ($connect_statut == "0minirezo" && $connect_toutes_rubriques) {	  // admin restreint (connect_toutes_rubriques si admin)

		echo gros_titre(_T('mesabonnes:mes_abonnes'),'', false);
    echo "<h3>"._T('mesabonnes:export_abonnes')."</h3>";
    
    if ($res = sql_select('id_abonne', 'spip_mesabonnes','statut="publie"')) {         
        if ($res and sql_count($res)>0) {
	          // inspi ecrire/inc/stastiques.php	
            include_spip('inc/invalideur');
          	include_spip('inc/acces');		
        		$args = array();
        		$args['id_article']='-mesabonnes-'.date('Y-m-d'); 
            suivre_invalideur("id='".$args['id_article']."'");	   // on purge le cache pour avoir l'export a jour	
        		$fond = "mesabonnes";
        	  $args = param_low_sec($fond, $args, '', 'transmettre');
        	  $url = generer_url_public('transmettre', $args);
        	  echo "<ul><li><a href='$url'>"._T('mesabonnes:export_abonnes_csv')."</a></li>";
        	  
        	  $args = array();
        	  $args['id_article']='-mesabonnes-bulk-'.date('Y-m-d');
        	  suivre_invalideur("id='".$args['id_article']."'");	   // on purge le cache pour avoir l'export a jour
        	  $fond = "mesabonnes_maxbulk";
        	  $args = param_low_sec($fond, $args, '', 'transmettre');
        	  $url = generer_url_public('transmettre', $args);
        	  echo "<li><a href='$url'>"._T('mesabonnes:export_abonnes_csv_bulk')."</a></li></ul>"; 
            
            echo "<p>"._T('mesabonnes:export_abonnes_compte', array('compte' => sql_count($res)))."</p>";              
        }  else {
            echo "<p><i>"._T('mesabonnes:export_abonnes_rien')."</i></p>";
        }
    }

    

		
	}	else { 
		echo "<strong>Vous n'avez pas acc&egrave;s &agrave; cette page.</strong>"; 
	}
	
	echo fin_gauche(),fin_page();
}

?>