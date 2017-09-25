<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/config');

function formulaires_configurer_lim_objets_charger_dist(){
	$valeurs_meta = lire_config('lim_objets');
	$valeurs['lim_objets']=explode(',',$valeurs_meta);
	return $valeurs;
}

function formulaires_configurer_lim_objets_traiter_dist(){
	if (!is_null($v=_request($m='lim_objets')))
		ecrire_meta($m, is_array($v)?implode(',',$v):'');
	
	$res['message_ok'] = _T('config_info_enregistree');
	include_spip('inc/headers');
	redirige_url_ecrire('configurer_lim_rubriques', 'var_mode=calcul');
	return $res;
}
