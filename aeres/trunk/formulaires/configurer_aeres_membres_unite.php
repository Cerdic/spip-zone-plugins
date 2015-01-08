<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_aeres_membres_unite_charger_dist(){
	if (isset($GLOBALS['meta']['aeres']))
		$meta = unserialize($GLOBALS['meta']['aeres']);
	else
		$meta = array();
	
	$valeurs = array();
	// Liste des membres
	if (isset($meta['membres']))
		$valeurs['membres'] = explode(';',$meta['membres']);
	else
		$valeurs['membres'] = array();
	
	return $valeurs;
}

function formulaires_configurer_aeres_membres_unite_verifier_dist(){
	$erreurs = array();
	if (!autoriser('webmestre')) $erreurs['message_erreur'] = 'Vous n\'avez pas les droits suffisants pour modifier la configuration.';
	return $erreurs;
}



function formulaires_configurer_aeres_membres_unite_traiter_dist(){
	$membres = _request('membres');
	if (count($membres)) aeres_tri_alpha($membres);
	else $membres = array();
	
	if (isset($GLOBALS['meta']['aeres']))
		$config = unserialize($GLOBALS['meta']['aeres']);
	else
		$config = array();
	
	$config['membres'] = implode(";", $membres);
	
	include_spip('inc/meta');
	ecrire_meta('aeres',serialize($config));
	
	return array('message_ok'=>_T('config_info_enregistree'));
}

// Source: http://www.memorandom.fr/php/trier-la-colonne-dun-tableau-sans-prendre-en-compte-la-casse-et-les-accents/

function aeres_sans_accent($chaine) {
        if (version_compare(PHP_VERSION, '5.2.3', '>='))
            $str = htmlentities($chaine, ENT_NOQUOTES, "UTF-8", false);
        else
            $str = htmlentities($chaine, ENT_NOQUOTES, "UTF-8");
 
        // NB : On ne peut pas utiliser strtr qui fonctionne mal avec utf8.
        $str = preg_replace('#\&([A-za-z])(?:acute|cedil|circ|grave|ring|tilde|uml)\;#', '\1', $str);
 
        return $str;
	};

function aeres_tri_alpha($data) {
	//On supprime les accents
	$array_sans_accent = array_map(aeres_sans_accent , $data);
	//On met en minuscule
	$array_lowercase = array_map('strtolower', $array_sans_accent);
	// Ajoute $data en tant que dernier paramètre, pour trier par la clé commune
	array_multisort($array_lowercase, SORT_ASC, $data);
	return $data;
}

?>