<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Lister les tables non-SPIP de la base
 * @return array
 **/ 
function saveauto_lister_tables_ext($serveur='') {
	spip_connect($serveur);
	$connexion = $GLOBALS['connexions'][$serveur ? $serveur : 0];
	$prefixe = $connexion['prefixe'];

	$p = '/^' . $prefixe . '/';
	$res = array();
	foreach(sql_alltable('%',$serveur) as $t) {
		if (!preg_match($p, $t)) 
			$res[]= $t;
	}

	sort($res);
	return $res;
}

?>