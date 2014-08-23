<?php
/**
 * Plugin Aspirateur pour Spip 3.0
 * Licence GPL 3
 *
 * (c) 2014 Anne-lise Martenot
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/config');
include_spip('inc/filtres');
include_spip('inc/aspirer_memo');
include_spip('inc/aspirer_dom');
include_spip('inc/aspirer_curl');
include_spip('inc/aspirer_nettoyer');
include_spip('inc/aspirer_spip');
include_spip('inc/aspirer_rss');

function formulaires_aspirateur_rss_charger_dist(){
	
	$valeurs = array(
	'nombre_de_pages' => _request('nombre_de_pages'),
	);

	return $valeurs;
}

function formulaires_aspirateur_rss_verifier_dist(){

	$erreurs = array();

	return $erreurs;
}


function formulaires_aspirateur_rss_traiter_dist(){
	
	$nombre_de_pages = _request('nombre_de_pages');
	$nom_site_aspirer = lire_config('aspirateur/nom_site_aspirer');
	$url_site_aspirer = lire_config('aspirateur/url_site_aspirer');
	$descriptif_site = lire_config('aspirateur/descriptif_site');
	$page_referente = lire_config('aspirateur/page_referente');
	$suivre_liens = lire_config('aspirateur/suivre_liens');
	$div_id_contenu = lire_config('aspirateur/div_id_contenu');
	$motif_contenu_regex = lire_config('aspirateur/motif_contenu_regex');
	$motif_chemin_documents_exclure = lire_config('aspirateur/motif_chemin_documents_exclure');
	$motif_chemin_documents = lire_config('aspirateur/motif_chemin_documents');
	$activer_rss = lire_config('aspirateur/activer_rss');
	$activer_spip = lire_config('aspirateur/activer_spip');

	//actions
	
	//le rss
	$aspirateur_tmp_liste=aspirateur_tmp_liste($url_site_aspirer);
	if($activer_rss==1 && $rss = do_rss("aspirateur_rss.xml",$nombre_de_pages)) {
		$message = _T('aspirateur:explication_tmp_liste', array('url_site'=>$url_site_aspirer,'url_tmp_liste'=>"<a href='".$aspirateur_tmp_liste."'>".$aspirateur_tmp_liste."</a>"));
		$message .= "<br /><strong>"._T('aspirateur:info_result_aspiration_rss')."</strong> ";
		$message .= " (".sinon(singulier_ou_pluriel($nombre_de_pages,'aspirateur:info_1_contenu','aspirateur:info_nb_contenus'),_T('aspirateur:info_aucun_contenu')).") ";
		$message .= " <a href='".$rss."'>Voir le fichier $rss</a>";
	}

	return array('message_ok'=>$message);
}