<?php
/**
 * Plugin Smush
 *
 * Auteur :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * Fichier de vérification des binaires présents
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/config');

/**
 * Vérifier si pngnq est présent
 * apt-get install pngnq
 *
 * @return bool true|false
 * 		true si présent, sinon false
 */
function tester_pngnq() {
	exec('pngnq -V', $out, $int);
	if ($int == 0) {
		ecrire_config('pngnq_casse', 'non');
		return true;
	} else {
		ecrire_config('pngnq_casse', 'oui');
		return false;
	}
}

/**
 * Vérifier si optipng est présent
 * apt-get install optipng
 *
 * @return bool true|false
 * 		true si présent, sinon false
 */
function tester_optipng() {
	exec('optipng -v', $out, $int);
	if ($int == 0) {
		ecrire_config('optipng_casse', 'non');
		return true;
	} else {
		ecrire_config('optipng_casse', 'oui');
		return false;
	}
}

/**
 * Vérifier si jpegtran est présent
 * apt-get install libjpeg-turbo-progs
 *
 * @return bool true|false
 * 		true si présent, sinon false
 */
function tester_jpegtran() {
	$ret = _DIR_CACHE.'jpegtran.txt';
	exec('jpegtran -verbose -h 2> '.$ret);
	$contenu = '';
	if (lire_fichier($ret, $contenu) && strlen($contenu) > 0) {
		ecrire_config('jpegtran_casse', 'non');
		return true;
	} else {
		ecrire_config('jpegtran_casse', 'oui');
		return false;
	}
}

/**
 * Vérifier si jpegoptim est présent
 * apt-get install jpegoptim
 *
 * @return bool true|false
 * 		true si présent, sinon false
 */
function tester_jpegoptim() {
	$ret = _DIR_CACHE.'jpegoptim.txt';
	exec('jpegoptim -verbose -h 2> '.$ret);
	$contenu = '';
	if (lire_fichier($ret, $contenu) && strlen($contenu) > 0) {
		ecrire_config('jpegoptim_casse', 'non');
		return true;
	} else {
		ecrire_config('jpegoptim_casse', 'oui');
		return false;
	}
}

/**
 * Vérifier si convert et identify sont présents
 * apt-get install imagemagick
 *
 * @return bool true|false
 * 		true si présents, sinon false
 */
function tester_convert() {
	if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
		$magick = 'magick ';
	} else {
		$magick = '';
	}
	exec($magick.'convert -version', $out, $int1);
	exec($magick.'identify -version', $out, $int2);
	if (($int1 == 0) && ($int2 == 0)) {
		ecrire_config('imagick_casse', 'non');
		return true;
	} else {
		ecrire_config('imagick_casse', 'oui');
		return false;
	}
}

/**
 * Vérifier la présence de gifsicle
 * apt-get install gifsicle
 *
 * @return bool true|false
 * 		true si présents, sinon false
 */
function tester_gifsicle() {
	exec('gifsicle --version', $out, $int);
	if (($int == 0)) {
		ecrire_config('gifsicle_casse', 'non');
		return true;
	} else {
		ecrire_config('gifsicle_casse', 'oui');
		return false;
	}
}

/**
 * Vérifier si impossibilité d'utiliser les binaires
 *
 * On invalide si smush est cassé pour relancer les metas
 *
 * @return bool true|false
 * 		true si vrai, sinon false
 */
function tester_global() {
	include_spip('inc/invalideur');
	$ancienne_valeur = lire_config('smush_casse', 'off');
	if ((lire_config('imagick_casse') == 'oui')
		|| (lire_config('jpegtran_casse') == 'oui')
		|| (lire_config('optipng_casse') == 'oui')
		|| (lire_config('gifsicle_casse') == 'oui')
		|| (lire_config('pngnq_casse') == 'oui')) {
			ecrire_config('smush_casse', 'oui');
			if ($ancienne_valeur != 'oui') {
				suivre_invalideur('1');
			}
			return false;
	} else {
		effacer_config('smush_casse');
		if ($ancienne_valeur != 'off') {
			suivre_invalideur('1');
		}
		return true;
	}
}
