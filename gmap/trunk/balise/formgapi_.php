<?php
/*
 * Plugin GMap
 * Géolocalisation des objets SPIP et insertion de cartes
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009-2012 - licence GNU/GPL
 *
 * Balise FORMGAPI_ :
 *  Insertion d'un formulaire spécifique à une API.
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/gmap_config_utils');


function _gmap_existe_formulaire_api($form, $api) {

	if (substr($form,0,11)=="FORMULAIRE_")
		$form = strtolower(substr($form,11));
	else 
		$form = strtolower($form);

	if (!$form) return ''; // on ne sait pas, le nom du formulaire n'est pas fourni ici

	return trouver_fond($form, 'mapimpl/'.$api.'/formulaires/') ? $form : false;
}

function balise_FORMGAPI_($p) {

	$api = gmap_lire_config('gmap_api', 'api', "gma3");
	
	// Cas d'un #FORMULAIRE_TOTO inexistant : renvoyer la chaine vide.
	// mais si #FORMULAIRE_{toto} on ne peut pas savoir a la compilation, continuer
	if (_gmap_existe_formulaire_api($p->nom_champ, $api)===FALSE) {
		    $p->code = "''";
		    $p->interdire_scripts = false;
		    return $p;
	}

	// sinon renvoyer un code php dnamique
	return calculer_balise_dynamique($p, $p->nom_champ, array());
}

function balise_FORMGAPI__dyn() {

	$api = gmap_lire_config('gmap_api', 'api', "gma3");
	$form = _gmap_existe_formulaire_api($form, $api);
	if (!$form) return '';

	$args = func_get_args();
	array_shift($args); // le premier c'est le nom
	
	$contexte = balise_FORMULAIRE__contexte($form, $args);
	
	if (!is_array($contexte))
		return $contexte;
	return array('mapimpl/'.$api.'/formulaires/'.$form, 3600, $contexte);
}

?>