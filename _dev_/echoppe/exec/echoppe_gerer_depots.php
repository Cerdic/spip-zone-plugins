<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/presentation');
include_spip('public/assembler');

function exec_echoppe_gerer_depots(){

	echo inc_commencer_page_dist($contexte['titre'], "redacteurs", "echoppe");
	
	
	echo debut_gauche('',true);
	
	echo debut_boite_info(true);
	echo recuperer_fond('fonds/echoppe_info_depot', $contexte);
	echo fin_boite_info(true);
	
	
	include_spip('inc/echoppe_raccourcis');
	$raccourcis = generer_raccourcis_echoppe();
	echo bloc_des_raccourcis($raccourcis);
	
	echo creer_colonne_droite(true);
	
	echo debut_droite(_T('echoppe:visualisation_d_un_depot'),true);
	
	echo recuperer_fond('fonds/echoppe_gerer_depots', $contexte);
	echo fin_gauche(true);
	echo fin_page(true);
	
}

?>
