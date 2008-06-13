<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/echoppe');
include_spip('inc/commencer_page');
include_spip('inc/presentation');
include_spip('public/assembler');

function exec_echoppe_prestataire_paiement(){

	if ($GLOBALS['meta']['version_installee'] <= '1.927'){
		echo debut_page($contexte['titre'], "redacteurs", "echoppe");	
	}else{
		echo inc_commencer_page_dist($contexte['titre'], "redacteurs", "echoppe");
	}
	
	
	echo debut_gauche();
	
	//echo recuperer_fond('fonds/echoppe_prestataires_paiement',$contexte);
	echo debut_boite_info();
	echo recuperer_fond('fonds/echoppe_info_prestataires_paiement', $contexte);
	echo fin_boite_info();
	
	$raccourcis .= icone_horizontale(_T('echoppe:gerer_les_prestataires_paiement'), generer_url_ecrire("echoppe_gerer_prestataires_paiements",""), _DIR_PLUGIN_ECHOPPE."images/go-home.png","", false);
	$raccourcis .= "<hr />";
	$raccourcis .= icone_horizontale(_T('echoppe:gerer_les_paniers'), generer_url_ecrire("echoppe_paniers"), _DIR_PLUGIN_ECHOPPE."images/panier.png","", false);
	$raccourcis .= '<hr />';
	$raccourcis .= icone_horizontale(_T('echoppe:gerer_echoppe'), generer_url_ecrire("echoppe",""), _DIR_PLUGIN_ECHOPPE."images/echoppe_blk_24.png","", false);
	echo bloc_des_raccourcis($raccourcis);
	
	echo creer_colonne_droite();
	
	echo debut_droite(_T('echoppe:gerer_les_prestataire_paiement'));
	//echo gros_titre($contexte['titre']);
	
	echo recuperer_fond('fonds/echoppe_gerer_prestataire_paiement', $contexte);
	echo fin_gauche();
	echo fin_page();
	
}

?>
