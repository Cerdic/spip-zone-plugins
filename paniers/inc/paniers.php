<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Chercher le panier associé au visiteur actuel, et sinon le créer
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
	$nom_cookie = $GLOBALS['cookie_prefix'].'panier';
	$cookie = $_COOKIE[$nom_cookie];
	
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
	
	// S'il n'y a pas encore de panier, soit on chercher pour l'auteur, soit on en crée un nouveau
	if (!$id_panier){
		// S'il y a un auteur on regarde s'il a un panier pas trop vieux en mémoire : le dernier en date
		if ($id_auteur
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
			
			// Mais ce panier n'est valide que s'il n'est pas trop vieux
			if (time() < strtotime($date + lire_config('paniers/limite_enregistres', 7*24*3600))){
				// Dans ce cas on le prend
				$id_panier = intval($panier['id_panier']);
			}
		}
		
		// S'il n'y a toujours pas de panier, il faut en créer un nouveau, ainsi qu'un cookie
		if (!$id_panier){
			include_spip("inc/acces");
			// On crée l'identifiant du cookie
			$cookie = creer_uniqid();
			// On crée le panier
			$id_panier = sql_insertq(
				'spip_paniers',
				array(
					'id_auteur' => $id_auteur ? $id_auteur : 0,
					'cookie' => $cookie,
					'date' => 'NOW()'
				)
			);
		}
	}
	
	// Si on a bien un panier et un cookie à la fin
	if ($id_panier > 0 and $cookie){
		// On met son cookie en mémoire
		spip_setcookie($nom_cookie, $_COOKIE[$nom_cookie] = $cookie, lire_config('paniers/limite_ephemere', 24*3600));
	}
	// Sinon on vide le cookie
	else{
		spip_setcookie($nom_cookie, '', 0);
		unset($_COOKIE[$nom_cookie]);
	}
	
	// On retourne enfin un panier
	return $id_panier;
}

?>
