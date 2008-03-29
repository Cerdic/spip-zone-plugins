<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_echoppe_paniers(){
	
	$contexte = array();
	echo debut_gauche();
	

	echo debut_boite_info();
	echo recuperer_fond('fonds/echoppe_info_paniers', $contexte);
	echo fin_boite_info();
	
	$raccourcis .= icone_horizontale(_T('echoppe:gerer_les_depots'), generer_url_ecrire("echoppe_gerer_depots",""), _DIR_PLUGIN_ECHOPPE."images/go-home.png","", false);
	$raccourcis .= "<hr />";
	$raccourcis .= icone_horizontale(_T('echoppe:gerer_echoppe'), generer_url_ecrire("echoppe",""), _DIR_PLUGIN_ECHOPPE."images/echoppe_blk_24.png","", false);
	echo bloc_des_raccourcis($raccourcis);
	
	echo creer_colonne_droite();
	
	echo debut_droite(_T('echoppe:visualisation_des_paniers'));
	//echo gros_titre($contexte['titre']);
	
	echo recuperer_fond('fonds/echoppe_paniers', $contexte);
	echo fin_gauche();
	echo fin_page();
	
}

?>
