<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_didaspip_configuration_charger_dist(){
	foreach(array(
		"didaspip_url",
		"didaspip_box",
		"didawidth",
		"didaheight",
		"accesdida",
		"accessuppr"
		) as $m)
		$valeurs[$m] = $GLOBALS['meta'][$m];
	return $valeurs;
}


function formulaires_didaspip_configuration_traiter_dist(){
	$res = array('editable'=>true);
	if (_request('dida_ok')!='') {
		ecrire_meta('didaspip_url',_request('url'));
		ecrire_meta('didaspip_box',_request('box'));
		ecrire_meta('didawidth',_request('width'));
		ecrire_meta('didaheight',_request('height'));
		ecrire_meta('accesdida',_request('acces'));
		ecrire_meta('accessuppr',_request('suppr'));
		$res['message_ok'] = _T('config_info_enregistree');
	}
	if (_request('dida_reinit')!='') {
		effacer_meta('didaspip_url');
		effacer_meta('didaspip_box');
		effacer_meta('didawidth');
		effacer_meta('didaheight');
		effacer_meta('accesdida');
		effacer_meta('accessuppr');
		$res['message_ok'] = 'Donn&eacute;es r&eacute;initialis&eacute;es';
	}
	return $res;
}

