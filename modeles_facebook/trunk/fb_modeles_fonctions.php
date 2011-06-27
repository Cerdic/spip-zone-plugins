<?php
/**
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function fb_modeles_conf($str) {
	return fbmod_config($str);
}


function fb_modeles_lang($lang) {
	return strtolower($lang).'_'.strtoupper($lang);
}

function fb_modeles_bool($val=null) {
	if ($val=='oui') return 'true';
	if ($val=='non') return 'false';
	return $val;
}

?>