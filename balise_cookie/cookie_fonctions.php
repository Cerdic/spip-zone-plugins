<?php
// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Compile la balise #COOKIE qui retourne la valeur d'un cookie préfixé 
 * si il existe (sinon du cookie sans le préfixe)
 * 
 * @balise
 * @see balise_SESSION_dist()
 * @example
 *     ```
 *     #COOKIE{nom_cookie}	// nom_cookie est prévu sans le préfixe
 *     ```
 * 
 * @global cookie_prefix Préfixe SPIP des cookies
 * @param Champ $p
 *     Pile au niveau de la balise.
 * @return Champ
 *     Pile completée du code PHP d'exécution de la balise
**/
function balise_COOKIE_dist($p) {
	$nom_cookie = interprete_argument_balise(1,$p);
	if (!$nom_cookie) {
		$msg = _T('zbug_balise_sans_argument', array('balise' => ' COOKIE'));
		erreur_squelette($msg, $p);
		$p->interdire_scripts = false;
		return $p;
	}
	
	// pour éviter les problèmes de valeur de cookie passé d'un utilisateur à l'autre via le cache
	// sur le modele de la balise #SESSION lever le drapeau d'invalidation du cache en fonction de la session 
	$p->descr['session'] = true;
	
	// si elle existe on récupère la valeur du cookie prefixe_nom_cookie
	// sinon on prend celle du cookie nom_cookie (qu'il existe ou non)
	$nom_cookie_pfx = $GLOBALS['cookie_prefix'].'_'.trim($nom_cookie,"'");
	$nom_cookie = "(isset(\$_COOKIE['$nom_cookie_pfx']) AND \$_COOKIE['$nom_cookie_pfx'] != '') ? '$nom_cookie_pfx' : $nom_cookie";

	if (!$p->etoile)
		$p->code = "entites_html(\$_COOKIE[$nom_cookie])";
	else
		$p->code = "\$_COOKIE[$nom_cookie]";

	$p->interdire_scripts = false;
	return $p;
}

/**
 * Compile la balise #COOKIE_SET qui pose un cookie préfixé 
 * gère le préfixe des cookie SPIP sans qu'il soit nécessaire de le passer dans le nom du cookie
 * contrairement à la fonction spip_setcookie(), cette balise prend une durée en seconde 
 * comme second paramètre et non une date d'expiration en timestamp
 * 
 * @balise
 * @see spip_setcookie()
 * @example
 *     ```
 *     #COOKIE_SET{truc,ma_valeur,ma_duree}	// crée le cookie "spip_truc" avec la valeur "ma_valeur" et la durée "ma_duree" (en secondes) avant expiration
 * 	   #COOKIE_SET{truc,ma_valeur}	// crée le cookie "spip_truc" pour la durée de la session uniquement
 * 	   #COOKIE_SET{truc}	// supprime le cookie "spip_truc"
 * 	   #COOKIE_SET{truc, ma_valeur, ma_duree, chemin, domaine, secure}	// tous les paramètres utilisés par spip_setcookie()
 * 	   
 *     ```
 * 
 * @param Champ $p
 *     Pile au niveau de la balise.
 * @return Champ
 *     Pile completée du code PHP d'exécution de la balise
**/

function balise_COOKIE_SET_dist($p) {
	$nom_cookie = interprete_argument_balise(1,$p);
	if (!$nom_cookie) {
		$msg = _T('zbug_balise_sans_argument', array('balise' => ' COOKIE_SET'));
		erreur_squelette($msg, $p);
		$p->interdire_scripts = false;
		return $p;
	}
	
	include_spip('inc/cookie');
	// pour la gestion du prefixe par spip_setcookie, ajouter spip_ en préfixe
	$nom_cookie = (strpos($nom_cookie,'spip_') !== 1 ? "'spip_".trim($nom_cookie,"'")."'" : $nom_cookie);
	
	$valeur_cookie = interprete_argument_balise(2,$p) ? interprete_argument_balise(2,$p) : "''";
	$expire = interprete_argument_balise(3,$p) ? time() + str_replace("'", "",interprete_argument_balise(3,$p)) : "0";
	$path = interprete_argument_balise(4,$p) ? interprete_argument_balise(4,$p) : "'AUTO'";
	$domaine = interprete_argument_balise(5,$p) ? interprete_argument_balise(5,$p) : "''";
	$secure = interprete_argument_balise(6,$p) ? interprete_argument_balise(6,$p) : "''";
	
	$p->code = "(spip_setcookie($nom_cookie, $valeur_cookie, $expire, $path, $domaine, $secure) ? ' ': '')";
	$p->interdire_scripts = false;
	return $p;
}


?>
