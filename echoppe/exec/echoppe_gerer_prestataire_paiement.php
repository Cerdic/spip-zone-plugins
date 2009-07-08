<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/presentation');
include_spip('public/assembler');

function exec_echoppe_gerer_prestataire_paiement(){

	if ($GLOBALS['meta']['version_installee'] <= '1.927'){
		echo debut_page($contexte['titre'], "redacteurs", "echoppe");	
	}else{
		echo inc_commencer_page_dist($contexte['titre'], "redacteurs", "echoppe");
	}
	
	
	echo debut_gauche('',true);
	
	//echo recuperer_fond('fonds/echoppe_prestataires_paiement',$contexte);
	
	echo debut_boite_info(true);
	echo recuperer_fond('fonds/echoppe_info_prestataire_paiement', $contexte);
	echo fin_boite_info(true);
	
	include_spip('inc/echoppe_raccourcis');
	$raccourcis = generer_raccourcis_echoppe();
	echo bloc_des_raccourcis($raccourcis);
	
	echo creer_colonne_droite(true);
	
	echo debut_droite(true,_T('echoppe:gerer_les_prestataire_paiement'));

	echo recuperer_fond('prive/prestataires/gerer_prestataires', $contexte);
	echo fin_gauche(true);
	echo fin_page(true);
	
}

?>
