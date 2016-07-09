<?php
/**
 * Fonctions utiles au plugin Incarner
 *
 * @plugin     incarner
 * @copyright  2016
 * @author     Michel Bystranowski
 * @licence    GNU/GPL
 */

/**
 * Vérifie si une clé d'incarnation est valide
 *
 * @param string $cle : La clé d'incarnation
 *
 * @return boolean : true si la clé est valide, false sinon
 */
function incarner_cle_valide($cle) {

	include_spip('inc/config');

	if (! $cles = lire_config('incarner/cles')) {
		$cles = array();
	}

	if ($cles and $id_auteur = array_search($cle, $cles)) {
		return true;
	} else {
		return false;
	}
}

/**
 * Renouveler la clé d'incarnation
 *
 * Si on en a pas déjà une on en crée une nouvelle
 *
 * @return null
 */
function incarner_renouveler_cle() {

	include_spip('inc/config');
	include_spip('inc/cookie');
	include_spip('inc/session');

	$cle_actuelle = $_COOKIE['spip_cle_incarner'];

	if (! $cles = lire_config('incarner/cles')) {
		$cles = array();
	}

	$nouvelle_cle = urlencode(openssl_random_pseudo_bytes(16));

	/* première incarnation */
	if (! incarner_cle_valide($cle_actuelle)) {
		$cles[session_get('id_auteur')] = $nouvelle_cle;
	} else {
		$id_auteur = array_search($cle_actuelle, $cles);
		$cles[$id_auteur] = $nouvelle_cle;
	}

	ecrire_config('incarner/cles', $cles);
	spip_setcookie('spip_cle_incarner', $nouvelle_cle);
}
