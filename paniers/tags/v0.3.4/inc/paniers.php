<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Chercher le panier associé au visiteur actuel. Cette fonction ne crée pas de panier.
 *
 * @return Retourne l'identifiant du panier en cours
 */
function paniers_id_panier_encours(){
	static $id_panier;
	// Si on a déjà fait les calculs, on termine déjà
	if ($id_panier > 0) return $id_panier;
	
	include_spip('inc/session');
	include_spip('inc/cookie');
	include_spip('inc/config');
	include_spip('base/abstract_sql');
	
	$id_panier = 0;
	$id_auteur = session_get('id_auteur') > 0 ? session_get('id_auteur') : 0;
	$nom_cookie = $GLOBALS['cookie_prefix'].'_panier';
	$cookie = isset($_COOKIE[$nom_cookie]) ? $_COOKIE[$nom_cookie] : null;
	
	// On va chercher un panier existant en cours, correspondant au cookie
	if ($cookie){
		$id_panier = intval(sql_getfetsel(
			'id_panier',
			'spip_paniers',
			array(
				'cookie = '.sql_quote($cookie),
				'statut = '.sql_quote('encours')
			)
		));
	}
	
	// S'il n'y a pas de panier avec le cookie, on regarde s'il y a un auteur connecté et un panier qui lui est lié
	if (!$id_panier
		and $id_auteur
		and $panier = sql_fetsel(
			'id_panier, cookie, date',
			'spip_paniers',
			array(
				'id_auteur = '.$id_auteur,
				'statut = '.sql_quote('encours')
			),
			'',
			'date desc',
			'0,1'
		)
	){
		$date = $panier['date'];
		$cookie = $panier['cookie'];
		
		// Mais ce panier n'est valide que s'il n'est pas trop vieux !
		if (time() < $st=strtotime("$date + " . 3600*intval(lire_config('paniers/limite_enregistres', 168)).'seconds')){
			// Dans ce cas on le prend
			$id_panier = intval($panier['id_panier']);
		}
	}
		
	// Si on a bien un panier et un cookie à la fin
	if ($id_panier > 0 and $cookie){
		// On met son cookie en mémoire
		spip_setcookie($nom_cookie, $_COOKIE[$nom_cookie] = $cookie, time()+3600*lire_config('paniers/limite_ephemere', 24));
	}
	// Sinon on vide le cookie
	else{
		spip_setcookie($nom_cookie, '', 0);
		unset($_COOKIE[$nom_cookie]);
	}
	
	// On retourne enfin un panier (ou pas)
	return $id_panier;
}

/*
 * Crée un panier pour le visiteur actuel et crée un cookie lié
 *
 * @return int Retourne l'identifiant du panier créé
 */
function paniers_creer_panier(){
	include_spip("inc/acces");
	include_spip('inc/session');
	include_spip('base/abstract_sql');
	include_spip('inc/cookie');
	
	$id_auteur = session_get('id_auteur') > 0 ? session_get('id_auteur') : 0;
	$nom_cookie = $GLOBALS['cookie_prefix'].'_panier';
	
	// On crée l'identifiant du cookie
	$cookie = creer_uniqid();
	
	// On crée le panier
	$id_panier = intval(sql_insertq(
		'spip_paniers',
		array(
			'id_auteur' => $id_auteur ? $id_auteur : 0,
			'cookie' => $cookie,
			'date' => 'NOW()'
		)
	));
	
	// Si on a un id_panier correct de créé, on le lie à un cookie
	if ($id_panier > 0){
		spip_setcookie($nom_cookie, $_COOKIE[$nom_cookie] = $cookie, time()+3600*lire_config('paniers/limite_ephemere', 24));
		// Et on met aussi le panier dans la session
		session_set('id_panier', $id_panier);
	}
	
	// On retourne
	return $id_panier;
}

?>
