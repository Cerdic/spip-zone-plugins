<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction de verification du formulaire de configuration
 * - On vérifie que la clé Bing est présente si cette couche est sélectionnée
 */
function formulaires_configurer_gis_verifier_dist() {
	$erreurs = array();
	$layers = _request('layers');
	if (!is_array($layers)) {
		$layers = array();
	}

	if ((_request('layer_defaut') == 'bing_aerial') or in_array('bing_aerial', $layers)) {
		$obligatoire = 'api_key_bing';
		if (!_request($obligatoire)) {
			$erreurs[$obligatoire] = _T('info_obligatoire');
		}
	}

	// S'il n'y a pas d'erreur on va chercher l'ancienne couche par défaut pour voir si elle a changé
	if (empty($erreurs)) {
		include_spip('inc/config');
		$layer_defaut = lire_config('gis/layer_defaut');
		// Si on change la couche par défaut ou si une couche google est présente dans la conf, le formulaire ne doit pas etre traiter en ajax
		if ((_request('layer_defaut') != $layer_defaut)
			or (count(array_intersect(array('google_roadmap', 'google_satellite', 'google_terrain'), $layers)) > 0)
			or (in_array('bing_aerial', $layers))) {
			refuser_traiter_formulaire_ajax();
		}
	}

	return $erreurs;
}
