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

function split_f_arg($f, $arg = '') {
	spip_log("split_f_arg($f, $arg", 'cachelab_OBSOLETE');
	return split_first_arg($f, $arg);
}

function slug_chemin($chemin, $sep = '_') {
	return str_replace('/', $sep, $chemin);
}

if (!function_exists('plugin_est_actif')) {
	function plugin_est_actif($prefixe) {
		$f = chercher_filtre('info_plugin');
		return $f($prefixe, 'est_actif');
	}
}
