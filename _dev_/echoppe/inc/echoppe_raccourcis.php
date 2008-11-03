<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function generer_raccourcis_echoppe(){
	
	$raccourcis .= icone_horizontale(_T('echoppe:gerer_echoppe'), generer_url_ecrire("echoppe"), _DIR_PLUGIN_ECHOPPE."images/echoppe_blk_24.png","", false);
	$raccourcis .= "<br />";
	$raccourcis .= icone_horizontale(_T('echoppe:creer_nouvelle_categorie'), generer_url_ecrire("echoppe_edit_categorie","new=oui&id_parent=0"), _DIR_PLUGIN_ECHOPPE."images/categorie-24.png","creer.gif", false);
	$raccourcis .= "<br />";
	$raccourcis .= icone_horizontale(_T('echoppe:gerer_les_paniers'), generer_url_ecrire("echoppe_paniers"), _DIR_PLUGIN_ECHOPPE."images/panier.png","", false);
	$raccourcis .= "<br />";
	$raccourcis .= icone_horizontale(_T('echoppe:gerer_les_depots'), generer_url_ecrire("echoppe_gerer_depots"), _DIR_PLUGIN_ECHOPPE."images/go-home.png","", false);
	$raccourcis .= "<br />";
	$raccourcis .= icone_horizontale(_T('echoppe:gerer_les_prestataire_paiement'), generer_url_ecrire("echoppe_gerer_prestataire_paiement"), _DIR_PLUGIN_ECHOPPE."images/credit_cards.png","", false);
	$raccourcis .= "<hr />";
	$raccourcis .= icone_horizontale(_T('echoppe:configurer_echoppe'), generer_url_ecrire("cfg","cfg=echoppe",false), find_in_path('cfg-22.png'),"", false);
	
	return $raccourcis;
}

?>
