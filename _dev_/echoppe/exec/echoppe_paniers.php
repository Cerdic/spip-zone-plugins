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
	
	echo inc_commencer_page_dist($contexte['titre'], "redacteurs", "paniers");	

	
	echo debut_gauche('',true);
	echo debut_boite_info(true);
	echo recuperer_fond('fonds/echoppe_info_paniers',$contexte);
	echo fin_boite_info(true);
	

	include_spip('inc/echoppe_raccourcis');
	$raccourcis = generer_raccourcis_echoppe();
	echo bloc_des_raccourcis($raccourcis);
	
	echo creer_colonne_droite(true);
	
	echo debut_droite(_T('echoppe:les_paniers'),true);
	
	echo recuperer_fond('fonds/echoppe_paniers', $contexte);
	
	echo fin_gauche(true);
	echo fin_page(true);	
}

?>
