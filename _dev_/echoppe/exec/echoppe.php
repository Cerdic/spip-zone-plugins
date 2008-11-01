<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/abstract_sql');
include_spip('public/assembler');
function exec_echoppe(){
	
	if ($GLOBALS['meta']['version_installee'] <= '1.927'){
		echo debut_page(_T('echoppe:echoppe'), "redacteurs", "echoppe");	
	}else{
		echo inc_commencer_page_dist(_T('echoppe:echoppe'), "redacteurs", "echoppe");
	}
	
	echo debut_gauche(_T('echoppe:echoppe'),true);
	
	
	echo debut_boite_info(true);
		echo recuperer_fond('fonds/descriptif_echoppe');
	echo fin_boite_info(true);
	
	
	include_spip('inc/echoppe_raccourcis');
	$raccourcis = generer_raccourcis_echoppe();
	echo bloc_des_raccourcis($raccourcis);
	
	echo creer_colonne_droite(true);
	echo debut_droite(_T('echoppe:echoppe'), true);
	
	echo recuperer_fond('fonds/echoppe_echoppe');
	
	echo fin_gauche(true);
	echo fin_page(true);
}

?>
