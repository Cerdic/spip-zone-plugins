<?php
if (!defined("_ECRIRE_INC_VERSION")) return;	#securite


function balise_COOKIE_dist($p) {
	return calculer_balise_dynamique($p, 'COOKIE', array());
}

function balise_COOKIE_stat ($args, $context_compil) {
	return $args;
}

/**
 * Calcule la balise dynamique #COOKIE qui retourne la valeur d'un cookie préfixé 
 * si il existe (sinon du cookie sans le préfixe)
 * 
 * @balise
 * @example
 *     ```
 *     #COOKIE{nom_cookie}
 *     ```
 * 
 * @global cookie_prefix Préfixe SPIP des cookies
 * @param String $nom_cookie
 *     nom du cookie (sans le préfixe en principe)
 * @return String
 *     Valeur enregistrée dans le cookie
 **/
function balise_COOKIE_dyn($nom_cookie) {
	if (!$nom_cookie OR trim($nom_cookie) == '')
		return '';

	$nom_cookie_pfx = $GLOBALS['cookie_prefix'].'_'.$nom_cookie;
	$nom_cookie = (isset($_COOKIE[$nom_cookie_pfx]) AND $_COOKIE[$nom_cookie_pfx] != '') ? $nom_cookie_pfx : $nom_cookie;
	
	if (!function_exists("entites_html"))
		include_spip('inc/filtres');
	return entites_html($_COOKIE[$nom_cookie]);
}
?>
