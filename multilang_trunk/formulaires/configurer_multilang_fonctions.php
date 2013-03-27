<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function multilang_nommer_langues($langues=array()){
	if(!is_array($langues) || count($langues) <= 1){
		return false;
	}
	sort($langues);
	foreach ($langues as $l => $langue) {
		$langues[$langue] = traduire_nom_langue($langue);
		unset($langues[$l]);
	}
	return $langues;
}
?>