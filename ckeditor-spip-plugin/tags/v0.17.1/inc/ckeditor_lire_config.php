<?php

include_spip("inc/config") ;

function ckeditor_lire_config($key, $def = null) { // non optimale, mais comportement consitant
	$cfg = lire_config('ckeditor') ;
	if (is_array($cfg) && array_key_exists($key, $cfg)) {
		return $cfg[$key] ;
	} else {
		return $def ;
	}
}

?>
