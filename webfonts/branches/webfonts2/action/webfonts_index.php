<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_webfonts_index_dist(){
	
	if(defined('_GOOGLE_API_KEY') && _GOOGLE_API_KEY != false) {
		include_spip(_DIR_PLUGIN_WEBFONTS2.'webfonts2_fonctions');
		
		$googlefonts = googlefont_api_get(_GOOGLE_API_KEY);
		
		include_spip('flock','inc');
		$jsonfile = ecrire_fichier(_DIR_TMP.'/googlefont_list.json',json_encode($googlefonts));
		
		spip_log('Fichier index cree','webfonts');
		return $jsonfile;
	}else{
		spip_log('API Key non definie','webfonts');
		return false;
	}

}