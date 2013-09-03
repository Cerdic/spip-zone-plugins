<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function liste_langues(){
	include_spip('inc/config');
	if(is_array($langues_autorisees = lire_config('tradlang/langues_autorisees')) AND count($langues_autorisees) > 0)
		return $langues_autorisees;
	else{
		include_spip('inc/lang_liste');
		return $GLOBALS['codes_langues'];
	}
}
?>