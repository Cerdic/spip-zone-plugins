<?php
if (!defined("_ECRIRE_INC_VERSION")) return;	#securite


function balise_COOKIE_SET_dist($p) {
	return calculer_balise_dynamique($p, 'COOKIE_SET', array());
}

function balise_COOKIE_SET_stat ($args, $context_compil) {
	return $args;
}

/**
 * Calcule la balise dynamique #COOKIE_SET qui pose un cookie préfixé 
 * gère le préfixe des cookie SPIP sans qu'il soit nécessaire de le passer dans le nom du cookie
 * contrairement à la fonction spip_setcookie(), cette balise prend une durée en seconde 
 * comme second paramètre et non une date d'expiration en timestamp
 * 
 * @balise
 * @see spip_setcookie()
 * @example
 *     ```
 *     #COOKIE_SET{truc,ma_valeur,ma_duree}	// crée le cookie "spip_truc" avec la valeur "ma_valeur" et la durée "ma_duree" (en secondes) avant expiration
 * 	   #COOKIE_SET{truc,ma_valeur}	// crée le cookie "spip_truc" pour la durée de la session
 * 	   #COOKIE_SET{truc}	// supprime le cookie "spip_truc"
 * 	   #COOKIE_SET{truc, ma_valeur, ma_duree, chemin, domaine, secure}	// tous les paramètres utilisés par spip_setcookie()
 *     ```
 * 
 * @param String $nom_cookie
 *     Nom du cookie
 * @param String $valeur_cookie=''
 * 		Valeur du cookie
 * @param Int $duree=0
 * 		Duree de vie du cookie en seconde (si absent cookie de session)
 * @param String $path='AUTO'
 * 		Chemin sur lequel le cookie sera disponible
 * @param String $domaine
 * 		Domaine à partir duquel le cookie est disponible
 * @param String $secure=''
 * 		Cookie sécurisé ou non
 * @return String 
 *     ' ' ou '' selon que la pose du cookie est OK ou non
 **/
function balise_COOKIE_SET_dyn($nom_cookie, $valeur_cookie='', $duree=0, $path='AUTO', $domaine='', $secure='') {
	if (!$nom_cookie OR trim($nom_cookie) == '')
		return '';
	// pour la gestion automagique du prefixe par spip_setcookie, ajouter spip_ en préfixe
	$nom_cookie = strpos($nom_cookie,'spip_') !== 0 ? 'spip_'.$nom_cookie : $nom_cookie;
	// calcul de la date d'expiration
echo '<br>duree: '.$duree;	
	if (!intval($duree))
		$expire = null;
	else
		$expire = time() + intval($duree);
	
	include_spip('inc/cookie');
	return (spip_setcookie($nom_cookie, $valeur_cookie, $expire, $path, $domaine, $secure) ? ' ' : '');
}


?>
