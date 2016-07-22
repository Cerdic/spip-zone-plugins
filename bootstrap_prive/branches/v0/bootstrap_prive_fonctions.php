<?php

/**
 * Retourner la valeur de $GLOBALS['spip_ecran'] défini dans les préférences de l'auteur
 * #SPIP_ECRAN
 * 
 * @param  $p
 * @return
 */
function balise_SPIP_ECRAN_dist($p){
	$p->code = isset($_COOKIE['spip_ecran']) ? $_COOKIE['spip_ecran'] : "etroit";
	return $p;
}
