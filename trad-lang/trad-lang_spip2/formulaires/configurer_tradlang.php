<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_configurer_tradlang_charger_dist(){
	$valeurs = @unserialize($GLOBALS['meta']['tradlang']);
	spip_log($valeurs);
	if (!is_array($valeurs))
		$valeurs = array();
	
	return $valeurs;
}

function formulaires_configurer_tradlang_verifier_dist(){
	$erreurs = array();
	return $erreurs;
}

function formulaires_configurer_tradlang_traiter_dist(){
	$res = array('editable'=>true);
	foreach(array(
		"sauvegarde_locale",
		"configurer_type",
		"configurer_statuts",
		"configurer_auteurs",
		"modifier_type",
		"modifier_statuts",
		"modifier_auteurs",
		"voir_type",
		"voir_statuts",
		"voir_auteurs"
		) as $m){
			if (!is_null($v=_request($m)))
				$config[$m] = _request($m);
		}
		ecrire_meta('tradlang',serialize($config));
	$res['message_ok'] = _T('config_info_enregistree');
	return $res;
}

