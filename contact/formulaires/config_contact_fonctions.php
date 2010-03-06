<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
if (!function_exists('lire_define')) {
function lire_define($t){
	if(defined($t)){
		$r = constant($t);	
	}else{
		$r = false;
		}
	return $r;
}
}

?>