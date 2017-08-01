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

if (!defined('_URLS_ARBO_MIN')) {
	/** autoriser fr/ en/ en urls arborescentes */
	define('_URLS_ARBO_MIN', 2);
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
			$url = str_replace(['http://', 'https://'], '', $url);
			if (0 === strpos($url, $_SERVER['HTTP_HOST'])) {
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

/**
 * Surcharge de la génération des URLs d'articles
 *
 * @uses domlang_generer_url_objet_lang()
 *
 * @param int $id
 * @param string $args
 * @param string $ancre
 * @return null|string
 */
function urls_generer_url_article_dist($id, $args, $ancre) {
	return domlang_generer_url_objet_lang($id, $args, $ancre, 'article');
}


/**
 * Surcharge de la génération des URLs de rubriques
 *
 * @uses domlang_generer_url_objet_lang()
 *
 * @param int $id
 * @param string $args
 * @param string $ancre
 * @return null|string
 */
function urls_generer_url_rubrique_dist($id, $args, $ancre) {
	// les rubriques racines n'ont pas d'URL spécifiques (= url du domaine).
	$r = sql_fetsel(['profondeur','lang'], 'spip_rubriques', 'id_rubrique=' . intval($id));
	if ($r['profondeur'] == 0) {
		if ($r['lang'] !== $GLOBALS['spip_lang']) {
			return domlang_url_langue($r['lang']);
		}
		// hum… pas d'autre moyen pour retourner l'url. Retourner '' ne fonctionne pas.
		return url_de_base();
	}
	return domlang_generer_url_objet_lang($id, $args, $ancre, 'rubrique');
}


/**
 * Crée une url absolue si la langue de l'objet ne correspond
 * pas à la langue en cours.
 *
 * Utilise l'URL du domaine correspondant à la langue de l'objet
 * (si différente de la langue en cours) pour créer l'URL absolue.
 *
 * @param int $id
 * @param string $args
 * @param string $ancre
 * @param string $type Type d'objet
 * @return null|string
 */
function domlang_generer_url_objet_lang($id, $args, $ancre, $type) {
	static $marqueur_passage = '@@@domlang@@@';
	// petit hack pour éviter d'entrer en boucle dans cette fonction.
	if (!$ancre or false === strpos($ancre, $marqueur_passage)) {
		$id = intval($id);
		$lang = sql_getfetsel('lang', table_objet_sql($type), id_table_objet($type) . '=' . $id);
		if ($lang !== $GLOBALS['spip_lang']) {
			$url = generer_url_entite($id, $type, $args, $ancre . $marqueur_passage, true);
			if ($ancre) {
				$url = str_replace($marqueur_passage, '', $url);
			} else {
				$url = str_replace('#' . $marqueur_passage, '', $url);
			}
			$url = url_absolue($url, domlang_url_langue($lang));
			return $url;
		}
	}
	// null utilisera la fonction habituelle
	return null;
}