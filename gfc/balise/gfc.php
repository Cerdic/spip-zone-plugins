<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_GFC($p) {
    return calculer_balise_dynamique($p, 'GFC', array());
}

function balise_GFC_dyn($param) {
	if(function_exists('lire_config')){
		$key = lire_config('gfc/consumer_id') ? str_replace('*:','',lire_config('gfc/consumer_id')) : str_replace('*:','',_GFC_CONSUMER_ID);
		$cookie_name = lire_config('gfc/cookie_name') ? lire_config('gfc/cookie_name') : _GFC_COOKIE_NAME;
	}else{
		$key = str_replace('*:','',_GFC_CONSUMER_ID);
		$cookie_name = _GFC_COOKIE_NAME;
	}
	switch($param){
		case "consumer_id":
			echo $key;
			break;
			
		case "cookie_name":
			echo $cookie_name;
			break;
	}
}

?>