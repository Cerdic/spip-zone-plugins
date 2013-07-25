<?php
/**
 * Plugin auteurs partout
 * (c) 2012 cy_altern
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_auteurspartout_charger_dist(){
	$valeurs = array();
	$valeurs['auteurs_objets'] = explode(',',$GLOBALS['meta']['auteurs_objets']);
	return $valeurs;
}


function formulaires_configurer_auteurspartout_traiter_dist(){
	$res = array('editable'=>true);
	if (!is_null($v=_request($m='auteurs_objets')))
		ecrire_meta($m, is_array($v)?implode(',',$v):'');

	$res['message_ok'] = _T('config_info_enregistree');
	return $res;
}

