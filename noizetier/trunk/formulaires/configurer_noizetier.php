<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


function formulaires_configurer_noizetier_traiter_dist() {
	$retour = array();

	// Si on a changé la configuration de l'ajax par défaut, on supprime le cache ajax des
	// noisettes pour forcer son recalcul à la prochaine utilisation.
	include_spip('inc/config');
	$defaut_ajax = lire_config('noizetier/ajax_noisette');

	if ($defaut_ajax != _request('ajax_noisette')) {
		include_spip('inc/flock');
		include_spip('noizetier_fonctions');
		supprimer_fichier(_CACHE_AJAX_NOISETTES);
	}

	// On enregistre les nouvelles valeurs saisies
	include_spip('inc/cvt_configurer');
	$trace = cvtconf_formulaires_configurer_enregistre('configurer_noizetier', array());
	$retour['message_ok'] = _T('config_info_enregistree') . $trace;
	$retour['editable'] = true;

	return $retour;
}
