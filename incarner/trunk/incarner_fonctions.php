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
	if (! $maj = lire_config('incarner/maj')) {
		$maj = array();
	}

	if ($cles and ($id_auteur = array_search($cle, $cles))
			and ((time() - $maj[$id_auteur]) < _INCARNER_DELAI_EXPIRATION)) {
		$maj[$id_auteur] = time();
		ecrire_config('incarner/maj', $maj);

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

	$cle_actuelle = (isset($_COOKIE['spip_cle_incarner']) ? $_COOKIE['spip_cle_incarner'] : '');

	if (! $cles = lire_config('incarner/cles')) {
		$cles = array();
	}
	if (! $maj = lire_config('incarner/maj')) {
		$maj = array();
	}

	$nouvelle_cle = urlencode(bin2hex(random_bytes(16)));

	/* première incarnation */
	if (! incarner_cle_valide($cle_actuelle)) {
		$id_auteur = session_get('id_auteur');
	} else {
		$id_auteur = array_search($cle_actuelle, $cles);
	}

	$cles[$id_auteur] = $nouvelle_cle;
	$maj[$id_auteur] = time();

	ecrire_config('incarner/cles', $cles);
	ecrire_config('incarner/maj', $maj);

	spip_setcookie('spip_cle_incarner', $nouvelle_cle);
}

/**
 * Invalider la clé de l'auteur et effacer son cookie
 *
 * @return null
 */
function incarner_invalider_cle() {

	include_spip('inc/config');
	include_spip('inc/cookie');

	$cle_actuelle = (isset($_COOKIE['spip_cle_incarner']) ? $_COOKIE['spip_cle_incarner'] : '');

	if (! $cles = lire_config('incarner/cles')) {
		$cles = array();
	}
	if (! $maj = lire_config('incarner/maj')) {
		$maj = array();
	}

	$index_cle_actuelle = array_search($cle_actuelle, $cles);

	unset($cles[$index_cle_actuelle]);
	unset($maj[$index_cle_actuelle]);
	ecrire_config('incarner/cles', $cles);
	ecrire_config('incarner/maj', $maj);

	spip_setcookie('spip_cle_incarner', '');
}
