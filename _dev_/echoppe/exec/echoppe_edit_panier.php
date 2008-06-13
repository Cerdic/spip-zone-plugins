<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/presentation');
include_spip('public/assembler');

function exec_echoppe_edit_panier(){

	$contexte = array();
	$contexte['token_panier'] = _request('token_panier');
	
	if ($GLOBALS['meta']['version_installee'] <= '1.927'){
		echo debut_page(_T('echoppe:le_panier'), "redacteurs", "echoppe");	
	}else{
		echo inc_commencer_page_dist(_T('echoppe:le_panier'), "redacteurs", "echoppe");
	}
	
	echo debut_gauche();
	
	//echo recuperer_fond('fonds/echoppe_depot',$contexte);
	echo debut_boite_info();
	echo recuperer_fond('fonds/echoppe_info_panier', $contexte);
	echo fin_boite_info();
	
	include_spip('inc/echoppe_raccourcis');
	$raccourcis = generer_raccourcis_echoppe();
	echo bloc_des_raccourcis($raccourcis);
	
	echo creer_colonne_droite();
	
	echo debut_droite(_T('echoppe:visualisation_d_un_panier'));
	//echo gros_titre($contexte['titre']);
	
	echo recuperer_fond('fonds/echoppe_edit_panier', $contexte);
	echo fin_gauche();
	echo fin_page();

}

?>
