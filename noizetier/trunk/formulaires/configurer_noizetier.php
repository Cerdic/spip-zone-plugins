<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function formulaires_configurer_noizetier_charger_dist() {

	// On récupère les valeurs configurées
	include_spip('inc/cvt_configurer');
	$valeurs = cvtconf_formulaires_configurer_recense('configurer_noizetier');

	// Injecter les objets exclus
	include_spip('inc/noizetier_objet');
	$valeurs['_objets_exclus'] = noizetier_objet_lister_exclusions();

	$valeurs['editable'] = true;

	return $valeurs;
}


function formulaires_configurer_noizetier_traiter_dist() {
	$retour = array();

	// Si on a changé la configuration de l'ajax par défaut, on supprime le cache ajax des
	// noisettes pour forcer son recalcul à la prochaine utilisation.
	include_spip('inc/config');
	$defaut_ajax = lire_config('noizetier/ajax_noisette');

	if ($defaut_ajax != _request('ajax_noisette')) {
		include_spip('inc/ncore_cache');
		cache_supprimer('noizetier', _NCORE_NOMCACHE_TYPE_NOISETTE_AJAX);
	}

	// On filtre le tableau des objets configurables pour éviter l'index vide fourni systématiquement par la saisie.
	$objets_configurables = _request('objets_noisettes');
	$objets_configurables = is_array($objets_configurables) ? array_filter($objets_configurables) : array();
	set_request('objets_noisettes', $objets_configurables);

	// On enregistre les nouvelles valeurs saisies
	include_spip('inc/cvt_configurer');
	$trace = cvtconf_formulaires_configurer_enregistre('configurer_noizetier', array());
	$retour['message_ok'] = _T('config_info_enregistree') . $trace;
	$retour['editable'] = true;

	return $retour;
}
