<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/config');

function formulaires_configurer_lim_objets_charger_dist(){
	$valeurs['objets'] = lire_config('lim/rubriques/objets');
	return $valeurs;
}

function formulaires_configurer_lim_objets_traiter_dist(){
	if ($v = _request('objets')) {
		ecrire_config('lim/rubriques/objets', $v);
	}
	
	include_spip('inc/headers');
	redirige_url_ecrire('configurer_lim_rubriques', 'var_mode=calcul');
	return $res;
}
