<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_webfonts_index_dist(){
	include_spip(_DIR_PLUGIN_WEBFONTS2.'webfonts2_fonctions');
	$api_key = lire_config('webfonts2/googlefonts_api_key');
	if($googlefonts = googlefont_api_get($api_key)) {

		include_spip('flock','inc');
		$jsonfile = ecrire_fichier(_DIR_TMP.'/googlefont_list.json',json_encode($googlefonts));
		spip_log("Fichier index cree $jsonfile",'webfonts');
		return $jsonfile;
	}else{
		spip_log($googlefont,'webfonts');
		return false;
	}
}
