<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/presentation');
include_spip('public/assembler');
function exec_echoppe_paniers(){
	if ($GLOBALS['connect_statut'] != "0minirezo"){
		die(echoppe_echec_autorisation().fin_page());
	}
	


	echo debut_page($contexte['titre'], "redacteurs", "paniers");	

	
	echo debut_gauche();
	echo debut_boite_info();
	echo recuperer_fond('fonds/echoppe_info_paniers',$contexte);
	echo fin_boite_info();
	

	include_spip('inc/echoppe_raccourcis');
	$raccourcis = generer_raccourcis_echoppe();
	echo bloc_des_raccourcis($raccourcis);
	
	echo creer_colonne_droite();
	
	echo debut_droite(_T('echoppe:les_paniers'));
	
	echo recuperer_fond('fonds/echoppe_paniers', $contexte);
	
	echo fin_gauche();
	echo fin_page();	
}

?>
