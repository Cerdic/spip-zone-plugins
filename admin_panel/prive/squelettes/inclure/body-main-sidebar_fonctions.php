<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/bandeau');
include_spip('action/menu_rubriques');

function searchForKey($recherche, $cle, $array) {
	$mon_array = json_decode(json_encode($array), true);

	foreach ($mon_array as $key => $val) {
		if ($val[$cle] === $recherche) {
        	return true;
       	}
   	}
   	return false;
}