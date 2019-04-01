<?php
/*
 * Plugin xxx
 * (c) 2009 xxx
 * Distribue sous licence GPL
 *
 */
function formulaires_configurer_webfonts2_charger_dist(){
	$valeurs = array(
		'methode_insert'=> lire_config('webfonts2/methode_insert'),
		'webfonts'=> lire_config('webfonts2/webfonts'),
		'insertion_prive'=> lire_config('webfonts2/insertion_prive'),
	);
	if(!defined('_GOOGLE_API_KEY') OR _GOOGLE_API_KEY == false){
		$valeurs['googlefonts_api_key'] = lire_config('webfonts2/googlefonts_api_key');
	}else{
		$valeurs['googlefonts_api_key'] = _GOOGLE_API_KEY;
	}

	return $valeurs;
}


?>
