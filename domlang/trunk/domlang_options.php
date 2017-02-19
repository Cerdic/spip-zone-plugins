<?php
/**
 * Options au chargement du plugin Domaines par secteur de langue
 *
 * @plugin     Domaines par secteur de langue
 * @copyright  2017
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Domlang\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/*
 * Plusieurs domaines à gérer. À chaque domaine sa langue.
 * - http://domaine.fr/   => langue FR
 * - http://domaine.es/   => langue ES
 * - ...
 */
if (!test_espace_prive()) {
	domlang_definir_langue();
}

/**
 * Définit la langue en cours du site en fonction du domaine depuis lequel on arrive.
 *
 * Si on ne trouve pas de domaine dans la configuration, on utilise la langue principale du site.
 *
 * @uses domlang_domaines_langues()
 */
function domlang_definir_langue() {
	$trouve = false;
	if ($langues = domlang_domaines_langues()) {
		foreach ($langues as $lang => $url) {
			if (false !== strpos($url, $_SERVER['HTTP_HOST'])) {
				$trouve = true;
				break;
			}
		}
	}
	if (!$trouve) {
		$lang = lire_config('langue_site');
	}
	include_spip('inc/lang');
	changer_langue($lang);
	// évitons qu'un malin passe ?lang=xx dans l'URL ce qui nous casserait notre machinerie.
	set_request('lang', $lang);
}


/**
 * Retourne l'URL du site en fonction de la langue.
 *
 * Les URLs des domaines sont configurées dans la méta
 * `domlang/domaines/[langue]/[url]`.
 *
 * En absence de correspondance, on utilise l'adresse du site
 * (la configuration habituelle donc).
 *
 * @uses domlang_domaines_langues()
 * @return string
 */
function domlang_url_langue($lang = null) {
	static $langues = null;
	if (!$lang) {
		$lang = $GLOBALS['spip_lang'];
	}
	if (is_null($langues)) {
		$langues = domlang_domaines_langues();
	}
	if (empty($langues[$lang])) {
		$langues[$lang] = lire_config('adresse_site');
	}
	return $langues[$lang];
}

/**
 * Retourne l'URL du site en fonction de la langue.
 *
 * Les URLs des domaines sont configurées dans la méta
 * `domlang/domaines/[langue]/[url]`.
 *
 * En absence de correspondance, on utilise l'adresse du site
 * (la configuration habituelle donc).
 *
 * @return string
 */
function domlang_domaines_langues() {
	static $langues = null;
	if (is_null($langues)) {
		include_spip('inc/config');
		$langues = lire_config('domlang/domaines/', []);
	}
	return $langues;
}