<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('prive/objets/liste/articles_fonctions');

if (!function_exists('defaut_tri_defined')) {
	function defaut_tri_defined($defaut) {
		return $defaut;
	}
}

if (!function_exists('defaut_tri_par')) {
	function defaut_tri_par($par, $defaut) {
			return $par;
	}
}
