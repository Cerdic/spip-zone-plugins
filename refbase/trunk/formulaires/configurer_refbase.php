<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function refbase_metas(){
	return array(
		"url_refbase",
		"vue",
		"liens",
		"max",
		"doublons",
		"tri",
		"style",
		"liens_exports",
		"css"
	);
}

function formulaires_configurer_refbase_charger_dist(){
	$valeurs = array();
	foreach(refbase_metas() as $m)
		$valeurs[$m] = '';
	// Valeurs par défaut
	$valeurs['url_refbase'] = "http://";
	$valeurs['liens'] = 'oui';
	$valeurs['max'] = 100;
	$valeurs['doublons'] = 'non';
	$valeurs['liens_export'] = 'oui';
	
	if ($config = unserialize($GLOBALS['meta']['refbase']))
		$valeurs = array_merge($valeurs,$config);

	return $valeurs;
}


function formulaires_configurer_refbase_traiter_dist(){
	include_spip('inc/meta');
	$config = array();
	foreach(refbase_metas() as $m)
		$config[$m] = _request($m);
	
	ecrire_meta('refbase', serialize($config));

	$res['message_ok'] = _T('config_info_enregistree');
	return $res;
}

