<?php
// ---------------------------------------------------------
//  Mes abonnés
// ---------------------------------------------------------


include_spip('inc/presentation');
include_spip('inc/distant');
include_spip('inc/affichage');
include_spip('inc/meta');
include_spip('inc/filtres');
include_spip('inc/lang');


function exec_mesabonnes(){ 
  global $connect_statut;
	global $connect_toutes_rubriques;
 
  include_spip("inc/charsets"); 
  include_spip("inc_presentation");
  

  // main ------------------------------------------------------  
	$commencer_page = charger_fonction('commencer_page', 'inc');
  echo $commencer_page(_T('malettre:ma_lettre'),_T('malettre:ma_lettre'),_T('malettre:ma_lettre'));	  
 
	if ($connect_statut == "0minirezo" && $connect_toutes_rubriques) {	  // admin restreint (connect_toutes_rubriques si admin)
		echo gros_titre(_T('mesabonnes:mes_abonnes'),'', false);
    
    echo debut_gauche('',  true);
    echo debut_droite('', true);
    echo "<p>"._T('mesabonnes:export_abonnes')."</p>";
    
    // inspi ecrire/inc/stastiques.php
		
		include_spip('inc/acces');
		$args = array();
		$fond = "mesabonnes";
	  $args = param_low_sec($fond, $args, '', 'transmettre');
	  $url = generer_url_public('transmettre', $args);
	  echo "<ul><li><a href='$url'>"._T('mesabonnes:export_abonnes_csv')."</a></li>";
	  
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