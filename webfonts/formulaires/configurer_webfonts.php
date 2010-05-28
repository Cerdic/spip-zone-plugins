<?php
/*
 * Plugin xxx
 * (c) 2009 xxx
 * Distribue sous licence GPL
 *
 */

function formulaires_configurer_webfonts_charger_dist(){
	$valeurs['googlefonts_api'] = $GLOBALS['meta']['googlefonts_api'];
	return $valeurs;
}

function formulaires_configurer_webfonts_traiter_dist(){
	include_spip('inc/meta');
	if ($f = _request('googlefonts_api'))
		ecrire_meta('googlefonts_api',$f);
	else
		effacer_meta('googlefonts_api');
	
	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
}

?>