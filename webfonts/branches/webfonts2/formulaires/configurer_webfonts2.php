<?php
/*
 * Plugin xxx
 * (c) 2009 xxx
 * Distribue sous licence GPL
 *
 */

function formulaires_configurer_webfonts2_charger_dist(){
	if(!defined('_GOOGLE_API_KEY') OR _GOOGLE_API_KEY == false){
		$valeurs['googlefonts_api_key'] = lire_config('webfonts2/googlefonts_api_key');
	}else{
		$valeurs['googlefonts_api_key'] = _GOOGLE_API_KEY;
	}
	
	return $valeurs;
}

function formulaires_configurer_webfonts2_traiter_dist(){
	include_spip('inc/meta');
	if ($f = _request('googlefonts_api'))
		ecrire_meta('googlefonts_api',$f);
	else
		effacer_meta('googlefonts_api');
	
	return array('message_ok'=>_T('config_info_enregistree'),'editable'=>true);
}

?>