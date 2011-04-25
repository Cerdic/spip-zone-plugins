<?php
// ---------------------------------------------------------
//  Mes abonnés
// ---------------------------------------------------------

function exec_mesabonnes(){ 
  global $connect_statut;
	global $connect_toutes_rubriques;

  // main ------------------------------------------------------  
	$commencer_page = charger_fonction('commencer_page', 'inc');
  echo $commencer_page(_T('mesabonnes:mes_abonnes'),_T('mesabonnes:mes_abonnes'),_T('mesabonnes:mes_abonnes'));	  
 
	if ($connect_statut == "0minirezo" && $connect_toutes_rubriques) {	  // admin restreint (connect_toutes_rubriques si admin)
		echo gros_titre(_T('mesabonnes:mes_abonnes'),'', false);
    
    echo debut_gauche('',  true);
    echo debut_droite('', true);
    echo "<p>"._T('mesabonnes:export_abonnes')."</p>";
    
    // inspi ecrire/inc/stastiques.php		
		include_spip('inc/acces');		
		$args = array();
		$args['id_article']='-mesabonnes-'.date('Y-m-d');
		$fond = "mesabonnes";
	  $args = param_low_sec($fond, $args, '', 'transmettre');
	  $url = generer_url_public('transmettre', $args);
	  echo "<ul><li><a href='$url'>"._T('mesabonnes:export_abonnes_csv')."</a></li>";
	  
	  $args = array();
	  $args['id_article']='-mesabonnes-bulk-'.date('Y-m-d');
	  $fond = "mesabonnes_maxbulk";
	  $args = param_low_sec($fond, $args, '', 'transmettre');
	  $url = generer_url_public('transmettre', $args);
	  echo "<li><a href='$url'>"._T('mesabonnes:export_abonnes_csv_bulk')."</a></li></ul>";
		
	}	else { 
		echo "<strong>Vous n'avez pas acc&egrave;s &agrave; cette page.</strong>"; 
	}
	
	echo fin_page();
}

?>