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

function formulaires_aspirateur_charger_dist(){
	
	$valeurs = array(
	'nombre_de_pages' => _request('nombre_de_pages'),
	);

	return $valeurs;
}

function formulaires_aspirateur_verifier_dist(){

	$erreurs = array();

	return $erreurs;
}


function formulaires_aspirateur_traiter_dist(){
	
	$nombre_de_pages = _request('nombre_de_pages');
	$nom_site_aspirer = lire_config('aspirateur/nom_site_aspirer');
	$url_site_aspirer = lire_config('aspirateur/url_site_aspirer');
	$descriptif_site = lire_config('aspirateur/descriptif_site');
	$page_referente = lire_config('aspirateur/page_referente');
	$suivre_liens = lire_config('aspirateur/suivre_liens');
	$contenu_inclure_tag_attribut = lire_config('aspirateur/contenu_inclure_tag_attribut');
	$motif_contenu_regex = lire_config('aspirateur/motif_contenu_regex');
	$motif_chemin_documents_exclure = lire_config('aspirateur/motif_chemin_documents_exclure');
	$motif_chemin_documents = lire_config('aspirateur/motif_chemin_documents');
	$activer_spip = lire_config('aspirateur/activer_spip');

	//actions
	$aspirateur_tmp_liste=aspirateur_tmp_liste($url_site_aspirer);
	$message = _T('aspirateur:explication_tmp_liste', array('url_site'=>$url_site_aspirer,'url_tmp_liste'=>"<a href='".$aspirateur_tmp_liste."'>".$aspirateur_tmp_liste."</a>"));
	
	//le contenu
	$titre= recupere_titre($page_referente);
	$texte= recupere_contenu($page_referente,$url_site_aspirer);
	
	//dans le texte transforme les liens des documents en chemin SPIP (option *)
	$traite_texte_documents=traite_texte_documents($texte);
	$texte=$traite_texte_documents['texte'];
	$nb = 1;
	$message .= "<br><strong>"._T('aspirateur:info_result_aspiration_contenu')."</strong> ";
	$message .= "<br>";
	$message .= "<strong>"._T('aspirateur:info_result_titre')."</strong>$titre<br>";
	$message .= "<strong>"._T('aspirateur:info_result_contenu')."</strong>$texte<br>";
	
	//les liens href
	$recupere_links = array();
	$recupere_links=recupere_links($page_referente,'loadHTMLFile','a','href');
	//dont sont soustrait les documents
	$documents=$traite_texte_documents['documents'];
	$recupere_links = array_merge(array_diff($recupere_links, $documents));
	
	$nb = count($recupere_links);
	$message .= "<br><strong>"._T('aspirateur:info_result_aspiration_liens')."</strong> ";
	$message .= sinon(singulier_ou_pluriel($nb,'aspirateur:info_1_lien','aspirateur:info_nb_liens'),_T('aspirateur:info_aucun_lien'));
	$message .= "</br>";
	$message .= join($recupere_links,'<br />');
	
	//affiche les documents
	$nb = count($documents);
	$message .= "<br><strong>"._T('aspirateur:info_result_aspiration_documents')."</strong> ";
	$message .= sinon(singulier_ou_pluriel($nb,'aspirateur:info_1_document','aspirateur:info_nb_documents'),_T('aspirateur:info_aucune_document'));
	$message .= "</br>";
	$message .= join($documents,'<br />');

	return array('message_ok'=>$message);
}

?>