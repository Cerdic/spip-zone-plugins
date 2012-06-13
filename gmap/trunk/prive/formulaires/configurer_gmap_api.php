<?php
/*
 * G�olocalisation et cartographie
 * Insertion de carte Google Maps sur les �l�ments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2012 - licence GNU/GPL
 *
 * Page de param�trage principale du plugin
 *
 */

include_spip('inc/gmap_config_utils');

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaire_configurer_gmap_api_initialiser_dist() {

	// R�cup�rer la liste des APIs
	$apis = gmap_apis_connues();
	
	// Les parcourir
	foreach ($apis as $api => $infos)
	{
		// Charger ce qui est sp�cifique � l'impl�mentation
		$init_api = charger_fonction("initialiser", "mapimpl/".$api."/prive/api", true);
		if ($init_api)
			$init_api();
	}
}

function formulaires_configurer_gmap_api_charger_dist(){

	$valeurs = array();
	
	// Lire l'API utilis�e
	$valeurs['api'] = gmap_lire_api();
	
	// Charger ce qui est sp�cifique � l'impl�mentation
	$show_api = charger_fonction("recuperer", "mapimpl/".$valeurs['api']."/prive/api", true);
	if ($show_api)
		$valeurs['_contenu_api'] = $show_api();
	// ATTENTION : le '_' en premi�re place est NECESSAIRE pour que SPIP n'encode pas le HTML !
		
	if (!isset($valeurs['_contenu_api']) || !strlen($valeurs['_contenu_api']))
		return ''; // pas de formulaire...
	else	
		return $valeurs;
}

function formulaires_configurer_gmap_api_verifier_dist(){

	$erreurs = array();

	$verif_api = charger_fonction("verifier", "mapimpl/".$valeurs['api']."/prive/api", true);
	if ($verif_api)
		$erreur = array_merge($erreur, $verif_api());
	
	return $erreurs;
}

function formulaires_configurer_gmap_api_traiter_dist(){

	// Lire l'API utilis�e
	$api = gmap_lire_api();
	
	// Charger ce qui est sp�cifique � l'impl�mentation
	$faire_api = charger_fonction("traiter", "mapimpl/".$api."/prive/api");
	if ($faire_api)
		$faire_api();
		
	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
}

?>
