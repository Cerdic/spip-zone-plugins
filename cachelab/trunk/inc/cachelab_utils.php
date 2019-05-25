<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

//
// sépare à la lisp le premier mot et les autres
//
// $f (string) : liste à séparer
// $cdr (string ou array) : valeur par défaut pour le 2eme élt renvoyé
// $string_wanted (bool) : type du 2eme élément renvoyé
//
// retour (array) :
// retour[0] (string) : 1er élément de la liste
// retour[1] (string ou array selon $string_wanted) : reste de la liste
//
/**
 * @param string $l             liste de termes
 * @param string $cdr           suite par défaut
 * @param bool $string_wanted   cdr renvoyé comme chaine ou comme tableau
 * @return array                renvoie un tableau [ car, cdr ]
 *      où car est le premier élément de la liste
 *      et cdr est le reste de la liste, ou le cdr reçu en argument s'il n'y a pas de reste
 *
 */
function split_first_arg($l, $cdr = '', $string_wanted = true) {
	$l = preg_replace('/\s+/', ' ', trim($l), -1, $n);
	$lparts = explode(' ', $l);
	if ($lparts != array_filter($lparts)) {
		spip_log("split_first_arg($l, $cdr, $string_wanted); mauvais format du 1er argument".print_r($lparts, 1), 'cachelab_ASSERT');
		exit;
	}
	$car = array_shift($lparts);
	if (!$car) {
		spip_log("split_first_arg($l,$cdr,$string_wanted) : pb avec le 1er argument, reste lparts=".print_r($lparts, 1), 'cachelab_ASSERT');
		exit;
	}

	if ($lparts) {
		if ($string_wanted) {
			$cdr = implode(' ', $lparts);
		} else {
			$cdr = $lparts;
		}
	}

	return array ($car, $cdr);
}

/**
 * @param string $chemin
 * @param string $sep
 * @return string   remplace les / par des _
 */
function slug_chemin($chemin, $sep = '_') {
	return str_replace('/', $sep, $chemin);
}

if (!function_exists('plugin_est_actif')) {
	/**
	 * @param $prefixe
	 * @return bool le plugin de ce préfixe est il actif ?
	 */
	function plugin_est_actif($prefixe) {
		$f = chercher_filtre('info_plugin');
		return $f($prefixe, 'est_actif');
	}
}

/**
 * @param string $cond              signal d'invalidation
 *       typiquement de la forme : "id='id_document/1234'"
 * @param string $cle_objet_attendue     id_objet attendu
 * @param string &$objet            objet effectivement signalé
 * @return int|null
 *
 *
 * renvoie l'id_objet ciblé par le signal
 */
function decoder_invalideur($cond, $cle_objet_attendue = '', &$objet='') {
	if (!preg_match(',["\']([a-z_]+)[/"\'](.*)[/"\'],', $cond, $r)) {
		spip_log("Signal non conforme pour decode_signal_invalideur ($cond, $cle_objet_attendue)", 'cachelab_erreur');
		return null;
	}
	// ignorer [0] = match total
	list (, $objet, $id_objet) = $r;
	if ($cle_objet_attendue and ($objet!=$cle_objet_attendue)) {
		spip_log(
			"decoder_invalideur($cond,) ne reçoit pas un '$cle_objet_attendue' mais un '$objet'",
			'cachelab_erreur'
		);
		return null;
	};
	if (!$id_objet) {
		spip_log(
			"decoder_invalideur($cond,) reçoit un $objet nul",
			'cachelab_erreur'
		);
		return null;
	}
	return $id_objet;
}
