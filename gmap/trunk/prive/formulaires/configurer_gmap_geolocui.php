<?php
/*
 * Géolocalisation et cartographie
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2012 - licence GNU/GPL
 *
 * Page de paramétrage principale du plugin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');

function formulaire_configurer_gmap_geolocui_initialiser_dist() {

	// Cas spécial à traiter pour les cas où un paramétrage GMap 0.* existe
	// mais pas encore la nouvelle description des objets géolocalisables.
	if (!gmap_config_existe('gmap_objets_geo', 'geopoints_objets')) {
	
		$geopoints_objets = array();
		if (gmap_lire_config('gmap_objets_geo', 'type_rubriques', "oui") === 'oui')
			$geopoints_objets[] = 'rubrique';
		if (gmap_lire_config('gmap_objets_geo', 'type_articles', "oui") === 'oui')
			$geopoints_objets[] = 'article';
		if (gmap_lire_config('gmap_objets_geo', 'type_documents', "oui") === 'oui')
			$geopoints_objets[] = 'document';
		if (gmap_lire_config('gmap_objets_geo', 'type_breves', "oui") === 'oui')
			$geopoints_objets[] = 'breve';
		if (gmap_lire_config('gmap_objets_geo', 'type_mots', "oui") === 'non')
			$geopoints_objets[] = 'mot';
		if (gmap_lire_config('gmap_objets_geo', 'type_auteurs', "oui") === 'oui')
			$geopoints_objets[] = 'auteur';
		gmap_ecrire_config('gmap_objets_geo', 'geopoints_objets', implode(',', $geopoints_objets));
	}

	// Tout le site et liste des rubriques vide
	gmap_init_config('gmap_objets_geo', 'tout_le_site', "oui");
	gmap_init_config('gmap_objets_geo', 'liste', "");
	
	gmap_init_config('gmap_edit_params', 'hack_modalbox', "oui");
	
}

function formulaires_configurer_gmap_geolocui_charger_dist(){

	$valeurs = array();
	
	$valeurs['geopoints_objets'] = gmap_lire_config('gmap_objets_geo', 'geopoints_objets', "rubrique,article,document,breve,auteur");
	$valeurs['geopoints_objets'] = explode(',', $valeurs['geopoints_objets']);

	$valeurs['tout_le_site'] = gmap_lire_config('gmap_objets_geo', 'tout_le_site', "oui");
	$simple_rubs = gmap_lire_config('gmap_objets_geo', 'liste', "");
	$valeurs['les_rubriques'] = array();
	if ($simple_rubs) {
		foreach ($simple_rubs as $rub)
			$valeurs['les_rubriques'][] = 'rubrique|'.$rub;
	}
	
	$hack_modalbox = gmap_lire_config('gmap_edit_params', 'hack_modalbox', "oui");
	
	return $valeurs;
}

function formulaires_configurer_gmap_geolocui_verifier_dist(){

	$erreurs = array();

	return $erreurs;
}

function formulaires_configurer_gmap_geolocui_traiter_dist(){

	// Objets géolocalisables
	gmap_ecrire_config('gmap_objets_geo', 'geopoints_objets', implode(',', _request('geopoints_objets')));
	
	// Tout le site
	gmap_ecrire_config('gmap_objets_geo', 'tout_le_site', ((_request('tout_le_site') === "oui") ? "oui" : "non"));
	
	// Liste des rubriques
	$simple_rubs = array();
	if ($rubgeo = _request('les_rubriques'))
	{
		foreach ($rubgeo as $rub)
		{
			$parts = explode('|', $rub);
			if ($parts[1])
				$simple_rubs[] = $parts[1];
		}
		$strrubs = implode(',', $simple_rubs);
	}
	gmap_ecrire_config('gmap_objets_geo', 'liste', $simple_rubs);
	
	// Contournement ModalBox.
	gmap_ecrire_config('gmap_objets_geo', 'hack_modalbox', ((_request('hack_modalbox') === "oui") ? "oui" : "non"));

	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
}

?>
