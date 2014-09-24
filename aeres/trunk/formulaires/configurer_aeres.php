<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_aeres_charger_dist(){
	if (isset($GLOBALS['meta']['aeres']))
		$valeurs = unserialize($GLOBALS['meta']['aeres']);
	else
		$valeurs = array(
			'debut' => '',
			'fin' => '',
			'csl' => '',
			'conference_actes' => '',
			'titre_biblio_unite' => '',
			'autorisation_verif_type' => '',
			'autorisation_verif_statuts' => '',
			'autorisation_verif_auteurs' => '',
			'autorisation_biblio_unite_type' => '',
			'autorisation_biblio_unite_statuts' => '',
			'autorisation_biblio_unite_auteurs' => '',
			'autorisation_stats_type' => '',
			'autorisation_stats_statuts' => '',
			'autorisation_stats_auteurs' => ''
		);
	
	// Liste des membres
	if (isset($valeurs['membres']))
		$membres = explode(';',$valeurs['membres']);
	else
		$membres = array();
	include_spip('base/abstract_sql');
	$zcreators = sql_allfetsel('auteur','spip_zcreators','','auteur','auteur');
	foreach ($zcreators as $cle => $zcreator) // remise a plat du tableau
		$zcreators[$cle] = $zcreator['auteur'];
	$non_membres = array_diff($zcreators,$membres);
	
	$valeurs['membres'] = aeres_tri_alpha($membres);
	$valeurs['non_membres'] = aeres_tri_alpha($non_membres);
	
	return $valeurs;
}

function formulaires_configurer_aeres_verifier_dist(){
	$erreurs = array();
	if (!_request('debut') || !intval(_request('debut'))) $erreurs['debut'] = 'Vous devez spécifier un nombre entier.';
	if (!_request('fin') || !intval(_request('fin'))) $erreurs['fin'] = 'Vous devez spécifier un nombre entier.';
	if (!autoriser('webmestre')) $erreurs['message_erreur'] = 'Vous n\'avez pas les droits suffisants pour modifier la configuration.';
	return $erreurs;
}



function formulaires_configurer_aeres_traiter_dist(){
	$membres = _request('membres');
	if (count($membres)) aeres_tri_alpha($membres);
	else $membres = array();
	set_request('membres',$membres); // On retransmet le tableau correctement trié
	$non_membres = _request('non_membres');
	if (count($non_membres)) aeres_tri_alpha($non_membres);
	else $non_membres = array();
	set_request('non_membres',$non_membres);
	$config = array(
		'debut' => _request('debut'),
		'fin' => _request('fin'),
		'csl' => _request('csl'),
		'conference_actes' => _request('conference_actes'),
		'titre_biblio_unite' => _request('titre_biblio_unite'),
		'membres' => implode(";", $membres),
		'autorisation_verif_type' => _request('autorisation_verif_type'),
		'autorisation_verif_statuts' => _request('autorisation_verif_statuts'),
		'autorisation_verif_auteurs' => _request('autorisation_verif_auteurs'),
		'autorisation_biblio_unite_type' => _request('autorisation_biblio_unite_type'),
		'autorisation_biblio_unite_statuts' => _request('autorisation_biblio_unite_statuts'),
		'autorisation_biblio_unite_auteurs' => _request('autorisation_biblio_unite_auteurs'),
		'autorisation_stats_type' => _request('autorisation_stats_type'),
		'autorisation_stats_statuts' => _request('autorisation_stats_statuts'),
		'autorisation_stats_auteurs' => _request('autorisation_stats_auteurs')
	);
	include_spip('inc/meta');
	ecrire_meta('aeres',serialize($config));
	
	return array('message_ok'=>_T('config_info_enregistree'));
}

// Source: http://www.memorandom.fr/php/trier-la-colonne-dun-tableau-sans-prendre-en-compte-la-casse-et-les-accents/

function aeres_tri_alpha($data) {
	$sans_accent = function ($chaine) {
        if (version_compare(PHP_VERSION, '5.2.3', '>='))
            $str = htmlentities($chaine, ENT_NOQUOTES, "UTF-8", false);
        else
            $str = htmlentities($chaine, ENT_NOQUOTES, "UTF-8");
 
        // NB : On ne peut pas utiliser strtr qui fonctionne mal avec utf8.
        $str = preg_replace('#\&([A-za-z])(?:acute|cedil|circ|grave|ring|tilde|uml)\;#', '\1', $str);
 
        return $str;
	};

	//On supprime les accents
	$array_sans_accent = array_map($sans_accent , $data);
	//On met en minuscule
	$array_lowercase = array_map('strtolower', $array_sans_accent);
	// Ajoute $data en tant que dernier paramètre, pour trier par la clé commune
	array_multisort($array_lowercase, SORT_ASC, $data);
	return $data;
}

?>